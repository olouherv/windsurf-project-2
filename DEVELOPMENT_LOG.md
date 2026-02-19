# Journal de Développement - EduManage SaaS

> Plateforme SaaS de gestion administrative et académique des universités

## 📊 Statut Global

| Phase | Description | Statut |
|-------|-------------|--------|
| 1 | Infrastructure Laravel | ✅ Complété |
| 2 | Auth & Rôles | ✅ Complété |
| 3 | Modules Core | ✅ Complété |
| 4 | Interface Blade/Livewire | ✅ Complété |
| 5 | Structure Académique (UE/ECU) | ✅ Complété |
| 6 | Garants & Contrats Étudiants | ✅ Complété |
| 7 | Fonctionnalités avancées | ⏳ À faire |

## 🔧 Stack Technique

- **Backend**: Laravel 12 (PHP 8.5+)
- **Frontend**: Blade + Livewire 3 + Alpine.js
- **CSS**: TailwindCSS
- **Base de données**: SQLite (dev) / MySQL 8 (prod)
- **Auth**: Laravel Breeze + Spatie Permission
- **PDF**: DomPDF
- **i18n**: Laravel Localization (FR/EN)

---

## 📝 Changelog

### 2026-02-19 - Multi-tenancy + Modules + SuperAdmin (V1)

#### ✅ Complété
- [x] Isolation multi-tenant renforcée sur les contrats (étudiants + vacataires)
  - Filtrage par `university_id` sur les recherches (étudiants, enseignants, ECU, années académiques)
  - Correction des fuites de données cross-tenant sur les listes/formulaires
- [x] Contrats vacataires : ajout `university_id` + backfill
  - Migration `add_university_id_to_vacataire_contracts_table`
  - Modèle `VacataireContract` rendu tenant-aware via `BelongsToUniversity`
- [x] Désactivation de modules réellement appliquée côté routes
  - Middleware `module:<key>` sur routes `contracts/*` et `vacataire-contracts/*`
  - Alias middleware `module`, `tenant`, `locale` enregistrés
- [x] Corrections erreurs null (Blade/Livewire)
  - `Attempt to read property "name" on null` (contrats étudiants)
  - `Attempt to read property "full_name" on null` (contrats vacataires)
- [x] Affichage période d'essai (démo)
  - Affichage robuste du temps restant (`trial_ends_at`) dans le layout admin

#### ✅ SuperAdmin — Universités (V1)
- [x] Liste des universités enrichie
  - Affichage admin de l'université (nom + email)
  - Affichage statut démo (restant/expirée)
- [x] SuperAdmin : activation/désactivation des modules par université (toggle)

#### ✅ Plans/Tarifs + Offre par université (V1)
- [x] Modèle + table `pricing_plans`
- [x] CRUD SuperAdmin Plans & Tarifs
- [x] Assignation d'une offre à une université (pricing_plan_id + plan_key + plan_started_at)

#### ✅ Exports (V1)
- [x] Contrats étudiants
  - [x] Export PDF (DomPDF)
  - [x] Export CSV (compatible Excel)

#### ✅ Démo / Trial
- [x] Migration pour mettre toutes les universités existantes en démo (14 jours)

#### ✅ Documents Officiels (V1)
- [x] Génération PDF
  - [x] Attestation d'inscription
  - [x] Certificat de scolarité
  - [x] Accès depuis la fiche étudiant

#### ✅ Stages & Mémoires (V1)
- [x] Migrations + modèles tenant-aware
- [x] CRUD Stages (internships)
- [x] CRUD Mémoires (theses)

#### ✅ Notifications (V1)
- [x] Notifications in-app (table `notifications`)
- [x] Page liste + action "tout marquer comme lu"

#### ✅ Plans/Tarifs dynamiques (V2)
- [x] Page d'accueil: affichage des plans actifs depuis la table `pricing_plans`
- [x] Ajout de `included_modules` (JSON) pour lier un plan aux modules inclus
- [x] UI SuperAdmin: sélection des modules inclus dans un plan

#### ✅ Abonnement & Paiements (V1)
- [x] Université: page Abonnement + demande de changement de plan (création paiement `pending`)
- [x] SuperAdmin: page Paiements + validation (marquer payé) appliquant le plan à l'université

