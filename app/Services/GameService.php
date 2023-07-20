<?php

namespace App\Services;

use App\Interfaces\GameServiceInterface;
use App\Interfaces\TeamGameServiceInterface;
use App\Models\Game;

class GameService implements GameServiceInterface {

    function __construct(TeamGameServiceInterface $teamGameService) {}

    function generateDivisionGame(int $tournamentId): Game {
        return $this->generateGame($tournamentId);
    }

    function generatePlayoffGame(int $tournamentId): Game {
        return $this->generateGame($tournamentId, Game::TYPE_PLAYOFFS);
    }

    function generateSemiFinalGame(int $tournamentId): Game {
        return $this->generateGame($tournamentId, Game::TYPE_SEMI_FINALS);
    }

    function generateFinalGame(int $tournamentId): Game {
        return $this->generateGame($tournamentId, Game::TYPE_FINALS);
    }

    protected function generateGame(int $tournamentId, string $type = Game::TYPE_DIVISION): Game {
        /** @var Game $game */
        $game = Game::query()->create([
            'tournament_id' => $tournamentId,
            'type' => $type,
        ]);

        return $game;
    }
}
