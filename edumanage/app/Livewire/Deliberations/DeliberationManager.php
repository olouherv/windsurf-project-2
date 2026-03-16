<?php

namespace App\Livewire\Deliberations;

use App\Models\AcademicYear;
use App\Models\Deliberation;
use App\Models\DeliberationSetting;
use App\Models\Program;
use App\Models\ProgramYear;
use App\Models\Semester;
use App\Services\DeliberationService;
use Livewire\Component;
use Livewire\WithPagination;

class DeliberationManager extends Component
{
    use WithPagination;

    public ?int $academicYearId = null;
    public ?int $programId = null;
    public ?int $programYearId = null;
    public string $typeFilter = '';

    // Création délibération
    public bool $showCreateModal = false;
    public string $deliberationType = 'semester';
    public ?int $semesterId = null;
    public string $session = 'normal';
    public ?string $deliberationDate = null;

    // Détails
    public bool $showDetailsModal = false;
    public ?Deliberation $selectedDeliberation = null;

    public function mount(): void
    {
        $currentYear = AcademicYear::where('university_id', auth()->user()->university_id)
            ->where('is_current', true)
            ->first();
        $this->academicYearId = $currentYear?->id;
        $this->deliberationDate = now()->format('Y-m-d');
    }

    public function updatedProgramId(): void
    {
        $this->reset(['programYearId', 'semesterId']);
    }

    public function updatedProgramYearId(): void
    {
        $this->reset(['semesterId']);
    }

    public function updatedDeliberationType(): void
    {
        if ($this->deliberationType === 'annual') {
            $this->semesterId = null;
        }
    }

    public function openCreateModal(): void
    {
        $this->reset(['deliberationType', 'semesterId', 'session']);
        $this->deliberationType = 'semester';
        $this->session = 'normal';
        $this->deliberationDate = now()->format('Y-m-d');
        $this->showCreateModal = true;
    }

    public function closeCreateModal(): void
    {
        $this->showCreateModal = false;
    }

    public function createDeliberation(): void
    {
        $this->validate([
            'academicYearId' => 'required|exists:academic_years,id',
            'programYearId' => 'required|exists:program_years,id',
            'deliberationType' => 'required|in:semester,annual',
            'semesterId' => 'required_if:deliberationType,semester|nullable|exists:semesters,id',
            'session' => 'required|in:normal,rattrapage',
            'deliberationDate' => 'required|date',
        ]);

        $deliberation = Deliberation::create([
            'university_id' => auth()->user()->university_id,
            'academic_year_id' => $this->academicYearId,
            'program_year_id' => $this->programYearId,
            'semester_id' => $this->deliberationType === 'semester' ? $this->semesterId : null,
            'type' => $this->deliberationType,
            'session' => $this->session,
            'deliberation_date' => $this->deliberationDate,
            'status' => 'draft',
        ]);

        session()->flash('success', 'Délibération créée. Lancez le calcul pour générer les résultats.');
        $this->closeCreateModal();
    }

    public function calculateResults(int $deliberationId): void
    {
        $deliberation = Deliberation::findOrFail($deliberationId);
        $settings = DeliberationSetting::getOrCreateForUniversity(auth()->user()->university_id);
        $academicYear = AcademicYear::findOrFail($deliberation->academic_year_id);

        $service = new DeliberationService($settings, $academicYear);

        if ($deliberation->type === 'semester') {
            $service->calculateSemesterResults($deliberation);
        } else {
            $service->calculateAnnualResults($deliberation);
        }

        $deliberation->update(['status' => 'in_progress']);
        session()->flash('success', 'Résultats calculés. Vous pouvez maintenant les consulter et valider.');
    }

    public function validateDeliberation(int $deliberationId): void
    {
        $deliberation = Deliberation::findOrFail($deliberationId);
        $deliberation->validate();
        session()->flash('success', 'Délibération validée.');
    }

    public function publishDeliberation(int $deliberationId): void
    {
        $deliberation = Deliberation::findOrFail($deliberationId);
        $deliberation->publish();
        session()->flash('success', 'Délibération publiée. Les résultats sont maintenant visibles.');
    }

    public function openDetails(int $deliberationId): void
    {
        $this->selectedDeliberation = Deliberation::with([
            'results.student',
            'results.ueResults.ue',
            'programYear.program',
            'semester',
            'academicYear',
        ])->find($deliberationId);
        $this->showDetailsModal = true;
    }

    public function closeDetails(): void
    {
        $this->showDetailsModal = false;
        $this->selectedDeliberation = null;
    }

    public function deleteDeliberation(int $deliberationId): void
    {
        Deliberation::whereKey($deliberationId)->delete();
        session()->flash('success', 'Délibération supprimée.');
    }

    public function getAcademicYearsProperty()
    {
        return AcademicYear::where('university_id', auth()->user()->university_id)
            ->orderByDesc('start_date')
            ->get();
    }

    public function getProgramsProperty()
    {
        return Program::where('university_id', auth()->user()->university_id)
            ->orderBy('name')
            ->get();
    }

    public function getProgramYearsProperty()
    {
        if (!$this->programId) {
            return collect();
        }
        return ProgramYear::where('program_id', $this->programId)
            ->orderBy('year_number')
            ->get();
    }

    public function getSemestersProperty()
    {
        if (!$this->programYearId) {
            return collect();
        }
        return Semester::where('program_year_id', $this->programYearId)
            ->orderBy('number')
            ->get();
    }

    public function getDeliberationsProperty()
    {
        $query = Deliberation::with(['programYear.program', 'semester', 'academicYear'])
            ->where('university_id', auth()->user()->university_id);

        if ($this->academicYearId) {
            $query->where('academic_year_id', $this->academicYearId);
        }

        if ($this->programYearId) {
            $query->where('program_year_id', $this->programYearId);
        }

        if ($this->typeFilter) {
            $query->where('type', $this->typeFilter);
        }

        return $query->orderByDesc('deliberation_date')->paginate(15);
    }

    public function render()
    {
        return view('livewire.deliberations.deliberation-manager', [
            'academicYears' => $this->academicYears,
            'programs' => $this->programs,
            'programYears' => $this->programYears,
            'semesters' => $this->semesters,
            'deliberations' => $this->deliberations,
        ]);
    }
}
