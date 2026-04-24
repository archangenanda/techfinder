<?php
use App\Http\Controllers\web\UserController;
use App\Http\Controllers\web\CompetenceController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('template');
});
Route::get('/web/Competence',[CompetenceController::class, 'index']);

//formulaire pour lajout de competence avec le bouton modifier et supprimer
Route::resource('web/competences', CompetenceController::class)->names('competences');
Route::resource('web/users', UserController::class)->names('web.users');
