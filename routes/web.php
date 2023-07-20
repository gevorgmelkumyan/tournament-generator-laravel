<?php

use App\Http\Controllers\Controller;
use App\Http\Controllers\TournamentController;
use Illuminate\Support\Facades\Route;

Route::apiResource('tournaments', TournamentController::class)->only('store', 'destroy');
Route::post('run_division_games/{tournament}', [Controller::class, 'runDivisionGames']);
Route::post('run_playoffs/{tournament}', [Controller::class, 'runPlayoffs']);
Route::post('run_semi_finals/{tournament}', [Controller::class, 'runSemiFinals']);
Route::post('run_finals/{tournament}', [Controller::class, 'runFinals']);
