---
title: Caching and templates
audience: developer, designer, app-creator
category: extension-development
order: 160
summary: The caches a template meets: compiled templates, the view cache, cache-block, override and design caches, HTTP and Velocity caches; what clears each automatically and how to clear it by hand.
keywords: cache, view cache, ViewCaching, cache-block, keys, expiry, subtree_expiry, ignore_content_expiry, viewcache.ini, SmartCacheClear, DependentClassIdentifier, ezcache, clear-tag, clear-id
type: help
---
# Caching and templates

Part 2, chapter 16. A page built from templates and fetches is expensive; Exponential caches it at several levels.
Caches make the site fast and make a builder's changes "not show up". This chapter explains each cache a template
touches and how it is cleared. The overview of every cache is [Caches](../../../guides/caches.md).

## The caches of a page

| Cache | Holds | Cleared automatically when | Clear by hand |
|---|---|---|---|
| Compiled templates | each `.tpl` as PHP | the template file changes (`TemplateCompile=enabled` compares times) | `--clear-tag=template` |
| Template override cache | the override rules, compiled | never for new files | `--clear-id=template-override` |
| Design base cache | the design chain | never | `--clear-id=design_base` |
| Content view cache | the full view of a node per user group, language and view parameters | the node (and related ones) is published | `--clear-tag=content` |
| Template block cache | the output of `{cache-block}` | content is published (unless `ignore_content_expiry`), or its expiry passes | `--clear-id=template-block` |
| INI cache | the merged settings | `exp:ini` and the settings page clear it | `--clear-tag=ini` |
| HTTP and Velocity response caches | whole responses | publishing, deploys | `--clear-id=exphttpcache`, `./console exp:velocity cache clear` |

All `--clear-...` options belong to `php bin/php/ezcache.php`; `--list-tags` and `--list-ids` print what exists.
`./console exp:cache` is the console form with more targets (static, OPcache, APCu).

## The content view cache

With `site.ini [ContentSettings] ViewCaching=enabled` (the default), the full view of a node is stored after the first
request and served from the cache until the node is published again. Which view modes are cached is
`CachedViewModes=full;sitemap;pdf`.

When an article is published, its own cache and the caches of related pages are cleared. Which "related" pages, is set
in `viewcache.ini`:

```ini
[ViewCacheSettings]
SmartCacheClear=enabled

[article]
DependentClassIdentifier[]
DependentClassIdentifier[]=frontpage
ClearCacheMethod[]
ClearCacheMethod[]=object
ClearCacheMethod[]=parent
ClearCacheMethod[]=relating
```

With `SmartCacheClear` on, publishing an `article` also clears the caches of `frontpage` objects above it, and of its
own object, parent and relating objects. The possible methods, from the comments of `settings/viewcache.ini`, are
`object`, `parent`, `relating`, `keyword`, `siblings`, `children`, `all` and `none`.

## cache-block

Parts of the pagelayout (menus, footers) are drawn on every page and are not in the view cache. Keep them in a
`cache-block`:

```text
{cache-block keys=array( $current_node_id ) expiry=3600}
    {def $menu = fetch( 'content', 'list', hash( 'parent_node_id', 2, 'sort_by', array( 'priority', true() ) ) )}
    <nav>
    {foreach $menu as $item}
        <a href={$item.url_alias|ezurl}>{$item.name|wash}</a>
    {/foreach}
    </nav>
{/cache-block}
```

| Parameter | Meaning |
|---|---|
| `keys` | values that make a separate cached copy (the current node, the user's language). Without keys there is one copy per template position. |
| `expiry` | seconds until the copy expires |
| `subtree_expiry` | a node or URL whose subtree's publishing clears the copy |
| `ignore_content_expiry` | do not clear the copy when content is published |

Things to know:

- Everything **inside** the block is cached, including the output of fetches, so a value that differs per user
  (their name, their basket) must not be inside, or must be in `keys`.
- Variables **defined inside** a block are not available after it on a cached request; define what you need after the
  block outside it.
- A stylesheet link that the packer writes with a time stamp in its name can sit in a cache-block of a pagelayout:
  after a stylesheet change, clear `template-block` too.

## When a change does not show

| You changed | Clear |
|---|---|
| A template file | nothing (if `TemplateCompile` checks times), else `--clear-tag=template` |
| Added a new template file | `--clear-id=template-override` |
| An override rule or a design setting | `--clear-tag=ini`, `--clear-id=template-override,design_base` |
| Content | nothing; publishing clears it |
| A template inside a cached full view | `--clear-tag=content` |
| A template inside a `cache-block` | `--clear-id=template-block` |
| A stylesheet or script | the packer: `--clear-id=ezjscore-packer`, then `template-block` |

On a server with Velocity, `./console exp:velocity deploy` runs the right clears in the right order after a change;
see [Deploying](../../../guides/deploying.md).

## Related pages

- Previous: [Debugging templates](debugging-templates.md)
- Next: [Content classes for builders](content-classes-for-builders.md)
- [Caches](../../../guides/caches.md)
- [Cache tags, cache blocks and clearing caches](../part-8-reference/cache-tags-and-clearing.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
