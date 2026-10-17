<?php

declare(strict_types=1);

namespace WorldCup;

use InvalidArgumentException;

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

        if ($this->teamKey($homeTeam) === $this->teamKey($awayTeam)) {
            throw new InvalidArgumentException('Teams must be different.');
        }

        $id = $this->nextId;
        $this->games[$id] = new Game($id, $homeTeam, $awayTeam);
        $this->nextId++;

        return $id;
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

    private function teamKey(string $displayName): string
    {
        return mb_strtolower($displayName, 'UTF-8');
    }
}
