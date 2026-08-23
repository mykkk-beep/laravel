@extends('layouts.app')

@section('showNavigation', 'false')

@section('content')
<div class="mx-auto max-w-6xl px-1 py-2 sm:px-0">
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="mb-1 text-xs font-semibold uppercase tracking-[0.16em] text-indigo-600">Family overview</p>
            <h1 class="text-2xl font-semibold tracking-tight text-slate-900 sm:text-3xl">Parent Portal</h1>
            <p class="mt-1 text-sm text-slate-500">Attendance updates for {{ $student->name }} <span class="text-slate-400">({{ $student->student_id }})</span></p>
        </div>
        <div class="flex w-full flex-col gap-2 sm:w-auto sm:flex-row">
            <a href="{{ route('parent.notifications') }}" class="relative inline-flex min-h-11 items-center justify-center rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700">
                <span aria-hidden="true">&#128276;</span><span class="ml-2">Notifications</span>
                @if(!empty($notificationCount) && $notificationCount > 0)
                    <span class="absolute -right-2 -top-2 flex h-5 min-w-5 items-center justify-center rounded-full bg-rose-500 px-1 text-xs font-bold text-white">{{ $notificationCount }}</span>
                @endif
            </a>
            <form method="POST" action="{{ route('parent.logout') }}" class="w-full sm:w-auto">@csrf<button class="min-h-11 w-full rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50 sm:w-auto">Logout</button></form>
        </div>
    </div>

    <div class="grid gap-4 lg:grid-cols-[minmax(0,1.35fr)_minmax(280px,0.65fr)]">
        <div class="card overflow-hidden">
            <div class="border-b border-slate-100 bg-gradient-to-r from-slate-950 to-indigo-800 px-5 py-5 text-white sm:px-7">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.16em] text-indigo-200">Attendance overview</p>
                        <h2 class="mt-1 text-xl font-semibold">Today and recent activity</h2>
                    </div>
                    <span class="rounded-full bg-white/10 px-3 py-1 text-xs font-semibold text-indigo-100">Latest updates</span>
                </div>
            </div>
            <div class="p-5 sm:p-7">

                    @if($attendance)
                        <div class="mb-6 rounded-2xl border p-4 {{ $attendance->status === 'present' ? 'border-emerald-200 bg-emerald-50 text-emerald-900' : ($attendance->status === 'late' ? 'border-amber-200 bg-amber-50 text-amber-900' : 'border-rose-200 bg-rose-50 text-rose-900') }}">
                            <div class="font-semibold">Today: {{ ucfirst($attendance->status) }}</div>
                            <div class="mt-1 text-sm opacity-75">Recorded at {{ $attendance->time_in ?? $attendance->updated_at->format('H:i') }}</div>
                            @if($attendance->notes)
                                <div class="mt-2 text-sm">Teacher note: {{ $attendance->notes }}</div>
                            @endif
                        </div>
                    @else
                        <div class="mb-6 rounded-2xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-900">No attendance recorded yet for today.</div>
                    @endif

                    <h3 class="mb-3 text-sm font-semibold uppercase tracking-[0.08em] text-slate-500">Recent attendance history</h3>
                    <div class="divide-y divide-slate-100">
                        @forelse($recentAttendance as $record)
                            <div class="flex items-center justify-between gap-3 py-4 first:pt-0 last:pb-0">
                                <div>
                                    <span class="block font-semibold text-slate-900">{{ \Carbon\Carbon::parse($record->date)->format('M d, Y') }}</span>
                                    <span class="text-sm text-slate-500">{{ $record->time_in ?? 'No time recorded' }}</span>
                                </div>
                                <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $record->status === 'present' ? 'bg-emerald-100 text-emerald-700' : ($record->status === 'late' ? 'bg-amber-100 text-amber-700' : 'bg-rose-100 text-rose-700') }}">{{ ucfirst($record->status) }}</span>
                            </div>
                        @empty
                            <div class="py-4 text-sm text-slate-500">No attendance history yet.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

        <div class="card h-fit overflow-hidden">
            <div class="border-b border-slate-100 px-5 py-5 sm:px-6">
                <p class="text-xs font-semibold uppercase tracking-[0.16em] text-indigo-600">Your child&apos;s day</p>
                <h2 class="mt-1 text-xl font-semibold text-slate-900">Current class schedule</h2>
            </div>
            <div class="p-5 sm:p-6">
                    <div>
                        @if($student->classRoom)
                            <div class="rounded-2xl bg-slate-50 p-4">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="min-w-0">
                                        <strong class="block text-slate-900">{{ $student->classRoom->name }}</strong>
                                        <div class="mt-1 text-sm text-slate-500">{{ $student->classRoom->classroom ?? '' }}</div>
                                    </div>
                                    <span class="shrink-0 rounded-full bg-slate-200 px-2.5 py-1 text-xs font-semibold text-slate-600">{{ $student->classRoom->date ?? '' }}</span>
                                </div>
                                <div class="mt-4 border-t border-slate-200 pt-3 text-sm text-slate-500">
                                    {{ $student->classRoom->time ?? '' }}
                                    @if(!empty($student->classRoom->end_time))
                                        - {{ $student->classRoom->end_time }}
                                    @endif
                                </div>
                            </div>
                        @else
                            <div class="rounded-2xl border border-dashed border-slate-200 p-5 text-sm text-slate-500">No schedule available.</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
