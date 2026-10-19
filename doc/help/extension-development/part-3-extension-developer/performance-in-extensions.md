---
title: Performance in extensions
audience: developer, app-creator
category: extension-development
order: 360
summary: Keep an extension fast and safe under persistent workers: count queries, fetch with limits, cache, keep listeners cheap, measure, and never keep request state in statics.
keywords: performance, speed, SQL, queries, fetch, limit, view cache, cache-block, cache, statics, Velocity, persistent worker, requestKey, benchmark, exp:benchmark, debug bar, timing
type: help
---
# Performance in extensions

Part 3, chapter 36. An extension can make every page of a site slower: a listener on `response/output`, an operator
in the pagelayout, a fetch in a loop. This chapter lists the usual causes and the tools that find them.

## Measure first

| Tool | Shows |
|---|---|
| The debug bar's timing table | how long each part of a request took |
| `[DatabaseSettings] SQLOutput=enabled` | every query of a page; count them |
| `{debug-timing-point id='...'}...{/debug-timing-point}` | the time of one part of a template |
| `./console exp:benchmark` | page timings over HTTP, A/B between two servers, kernel probes, a regression check against a baseline ([Benchmarking](../../../guides/benchmarking.md)) |

Measure the page before and after you switch your extension on; the difference is what it costs.

## Database

- **Count queries.** A page that runs the same query once per list item has a loop that should fetch once.
- **Fetch with limits** and filters; never load a whole subtree to count it (`subTreeCountByNodeID()` and the `_count`
  fetch functions count in the database).
- **Do not load attributes you do not use**: `load_data_map` `false()` for lists of names.
- **Index your own tables** on the columns you query by.

## Caching

| Level | Use |
|---|---|
| The content view cache | content views are cached already; do not defeat it by printing per-user data inside a full view template |
| `cache-block` | expensive parts of the pagelayout: menus, footers ([Caching and templates](../part-2-builder/caching-and-templates.md)) |
| Your own cache | results of slow work (an external API): store them in a cache file under the cache directory, or use the cluster file handler, with an expiry; clear them when your data changes |
| The view cache keys | when a view's output depends on something new, add it with the `content/view/cachekeys` filter rather than switching caching off |

## Listeners and operators on every page

`response/output`, `request/input` and operators in the pagelayout run on every request. Keep them to a few
microseconds: no database, no file reads, no network. Do the work elsewhere and store the result.

## Persistent workers (Velocity)

Velocity, the recommended application server, keeps PHP processes alive between requests. That makes the kernel fast,
and it changes the rules for your code:

- **No request state in statics.** A worker puts every class's statics back between requests, but code that relies on
  a static being empty at the start of a request should not exist: compute per request, or key the value by request.
- **Register once per request with a request key**, not with a `true` flag, as the Velocity documentation shows:

  ```php
  if ( self::$registered !== eZExecution::requestKey() )
  {
      self::$registered = eZExecution::requestKey();
      register_shutdown_function( array( __CLASS__, 'flush' ) );
  }
  ```

- **New classes need a restart of the workers.** After deploying new code, `./console exp:velocity deploy` regenerates
  autoloads, clears caches and restarts the workers.
- **Never wrap a download plus `eZExecution::cleanExit()` in `catch ( Exception )`**: under Velocity the clean exit
  throws to end the request.

See [Velocity: running Exponential in a persistent-worker web server](../../../features/6.0/velocity-persistent-worker-server.md).

## Front end

- Load scripts and styles through the packer (one bundle, cached, with the time stamp in the name).
- Require page-specific scripts with `ezscript_require` on that page only.
- Images through image aliases, not full originals.

## Related pages

- Previous: [Debugging extensions](debugging-extensions.md)
- Next: [Documenting your extension, including help pages](documenting-your-extension.md)
- [Benchmarking](../../../guides/benchmarking.md), [Caches](../../../guides/caches.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
