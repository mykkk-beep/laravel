@extends('layouts.app')

@section('showNavigation', 'false')

@section('content')
<div class="container">
    <h2>Parent Portal Login</h2>

    @if($errors->any())
        <div class="alert alert-danger">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('parent.login.submit') }}">
        @csrf
        <div class="mb-3">
            <label for="student_id" class="form-label">Student ID</label>
            <input id="student_id" name="student_id" class="form-control" required />
            <div class="form-text">Use your child&apos;s student ID to view attendance and teacher notifications.</div>
        </div>
        <button class="btn btn-primary">Login</button>
    </form>
</div>
@endsection
