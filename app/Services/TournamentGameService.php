<?php

namespace App\Services;

use App\Interfaces\GameServiceInterface;
use App\Interfaces\TeamGameServiceInterface;
use App\Interfaces\TournamentGameServiceInterface;
use App\Models\TeamGame;
use App\Models\Tournament;
use Illuminate\Support\Collection;

class TournamentGameService implements TournamentGameServiceInterface {

    function __construct(
        protected GameServiceInterface $gameService,
        protected TeamGameServiceInterface $teamGameService
    ) {}

    function generateDivisionGames(Tournament $tournament, string $division = 'A'): array {
        $teams = $tournament
            ->teams()
            ->where('division', $division)
            ->get();
        $teamCount = count($teams);

        $teamGames = [];

        for ($i = 0; $i < $teamCount; ++$i) {
            for ($j = $i + 1; $j < $teamCount; ++$j) {
                $game = $this->gameService->generateDivisionGame($tournament->id);

                list($teamGameA, $teamGameB) = $this->teamGameService->generateTeamGame($game->id, $teams[$i]->id, $teams[$j]->id);

                $teamGames[] = $teamGameA;
                $teamGames[] = $teamGameB;
            }
        }

        TeamGame::query()->insert($teamGames);

        return $this->formatTeamGames($teamGames, $teams);
    }

    function generatePlayoffGames(Tournament $tournament): array {
        $winners = [];

        foreach (['A', 'B'] as $division) {
            $winners[$division] = $tournament->getDivisionGameWinners($division);
        }

        $playoffs = [];

        for ($i = 0; $i < 4; ++$i) {
            $game = $this->gameService->generatePlayoffGame($tournament->id);

            list($teamGameA, $teamGameB) = $this->teamGameService->generateTeamGame(
                $game->id,
                $winners['A'][$i]->team_id,
                $winners['B'][4 - ($i + 1)]->team_id
            );

            $playoffs[] = [
                $teamGameA,
                $teamGameB
            ];
        }

        TeamGame::query()->insert(array_merge(...$playoffs));

        return $playoffs;
    }

    function generateSemiFinalGames(Tournament $tournament): array {
        $playoffWinners = $tournament->getPlayoffWinners();

        $semifinals = [];

        for ($i = 0; $i < 2; ++$i) {
            $game = $this->gameService->generateSemiFinalGame($tournament->id);

            list($teamGameA, $teamGameB) = $this->teamGameService->generateTeamGame(
                $game->id,
                $playoffWinners[$i]->team_id,
                $playoffWinners[4 - ($i + 1)]->team_id
            );

            $semifinals[] = [
                $teamGameA,
                $teamGameB
            ];
        }


        TeamGame::query()->insert(array_merge(...$semifinals));

        return $semifinals;
    }

    function generateFinalGames(Tournament $tournament): array {
        $semifinalWinners = $tournament->getSemiFinalTeams();
        $semifinalLosers = $tournament->getSemiFinalTeams(false);

        $finals = [];

        foreach ([$semifinalWinners, $semifinalLosers] as $collection) {
            $game = $this->gameService->generateFinalGame($tournament->id);

            list($teamGameA, $teamGameB) = $this->teamGameService->generateTeamGame(
                $game->id,
                $collection[0]->team_id,
                $collection[1]->team_id
            );

            $finals[] = [
                $teamGameA,
                $teamGameB,
            ];
        }

        TeamGame::query()->insert(array_merge(...$finals));

        return $finals;
    }

    protected function formatTeamGames(array $teamGames, Collection $teams): array {
        $teamGames = collect($teamGames);

        $rows = [];

        foreach ($teams as $key => $divisionATeam) {
            $rowData = array_values($teamGames->where('team_id', $divisionATeam->id)->toArray());
            array_splice($rowData, $key, 0, [['game_id' => $rowData[0]['game_id'], 'team_id' => $divisionATeam->id, 'score' => '-']]);
            $rows[] = array_values($rowData);
        }

        return [
            'columns' => $teams,
            'rows' => $rows,
        ];
    }
}
