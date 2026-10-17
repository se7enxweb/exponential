# Continuous integration: the workflows, reproducing a failure, the commit message policy

Read this page if a check on a commit or a pull request of `se7enxweb/exponential` is red, if you change one of the
workflows in `.github/workflows/`, or if you want to know how healthy CI has been lately. The checks themselves (git
hooks, file manifest, code style, PHPStan, the tests of a change) are described in
[doc/features/6.0/quality-checks.md](../../features/6.0/quality-checks.md); this page is the map around them.

## In short

| | |
|---|---|
| What runs | **Quality** and **PHPUnit** on every push and pull request, **Performance** when PHP, templates or settings change, and **CI health** once a week. |
| What blocks | Quality and PHPUnit. Performance is a warning only, and CI health only reports. |
| A red check | Open the run, find the failed step in the table below, and run that step's command on your machine. `gh run view <id> --log-failed` prints only the failed steps' log. |
| A contributor's commit | Kept as written. A subject outside `Added:`/`Updated:`/`Fixed:`/`Removed:`/`Renamed:` is a warning for a contributor, and an error only for the project's own authors. |
| How healthy is CI | `python3 .github/scripts/ci_health_report.py --days 14`, or the weekly **CI health** run's summary. |

## The workflows

| Workflow | Runs on | Jobs | Blocks |
|---|---|---|---|
| `quality.yml` (Quality) | every push to `main` and every pull request to it, and by hand | **Syntax, manifest, commit messages, code style** (PHP 8.3, Node.js 22), **PHPStan** (PHP 8.3), **Code style sniff tests** (PHP 8.4), **PHP 8.0 (oldest supported) syntax and boot** | yes; code style is a warning, and the file manifest is a warning on a pull request (blocking on a push to `main`) |
| `phpunit.yml` (PHPUnit) | every push to `main` and every pull request to it (no paths filter: the tests also read templates, stylesheets, scripts, translations, settings and documentation), and by hand | **PHPUnit (PHP 8.1 to 8.5)**, a matrix without a database; **Database tests** on MySQL 8.0 and PostgreSQL 15 (PHP 8.3) | yes |
| `performance.yml` (Performance) | a push or pull request that changes PHP, templates, `settings/`, the German translation or `tests/benchmark/` | **Micro benchmark (PHP 8.5)** against `tests/benchmark/baseline.json`; a probe over the threshold is measured once more and only the second run counts | no (`continue-on-error`) |
| `ci-health.yml` (CI health) | Mondays 06:17 UTC, and by hand | **CI health report** | no; it only reports |

These things hold for all of them:

- **The root package is `dev-main`.** The workflows set `COMPOSER_ROOT_VERSION=dev-main`, because a pull request is
  checked out as a detached merge commit, where Composer would call the root package `dev-<sha>` and no package that
  requires `se7enxweb/exponential: ^6.0.14` (expai, expqueue) could be installed.
- **PHPUnit resolves its dependencies fresh on every run.** The install step copies `composer.json` to
  `composer.ci-phpunit.json`, sets the PHPUnit version for the leg (10.5 on PHP 8.1, 11.5 on 8.2 and 8.3, the
  version of `composer.json` from 8.4 on), adds `mongodb/mongodb`, and runs `composer update` on that copy. A
  package release that breaks the resolution therefore breaks the install step of every leg at once, without a
  change in this repository.
- **A pull request is tested as merged into `main`.** The checks see `main` plus the branch, so a branch that was
  green can turn red when `main` moves. Merge `main` into the branch and push again.
- **Only a superseded pull request run is cancelled.** Quality, PHPUnit and Performance have one concurrency group
  per pull request, and a new push to the branch cancels the run it replaces. A push to `main` gets a group of its
  own commit, so a later push never cancels it halfway and every commit on `main` keeps a complete result. A
  cancelled run on a pull request is therefore not a failure; look at the newest run of the branch.
- **Network steps are retried, hung jobs are stopped.** Every Composer install or update is tried up to three times,
  15 and 30 seconds apart. The PHP 8.0 production install retries only a network error: a requirement PHP 8.0 cannot
  meet fails at once, because that is what the step checks. Composer downloads are cached per PHP version. Every job
  has a timeout (20 or 30 minutes), so a hung job fails instead of blocking the queue for six hours.

## Reproduce a failure on your machine

Find the failed run and step first:

```sh
gh run list -R se7enxweb/exponential --limit 20                 # the latest runs and their conclusion
gh run view <run id> -R se7enxweb/exponential --log-failed      # only the log of the failed steps
gh run rerun <run id> -R se7enxweb/exponential --failed         # run only the failed jobs again
```

Then run the step's command from the root of a checkout of the same commit. `BASE` is the commit before the push,
or `origin/main` for a pull request.

