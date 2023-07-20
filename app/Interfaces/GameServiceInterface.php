<?php

namespace App\Interfaces;

use App\Models\Game;

interface GameServiceInterface {

    function __construct(TeamGameServiceInterface $teamGameService);

    function generateDivisionGame(int $tournamentId): Game;

    function generatePlayoffGame(int $tournamentId): Game;

    function generateSemiFinalGame(int $tournamentId): Game;

    function generateFinalGame(int $tournamentId): Game;
}
