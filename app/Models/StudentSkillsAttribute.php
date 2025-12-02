<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class StudentSkillsAttribute extends Model
{
    use HasFactory, SoftDeletes;

    public $incrementing = false;
    protected $keyType = 'string';
    protected $table = 'student_skills_attributes';

    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
        });
    }

    protected $fillable = ['student_id', 'skill_attribute_id', 'term_id', 'score', 'recorded_by', 'date'];

    protected $casts = [
        'date' => 'date',
        'score' => 'integer',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function skillAttribute()
    {
        return $this->belongsTo(SkillsAttribute::class, 'skill_attribute_id');
    }

    public function term()
    {
        return $this->belongsTo(Term::class);
    }

    public function recordedBy()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }
}
