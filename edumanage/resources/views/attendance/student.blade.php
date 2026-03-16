<x-layouts.admin :title="__('Présences de') . ' ' . $student->full_name">
    <x-slot name="header">{{ __('Présences de') }} {{ $student->full_name }}</x-slot>

    <div class="mb-4">
        <a href="{{ route('students.show', $student) }}" class="text-indigo-600 hover:text-indigo-900 text-sm">
            ← Retour à la fiche étudiant
        </a>
    </div>

    @livewire('attendance.student-attendance-history', ['student' => $student])
</x-layouts.admin>
