<x-layouts.admin :title="__('Moodle')">
    <x-slot name="header">{{ __('Intégration Moodle') }}</x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Menu latéral -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
                <nav class="space-y-1">
                    <a href="{{ route('settings.index') }}" class="flex items-center px-4 py-2 rounded-lg text-gray-700 hover:bg-gray-50">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                        {{ __('Général') }}
                    </a>
                    <a href="{{ route('settings.modules') }}" class="flex items-center px-4 py-2 rounded-lg text-gray-700 hover:bg-gray-50">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                        </svg>
                        {{ __('Modules') }}
                    </a>
                    <a href="{{ route('settings.moodle') }}" class="flex items-center px-4 py-2 rounded-lg bg-indigo-50 text-indigo-700">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        {{ __('Moodle') }}
                    </a>
                </nav>
            </div>
        </div>

        <!-- Contenu -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ __('Configuration Moodle') }}</h3>
                <p class="text-sm text-gray-500 mb-6">Connectez votre instance Moodle pour synchroniser les cours et les utilisateurs.</p>
                
                @php
                    $university = auth()->user()->university;
                    $config = $university?->moodleConfig;
                @endphp
                
                <form action="{{ route('settings.moodle.update') }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="space-y-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('URL Moodle') }}</label>
                            <input type="url" name="moodle_url" value="{{ $config->moodle_url ?? '' }}" placeholder="https://moodle.example.com" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                            <p class="mt-1 text-xs text-gray-500">L'URL de votre instance Moodle</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Token API') }}</label>
                            <input type="password" name="moodle_token" value="{{ $config->moodle_token ?? '' }}" placeholder="••••••••••••••••" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                            <p class="mt-1 text-xs text-gray-500">Le token d'authentification pour l'API Moodle</p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <label class="inline-flex items-center">
                                <input type="checkbox" name="is_active" value="1" class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500" {{ ($config->is_active ?? false) ? 'checked' : '' }}>
                                <span class="ml-2 text-sm text-gray-700">Activer la synchronisation</span>
                            </label>
                            <div class="text-sm text-gray-500">Choisissez les données à synchroniser :</div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <label class="inline-flex items-center">
                                <input type="checkbox" name="sync_students" value="1" class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500" {{ ($config->sync_students ?? false) ? 'checked' : '' }}>
                                <span class="ml-2 text-sm text-gray-700">Étudiants</span>
                            </label>
                            <label class="inline-flex items-center">
                                <input type="checkbox" name="sync_teachers" value="1" class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500" {{ ($config->sync_teachers ?? false) ? 'checked' : '' }}>
                                <span class="ml-2 text-sm text-gray-700">Enseignants</span>
                            </label>
                            <label class="inline-flex items-center">
                                <input type="checkbox" name="sync_courses" value="1" class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500" {{ ($config->sync_courses ?? false) ? 'checked' : '' }}>
                                <span class="ml-2 text-sm text-gray-700">Cours</span>
                            </label>
                            <label class="inline-flex items-center">
                                <input type="checkbox" name="sync_cohorts" value="1" class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500" {{ ($config->sync_cohorts ?? false) ? 'checked' : '' }}>
                                <span class="ml-2 text-sm text-gray-700">Cohortes / Groupes</span>
                            </label>
                        </div>
                        
                        <div class="p-4 bg-gray-50 rounded-lg">
                            <h4 class="text-sm font-medium text-gray-900 mb-2">Statut de la connexion</h4>
                            <div class="flex items-center">
                                <span class="w-3 h-3 rounded-full {{ ($config && $config->is_active) ? 'bg-green-500' : 'bg-gray-400' }} mr-2"></span>
                                <span class="text-sm text-gray-600">{{ ($config && $config->is_active) ? 'Actif' : 'Inactif / Non configuré' }}</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-6 flex justify-end space-x-3">
                        <button type="button" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                            Tester la connexion
                        </button>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                            {{ __('Enregistrer') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.admin>
