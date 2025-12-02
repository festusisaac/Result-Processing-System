<?php

namespace App\Http\Controllers;

use App\Models\SkillsAttribute;
use App\Models\StudentSkillsAttribute;
use App\Models\Student;
use App\Models\ClassRoom;
use App\Models\Term;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SkillsAttributeController extends Controller
{
    public function skills()
    {
        $classes = ClassRoom::orderBy('name')->get();
        $terms = Term::orderBy('term_name')->get();
        $skillAttributes = SkillsAttribute::where('slug', 'not like', 'psychomotor-%')
            ->orderBy('name')
            ->get();

        return view('skills.index', compact('classes', 'terms', 'skillAttributes'));
    }

    public function getStudentsWithSkills(Request $request)
    {
        $request->validate([
            'class_id' => 'required|exists:classes,id',
            'term_id' => 'required|exists:terms,id'
        ]);

        $students = Student::where('class_id', $request->class_id)
            ->when($request->search, function($query) use ($request) {
                $query->where(function($q) use ($request) {
                    $q->where('adm_no', 'like', '%' . $request->search . '%')
                      ->orWhere('full_name', 'like', '%' . $request->search . '%');
                });
            })
            ->with(['skillsAttributes' => function($query) use ($request) {
                $query->where('term_id', $request->term_id)
                      ->whereHas('skillAttribute', function($q) {
                          $q->where('slug', 'not like', 'psychomotor-%');
                      });
            }])
            ->get()
            ->map(function($student) {
                return [
                    'id' => $student->id,
                    'adm_no' => $student->adm_no,
                    'full_name' => $student->full_name,
                    'skills' => $student->skillsAttributes->keyBy('skill_attribute_id')
                        ->map(fn($s) => ['score' => $s->score])
                        ->toArray()
                ];
            });

        return response()->json($students);
    }

    public function storeBulk(Request $request)
    {
        $request->validate([
            'class_id' => 'required|exists:classes,id',
            'term_id' => 'required|exists:terms,id',
            'skills' => 'required|array'
        ]);

        DB::beginTransaction();
        try {
            foreach ($request->skills as $studentId => $skillsData) {
                foreach ($skillsData as $skillId => $score) {
                    $skillRecord = StudentSkillsAttribute::firstOrNew([
                        'student_id' => $studentId,
                        'skill_attribute_id' => $skillId,
                        'term_id' => $request->term_id,
                    ]);

                    $skillRecord->score = intval($score ?? 0);
                    $skillRecord->date = now()->toDateString();
                    $skillRecord->recorded_by = auth()->id();
                    $skillRecord->save();
                }
            }
            DB::commit();
            return response()->json(['message' => 'Skills saved successfully']);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Failed to save skills', [
                'message' => $e->getMessage(),
                'exception' => $e,
                'payload' => $request->all(),
            ]);
            return response()->json(['error' => 'Failed to save skills'], 500);
        }
    }

    // Psychomotor Skills Management
    public function psychomotorSkills()
    {
        $classes = ClassRoom::orderBy('name')->get();
        $terms = Term::orderBy('term_name')->get();
        $psychomotorSkills = SkillsAttribute::where('slug', 'like', 'psychomotor-%')
            ->orderBy('name')
            ->get();

        return view('skills.psychomotor', compact('classes', 'terms', 'psychomotorSkills'));
    }

    public function getStudentsWithPsychomotorSkills(Request $request)
    {
        $request->validate([
            'class_id' => 'required|exists:classes,id',
            'term_id' => 'required|exists:terms,id'
        ]);

        // Get psychomotor skill IDs
        $psychomotorSkillIds = SkillsAttribute::where('slug', 'like', 'psychomotor-%')
            ->pluck('id')
            ->toArray();

        $students = Student::where('class_id', $request->class_id)
            ->when($request->search, function($query) use ($request) {
                $query->where(function($q) use ($request) {
                    $q->where('adm_no', 'like', '%' . $request->search . '%')
                      ->orWhere('full_name', 'like', '%' . $request->search . '%');
                });
            })
            ->with(['skillsAttributes' => function($query) use ($request, $psychomotorSkillIds) {
                $query->where('term_id', $request->term_id)
                      ->whereIn('skill_attribute_id', $psychomotorSkillIds);
            }])
            ->get()
            ->map(function($student) {
                return [
                    'id' => $student->id,
                    'adm_no' => $student->adm_no,
                    'full_name' => $student->full_name,
                    'skills' => $student->skillsAttributes->keyBy('skill_attribute_id')
                        ->map(fn($s) => ['score' => $s->score])
                        ->toArray()
                ];
            });

        return response()->json($students);
    }

    public function storePsychomotorSkillsBulk(Request $request)
    {
        $request->validate([
            'class_id' => 'required|exists:classes,id',
            'term_id' => 'required|exists:terms,id',
            'skills' => 'required|array'
        ]);

        DB::beginTransaction();
        try {
            foreach ($request->skills as $studentId => $skillsData) {
                foreach ($skillsData as $skillId => $score) {
                    $skillRecord = StudentSkillsAttribute::firstOrNew([
                        'student_id' => $studentId,
                        'skill_attribute_id' => $skillId,
                        'term_id' => $request->term_id,
                    ]);

                    $skillRecord->score = intval($score ?? 0);
                    $skillRecord->date = now()->toDateString();
                    $skillRecord->recorded_by = auth()->id();
                    $skillRecord->save();
                }
            }
            DB::commit();
            return response()->json(['message' => 'Psychomotor skills saved successfully']);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Failed to save psychomotor skills', [
                'message' => $e->getMessage(),
                'exception' => $e,
                'payload' => $request->all(),
            ]);
            return response()->json(['error' => 'Failed to save psychomotor skills'], 500);
        }
    }


    // CRUD endpoints for managing skill attributes
    public function index()
    {
        $skills = SkillsAttribute::paginate(15);
        return view('skills.manage', compact('skills'));
    }

    public function show(SkillsAttribute $skillsAttribute)
    {
        return response()->json($skillsAttribute);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:skills_attributes',
            'description' => 'nullable|string',
        ]);

        $skill = SkillsAttribute::create($validated);
        return response()->json($skill, 201);
    }

    public function update(Request $request, SkillsAttribute $skillsAttribute)
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|unique:skills_attributes,name,' . $skillsAttribute->id . ',id',
            'description' => 'nullable|string',
        ]);

        $skillsAttribute->update($validated);
        return response()->json($skillsAttribute);
    }

    public function destroy(SkillsAttribute $skillsAttribute)
    {
        $skillsAttribute->delete();
        return response()->json(null, 204);
    }
}
