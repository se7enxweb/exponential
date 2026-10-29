---
title: "Software engineers: start here"
audience: software-engineer, developer
category: software-engineering
order: 1
summary: Where software engineers start who evaluate, architect, operate, review or contribute to Exponential as a system. The system in one paragraph, your first hour, five engineering rules, and a map of every page for software engineers, from architecture, data and caching to application servers, testing, CI, release engineering, security and contributing upstream.
keywords: software engineer, engineering, architecture, system design, evaluation, code review, operations, CI, testing, release engineering, start here, overview, index, map
featured: true
type: help
---
# Software engineers: start here

This section of the help is for software engineers, whatever language you usually write: people who evaluate
Exponential for a project, design a system around it, run it in production, review changes to it or contribute code
back. It explains the system as a system: its parts and their contracts, how a request travels through it, where data
lives, which caches sit where, how it is served, tested, released and secured, and which documents are the source of
truth for each of those.

These pages do not repeat the detailed guides. They give you the engineering view and link to the page that has the
details, usually in `doc/eng-us/standards/`, `doc/eng-us/bc/6.0/`, `doc/eng-us/features/6.0/` or the guides. Where the
sources do not answer a question, the pages say so instead of guessing.

## Contents

1. [Exponential in one paragraph](#exponential-in-one-paragraph)
2. [Your first hour](#your-first-hour)
3. [Five engineering rules](#five-engineering-rules)
4. [Map of the software engineering pages](#map-of-the-software-engineering-pages)
5. [Pages from other audiences you will use](#pages-from-other-audiences-you-will-use)
6. [Finding these pages again](#finding-these-pages-again)

## Exponential in one paragraph

**Exponential** is a free and open source PHP content management system and application framework, licensed under
the GNU GPL, version 2 or later. Its README calls it "a professional PHP application framework with advanced CMS
(content management system) functionality", whose most notable feature is "its fully customizable and extendable
content model". The kernel (`kernel/`, `lib/`) runs on PHP 8.0 to 8.5 (PHP 7.4 usable without support), stores content in SQLite 3 (the default),
PostgreSQL, MongoDB, MariaDB, Oracle or MySQL, renders through its own template language, and is extended through
extensions in `extension/`. Since 6.0.15 it ships its own application server, Exponential Velocity, next to Apache or
nginx with PHP-FPM and FrankenPHP. The code base was developed from 1999, first under the name eZ Publish, a product
its creator discontinued after almost 18 years; 7x leads the development today and the Exponential Foundation
operates and funds the project ([Who is 7x?](../who-is-7x.md), [Who is the Exponential Foundation?](../who-is-the-exponential-foundation.md)).

## Your first hour

1. **Get the shape of it** (10 minutes): [Exponential for software engineers](exponential-for-software-engineers.md)
   and [Architecture overview](architecture-overview.md).
2. **Follow one request** (10 minutes): [The request lifecycle](request-lifecycle.md).
3. **Know where state lives** (10 minutes): [Databases: choosing and running one](databases-and-choosing-one.md) and
   [Caching layers for engineers](caching-layers-for-engineers.md).
4. **Know how it runs** (10 minutes): [Application servers](application-servers.md) and
   [Concurrency, statics and long-lived processes](concurrency-statics-and-long-lived-processes.md).
5. **Know how change is controlled** (10 minutes): [Testing strategy](testing-strategy.md), [CI gates](ci-gates.md)
   and [Release engineering](release-engineering.md).
6. **Know the security contract** (10 minutes): [Security engineering overview](security-engineering-overview.md).

## Five engineering rules

1. **Never edit `kernel/`, `lib/` or another extension in place.** An update replaces them. Use the extension points;
   a kernel class override is the last resort ([Events, extension points and plugins](events-extension-points-and-plugins.md)).
2. **Assume a long-lived process.** Under Velocity a worker serves many requests: no per-request data in statics
   without a reset, idempotent registrations ([Concurrency, statics and long-lived processes](concurrency-statics-and-long-lived-processes.md)).
3. **Write for every database.** Go through `eZDB::instance()`, escape and cast, and ship schema changes for every
   driver ([Writing portable SQL](writing-portable-sql.md)).
4. **A published tag never changes.** A mistake in a release is fixed by the next version, never by moving a tag
   ([Version numbers and immutable tags](version-numbers-and-immutable-tags.md)).
5. **Every break has upgrade notes.** A change that needs an action from an installation gets a page in
   `doc/eng-us/bc/6.0/` ([Backward compatibility policy](backward-compatibility-policy.md)).

## Map of the software engineering pages

### Orientation

| Page | What it helps you do |
|---|---|
| [Exponential for software engineers](exponential-for-software-engineers.md) | Understand the system, its stack and its constraints on one page |
| [Reading path for software engineers](software-engineers-reading-path.md) | Read the pages in a sensible order, by role |
| [Evaluating Exponential](evaluating-exponential.md) | Run a structured technical evaluation |
| [Glossary for software engineers](glossary-for-software-engineers.md), [Software engineer FAQ](software-engineer-faq.md) | Terms and quick answers |

### Architecture

| Page | What it helps you do |
|---|---|
| [Architecture overview](architecture-overview.md) | See the layers and their responsibilities |
| [The request lifecycle](request-lifecycle.md) | Follow a request from `index.php` to the response |
| [Kernel, lib and extensions: the layering](kernel-lib-and-extensions.md) | Know which code may depend on which |
| [Siteaccesses and configuration](siteaccesses-and-configuration.md) | Understand the INI cascade and siteaccess matching |
| [Modules, views and access control](modules-views-and-access-control.md) | Understand routing and the permission model |
| [The content model for engineers](content-model-for-engineers.md) | Understand classes, objects, versions, nodes and datatypes |
| [Persistence and the database layer](persistence-and-the-database-layer.md) | Understand `eZDB` and `eZPersistentObject` |
| [The template engine and the design chain](template-engine-and-design-chain.md) | Understand compiled templates, overrides and designs |
| [Events, extension points and plugins](events-extension-points-and-plugins.md) | Extend without forking |
| [Console, cronjobs and background work](console-cron-and-background-work.md) | Run work outside a web request |

### Data

| Page | What it helps you do |
|---|---|
| [Databases: choosing and running one](databases-and-choosing-one.md) | Choose among the six supported databases |
| [Schema changes and migrations](schema-changes-and-migrations.md) | Ship a schema change that every installation can apply |
| [Writing portable SQL](writing-portable-sql.md) | Write queries that work on every driver |

### Runtime, performance and scaling

| Page | What it helps you do |
|---|---|
| [Caching layers for engineers](caching-layers-for-engineers.md) | Know every cache and what it is keyed on |
| [Cache invalidation and clearing](cache-invalidation-and-clearing.md) | Invalidate correctly after a change |
| [Application servers](application-servers.md) | Choose between Apache or nginx with PHP-FPM, Velocity and FrankenPHP |
| [Velocity persistent workers](velocity-persistent-workers.md) | Understand the worker pool and its reset model |
| [Concurrency, statics and long-lived processes](concurrency-statics-and-long-lived-processes.md) | Write code that is safe in a persistent worker |
| [Scaling and clustering](scaling-and-clustering.md) | Plan several web nodes and shared storage |
| [Performance measurement and benchmarks](performance-measurement-and-benchmarks.md) | Measure before and after a change |

### Operations

| Page | What it helps you do |
|---|---|
| [Observability: logs, debug bar and audit trail](observability-logs-debug-and-audit.md) | See what the system is doing |
| [Deploying a change](deploying-a-change.md) | Ship a PHP, settings or template change in the right order |
| [Incident handling](incident-handling.md) | Respond to an outage or a regression |
| [Multi-site installations](multi-site-installations.md) | Run several sites from one installation |
| [Internationalization and right to left](i18n-and-right-to-left.md) | Handle languages, locales and text direction |

### Quality

| Page | What it helps you do |
|---|---|
| [Testing strategy](testing-strategy.md) | Know which kinds of tests exist and when each runs |
| [DB-less tests and test suites](db-less-tests-and-test-suites.md) | Write and run tests without a database |
| [Static analysis and code style](static-analysis-and-code-style.md) | Work with PHPStan, the baseline and phpcs |
| [CI gates](ci-gates.md) | Read a red check and reproduce it |
| [Code review checklist](code-review-checklist.md), [Design review checklist](design-review-checklist.md) | Review a change or a design |
| [Handling technical debt](technical-debt.md) | Find, record and pay down debt |

### Release engineering and compatibility

| Page | What it helps you do |
|---|---|
| [Release engineering](release-engineering.md) | Cut a release of the kernel or an extension |
| [Version numbers and immutable tags](version-numbers-and-immutable-tags.md) | Choose the next number and never move a tag |
| [Changelogs and upgrade notes](changelogs-and-bc-notes.md) | Write the record of a change |
| [Dependency management with Composer](dependency-management-with-composer.md) | Pin, repin and resolve packages |
| [Backward compatibility policy](backward-compatibility-policy.md) | Know what counts as a break and what must accompany it |

### Security

| Page | What it helps you do |
|---|---|
| [Security engineering overview](security-engineering-overview.md) | Know the security model, the defaults and the disclosure process |
| [HTTP security: headers, CSP, request rules and proxies](http-security-csp-and-proxies.md) | Configure the HTTP layer safely |
| [Authentication, sessions and two-factor sign-in](authentication-sessions-and-2fa.md) | Understand sign-in and sessions |
| [Security review checklist](security-review-checklist.md) | Review a change for security |

### Interfaces and collaboration

| Page | What it helps you do |
|---|---|
| [API design: REST, ezjscore and headless](api-design-rest-ezjscore-headless.md) | Choose and design an API |
| [Git workflow](git-workflow.md) | Work with branches, commits and pull requests |
| [Contributing upstream](contributing-upstream.md) | Send a change to the kernel or an extension |
| [Documentation as code](documentation-as-code.md) | Write and check help pages like code |

## Pages from other audiences you will use

| Page | Why software engineers read it |
|---|---|
| [Extension development](../extension-development/README.md) | The full course from user to extension developer, with an API reference |
| [Application creators: start here](../creators/app-creators/app-creators-start-here.md) | Building applications on Exponential, with code |
| [PHP developers: start here](../php-developers/php-developers-start-here.md) | The PHP side in depth |
| [Web framework developers: start here](../web-framework-developers/web-framework-developers-start-here.md) | Exponential compared with other web frameworks |
| [Hosting companies: start here](../hosting/hosting-companies-start-here.md) | Servers, panels, operations and runbooks |
| [Database support](../database-support.md) | The support statement for every database |
| [Develop with Git](../develop-with-git.md), [Update with Git](../update-with-git.md) | Working with clones of the kernel and extensions |
| [Security hardening](../security-hardening.md) | The attacker-oriented hardening guide |
| [What Exponential is](../leadership/what-exponential-is.md) | The product family and what each product contains |

## Finding these pages again

- In the help, filter by the audience **Software Engineers**, or open **All pages** and the folder
  `software-engineers`.
- Each page has **Edit on GitHub**. If a statement here is wrong or out of date, correct it there; the pages are
  checked like code ([Documentation as code](documentation-as-code.md)).

## Related pages

- [Exponential for software engineers](exponential-for-software-engineers.md)
- [Architecture overview](architecture-overview.md)
- [Reading path for software engineers](software-engineers-reading-path.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
