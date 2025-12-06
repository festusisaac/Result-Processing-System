<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Auditable;

class Score extends Model
{
    use HasFactory, SoftDeletes, Auditable;

    public $incrementing = false;
    protected $keyType = 'string';

    protected static function booted()
    {
        // Call parent boot if needed, but here we just have local boot logic
        
        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
        });

        $invalidateCache = function ($score) {
            try {
                \App\Models\StudentTermSummary::where('student_id', $score->student_id)
                    ->where('term_id', $score->term_id)
                    ->where('session_id', $score->session_id)
                    ->delete();
            } catch (\Exception $e) {
                // Log error but don't stop the process
                \Illuminate\Support\Facades\Log::error('Failed to invalidate result cache: ' . $e->getMessage());
            }
        };

        static::saved($invalidateCache);
        static::deleted($invalidateCache);
    }

    protected $fillable = ['student_id','subject_id','ca_score','ca1_score','ca2_score','exam_score','grade','remark','term_id','session_id'];

    protected $appends = ['total_score'];

    public function getTotalScoreAttribute()
    {
        return ($this->ca1_score ?? 0) + ($this->ca2_score ?? 0) + ($this->exam_score ?? 0);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function term()
    {
        return $this->belongsTo(Term::class);
    }

    // Optional: Descriptive audit log
    public function getAuditDescription($action)
    {
        $subjectName = $this->subject ? $this->subject->name : 'Unknown Subject';
        $studentName = $this->student ? $this->student->full_name : 'Unknown Student';
        
        return "{$action} Score: {$studentName} - {$subjectName}";
    }
}
