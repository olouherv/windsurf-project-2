<x-layouts.admin :title="__('Super Admin')">
    <x-slot name="header">{{ __('Tableau de bord Super Admin') }}</x-slot>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-indigo-100 text-indigo-600">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">{{ __('Universités') }}</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['universities']) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">{{ __('Total Étudiants') }}</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_students']) }}</p>
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
                    <p class="text-sm font-medium text-gray-500">{{ __('Total Enseignants') }}</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_teachers']) }}</p>
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
                    <p class="text-sm font-medium text-gray-500">{{ __('Total Programmes') }}</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_programs']) }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Universities List -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        <div class="p-6 border-b border-gray-100">
            <h3 class="text-lg font-semibold text-gray-900">{{ __('Universités enregistrées') }}</h3>
        </div>
        <div class="p-6">
            @php $universities = \App\Models\University::with('students', 'teachers')->get(); @endphp
            @forelse($universities as $university)
            <div class="flex items-center justify-between py-4 {{ !$loop->last ? 'border-b border-gray-100' : '' }}">
                <div class="flex items-center">
                    <div class="w-12 h-12 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 font-bold">
                        {{ strtoupper(substr($university->name, 0, 2)) }}
                    </div>
                    <div class="ml-4">
                        <p class="font-medium text-gray-900">{{ $university->name }}</p>
                        <p class="text-sm text-gray-500">{{ $university->code }} - {{ $university->email }}</p>
                    </div>
                </div>
                <div class="flex items-center space-x-6 text-sm text-gray-500">
                    <span>{{ $university->students->count() }} {{ __('étudiants') }}</span>
                    <span>{{ $university->teachers->count() }} {{ __('enseignants') }}</span>
                    <span class="px-2 py-1 rounded-full text-xs {{ $university->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ $university->is_active ? __('Actif') : __('Inactif') }}
                    </span>
                </div>
            </div>
            @empty
            <p class="text-center text-gray-500 py-4">{{ __('Aucune université enregistrée.') }}</p>
            @endforelse
        </div>
    </div>
</x-layouts.admin>
