<?php

namespace App\Interfaces;

interface TeamServiceInterface {
    function generateTeamForTournament(int $tournamentId, string $division = 'A'): array;
}
