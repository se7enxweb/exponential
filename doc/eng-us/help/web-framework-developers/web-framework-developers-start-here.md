---
title: "Web framework developers: start here"
audience: web-framework-developer, developer
category: web-frameworks
order: 1
summary: Where developers who know Symfony, Laravel, Slim, CodeIgniter, Yii, CakePHP, Laminas, Django, Rails, Express or Spring start with Exponential - what it is in their terms, the first hour, a map of every page that translates framework concepts (request lifecycle, routing, controllers, templates, ORM, migrations, configuration, DI, middleware, events, security, caching, i18n, CLI, queues, APIs, testing, deployment, the Symfony bridge and the platform products) and the pages of other audiences to use next.
keywords: web framework developer, framework, Symfony, Laravel, Slim, CodeIgniter, Yii, CakePHP, Laminas, Django, Rails, Express, Spring, start here, overview, index, concept map, translation, onboarding
featured: true
type: help
---
# Web framework developers: start here

This section of the help is for developers who know a web framework (Symfony, Laravel, Slim, CodeIgniter, Yii,
CakePHP, Laminas, Django, Rails, Express, Spring) and want to build on Exponential. Exponential describes itself as "a
professional PHP application framework with advanced CMS (content management system) functionality" (README). These
pages translate what you already know into Exponential's terms, say plainly where something works differently, and
say just as plainly where Exponential has no direct equivalent.

Every class, setting and command named on these pages was checked against the kernel, the settings or the existing
documentation. Where a page compares with another framework, it describes what that framework is known for, without
judging it.

## Contents

