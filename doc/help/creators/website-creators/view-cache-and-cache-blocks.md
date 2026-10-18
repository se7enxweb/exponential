---
title: View cache and cache blocks
audience: website-creator, developer
category: website-creation
order: 630
summary: How the content view cache and template cache blocks work - what is cached per page, which publish clears what (viewcache.ini), cache-block keys, expiry and subtree expiry, and how to debug stale output.
keywords: view cache, ViewCaching, CachedViewModes, viewcache.ini, ClearRelationTypes, cache-block, keys, subtree_expiry, expiry, ignore_content_expiry, stale content, website creator
type: help
---
# View cache and cache blocks

This page is for website creators who template pages and need them both fast and current. Two caches do most of the
work: the **content view cache** keeps the rendered main area of each page, and **cache blocks** keep parts of
templates such as menus.

## Contents

1. [The content view cache](#the-content-view-cache)
2. [What a publish clears](#what-a-publish-clears)
3. [Cache blocks](#cache-blocks)
4. [Keys, expiry and subtree expiry](#keys-expiry-and-subtree-expiry)
5. [Debugging stale output](#debugging-stale-output)

## The content view cache

`site.ini [ContentSettings]`:

| Key | Shipped | Meaning |
|---|---|---|
| `ViewCaching` | `enabled` | cache the output of `content/view` |
| `CachedViewModes` | `full;sitemap;pdf` | the view modes cached |
| `CachedViewPreferences[]` | | user preferences that change the output and become part of the key |

A cached view is kept per node, view mode, language, siteaccess, user's roles and limitations, view parameters and
layout. The pagelayout is not part of it: it runs every time, which is why it uses cache blocks.

## What a publish clears

Publishing an object clears the cache of its own nodes, their parents, and related objects, as
`settings/viewcache.ini [ViewCacheSettings]` says: `ClearRelationTypes[]` (`common`, `reverse_common`,
`reverse_embedded`) decides which relations count; `SmartCacheClear` adds rules per class. A page that lists content
from far away in the tree (a front page listing news) is not cleared automatically unless a rule says so; use
subtree expiry in its cache blocks, or a smart cache clear rule.

## Cache blocks

```text
{cache-block keys=array( $module_result.uri, $current_user.contentobject_id )}
  ... expensive menu ...
{/cache-block}
```

The block's output is stored and reused while its keys match and it has not expired. Published content clears all
template block caches by default, so a new folder appears in menus.

## Keys, expiry and subtree expiry

| Parameter | Meaning |
|---|---|
| `keys` | values the output depends on: the address, the user, the language |
| `expiry` | seconds until the block expires (default two hours); `0` means never by time |
| `ignore_content_expiry` | the block is not cleared by publishing |
| `subtree_expiry` | the block is cleared only when something below this node is published |

A front page news list:

```text
{cache-block subtree_expiry='/News' expiry=0}
  {foreach fetch( 'content', 'list', hash( 'parent_node_id', 58, 'limit', 5, 'sort_by', array( 'published', false() ) ) ) as $item}
    {node_view_gui content_node=$item view='line'}
  {/foreach}
{/cache-block}
```

Never cache output that differs per user (a greeting, a basket count) without the user in the keys.

## Debugging stale output

| Symptom | Try |
|---|---|
| A page shows old content after publish | `--clear-tag=content`; then add a smart cache clear rule or subtree expiry |
| A menu shows an old folder | `--clear-id=template-block`; check the block's keys |
| One user's data shown to another | the block lacks the user in its keys: fix now |
| Logged-in users see the same as visitors | `CachedViewPreferences[]` or keys lack what differs |
| Nothing changes after a template edit | the template cache, then the content cache, then Velocity's cache |

The debug bar shows which caches were used for a page ([Exp debug bar](../../../features/6.0/exp-debug-bar.md)).

## Related pages

- [Performance and caching for websites](performance-and-caching.md)
- [Caches](../../../guides/caches.md)
- Caching in applications
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
