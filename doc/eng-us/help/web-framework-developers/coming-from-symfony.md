---
title: Coming from Symfony
audience: web-framework-developer, developer
category: web-frameworks
order: 430
summary: A guide for Symfony developers - what maps directly (front controller, kernel events, console, Twig-like templates, bundles) and what does not (container, routing, Doctrine, Forms, Security), the habits to keep and to drop, a first week plan, and when to use the Symfony-based platform or the legacy bridge instead.
keywords: Symfony, coming from Symfony, HttpKernel, bundle, services.yaml, container, Doctrine, Twig, Forms, Security, Messenger, Console, kernel events, legacy bridge, Exponential Platform, web framework developer
type: help
---
# Coming from Symfony

This page is for developers who build with Symfony. Many Exponential concepts have a Symfony counterpart, a few do not,
and there is a Symfony-based Exponential product line if you want to stay in Symfony entirely. Read it with the
[concept dictionary](concept-dictionary.md) at hand.

## Contents

1. [What maps directly](#what-maps-directly)
2. [What works differently](#what-works-differently)
3. [Habits to keep, habits to drop](#habits-to-keep-habits-to-drop)
4. [Your first week](#your-first-week)
5. [Staying in Symfony](#staying-in-symfony)

## What maps directly

| Symfony | Exponential 6 | Page |
|---|---|---|
| `public/index.php`, `Kernel::handle()` | `index.php`, `ezpKernel` with `ezpKernelWeb` | [The request lifecycle](the-request-lifecycle.md) |
| `kernel.request`, `kernel.response` listeners | `request/input` listeners, `response/output` filters | [Events and listeners](events-and-listeners.md) |
| `bin/console`, `#[AsCommand]` | `bin/php/console`, `Exponential\Runnable\Command` | [Command line](cli-and-console-commands.md) |
| bundles, `config/bundles.php` | extensions, `ActiveExtensions[]` | [Packages](packages-extensions-and-composer.md) |
| `APP_ENV`, `when@prod` | `EXP_ENV`, settings variants | [Environments](environments-and-secrets.md) |
| Messenger | `expqueue` | [Queues](queues-and-background-jobs.md) |
| Translation component | `.ts` catalogues, `i18n` operator | [Translations](i18n-l10n-and-rtl.md) |
| Web profiler | Exp debug bar | [Debugging](debugging-logging-and-errors.md) |

## What works differently

| Symfony | Exponential 6 | Page |
|---|---|---|
| DependencyInjection container, autowiring | no container; handler classes named in INI, factories | [Dependency injection](dependency-injection-and-services.md) |
| Routing component, attribute routes | `/<module>/<view>` declared in `module.php`; URL aliases for content | [Routing](routing-with-modules-and-views.md) |
| Controllers returning `Response` | module views returning a `$Result` array | [Controllers](controllers-as-module-views.md) |
| Twig with `extends` and blocks, auto-escaping | the template language with overrides, `\|wash` by hand | [The view layer](templates-vs-twig-and-blade.md) |
| Doctrine entities and migrations | content classes; `eZPersistentObject`; `.dba` schema files | [ORM](orm-and-ezpersistentobject.md), [Migrations](migrations-and-dba-schema-files.md) |
| Form component | templates, module actions, `ezformtoken` | [Forms](forms-and-csrf-protection.md) |
| Security: firewalls, voters | login handlers, roles and policies stored as data | [Authorization](authorization-roles-and-policies.md) |
| YAML configuration | INI files in layers | [Configuration](configuration-ini-files.md) |
| HttpCache, ESI | role-aware HTTP cache, Velocity response cache; no ESI | [HTTP caching](http-caching-and-reverse-proxies.md) |

## Habits to keep, habits to drop

Keep: thin controllers with logic in services, constructor parameters for testability, PHPUnit, environment-specific
configuration, never committing secrets, one command per deploy step.

Drop: looking for a container to register services in; expecting auto-escaping; writing an entity and a migration for
something editors manage (make it a content class); configuring the same value in five layers without checking
`exp:ini where`; assuming a fresh process per request (Velocity keeps workers, as RoadRunner would).

## Your first week

1. Install a development copy (`./console exp:install --apply`) and read [Exponential for web framework developers](exponential-for-framework-developers.md).
2. Day two: [The request lifecycle](the-request-lifecycle.md), [Siteaccesses](siteaccesses-hosts-and-tenants.md),
   [Configuration](configuration-ini-files.md).
3. Day three: [Task: build a small CRUD module](your-first-module-step-by-step.md).
4. Day four: [The content model for ORM users](the-content-model-for-orm-users.md) and
   [Template resolution](template-resolution-designs-and-overrides.md).
5. Day five: [Servers and deployment](servers-and-deployment.md) and [Testing](testing.md).

## Staying in Symfony

If your project must be a Symfony application, two routes exist:

- **Exponential Platform DXP** is a Symfony-only CMS, built like any Symfony project ([The platform products](the-platform-products.md)).
- **The legacy bridge** runs Exponential 6 inside a Symfony platform project, so editors keep the legacy
  administration while you write Symfony code and Twig ([The Symfony legacy bridge](symfony-and-the-legacy-bridge.md)).

## Related pages

- [Coming from Laravel](coming-from-laravel.md)
- [Mixing Exponential and a framework](mixing-exponential-and-a-framework.md)
- [Ten pitfalls for framework developers](pitfalls-for-framework-developers.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
