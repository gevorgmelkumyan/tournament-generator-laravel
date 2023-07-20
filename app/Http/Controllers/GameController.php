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
            $winners[$division] = TeamGame::query()
                ->selectRaw('team_id, sum(score) total')
                ->join('games', 'game_id', '=', 'games.id')
                ->join('teams', 'team_id', '=', 'teams.id')
                ->where('type', Game::TYPE_DIVISION)
                ->where('games.tournament_id', $tournament->id)
                ->where('teams.division', $division)
                ->groupBy('team_games.team_id')
                ->orderByDesc('total')
                ->limit(4)
                ->get();
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

        $results = TeamGame::query()
            ->select('game_id', 'team_id', 'score')
            ->join('games', 'games.id', '=', 'team_games.game_id')
            ->where('games.type', Game::TYPE_FINALS)
            ->where('tournament_id', $tournament->id)
            ->orderByRaw('game_id, score desc')
            ->get();

        return response()->json(compact('results', 'finals'));
    }
}
