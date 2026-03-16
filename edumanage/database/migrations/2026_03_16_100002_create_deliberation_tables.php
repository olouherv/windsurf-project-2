<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Paramètres de délibération par université
        Schema::create('deliberation_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('university_id')->constrained()->onDelete('cascade');
            
            // Critères de validation UE
            $table->decimal('ue_validation_average', 4, 2)->default(10.00);
            $table->boolean('ue_allow_compensation')->default(true);
            $table->decimal('ue_compensation_min', 4, 2)->default(8.00);
            
            // Critères de validation Semestre
            $table->decimal('semester_validation_average', 4, 2)->default(10.00);
            $table->integer('semester_min_ue_validated_percent')->default(100);
            $table->boolean('semester_allow_compensation')->default(true);
            $table->integer('semester_max_ue_failed')->default(2);
            
            // Critères de validation Année
            $table->decimal('year_validation_average', 4, 2)->default(10.00);
            $table->boolean('year_require_all_semesters')->default(true);
            $table->integer('year_max_credits_failed')->default(12);
            
            // Critères de passage conditionnel
            $table->boolean('allow_conditional_pass')->default(true);
            $table->integer('conditional_max_credits_debt')->default(6);
            
            // Mentions
            $table->decimal('mention_passable_min', 4, 2)->default(10.00);
            $table->decimal('mention_assez_bien_min', 4, 2)->default(12.00);
            $table->decimal('mention_bien_min', 4, 2)->default(14.00);
            $table->decimal('mention_tres_bien_min', 4, 2)->default(16.00);
            
            $table->timestamps();
            
            $table->unique('university_id');
        });

        // Délibérations (sessions de jury)
        Schema::create('deliberations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('university_id')->constrained()->onDelete('cascade');
            $table->foreignId('academic_year_id')->constrained()->onDelete('cascade');
            $table->foreignId('program_year_id')->constrained()->onDelete('cascade');
            $table->foreignId('semester_id')->nullable()->constrained()->onDelete('cascade');
            
            $table->enum('type', ['semester', 'annual'])->default('semester');
            $table->enum('session', ['normal', 'rattrapage'])->default('normal');
            $table->date('deliberation_date');
            $table->enum('status', ['draft', 'in_progress', 'validated', 'published'])->default('draft');
            
            $table->foreignId('president_id')->nullable()->constrained('users')->onDelete('set null');
            $table->text('jury_members')->nullable();
            $table->text('notes')->nullable();
            
            $table->timestamp('validated_at')->nullable();
            $table->foreignId('validated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('published_at')->nullable();
            
            $table->timestamps();
            
            $table->index(['academic_year_id', 'program_year_id', 'type']);
        });

        // Résultats de délibération par étudiant
        Schema::create('deliberation_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('deliberation_id')->constrained()->onDelete('cascade');
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            
            // Moyennes calculées
            $table->decimal('semester_average', 5, 2)->nullable();
            $table->decimal('year_average', 5, 2)->nullable();
            $table->integer('credits_validated')->default(0);
            $table->integer('credits_total')->default(0);
            
            // Décision
            $table->enum('decision', [
                'validated',           // Admis
                'validated_compensated', // Admis par compensation
                'conditional',         // Passage conditionnel (dette de crédits)
                'retake',              // Rattrapage
                'repeat',              // Redoublement
                'exclusion',           // Exclusion
                'pending'              // En attente
            ])->default('pending');
            
            $table->string('mention')->nullable();
            $table->integer('rank')->nullable();
            
            $table->text('jury_observation')->nullable();
            $table->text('conditions')->nullable();
            
            $table->timestamps();
            
            $table->unique(['deliberation_id', 'student_id']);
        });

        // Détail par UE pour la délibération
        Schema::create('deliberation_ue_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('deliberation_result_id')->constrained()->onDelete('cascade');
            $table->foreignId('ue_id')->constrained()->onDelete('cascade');
            
            $table->decimal('average', 5, 2)->nullable();
            $table->integer('credits')->default(0);
            $table->boolean('is_validated')->default(false);
            $table->boolean('is_compensated')->default(false);
            
            $table->timestamps();
            
            $table->unique(['deliberation_result_id', 'ue_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('deliberation_ue_results');
        Schema::dropIfExists('deliberation_results');
        Schema::dropIfExists('deliberations');
        Schema::dropIfExists('deliberation_settings');
    }
};
