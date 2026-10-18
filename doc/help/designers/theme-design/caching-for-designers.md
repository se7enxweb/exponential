---
title: Caching for designers
audience: designer
category: theme-design
order: 640
summary: The caches a designer's templates live in (view cache, cache-blocks, compiled templates, packed assets, Velocity's response cache), what each keeps, what clears it, and the template rules that keep cached pages correct for every visitor.
keywords: caching, view cache, cache-block, ViewCaching, CachedViewModes, cache_ttl, ViewCacheTweaks, template cache, cache keys, user specific content, csp nonce cache, stale pages
type: help
---
# Caching for designers

This page is for designers who want fast pages that are still correct for every visitor. Exponential caches at several
levels; most of them work without the designer doing anything, but templates can break them, or be broken by them.

## Contents

1. [The layers](#the-layers)
2. [The view cache](#the-view-cache)
3. [Cache-blocks](#cache-blocks)
4. [Rules for templates](#rules-for-templates)
5. [Clearing](#clearing)

## The layers

| Layer | Holds | Cleared |
|---|---|---|
| View cache | the HTML of a node's full view (`$module_result.content`) | on publish, for the affected nodes |
| Cache-blocks | parts of templates you wrap in `{cache-block}` | when keys change, on expiry, on content publish (unless told otherwise) |
| Compiled templates | templates turned into PHP | when the template file changes (with development checks) or on clear |
| Packed assets | merged CSS and JS | a new file per source change |
| Velocity response cache | whole responses, on installations with the Velocity server | `./console exp:velocity cache clear` |

## The view cache

`site.ini [ContentSettings]`:

```ini
ViewCaching=enabled
CachedViewModes=full;sitemap;pdf
```

The full view of a node is cached per node, view mode, language, the user's roles and limitations, view parameters and
more (the hash is built in `kernel/classes/eznodeviewfunctions.php`). The pagelayout is **not** cached; it runs on every
request around the cached content.

Consequences for designers:

- A full view template must not print anything specific to one visitor (their name, their basket) unless the cache
  varies by it. Put such things in the pagelayout, or in a part that is not cached.
- A full view can set its own lifetime with the template variable `cache_ttl`, which the kernel reads after rendering
  (`eznodeviewfunctions.php`). The ezwebin event calendar keeps its page for 400 seconds:
  `{set-block scope=root variable=cache_ttl}400{/set-block}`. A value of `0` stores nothing; use that rarely, since a
  page that is never cached is rendered on every request.
- Per node, `ViewCacheTweaks[<node_id>]` has options such as `pr_user` (cache per user) and `disabled` (comments in
  `settings/site.ini`).

## Cache-blocks

Wrap expensive parts of the pagelayout (menus, footers) in a cache-block with the keys that change their output:

```tpl
{cache-block keys=array( $module_result.uri, $current_user.role_id_list|implode( ',' ) )}
    {include uri='design:pagelayout/menu.tpl'}
{/cache-block}
```

Parameters (`lib/eztemplate/classes/eztemplatecachefunction.php`): `keys`, `expiry` (seconds; default 7200; `0` for
no time limit), `ignore_content_expiry` (keep it when content is published), `subtree_expiry` (expire when something
below a node is published). Details: [Cache-block](../templates/cache-block.md).

## Rules for templates

- **Everything that changes the output is a key.** The current page, the user's roles, the language, the basket state.
  A menu that marks the current page needs the URI or node id in its keys.
- **Never put user data in a block without the user in the keys** (or without the role list, if it varies only by role).
- **Never print `csp_nonce()` in a cache-block or a view-cached template**: the nonce changes on every request.
- **Keep keys small.** Every distinct key value writes a cache file. The admin pagelayout casts its section filter to a
  number for that reason ("every distinct raw value would write its own cache file").
- **Do not fetch inside a block what is needed outside it**: a cache hit skips the fetch.

## Clearing

| After | Clear |
|---|---|
| changing a view template | `--clear-id=content` (and `template` if compiled templates are not checked) |
| changing a part inside a cache-block | `--clear-id=template-block` |
| adding templates or overrides | `--clear-id=template-override` |

See [The packer cache and deploys](packer-cache-and-deploy.md) for the full table.

## Related pages

- [Cache-block](../templates/cache-block.md)
- Template compilation and cache
- [Theme performance](theme-performance.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
