---
title: Caching
audience: developer, admin
category: examples
order: 120
summary: Use the caches of Exponential in a design and an extension — the view cache and when to turn it off for one template, cache-block with keys, expiry and subtree_expiry, a cache of an extension's own in the cache list, and clearing in the right order under PHP-FPM and Velocity.
keywords: cache, caching, view cache, ViewCaching, CachedViewModes, cache_ttl, cache-block, keys, expiry, subtree_expiry, ignore_content_expiry, template cache, TemplateCache, ezcache.php, clear-tag, clear-id, CacheItems, Cache_, HTTP cache, httpcache.ini, Velocity response cache
type: example
---
# Caching

A page of Exponential can be answered from four layers, from the outside in:

| Layer | What it holds | Cleared |
|---|---|---|
| Velocity's response cache | whole answers, for a few seconds | `./console exp:velocity cache clear` |
| The HTTP cache (off by default) | whole pages per permission context | `--clear-id=exphttpcache`; expired on publish |
| The **view cache** | the rendered content of a node view, per user role and view parameters | expired for the node, its parents and relations on publish |
| **cache-block** | a piece of a template (a menu, a footer) | by its `expiry`, `subtree_expiry`, or the content expiry |

Below them, compiled templates, the INI cache, the class and translation caches make each request cheaper. You use
the view cache and `cache-block` in the templates of a design, give an extension a cache of its own in **Setup >
Caches**, and clear the right thing after a change.

Prerequisites: a design you can edit (here in the example extension, [A template override](template-override.md)).

## 1. The view cache

`site.ini [ContentSettings] ViewCaching=enabled` (the default) stores the output of `content/view` for the view modes
in `CachedViewModes` (`full;sitemap;pdf`). A view is cached per node, view mode, language, the user's roles and
limitations, and the view parameters; publishing an object expires the views of its nodes, their parents and the
related objects `viewcache.ini [ViewCacheSettings]` names.

