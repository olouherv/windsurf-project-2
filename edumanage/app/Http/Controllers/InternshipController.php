<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\Internship;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InternshipController extends Controller
{
    public function index(): View
    {
        $internships = Internship::query()
            ->with(['student', 'academicYear', 'supervisor'])
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('internships.index', compact('internships'));
    }

    public function create(): View
    {
        $universityId = auth()->user()->university_id;

        return view('internships.create', [
            'academicYears' => AcademicYear::where('university_id', $universityId)->orderByDesc('start_date')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $universityId = auth()->user()->university_id;

        $data = $request->validate([
            'student_id' => ['required', 'exists:students,id'],
            'academic_year_id' => ['nullable', 'exists:academic_years,id'],
            'supervisor_teacher_id' => ['nullable', 'exists:teachers,id'],
            'company_name' => ['required', 'string', 'max:255'],
            'company_address' => ['nullable', 'string', 'max:255'],
            'company_contact_name' => ['nullable', 'string', 'max:255'],
            'company_contact_email' => ['nullable', 'email', 'max:255'],
            'company_contact_phone' => ['nullable', 'string', 'max:255'],
            'topic' => ['nullable', 'string', 'max:255'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'status' => ['required', 'in:draft,active,completed,cancelled'],
            'notes' => ['nullable', 'string'],
        ]);

        $data['university_id'] = $universityId;

        Internship::create($data);

        return redirect()->route('internships.index')->with('success', 'Stage créé.');
    }

    public function show(Internship $internship): View
    {
        $internship->load(['student', 'academicYear', 'supervisor']);

        return view('internships.show', compact('internship'));
    }

    public function edit(Internship $internship): View
    {
        $universityId = auth()->user()->university_id;

        return view('internships.edit', [
            'internship' => $internship,
            'academicYears' => AcademicYear::where('university_id', $universityId)->orderByDesc('start_date')->get(),
        ]);
    }

    public function update(Request $request, Internship $internship): RedirectResponse
    {
        $data = $request->validate([
            'student_id' => ['required', 'exists:students,id'],
            'academic_year_id' => ['nullable', 'exists:academic_years,id'],
            'supervisor_teacher_id' => ['nullable', 'exists:teachers,id'],
            'company_name' => ['required', 'string', 'max:255'],
            'company_address' => ['nullable', 'string', 'max:255'],
            'company_contact_name' => ['nullable', 'string', 'max:255'],
            'company_contact_email' => ['nullable', 'email', 'max:255'],
            'company_contact_phone' => ['nullable', 'string', 'max:255'],
            'topic' => ['nullable', 'string', 'max:255'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'status' => ['required', 'in:draft,active,completed,cancelled'],
            'notes' => ['nullable', 'string'],
        ]);

        $internship->update($data);

        return redirect()->route('internships.show', $internship)->with('success', 'Stage mis à jour.');
    }

    public function destroy(Internship $internship): RedirectResponse
    {
        $internship->delete();

        return redirect()->route('internships.index')->with('success', 'Stage supprimé.');
    }
}
