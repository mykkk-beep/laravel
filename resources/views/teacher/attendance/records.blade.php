@extends('layouts.app')

@section('pageTitle', 'Monthly Attendance Tracker')
@section('pageSubtitle', 'Review and adjust monthly attendance totals.')

@section('content')
@php
    $calendarMonth = \Carbon\Carbon::createFromFormat('Y-m', $month);
    $calendarCounts = [];
    $monthlyTotals = ['present' => 0, 'late' => 0, 'absent' => 0, 'excused' => 0];

    foreach ($monthDays as $calendarDay) {
        if ($calendarDay === null) {
            continue;
        }

        $calendarCounts[$calendarDay['date']] = [
            'present' => $calendarDay['present'],
            'late' => $calendarDay['late'],
            'absent' => $calendarDay['absent'],
            'excused' => $calendarDay['excused'],
        ];

        foreach ($monthlyTotals as $status => $total) {
            $monthlyTotals[$status] += $calendarDay[$status];
        }
    }
@endphp
<div class="no-print flex flex-col gap-4 rounded-[28px] border border-slate-200/80 bg-white/90 p-6 shadow-sm lg:flex-row lg:items-center lg:justify-between">
    <div>
        <h2 class="text-2xl font-semibold text-slate-900">Monthly Attendance Tracker</h2>
        <p class="mt-1 text-sm text-slate-500">Track daily attendance at a glance and adjust this browser's monthly counters.</p>
    </div>
    <div class="flex flex-wrap gap-2">
        <a href="{{ route('teacher.attendance.calendar-export', array_filter(['month' => $month, 'class_room_id' => request('class_room_id')])) }}" class="btn btn-success">Export Calendar Excel</a>
        <a href="{{ route('teacher.attendance.export', array_merge(request()->query(), ['format' => 'pdf'])) }}" class="btn btn-danger">Export PDF</a>
        <!--<button type="button" class="btn btn-outline-secondary" onclick="window.print()">Print Calendar</button> -->
    </div>
</div>

<div class="no-print card">
    <div class="card-body">
        <form method="GET" action="{{ route('teacher.attendance.records') }}" class="grid gap-3 md:grid-cols-4 md:items-end">
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
                    <option value="excused" {{ request('status') == 'excused' ? 'selected' : '' }}>Excused</option>
                </select>
            </div>
            <div>
                <label for="attendance-month" class="form-label">Month</label>
                <input id="attendance-month" type="month" name="month" value="{{ $month }}" class="form-input">
            </div>
            <div>
                <button type="submit" class="btn btn-primary w-full">Filter</button>
            </div>
        </form>
    </div>
</div>

