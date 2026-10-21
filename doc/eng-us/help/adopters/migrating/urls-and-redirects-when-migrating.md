---
title: URLs and redirects when migrating
audience: adopter, developer, editor, project-manager
category: adoption
order: 45
summary: How adopters keep links and search rankings when moving a site to Exponential - URL aliases, mapping old addresses to new ones, redirects, languages, checking 404s after launch, and the documentation for each.
keywords: URLs, redirects, URL aliases, SEO, 404, old addresses, mapping, languages, migration, search rankings
type: help
---
# URLs and redirects when migrating

Old addresses live in bookmarks, other sites and search engines. A migration that breaks them loses visitors.

## Contents

1. [How Exponential builds addresses](#how-exponential-builds-addresses)
2. [Mapping old to new](#mapping-old-to-new)
3. [After launch](#after-launch)

## How Exponential builds addresses

Exponential gives content readable addresses (URL aliases) from its place in the content tree and its name, per
language. Moved or renamed content keeps working through its aliases. The owner's view:
[URLs, redirects and moved pages](../../customers/day-to-day/urls-redirects-and-moved-pages.md). Rebuilding the aliases
is one of the measured steps of a migration ([Switching to Exponential](../../businesses/choosing/switching-to-exponential.md#what-drives-the-effort)).

## Mapping old to new

1. Export the list of old addresses, with traffic if you have it.
2. For each, decide: same address, a new address, or retired.
3. Create redirects for every changed or retired address that has traffic or links.
4. Test the list on staging before launch.

Redirect rules can also live in the web server; the hosting pages cover each server
([Web servers and engines](../../ecosystem/php-stack/web-servers-and-engines.md)).

## After launch

- Watch the "not found" errors daily for the first weeks ([Logs for owners](../../customers/healthy/logs-for-owners.md)).
- Add redirects for addresses that still get traffic.
- Check search and sitemaps ([SEO checks](../../customers/day-to-day/seo-checks.md)).

## Related pages

- [Migrating](README.md)
- [Content migration checklist](content-migration-checklist.md)
- [Safe redirects](../../../features/6.0/safe-redirects.md)
- [SEO monthly checklist](../../customers/day-to-day/seo-monthly-checklist.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
