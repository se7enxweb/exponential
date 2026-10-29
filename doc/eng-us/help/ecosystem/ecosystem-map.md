---
title: The Exponential ecosystem map
audience: ecosystem, developer, partner, project-manager
category: ecosystem
order: 3
summary: How the parts of the Exponential ecosystem connect - from PHP and Composer through the kernel, Velocity and the bundled extensions to the Platform line, sites and their users - with what depends on what and where each part is documented.
keywords: ecosystem map, architecture, dependencies, kernel, Velocity, extensions, Platform, Nexus, DXP, Legacy bridge, Composer, Packagist, sites, users
type: help
---
# The Exponential ecosystem map

## Contents

1. [The layers](#the-layers)
2. [What depends on what](#what-depends-on-what)
3. [Two families: legacy kernel and Symfony platform](#two-families-legacy-kernel-and-symfony-platform)
4. [Where the code lives](#where-the-code-lives)

## The layers

| Layer | Parts | Page |
|---|---|---|
| 1. Language and tools | PHP 7.4 and later, Composer, Packagist, Git | [PHP versions](php-stack/php-versions.md), [Composer and Packagist](packages/composer-and-packagist.md) |
| 2. Libraries | Zeta Components (kernel); Symfony, Twig, Doctrine (platform line) | [Upstream projects and forks](php-stack/upstream-projects-and-forks.md) |
| 3. Kernel | Exponential 6 (`se7enxweb/exponential`) | [The Exponential 6 kernel](products/exponential-6-kernel.md) |
| 4. Serving | Velocity, FrankenPHP, PHP's built-in server, Apache or nginx with PHP-FPM | [Web servers and engines](php-stack/web-servers-and-engines.md) |
| 5. Storage | SQLite 3, PostgreSQL, MongoDB, MariaDB, Oracle, MySQL | [Databases](php-stack/databases.md) |
| 6. Extensions | 66 bundled se7enxweb packages, among them Layouts and Intelligence; extensions of others | [The bundled extensions](packages/the-bundled-extensions.md) |
| 7. Designs and themes | Admin designs, AdminUI, site themes | [Themes and designs](packages/themes-and-designs.md) |
| 8. Sites | Site packages, custom sites, integrations, apps through the REST API | [Site packages and the package server](packages/site-packages-and-the-package-server.md) |
| 9. Users | Owners, editors, members, visitors | [Users in the ecosystem](people/users-in-the-ecosystem.md) |

## What depends on what

```text
your site  ->  extensions and designs  ->  Exponential 6 kernel  ->  Zeta Components  ->  PHP
                                              ^
                    Velocity serves it        |  legacy bridge
                                              |
                     Exponential Platform Legacy / Nexus (Symfony + the kernel)
                     Exponential Platform 3.2.9 / DXP     (Symfony only, no kernel)
```

- An extension depends on the kernel version it was written for; check its `composer.json`
  ([Compatibility and dependencies](packages/compatibility-and-dependencies.md)).
- Velocity serves PHP applications and is required by the kernel's `composer.json` since 6.0.15
  ([Exponential Velocity](products/exponential-velocity.md)).
- Legacy and Nexus run the kernel next to the Symfony platform through the legacy bridge; DXP and Platform 3.2.9 do
  not include the kernel ([The Platform line](products/the-platform-line.md)).

## Two families: legacy kernel and Symfony platform

| | Exponential 6 kernel | Symfony platform line |
|---|---|---|
| Framework | Its own (CMF), templates in its template language | Symfony, Twig templates |
| Packages | `se7enxweb/exponential` and extensions | Platform, Legacy, Nexus, DXP distributions and their bundles |
| Can be combined | Yes: Legacy and Nexus include it | Yes, through the legacy bridge |
| Map of repositories | [Who is 7x?](../who-is-7x.md#the-extension-ecosystem) | [The Exponential platform ecosystem](../../history/ecosystem.md) |

## Where the code lives

- All repositories: <https://github.com/se7enxweb>.
- All Composer packages: the vendor `se7enxweb` on Packagist.
- The change history of 53 repositories since December 2023, with the ledgers: [The Exponential platform
  ecosystem](../../history/ecosystem.md).

## Related pages

- [Ecosystem: start here](ecosystem-start-here.md)
- [Products](products/README.md)
- [Packages](packages/README.md)
- [The PHP stack](php-stack/README.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
