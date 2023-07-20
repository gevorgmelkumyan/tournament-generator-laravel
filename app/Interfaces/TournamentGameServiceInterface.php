<?php

namespace App\Interfaces;

interface TournamentGameServiceInterface {

    function __construct(GameServiceInterface $gameService, TeamGameServiceInterface $teamGameService);

    function generateDivisionGames(int $tournamentId, string $division = 'A'): array;
}
