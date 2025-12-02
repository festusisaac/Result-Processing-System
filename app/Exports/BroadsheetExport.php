<?php

namespace App\Exports;

use App\Models\ClassRoom;
use App\Models\Student;
use App\Models\Score;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Facades\DB;

class BroadsheetExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles
{
    protected $classId;
    protected $termId;
    protected $sessionId;
    protected $cumulative;

    public function __construct($classId, $termId = null, $sessionId = null, $cumulative = false)
    {
        $this->classId = $classId;
        $this->termId = $termId;
        $this->sessionId = $sessionId;
        $this->cumulative = (bool) $cumulative;
    }

    public function collection()
    {
        $class = ClassRoom::with(['subjects' => function($q) { $q->orderBy('name'); }])->findOrFail($this->classId);
        $subjects = $class->subjects;

        $students = Student::where('class_id', $this->classId)
            ->orderBy('adm_no')
            ->get();

        $rows = collect();

        foreach ($students as $student) {
            $row = [];
            $row[] = $student->adm_no;
            $row[] = $student->full_name;

            $totalForStudent = 0;
            $subjectCount = 0;
            $passes = 0;

            foreach ($subjects as $subject) {
                if ($this->cumulative) {
                    // Sum across session for the subject
                    $scoreValue = (int) Score::where('student_id', $student->id)
                        ->where('subject_id', $subject->id)
                        ->where(function($q) {
                            if ($this->sessionId) {
                                $q->where('session_id', $this->sessionId);
                            }
                        })
                        ->sum(DB::raw('COALESCE(ca_score,0) + COALESCE(exam_score,0)'));
                } else {
                    $score = Score::where('student_id', $student->id)
                        ->where('subject_id', $subject->id)
                        ->when($this->termId, function($q) {
                            $q->where('term_id', $this->termId);
                        })
                        ->when($this->sessionId, function($q) {
                            $q->where('session_id', $this->sessionId);
                        })
                        ->first();

                    $scoreValue = (int) (($score?->ca_score ?? 0) + ($score?->exam_score ?? 0));
                }

                $row[] = $scoreValue;
                $totalForStudent += $scoreValue;
                $subjectCount++;
            }

            $average = $subjectCount ? round($totalForStudent / $subjectCount, 2) : 0;

            $row[] = $totalForStudent;
            $row[] = $average;

            $rows->push($row);
        }

        return $rows;
    }

    public function headings(): array
    {
        $class = ClassRoom::with(['subjects' => function($q) { $q->orderBy('name'); }])->findOrFail($this->classId);
        $subjects = $class->subjects;

        $heads = ['Admission No', 'Full Name'];
        foreach ($subjects as $subject) {
            $heads[] = $subject->name;
        }

        $heads[] = 'Total';
        $heads[] = 'Average';

        return $heads;
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:Z1')->getFont()->setBold(true);
        return $sheet;
    }
}
