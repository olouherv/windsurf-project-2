# Journal de Développement - EduManage

## 2026-03-16 - Module Planification et Délibération

### Calendrier de Planification (style Google Calendar)

**Nouvelles fonctionnalités :**
- Vue calendrier mensuelle interactive avec navigation (mois précédent/suivant, aujourd'hui)
- Affichage des séances sur chaque jour avec code couleur par type (CM/TD/TP)
- Clic sur un jour pour voir les détails : séances, enseignants, salles, groupes
- Ajout de séance avec détection automatique des salles et enseignants disponibles
- **Répétition automatique** des séances :
  - Jusqu'à épuisement de la masse horaire (automatique)
  - Jusqu'à une date précise
  - Nombre fixe de séances
- Sélection des jours de répétition (Lun, Mar, Mer, etc.)
- Affichage du résumé de la masse horaire par type (CM/TD/TP) avec heures restantes
- Filtres par ECU, enseignant, salle
- Actions rapides : marquer effectuée, annuler, modifier, supprimer

**Fichiers créés :**
- `app/Livewire/Schedules/ScheduleCalendar.php` - Composant calendrier
- `resources/views/livewire/schedules/schedule-calendar.blade.php` - Vue calendrier
- `resources/views/schedules/calendar.blade.php` - Page principale

**Route :** `GET /schedules/calendar`

### Planification - Créneaux récurrents et séances

**Nouvelles fonctionnalités :**
- Génération automatique de séances à partir de créneaux récurrents
- Gestion manuelle des séances individuelles (ajout, modification, suppression)
- Suivi de la masse horaire par ECU (planifié, effectué, restant)
- Marquage des séances comme effectuées ou annulées

**Fichiers créés :**
- `app/Models/ScheduleSession.php` - Modèle pour les séances individuelles
- `app/Livewire/Schedules/SessionManager.php` - Composant de gestion des séances
- `resources/views/livewire/schedules/session-manager.blade.php` - Vue de gestion
- `resources/views/schedules/sessions.blade.php` - Page principale
- `database/migrations/2026_03_16_100001_create_schedule_sessions_table.php`

**Fichiers modifiés :**
- `app/Livewire/Schedules/ScheduleManager.php` - Ajout méthode `generateSessions()`
- `routes/web.php` - Nouvelles routes
- `resources/views/components/layouts/admin.blade.php` - Lien menu

### Module Délibération

**Fonctionnalités :**
- Délibération semestrielle et annuelle
- Paramétrage des critères de validation (UE, semestre, année)
- Compensation configurable (moyenne minimale, % UE validées)
- Passage conditionnel avec dette de crédits
- Calcul automatique des mentions
- Workflow : brouillon → calcul → validation → publication
- Classement automatique des étudiants

**Critères paramétrables :**
- Validation UE : moyenne minimale, compensation, seuil de compensation
- Validation semestre : moyenne, % UE à valider, max UE non validées
- Validation année : moyenne, tous semestres requis, max crédits non validés
- Passage conditionnel : autorisation, max crédits en dette
- Mentions : Passable, Assez Bien, Bien, Très Bien

**Fichiers créés :**
- `app/Models/DeliberationSetting.php` - Paramètres de délibération
- `app/Models/Deliberation.php` - Sessions de délibération
- `app/Models/DeliberationResult.php` - Résultats par étudiant
- `app/Models/DeliberationUeResult.php` - Résultats par UE
- `app/Services/DeliberationService.php` - Logique de calcul
- `app/Livewire/Deliberations/DeliberationManager.php` - Gestion
- `app/Livewire/Deliberations/DeliberationSettings.php` - Paramètres
- `resources/views/livewire/deliberations/deliberation-manager.blade.php`
- `resources/views/livewire/deliberations/deliberation-settings.blade.php`
- `resources/views/deliberations/index.blade.php`
- `resources/views/settings/deliberation.blade.php`
- `database/migrations/2026_03_16_100002_create_deliberation_tables.php`

**Routes ajoutées :**
- `GET /schedules/sessions` - Gestion des séances
- `GET /deliberations` - Liste des délibérations
- `GET /settings/deliberation` - Paramètres de délibération

### Corrections du module Absences

- Correction de la requête pour inclure les inscriptions pédagogiques (`pedagogicEnrollments`)
- Ajout du résumé des heures par ECU avec progression visuelle
- Message explicite quand aucun étudiant n'est trouvé malgré les filtres

---

## Structure des tables

### schedule_sessions
| Colonne | Type | Description |
|---------|------|-------------|
| id | bigint | PK |
| schedule_id | bigint | FK vers schedules (nullable) |
| ecu_id | bigint | FK vers ecus |
| teacher_id | bigint | FK vers teachers (nullable) |
| room_id | bigint | FK vers rooms (nullable) |
| academic_year_id | bigint | FK vers academic_years |
| student_group_id | bigint | FK vers student_groups (nullable) |
| session_date | date | Date de la séance |
| start_time | time | Heure de début |
| end_time | time | Heure de fin |
| type | enum | cm, td, tp |
| status | enum | planned, completed, cancelled, rescheduled |
| notes | text | Notes (nullable) |
| cancellation_reason | string | Motif d'annulation (nullable) |

### deliberation_settings
| Colonne | Type | Description |
|---------|------|-------------|
| id | bigint | PK |
| university_id | bigint | FK vers universities |
| ue_validation_average | decimal | Moyenne validation UE (défaut: 10) |
| ue_allow_compensation | boolean | Autoriser compensation UE |
| ue_compensation_min | decimal | Note min pour compensation |
| semester_validation_average | decimal | Moyenne validation semestre |
| semester_min_ue_validated_percent | int | % UE à valider |
| semester_allow_compensation | boolean | Autoriser compensation semestre |
| semester_max_ue_failed | int | Max UE non validées |
| year_validation_average | decimal | Moyenne validation année |
| year_require_all_semesters | boolean | Tous semestres requis |
| year_max_credits_failed | int | Max crédits non validés |
| allow_conditional_pass | boolean | Autoriser passage conditionnel |
| conditional_max_credits_debt | int | Max crédits en dette |
| mention_* | decimal | Seuils des mentions |

### deliberations
| Colonne | Type | Description |
|---------|------|-------------|
| id | bigint | PK |
| university_id | bigint | FK |
| academic_year_id | bigint | FK |
| program_year_id | bigint | FK |
| semester_id | bigint | FK (nullable) |
| type | enum | semester, annual |
| session | enum | normal, rattrapage |
| status | enum | draft, in_progress, validated, published |
| deliberation_date | date | Date de délibération |
| jury_members | json | Membres du jury (nullable) |
| notes | text | Notes (nullable) |

### deliberation_results
| Colonne | Type | Description |
|---------|------|-------------|
| id | bigint | PK |
| deliberation_id | bigint | FK |
| student_id | bigint | FK |
| semester_average | decimal | Moyenne semestre (nullable) |
| year_average | decimal | Moyenne année (nullable) |
| credits_validated | int | Crédits validés |
| credits_total | int | Crédits totaux |
| decision | string | Décision (validated, repeat, etc.) |
| mention | string | Mention (nullable) |
| rank | int | Classement (nullable) |
| jury_observation | text | Observation du jury (nullable) |
| conditions | json | Conditions de passage (nullable) |

### deliberation_ue_results
| Colonne | Type | Description |
|---------|------|-------------|
| id | bigint | PK |
| deliberation_result_id | bigint | FK |
| ue_id | bigint | FK |
| average | decimal | Moyenne UE (nullable) |
| credits | int | Crédits UE |
| is_validated | boolean | UE validée |
| is_compensated | boolean | Validée par compensation |
