<x-layouts.admin :title="__('Tableau de bord')">
    <x-slot name="header">{{ __('Tableau de bord') }}</x-slot>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">{{ __('Étudiants') }}</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['students']) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-600">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">{{ __('Enseignants') }}</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['teachers']) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">{{ __('Programmes') }}</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['programs']) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-orange-100 text-orange-600">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">{{ __('Inscriptions') }}</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['enrollments']) }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Students -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="p-6 border-b border-gray-100">
                <h3 class="text-lg font-semibold text-gray-900">{{ __('Derniers étudiants inscrits') }}</h3>
            </div>
            <div class="p-6">
                @forelse($recentStudents as $student)
                <div class="flex items-center py-3 {{ !$loop->last ? 'border-b border-gray-100' : '' }}">
                    <div class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 font-semibold">
                        {{ strtoupper(substr($student->first_name, 0, 1) . substr($student->last_name, 0, 1)) }}
                    </div>
                    <div class="ml-4 flex-1">
                        <p class="font-medium text-gray-900">{{ $student->full_name }}</p>
                        <p class="text-sm text-gray-500">{{ $student->student_id }}</p>
                    </div>
                    <span class="px-2 py-1 text-xs rounded-full {{ $student->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                        {{ __($student->status) }}
                    </span>
                </div>
                @empty
                <p class="text-gray-500 text-center py-4">{{ __('Aucun étudiant') }}</p>
                @endforelse
            </div>
        </div>

        <!-- Recent Teachers -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="p-6 border-b border-gray-100">
                <h3 class="text-lg font-semibold text-gray-900">{{ __('Derniers enseignants ajoutés') }}</h3>
            </div>
            <div class="p-6">
                @forelse($recentTeachers as $teacher)
                <div class="flex items-center py-3 {{ !$loop->last ? 'border-b border-gray-100' : '' }}">
                    <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center text-green-600 font-semibold">
                        {{ strtoupper(substr($teacher->first_name, 0, 1) . substr($teacher->last_name, 0, 1)) }}
                    </div>
                    <div class="ml-4 flex-1">
                        <p class="font-medium text-gray-900">{{ $teacher->full_name }}</p>
                        <p class="text-sm text-gray-500">{{ $teacher->specialization ?? __('Non spécifié') }}</p>
                    </div>
                    <span class="px-2 py-1 text-xs rounded-full {{ $teacher->type === 'permanent' ? 'bg-blue-100 text-blue-800' : 'bg-yellow-100 text-yellow-800' }}">
                        {{ __($teacher->type) }}
                    </span>
                </div>
                @empty
                <p class="text-gray-500 text-center py-4">{{ __('Aucun enseignant') }}</p>
                @endforelse
            </div>
        </div>
    </div>
</x-layouts.admin>
