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
`.js` file), the manifest, the commit messages of the pushed or proposed commits, PHPStan and the tests of the own sniffs
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

The dependency installs of the workflows run on the `main` branch, where the root package is `dev-main`. Its branch
alias `"dev-main": "6.0.x-dev"` in `composer.json` (from 2026-10-12) lets a required package that names
`se7enxweb/exponential` with a version constraint, such as expqueue's `^6.0.14`, resolve against it; without it the
install stops with "could not be resolved".

### Before you push

The blocking checks of the Quality workflow look at the commits of the push, so they can be run on the committed
`HEAD` before it leaves the machine:

```sh
git diff --name-only --diff-filter=AM origin/main..HEAD -- '*.php' | xargs -r -n1 php -l   # syntax of changed files
php bin/php/checkmanifest.php --all                                                        # the file manifest
for c in $(git rev-list --no-merges origin/main..HEAD); do
    git log -1 --format=%B "$c" | sh .githooks/commit-msg /dev/stdin || echo "$c"
done                                                                                       # the commit messages
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
