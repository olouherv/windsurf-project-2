<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Thesis;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ThesisController extends Controller
{
    public function index(): View
    {
        $theses = Thesis::query()
            ->with(['student', 'academicYear', 'supervisor'])
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('theses.index', compact('theses'));
    }

    public function create(): View
    {
        $universityId = auth()->user()->university_id;

        return view('theses.create', [
            'students' => Student::where('university_id', $universityId)->orderBy('last_name')->orderBy('first_name')->get(),
            'academicYears' => AcademicYear::where('university_id', $universityId)->orderByDesc('start_date')->get(),
            'teachers' => Teacher::where('university_id', $universityId)->orderBy('last_name')->orderBy('first_name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $universityId = auth()->user()->university_id;

        $data = $request->validate([
            'student_id' => ['required', 'exists:students,id'],
            'academic_year_id' => ['nullable', 'exists:academic_years,id'],
            'supervisor_teacher_id' => ['nullable', 'exists:teachers,id'],
            'title' => ['required', 'string', 'max:255'],
            'abstract' => ['nullable', 'string'],
            'submission_date' => ['nullable', 'date'],
            'defense_date' => ['nullable', 'date'],
            'grade' => ['nullable', 'numeric', 'min:0', 'max:20'],
            'status' => ['required', 'in:draft,in_progress,submitted,defended,cancelled'],
            'notes' => ['nullable', 'string'],
        ]);

        $data['university_id'] = $universityId;

        Thesis::create($data);

        return redirect()->route('theses.index')->with('success', 'Mémoire créé.');
    }

    public function show(Thesis $thesis): View
    {
        $thesis->load(['student', 'academicYear', 'supervisor']);

        return view('theses.show', compact('thesis'));
    }

    public function edit(Thesis $thesis): View
    {
        $universityId = auth()->user()->university_id;

        return view('theses.edit', [
            'thesis' => $thesis,
            'students' => Student::where('university_id', $universityId)->orderBy('last_name')->orderBy('first_name')->get(),
            'academicYears' => AcademicYear::where('university_id', $universityId)->orderByDesc('start_date')->get(),
            'teachers' => Teacher::where('university_id', $universityId)->orderBy('last_name')->orderBy('first_name')->get(),
        ]);
    }

    public function update(Request $request, Thesis $thesis): RedirectResponse
    {
        $data = $request->validate([
            'student_id' => ['required', 'exists:students,id'],
            'academic_year_id' => ['nullable', 'exists:academic_years,id'],
            'supervisor_teacher_id' => ['nullable', 'exists:teachers,id'],
            'title' => ['required', 'string', 'max:255'],
            'abstract' => ['nullable', 'string'],
            'submission_date' => ['nullable', 'date'],
            'defense_date' => ['nullable', 'date'],
            'grade' => ['nullable', 'numeric', 'min:0', 'max:20'],
            'status' => ['required', 'in:draft,in_progress,submitted,defended,cancelled'],
            'notes' => ['nullable', 'string'],
        ]);

        $thesis->update($data);

        return redirect()->route('theses.show', $thesis)->with('success', 'Mémoire mis à jour.');
    }

    public function destroy(Thesis $thesis): RedirectResponse
    {
        $thesis->delete();

        return redirect()->route('theses.index')->with('success', 'Mémoire supprimé.');
    }
}
