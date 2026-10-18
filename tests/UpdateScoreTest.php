<?php

declare(strict_types=1);

namespace WorldCup\Tests;

use PHPUnit\Framework\TestCase;
use WorldCup\Scoreboard;

final class UpdateScoreTest extends TestCase
{
    public function testUpdateReplacesAbsoluteScore(): void
    {
        $scoreboard = new Scoreboard();
        $id = $scoreboard->startGame('Mexico', 'Canada');

        $scoreboard->updateScore($id, 10, 2);

        $afterJump = $scoreboard->getSummary()[0];

        self::assertSame(10, $afterJump->homeScore);
        self::assertSame(2, $afterJump->awayScore);

        $scoreboard->updateScore($id, 1, 0);

        $afterCorrection = $scoreboard->getSummary()[0];

        self::assertSame($id, $afterCorrection->id);
        self::assertSame('Mexico', $afterCorrection->homeTeam);
        self::assertSame('Canada', $afterCorrection->awayTeam);
        self::assertSame(1, $afterCorrection->homeScore);
        self::assertSame(0, $afterCorrection->awayScore);
    }

    public function testUpdateLeavesOtherGamesUntouched(): void
    {
        $scoreboard = new Scoreboard();
        $firstId = $scoreboard->startGame('Mexico', 'Canada');
        $secondId = $scoreboard->startGame('Spain', 'Brazil');

        $scoreboard->updateScore($firstId, 3, 1);

        $byId = [];

        foreach ($scoreboard->getSummary() as $game) {
            $byId[$game->id] = $game;
        }

        self::assertSame(
            ['Spain', 'Brazil', 0, 0],
            [
                $byId[$secondId]->homeTeam,
                $byId[$secondId]->awayTeam,
                $byId[$secondId]->homeScore,
                $byId[$secondId]->awayScore,
            ],
        );
        self::assertSame(3, $scoreboard->startGame('Germany', 'France'));
    }
}
