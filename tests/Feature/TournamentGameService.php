<?php

namespace Tests\Feature;

use App\Interfaces\TournamentGameServiceInterface;
use App\Interfaces\TournamentServiceInterface;
use App\Models\Game;
use App\Models\Tournament;
use Tests\TestCase;

class TournamentGameService extends TestCase {

    protected Tournament $tournament;

    protected TournamentGameServiceInterface $tournamentGameService;

    protected function setUp(): void {
        parent::setUp();

        /** @var TournamentServiceInterface $tournamentService */
        $tournamentService = app(TournamentServiceInterface::class);

        $this->tournament = $tournamentService->create()['tournament'];
        $this->tournamentGameService = app(TournamentGameServiceInterface::class);
    }

    public function testGenerateDivisionGames(): void {
        $data = $this->tournamentGameService->generateDivisionGames($this->tournament);

        $gamesCount = $this
            ->tournament
            ->games()
            ->where('type', Game::TYPE_DIVISION)
            ->count();

        $teamGamesCount = $this
            ->tournament
            ->teamGames()
            ->count();

        $this->assertGreaterThan(0, $gamesCount);
        $this->assertGreaterThan(0, $teamGamesCount);

        $this->assertArrayHasKey('columns', $data);
        $this->assertArrayHasKey('rows', $data);
    }

    public function testGeneratePlayoffGames(): void {
        $data = $this->tournamentGameService->generatePlayoffGames($this->tournament);

        $this->assertGreaterThan(0, count($data));

        $gamesCount = $this
            ->tournament
            ->games()
            ->where('type', Game::TYPE_PLAYOFFS)
            ->count();

        $this->assertGreaterThan(0, $gamesCount);
    }
}