A template whose output must not be cached (it shows the visitor's basket or the time) turns it off for itself:

```tpl
{* Not cached: shows what is in the visitor's basket *}
{set-block scope=root variable=cache_ttl}0{/set-block}
```

`cache_ttl` set to a number of seconds caches the view for at most that long. For one node, without touching
templates: `site.ini [ContentSettings] ViewCacheTweaks[<node id>]=disabled` (or `pr_user`, one cache per user).

## 2. cache-block

`cache-block` caches what is between its tags. In a pagelayout or an included template:

```tpl
{* The top menu: the same for everybody on this siteaccess, rebuilt when anything below node 2 is published *}
{cache-block keys=array( $access_type.name ) subtree_expiry=2}
    {def $menu = fetch( 'content', 'list', hash( 'parent_node_id', 2,
                                                  'class_filter_type', 'include',
                                                  'class_filter_array', array( 'folder' ),
                                                  'sort_by', array( 'priority', true() ) ) )}
    <nav class="top-menu">
    {foreach $menu as $item}
        <a href={$item.url_alias|ezurl}>{$item.name|wash}</a>
    {/foreach}
    </nav>
    {undef $menu}
{/cache-block}

{* The footer: rebuilt every hour, not on publishing *}
{cache-block expiry=3600 ignore_content_expiry}
    {include uri='design:parts/footer.tpl'}
{/cache-block}
```

| Parameter | Meaning |
|---|---|
| `keys` | what the cached copy depends on; one copy per distinct value (a siteaccess, a node id, the user's language). Without `keys` the template file and position are the key. |
| `expiry` | seconds a copy lives (default 7200); `0` for no time limit |
| `subtree_expiry` | a node id or a URL alias: the copy is expired when anything below that node is published, instead of on every publish |
| `ignore_content_expiry` | the copy is not expired by publishing at all, only by `expiry` |

Rules that keep a cache-block correct:

- **Everything that changes the output must be in `keys`**: the node of the page (`$node.node_id`), the user's
  language, the view parameters (`$view_parameters.offset`). A block whose output depends on the user (a greeting, the
  basket) does not belong in a cache-block at all.
- Variables defined inside a block are not set when the copy is served: define what the rest of the template needs
  outside it.
- `site.ini [TemplateSettings] TemplateCache=enabled` (the default) switches cache-block on; with it disabled the blocks
  are rendered every time.

## 3. A cache of an extension's own

An extension that keeps files under the cache directory registers them, so that **Setup > Caches** lists the cache
and `ezcache.php` clears it. In `extension/expexample/settings/site.ini.append.php`:

```ini
[Cache]
CacheItems[]=expexample

[Cache_expexample]
name=Example extension cache
id=expexample
tags[]=content
path=expexample
```

`path` is relative to the cache directory of the site (`var/<site>/cache/expexample`); the tag decides which
group clears it too (`--clear-tag=content` now clears it). A cache with its own way of clearing names a `class`
with a static `clearCache()` method instead (ezjscore's `[Cache_ezjscore]` with `class=ezjscCacheManager` is an
example in this release).

## 4. Clearing, and in what order

```bash
php bin/php/ezcache.php --clear-id=template-block,content --allow-root-user   # one cache or several, by id
php bin/php/ezcache.php --clear-tag=template --allow-root-user                 # every cache with a tag
php bin/php/ezcache.php --clear-all --allow-root-user                          # everything
./console exp:cache clear --id=content --dry-run --allow-root-user             # what would go, with sizes
./console exp:velocity cache clear                                            # Velocity's response cache
```

After a **template** change: nothing while the template cache is off; with it on, `--clear-tag=template`; a **new**
template file or override rule also needs `--clear-id=template-override`. After an **INI** change: `--clear-tag=ini`,
and a Velocity restart. After a **PHP** change: the order matters, because a page rendered by the old code between a
clear and the reload is cached again: reload PHP-FPM and restart Velocity first, then clear content, template blocks
and the response cache. `exp:velocity deploy` does exactly that ([Deploying with Velocity](velocity-deploy.md)).

## 5. Check it

- A cache-block works when the second request of a page is faster, and the block's files appear below
  `var/<site>/cache/template-block/` after the first one.
- `curl -sI https://example.com/ | grep -i x-exp-cache` shows the HTTP cache's answer once it is on (`HIT`, `MISS`,
  `BYPASS` with the reason).
- **Setup > Caches** lists *Example extension cache* in its group, with its size.

Checked for this page (2026-10-18): the cache-block parameters are those `lib/eztemplate/classes/eztemplatecachefunction.php`
reads; `cache_ttl` is read by `kernel/classes/eznodeviewfunctions.php`; the settings named are in the shipped
`settings/site.ini` and `settings/viewcache.ini`; the cache registration follows `extension/ezjscore/settings/site.ini.append.php`.
`bin/php/eztemplatecheck.php` reports the template of section 2 valid (syntax).

## Common mistakes

| Symptom | Cause |
|---|---|
| One visitor sees another's name or basket | personal output inside a cache-block, or in a view without `cache_ttl` 0 |
| A menu is stale after publishing | `ignore_content_expiry`, or `subtree_expiry` names a node above which nothing was published |
| Every page shows the same content in a block | the node of the page is missing from `keys` |
| A change shows up only after a few seconds | Velocity's response cache; clear it or wait |
| The log says *Expiring all view cache* | a publish touched more than `[ContentSettings] CacheThreshold` nodes; see [Caches](../../guides/caches.md) |

## Derived from

- `lib/eztemplate/classes/eztemplatecachefunction.php` (`keys`, `expiry`, `subtree_expiry`, `ignore_content_expiry`)
- `kernel/classes/eznodeviewfunctions.php` (`cache_ttl`), `settings/site.ini` (`[ContentSettings]`, `[TemplateSettings]`),
  `settings/viewcache.ini`
- `extension/ezjscore/settings/site.ini.append.php` (`[Cache]`, `[Cache_ezjscore]`)
- `doc/guides/caches.md`, `doc/bc/6.0/httpcache.md`, `doc/install/08-serving-the-site.md` (8.3.14)

## Related pages

- [Cookbook](README.md)
- [Caches: what each one holds and how to clear it](../../guides/caches.md), [Cache console](../../bc/6.0/cache-console.md)
- [The role-aware HTTP cache](../../bc/6.0/httpcache.md), [Velocity response cache](../../features/6.0/velocity-response-cache.md),
  [Static cache generator](../../features/6.0/static-cache-generator.md)
- [INI settings](../ini-settings/README.md) for every cache setting
- The coding rules and the pattern catalogue: [the standards](../../standards/README.md)
- [Free documentation](../../help/free-documentation.md): this page is free to copy, translate and improve.