<section id="attendance-calendar-print" class="calendar-sheet mx-auto mt-5 max-w-7xl bg-white sm:mt-6" data-storage-key="attendance-calendar-v1-{{ Auth::id() }}-{{ request('class_room_id') ?: 'all' }}">
    <div class="mb-4 flex items-center justify-between border-b border-slate-200 pb-3">
        <a href="{{ route('teacher.attendance.records', array_merge(request()->query(), ['month' => $calendarMonth->copy()->subMonth()->format('Y-m'), 'date' => null])) }}" class="calendar-nav no-print" aria-label="Previous month">&larr;</a>
        <h1 class="text-xl font-semibold text-slate-900">{{ $calendarMonth->format('F Y') }}</h1>
        <a href="{{ route('teacher.attendance.records', array_merge(request()->query(), ['month' => $calendarMonth->copy()->addMonth()->format('Y-m'), 'date' => null])) }}" class="calendar-nav no-print" aria-label="Next month">&rarr;</a>
    </div>

    <div class="mb-3 flex flex-wrap justify-center gap-x-4 gap-y-1 text-xs font-medium sm:text-sm">
        <span class="text-emerald-700">Present</span>
        <span class="text-orange-600">Late</span>
        <span class="text-red-700">Absent</span>
        <span class="text-blue-700">Excuse</span>
    </div>

    <div class="calendar-weekdays grid grid-cols-7 border-l border-t border-slate-200 bg-white text-center">
        @foreach(['SUN', 'MON', 'TUE', 'WED', 'THU', 'FRI', 'SAT'] as $index => $weekday)
            <div aria-label="{{ ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'][$index] }}" class="border-b border-r border-slate-200 py-2 text-[10px] font-medium tracking-wide sm:text-xs {{ $index === 0 ? 'text-red-700' : 'text-slate-600' }}">{{ $weekday }}</div>
        @endforeach
    </div>
    <div class="calendar-grid grid grid-cols-7 border-l border-slate-200">
        @foreach($monthDays as $day)
            @if($day === null)
                <div aria-hidden="true" class="calendar-empty border-b border-r border-slate-200 bg-white"></div>
            @else
                @php $isSunday = \Carbon\Carbon::parse($day['date'])->dayOfWeek === 0; @endphp
                <button type="button" class="calendar-day border-b border-r border-slate-200 bg-white p-1 text-left hover:bg-slate-50 focus:z-10 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-slate-500 sm:p-2" data-attendance-day="{{ $day['date'] }}" aria-label="{{ \Carbon\Carbon::parse($day['date'])->format('l, F j, Y') }}: {{ $day['total'] }} students">
                    <span class="calendar-date-number block text-base font-semibold leading-none sm:text-lg {{ $isSunday ? 'text-red-700' : 'text-slate-800' }}">{{ $day['day'] }}</span>
                    <span class="calendar-counters mt-2 grid grid-cols-2 gap-x-1 gap-y-1">
                        @foreach(['present' => 'P', 'late' => 'L', 'absent' => 'A', 'excused' => 'E'] as $status => $label)
                            @php
                                $statusName = $status === 'excused' ? 'Excuse' : ucfirst($status);
                                $statusColor = ['present' => 'text-emerald-700', 'late' => 'text-orange-600', 'absent' => 'text-red-700', 'excused' => 'text-blue-700'][$status];
                            @endphp
                            <span class="calendar-counter {{ $statusColor }}" title="{{ $statusName }}: {{ $day[$status] }}" aria-label="{{ $statusName }}: {{ $day[$status] }}">
                                <span>{{ $label }}</span><span class="counter-value">{{ $day[$status] }}</span>
                            </span>
                        @endforeach
                    </span>
                </button>
            @endif
        @endforeach
    </div>

    <footer class="mt-4 border-t border-slate-200 py-3">
        <div class="flex flex-wrap items-center justify-between gap-x-4 gap-y-2">
            <h2 class="w-full text-center text-sm font-semibold text-slate-700 sm:w-auto sm:text-left">Monthly totals</h2>
            <div class="calendar-month-summary grid w-full grid-cols-2 gap-2 sm:w-auto sm:grid-cols-4">
                <div class="summary-counter text-emerald-700"><span>Present</span><strong id="monthly-present">{{ $monthlyTotals['present'] }}</strong></div>
                <div class="summary-counter text-orange-600"><span>Late</span><strong id="monthly-late">{{ $monthlyTotals['late'] }}</strong></div>
                <div class="summary-counter text-red-700"><span>Absent</span><strong id="monthly-absent">{{ $monthlyTotals['absent'] }}</strong></div>
                <div class="summary-counter text-blue-700"><span>Excuse</span><strong id="monthly-excused">{{ $monthlyTotals['excused'] }}</strong></div>
            </div>
        </div>
        <p id="calendar-storage-message" class="no-print mt-2 text-center text-xs text-slate-500">Changes are saved on this browser for this class and month.</p>
    </footer>
</section>

