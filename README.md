# EduManage SaaS - Plateforme de Gestion Universitaire

Une solution SaaS complète pour la gestion administrative et académique des universités avec architecture multi-tenant.

## 🎯 Fonctionnalités

- **Multi-tenant** : Chaque université a son espace isolé
- **Modules activables** : L'admin active/désactive les modules selon ses besoins
- **Structure UE/ECU** : Programmes → Années → Semestres → UE → ECU
- **Gestion complète** : Étudiants, enseignants, cours, notes, planification
- **Préparé pour Moodle** : Champs de synchronisation déjà en place
- **Multilingue** : Français et Anglais

## 🏗️ Stack Technique

| Composant | Technologie |
|-----------|-------------|
| **Backend** | Laravel 11 (PHP 8.2+) |
| **Frontend** | Blade + Livewire + Alpine.js |
| **CSS** | TailwindCSS |
| **Base de données** | MySQL 8 |
| **Auth** | Laravel Breeze + Spatie Permission |
| **PDF** | DomPDF |
| **i18n** | Laravel Localization (FR/EN) |

### Modules Principaux

#### 🎓 Académiques
- Gestion des étudiants
- Gestion des cours et programmes
- Suivi des notes et évaluations
- Inscriptions et admissions
- Emplois du temps

#### 🏢 Administratifs
- Gestion du personnel (professeurs, administratifs)
- Gestion financière (budgets, dépenses)
- Rapports et analytics
- Gestion des infrastructures
- Communication interne

#### 💼 SaaS
- Gestion multi-universités
- Abonnements et facturation
- Tableaux de bord administrateurs
- Personnalisation par université

## 🚀 Démarrage Rapide

### Prérequis
- PHP 8.2+
- Composer 2.x
- MySQL 8.x
- Node.js 18+ (pour les assets)

### Installation
```bash
# Aller dans le dossier du projet
cd edumanage

# Installer les dépendances PHP
composer install

# Installer les dépendances JS
npm install

# Copier le fichier d'environnement
cp .env.example .env

# Générer la clé d'application
php artisan key:generate

# Configurer la base de données dans .env
# DB_DATABASE=edumanage
# DB_USERNAME=root
# DB_PASSWORD=

# Exécuter les migrations
php artisan migrate

# Exécuter le seeder (données de test)
php artisan db:seed

# Compiler les assets
npm run build

# Démarrer le serveur
php artisan serve
```

### Comptes de test
| Rôle | Email | Mot de passe |
|------|-------|--------------|
| Super Admin | superadmin@edumanage.com | password |
| Admin Université | admin@univ-demo.edu | password |

## 📊 Structure du Projet

```
edumanage/
├── app/
│   ├── Http/
│   │   ├── Controllers/     # Controllers (Dashboard, Student, Teacher, Program)
│   │   ├── Middleware/      # TenantAccess, CheckModule, SetLocale
│   │   └── Livewire/        # Composants interactifs
│   ├── Models/              # 20 modèles Eloquent
│   ├── Traits/              # BelongsToUniversity, HasMoodleSync
│   └── Services/            # Logique métier
├── database/
│   ├── migrations/          # 19 tables MySQL
│   └── seeders/             # Données de test
├── resources/
│   ├── views/               # Vues Blade + Livewire
│   │   ├── layouts/         # Layout admin avec sidebar
│   │   ├── dashboard/       # Dashboards par rôle
│   │   ├── students/        # CRUD étudiants
│   │   ├── teachers/        # CRUD enseignants
│   │   ├── programs/        # CRUD programmes
│   │   └── settings/        # Paramètres université
│   └── lang/                # Traductions FR/EN
├── routes/
│   └── web.php              # Routes principales
└── config/
    └── permission.php       # Config Spatie
```

## 🔐 Sécurité

- Authentification JWT avec rafraîchissement
- Validation des entrées côté serveur
- Protection CSRF
- Chiffrement des données sensibles
- Rôles et permissions granulaires

## 📈 Scalabilité

- Architecture multi-tenant optimisée
- Cache Redis pour les performances
- Support de haute disponibilité
- Monitoring et logging intégré

## 🤝 Contribution

1. Fork le projet
2. Créer une branche feature
3. Commit les changements
4. Push vers la branche
5. Créer une Pull Request

## 📄 Licence

MIT License
