<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AcademicSessionController;
use App\Http\Controllers\ClassRoomController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\SubjectGroupController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\ScoreController;
use App\Http\Controllers\ScratchCardController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\TermController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\SkillsAttributeController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\ResultCheckerController;
use App\Http\Controllers\ReportPdfController;
use App\Http\Controllers\ResultPublishController;
use App\Http\Controllers\ResultManagementController;
use App\Http\Controllers\ReportSettingController;
use App\Http\Controllers\ScratchCardBatchController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Public Routes
Route::get('/', [PublicController::class, 'index'])->name('home');
Route::get('/contact', [PublicController::class, 'contact'])->name('public.contact');
Route::post('/contact', [PublicController::class, 'sendContactMessage'])->name('public.contact.send');
Route::get('/check-result-landing', [PublicController::class, 'checkResult'])->name('public.check-result');
Route::get('/news', [PublicController::class, 'blog'])->name('public.blog');
Route::get('/news/{slug}', [PublicController::class, 'blogShow'])->name('public.blog.show');

// Result Checker (Public access allowed for parents/students with scratch cards)
Route::get('/check-result', [ResultCheckerController::class, 'index'])->name('result.check');
Route::post('/check-result', [ResultCheckerController::class, 'check'])->name('result.check.submit');

// Student report preview and PDF export (Public access allowed if session key exists)
Route::get('/reports/students/{student}/preview', [ReportPdfController::class, 'preview'])->name('reports.students.preview');
Route::get('/reports/students/{student}/pdf', [ReportPdfController::class, 'pdf'])->name('student.report.pdf');


// Guest routes (Login)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

