# PHP-Parser: platform repository history

The history of `PHP-Parser`, one of the platform repositories around Exponential (group: Framework forks). Read it to learn what the repository gives you, how it relates to Exponential and when it changed. The ledger records 2 changes from 2026-05-11 to 2026-05-11, all made by the se7enxweb team.

## What it is

Fork of the PHP-Parser 0.9 line (replaces nikic/php-parser 0.9.5).

## How it relates to Exponential

Adds PHP 8 token compatibility for the old stack.

## What a user gets

Old tooling parses PHP 8 code.

Install it with Composer (a project that already requires the platform pulls it in by itself):

```bash
composer require se7enxweb/php-parser
```

## Counts by kind

| Kind | Changes |
|---|---|
| Fixes | 1 |
| Behaviour and upgrade changes | 1 |

## Changes made by the se7enxweb team, by theme

### PHP 8.x compatibility (1)

- 2026-05-11 `0fdb2d5a` fix: Add PHP 8 token compatibility for 0.9.x (se7enxweb fork)

### Replace declarations for the upstream package (1)

- 2026-05-11 `83cf9a0f` bc: Add replace directive for nikic/php-parser 0.9.5 in composer.json

## Related pages

- [Framework forks](../../features/6.0/platform-php85-framework-forks.md)
- [Package map](../../specifications/6.0/platform-package-map.md)
- [Upgrade notes](../../bc/6.0/platform-package-forks-and-command-renames.md)
- [Ecosystem overview](../ecosystem.md)
- [Complete ledger of this repository](../ledger/PHP-Parser.md)
- Platform ecosystem by month: [2026-05](months/2026-05.md)
