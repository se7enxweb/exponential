---
title: Two ways to build
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 10
featured: true
summary: Exponential 6.0.15+ builds pages two ways - template code in a design extension, and the Layouts editor with no code - and the two meet in blocks and template blocks. What each way is, who uses it, when to choose which and how they combine, shown on the real Fit & Healthy site.
keywords: two ways to build, templates, design extension, template override, Layouts editor, no code, blocks, zones, template block, theme, site building, when to use, combine
type: help
---
# Two ways to build

Exponential 6.0.15+ gives a site two ways of being built, and most real sites use both at once.

| | 1. Templates in a design extension | 2. The Layouts editor |
|---|---|---|
| What you make | `.tpl` files, stylesheets, settings, in your own extension | Layouts: zones filled with blocks, stored in the database |
| Tool | an editor, a terminal, git | the administration: **Layouts** |
| Who | designers, developers | website creators, editors, administrators |
| Changes go live | after deploy and a cache clear | when you press **Publish layout** |
| Versioned | in git with your extension | drafts and published versions in the database; export and import as `.zip` (`exp:layouts`) |
| Reach | everything: markup, data, logic | what the block types and their view types offer |

## Contents

1. [Way 1: templates in a design extension](#way-1-templates-in-a-design-extension)
2. [Way 2: the Layouts editor](#way-2-the-layouts-editor)
3. [Where the two meet](#where-the-two-meet)
4. [A third tool: settings](#a-third-tool-settings)
5. [Which way when](#which-way-when)
6. [What it costs, measured](#what-it-costs-measured)

## Way 1: templates in a design extension

A design is a folder of templates under `extension/<name>/design/<design>/templates/`. The Fit & Healthy site is the
design `media` of the extension `sevenx_themes_media`. Every part of a page is a template there:
`pagelayout.tpl` (the HTML page), `pagelayout/header.tpl`, `pagelayout/footer.tpl`, the content views under
`content/views/`, the block templates under `explayouts/block/`.

A template that another extension provides is changed without touching it: put a file with the same path in your own
design extension, listed later in `design.ini [ExtensionSettings] DesignExtensions[]` (the last one listed wins), and
clear the template caches. Templates use Exponential's template language: `{def}`, `{foreach}`, `fetch()`, `ezini()`,
`|wash`, `|ezurl`.

Choose it for anything structural: the HTML of the page, a new kind of block, a view of a content class, a menu that
fetches its items, markup that must be the same on every page.

## Way 2: the Layouts editor

**Layouts** in the administration builds pages from blocks, by drag and drop. A **layout** has a type (its zones: for
example `header`, `post_header`, `main`, `pre_footer`, `footer`) and holds **blocks** in its zones. The editor offers
the block types defined in `explayouts.ini` (Title, Text, Rich text, Image, Button, List, Grid, Gallery, Slider, Hero,
Card, Columns and more), each with its **view types** and options. **Layout mappings** decide which layout a page gets
(by node, subtree, path or content class).

Choose it for page composition: landing pages, campaign pages, a hero and three teasers, a list of the newest articles,
the blocks above the footer, anything an editor should be able to rearrange without a release.

## Where the two meet

The two ways are one system:

- Every block is rendered by a **template**: `design:explayouts/block/<block type>.tpl` in the design. Change that
  template in your extension and every block of that type changes, everywhere.
- A **Template block** is a block whose content is a template you name. That is how the shared layout **Header /
  Footer** puts the theme's header and footer into every layout: the layout decides *where*, the template decides
  *what* ([How the header is built](how-the-header-is-built.md)).
- **Shared layouts** with **linked zones** let one set of blocks appear on many page types
  ([Shared layouts and zone linking](shared-layouts-and-zone-linking.md)).
- The templates read content with `fetch()`, the blocks read it through **collections** (hand-picked items or a
  query on the tree). Both show the same content tree.

## A third tool: settings

Some things are neither: the header menu of this theme is a list of node IDs in `menu.ini`, read by the header
template. Settings are changed in **Setup > Ini settings** or with `./console exp:ini`, no code
([Add a header menu item](add-a-header-menu-item.md)).

## Which way when

The full table is on [Which way: the decision table](decision-table.md). In short:

| If the change is | Use |
|---|---|
| Where blocks are and which ones | Layouts editor |
| What a block type looks like everywhere | Template override of `explayouts/block/<type>.tpl` |
| The site's chrome (header, footer, page HTML) | Templates; place it with a template block |
| The header or footer menu entries of this theme | Settings (`menu.ini`) or, for the footer's second line, content |
| A new kind of block | Both: a block definition in `explayouts.ini` plus its template, then the editor offers it |

## What it costs, measured

On a throwaway copy of the installation (PHP's built-in server, a shared host under load):

| Task | Way | Steps | Time measured |
|---|---|---|---|
| Add a header menu item | Administration (settings) | 10 actions | |
| Add a header menu item | Console | 2 commands | 1.3 to 2.1 s + 2.0 to 2.5 s |
| Add a footer link | Content tree | 9 actions, no cache clear | |
| First page after a full template and content clear | | | 2.9 to 3.4 s |
| A page from the page cache | | | 22 to 102 ms |

How these were counted: [Measured steps and times](measured-steps-and-times.md).

## Related pages

- [Two ways to build, every page](README.md)
- [Which way: the decision table](decision-table.md)
- [Life-cycle examples](examples/README.md): 206 tasks from planning to migration, each done both ways
- [Build a campaign page, start to finish](build-a-campaign-page.md)
- [Add a header menu item](add-a-header-menu-item.md)
- [The layouts editor](../layouts-editor.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
