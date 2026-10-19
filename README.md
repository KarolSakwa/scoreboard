# Football World Cup Score Board

A small PHP library for tracking World Cup matches in progress. All data is kept in memory.

## Requirements

- PHP 8.5
- `ext-mbstring`

## Setup

Install dependencies:

```bash
composer install
```

Run all checks:

```bash
composer check
```

You can also run them separately with `composer test`, `composer phpstan` and `composer cs`.

## Usage

The `Scoreboard` class provides four methods: `startGame()`, `updateScore()`, `finishGame()` and `getSummary()`.

The `example.php` file contains the five-game scenario from the assignment. Run it from the project root:

```bash
php example.php
```

Expected output:

```text
Uruguay 6 - Italy 6
Spain 10 - Brazil 2
Mexico 0 - Canada 5
Argentina 3 - Australia 1
Germany 2 - France 2
```

Games are sorted by total goals, highest first. Ties are broken by start order, with the most recently started game first.

## Implementation notes

`Scoreboard` manages the active games. `Game` holds the mutable state, while `GameSummary` provides an immutable snapshot.

- Game IDs start at 1 and are never reused.
- `updateScore()` replaces the current score rather than adding goals.
- Team names are trimmed and compared case-insensitively using `mb_strtolower()`. No aliases or additional Unicode normalization are applied.
- A team can only play in one active game at a time.
- Finished games are removed, no match history is stored.

## Errors

| Situation | Exception |
|---|---|
| Empty team name or identical teams | `InvalidArgumentException` |
| Team already playing | `DomainException` |
| Negative score | `InvalidArgumentException` |
| Unknown or finished game ID | `OutOfBoundsException` |

Invalid operations leave the scoreboard unchanged.