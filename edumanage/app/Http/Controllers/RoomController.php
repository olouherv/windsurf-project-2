<?php

namespace App\Http\Controllers;

use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RoomController extends Controller
{
    public function index(): View
    {
        return view('rooms.index');
    }

    public function create(): View
    {
        return view('rooms.create');
    }

    public function show(Room $room, Request $request): View
    {
        $academicYearId = $request->integer('academic_year_id');
        return view('rooms.show', compact('room', 'academicYearId'));
    }

    public function edit(Room $room): View
    {
        return view('rooms.edit', compact('room'));
    }
}
