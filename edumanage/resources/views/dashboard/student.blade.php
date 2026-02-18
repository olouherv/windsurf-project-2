<x-layouts.admin :title="__('Espace Étudiant')">
    <x-slot name="header">{{ __('Mon espace étudiant') }}</x-slot>

    @if($student)
    <!-- Welcome Card -->
    <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl shadow-sm p-6 mb-6 text-white">
        <h2 class="text-2xl font-bold">{{ __('Bienvenue') }}, {{ $student->full_name }}</h2>
        <p class="text-blue-100 mt-1">{{ __('Matricule') }}: {{ $student->student_id }}</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Inscription actuelle -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="p-6 border-b border-gray-100">
                <h3 class="text-lg font-semibold text-gray-900">{{ __('Mon inscription') }}</h3>
            </div>
            <div class="p-6">
                @if($enrollment)
                <div class="space-y-4">
                    <div>
                        <p class="text-sm text-gray-500">{{ __('Programme') }}</p>
                        <p class="font-medium text-gray-900">{{ $enrollment->programYear->program->name ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">{{ __('Année') }}</p>
                        <p class="font-medium text-gray-900">{{ $enrollment->programYear->name ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">{{ __('Année académique') }}</p>
                        <p class="font-medium text-gray-900">{{ $enrollment->academicYear->name ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">{{ __('Statut') }}</p>
                        <span class="px-2 py-1 text-xs rounded-full {{ $enrollment->status === 'confirmed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                            {{ __($enrollment->status) }}
                        </span>
                    </div>
                </div>
                @else
                <p class="text-center text-gray-500 py-4">{{ __('Aucune inscription active.') }}</p>
                @endif
            </div>
        </div>

        <!-- Dernières notes -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="p-6 border-b border-gray-100">
                <h3 class="text-lg font-semibold text-gray-900">{{ __('Mes dernières notes') }}</h3>
            </div>
            <div class="p-6">
                @forelse($grades as $grade)
                <div class="flex items-center justify-between py-3 {{ !$loop->last ? 'border-b border-gray-100' : '' }}">
                    <div>
                        <p class="font-medium text-gray-900">{{ $grade->evaluation->name ?? 'N/A' }}</p>
                        <p class="text-sm text-gray-500">{{ $grade->evaluation->ecu->name ?? '' }}</p>
                    </div>
                    <div class="text-right">
                        @if($grade->is_absent)
                            <span class="text-red-600 font-medium">{{ __('Absent') }}</span>
                        @elseif($grade->score !== null)
                            <span class="text-lg font-bold {{ $grade->normalized_score >= 10 ? 'text-green-600' : 'text-red-600' }}">
                                {{ number_format($grade->score, 2) }}/{{ $grade->evaluation->max_score }}
                            </span>
                        @else
                            <span class="text-gray-400">-</span>
                        @endif
                    </div>
                </div>
                @empty
                <p class="text-center text-gray-500 py-4">{{ __('Aucune note disponible.') }}</p>
                @endforelse
            </div>
        </div>
    </div>
    @else
    <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-6 text-center">
        <p class="text-yellow-800">{{ __('Votre profil étudiant n\'est pas encore configuré.') }}</p>
    </div>
    @endif
</x-layouts.admin>
