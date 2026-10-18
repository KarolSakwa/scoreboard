<?php

declare(strict_types=1);

namespace WorldCup;

final class Game
{
    private int $homeScore = 0;

    private int $awayScore = 0;

    public function __construct(
        private readonly int $id,
        private readonly string $homeTeam,
        private readonly string $awayTeam,
    ) {
    }

    public function id(): int
    {
        return $this->id;
    }

    public function homeTeam(): string
    {
        return $this->homeTeam;
    }

    public function awayTeam(): string
    {
        return $this->awayTeam;
    }

    public function homeScore(): int
    {
        return $this->homeScore;
    }

    public function awayScore(): int
    {
        return $this->awayScore;
    }

    public function updateScore(int $homeScore, int $awayScore): void
    {
        $this->homeScore = $homeScore;
        $this->awayScore = $awayScore;
    }
}
