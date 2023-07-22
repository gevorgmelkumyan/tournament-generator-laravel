<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

/**
 * @property Collection $teams
 */
class Tournament extends Model {
    use HasFactory;

    public function teams(): HasMany {
        return $this->hasMany(Team::class);
    }

    public function games(): HasMany {
        return $this->hasMany(Game::class);
    }

    public function teamGames(): HasManyThrough {
        return $this->hasManyThrough(TeamGame::class, Game::class);
    }

    public function canRunDivisionGames(): bool {
        return $this->teams()->count();
    }

    public function canRunPlayoffs(): bool {
        return $this->games()->where('type', Game::TYPE_DIVISION)->count();
    }

    public function canRunSemiFinals(): bool {
        return $this->games()->where('type', Game::TYPE_PLAYOFFS)->count();
    }
    public function canRunFinals(): bool {
        return $this->games()->where('type', Game::TYPE_SEMI_FINALS)->count();
    }

    public function getDivisionGameWinners(string $division = 'A'): Collection|array {
        return TeamGame::query()
            ->selectRaw('team_id, sum(score) total')
            ->join('games', 'game_id', '=', 'games.id')
            ->join('teams', 'team_id', '=', 'teams.id')
            ->where('type', Game::TYPE_DIVISION)
            ->where('games.tournament_id', $this->id)
            ->where('teams.division', $division)
            ->groupBy('team_games.team_id')
            ->orderByDesc('total')
            ->limit(4)
            ->get();
    }

    public function getPlayoffWinners(): Collection|array {
        return TeamGame::query()
            ->select('game_id', 'team_id', 'score')
            ->join('games', 'team_games.game_id', '=', 'games.id')
            ->where('games.tournament_id', $this->id)
            ->where('games.type', Game::TYPE_PLAYOFFS)
            ->whereRaw("(game_id, score) in (
                select game_id, max(score)
                from team_games
                group by game_id
            )")
            ->orderByDesc('score')
            ->get();
    }

    public function getSemiFinalTeams(bool $winners = true): Collection|array {
        $aggregation = $winners ? 'max' : 'min';

        return TeamGame::query()
            ->select('game_id', 'team_id', 'score')
            ->join('games', 'team_games.game_id', '=', 'games.id')
            ->where('games.tournament_id', $this->id)
            ->where('games.type', Game::TYPE_SEMI_FINALS)
            ->whereRaw("(game_id, score) in (
                select game_id, $aggregation(score)
                from team_games
                group by game_id
            )")
            ->get();
    }

    public function getFinalWinners(): Collection|array {
        return TeamGame::query()
            ->select('game_id', 'team_id', 'score')
            ->join('games', 'games.id', '=', 'team_games.game_id')
            ->where('games.type', Game::TYPE_FINALS)
            ->where('tournament_id', $this->id)
            ->orderByRaw('game_id, score desc')
            ->get();
    }
}
