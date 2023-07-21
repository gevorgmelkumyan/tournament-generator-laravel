<?php

namespace Tests\Unit;

use App\Interfaces\ScoreServiceInterface;
use Tests\TestCase;

class ScoreServiceTest extends TestCase {

    protected ScoreServiceInterface $scoreService;

    protected function setUp(): void {
        parent::setUp();

        $this->scoreService = app(ScoreServiceInterface::class);
    }

    public function testGenerateScores(): void {
        list($scoreA, $scoreB) = $this->scoreService->generateScores();

        $this->assertTrue($scoreA != $scoreB);
    }
}
