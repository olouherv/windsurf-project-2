<?php

namespace App\Http\Controllers;

use App\Models\Program;
use Illuminate\View\View;

class ProgramController extends Controller
{
    public function index(): View
    {
        return view('programs.index');
    }

    public function show(Program $program): View
    {
        $program->load(['programYears.semesters.ues.ecus']);
        return view('programs.show', compact('program'));
    }

    public function create(): View
    {
        return view('programs.create');
    }

    public function edit(Program $program): View
    {
        return view('programs.edit', compact('program'));
    }
}
