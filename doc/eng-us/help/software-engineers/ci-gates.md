---
title: CI gates
audience: software-engineer, developer
category: software-engineering
order: 530
summary: The continuous integration of Exponential as a set of gates - the Quality, PHPUnit, Performance and CI health workflows, what blocks and what only warns, how pull requests are tested as merged into main, how to reproduce a red step on your machine, the local gates that mirror CI, and the differences that remain.
keywords: CI, continuous integration, GitHub Actions, quality.yml, phpunit.yml, performance.yml, ci-health.yml, gates, red check, reproduce, pull request, merge, flaky tests, Packagist check, software engineer
type: help
---
# CI gates

This page is for engineers whose change is red in CI, who review a pull request, or who change the workflows. It
summarizes [Continuous integration](../../bc/6.0/ci.md), which has the full table of steps and commands.

## Contents

1. [The workflows](#the-workflows)
2. [Properties every engineer should know](#properties-every-engineer-should-know)
3. [Reproducing a red step](#reproducing-a-red-step)
4. [The local gates](#the-local-gates)
5. [Watching CI health](#watching-ci-health)
6. [Known gaps](#known-gaps)

## The workflows

| Workflow | Runs on | What | Blocks |
|---|---|---|---|
| Quality (`quality.yml`) | every push to `main` and every pull request to it | syntax of changed PHP and JavaScript, file manifest, commit messages, code style, PHPStan, sniff tests, PHP 8.0 syntax, install and boot | yes; code style warns, the manifest warns on a pull request |
| PHPUnit (`phpunit.yml`) | every push and pull request, no paths filter | DB-less PHPUnit on PHP 8.1 to 8.5; database tests on MySQL 8.0 and PostgreSQL 15 (PHP 8.3) | yes |
| Performance (`performance.yml`) | changes to PHP, templates, settings, the German translation or the benchmark | the micro benchmark against `tests/benchmark/baseline.json` | no |
| CI health (`ci-health.yml`) | Mondays, and by hand | a report of pass rates, failing steps, failing and flaky tests | no |

## Properties every engineer should know

- **A pull request is tested as merged into `main`.** A green branch can turn red when `main` moves; merge `main` into
  the branch and push again. A pull request that conflicts with `main` gets no checks at all.
- **Dependencies are resolved fresh** in the PHPUnit jobs, so a package release can change a result without a change
  in the repository.
- **The root package is `dev-main`** (`COMPOSER_ROOT_VERSION=dev-main`), so packages that require
  `se7enxweb/exponential` with a constraint resolve on a pull request.
- **Only a superseded run is cancelled.** A cancelled run on a pull request is not a failure; look at the newest run.
- **Jobs have timeouts** and network steps retry, so a hung job fails instead of blocking the queue.
- **The jobs have no installed site**: no `settings/override`, no database, no `var/` caches.

## Reproducing a red step

```sh
gh run list -R se7enxweb/exponential --limit 20
gh run view <run id> -R se7enxweb/exponential --log-failed
```

Then run the step's command from a checkout of the same commit:

| Failed step | Command |
|---|---|
| PHP syntax | `php -l` on the changed files |
| File manifest | `php bin/php/checkmanifest.php --all`; fix with `make manifest-fix` |
| Commit messages | `sh .githooks/check-commit-range $BASE HEAD` |
| Code style | `make phpcs BASE=$BASE` |
| PHPStan | `make devtools` once, then `make phpstan` |
| Sniff tests | `vendor/bin/phpunit --testsuite quality --no-coverage` |
| PHP 8.0 syntax | `make lint80-all PHP80=/path/to/php8.0` |
| Boot smoke test | `php bin/php/ezexec.php tests/bin/bootsmoke.php` with PHP 8.0 |
| PHPUnit | `php vendor/bin/phpunit <the failed test file>`, then `php vendor/bin/phpunit` |
| Database tests | `php -d date.timezone=America/Los_Angeles tests/runtests.php --dsn mysql://user@127.0.0.1/testdb --db-per-test` |
| Micro benchmark | `php bin/php/benchmark.php micro --baseline=tests/benchmark/baseline.json --all-checks` |

A test that passes on your installation and fails in CI usually reads something from your installation. Run it in a
fresh clone with only `vendor/` installed before deciding that CI is wrong.

## The local gates

The team's push gate runs the Quality checks and the DB-less PHPUnit job against a committed revision in a clean
worktree, in three modes: `--fast` (the tests of the change), default (the whole DB-less run) and `--ci` (plus PHP 8.0
syntax of every file and the PHP 8.0 install with the boot test). It also checks that every changed `se7enxweb/*`
constraint in `composer.json` matches a release Packagist already lists, because a repin to an unpublished version
breaks every CI leg at once. The commands it runs are all in the repository; contributors run them as plain commands
([Quality checks](../../features/6.0/quality-checks.md)).

Differences that remain: the local gate runs PHPUnit on one PHP version against the installed `vendor/`, and does not
run the database jobs or the benchmark.

## Watching CI health

```sh
python3 .github/scripts/ci_health_report.py --days 14
```

prints the pass rate per workflow and event, the steps and tests that fail most, and the flaky runs. A baseline from
October 2026 is recorded in the CI page, so later reports can be compared with it.

## Known gaps

- `main` has no branch protection rule, so a red check does not technically block a merge; it is policy.
- Database jobs cover MySQL and PostgreSQL only; PHPUnit does not run on PHP 8.0.
- Code style and performance are warnings only; coverage is not published.

## Related pages

- [Testing strategy](testing-strategy.md)
- [Static analysis and code style](static-analysis-and-code-style.md)
- [CI for extensions](../creators/app-creators/ci-for-extensions.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
