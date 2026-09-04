@extends('layouts.app')

@section('showNavigation', 'false')

@section('content')
<div class="mx-auto w-full max-w-7xl px-3 py-4 sm:px-4 sm:py-6 md:px-6">
    <!-- Header Section -->
    <div class="mb-6 flex flex-col gap-3 sm:gap-4">
        <div>
            <p class="mb-1 text-xs font-semibold uppercase tracking-[0.16em] text-indigo-600">Student overview</p>
            <h1 class="text-xl font-bold tracking-tight text-slate-900 sm:text-2xl md:text-3xl">Student Portal</h1>
            <p class="mt-2 text-xs sm:text-sm text-slate-500">Welcome, <span class="font-semibold text-slate-700">{{ $student->name }}</span> <span class="text-slate-400">({{ $student->student_id }})</span></p>
        </div>
        
        <!-- Action Buttons - Mobile Optimized -->
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center">
            <a href="{{ route('student.notifications') }}" class="relative inline-flex min-h-10 w-full items-center justify-center rounded-lg bg-indigo-600 px-3 py-2 text-xs sm:text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700 active:scale-95 sm:w-auto">
                <span aria-hidden="true" class="text-sm sm:text-base">🔔</span>
                <span class="ml-2">Notifications</span>
                @if(!empty($notificationCount) && $notificationCount > 0)
                    <span class="absolute -right-2 -top-2 flex h-5 w-5 items-center justify-center rounded-full bg-rose-500 text-xs font-bold text-white">{{ $notificationCount }}</span>
                @endif
            </a>
            <a href="{{ route('student.profile') }}" class="inline-flex min-h-10 w-full items-center justify-center rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs sm:text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50 active:scale-95 sm:w-auto">Profile</a>
            <form method="POST" action="{{ route('student.logout') }}" class="w-full sm:w-auto">
                @csrf
                <button class="min-h-10 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs sm:text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50 active:scale-95">Logout</button>
            </form>
        </div>
    </div>

    <!-- Main Content Grid - Responsive -->
    <div class="grid gap-4 md:gap-6 lg:grid-cols-[minmax(0,1.35fr)_minmax(280px,0.65fr)]">
        <!-- Main Attendance Card -->
        <div class="card overflow-hidden shadow-sm hover:shadow-md transition-shadow">
            <div class="border-b border-slate-100 bg-gradient-to-r from-slate-950 to-indigo-800 px-4 py-4 text-white sm:px-6 sm:py-5">
                <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.16em] text-indigo-200">Attendance overview</p>
                        <h2 class="mt-2 text-lg font-semibold sm:text-xl">Your progress this term</h2>
                    </div>
                    <span class="inline-block rounded-full bg-white/10 px-3 py-1 text-xs font-semibold text-indigo-100 w-fit">Latest updates</span>
                </div>
            </div>
            
            <div class="p-4 sm:p-6">
                <!-- Stats Cards - Mobile Optimized -->
                <div class="mb-6 grid grid-cols-3 gap-2 sm:gap-3">
                    <div class="rounded-xl border border-slate-200 bg-white p-3 text-center hover:border-indigo-200 transition-colors">
                        <div class="text-xs font-semibold uppercase tracking-wide text-slate-500 mb-1">Total Classes</div>
                        <div class="text-lg sm:text-2xl font-bold text-slate-900">{{ $totalClasses }}</div>
                    </div>
                    <div class="rounded-xl border border-slate-200 bg-white p-3 text-center hover:border-emerald-200 transition-colors">
                        <div class="text-xs font-semibold uppercase tracking-wide text-slate-500 mb-1">Present</div>
                        <div class="text-lg sm:text-2xl font-bold text-emerald-600">{{ $presentCount }}</div>
                    </div>
                    <div class="rounded-xl border border-indigo-100 bg-indigo-50 p-3 text-center hover:bg-indigo-100 transition-colors">
                        <div class="text-xs font-semibold uppercase tracking-wide text-indigo-600 mb-1">Attendance %</div>
                        <div class="text-lg sm:text-2xl font-bold text-indigo-700">{{ $attendancePercentage }}%</div>
                    </div>
                </div>

                <!-- Attendance History -->
                <div>
                    <h3 class="mb-3 text-xs sm:text-sm font-bold uppercase tracking-[0.1em] text-slate-600">Recent attendance</h3>
                    <div class="space-y-2 max-h-96 overflow-y-auto">
                        @forelse($attendanceRecords as $record)
                            <div class="flex items-center justify-between gap-2 p-3 rounded-lg bg-slate-50 hover:bg-slate-100 transition-colors">
                                <div class="min-w-0">
                                    <span class="block text-xs sm:text-sm font-semibold text-slate-900">{{ $record->created_at->format('M d, Y') }}</span>
                                    <div class="text-xs text-slate-500 truncate">{{ $record->classRoom?->name ?? 'No class' }}</div>
                                </div>
                                <span class="shrink-0 rounded-full px-2 sm:px-3 py-1 text-xs font-semibold whitespace-nowrap {{ $record->status === 'present' ? 'bg-emerald-100 text-emerald-700' : ($record->status === 'late' ? 'bg-amber-100 text-amber-700' : 'bg-rose-100 text-rose-700') }}">
                                    {{ ucfirst($record->status) }}
                                </span>
                            </div>
                        @empty
                            <div class="p-4 text-center text-sm text-slate-500 bg-slate-50 rounded-lg">
                                No attendance history yet.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar Card -->
        <div class="card h-fit overflow-hidden shadow-sm hover:shadow-md transition-shadow">
            <div class="border-b border-slate-100 px-4 py-4 sm:px-6 sm:py-5">
                <p class="text-xs font-semibold uppercase tracking-[0.16em] text-indigo-600">Your day</p>
                <h2 class="mt-2 text-lg font-semibold text-slate-900 sm:text-xl">Current class schedule</h2>
            </div>
            <div class="p-4 sm:p-6">
                @if($student->classRoom)
                    <div class="rounded-xl bg-gradient-to-br from-slate-50 to-slate-100 p-4 border border-slate-200">
                        <div class="flex items-start justify-between gap-2 mb-3">
                            <div class="min-w-0">
                                <strong class="block text-sm font-bold text-slate-900 truncate">{{ $student->classRoom->name }}</strong>
                                <div class="mt-1 text-xs text-slate-600">{{ $student->classRoom->classroom ?? '—' }}</div>
                            </div>
                            @if($student->classRoom->date)
                                <span class="shrink-0 rounded-lg bg-indigo-100 px-2 py-1 text-xs font-semibold text-indigo-700 whitespace-nowrap">{{ $student->classRoom->date }}</span>
                            @endif
                        </div>
                        <div class="border-t border-slate-200 pt-3">
                            <div class="text-xs text-slate-600">
                                <span class="font-semibold">Time:</span>
                                {{ $student->classRoom->time ?? '—' }}
                                @if(!empty($student->classRoom->end_time))
                                    <span class="text-slate-400">to</span> {{ $student->classRoom->end_time }}
                                @endif
                            </div>
                        </div>
                    </div>
                @else
                    <div class="rounded-xl border-2 border-dashed border-slate-300 p-6 text-center">
                        <div class="text-sm text-slate-500">No schedule available.</div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection

