<?php

declare(strict_types=1);

namespace WorldCup\Tests;

use DomainException;
use OutOfBoundsException;
use PHPUnit\Framework\TestCase;
use WorldCup\Scoreboard;

final class FinishGameTest extends TestCase
{
    public function testFinishRemovesOnlyThatGame(): void
    {
        $scoreboard = new Scoreboard();
        $scoreboard->startGame('Mexico', 'Canada');
        $secondId = $scoreboard->startGame('Spain', 'Brazil');

        $scoreboard->finishGame(1);

        $summary = $scoreboard->getSummary();

        self::assertCount(1, $summary);
        self::assertSame($secondId, $summary[0]->id);
        self::assertSame('Spain', $summary[0]->homeTeam);
        self::assertSame('Brazil', $summary[0]->awayTeam);
    }

    public function testUnknownOrRepeatedFinishThrows(): void
    {
        $scoreboard = new Scoreboard();
        $scoreboard->startGame('Mexico', 'Canada');
        $secondId = $scoreboard->startGame('Spain', 'Brazil');

        try {
            $scoreboard->finishGame(99);
            self::fail('An unknown id must be rejected.');
        } catch (OutOfBoundsException) {
        }

        self::assertCount(2, $scoreboard->getSummary());

        $scoreboard->finishGame(1);

        try {
            $scoreboard->finishGame(1);
            self::fail('A finished game must not be finished again.');
        } catch (OutOfBoundsException) {
        }

        $summary = $scoreboard->getSummary();

        self::assertCount(1, $summary);
        self::assertSame($secondId, $summary[0]->id);
        self::assertSame('Spain', $summary[0]->homeTeam);
    }

    public function testSameTeamsMayPlayAgainUnderNewId(): void
    {
        $scoreboard = new Scoreboard();
        $scoreboard->startGame('Mexico', 'Canada');
        $scoreboard->finishGame(1);

        try {
            $scoreboard->updateScore(1, 1, 0);
            self::fail('A finished game cannot be updated.');
        } catch (OutOfBoundsException) {
        }

        $newId = $scoreboard->startGame('Mexico', 'Canada');
        $summary = $scoreboard->getSummary();

        self::assertSame(2, $newId);
        self::assertCount(1, $summary);
        self::assertSame($newId, $summary[0]->id);
        self::assertSame('Mexico', $summary[0]->homeTeam);
        self::assertSame('Canada', $summary[0]->awayTeam);
    }

    public function testFailedFinishDoesNotFreeATeam(): void
    {
        $scoreboard = new Scoreboard();
        $scoreboard->startGame('Mexico', 'Canada');

        try {
            $scoreboard->finishGame(99);
            self::fail('An unknown id must be rejected.');
        } catch (OutOfBoundsException) {
        }

        try {
            $scoreboard->startGame('Mexico', 'Brazil');
            self::fail('A failed finish must not free an active team.');
        } catch (DomainException) {
        }

        $summary = $scoreboard->getSummary();

        self::assertCount(1, $summary);
        self::assertSame(1, $summary[0]->id);
        self::assertSame('Mexico', $summary[0]->homeTeam);
        self::assertSame('Canada', $summary[0]->awayTeam);
    }
}
