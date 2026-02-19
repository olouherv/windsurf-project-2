<?php

namespace App\Models;

use App\Traits\BelongsToUniversity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Thesis extends Model
{
    use HasFactory, SoftDeletes, BelongsToUniversity;

    protected $fillable = [
        'university_id',
        'student_id',
        'academic_year_id',
        'supervisor_teacher_id',
        'title',
        'abstract',
        'submission_date',
        'defense_date',
        'grade',
        'status',
        'notes',
    ];

    protected $casts = [
        'submission_date' => 'date',
        'defense_date' => 'date',
        'grade' => 'decimal:2',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function supervisor(): BelongsTo
    {
        return $this->belongsTo(Teacher::class, 'supervisor_teacher_id');
    }
}