<dialog id="attendance-day-dialog" class="attendance-dialog w-[min(28rem,calc(100vw-2rem))] border border-slate-300 bg-white p-0 shadow-lg">
    <form id="attendance-day-form">
        <div class="border-b border-slate-200 px-5 py-4">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <p class="text-xs font-bold uppercase tracking-widest text-blue-700">Daily attendance</p>
                    <h2 id="attendance-dialog-title" class="mt-1 text-xl font-black text-blue-950"></h2>
                </div>
                <button type="button" class="rounded-lg px-2 py-1 text-xl leading-none text-slate-500 hover:bg-slate-100" data-dialog-close aria-label="Close">&times;</button>
            </div>
        </div>
        <div class="grid grid-cols-2 gap-3 px-5 py-5">
            @foreach(['present' => ['Present', 'emerald'], 'late' => ['Late', 'orange'], 'absent' => ['Absent', 'red'], 'excused' => ['Excuse', 'blue']] as $status => [$label, $color])
                <label class="text-sm font-bold {{ ['emerald' => 'text-emerald-700', 'orange' => 'text-orange-700', 'red' => 'text-red-700', 'blue' => 'text-blue-700'][$color] }}">
                    {{ $label }}
                    <input type="number" min="0" max="10000" step="1" value="0" required inputmode="numeric" data-dialog-status="{{ $status }}" class="mt-1 block w-full rounded-lg border border-slate-300 px-3 py-2 text-lg font-bold text-slate-900 focus:border-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-200">
                </label>
            @endforeach
        </div>
        <div class="flex items-center justify-between gap-3 border-t border-slate-200 bg-slate-50 px-5 py-4">
            <a href="#" data-day-details class="text-sm font-semibold text-blue-700 hover:underline">View records</a>
            <div class="flex gap-2">
                <button type="button" data-dialog-close class="rounded-lg border border-slate-300 px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-white">Cancel</button>
                <button type="submit" class="rounded-lg bg-blue-700 px-4 py-2 text-sm font-bold text-white hover:bg-blue-800">Save counts</button>
            </div>
        </div>
    </form>
