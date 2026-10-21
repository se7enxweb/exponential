---
title: Architecture overview
audience: software-engineer, developer
category: software-engineering
order: 100
summary: The layers of an Exponential installation and what each is responsible for - servers, the kernel entry points, the domain classes, the libraries, the presentation layer, extensions, settings and var/ - with the directory layout and the architectural decisions an engineer has to know.
keywords: architecture, layers, kernel, lib, extensions, siteaccess, module, view, content model, persistence, template engine, var, directory layout, software engineer
type: help
---
# Architecture overview

This page is the engineer's map of an Exponential installation: which layer does what, where it lives on disk, and
which architectural decisions shape everything else. The authoritative description, with a rule for each building
block, is [Kernel architecture](../../standards/architecture/kernel-architecture.md); this page summarizes it and adds
the reasoning.

## Contents

1. [The layers](#the-layers)
2. [The directory layout](#the-directory-layout)
3. [Five architectural decisions](#five-architectural-decisions)
4. [Where state lives](#where-state-lives)
5. [What the architecture does not have](#what-the-architecture-does-not-have)

## The layers

```text
 Browser / CLI / cron
        |
 Server: Apache or nginx + PHP-FPM  |  Velocity (qbix)  |  FrankenPHP
        |
 Entry points: index.php  /  bin/php/console  /  runcronjobs.php
        |
 Kernel: ezpKernel -> ezpKernelWeb   settings -> extensions -> siteaccess -> session -> module
        |
 Modules and views: eZModule::run() -> view class  (kernel/<module>/, extensions)
        |
 Domain: kernel/classes  (content, users, roles, datatypes, workflows, caches)
        |
 Libraries: lib/ezdb (eZDB), lib/eztemplate (eZTemplate), lib/ezutils (eZINI, eZSys, ...)
```

| Layer | Responsibility | Main classes |
|---|---|---|
| Server | Accept the connection, serve static files, run PHP | Velocity, PHP-FPM, FrankenPHP ([Application servers](application-servers.md)) |
| Entry points | Start the right kernel for web, console or cron | `index.php`, `bin/php/console`, `runcronjobs.php`, `eZScript` |
| Kernel | Settings, extensions, siteaccess, session, dispatch | `ezpKernel`, `ezpKernelWeb`, `eZSiteAccess`, `eZExtension` |
| Modules and views | URL namespaces and pages, permission checks | `eZModule`, `Exponential\Runnable\ModuleView` |
| Domain | Content, users, roles, datatypes, workflows, notifications, caches | `eZContentObject`, `eZContentObjectTreeNode`, `eZUser`, `eZRole`, `eZDataType`, `eZCache` |
| Libraries | Database, templates, settings, files, locale, mail | `eZDB`, `eZTemplate`, `eZINI`, `eZClusterFileHandler`, `eZLocale` |
| Presentation | Templates, designs, overrides | `design/<design>/templates`, `override.ini` |
| Extensions | Everything an installation adds | `extension/<name>/` |

## The directory layout

From the installation book ([1.2 The parts of an installation](../../install/01-introduction.md#12-the-parts-of-an-installation)):

```text
kernel/, lib/      the kernel: modules, classes, the template engine, the database layer
autoload/          the kernel's class map (autoload/ezp_kernel.php)
extension/         extensions: your application lives here
design/            designs: templates, stylesheets, images
settings/          shipped settings; settings/siteaccess/ and settings/override/ hold yours
var/               runtime: caches, logs, storage, the SQLite database
bin/php/console    one entry point for every command
cronjobs/          the scripts runcronjobs.php runs
index.php          the front controller
```

## Five architectural decisions

1. **One front controller, one dispatch loop.** Every web request goes through `index.php` and
   `ezpKernelWeb::run()`; there is no second web entry point for normal pages. Special entry points exist for REST
   (`index_rest.php`), the tree menu (`index_treemenu.php`) and clustered file serving (`index_cluster.php`).
2. **The siteaccess is the unit of configuration.** A siteaccess is a named configuration selected per request (a
   site, the administration, a language); most behavior differences are settings, not code
   ([Siteaccesses and configuration](siteaccesses-and-configuration.md)).
3. **Everything pluggable is named in settings.** Modules, datatypes, workflow events, template operators, cronjob
   parts, notification handlers, login handlers and cluster file handlers are found through INI lists, so an extension
   plugs in by shipping a class and an `.ini.append.php` file ([Events, extension points and plugins](events-extension-points-and-plugins.md)).
4. **A runtime-defined content model.** Content classes are data, not code: administrators add classes and attributes
   without a deployment, and datatypes give each attribute storage, validation and templates
   ([The content model for engineers](content-model-for-engineers.md)).
5. **All cache files through the cluster file handler.** Caches are written through `eZClusterFileHandler`, so the
   same code runs on one disk or on shared storage for several web nodes ([Scaling and clustering](scaling-and-clustering.md)).

## Where state lives

| State | Where | Shared between web nodes |
|---|---|---|
| Content, users, roles, URL aliases | the database | yes |
| Uploaded files and image variations | `var/<site>/storage` (or the cluster backend) | must be |
| Caches | `var/<site>/cache`, or Redis or Valkey with `sevenx_valkey_cache` | depends on the cache ([Caching layers for engineers](caching-layers-for-engineers.md)) |
| Sessions | PHP's session storage, or the `ezsession` table with `ezpSessionHandlerDB` | must be, for several nodes |
| Logs and audit records | `var/log`, `var/<site>/log` | per node unless collected |
| Settings | `settings/`, extension `settings/` | deployed with the code |

## What the architecture does not have

- **No dependency injection container of its own.** Classes are found through the class map and settings;
  singletons such as `eZINI::instance()` and `eZDB::instance()` are the norm. When the kernel runs inside Symfony,
  `ezpKernel::hasServiceContainer()` and `getServiceContainer()` give access to Symfony's container.
- **No migration runner.** Database updates are SQL files per driver, applied by hand ([Schema changes and migrations](schema-changes-and-migrations.md)).
- **No message broker in the kernel.** Background work runs as cronjob parts, content jobs and the `expqueue`
  extension ([Console, cronjobs and background work](console-cron-and-background-work.md)).
- **No Symfony in the kernel.** The Symfony platform products embed the kernel through the legacy bridge
  ([Symfony and the legacy bridge](../creators/app-creators/symfony-and-the-legacy-bridge.md)).

## Related pages

- [The request lifecycle](request-lifecycle.md)
- [Kernel, lib and extensions: the layering](kernel-lib-and-extensions.md)
- [Architecture for application creators](../creators/app-creators/architecture-for-app-creators.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
