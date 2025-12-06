<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Traits\Auditable;

class Student extends Model
{
    use HasFactory, SoftDeletes, Auditable;

    public $incrementing = false;
    protected $keyType = 'string';

    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
        });
    }

    protected $fillable = ['adm_no','full_name','class_id','session_id','dob','gender','passport'];

    protected $casts = [
        'dob' => 'date',
    ];

    public function classRoom()
    {
        return $this->belongsTo(ClassRoom::class, 'class_id');
    }

    public function session()
    {
        return $this->belongsTo(AcademicSession::class, 'session_id');
    }

    public function scores()
    {
        return $this->hasMany(Score::class);
    }

    public function attendance()
    {
        return $this->hasMany(Attendance::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function skillsAttributes()
    {
        return $this->hasMany(StudentSkillsAttribute::class);
    }
}
