<x-layouts.admin>
    <x-slot name="header">Modifier le stage</x-slot>

    <div class="w-full">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="p-6 border-b border-gray-100">
                <h3 class="text-lg font-semibold text-gray-900">Modifier le stage</h3>
                <p class="text-sm text-gray-500">{{ $internship->student?->full_name ?? '-' }} • {{ $internship->company_name }}</p>
            </div>

            <form method="POST" action="{{ route('internships.update', $internship) }}" class="p-6 space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-sm font-medium text-gray-700">Étudiant</label>
                    <livewire:shared.student-search-select input-name="student_id" :initial-id="old('student_id', $internship->student_id)" />
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Année académique</label>
                        <select name="academic_year_id" class="mt-1 w-full border-gray-300 rounded-md">
                            <option value="">--</option>
                            @foreach($academicYears as $ay)
                                <option value="{{ $ay->id }}" {{ old('academic_year_id', $internship->academic_year_id) == $ay->id ? 'selected' : '' }}>{{ $ay->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Encadrant</label>
                        <livewire:shared.teacher-search-select input-name="supervisor_teacher_id" :initial-id="old('supervisor_teacher_id', $internship->supervisor_teacher_id)" />
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Entreprise</label>
                    <input type="text" name="company_name" value="{{ old('company_name', $internship->company_name) }}" class="mt-1 w-full border-gray-300 rounded-md" required />
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Sujet</label>
                    <input type="text" name="topic" value="{{ old('topic', $internship->topic) }}" class="mt-1 w-full border-gray-300 rounded-md" />
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Début</label>
                        <input type="date" name="start_date" value="{{ old('start_date', $internship->start_date?->format('Y-m-d')) }}" class="mt-1 w-full border-gray-300 rounded-md" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Fin</label>
                        <input type="date" name="end_date" value="{{ old('end_date', $internship->end_date?->format('Y-m-d')) }}" class="mt-1 w-full border-gray-300 rounded-md" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Statut</label>
                        <select name="status" class="mt-1 w-full border-gray-300 rounded-md" required>
                            @foreach(['draft' => 'Brouillon', 'active' => 'Actif', 'completed' => 'Terminé', 'cancelled' => 'Annulé'] as $k => $v)
                                <option value="{{ $k }}" {{ old('status', $internship->status) === $k ? 'selected' : '' }}>{{ $v }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Notes</label>
                    <textarea name="notes" class="mt-1 w-full border-gray-300 rounded-md" rows="4">{{ old('notes', $internship->notes) }}</textarea>
                </div>

                <div class="flex justify-end space-x-2 pt-4">
                    <a href="{{ route('internships.show', $internship) }}" class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50">Annuler</a>
                    <button type="submit" class="px-4 py-2 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.admin>
