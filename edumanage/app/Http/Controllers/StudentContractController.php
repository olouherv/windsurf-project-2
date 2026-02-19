<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\StudentContract;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;
use Illuminate\View\View;

class StudentContractController extends Controller
{
    public function index(): View
    {
        return view('contracts.index');
    }

    public function create(?Student $student = null): View
    {
        return view('contracts.create', compact('student'));
    }

    public function show(StudentContract $contract): View
    {
        $contract->load(['student', 'academicYear', 'programYear.program', 'payments.recordedBy', 'payments.paymentSchedule', 'paymentSchedules']);
        return view('contracts.show', compact('contract'));
    }

    public function pdf(StudentContract $contract): Response
    {
        $contract->load(['student', 'academicYear', 'programYear.program', 'paymentSchedules']);

        $pdf = Pdf::loadView('contracts.pdf', [
            'contract' => $contract,
        ]);

        return $pdf->download('contrat-' . $contract->contract_number . '.pdf');
    }

    public function exportCsv(): Response
    {
        $contracts = StudentContract::query()
            ->with(['student', 'academicYear', 'programYear.program'])
            ->orderByDesc('created_at')
            ->limit(5000)
            ->get();

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="contrats-etudiants.csv"',
        ];

        $callback = function () use ($contracts) {
            $out = fopen('php://output', 'w');

            fwrite($out, "\xEF\xBB\xBF");

            fputcsv($out, [
                'Numéro',
                'Étudiant',
                'Matricule',
                'Année académique',
                'Formation',
                'Type',
                'Statut',
                'Statut paiement',
                'Début',
                'Fin',
                'Total',
                'Payé',
            ], ';');

            foreach ($contracts as $c) {
                $program = $c->programYear?->program?->name;
                $programYear = $c->programYear?->name;

                fputcsv($out, [
                    $c->contract_number,
                    $c->student?->full_name,
                    $c->student?->student_id,
                    $c->academicYear?->name,
                    trim(($program ? $program : '') . ($programYear ? ' - ' . $programYear : '')),
                    $c->type,
                    $c->status,
                    $c->payment_status,
                    $c->start_date?->format('Y-m-d'),
                    $c->end_date?->format('Y-m-d'),
                    (string) $c->total_amount,
                    (string) $c->amount_paid,
                ], ';');
            }

            fclose($out);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function edit(StudentContract $contract): View
    {
        $contract->load(['student', 'academicYear', 'programYear']);
        return view('contracts.edit', compact('contract'));
    }
}
