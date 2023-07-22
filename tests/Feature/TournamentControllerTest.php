<?php

namespace Tests\Feature;

use App\Models\Tournament;
use Tests\TestCase;

class TournamentControllerTest extends TestCase {

    public function testStore(): void {
        $response = $this->post('/tournaments');

        $response->assertStatus(201);

        $data = $response->json();

        $this->assertArrayHasKey('divisionA', $data);
        $this->assertArrayHasKey('divisionB', $data);
        $this->assertArrayHasKey('tournament', $data);

        $tournamentId = $data['tournament']['id'];
        /** @var Tournament $tournament */
        $tournament = Tournament::query()->find($tournamentId);

        $this->assertNotNull($tournament);

        $teamsACount = $tournament
            ->teams()
            ->where('division', 'A')
            ->count();
        $teamsBCount = $tournament
            ->teams()
            ->where('division', 'B')
            ->count();

        $this->assertGreaterThan(0, $teamsACount);
        $this->assertGreaterThan(0, $teamsBCount);
    }

    public function testDestroy(): void {
        $tournament = Tournament::query()->create();
        $response = $this->delete("/tournaments/$tournament->id");

        $response->assertStatus(200);
    }
}
