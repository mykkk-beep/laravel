@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-body">
        <h2>Edit Teacher</h2>
        <form method="POST" action="{{ route('superadmin.teachers.update', $teacher) }}">
            @csrf
            @method('PUT')
            <div class="mb-3">
                <label class="form-label">Name</label>
                <input type="text" name="name" class="form-control" value="{{ old('name', $teacher->name) }}" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" value="{{ old('email', $teacher->email) }}" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Password (leave blank to keep current)</label>
                <input type="password" name="password" class="form-control">
            </div>
            <div class="mb-3">
                <label class="form-label">Confirm Password</label>
                <input type="password" name="password_confirmation" class="form-control">
            </div>
            <div class="mb-3">
                <label class="form-label">Account Status</label>
                <select name="status" class="form-select">
                    <option value="active" {{ $teacher->status === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="disabled" {{ $teacher->status === 'disabled' ? 'selected' : '' }}>Disabled</option>
                    <option value="pending" {{ $teacher->status === 'pending' ? 'selected' : '' }}>Pending</option>
                </select>
                @if ($teacher->status === 'pending')
                    <div class="form-text text-warning">Account is pending, wait for the administrator verification.</div>
                @endif
            </div>
            <button class="btn btn-primary">Update Teacher</button>
        </form>
    </div>
</div>
@endsection
