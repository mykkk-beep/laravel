<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Attendance Report</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        th, td { border: 1px solid #ddd; padding: 6px; text-align: left; }
        th { background: #f2f2f2; }
        .summary { margin-bottom: 12px; }
        .summary span { margin-right: 12px; }
    </style>
</head>
<body>
    <h2>Attendance Report</h2>
    <p>Generated on {{ now()->format('Y-m-d H:i:s') }}</p>
    <div class="summary">
        <span>Total Entries: {{ $summary['total'] }}</span>
        <span>Present: {{ $summary['present'] }}</span>
        <span>Absent: {{ $summary['absent'] }}</span>
        <span>Attendance Rate: {{ $summary['attendance_rate'] }}%</span>
    </div>
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Student</th>
                <th>Class</th>
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
                    <td>{{ ucfirst($record->status) }}</td>
                    <td>{{ $record->created_at->format('Y-m-d H:i:s') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
