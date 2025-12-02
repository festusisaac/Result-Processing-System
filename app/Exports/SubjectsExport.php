<?php

namespace App\Exports;

use App\Models\Subject;
use App\Models\AcademicSession;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SubjectsExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle, ShouldAutoSize
{
    protected $sessionId;

    public function __construct($sessionId)
    {
        $this->sessionId = $sessionId;
    }

    public function collection()
    {
        return Subject::with(['subjectGroup', 'teacher', 'classRoom'])
            ->where('session_id', $this->sessionId)
            ->orderBy('name')
            ->get();
    }

    public function map($subject): array
    {
        return [
            $subject->id,
            $subject->subjectGroup?->name ?? 'Ungrouped',
            $subject->name,
            $subject->teacher?->name ?? 'Not Assigned',
            $subject->classRoom?->name ?? 'Not Assigned',
            $subject->created_at->format('Y-m-d'),
            $subject->updated_at->format('Y-m-d'),
        ];
    }

    public function headings(): array
    {
        $session = AcademicSession::find($this->sessionId);
        return [
            ['Subjects List - ' . ($session ? $session->name : 'All Sessions')],
            [
                'ID',
                'Subject Group',
                'Subject Name',
                'Teacher',
                'Class',
                'Created Date',
                'Last Updated',
            ]
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Style for the title row
        $sheet->mergeCells('A1:G1');
        $sheet->getStyle('A1')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 14,
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ],
        ]);

        // Style for the header row
        $sheet->getStyle('A2:G2')->applyFromArray([
            'font' => [
                'bold' => true,
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'color' => ['rgb' => 'E2E8F0'],
            ],
        ]);

        // Add borders to all cells
        $sheet->getStyle($sheet->calculateWorksheetDimension())->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
        ]);

        return $sheet;
    }

    public function title(): string
    {
        $session = AcademicSession::find($this->sessionId);
        return 'Subjects - ' . ($session ? $session->name : 'All Sessions');
    }
}