| Failed step | Command |
|---|---|
| PHP syntax of changed files | `git diff --name-only --diff-filter=ACMR $BASE HEAD -- '*.php' \| xargs -r -n1 php -l` |
| JavaScript syntax of changed files | `git diff --name-only --diff-filter=ACMR $BASE HEAD -- '*.js' ':!*.min.js' \| xargs -r -n1 node --check` (Node.js 22 or later) |
| File manifest (share/filelist.md5) | `php bin/php/checkmanifest.php --all`; fix with `make manifest-fix` and commit `share/filelist.md5` (blocking on a push to `main`, a warning on a pull request: the person merging refreshes it) |
| Commit messages | `sh .githooks/check-commit-range $BASE HEAD` (see the policy below) |
| Code style of changed files | `make phpcs BASE=$BASE` (a warning in CI) |
| PHPStan | `make devtools` once, then `make phpstan`; only findings missing from `phpstan-baseline.neon` fail |
| Code style sniff tests | `vendor/bin/phpunit --testsuite quality --no-coverage` (PHP 8.4) |
| PHP 8.0 syntax of every PHP file | `make lint80-all PHP80=/path/to/php8.0` |
| Install the production dependencies with PHP 8.0 | in a clean checkout, with PHP 8.0: `composer install --no-dev --prefer-dist`. A failure names the package whose PHP requirement is too high. |
| Boot smoke test | `php bin/php/ezexec.php tests/bin/bootsmoke.php` with PHP 8.0 |
| Install dependencies (PHPUnit) | in a clean checkout: copy `composer.json` to `composer.ci-phpunit.json`, set `require-dev` as the workflow does, and run `COMPOSER=composer.ci-phpunit.json COMPOSER_ROOT_VERSION=dev-main composer update --with-all-dependencies --ignore-platform-req=php --ignore-platform-req=ext-mongodb`. Never in an installed site: it rewrites `vendor/`. |
| Run PHPUnit | `php vendor/bin/phpunit <the failed test file>` first, then the whole run, `php vendor/bin/phpunit` |
| Run database tests | `php -d date.timezone=America/Los_Angeles tests/runtests.php --dsn mysql://user@127.0.0.1/testdb --db-per-test` against a throwaway database |
| Micro benchmark | `php bin/php/benchmark.php micro --baseline=tests/benchmark/baseline.json --all-checks` with OPcache off for the CLI |

The PHPUnit jobs have no installed site: no `settings/override`, no database, no `var/` caches. A test that passes on
a development installation and fails in CI usually reads something from that installation. Run it the way CI does,
in a fresh clone with only `vendor/` installed, before deciding that CI is wrong. A test that passes alone and fails
in the whole run shares state with another test (a static cache, an INI value set in-process, a file under `var/`);
run the two files together to find the pair.

## The local gates and CI

The team's push gate runs the checks of `.github/workflows/quality.yml` and the DB-less job of
`phpunit.yml` against a committed revision, in a clean git worktree of it:

| Mode | What runs | When |
|------|-----------|------|
| `--fast` | syntax of the changed PHP files (PHP 8.3 as CI, and PHP 8.0), `node --check` of the changed scripts (node 22), commit messages (the same `.githooks/check-commit-range` CI runs), the file manifest, the code style of the changed files (warning only, as in CI), PHPStan, and the PHPUnit test files of the change | every commit and push |
| default | as `--fast`, with the whole PHPUnit run instead of the changed tests | larger pushes |
| `--ci` | the default set plus PHP 8.0 syntax of every file and the PHP 8.0 production install with the boot smoke test (CI's `php80` job) | before a release |

How it matches CI:

- **Extensions.** The clean tree gets every `se7enxweb/*` extension `composer.json` requires at the release CI's
  `composer update` installs (the highest Packagist version inside the constraint), not a developer's checkout. A
  checkout carrying unreleased work made a kernel test pass locally and fail in CI on 2026-10-16.
- **Environment.** PHPUnit runs with `CI=1`, as GitHub sets it, and with node on `PATH`: the design tests that run
  admin JavaScript, and the check that every shipped extension is present, fail instead of skipping.
- **Test selection in `--fast`.** A changed file runs the tests that map to it (`tests/test-map.json`), the tests
  named after its classes, the tests that name its classes, path or changed INI settings, and the tests that walk a
  directory the file is in and check files of its kind (every admin4 template, every `translation.ts`). The last
  rule closes the gap that let a template's inline script tag and a translation's markup through on 2026-10-16/17.

Differences that remain, and why:

- **One PHP version for PHPUnit.** CI runs PHPUnit on PHP 8.1 to 8.5, with PHPUnit 10.5 (8.1), 11.5 (8.2, 8.3) and
  13 (8.4, 8.5) resolved fresh each run. The local gate runs PHP 8.5 against the installation's `vendor/` (PHPUnit
  13, which needs PHP 8.4). A test that only fails on an old PHPUnit's API, or on PHP 8.1 to 8.3 behaviour, is found
  in CI only. Syntax is checked with 8.0 and 8.3 locally, so a parse error is not one of them.
