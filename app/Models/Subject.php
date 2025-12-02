<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Subject extends Model
{
    use HasFactory;

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

    protected $fillable = ['name','subject_group_id','class_id','teacher_id','session_id'];

    public function scores()
    {
        return $this->hasMany(Score::class);
    }

    public function classes()
    {
        return $this->belongsToMany(ClassRoom::class, 'class_subject', 'subject_id', 'class_room_id')
            ->withTimestamps()
            ->withPivot('id')
            ->using(ClassSubjectPivot::class);
    }

    public function subjectGroup()
    {
        return $this->belongsTo(SubjectGroup::class);
    }

    public function classRoom()
    {
        return $this->belongsTo(ClassRoom::class, 'class_id');
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function session()
    {
        return $this->belongsTo(AcademicSession::class, 'session_id');
    }
}
