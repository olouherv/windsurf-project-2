<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use Illuminate\View\View;

class EquipmentController extends Controller
{
    public function index(): View
    {
        return view('equipments.index');
    }

    public function create(): View
    {
        return view('equipments.create');
    }

    public function edit(Equipment $equipment): View
    {
        return view('equipments.edit', compact('equipment'));
    }
}
