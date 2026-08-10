<?php

use App\Http\Controllers\Api\ApplicationController;
use App\Http\Controllers\Api\CountryController;
use App\Http\Controllers\Api\CourseController;
use App\Http\Controllers\Api\EventController;
use App\Http\Controllers\Api\HomeController;
use App\Http\Controllers\Api\LeadController;
use App\Http\Controllers\Api\PageContentController;
use App\Http\Controllers\Api\PostCategoryController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\ProgramController;
use App\Http\Controllers\Api\ScholarshipController;
use App\Http\Controllers\Api\ServiceController;
use App\Http\Controllers\Api\SitemapController;
use App\Http\Controllers\Api\StudentPortalController;
use App\Http\Controllers\Api\UniversityController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\SocialAuthController;
use Illuminate\Support\Facades\Route;

Route::get('/home', HomeController::class);
Route::get('/sitemap', SitemapController::class);
Route::get('/pages/{key}', [PageContentController::class, 'show']);

Route::get('/countries', [CountryController::class, 'index']);
Route::get('/countries/{slug}', [CountryController::class, 'show']);

Route::get('/universities', [UniversityController::class, 'index']);
Route::get('/universities/{slug}', [UniversityController::class, 'show']);

Route::get('/courses', [CourseController::class, 'index']);
Route::get('/courses/{slug}', [CourseController::class, 'show']);

Route::get('/scholarships', [ScholarshipController::class, 'index']);
Route::get('/scholarships/{slug}', [ScholarshipController::class, 'show']);

Route::get('/services', [ServiceController::class, 'index']);
Route::get('/services/{slug}', [ServiceController::class, 'show']);

Route::get('/posts', [PostController::class, 'index']);
Route::get('/posts/{slug}', [PostController::class, 'show']);
Route::get('/post-categories', [PostCategoryController::class, 'index']);

Route::get('/events', [EventController::class, 'index']);
Route::get('/events/{slug}', [EventController::class, 'show']);

Route::get('/programs', [ProgramController::class, 'index']);
Route::get('/programs/{slug}', [ProgramController::class, 'show']);

Route::post('/leads', [LeadController::class, 'store']);
Route::post('/applications', [ApplicationController::class, 'store']);

Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/{provider}/redirect', [SocialAuthController::class, 'redirect']);
    Route::get('/{provider}/callback', [SocialAuthController::class, 'callback']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/me', [AuthController::class, 'me']);
        Route::post('/logout', [AuthController::class, 'logout']);
    });
});

Route::middleware('auth:sanctum')->prefix('student')->group(function () {
    Route::get('/dashboard', [StudentPortalController::class, 'dashboard']);
    Route::get('/applications', [StudentPortalController::class, 'applications']);
    Route::get('/documents', [StudentPortalController::class, 'documents']);
    Route::post('/documents', [StudentPortalController::class, 'uploadDocument']);
    Route::get('/appointments', [StudentPortalController::class, 'appointments']);
    Route::get('/notifications', [StudentPortalController::class, 'notifications']);
    Route::patch('/notifications/read-all', [StudentPortalController::class, 'markAllNotificationsRead']);
    Route::patch('/notifications/{notification}/read', [StudentPortalController::class, 'markNotificationRead']);
    Route::patch('/profile', [StudentPortalController::class, 'updateProfile']);
});
