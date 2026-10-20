---
title: Migrating customers from older versions
audience: hosting
category: hosting
order: 30
summary: What a host needs to know when a customer's site runs an older release of the software (3.x, 4.x and 5.x under the old name, or an older 6.0.x): what stays the same, what changes, the effort drivers, a staging plan, and the installation book's chapters for each starting point.
keywords: migration, upgrade, older versions, 3.10, 4.x, 5.x, legacy, 6.0.x, PHP 5, PHP 8, update files, database update, extensions, porting, staging, rehearsal, dbupdate
type: help
---
# Migrating customers from older versions

This page is for hosting companies whose customers run an older release: a site from the 3.x, 4.x or 5.x era (when
the product was still called eZ Publish, a product its creator discontinued after almost 18 years) or an older
Exponential 6.0.x. It answers: how big is the job, what drives the effort, and where are the exact steps?

> **Note**
> The steps themselves are in the installation book. This page tells a host how to assess, offer and stage the work;
> it does not repeat the database runbooks.

## Contents

1. [Starting points](#starting-points)
2. [What stays the same](#what-stays-the-same)
3. [What changes](#what-changes)
4. [What drives the effort](#what-drives-the-effort)
5. [From an older 6.0.x release](#from-an-older-60x-release)
6. [A staging plan](#a-staging-plan)
7. [What to tell the customer](#what-to-tell-the-customer)

## Starting points

| The site runs | Chapter |
|---|---|
| 3.10, 4.0 to 4.7, an early 5.x legacy kernel on PHP 5 | [Chapter 14: Migrating from the 4.x line](../../../install/14-migrating-from-4x.md) |
| 5.0 to 5.4, the community releases 2013.x and 2014.x, 5.90 (legacy kernel beside a Symfony stack) | [Chapter 15: Migrating from the 5.x legacy stack](../../../install/15-migrating-from-5x-legacy.md) |
| Exponential 6.0.x, older than the current release | [Chapter 11: Upgrading, 11.6](../../../install/11-upgrading.md#116-from-any-60x-to-today) |
| any of these, table and script reference | [Chapter 17: Migration reference](../../../install/17-migration-reference.md) |

## What stays the same

Exponential 6.0 is "the same legacy kernel, carried forward" ([14.1](../../../install/14-migrating-from-4x.md#141-what-changes-and-what-does-not)):

- **The database schema family.** The same tables; the old update files bring an old database up to the current
  schema. Nothing is exported and re-imported.
- **The datatypes.** The 3.x/4.x kernel datatypes are still there; rich text stays `ezxmltext`.
- **Templates and settings.** The template language and the INI block and key names keep their names.
- **The extension API.** Modules, datatypes, workflow events, fetch functions and template operators are written the
  same way.

## What changes

From the same section of chapter 14: PHP 5 to PHP 8 ("your own PHP code is the largest piece of work"), eZ Components
replaced by Zeta Components installed by Composer, the cluster handlers that stored files in the database, YUI (gone),
jQuery (4.0.0), the online editor, several rewrite rules and a stricter security baseline.

For a 5.x site, the move is "an extraction rather than a rewrite": the legacy part is taken out of the Symfony
application, its Symfony configuration is written down in INI files, and the database is upgraded in place
([Chapter 15](../../../install/15-migrating-from-5x-legacy.md)).

## What drives the effort

Chapter 14 is clear that "the time depends far more on the size of a few tables than on the version you come from"
([14.15](../../../install/14-migrating-from-4x.md#1415-how-long-it-takes)). For an offer, assess:

| Driver | Grows with | Ask for |
|---|---|---|
| Database steps | database size, rows of `ezcontentobject_attribute`, `ezurlalias_ml` | a database dump for a rehearsal |
| Search reindex | number of objects; "usually the longest single step" | the object count |
| Copying files | number and size of files in `var/` | the size of `var/storage` |
| Porting extensions | "lines of your own PHP and templates, not database size" | the list of custom extensions and their source |
| Designs | custom templates and JavaScript that used YUI or old jQuery | the design extensions |

> **Tip**
> Make the rehearsal the first billable step. The measured time of the rehearsal is the only reliable basis for
> the production window and for the rest of the offer.

## From an older 6.0.x release

This is an update, not a migration. In outline ([11.6](../../../install/11-upgrading.md#116-from-any-60x-to-today)):

1. Put the new code in place (Git checkout of the tag, or `composer update se7enxweb/exponential`).
2. Regenerate the autoloads and clear the caches:

   ```bash
   php bin/php/ezpgenerateautoloads.php -e
   php bin/php/ezcache.php --clear-all
   ```

3. Reload the PHP that serves the site; under Velocity, `php bin/php/console exp:velocity deploy` runs every step in
   order ([Deploying changes](../operations/deploying-changes.md)).
4. Apply the database update file of the line once, whole, in order:
   `update/database/<engine>/6.0/dbupdate-6.0.0-6.0.15.sql`, then the audit tables script
   `php update/common/scripts/6.0/createaudittables.php` (with `--dry-run` first).
5. Read the checklist of each period you pass and check the customer's own extensions (11.6, steps 3 and 4).

See [Upgrading customer sites](../operations/upgrading-customer-sites.md) for the host's routine.

## A staging plan

1. Copy the old site (database and files) to a staging machine on your platform.
2. Walk the chapter for the starting point on that copy, and write down every step and its duration.
3. Port the custom extensions to PHP 8; the checklist is in
   [14.9](../../../install/14-migrating-from-4x.md#149-porting-your-own-extensions-from-php-5-to-php-8).
4. Let the customer test the staging copy.
5. Repeat the database steps on a fresh copy for the final run, in a maintenance window.
6. Keep the rollback plan of the chapter ready ([14.14](../../../install/14-migrating-from-4x.md#1414-rollback-plan)).

## What to tell the customer

- Their old version receives no security fixes: only the 6.0 line is maintained (`SECURITY.md`).
- Their content, users and URLs carry over; the database is upgraded, not rebuilt.
- Their custom code may need porting; that is the largest unknown and is measured during the rehearsal.
- The final switch needs a content freeze and a maintenance window.

## Related pages

- [Migrating customers to your hosting](migrating-customers-to-your-hosting.md)
- [From eZ Platform and Ibexa](from-ez-platform-and-ibexa.md)
- [Upgrading customer sites](../operations/upgrading-customer-sites.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
