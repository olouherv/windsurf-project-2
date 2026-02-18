<?php

namespace App\Http\Controllers;

use App\Models\Semester;
use App\Models\Ue;
use Illuminate\View\View;

class UeController extends Controller
{
    public function create(Semester $semester): View
    {
        $semester->load('programYear.program');
        return view('ues.create', compact('semester'));
    }

    public function show(Ue $ue): View
    {
        $ue->load(['semester.programYear.program', 'ecus']);
        return view('ues.show', compact('ue'));
    }

    public function edit(Ue $ue): View
    {
        $ue->load('semester.programYear.program');
        return view('ues.edit', compact('ue'));
    }

    public function destroy(Ue $ue)
    {
        $semester = $ue->semester;
        $programYear = $semester->programYear;
        
        $ue->delete();
        
        session()->flash('success', __('UE supprimée avec succès.'));
        
        return redirect()->route('programs.years.show', [
            'program' => $programYear->program_id,
            'year' => $programYear->id
        ]);
    }
}
