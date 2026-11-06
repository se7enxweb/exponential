---
title: Upstream projects and forks
audience: ecosystem, developer, partner, advocate
category: ecosystem
order: 35
summary: The PHP projects the Exponential ecosystem stands on and gives back to - PHP, Composer, Zeta Components, Symfony, Twig, Doctrine, Netgen's projects, the Qbix web server, AdminNeo and the front-end libraries - what each is used for, the forks 7x maintains, and how to treat upstream projects respectfully.
keywords: upstream, forks, Zeta Components, expcomponents, eZ Components, Symfony, Twig, Doctrine, Media Site, Qbix, AdminNeo, jQuery, TinyMCE, give back, pull requests upstream
type: help
---
# Upstream projects and forks

## Contents

1. [What Exponential stands on](#what-exponential-stands-on)
2. [Why forks](#why-forks)
3. [Credit and respect](#credit-and-respect)

## What Exponential stands on

| Project | Used for | Given back | Source |
|---|---|---|---|
| PHP | The language | Support for each new PHP version | `README.md` |
| Composer and Packagist | Installation of the kernel and every extension | 219 packages published by se7enxweb | `composer.json`, Packagist |
| Zeta Components | The kernel's libraries (archive, cache, database, feed, image conversion, mail, WebDAV ...) up to 6.0.14; since 6.0.15 through the expcomponents forks | "Forked and patched 47+ Zeta Components repos"; "14 security PRs submitted upstream to zetacomponents"; the official fork expcomponents (Apache 2.0) | se7enxweb profile README, [expcomponents](../../../features/6.0/expcomponents.md) |
| Symfony, Twig, Doctrine | The platform line | Maintained forks for PHP 8.4 and 8.5 | [The Exponential platform ecosystem](../../../history/ecosystem.md) |
| Upstream layouts packages, Media Site, Site API | Exponential Layouts and Nexus | Ports to the Exponential kernel; a fork of layouts-core | `README.md` |
| Qbix web server | The engine of Velocity | A production server built on it | Velocity README |
| AdminNeo | The database source editor | | [sevenx_dse](../../../features/6.0/extensions/sevenx_dse.md) |
| jQuery, TinyMCE, Bootstrap Icons | The administration's scripts, editor and icons | | [October 2026](../../../history/2026/2026-10.md) |

Source table: [The PHP stack it stands on and gives back to](../../who-is-the-exponential-foundation.md#the-php-stack-it-stands-on-and-gives-back-to).

## Why forks

Many upstream packages of the old stack were no longer maintained for current PHP. To keep sites running, 7x keeps
forks that fix them, under the se7enxweb vendor, and lists which fork replaces which upstream package in the
[package map](../../../specifications/6.0/platform-package-map.md). What changed for users of a fork is in
[Package forks and command renames](../../../bc/6.0/platform-package-forks-and-command-renames.md).

## The kernel's libraries: expcomponents

Since 6.0.15 the kernel's own libraries are forks too. The Zeta Components, which started as eZ Components at eZ
Systems (New BSD license) and went on through the Apache Software Foundation's incubator (Apache License 2.0) to an
independent project, are forked as **expcomponents**: one package per library, `se7enxweb/expcomponents-<name>`
(`se7enxweb/expcomponents-mail` for `zetacomponents/mail`), still under the Apache License 2.0, maintained by 7x and the
Exponential Foundation and modernized internally. The `ezc` class names and APIs stay, and each fork replaces its
upstream name in Composer, so code and third-party packages keep working. The first two forks were archive and WebDAV;
the full list, the history and the reasons are on the feature page
[Exponential Components (expcomponents)](../../../features/6.0/expcomponents.md), the upgrade in its
[upgrade notes](../../../bc/6.0/expcomponents.md). Fixes useful to everyone are still offered to the Zeta Components
project as pull requests.

## Credit and respect

- Work from other projects is credited, for example the LoveStack features "with full credit to Mugo Web" (`README.md`).
- Fixes go upstream where upstream is active.
- When you fork, follow [When to fork](../../extension-development/part-5-extend-fork-contribute/when-to-fork.md) and
  [Rebranding ethically](../../extension-development/part-5-extend-fork-contribute/rebranding-ethically.md).
- Speak about other projects fairly ([Respecting other projects and companies](../../advocates/respecting-other-projects-and-companies.md)).

## Related pages

- [The PHP stack](README.md)
- [Companion projects and other companies](../organizations/companion-projects-and-other-companies.md)
- [Attribution and copyright notices](../../extension-development/part-5-extend-fork-contribute/attribution-and-copyright-notices.md)
- [Licenses and compatibility](../../extension-development/part-5-extend-fork-contribute/licenses-and-compatibility.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
