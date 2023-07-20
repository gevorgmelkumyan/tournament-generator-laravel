<?php

namespace App\Services;

use App\Interfaces\ScoreServiceInterface;
use App\Interfaces\TeamGameServiceInterface;

class TeamGameService implements TeamGameServiceInterface {

    function __construct(protected ScoreServiceInterface $scoreService) {}

    function generateTeamGame(int $gameId, int $teamAId, int $teamBId): array {

        $data = [
            'game_id' => $gameId,
            'created_at' => now(),
            'updated_at' => now(),
        ];

        list($score1, $score2) = $this->scoreService->generateScores();

        $teamAGame = array_merge($data, [
            'team_id' => $teamAId,
            'score' => $score1,
        ]);

        $teamBGame = array_merge($data, [
            'team_id' => $teamBId,
            'score' => $score2,
        ]);

        return [$teamAGame, $teamBGame];
    }
}
