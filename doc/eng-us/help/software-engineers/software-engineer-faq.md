---
title: Software engineer FAQ
audience: software-engineer, developer
category: software-engineering
order: 980
summary: Short answers to the questions software engineers ask first about Exponential - language and versions, frameworks and PSRs, ORM and migrations, databases, servers, statics, caches, tests, releases, security reports, licensing and contributing - each with the page that has the full answer.
keywords: FAQ, questions, answers, PHP version, Symfony, PSR, ORM, migrations, database, Velocity, FrankenPHP, statics, caching, tests, CI, versioning, tags, security, GPL, CLA, software engineer
type: help
---
# Software engineer FAQ

Short answers, each with the page that has the details.

## Contents

1. [Platform](#platform)
2. [Data](#data)
3. [Runtime](#runtime)
4. [Quality and releases](#quality-and-releases)
5. [People and licenses](#people-and-licenses)

## Platform

**Which PHP versions?** PHP 8.0 to 8.5. CI checks every file with PHP 8.0 and boots the kernel on it; PHPUnit runs on
8.1 to 8.5 ([CI gates](ci-gates.md)).

**Is it built on Symfony or Laravel?** No. The kernel is its own framework. The Symfony platform products embed it
through the legacy bridge ([Symfony and the legacy bridge](../creators/app-creators/symfony-and-the-legacy-bridge.md)).

**Does it follow PSR-4 and PSR-12?** No, deliberately: generated class maps instead of PSR-4, and its own code style
checked by `phpcs.xml.dist` ([Dependency management with Composer](dependency-management-with-composer.md#psr-status)).

**Why no type declarations in kernel code?** The code must run on PHP 8.0 and keep its signatures, which extensions
override ([Static analysis and code style](static-analysis-and-code-style.md#why-no-type-declarations)).

**Where does my code go?** In an extension, almost always ([Kernel, lib and extensions: the layering](kernel-lib-and-extensions.md)).

## Data

**Is there an ORM?** An active-record mapper, `eZPersistentObject`, one class per table; no unit of work, no lazy
relations ([Persistence and the database layer](persistence-and-the-database-layer.md)).

**Are there migrations?** Schema files (`.dba`) and SQL update files per driver, applied by hand; no runner records what
ran ([Schema changes and migrations](schema-changes-and-migrations.md)).

**Which database should we use?** SQLite 3 for one machine, PostgreSQL for several nodes or the best-tested server; the
official order is SQLite 3, PostgreSQL, MongoDB, MariaDB, Oracle, MySQL ([Databases: choosing and running one](databases-and-choosing-one.md)).

**Are prepared statements used?** Statements are built as strings; every string is escaped with `escapeString()` and
every number cast ([Writing portable SQL](writing-portable-sql.md)).

## Runtime

**Which server should serve it?** The project recommends Velocity; FrankenPHP is the production-ready alternative, and
PHP-FPM behind any web server keeps working ([Application servers](application-servers.md)).

**Are statics safe under Velocity?** Static properties are restored after each request, but warm-up state, globals kept
on purpose, resources and accumulated registrations are your job ([Concurrency, statics and long-lived processes](concurrency-statics-and-long-lived-processes.md)).

**Why did my change not show up?** Usually the order: code must be loaded (PHP-FPM, Velocity restart) before the page
caches are cleared ([Deploying a change](deploying-a-change.md)).

**Is there a health endpoint?** No single one; check the front page and the administration login from outside, and the
status commands on the server ([Observability](observability-logs-debug-and-audit.md#health-and-status)).

**Can it run on several web nodes?** Yes, with a database server, the DFS cluster file handler for files and a shared
session handler ([Scaling and clustering](scaling-and-clustering.md)).

## Quality and releases

**How do I run the tests?** `php vendor/bin/phpunit` runs every suite without a database ([DB-less tests and test suites](db-less-tests-and-test-suites.md)).

**Why is CI red on my pull request when my branch was green?** It tests the merge with `main`; merge `main` into your
branch ([CI gates](ci-gates.md#properties-every-engineer-should-know)).

**What comes after 0.0.4.9?** `0.0.4.10` ([Version numbers and immutable tags](version-numbers-and-immutable-tags.md)).

**Can a broken tag be fixed?** No. Publish the next version and say in its notes what was wrong.

**Where do I find what changed?** `doc/changelogs/` and the upgrade notes in `doc/eng-us/bc/6.0/`
([Changelogs and upgrade notes](changelogs-and-bc-notes.md)).

## People and licenses

**Who maintains it?** 7x leads the development; the Exponential Foundation operates and funds the project
([Who is 7x?](../who-is-7x.md), [Who is the Exponential Foundation?](../who-is-the-exponential-foundation.md)).

**What license?** The GNU GPL, version 2 or later, for the code; the GNU FDL 1.3 for the documentation.

**What does contributing to the kernel require?** A signed CLA, which assigns the copyright in the contribution to the Exponential Foundation and 7x;
your own extensions are not affected ([Contributing upstream](contributing-upstream.md)).

**How do I report a vulnerability?** Privately, to the address in `SECURITY.md` ([Security engineering overview](security-engineering-overview.md#reporting-and-fixing-vulnerabilities)).

## Related pages

- [Glossary for software engineers](glossary-for-software-engineers.md)
- [Application creator FAQ](../creators/app-creators/app-creator-faq.md)
- [Software engineers: start here](software-engineers-start-here.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
