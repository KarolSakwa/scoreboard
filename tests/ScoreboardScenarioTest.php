<?php

declare(strict_types=1);

namespace WorldCup\Tests;

use PHPUnit\Framework\TestCase;
use WorldCup\GameSummary;
use WorldCup\Scoreboard;

final class ScoreboardScenarioTest extends TestCase
{
    public function testSportradarSampleOrder(): void
    {
        $scoreboard = new Scoreboard();

        $mexico = $scoreboard->startGame('Mexico', 'Canada');
        $spain = $scoreboard->startGame('Spain', 'Brazil');
        $germany = $scoreboard->startGame('Germany', 'France');
        $uruguay = $scoreboard->startGame('Uruguay', 'Italy');
        $argentina = $scoreboard->startGame('Argentina', 'Australia');

        $scoreboard->updateScore($mexico, 0, 5);
        $scoreboard->updateScore($spain, 10, 2);
        $scoreboard->updateScore($germany, 2, 2);
        $scoreboard->updateScore($uruguay, 6, 6);
        $scoreboard->updateScore($argentina, 3, 1);

        self::assertSame(
            [
                [4, 'Uruguay', 'Italy', 6, 6],
                [2, 'Spain', 'Brazil', 10, 2],
                [1, 'Mexico', 'Canada', 0, 5],
                [5, 'Argentina', 'Australia', 3, 1],
                [3, 'Germany', 'France', 2, 2],
            ],
            array_map(
                static fn (GameSummary $game): array => [
                    $game->id,
                    $game->homeTeam,
                    $game->awayTeam,
                    $game->homeScore,
                    $game->awayScore,
                ],
                $scoreboard->getSummary(),
            ),
        );
    }
}
