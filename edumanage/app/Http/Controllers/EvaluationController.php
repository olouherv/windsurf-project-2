<?php

namespace App\Http\Controllers;

use App\Models\Ecu;
use App\Models\Evaluation;
use Illuminate\View\View;

class EvaluationController extends Controller
{
    public function index(): View
    {
        return view('evaluations.index');
    }

    public function create(?Ecu $ecu = null): View
    {
        return view('evaluations.create', compact('ecu'));
    }

    public function show(Evaluation $evaluation): View
    {
        $evaluation->load(['ecu.ue', 'academicYear', 'grades.student']);
        return view('evaluations.show', compact('evaluation'));
    }

    public function edit(Evaluation $evaluation): View
    {
        $evaluation->load(['ecu', 'academicYear']);
        return view('evaluations.edit', compact('evaluation'));
    }

    public function grades(Evaluation $evaluation): View
    {
        $evaluation->load(['ecu.ue', 'academicYear', 'grades.student']);
        return view('evaluations.grades', compact('evaluation'));
    }
}
