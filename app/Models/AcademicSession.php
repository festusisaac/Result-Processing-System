<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Models\Term;
use App\Models\Score;
use App\Models\Comment;
use App\Models\Attendance;
use App\Models\Student;

class AcademicSession extends Model
{
    use HasFactory;

    protected $table = 'academic_sessions';
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

    protected $fillable = ['name', 'active'];

    protected $casts = [
        'active' => 'boolean',
    ];

    public function students()
    {
        return $this->hasMany(Student::class, 'session_id');
    }

    public function terms()
    {
        return $this->hasMany(Term::class, 'session_id');
    }

    public function scores()
    {
        return $this->hasMany(Score::class, 'session_id');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class, 'session_id');
    }

    public function attendance()
    {
        return $this->hasMany(Attendance::class, 'session_id');
    }

    /**
     * Activate this session and manage related data
     */
    public function activate()
    {
        DB::transaction(function () {
            // 1. Soft delete academic data from currently active session
            if ($currentActive = static::where('active', true)->first()) {
                // Soft delete related data from current active session
                $currentActive->terms()->delete();
                $currentActive->scores()->delete();
                $currentActive->comments()->delete();
                $currentActive->attendance()->delete();
                
                // Deactivate current session
                $currentActive->update(['active' => false]);
            }

            // 2. Restore any previously soft-deleted academic data for this session
            // The models using the BelongsToSession trait have a global
            // scope that filters to the currently active session. At this
            // point in the flow we temporarily have no active session (we
            // just deactivated the previous one), so the global scope
            // would prevent us from finding the trashed records we need to
            // restore. Use `withoutGlobalScope('session')` to bypass that
            // scope when restoring.
            Term::withTrashed()
                ->withoutGlobalScope('session')
                ->where('session_id', $this->id)
                ->restore();

            Score::withTrashed()
                ->withoutGlobalScope('session')
                ->where('session_id', $this->id)
                ->restore();

            Comment::withTrashed()
                ->withoutGlobalScope('session')
                ->where('session_id', $this->id)
                ->restore();

            Attendance::withTrashed()
                ->withoutGlobalScope('session')
                ->where('session_id', $this->id)
                ->restore();

            // 3. Activate this session
            $this->update(['active' => true]);
        });
    }

    /**
     * Get the currently active academic session
     */
    public static function getActive()
    {
        return static::where('active', true)->first();
    }
}
