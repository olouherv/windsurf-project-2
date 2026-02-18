<x-layouts.admin :title="__('Modifier étudiant')">
    <x-slot name="header">{{ __('Modifier l\'étudiant') }}: {{ $student->full_name }}</x-slot>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        <div class="p-6 border-b border-gray-100">
            <h3 class="text-lg font-semibold text-gray-900">{{ __('Informations de l\'étudiant') }}</h3>
        </div>
        <livewire:students.student-form :studentId="$student->id" />
    </div>
</x-layouts.admin>
