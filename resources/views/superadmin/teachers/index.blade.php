@extends('layouts.app')

@section('content')
@if (session('success'))
    <div class="alert alert-success" role="alert">
        {{ session('success') }}
    </div>
@endif

<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <h6 class="text-muted">Active Teachers</h6>
                <h2 class="mb-0">{{ $activeTeachersCount ?? 0 }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <h5 class="card-title">Recent Activities</h5>
                <ul class="list-group list-group-flush">
                    @forelse($activities ?? collect() as $activity)
                        <li class="list-group-item px-0">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <strong>{{ $activity->description }}</strong>
                                    <div class="text-muted small">{{ $activity->created_at->diffForHumans() }}</div>
                                </div>
                            </div>
                        </li>
                    @empty
                        <li class="list-group-item px-0 text-muted">No activity recorded yet.</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Teachers</h2>
    <a class="btn btn-success" href="{{ route('superadmin.teachers.create') }}">Create Teacher</a>
</div>

<form method="GET" action="{{ route('superadmin.dashboard') }}" class="row g-2 mb-4">
    <div class="col-md-8">
        <input type="text" name="search" class="form-control" placeholder="Search by name or email" value="{{ $search ?? '' }}">
    </div>
    <div class="col-md-4 d-flex gap-2">
        <button type="submit" class="btn btn-outline-primary">Search</button>
        @if(!empty($search))
            <a href="{{ route('superadmin.dashboard') }}" class="btn btn-outline-secondary">Clear</a>
        @endif
    </div>
</form>

<div class="table-responsive">
    @if(($teachers ?? collect())->isEmpty())
        <div class="alert alert-info">No teachers found.</div>
    @else
    <table class="table table-bordered">
        <thead>
            <tr>
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
                        <a class="btn btn-sm btn-primary" href="{{ route('superadmin.teachers.edit', $teacher) }}">Edit</a>
                        <form action="{{ route('superadmin.teachers.destroy', $teacher) }}" method="POST" class="d-inline-block" onsubmit="return confirm('Delete this teacher?');">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-danger">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    @endif
</div>
@endsection
