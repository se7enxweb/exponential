---
title: "PHP Developers: start here"
audience: php-developer, developer
category: php-development
order: 1
summary: Where PHP developers start - what Exponential is as a PHP code base, your first hour, the rules every PHP change follows, and a map of every page for PHP developers, from the request life cycle and PHP 8.0 compatibility to module views, datatypes, security, debugging, Velocity workers, tests, static analysis and contributing.
keywords: PHP developer, PHP developers, start here, overview, index, kernel, extensions, PHP 8, coding standard, PHPStan, PHPUnit, Velocity, security, debugging, Composer, contributing
featured: true
type: help
---
# PHP Developers: start here

This section of the help is for PHP developers who write PHP code on or for Exponential: kernel fixes, extensions,
modules and views, datatypes, persistent objects, fetch functions, template operators, workflow event types, cronjob
parts, commands, JSON and REST endpoints, and the tests that go with them. It explains the code base's conventions, the
rules the project checks on every change, and how code behaves under each runtime, with real class names, files and
commands of the repository.

Every statement on these pages was checked against the code, the settings or the existing documentation of the 6.0.15
line. Where these pages overlap with the pages for application creators and the extension developer book, they link to
them instead of repeating them.

## Contents

1. [Exponential in one paragraph, for PHP developers](#exponential-in-one-paragraph-for-php-developers)
2. [Your first hour](#your-first-hour)
3. [Seven rules for every PHP change](#seven-rules-for-every-php-change)
4. [Map of the PHP developer pages](#map-of-the-php-developer-pages)
5. [Pages from other audiences you will use](#pages-from-other-audiences-you-will-use)
6. [Finding these pages again](#finding-these-pages-again)

## Exponential in one paragraph, for PHP developers

**Exponential** is a free and open source PHP content management system and application framework, licensed under the
GNU GPL, version 2 or later. Its code base has been developed since 1999, first under the name eZ Publish, a product its
creator discontinued after almost 18 years; 7x kept it alive, and it is led by 7x together with the Exponential
Foundation ([Who is 7x?](../who-is-7x.md), [Who is the Exponential Foundation?](../who-is-the-exponential-foundation.md)).
For a PHP developer it is a large, mature code base with its own conventions: PHP 8.0 to 8.5, generated class maps
instead of PSR-4, layered INI settings read through `eZINI`, `eZPersistentObject` for tables, a compiled template
language, six supported databases (SQLite 3 by default, PostgreSQL, MongoDB, MariaDB, Oracle, MySQL), and Velocity, an
application server with persistent workers, as part of the product
([Exponential for PHP developers](exponential-for-php-developers.md)).

## Your first hour

1. **Orient yourself** (10 minutes): [Exponential for PHP developers](exponential-for-php-developers.md) and
   [A tour of the source tree](a-tour-of-the-source-tree.md).
2. **Follow one request** (10 minutes): [How a request runs in PHP](how-a-request-runs-in-php.md).
3. **Set up a checkout** (20 minutes): [Setting up a PHP development checkout](a-php-development-checkout.md), with
   `make devtools` and `make hooks`.
4. **Learn the rules** (10 minutes): [Writing code that runs on PHP 8.0](writing-code-that-runs-on-php-8-0.md) and
   [The coding standard](the-coding-standard.md).
5. **Build something** (10 minutes to start): [Write your first module view](write-your-first-module-view.md).

The whole section in order is [The PHP developer's reading path](php-developer-reading-path.md).

## Seven rules for every PHP change

1. **Runs on PHP 8.0 to 8.5.** No syntax or unguarded function newer than 8.0
   ([Writing code that runs on PHP 8.0](writing-code-that-runs-on-php-8-0.md)).
2. **Follows the house style.** `array()`, spaces inside parentheses, no type declarations; `make phpcs` and
   `make phpstan` add nothing ([The coding standard](the-coding-standard.md)).
3. **Is named with a prefix.** `exp` for project code, your own for yours; kernel `eZ*` identifiers never change
   ([Class naming and the exp prefix](class-naming-and-the-exp-prefix.md)).
4. **Trusts no input and escapes all output.** Casts, allow lists, `escapeString()`, `|wash`, the form token, permission
   checks in every entry point ([Security in PHP code](security-in-php-code.md)).
5. **Keeps no state between requests.** Correct under Velocity's persistent workers
   ([Static state traps](static-state-traps.md)).
6. **Works on every supported database.** Portable SQL or persistent objects
   ([Code for every supported database](code-for-every-supported-database.md)).
7. **Comes with a test.** DB-less where possible ([PHPUnit without a database](phpunit-without-a-database.md)).

## Map of the PHP developer pages

### Orientation

| Page | What it helps you do |
|---|---|
| [Exponential for PHP developers](exponential-for-php-developers.md) | Understand the kind of code base you are working in |
| [A tour of the source tree](a-tour-of-the-source-tree.md) | Find the kernel, libraries, commands, settings and tests |
| [How a request runs in PHP](how-a-request-runs-in-php.md) | Know where your code runs and which events you can use |
| [Setting up a PHP development checkout](a-php-development-checkout.md) | Get a checkout with the tools and hooks |
| [The PHP developer's reading path](php-developer-reading-path.md) | Read the section in order |

### PHP versions and code rules

| Page | What it helps you do |
|---|---|
| [Supported PHP versions: 8.0 to 8.5](supported-php-versions.md) | Know the range and how each version is tested |
| [Writing code that runs on PHP 8.0](writing-code-that-runs-on-php-8-0.md) | Avoid newer syntax and functions, guard what you need |
| [PHP 8 migration notes for your code](php-8-migration-notes-for-your-code.md) | Bring older code up to PHP 8 |
| [The coding standard (phpcs.xml.dist)](the-coding-standard.md), [Why the code has no type declarations](why-no-type-declarations.md) | Write in the house style and understand why |
| [Class naming and the exp prefix](class-naming-and-the-exp-prefix.md), [Autoloads and class maps](autoloads-and-class-maps.md) | Name classes and make them findable |
| [Reading settings with eZINI](reading-settings-with-ezini.md) | Read configuration correctly |

### Building blocks

| Page | What it helps you do |
|---|---|
| [Write your first module view](write-your-first-module-view.md), [Module views as classes](module-views-as-classes.md) | Add pages, and change kernel views without copying them |
| [Write a console command](write-a-console-command.md), [Plain CLI scripts with eZScript and ezexec](legacy-cli-scripts-with-ezscript.md) | Add command line tools |
| [Write a cronjob part](write-a-cronjob-part.md) | Schedule work |
| [Write a fetch function](write-a-fetch-function.md), [Write a template operator in PHP](write-a-template-operator-in-php.md) | Give templates data and helpers |
| [Write a datatype](write-a-datatype.md), [Write a workflow event type](write-a-workflow-event-type.md) | Add field types and workflow steps |
| [Events, listeners and filters](events-listeners-and-filters.md), [Extension points from PHP](extension-points-from-php.md) | Plug into the kernel without editing it |

### Data and APIs

| Page | What it helps you do |
|---|---|
| [SQL with eZDB, safely](sql-with-ezdb.md), [Code for every supported database](code-for-every-supported-database.md) | Write safe, portable SQL |
| [Persistent object definitions in depth](persistent-object-definitions-in-depth.md) | Store data in tables of your own |
| [ezjscore JSON endpoints in PHP](ezjscore-json-endpoints-in-php.md), [REST and remote services from PHP](rest-and-remote-services-from-php.md) | Serve data over HTTP |
| [Composer for PHP developers](composer-for-php-developers.md) | Manage dependencies and package extensions |

### Security

| Page | What it helps you do |
|---|---|
| [Security in PHP code](security-in-php-code.md) | Apply every security rule in one pass |
| [Validating input and casting](validating-input-and-casting.md), [Escaping output from PHP](escaping-output-from-php.md) | Handle input and output safely |
| [CSRF protection and ezformtoken](csrf-and-ezformtoken.md) | Protect POSTs, also from JavaScript |

### Debugging and performance

| Page | What it helps you do |
|---|---|
| [Debugging with eZDebug](debugging-with-ezdebug.md), [The debug bar for PHP developers](the-debug-bar-for-php-developers.md) | See inside a request; add a debug panel |
| [Logs, the log viewer and request logs](logs-log-viewer-and-request-logs.md) | Write and read logs; follow an error reference |
| [Profiling PHP code](profiling-php-code.md), [OPcache and cache preloading](opcache-and-cache-preloading.md) | Measure, speed up and warm |

### Runtimes

| Page | What it helps you do |
|---|---|
| [Velocity persistent workers for PHP developers](velocity-persistent-workers-for-php-developers.md), [Static state traps](static-state-traps.md) | Write code that is correct under persistent workers |
| [FrankenPHP for PHP developers](frankenphp-for-php-developers.md) | Run and test under FrankenPHP |
| [The engine archive (phar) for PHP developers](the-engine-phar-for-php-developers.md) | Keep paths right when the kernel runs from an archive |

### Tests, quality and contributing

| Page | What it helps you do |
|---|---|
| [PHPUnit without a database](phpunit-without-a-database.md), [Database and live tests](database-and-live-tests.md) | Test your code at the right level |
| [Static analysis with PHPStan](static-analysis-with-phpstan.md), [Git hooks and make targets](git-hooks-and-make-targets.md) | Run the project's checks locally |
| [CI and reproducing a red check](ci-and-reproducing-a-red-check.md) | Fix a red CI check |
| [Contributing PHP changes](contributing-php-changes.md) | Get a change into the kernel |

### Reference

| Page | What it helps you do |
|---|---|
| [Checklists for PHP developers](php-developer-checklists.md) | Check your work in one pass |
| [PHP developer FAQ](php-developer-faq.md), [Glossary for PHP developers](glossary-for-php-developers.md) | Quick answers and terms |

## Pages from other audiences you will use

| Page | Why PHP developers read it |
|---|---|
| [Application creators: start here](../creators/app-creators/app-creators-start-here.md) | Building complete applications: content API, queues, REST, deployment, scaling |
| [The extension developer book](../extension-development/README.md) | The full book for developers of extensions, from first steps to releasing, with an API reference in part 8 |
| [Software Engineers: start here](../software-engineers/software-engineers-start-here.md) | Architecture, evaluation and engineering practice around Exponential |
| [Web Framework Developers: start here](../web-framework-developers/web-framework-developers-start-here.md) | Exponential compared with and connected to other web frameworks |
| [The template language](../template-language/README.md), [Template operators](../template-operators/README.md) | The templates your PHP code feeds |
| [Database support](../database-support.md) | The six databases, their versions, settings and test coverage |
| [Develop with Git](../develop-with-git.md) | Working with the repository |
| [Free documentation](../free-documentation.md) | What you may do with these pages |

## Finding these pages again

- In the help, filter by the audience **PHP Developers**, or open **All pages** and the folder `php-developers`.
- Each page has **Edit on GitHub**. If a statement here no longer matches the code, tell us there or on the
  [community issue tracker](https://github.com/se7enxweb/exponential-community/issues).

## Related pages

- [Exponential for PHP developers](exponential-for-php-developers.md)
- [The PHP developer's reading path](php-developer-reading-path.md)
- [Application creators: start here](../creators/app-creators/app-creators-start-here.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
