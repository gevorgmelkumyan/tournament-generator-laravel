<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\Team;
use App\Models\TeamGame;
use App\Models\Tournament;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Throwable;

class Controller extends BaseController {
    use AuthorizesRequests, ValidatesRequests;

    const NUMBER_OF_TEAMS = 15;

    public function createTournament(): JsonResponse {
        /** @var Tournament $tournament */
        $tournament = Tournament::query()->create();

        $divisionA = [];
        $divisionB = [];

        for ($i = 0; $i < self::NUMBER_OF_TEAMS; ++$i) {
            $divisionA[] = [
                'tournament_id' => $tournament->id,
                'division' => 'A',
                'name' => strtoupper(substr(Str::random(20), rand(1, 10), 3)),
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $divisionB[] = [
                'tournament_id' => $tournament->id,
                'division' => 'B',
                'name' => strtoupper(substr(Str::random(20), rand(1, 10), 3)),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        Team::query()->insert(array_merge($divisionA, $divisionB));

        return response()->json(compact('divisionA', 'divisionB', 'tournament'));
    }

    public function runDivisionGames(Tournament $tournament): JsonResponse {

        DB::beginTransaction();

        try {
            $divisionATeamGames = $this->runGamesForDivision($tournament);
            $divisionBTeamGames = $this->runGamesForDivision($tournament, 'B');

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

    public function runPlayoffs(Tournament $tournament): JsonResponse {
        $teamsA = TeamGame::query()
            ->selectRaw('team_id, sum(score) total')
            ->join('games', 'game_id', '=', 'games.id')
            ->join('teams', 'team_id', '=', 'teams.id')
            ->where('type', Game::TYPE_DIVISION)
            ->where('games.tournament_id', $tournament->id)
            ->where('teams.division', 'A')
            ->groupBy('team_games.team_id')
            ->orderByDesc('total')
            ->limit(4)
            ->get();

        $teamsB = TeamGame::query()
            ->selectRaw('team_id, sum(score) total')
            ->join('games', 'game_id', '=', 'games.id')
            ->join('teams', 'team_id', '=', 'teams.id')
            ->where('type', Game::TYPE_DIVISION)
            ->where('games.tournament_id', $tournament->id)
            ->where('teams.division', 'B')
            ->groupBy('team_games.team_id')
            ->orderByDesc('total')
            ->limit(4)
            ->get();

        $playoffs = [];

        for ($i = 0; $i < 4; ++$i) {
            /** @var Game $game */
            $game = Game::query()->create([
                'tournament_id' => $tournament->id,
                'type' => Game::TYPE_PLAYOFFS,
            ]);

            $score1 = rand(0, 5);

            do {
                $score2 = rand(0, 5);
            } while ($score1 == $score2);

            $team1 = [
                'game_id' => $game->id,
                'team_id' => $teamsA[$i]->team_id,
                'score' => $score1,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $team2 = [
                'game_id' => $game->id,
                'team_id' => $teamsB[4 - ($i + 1)]->team_id,
                'score' => $score2,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $playoffs[] = [
                $team1,
                $team2,
            ];

            TeamGame::query()->insert([
                $team1,
                $team2,
            ]);
        }

        return response()->json($playoffs);
    }

    public function runSemiFinals(Tournament $tournament): JsonResponse {
        $playoffWinners = TeamGame::query()
            ->select('game_id', 'team_id', 'score')
            ->join('games', 'team_games.game_id', '=', 'games.id')
            ->where('games.tournament_id', $tournament->id)
            ->where('games.type', Game::TYPE_PLAYOFFS)
            ->whereRaw("(game_id, score) in (
                select game_id, max(score)
                from team_games
                group by game_id
            )")
            ->orderByDesc('score')
            ->get();

        for ($i = 0; $i < 2; ++$i) {
            /** @var Game $game */
            $game = Game::query()->create([
                'tournament_id' => $tournament->id,
                'type' => Game::TYPE_SEMI_FINALS,
            ]);

            $score1 = rand(0, 5);

            do {
                $score2 = rand(0, 5);
            } while ($score1 == $score2);

            $data = [
                [
                    'game_id' => $game->id,
                    'team_id' => $playoffWinners[$i]->team_id,
                    'score' => $score1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'game_id' => $game->id,
                    'team_id' => $playoffWinners[4 - ($i + 1)]->team_id,
                    'score' => $score2,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            ];

            $semifinals[] = $data;

            TeamGame::query()->insert($data);
        }

        return response()->json($semifinals);
    }

    public function runFinals(Tournament $tournament): JsonResponse {
        $semifinalWinners = TeamGame::query()
            ->select('game_id', 'team_id', 'score')
            ->join('games', 'team_games.game_id', '=', 'games.id')
            ->where('games.tournament_id', $tournament->id)
            ->where('games.type', Game::TYPE_SEMI_FINALS)
            ->whereRaw("(game_id, score) in (
                select game_id, max(score)
                from team_games
                group by game_id
            )")
            ->get();

        $semifinalLosers = TeamGame::query()
            ->select('game_id', 'team_id', 'score')
            ->join('games', 'team_games.game_id', '=', 'games.id')
            ->where('games.tournament_id', $tournament->id)
            ->where('games.type', Game::TYPE_SEMI_FINALS)
            ->whereRaw("(game_id, score) in (
                select game_id, min(score)
                from team_games
                group by game_id
            )")
            ->get();

        foreach ([$semifinalWinners, $semifinalLosers] as $collection) {
            /** @var Game $game */
            $game = Game::query()->create([
                'tournament_id' => $tournament->id,
                'type' => Game::TYPE_FINALS,
            ]);

            $score1 = rand(0, 5);

            do {
                $score2 = rand(0, 5);
            } while ($score1 == $score2);

            $data = [
                [
                    'game_id' => $game->id,
                    'team_id' => $collection[0]->team_id,
                    'score' => $score1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'game_id' => $game->id,
                    'team_id' => $collection[1]->team_id,
                    'score' => $score2,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            ];

            $finals[] = $data;

            TeamGame::query()->insert($data);
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

    public function destroy(Tournament $tournament): JsonResponse {
        $tournament->deleteOrFail();

        return response()->json();
    }

    protected function runGamesForDivision(Tournament $tournament, string $division = 'A'): array {
        $teams = $tournament
            ->teams()
            ->where('division', $division)
            ->get();
        $teamCount = count($teams);

        $teamGames = [];

        for ($i = 0; $i < $teamCount; ++$i) {
            for ($j = $i + 1; $j < $teamCount; ++$j) {

                /** @var Game $game */
                $game = Game::query()->create([
                    'tournament_id' => $tournament->id,
                    'type' => Game::TYPE_DIVISION,
                ]);

                $score1 = rand(0, 5);

                do {
                    $score2 = rand(0, 5);
                } while ($score1 == $score2);

                $teamGames[] = [
                    'game_id' => $game->id,
                    'team_id' => $teams[$i]->id,
                    'score' => $score1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
                $teamGames[] = [
                    'game_id' => $game->id,
                    'team_id' => $teams[$j]->id,
                    'score' => $score2,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        TeamGame::query()->insert($teamGames);

        return $teamGames;
    }
}
