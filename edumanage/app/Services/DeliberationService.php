<?php

namespace App\Services;

use App\Models\AcademicYear;
use App\Models\Deliberation;
use App\Models\DeliberationResult;
use App\Models\DeliberationSetting;
use App\Models\DeliberationUeResult;
use App\Models\ProgramYear;
use App\Models\Semester;
use App\Models\Student;
use App\Models\Ue;
use Illuminate\Support\Collection;

class DeliberationService
{
    protected DeliberationSetting $settings;
    protected AcademicYear $academicYear;

    public function __construct(DeliberationSetting $settings, AcademicYear $academicYear)
    {
        $this->settings = $settings;
        $this->academicYear = $academicYear;
    }

    public function calculateSemesterResults(Deliberation $deliberation): void
    {
        $semester = $deliberation->semester;
        $programYear = $deliberation->programYear;
        
        // Récupérer les étudiants inscrits
        $students = $this->getEnrolledStudents($programYear, $this->academicYear);
        
        foreach ($students as $student) {
            $result = $this->calculateStudentSemesterResult($student, $semester, $deliberation);
            $this->saveDeliberationResult($deliberation, $student, $result);
        }
        
        // Calculer les rangs
        $this->calculateRanks($deliberation);
    }

    public function calculateAnnualResults(Deliberation $deliberation): void
    {
        $programYear = $deliberation->programYear;
        
        $students = $this->getEnrolledStudents($programYear, $this->academicYear);
        
        foreach ($students as $student) {
            $result = $this->calculateStudentAnnualResult($student, $programYear, $deliberation);
            $this->saveDeliberationResult($deliberation, $student, $result);
        }
        
        $this->calculateRanks($deliberation);
    }

    protected function calculateStudentSemesterResult(Student $student, Semester $semester, Deliberation $deliberation): array
    {
        $ues = $semester->ues;
        $ueResults = [];
        $totalCredits = 0;
        $validatedCredits = 0;
        $weightedSum = 0;
        $totalCoef = 0;
        $uesBelowThreshold = 0;
        
        foreach ($ues as $ue) {
            $ueAverage = $this->calculateUeAverage($student, $ue);
            $ueCredits = $ue->credits_ects ?? 0;
            $totalCredits += $ueCredits;
            
            $isValidated = $ueAverage !== null && $ueAverage >= $this->settings->ue_validation_average;
            $canBeCompensated = $this->settings->ue_allow_compensation 
                && $ueAverage !== null 
                && $ueAverage >= $this->settings->ue_compensation_min;
            
            if ($isValidated) {
                $validatedCredits += $ueCredits;
            } elseif (!$canBeCompensated) {
                $uesBelowThreshold++;
            }
            
            $ueResults[] = [
                'ue_id' => $ue->id,
                'average' => $ueAverage,
                'credits' => $ueCredits,
                'is_validated' => $isValidated,
                'can_compensate' => $canBeCompensated,
            ];
            
            if ($ueAverage !== null) {
                $coef = $ueCredits > 0 ? $ueCredits : 1;
                $weightedSum += $ueAverage * $coef;
                $totalCoef += $coef;
            }
        }
        
        $semesterAverage = $totalCoef > 0 ? round($weightedSum / $totalCoef, 2) : null;
        
        // Appliquer la compensation si la moyenne semestrielle est suffisante
        if ($this->settings->semester_allow_compensation && $semesterAverage >= $this->settings->semester_validation_average) {
            foreach ($ueResults as &$ueResult) {
                if (!$ueResult['is_validated'] && $ueResult['can_compensate']) {
                    $ueResult['is_validated'] = true;
                    $ueResult['is_compensated'] = true;
                    $validatedCredits += $ueResult['credits'];
                }
            }
        }
        
        // Déterminer la décision
        $decision = $this->determineSemesterDecision(
            $semesterAverage,
            $validatedCredits,
            $totalCredits,
            $uesBelowThreshold,
            $deliberation->session
        );
        
        return [
            'semester_average' => $semesterAverage,
            'credits_validated' => $validatedCredits,
            'credits_total' => $totalCredits,
            'decision' => $decision,
            'mention' => $decision === 'validated' || $decision === 'validated_compensated' 
                ? $this->settings->getMention($semesterAverage ?? 0) 
                : null,
            'ue_results' => $ueResults,
        ];
    }

