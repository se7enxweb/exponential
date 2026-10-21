---
title: Testing a bundle
audience: bundler, developer
category: partnership
order: 430
summary: What to test before a bundle release - the build, a clean install with each database and PHP version you claim, the first boot, an offline install, permissions, security checks, integrity, an upgrade from the previous bundle release, and a test matrix and log to keep.
keywords: test, testing, bundle, test matrix, clean install, dry run, smoke test, setup.log, exit status, checkclasses, checkmanifest, upgrade test, offline, PHP versions, databases
type: help
---
# Testing a bundle

This page is for bundlers who test a release before it goes out. A bundle combines many parts; the only proof that
they work together is installing and using the bundle as a recipient would.

## Contents

1. [The test matrix](#the-test-matrix)
2. [Build checks](#build-checks)
3. [Install tests](#install-tests)
4. [Running-site checks](#running-site-checks)
5. [Upgrade test](#upgrade-test)
6. [The test log](#the-test-log)

## The test matrix

List what you claim and test each combination you claim, not more:

```text
PHP:        8.x, 8.y
Database:   SQLite 3, PostgreSQL <version>
Server:     Velocity (qbix) / Apache + PHP-FPM / nginx + PHP-FPM
Install:    exp:install / kickstarter / setup wizard
Network:    online / offline
Upgrade:    from bundle <previous version>
```

The project's own CI tests PostgreSQL 15 and MySQL 8.0 and runs driver unit tests for the others
([Database support](../database-support.md#at-a-glance)); a combination your bundle claims beyond that is yours to
prove.

## Build checks

| Check | Command or method | Pass when |
|---|---|---|
| Composer | `composer validate --no-check-publish`; `composer install --no-dev` from the lock file | no errors |
| PHP extensions | `php bin/php/console exp:velocity ext check` | nothing missing |
| Classes | `php bin/php/console exp:checkclasses` | no class PHP refuses to load |
| Integrity | `php bin/php/checkmanifest.php --all` | exit `0`, or only your listed changes |
| Licenses | `composer licenses --no-dev` | no package without a license you have not cleared |
| Nothing secret shipped | inspect the artifact for `settings/override/*.ini.append.php`, `kickstart.ini`, `var/log/` | none present |

## Install tests

1. **Dry run:** `php bin/php/console exp:install --dry-run ...` or `exp:kickstarter run --dry-run`; exit `0`.
2. **Install:** exit `0`; `var/log/setup.log` ends with a RESULT line and no errors. The log includes "health checks
   after the install (database, declared tables, content classes, objects, content root, name lists, settings,
   siteaccesses, file ownership)" ([Install in one command](../../features/6.0/install-in-one-command.md#what-the-logs-tell-you)).
3. **Second start:** the already-installed test skips the install; data is still there.
4. **Offline:** the same install with no outgoing network ([Offline bundles](offline-bundles.md)).
5. **Failure path:** a wrong database password gives exit `1`, maintenance mode on, a readable reason in
   `var/log/setup.log`.

## Running-site checks

- [ ] The site and the administration answer at the documented addresses.
- [ ] Sign in with the handed-over password; change it; the file `var/log/initial-admin-password` is gone.
- [ ] Publish an article with an image; it appears on the site; image variations are created.
- [ ] Search finds it.
- [ ] Cron runs (`runcronjobs.php`) as the site user without permission errors.
- [ ] `/.git/config`, `/settings/site.ini`, `/composer.json` are not served ([13.16](../../install/13-security-hardening.md#1316-go-live-checklist)).
- [ ] No **Demo mode** banner (except in demo bundles).
- [ ] **Info > About** and **Info > Copyright** show the versions and licenses.
- [ ] No files owned by root under `var/`.
- [ ] After a restart of the whole bundle, everything above still holds.

## Upgrade test

1. Install the previous bundle release; add some content.
2. Apply your update the way recipients will ([Updates and upgrade paths](updates-and-upgrade-paths.md)).
3. Check the version row (expected value for the 6.0.15 line: `6.0.15stable`, query in
   [11.2](../../install/11-upgrading.md#112-which-version-do-you-run)), the content, the sign-in and the checks above.
4. Follow [11.7 Verify the upgrade](../../install/11-upgrading.md#117-verify-the-upgrade).

## The test log

Keep, per release: the matrix with results, `var/log/setup.log` and `var/log/kickstart.log` of each install, the
build record ([Lock files and reproducible builds](lock-files-and-reproducible-builds.md#the-build-record)) and the
names of the testers. Partners who test Exponential releases themselves can feed results back
([Testing releases](../partners/testing-releases.md)).

## Related pages

- [Bundle release checklist](bundle-release-checklist.md)
- [Testing in a sandbox (site packages)](../site-packages/your-own/testing-in-a-sandbox.md)
- [Benchmarking a hosted site](../hosting/performance/benchmarking-a-hosted-site.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
