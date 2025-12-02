<?php

namespace App\Imports;

use App\Models\Student;
use App\Models\Score;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Str;

class ScoresImport implements ToModel, WithHeadingRow
{
    protected $classId;
    protected $subjectId;
    protected $termId;

    public function __construct($classId, $subjectId, $termId)
    {
        $this->classId = $classId;
        $this->subjectId = $subjectId;
        $this->termId = $termId;
    }

    /**
     * @param array $row
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        // Expect either 'adm_no' or 'student_id' columns
        $admNo = $row['adm_no'] ?? null;
        $studentId = $row['student_id'] ?? null;

        $student = null;
        if ($studentId) {
            $student = Student::find($studentId);
        } elseif ($admNo) {
            $student = Student::where('adm_no', $admNo)->first();
        }

        if (!$student) {
            // skip rows without student match
            return null;
        }

        $ca = isset($row['ca_score']) ? floatval($row['ca_score']) : 0;
        // allow older files with ca1/ca2
        if ($ca === 0 && (isset($row['ca1']) || isset($row['ca2']))) {
            $ca = floatval($row['ca1'] ?? 0) + floatval($row['ca2'] ?? 0);
        }

        $exam = isset($row['exam_score']) ? floatval($row['exam_score']) : (isset($row['exam']) ? floatval($row['exam']) : 0);

        $score = Score::updateOrCreate([
            'student_id' => $student->id,
            'subject_id' => $this->subjectId,
            'term_id' => $this->termId,
        ], [
            'ca_score' => $ca,
            'exam_score' => $exam,
            'grade' => $this->calculateGrade($ca + $exam),
            'remark' => $this->calculateRemark($this->calculateGrade($ca + $exam)),
            'session_id' => $student->session_id ?? null,
        ]);

        return $score;
    }

    private function calculateGrade($totalScore)
    {
        if ($totalScore >= 70) return 'A';
        if ($totalScore >= 60) return 'B';
        if ($totalScore >= 50) return 'C';
        if ($totalScore >= 45) return 'D';
        if ($totalScore >= 40) return 'E';
        return 'F';
    }

    private function calculateRemark($grade)
    {
        return match($grade) {
            'A' => 'Excellent',
            'B' => 'Very Good',
            'C' => 'Good',
            'D' => 'Fair',
            'E' => 'Poor',
            'F' => 'Failed',
            default => 'Unknown'
        };
    }
}
