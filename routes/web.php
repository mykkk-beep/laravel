<?php

use App\Http\Controllers\Parent\DashboardController as ParentDashboardController;
use App\Http\Controllers\Parent\ParentAuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StudentPortalController;
use App\Http\Controllers\SuperAdmin\TeacherController;
use App\Http\Controllers\Teacher\AttendanceController as TeacherAttendanceController;
use App\Http\Controllers\Teacher\ClassController as TeacherClassController;
use App\Http\Controllers\Teacher\DashboardController as TeacherDashboardController;
use App\Http\Controllers\Teacher\StudentController as TeacherStudentController;
use App\Http\Controllers\Teacher\SubjectController as TeacherSubjectController;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }

    return view('welcome');
});

Route::get('/dashboard', function () {
    $user = Auth::user();

    if ($user?->role === User::ROLE_TEACHER) {
        return redirect()->route('teacher.dashboard');
    }

    if ($user?->role === User::ROLE_SUPERADMIN) {
        return redirect()->route('superadmin.dashboard');
    }

    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::prefix('student')->name('student.')->group(function () {
    Route::get('/login', [StudentPortalController::class, 'showLogin'])->name('login');
    Route::post('/login', [StudentPortalController::class, 'login'])->name('login.submit');

    Route::middleware('student.authenticated')->group(function () {
        Route::get('/dashboard', [StudentPortalController::class, 'dashboard'])->name('dashboard');
        Route::get('/notifications', [StudentPortalController::class, 'notifications'])->name('notifications');
        Route::post('/notifications/{notification}/reply', [StudentPortalController::class, 'reply'])->name('notifications.reply');
        Route::delete('/notifications/{notification}', [StudentPortalController::class, 'destroy'])->name('notifications.destroy');
        Route::get('/profile', [StudentPortalController::class, 'profile'])->name('profile');
        Route::post('/logout', [StudentPortalController::class, 'logout'])->name('logout');
    });
});

Route::prefix('parent')->name('parent.')->group(function () {
    Route::get('/login', [ParentAuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [ParentAuthController::class, 'login'])->name('login.submit');
    Route::post('/logout', [ParentAuthController::class, 'logout'])->name('logout');

    Route::middleware('parent.auth')->group(function () {
        Route::get('/dashboard', [ParentDashboardController::class, 'index'])->name('dashboard');
        Route::get('/notifications', [ParentDashboardController::class, 'notifications'])->name('notifications');
        Route::post('/notifications/{notification}/reply', [ParentDashboardController::class, 'reply'])->name('notifications.reply');
        Route::delete('/notifications/{notification}', [ParentDashboardController::class, 'destroy'])->name('notifications.destroy');
    });
});

Route::middleware(['auth', 'role:teacher'])->group(function () {
    Route::get('/teacher/dashboard', [TeacherDashboardController::class, 'index'])->name('teacher.dashboard');
    Route::get('/teacher/notification', [TeacherDashboardController::class, 'notifications'])->name('teacher.notifications');
    Route::get('/teacher/recommendations', [TeacherDashboardController::class, 'recommendations'])->name('teacher.recommendations');
    Route::post('/teacher/recommendations/{student}/generate', [TeacherDashboardController::class, 'generateRecommendation'])->name('teacher.recommendations.generate');
    Route::get('/teacher/recommendations/{notification}', [TeacherDashboardController::class, 'showRecommendation'])->name('teacher.recommendations.show');
    Route::get('/teacher/recommendations/{notification}/edit', [TeacherDashboardController::class, 'editRecommendation'])->name('teacher.recommendations.edit');
    Route::put('/teacher/recommendations/{notification}', [TeacherDashboardController::class, 'updateRecommendation'])->name('teacher.recommendations.update');
    Route::delete('/teacher/recommendations/{notification}', [TeacherDashboardController::class, 'destroyRecommendation'])->name('teacher.recommendations.destroy');
    Route::post('/teacher/recommendations/{notification}/resend', [TeacherDashboardController::class, 'resendRecommendation'])->name('teacher.recommendations.resend');
    Route::get('/teacher/recommendations/{notification}/data', [TeacherDashboardController::class, 'recommendationData'])->name('teacher.recommendations.data');
    Route::post('/teacher/notify-student/{student}', [TeacherDashboardController::class, 'notifyStudent'])->name('teacher.notifyStudent');
    Route::post('/teacher/notifications/{notification}/reply', [TeacherDashboardController::class, 'replyToParent'])->name('teacher.notifications.reply');

    Route::get('/teacher/classes', [TeacherClassController::class, 'index'])->name('teacher.classes.index');
    Route::get('/teacher/classes/create', [TeacherClassController::class, 'create'])->name('teacher.classes.create');
    Route::post('/teacher/classes', [TeacherClassController::class, 'store'])->name('teacher.classes.store');
    Route::get('/teacher/classes/{classRoom}/edit', [TeacherClassController::class, 'edit'])->name('teacher.classes.edit');
    Route::put('/teacher/classes/{classRoom}', [TeacherClassController::class, 'update'])->name('teacher.classes.update');
    Route::delete('/teacher/classes/{classRoom}', [TeacherClassController::class, 'destroy'])->name('teacher.classes.destroy');

    Route::get('/teacher/students', [TeacherStudentController::class, 'allStudents'])->name('teacher.students.all');
    Route::get('/teacher/students/create', [TeacherStudentController::class, 'createFromDashboard'])->name('teacher.students.create');
    Route::post('/teacher/students', [TeacherStudentController::class, 'storeFromDashboard'])->name('teacher.students.store');
    Route::get('/teacher/classes/{classRoom}/students', [TeacherStudentController::class, 'index'])->name('teacher.classes.students.index');
    Route::get('/teacher/classes/{classRoom}/students/create', [TeacherStudentController::class, 'create'])->name('teacher.classes.students.create');
    Route::post('/teacher/classes/{classRoom}/students', [TeacherStudentController::class, 'store'])->name('teacher.classes.students.store');
    Route::post('/teacher/students/{student}/enroll', [TeacherStudentController::class, 'enroll'])->name('teacher.students.enroll');
    Route::post('/teacher/classes/{classRoom}/students/enroll-all', [TeacherStudentController::class, 'enrollAll'])->name('teacher.classes.students.enroll_all');
    Route::delete('/teacher/students/{student}', [TeacherStudentController::class, 'destroy'])->name('teacher.students.destroy');
    Route::get('/teacher/students/{student}/edit', [TeacherStudentController::class, 'edit'])->name('teacher.students.edit');
    Route::put('/teacher/students/{student}', [TeacherStudentController::class, 'update'])->name('teacher.students.update');

    Route::get('/teacher/attendance', [TeacherAttendanceController::class, 'scan'])->name('teacher.attendance.scan');
    Route::get('/teacher/attendance/records', [TeacherAttendanceController::class, 'records'])->name('teacher.attendance.records');
    Route::get('/teacher/attendance/report', [TeacherAttendanceController::class, 'report'])->name('teacher.attendance.report');
    Route::get('/teacher/attendance/export/{format}', [TeacherAttendanceController::class, 'export'])->name('teacher.attendance.export');
    Route::post('/teacher/attendance/initialize', [TeacherAttendanceController::class, 'initialize'])->name('teacher.attendance.initialize');
    Route::post('/teacher/attendance/record', [TeacherAttendanceController::class, 'record'])->name('teacher.attendance.record');
    Route::post('/teacher/attendance/finalize-quick', [TeacherAttendanceController::class, 'finalizeQuick'])->name('teacher.attendance.finalize-quick');
    Route::post('/teacher/attendance/mark-all-present', [TeacherAttendanceController::class, 'markAllPresent'])->name('teacher.attendance.mark-all-present');

    Route::get('/teacher/subjects', [TeacherSubjectController::class, 'index'])->name('teacher.subjects.index');
    Route::get('/teacher/subjects/create', [TeacherSubjectController::class, 'create'])->name('teacher.subjects.create');
    Route::post('/teacher/subjects', [TeacherSubjectController::class, 'store'])->name('teacher.subjects.store');
});

Route::middleware('auth')->prefix('superadmin')->name('superadmin.')->group(function () {
    Route::get('/dashboard', [TeacherController::class, 'index'])->name('dashboard');

    Route::resource('teachers', TeacherController::class)->except(['show']);

    Route::get('profile', [TeacherController::class, 'profile'])->name('profile');
    Route::put('profile', [TeacherController::class, 'updateProfile'])->name('profile.update');
    Route::put('profile/password', [TeacherController::class, 'updatePassword'])->name('profile.password.update');
});

require __DIR__.'/auth.php';
