<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\View\View;

class StudentController extends Controller
{
    public function index(): View
    {
        return view('students.index');
    }

    public function show(Student $student): View
    {
        $student->load([
            'enrollments.programYear.program',
            'enrollments.academicYear',
            'grades.evaluation.ecu',
        ]);
        return view('students.show', compact('student'));
    }

    public function create(): View
    {
        return view('students.create');
    }

    public function edit(Student $student): View
    {
        return view('students.edit', compact('student'));
    }
}
