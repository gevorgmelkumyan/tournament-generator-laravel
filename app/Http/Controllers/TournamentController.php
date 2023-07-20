<?php

namespace App\Http\Controllers;

use App\Interfaces\TournamentServiceInterface;
use App\Models\Tournament;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class TournamentController extends Controller {

    public function __construct(protected TournamentServiceInterface $tournamentService) {}

    public function store(): JsonResponse {
        DB::beginTransaction();

        try {
            $response = $this->tournamentService->create();

            DB::commit();
        } catch (Throwable $e) {
            DB::rollBack();

            return $this->respond([
                'message' => 'Something went wrong, please try again.',
            ], 500);
        }

        return $this->respond($response, 201);
    }

    public function destroy(Tournament $tournament): JsonResponse {
        $tournament->deleteOrFail();

        return $this->respond();
    }
}
