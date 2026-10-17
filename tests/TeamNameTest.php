<?php

declare(strict_types=1);

namespace WorldCup\Tests;

use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use WorldCup\GameSummary;
use WorldCup\Scoreboard;

final class TeamNameTest extends TestCase
{
    public function testDisplayNameIsTrimmedAndCaseIsPreserved(): void
    {
        $scoreboard = new Scoreboard();

        $mexicoId = $scoreboard->startGame('  MeXiCo  ', "\tcanada\n");
        $koreaId = $scoreboard->startGame('  South  Korea  ', 'Germany');

        $byId = $this->summaryById($scoreboard);

        self::assertSame('MeXiCo', $byId[$mexicoId]->homeTeam);
        self::assertSame('canada', $byId[$mexicoId]->awayTeam);
        self::assertSame('South  Korea', $byId[$koreaId]->homeTeam);
    }

    #[DataProvider('emptyNames')]
    public function testEmptyOrWhitespaceNameThrows(string $homeTeam, string $awayTeam): void
    {
        $scoreboard = new Scoreboard();

        $this->expectException(InvalidArgumentException::class);

        $scoreboard->startGame($homeTeam, $awayTeam);
    }

    /**
     * @return array<string, array{string, string}>
     */
    public static function emptyNames(): array
    {
        return [
            'empty' => ['', 'Poland'],
            'whitespace' => ["\t\n", 'Poland'],
            'empty away' => ['Poland', '   '],
        ];
    }

    public function testFailedStartDoesNotConsumeIdOrExistingGame(): void
    {
        $scoreboard = new Scoreboard();
        $existingId = $scoreboard->startGame('Poland', 'Germany');

        try {
            $scoreboard->startGame('   ', 'Spain');
            self::fail('An empty team name must be rejected.');
        } catch (InvalidArgumentException) {
        }

        $byId = $this->summaryById($scoreboard);

        self::assertSame('Poland', $byId[$existingId]->homeTeam);
        self::assertSame('Germany', $byId[$existingId]->awayTeam);
        self::assertSame(2, $scoreboard->startGame('Spain', 'Brazil'));
    }

    #[DataProvider('sameTeamNames')]
    public function testSameTeamAfterTrimAndCaseFoldThrows(string $homeTeam, string $awayTeam): void
    {
        $scoreboard = new Scoreboard();

        $this->expectException(InvalidArgumentException::class);

        $scoreboard->startGame($homeTeam, $awayTeam);
    }

    /**
     * @return array<string, array{string, string}>
     */
    public static function sameTeamNames(): array
    {
        return [
            'trim and case' => [' Poland ', 'POLAND'],
            'utf-8' => ['MÜNCHEN', 'münchen'],
        ];
    }

    /**
     * @return array<int, GameSummary>
     */
    private function summaryById(Scoreboard $scoreboard): array
    {
        $byId = [];

        foreach ($scoreboard->getSummary() as $game) {
            $byId[$game->id] = $game;
        }

        return $byId;
    }
}
