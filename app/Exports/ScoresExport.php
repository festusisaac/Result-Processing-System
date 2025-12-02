<?php

namespace App\Exports;

use App\Models\Student;
use App\Models\Score;
use App\Models\ClassRoom;
use App\Models\Subject;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ScoresExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
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

    public function collection()
    {
        return Student::where('class_id', $this->classId)
            ->orderBy('adm_no')
            ->get()
            ->map(function($student) {
                $score = $student->scores()
                    ->where('subject_id', $this->subjectId)
                    ->where('term_id', $this->termId)
                    ->first();

                return [
                    'student_id' => $student->id,
                    'adm_no' => $student->adm_no,
                    'full_name' => $student->full_name,
                    'ca_score' => $score?->ca_score ?? 0,
                    'exam_score' => $score?->exam_score ?? 0,
                    'total_score' => $score?->total_score ?? ($score?->ca_score + $score?->exam_score ?? 0),
                    'grade' => $score?->grade ?? null,
                    'remark' => $score?->remark ?? null,
                ];
            });
    }

    public function map($row): array
    {
        return [
            $row['student_id'],
            $row['adm_no'],
            $row['full_name'],
            $row['ca_score'],
            $row['exam_score'],
            $row['total_score'],
            $row['grade'],
            $row['remark'],
        ];
    }

    public function headings(): array
    {
        return [
            'Student ID',
            'Admission No',
            'Full Name',
            'CA Score',
            'Exam Score',
            'Total Score',
            'Grade',
            'Remark'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:H1')->getFont()->setBold(true);
        return $sheet;
    }
}
