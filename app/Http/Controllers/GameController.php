<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseMessage;
use App\Interfaces\TournamentGameServiceInterface;
use App\Models\Tournament;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Throwable;

class GameController extends Controller {

    public function __construct(protected TournamentGameServiceInterface $tournamentGameService) {}

    public function runDivisionGames(Tournament $tournament): JsonResponse {
        if (!$tournament->canRunDivisionGames()) {
            return $this->respond(['message' => ResponseMessage::ERROR_CANT_RUN_DIVISION_GAMES], 400);
        }

        DB::beginTransaction();

        try {
            $divisionAData = $this->tournamentGameService->generateDivisionGames($tournament);
            $divisionBData = $this->tournamentGameService->generateDivisionGames($tournament, 'B');

            DB::commit();
        } catch (Throwable $e) {
            DB::rollBack();

            return $this->respond(['message' => ResponseMessage::ERROR_SOMETHING_WENT_WRONG], 500);
        }

        $winners = [];

        foreach (['A', 'B'] as $division) {
            $winners[$division] = $tournament->getDivisionGameWinners($division);
        }

        return $this->respond([
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
        if (!$tournament->canRunPlayoffs()) {
            return $this->respond(['message' => ResponseMessage::ERROR_CANT_RUN_PLAYOFFS], 400);
        }

        DB::beginTransaction();

        try {
            $playoffs = $this->tournamentGameService->generatePlayoffGames($tournament);

            DB::commit();
        } catch (Throwable $e) {
            DB::rollBack();

            return $this->respond(['message' => ResponseMessage::ERROR_SOMETHING_WENT_WRONG], 500);
        }

        return $this->respond($playoffs);
    }

    public function runSemiFinals(Tournament $tournament): JsonResponse {
        if (!$tournament->canRunSemiFinals()) {
            return $this->respond(['message' => ResponseMessage::ERROR_CANT_RUN_SEMI_FINALS], 400);
        }

        DB::beginTransaction();

        try {
            $semifinals = $this->tournamentGameService->generateSemiFinalGames($tournament);

            DB::commit();
        } catch (Throwable $e) {
            DB::rollBack();

            return $this->respond(['message' => ResponseMessage::ERROR_SOMETHING_WENT_WRONG], 500);
        }

        return $this->respond($semifinals);
    }

    public function runFinals(Tournament $tournament): JsonResponse {
        if (!$tournament->canRunFinals()) {
            return $this->respond(['message' => ResponseMessage::ERROR_CANT_RUN_FINALS], 400);
        }

        DB::beginTransaction();

        try {
            $finals = $this->tournamentGameService->generateFinalGames($tournament);

            DB::commit();
        } catch (Throwable $e) {
            DB::rollBack();

            return $this->respond(['message' => ResponseMessage::ERROR_SOMETHING_WENT_WRONG], 500);
        }

        $results = $tournament->getFinalWinners();

        return $this->respond(compact('results', 'finals'));
    }
}
