<?php

namespace App\Http\Controllers;

use App\Models\Program;
use App\Models\ProgramYear;
use Illuminate\View\View;

class ProgramYearController extends Controller
{
    public function index(Program $program): View
    {
        $program->load('programYears.semesters.ues.ecus');
        return view('programs.years.index', compact('program'));
    }

    public function create(Program $program): View
    {
        return view('programs.years.create', compact('program'));
    }

    public function show(Program $program, ProgramYear $year): View
    {
        $year->load('semesters.ues.ecus');
        return view('programs.years.show', compact('program', 'year'));
    }

    public function edit(Program $program, ProgramYear $year): View
    {
        return view('programs.years.edit', compact('program', 'year'));
    }
}
