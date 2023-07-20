<?php

namespace App\Interfaces;

use App\Models\Tournament;

interface TournamentGameServiceInterface {

    function __construct(GameServiceInterface $gameService, TeamGameServiceInterface $teamGameService);

    function generateDivisionGames(Tournament $tournament, string $division = 'A'): array;

    function generatePlayoffGames(Tournament $tournament): array;

    function generateSemiFinalGames(Tournament $tournament): array;

    function generateFinalGames(Tournament $tournament): array;
}
