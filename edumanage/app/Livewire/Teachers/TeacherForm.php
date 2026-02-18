<?php

namespace App\Livewire\Teachers;

use App\Models\Teacher;
use Livewire\Component;
use Livewire\WithFileUploads;

class TeacherForm extends Component
{
    use WithFileUploads;

    public ?int $teacherId = null;
    public bool $editMode = false;

    public string $employee_id = '';
    public string $first_name = '';
    public string $last_name = '';
    public ?string $gender = null;
    public string $email = '';
    public ?string $phone = '';
    public ?string $specialization = '';
    public string $type = 'permanent';
    public ?string $grade = '';
    public ?string $title = '';
    public ?string $rib = '';
    public ?string $ifu = '';
    public $cv_file = null;
    public $rib_file = null;
    public $ifu_file = null;
    public ?string $existing_cv = null;
    public ?string $existing_rib_file = null;
    public ?string $existing_ifu_file = null;
    public string $status = 'active';
    public ?string $hire_date = null;
    public $photo = null;

    protected function rules(): array
    {
        $teacherId = $this->teacherId;
        $universityId = auth()->user()->university_id;

        return [
            'employee_id' => "required|string|max:50|unique:teachers,employee_id,{$teacherId},id,university_id,{$universityId}",
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'gender' => 'nullable|in:M,F',
            'email' => "required|email|unique:teachers,email,{$teacherId},id,university_id,{$universityId}",
            'phone' => 'nullable|string|max:20',
            'specialization' => 'nullable|string|max:200',
            'type' => 'required|in:permanent,temporary,vacataire',
            'grade' => 'nullable|string|max:100',
            'title' => 'nullable|string|max:100',
            'rib' => 'nullable|string|max:50',
            'ifu' => 'nullable|string|max:50',
            'cv_file' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
            'rib_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'ifu_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'status' => 'required|in:active,inactive,on_leave',
            'hire_date' => 'nullable|date',
            'photo' => 'nullable|image|max:2048',
        ];
    }

    public function mount($teacherId = null): void
    {
        if ($teacherId) {
            $teacher = Teacher::find($teacherId);
            
            if ($teacher) {
                $this->teacherId = $teacher->id;
                $this->editMode = true;
                $this->employee_id = $teacher->employee_id ?? '';
                $this->first_name = $teacher->first_name ?? '';
                $this->last_name = $teacher->last_name ?? '';
                $this->gender = $teacher->gender;
                $this->email = $teacher->email ?? '';
                $this->phone = $teacher->phone ?? '';
                $this->specialization = $teacher->specialization ?? '';
                $this->type = $teacher->type ?? 'permanent';
                $this->grade = $teacher->grade ?? '';
                $this->title = $teacher->title ?? '';
                $this->rib = $teacher->rib ?? '';
                $this->ifu = $teacher->ifu ?? '';
                $this->existing_cv = $teacher->cv_file;
                $this->existing_rib_file = $teacher->rib_file;
                $this->existing_ifu_file = $teacher->ifu_file;
                $this->status = $teacher->status ?? 'active';
                $this->hire_date = $teacher->hire_date?->format('Y-m-d');
            }
        }
    }

    public function save()
    {
        $validated = $this->validate();

        if ($this->photo) {
            $validated['photo'] = $this->photo->store('teachers/photos', 'public');
        }
        if ($this->cv_file) {
            $validated['cv_file'] = $this->cv_file->store('teachers/cv', 'public');
        }
        if ($this->rib_file) {
            $validated['rib_file'] = $this->rib_file->store('teachers/rib', 'public');
        }
        if ($this->ifu_file) {
            $validated['ifu_file'] = $this->ifu_file->store('teachers/ifu', 'public');
        }

        if ($this->editMode && $this->teacherId) {
            $teacher = Teacher::findOrFail($this->teacherId);
            $teacher->update($validated);
            session()->flash('success', __('Enseignant mis à jour avec succès.'));
        } else {
            $validated['university_id'] = auth()->user()->university_id;
            Teacher::create($validated);
            session()->flash('success', __('Enseignant créé avec succès.'));
        }

        return redirect()->route('teachers.index');
    }

    public function render()
    {
        return view('livewire.teachers.teacher-form');
    }
}
