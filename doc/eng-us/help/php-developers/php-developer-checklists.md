---
title: Checklists for PHP developers
audience: php-developer, developer
category: php-development
order: 480
summary: Copyable checklists for PHP work on Exponential - before you write, for every PHP file, for a new class, a view, a command or cronjob part, a datatype or workflow event, security, Velocity compatibility, tests, a commit and pull request, and a deploy.
keywords: checklist, PHP checklist, code review, pull request checklist, security checklist, Velocity checklist, deploy checklist, new class, module view, datatype, PHP 8.0, phpcs, phpstan, tests, manifest
type: help
---
# Checklists for PHP developers

This page is for PHP developers who want to check their work in one pass. Each list links to the page that explains it.
Copy a list into your pull request description or your review notes.

## Contents

1. [Before you write](#before-you-write)
2. [Every PHP file](#every-php-file)
3. [A new class](#a-new-class)
4. [A module view](#a-module-view)
5. [A command or cronjob part](#a-command-or-cronjob-part)
6. [A datatype or workflow event type](#a-datatype-or-workflow-event-type)
7. [Security](#security)
8. [Velocity and long runs](#velocity-and-long-runs)
9. [Tests](#tests)
10. [Commit and pull request](#commit-and-pull-request)
11. [Deploy](#deploy)

## Before you write

- [ ] Is there a setting, a shipped extension or an extension point that does it already? ([Extension points from PHP](extension-points-from-php.md))
- [ ] Kernel change or extension? ([Contributing PHP changes](contributing-php-changes.md#kernel-or-extension))
- [ ] The prefix of the new names is chosen ([Class naming and the exp prefix](class-naming-and-the-exp-prefix.md))

## Every PHP file

- [ ] Starts with `<?php`, Unix line endings, spaces, no trailing spaces
- [ ] `php -l` passes on the newest PHP, and on PHP 8.0 (`make lint80`)
- [ ] No syntax or function newer than PHP 8.0 unguarded ([Writing code that runs on PHP 8.0](writing-code-that-runs-on-php-8-0.md))
- [ ] `array( ... )`, spaces inside parentheses, function braces on their own line ([The coding standard](the-coding-standard.md))
- [ ] No scalar, return or property type declarations; docblocks with `@param` and `@return`
- [ ] `make phpcs` adds no violations; `make phpstan` adds no findings

## A new class

- [ ] One class per file, lower case file name of the class
- [ ] Properties declared (no dynamic properties)
- [ ] `php bin/php/ezpgenerateautoloads.php -e` run ([Autoloads and class maps](autoloads-and-class-maps.md))
- [ ] No class static assigned outside the class body
- [ ] A setting that names the class goes live after the class is deployed

## A module view

- [ ] `module.php` names the view's `functions` (policy) ([Write your first module view](write-your-first-module-view.md))
- [ ] Written as a class extending `Exponential\Runnable\ModuleView`, the view file one line
- [ ] `$Params` cast and validated
- [ ] Data changes only on POST, through module actions
- [ ] HTML in a template, every value through `|wash`
- [ ] POST, redirect, GET after a successful save; redirect targets from the request checked

## A command or cronjob part

- [ ] A class extending `Exponential\Runnable\Command` or `CronjobPart` ([Write a console command](write-a-console-command.md), [Write a cronjob part](write-a-cronjob-part.md))
- [ ] `@description` tag in the entry file
- [ ] Exit code `0` on success, another on failure; quiet with `-q`
- [ ] `--dry-run` for anything that changes data
- [ ] Bounded work per run, short transactions, in-memory caches cleared between batches
- [ ] The work in a service class the view, the command and the cronjob part share

## A datatype or workflow event type

- [ ] Method signatures exactly as in `eZDataType` / `eZWorkflowEventType` ([Write a datatype](write-a-datatype.md))
- [ ] Registered with `eZDataType::register()` or `eZWorkflowEventType::registerEventType()` and in the INI file
- [ ] `toString()`/`fromString()` round trip tested
- [ ] `execute()` fast, returns a status, never throws ([Write a workflow event type](write-a-workflow-event-type.md))

## Security

- [ ] Input read through `eZHTTPTool`, `$Params` or module actions; numbers cast, choices in allow lists ([Validating input and casting](validating-input-and-casting.md))
- [ ] SQL: strings through `escapeString()`, numbers cast, or persistent object conditions ([SQL with eZDB, safely](sql-with-ezdb.md))
- [ ] Output escaped for its context; `htmlspecialchars()` with explicit flags ([Escaping output from PHP](escaping-output-from-php.md))
- [ ] Permissions checked in views, fetch functions, server functions, REST actions and commands ([Security in PHP code](security-in-php-code.md))
- [ ] POSTs carry the form token ([CSRF protection and ezformtoken](csrf-and-ezformtoken.md))
- [ ] No secrets in code, URLs or logs
- [ ] No inline scripts or `style=""` in generated markup

## Velocity and long runs

- [ ] No function statics, no register-once `true` flags ([Static state traps](static-state-traps.md))
- [ ] Caches keyed by siteaccess, language and user where they depend on them
- [ ] `eZExecution::cleanExit()` not inside `catch ( Exception )`
- [ ] Paths from `EXP_ROOT_DIR` or `eZSys`, not from a kernel file's `__DIR__`
- [ ] Tested twice in a row on the same worker, as two users

## Tests

- [ ] A DB-less test for the logic ([PHPUnit without a database](phpunit-without-a-database.md))
- [ ] Database tests marked `@group database` ([Database and live tests](database-and-live-tests.md))
- [ ] INI values set in a test are restored in `tearDown()`
- [ ] Works with PHPUnit 10.5 to 13
- [ ] `tests/test-map.json` entry when the test does not name the source file literally

## Commit and pull request

- [ ] Subject starts with `Added: `, `Updated: `, `Fixed: `, `Removed: ` or `Renamed: `; the body says why
- [ ] The product is written Exponential in the message
- [ ] `share/filelist.md5` refreshed (`make manifest-fix`) and committed
- [ ] `make check` passes
- [ ] CLA on file; pull request small and focused; squashed and rebased onto `main` ([Contributing PHP changes](contributing-php-changes.md))

## Deploy

- [ ] Autoloads regenerated, INI cache cleared (`--clear-tag=ini`)
- [ ] PHP-FPM reloaded, Velocity workers restarted (`exp:velocity deploy`)
- [ ] Rendered-output caches cleared after the reload
- [ ] Caches warmed ([OPcache and cache preloading](opcache-and-cache-preloading.md))
- [ ] Front page and the changed pages checked; logs read

## Related pages

- [The upgrade checklist](../extension-development/part-4-upgrading/the-upgrade-checklist.md)
- [The release checklist](../extension-development/part-7-releasing/the-release-checklist.md)
- [PHP developer FAQ](php-developer-faq.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
