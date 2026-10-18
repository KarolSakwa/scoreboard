<?php

declare(strict_types=1);

namespace WorldCup\Tests;

use InvalidArgumentException;
use OutOfBoundsException;
use PHPUnit\Framework\Attributes\DataProvider;
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

    public function testSettingIdenticalScoreSucceeds(): void
    {
        $scoreboard = new Scoreboard();
        $id = $scoreboard->startGame('Mexico', 'Canada');

        $scoreboard->updateScore($id, 3, 1);
        $scoreboard->updateScore($id, 3, 1);

        $game = $scoreboard->getSummary()[0];

        self::assertSame(3, $game->homeScore);
        self::assertSame(1, $game->awayScore);
    }

    #[DataProvider('negativeScores')]
    public function testNegativeScoreThrowsAndKeepsPrevious(int $homeScore, int $awayScore): void
    {
        $scoreboard = new Scoreboard();
        $id = $scoreboard->startGame('Mexico', 'Canada');
        $scoreboard->updateScore($id, 2, 2);

        try {
            $scoreboard->updateScore($id, $homeScore, $awayScore);
            self::fail('A negative score must be rejected.');
        } catch (InvalidArgumentException) {
        }

        $game = $scoreboard->getSummary()[0];

        self::assertSame(2, $game->homeScore);
        self::assertSame(2, $game->awayScore);
    }

    /**
     * @return array<string, array{int, int}>
     */
    public static function negativeScores(): array
    {
        return [
            'home' => [-1, 2],
            'away' => [2, -1],
        ];
    }

    #[DataProvider('unknownIds')]
    public function testUnknownIdThrows(int $gameId): void
    {
        $scoreboard = new Scoreboard();
        $existingId = $scoreboard->startGame('Mexico', 'Canada');
        $scoreboard->updateScore($existingId, 2, 2);

        try {
            $scoreboard->updateScore($gameId, 1, 0);
            self::fail('An unknown id must be rejected.');
        } catch (OutOfBoundsException) {
        }

        $game = $scoreboard->getSummary()[0];

        self::assertSame(2, $game->homeScore);
        self::assertSame(2, $game->awayScore);
        self::assertSame(2, $scoreboard->startGame('Spain', 'Brazil'));
    }

    /**
     * @return array<string, array{int}>
     */
    public static function unknownIds(): array
    {
        return [
            'zero' => [0],
            'negative' => [-1],
            'never issued' => [99],
        ];
    }

    public function testNegativeScoreOnUnknownIdIsArgumentError(): void
    {
        $scoreboard = new Scoreboard();
        $id = $scoreboard->startGame('Mexico', 'Canada');
        $scoreboard->updateScore($id, 2, 2);

        try {
            $scoreboard->updateScore(999, -1, 0);
            self::fail('A negative score must be rejected before the id lookup.');
        } catch (InvalidArgumentException) {
        }

        $game = $scoreboard->getSummary()[0];

        self::assertSame(2, $game->homeScore);
        self::assertSame(2, $game->awayScore);
        self::assertSame(2, $scoreboard->startGame('Spain', 'Brazil'));
    }
}