    protected function calculateStudentAnnualResult(Student $student, ProgramYear $programYear, Deliberation $deliberation): array
    {
        $semesters = $programYear->semesters;
        $totalCredits = 0;
        $validatedCredits = 0;
        $weightedSum = 0;
        $totalCoef = 0;
        $allSemestersValidated = true;
        $ueResults = [];
        
        foreach ($semesters as $semester) {
            foreach ($semester->ues as $ue) {
                $ueAverage = $this->calculateUeAverage($student, $ue);
                $ueCredits = $ue->credits_ects ?? 0;
                $totalCredits += $ueCredits;
                
                $isValidated = $ueAverage !== null && $ueAverage >= $this->settings->ue_validation_average;
                
                if ($isValidated) {
                    $validatedCredits += $ueCredits;
                }
                
                $ueResults[] = [
                    'ue_id' => $ue->id,
                    'average' => $ueAverage,
                    'credits' => $ueCredits,
                    'is_validated' => $isValidated,
                    'is_compensated' => false,
                ];
                
                if ($ueAverage !== null) {
                    $coef = $ueCredits > 0 ? $ueCredits : 1;
                    $weightedSum += $ueAverage * $coef;
                    $totalCoef += $coef;
                }
            }
        }
        
        $yearAverage = $totalCoef > 0 ? round($weightedSum / $totalCoef, 2) : null;
        $creditsFailed = $totalCredits - $validatedCredits;
        
        // Appliquer la compensation annuelle
        if ($this->settings->semester_allow_compensation && $yearAverage >= $this->settings->year_validation_average) {
            foreach ($ueResults as &$ueResult) {
                if (!$ueResult['is_validated'] && $ueResult['average'] >= $this->settings->ue_compensation_min) {
                    $ueResult['is_validated'] = true;
                    $ueResult['is_compensated'] = true;
                    $validatedCredits += $ueResult['credits'];
                }
            }
            $creditsFailed = $totalCredits - $validatedCredits;
        }
        
        $decision = $this->determineAnnualDecision(
            $yearAverage,
            $validatedCredits,
            $totalCredits,
            $creditsFailed,
            $deliberation->session
        );
        
        return [
            'year_average' => $yearAverage,
            'credits_validated' => $validatedCredits,
            'credits_total' => $totalCredits,
            'decision' => $decision,
            'mention' => in_array($decision, ['validated', 'validated_compensated']) 
                ? $this->settings->getMention($yearAverage ?? 0) 
                : null,
            'ue_results' => $ueResults,
        ];
    }

    protected function calculateUeAverage(Student $student, Ue $ue): ?float
    {
        $ecus = $ue->ecus;
        $totalCoef = 0;
        $weightedSum = 0;
        
        foreach ($ecus as $ecu) {
            $ecuAverage = $ecu->calculateStudentAverage($student, $this->academicYear);
            if ($ecuAverage !== null) {
                $coef = $ecu->coefficient ?? 1;
                $weightedSum += $ecuAverage * $coef;
                $totalCoef += $coef;
            }
        }
        
        return $totalCoef > 0 ? round($weightedSum / $totalCoef, 2) : null;
    }

