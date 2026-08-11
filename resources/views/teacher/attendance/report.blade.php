@extends('layouts.app')

@section('pageTitle', 'Attendance Summary')
@section('pageSubtitle', 'Review the selected attendance report details.')

@section('content')
<div class="flex flex-col gap-4 rounded-[28px] border border-slate-200/80 bg-white/90 p-6 shadow-sm lg:flex-row lg:items-center lg:justify-between">
    <div>
        <h2 class="text-2xl font-semibold text-slate-900">Attendance Summary</h2>
        <p class="mt-1 text-sm text-slate-500">Generate and review attendance reports for the selected filters.</p>
    </div>
    <div class="flex flex-wrap gap-2">
        <a href="{{ route('teacher.attendance.records', request()->query()) }}" class="btn btn-outline-secondary">Back to Records</a>
        <a href="{{ route('teacher.attendance.export', array_merge(request()->query(), ['format' => 'excel'])) }}" class="btn btn-success">Export Excel</a>
        <a href="{{ route('teacher.attendance.export', array_merge(request()->query(), ['format' => 'pdf'])) }}" class="btn btn-danger">Export PDF</a>
        <button type="button" class="btn btn-outline-secondary" onclick="window.print()">Print</button>
    </div>
</div>

<div class="grid gap-4 md:grid-cols-4">
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

<div class="card">
    <div class="card-body">
        @if($records->isEmpty())
            <div class="rounded-2xl border border-dashed border-slate-200 bg-slate-50 p-4 text-sm text-slate-500">No attendance records found for the selected report.</div>
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
                                <td>{{ optional($record->student->classRoom)->name }}</td>
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
