---
title: Exponential for PHP developers
audience: php-developer, developer
category: php-development
order: 10
summary: What a PHP developer meets in Exponential - a large, mature PHP code base with its own conventions, generated class maps, layered INI settings, persistent objects and a template engine - and how it differs from a framework you may know.
keywords: PHP developer, overview, kernel, lib, extension, conventions, class map, eZINI, eZDB, eZPersistentObject, template engine, legacy code base, PHP 8
type: help
---
# Exponential for PHP developers

This page is for PHP developers who are new to the Exponential code base. It explains what kind of PHP application
Exponential is, which conventions you will meet in the first hour, and where they differ from the frameworks most PHP
developers know today. The detailed pages of this section build on it.

## Contents

1. [What the code base is](#what-the-code-base-is)
2. [Five things that surprise PHP developers](#five-things-that-surprise-php-developers)
3. [The building blocks you will write](#the-building-blocks-you-will-write)
4. [What the project checks on every change](#what-the-project-checks-on-every-change)
5. [Where to go next](#where-to-go-next)

## What the code base is

Exponential is an open source content management system and PHP application framework, licensed under the GNU GPL,
version 2 or later (`composer.json`, `"license": "GPL-2.0-or-later"`). The code base has been developed since 1999,
first under the name eZ Publish, a product its creator discontinued after almost 18 years; 7x kept it alive, and the
product has been called Exponential since August 2025 ([Who is 7x?](../who-is-7x.md)).

For a PHP developer that history matters in one way: the code is older than Composer, PSR-4 and namespaces, and it
kept its own conventions. They are consistent and documented, and the project's checks enforce them on new code.

| Part | Directory | What it holds |
|---|---|---|
| Kernel | `kernel/` | content model, modules and views, datatypes, workflows, users and roles, the request kernel (`kernel/private/classes/ezpkernelweb.php`) |
| Libraries | `lib/` | general building blocks: `lib/ezutils` (`eZINI`, `eZDebug`, `eZSys`, `eZHTTPTool`, `eZExecution`), `lib/ezdb` (`eZDB` and the drivers), `lib/eztemplate` (the template engine), `lib/ezfile`, `lib/ezi18n` and more |
| Extensions | `extension/` | everything added on top, shipped or your own: modules, datatypes, operators, designs, settings |
| Settings | `settings/` | the INI defaults; installations override them in `settings/override/` and `settings/siteaccess/` |
| Entry points | `index.php`, `bin/php/*.php`, `runcronjobs.php` | the web front controller, the command line scripts and the cronjob runner |

A tour of every directory is on [A tour of the source tree](a-tour-of-the-source-tree.md).

## Five things that surprise PHP developers

1. **Class maps, not PSR-4.** Classes are found through generated arrays: `autoload/ezp_kernel.php` for the kernel and
   `var/autoload/ezp_extension.php` for the extensions, written by `php bin/php/ezpgenerateautoloads.php`. A new class
   that is not in the map does not exist ([Autoloads and class maps](autoloads-and-class-maps.md)).
2. **The kernel style has no type declarations.** `phpcs.xml.dist` refuses `declare( strict_types=1 )`, scalar
   parameter types, return types and property types in new code, to match the existing code and keep PHP 8.0 working
   ([Why the code has no type declarations](why-no-type-declarations.md)).
3. **`array( ... )`, not `[ ... ]`, and spaces inside parentheses.** `foo( $a, $b )`, `if ( $a )`, the opening brace of
   a function on its own line ([The coding standard](the-coding-standard.md)).
4. **Settings are INI files in layers.** `eZINI::instance( 'site.ini' )->variable( 'Block', 'Key' )` reads the value after
   the kernel defaults, the extensions, the siteaccess and the installation's overrides were merged
   ([Reading settings with eZINI](reading-settings-with-ezini.md)).
5. **Much state is static.** Singletons such as `eZINI::instance()`, `eZDB::instance()` and in-memory caches live in
   static properties. Under PHP-FPM each request starts fresh; under Velocity's persistent workers they survive between
   requests unless the kernel resets them ([Static state traps](static-state-traps.md)).

## The building blocks you will write

| You want | You write | Page |
|---|---|---|
| a new screen at `/<module>/<view>` | a module with views | [Write your first module view](write-your-first-module-view.md) |
| a command line tool | a class extending `Exponential\Runnable\Command` | [Write a console command](write-a-console-command.md) |
| scheduled work | a cronjob part | [Write a cronjob part](write-a-cronjob-part.md) |
| data for templates | a fetch function | [Write a fetch function](write-a-fetch-function.md) |
| a template filter or helper | a template operator class | [Write a template operator in PHP](write-a-template-operator-in-php.md) |
| a new field type | a class extending `eZDataType` | [Write a datatype](write-a-datatype.md) |
| a step in a workflow | a class extending `eZWorkflowEventType` | [Write a workflow event type](write-a-workflow-event-type.md) |
| data in your own table | a class extending `eZPersistentObject` | [Persistent object definitions in depth](persistent-object-definitions-in-depth.md) |
| JSON for your own JavaScript | an ezjscore server function | [ezjscore JSON endpoints in PHP](ezjscore-json-endpoints-in-php.md) |
| a reaction to something the kernel does | an event listener | [Events, listeners and filters](events-listeners-and-filters.md) |

## What the project checks on every change

| Check | Tool | Page |
|---|---|---|
| PHP syntax, also with PHP 8.0 | `php -l`, `make lint80` | [Writing code that runs on PHP 8.0](writing-code-that-runs-on-php-8-0.md) |
| Code style of changed files | `make phpcs` (`phpcs.xml.dist`) | [The coding standard](the-coding-standard.md) |
| Static analysis | `make phpstan` (level 1 with a baseline) | [Static analysis with PHPStan](static-analysis-with-phpstan.md) |
| Unit tests | `vendor/bin/phpunit` | [PHPUnit without a database](phpunit-without-a-database.md) |
| The file manifest | `make manifest-check` | [Git hooks and make targets](git-hooks-and-make-targets.md) |

## Where to go next

- Follow [The PHP developer's reading path](php-developer-reading-path.md) in order.
- Set up a checkout: [Setting up a PHP development checkout](a-php-development-checkout.md).
- Learn the request: [How a request runs in PHP](how-a-request-runs-in-php.md).

## Related pages

- [PHP Developers: start here](php-developers-start-here.md)
- [Kernel architecture](../../standards/architecture/kernel-architecture.md)
- [Application creators: start here](../creators/app-creators/app-creators-start-here.md),
  [the extension developer book](../extension-development/README.md)
- [Software Engineers: start here](../software-engineers/software-engineers-start-here.md),
  [Web Framework Developers: start here](../web-framework-developers/web-framework-developers-start-here.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
