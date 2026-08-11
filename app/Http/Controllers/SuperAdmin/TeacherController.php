<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\SuperAdminActivity;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class TeacherController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->query('search', ''));

        $teachersQuery = User::where('role', User::ROLE_TEACHER);

        if ($search !== '') {
            $teachersQuery->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $teachers = $teachersQuery->orderBy('name')->get();
        $activeTeachersCount = User::where('role', User::ROLE_TEACHER)->where('active', true)->count();
        $inactiveTeachersCount = User::where('role', User::ROLE_TEACHER)->where('active', false)->count();
        $activities = SuperAdminActivity::latest()->take(8)->get();

        if ($request->route()?->getName() === 'superadmin.dashboard') {
            return view('superadmin.dashboard', compact(
                'teachers',
                'search',
                'activeTeachersCount',
                'inactiveTeachersCount',
                'activities'
            ));
        }

        return view('superadmin.teachers.index', compact('teachers', 'search', 'activeTeachersCount', 'activities'));
    }

    public function create()
    {
        return view('superadmin.teachers.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $teacher = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => User::ROLE_TEACHER,
            'active' => true,
            'status' => User::STATUS_ACTIVE,
        ]);

        $this->logActivity('teacher_created', 'Created teacher account', ['teacher_id' => $teacher->id, 'teacher_name' => $teacher->name]);

        return redirect()->route('superadmin.teachers.index')->with('success', 'Teacher created successfully.');
    }

    public function edit(User $teacher)
    {
        abort_unless($teacher->role === User::ROLE_TEACHER, 404);

        return view('superadmin.teachers.edit', compact('teacher'));
    }

    public function update(Request $request, User $teacher)
    {
        abort_unless($teacher->role === User::ROLE_TEACHER, 404);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $teacher->id],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'status' => ['required', 'string', 'in:' . implode(',', [User::STATUS_ACTIVE, User::STATUS_DISABLED, User::STATUS_PENDING])],
        ]);

        $teacher->name = $data['name'];
        $teacher->email = $data['email'];
        $teacher->status = $data['status'];
        $teacher->active = $data['status'] === User::STATUS_ACTIVE;

        if (!empty($data['password'])) {
            $teacher->password = Hash::make($data['password']);
        }

        $teacher->save();

        $this->logActivity('teacher_updated', 'Updated teacher account', ['teacher_id' => $teacher->id, 'teacher_name' => $teacher->name]);

        return redirect()->route('superadmin.teachers.index')->with('success', 'Teacher updated successfully.');
    }

    public function destroy(User $teacher)
    {
        abort_unless($teacher->role === User::ROLE_TEACHER, 404);

        $teacher->delete();
        $this->logActivity('teacher_deleted', 'Removed teacher account', ['teacher_name' => $teacher->name]);

        return redirect()->route('superadmin.teachers.index')->with('success', 'Teacher removed successfully.');
    }

    public function profile()
    {
        return view('superadmin.profile', ['user' => auth()->user()]);
    }

    public function updateProfile(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . auth()->id()],
        ]);

        $user = auth()->user();
        $user->fill($data);
        $user->save();

        $this->logActivity('profile_updated', 'Updated superadmin profile', ['name' => $user->name]);

        return redirect()->route('superadmin.profile')->with('success', 'Profile updated successfully.');
    }

    public function updatePassword(Request $request)
    {
        $data = $request->validate([
            'current_password' => ['required', 'string'],
            'new_password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = auth()->user();

        if (!Hash::check($data['current_password'], $user->password)) {
            return back()->withErrors(['current_password' => 'The current password is incorrect.']);
        }

        $user->password = Hash::make($data['new_password']);
        $user->save();

        $this->logActivity('password_changed', 'Changed superadmin password');

        return redirect()->route('superadmin.profile')->with('success', 'Password updated successfully.');
    }

    private function logActivity(string $type, string $description, ?array $details = null): void
    {
        SuperAdminActivity::create([
            'user_id' => auth()->id(),
            'type' => $type,
            'description' => $description,
            'details' => $details ? json_encode($details) : null,
        ]);
    }
}