    protected function determineSemesterDecision(
        ?float $average,
        int $validatedCredits,
        int $totalCredits,
        int $uesBelowThreshold,
        string $session
    ): string {
        if ($average === null) {
            return 'pending';
        }
        
        $validationRate = $totalCredits > 0 ? ($validatedCredits / $totalCredits) * 100 : 0;
        
        // Validation directe
        if ($average >= $this->settings->semester_validation_average 
            && $validationRate >= $this->settings->semester_min_ue_validated_percent) {
            return 'validated';
        }
        
        // Validation par compensation
        if ($this->settings->semester_allow_compensation 
            && $average >= $this->settings->semester_validation_average
            && $uesBelowThreshold <= $this->settings->semester_max_ue_failed) {
            return 'validated_compensated';
        }
        
        // Session normale : rattrapage
        if ($session === 'normal') {
            return 'retake';
        }
        
        // Session rattrapage : passage conditionnel ou redoublement
        $creditsFailed = $totalCredits - $validatedCredits;
        if ($this->settings->allow_conditional_pass 
            && $creditsFailed <= $this->settings->conditional_max_credits_debt) {
            return 'conditional';
        }
        
        return 'repeat';
    }

    protected function determineAnnualDecision(
        ?float $average,
        int $validatedCredits,
        int $totalCredits,
        int $creditsFailed,
        string $session
    ): string {
        if ($average === null) {
            return 'pending';
        }
        
        // Validation directe
        if ($average >= $this->settings->year_validation_average && $creditsFailed === 0) {
            return 'validated';
        }
        
        // Validation par compensation
        if ($average >= $this->settings->year_validation_average 
            && $creditsFailed <= $this->settings->year_max_credits_failed) {
            return 'validated_compensated';
        }
        
        // Session normale : rattrapage
        if ($session === 'normal') {
            return 'retake';
        }
        
        // Passage conditionnel
        if ($this->settings->allow_conditional_pass 
            && $creditsFailed <= $this->settings->conditional_max_credits_debt) {
            return 'conditional';
        }
        
        return 'repeat';
    }

    protected function saveDeliberationResult(Deliberation $deliberation, Student $student, array $data): void
    {
        $result = DeliberationResult::updateOrCreate(
            [
                'deliberation_id' => $deliberation->id,
                'student_id' => $student->id,
            ],
            [
                'semester_average' => $data['semester_average'] ?? null,
                'year_average' => $data['year_average'] ?? null,
                'credits_validated' => $data['credits_validated'],
                'credits_total' => $data['credits_total'],
                'decision' => $data['decision'],
                'mention' => $data['mention'],
            ]
        );
        
        // Sauvegarder les résultats par UE
        foreach ($data['ue_results'] as $ueData) {
            DeliberationUeResult::updateOrCreate(
                [
                    'deliberation_result_id' => $result->id,
                    'ue_id' => $ueData['ue_id'],
                ],
                [
                    'average' => $ueData['average'],
                    'credits' => $ueData['credits'],
                    'is_validated' => $ueData['is_validated'],
                    'is_compensated' => $ueData['is_compensated'] ?? false,
                ]
            );
        }
    }

    protected function calculateRanks(Deliberation $deliberation): void
    {
        $averageField = $deliberation->type === 'semester' ? 'semester_average' : 'year_average';
        
        $results = $deliberation->results()
            ->whereNotNull($averageField)
            ->orderByDesc($averageField)
            ->get();
        
        $rank = 1;
        $previousAverage = null;
        $sameRankCount = 0;
        
        foreach ($results as $result) {
            $currentAverage = $result->$averageField;
            
            if ($previousAverage !== null && $currentAverage < $previousAverage) {
                $rank += $sameRankCount;
                $sameRankCount = 1;
            } else {
                $sameRankCount++;
            }
            
            $result->update(['rank' => $rank]);
            $previousAverage = $currentAverage;
        }
    }

    protected function getEnrolledStudents(ProgramYear $programYear, AcademicYear $academicYear): Collection
    {
        return Student::where('university_id', $programYear->program->university_id)
            ->where(function ($query) use ($programYear, $academicYear) {
                $query->whereHas('enrollments', function ($q) use ($programYear, $academicYear) {
                    $q->where('academic_year_id', $academicYear->id)
                      ->where('program_year_id', $programYear->id);
                })
                ->orWhereHas('pedagogicEnrollments', function ($q) use ($programYear, $academicYear) {
                    $q->where('academic_year_id', $academicYear->id)
                      ->where('program_year_id', $programYear->id);
                });
            })
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();
    }
}
