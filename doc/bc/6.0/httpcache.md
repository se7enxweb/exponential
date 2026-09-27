# The role-aware HTTP cache

Whole rendered pages, kept per permission context and served before the
kernel starts — for signed-in visitors as well as anonymous ones. Off by
default: `settings/httpcache.ini`, `Enabled=disabled`.

---

## What it does

A content view (`content/view`, every URL alias) is stored after it is
rendered, keyed by scheme, host, siteaccess, path and the visitor's
**permission context**:

- **anonymous** — one context for everyone who is not signed in;
- **role context** — shared by every user with the same roles and the same role
  assignment limitations, so a thousand members share one copy of each page;
- **private context** — one user alone, when a `content/read` policy depends on
  who they are (`Owner`, `ParentOwner`, `Group`, `ParentGroup`, `User_Section`,
  `User_Subtree`).

The next request for the same page in the same context is answered from the
store:

- under Apache or php-fpm by `kernel/private/classes/httpcache/ezphttpcacheearlyexit.php`,
  included from `config.php`, before the autoloader, INI files or database;
- under Exponential Velocity by the server process itself (`Q.web.appCache`,
  written by `exp:velocity`), before a worker is woken.

Both use the same class, `ezpHttpCacheContract`, so they compute the same keys
and serve the same bytes. The visitor's form token is taken out of the stored
page and the visitor's own put back in on every hit.

## Measured on alpha (2026-09-26, /fitness, 8 concurrent)

| | Hit | Full render |
|---|---|---|
| Apache, anonymous | 508 req/s, p50 14 ms | 28 req/s, p50 220 ms |
| Apache, signed in | 484 req/s, p50 15 ms | 4.3 req/s, p50 1.86 s |
| Velocity, anonymous | 1392 req/s, p50 4 ms | — |
| Velocity, signed in | 1156 req/s, p50 5 ms | 4.3 req/s, p50 1.84 s |

## Switching it on

```ini
# settings/override/httpcache.ini.append.php
[HttpCacheSettings]
Enabled=enabled
```

Then:

1. **Include the early exit from `config.php`** (Apache, php-fpm, FrankenPHP):

   ```php
   if ( is_file( __DIR__ . '/kernel/private/classes/httpcache/ezphttpcacheearlyexit.php' ) )
       require __DIR__ . '/kernel/private/classes/httpcache/ezphttpcacheearlyexit.php';
   ```

2. **Keep sessions in files that every server can read.** The early exit finds
   the visitor from the session file named by the session cookie; it cannot
   ask Redis or a database. Every server that serves a cached siteaccess must
   use the same directory — Apache's pool and Velocity (`[PHPSettings]
   IniOptions[]=session.save_path=0;0660;<dir>` in `velocity.ini`). A server
   whose session storage differs from the one the cache recorded stores
   nothing for signed-in visitors, and says so in `X-Exp-Cache`.

3. **Restart Velocity** (`exp:velocity restart`) so it loads the kernel classes
   and asks the cache.

The first request on a cached siteaccess writes the configuration the early
exit reads (`var/<site>/cache/exphttpcache/contract.php`, with a generated key).

## Settings (`httpcache.ini [HttpCacheSettings]`)

| Setting | Default | |
|---|---|---|
| `Enabled` | `disabled` | Nothing is stored or served while disabled. Switching off takes effect on the next request that reaches the kernel. |
| `CachedSiteAccesses[]` | `site` | Host-matched siteaccesses only: the early exit knows the host, not the URI rules. |
| `MaxAge` | `3600` | Longest life of a page even if nothing purges it. A template's `cache_ttl` shortens it; `cache_ttl=0` keeps the page out, as it does the view cache. |
| `StaleWhileRevalidate` | `60` | An expired (not purged) page is served as `STALE` while one request renders it again. |
| `ContentChangePurges` | `all` | `all`: any content change purges every page. `tags`: only pages showing the changed objects, nodes, parents, and pages with query blocks. |
| `QueryStringParameters[]` | none | Pages requested with any other query parameter are not cached. |
| `ProxyHeaders` | `enabled` | `xkey` and `Surrogate-Key` headers for Varnish or Fastly. |
| `APCu` | `enabled` | Hot entries in APCu in front of the files. |
| `MaxBodySize` | 2 MB | Larger pages are not stored. |

## When pages are purged

- **An edit, publish, move, hide or delete** — the view cache clear the kernel
  already does fires `content/cache`; with `ContentChangePurges=all` every page
  goes, with `tags` the pages tagged with what changed. Scripts and cronjobs
  purge too (`eZScript` attaches the listeners).
- **Layout editor** — any successful write request to an `explayouts*` module
  purges every page. Layout blocks tag the page with every item they show and,
  for query blocks, `dq`, which every content change purges.
- **Roles, policies, assignments** — every context changes (a new generation).
- **A user's own data** — that user's context record is forgotten.
- **Setup > Caches, `bin/php/ezcache.php`** — "HTTP cache (role-aware pages)",
  `--clear-id=exphttpcache`, and with `--clear-tag=content` or `template`.

What else a page shows is tagged while it renders, pagelayout included:
`fetch( 'content', 'list' )` and `list_count` of a parent's children tag
`pl<parent>` (any change to one of them purges it), deeper `tree` fetches tag
`dq`, and `fetch( 'content', 'node' )` tags `l<node>`. Listings inside a content
view that comes from the view cache are not fetched again, and follow the view
cache: the page is purged when that view cache is. Operators that read content
some other way are not tagged, which is why `ContentChangePurges=all` stays the
default; `tags` is for installations that know their templates.

## Seeing what it does

- Every response says `X-Exp-Cache: HIT`, `STALE`, `MISS (reason)` or
  `BYPASS (reason)`.
- **Setup > System information** has an "HTTP cache (role-aware)" box: hit rate
  across all servers, why requests missed, entries, disk use, user contexts,
  the last purge, the settings, and buttons to purge everything, remove dead
  entries and reset the counters.
- The `cache_cleanup` cronjob part removes expired, purged and orphaned files
  (`cronjobs/httpcache_cleanup.php`).

## What is never cached

POST and other writes; responses other than 200; responses that set a cookie
of their own; pages with a disallowed query string; pages whose template sets
`cache_ttl=0`; siteaccesses not host-matched; anything not a content view.

## Security

- Keys and contexts are HMACs with a generated key (`secret`, 0600), never sent.
- A visitor with a session cookie the cache cannot resolve is a miss, never
  the anonymous page; an unknown session cookie name is a miss.
- A signed-in visitor gets pages of their own permission context only; a role
  change moves every user to a new context at once.
- Form tokens are per visitor and put back on every hit; no other personal
  data may be in a cached page — pages that show the visitor's name belong in a
  private context or out of the cache (`cache_ttl=0`).
- Files written by root (a CLI clear) are handed to the owner of the cache
  directory, so the web server can still update them.

## Files

| | |
|---|---|
| `kernel/private/classes/httpcache/ezphttpcachecontract.php` | Keys, contexts, entries, state, sessions, placeholders, serving; pure PHP |
| `kernel/private/classes/httpcache/ezphttpcacheearlyexit.php` | The early exit included from `config.php` |
| `kernel/private/classes/httpcache/ezphttpcachelistener.php` | Store after render, purges, sign-in record |
| `settings/httpcache.ini` | Settings |
| `cronjobs/httpcache_cleanup.php` | Removes dead entries |
| `tests/tests/kernel/classes/httpcache/ezpHttpCacheContractTest.php` | Unit tests (HC-01 … HC-10) |
