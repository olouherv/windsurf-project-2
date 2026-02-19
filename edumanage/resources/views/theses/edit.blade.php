<x-layouts.admin>
    <x-slot name="header">Modifier le mémoire</x-slot>

    <div class="w-full">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="p-6 border-b border-gray-100">
                <h3 class="text-lg font-semibold text-gray-900">Modifier le mémoire</h3>
                <p class="text-sm text-gray-500">{{ $thesis->student?->full_name ?? '-' }} • {{ $thesis->title }}</p>
            </div>

            <form method="POST" action="{{ route('theses.update', $thesis) }}" class="p-6 space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-sm font-medium text-gray-700">Étudiant</label>
                    <select name="student_id" class="mt-1 w-full border-gray-300 rounded-md" required>
                        @foreach($students as $s)
                            <option value="{{ $s->id }}" {{ old('student_id', $thesis->student_id) == $s->id ? 'selected' : '' }}>{{ $s->full_name }} ({{ $s->student_id }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Année académique</label>
                        <select name="academic_year_id" class="mt-1 w-full border-gray-300 rounded-md">
                            <option value="">--</option>
                            @foreach($academicYears as $ay)
                                <option value="{{ $ay->id }}" {{ old('academic_year_id', $thesis->academic_year_id) == $ay->id ? 'selected' : '' }}>{{ $ay->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Encadrant</label>
                        <select name="supervisor_teacher_id" class="mt-1 w-full border-gray-300 rounded-md">
                            <option value="">--</option>
                            @foreach($teachers as $t)
                                <option value="{{ $t->id }}" {{ old('supervisor_teacher_id', $thesis->supervisor_teacher_id) == $t->id ? 'selected' : '' }}>{{ $t->full_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Titre</label>
                    <input type="text" name="title" value="{{ old('title', $thesis->title) }}" class="mt-1 w-full border-gray-300 rounded-md" required />
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Résumé</label>
                    <textarea name="abstract" class="mt-1 w-full border-gray-300 rounded-md" rows="4">{{ old('abstract', $thesis->abstract) }}</textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Soumission</label>
                        <input type="date" name="submission_date" value="{{ old('submission_date', $thesis->submission_date?->format('Y-m-d')) }}" class="mt-1 w-full border-gray-300 rounded-md" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Soutenance</label>
                        <input type="date" name="defense_date" value="{{ old('defense_date', $thesis->defense_date?->format('Y-m-d')) }}" class="mt-1 w-full border-gray-300 rounded-md" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Statut</label>
                        <select name="status" class="mt-1 w-full border-gray-300 rounded-md" required>
                            @foreach(['draft' => 'Brouillon', 'in_progress' => 'En cours', 'submitted' => 'Soumis', 'defended' => 'Soutenu', 'cancelled' => 'Annulé'] as $k => $v)
                                <option value="{{ $k }}" {{ old('status', $thesis->status) === $k ? 'selected' : '' }}>{{ $v }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Note (sur 20)</label>
                    <input type="number" step="0.01" min="0" max="20" name="grade" value="{{ old('grade', $thesis->grade) }}" class="mt-1 w-full border-gray-300 rounded-md" />
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Notes</label>
                    <textarea name="notes" class="mt-1 w-full border-gray-300 rounded-md" rows="4">{{ old('notes', $thesis->notes) }}</textarea>
                </div>

                <div class="flex justify-end space-x-2 pt-4">
                    <a href="{{ route('theses.show', $thesis) }}" class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50">Annuler</a>
                    <button type="submit" class="px-4 py-2 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.admin>
