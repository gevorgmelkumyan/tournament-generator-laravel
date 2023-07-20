<?php

namespace App\Services;

use App\Interfaces\TeamServiceInterface;
use App\Interfaces\TournamentServiceInterface;
use App\Models\Team;
use App\Models\Tournament;
use Illuminate\Support\Str;

class TournamentService implements TournamentServiceInterface {

    const NUMBER_OF_TEAMS = 15;

    function __construct(protected TeamServiceInterface $teamService) {}

    function create(): array {
        /** @var Tournament $tournament */
        $tournament = Tournament::query()->create();

        $divisionA = [];
        $divisionB = [];

        for ($i = 0; $i < self::NUMBER_OF_TEAMS; ++$i) {
            $divisionA[] = $this->teamService->generateTeamForTournament($tournament->id);
            $divisionB[] = $this->teamService->generateTeamForTournament($tournament->id, 'B');
        }

        Team::query()->insert(array_merge($divisionA, $divisionB));

        return compact('divisionA', 'divisionB', 'tournament');
    }
}
