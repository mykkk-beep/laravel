@extends('layouts.app')

@section('pageTitle', 'Attendance Summary')
@section('pageSubtitle', 'Review the selected attendance report details.')

@section('content')
<div class="no-print flex flex-col gap-4 rounded-[28px] border border-slate-200/80 bg-white/90 p-6 shadow-sm lg:flex-row lg:items-center lg:justify-between">
    <div>
        <h2 class="text-2xl font-semibold text-slate-900">Attendance Summary</h2>
        <p class="mt-1 text-sm text-slate-500">Generate and review attendance reports for the selected filters.</p>
    </div>
    <div class="flex flex-wrap gap-2">
        <a href="{{ route('teacher.attendance.report', request()->query()) }}" class="btn btn-primary">Generate Report</a>
        <a href="{{ route('teacher.attendance.export', array_merge(request()->query(), ['format' => 'excel'])) }}" class="btn btn-success">Export Excel</a>
        <a href="{{ route('teacher.attendance.export', array_merge(request()->query(), ['format' => 'pdf'])) }}" class="btn btn-danger">Export PDF</a>
        <button type="button" class="btn btn-outline-secondary" onclick="window.print()">Print</button>
    </div>
</div>

<div class="no-print grid gap-4 md:grid-cols-4">
    <div class="card">
        <div class="card-body">
            <div class="text-sm font-medium text-slate-500">Total Entries</div>
            <div class="mt-2 text-2xl font-semibold text-slate-900">{{ $summary['total'] }}</div>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="text-sm font-medium text-slate-500">Present</div>
            <div class="mt-2 text-2xl font-semibold text-emerald-600">{{ $summary['present'] }}</div>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="text-sm font-medium text-slate-500">Absent</div>
            <div class="mt-2 text-2xl font-semibold text-rose-600">{{ $summary['absent'] }}</div>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="text-sm font-medium text-slate-500">Attendance Rate</div>
            <div class="mt-2 text-2xl font-semibold text-slate-900">{{ $summary['attendance_rate'] }}%</div>
        </div>
    </div>
</div>

<div class="no-print card">
    <div class="card-body">
        <form method="GET" action="{{ route('teacher.attendance.records') }}" class="grid gap-3 md:grid-cols-5 md:items-end">
            <div>
                <label class="form-label">Class</label>
                <select name="class_room_id" class="form-input">
                    <option value="">All classes</option>
                    @foreach($classes as $class)
                        <option value="{{ $class->id }}" {{ request('class_room_id') == $class->id ? 'selected' : '' }}>{{ $class->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="form-label">Status</label>
                <select name="status" class="form-input">
                    <option value="">All statuses</option>
                    <option value="present" {{ request('status') == 'present' ? 'selected' : '' }}>Present</option>
                    <option value="late" {{ request('status') == 'late' ? 'selected' : '' }}>Late</option>
                    <option value="absent" {{ request('status') == 'absent' ? 'selected' : '' }}>Absent</option>
                </select>
            </div>
            <div>
                <button type="submit" class="btn btn-primary w-full">Filter</button>
            </div>
        </form>
        @if(isset($weekDays))
            <div class="mt-6 flex flex-wrap gap-2">
                @foreach($weekDays as $day)
                    <a href="{{ route('teacher.attendance.records', array_merge(request()->query(), ['date' => $day['date']])) }}" class="inline-flex items-center rounded-full border px-4 py-2 text-sm font-semibold transition {{ $day['is_selected'] ? 'border-indigo-600 bg-indigo-600 text-white' : 'border-slate-200 bg-white text-slate-700 hover:border-slate-300 hover:bg-slate-50' }}">
                        <span class="mr-2 font-semibold">{{ $day['label'] }}</span>
                        <span>{{ $day['date'] }}</span>
                    </a>
                @endforeach
            </div>
        @endif
    </div>
</div>

@if(isset($studentStatuses) && $studentStatuses->isNotEmpty())
    <div class="card">
        <div class="card-body">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-slate-900">{{ $selectedClass?->name ?? 'Selected Class' }} — {{ \Carbon\Carbon::parse($selectedDate)->format('l, F j, Y') }}</h3>
                    <p class="text-sm text-slate-500">Daily status for every student in the selected class.</p>
                </div>
                <div class="rounded-full border border-slate-200 bg-slate-50 px-4 py-2 text-sm text-slate-600">
                    {{ $studentStatuses->count() }} students
                </div>
            </div>

            <div class="mt-4 overflow-x-auto">
                <table class="table w-full">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Student ID</th>
                            <th>Name</th>
                            <th>AM</th>
                            <th>PM</th>
                            <th>Notes</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($studentStatuses as $index => $status)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $status['student_id'] }}</td>
                                <td>{{ $status['name'] }}</td>
                                @foreach(['am', 'pm'] as $period)
                                    <td>
                                        <span class="rounded-full px-3 py-1 text-xs font-semibold uppercase tracking-[0.1em] {{ $status[$period] === 'present' ? 'bg-emerald-100 text-emerald-700' : ($status[$period] === 'late' ? 'bg-amber-100 text-amber-700' : 'bg-rose-100 text-rose-700') }}">
                                            {{ ucfirst($status[$period]) }}
                                        </span>
                                    </td>
                                @endforeach
                                <td>{{ $status['notes'] ?? '—' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endif

@if(isset($studentStatuses) && $studentStatuses->isNotEmpty())
    <div class="print-only">
        <h1>Attendance Report</h1>
        <p>{{ $selectedClass?->name ?? 'Selected Class' }} — {{ \Carbon\Carbon::parse($selectedDate)->format('l, F j, Y') }}</p>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Student ID</th>
                    <th>Student</th>
                    <th>AM</th>
                    <th>PM</th>
                </tr>
            </thead>
            <tbody>
                @foreach($studentStatuses as $index => $status)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $status['student_id'] }}</td>
                        <td>{{ $status['name'] }}</td>
                        <td>{{ ucfirst($status['am']) }}</td>
                        <td>{{ ucfirst($status['pm']) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif

<style>
    .print-only { display: none; }

    @media print {
        .no-print { display: none !important; }
        .print-only { display: block; }
        .print-only h1 { margin: 0 0 8px; }
        .print-only p { margin: 0 0 20px; }
        .print-only table { width: 100%; border-collapse: collapse; }
        .print-only th, .print-only td { border: 1px solid #334155; padding: 8px; text-align: left; }
        .print-only th { background: #e2e8f0; }
    }
</style>

<div class="no-print card">
    <div class="card-body">
        @if($records->isEmpty())
            <div class="rounded-2xl border border-dashed border-slate-200 bg-slate-50 p-4 text-sm text-slate-500">No attendance records found.</div>
        @else
            <div class="overflow-x-auto">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Student</th>
                            <th>Class</th>
                            <th>QR Code</th>
                            <th>Status</th>
                            <th>Scanned At</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($records as $record)
                            <tr>
                                <td>{{ $record->date->format('Y-m-d') }}</td>
                                <td>{{ $record->student->name }}</td>
                                <td>{{ $record->student->classRoom->name }}</td>
                                <td><code class="rounded bg-slate-100 px-2 py-1 text-xs">{{ $record->qr_code }}</code></td>
                                <td>{{ ucfirst($record->status) }}</td>
                                <td>{{ $record->created_at->format('Y-m-d H:i:s') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection
