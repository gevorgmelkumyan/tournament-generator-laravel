<?php

namespace Tests\Feature;

use App\Interfaces\GameServiceInterface;
use App\Models\Game;
use App\Models\Tournament;
use Tests\TestCase;

class GameServiceTest extends TestCase {

    protected Tournament $tournament;

    protected GameServiceInterface $gameService;

    protected function setUp(): void {
        parent::setUp();

        $this->tournament = Tournament::query()->create();
        $this->gameService = app(GameServiceInterface::class);
    }

    public function testGenerateDivisionGame(): void {
        $game = $this->gameService->generateDivisionGame($this->tournament->id);

        $this->assertEquals($this->tournament->id, $game->tournament_id);
        $this->assertEquals(Game::TYPE_DIVISION, $game->type);
    }

    protected function tearDown(): void {
        $this->tournament->deleteOrFail();

        parent::tearDown();
    }
}
