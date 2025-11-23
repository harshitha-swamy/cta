<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\PreviewController;

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.post');
Route::get('/logout', [LoginController::class, 'logout'])->name('logout');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
});

Route::get('/catalogue', [TicketController::class, 'catalogue'])->name('catalogue');
Route::post('/select-eshop', [DashboardController::class, 'selectEshop'])->name('eshop.select');



Route::get('/add-task', [DashboardController::class, 'create'])->name('task.create');
Route::post('/add-task', [DashboardController::class, 'store'])->name('task.store');


Route::get('/get-website-link', [TicketController::class, 'getWebsiteLink'])->name('get.website.link');


Route::post('approver/review', [PreviewController::class, 'approver_review'])->name('approver.review');

Route::post('developer/edit', [PreviewController::class, 'developer_edit'])->name('developer.edit');

Route::post('/task/approve', [PreviewController::class, 'approveTask'])->name('task.approve');

Route::post('/task/send-for-changes/{id}', [PreviewController::class, 'sendForChanges']);

// Route::post('/task/send-for-changes/{id}', [TaskController::class, 'sendForChanges']);

Route::post('/upload_custom_part_image', 'Api\AccessoriesController@upload_custom_part_image');

Route::post('/upload-svg-single', [TicketController::class, 'uploadSvgSingle']);

Route::post('/upload-svg-temp', [TicketController::class, 'uploadSvgTemp']);





