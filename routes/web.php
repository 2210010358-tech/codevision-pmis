<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\MilestoneController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\BugController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\WorkloadController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\NotificationController;

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Authenticated Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [DashboardController::class, 'index']);

    // Projects
    Route::resource('projects', ProjectController::class);

    // Milestones
    Route::post('/projects/{project}/milestones', [MilestoneController::class, 'store'])->name('projects.milestones.store');
    Route::put('/milestones/{milestone}', [MilestoneController::class, 'update'])->name('milestones.update');
    Route::delete('/milestones/{milestone}', [MilestoneController::class, 'destroy'])->name('milestones.destroy');

    // Tasks
    Route::post('/milestones/{milestone}/tasks', [TaskController::class, 'store'])->name('milestones.tasks.store');
    Route::get('/tasks/{task}', [TaskController::class, 'show'])->name('tasks.show');
    Route::put('/tasks/{task}', [TaskController::class, 'update'])->name('tasks.update');
    Route::delete('/tasks/{task}', [TaskController::class, 'destroy'])->name('tasks.destroy');
    Route::post('/tasks/{task}/status', [TaskController::class, 'updateStatus'])->name('tasks.status.update');
    Route::post('/tasks/{task}/progress', [TaskController::class, 'updateProgress'])->name('tasks.progress.update');
    Route::post('/tasks/{task}/hours', [TaskController::class, 'logHours'])->name('tasks.hours.log');
    Route::post('/tasks/{task}/checklist', [TaskController::class, 'storeChecklist'])->name('tasks.checklist.store');
    Route::post('/checklist-items/{item}/toggle', [TaskController::class, 'toggleChecklist'])->name('checklist.toggle');
    Route::post('/tasks/{task}/comments', [TaskController::class, 'storeComment'])->name('tasks.comments.store');

    // Bugs
    Route::get('/bugs', [BugController::class, 'index'])->name('bugs.index');
    Route::post('/projects/{project}/bugs', [BugController::class, 'store'])->name('projects.bugs.store');
    Route::put('/bugs/{bug}', [BugController::class, 'update'])->name('bugs.update');
    Route::post('/bugs/{bug}/status', [BugController::class, 'updateStatus'])->name('bugs.status.update');

    // Documents
    Route::post('/projects/{project}/documents', [DocumentController::class, 'store'])->name('projects.documents.store');
    Route::get('/documents/{document}/download', [DocumentController::class, 'download'])->name('documents.download');
    Route::delete('/documents/{document}', [DocumentController::class, 'destroy'])->name('documents.destroy');

    // Workload
    Route::get('/workload', [WorkloadController::class, 'index'])->name('workload.index');

    // Reports
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::post('/reports/export', [ReportController::class, 'export'])->name('reports.export');

    // Notifications
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllRead'])->name('notifications.readAll');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markRead'])->name('notifications.read');
});
