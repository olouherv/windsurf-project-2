<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Reçu de paiement</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #111827; }
        .muted { color: #6b7280; }
        .title { font-size: 18px; font-weight: 700; margin: 0 0 8px 0; }
        .subtitle { font-size: 14px; font-weight: 600; margin: 16px 0 8px 0; }
        .block { margin-top: 14px; }
        .card { border: 1px solid #e5e7eb; padding: 12px; border-radius: 6px; margin-bottom: 12px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 6px 8px; border-bottom: 1px solid #e5e7eb; text-align: left; }
        th { background: #f9fafb; font-weight: 600; }
        .text-right { text-align: right; }
        .total-row { font-weight: 700; background: #f3f4f6; }
        .header { display: flex; justify-content: space-between; margin-bottom: 20px; }
        .logo { font-size: 20px; font-weight: bold; color: #4f46e5; }
        .receipt-number { font-size: 14px; color: #6b7280; }
    </style>
</head>
<body>
    <div style="margin-bottom: 20px;">
        <div class="logo">{{ $university->name ?? 'EduManage' }}</div>
        <p class="muted">{{ $university->address ?? '' }}</p>
    </div>

    <h1 class="title">Reçu de paiement</h1>
    <p class="receipt-number">N° {{ $payment->id }}-{{ now()->format('Ymd') }}</p>
    <p class="muted">Date: {{ $payment->paid_at?->format('d/m/Y') ?? now()->format('d/m/Y') }}</p>

    <div class="block card">
        <h2 class="subtitle" style="margin-top: 0;">Informations étudiant</h2>
        <table>
            <tr>
                <th width="40%">Nom complet</th>
                <td>{{ $contract->student?->full_name ?? '-' }}</td>
            </tr>
            <tr>
                <th>Matricule</th>
                <td>{{ $contract->student?->student_id ?? '-' }}</td>
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
                <th>Année académique</th>
                <td>{{ $contract->academicYear?->name ?? '-' }}</td>
            </tr>
        </table>
    </div>

    <div class="block card">
        <h2 class="subtitle" style="margin-top: 0;">Détails du paiement</h2>
        <table>
            <tr>
                <th width="40%">Contrat N°</th>
                <td>{{ $contract->contract_number }}</td>
            </tr>
            <tr>
                <th>Tranche</th>
                <td>{{ $schedule->label ?? 'Paiement direct' }}</td>
            </tr>
            <tr>
                <th>Montant payé</th>
                <td><strong>{{ number_format((float) $payment->amount, 2) }} {{ $currency ?? 'FCFA' }}</strong></td>
            </tr>
            <tr>
                <th>Mode de paiement</th>
                <td>{{ ucfirst($payment->payment_method ?? 'Espèces') }}</td>
            </tr>
            @if($payment->reference)
            <tr>
                <th>Référence</th>
                <td>{{ $payment->reference }}</td>
            </tr>
            @endif
        </table>
    </div>

    <div class="block card">
        <h2 class="subtitle" style="margin-top: 0;">Situation financière</h2>
        <table>
            <tr>
                <th width="40%">Total à payer</th>
                <td class="text-right">{{ number_format((float) $contract->total_amount, 2) }} {{ $currency ?? 'FCFA' }}</td>
            </tr>
            <tr>
                <th>Total déjà payé</th>
                <td class="text-right">{{ number_format((float) $contract->amount_paid, 2) }} {{ $currency ?? 'FCFA' }}</td>
            </tr>
            <tr class="total-row">
                <th>Reste à payer</th>
                <td class="text-right">{{ number_format((float) $contract->remaining_amount, 2) }} {{ $currency ?? 'FCFA' }}</td>
            </tr>
        </table>
    </div>

    <div class="block" style="margin-top: 30px;">
        <p>Ce reçu atteste du paiement effectué par l'étudiant mentionné ci-dessus.</p>
        <p style="margin-top: 20px;">Signature et cachet :</p>
        <div style="height: 60px; border-bottom: 1px solid #e5e7eb; width: 200px;"></div>
    </div>

    <div class="block muted" style="margin-top: 30px; font-size: 10px;">
        Document généré automatiquement par EduManage le {{ now()->format('d/m/Y à H:i') }}
    </div>
</body>
</html>
