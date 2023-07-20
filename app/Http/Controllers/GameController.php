<?php

namespace App\Http\Controllers;

use App\Interfaces\TournamentGameServiceInterface;
use App\Models\Game;
use App\Models\Team;
use App\Models\TeamGame;
use App\Models\Tournament;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class GameController extends Controller {

    public function __construct(protected TournamentGameServiceInterface $tournamentGameService) {}

    public function runDivisionGames(Tournament $tournament): JsonResponse {
        DB::beginTransaction();

        try {
            $divisionAData = $this->tournamentGameService->generateDivisionGames($tournament);
            $divisionBData = $this->tournamentGameService->generateDivisionGames($tournament, 'B');

            DB::commit();
        } catch (Throwable $e) {
            DB::rollBack();

            return response()->json($e->getMessage(), 500);
        }

        $winners = [];

        foreach (['A', 'B'] as $division) {
            $winners[$division] = $tournament->getDivisionGameWinners($division);
        }

        return response()->json([
            'divisionA' => [
                'columns' => $divisionAData['columns'],
                'rows' => $divisionAData['rows'],
                'winners' => $winners['A'],
            ],
            'divisionB' => [
                'columns' => $divisionBData['columns'],
                'rows' => $divisionBData['rows'],
                'winners' => $winners['B'],
            ],
        ]);
    }

    public function runPlayoffs(Tournament $tournament): JsonResponse {
        DB::beginTransaction();

        try {
            $playoffs = $this->tournamentGameService->generatePlayoffGames($tournament);

            DB::commit();
        } catch (Throwable $e) {
            DB::rollBack();

            return response()->json($e->getMessage(), 500);
        }

        return response()->json($playoffs);
    }

    public function runSemiFinals(Tournament $tournament): JsonResponse {
        DB::beginTransaction();

        try {
            $semifinals = $this->tournamentGameService->generateSemiFinalGames($tournament);

            DB::commit();
        } catch (Throwable $e) {
            DB::rollBack();

            return response()->json($e->getMessage(), 500);
        }

        return response()->json($semifinals);
    }

    public function runFinals(Tournament $tournament): JsonResponse {
        DB::beginTransaction();

        try {
            $finals = $this->tournamentGameService->generateFinalGames($tournament);

            DB::commit();
        } catch (Throwable $e) {
            DB::rollBack();

            return response()->json($e->getMessage(), 500);
        }

        $results = $tournament->getFinalWinners();

        return response()->json(compact('results', 'finals'));
    }
}
