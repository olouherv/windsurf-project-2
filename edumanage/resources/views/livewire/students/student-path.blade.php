<div>
    <h3 class="text-lg font-semibold text-gray-900 mb-4">Parcours académique</h3>

    @if($enrollments->count() > 0)
    <div class="relative">
        <div class="absolute left-4 top-0 bottom-0 w-0.5 bg-gray-200"></div>
        
        <div class="space-y-4">
            @foreach($enrollments as $enrollment)
            <div class="relative flex items-start pl-10">
                <div class="absolute left-2.5 w-3 h-3 rounded-full 
                    @if($enrollment->status === 'validated') bg-green-500
                    @elseif($enrollment->status === 'enrolled') bg-blue-500
                    @elseif($enrollment->status === 'failed') bg-red-500
                    @else bg-gray-400 @endif
                    ring-4 ring-white"></div>
                
                <div class="flex-1 bg-gray-50 rounded-lg p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <span class="font-medium text-gray-900">{{ $enrollment->programYear->full_name }}</span>
                            <span class="ml-2 px-2 py-0.5 text-xs rounded-full
                                @if($enrollment->status === 'validated') bg-green-100 text-green-800
                                @elseif($enrollment->status === 'enrolled') bg-blue-100 text-blue-800
                                @elseif($enrollment->status === 'failed') bg-red-100 text-red-800
                                @else bg-gray-100 text-gray-800 @endif">
                                {{ $enrollment->status_label }}
                            </span>
                        </div>
                        <span class="text-sm text-gray-500">{{ $enrollment->academicYear->name }}</span>
                    </div>
                    @if($enrollment->enrollment_date || $enrollment->validation_date)
                    <div class="mt-2 text-xs text-gray-500">
                        @if($enrollment->enrollment_date)
                        Inscrit le {{ $enrollment->enrollment_date->format('d/m/Y') }}
                        @endif
                        @if($enrollment->validation_date)
                        • Validé le {{ $enrollment->validation_date->format('d/m/Y') }}
                        @endif
                    </div>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @else
    <div class="text-center py-6 text-gray-500">
        <svg class="w-10 h-10 mx-auto text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
        </svg>
        <p class="text-sm">Aucune inscription pédagogique</p>
    </div>
    @endif
</div>
