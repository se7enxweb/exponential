---
title: CI and reproducing a red check
audience: php-developer, developer
category: php-development
order: 120
summary: The continuous integration of the Exponential repository from a PHP developer's side - the Quality, PHPUnit, Performance and CI health workflows, what blocks, how a pull request is tested, and the local command for each failing step.
keywords: CI, continuous integration, GitHub Actions, quality.yml, phpunit.yml, performance.yml, ci-health.yml, php80 job, red check, gh run view, log-failed, reproduce failure, COMPOSER_ROOT_VERSION, database tests, PHPUnit matrix
type: help
---
# CI and reproducing a red check

This page is for PHP developers whose commit or pull request to `se7enxweb/exponential` got a red check. It explains
what runs, what blocks, and gives the command that reproduces each failing step on your machine. The full reference is
[Continuous integration: the workflows](../../bc/6.0/ci.md).

## Contents

1. [The workflows](#the-workflows)
2. [Find the failed step](#find-the-failed-step)
3. [Reproduce it locally](#reproduce-it-locally)
4. [Why CI and your machine disagree](#why-ci-and-your-machine-disagree)
5. [Pull requests](#pull-requests)

## The workflows

| Workflow | Runs | Jobs | Blocks |
|---|---|---|---|
| Quality (`quality.yml`) | every push to `main` and every pull request | syntax, manifest, commit messages, code style (PHP 8.3); PHPStan (8.3); code style sniff tests (8.4); PHP 8.0 syntax and boot | yes; code style is a warning, the manifest a warning on pull requests |
| PHPUnit (`phpunit.yml`) | every push and pull request | PHPUnit without a database on PHP 8.1 to 8.5; database tests on MySQL 8.0 and PostgreSQL 15 (PHP 8.3) | yes |
| Performance (`performance.yml`) | changes to PHP, templates, settings or the benchmark | micro benchmark (PHP 8.5) against `tests/benchmark/baseline.json` | no |
| CI health (`ci-health.yml`) | Mondays, and by hand | a report of pass rates and flaky tests | no |

## Find the failed step

```bash
gh run list -R se7enxweb/exponential --limit 20
gh run view <run id> -R se7enxweb/exponential --log-failed      # only the failed steps' log
gh run rerun <run id> -R se7enxweb/exponential --failed         # run only the failed jobs again
```

## Reproduce it locally

Run from the root of a checkout of the same commit; `BASE` is `origin/main` for a pull request.

| Failed step | Command |
|---|---|
| PHP syntax of changed files | `git diff --name-only --diff-filter=ACMR $BASE HEAD -- '*.php' \| xargs -r -n1 php -l` |
| JavaScript syntax | `git diff --name-only --diff-filter=ACMR $BASE HEAD -- '*.js' ':!*.min.js' \| xargs -r -n1 node --check` |
| File manifest | `php bin/php/checkmanifest.php --all`, fix with `make manifest-fix` |
| Commit messages | `sh .githooks/check-commit-range $BASE HEAD` |
| Code style | `make phpcs BASE=$BASE` |
| PHPStan | `make devtools` once, then `make phpstan` |
| Code style sniff tests | `vendor/bin/phpunit --testsuite quality --no-coverage` (PHP 8.4) |
| PHP 8.0 syntax of every file | `make lint80-all PHP80=/path/to/php8.0` |
| PHP 8.0 production install | in a clean checkout with PHP 8.0: `composer install --no-dev --prefer-dist` |
| Boot smoke test | `php bin/php/ezexec.php tests/bin/bootsmoke.php` with PHP 8.0 |
| PHPUnit | `php vendor/bin/phpunit <the failed test file>`, then `php vendor/bin/phpunit` |
| Database tests | `php -d date.timezone=America/Los_Angeles tests/runtests.php --dsn mysql://user@127.0.0.1/testdb --db-per-test` against a throwaway database |
| Micro benchmark | `php bin/php/benchmark.php micro --baseline=tests/benchmark/baseline.json --all-checks` with OPcache off |

Never run CI's PHPUnit dependency step (`composer update` on a copied `composer.json`) inside an installed site: it
rewrites `vendor/`.

## Why CI and your machine disagree

| Situation | Usual cause | What to do |
|---|---|---|
| a test passes locally, fails in CI | the test reads something of your installation: `settings/override`, a database, a cache in `var/` | run it in a fresh clone with only `vendor/` installed |
| a test passes alone, fails in the whole run | shared state with another test: a static cache, an INI value set in-process, a file under `var/` | run the two files together; reset the state in `tearDown()` |
| every PHPUnit leg fails at install | a dependency release changed the resolution, or a repinned package is not on Packagist yet | wait for the release on Packagist; check the constraint |
| only the PHP 8.1 leg fails | an API of PHPUnit 10.5, or PHP 8.1 behavior | read the leg's log; avoid newer PHPUnit APIs |
| only `php80` fails | syntax or a function newer than 8.0, or a dependency that needs 8.1 | [Writing code that runs on PHP 8.0](writing-code-that-runs-on-php-8-0.md) |

The jobs set `COMPOSER_ROOT_VERSION=dev-main` because a pull request is checked out as a detached merge commit, where
Composer would otherwise name the root package `dev-<sha>` and packages requiring `se7enxweb/exponential: ^6.0.14`
could not be installed.

## Pull requests

- A pull request is tested **as merged into `main`**. A branch that was green can turn red when `main` moves: merge
  `main` into the branch and push again.
- A pull request that conflicts with `main` gets **no checks at all**; resolve the conflict.
- A new push to the branch **cancels** the run it replaces; a cancelled run is not a failure.
- Composer installs are tried up to three times; every job has a 20 or 30 minute timeout.

## Related pages

- [Continuous integration specification](../../specifications/6.0/continuous-integration.md)
- [Git hooks and make targets](git-hooks-and-make-targets.md)
- [Database and live tests](database-and-live-tests.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
