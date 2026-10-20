---
title: Template caches
audience: developer, designer, app-creator
category: development
order: 410
summary: Every cache a template touches - compiled templates, the template-block cache of cache-block (keys, expiry, subtree_expiry, ignore_content_expiry and how the cache file name is built), the content view cache and its key, the template override cache, the design base and INI caches - with the ezcache ids that clear each and what exp:velocity deploy clears.
keywords: template cache, cache-block, template-block, keys, expiry, subtree_expiry, ignore_content_expiry, view cache, ViewCaching, CachedViewModes, template-override, design_base, ini cache, ezcache, clear-id, exp:velocity deploy
type: help
---
# Template caches

A template is cached in several ways at once, each with its own key, its own expiry and its own way to clear it.
Knowing which cache holds an output is most of the work when "my change does not show". This page lists them in the
order a request meets them.

## Contents

1. [Overview](#overview)
2. [Compiled templates](#compiled-templates)
3. [The template override cache and the design base cache](#the-template-override-cache-and-the-design-base-cache)
4. [The content view cache](#the-content-view-cache)
5. [The template-block cache (cache-block)](#the-template-block-cache-cache-block)
6. [The INI cache](#the-ini-cache)
7. [Clearing, and what a deploy clears](#clearing-and-what-a-deploy-clears)
8. [Related pages](#related-pages)

## Overview

The caches the kernel knows are listed in `eZCache::fetchList()` (`kernel/classes/ezcache.php`); each has an id for
`php bin/php/ezcache.php --clear-id=<id>` and tags for `--clear-tag=<tag>`:

| Cache | id | Tags | Folder below `var/<site>/cache/` | Switched on by |
|---|---|---|---|---|
| Compiled templates | `template` | template | `template/` | `[TemplateSettings] TemplateCompile` |
| Template-block (cache-block) | `template-block` | template, content | `template-block/` | `[TemplateSettings] TemplateCache` |
| Template override | `template-override` | template | `override/` | always |
| Content view | `content` | content | `[ContentSettings] CacheDir` (`content`) | `[ContentSettings] ViewCaching` |
| Design base list | `design_base` | template | design base cache files | `[DesignSettings] DesignLocationCache` |
| INI | `ini`, `global_ini` | ini | `ini/`, and `var/cache/ini` | always |
| Translation | `translation` | i18n | | always |

`php bin/php/ezcache.php --list-ids` and `--list-tags` print the full list of an installation, with the caches of
extensions. The template-related settings on alpha's `site` siteaccess:

```tpl
TemplateCompile:  {ezini( 'TemplateSettings', 'TemplateCompile' )}
DevelopmentMode:  {ezini( 'TemplateSettings', 'DevelopmentMode' )}
TemplateCache:    {ezini( 'TemplateSettings', 'TemplateCache' )}
NodeTreeCaching:  {ezini( 'TemplateSettings', 'NodeTreeCaching' )}
ViewCaching:      {ezini( 'ContentSettings', 'ViewCaching' )}
CachedViewModes:  {ezini( 'ContentSettings', 'CachedViewModes' )}
```

Output on alpha:

```text
TemplateCompile:  enabled
DevelopmentMode:  disabled
TemplateCache:    enabled
NodeTreeCaching:  disabled
ViewCaching:      enabled
CachedViewModes:  full;sitemap;pdf;googlesitemap
```

## Compiled templates

The PHP files of the [template compiler](template-compiler.md), one per template, siteaccess and locale. A compiled
template is rebuilt when its own file is newer; an included template is only checked for a newer file with
`DevelopmentMode=enabled`. Clear with `--clear-id=template`.

## The template override cache and the design base cache

The [design resource](resources-and-overrides.md) keeps two lookups:

- the **override cache** (`override/`): the match rules of `override.ini` per template, built by
  `eZTemplateDesignResource::createOverrideCache()`. A **new** override rule or a new template file is not seen
  until it is cleared (`--clear-id=template-override`); a changed existing template is.
- the **design base cache**: the ordered list of design folders of the siteaccess, written when
  `DesignLocationCache=enabled` (`eZTemplateDesignResource::allDesignBases()`). Clear it (`--clear-id=design_base`)
  after adding a design to an extension or enabling an extension with a design.

## The content view cache

`content/view` stores the whole `$Result` of a node view (HTML, `content_info`, the persistent variable, the list of
templates used) for the view modes in `[ContentSettings] CachedViewModes` (`full;sitemap;pdf;googlesitemap` on
alpha). The cache file name is a hash of (`eZNodeviewfunctions::generateViewCacheFile()`):

- node ID, view mode, language, offset, layout;
- the user's role IDs, limited-assignment values and discount rules (each can be left out with a
  `ViewCacheTweaks` value: `ignore_userroles`, `ignore_userlimitedlist`, `ignore_discountlist`);
- the index file, the siteaccess (unless `ignore_siteaccess_name`) and, for URI siteaccesses, the access type;
- the [view parameters](view-parameters.md) (unless `ignore_viewparameters`);
- user preferences listed in `CachedViewPreferences` for the view mode;
- keys an extension adds through the `content/view/cachekeys` event filter.

It expires when content is published (the cache manager clears the node and the nodes related to it, by the rules of
`viewcache.ini`), when the global `content-view-cache` expiry timestamp moves, and when one of the templates in
`template_list` is newer than the cache file. A node template can shorten its life with `{set-block scope=root
variable=cache_ttl}0{/set-block}` (as the cjw_newsletter templates do): `$cache_ttl` set to `0` stops caching of that
page, a positive value is
a lifetime in seconds. Clear with `--clear-id=content`.

## The template-block cache (cache-block)

`cache-block` stores the output of a part of a template (`lib/eztemplate/classes/eztemplatecachefunction.php`,
`eztemplatecacheblock.php`). Not run on alpha: rendering a cache-block writes a cache file, so this example is shown
without output:

```tpl
{cache-block keys=array( $node.node_id, $view_parameters.offset ) subtree_expiry=$node.node_id expiry=3600}
    {foreach fetch( 'content', 'list', hash( 'parent_node_id', $node.node_id, 'limit', 10 ) ) as $child}
        {node_view_gui content_node=$child view='line'}
    {/foreach}
{/cache-block}
```

| Parameter | Effect |
|---|---|
| `keys` | values that make the cache differ; anything, arrays are walked |
| `expiry` | lifetime in seconds; default 7200 (`eZTemplateCacheFunction::DEFAULT_TTL`); `0` means no time limit |
| `ignore_content_expiry` | do not expire when any content is published |
| `subtree_expiry` | a node ID, URL alias or `content/view/full/<id>`; the block expires when content below that node is published; implies `ignore_content_expiry` |

How the cache is found:

- The key always includes the **placement** of the tag (`eZTemplateCacheBlock::placementString()`: start line and
  column, end line and column, template file) and the **siteaccess** name, plus the `keys` value when given. Two
  cache-blocks never share a file, and moving a cache-block in its template makes a new file.
- The file name is the md5 of that key, stored in `template-block/<a>/<b>/<c>/<md5>.cache`, or below the subtree
  folder of the node for `subtree_expiry`.
- Without `ignore_content_expiry`, every publish expires every such block: publishing sets the
  `template-block-cache` timestamp (`eZContentObject::expireTemplateBlockCache()`). The global
  `global-template-block-cache` timestamp, moved by clearing the cache, expires all blocks.
- With `TemplateCache=disabled` the block's content is rendered every time, as if the tag were not there.

Things that follow:

- Anything that differs per visitor, per role or per request inside a block must be in `keys`, or it is shown to the
  next visitor too. `$current_user.contentobject_id` in the keys makes one block per user.
- `rand`, `currentdate` and similar values freeze until the block expires.
- A `def` inside a block is not run on a cache hit: variables the rest of the template needs must be defined outside.

The designer's guide is [Cache block](../designers/templates/cache-block.md).

## The INI cache

Settings files are cached as PHP in `var/cache/ini` (`global_ini`) and the site cache's `ini` folder. A change to an
`.ini` file of an extension or of `settings/` is seen after `--clear-tag=ini`; the templates that read settings with
`ezini()` then get the new value.

## Clearing, and what a deploy clears

```text
php bin/php/ezcache.php --clear-id=template,template-override --allow-root-user
php bin/php/ezcache.php --clear-tag=template --allow-root-user
php bin/php/ezcache.php --clear-all --allow-root-user
```

`./console exp:velocity deploy` (`kernel/classes/expvelocitydeploy.php`) clears, in this order: the `template`,
`template-override`, `translation` and `design_base` caches, then, after reloading PHP and restarting Velocity,
`content`, `exphttpcache`, `ezjscore-packer` (with `--packer`) and `template-block`. The order matters: a page
rendered with the old code between a clear and a reload would be cached again.

Which to clear after what:

| You changed | Clear |
|---|---|
| an existing template | nothing with `DevelopmentMode=enabled`; otherwise `template`, and `content` / `template-block` for cached output |
| a new template file or a new override rule | `template-override` (and `template`) |
| a design folder of an extension | `design_base`, `template-override` |
| a setting | `ini` tag |
| content | nothing: publishing expires the view cache and the template-block cache |

## Related pages

- [The template compiler](template-compiler.md)
- [Template compilation and cache](../designers/templates/template-compilation-and-cache.md)
- [Cache block](../designers/templates/cache-block.md)
- [Template resources and overrides](resources-and-overrides.md)
- [Caching in applications](../creators/app-creators/caching-in-applications.md)
- [ezcache](../../examples/commands/ezcache.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
