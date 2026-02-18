<?php

namespace App\Livewire\Evaluations;

use App\Models\Evaluation;
use App\Models\Grade;
use App\Models\Student;
use App\Models\StudentEnrollment;
use Livewire\Component;

class GradeEntry extends Component
{
    public Evaluation $evaluation;
    public array $grades = [];
    public string $search = '';

    public function mount(Evaluation $evaluation): void
    {
        $this->evaluation = $evaluation;
        $this->loadGrades();
    }

    protected function loadGrades(): void
    {
        $programYearId = $this->evaluation->ecu->ue->semester->program_year_id;
        
        $enrolledStudents = StudentEnrollment::where('program_year_id', $programYearId)
            ->where('academic_year_id', $this->evaluation->academic_year_id)
            ->where('status', '!=', 'abandoned')
            ->with('student')
            ->get();

        foreach ($enrolledStudents as $enrollment) {
            $existingGrade = Grade::where('evaluation_id', $this->evaluation->id)
                ->where('student_id', $enrollment->student_id)
                ->first();

            $this->grades[$enrollment->student_id] = [
                'student_id' => $enrollment->student_id,
                'student_name' => $enrollment->student->full_name,
                'student_number' => $enrollment->student->student_id,
                'score' => $existingGrade?->score,
                'is_absent' => $existingGrade?->is_absent ?? false,
                'comment' => $existingGrade?->comment ?? '',
            ];
        }
    }

    public function updatedGrades($value, $key): void
    {
        [$studentId, $field] = explode('.', $key);
        
        if ($field === 'is_absent' && $value) {
            $this->grades[$studentId]['score'] = null;
        }
    }

    public function saveGrades()
    {
        foreach ($this->grades as $studentId => $data) {
            $gradeData = [
                'evaluation_id' => $this->evaluation->id,
                'student_id' => $studentId,
                'score' => $data['is_absent'] ? null : $data['score'],
                'is_absent' => $data['is_absent'],
                'comment' => $data['comment'] ?? null,
                'graded_by' => auth()->id(),
                'graded_at' => now(),
            ];

            Grade::updateOrCreate(
                ['evaluation_id' => $this->evaluation->id, 'student_id' => $studentId],
                $gradeData
            );

        }

        session()->flash('success', __('Notes enregistrées avec succès.'));
    }

    public function publishGrades()
    {
        $this->saveGrades();
        $this->evaluation->update(['is_published' => true]);
        session()->flash('success', __('Notes publiées avec succès.'));
    }

    public function render()
    {
        $filteredGrades = collect($this->grades);
        
        if ($this->search) {
            $filteredGrades = $filteredGrades->filter(function ($grade) {
                return str_contains(strtolower($grade['student_name']), strtolower($this->search))
                    || str_contains(strtolower($grade['student_number']), strtolower($this->search));
            });
        }

        $stats = [
            'total' => count($this->grades),
            'graded' => collect($this->grades)->filter(fn($g) => $g['score'] !== null || $g['is_absent'])->count(),
            'average' => collect($this->grades)->filter(fn($g) => $g['score'] !== null)->avg('score'),
            'absent' => collect($this->grades)->filter(fn($g) => $g['is_absent'])->count(),
        ];

        return view('livewire.evaluations.grade-entry', [
            'filteredGrades' => $filteredGrades,
            'stats' => $stats,
        ]);
    }
}
