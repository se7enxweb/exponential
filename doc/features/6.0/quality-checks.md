# Quality checks: git hooks, file manifest, code style and PHPStan

This page is for developers who commit to Exponential. Since October 2026 the repository checks every change the same
way on the developer machine (git hooks) and in CI (the Quality workflow): PHP syntax, the file manifest
`share/filelist.md5`, the commit message, the code style of changed files and static analysis with PHPStan. No existing
code was changed or reformatted for this; the checks look at what a change adds.

## Set it up

```sh
make devtools   # PHP_CodeSniffer and PHPStan into .devtools/ (composer.dev.json), never into vendor/
make hooks      # git config core.hooksPath .githooks
```

`make devtools` is optional: without it the hooks still check syntax, the manifest and the commit message, and skip
the code style and PHPStan with a hint.

## What runs when

| When | Check | Refuses |
|---|---|---|
| `git commit` (pre-commit) | `php -l` on every staged PHP file | a syntax error |
| `git commit` (pre-commit) | `php -l` with PHP 8.0, when `EXP_PHP80` names it, `php8.0` or `php80` is on the PATH, or `/opt/plesk/php/8.0/bin/php` exists | syntax of PHP 8.1 and later: enums, readonly properties, `never`, `new` in initializers, first-class callables, ... |
| `git commit` (pre-commit) | `php bin/php/checkmanifest.php --staged` | a staged file whose checksum is missing or stale in `share/filelist.md5` |
| `git commit` (pre-commit) | `php bin/php/checkcodestyle.php --staged` | a change that adds code style violations to a PHP file |
| `git commit` (commit-msg) | subject format | a subject that does not start with `Added: `, `Updated: `, `Fixed: ` or `Removed: ` |
| `git push` (pre-push) | PHPStan against `phpstan-baseline.neon` | a finding that is not in the baseline |

Skip the hooks in an emergency with `git commit --no-verify` or `git push --no-verify`; CI runs the same checks.

## The make targets

| Target | Does |
|---|---|
| `make check` | lint, manifest-check, phpcs and phpstan, as CI does |
| `make lint` | `php -l` on the PHP files changed since `origin/main` (`BASE=...` to compare with another ref) |
| `make lint80` | the same with PHP 8.0 (`PHP80=/path/to/php` or `EXP_PHP80`; found as in the pre-commit hook) |
| `make lint80-all` | PHP 8.0 syntax of every PHP file in git, as the CI job `php80` runs it (about 25 s) |
| `make manifest-check` | every entry of `share/filelist.md5` exists and matches; git files missing from it are warnings |
| `make manifest-fix` | rewrites stale checksums, adds missing files at their sorted place, drops entries of removed files |
| `make phpcs` | code style of the PHP files changed since `origin/main`; only new violations fail |
| `make phpstan` | PHPStan; only findings missing from the baseline fail |
| `make phpstan-baseline` | accepts all current findings as the new baseline |

## The file manifest

`share/filelist.md5` lists `md5  path` for the files of the distribution; the system upgrade check of the admin
interface compares the files against it, so a stale entry shows up there as a modified file. After changing or adding a
file, run `make manifest-fix` and commit the manifest with the change. `var/`, `extension/ezoe/` and the manifest
itself are outside it.

## Code style

`phpcs.xml.dist` describes the Exponential 6.0.15 style: `array( ... )` instead of `[ ... ]`, one space inside the
parentheses of control structures, calls and function declarations (`if ( $a )`, `foo( $a, $b )`), the opening
brace of a function on its own line, `!$a` without a space, lower case keywords and constants, Unix line endings,
spaces instead of tabs and no trailing spaces, and no functions removed from PHP. Each rule was measured against the
existing code before it was added.

Two more parts of the style:

- **No type declarations of PHP 7 and later.** The sniff `bin/phpcs/Exponential/Sniffs/TypeDeclarations/` reports
  `declare( strict_types=1 )`, scalar, pseudo and union parameter types (`int $a`, `string|array $a`), return types,
  property types and constant types. The parameter hints PHP 5 already had stay allowed: `array`, `callable`, `self`
  and class names, also nullable (`?array`, `?eZINI`), as the kernel uses them throughout. Files under `tests/` may
  declare the `: void` PHPUnit requires. Its test is `tests/tests/bin/phpcs/NoTypeDeclarationsSniffTest.php`
  (`php vendor/bin/phpunit --testsuite quality`).
- **PHP 8.0 compatibility.** PHPCompatibility (installed with the development tools) checks for `testVersion`
  `8.0-`: no syntax or functions newer than PHP 8.0 (`array_is_list()`, `fsync()`, `fputcsv()`'s `$eol`, enums,
  `never`, readonly, `json_validate()`, ...), and no functions that later versions removed or deprecate. A newer
  function is fine behind `function_exists()`, or defined in `lib/phpcompat.php` when it can be written in PHP; see
  [PHP 8.0 support](../../bc/6.0/php-8.0-support.md). `composer.dev.json` resolves the tools for PHP 8.0.30.

Old files do not follow every rule, so the check compares the number of violations of each changed file with its
previous version and fails only when a change adds violations. In CI the code style is a warning for now.

## PHPStan

`phpstan.neon.dist` analyses `kernel/` and `lib/` at level 1. `phpstan-baseline.neon` holds the 3033 findings the code
had when the check was introduced; only new findings fail. Two paths are left out and noted in the configuration: the
command line scripts in `kernel/private/classes/commands/` (they declare global helper functions) and
`dfs_filter_iterator.php` (its iterator methods need tentative return types first). Raise the level or shrink the
baseline in small separate changes, and regenerate it with `make phpstan-baseline`.

## CI

`.github/workflows/quality.yml` runs on every push and pull request to `main`: PHP syntax of the changed files with
PHP 8.3, the manifest, the commit messages of the pushed or proposed commits, PHPStan and the tests of the own sniffs
(their own job, on PHP 8.4) block; the code style of the changed files, including type declarations and PHP 8.0
compatibility, is reported as a warning.

The job `php80` is the check for the oldest supported version, PHP 8.0 (the stock PHP of RHEL 9), in one place:

1. `make lint80-all`: `php -l` with PHP 8.0 on every PHP file in git (blocking).
2. `composer install --no-dev` with PHP 8.0. It passes `--ignore-platform-req=php`, because `composer.json` and
   some se7enxweb packages still declare 8.1 (Velocity needs 8.1 for its own server); what the job tests is that the
   code runs on 8.0.
3. `php bin/php/ezexec.php tests/bin/bootsmoke.php`: boots the kernel without a database, loads every class of the
   kernel and extension autoload arrays, checks `lib/phpcompat.php` and renders a template with `wash`. A warning
   until syndication and ezstarrating are released with their fixed declarations, which no PHP 8 can load today.

The pre-commit hook and `make lint80` run the same syntax check on changed files when a PHP 8.0 binary is available.