- **`vendor/` is the installation's, not a fresh resolve.** CI runs `composer update` each time; the local gate
  copies the installed `vendor/`. A newer dependency release can change CI's result between two runs of the same
  commit. `--ci` does a fresh `composer install --no-dev` with PHP 8.0 for the boot test.
- **PHPStan** runs on PHP 8.3 as in CI. The installed `vendor/` asks for PHP 8.4, so the gate switches off its
  Composer platform check in the clean tree for the PHPStan run only.
- **No database jobs.** CI's MySQL and PostgreSQL jobs (`tests/runtests.php --dsn ...`) are not run locally; the
  DB-less run covers the same test files except those that need a database.
- **Performance** (the micro benchmark) is warning only in CI and is not run by the gate.
- **The commit range on push.** CI checks `before..sha` of the push; the gate checks `<base>..<rev>` with
  `<base>` = `origin/main` after a fetch, which is the same range for a fast-forward push.

## The commit message policy

Every commit of the project starts with what it does: `Added: `, `Updated: `, `Fixed: `, `Removed: ` or `Renamed: `
(`.githooks/commit-msg`; git's own `Merge`, `Revert`, `fixup!` and `squash!` subjects pass too). The Quality step
**Commit messages** checks the commits of the push or the pull request with `.githooks/check-commit-range`:

- A commit by a **project author**, an e-mail address listed in `.githooks/commit-msg-authors`, must follow the
  format. A bad subject fails the check.
- A commit by **anyone else** is a contributor's commit. Every pull request is merged, and its commits are kept as
  the contributor wrote them and never rewritten, so a subject such as `Changed: ...` or `Fix typo` is a warning
  annotation and never fails the check: neither on the pull request nor on the push of `main` that carries the merge.
- Merge commits are not checked.
- `EXP_COMMIT_MSG_STRICT_ALL=1 sh .githooks/check-commit-range origin/main HEAD` checks every commit as a project
  commit, for a contributor who wants their branch to follow the format.

`make hooks` installs the hook that checks each message at `git commit`. The details are in
[quality-checks.md](../../features/6.0/quality-checks.md), "Pull requests".

## How healthy CI is: the report

`.github/scripts/ci_health_report.py` reads the runs of the last N days through the `gh` CLI and prints:

- the **pass rate** per repository, and per workflow and event (`push`, `pull_request`, `schedule`,
  `workflow_dispatch`): successes divided by successes plus failures, on the last attempt of each run. Cancelled and
  skipped runs are counted but left out of the rate.
- the **steps that fail most**, as failed jobs: each leg of a matrix counts once, so one PHPUnit failure on five PHP
  versions counts five.
- the **tests that fail most**, read from the PHPUnit lists of failures and errors in the logs of failed test jobs
  (flaky runs first, then the newest, up to `--max-logs`).
- the **flaky runs**: a run that passed on a rerun after a failed attempt, or a failed run whose commit passed in
  another run of the same workflow and event. A test that failed in such a run is counted as flaky.

```sh
python3 .github/scripts/ci_health_report.py                         # this repository, last 14 days
python3 .github/scripts/ci_health_report.py --days 7 --md ci.md     # also a Markdown file
python3 .github/scripts/ci_health_report.py --org se7enxweb --json ci.json   # every repository pushed to lately
python3 .github/scripts/ci_health_report.py --fail-under 90 --min-runs 5     # exit 1 below 90 % in any group
```

It needs Python 3.8 or later and `gh` logged in (or `GH_TOKEN`). It uses the GitHub API: about one call per
repository, one per failed or rerun run and one per log read. The hourly limit is 5,000 calls, shared by everything
that uses the same token. `--org` takes only the repositories pushed to in the last N+60 days (GitHub stops scheduled
workflows after 60 days without activity); `--all-repos` takes all of them, at one call each. A rate limit stops the
report with a message instead of counting the repositories as having no runs. Jobs and logs of finished runs are
cached in `--cache-dir` (`var/tmp/ci_health_cache` when `var/tmp` exists), so a second report costs only the run
lists.

The **CI health** workflow runs the report every Monday for the last 7 days and writes it to the log and the job
summary, and keeps the Markdown and JSON as the artifact `ci-health` for 90 days. Run it by hand from the Actions tab
to choose the days, or to add every repository of an owner.

### The baseline (2026-10-17)

The first report, for 2026-10-03 to 2026-10-17, before the CI repairs of that week: 1,540 finished runs in this
repository, 61.7 % passed. By workflow and event: PHPUnit on push 57.0 %, on pull requests 30.4 %; Quality on push
63.7 %, on pull requests 27.2 %; Performance on push 100 % (11 cancelled by newer pushes), on pull requests 77.7 %.
The failed jobs were about evenly split between dependency installs (PHPUnit, Database tests, PHP 8.0, PHPStan and
the sniff tests) and real test failures. The tests that failed most were the translation quality, packer, admin
content security policy and admin tab order tests. Six runs passed on a rerun. Compare later reports with these
figures.
