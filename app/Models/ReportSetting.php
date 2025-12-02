<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportSetting extends Model
{
    use HasFactory;

    protected $table = 'report_settings';

    protected $fillable = ['key', 'value', 'value_json'];

    public $timestamps = true;

    protected $casts = [
        'value_json' => 'array',
    ];

    public static function get($key, $default = null)
    {
        $row = static::where('key', $key)->first();
        return $row ? $row->value : $default;
    }

    public static function set($key, $value)
    {
        return static::updateOrCreate(['key' => $key], ['value' => $value]);
    }

    /**
     * Parse grading textarea into structured rules.
     * Expected lines: "min-max:Grade" or "min-max: Grade"
     * Returns array of ['min'=>int,'max'=>int,'grade'=>string]
     */
    public static function getGradingRules()
    {
        // Prefer structured JSON if available
        $row = static::where('key', 'grading')->first();
        if ($row) {
            if (!empty($row->value_json) && is_array($row->value_json)) {
                // ensure sorted by min desc
                $rules = $row->value_json;
                usort($rules, function ($a, $b) { return $b['min'] <=> $a['min']; });
                return $rules;
            }
            // fall back to old text format
            $raw = $row->value ?? '';
        } else {
            $raw = '';
        }

        $lines = preg_split('/\r?\n/', trim($raw));
        $rules = [];
        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '') continue;
            // split by ':'
            if (!str_contains($line, ':')) continue;
            [$range, $grade] = array_map('trim', explode(':', $line, 2));
            if (!str_contains($range, '-')) continue;
            [$min, $max] = array_map('trim', explode('-', $range, 2));
            $min = is_numeric($min) ? (int)$min : null;
            $max = is_numeric($max) ? (int)$max : null;
            if ($min === null || $max === null) continue;
            // normalize so min <= max even if user typed 100-70
            $low = min($min, $max);
            $high = max($min, $max);
            $rules[] = ['min' => $low, 'max' => $high, 'grade' => $grade];
        }
        // sort by min desc so higher grades are checked first
        usort($rules, function($a, $b) { return $b['min'] <=> $a['min']; });
        return $rules;
    }

    /**
     * Compute grade from score using parsed grading rules.
     * Falls back to default scale if no rules defined.
     */
    public static function computeGradeFromScore($score)
    {
        $rules = static::getGradingRules();
        if (!empty($rules)) {
            foreach ($rules as $r) {
                if ($score >= $r['min'] && $score <= $r['max']) {
                    return $r['grade'];
                }
            }
        }

        // fallback default
        if ($score >= 70) return 'A';
        if ($score >= 60) return 'B';
        if ($score >= 50) return 'C';
        if ($score >= 45) return 'D';
        if ($score >= 40) return 'E';
        return 'F';
    }

    /**
     * Parse remarks textarea or structured JSON into rules.
     * Returns array of ['min'=>int,'max'=>int,'remark'=>string]
     */
    public static function getRemarksRules()
    {
        $row = static::where('key', 'remarks')->first();
        if ($row) {
            if (!empty($row->value_json) && is_array($row->value_json)) {
                $rules = $row->value_json;
                usort($rules, function ($a, $b) { return $b['min'] <=> $a['min']; });
                return $rules;
            }
            $raw = $row->value ?? '';
        } else {
            $raw = '';
        }

        $lines = preg_split('/\r?\n/', trim($raw));
        $rules = [];
        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '') continue;
            if (str_contains($line, ':')) {
                [$range, $remark] = array_map('trim', explode(':', $line, 2));
            } else {
                $parts = preg_split('/\s+/', $line, 2);
                if (count($parts) < 2) continue;
                [$range, $remark] = [$parts[0], trim($parts[1])];
            }
            if (!str_contains($range, '-')) continue;
            [$min, $max] = array_map('trim', explode('-', $range, 2));
            $min = is_numeric($min) ? (int)$min : null;
            $max = is_numeric($max) ? (int)$max : null;
            if ($min === null || $max === null) continue;
            // normalize order
            $low = min($min, $max);
            $high = max($min, $max);
            $rules[] = ['min' => $low, 'max' => $high, 'remark' => $remark];
        }
        usort($rules, function ($a, $b) { return $b['min'] <=> $a['min']; });
        return $rules;
    }

    /**
     * Compute remark from score using parsed remarks rules.
     */
    public static function computeRemarkFromScore($score)
    {
        $rules = static::getRemarksRules();
        foreach ($rules as $r) {
            if ($score >= $r['min'] && $score <= $r['max']) return $r['remark'];
        }
        return '';
    }

    /**
     * Parse class teacher comment rules (structured or text) into array of ['min','max','comment']
     */
    public static function getClassTeacherCommentRules()
    {
        $row = static::where('key', 'class_teacher_comments')->first();
        if ($row) {
            if (!empty($row->value_json) && is_array($row->value_json)) {
                $rules = $row->value_json;
                usort($rules, function ($a, $b) { return $b['min'] <=> $a['min']; });
                return $rules;
            }
            $raw = $row->value ?? '';
        } else {
            $raw = '';
        }

        $lines = preg_split('/\r?\n/', trim($raw));
        $rules = [];
        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '') continue;
            if (!str_contains($line, ':')) continue;
            [$range, $comment] = array_map('trim', explode(':', $line, 2));
            if (!str_contains($range, '-')) continue;
            [$min, $max] = array_map('trim', explode('-', $range, 2));
            $min = is_numeric($min) ? (int)$min : null;
            $max = is_numeric($max) ? (int)$max : null;
            if ($min === null || $max === null) continue;
            $low = min($min, $max);
            $high = max($min, $max);
            $rules[] = ['min' => $low, 'max' => $high, 'comment' => $comment];
        }
        usort($rules, function ($a, $b) { return $b['min'] <=> $a['min']; });
        return $rules;
    }

    /**
     * Compute class teacher comment for a score
     */
    public static function computeClassTeacherCommentFromScore($score)
    {
        $rules = static::getClassTeacherCommentRules();
        foreach ($rules as $r) {
            if ($score >= $r['min'] && $score <= $r['max']) return $r['comment'];
        }
        return '';
    }

    /**
     * Parse principal comment rules (structured or text) into array of ['min','max','comment']
     */
    public static function getPrincipalCommentRules()
    {
        $row = static::where('key', 'principal_comment')->first();
        if ($row) {
            if (!empty($row->value_json) && is_array($row->value_json)) {
                $rules = $row->value_json;
                usort($rules, function ($a, $b) { return $b['min'] <=> $a['min']; });
                return $rules;
            }
            $raw = $row->value ?? '';
        } else {
            $raw = '';
        }

        $lines = preg_split('/\r?\n/', trim($raw));
        $rules = [];
        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '') continue;
            if (!str_contains($line, ':')) continue;
            [$range, $comment] = array_map('trim', explode(':', $line, 2));
            if (!str_contains($range, '-')) continue;
            [$min, $max] = array_map('trim', explode('-', $range, 2));
            $min = is_numeric($min) ? (int)$min : null;
            $max = is_numeric($max) ? (int)$max : null;
            if ($min === null || $max === null) continue;
            $low = min($min, $max);
            $high = max($min, $max);
            $rules[] = ['min' => $low, 'max' => $high, 'comment' => $comment];
        }
        usort($rules, function ($a, $b) { return $b['min'] <=> $a['min']; });
        return $rules;
    }

    /**
     * Compute principal comment for a score
     */
    public static function computePrincipalCommentFromScore($score)
    {
        $rules = static::getPrincipalCommentRules();
        foreach ($rules as $r) {
            if ($score >= $r['min'] && $score <= $r['max']) return $r['comment'];
        }
        return '';
    }

    /**
     * Parse promotion status rules (structured or text) into array of ['min','max','status']
     */
    public static function getPromotionStatusRules()
    {
        $row = static::where('key', 'promotion_status_rules')->first();
        if ($row) {
            if (!empty($row->value_json) && is_array($row->value_json)) {
                $rules = $row->value_json;
                usort($rules, function ($a, $b) { return $b['min'] <=> $a['min']; });
                return $rules;
            }
            $raw = $row->value ?? '';
        } else {
            $raw = '';
        }

        $lines = preg_split('/\r?\n/', trim($raw));
        $rules = [];
        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '') continue;
            if (!str_contains($line, ':')) continue;
            [$range, $status] = array_map('trim', explode(':', $line, 2));
            if (!str_contains($range, '-')) continue;
            [$min, $max] = array_map('trim', explode('-', $range, 2));
            $min = is_numeric($min) ? (int)$min : null;
            $max = is_numeric($max) ? (int)$max : null;
            if ($min === null || $max === null) continue;
            $low = min($min, $max);
            $high = max($min, $max);
            $rules[] = ['min' => $low, 'max' => $high, 'status' => $status];
        }
        usort($rules, function ($a, $b) { return $b['min'] <=> $a['min']; });
        return $rules;
    }

    /**
     * Compute promotion status for a score
     */
    public static function computePromotionStatusFromScore($score)
    {
        $rules = static::getPromotionStatusRules();
        foreach ($rules as $r) {
            if ($score >= $r['min'] && $score <= $r['max']) return $r['status'];
        }
        // Fallback to default
        return static::get('promotion_status', 'PROMOTED TO NEXT CLASS');
    }

}

