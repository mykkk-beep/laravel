@extends('layouts.app')

@section('content')
@if (session('success'))
    <div class="alert alert-success" role="alert">
        {{ session('success') }}
    </div>
@endif
@if (session('error'))
    <div class="alert alert-danger" role="alert">
        {{ session('error') }}
    </div>
@endif


<div class="grid grid-cols-3 gap-3 mb-4">
    <div class="min-w-0">
        <div class="card shadow-sm border-0 rounded-4 h-100">
            <div class="card-body">
                <h6 class="text-muted">Active Accounts</h6>
                <h2 class="mb-0">{{ $activeTeachersCount ?? 0 }}</h2>
            </div>
        </div>
    </div>

    <div class="min-w-0">
        <div class="card shadow-sm border-0 rounded-4 h-100">
            <div class="card-body">
                <h6 class="text-muted">Disabled Accounts</h6>
                <h2 class="mb-0">{{ $disabledTeachersCount ?? 0 }}</h2>
            </div>
        </div>
    </div>

    <div class="min-w-0">
        <div class="card shadow-sm border-0 rounded-4 h-100">
            <div class="card-body">
                <h6 class="text-muted">Pending Accounts</h6>
                <h2 class="mb-0">{{ $pendingTeachersCount ?? 0 }}</h2>
            </div>
        </div>
    </div>
</div>


<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Teachers</h2>
    <a class="btn btn-success" href="{{ route('superadmin.teachers.create') }}">Create Teacher Account</a>
</div>

<form method="GET" action="{{ route('superadmin.teachers.index') }}" class="d-flex flex-nowrap align-items-center gap-2 mb-4">
    <div class="flex-grow-1">
        <input type="text" name="search" class="form-control" placeholder="Search by name or email" value="{{ $search ?? '' }}">
    </div>
    <div class="flex-grow-1">
        <select name="sort" class="form-select" aria-label="Sort teacher accounts by creation date">
            <option value="name" @selected(($sort ?? 'name') === 'name')>Default order</option>
            <option value="latest" @selected(($sort ?? 'name') === 'latest')>Latest created accounts</option>
            <option value="oldest" @selected(($sort ?? 'name') === 'oldest')>Oldest created accounts</option>
        </select>
    </div>
    <div class="d-flex flex-shrink-0 gap-2">
        <button type="submit" class="btn btn-outline-primary">Search</button>
        @if(!empty($search) || ($sort ?? 'name') !== 'name')
            <a href="{{ route('superadmin.teachers.index') }}" class="btn btn-outline-secondary">Clear</a>
        @endif
    </div>
</form>

<div class="table-responsive">
    @if(($teachers ?? collect())->isEmpty())
        <div class="alert alert-info">No teachers found.</div>
    @else
    <table class="table table-bordered">
        <thead>
            <tr class="font-bold">
                <th>Name</th>
                <th>Username</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($teachers ?? collect() as $teacher)
                <tr>
                    <td>{{ $teacher->name }}</td>
                    <td>{{ $teacher->email }}</td>
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
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            @if(($teacher->status ?? null) === \App\Models\User::STATUS_PENDING)
                                <form action="{{ route('superadmin.teachers.approve', $teacher) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-success">Accept</button>
                                </form>
                                <form action="{{ route('superadmin.teachers.decline', $teacher) }}" method="POST" class="d-inline" onsubmit="return confirm('Decline this teacher account?');">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Decline</button>
                                </form>
                            @else
                                <a class="btn btn-sm bg-indigo-50 text-indigo-700 border border-indigo-200 hover:bg-indigo-100" href="{{ route('superadmin.teachers.edit', $teacher) }}">Edit</a>
                                <form action="{{ route('superadmin.teachers.destroy', $teacher) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this teacher account?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm bg-rose-50 text-rose-700 border border-rose-200 hover:bg-rose-100">Delete</button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    @endif
</div>
@endsection
