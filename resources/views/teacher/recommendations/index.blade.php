@extends('layouts.app')

@section('content')
@php
    $teacherName = Auth::user()->name ?? 'Teacher';
    $recommendationItems = $recommendations->map(function ($student) use ($teacherName) {
        $attendanceRecords = $student->attendanceRecords ?? collect();
        $absentCount = $attendanceRecords->where('status', 'absent')->count();
        $lateCount = $attendanceRecords->where('status', 'late')->count();
        $presentCount = $attendanceRecords->where('status', 'present')->count();
        $attendancePercentage = $attendanceRecords->count() > 0
            ? round(($presentCount / $attendanceRecords->count()) * 100)
            : 100;
        $grade = optional($student->enrollments()->where('status', \App\Models\Enrollment::STATUS_ENROLLED)->latest()->first())->grade;
        $category = $absentCount >= 3 ? 'Attendance' : ($grade !== null && $grade < 75 ? 'Academic Performance' : 'Behavior');
        $status = $absentCount >= 4 ? 'Viewed' : ($absentCount >= 2 ? 'Sent' : 'Draft');
        $message = $absentCount >= 3
            ? "Your child has had {$absentCount} absences this term and needs stronger attendance support at school."
            : "We encourage continued support at home to help maintain steady academic progress and positive classroom behavior.";

        return [
            'student_name' => $student->name,
            'class_name' => optional($student->classRoom)->name ?? 'Unassigned',
            'category' => $category,
            'message' => $message,
            'teacher_name' => $teacherName,
            'date_sent' => now()->subDays($absentCount > 0 ? $absentCount : 1)->translatedFormat('M d, Y'),
            'status' => $status,
            'attendance_percentage' => $attendancePercentage,
            'absences' => $absentCount,
            'late_arrivals' => $lateCount,
            'grade' => $grade,
        ];
    })->values();
@endphp

