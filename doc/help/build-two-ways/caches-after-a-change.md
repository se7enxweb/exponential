---
title: Caches after a change
audience: admin, website-creator, designer, developer, editor
category: layouts
order: 170
summary: Which cache to clear after which kind of change - menu settings, templates, layouts, content - in the administration's Clear cache box and with exp:cache, and what each choice covers.
keywords: cache, clear cache, Template & content, exp:cache, exphttpcache, compiled templates, template-block, content view cache, ini cache, Velocity, layouts resolver
type: help
---
# Caches after a change

Exponential keeps several caches so pages are fast. After a change, the right one has to go. This table is what was
needed on the Fit & Healthy site while writing these pages.

## Contents

1. [The table](#the-table)
2. [The Clear cache box](#the-clear-cache-box)
3. [The console](#the-console)
4. [Velocity](#velocity)

## The table

| You changed | Clear | Why |
|---|---|---|
| A content page (publish) | nothing, usually | Publishing clears the caches of the page; on the test copy, other pages were also rendered fresh (page cache miss) right after a publish |
| `menu.ini` (header or footer menu IDs) | Template & content | The menu templates are compiled with the setting's values in them |
| Another INI setting read in a template with `ezini()` | Template & content | Same reason |
| An INI setting read only in PHP | Ini settings (saving in the administration or `exp:ini` does it) | The INI cache |
| An existing `.tpl` file | Template & content | Compiled template, template blocks, view cache, page cache |
| A new `.tpl` file (a new override) | Template & content | The override list (`template-override`) is part of the `template` tag |
| A layout, published in the Layouts editor | Template & content if a page still shows the old blocks | See [Why doesn't my change appear?](why-doesnt-my-change-appear.md) |
| A stylesheet or script of the design | Template & content | The packed files (`ezjscore-packer`) and the pages that link them |

## The clear cache box

The **Clear cache** box on the right of the administration offers:

| Choice | Clears |
|---|---|
| All caches | every cache |
| Content | the caches with the tag `content` |
| Content - node, Content - subtree | the view cache of one page or a branch (from a page's view) |
| Template | the caches with the tag `template` |
| Template & content | the tags `template` and `content` |
| Ini settings | the tag `ini` |
| Static | the static page cache, regenerated |

![The Clear cache box with the choice list and the Clear button](images/hm-07-clear-cache-panel.webp)

With the tags `template` and `content` together, these are cleared (from a real run): `exphttpcache` (page cache),
`template` (compiled templates), `template-block`, `template-override`, `texttoimage`, `design_base`,
`ezjscore-packer`, `content` (view cache), `querycache`, `classid`, `sortkey`, `urlalias`, `rss_cache`,
`content_tree_menu`, `state_limitations`, `content_language` and `rest`: 17 caches.

## The console

```text
./console exp:cache clear --tag=template,content     # = Template & content
./console exp:cache clear --id=exphttpcache          # one cache by id
./console exp:cache list                             # every cache, id, tags
./console exp:cache clear --tag=template --dry-run   # what would be cleared
```

`exp:cache` ends with `PASS` or `FAIL`. On the test copy, clearing `template,content` took 2.0 to 2.5 seconds and
the first page after it 2.9 to 3.4 seconds to render, the next one 22 to 102 milliseconds
([Measured steps and times](measured-steps-and-times.md)).

## Velocity

A site served by Exponential Velocity has its own response cache and workers that keep the settings of their
warm-up. After a settings change there, `exp:ini` itself advises `./console exp:velocity restart`.

## Related pages

- [Header menu troubleshooting](header-menu-troubleshooting.md)
- [Why doesn't my change appear?](why-doesnt-my-change-appear.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
