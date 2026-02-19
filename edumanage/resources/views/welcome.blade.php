<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>EduManage - Gestion universitaire moderne</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased font-sans bg-white">
    <!-- Navigation -->
    <nav class="fixed w-full bg-white/95 backdrop-blur-sm z-50 border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <span class="text-2xl font-bold text-indigo-600">EduManage</span>
                </div>
                <div class="hidden md:flex items-center space-x-8">
                    <a href="#features" class="text-gray-600 hover:text-gray-900">Fonctionnalités</a>
                    <a href="#modules" class="text-gray-600 hover:text-gray-900">Modules</a>
                    <a href="#pricing" class="text-gray-600 hover:text-gray-900">Tarifs</a>
                    <a href="#contact" class="text-gray-600 hover:text-gray-900">Contact</a>
                </div>
                <div class="flex items-center space-x-4">
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/dashboard') }}" class="text-gray-600 hover:text-gray-900">Tableau de bord</a>
                        @else
                            <a href="{{ route('login') }}" class="text-gray-600 hover:text-gray-900">Connexion</a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">Essai gratuit</a>
                            @endif
                        @endauth
                    @endif
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="pt-32 pb-20 bg-gradient-to-br from-indigo-50 via-white to-purple-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <h1 class="text-5xl md:text-6xl font-bold text-gray-900 mb-6">
                    Gérez votre <span class="text-indigo-600">université</span><br>en toute simplicité
                </h1>
                <p class="text-xl text-gray-600 mb-8 max-w-3xl mx-auto">
                    Solution complète de gestion universitaire : étudiants, enseignants, programmes, notes, planification et plus encore. Tout en un seul endroit.
                </p>
                <div class="flex flex-col sm:flex-row justify-center gap-4">
                    <a href="{{ route('register') }}" class="px-8 py-4 bg-indigo-600 text-white text-lg font-semibold rounded-xl hover:bg-indigo-700 shadow-lg shadow-indigo-200">
                        Créer un compte démo
                    </a>
                    <a href="#contact" class="px-8 py-4 bg-white text-indigo-600 text-lg font-semibold rounded-xl border-2 border-indigo-600 hover:bg-indigo-50">
                        Demander un devis
                    </a>
                </div>
                <p class="mt-4 text-sm text-gray-500">14 jours d'essai gratuit • Aucune carte bancaire requise</p>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl font-bold text-gray-900 mb-4">Pourquoi choisir EduManage ?</h2>
                <p class="text-lg text-gray-600">Une solution moderne et intuitive pour les établissements d'enseignement supérieur</p>
            </div>
            <div class="grid md:grid-cols-3 gap-8">
                <div class="text-center p-6">
                    <div class="w-16 h-16 bg-indigo-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Multi-tenant</h3>
                    <p class="text-gray-600">Chaque université dispose de son espace isolé et sécurisé avec ses propres données.</p>
                </div>
                <div class="text-center p-6">
                    <div class="w-16 h-16 bg-green-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Sécurisé</h3>
                    <p class="text-gray-600">Données chiffrées, sauvegardes automatiques et conformité RGPD garantie.</p>
                </div>
                <div class="text-center p-6">
                    <div class="w-16 h-16 bg-purple-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Modulaire</h3>
                    <p class="text-gray-600">Activez uniquement les modules dont vous avez besoin. Payez ce que vous utilisez.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Modules Section -->
    <section id="modules" class="py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl font-bold text-gray-900 mb-4">Nos modules</h2>
                <p class="text-lg text-gray-600">Découvrez toutes les fonctionnalités disponibles</p>
            </div>
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Module Étudiants -->
                <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100 hover:shadow-md transition">
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Gestion des étudiants</h3>
                    <p class="text-gray-600 text-sm">Inscriptions, dossiers, garants, parcours académique et suivi complet des étudiants.</p>
                    <span class="inline-block mt-3 text-xs font-medium text-blue-600 bg-blue-50 px-2 py-1 rounded">Inclus</span>
                </div>

                <!-- Module Enseignants -->
                <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100 hover:shadow-md transition">
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Gestion des enseignants</h3>
                    <p class="text-gray-600 text-sm">Permanents, vacataires, contrats, affectations ECU et documents administratifs.</p>
                    <span class="inline-block mt-3 text-xs font-medium text-green-600 bg-green-50 px-2 py-1 rounded">Inclus</span>
                </div>

                <!-- Module Structure académique -->
                <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100 hover:shadow-md transition">
                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Structure académique</h3>
                    <p class="text-gray-600 text-sm">Programmes, années, semestres, UE, ECU et maquettes pédagogiques complètes.</p>
                    <span class="inline-block mt-3 text-xs font-medium text-purple-600 bg-purple-50 px-2 py-1 rounded">Inclus</span>
                </div>

                <!-- Module Contrats -->
                <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100 hover:shadow-md transition">
                    <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Contrats & Paiements</h3>
                    <p class="text-gray-600 text-sm">Contrats étudiants, échéanciers, suivi des paiements et contrats vacataires.</p>
                    <span class="inline-block mt-3 text-xs font-medium text-yellow-600 bg-yellow-50 px-2 py-1 rounded">Inclus</span>
                </div>

                <!-- Module Notes -->
                <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100 hover:shadow-md transition">
                    <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Notes & Évaluations</h3>
                    <p class="text-gray-600 text-sm">Saisie des notes, calcul automatique des moyennes, PV et bulletins.</p>
                    <span class="inline-block mt-3 text-xs font-medium text-red-600 bg-red-50 px-2 py-1 rounded">Optionnel</span>
                </div>

                <!-- Module Planification -->
                <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100 hover:shadow-md transition">
                    <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Planification</h3>
                    <p class="text-gray-600 text-sm">Emplois du temps, salles, équipements et gestion des créneaux.</p>
                    <span class="inline-block mt-3 text-xs font-medium text-indigo-600 bg-indigo-50 px-2 py-1 rounded">Optionnel</span>
                </div>
            </div>
        </div>
    </section>

    <!-- Pricing Section -->
    <section id="pricing" class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl font-bold text-gray-900 mb-4">Tarification simple et transparente</h2>
                <p class="text-lg text-gray-600">Payez uniquement ce dont vous avez besoin</p>
            </div>
            <div class="grid md:grid-cols-3 gap-8 max-w-5xl mx-auto">
                @php
                    $moduleLabels = \App\Models\ModuleSetting::MODULES;
                @endphp
                @forelse($plans as $plan)
                    @php
                        $isFeatured = in_array(strtolower($plan->key), ['pro', 'premium'], true);
                        $bg = $isFeatured ? 'bg-indigo-600 text-white relative' : 'bg-white border border-gray-200';
                        $textMuted = $isFeatured ? 'text-indigo-200' : 'text-gray-600';
                        $cta = $isFeatured ? 'bg-white text-indigo-600 hover:bg-indigo-50' : 'border-2 border-indigo-600 text-indigo-600 hover:bg-indigo-50';
                        $price = (float) ($plan->price_monthly ?? 0);
                    @endphp

                    <div class="rounded-2xl p-8 {{ $bg }}">
                        @if($isFeatured)
                            <span class="absolute -top-3 left-1/2 -translate-x-1/2 bg-yellow-400 text-yellow-900 text-xs font-bold px-3 py-1 rounded-full">POPULAIRE</span>
                        @endif

                        <h3 class="text-xl font-semibold {{ $isFeatured ? '' : 'text-gray-900' }} mb-2">{{ $plan->name }}</h3>
                        <p class="{{ $textMuted }} text-sm mb-6">{{ $plan->subtitle ?: $plan->description ?: $plan->key }}</p>

                        <div class="mb-6">
                            <span class="text-4xl font-bold {{ $isFeatured ? '' : 'text-gray-900' }}">
                                {{ $price > 0 ? number_format($price, 0, ',', ' ') . $plan->currency : 'Sur devis' }}
                            </span>
                            @if($price > 0)
                                <span class="{{ $textMuted }}">/mois</span>
                            @endif
                        </div>

                        <ul class="space-y-3 mb-8">
                            @foreach(($plan->features ?? []) as $f)
                                <li class="flex items-center text-sm {{ $isFeatured ? '' : 'text-gray-600' }}">
                                    <svg class="w-5 h-5 {{ $isFeatured ? 'text-yellow-400' : 'text-green-500' }} mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    {{ $f }}
                                </li>
                            @endforeach

                            @if(is_array($plan->included_modules) && count($plan->included_modules) > 0)
                                <li class="pt-2 text-xs {{ $textMuted }}">Modules inclus :</li>
                                @foreach($plan->included_modules as $mk)
                                    <li class="flex items-center text-sm {{ $isFeatured ? '' : 'text-gray-600' }}">
                                        <svg class="w-5 h-5 {{ $isFeatured ? 'text-yellow-400' : 'text-green-500' }} mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        {{ $moduleLabels[$mk]['name'] ?? $mk }}
                                    </li>
                                @endforeach
                            @endif
                        </ul>

                        <a href="{{ route('register') }}" class="block w-full text-center px-6 py-3 rounded-lg font-medium {{ $cta }}">
                            Commencer
                        </a>
                    </div>
                @empty
                    <div class="md:col-span-3 text-center text-gray-500">
                        Aucun plan actif pour le moment.
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="py-20 bg-gray-50">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-900 mb-4">Demandez un devis personnalisé</h2>
                <p class="text-lg text-gray-600">Notre équipe vous répondra sous 24h</p>
            </div>
            <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100">
                <form action="#" method="POST" class="space-y-6">
                    @csrf
                    <div class="grid md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nom complet</label>
                            <input type="text" name="name" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email professionnel</label>
                            <input type="email" name="email" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                    </div>
                    <div class="grid md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Établissement</label>
                            <input type="text" name="institution" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nombre d'étudiants</label>
                            <select name="students_count" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="<100">Moins de 100</option>
                                <option value="100-500">100 - 500</option>
                                <option value="500-1000">500 - 1000</option>
                                <option value="1000-5000">1000 - 5000</option>
                                <option value=">5000">Plus de 5000</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Message</label>
                        <textarea name="message" rows="4" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Décrivez vos besoins..."></textarea>
                    </div>
                    <button type="submit" class="w-full px-6 py-4 bg-indigo-600 text-white font-semibold rounded-lg hover:bg-indigo-700">
                        Envoyer la demande
                    </button>
                </form>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-4 gap-8">
                <div>
                    <span class="text-2xl font-bold">EduManage</span>
                    <p class="mt-4 text-gray-400 text-sm">Solution complète de gestion universitaire pour les établissements d'enseignement supérieur.</p>
                </div>
                <div>
                    <h4 class="font-semibold mb-4">Produit</h4>
                    <ul class="space-y-2 text-sm text-gray-400">
                        <li><a href="#features" class="hover:text-white">Fonctionnalités</a></li>
                        <li><a href="#modules" class="hover:text-white">Modules</a></li>
                        <li><a href="#pricing" class="hover:text-white">Tarifs</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-semibold mb-4">Support</h4>
                    <ul class="space-y-2 text-sm text-gray-400">
                        <li><a href="#" class="hover:text-white">Documentation</a></li>
                        <li><a href="#contact" class="hover:text-white">Contact</a></li>
                        <li><a href="#" class="hover:text-white">FAQ</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-semibold mb-4">Légal</h4>
                    <ul class="space-y-2 text-sm text-gray-400">
                        <li><a href="#" class="hover:text-white">Mentions légales</a></li>
                        <li><a href="#" class="hover:text-white">Politique de confidentialité</a></li>
                        <li><a href="#" class="hover:text-white">CGU</a></li>
                    </ul>
                </div>
            </div>
            <div class="mt-12 pt-8 border-t border-gray-800 text-center text-sm text-gray-400">
                © {{ date('Y') }} EduManage. Tous droits réservés.
            </div>
        </div>
    </footer>
</body>
</html>
