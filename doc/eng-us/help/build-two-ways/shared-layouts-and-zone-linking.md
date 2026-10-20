---
title: Shared layouts and zone linking
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 300
summary: Why one header serves every page - shared layouts, linked zones and the real link map of the Fit & Healthy site - and the "why doesn't my change appear" cases that come from it.
keywords: shared layout, linked zone, zone linking, Header / Footer, Prefooter, linked_layout_id, linked_zone_identifier, layouts editor, exp:layouts list, Links to, why change not shown
type: help
---
# Shared layouts and zone linking

A layout is made of **zones**, and a zone either holds its own blocks or is **linked** to a zone of a **shared
layout**. A linked zone shows the blocks of the shared zone. Change the shared zone once and every linked page changes.

## Contents

1. [The shared layouts of the site](#the-shared-layouts-of-the-site)
2. [Who links to what](#who-links-to-what)
3. [What linking means for editing](#what-linking-means-for-editing)
4. [See the links](#see-the-links)
5. [Common surprises](#common-surprises)

## The shared layouts of the site

**Layouts > Shared layouts** lists the layouts that other layouts link to:

![The Shared layouts list: Header / Footer with 36 references, Prefooter with 2 references, and the Starter shared layouts](images/hm-10-shared-layouts-list.webp)

| Shared layout | Type | Zones and blocks |
|---|---|---|
| Header / Footer | `layout_2` | `header`: template blocks header and breadcrumb; `footer`: template block footer; `post_header`, `main`, `pre_footer`: empty |
| Prefooter | `layout_1` | `main`: a Column (background `secondary`) with a Title "Most popular articles" and a List (view Grid, 3 columns) of three hand-picked articles |

The **Starter** shared layouts belong to another theme of the installation.

## Who links to what

On the Fit & Healthy site (published layouts, from the database):

| Layouts | `header` | `pre_footer` | `footer` |
|---|---|---|---|
| Full / Article, Full / Recipe | Header / Footer: header | Prefooter: main | Header / Footer: footer |
| Default Layout, Full / Topic, Full / Video, Full / Article (Plain), the five Landing pages (Running, Fitness, Healthy Eating, Home, Video), Showcase, the six Bold Agency layouts | Header / Footer: header | own (empty) | Header / Footer: footer |

So the **Prefooter** shows only on articles and recipes. Adding a block to it changes those two page types and no
others.

## What linking means for editing

- Blocks of a linked zone are **not** the page layout's blocks. The editor shows the zone as linked; its blocks are
  edited by opening the shared layout itself (Layouts > Shared layouts > Header / Footer).
- A shared layout is edited like any layout: open it, change it in a draft, **Publish layout**. Every linked page
  shows the published version.
- Linking is set per zone, in the editor of the page layout. A zone can be unlinked to give one page type its own
  blocks.

## See the links

In the console, `exp:layouts list` shows the column **Links to**:

```text
$ ./console exp:layouts list
ID     Identifier                               Name                                 Status     Shared    Rules  Links to
2      fd0aa7af77a85a03bf1080018f5db1dc         Header / Footer                      published  shared        0
7      d14c82715a0c5f1da1e6df4d0b77e638         Prefooter                            published  shared        0
...
21     84f28edc38d2524da1e1d720de5942ba         Full / Article                       published                1  fd0aa7af77a85a03bf1080018f5db1dc, d14c82715a0c5f1da1e6df4d0b77e638
...
29     df2a8cde629658cc80f2fe59cefa77e9         Landing Page / Home                  published                2  fd0aa7af77a85a03bf1080018f5db1dc
```

The links are stored per zone as `linked_layout_id` plus `linked_zone_identifier`; both are needed.

## Common surprises

| What you see | Why | Do |
|---|---|---|
| You added a block to the page layout's `header` zone and it is not shown | The zone is linked; the page shows the shared zone | Add it to Header / Footer, or unlink the zone |
| A block added to Prefooter shows on articles but not on landing pages | Only Full / Article and Full / Recipe link `pre_footer` | Link the other layouts' `pre_footer`, or add the block there |
| The header menu is not in the editor | The header is a template block; the menu is in `menu.ini` | [Add a header menu item](add-a-header-menu-item.md) |
| You changed Header / Footer and nothing changed | The draft is not published yet | **Publish layout** |

More cases: [Why doesn't my change appear?](why-doesnt-my-change-appear.md)

## Related pages

- [How the header is built](how-the-header-is-built.md)
- [The layouts editor](../layouts-editor.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
