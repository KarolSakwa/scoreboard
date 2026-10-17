<?php

declare(strict_types=1);

namespace WorldCup\Tests;

use PHPUnit\Framework\TestCase;
use WorldCup\GameSummary;
use WorldCup\Scoreboard;

final class StartGameTest extends TestCase
{
    public function testNewScoreboardHasNoGames(): void
    {
        $scoreboard = new Scoreboard();

        self::assertSame([], $scoreboard->getSummary());
    }

    public function testStartReturnsFirstIdAndZeroScore(): void
    {
        $scoreboard = new Scoreboard();

        $id = $scoreboard->startGame('Mexico', 'Canada');

        self::assertSame(1, $id);

        $summary = $scoreboard->getSummary();

        self::assertCount(1, $summary);
        self::assertInstanceOf(GameSummary::class, $summary[0]);
        self::assertSame(1, $summary[0]->id);
        self::assertSame('Mexico', $summary[0]->homeTeam);
        self::assertSame('Canada', $summary[0]->awayTeam);
        self::assertSame(0, $summary[0]->homeScore);
        self::assertSame(0, $summary[0]->awayScore);
    }

    public function testIdsIncreaseAcrossDisjointPairs(): void
    {
        $scoreboard = new Scoreboard();

        $firstId = $scoreboard->startGame('Mexico', 'Canada');
        $secondId = $scoreboard->startGame('Spain', 'Brazil');

        self::assertSame(1, $firstId);
        self::assertSame(2, $secondId);

        $byId = [];
        foreach ($scoreboard->getSummary() as $game) {
            $byId[$game->id] = $game;
        }

        self::assertSame(
            ['Mexico', 'Canada', 0, 0],
            [
                $byId[1]->homeTeam,
                $byId[1]->awayTeam,
                $byId[1]->homeScore,
                $byId[1]->awayScore,
            ],
        );
        self::assertSame(
            ['Spain', 'Brazil', 0, 0],
            [
                $byId[2]->homeTeam,
                $byId[2]->awayTeam,
                $byId[2]->homeScore,
                $byId[2]->awayScore,
            ],
        );
    }
}
