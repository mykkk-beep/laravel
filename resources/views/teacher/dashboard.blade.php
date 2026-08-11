@extends('layouts.app')

@section('pageTitle', 'Teacher Dashboard')
@section('pageSubtitle', 'Overview for ' . $today)

@section('content')
<div class="space-y-6">
    <div class="flex flex-col gap-4 rounded-[28px] border border-slate-200/80 bg-white/90 p-6 shadow-sm lg:flex-row lg:items-center lg:justify-between">
        <div>
            <h2 class="text-2xl font-semibold text-slate-900">Teacher Dashboard</h2>
            <p class="mt-1 text-sm text-slate-500">Track attendance, classes, and student needs from one place.</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a class="btn btn-primary" href="{{ route('teacher.attendance.scan') }}">Scan Attendance</a>
            <a class="btn btn-success" href="{{ route('teacher.students.create') }}">Add Student</a>
            <a class="btn btn-outline-secondary" href="{{ route('teacher.attendance.records') }}">View History</a>
        </div>
    </div>

    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
        <div class="card">
            <div class="card-body">
                <div class="text-sm font-medium text-slate-500">Today's Classes</div>
                <div class="mt-3 text-3xl font-semibold text-slate-900">{{ $todayClasses->count() }}</div>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <div class="text-sm font-medium text-slate-500">Number of Classes</div>
                <div class="mt-3 text-3xl font-semibold text-slate-900">{{ $classes->count() }}</div>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <div class="text-sm font-medium text-slate-500">Total Students</div>
                <div class="mt-3 text-3xl font-semibold text-slate-900">{{ $totalStudents }}</div>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <div class="text-sm font-medium text-slate-500">Total Enrolled Students</div>
                <div class="mt-3 text-3xl font-semibold text-slate-900">{{ $totalEnrolledStudents }}</div>
            </div>
        </div>
    </div>

    <div class="grid gap-6 xl:grid-cols-[0.95fr_1.05fr]">
        <div class="card">
            <div class="card-body">
                <h3 class="card-title">Today's Attendance Summary</h3>
                <div class="grid grid-cols-3 gap-4 text-center">
                    <div>
                        <div class="text-3xl font-semibold text-emerald-600">{{ $presentCount }}</div>
                        <div class="text-sm text-slate-500">Present</div>
                    </div>
                    <div>
                        <div class="text-3xl font-semibold text-amber-600">{{ $lateCount }}</div>
                        <div class="text-sm text-slate-500">Late</div>
                    </div>
                    <div>
                        <div class="text-3xl font-semibold text-rose-600">{{ $absentCount }}</div>
                        <div class="text-sm text-slate-500">Absent</div>
                    </div>
                </div>
                <div class="mt-5 rounded-2xl border border-slate-200 bg-slate-50 p-4">
                    <div class="text-sm text-slate-500">Attendance Rate</div>
                    <div class="mt-1 text-2xl font-semibold text-slate-900">{{ $attendanceRate }}%</div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <h3 class="card-title">Recent Attendance Activities</h3>
                <ul class="space-y-3">
                    @forelse($recentActivities as $activity)
                        <li class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                            <div class="flex items-center justify-between gap-3">
                                <span class="font-semibold text-slate-900">{{ $activity->student->name ?? 'Unknown student' }}</span>
                                <span class="rounded-full px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em] {{ $activity->status === 'present' ? 'bg-emerald-100 text-emerald-700' : ($activity->status === 'late' ? 'bg-amber-100 text-amber-700' : 'bg-slate-100 text-slate-700') }}">{{ ucfirst($activity->status) }}</span>
                            </div>
                            <div class="mt-1 text-sm text-slate-500">{{ $activity->date }} • {{ $activity->time_in ?? '—' }}</div>
                        </li>
                    @empty
                        <li class="rounded-2xl border border-dashed border-slate-200 bg-slate-50 p-4 text-sm text-slate-500">No recent attendance activity.</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>

    <div class="grid gap-6 xl:grid-cols-2">
        <div class="card">
            <div class="card-body">
                <h3 class="card-title">Upcoming Class Schedule</h3>
                <ul class="space-y-3">
                    @forelse($upcomingClasses as $class)
                        <li class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                            <div class="flex items-center justify-between gap-3">
                                <strong class="text-slate-900">{{ $class->name }}</strong>
                                <span class="rounded-full bg-indigo-100 px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em] text-indigo-700">{{ $class->date }}</span>
                            </div>
                            <div class="mt-1 text-sm text-slate-500">{{ $class->classroom }} • {{ $class->time }}{{ $class->end_time ? ' - '.$class->end_time : '' }}</div>
                        </li>
                    @empty
                        <li class="rounded-2xl border border-dashed border-slate-200 bg-slate-50 p-4 text-sm text-slate-500">No upcoming classes.</li>
                    @endforelse
                </ul>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <h3 class="card-title">Students Needing Recommendations</h3>
                <ul class="space-y-3">
                    @forelse($recommendations as $student)
                        <li class="flex items-center justify-between gap-3 rounded-2xl border border-slate-200 bg-slate-50 p-4">
                            <span class="font-semibold text-slate-900">{{ $student->name }}</span>
                            <a href="{{ route('teacher.recommendations') }}" class="btn btn-sm btn-outline-primary">View</a>
                        </li>
                    @empty
                        <li class="rounded-2xl border border-dashed border-slate-200 bg-slate-50 p-4 text-sm text-slate-500">No students need recommendations right now.</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <h3 class="card-title">Attendance Graph (Last 7 Days)</h3>
            <canvas id="attendanceChart" height="120"></canvas>
        </div>
    </div>
</div>

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        (function() {
            const ctx = document.getElementById('attendanceChart').getContext('2d');
            const data = @json($dailyAttendance ?? ['labels'=>[], 'present'=>[], 'absent'=>[]]);

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: data.labels,
                    datasets: [
                        {
                            label: 'Present',
                            data: data.present,
                            borderColor: 'rgba(16,185,129,1)',
                            backgroundColor: 'rgba(16,185,129,0.15)',
                            fill: true,
                        },
                        {
                            label: 'Absent',
                            data: data.absent,
                            borderColor: 'rgba(248,113,113,1)',
                            backgroundColor: 'rgba(248,113,113,0.15)',
                            fill: true,
                        }
                    ]
                },
                options: {
                    responsive: true,
                    scales: {
                        x: { display: true },
                        y: { beginAtZero: true, precision: 0 }
                    }
                }
            });
        })();
    </script>
@endpush
@endsection
