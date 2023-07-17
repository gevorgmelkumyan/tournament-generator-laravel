<?php

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::post('create_tournament', [Controller::class, 'createTournament']);
Route::post('run_division_games/{tournament}', [Controller::class, 'runDivisionGames']);
Route::post('run_division_games/{tournament}', [Controller::class, 'runDivisionGames']);
Route::post('run_semi_finals/{tournament}', [Controller::class, 'runSemiFinals']);
