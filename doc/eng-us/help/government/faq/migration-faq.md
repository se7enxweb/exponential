---
title: "FAQ: migration from older systems"
audience: government, project-manager, admin, partner
category: public-sector
order: 100
summary: Answers to the migration questions public bodies ask - moving from older eZ Publish era sites, from eZ Platform or Ibexa, or from other systems such as WordPress, Drupal or static HTML to Exponential, what is kept, what changes and where the guides are.
keywords: migration FAQ, migrate, eZ Publish history, 4.x, 5.x, eZ Platform, Ibexa, WordPress, Drupal, Joomla, static HTML, content mapping, redirects, public sector
type: help
---
# FAQ: migration from older systems

This page answers the migration questions public bodies ask when they bring an old website to Exponential. The answers
come from the migration chapters of the installation book (chapters 14 to 17) and the
[migration hub](../../../install/migration/README.md).

## Contents

1. [What is the history of the code base?](#what-is-the-history-of-the-code-base)
2. [We run an old 4.x or 5.x site. Can we move to Exponential?](#we-run-an-old-4x-or-5x-site-can-we-move-to-exponential)
3. [What stays the same, and what changes?](#what-stays-the-same-and-what-changes)
4. [We run eZ Platform or Ibexa. Can we move?](#we-run-ez-platform-or-ibexa-can-we-move)
5. [We run WordPress, Drupal or another system. Can we move?](#we-run-wordpress-drupal-or-another-system-can-we-move)
6. [How do we keep old addresses working?](#how-do-we-keep-old-addresses-working)
7. [Is there training for a migration team?](#is-there-training-for-a-migration-team)

## What is the history of the code base?

Exponential continues the code base of eZ Publish, which its creator discontinued after almost eighteen years of
service ([Who is 7x?](../../who-is-7x.md)). 7x carried that code forward to PHP 8 as Exponential 6
([Who is the Exponential Foundation?](../../who-is-the-exponential-foundation.md)). Sites built in that era can
therefore move to Exponential without changing systems.

## We run an old 4.x or 5.x site. Can we move to Exponential?

Yes, and those lines are no longer supported, so you should (`SECURITY.md`). Chapters
[14. Migrating from the 4.x line](../../../install/14-migrating-from-4x.md) and
[15. Migrating from the 5.x legacy stack](../../../install/15-migrating-from-5x-legacy.md) take such sites to 6.0; the
database is brought up with the update files rather than exported and re-imported
([11. Upgrading](../../../install/11-upgrading.md)).

## What stays the same, and what changes?

The database schema family, the content datatypes, rich text (`ezxmltext`), the template language and the extension
API stay the same. What changes is mainly PHP 5 to PHP 8 (your own PHP code is the largest piece of work), components
installed by Composer, and removed old JavaScript libraries
([14.1 What changes, and what does not](../../../install/14-migrating-from-4x.md#141-what-changes-and-what-does-not)).

## We run eZ Platform or Ibexa. Can we move?

Yes; chapter [16. Migrating from eZ Platform and Ibexa](../../../install/16-migrating-from-ez-platform-and-ibexa.md)
covers it, and [17. Migration reference](../../../install/17-migration-reference.md) holds the mapping tables. The
Exponential Platform distributions are another route ([Exponential Platform](../../../install/migration/exponential-platform.md)).

## We run WordPress, Drupal or another system. Can we move?

Yes, with a content migration: guides exist for [WordPress](../../../install/migration/wordpress.md),
[Drupal](../../../install/migration/drupal.md), [Joomla](../../../install/migration/joomla.md),
[static HTML](../../../install/migration/static-html.md) and others in the [migration hub](../../../install/migration/README.md).
Map content types to content classes first ([Content model mapping](../../../install/migration/content-model-mapping.md)).

## How do we keep old addresses working?

With redirects: URL wildcards and custom URL aliases in Exponential, or rules on the web server
([Redirects and moved content](../../creators/website-creators/redirects-and-moved-content.md),
[URL wildcards](../../creators/website-creators/url-wildcards.md)). Build the list from the old site before switching.

## Is there training for a migration team?

Yes: [Migrating to Exponential](../../trainers/migration/README.md), 25 lessons, from planning to leaving, with a
capstone migration plan. Use the [migration checklist](../../../install/migration/checklist.md) for the project.

## Related pages

- [Government FAQ](README.md)
- [Migration hub](../../../install/migration/README.md)
- [Migrations for hosts](../../hosting/migrations/README.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
