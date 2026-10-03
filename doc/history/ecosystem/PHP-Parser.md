# Ecosystem repository: PHP-Parser

**Group:** Framework forks. **Period in the ledger:** 2026-05-11 to 2026-05-11. **Changes:** 2 (2 made by the se7enxweb team, 0 upstream history carried by the fork).

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

## Where to read more

- [Framework forks](../../features/6.0/platform-php85-framework-forks.md)
- [Package map](../../specifications/6.0/platform-package-map.md)
- [Upgrade notes](../../bc/6.0/platform-package-forks-and-command-renames.md)

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

## Full record

- Every change with date, kind, size and release tag: [ledger of PHP-Parser](../ledger/PHP-Parser.md).
- Overview of all platform repositories: [Ecosystem](../ecosystem.md).

<!-- rev2-see-also:start -->
## See also

- [Ecosystem overview](../ecosystem.md)
- [Complete ledger of this repository](../ledger/PHP-Parser.md)
- [Framework forks on PHP 8.5](../../features/6.0/platform-php85-framework-forks.md)
- Platform ecosystem by month: [2026-05](months/2026-05.md)

<!-- rev2-see-also:end -->
