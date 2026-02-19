<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Attestation d'inscription</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #111827; }
        .muted { color: #6b7280; }
        .title { font-size: 18px; font-weight: 700; margin: 0 0 8px 0; }
        .block { margin-top: 14px; }
    </style>
</head>
<body>
    <h1 class="title">Attestation d'inscription</h1>

    <p class="muted">Date: {{ now()->format('d/m/Y') }}</p>

    <div class="block">
        <p>Je soussigné(e), certifie que :</p>
        <p><strong>{{ $student->full_name }}</strong>, matricule <strong>{{ $student->student_id }}</strong></p>
        <p>est inscrit(e) au sein de l'établissement pour l'année académique <strong>{{ $academicYear?->name ?? '-' }}</strong>.</p>

        <p>
            Formation :
            <strong>{{ $student->currentEnrollment?->programYear?->program?->name ?? '-' }}</strong>
            @if($student->currentEnrollment?->programYear)
                - {{ $student->currentEnrollment->programYear->name }}
            @endif
        </p>
    </div>

    <div class="block">
        <p>Fait pour servir et valoir ce que de droit.</p>
    </div>

    <div class="block muted">
        Généré automatiquement par EduManage.
    </div>
</body>
</html>