#### ✅ Corrections
- [x] Menu latéral: conditions modules corrigées (contracts/vacataire_contracts/enrollments)
- [x] Dashboard université: compteur Inscriptions basé sur `StudentEnrollment` + année académique courante

#### ✅ Améliorations UI/Modules (V1)
- [x] Stages & Mémoires : autocomplétion Étudiant / Encadrant via composants Livewire réutilisables
  - Remplacement des `select` lourds par recherche dynamique (nom/matricule / nom/matricule enseignant)
  - Sélection automatique sur match exact matricule (`student_id`)
- [x] Plans & Tarifs (SuperAdmin) : gestion des `features`
  - Ajout du champ Features dans create/edit (1 feature par ligne)
  - Parsing + persistence en JSON (`features`)
- [x] Inscriptions pédagogiques : inscription groupée depuis la page Étudiants
  - Sélection multiple (checkbox) + modal (année académique + année de formation)
  - Ignorer / notifier les étudiants déjà inscrits pour l'année choisie
  - Suppression de l’onglet et de la route dédiée `enrollments`

#### ⏳ À faire (SuperAdmin)
- [ ] Offres / Abonnements par université (plan choisi)
- [ ] Tarification : interface pour modifier les tarifs (plans, options)
- [ ] Facturation (historique, statuts, échéances)

### 2026-02-18 - Phase 6: Garants & Contrats Étudiants

#### ✅ Complété
- [x] Garant étudiant (optionnel)
  - Champs: nom, prénom, relation, téléphone, email, adresse, profession
  - Migration `add_guarantor_to_students_table`
  - Intégré au modèle Student
- [x] Contrats étudiants (`student_contracts`)
  - Types: inscription, formation, stage, apprentissage
  - Frais: scolarité, inscription, montant payé
  - Statuts: brouillon, actif, complété, annulé, suspendu
  - Signatures: étudiant, garant, admin
  - Modèle StudentContract avec relations
- [x] Paiements (`contract_payments`)
  - Méthodes: espèces, virement, chèque, carte, mobile money
  - Référence, numéro de reçu
  - Mise à jour automatique du statut de paiement
  - Modèle ContractPayment avec événements

### 2026-02-18 - Phase 5: Structure Académique

#### ✅ Complété
- [x] Gestion des Années de Formation (ProgramYears)
  - Création, édition, visualisation
  - Composant Livewire ProgramYearForm
  - Navigation hiérarchique (Programme > Année > Semestre > UE > ECU)
