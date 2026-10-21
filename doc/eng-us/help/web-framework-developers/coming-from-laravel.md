---
title: Coming from Laravel
audience: web-framework-developer, developer
category: web-frameworks
order: 440
summary: A guide for Laravel developers - routes, controllers, Blade, Eloquent, migrations, middleware, gates, queues, the scheduler, Artisan and Octane, each with its Exponential counterpart, what you get without code (administration, versions, translations, permissions), the habits to adjust, and a first week plan.
keywords: Laravel, coming from Laravel, Eloquent, Blade, Artisan, migrations, middleware, gates, policies, queues, scheduler, Octane, service container, service providers, web framework developer
type: help
---
# Coming from Laravel

This page is for developers who build with Laravel. Laravel gives you a toolkit and you build the CMS; Exponential
gives you a CMS with an application framework underneath. Much of what you would code (admin screens, revisions,
translations, permissions, slugs) is already there; some Laravel conveniences have no counterpart.

## Contents

1. [Side by side](#side-by-side)
2. [What you get without code](#what-you-get-without-code)
3. [What you will miss](#what-you-will-miss)
4. [Habits to adjust](#habits-to-adjust)
5. [Your first week](#your-first-week)

## Side by side

| Laravel | Exponential 6 | Page |
|---|---|---|
| `routes/web.php` | `module.php` views; URL aliases for content | [Routing](routing-with-modules-and-views.md) |
| Controllers | module views | [Controllers](controllers-as-module-views.md) |
| Blade | the template language | [The view layer](templates-vs-twig-and-blade.md) |
| Eloquent models | content classes for editorial data; `eZPersistentObject` for your tables | [The content model](the-content-model-for-orm-users.md), [ORM](orm-and-ezpersistentobject.md) |
| Migrations, seeders | `.dba` schema files, upgrade scripts, packages | [Migrations](migrations-and-dba-schema-files.md) |
| Middleware | request rules, input and output listeners | [Middleware](middleware-and-request-rules.md) |
| `@csrf` | added automatically for signed-in users by `ezformtoken` | [Forms and CSRF](forms-and-csrf-protection.md) |
| Form requests, validation rules | checks in the view or a validator class; datatypes for content | [Validation](validation.md) |
| Gates and policies | functions in `module.php`, roles and policies as data | [Authorization](authorization-roles-and-policies.md) |
| Events, observers | `ezpEvent`, workflows on content operations | [Events](events-and-listeners.md), [Workflows](workflows-triggers-and-domain-events.md) |
| Queues, Horizon | `expqueue`, **Setup > Job queue** | [Queues](queues-and-background-jobs.md) |
| Scheduler | cronjob parts | [Scheduled tasks](scheduled-tasks-and-cronjobs.md) |
| Artisan | `bin/php/console` | [Command line](cli-and-console-commands.md) |
| `.env`, `config/*.php` | `EXP_ENV`, INI files | [Configuration](configuration-ini-files.md), [Environments](environments-and-secrets.md) |
| `Cache::remember()` | `processCache()`, `{cache-block}`, the view cache | [Caching](caching-layers.md) |
| `__()`, `lang/` | `ezpI18n::tr()`, the `i18n` operator, `.ts` files | [Translations](i18n-l10n-and-rtl.md) |
| Octane | Velocity's persistent workers | [Servers and deployment](servers-and-deployment.md) |
| Sanctum, Passport | personal API keys, OAuth2 | [REST and JSON APIs](rest-and-json-apis.md) |

## What you get without code

- An administration for every content class, with drafts, versions, history and compare.
- Translations of content and per-language URL aliases.
- Roles and policies administrators edit, limited by section, class, subtree, language or state.
- Multisite and multilingual siteaccesses on one database.
- A shop, workflows with approval, search, image variations, a page builder (Exponential Layouts) and a REST API
  ([What Exponential is](../leadership/what-exponential-is.md#what-the-kernel-does)).

## What you will miss

- A service container and service providers ([Dependency injection](dependency-injection-and-services.md)).
- Named routes and route model binding.
- Eloquent's relations, scopes and query builder ([Queries](querying-content-and-tables.md)).
- Blade components and auto-escaping.
- An in-process HTTP test client and model factories ([Testing](testing.md)).

## Habits to adjust

- Before creating a table, ask whether editors manage the data: if so, it is a content class.
- Escape every printed value with `|wash`.
- Run `./console exp:velocity deploy` after code changes, as you would `octane:reload`.
- Check where a setting comes from with `./console exp:ini where` before changing it.

## Your first week

Follow the plan on [Coming from Symfony](coming-from-symfony.md#your-first-week); it is the same for Laravel developers,
with [Task: build a small CRUD module](your-first-module-step-by-step.md) as the centerpiece.

## Related pages

- [Concept dictionary](concept-dictionary.md)
- [When to choose Exponential](when-to-choose-exponential.md)
- [Ten pitfalls for framework developers](pitfalls-for-framework-developers.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
