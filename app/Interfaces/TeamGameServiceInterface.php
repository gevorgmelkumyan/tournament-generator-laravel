<?php

namespace App\Interfaces;

interface TeamGameServiceInterface {

    function __construct(ScoreServiceInterface $scoreService);

    function generateTeamGame(int $gameId, int $teamAId, int $teamBId): array;
}