// Protected routes
// Require authentication and an active academic session for most routes.
Route::middleware(['auth', 'active.session'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Academic Session Management
    Route::resource('sessions', AcademicSessionController::class);
    Route::post('/sessions/{session}/activate', [AcademicSessionController::class, 'activate'])->name('sessions.activate');
    
    // Class & Subject Management
    Route::resource('classes', ClassRoomController::class);
    
    // Subject routes - specific routes before resource route
    Route::get('/subjects/search', [SubjectController::class, 'search'])->name('subjects.search');
    Route::get('/subjects/export', [SubjectController::class, 'export'])->name('subjects.export');
    Route::post('/subjects/import', [SubjectController::class, 'import'])->name('subjects.import');
    Route::resource('subjects', SubjectController::class);
    Route::resource('subject-groups', SubjectGroupController::class);
    
    // Teacher Management
    Route::resource('teachers', TeacherController::class);
    
    // Term settings (manage FIRST/SECOND/THIRD)
    Route::get('/terms', [TermController::class, 'index'])->name('terms.index');
    Route::post('/terms', [TermController::class, 'store'])->name('terms.store');
    Route::get('/terms/fetch', [TermController::class, 'fetch'])->name('terms.fetch');
    
    // Student management
    Route::get('/students/autocomplete', [StudentController::class, 'autocomplete'])->name('students.autocomplete');
    Route::post('/students/{student}/promote', [StudentController::class, 'promote'])->name('students.promote');
    Route::post('/students/bulk-promote', [StudentController::class, 'bulkPromote'])->name('students.bulk-promote');
    Route::resource('students', StudentController::class);
    
    // Score management (specific routes first)
    // Scoresheet UI
    Route::get('/scoresheet', [ScoreController::class, 'scoresheet'])->name('scores.scoresheet');
    // Broadsheet UI
    Route::get('/broadsheet', [ScoreController::class, 'broadsheet'])->name('scores.broadsheet');
    Route::get('/broadsheet/data', [ScoreController::class, 'broadsheetData'])->name('scores.broadsheet.data');
    Route::get('/broadsheet/export', [ScoreController::class, 'broadsheetExport'])->name('scores.broadsheet.export');
    // Result UI
    Route::get('/results', [ScoreController::class, 'results'])->name('results.index');
    Route::get('/results/data', [ScoreController::class, 'resultsData'])->name('results.data');
    // Web endpoint for fetching students with scores (used by the Blade UI)
    Route::get('/scores/students', [ScoreController::class, 'getStudentsWithScores'])->name('scores.students');
    // Bulk store route used by the scoresheet form (named so blade route() resolves)
    Route::post('/scores/store-bulk', [ScoreController::class, 'storeBulk'])->name('scores.store-bulk');

    Route::resource('scores', ScoreController::class);
    Route::get('/results/{student}', [ScoreController::class, 'showResults'])->name('results.show');
    Route::get('/results/{student}/print', [ScoreController::class, 'printResults'])->name('results.print');

    // Attendance management (specific routes first)
    Route::get('/attendance', [AttendanceController::class, 'attendance'])->name('attendance.index');
    Route::get('/attendance/students', [AttendanceController::class, 'getStudentsWithAttendance'])->name('attendance.students');
    Route::post('/attendance/store-bulk', [AttendanceController::class, 'storeBulk'])->name('attendance.store-bulk');
    
    // Skills & Attributes management (specific routes first)
    Route::get('/skills', [SkillsAttributeController::class, 'skills'])->name('skills.index');
    Route::get('/skills/students', [SkillsAttributeController::class, 'getStudentsWithSkills'])->name('skills.students');
    Route::post('/skills/store-bulk', [SkillsAttributeController::class, 'storeBulk'])->name('skills.store-bulk');
    
    // Psychomotor Skills management
    Route::get('/psychomotor-skills', [SkillsAttributeController::class, 'psychomotorSkills'])->name('psychomotor-skills.index');
    Route::get('/psychomotor-skills/students', [SkillsAttributeController::class, 'getStudentsWithPsychomotorSkills'])->name('psychomotor-skills.students');
    Route::post('/psychomotor-skills/store-bulk', [SkillsAttributeController::class, 'storePsychomotorSkillsBulk'])->name('psychomotor-skills.store-bulk');

    
    // Result publish/unpublish (admin/teacher)
    Route::post('/results/calculate-summaries', [App\Http\Controllers\ScoreController::class, 'calculateSummaries'])->name('results.calculate_summaries');
    Route::post('/results/terms/{term}/publish', [ResultPublishController::class, 'toggle'])->name('results.terms.publish');
    Route::get('/results/terms/{term}/published-status', [ResultPublishController::class, 'status'])->name('results.terms.published-status');

    // Result Management (batches)
    Route::get('/reports/result-management', [ResultManagementController::class, 'index'])->name('reports.result_management');
    Route::post('/reports/result-management/{term}/publish', [ResultManagementController::class, 'publish'])->name('reports.result_management.publish');
    Route::post('/reports/result-management/{term}/unpublish', [ResultManagementController::class, 'unpublish'])->name('reports.result_management.unpublish');
    Route::post('/reports/result-management/{term}/status', [ResultManagementController::class, 'setStatus'])->name('reports.result_management.set_status');
    

    // Comments management (specific routes first)
    Route::get('/comments', [CommentController::class, 'index'])->name('comments.index');
    Route::get('/comments/students', [CommentController::class, 'getStudentsWithComments'])->name('comments.students');
    Route::post('/comments/store-bulk', [CommentController::class, 'storeBulk'])->name('comments.store-bulk');
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');
    Route::get('/comments/{comment}', [CommentController::class, 'show'])->name('comments.show');
    
    // Report settings
    Route::get('/report-settings', [ReportSettingController::class, 'index'])->name('report-settings.index');
    Route::post('/report-settings', [ReportSettingController::class, 'store'])->name('report-settings.store');
    
    // Scratch card management
    Route::prefix('scratch-cards')->name('scratch-cards.')->group(function () {
        Route::get('/', [ScratchCardController::class, 'index'])->name('index');
        Route::get('/create', [ScratchCardController::class, 'create'])->name('create');
        Route::post('/', [ScratchCardController::class, 'store'])->name('store');
        Route::get('/generate', [ScratchCardController::class, 'showGenerateForm'])->name('generate');
        Route::post('/generate', [ScratchCardController::class, 'generate'])->name('generate.post');
        Route::get('/print/{batch}', [ScratchCardController::class, 'printBatch'])->name('print');
        Route::post('/redeem', [ScratchCardController::class, 'redeem'])->name('redeem');
        
        // Batch Management
        Route::resource('batches', ScratchCardBatchController::class);
        Route::patch('batches/{batch}/status', [ScratchCardBatchController::class, 'updateStatus'])->name('batches.update-status');
    });

    
    // Settings
    Route::get('/settings', [App\Http\Controllers\SettingController::class, 'index'])->name('settings.index');
    Route::post('/settings', [App\Http\Controllers\SettingController::class, 'update'])->name('settings.update');
    Route::post('/settings/profile', [App\Http\Controllers\SettingController::class, 'updateProfile'])->name('settings.profile.update');
    
    // Blog Management
    Route::resource('blog', App\Http\Controllers\BlogController::class);
});