- [x] Gestion des UEs (Unités d'Enseignement)
  - CRUD complet avec formulaire Livewire
  - Crédits ECTS, coefficients, UE optionnelles
- [x] Gestion des ECUs (Éléments Constitutifs)
  - CRUD complet avec formulaire Livewire
  - Répartition horaire (CM/TD/TP)
  - Objectifs pédagogiques
- [x] Routes imbriquées pour navigation fluide

### 2026-02-18 - Phase 4: Interface Blade/Livewire

#### ✅ Complété
- [x] CRUD Étudiants complet (index, show, create, edit)
  - Composant StudentList avec recherche et pagination
  - Composant StudentForm avec validation
  - Gestion des valeurs null (formulaires pré-remplis)
- [x] CRUD Enseignants complet (index, show, create, edit)
  - Composant TeacherList avec filtres
  - Composant TeacherForm avec types (permanent, temporaire, vacataire)
- [x] CRUD Programmes complet (index, show, create, edit)
  - Composant ProgramList
  - Composant ProgramForm avec is_active (boolean)
  - Affichage des années de formation
- [x] Layout admin responsive avec sidebar
  - Menu dynamique selon rôle utilisateur
  - Support mobile avec overlay
  - Menu Super Admin dédié
- [x] Correction route logout
- [x] Messages flash (succès/erreur)

### 2026-02-18 - Phases 1-3: Infrastructure + Core

#### ✅ Complété
- [x] Création projet Laravel 12 avec Breeze + Livewire
- [x] Installation Spatie Permission + DomPDF
- [x] 21 migrations créées (multi-tenant)
- [x] 22 modèles Eloquent avec relations
- [x] Traits: BelongsToUniversity, HasMoodleSync
- [x] Middleware: EnsureTenantAccess, CheckModuleEnabled, SetLocale
- [x] Controllers: Dashboard, Student, Teacher, Program, ProgramYear, Ue, Ecu
- [x] Composants Livewire: StudentList, StudentForm, TeacherList, TeacherForm, ProgramList, ProgramForm, ProgramYearForm, UeForm, EcuForm
- [x] Routes web.php configurées
- [x] Traductions FR/EN
- [x] Seeder avec données de test

#### ⏳ À faire
- [ ] Module planification (calendrier, emplois du temps)
- [ ] Module notes et évaluations
- [ ] Interface gestion Garants (formulaire étudiant)
- [ ] Intégration Moodle API
- [ ] Export PDF (bulletins, relevés, contrats)

---

## 🏗️ Architecture

### Structure Multi-Tenant
```
Chaque université = 1 tenant isolé
├── university_id dans toutes les tables
├── Middleware TenantScope automatique
└── Données complètement séparées
```

### Préparation Moodle (V1)
Champs `moodle_id` ajoutés dans:
- `students.moodle_id`
- `teachers.moodle_id`
- `ecus.moodle_course_id`
- `programs.moodle_category_id`
- `student_groups.moodle_cohort_id`

Tables créées:
- `moodle_configs` - Configuration par université
- `moodle_sync_logs` - Historique des synchronisations

---

## 📋 Modules

### Modules Obligatoires (toujours actifs)
- [x] Étudiants (CRUD complet)
  - [x] Garant optionnel (infos contact, relation, profession)
  - [x] Inscription pédagogique possible lors de la création (formation + année académique)
  - [x] Option : créer un contrat étudiant après la création
  - [x] Fiche étudiant : affichage correct des notes récentes (ECU + type d'évaluation)
- [x] Enseignants (CRUD complet)
  - [x] Champs : sexe, grade, titre, spécialisation
  - [x] Informations fiscales : RIB, IFU (numéro + document)
  - [x] Upload de documents : CV, RIB, IFU (PDF/images)
  - [x] Types : Permanent, Temporaire, Vacataire
- [x] Programmes (CRUD complet)
- [x] Structure Académique
  - [x] Années de formation (ProgramYears)
  - [x] Semestres (composant SemesterManager avec modal)
  - [x] Unités d'Enseignement (UEs)
  - [x] Éléments Constitutifs (ECUs)
- [x] Contrats Étudiants
  - [x] Types de contrats (inscription, formation, stage, apprentissage)
  - [x] Recherche étudiant avec autocomplete (nom/matricule)
  - [x] Dates début/fin remplies auto depuis l'année académique
  - [x] Frais définis au niveau Année de Formation (remplissage auto)
  - [x] Échéancier de paiement (1 à 12 tranches)
  - [x] Gestion des paiements avec historique
  - [x] Interface CRUD complète (liste, création, édition, détails)
  - [x] Filtrage par année académique (présélection année courante)
  - [x] Ajout de paiements ciblés par tranche via modal
- [x] Contrats Vacataires
  - [x] CRUD complet (liste, création, édition, détails)
  - [x] Recherche enseignant vacataire avec autocomplete
  - [x] Dates auto-remplies depuis année académique
  - [x] Calcul automatique du montant (heures × taux horaire)
  - [x] Déclaration d'heures effectuées avec type (CM/TD/TP)
  - [x] Suivi progression heures/montants
  - [x] Lien vers fiche enseignant avec liste des contrats
  - [x] **Contrat lié à un ECU** : sélection ECU avec autocomplete
  - [x] **Type d'enseignement** : CM, TD, TP ou Tous
  - [x] **Heures auto-remplies** selon ECU et type choisi
- [x] Assignation ECU aux Enseignants
  - [x] Composant Livewire interactif sur fiche enseignant
  - [x] Recherche ECU avec autocomplete
  - [x] Type d'enseignement (CM, TD, TP, Tous)
  - [x] Indicateur responsable ECU
  - [x] Filtrage par année académique
- [x] Notes & Évaluations
  - [x] Création d'évaluations (exam, CC, TP, projet, oral)
  - [x] Saisie des notes par évaluation
  - [x] Calcul automatique des moyennes ECU
  - [x] Gestion absences (absent, excusé)
  - [x] Publication des notes
  - [x] Statistiques (moyenne, taux de réussite)
- [x] Inscriptions Pédagogiques & Parcours Étudiant
  - [x] Inscription des étudiants dans une formation (ProgramYear) par année académique
  - [x] Liste des étudiants inscrits sur la page de la formation
  - [x] Gestion des statuts (inscrit, validé, ajourné, abandonné, transféré)
  - [x] Parcours académique visible sur la fiche étudiant
  - [x] Liaison automatique : étudiants inscrits → notes ECU

### Modules Optionnels (activables par admin)
- [x] Planification (emplois du temps, salles)
  - [x] Gestion des séances (CM/TD/TP) par année académique
  - [x] Affectation ECU, enseignant, salle, groupe (optionnels)
  - [x] Recherche + liste triée (jour / horaire)
  - [x] Création en une fois de plusieurs séances (multi-créneaux : jours + heures)
  - [x] Planification datée (activités)
    - [x] Catégorie : Cours (récurrent) vs Activité (réunion/évènement daté)
    - [x] Contrôle d'indisponibilité des salles sur un créneau (date + heure) y compris avec cours récurrents
- [x] Salles (CRUD + disponibilités)
  - [x] CRUD (liste, création, édition, suppression)
  - [x] Page de détails : séances planifiées + vérification de disponibilité (date + heure)
- [x] Équipements
  - [x] CRUD équipements (multi-tenant)
  - [x] Affectation multi-équipements sur une planification
- [ ] Absences & Présences
  - [ ] Feuilles de présence par séance/ECU
  - [ ] Absences (absent/excusé) + retards
  - [ ] Exports (PDF/Excel)
- [ ] Inscriptions Pédagogiques
  - [ ] Gestion des groupes / parcours
  - [ ] Workflow de validation
- [ ] Stages & Mémoires
  - [ ] Suivi stage (entreprise, tuteur, convention)
  - [ ] Mémoire (sujet, encadrant, soutenance)
- [ ] Documents Officiels
  - [ ] Génération PDF : attestations, certificats, contrats, bulletins
- [ ] Notifications
  - [ ] Notifications in-app + email (événements: paiement, notes publiées, absences)
- [ ] Intégration Moodle
  - [ ] Synchronisation (étudiants/enseignants/cours/cohortes)

### Exports & Documents (général)
- [ ] Export Excel/CSV (listes, paiements, absences, notes)
- [ ] Génération PDF (contrats, reçus, bulletins, attestations)

---

## 🔐 Rôles

| Rôle | Accès |
|------|-------|
| Super Admin | Gestion toutes universités |
| Admin Université | Gestion complète de son université |
| Secrétariat | Inscriptions, planning, documents |
| Enseignant | Ses cours, notes, disponibilités |
| Étudiant | Son profil, notes, emploi du temps |

---

## 📝 Notes Techniques

### Décisions d'architecture
1. Multi-tenant par colonne `university_id` (pas par base de données séparée)
2. Utilisation de Global Scopes pour filtrer automatiquement par tenant
3. Livewire pour les interfaces interactives (CRUD sans rechargement)
4. Spatie Permission pour la gestion fine des permissions

### Points d'attention
- Toujours vérifier le `university_id` dans les requêtes
- Les champs `moodle_*` sont nullable (sync optionnelle)
- Utiliser les traits pour le code réutilisable

---

## 🐛 Issues Connues

*Aucune issue pour le moment*

---

## 📈 Prochaines Étapes

1. ~~Créer le projet Laravel~~ ✅
2. ~~Installer les dépendances (Breeze, Livewire, Spatie)~~ ✅
3. ~~Configurer les migrations~~ ✅
4. ~~Créer les models de base~~ ✅
5. ~~Implémenter l'authentification multi-rôles~~ ✅
6. ~~CRUD Étudiants, Enseignants, Programmes~~ ✅
7. ~~Gestion Structure Académique (Années, UE, ECU)~~ ✅
8. ~~Garants étudiants (optionnel)~~ ✅
9. ~~Contrats étudiants et paiements~~ ✅
10. ~~Implémenter le CRUD Semestres~~ ✅
11. ~~Interface CRUD Contrats étudiants~~ ✅
12. ~~Module Notes & Évaluations~~ ✅
13. ~~Module Planification (calendrier)~~ ✅
14. Intégration Moodle API