</dialog>

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
                                        <span class="rounded-full px-3 py-1 text-xs font-semibold uppercase tracking-[0.1em] {{ $status[$period] === 'present' ? 'bg-emerald-100 text-emerald-700' : ($status[$period] === 'late' ? 'bg-amber-100 text-amber-700' : ($status[$period] === 'excused' ? 'bg-sky-100 text-sky-700' : 'bg-rose-100 text-rose-700')) }}">
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
    .calendar-sheet { font-family: Arial, Helvetica, sans-serif; }
    .calendar-nav { border: 1px solid #cbd5e1; border-radius: .25rem; padding: .35rem .65rem; color: #334155; font-size: .85rem; font-weight: 600; }
    .calendar-nav:hover { background: #f8fafc; }
    .calendar-day, .calendar-empty { min-width: 0; min-height: 5.5rem; }
    .calendar-counter { display: flex; align-items: center; gap: .2rem; font-size: .65rem; line-height: 1.2; }
    .summary-counter { display: flex; align-items: center; justify-content: space-between; gap: .5rem; padding: .25rem 0; font-size: .8rem; font-weight: 500; }
    .summary-counter strong { font-size: .9rem; font-weight: 600; }
    .attendance-dialog::backdrop { background: rgb(15 23 42 / .55); }

    @media print {
        @page { size: landscape; margin: 10mm; }
        body * { visibility: hidden !important; }
        #attendance-calendar-print, #attendance-calendar-print * { visibility: visible !important; }
        #attendance-calendar-print { position: fixed; top: 0; left: 0; width: 100%; max-width: none; margin: 0; border: 0; padding: 0; box-shadow: none; }
        #attendance-calendar-print .no-print { display: none !important; }
        .calendar-day, .calendar-empty { min-height: 6rem; }
        .calendar-counter { font-size: 9pt; }
        .attendance-dialog { display: none; }
    }

    @media (max-width: 420px) {
        .calendar-day, .calendar-empty { min-height: 5rem; }
        .calendar-counter { gap: .1rem; font-size: .55rem; }
    }
</style>

<script>
    (() => {
        const calendar = document.getElementById('attendance-calendar-print');
        const dialog = document.getElementById('attendance-day-dialog');
        const form = document.getElementById('attendance-day-form');
        const message = document.getElementById('calendar-storage-message');
        if (!calendar || !dialog || !form || !message) return;

        const storageKey = calendar.dataset.storageKey;
        const monthKey = @json($month);
        const statuses = ['present', 'late', 'absent', 'excused'];
        const dayTotals = @json($calendarCounts);
        let savedData = {};
        let selectedDate = null;
        let storageAvailable = true;

        try {
            savedData = JSON.parse(localStorage.getItem(storageKey) || '{}');
            if (!savedData || typeof savedData !== 'object' || Array.isArray(savedData)) savedData = {};
        } catch (error) {
            storageAvailable = false;
            message.textContent = 'Browser storage is unavailable or unreadable. Counter changes cannot be saved.';
        }

        const validCount = (value) => Number.isInteger(Number(value)) && Number(value) >= 0 && Number(value) <= 10000;
        const monthOverrides = savedData[monthKey] && typeof savedData[monthKey] === 'object' ? savedData[monthKey] : {};
        Object.entries(monthOverrides).forEach(([date, counts]) => {
            if (!dayTotals[date] || !counts || typeof counts !== 'object') return;
            statuses.forEach((status) => {
                if (validCount(counts[status])) dayTotals[date][status] = Number(counts[status]);
            });
        });

        const render = () => {
            const summary = { present: 0, late: 0, absent: 0, excused: 0 };
            document.querySelectorAll('[data-attendance-day]').forEach((button) => {
                const counts = dayTotals[button.dataset.attendanceDay];
                statuses.forEach((status, index) => {
                    summary[status] += counts[status];
                    const counter = button.querySelectorAll('.calendar-counter')[index];
                    const value = counter.querySelector('.counter-value');
                    const name = status === 'excused' ? 'Excuse' : status[0].toUpperCase() + status.slice(1);
                    value.textContent = counts[status];
                    counter.title = `${name}: ${counts[status]}`;
                    counter.setAttribute('aria-label', `${name}: ${counts[status]}`);
                });
            });
            statuses.forEach((status) => {
                document.getElementById(`monthly-${status}`).textContent = summary[status];
            });
        };

        document.querySelectorAll('[data-attendance-day]').forEach((button) => {
            button.addEventListener('click', () => {
                selectedDate = button.dataset.attendanceDay;
                const [year, month, day] = selectedDate.split('-').map(Number);
                document.getElementById('attendance-dialog-title').textContent = new Intl.DateTimeFormat(undefined, { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' }).format(new Date(year, month - 1, day));
                form.querySelectorAll('[data-dialog-status]').forEach((input) => {
                    input.value = dayTotals[selectedDate][input.dataset.dialogStatus];
                });
                const detailsUrl = new URL(window.location.href);
                detailsUrl.searchParams.set('month', monthKey);
                detailsUrl.searchParams.set('date', selectedDate);
                dialog.querySelector('[data-day-details]').href = detailsUrl.toString();
                dialog.showModal();
            });
        });

        dialog.querySelectorAll('[data-dialog-close]').forEach((button) => {
            button.addEventListener('click', () => dialog.close());
        });

        form.addEventListener('submit', (event) => {
            event.preventDefault();
            const updated = {};
            form.querySelectorAll('[data-dialog-status]').forEach((input) => {
                input.setCustomValidity('');
                const value = Number(input.value);
                if (!validCount(value)) {
                    input.focus();
                    input.setCustomValidity('Enter a whole number from 0 to 10,000.');
                    input.reportValidity();
                    return;
                }
                updated[input.dataset.dialogStatus] = value;
            });
            if (Object.keys(updated).length !== statuses.length) return;

            dayTotals[selectedDate] = updated;
            if (storageAvailable) {
                try {
                    savedData[monthKey] = savedData[monthKey] || {};
                    savedData[monthKey][selectedDate] = updated;
                    localStorage.setItem(storageKey, JSON.stringify(savedData));
                    message.textContent = 'Changes saved on this browser for this class and month.';
                } catch (error) {
                    storageAvailable = false;
                    message.textContent = 'Your counts changed on screen, but browser storage failed; changes were not saved.';
                }
            }
            render();
            dialog.close();
        });

        render();
    })();
</script>

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
