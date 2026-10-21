---
title: Caching after imports
audience: integrator, developer, admin
category: integration
order: 360
summary: Which caches an import or a sync touches and which it does not - what the publish operation clears on its own, when to clear more, the cache ids of ezcache.php, the HTTP cache and Velocity's response cache, caches after settings and code changes, and CDNs in front of the site.
keywords: cache, caching, ezcache.php, clear-id, clear-tag, content cache, view cache, template-block, exphttpcache, querycache, rss_cache, urlalias, Velocity response cache, exp:velocity cache clear, eZContentCacheManager, clearContentCacheIfNeeded, CDN purge, import
type: help
---
# Caching after imports

This page is for integrators and operators who see old content after an import, or who want to clear exactly as much
as needed and no more. Exponential caches at several layers. Content changed through the supported functions clears
its own caches; content changed any other way, settings, code and outer caches need help. The overview of every
cache is [Caches](../../guides/caches.md).

## Contents

1. [What publishing clears on its own](#what-publishing-clears-on-its-own)
2. [When to clear more](#when-to-clear-more)
3. [The cache ids](#the-cache-ids)
4. [Outer caches](#outer-caches)
5. [After settings and code changes](#after-settings-and-code-changes)
6. [Recipes](#recipes)

## What publishing clears on its own

`eZContentFunctions::createAndPublishObject()`, `updateAndPublishObject()`, the REST create route and the
`expservices` writes all run the publish operation, which clears the view caches of the object's nodes and of the
nodes that depend on it (parents, related objects, per `viewcache.ini`) through `eZContentCacheManager`, and announces
the event `content/cache` for listeners ([Event listeners](event-listeners-for-integrations.md)).

> **Note**
> When one change would clear more nodes than the cleanup threshold allows, the kernel clears **every** view cache
> instead, writes a warning to the log, and announces `content/cache/all`. A large import can therefore make the next
> visits slower everywhere; run big imports at quiet hours.

## When to clear more

| Situation | What to clear |
|---|---|
| Content was changed outside the publish operation (direct SQL, a repair) | The objects' view caches: `eZContentCacheManager::clearContentCacheIfNeeded( $objectID )` in PHP, or `expobject::expireCache` over `expservices`; better, do not change content that way |
| Template blocks (`cache-block`) show lists of imported content | `template-block`, or rely on their expiry |
| Menus show imported folders | `content_tree_menu` (admin tree), `template-block` |
| RSS exports must show imported items at once | `rss_cache` |
| Imported objects got new URL aliases | Usually nothing; `urlalias` only after a repair |
| Classes or class attributes changed (a new class package) | `classid`, `sortkey`, and the content view caches |
| Search results are old | Not a cache: rebuild the index ([Search engine integration](search-engine-integration.md#indexing-after-imports)) |

## The cache ids

`php bin/php/ezcache.php --list-ids --allow-root-user` lists them. Those an integration meets most:

| Id | Holds |
|---|---|
| `content` | Content view caches |
| `template-block` | Template `cache-block` output |
| `exphttpcache` | The HTTP cache of whole pages |
| `querycache` | The SQL query cache |
| `rss_cache` | Written RSS exports |
| `urlalias` | URL alias lookups |
| `classid`, `sortkey` | Class lookups |
| `content_tree_menu` | The administration's tree menu |
| `ini`, `global_ini` | Settings (clear with `--clear-tag=ini`) |
| `template`, `template-override` | Compiled templates and the override index |

```bash
php bin/php/ezcache.php --clear-id=content,template-block --allow-root-user
php bin/php/ezcache.php --clear-tag=content --allow-root-user
php bin/php/console exp:cache clear --id=content --dry-run --allow-root-user   # what would be cleared, with sizes
```

Avoid `--clear-all` on a busy production site after an import: it empties every cache at once, and every visitor pays
for the rebuild.

## Outer caches

| Layer | Clear |
|---|---|
| Velocity's response cache (when Velocity serves the site) | `php bin/php/console exp:velocity cache clear` |
| The HTTP cache | cache id `exphttpcache` ([HTTP cache](../../bc/6.0/httpcache.md)) |
| A CDN or reverse proxy you run | Its own purge API, driven by `content/cache` events ([example](event-listeners-for-integrations.md#example-purging-a-cdn-when-caches-are-cleared)) |
| Browsers | Cache headers of your pages; nothing to clear |

Static cache is off in the shipped settings; if a site switched it on, its files must be regenerated too
([Static cache generator](../../features/6.0/static-cache-generator.md)).

## After settings and code changes

Deploying a new version of your integration extension is not an import, and needs more:

1. `php bin/php/ezpgenerateautoloads.php -e` when classes were added or renamed.
2. `php bin/php/ezcache.php --clear-tag=ini --allow-root-user` after settings changes (not `--clear-id=ini`, which
   leaves the global INI cache).
3. Reload PHP-FPM, and restart Velocity: Velocity's workers keep the classes they loaded at start.
4. Then clear the content view cache and Velocity's response cache, **after** the reload, so no page rendered by the
   old code is cached again.

`php bin/php/console exp:velocity deploy` does these steps in order ([Deploying](../../guides/deploying.md#7-ship-a-php-change-with-one-command)).

## Recipes

```bash
# after a nightly product import that publishes through eZContentFunctions: usually nothing; lists in cache-blocks:
php bin/php/ezcache.php --clear-id=template-block --allow-root-user

# after installing a package with classes and content (indexing was delayed)
php bin/php/updatesearchindex.php --allow-root-user
php bin/php/ezcache.php --clear-id=content,template-block,classid,sortkey --allow-root-user
php bin/php/console exp:velocity cache clear

# after a repair that changed content tables directly
php bin/php/ezcache.php --clear-id=content,template-block,urlalias --allow-root-user
```

## Related pages

- [Caches](../../guides/caches.md)
- [Cache tags and clearing](../extension-development/part-8-reference/cache-tags-and-clearing.md)
- [Caching in applications](../creators/app-creators/caching-in-applications.md)
- [Velocity and performance for integrations](velocity-and-performance-for-integrations.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
