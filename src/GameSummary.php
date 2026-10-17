<?php

declare(strict_types=1);

namespace WorldCup;

final readonly class GameSummary
{
    public function __construct(
        public int $id,
        public string $homeTeam,
        public string $awayTeam,
        public int $homeScore,
        public int $awayScore,
    ) {
    }
}