<div x-data="{ open: false, message: 'Dear Parent/Guardian, your child has shown a decline in attendance over the past month, which may affect academic performance. We encourage regular attendance and a consistent daily routine. Working together can help improve learning outcomes.' }" class="space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h1 class="text-3xl font-semibold tracking-tight text-slate-900">Recommendations</h1>
            <p class="mt-1 text-sm text-slate-500">Send guidance to parents</p>
        </div>
        <button type="button" @click="open = true" class="inline-flex items-center justify-center rounded-2xl bg-gradient-to-r from-violet-600 to-indigo-600 px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-indigo-200 transition duration-200 hover:brightness-95">
            <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path d="M10.75 4.25a.75.75 0 00-1.5 0v5.25H4a.75.75 0 000 1.5h5.25v5.25a.75.75 0 001.5 0v-5.25H15a.75.75 0 000-1.5h-4.25V4.25z" />
            </svg>
            New Recommendation
        </button>
    </div>

    @if (session('success'))
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700 shadow-sm">
            {{ session('success') }}
        </div>
    @endif

    <div class="rounded-3xl border border-slate-200/70 bg-white p-5 shadow-[0_12px_40px_-16px_rgba(15,23,42,0.18)] sm:p-7">
        <div class="mb-6 flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <h2 class="text-lg font-semibold text-slate-900">Parent guidance overview</h2>
                <p class="text-sm text-slate-500">Monitor progress and share timely recommendations.</p>
            </div>
            <div class="flex flex-wrap gap-3">
                <label class="flex items-center gap-2 rounded-2xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M8.5 2.75a5.75 5.75 0 100 11.5 5.75 5.75 0 000-11.5zM1.75 8.5a6.75 6.75 0 1112.5 0 6.75 6.75 0 01-12.5 0z" clip-rule="evenodd" />
                    </svg>
                    <input type="text" placeholder="Search by student name" class="w-44 border-0 bg-transparent text-sm outline-none placeholder:text-slate-400">
                </label>
                <select class="rounded-2xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-600 outline-none">
                    <option>Filter by class</option>
                    <option>Grade 8A</option>
                    <option>Grade 9B</option>
                </select>
                <select class="rounded-2xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-600 outline-none">
                    <option>Filter by type</option>
                    <option>Attendance</option>
                    <option>Academic Performance</option>
                    <option>Behavior</option>
                </select>
                <select class="rounded-2xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-600 outline-none">
                    <option>Filter by status</option>
                    <option>Draft</option>
                    <option>Sent</option>
                    <option>Viewed</option>
                </select>
                <input type="date" class="rounded-2xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-600 outline-none">
            </div>
        </div>

        @if($recommendationItems->isEmpty())
            <div class="flex flex-col items-center justify-center rounded-3xl border border-dashed border-slate-200 bg-slate-50/80 px-8 py-20 text-center">
                <div class="mb-4 rounded-full border border-slate-200 bg-white p-4 shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 10h8m-8 4h5m-8 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-slate-900">No recommendations yet.</h3>
                <p class="mt-2 max-w-md text-sm text-slate-500">Create a new recommendation to guide parents on attendance, behavior, or academic progress.</p>
            </div>
        @else
            <div class="space-y-4">
                @foreach($recommendationItems as $item)
                    @php
                        $statusStyles = match($item['status']) {
                            'Viewed' => 'bg-emerald-50 text-emerald-700 ring-emerald-200',
                            'Sent' => 'bg-indigo-50 text-indigo-700 ring-indigo-200',
                            default => 'bg-amber-50 text-amber-700 ring-amber-200',
                        };
                    @endphp
                    <article class="rounded-3xl border border-slate-200 bg-slate-50/70 p-5 shadow-sm transition duration-200 hover:-translate-y-0.5 hover:shadow-md">
                        <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                            <div class="space-y-2">
                                <div class="flex flex-wrap items-center gap-2">
                                    <h3 class="text-lg font-semibold text-slate-900">{{ $item['student_name'] }}</h3>
                                    <span class="rounded-full bg-white px-3 py-1 text-xs font-semibold text-slate-500 ring-1 ring-slate-200">{{ $item['class_name'] }}</span>
                                    <span class="rounded-full px-3 py-1 text-xs font-semibold ring-1 {{ $statusStyles }}">{{ $item['status'] }}</span>
                                </div>
                                <p class="text-sm font-medium text-indigo-600">{{ $item['category'] }}</p>
                                <p class="max-w-2xl text-sm leading-6 text-slate-600">{{ $item['message'] }}</p>
                            </div>
                            <div class="rounded-2xl bg-white px-4 py-3 text-sm text-slate-600 shadow-sm">
                                <p class="font-semibold text-slate-900">{{ $item['teacher_name'] }}</p>
                                <p class="mt-1">{{ $item['date_sent'] }}</p>
                            </div>
                        </div>

                        <div class="mt-5 flex flex-wrap items-center gap-2 text-sm text-slate-500">
                            <span class="rounded-full bg-white px-3 py-1">Attendance: {{ $item['attendance_percentage'] }}%</span>
                            <span class="rounded-full bg-white px-3 py-1">Absences: {{ $item['absences'] }}</span>
                            <span class="rounded-full bg-white px-3 py-1">Late arrivals: {{ $item['late_arrivals'] }}</span>
                            <span class="rounded-full bg-white px-3 py-1">Grade: {{ $item['grade'] ?? '—' }}</span>
                        </div>

                        <div class="mt-5 flex flex-wrap gap-2">
                            <button type="button" class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-100">View</button>
                            <button type="button" class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-100">Edit</button>
                            <button type="button" class="rounded-xl border border-rose-200 bg-rose-50 px-3 py-2 text-sm font-medium text-rose-700 transition hover:bg-rose-100">Delete</button>
                            <button type="button" class="rounded-xl border border-indigo-200 bg-indigo-50 px-3 py-2 text-sm font-medium text-indigo-700 transition hover:bg-indigo-100">Resend</button>
                        </div>
                    </article>
                @endforeach
            </div>
        @endif
    </div>

    <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/45 px-4 py-6" style="display: none;">
        <form method="POST" action="{{ route('teacher.recommendations.generate', ['student' => '__STUDENT__']) }}" class="w-full max-w-3xl rounded-3xl bg-white p-6 shadow-2xl sm:p-8" id="recommendation-form" @click.away="open = false">
            @csrf
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h3 class="text-xl font-semibold text-slate-900">Create recommendation</h3>
                    <p class="mt-1 text-sm text-slate-500">Send a thoughtful note to parents with attendance and academic guidance.</p>
                </div>
                <button type="button" @click="open = false" class="rounded-full p-2 text-slate-400 transition hover:bg-slate-100 hover:text-slate-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 001.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z" />
                    </svg>
                </button>
            </div>

            <div class="mt-6 grid gap-4 md:grid-cols-2">
                <label class="block text-sm font-medium text-slate-700">
                    <span class="mb-2 block">Student</span>
                    <select name="student_id" id="student-select" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm text-slate-700 outline-none" required>
                        <option value="">Select student</option>
                        @if(isset($students) && $students->isNotEmpty())
                            @foreach($students as $student)
                                <option value="{{ $student->id }}">{{ $student->name }} ({{ $student->student_id ?? $student->id }})</option>
                            @endforeach
                        @else
                            <option disabled>No students available</option>
                        @endif
                    </select>
                </label>
                <label class="block text-sm font-medium text-slate-700">
                    <span class="mb-2 block">Parent information</span>
                    <input type="text" value="(Parent/Guardian)" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm text-slate-700 outline-none" readonly>
                </label>
                <label class="block text-sm font-medium text-slate-700">
                    <span class="mb-2 block">Category</span>
                    <select class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm text-slate-700 outline-none">
                        <option>Attendance</option>
                        <option>Academic Performance</option>
                        <option>Behavior</option>
                    </select>
                </label>
                <label class="block text-sm font-medium text-slate-700">
                    <span class="mb-2 block">Subject</span>
                    <input type="text" value="Mathematics" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm text-slate-700 outline-none">
                </label>
            </div>

            <label class="mt-4 block text-sm font-medium text-slate-700">
                <span class="mb-2 block">Recommendation message</span>
                <textarea name="message" x-model="message" rows="5" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-3 py-3 text-sm text-slate-700 outline-none" placeholder="Write a recommendation..."></textarea>
            </label>

            <div class="mt-5 flex flex-wrap items-center gap-3">
                <label class="flex items-center gap-2 text-sm text-slate-600">
                    <input type="checkbox" checked class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500" readonly>
                    <span>Send to parent</span>
                </label>
            </div>

            <div class="mt-6 flex flex-wrap justify-end gap-3">
                <button type="button" class="rounded-2xl border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-100">Save Draft</button>
                <button type="submit" class="rounded-2xl bg-gradient-to-r from-violet-600 to-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-lg shadow-indigo-200 transition hover:brightness-95">Send Recommendation</button>
            </div>
        </div>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const form = document.getElementById('recommendation-form');
                const studentSelect = document.getElementById('student-select');

                if (!form || !studentSelect) {
                    return;
                }

                form.addEventListener('submit', function (event) {
                    const studentId = studentSelect.value;

                    if (!studentId) {
                        event.preventDefault();
                        return;
                    }

                    form.action = form.action.replace('__STUDENT__', studentId);
                });
            });
        </script>
    </form>
</div>
@endsection
