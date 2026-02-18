<?php

namespace App\Livewire\Contracts;

use App\Models\AcademicYear;
use App\Models\StudentContract;
use Livewire\Component;
use Livewire\WithPagination;

class ContractList extends Component
{
    use WithPagination;

    public string $search = '';
    public string $status = '';
    public string $paymentStatus = '';
    public string $type = '';
    public string $academicYearId = '';

    protected $queryString = ['search', 'status', 'paymentStatus', 'type', 'academicYearId'];

    public function mount(): void
    {
        if (!$this->academicYearId) {
            $currentYear = AcademicYear::where('is_current', true)->first();
            $this->academicYearId = $currentYear?->id ?? '';
        }
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingAcademicYearId()
    {
        $this->resetPage();
    }

    public function render()
    {
        $contracts = StudentContract::query()
            ->with(['student', 'academicYear', 'programYear.program'])
            ->when($this->academicYearId, fn($q) => $q->where('academic_year_id', $this->academicYearId))
            ->when($this->search, function ($query) {
                $query->whereHas('student', function ($q) {
                    $q->where('first_name', 'like', "%{$this->search}%")
                      ->orWhere('last_name', 'like', "%{$this->search}%")
                      ->orWhere('student_id', 'like', "%{$this->search}%");
                })->orWhere('contract_number', 'like', "%{$this->search}%");
            })
            ->when($this->status, fn($q) => $q->where('status', $this->status))
            ->when($this->paymentStatus, fn($q) => $q->where('payment_status', $this->paymentStatus))
            ->when($this->type, fn($q) => $q->where('type', $this->type))
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('livewire.contracts.contract-list', [
            'contracts' => $contracts,
            'academicYears' => AcademicYear::orderBy('start_date', 'desc')->get(),
        ]);
    }
}
