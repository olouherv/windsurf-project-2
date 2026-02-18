<?php

namespace App\Http\Controllers;

use App\Models\Teacher;
use App\Models\VacataireContract;
use Illuminate\View\View;

class VacataireContractController extends Controller
{
    public function index(): View
    {
        return view('vacataire-contracts.index');
    }

    public function create(?Teacher $teacher = null): View
    {
        return view('vacataire-contracts.create', compact('teacher'));
    }

    public function show(VacataireContract $vacataireContract): View
    {
        $vacataireContract->load(['teacher', 'academicYear', 'ecu.ue', 'hours.ecu', 'hours.validatedBy']);
        return view('vacataire-contracts.show', compact('vacataireContract'));
    }

    public function edit(VacataireContract $vacataireContract): View
    {
        $vacataireContract->load(['teacher', 'academicYear', 'ecu']);
        return view('vacataire-contracts.edit', compact('vacataireContract'));
    }
}
