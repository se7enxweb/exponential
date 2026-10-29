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
| `git commit` (pre-commit) | `node --check` on every staged JavaScript file (not `*.min.js`), when node is installed | a syntax error |
| `git commit` (pre-commit) | `php bin/php/checkmanifest.php --staged` | a staged file whose checksum is missing or stale in `share/filelist.md5` |
| `git commit` (pre-commit) | `php bin/php/checkcodestyle.php --staged` | a change that adds code style violations to a PHP file |
| `git commit` (commit-msg) | subject format | a subject that does not start with `Added: `, `Updated: `, `Fixed: `, `Removed: ` or `Renamed: ` |
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

## Tests that need a live server

Some tests talk to a real database or web server. They read where it is from environment variables and skip with
the variable's name when it is not set, so a plain `vendor/bin/phpunit` on a machine without those servers (a CI
runner, a laptop) passes. No test carries a host name, an account or a password of its own.

| Tests | Variables |
| --- | --- |
| `expMongoDBIntegrationTest`, `sevenxMongoDBIntegrationTest` (MongoDB part) | `EXP_TEST_MONGO_URI` (a `mongodb://` connection string), `EXP_TEST_MONGO_DATABASE` (default `exp`) |
| `expMongoDBIntegrationTest`, `sevenxMongoDBIntegrationTest` (MySQL part) | `EXP_TEST_MYSQL_USER`, `EXP_TEST_MYSQL_PASSWORD`, `EXP_TEST_MYSQL_DATABASE`, `EXP_TEST_MYSQL_HOST` (default `localhost`), `EXP_TEST_MYSQL_PORT` (default 3306) |
| `expMongoDBDeclaredDefaultTest`, `TrashListPagedTest` | `EXP_TEST_MONGO_SCRATCH` (`<host>:<port>/<database>`, a database whose name contains "scratch" or "test") |
| Live HTTP tests (`ApiKeyLiveTest`, `RestContentPermissionLiveTest`, ...) | `EXP_TEST_PUBLIC_HOST` (else the host of the default siteaccess's `SiteURL`), `APIKEY_LIVE_BASES`, `APIKEY_LIVE_ADMIN_PASSWORD` |

Pass a password through the environment, not on the command line, so it does not end up in the shell history or
the process list, for example from a file only you can read:

```sh
EXP_TEST_MYSQL_PASSWORD="$(cat ~/.exp-test-mysql-password)" EXP_TEST_MYSQL_USER=test EXP_TEST_MYSQL_DATABASE=testdb \
    php vendor/bin/phpunit tests/tests/lib/ezdb/mongodb/expMongoDBIntegrationTest.php
```

A test run writes its own audit records into a directory under `var/tmp` (`tests/bootstrap.php`). A live test that
checks what a web server recorded reads the site's audit directory, `expAuditConfig::siteLogDir()`, not
`expAuditConfig::get()['logDir']`, which names the run's directory.

## CI

`.github/workflows/quality.yml` runs on every push and pull request to `main`: PHP syntax of the changed files with
PHP 8.3, JavaScript syntax of the changed scripts (`node --check` with Node.js 22, which reads an ES module in a
`.js` file), the manifest, the commit messages of the pushed or proposed commits (a contributor's commit only warns), PHPStan and the tests of the own sniffs
(their own job, on PHP 8.4) block; the code style of the changed files, including type declarations and PHP 8.0
compatibility, is reported as a warning.

The job `php80` is the check for the oldest supported version, PHP 8.0 (the stock PHP of RHEL 9), in one place:

1. `make lint80-all`: `php -l` with PHP 8.0 on every PHP file in git (blocking).
2. `composer install --no-dev` with PHP 8.0. It passes `--ignore-platform-req=php`, because `composer.json` and
   some se7enxweb packages still declare 8.1 (Velocity did until its release 0.0.4.54); what the job tests is that the
   code runs on 8.0.
3. `php bin/php/ezexec.php tests/bin/bootsmoke.php`: boots the kernel without a database, loads every class of the
   kernel and extension autoload arrays, checks `lib/phpcompat.php` and renders a template with `wash`. A warning
   until syndication and ezstarrating are released with their fixed declarations, which no PHP 8 can load today.

The pre-commit hook and `make lint80` run the same syntax check on changed files when a PHP 8.0 binary is available.

The dependency installs of the workflows resolve the root package as `dev-main`. Its branch alias
`"dev-main": "6.0.x-dev"` in `composer.json` (from 2026-10-12) lets a required package that names
`se7enxweb/exponential` with a version constraint, such as expqueue's and expai's `^6.0.14`, resolve against it;
without it the install stops with "could not be resolved". A push to `main` gets `dev-main` from the checked-out
branch. A pull request is checked out as a detached merge commit, where Composer finds no branch and calls the root
package `dev-<sha>` or `1.0.0`, so the three workflows set `COMPOSER_ROOT_VERSION=dev-main` (from 2026-10-15). Until
then every pull request failed its installs and with them PHPStan, PHPUnit, the PHP 8.0 job and the sniff tests,
and the benchmark ran nothing.

### Pull requests

A pull request gets the same blocking checks as a push to `main`, on the merge of the branch into `main`. Two of
them depend on the branch, not on the code:

- **The file manifest.** Every commit that adds, changes or removes a file updates its line in
  `share/filelist.md5`; `make hooks` makes the pre-commit hook refuse a commit that forgets it. When CI finds a stale
  line, the job prints the change to `share/filelist.md5` that makes it match, as an annotation, in the log and in
  the job summary. Run `make manifest-fix` (`php bin/php/checkmanifest.php --fix`) in the branch and commit the
  result, e.g. `Updated: The file manifest carries the checksums of ...`. On a pull request the check is a
  **warning** (a `::warning::` annotation and a note in the job summary), never a failure: it runs on the merge of
  the branch into the current `main`, so when `main` changed the same files the contributor cannot produce the
  merged checksums, and the person merging the pull request refreshes the manifest on `main` in the same push. On
  a push to `main` the check stays **blocking**: a stale line on `main` makes every site's upgrade check list the
  file as modified.
- **The commit messages.** `.githooks/check-commit-range <base> <head>` checks every commit of the push or the pull
  request; CI and the local gates run the same script. A commit by a project author (an address listed in
  `.githooks/commit-msg-authors`) starts with `Added: `, `Updated: `, `Fixed: `, `Removed: ` or `Renamed: `, or the
  check fails; reword it with `git rebase -i` before the merge. A commit by anyone else is a contributor's commit:
  every pull request is merged (its author squashes and rebases it onto `main` while it is open, and `main` is never
  rewritten after the merge), and a subject such as `Changed: ...` or `Fix typo` is a warning annotation, never a
  failure, on the pull request and later on `main`. A merge commit is
  not checked. `EXP_COMMIT_MSG_STRICT_ALL=1` makes every author strict, for a contributor who wants the project's
  check on their own branch.

When `main` changed `share/filelist.md5` since the branch started, merge `main` into the branch and run
`make manifest-fix` again rather than resolving the conflict by hand.

The PHPUnit workflow runs for every push and pull request (from 2026-10-17). Until then it ran only when a `.php`
file, `composer.json`, `composer.lock` or `phpunit.xml` changed, although the tests also read templates,
stylesheets, scripts, translations, INI files and documentation: a change to those alone ran no test, and its
failure showed up later on an unrelated commit. `tests/tests/bin/CiWorkflowPullRequestTest.php` (suite `quality`)
keeps the workflows that way: no paths filter for PHPUnit, `COMPOSER_ROOT_VERSION=dev-main` in all three, no
`pull_request_target` and no write permission, so the code of a fork never runs with a token that can change the
repository.

The first pull request of a new contributor from a fork waits for a maintainer's approval before any workflow runs
(the repository's setting "Require approval for first-time contributors"). A run that nobody approves expires and
is listed as failed with no jobs; it says nothing about the code. Approve it under Actions, or push again after the
first pull request of that contributor has been merged.

The `Database tests` jobs run every suite of `phpunit.xml` with `tests/runtests.php --dsn ... --db-per-test` and fail
on a failure or error (before 2026-10-15 the runner always exited with 0, and the job passed whatever the tests
reported; it also named the `tests` directory, which ran files that `phpunit.xml` excludes).

### Before you push

The blocking checks of the Quality workflow look at the commits of the push, so they can be run on the committed
`HEAD` before it leaves the machine:

```sh
git diff --name-only --diff-filter=AM origin/main..HEAD -- '*.php' | xargs -r -n1 php -l   # syntax of changed files
php bin/php/checkmanifest.php --all                                                        # the file manifest
sh .githooks/check-commit-range origin/main HEAD                                           # the commit messages
make phpstan                                                                               # needs make devtools
```

A failure here fails the workflow too. Code style stays a warning in CI.

### Run only the tests of a change

The whole PHPUnit run (more than 10,000 tests) takes a long time on a busy machine. For an everyday commit it is
enough to run the test files that cover the files the change touches, each in its own PHPUnit process, so that one
file's global state (a static cache, an INI value set in-process) cannot change the result of another. The full run
stays the check before a release or a large push, and CI still runs everything.

A test file belongs to a change when:

1. `tests/test-map.json` names it for a changed file. The file has three sections: `map`, hand-made entries (a path
   or a pattern where `*` also matches `/`) for code that tests reach without naming it, such as a module view that
   a test runs through the module; `generated`, the source paths that test files name literally; and `ignore`, files
   no PHPUnit test covers on purpose (documentation, translations, the manifest).
2. It is named after a class the changed file declares, or after the file itself, or it names one of those classes
   or functions, the changed file's path, or an INI setting the change adds or changes.
3. It is itself a changed test file.

A changed file that none of these finds is an untested change. That is a warning, not a failure, but it shows where
a test is missing. A change to `tests/bootstrap.php`, `tests/toolkit/` or `phpunit.xml` touches every test, so run
the whole suite for it.

With git, grep and xargs (and `jq` for the map), from the root of the installation:

```sh
BASE=origin/main
mkdir -p var/tmp
{
    # 3. changed test files
    git diff --name-only --diff-filter=AM "$BASE"..HEAD -- 'tests/tests/*Test.php'
    for f in $(git diff --name-only --diff-filter=AM "$BASE"..HEAD -- ':!tests/tests/*Test.php'); do
        # 1. the explicit map (exact paths; patterns need a look at the file)
        jq -r --arg f "$f" '(.map[$f] // []) + (.generated[$f] // []) | .[]' tests/test-map.json
        # 2. test files that name a class the changed file declares
        case "$f" in *.php)
            for c in $(sed -nE 's/^\s*((abstract|final|readonly)\s+)*(class|interface|trait|enum)\s+([A-Za-z_][A-Za-z0-9_]*).*/\4/p' "$f"); do
                grep -rliw --include='*Test.php' -- "$c" tests/tests
            done ;;
        esac
    done
} | sort -u > var/tmp/tests-of-change.txt
# one PHPUnit process per test file, three at a time, at low priority
xargs -r -P 3 -I{} sh -c 'nice -n 15 php vendor/bin/phpunit --no-coverage --do-not-cache-result "{}" \
    > /dev/null 2>&1 && echo "PASS {}" || echo "FAIL {}"' < var/tmp/tests-of-change.txt
```

Run a failing file again on its own (`php vendor/bin/phpunit <file>`) to see the failure. A test file listed here
that is outside the suites of `phpunit.xml` is not part of the full run either; leave it out. A class that most test
files use (`eZINI`, `eZUser`) finds a large part of the suite; keep the files named after the class and the ones of
the changed file's own directory. When a new test covers code that its file does not name, add an entry to `map` in
`tests/test-map.json` in the same commit.

Tests that scan the whole tree name no file, so the map lists them by pattern: every PHP file and every script in
`bin/` runs `expFileModeLimitsTest` (no `chmod()`, `mkdir()` or `umask()` past the mode limits), every INI file runs
`expIniRoundTripTest`, and `composer.json` runs both and `AdminStylingRulesTest`, because a repinned package brings
its own PHP, INI files and admin templates. Each of these once reached `main` through a change whose own tests
passed (October 2026).

The copyright notice is checked the same way: every PHP, JavaScript, template, style sheet, XML, INI, SQL and text
file, every script in `bin/`, `LICENSE`, `composer.json` and every catalogue runs `CopyrightNoticeTest`, which fails
on any line that names 7x or the Exponential Foundation as a holder in a form other than these two:
"Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved." or
"Exponential Copyright © 1998-2026 Exponential Foundation and 7x" (exceptions with their reasons: `tests/tests/bin/copyright-notice-allowlist.json`); the footers, the
sign-in pages, the copyright page, the help notice, the console and the catalogues also run
`CopyrightFooterNoticeTest`. See [Copyright and copyleft](../../help/copyright-and-copyleft.md).

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../help/partners/partners-start-here.md) and [How to apply](../../help/partners/how-to-apply.md).
<!-- exp-partner-block:end -->
