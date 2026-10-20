---
title: Caching in applications
audience: app-creator, developer
category: app-creation
order: 610
summary: Cache an application's work correctly - which kernel caches exist, cache blocks for template output, caching computed data in PHP with eZClusterFileHandler and expiry, clearing on change, caches under Velocity's persistent workers, and a cache of your own in the cache list.
keywords: caching, cache-block, view cache, eZClusterFileHandler, expiry, cache clear, ezcache, cache ids, tags, persistent workers, static variables, app creator
type: help
---
# Caching in applications

This page is for application creators who cache computed data and rendered output. Caching makes pages fast; a wrong
key or a missing clear shows one user another user's data or keeps stale data forever.

## Contents

1. [The kernel's caches](#the-kernels-caches)
2. [Template output: cache blocks](#template-output-cache-blocks)
3. [Computed data in PHP](#computed-data-in-php)
4. [Clearing](#clearing)
5. [Under persistent workers](#under-persistent-workers)

## The kernel's caches

| Cache | Clears on | Your concern |
|---|---|---|
| Content view cache | publish of the node and related content | views of content are cached per user's roles; your view module output is not |
| Template block cache | publish (by default), expiry | keys and expiry of your blocks |
| Compiled templates, overrides, INI | template and setting changes | clear after deploys |
| Velocity response cache | its own rules and explicit clears | anonymous pages only |

The full list with ids and tags: [10.6 Caches](../../../install/10-after-installing.md#106-caches).

## Template output: cache blocks

```text
{cache-block keys=array( 'expdemo-stats', $current_user.contentobject_id ) expiry=300}
  {def $stats = fetch( 'expdemo', 'stats', hash() )} ... {undef $stats}
{/cache-block}
```

Data from your own tables does not trigger the publish clear: choose an `expiry` that matches how stale it may be, or
add `ignore_content_expiry` and clear the `template-block` cache yourself when your data changes
([Template functions, includes and cache blocks](template-functions-and-includes.md#cache-blocks)).

## Computed data in PHP

Store expensive results through the kernel's cluster file handler, so they work on one server and on a cluster:

```php
$file = eZClusterFileHandler::instance( eZSys::cacheDirectory() . '/expdemo/stats.cache' );
$stats = $file->processCache(
    function ( $path, $mtime ) {                                          // read a valid cache file
        return json_decode( (string)file_get_contents( $path ), true );
    },
    function ( $path ) {                                                  // generate when expired or missing
        $stats = expDemoStats::compute();
        return array( 'content' => $stats, 'binarydata' => json_encode( $stats ), 'scope' => 'expdemo-stats' );
    },
    300 );                                                                // $ttl: seconds from storing
```

`processCache( $retrieveCallback, $generateCallback = null, $ttl = null, $expiry = null, $extraData = null )` is
documented in `kernel/classes/clusterfilehandlers/ezfsfilehandler.php`: the retrieve callback gets the path and the
mtime, the generate callback the path and returns `content` (returned to you) and `binarydata` (written to the file);
while one process generates, others wait instead of generating the same data.

## Clearing

- Clear what you cached when its source changes: in the code that changes it, or in a workflow event or listener.
- Give your cache a directory of its own so it can be removed alone.
- An extension can add its cache to the cache list (`site.ini [Cache] CacheItems[]` and a `[Cache_<id>]` block, as the shipped `[Cache_rest]` does), so `ezcache.php
  --clear-id=<id>` and **Setup > Caches** can clear it, as the kernel's `[Cache_*]` blocks do.

## Under persistent workers

Velocity keeps PHP processes between requests: a static variable or a singleton survives into the next request. Never
keep user-specific data in static variables; reset per-request state, or key it by request. Process memory is not
shared between workers, so it is not a shared cache either
([Velocity persistent worker server](../../../features/6.0/velocity-persistent-worker-server.md)).

## Related pages

- [View cache and cache blocks](../website-creators/view-cache-and-cache-blocks.md)
- [Caches](../../../guides/caches.md)
- Caching cookbook (`doc/eng-us/examples/cookbook/caching.md`)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
