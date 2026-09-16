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
        | Sprint
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
        | Issue Export
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/issues/export',
            [ScrumController::class, 'exportIssues']
        )->name('issues.export');

        /*
        |--------------------------------------------------------------------------
        | Issue Bulk Delete
        |--------------------------------------------------------------------------
        */

        Route::delete(
            '/issues/bulk-delete',
            [ScrumController::class, 'bulkDeleteIssues']
        )->name('issues.bulk-delete');

        /*
        |--------------------------------------------------------------------------
        | Issues
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
    });