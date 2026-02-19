<?php

namespace App\Http\Controllers;

use App\Models\StudentEnrollment;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Program;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();
        
        if ($user->isSuperAdmin()) {
            return $this->superAdminDashboard();
        }

        if ($user->isAdmin() || $user->isSecretary()) {
            return $this->adminDashboard();
        }

        if ($user->isTeacher()) {
            return $this->teacherDashboard();
        }

        return $this->studentDashboard();
    }

    protected function superAdminDashboard(): View
    {
        $stats = [
            'universities' => \App\Models\University::count(),
            'total_students' => Student::withoutGlobalScopes()->count(),
            'total_teachers' => Teacher::withoutGlobalScopes()->count(),
            'total_programs' => Program::withoutGlobalScopes()->count(),
        ];

        return view('dashboard.super-admin', compact('stats'));
    }

    protected function adminDashboard(): View
    {
        $university = auth()->user()->university;
        $currentYear = $university->getCurrentAcademicYear();

        $stats = [
            'students' => Student::count(),
            'teachers' => Teacher::count(),
            'programs' => Program::where('is_active', true)->count(),
            'enrollments' => $currentYear 
                ? StudentEnrollment::where('academic_year_id', $currentYear->id)
                    ->whereIn('status', ['enrolled', 'validated'])
                    ->count() 
                : 0,
        ];

        $recentStudents = Student::latest()->take(5)->get();
        $recentTeachers = Teacher::latest()->take(5)->get();

        return view('dashboard.admin', compact('stats', 'recentStudents', 'recentTeachers', 'university'));
    }

    protected function teacherDashboard(): View
    {
        $teacher = auth()->user()->teacher;
        $university = auth()->user()->university;
        $currentYear = $university?->getCurrentAcademicYear();

        $data = [
            'teacher' => $teacher,
            'ecus' => $teacher ? $teacher->ecus()->wherePivot('academic_year_id', $currentYear?->id)->get() : collect(),
            'schedules' => $teacher ? $teacher->schedules()->where('academic_year_id', $currentYear?->id)->get() : collect(),
        ];

        return view('dashboard.teacher', $data);
    }

    protected function studentDashboard(): View
    {
        $student = auth()->user()->student;
        $university = auth()->user()->university;
        $currentYear = $university?->getCurrentAcademicYear();

        $enrollment = $student?->getCurrentEnrollment();

        $data = [
            'student' => $student,
            'enrollment' => $enrollment,
            'grades' => $student ? $student->grades()->with('evaluation.ecu')->latest()->take(10)->get() : collect(),
        ];

        return view('dashboard.student', $data);
    }
}
