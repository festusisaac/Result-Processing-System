<?php

namespace App\Enums;

class Permission
{
    // User Management
    const MANAGE_USERS = 'manage_users';
    
    // Academic Management
    const MANAGE_SESSIONS = 'manage_sessions';
    const MANAGE_CLASSES = 'manage_classes';
    const MANAGE_SUBJECTS = 'manage_subjects';
    const MANAGE_TEACHERS = 'manage_teachers';
    const MANAGE_TERMS = 'manage_terms';
    
    // Student Management
    const MANAGE_STUDENTS = 'manage_students'; // Full CRUD (admin only)
    const VIEW_STUDENTS = 'view_students';
    const EDIT_STUDENTS = 'edit_students'; // Edit only (teachers)
    
    // Assessment
    const ENTER_SCORES = 'enter_scores';
    const VIEW_SCORES = 'view_scores';
    const ENTER_ATTENDANCE = 'enter_attendance';
    const ENTER_SKILLS = 'enter_skills';
    const ENTER_COMMENTS = 'enter_comments';
    
    // Reports
    const VIEW_REPORTS = 'view_reports';
    const MANAGE_REPORT_SETTINGS = 'manage_report_settings';
    const PUBLISH_RESULTS = 'publish_results';
    
    // Scratch Cards
    const MANAGE_SCRATCH_CARDS = 'manage_scratch_cards';
    
    // System
    const MANAGE_SETTINGS = 'manage_settings';
    const MANAGE_BLOG = 'manage_blog';

    /**
     * Get all permissions
     */
    public static function all(): array
    {
        return [
            self::MANAGE_USERS,
            self::MANAGE_SESSIONS,
            self::MANAGE_CLASSES,
            self::MANAGE_SUBJECTS,
            self::MANAGE_TEACHERS,
            self::MANAGE_TERMS,
            self::MANAGE_STUDENTS,
            self::VIEW_STUDENTS,
            self::EDIT_STUDENTS,
            self::ENTER_SCORES,
            self::VIEW_SCORES,
            self::ENTER_ATTENDANCE,
            self::ENTER_SKILLS,
            self::ENTER_COMMENTS,
            self::VIEW_REPORTS,
            self::MANAGE_REPORT_SETTINGS,
            self::PUBLISH_RESULTS,
            self::MANAGE_SCRATCH_CARDS,
            self::MANAGE_SETTINGS,
            self::MANAGE_BLOG,
        ];
    }

    /**
     * Get admin permissions (all)
     */
    public static function adminPermissions(): array
    {
        return self::all();
    }

    /**
     * Get teacher permissions
     */
    public static function teacherPermissions(): array
    {
        return [
            self::VIEW_STUDENTS,
            self::EDIT_STUDENTS, // Can edit but not create/delete
            self::ENTER_SCORES,
            self::VIEW_SCORES,
            self::ENTER_ATTENDANCE,
            self::ENTER_SKILLS,
            self::ENTER_COMMENTS,
            self::VIEW_REPORTS,
        ];
    }

    /**
     * Get accountant permissions
     */
    public static function accountantPermissions(): array
    {
        return [
            self::MANAGE_SCRATCH_CARDS,
            self::VIEW_STUDENTS,
            self::VIEW_REPORTS,
        ];
    }
}
