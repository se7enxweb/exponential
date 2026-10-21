---
title: Cache invalidation and clearing
audience: software-engineer, developer, admin
category: software-engineering
order: 310
summary: How Exponential invalidates its caches on its own (eZContentCacheManager on publish, the content/cache event, expiry timestamps, query cache table tracking) and how to clear them by hand by id or tag, which change needs which clear, the order relative to reloading code, and the rules for code that writes cache files.
keywords: cache invalidation, cache clear, ezcache.php, exp:cache, clear-tag, clear-id, eZContentCacheManager, eZCache, expiry, content/cache event, template-block, ini, Velocity cache clear, software engineer
type: help
---
# Cache invalidation and clearing

This page is for engineers who change code, settings or data and need the right caches cleared, no more and no less,
and for those who write code that stores something in a cache. The administration page is described in
[Caches](../../guides/caches.md); the cache tags and ids for extension authors are in
[Cache tags and clearing](../extension-development/part-8-reference/cache-tags-and-clearing.md).

## Contents

1. [What invalidates itself](#what-invalidates-itself)
2. [Clearing by hand](#clearing-by-hand)
3. [Which change needs which clear](#which-change-needs-which-clear)
4. [The order matters](#the-order-matters)
5. [Rules for code that caches](#rules-for-code-that-caches)

## What invalidates itself

| Mechanism | What it does |
|---|---|
| `eZContentCacheManager` (`kernel/classes/ezcontentcachemanager.php`) | on publish, clears the content view caches of the affected nodes and emits the `content/cache` event |
| `eZExpiryHandler` (`expiry.php` in the cache directory) | keeps expiry timestamps; a cache older than its timestamp is regenerated |
| Role-aware HTTP cache | purges stored views on content change (`ContentChangePurges=all` by default) |
| SQL query cache | a write to a table makes every cached result that read it stale |
| `sevenx_valkey_cache` | reverse node and subtree indexes purge exactly the entries a publish affects |
| Template blocks | expire after their configured time unless their keys change first |

## Clearing by hand

```bash
php bin/php/ezcache.php --list-ids --allow-root-user        # every cache id
php bin/php/ezcache.php --list-tags --allow-root-user       # every tag
php bin/php/ezcache.php --clear-tag=ini --allow-root-user   # the INI and global INI caches
php bin/php/ezcache.php --clear-id=template-block --allow-root-user
php bin/php/ezcache.php --clear-all --allow-root-user       # everything: expensive on a busy site
./console exp:velocity cache clear                         # Velocity's response cache
```

The cache ids in `eZCache` include `content`, `template`, `template-block`, `template-override`, `ini`, `global_ini`,
`translation`, `design_base`, `active_extensions`, `classid`, `urlalias`, `imagealias`, `user_info_cache`,
`exphttpcache` and `querycache`; the tags are `content`, `template`, `ini`, `i18n`, `image` and `user`. Every clear is
recorded in the audit trail as `system.cache.clear`, with who did it.

## Which change needs which clear

| You changed | Clear |
|---|---|
| An existing `.tpl` file | `template` when the rendered result looks stale, and the content view cache of the pages that use it |
| A new template file or a new override rule | `template-override` (otherwise the new file is not found) |
| An INI file | `--clear-tag=ini`; under Velocity also restart it |
| A PHP class | regenerate the class maps, reload PHP-FPM, restart Velocity ([Deploying a change](deploying-a-change.md)) |
| A stylesheet or script of a design | the packer cache (`ezjscore-packer`) and `template-block` |
| A translation | `translation` |
| Content, through the kernel | nothing: the kernel invalidates |
| The database, from outside (restore, SQL client) | `querycache`, `content`, and the HTTP caches |

## The order matters

Load the new code first, clear the page caches last. A page rendered by the old code between a clear and the reload of
PHP-FPM or Velocity is cached again and served as if the change had not worked. `exp:velocity deploy` runs the steps
in this order ([Compatibility, releases and deployment](../../standards/architecture/compatibility-releases-deployment.md#shipping-a-code-change)).

## Rules for code that caches

- Cache files **must** be written through `eZClusterFileHandler::instance()`, not with direct file calls, so clustered
  installations work.
- Register a cache of your own with an id and a tag so that `ezcache.php`, `exp:cache` and Setup > Caches can clear it
  ([Caching in applications](../creators/app-creators/caching-in-applications.md)).
- Key a cache on everything that changes its content: the user's access context, the siteaccess, the language.
- A content-dependent cache listens to the `content/cache` event or keys on content modification, so a publish reaches
  it.

## Related pages

- [Caching layers for engineers](caching-layers-for-engineers.md)
- [Cache clearing and warming, for hosts](../hosting/performance/cache-clearing-and-warming.md)
- [Cache console](../../bc/6.0/cache-console.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
