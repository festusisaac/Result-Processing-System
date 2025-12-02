<?php

namespace App\Jobs;

use App\Models\Student;
use App\Models\ClassRoom;
use App\Models\AuditLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class PromoteStudentsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /** @var array */
    public $studentIds;

    /** @var int|null */
    public $initiatorId;

    /**
     * Create a new job instance.
     *
     * @param array $studentIds
     * @param int|null $initiatorId
     */
    public function __construct(array $studentIds, $initiatorId = null)
    {
        $this->studentIds = $studentIds;
        $this->initiatorId = $initiatorId;
        // give job a reasonable timeout when processing many records
        $this->timeout = 1200; // 20 minutes
    }

    /**
     * Execute the job.
     * Processes students in chunks to avoid memory/time spikes.
     */
    public function handle()
    {
        $promoted = 0;
        $failed = 0;
        $errors = [];

        try {
            // Process in chunks of 100 to be safe for large batches
            $chunks = array_chunk($this->studentIds, 100);
            foreach ($chunks as $chunk) {
                $students = Student::with('classRoom')->whereIn('id', $chunk)->get();

                foreach ($students as $student) {
                    try {
                        $currentClass = $student->classRoom;
                        if (! $currentClass) {
                            $errors[] = "{$student->full_name}: No assigned class.";
                            $failed++;
                            continue;
                        }

                        $promoteToName = trim($currentClass->promoting_class_name ?? '');
                        if ($promoteToName === '') {
                            $errors[] = "{$student->full_name}: No target class configured.";
                            $failed++;
                            continue;
                        }

                        $targetClass = ClassRoom::where('name', $promoteToName)->first();
                        if (! $targetClass) {
                            $errors[] = "{$student->full_name}: Target class '{$promoteToName}' not found.";
                            $failed++;
                            continue;
                        }

                        $from = $currentClass->name;
                        $to = $targetClass->name;

                        // Use transaction per student to avoid partial state if something fails
                        DB::transaction(function () use ($student, $targetClass, $from, $to) {
                            $student->update(['class_id' => $targetClass->id]);

                            AuditLog::log('promoted a student (bulk job)', [
                                'student' => $student->adm_no . ' - ' . $student->full_name,
                                'from' => $from,
                                'to' => $to,
                                'processed_by' => $this->initiatorId ?? 'system'
                            ]);
                        });

                        $promoted++;
                    } catch (\Exception $e) {
                        $failed++;
                        $errors[] = "{$student->full_name}: {$e->getMessage()}";
                        Log::error('PromoteStudentsJob error for student ' . $student->id, ['error' => $e->getMessage()]);
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error('PromoteStudentsJob failed', ['error' => $e->getMessage()]);
            // rethrow so job can be retried if configured
            throw $e;
        }

        // final log
        AuditLog::log('completed bulk student promotion job', [
            'requested_by' => $this->initiatorId ?? 'system',
            'promoted' => $promoted,
            'failed' => $failed,
            'errors' => array_slice($errors, 0, 50) // keep logs bounded
        ]);
    }
}
