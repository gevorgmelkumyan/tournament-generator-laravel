<?php

namespace App\Services;

use App\Interfaces\ScoreServiceInterface;

class ScoreService implements ScoreServiceInterface {

    function generateScores(): array {
        $score1 = rand(0, 5);

        do {
            $score2 = rand(0, 5);
        } while ($score1 == $score2);

        return [
            $score1,
            $score2,
        ];
    }
}
