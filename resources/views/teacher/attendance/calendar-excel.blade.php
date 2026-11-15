<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Attendance Calendar - {{ \Carbon\Carbon::createFromFormat('Y-m', $month)->format('F Y') }}</title>
</head>
<body>
    <h1>Attendance Calendar - {{ \Carbon\Carbon::createFromFormat('Y-m', $month)->format('F Y') }}</h1>
    <p>{{ $className }} - Class-wide daily attendance counts</p>

    <table border="1" cellspacing="0" cellpadding="5" style="border-collapse: collapse; width: 100%;">
        <thead>
            <tr style="background-color: #e2e8f0; font-weight: bold; text-align: center;">
                @foreach(['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'] as $weekday)
                    <th style="width: 14.28%;">{{ $weekday }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($calendarWeeks as $week)
                <tr>
                    @foreach($week as $day)
                        <td style="height: 105px; vertical-align: top; width: 14.28%;">
                            @if($day)
                                <strong>{{ $day['day'] }}</strong><br>
                                <span style="background-color: #dcfce7; color: #166534;">P: {{ $day['present'] }}</span><br>
                                <span style="background-color: #fee2e2; color: #991b1b;">A: {{ $day['absent'] }}</span><br>
                                <span style="background-color: #fef3c7; color: #92400e;">L: {{ $day['late'] }}</span><br>
                                <span style="background-color: #dbeafe; color: #1e40af;">B: {{ $day['excused'] }}</span>
                            @endif
                        </td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>

    <h2>Monthly Summary</h2>
    <table border="1" cellspacing="0" cellpadding="5" style="border-collapse: collapse;">
        <thead>
            <tr style="background-color: #e2e8f0; font-weight: bold;">
                <th>Present (P)</th>
                <th>Absent (A)</th>
                <th>Late (L)</th>
                <th>Excused (B)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="background-color: #dcfce7; color: #166534;">{{ $summary['present'] }}</td>
                <td style="background-color: #fee2e2; color: #991b1b;">{{ $summary['absent'] }}</td>
                <td style="background-color: #fef3c7; color: #92400e;">{{ $summary['late'] }}</td>
                <td style="background-color: #dbeafe; color: #1e40af;">{{ $summary['excused'] }}</td>
            </tr>
        </tbody>
    </table>
    <p>Summary totals are attendance records for the selected month and class scope.</p>
</body>
</html>
