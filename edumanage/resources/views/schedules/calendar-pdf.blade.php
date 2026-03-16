<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendrier - {{ $monthName }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 9px;
            color: #1f2937;
        }
        .header {
            text-align: center;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #4f46e5;
        }
        .header h1 {
            font-size: 18px;
            color: #4f46e5;
            margin-bottom: 5px;
        }
        .header p {
            font-size: 11px;
            color: #6b7280;
        }
        .calendar {
            width: 100%;
            border-collapse: collapse;
        }
        .calendar th {
            background-color: #4f46e5;
            color: white;
            padding: 8px 4px;
            font-size: 10px;
            font-weight: 600;
            text-align: center;
            border: 1px solid #4338ca;
        }
        .calendar td {
            border: 1px solid #d1d5db;
            padding: 4px;
            vertical-align: top;
            height: 90px;
            width: 14.28%;
        }
        .calendar td.other-month {
            background-color: #f9fafb;
        }
        .calendar td.today {
            background-color: #fef3c7;
        }
        .day-number {
            font-weight: 600;
            font-size: 11px;
            margin-bottom: 3px;
            color: #374151;
        }
        .other-month .day-number {
            color: #9ca3af;
        }
        .session {
            font-size: 7px;
            padding: 2px 3px;
            margin-bottom: 2px;
            border-radius: 2px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .session-cm {
            background-color: #dbeafe;
            color: #1e40af;
        }
        .session-td {
            background-color: #d1fae5;
            color: #065f46;
        }
        .session-tp {
            background-color: #e9d5ff;
            color: #6b21a8;
        }
        .footer {
            margin-top: 15px;
            padding-top: 10px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            font-size: 8px;
            color: #6b7280;
        }
        .legend {
            margin-top: 10px;
            display: flex;
            justify-content: center;
            gap: 15px;
        }
        .legend-item {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-size: 8px;
        }
        .legend-color {
            width: 15px;
            height: 10px;
            border-radius: 2px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Calendrier de Planification - {{ $monthName }}</h1>
        @if($academicYear)
            <p>Année académique : {{ $academicYear->name }}</p>
        @endif
    </div>

    <table class="calendar">
        <thead>
            <tr>
                <th>Lundi</th>
                <th>Mardi</th>
                <th>Mercredi</th>
                <th>Jeudi</th>
                <th>Vendredi</th>
                <th>Samedi</th>
                <th>Dimanche</th>
            </tr>
        </thead>
        <tbody>
            @php
                $weeks = array_chunk($calendarDays, 7);
            @endphp
            @foreach($weeks as $week)
                <tr>
                    @foreach($week as $day)
                        <td class="{{ !$day['isCurrentMonth'] ? 'other-month' : '' }} {{ $day['isToday'] ? 'today' : '' }}">
                            <div class="day-number">{{ $day['day'] }}</div>
                            @php
                                $daySessions = $sessionsForMonth[$day['date']] ?? collect();
                            @endphp
                            @foreach($daySessions->take(5) as $session)
                                <div class="session session-{{ $session->type }}">
                                    {{ substr($session->start_time, 0, 5) }} {{ $session->ecu?->code }}
                                    @if($session->room)
                                        - {{ $session->room->code }}
                                    @endif
                                </div>
                            @endforeach
                            @if($daySessions->count() > 5)
                                <div class="session" style="background-color: #f3f4f6; color: #6b7280;">
                                    +{{ $daySessions->count() - 5 }} autres
                                </div>
                            @endif
                        </td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="legend">
        <div class="legend-item">
            <div class="legend-color session-cm"></div>
            <span>CM (Cours Magistral)</span>
        </div>
        <div class="legend-item">
            <div class="legend-color session-td"></div>
            <span>TD (Travaux Dirigés)</span>
        </div>
        <div class="legend-item">
            <div class="legend-color session-tp"></div>
            <span>TP (Travaux Pratiques)</span>
        </div>
    </div>

    <div class="footer">
        <p>Généré le {{ now()->translatedFormat('d F Y à H:i') }}</p>
    </div>
</body>
</html>