1. [Exponential in one paragraph](#exponential-in-one-paragraph)
2. [Your first hour](#your-first-hour)
3. [Five things that are different](#five-things-that-are-different)
4. [Map of the pages](#map-of-the-pages)
5. [Pages from other audiences you will use](#pages-from-other-audiences-you-will-use)
6. [Finding these pages again](#finding-these-pages-again)

## Exponential in one paragraph

**Exponential 6** is a free and open source PHP application framework and content management system under the GNU GPL,
version 2 or later. A request enters `index.php`, is matched to a **siteaccess** (a named configuration per site,
language or administration), translated from a URL alias to a `/<module>/<view>` address, checked against **roles and
policies**, handled by a **module view**, rendered with the **template language** and placed into the pagelayout. Data
editors manage lives in **content classes** with versions and translations; your own tables use `eZPersistentObject`.
Configuration is **INI files in layers**; code lives in **extensions** installed with Composer. It is served by Apache or
nginx with PHP-FPM, by **Exponential Velocity**, a persistent-worker server that is part of the product as of 6.0.15, or
by FrankenPHP. The code base was developed from 1999, first under the name eZ Publish, which its creator discontinued
after almost 18 years; 7x continued it as Exponential ([Who is 7x?](../who-is-7x.md)).

## Your first hour

1. **Install** (10 minutes): `./console exp:install` gives a site on SQLite
   ([Installing in one command](../../features/6.0/install-in-one-command.md)).
2. **Orient** (10 minutes): [Exponential for web framework developers](exponential-for-framework-developers.md) and
   your "coming from" page ([Symfony](coming-from-symfony.md), [Laravel](coming-from-laravel.md),
   [Slim, CodeIgniter, Yii, CakePHP or Laminas](coming-from-micro-and-classic-php-frameworks.md),
   [Django or Rails](coming-from-django-and-rails.md), [Express or Spring](coming-from-express-and-spring.md)).
3. **Translate** (10 minutes): the [concept dictionary](concept-dictionary.md).
4. **Follow a request** (10 minutes): [The request lifecycle](the-request-lifecycle.md).
5. **Build something** (20 minutes to start): [Task: build a small CRUD module](your-first-module-step-by-step.md).

## Five things that are different

1. **Content is the model.** Editorial data is a content class with versions, translations and permissions, not a table
   you design ([The content model for ORM users](the-content-model-for-orm-users.md)).
2. **Siteaccess before routing.** A whole configuration is chosen per request ([Siteaccesses](siteaccesses-hosts-and-tenants.md)).
3. **No service container.** Handlers are named in INI settings ([Dependency injection](dependency-injection-and-services.md)).
4. **Templates do not escape automatically** and are overridden rather than extended ([The view layer](templates-vs-twig-and-blade.md)).
5. **Persistent workers.** Under Velocity, code must not keep per-request state in statics ([Servers and deployment](servers-and-deployment.md)).

## Map of the pages

### Orientation

| Page | What it helps you do |
|---|---|
| [Exponential for web framework developers](exponential-for-framework-developers.md) | See the whole picture in framework terms |
| [Concept dictionary](concept-dictionary.md) | Look up your framework's word and find the Exponential one |
| [Reading path](web-framework-developers-reading-path.md) | Read the pages in a sensible order |
| [When to choose Exponential, and when a plain framework](when-to-choose-exponential.md) | Decide fairly for a project |

### Coming from another framework

| Page | What it helps you do |
|---|---|
| [Coming from Symfony](coming-from-symfony.md) | Map bundles, kernel events, Doctrine, Forms and Security |
| [Coming from Laravel](coming-from-laravel.md) | Map routes, Eloquent, Blade, middleware, queues and Artisan |
| [Coming from Slim, CodeIgniter, Yii, CakePHP or Laminas](coming-from-micro-and-classic-php-frameworks.md) | Map classic MVC and micro framework habits |
| [Coming from Django or Rails](coming-from-django-and-rails.md) | Map URLconf, ORMs, the admin, signals and commands |
| [Coming from Express or Spring](coming-from-express-and-spring.md) | Understand the PHP process model and map the rest |

### The request

| Page | What it helps you do |
|---|---|
| [The request lifecycle](the-request-lifecycle.md) | Know where your code runs, from `index.php` to the output filters |
| [Siteaccesses: matching a request to a site](siteaccesses-hosts-and-tenants.md) | Configure sites, languages and the administration per request |
| [Routing: modules, views and parameters](routing-with-modules-and-views.md) | Declare addresses and generate URLs |
| [Pretty URLs: URL aliases and URL wildcards](url-aliases-and-wildcards.md) | Use slugs, redirects and rewrites without code |
| [Controllers: module views](controllers-as-module-views.md) | Write a view as a script or a class, and change kernel views |
| [Responses, redirects, errors and JSON](responses-redirects-and-errors.md) | Return pages, redirects, errors and JSON |

### Views and templates

| Page | What it helps you do |
|---|---|
| [The view layer: the template language beside Twig and Blade](templates-vs-twig-and-blade.md) | Write templates and avoid the classic mistakes |
| [Template resolution: designs and overrides](template-resolution-designs-and-overrides.md) | Know which file renders and override it |
| [Getting data into templates: fetch functions and operators](fetch-functions-and-template-data.md) | Give templates data and helpers |

### Data

| Page | What it helps you do |
|---|---|
| [The content model for ORM users](the-content-model-for-orm-users.md) | Model editorial data as classes, objects, versions and nodes |
| [ORM and active record: eZPersistentObject](orm-and-ezpersistentobject.md) | Map your own tables to classes |
| [Queries: content fetches, eZDB and SQL](querying-content-and-tables.md) | Query content and tables portably |
| [Migrations and seed data: .dba schema files and packages](migrations-and-dba-schema-files.md) | Ship and upgrade schemas and seed data |

### Configuration and wiring

| Page | What it helps you do |
|---|---|
| [Configuration: INI files instead of YAML, PHP arrays or .env](configuration-ini-files.md) | Read, layer and change settings |
| [Environments and secrets: EXP_ENV and settings/override](environments-and-secrets.md) | Run dev, test and prod, and keep secrets out of Git |
| [Dependency injection and services: what exists](dependency-injection-and-services.md) | Wire testable code without a container |
| [Extension points: handlers, INI registration and overrides](extension-points-and-handlers.md) | Find the right place to plug in |
| [Packages: extensions, Composer and bundles](packages-extensions-and-composer.md) | Structure and distribute your code |

### Request handling, events and security

| Page | What it helps you do |
|---|---|
| [Middleware: request rules, input and output listeners](middleware-and-request-rules.md) | Do what middleware does |
| [Events and listeners: ezpEvent](events-and-listeners.md) | Subscribe to kernel events and emit your own |
| [Workflows and triggers: hooks on content operations](workflows-triggers-and-domain-events.md) | Run steps before and after publishing and checkout |
| [Authentication and sessions](authentication-and-sessions.md) | Sign users and machines in |
| [Authorization: roles, policies and limitations](authorization-roles-and-policies.md) | Declare and check permissions |
| [Forms and CSRF protection](forms-and-csrf-protection.md) | Handle forms safely |
| [Validation: input, datatypes and escaping](validation.md) | Validate input and escape output |

### Performance and languages

| Page | What it helps you do |
|---|---|
| [Caching: the layers and how to invalidate them](caching-layers.md) | Cache correctly and clear the right thing |
| [HTTP caching and reverse proxies](http-caching-and-reverse-proxies.md) | Use headers, the HTTP cache and the response cache |
| [Translations, locales and right-to-left](i18n-l10n-and-rtl.md) | Translate the interface and the content |

### Background work, APIs and operations

| Page | What it helps you do |
|---|---|
| [Command line: the console and eZScript](cli-and-console-commands.md) | Run and write commands |
| [Queues and background jobs](queues-and-background-jobs.md) | Move slow work out of requests |
| [Scheduled tasks: cronjobs](scheduled-tasks-and-cronjobs.md) | Run work on a schedule |
| [REST and JSON APIs](rest-and-json-apis.md) | Pick the right API layer |
| [Headless and decoupled front ends](headless-and-decoupled-front-ends.md) | Serve content to another front end |
| [Testing: PHPUnit, database tests and checks](testing.md) | Test without fear |
| [Debugging, logging and errors](debugging-logging-and-errors.md) | See inside a request and read the logs |
| [Servers and deployment: Apache and PHP-FPM, Velocity, FrankenPHP](servers-and-deployment.md) | Serve and deploy |

### Exponential with other frameworks

| Page | What it helps you do |
|---|---|
| [Mixing Exponential and a framework](mixing-exponential-and-a-framework.md) | Combine both in one project |
| [The Symfony legacy bridge for framework developers](symfony-and-the-legacy-bridge.md) | Run Exponential 6 inside Symfony |
| [The Exponential Platform products](the-platform-products.md) | Choose a Symfony-based distribution |

### Hands-on, checks and reference

| Page | What it helps you do |
|---|---|
| [Task: build a small CRUD module step by step](your-first-module-step-by-step.md) | Build every piece once by hand |
| [Ten pitfalls for framework developers](pitfalls-for-framework-developers.md) | Avoid the mistakes everyone makes first |
| [Checklists for framework developers](checklists-for-framework-developers.md) | Evaluate, set up, review and go live |
| [Web framework developer FAQ](web-framework-developer-faq.md) | Get quick answers |
| [Glossary for web framework developers](glossary-for-framework-developers.md) | Look up an Exponential term |

## Pages from other audiences you will use

| Page | Why |
|---|---|
| [Application creators: start here](../creators/app-creators/app-creators-start-here.md) | The detailed guides behind most pages here: modules, datatypes, workflows, queues, REST, deployment |
| [Extension development: the book](../extension-development/README.md) | Every extension point, step by step, from user to maintainer |
| [PHP developers: start here](../php-developers/php-developers-start-here.md) | The PHP side: language, conventions and the kernel's classes |
| [Software engineers: start here](../software-engineers/software-engineers-start-here.md) | Architecture, quality and engineering practice |
| [The template language](../template-language/README.md) | The language in depth, with Smarty comparisons |
| [Kernel architecture](../../standards/architecture/kernel-architecture.md) | The authoritative description of the kernel's layers |
| [Extension points](../../bc/6.0/rad-extension-points.md) | The catalogue of every extension point |
| [Database support](../database-support.md) | The supported databases and how each is tested |

## Finding these pages again

In the help, filter by the audience **Web Framework Developers**; the pages live in the folder
`web-framework-developers`. Search for a framework's name ("Symfony", "Laravel", "Django") to find the comparison that
mentions it.

## Related pages

- [Getting started](../getting-started.md)
- [When something goes wrong](../when-something-goes-wrong.md)
- [Develop with Git](../develop-with-git.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
