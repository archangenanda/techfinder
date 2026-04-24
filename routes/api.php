<?php
use App\Http\Controllers\CompetenceController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InterventionController;
use App\Http\Controllers\UserCompetenceController;
use App\Http\Controllers\UtilisateurController;
use App\Http\Controllers\AuthController;

// Authentication routes - Public
Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);

// Authentication routes - Protected
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/auth/me', [AuthController::class, 'me']);
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::post('/auth/refresh', [AuthController::class, 'refresh']);
});

// Competence routes
Route::get('/competences/search', [CompetenceController::class, 'search']);
Route::apiResource('competences', CompetenceController::class)->names('api.competences');

// Intervention routes
Route::get('/interventions/search', [InterventionController::class, 'search']);
Route::apiResource('interventions', InterventionController::class);

// User Competence routes
Route::get('user-competences/show', [UserCompetenceController::class, 'show']);
Route::put('user-competences/update', [UserCompetenceController::class, 'update']);
Route::delete('user-competences/delete', [UserCompetenceController::class, 'destroy']);
Route::apiResource('user-competences', UserCompetenceController::class);

// Utilisateur routes - search must come before show parameter
Route::get('utilisateurs/search', [UtilisateurController::class, 'search']);
Route::apiResource('utilisateurs', UtilisateurController::class);

