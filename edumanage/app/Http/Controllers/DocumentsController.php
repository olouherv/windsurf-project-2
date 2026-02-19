<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\Student;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;

class DocumentsController extends Controller
{
    public function attestationInscription(Student $student): Response
    {
        $student->load(['currentEnrollment.programYear.program']);

        $currentYear = AcademicYear::where('is_current', true)
            ->where('university_id', auth()->user()->university_id)
            ->first();

        $pdf = Pdf::loadView('documents.attestation-inscription', [
            'student' => $student,
            'academicYear' => $currentYear,
        ]);

        return $pdf->download('attestation-inscription-' . $student->student_id . '.pdf');
    }

    public function certificatScolarite(Student $student): Response
    {
        $student->load(['currentEnrollment.programYear.program']);

        $currentYear = AcademicYear::where('is_current', true)
            ->where('university_id', auth()->user()->university_id)
            ->first();

        $pdf = Pdf::loadView('documents.certificat-scolarite', [
            'student' => $student,
            'academicYear' => $currentYear,
        ]);

        return $pdf->download('certificat-scolarite-' . $student->student_id . '.pdf');
    }
}
