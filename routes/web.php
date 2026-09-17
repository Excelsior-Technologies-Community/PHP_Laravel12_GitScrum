<?php

use App\Http\Controllers\ScrumController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('scrum.dashboard');
});

Route::prefix('scrum')
    ->name('scrum.')
    ->group(function () {

        /*
        |--------------------------------------------------------------------------
        | Dashboard
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/dashboard',
            [ScrumController::class, 'dashboard']
        )->name('dashboard');

        /*
        |--------------------------------------------------------------------------
        | Kanban Board
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/kanban',
            [ScrumController::class, 'kanban']
        )->name('kanban');

        Route::post(
            '/kanban/move',
            [ScrumController::class, 'moveIssue']
        )->name('kanban.move');

        /*
        |--------------------------------------------------------------------------
        | Analytics & Sprint Burndown
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/analytics',
            [ScrumController::class, 'analytics']
        )->name('analytics');

        Route::get(
            '/analytics/burndown-data',
            [ScrumController::class, 'burndownData']
        )->name('analytics.burndown-data');

        /*
        |--------------------------------------------------------------------------
        | Sprints
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/sprints/create',
            [ScrumController::class, 'createSprint']
        )->name('sprints.create');

        Route::post(
            '/sprints',
            [ScrumController::class, 'storeSprint']
        )->name('sprints.store');

        /*
        |--------------------------------------------------------------------------
        | Issue Export & Bulk Delete
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/issues/export',
            [ScrumController::class, 'exportIssues']
        )->name('issues.export');

        Route::delete(
            '/issues/bulk-delete',
            [ScrumController::class, 'bulkDeleteIssues']
        )->name('issues.bulk-delete');

        /*
        |--------------------------------------------------------------------------
        | Issues CRUD
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/issues',
            [ScrumController::class, 'issues']
        )->name('issues');

        Route::get(
            '/issues/create',
            [ScrumController::class, 'createIssue']
        )->name('issues.create');

        Route::post(
            '/issues',
            [ScrumController::class, 'storeIssue']
        )->name('issues.store');

        Route::get(
            '/issues/{issue}',
            [ScrumController::class, 'showIssue']
        )->name('issues.show');

        Route::get(
            '/issues/{issue}/edit',
            [ScrumController::class, 'editIssue']
        )->name('issues.edit');

        Route::put(
            '/issues/{issue}',
            [ScrumController::class, 'updateIssue']
        )->name('issues.update');

        Route::delete(
            '/issues/{issue}',
            [ScrumController::class, 'deleteIssue']
        )->name('issues.destroy');

        /*
        |--------------------------------------------------------------------------
        | Work Logs (Time Tracking)
        |--------------------------------------------------------------------------
        */

        Route::post(
            '/issues/{issue}/work-logs',
            [ScrumController::class, 'storeWorkLog']
        )->name('issues.work-logs.store');

        Route::delete(
            '/work-logs/{workLog}',
            [ScrumController::class, 'deleteWorkLog']
        )->name('work-logs.destroy');

        /*
        |--------------------------------------------------------------------------
        | Comments & Discussions
        |--------------------------------------------------------------------------
        */

        Route::post(
            '/issues/{issue}/comments',
            [ScrumController::class, 'storeComment']
        )->name('issues.comments.store');

        Route::delete(
            '/comments/{comment}',
            [ScrumController::class, 'deleteComment']
        )->name('comments.destroy');

        /*
        |--------------------------------------------------------------------------
        | Attachments
        |--------------------------------------------------------------------------
        */

        Route::post(
            '/issues/{issue}/attachments',
            [ScrumController::class, 'storeAttachment']
        )->name('issues.attachments.store');

        Route::get(
            '/attachments/{attachment}/download',
            [ScrumController::class, 'downloadAttachment']
        )->name('attachments.download');

        Route::delete(
            '/attachments/{attachment}',
            [ScrumController::class, 'deleteAttachment']
        )->name('attachments.destroy');
    });
