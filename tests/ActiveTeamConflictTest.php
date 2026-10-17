<?php

declare(strict_types=1);

namespace WorldCup\Tests;

use DomainException;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use WorldCup\GameSummary;
use WorldCup\Scoreboard;

final class ActiveTeamConflictTest extends TestCase
{
    public function testActivePairCannotStartAgain(): void
    {
        $scoreboard = new Scoreboard();
        $scoreboard->startGame('Mexico', 'Canada');

        try {
            $scoreboard->startGame('Mexico', 'Canada');
            self::fail('An active pair must not start again.');
        } catch (DomainException) {
        }

        self::assertSame(2, $scoreboard->startGame('Spain', 'Brazil'));
    }

    public function testReversedPairIsTheSameFixture(): void
    {
        $scoreboard = new Scoreboard();
        $scoreboard->startGame('Mexico', 'Canada');

        $this->expectException(DomainException::class);

        $scoreboard->startGame('Canada', 'Mexico');
    }

    #[DataProvider('teamAlreadyPlaying')]
    public function testTeamCannotPlayTwoMatches(string $homeTeam, string $awayTeam): void
    {
        $scoreboard = new Scoreboard();
        $scoreboard->startGame('Mexico', 'Canada');

        $this->expectException(DomainException::class);

        $scoreboard->startGame($homeTeam, $awayTeam);
    }

    /**
     * @return array<string, array{string, string}>
     */
    public static function teamAlreadyPlaying(): array
    {
        return [
            'home switches side' => ['Brazil', 'Mexico'],
            'away switches side ignoring case' => ['canada', 'Brazil'],
        ];
    }

    public function testArgumentErrorWinsOverActiveCollision(): void
    {
        $scoreboard = new Scoreboard();
        $scoreboard->startGame('Poland', 'Germany');

        try {
            $scoreboard->startGame('   ', 'Poland');
            self::fail('An empty name must be rejected before the active-team check.');
        } catch (InvalidArgumentException) {
        }

        self::assertSame(2, $scoreboard->startGame('Spain', 'Brazil'));
    }

    public function testDifferentSpellingsAreDistinctTeams(): void
    {
        $scoreboard = new Scoreboard();

        $scoreboard->startGame('Poland', 'Brazil');
        $scoreboard->startGame('Polska', 'Canada');
        $scoreboard->startGame('USA', 'Germany');
        $scoreboard->startGame('United States', 'France');

        $homeTeams = array_map(
            static fn (GameSummary $game): string => $game->homeTeam,
            $scoreboard->getSummary(),
        );
        sort($homeTeams);

        self::assertSame(['Poland', 'Polska', 'USA', 'United States'], $homeTeams);
    }
}
