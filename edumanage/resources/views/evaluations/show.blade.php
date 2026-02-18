<x-layouts.admin>
    <x-slot name="title">{{ $evaluation->name }}</x-slot>

    <div class="mb-6 flex justify-between items-center">
        <a href="{{ route('evaluations.index') }}" class="inline-flex items-center text-indigo-600 hover:text-indigo-800">
            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Retour à la liste
        </a>
        <div class="flex space-x-2">
            <a href="{{ route('evaluations.grades', $evaluation) }}" class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                Saisir les notes
            </a>
            <a href="{{ route('evaluations.edit', $evaluation) }}" class="inline-flex items-center px-4 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Modifier
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded-lg">
        {{ session('success') }}
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-start justify-between mb-4">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">{{ $evaluation->name }}</h1>
                        <p class="text-gray-500">{{ $evaluation->ecu->code }} - {{ $evaluation->ecu->name }}</p>
                    </div>
                    <div class="flex items-center space-x-2">
                        <span class="px-3 py-1 text-sm rounded-full
                            @switch($evaluation->type)
                                @case('exam') bg-red-100 text-red-800 @break
                                @case('cc') bg-blue-100 text-blue-800 @break
                                @case('tp') bg-green-100 text-green-800 @break
                                @case('project') bg-purple-100 text-purple-800 @break
                                @case('oral') bg-yellow-100 text-yellow-800 @break
                            @endswitch
                        ">
                            @switch($evaluation->type)
                                @case('exam') Examen @break
                                @case('cc') Contrôle continu @break
                                @case('tp') TP @break
                                @case('project') Projet @break
                                @case('oral') Oral @break
                            @endswitch
                        </span>
                        @if($evaluation->is_published)
                        <span class="px-3 py-1 text-sm rounded-full bg-green-100 text-green-800">Publiée</span>
                        @else
                        <span class="px-3 py-1 text-sm rounded-full bg-gray-100 text-gray-800">Non publiée</span>
                        @endif
                    </div>
                </div>

                <dl class="grid grid-cols-2 gap-4 mt-6 pt-4 border-t">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Année académique</dt>
                        <dd class="text-gray-900">{{ $evaluation->academicYear->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Session</dt>
                        <dd class="text-gray-900">{{ $evaluation->session === 'normal' ? 'Normale' : 'Rattrapage' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Date</dt>
                        <dd class="text-gray-900">{{ $evaluation->date ? $evaluation->date->format('d/m/Y') : '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Coefficient</dt>
                        <dd class="text-gray-900">{{ $evaluation->coefficient }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Note maximale</dt>
                        <dd class="text-gray-900">{{ $evaluation->max_score }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">UE</dt>
                        <dd class="text-gray-900">{{ $evaluation->ecu->ue->name ?? '-' }}</dd>
                    </div>
                </dl>

                @if($evaluation->description)
                <div class="mt-6 pt-4 border-t">
                    <h3 class="text-sm font-medium text-gray-500 mb-2">Description</h3>
                    <p class="text-gray-700">{{ $evaluation->description }}</p>
                </div>
                @endif
            </div>

            @if($evaluation->grades->count() > 0)
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Notes saisies</h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Étudiant</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Matricule</th>
                                <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase">Note</th>
                                <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase">Statut</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($evaluation->grades->sortBy('student.last_name') as $grade)
                            <tr>
                                <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $grade->student->full_name }}</td>
                                <td class="px-4 py-3 text-sm text-gray-500">{{ $grade->student->student_id }}</td>
                                <td class="px-4 py-3 text-sm text-center">
                                    @if($grade->is_absent)
                                    <span class="text-red-600">ABS</span>
                                    @elseif($grade->score !== null)
                                    <span class="font-medium {{ $grade->score >= ($evaluation->max_score / 2) ? 'text-green-600' : 'text-red-600' }}">
                                        {{ number_format($grade->score, 2) }}/{{ $evaluation->max_score }}
                                    </span>
                                    @else
                                    <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-sm text-center">
                                    @if($grade->is_absent)
                                    <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">Absent</span>
                                    @elseif($grade->score !== null && $grade->score >= ($evaluation->max_score / 2))
                                    <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Validé</span>
                                    @elseif($grade->score !== null)
                                    <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">Non validé</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif
        </div>

        <div class="space-y-6">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Statistiques</h2>
                
                @php
                    $gradedCount = $evaluation->grades->filter(fn($g) => $g->score !== null && !$g->is_absent)->count();
                    $absentCount = $evaluation->grades->filter(fn($g) => $g->is_absent)->count();
                    $avgScore = $evaluation->grades->filter(fn($g) => $g->score !== null && !$g->is_absent)->avg('score');
                    $passedCount = $evaluation->grades->filter(fn($g) => $g->score !== null && !$g->is_absent && $g->score >= ($evaluation->max_score / 2))->count();
                @endphp

                <div class="space-y-4">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Notes saisies</span>
                        <span class="font-medium">{{ $gradedCount }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Absents</span>
                        <span class="font-medium text-red-600">{{ $absentCount }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Moyenne</span>
                        <span class="font-bold text-lg {{ $avgScore && $avgScore >= ($evaluation->max_score / 2) ? 'text-green-600' : 'text-red-600' }}">
                            {{ $avgScore ? number_format($avgScore, 2) : '-' }}/{{ $evaluation->max_score }}
                        </span>
                    </div>
                    @if($gradedCount > 0)
                    <div class="pt-4 border-t">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-gray-600">Taux de réussite</span>
                            <span class="font-medium">{{ round(($passedCount / $gradedCount) * 100, 1) }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-3">
                            <div class="bg-green-600 h-3 rounded-full" style="width: {{ ($passedCount / $gradedCount) * 100 }}%"></div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-layouts.admin>
