<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\StudentContract;
use Illuminate\View\View;

class StudentContractController extends Controller
{
    public function index(): View
    {
        return view('contracts.index');
    }

    public function create(?Student $student = null): View
    {
        return view('contracts.create', compact('student'));
    }

    public function show(StudentContract $contract): View
    {
        $contract->load(['student', 'academicYear', 'programYear.program', 'payments.recordedBy', 'payments.paymentSchedule', 'paymentSchedules']);
        return view('contracts.show', compact('contract'));
    }

    public function edit(StudentContract $contract): View
    {
        $contract->load(['student', 'academicYear', 'programYear']);
        return view('contracts.edit', compact('contract'));
    }
}
