<?php

namespace App\Services;

use App\Interfaces\TeamServiceInterface;
use Illuminate\Support\Str;

class TeamService implements TeamServiceInterface {

    function generateTeamForTournament(int $tournamentId, string $division = 'A'): array {
        return [
            'tournament_id' => $tournamentId,
            'division' => $division,
            'name' => strtoupper(substr(Str::random(20), rand(1, 10), 3)),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
