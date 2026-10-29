---
title: Exponential for software engineers
audience: software-engineer, developer
category: software-engineering
order: 10
summary: The whole system on one page for an engineer from any language background - runtime, stack, process model, data stores, configuration, extension model, quality gates and release rules, each with the file or document that is the source of truth.
keywords: overview, stack, PHP, Composer, runtime, process model, kernel, extensions, databases, template engine, configuration, INI, quality, release, software engineer
type: help
---
# Exponential for software engineers

This page is for engineers who need the whole system in one view before they read the details. It is written for
people from any language background: where Exponential does something differently from the frameworks you may know,
the page says so. Every row names the file or document that is the source of truth.

## Contents

1. [The stack](#the-stack)
2. [What kind of system it is](#what-kind-of-system-it-is)
3. [Where things differ from other frameworks](#where-things-differ-from-other-frameworks)
4. [Constraints you inherit](#constraints-you-inherit)
5. [Where the truth lives](#where-the-truth-lives)

## The stack

| Concern | What Exponential uses | Source |
|---|---|---|
| Language | PHP 8.0 to 8.5 (PHP 7.4 usable without support) (`"php": "^7.4 \|\| ... \|\| ^8.8"`) | `composer.json` |
| Packages | Composer; the root package is `se7enxweb/exponential`, type `ezpublish-legacy` | `composer.json` |
| License | GNU GPL, version 2 or later | `LICENSE`, `composer.json` |
| Databases | SQLite 3 (default), PostgreSQL, MongoDB, MariaDB, Oracle, MySQL | [Database support](../database-support.md) |
| Configuration | INI files read through `eZINI`, merged in a fixed order | [Siteaccesses and configuration](siteaccesses-and-configuration.md) |
| Templates | Its own template language (`.tpl`), compiled to PHP by `eZTemplate` | [The template engine and the design chain](template-engine-and-design-chain.md) |
| Servers | Apache or nginx with PHP-FPM, Exponential Velocity (`qbix` engine), FrankenPHP | [Application servers](application-servers.md) |
| Tests | PHPUnit (`phpunit.xml`), most suites without a database | [Testing strategy](testing-strategy.md) |
| Static checks | PHPStan level 1 with a baseline, PHP_CodeSniffer with the project's own sniffs | [Static analysis and code style](static-analysis-and-code-style.md) |
| CI | GitHub Actions: Quality, PHPUnit, Performance, CI health | [CI gates](ci-gates.md) |

## What kind of system it is

- **A content repository with an application framework around it.** The center is a content model that
  administrators define at run time (content classes with typed attributes), versioned objects, and a tree of
  locations. Most code you write reads or changes that model ([The content model for engineers](content-model-for-engineers.md)).
- **A modular monolith.** One installation is one PHP application: the kernel, its libraries and the extensions run in
  the same process and share one database. Extensions are directories, not services.
- **Configuration-driven.** Modules, datatypes, workflow events, template operators, cronjob parts, caches and
  handlers are all found through INI settings, so most extension points are "add a line to a settings file and a
  class to your extension" ([Events, extension points and plugins](events-extension-points-and-plugins.md)).
- **Server-rendered first, API-capable.** Pages are rendered by templates; JSON endpoints (ezjscore), a REST API
  (`ezprestapi`) and remote services exist for headless and app use ([API design](api-design-rest-ezjscore-headless.md)).

## Where things differ from other frameworks

| You may expect | In Exponential |
|---|---|
| PSR-4 namespaces everywhere | The kernel keeps its class names (`eZContentObject`, `eZINI`) and a generated class map; newer code uses the `exp` prefix and, for the runnable classes, namespaces such as `Exponential\Runnable` |
| A router file | Module and view definitions in `module.php`, URL aliases in the database, siteaccess matching from `site.ini` |
| An ORM with migrations | `eZPersistentObject` (one class per table, a `definition()` array) and SQL update files per driver, applied by hand |
| Environment variables for config | INI files in a cascade; environment-specific settings are supported ([Environment settings](../../features/6.0/environment-settings.md)) |
| Twig or Blade | The Exponential template language, with operators, functions and fetch functions; Symfony and Twig only through the legacy bridge |
| Type declarations | The kernel style forbids PHP 7+ scalar and return type declarations, because the code must run on PHP 8.0 and keep its signatures ([Code style](../../features/6.0/quality-checks.md#code-style)) |

## Constraints you inherit

- **The oldest supported PHP is 8.0.** CI checks every PHP file with PHP 8.0 and boots the kernel on it; no syntax or
  function newer than 8.0 without a guard ([CI gates](ci-gates.md)).
- **Identifiers are not renamed.** Existing `eZ*` and `ezp*` classes, tables, settings and paths stay; new code uses
  `exp` ([Layering and naming rules](../../standards/architecture/layering-and-naming.md)).
- **Long-lived workers.** Code must be safe in a process that serves many requests ([Concurrency, statics and long-lived processes](concurrency-statics-and-long-lived-processes.md)).
- **Six databases.** Kernel code must not rely on one driver's features without a fallback ([Writing portable SQL](writing-portable-sql.md)).
- **Permanent releases.** Tags and releases are immutable ([Version numbers and immutable tags](version-numbers-and-immutable-tags.md)).

## Where the truth lives

| Question | Document |
|---|---|
| How is the kernel built? | [Kernel architecture](../../standards/architecture/kernel-architecture.md) |
| Which layer may call which? | [Layering and naming rules](../../standards/architecture/layering-and-naming.md) |
| What is a break, how is it released and deployed? | [Compatibility, releases and deployment](../../standards/architecture/compatibility-releases-deployment.md) |
| How are commits and releases made? | [Source control and releases](../../standards/source-control-and-releases.md) |
| Which patterns does the code use? | [Design pattern catalogue](../../standards/patterns/README.md) |
| What changed in a release? | [Changelog 6.0.15](../../../changelogs/6.0/6.0.15.md) and the upgrade notes in `doc/eng-us/bc/6.0/` |

## Related pages

- [Software engineers: start here](software-engineers-start-here.md)
- [Architecture overview](architecture-overview.md)
- [Evaluating Exponential](evaluating-exponential.md)
- [PHP developers: start here](../php-developers/php-developers-start-here.md), [Extension development](../extension-development/README.md) and [Application creators: start here](../creators/app-creators/app-creators-start-here.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
