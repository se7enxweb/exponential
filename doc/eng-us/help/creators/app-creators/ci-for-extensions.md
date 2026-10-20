---
title: Continuous integration for extensions
audience: app-creator, developer
category: app-creation
order: 510
summary: Set up CI for an extension the way the kernel does it - GitHub Actions jobs for PHP syntax on every supported version, PHPUnit, static analysis, commit message checks, least privileges, and testing against the Exponential kernel.
keywords: CI, continuous integration, GitHub Actions, workflow, PHP matrix, PHPUnit, PHPStan, phpcs, lint, commit messages, permissions, app creator
type: help
---
# Continuous integration for extensions

This page is for application creators who want every push of their extension checked automatically. The kernel's own
workflows in `.github/workflows/` are a tested model ([GitHub Actions workflows](../../../standards/testing-and-ci.md#github-actions-workflows)).

## Contents

1. [What the kernel's CI does](#what-the-kernels-ci-does)
2. [A workflow for an extension](#a-workflow-for-an-extension)
3. [Testing against the kernel](#testing-against-the-kernel)
4. [Rules](#rules)

## What the kernel's CI does

| Workflow | Checks |
|---|---|
| `quality.yml` | PHP and JavaScript syntax of changed files, the file manifest, commit messages, code style (warning), PHPStan |
| `phpunit.yml` | PHPUnit on PHP 8.1 to 8.5, and a job with MySQL and PostgreSQL |
| `performance.yml` | a micro benchmark against a baseline (warning only) |
| `ci-health.yml` | a weekly report of pass rates and flaky tests |

## A workflow for an extension

```yaml
# .github/workflows/ci.yml
name: CI
on: [push, pull_request]
permissions:
  contents: read
jobs:
  lint:
    runs-on: ubuntu-latest
    strategy:
      matrix:
        php: ['8.0', '8.1', '8.2', '8.3', '8.4', '8.5']
    steps:
      - uses: actions/checkout@v4
      - uses: shivammathur/setup-php@v2
        with:
          php-version: ${{ matrix.php }}
      - run: find . -name '*.php' -not -path './vendor/*' -print0 | xargs -0 -n1 php -l
  tests:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4
      - uses: shivammathur/setup-php@v2
        with:
          php-version: '8.3'
      - run: composer install --no-interaction
      - run: vendor/bin/phpunit tests/
```

Lint on every PHP version the kernel supports (8.0 to 8.5, [Requirements](../../../install/02-requirements.md)), because
installations run on all of them.

## Testing against the kernel

Tests that need kernel classes run in a checkout of `se7enxweb/exponential` with your extension placed in `extension/`:
check out both, run `composer install` in the kernel, copy or link your extension, regenerate autoloads, then run your
tests with the kernel's `phpunit.xml` bootstrap. Keep these tests DB-less where possible
([DB-less PHPUnit tests](db-less-phpunit-tests.md)).

## Rules

- Least privileges: `permissions: contents: read`; no `pull_request_target`.
- A timeout per job.
- Pin action versions; update them deliberately.
- No secrets in logs; secrets only where a job truly needs them, never on pull requests from forks.
- Red CI blocks the release tag ([Versions and releases](versions-and-releases.md)).

## Related pages

- [Testing applications](testing-applications.md)
- [Quality checks](../../../features/6.0/quality-checks.md)
- [CI behavior changes](../../../bc/6.0/ci.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
