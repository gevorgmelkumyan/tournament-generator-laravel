<?php

namespace Tests\Feature;

use App\Models\Tournament;
use Tests\TestCase;

class GameControllerTest extends TestCase {

    public function testRunDivisionGames(): void {
        /** @var Tournament $tournament */
        $tournament = Tournament::query()->create();

        $response = $this->post("/tournaments/$tournament->id/run_division_games");
        $response->assertStatus(400);

        $response = $this->post('/tournaments');
        $tournament = $response->json('tournament');

        $response = $this->post("/tournaments/{$tournament['id']}/run_division_games");
        $response->assertStatus(200);

        $data = $response->json();
        $this->assertArrayHasKey('divisionA', $data);
        $this->assertArrayHasKey('divisionB', $data);
        $this->assertIsArray($data['divisionA']);
        $this->assertIsArray($data['divisionB']);
    }

    public function testRunPlayoffs(): void {
        /** @var Tournament $tournament */
        $tournament = Tournament::query()->create();

        $response = $this->post("/tournaments/$tournament->id/run_playoffs");
        $response->assertStatus(400);

        $response = $this->post('/tournaments');
        $tournament = $response->json('tournament');

        $response = $this->post("/tournaments/{$tournament['id']}/run_division_games");
        $response->assertStatus(200);

        $response = $this->post("/tournaments/{$tournament['id']}/run_playoffs");
        $response->assertStatus(200);

        $data = $response->json();
        $this->assertGreaterThan(0, count($data));

        $item = $data[0];
        $this->assertIsArray($item);
        $this->assertCount(2, $item);
    }

    public function testRunSemiFinals(): void {
        /** @var Tournament $tournament */
        $tournament = Tournament::query()->create();

        $response = $this->post("/tournaments/$tournament->id/run_semi_finals");
        $response->assertStatus(400);

        $response = $this->post('/tournaments');
        $tournament = $response->json('tournament');

        $response = $this->post("/tournaments/{$tournament['id']}/run_division_games");
        $response->assertStatus(200);

        $response = $this->post("/tournaments/{$tournament['id']}/run_playoffs");
        $response->assertStatus(200);

        $response = $this->post("/tournaments/{$tournament['id']}/run_semi_finals");
        $response->assertStatus(200);

        $data = $response->json();
        $this->assertGreaterThan(0, count($data));

        $item = $data[0];
        $this->assertIsArray($item);
        $this->assertCount(2, $item);
    }

    public function testRunFinals(): void {
        /** @var Tournament $tournament */
        $tournament = Tournament::query()->create();

        $response = $this->post("/tournaments/$tournament->id/run_finals");
        $response->assertStatus(400);

        $response = $this->post('/tournaments');
        $tournament = $response->json('tournament');

        $response = $this->post("/tournaments/{$tournament['id']}/run_division_games");
        $response->assertStatus(200);

        $response = $this->post("/tournaments/{$tournament['id']}/run_playoffs");
        $response->assertStatus(200);

        $response = $this->post("/tournaments/{$tournament['id']}/run_semi_finals");
        $response->assertStatus(200);

        $response = $this->post("/tournaments/{$tournament['id']}/run_finals");
        $response->assertStatus(200);

        $data = $response->json();
        $this->assertGreaterThan(0, count($data));
        $this->assertArrayHasKey('results', $data);
        $this->assertArrayHasKey('finals', $data);
    }
}
