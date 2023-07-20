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

    public function respond(mixed $data = [], int $code = 200): JsonResponse {
        return response()->json($data, $code);
    }
}
