<?php

namespace App\Providers;

use App\Interfaces\GameServiceInterface;
use App\Interfaces\ScoreServiceInterface;
use App\Interfaces\TeamGameServiceInterface;
use App\Interfaces\TeamServiceInterface;
use App\Interfaces\TournamentGameServiceInterface;
use App\Interfaces\TournamentServiceInterface;
use App\Services\GameService;
use App\Services\ScoreService;
use App\Services\TeamGameService;
use App\Services\TeamService;
use App\Services\TournamentGameService;
use App\Services\TournamentService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider {

    public function register(): void {
        $this->app->bind(TournamentServiceInterface::class, TournamentService::class);
        $this->app->bind(GameServiceInterface::class, GameService::class);
        $this->app->bind(ScoreServiceInterface::class, ScoreService::class);
        $this->app->bind(TournamentGameServiceInterface::class, TournamentGameService::class);
        $this->app->bind(TeamServiceInterface::class, TeamService::class);
        $this->app->bind(TeamGameServiceInterface::class, TeamGameService::class);
    }

    public function boot(): void {
        //
    }
}
