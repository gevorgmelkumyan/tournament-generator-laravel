<?php

use App\Http\Controllers\GameController;
use App\Http\Controllers\TournamentController;
use Illuminate\Support\Facades\Route;

Route::apiResource('tournaments', TournamentController::class)->only('store', 'destroy');
Route::post('tournaments/{tournament}/run_division_games', [GameController::class, 'runDivisionGames']);
Route::post('tournaments/{tournament}/run_playoffs', [GameController::class, 'runPlayoffs']);
Route::post('tournaments/{tournament}/run_semi_finals', [GameController::class, 'runSemiFinals']);
Route::post('tournaments/{tournament}/run_finals', [GameController::class, 'runFinals']);
