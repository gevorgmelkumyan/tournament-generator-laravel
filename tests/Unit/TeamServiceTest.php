<?php

namespace Tests\Unit;

use App\Interfaces\TeamServiceInterface;
use Tests\TestCase;

class TeamServiceTest extends TestCase {

    protected TeamServiceInterface $teamService;

    protected function setUp(): void {
        parent::setUp();

        $this->teamService = app(TeamServiceInterface::class);
    }

    public function testGenerateTeamForTournament(): void {
        $data = $this->teamService->generateTeamForTournament(1);

        $this->assertArrayHasKey('tournament_id', $data);
        $this->assertArrayHasKey('division', $data);
        $this->assertArrayHasKey('name', $data);

        $this->assertEquals(1, $data['tournament_id']);
        $this->assertEquals('A', $data['division']);
        $this->assertNotEmpty($data['name']);
    }
}
