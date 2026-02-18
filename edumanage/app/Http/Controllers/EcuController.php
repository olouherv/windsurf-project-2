<?php

namespace App\Http\Controllers;

use App\Models\Ecu;
use App\Models\Ue;
use Illuminate\View\View;

class EcuController extends Controller
{
    public function create(Ue $ue): View
    {
        $ue->load('semester.programYear.program');
        return view('ecus.create', compact('ue'));
    }

    public function show(Ecu $ecu): View
    {
        $ecu->load(['ue.semester.programYear.program', 'teachers']);
        return view('ecus.show', compact('ecu'));
    }

    public function edit(Ecu $ecu): View
    {
        $ecu->load('ue.semester.programYear.program');
        return view('ecus.edit', compact('ecu'));
    }

    public function destroy(Ecu $ecu)
    {
        $ueId = $ecu->ue_id;
        
        $ecu->delete();
        
        session()->flash('success', __('ECU supprimé avec succès.'));
        
        return redirect()->route('ues.show', $ueId);
    }
}
