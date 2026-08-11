@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Student Login</h2>

    @if($errors->any())
        <div class="alert alert-danger">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('student.login.submit') }}">
        @csrf
        <div class="mb-3">
            <label for="student_id" class="form-label">Student ID</label>
            <input id="student_id" name="student_id" class="form-control" required />
        </div>
        <button class="btn btn-primary">Login</button>
    </form>
</div>
@endsection
