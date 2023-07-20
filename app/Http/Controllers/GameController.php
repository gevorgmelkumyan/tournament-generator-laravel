<?php

namespace App\Http\Controllers;

use App\Interfaces\TournamentGameServiceInterface;
use App\Models\Game;
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
            $divisionATeamGames = $this->tournamentGameService->generateDivisionGames($tournament->id);
            $divisionBTeamGames = $this->tournamentGameService->generateDivisionGames($tournament->id, 'B');

            DB::commit();
        } catch (Throwable $e) {
            DB::rollBack();

            return response()->json($e->getMessage(), 500);
        }

        $divisionATeamGames = collect($divisionATeamGames);
        $divisionATeams = $tournament->teams()->where('division', 'A')->get();

        $rowsA = [];

        foreach ($divisionATeams as $key => $divisionATeam) {
            $rowData = array_values($divisionATeamGames->where('team_id', $divisionATeam->id)->toArray());
            array_splice($rowData, $key, 0, [['game_id' => $rowData[0]['game_id'], 'team_id' => $divisionATeam->id, 'score' => '-']]);
            $rowsA[] = array_values($rowData);
        }

        $divisionBTeamGames = collect($divisionBTeamGames);
        $divisionBTeams = $tournament->teams()->where('division', 'B')->get();

        $rowsB = [];

        foreach ($divisionBTeams as $key => $divisionBTeam) {
            $rowData = array_values($divisionBTeamGames->where('team_id', $divisionBTeam->id)->toArray());
            array_splice($rowData, $key, 0, [['game_id' => $rowData[0]['game_id'], 'team_id' => $divisionBTeam->id, 'score' => '-']]);
            $rowsB[] = array_values($rowData);
        }

        return response()->json([
            'divisionA' => [
                'columns' => $divisionATeams,
                'rows' => $rowsA,
                'winners' => TeamGame::query()
                    ->selectRaw('team_id, sum(score) total')
                    ->join('games', 'game_id', '=', 'games.id')
                    ->join('teams', 'team_id', '=', 'teams.id')
                    ->where('type', Game::TYPE_DIVISION)
                    ->where('games.tournament_id', $tournament->id)
                    ->where('teams.division', 'A')
                    ->groupBy('team_games.team_id')
                    ->orderByDesc('total')
                    ->limit(4)
                    ->get(),
            ],
            'divisionB' => [
                'columns' => $divisionBTeams,
                'rows' => $rowsB,
                'winners' => TeamGame::query()
                    ->selectRaw('team_id, sum(score) total')
                    ->join('games', 'game_id', '=', 'games.id')
                    ->join('teams', 'team_id', '=', 'teams.id')
                    ->where('type', Game::TYPE_DIVISION)
                    ->where('games.tournament_id', $tournament->id)
                    ->where('teams.division', 'B')
                    ->groupBy('team_games.team_id')
                    ->orderByDesc('total')
                    ->limit(4)
                    ->get(),
            ],
        ]);
    }
}
