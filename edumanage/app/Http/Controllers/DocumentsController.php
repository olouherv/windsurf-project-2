<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\ContractPayment;
use App\Models\Ecu;
use App\Models\Grade;
use App\Models\ProgramYear;
use App\Models\Semester;
use App\Models\Student;
use App\Models\StudentContract;
use App\Models\StudentEnrollment;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
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

    public function recuPaiement(ContractPayment $payment): Response
    {
        $payment->load(['contract.student', 'contract.programYear.program', 'contract.academicYear', 'schedule']);
        
        $university = auth()->user()->university;

        $pdf = Pdf::loadView('documents.recu-paiement', [
            'payment' => $payment,
            'contract' => $payment->contract,
            'schedule' => $payment->schedule,
            'university' => $university,
            'currency' => 'FCFA',
        ]);

        return $pdf->download('recu-paiement-' . $payment->id . '.pdf');
    }

    public function bulletinNotes(Student $student, ?int $academicYearId = null): Response
    {
        $universityId = auth()->user()->university_id;
        $university = auth()->user()->university;
        
        $academicYear = $academicYearId 
            ? AcademicYear::find($academicYearId)
            : AcademicYear::where('is_current', true)->where('university_id', $universityId)->first();

        $enrollment = StudentEnrollment::where('student_id', $student->id)
            ->where('academic_year_id', $academicYear?->id)
            ->with(['programYear.program'])
            ->first();

        if (!$enrollment || !$enrollment->programYear) {
            abort(404, 'Inscription non trouvée pour cette année académique.');
        }

        $semesters = Semester::where('program_year_id', $enrollment->program_year_id)
            ->with(['ues.ecus'])
            ->orderBy('number')
            ->get();

        $ecuGrades = [];
        $ueAverages = [];

        foreach ($semesters as $semester) {
            foreach ($semester->ues as $ue) {
                $ueScores = [];
                $ueTotalCoef = 0;
                
                foreach ($ue->ecus as $ecu) {
                    $grades = Grade::whereHas('evaluation', function ($q) use ($ecu, $academicYear) {
                        $q->where('ecu_id', $ecu->id)
                          ->where('academic_year_id', $academicYear?->id)
                          ->where('is_published', true);
                    })
                    ->where('student_id', $student->id)
                    ->whereNotNull('score')
                    ->where('is_absent', false)
                    ->get();

                    if ($grades->count() > 0) {
                        $ecuAverage = $grades->avg(fn($g) => $g->normalized_score);
                        $ecuGrades[$ecu->id] = round($ecuAverage, 2);
                        
                        $coef = $ecu->coefficient ?? 1;
                        $ueScores[] = $ecuAverage * $coef;
                        $ueTotalCoef += $coef;
                    }
                }

                if ($ueTotalCoef > 0) {
                    $ueAverages[$ue->id] = round(array_sum($ueScores) / $ueTotalCoef, 2);
                }
            }
        }

        $pdf = Pdf::loadView('documents.bulletin-notes', [
            'student' => $student,
            'enrollment' => $enrollment,
            'academicYear' => $academicYear,
            'university' => $university,
            'semesters' => $semesters,
            'ecuGrades' => $ecuGrades,
            'ueAverages' => $ueAverages,
        ]);

        return $pdf->download('bulletin-' . $student->student_id . '-' . ($academicYear?->name ?? 'annee') . '.pdf');
    }

    public function listeEtudiants(Request $request): Response
    {
        $universityId = auth()->user()->university_id;
        $university = auth()->user()->university;

        $academicYearId = $request->get('academic_year_id');
        $programYearId = $request->get('program_year_id');

        $academicYear = $academicYearId 
            ? AcademicYear::find($academicYearId)
            : AcademicYear::where('is_current', true)->where('university_id', $universityId)->first();

        $programYear = ProgramYear::with('program')->find($programYearId);

        if (!$programYear) {
            abort(404, 'Formation non trouvée.');
        }

        $students = Student::whereHas('enrollments', function ($q) use ($academicYear, $programYearId) {
            $q->where('academic_year_id', $academicYear?->id)
              ->where('program_year_id', $programYearId);
        })
        ->where('university_id', $universityId)
        ->with('user')
        ->orderBy('last_name')
        ->orderBy('first_name')
        ->get();

        $pdf = Pdf::loadView('documents.liste-etudiants', [
            'students' => $students,
            'programYear' => $programYear,
            'academicYear' => $academicYear,
            'university' => $university,
        ]);

        $filename = 'liste-etudiants-' . str_replace(' ', '-', $programYear->program->name ?? 'formation') . '-' . ($programYear->name ?? '') . '.pdf';

        return $pdf->download($filename);
    }
}
