<?php

namespace Tests\Feature;

use App\Interfaces\TournamentServiceInterface;
use App\Models\Team;
use App\Models\Tournament;
use Tests\TestCase;

class TournamentServiceTest extends TestCase {

    protected TournamentServiceInterface $tournamentService;

    protected function setUp(): void {
        parent::setUp();

        $this->tournamentService = app(TournamentServiceInterface::class);
    }

    public function testCreate(): void {
        $data = $this->tournamentService->create();

        $this->assertCount(3, $data);
        $this->assertArrayHasKey('divisionA', $data);
        $this->assertArrayHasKey('divisionB', $data);
        $this->assertArrayHasKey('tournament', $data);

        $tournament = $data['tournament'];

        $this->assertInstanceOf(Tournament::class, $tournament);

        $teamsA = $tournament
            ->teams()
            ->where('division', 'A')
            ->get();

        $teamsB = $tournament
            ->teams()
            ->where('division', 'B')
            ->get();

        $this->assertGreaterThan(0, $teamsA->count());
        $this->assertGreaterThan(0, $teamsB->count());
    }
}
