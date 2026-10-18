<?php

declare(strict_types=1);

namespace WorldCup\Tests;

use PHPUnit\Framework\TestCase;
use WorldCup\GameSummary;
use WorldCup\Scoreboard;

final class SummarySortingTest extends TestCase
{
    public function testHigherTotalComesFirst(): void
    {
        $scoreboard = new Scoreboard();
        $lowTotalId = $scoreboard->startGame('Mexico', 'Canada');
        $highTotalId = $scoreboard->startGame('Spain', 'Brazil');
        $middleTotalId = $scoreboard->startGame('Germany', 'France');

        $scoreboard->updateScore($lowTotalId, 2, 2);
        $scoreboard->updateScore($highTotalId, 10, 2);
        $scoreboard->updateScore($middleTotalId, 3, 2);

        self::assertSame(
            [$highTotalId, $middleTotalId, $lowTotalId],
            array_map(
                static fn (GameSummary $game): int => $game->id,
                $scoreboard->getSummary(),
            ),
        );

        $scoreboard->updateScore($highTotalId, 1, 0);

        self::assertSame(
            [$middleTotalId, $lowTotalId, $highTotalId],
            array_map(
                static fn (GameSummary $game): int => $game->id,
                $scoreboard->getSummary(),
            ),
        );
    }

    public function testTiedGamesUseLaterId(): void
    {
        $scoreboard = new Scoreboard();
        $firstId = $scoreboard->startGame('Mexico', 'Canada');
        $secondId = $scoreboard->startGame('Spain', 'Brazil');
        $thirdId = $scoreboard->startGame('Germany', 'France');

        self::assertSame(
            [$thirdId, $secondId, $firstId],
            array_map(
                static fn (GameSummary $game): int => $game->id,
                $scoreboard->getSummary(),
            ),
        );
    }

    public function testLaterScoreUpdateDoesNotWinTieBreak(): void
    {
        $scoreboard = new Scoreboard();
        $earlierId = $scoreboard->startGame('Mexico', 'Canada');
        $laterId = $scoreboard->startGame('Spain', 'Brazil');

        $scoreboard->updateScore($laterId, 1, 1);
        $scoreboard->updateScore($earlierId, 2, 0);

        self::assertSame(
            [$laterId, $earlierId],
            array_map(
                static fn (GameSummary $game): int => $game->id,
                $scoreboard->getSummary(),
            ),
        );
    }
}
