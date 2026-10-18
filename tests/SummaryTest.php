<?php

declare(strict_types=1);

namespace WorldCup\Tests;

use Error;
use PHPUnit\Framework\TestCase;
use WorldCup\Scoreboard;

final class SummaryTest extends TestCase
{
    public function testEarlierSnapshotStaysUnchangedAfterUpdateAndFinish(): void
    {
        $scoreboard = new Scoreboard();
        $id = $scoreboard->startGame('Mexico', 'Canada');

        $beforeUpdate = $scoreboard->getSummary()[0];

        $scoreboard->updateScore($id, 2, 1);

        $afterUpdate = $scoreboard->getSummary()[0];

        $scoreboard->finishGame($id);

        self::assertSame($id, $beforeUpdate->id);
        self::assertSame('Mexico', $beforeUpdate->homeTeam);
        self::assertSame('Canada', $beforeUpdate->awayTeam);
        self::assertSame(0, $beforeUpdate->homeScore);
        self::assertSame(0, $beforeUpdate->awayScore);

        self::assertSame($id, $afterUpdate->id);
        self::assertSame('Mexico', $afterUpdate->homeTeam);
        self::assertSame('Canada', $afterUpdate->awayTeam);
        self::assertSame(2, $afterUpdate->homeScore);
        self::assertSame(1, $afterUpdate->awayScore);

        self::assertSame([], $scoreboard->getSummary());
    }

    public function testSummaryScoreCannotBeReassigned(): void
    {
        $scoreboard = new Scoreboard();
        $scoreboard->startGame('Mexico', 'Canada');

        $summary = $scoreboard->getSummary()[0];

        $this->expectException(Error::class);

        // @phpstan-ignore assign.propertyProtectedSet
        $summary->homeScore = 1;
    }
}
