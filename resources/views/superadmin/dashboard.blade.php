@extends('layouts.app')

@section('pageTitle', 'Super Admin Dashboard')
@section('pageSubtitle', 'Manage teachers, monitor activity, and keep the platform running smoothly.')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col gap-4 rounded-[28px] border border-slate-200/80 bg-white/90 p-6 shadow-sm lg:flex-row lg:items-center lg:justify-between">
        <div>
            <h2 class="text-2xl font-semibold text-slate-900">Super Admin Dashboard</h2>
            <p class="mt-1 text-sm text-slate-500">A concise overview of teacher health, activity, and admin actions.</p>
        </div>
        <a class="btn btn-success" href="{{ route('superadmin.teachers.create') }}">Create Teacher</a>
    </div>

    <div class="grid gap-4 md:grid-cols-3">
        <div class="card">
            <div class="card-body">
                <div class="text-sm font-medium text-slate-500">Active Teachers</div>
                <div class="mt-3 text-3xl font-semibold text-slate-900">{{ $activeTeachersCount ?? 0 }}</div>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <div class="text-sm font-medium text-slate-500">Inactive Teachers</div>
                <div class="mt-3 text-3xl font-semibold text-slate-900">{{ $inactiveTeachersCount ?? 0 }}</div>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <div class="text-sm font-medium text-slate-500">Recent System Activity</div>
                <div class="mt-3 text-3xl font-semibold text-slate-900">{{ ($activities ?? collect())->count() }}</div>
            </div>
        </div>
    </div>

    <div class="grid gap-6 xl:grid-cols-[1.3fr_0.7fr]">
        <div class="card">
            <div class="card-body">
                <div class="mb-4 flex items-center justify-between gap-3">
                    <div>
                        <h3 class="card-title mb-0">Teacher Management</h3>
                        <p class="text-sm text-slate-500">Search and review teacher accounts quickly.</p>
                    </div>
                </div>

                <form method="GET" action="{{ route('superadmin.dashboard') }}" class="mb-4 flex flex-col gap-3 md:flex-row">
                    <input type="text" name="search" class="form-input" placeholder="Search by name or email" value="{{ $search ?? '' }}">
                    <div class="flex gap-2">
                        <button type="submit" class="btn btn-outline-primary">Search</button>
                        @if(!empty($search))
                            <a href="{{ route('superadmin.dashboard') }}" class="btn btn-outline-secondary">Clear</a>
                        @endif
                    </div>
                </form>

                <div class="overflow-x-auto">
                    @if(($teachers ?? collect())->isEmpty())
                        <div class="rounded-2xl border border-dashed border-slate-200 bg-slate-50 p-4 text-sm text-slate-500">No teachers found.</div>
                    @else
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($teachers ?? collect() as $teacher)
                                    <tr>
                                        <td>
                                            <div class="font-semibold text-slate-900">{{ $teacher->name }}</div>
                                            <div class="text-sm text-slate-500">{{ $teacher->email }}</div>
                                        </td>
                                        <td>
                                            @php
                                                $badgeClass = match($teacher->status ?? ($teacher->active ? 'active' : 'disabled')) {
                                                    'pending' => 'warning',
                                                    'disabled' => 'secondary',
                                                    default => 'success',
                                                };
                                                $statusLabel = match($teacher->status ?? ($teacher->active ? 'active' : 'disabled')) {
                                                    'pending' => 'Pending',
                                                    'disabled' => 'Disabled',
                                                    default => 'Active',
                                                };
                                            @endphp
                                            <span class="badge bg-{{ $badgeClass }}">{{ $statusLabel }}</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="card">
                <div class="card-body">
                    <div class="mb-4 flex items-center justify-between gap-3">
                        <h3 class="card-title mb-0">Recent Activities</h3>
                        <span class="rounded-full bg-indigo-50 px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em] text-indigo-600">Live overview</span>
                    </div>
                    <ul class="space-y-3">
                        @forelse($activities ?? collect() as $activity)
                            <li class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                                <div class="font-semibold text-slate-900">{{ $activity->description }}</div>
                                <div class="mt-1 text-sm text-slate-500">{{ $activity->created_at->diffForHumans() }}</div>
                            </li>
                        @empty
                            <li class="rounded-2xl border border-dashed border-slate-200 bg-slate-50 p-4 text-sm text-slate-500">No activity recorded yet.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
