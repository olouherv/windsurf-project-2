<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Contrat {{ $contract->contract_number }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #111827; }
        .muted { color: #6b7280; }
        .title { font-size: 18px; font-weight: 700; margin: 0 0 4px 0; }
        .section { margin-top: 16px; }
        .card { border: 1px solid #e5e7eb; padding: 12px; border-radius: 6px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 6px 8px; border-bottom: 1px solid #e5e7eb; text-align: left; }
        th { background: #f9fafb; }
    </style>
</head>
<body>
    <p class="muted">{{ $contract->contract_number }}</p>
    <h1 class="title">Contrat étudiant</h1>

    <div class="section card">
        <table>
            <tr>
                <th>Étudiant</th>
                <td>{{ $contract->student?->full_name ?? '-' }} ({{ $contract->student?->student_id ?? '-' }})</td>
            </tr>
            <tr>
                <th>Année académique</th>
                <td>{{ $contract->academicYear?->name ?? '-' }}</td>
            </tr>
            <tr>
                <th>Formation</th>
                <td>
                    {{ $contract->programYear?->program?->name ?? '-' }}
                    @if($contract->programYear)
                        - {{ $contract->programYear->name }}
                    @endif
                </td>
            </tr>
            <tr>
                <th>Période</th>
                <td>
                    {{ $contract->start_date?->format('d/m/Y') ?? '-' }}
                    -
                    {{ $contract->end_date?->format('d/m/Y') ?? '-' }}
                </td>
            </tr>
            <tr>
                <th>Type</th>
                <td>{{ ucfirst($contract->type) }}</td>
            </tr>
            <tr>
                <th>Statut</th>
                <td>{{ ucfirst($contract->status) }}</td>
            </tr>
        </table>
    </div>

    <div class="section card">
        <table>
            <tr>
                <th>Frais scolarité</th>
                <td>{{ number_format((float) $contract->tuition_fees, 2) }}</td>
            </tr>
            <tr>
                <th>Frais inscription</th>
                <td>{{ number_format((float) $contract->registration_fees, 2) }}</td>
            </tr>
            <tr>
                <th>Total</th>
                <td>{{ number_format((float) $contract->total_amount, 2) }}</td>
            </tr>
            <tr>
                <th>Payé</th>
                <td>{{ number_format((float) $contract->amount_paid, 2) }}</td>
            </tr>
        </table>
    </div>

    @if($contract->paymentSchedules && $contract->paymentSchedules->count() > 0)
        <div class="section">
            <h2 style="font-size: 14px; margin: 0 0 6px 0;">Échéancier</h2>
            <div class="card">
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Libellé</th>
                            <th>Échéance</th>
                            <th>Montant</th>
                            <th>Statut</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($contract->paymentSchedules->sortBy('installment_number') as $s)
                            <tr>
                                <td>{{ $s->installment_number }}</td>
                                <td>{{ $s->label }}</td>
                                <td>{{ $s->due_date?->format('d/m/Y') ?? '-' }}</td>
                                <td>{{ number_format((float) $s->amount, 2) }}</td>
                                <td>{{ $s->status }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <div class="section muted">
        Généré le {{ now()->format('d/m/Y H:i') }}
    </div>
</body>
</html>
