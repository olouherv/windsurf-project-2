<x-layouts.admin>
    <x-slot name="header">Détails de l'étudiant</x-slot>

    <div class="w-full">
        <!-- En-tête avec photo et infos principales -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex items-start justify-between">
                <div class="flex items-center space-x-6">
                    <div class="w-24 h-24 bg-indigo-100 rounded-full flex items-center justify-center">
                        @if($student->photo)
                            <img src="{{ Storage::url($student->photo) }}" alt="{{ $student->full_name }}" class="w-24 h-24 rounded-full object-cover">
                        @else
                            <span class="text-3xl font-bold text-indigo-600">{{ substr($student->first_name, 0, 1) }}{{ substr($student->last_name, 0, 1) }}</span>
                        @endif
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">{{ $student->first_name }} {{ $student->last_name }}</h1>
                        <p class="text-gray-500">{{ $student->student_id }}</p>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium mt-2
                            {{ $student->status === 'active' ? 'bg-green-100 text-green-800' : '' }}
                            {{ $student->status === 'graduated' ? 'bg-blue-100 text-blue-800' : '' }}
                            {{ $student->status === 'suspended' ? 'bg-yellow-100 text-yellow-800' : '' }}
                            {{ $student->status === 'expelled' ? 'bg-red-100 text-red-800' : '' }}">
                            {{ ucfirst($student->status) }}
                        </span>
                    </div>
                </div>
                <div class="flex space-x-2">
                    <a href="{{ route('students.edit', $student) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Modifier
                    </a>
                    <a href="{{ route('students.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                        Retour
                    </a>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Informations personnelles -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Informations personnelles</h2>
                <dl class="space-y-3">
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Email</dt>
                        <dd class="text-gray-900">{{ $student->email }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Téléphone</dt>
                        <dd class="text-gray-900">{{ $student->phone ?? '-' }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Date de naissance</dt>
                        <dd class="text-gray-900">{{ $student->birth_date ? $student->birth_date->format('d/m/Y') : '-' }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Lieu de naissance</dt>
                        <dd class="text-gray-900">{{ $student->birth_place ?? '-' }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Genre</dt>
                        <dd class="text-gray-900">{{ $student->gender === 'male' ? 'Masculin' : 'Féminin' }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Nationalité</dt>
                        <dd class="text-gray-900">{{ $student->nationality ?? '-' }}</dd>
                    </div>
                </dl>
            </div>

            <!-- Informations académiques -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Informations académiques</h2>
                <dl class="space-y-3">
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Date d'inscription</dt>
                        <dd class="text-gray-900">{{ $student->enrollment_date->format('d/m/Y') }}</dd>
                    </div>
                    @if($student->currentEnrollment)
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Programme actuel</dt>
                        <dd class="text-gray-900">{{ $student->currentEnrollment->program->name ?? '-' }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Niveau</dt>
                        <dd class="text-gray-900">{{ $student->currentEnrollment->level ?? '-' }}</dd>
                    </div>
                    @endif
                    @if($student->moodle_id)
                    <div class="flex justify-between">
                        <dt class="text-gray-500">ID Moodle</dt>
                        <dd class="text-gray-900">{{ $student->moodle_id }}</dd>
                    </div>
                    @endif
                </dl>
            </div>
        </div>

        <!-- Parcours académique -->
        <div class="bg-white rounded-lg shadow-sm p-6 mt-6">
            @livewire('students.student-path', ['student' => $student])
        </div>

        <!-- Notes récentes -->
        @if($student->grades->count() > 0)
        <div class="bg-white rounded-lg shadow-sm p-6 mt-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Notes récentes</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">ECU</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Note</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($student->grades->take(10) as $grade)
                        <tr>
                            <td class="px-4 py-3 text-sm text-gray-900">{{ $grade->ecu->name ?? '-' }}</td>
                            <td class="px-4 py-3 text-sm text-gray-500">{{ $grade->type }}</td>
                            <td class="px-4 py-3 text-sm font-medium {{ $grade->score >= 10 ? 'text-green-600' : 'text-red-600' }}">{{ number_format($grade->score, 2) }}/20</td>
                            <td class="px-4 py-3 text-sm text-gray-500">{{ $grade->created_at->format('d/m/Y') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    </div>
</x-layouts.admin>
