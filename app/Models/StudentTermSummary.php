<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentTermSummary extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory;

    protected $fillable = [
        'student_id',
        'term_id',
        'session_id',
        'class_id',
        'total_score',
        'average_score',
        'position',
        'class_size',
        'number_of_subjects',
        'total_obtainable',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function term()
    {
        return $this->belongsTo(Term::class);
    }

    public function session()
    {
        return $this->belongsTo(Session::class);
    }

    public function classRoom()
    {
        return $this->belongsTo(ClassRoom::class, 'class_id');
    }
}
