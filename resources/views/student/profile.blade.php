@extends('layouts.app')

@section('showNavigation', 'false')

@section('content')
<div class="container-fluid px-3 px-md-4 py-3 py-md-4">
    <a href="{{ route('student.dashboard') }}" class="btn btn-sm btn-link mb-3 text-start ps-0">&larr; Back to Dashboard</a>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0 h5 h4-md">Student Profile</h4>
        </div>
        <div class="card-body p-3 p-md-4">
            <!-- Personal Information Section -->
            <div class="row mb-4">
                <div class="col-12 col-md-6 mb-4 mb-md-0">
                    <h5 class="border-bottom pb-2 mb-3 h6 h5-md">Personal Information</h5>
                    <div class="info-group mb-2">
                        <strong>Name:</strong>
                        <p class="text-muted mb-0">{{ $student->name }}</p>
                    </div>
                    <div class="info-group mb-2">
                        <strong>Email:</strong>
                        <p class="text-muted mb-0 text-break">{{ $student->email ?? 'Not provided' }}</p>
                    </div>
                    <div class="info-group mb-2">
                        <strong>Gender:</strong>
                        <p class="text-muted mb-0">{{ $student->sex ?? 'Not provided' }}</p>
                    </div>
                </div>
                <div class="col-12 col-md-6">
                    <h5 class="border-bottom pb-2 mb-3 h6 h5-md">Academic Information</h5>
                    <div class="info-group mb-2">
                        <strong>Student ID:</strong>
                        <p class="text-muted mb-0"><code class="bg-light p-1 rounded small">{{ $student->student_id }}</code></p>
                    </div>
                    <div class="info-group mb-2">
                        <strong>Primary Class:</strong>
                        <p class="text-muted mb-0">{{ $student->classRoom?->name ?? 'Not assigned' }}</p>
                    </div>
                    <div class="info-group mb-2">
                        <strong>Enrollment Status:</strong>
                        <p class="mb-0">
                            <span class="badge @if($student->status === 'enrolled') bg-success @elseif($student->status === 'pending') bg-warning @else bg-danger @endif">
                                {{ ucfirst($student->status ?? 'Not specified') }}
                            </span>
                        </p>
                    </div>
                    <div class="info-group mb-0">
                        <strong>Total Enrollments:</strong>
                        <p class="mb-0"><span class="badge bg-info">{{ $student->enrollments->count() }}</span></p>
                    </div>
                </div>
            </div>

            <hr class="my-4">

            <!-- QR Code Section -->
            <div class="row mb-4">
                <div class="col-12">
                    <h5 class="mb-3 h6 h5-md">QR Code for Attendance</h5>
                    <div class="d-flex justify-content-center p-3 p-md-4 bg-light rounded border">
                        <div class="text-center">
                            <div class="mb-3">
                                <img src="https://api.qrserver.com/v1/create-qr-code/?size=300x300&data={{ $student->student_id }}" 
                                     alt="Student QR Code" 
                                     class="border border-2 p-2 p-md-3 bg-white rounded img-fluid" 
                                     style="max-width: 100%; width: 250px; height: 250px;">
                            </div>
                            <p class="text-muted mb-2"><small><strong>Student ID:</strong> {{ $student->student_id }}</small></p>
                            <p class="text-muted small">📱 Scan this QR code for attendance marking or verification</p>
                        </div>
                    </div>
                </div>
            </div>

            <hr class="my-4">

            <!-- Enrolled Classes Section -->
            <div class="row">
                <div class="col-12">
                    <h5 class="mb-3 h6 h5-md">
                        <i class="fas fa-book"></i> Enrolled Classes 
                        @if($student->enrollments->count() > 0)
                            <span class="badge bg-primary">{{ $student->enrollments->count() }}</span>
                        @endif
                    </h5>
                    
                    @if($student->enrollments->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover table-sm">
                                <thead class="table-light">
                                    <tr>
                                        <th scope="col" class="small">Class Name</th>
                                        <th scope="col" class="small">Status</th>
                                        <th scope="col" class="small d-none d-lg-table-cell">Grade</th>
                                        <th scope="col" class="small d-none d-lg-table-cell">Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($student->enrollments as $enrollment)
                                        <tr>
                                            <td class="small">
                                                <strong class="d-block">{{ $enrollment->classRoom?->name ?? 'Unknown Class' }}</strong>
                                                <span class="d-md-none text-muted">{{ $enrollment->subject?->name ?? 'Not assigned' }}</span>
                                            </td>
                                            <td class="small d-none d-md-table-cell">
                                                {{ $enrollment->subject?->name ?? 'Not assigned' }}
                                            </td>
                                            <td class="small">
                                                <span class="badge badge-sm @if($enrollment->status === 'enrolled') bg-success @elseif($enrollment->status === 'pending') bg-warning text-dark @else bg-secondary @endif">
                                                    {{ ucfirst($enrollment->status) }}
                                                </span>
                                            </td>
                                            <td class="small d-none d-lg-table-cell">
                                                @if($enrollment->grade)
                                                    <span class="badge bg-info">{{ $enrollment->grade }}</span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td class="small d-none d-lg-table-cell">
                                                <small class="text-muted">{{ $enrollment->created_at->format('M d, Y') }}</small>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-warning" role="alert">
                            <i class="fas fa-info-circle"></i> You are not enrolled in any classes yet. Please contact your teacher or administration for enrollment.
                        </div>
                    @endif
                </div>
            </div>

            <hr class="my-4">

            <!-- Information Alert -->
            <div class="alert alert-info mb-0" role="alert">
                <i class="fas fa-lightbulb"></i> <strong>Note:</strong> For any profile updates or issues, please contact your teacher or the administration.
            </div>
        </div>
    </div>
</div>

<style>
    .info-group {
        display: flex;
        flex-direction: column;
    }
    
    .info-group strong {
        display: block;
        margin-bottom: 0.25rem;
    }
    
    .table-hover tbody tr:hover {
        background-color: #f5f5f5;
    }
    
    code {
        background-color: #f4f4f4;
        padding: 2px 6px;
        border-radius: 3px;
        font-size: 0.875rem;
    }
    
    @media (max-width: 576px) {
        .h4-md {
            font-size: 1.25rem !important;
        }
        .h5-md {
            font-size: 1rem !important;
        }
        .table-sm {
            font-size: 0.8rem;
        }
        .badge-sm {
            font-size: 0.65rem;
            padding: 0.3rem 0.5rem;
        }
    }
    
    @media (min-width: 577px) {
        .h4-md {
            font-size: 1.5rem !important;
        }
        .h5-md {
            font-size: 1.25rem !important;
        }
    }
</style>
@endsection
