@extends('layouts.app')

@section('showNavigation', 'false')

@section('content')
<div class="mx-auto max-w-6xl px-1 py-2 sm:px-0">
    <a href="{{ route('student.dashboard') }}" class="mb-4 inline-flex items-center gap-2 text-sm font-semibold text-indigo-600 transition hover:text-indigo-800">
        <span aria-hidden="true">&larr;</span> Back to Dashboard
    </a>

    <div class="card overflow-hidden">
        <div class="bg-gradient-to-r from-slate-950 to-indigo-800 px-5 py-6 text-white sm:px-8">
            <p class="mb-1 text-xs font-semibold uppercase tracking-[0.16em] text-indigo-200">Account overview</p>
            <h1 class="text-2xl font-semibold tracking-tight sm:text-3xl">Student Profile</h1>
        </div>
        <div class="p-5 sm:p-8">
            <!-- Personal Information Section -->
            <div class="grid gap-8 md:grid-cols-2">
                <div>
                    <h2 class="mb-4 border-b border-slate-200 pb-3 text-base font-semibold text-slate-900">Personal Information</h2>
                    <div class="space-y-4">
                    <div class="info-group">
                        <strong>Name:</strong>
                        <p class="mb-0 text-sm text-slate-500">{{ $student->name }}</p>
                    </div>
                    <div class="info-group">
                        <strong>Email:</strong>
                        <p class="mb-0 break-words text-sm text-slate-500">{{ $student->email ?? 'Not provided' }}</p>
                    </div>
                    <div class="info-group">
                        <strong>Gender:</strong>
                        <p class="mb-0 text-sm text-slate-500">{{ $student->sex ?? 'Not provided' }}</p>
                    </div>
                    </div>
                </div>
                <div>
                    <h2 class="mb-4 border-b border-slate-200 pb-3 text-base font-semibold text-slate-900">Academic Information</h2>
                    <div class="space-y-4">
                    <div class="info-group">
                        <strong>Student ID:</strong>
                        <p class="mb-0 text-sm text-slate-500"><code>{{ $student->student_id }}</code></p>
                    </div>
                    <div class="info-group">
                        <strong>Primary Class:</strong>
                        <p class="mb-0 text-sm text-slate-500">{{ $student->classRoom?->name ?? 'Not assigned' }}</p>
                    </div>
                    <div class="info-group">
                        <strong>Enrollment Status:</strong>
                        <p class="mb-0">
                            <span class="badge @if($student->status === 'enrolled') bg-success @elseif($student->status === 'pending') bg-warning @else bg-danger @endif">
                                {{ ucfirst($student->status ?? 'Not specified') }}
                            </span>
                        </p>
                    </div>
                    <div class="info-group">
                        <strong>Total Enrollments:</strong>
                        <p class="mb-0"><span class="badge bg-indigo-100 text-indigo-700">{{ $student->enrollments->count() }}</span></p>
                    </div>
                    </div>
                </div>
            </div>

            <hr class="my-8 border-slate-200">

            <!-- QR Code Section -->
            <div class="mb-8">
                    <h2 class="mb-4 text-base font-semibold text-slate-900">QR Code for Attendance</h2>
                    <div class="flex justify-center rounded-3xl border border-slate-200 bg-slate-50 p-5 sm:p-8">
                        <div class="text-center">
                            <div class="mb-3">
                                <img src="https://api.qrserver.com/v1/create-qr-code/?size=300x300&data={{ $student->student_id }}" 
                                     alt="Student QR Code" 
                                     class="rounded-2xl border-2 border-slate-200 bg-white p-2 sm:p-3" 
                                     style="max-width: 100%; width: 250px; height: 250px;">
                            </div>
                            <p class="mb-2 text-sm text-slate-500"><strong>Student ID:</strong> {{ $student->student_id }}</p>
                            <p class="text-sm text-slate-500">Scan this QR code for attendance marking or verification</p>
                        </div>
                    </div>
            </div>

            <hr class="my-8 border-slate-200">

            <!-- Enrolled Classes Section -->
            <div>
                    <h2 class="mb-4 text-base font-semibold text-slate-900">
                        <i class="fas fa-book"></i> Enrolled Classes 
                        @if($student->enrollments->count() > 0)
                            <span class="badge bg-indigo-100 text-indigo-700">{{ $student->enrollments->count() }}</span>
                        @endif
                    </h2>
                    
                    @if($student->enrollments->count() > 0)
                        <div class="table-responsive overflow-hidden rounded-2xl border border-slate-200">
                            <table class="w-full text-left text-sm">
                                <thead class="bg-slate-50 text-xs uppercase tracking-[0.08em] text-slate-500">
                                    <tr>
                                        <th scope="col" class="px-4 py-3 font-semibold">Class Name</th>
                                        <th scope="col" class="hidden px-4 py-3 font-semibold lg:table-cell">Status</th>
                                        <th scope="col" class="hidden px-4 py-3 font-semibold lg:table-cell">Grade</th>
                                        <th scope="col" class="hidden px-4 py-3 font-semibold lg:table-cell">Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($student->enrollments as $enrollment)
                                        <tr>
                                            <td class="px-4 py-3 align-top text-slate-900">
                                                <strong class="block">{{ $enrollment->classRoom?->name ?? 'Unknown Class' }}</strong>
                                                <span class="text-xs text-slate-500 lg:hidden">{{ $enrollment->subject?->name ?? 'Not assigned' }}</span>
                                            </td>
                                            <td class="px-4 py-3">
                                                <span class="badge badge-sm @if($enrollment->status === 'enrolled') bg-success @elseif($enrollment->status === 'pending') bg-warning text-dark @else bg-secondary @endif">
                                                    {{ ucfirst($enrollment->status) }}
                                                </span>
                                            </td>
                                            <td class="hidden px-4 py-3 lg:table-cell">
                                                @if($enrollment->grade)
                                                    <span class="badge bg-indigo-100 text-indigo-700">{{ $enrollment->grade }}</span>
                                                @else
                                                    <span class="text-slate-400">-</span>
                                                @endif
                                            </td>
                                            <td class="hidden px-4 py-3 text-slate-500 lg:table-cell">
                                                {{ $enrollment->created_at->format('M d, Y') }}
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

            <hr class="my-8 border-slate-200">

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
        color: #334155;
        font-size: 0.875rem;
        font-weight: 600;
    }

    tbody tr {
        border-top: 1px solid #e2e8f0;
    }

    tbody tr:hover {
        background-color: #f8fafc;
    }
    
    code {
        background-color: #eef2ff;
        color: #4338ca;
        padding: 0.25rem 0.5rem;
        border-radius: 0.5rem;
        font-size: 0.75rem;
        font-weight: 700;
    }
</style>
@endsection
