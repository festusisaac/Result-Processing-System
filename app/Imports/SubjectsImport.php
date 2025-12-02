<?php

namespace App\Imports;

use App\Models\Subject;
use App\Models\AcademicSession;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class SubjectsImport implements ToModel, WithHeadingRow
{
    /**
     * Map a row to a Subject model. Rows with missing critical mappings are skipped and logged.
     * This uses firstOrCreate to avoid creating duplicate subjects for the same class & session.
     */
    public function model(array $row)
    {
        $activeSession = AcademicSession::where('active', 1)->first();

        if (!$activeSession) {
            Log::warning('SubjectsImport skipped row because no active session found', $row);
            return null;
        }

        $name = Arr::get($row, 'subject_name') ?: null;
        $groupName = Arr::get($row, 'subject_group');
        $teacherName = Arr::get($row, 'subject_teacher');
        $className = Arr::get($row, 'class');

        // Require at least subject name and class name to create
        if (empty($name) || empty($className)) {
            Log::warning('SubjectsImport skipped row due to missing subject name or class', $row);
            return null;
        }

        $classId = $this->getClassId($className);
        if (!$classId) {
            Log::warning('SubjectsImport skipped row because class not found', ['class' => $className, 'row' => $row]);
            return null;
        }

        $subjectGroupId = $this->getSubjectGroupId($groupName);
        $teacherId = $this->getTeacherId($teacherName);

        // Avoid duplicates: unique by name + class + session
        $subject = Subject::firstOrNew([
            'name' => $name,
            'class_id' => $classId,
            'session_id' => $activeSession->id,
        ]);

        // Set/overwrite fields
        $subject->subject_group_id = $subjectGroupId;
        $subject->teacher_id = $teacherId;
        $subject->session_id = $activeSession->id;

        // Save and return as expected by ToModel
        $subject->save();

        return $subject;
    }

    private function getSubjectGroupId($name)
    {
        if (empty($name)) return null;
        $activeSession = AcademicSession::where('active', 1)->first();
        return \App\Models\SubjectGroup::where('name', $name)
            ->when($activeSession, fn($q) => $q->where('session_id', $activeSession->id))
            ->first()?->id;
    }

    private function getTeacherId($name)
    {
        if (empty($name)) return null;
        return \App\Models\User::where('name', $name)->first()?->id;
    }

    private function getClassId($name)
    {
        if (empty($name)) return null;
        return \App\Models\ClassRoom::where('name', $name)->first()?->id;
    }
}
