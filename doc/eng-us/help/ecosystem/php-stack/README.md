---
title: The PHP stack of the Exponential ecosystem
audience: ecosystem, developer, admin, hosting
category: ecosystem
order: 30
summary: The landing page of the PHP stack part of the ecosystem section - PHP versions, databases, web servers and engines, operating systems, the upstream projects Exponential uses and gives back to, and the standards and tooling it follows.
keywords: PHP stack, PHP versions, databases, web servers, Velocity, FrankenPHP, operating systems, Zeta Components, Symfony, Twig, Doctrine, standards, PSR, tooling
type: help
---
# The PHP stack of the Exponential ecosystem

The ecosystem lives "within the PHP stack". These pages describe the parts of that stack Exponential runs on and
works with.

## Contents

1. [The pages of this part](#the-pages-of-this-part)
2. [The stack in one table](#the-stack-in-one-table)

## The pages of this part

| Page | What it answers |
|---|---|
| [PHP versions](php-versions.md) | Which PHP versions run which release |
| [Databases](databases.md) | The six supported databases in the project's order |
| [Web servers and engines](web-servers-and-engines.md) | Velocity, FrankenPHP and the traditional servers |
| [Operating systems](operating-systems.md) | The four tiers of operating system support |
| [Upstream projects and forks](upstream-projects-and-forks.md) | What Exponential uses and gives back to |
| [Standards and tooling](standards-and-tooling.md) | PSR, coding standards, CI, quality checks |

## The stack in one table

| Layer | Exponential 6.0.15 | Source |
|---|---|---|
| Language | PHP 8.0 to 8.5 | `README.md` |
| Package manager | Composer, Packagist | `README.md` |
| Kernel libraries | Zeta Components | `composer.json` |
| Database | SQLite 3 (default), PostgreSQL, MongoDB, MariaDB, Oracle, MySQL | [Database support](../../database-support.md) |
| Server | Velocity (Qbix, FrankenPHP or PHP's built-in server), or Apache, Nginx, LiteSpeed, Caddy, IIS and others | `README.md` |
| Operating system | Almost any that runs PHP 8.0 or later | [Operating system support](../../os-support.md) |
| Search | The built-in search engine; Solr as an option | [Search backends](../../hosting/basics/search-backends.md) |

## Related pages

- [Ecosystem: start here](../ecosystem-start-here.md)
- [Requirements](../../../install/02-requirements.md)
- [Hosting basics](../../hosting/basics/README.md)
- [The PHP stack it stands on and gives back to](../../who-is-the-exponential-foundation.md#the-php-stack-it-stands-on-and-gives-back-to)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
