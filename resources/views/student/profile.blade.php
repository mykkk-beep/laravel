@extends('layouts.app')

@section('showNavigation', 'false')

@section('content')
<div class="container mt-4">
    <a href="{{ route('student.dashboard') }}" class="btn btn-link mb-3">&larr; Back to Dashboard</a>

    <div class="card">
        <div class="card-header">
            <h4>Student Profile</h4>
        </div>
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-6">
                    <h5>Contact Information</h5>
                    <p><strong>Name:</strong> {{ $student->name }}</p>
                    <p><strong>Email:</strong> {{ $student->email ?? 'Not provided' }}</p>
                    <p><strong>Mobile:</strong> {{ $student->mobile ?? 'Not provided' }}</p>
                    <p><strong>Gender:</strong> {{ $student->sex ?? 'Not provided' }}</p>
                </div>
                <div class="col-md-6">
                    <div>
                        <h5>Academic Information</h5>
                        <p><strong>Student ID:</strong> {{ $student->student_id }}</p>
                        <p><strong>Class:</strong> {{ $student->classRoom?->name ?? 'Not assigned' }}</p>
                        <p><strong>Status:</strong> 
                            <span class="badge @if($student->status === 'enrolled') bg-success @elseif($student->status === 'pending') bg-warning @else bg-danger @endif">
                                {{ ucfirst($student->status ?? 'Not specified') }}
                            </span>
                        </p>
                    </div>
                </div>
            </div>

            <hr>
            <div class="row mb-4">
                <div class="col-md-12">
                    <h5>QR Code for Attendance</h5>
                    <div class="d-flex justify-content-center p-4 bg-light rounded">
                        <div class="text-center">
                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=250x250&data={{ $student->student_id }}" alt="Student QR Code" class="border p-2" style="max-width: 300px;">
                            <p class="text-muted mt-3 small">Scan this code for attendance or verification</p>
                        </div>
                    </div>
                </div>
            </div>

            @if($student->enrollments->count() > 0)
                <hr>
                <h5>Course Enrollments</h5>
                <div class="row">
                    @foreach($student->enrollments as $enrollment)
                        <div class="col-md-4 mb-3">
                            <div class="card">
                                <div class="card-body">
                                    <h6 class="card-title">{{ $enrollment->classRoom?->name }}</h6>
                                    <p class="card-text text-sm">Status: 
                                        <span class="badge @if($enrollment->status === 'enrolled') bg-success @else bg-secondary @endif">
                                            {{ ucfirst($enrollment->status) }}
                                        </span>
                                    </p>
                                    <small class="text-muted">Enrolled: {{ $enrollment->created_at->format('M d, Y') }}</small>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            <hr>
            <div class="alert alert-info" role="alert">
                <strong>Note:</strong> For any profile updates or issues, please contact your teacher or the administration.
            </div>
        </div>
    </div>
</div>
@endsection
