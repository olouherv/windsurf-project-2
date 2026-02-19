<x-layouts.admin :title="__('Inscriptions')">
    <x-slot name="header">{{ __('Inscriptions pédagogiques') }}</x-slot>

    <div class="w-full">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            @livewire('enrollments.student-enrollment-manager')
        </div>
    </div>
</x-layouts.admin>
