---
title: Coming from Exponential 6
audience: dxp-developer
category: dxp-development
order: 42
summary: A translation table for Exponential 6 developers who start on the DXP - classes to content types, nodes to locations, INI to YAML, TPL to Twig, extensions to bundles, modules to controllers, fetch to the repository - and the honest answer to whether to move a site at all.
keywords: Exponential 6, legacy, migration, INI to YAML, TPL to Twig, extensions to bundles, content class, node, location, fetch, siteaccess, legacy bridge, comparison
type: help
---
# Coming from Exponential 6

This page is for developers who know Exponential 6 (INI settings, `.tpl` templates, extensions) and start a project on
the DXP. It maps what you know to what you will meet, and helps you decide whether an existing site should move.

## Contents

1. [Should the site move?](#should-the-site-move)
2. [The translation table](#the-translation-table)
3. [Habits to keep and habits to change](#habits-to-keep-and-habits-to-change)
4. [Running both](#running-both)

## Should the site move?

Exponential 6 is the maintained line, not a dead end ("Legacy" only means the kernel beside a Symfony platform,
[Knowing the product family](../advocates/knowing-the-product-family.md#common-mix-ups)). Move a site to the DXP
when the team wants Symfony, Twig and Doctrine, or needs what the DXP's APIs offer in that stack. Stay on
Exponential 6 when the site relies on its kernel features that the DXP does not ship: the shop, workflows such as
approval or waiting until a date, Exponential Layouts, Exponential Velocity
([What the DXP ships and what it does not](what-the-dxp-ships-and-what-it-does-not.md#what-it-does-not-ship)).

## The translation table

| Exponential 6 | DXP | Read |
|---|---|---|
| Content class, class attribute | Content type, field definition | [Content, content types and locations](content-types-and-locations.md) |
| Content object, object attribute | Content item, field | same |
| Node | Location | same |
| Datatype | Field type | [The field types it ships](the-field-types-it-ships.md) |
| `settings/*.ini`, `.ini.append.php` | YAML under `config/packages/` | [The project directory layout](the-project-directory-layout.md) |
| Siteaccess in `site.ini` | `ibexa.siteaccess` and `ibexa.system.<scope>` | [Siteaccesses](siteaccesses-site-and-admin.md) |
| Design chain (`[DesignSettings]`) | Design engine: designs of ordered themes | [Twig and the design engine](twig-templates-and-the-design-engine.md) |
| `.tpl` templates, `override.ini` | Twig templates, content view configuration | [Twig functions](twig-functions-you-use-daily.md) |
| `attribute_view_gui` | `ibexa_render_field` | same |
| `fetch( 'content', 'node', ... )` | Repository services in PHP, or a content query field | [Content, content types and locations](content-types-and-locations.md#working-with-the-repository-in-php) |
| Image aliases in `image.ini` | Image variations in YAML | [Image variations](image-variations.md) |
| Extension | Symfony bundle, or code in `src/` | |
| Module and view | Controller and route | |
| Cronjob parts | `ibexa:cron:run`, console commands | [Cron](cron.md) |
| `bin/php/ezcache.php` | `bin/console cache:clear` and the pool commands | [Application cache](application-cache-and-redis.md) |
| Roles and policies | Roles and policies (same idea) | |
| The admin designs | `/adminui/` | [The admin UI](the-admin-ui.md) |

Chapter 16 of the installation guide shows the opposite direction in detail (YAML to INI, Twig to TPL), which reads
just as well as a dictionary ([16.6.5](../../install/16-migrating-from-ez-platform-and-ibexa.md#1665-configuration-siteaccess-yaml-to-ini),
[16.6.6](../../install/16-migrating-from-ez-platform-and-ibexa.md#1666-templates-twig-to-tpl)).

## Habits to keep and habits to change

**Keep**: thinking in content types first; using roles instead of code for access; overriding templates instead of
copying them; clearing only the cache that needs clearing.

**Change**: configuration is compiled into the Symfony container, so any YAML change needs `cache:clear`; services are
injected, not fetched from a global; assets are built with Encore, not packed at runtime.

## Running both

The legacy bridge runs the Exponential 6 kernel inside a Symfony platform on one database, so editors can keep the
legacy administration while a Twig front end grows. It is not part of the DXP: use Exponential Platform Legacy or a
Nexus line that includes the legacy kernel ([Legacy bridge](../../features/6.0/legacy-bridge.md),
[Choosing](choosing-dxp-legacy-nexus-or-exponential-6.md)).

## Related pages

- [Developers: the extension development book](../extension-development/README.md)
- [Choosing DXP, Legacy, Nexus or Exponential 6](choosing-dxp-legacy-nexus-or-exponential-6.md)
- [Legacy bridge](../../features/6.0/legacy-bridge.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
