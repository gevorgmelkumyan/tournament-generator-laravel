<?php

namespace Tests\Unit;

use App\Interfaces\TeamGameServiceInterface;
use Tests\TestCase;

class TeamGameServiceTest extends TestCase {

    protected TeamGameServiceInterface $teamGameService;

    protected function setUp(): void {
        parent::setUp();

        $this->teamGameService = app(TeamGameServiceInterface::class);
    }

    public function testGenerateTeamGame(): void {
        $data = $this->teamGameService->generateTeamGame(1, 2, 3);

        $this->assertCount(2, $data);

        foreach ($data as $item) {
            $this->assertArrayHasKey('game_id', $item);
            $this->assertArrayHasKey('team_id', $item);
            $this->assertArrayHasKey('score', $item);

            $this->assertEquals(1, $item['game_id']);
        }
    }
}
