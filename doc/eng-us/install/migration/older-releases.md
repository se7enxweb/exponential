---
title: Older releases of this kernel
audience: admin, developer, project-manager
category: install-upgrade
order: 25
summary: Which guide moves a site from an older release of this kernel - eZ Publish 3.x, 4.x and 5.x, the community 2013.x and 2014.x releases, the 5.90 legacy-only line (2015.01, 2017.x and later), LoveStack - to Exponential 6, and how to tell which one you run.
keywords: eZ Publish 3.x, 4.x, 5.x, 5.4, 2014.11, 5.90, 2017.12, LoveStack, ezpublish-legacy, upgrade chain, older versions, legacy
type: install
---
# Older releases of this kernel

Exponential 6 is the continuation of the eZ Publish legacy kernel, a product its creator discontinued after almost 18
years. Sites that still run one of its releases move to Exponential 6 **in place**: the database is updated by the
update files of this repository, the code is replaced, and the extensions are ported to PHP 8. Nothing has to be
exported and imported. The old name appears below only to identify the release you migrate from.

[Migration hub](README.md) · [Checklist](checklist.md)

## Which release do you run?

Read `lib/version.php` of the old installation and the version rows of `ezsite_data`
([chapter 11.2](../11-upgrading.md#112-which-version-do-you-run)); for the 5.x era, look at the installation root
([chapter 15.2](../15-migrating-from-5x-legacy.md#152-which-variant-do-you-run)).

| You run | Guide | The database path |
|---|---|---|
| 3.0 to 3.9 | [Chapter 14.5.1](../14-migrating-from-4x.md#1451-which-starting-points-work) | not directly: the update scripts of those versions are not in this repository; bring the site to 3.10 with the old 3.10 release first, then follow the next row |
| 3.10, 4.0 to 4.7 on PHP 5 | [Chapter 14](../14-migrating-from-4x.md) | the old chain to 5.4, then to 6.0.0, then to today |
| 5.0 to 5.4, community 2013.x and 2014.x (with the Symfony stack) | [Chapter 15](../15-migrating-from-5x-legacy.md) | extract the legacy part; the 5.4 database goes to 6.0.0, then to today |
| The 5.90 legacy-only line (2015.01, 2017.08, 2017.12 and later releases that report `VERSION_MAJOR = 5`, `VERSION_MINOR = 90`) | [Chapter 11.5](../11-upgrading.md#115-from-54-or-590-to-600), with [15.9](../15-migrating-from-5x-legacy.md#159-users-password-hashes-and-sessions) for passwords | to 6.0.0, then to today |
| Releases named after a year later than 2017 | not verified in this repository | read `lib/version.php`: a 5.90 release follows the row above |
| LoveStack (Mugo Web's distribution of the legacy kernel) | [LoveStack: migration](../../bc/6.0/love/MIGRATION.md) | `./console exp:explovestack` checks it and records its database as Exponential's |
| Exponential 6.0.0 to 6.0.14 | [Chapter 11.6](../11-upgrading.md#116-from-any-60x-to-today) | the 6.0.x update files |

Then [chapter 17](../17-migration-reference.md) has the lookup tables, and [chapter 12](../12-troubleshooting.md) the
fixes for what goes wrong.

## The steps every older release shares

1. **Copy, never in place.** Restore a backup of the old site on a staging server and migrate the copy.
2. **Preflight**: charset and table type (MySQL), the preflight queries of [14.5.3](../14-migrating-from-4x.md#1453-preflight-queries).
3. **Phase A**: every SQL update file of the chain, in order ([14.5.5](../14-migrating-from-4x.md#1455-phase-a-the-sql-files)).
4. **Phase B**: the PHP repair scripts, against the finished schema ([14.5.6](../14-migrating-from-4x.md#1456-phase-b-the-repair-scripts)).
5. **Code**: a fresh Exponential 6 ([chapter 3](../03-getting-the-code.md)); settings, designs and extensions carried
   over; extensions ported to PHP 8 ([14.9](../14-migrating-from-4x.md#149-porting-your-own-extensions-from-php-5-to-php-8)).
6. **Verify** ([14.12](../14-migrating-from-4x.md#1412-verify-the-content-after-the-migration)), rehearse, cut over;
   keep the old site until sign-off ([14.14](../14-migrating-from-4x.md#1414-rollback-plan)).

The addresses of an in-place migration stay the same (the URL alias tables move with the database), so redirects are
needed only for what you change on purpose.

## Moving on from there

Once the site runs on Exponential 6, every other path of the [hub](README.md) is open: the Symfony stack next to it
([Platform Legacy](exponential-platform-legacy.md)), a page-building distribution ([Nexus](exponential-platform-nexus.md)),
or a Symfony-only platform ([DXP](exponential-platform-dxp.md)). Do one move at a time.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../help/partners/partners-start-here.md) and [How to apply](../../help/partners/how-to-apply.md).
<!-- exp-partner-block:end -->

This page is part of the Exponential documentation and is free documentation under the GNU Free Documentation License
1.3 or any later version ([the license of this documentation](../../README-LICENSE.md)).
