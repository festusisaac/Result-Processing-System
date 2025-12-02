<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Models\ReportSetting;

class ReportSettingController extends Controller
{
    public function index()
    {
        $keys = [
            'result_title',
            'principal_comment',
            'school_logo',
            'principal_signature',
            'school_stamp',
            'school_address',
            'school_motto',
            'class_teacher_comments',
            'promotion_status',
            'promotion_status_rules',
            'grading',
            'remarks'
        ];

        $settings = [];
        foreach ($keys as $k) {
            $settings[$k] = ReportSetting::get($k, '');
        }

    // parse grading, remarks, class teacher and principal comment rules for table display
    $gradingRules = ReportSetting::getGradingRules();
    $remarksRules = ReportSetting::getRemarksRules();
    $classCommentsRules = ReportSetting::getClassTeacherCommentRules();
    $principalRules = ReportSetting::getPrincipalCommentRules();
    $promotionRules = ReportSetting::getPromotionStatusRules();

    return view('report_settings.index', compact('settings', 'gradingRules', 'remarksRules', 'classCommentsRules', 'principalRules', 'promotionRules'));
    }

    public function store(Request $request)
    {
        // log incoming values to help debug why changes sometimes aren't persisted
        try {
            Log::info('ReportSettingController@store incoming', $request->only(['grading','remarks','class_teacher_comments']));
        } catch (\Throwable $e) {
            // logging should not break saving
        }

        $data = $request->validate([
            'result_title' => 'nullable|string',
            'principal_comment' => 'nullable|string',
            'class_teacher_comments' => 'nullable|string',
            'promotion_status' => 'nullable|string',
            'promotion_status_rules' => 'nullable|string',
            'grading' => 'nullable|string',
            'remarks' => 'nullable|string',
            'school_logo' => 'nullable|image|max:2048',
            'principal_signature' => 'nullable|image|max:2048',
            'school_stamp' => 'nullable|image|max:2048',
            'school_address' => 'nullable|string',
            'school_motto' => 'nullable|string',
            'remove_school_logo' => 'nullable|in:1',
            'remove_principal_signature' => 'nullable|in:1',
            'remove_school_stamp' => 'nullable|in:1',
        ]);

        // don't persist the remove flags as report settings
        if (isset($data['remove_school_logo'])) {
            unset($data['remove_school_logo']);
        }
        if (isset($data['remove_principal_signature'])) {
            unset($data['remove_principal_signature']);
        }
        if (isset($data['remove_school_stamp'])) {
            unset($data['remove_school_stamp']);
        }

        // Server-side validation for grading/remarks/class_teacher_comments/principal_comment/promotion_status_rules
        $validationErrors = [];
        foreach (['grading','remarks','class_teacher_comments','principal_comment','promotion_status_rules'] as $key) {
            if (isset($data[$key]) && trim($data[$key]) !== '') {
                $errs = $this->validateRangeText($data[$key], $key);
                if (!empty($errs)) {
                    $validationErrors[$key] = $errs;
                }
            }
        }
        if (!empty($validationErrors)) {
            // flatten errors into message bag
            $messages = [];
            foreach ($validationErrors as $field => $errs) {
                foreach ($errs as $e) $messages[] = "{$field}: {$e}";
            }
            return back()->withErrors($messages)->withInput();
        }

        foreach ($data as $key => $value) {
            $value = $value ?? '';
            // handle grading, remarks, class_teacher_comments, principal_comment, and promotion_status_rules specially: store both text and structured JSON
            if (in_array($key, ['grading', 'remarks', 'class_teacher_comments', 'principal_comment', 'promotion_status_rules'])) {
                // parse lines like min-max:Label into structured rules
                $lines = preg_split('/\r?\n/', trim($value));
                $rules = [];
                foreach ($lines as $line) {
                    $line = trim($line);
                    if ($line === '') continue;
                    if (!str_contains($line, ':')) continue;
                    [$range, $label] = array_map('trim', explode(':', $line, 2));
                    if (!str_contains($range, '-')) continue;
                    [$min, $max] = array_map('trim', explode('-', $range, 2));
                    if (!is_numeric($min) || !is_numeric($max)) continue;
                    $min = (int)$min; $max = (int)$max;
                    $low = min($min, $max); $high = max($min, $max);
                    if ($key === 'grading') {
                        $rules[] = ['min' => $low, 'max' => $high, 'grade' => $label];
                    } elseif ($key === 'remarks') {
                        $rules[] = ['min' => $low, 'max' => $high, 'remark' => $label];
                    } elseif ($key === 'promotion_status_rules') {
                        $rules[] = ['min' => $low, 'max' => $high, 'status' => $label];
                    } else {
                        // class_teacher_comments or principal_comment
                        $rules[] = ['min' => $low, 'max' => $high, 'comment' => $label];
                    }
                }

                // save both textual and JSON representations
                \App\Models\ReportSetting::updateOrCreate(['key' => $key], ['value' => $value, 'value_json' => $rules]);
            } else {
                ReportSetting::set($key, $value);
            }
        }

        // handle uploaded logo file separately so we can store on disk
        if ($request->hasFile('school_logo')) {
            try {
                // delete previous logo file if present
                $existing = ReportSetting::get('school_logo', '');
                if (!empty($existing) && Storage::disk('public')->exists($existing)) {
                    Storage::disk('public')->delete($existing);
                }

                $file = $request->file('school_logo');
                $path = $file->store('school_logos', 'public');
                ReportSetting::set('school_logo', $path);
            } catch (\Throwable $e) {
                Log::error('Failed to store school_logo: ' . $e->getMessage());
            }
        }

        // handle remove logo request: delete file from disk and clear setting
        if ($request->input('remove_school_logo')) {
            try {
                $existing = ReportSetting::get('school_logo', '');
                if (!empty($existing) && Storage::disk('public')->exists($existing)) {
                    Storage::disk('public')->delete($existing);
                }
                ReportSetting::set('school_logo', '');
            } catch (\Throwable $e) {
                Log::error('Failed to remove school_logo: ' . $e->getMessage());
            }
        }



        // handle principal signature upload
        if ($request->hasFile('principal_signature')) {
            try {
                $existing = ReportSetting::get('principal_signature', '');
                if (!empty($existing) && Storage::disk('public')->exists($existing)) {
                    Storage::disk('public')->delete($existing);
                }
                $file = $request->file('principal_signature');
                $path = $file->store('signatures', 'public');
                ReportSetting::set('principal_signature', $path);
            } catch (\Throwable $e) {
                Log::error('Failed to store principal_signature: ' . $e->getMessage());
            }
        }

        if ($request->input('remove_principal_signature')) {
            try {
                $existing = ReportSetting::get('principal_signature', '');
                if (!empty($existing) && Storage::disk('public')->exists($existing)) {
                    Storage::disk('public')->delete($existing);
                }
                ReportSetting::set('principal_signature', '');
            } catch (\Throwable $e) {
                Log::error('Failed to remove principal_signature: ' . $e->getMessage());
            }
        }

        // handle school stamp upload
        if ($request->hasFile('school_stamp')) {
            try {
                $existing = ReportSetting::get('school_stamp', '');
                if (!empty($existing) && Storage::disk('public')->exists($existing)) {
                    Storage::disk('public')->delete($existing);
                }
                $file = $request->file('school_stamp');
                $path = $file->store('stamps', 'public');
                ReportSetting::set('school_stamp', $path);
            } catch (\Throwable $e) {
                Log::error('Failed to store school_stamp: ' . $e->getMessage());
            }
        }

        if ($request->input('remove_school_stamp')) {
            try {
                $existing = ReportSetting::get('school_stamp', '');
                if (!empty($existing) && Storage::disk('public')->exists($existing)) {
                    Storage::disk('public')->delete($existing);
                }
                ReportSetting::set('school_stamp', '');
            } catch (\Throwable $e) {
                Log::error('Failed to remove school_stamp: ' . $e->getMessage());
            }
        }

        // ensure the transient remove flag isn't stored as a persistent setting
        try {
            ReportSetting::where('key', 'remove_school_logo')->delete();
        } catch (\Throwable $e) {
            Log::error('Failed to clean remove_school_logo DB row: ' . $e->getMessage());
        }

        return back()->with('success', 'Report settings saved.');
    }

