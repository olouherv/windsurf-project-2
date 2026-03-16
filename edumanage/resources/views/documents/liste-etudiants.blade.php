<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Liste des étudiants</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #111827; }
        .muted { color: #6b7280; }
        .title { font-size: 16px; font-weight: 700; margin: 0 0 4px 0; text-align: center; }
        .subtitle { font-size: 12px; color: #6b7280; text-align: center; margin-bottom: 15px; }
        .block { margin-top: 12px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 5px 6px; border: 1px solid #d1d5db; text-align: left; }
        th { background: #4f46e5; color: white; font-weight: 600; font-size: 10px; }
        tr:nth-child(even) { background: #f9fafb; }
        .text-center { text-align: center; }
        .header-info { margin-bottom: 15px; }
        .logo { font-size: 14px; font-weight: bold; color: #4f46e5; }
        .stats { background: #f3f4f6; padding: 8px; border-radius: 4px; margin-bottom: 10px; }
        @page { margin: 15mm; }
    </style>
</head>
<body>
    <div class="header-info">
        <div class="logo">{{ $university->name ?? 'EduManage' }}</div>
        <p class="muted" style="margin: 2px 0;">{{ $university->address ?? '' }}</p>
    </div>

    <h1 class="title">LISTE DES ÉTUDIANTS</h1>
    <p class="subtitle">
        {{ $programYear->program->name ?? '' }} - {{ $programYear->name ?? '' }}<br>
        Année académique: {{ $academicYear->name ?? '-' }}
    </p>

    <div class="stats">
        <strong>Total: {{ $students->count() }} étudiant(s)</strong>
    </div>

    <table>
        <thead>
            <tr>
                <th width="5%" class="text-center">N°</th>
                <th width="15%">Matricule</th>
                <th width="30%">Nom complet</th>
                <th width="10%" class="text-center">Sexe</th>
                <th width="20%">Email</th>
                <th width="20%">Téléphone</th>
            </tr>
        </thead>
        <tbody>
            @forelse($students as $index => $student)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $student->student_id }}</td>
                    <td>{{ $student->full_name }}</td>
                    <td class="text-center">{{ $student->gender === 'male' ? 'M' : ($student->gender === 'female' ? 'F' : '-') }}</td>
                    <td>{{ $student->user?->email ?? '-' }}</td>
                    <td>{{ $student->phone ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center muted">Aucun étudiant inscrit</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="block muted" style="margin-top: 20px; font-size: 9px; text-align: center;">
        Document généré le {{ now()->format('d/m/Y à H:i') }} par EduManage
    </div>
</body>
</html>
