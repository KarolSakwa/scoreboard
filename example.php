<?php

declare(strict_types=1);

use WorldCup\Scoreboard;

require __DIR__ . '/vendor/autoload.php';

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

foreach ($scoreboard->getSummary() as $game) {
    echo $game->homeTeam, ' ', $game->homeScore, ' - ',
         $game->awayTeam, ' ', $game->awayScore, PHP_EOL;
}
