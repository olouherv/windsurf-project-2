<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bulletin de notes</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #111827; }
        .muted { color: #6b7280; }
        .title { font-size: 18px; font-weight: 700; margin: 0 0 4px 0; text-align: center; }
        .subtitle { font-size: 12px; font-weight: 600; margin: 12px 0 6px 0; color: #374151; }
        .block { margin-top: 12px; }
        .card { border: 1px solid #e5e7eb; padding: 10px; border-radius: 4px; margin-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 5px 6px; border: 1px solid #e5e7eb; text-align: left; }
        th { background: #f9fafb; font-weight: 600; font-size: 10px; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .total-row { font-weight: 700; background: #f3f4f6; }
        .pass { color: #059669; }
        .fail { color: #dc2626; }
        .header-info { display: table; width: 100%; margin-bottom: 15px; }
        .header-left, .header-right { display: table-cell; vertical-align: top; }
        .header-right { text-align: right; }
        .logo { font-size: 16px; font-weight: bold; color: #4f46e5; }
        .semester-title { background: #4f46e5; color: white; padding: 6px 10px; font-weight: 600; margin: 10px 0 0 0; }
        .ue-row { background: #eef2ff; font-weight: 600; }
        .ecu-row { background: #fff; }
        .average-box { border: 2px solid #4f46e5; padding: 10px; text-align: center; margin-top: 15px; }
        .average-value { font-size: 24px; font-weight: 700; color: #4f46e5; }
    </style>
</head>
<body>
    <div class="header-info">
        <div class="header-left">
            <div class="logo">{{ $university->name ?? 'EduManage' }}</div>
            <p class="muted">{{ $university->address ?? '' }}</p>
        </div>
        <div class="header-right">
            <p><strong>Année académique:</strong> {{ $academicYear->name ?? '-' }}</p>
            <p><strong>Date:</strong> {{ now()->format('d/m/Y') }}</p>
        </div>
    </div>

    <h1 class="title">BULLETIN DE NOTES</h1>

    <div class="card">
        <table style="border: none;">
            <tr style="border: none;">
                <td style="border: none; width: 50%;">
                    <strong>Étudiant:</strong> {{ $student->full_name }}<br>
                    <strong>Matricule:</strong> {{ $student->student_id }}
                </td>
                <td style="border: none; width: 50%;">
                    <strong>Formation:</strong> {{ $enrollment->programYear?->program?->name ?? '-' }}<br>
                    <strong>Niveau:</strong> {{ $enrollment->programYear?->name ?? '-' }}
                </td>
            </tr>
        </table>
    </div>

    @php
        $totalCredits = 0;
        $totalWeightedScore = 0;
        $totalCreditsValidated = 0;
    @endphp

    @foreach($semesters as $semester)
        <div class="semester-title">{{ $semester->name }}</div>
        
        <table>
            <thead>
                <tr>
                    <th width="40%">Matière</th>
                    <th width="10%" class="text-center">Crédits</th>
                    <th width="12%" class="text-center">Coef.</th>
                    <th width="12%" class="text-center">Note/20</th>
                    <th width="13%" class="text-center">Résultat</th>
                    <th width="13%" class="text-center">Points</th>
                </tr>
            </thead>
            <tbody>
                @foreach($semester->ues as $ue)
                    @php
                        $ueAverage = $ueAverages[$ue->id] ?? null;
                        $ueCredits = $ue->credits ?? 0;
                        $ueValidated = $ueAverage !== null && $ueAverage >= 10;
                    @endphp
                    <tr class="ue-row">
                        <td colspan="6">
                            <strong>{{ $ue->code }} - {{ $ue->name }}</strong>
                            @if($ueAverage !== null)
                                <span style="float: right;">Moyenne UE: <strong class="{{ $ueValidated ? 'pass' : 'fail' }}">{{ number_format($ueAverage, 2) }}/20</strong></span>
                            @endif
                        </td>
                    </tr>
                    @foreach($ue->ecus as $ecu)
                        @php
                            $ecuGrade = $ecuGrades[$ecu->id] ?? null;
                            $ecuCredits = $ecu->credits ?? 0;
                            $ecuCoef = $ecu->coefficient ?? 1;
                            $ecuValidated = $ecuGrade !== null && $ecuGrade >= 10;
                            $points = $ecuGrade !== null ? round($ecuGrade * $ecuCoef, 2) : '-';
                            
                            if ($ecuGrade !== null) {
                                $totalCredits += $ecuCredits;
                                $totalWeightedScore += $ecuGrade * $ecuCredits;
                                if ($ecuValidated) {
                                    $totalCreditsValidated += $ecuCredits;
                                }
                            }
                        @endphp
                        <tr class="ecu-row">
                            <td style="padding-left: 20px;">{{ $ecu->code }} - {{ $ecu->name }}</td>
                            <td class="text-center">{{ $ecuCredits }}</td>
                            <td class="text-center">{{ $ecuCoef }}</td>
                            <td class="text-center {{ $ecuGrade !== null ? ($ecuValidated ? 'pass' : 'fail') : '' }}">
                                {{ $ecuGrade !== null ? number_format($ecuGrade, 2) : '-' }}
                            </td>
                            <td class="text-center">
                                @if($ecuGrade !== null)
                                    <span class="{{ $ecuValidated ? 'pass' : 'fail' }}">{{ $ecuValidated ? 'Validé' : 'Non validé' }}</span>
                                @else
                                    -
                                @endif
                            </td>
                            <td class="text-center">{{ $points }}</td>
                        </tr>
                    @endforeach
                @endforeach
            </tbody>
        </table>
    @endforeach

    @php
        $generalAverage = $totalCredits > 0 ? round($totalWeightedScore / $totalCredits, 2) : null;
        $passed = $generalAverage !== null && $generalAverage >= 10;
    @endphp

    <div class="average-box">
        <p style="margin: 0 0 5px 0;">Moyenne Générale</p>
        <div class="average-value {{ $passed ? 'pass' : 'fail' }}">
            {{ $generalAverage !== null ? number_format($generalAverage, 2) . '/20' : 'N/A' }}
        </div>
        <p style="margin: 5px 0 0 0;">
            Crédits validés: <strong>{{ $totalCreditsValidated }}</strong> / {{ $totalCredits }}
        </p>
        @if($generalAverage !== null)
            <p style="margin: 5px 0 0 0; font-weight: 600;" class="{{ $passed ? 'pass' : 'fail' }}">
                {{ $passed ? 'ADMIS(E)' : 'AJOURNÉ(E)' }}
            </p>
        @endif
    </div>

    <div class="block" style="margin-top: 25px;">
        <table style="border: none;">
            <tr style="border: none;">
                <td style="border: none; width: 50%;">
                    <p><strong>Observations:</strong></p>
                    <div style="height: 40px; border-bottom: 1px solid #e5e7eb;"></div>
                </td>
                <td style="border: none; width: 50%; text-align: right;">
                    <p><strong>Signature du responsable:</strong></p>
                    <div style="height: 40px; border-bottom: 1px solid #e5e7eb; width: 150px; margin-left: auto;"></div>
                </td>
            </tr>
        </table>
    </div>

    <div class="block muted" style="margin-top: 20px; font-size: 9px; text-align: center;">
        Document généré automatiquement par EduManage le {{ now()->format('d/m/Y à H:i') }}
    </div>
</body>
</html>
