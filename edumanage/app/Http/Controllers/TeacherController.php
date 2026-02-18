<?php

namespace App\Http\Controllers;

use App\Models\Teacher;
use Illuminate\View\View;

class TeacherController extends Controller
{
    public function index(): View
    {
        return view('teachers.index');
    }

    public function show(Teacher $teacher): View
    {
        $teacher->load(['ecus.ue.semester', 'schedules', 'vacataireContracts']);
        return view('teachers.show', compact('teacher'));
    }

    public function create(): View
    {
        return view('teachers.create');
    }

    public function edit(Teacher $teacher): View
    {
        return view('teachers.edit', compact('teacher'));
    }
}
