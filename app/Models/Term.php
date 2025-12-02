<?php

namespace App\Models;

use App\Traits\BelongsToSession;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Term extends Model
{
    use HasFactory, SoftDeletes, BelongsToSession;

    // using default auto-incrementing id

    protected $fillable = [
        'term_name',
        'term_begins',
        'term_ends',
        'school_opens',
        'terminal_duration',
        'next_term_begins',
        'session_id',
        'result_status',
        'published_by',
        'published_at'
    ];


    protected $casts = [
        'term_begins' => 'date',
        'term_ends' => 'date',
        'next_term_begins' => 'date',
        'school_opens' => 'integer',
        'published_at' => 'datetime'
    ];

    public function isPublished()
    {
        return strtolower($this->result_status) === 'published';
    }

    public function isDraft()
    {
        return strtolower($this->result_status) === 'draft';
    }

    public function canPublish()
    {
        return in_array(strtolower($this->result_status), ['draft', 'approved', 'withdrawn']);
    }

    public function getStudentCount()
    {
        return Student::whereHas('scores', function($q) {
            $q->where('term_id', $this->id);
        })->count();
    }

}
