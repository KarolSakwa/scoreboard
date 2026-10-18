<?php

declare(strict_types=1);

namespace WorldCup;

use DomainException;
use InvalidArgumentException;
use OutOfBoundsException;

final class Scoreboard
{
    /** @var array<int, Game> */
    private array $games = [];

    private int $nextId = 1;

    public function startGame(string $homeTeam, string $awayTeam): int
    {
        $homeTeam = trim($homeTeam);
        $awayTeam = trim($awayTeam);

        if ($homeTeam === '' || $awayTeam === '') {
            throw new InvalidArgumentException('Team name cannot be empty.');
        }

        $homeKey = $this->teamKey($homeTeam);
        $awayKey = $this->teamKey($awayTeam);

        if ($homeKey === $awayKey) {
            throw new InvalidArgumentException('Teams must be different.');
        }

        $this->assertTeamsAvailable($homeKey, $awayKey);

        $id = $this->nextId;
        $this->games[$id] = new Game($id, $homeTeam, $awayTeam);
        $this->nextId++;

        return $id;
    }

    public function finishGame(int $gameId): void
    {
        $this->activeGame($gameId);

        unset($this->games[$gameId]);
    }

    public function updateScore(int $gameId, int $homeScore, int $awayScore): void
    {
        if ($homeScore < 0 || $awayScore < 0) {
            throw new InvalidArgumentException('Score cannot be negative.');
        }

        $this->activeGame($gameId)->updateScore($homeScore, $awayScore);
    }

    /**
     * @return list<GameSummary>
     */
    public function getSummary(): array
    {
        return array_map(
            static fn (Game $game): GameSummary => new GameSummary(
                $game->id(),
                $game->homeTeam(),
                $game->awayTeam(),
                $game->homeScore(),
                $game->awayScore(),
            ),
            array_values($this->games),
        );
    }

    private function activeGame(int $gameId): Game
    {
        if (!isset($this->games[$gameId])) {
            throw new OutOfBoundsException('Game does not exist.');
        }

        return $this->games[$gameId];
    }

    private function assertTeamsAvailable(string $homeKey, string $awayKey): void
    {
        foreach ($this->games as $game) {
            $activeKeys = [$this->teamKey($game->homeTeam()), $this->teamKey($game->awayTeam())];

            if (in_array($homeKey, $activeKeys, true) || in_array($awayKey, $activeKeys, true)) {
                throw new DomainException('Team is already playing an active game.');
            }
        }
    }

    private function teamKey(string $displayName): string
    {
        return mb_strtolower($displayName, 'UTF-8');
    }
}
