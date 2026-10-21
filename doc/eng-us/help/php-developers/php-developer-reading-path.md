---
title: The PHP developer's reading path
audience: php-developer, developer
category: php-development
order: 5
summary: Every page of the PHP developers section in a recommended reading order, in six stages from orientation to contributing, with the time each stage takes and what you can do after it.
keywords: reading path, learning path, order, PHP developer, curriculum, stages, onboarding, first week
type: help
---
# The PHP developer's reading path

This page is for PHP developers who want to read this section in a sensible order. It has six stages. After each stage
you can do something you could not do before. The times are rough reading times, without the exercises.

## Contents

1. [Stage 1: orientation (1 hour)](#stage-1-orientation-1-hour)
2. [Stage 2: the rules of the code (2 hours)](#stage-2-the-rules-of-the-code-2-hours)
3. [Stage 3: building blocks (half a day)](#stage-3-building-blocks-half-a-day)
4. [Stage 4: data and APIs (half a day)](#stage-4-data-and-apis-half-a-day)
5. [Stage 5: security, debugging and performance (half a day)](#stage-5-security-debugging-and-performance-half-a-day)
6. [Stage 6: runtimes, tests and contributing (half a day)](#stage-6-runtimes-tests-and-contributing-half-a-day)
7. [Reference while you work](#reference-while-you-work)

## Stage 1: orientation (1 hour)

1. [PHP Developers: start here](php-developers-start-here.md)
2. [Exponential for PHP developers](exponential-for-php-developers.md)
3. [A tour of the source tree](a-tour-of-the-source-tree.md)
4. [How a request runs in PHP](how-a-request-runs-in-php.md)
5. [Setting up a PHP development checkout](a-php-development-checkout.md)

After it: you have a working checkout and know where code runs.

## Stage 2: the rules of the code (2 hours)

1. [Supported PHP versions: 8.0 to 8.5](supported-php-versions.md)
2. [Writing code that runs on PHP 8.0](writing-code-that-runs-on-php-8-0.md)
3. [PHP 8 migration notes for your code](php-8-migration-notes-for-your-code.md)
4. [The coding standard (phpcs.xml.dist)](the-coding-standard.md)
5. [Why the code has no type declarations](why-no-type-declarations.md)
6. [Class naming and the exp prefix](class-naming-and-the-exp-prefix.md)
7. [Autoloads and class maps](autoloads-and-class-maps.md)
8. [Reading settings with eZINI](reading-settings-with-ezini.md)

After it: your code passes the checks and fits the code base.

## Stage 3: building blocks (half a day)

1. [Write your first module view](write-your-first-module-view.md) (do it)
2. [Module views as classes](module-views-as-classes.md)
3. [Write a console command](write-a-console-command.md)
4. [Write a cronjob part](write-a-cronjob-part.md)
5. [Plain CLI scripts with eZScript and ezexec](legacy-cli-scripts-with-ezscript.md)
6. [Write a fetch function](write-a-fetch-function.md)
7. [Write a template operator in PHP](write-a-template-operator-in-php.md)
8. [Write a datatype](write-a-datatype.md)
9. [Write a workflow event type](write-a-workflow-event-type.md)
10. [Events, listeners and filters](events-listeners-and-filters.md)
11. [Extension points from PHP](extension-points-from-php.md)

After it: you can add screens, commands, scheduled work, template helpers, field types and workflow steps.

## Stage 4: data and APIs (half a day)

1. [SQL with eZDB, safely](sql-with-ezdb.md)
2. [Code for every supported database](code-for-every-supported-database.md)
3. [Persistent object definitions in depth](persistent-object-definitions-in-depth.md)
4. [ezjscore JSON endpoints in PHP](ezjscore-json-endpoints-in-php.md)
5. [REST and remote services from PHP](rest-and-remote-services-from-php.md)
6. [Composer for PHP developers](composer-for-php-developers.md)

After it: you store data portably and serve it over HTTP.

## Stage 5: security, debugging and performance (half a day)

1. [Security in PHP code](security-in-php-code.md)
2. [Validating input and casting](validating-input-and-casting.md)
3. [Escaping output from PHP](escaping-output-from-php.md)
4. [CSRF protection and ezformtoken](csrf-and-ezformtoken.md)
5. [Debugging with eZDebug](debugging-with-ezdebug.md)
6. [The debug bar for PHP developers](the-debug-bar-for-php-developers.md)
7. [Logs, the log viewer and request logs](logs-log-viewer-and-request-logs.md)
8. [Profiling PHP code](profiling-php-code.md)

After it: your code is safe, and you can find and measure what goes wrong.

## Stage 6: runtimes, tests and contributing (half a day)

1. [Velocity persistent workers for PHP developers](velocity-persistent-workers-for-php-developers.md)
2. [Static state traps](static-state-traps.md)
3. [FrankenPHP for PHP developers](frankenphp-for-php-developers.md)
4. [The engine archive (phar) for PHP developers](the-engine-phar-for-php-developers.md)
5. [OPcache and cache preloading](opcache-and-cache-preloading.md)
6. [PHPUnit without a database](phpunit-without-a-database.md)
7. [Database and live tests](database-and-live-tests.md)
8. [Static analysis with PHPStan](static-analysis-with-phpstan.md)
9. [Git hooks and make targets](git-hooks-and-make-targets.md)
10. [CI and reproducing a red check](ci-and-reproducing-a-red-check.md)
11. [Contributing PHP changes](contributing-php-changes.md)

After it: your code runs under every engine, is tested, and can go into the kernel.

## Reference while you work

- [Checklists for PHP developers](php-developer-checklists.md)
- [PHP developer FAQ](php-developer-faq.md)
- [Glossary for PHP developers](glossary-for-php-developers.md)
- [Extension developer reference](../extension-development/part-8-reference/README.md)

## Related pages

- [The application creator's reading path](../creators/app-creators/app-creators-reading-path.md)
- [Software Engineers: start here](../software-engineers/software-engineers-start-here.md),
  [Web Framework Developers: start here](../web-framework-developers/web-framework-developers-start-here.md)
- [Reading paths of the extension developer book](../extension-development/reading-paths.md)
- [PHP Developers: start here](php-developers-start-here.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