    /**
     * Validate multiline range text like "min-max:Label".
     * Returns array of error messages (empty if ok).
     */
    private function validateRangeText(string $text, string $field)
    {
        $lines = preg_split('/\r?\n/', trim($text));
        $ranges = [];
        $errors = [];
        foreach ($lines as $i => $line) {
            $line = trim($line);
            if ($line === '') continue;
            if (!str_contains($line, ':')) {
                $errors[] = "Line " . ($i+1) . " must contain ':' separating range and label.";
                continue;
            }
            [$range, $label] = array_map('trim', explode(':', $line, 2));
            if (!str_contains($range, '-')) {
                $errors[] = "Line " . ($i+1) . " range must be in 'min-max' format.";
                continue;
            }
            [$min, $max] = array_map('trim', explode('-', $range, 2));
            if (!is_numeric($min) || !is_numeric($max)) {
                $errors[] = "Line " . ($i+1) . " min and max must be numeric.";
                continue;
            }
            $min = (int)$min; $max = (int)$max;
            if ($min > $max) {
                // normalize but also warn
                [$min, $max] = [$max, $min];
            }
            if ($min < 0 || $max > 100) {
                $errors[] = "Line " . ($i+1) . " ranges must be between 0 and 100.";
            }
            $ranges[] = ['min'=>$min,'max'=>$max,'line'=>$i+1];
        }

        // Check for overlaps
        usort($ranges, function($a,$b){return $a['min'] <=> $b['min'];});
        for ($i=1;$i<count($ranges);$i++) {
            if ($ranges[$i]['min'] <= $ranges[$i-1]['max']) {
                $errors[] = "Ranges overlap between lines {$ranges[$i-1]['line']} and {$ranges[$i]['line']}";
            }
        }

        return $errors;
    }
}
