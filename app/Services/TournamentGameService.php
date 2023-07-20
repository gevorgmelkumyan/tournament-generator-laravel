<?php

namespace App\Services;

use App\Interfaces\GameServiceInterface;
use App\Interfaces\TeamGameServiceInterface;
use App\Interfaces\TournamentGameServiceInterface;
use App\Models\TeamGame;
use App\Models\Tournament;

class TournamentGameService implements TournamentGameServiceInterface {

    function __construct(
        protected GameServiceInterface $gameService,
        protected TeamGameServiceInterface $teamGameService
    ) {}

    function generateDivisionGames(int $tournamentId, string $division = 'A'): array {

        /** @var Tournament $tournament */
        $tournament = Tournament::query()->findOrFail($tournamentId);

        $teams = $tournament
            ->teams()
            ->where('division', $division)
            ->get();
        $teamCount = count($teams);

        $teamGames = [];

        for ($i = 0; $i < $teamCount; ++$i) {
            for ($j = $i + 1; $j < $teamCount; ++$j) {
                $game = $this->gameService->generateDivisionGame($tournamentId);

                list($teamGameA, $teamGameB) = $this->teamGameService->generateTeamGame($game->id, $teams[$i]->id, $teams[$j]->id);

                $teamGames[] = $teamGameA;
                $teamGames[] = $teamGameB;
            }
        }

        TeamGame::query()->insert($teamGames);

        return $teamGames;
    }
}
