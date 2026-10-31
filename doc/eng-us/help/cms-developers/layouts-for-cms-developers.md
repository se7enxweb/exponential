---
title: Layouts for CMS developers
audience: cms-developer
category: cms-development
order: 31
summary: For CMS developers, Exponential Layouts from the developer's side. It covers layouts, zones, blocks and rules, how a page resolves its layout, how layouts meet templates in the pagelayout, layout types, block definitions and view types, template blocks, shared layouts, moving layouts between installations, and when to use layouts instead of templates.
keywords: Exponential Layouts, explayouts, layouts editor, layout type, zone, block, block definition, view type, rule, mapping, resolve_layout, template block, shared layout, zone linking, collections, exp:layouts, landing page, page builder
type: help
---
# Layouts for CMS developers

This page is for developers who decide which parts of a site editors compose themselves. **Exponential Layouts** (the
`explayouts` extensions, a port of the upstream layouts packages to this kernel) lets editors build pages from blocks in zones,
without code, while your templates and content model stay in charge of what each block looks like.

## Contents

1. [The pieces](#the-pieces)
2. [How a page gets its layout](#how-a-page-gets-its-layout)
3. [Where layouts meet your templates](#where-layouts-meet-your-templates)
4. [What you define as a developer](#what-you-define-as-a-developer)
5. [Template blocks: your code inside a layout](#template-blocks-your-code-inside-a-layout)
6. [Shared layouts](#shared-layouts)
7. [Layouts between installations](#layouts-between-installations)
8. [Layouts or templates](#layouts-or-templates)
9. [After a change](#after-a-change)

## The pieces

| Piece | What it is |
|---|---|
| Layout | the plan of a page, made by an editor in the layouts editor |
| Layout type | the zones a layout has (*1 column*, *2 columns*, *Hero + 3 columns* ...), defined in settings with a template |
| Zone | an area of the layout; holds blocks one under the other |
| Block | a title, rich text, an image, a list of content, a gallery, a hero and more; each of a block definition with view types |
| Collection | the items of a list block: picked by hand, or filled by a query |
| Rule (mapping) | which pages use a layout: targets (a location, a subtree, a module page) and conditions (a class, a siteaccess) |
| Shared layout | a layout whose zones others link to, such as header and footer |

The editor's view: [The layouts editor](../layouts-editor.md); the tour: [Layouts editor tour](../build-two-ways/layouts-editor-tour.md).

## How a page gets its layout

For each request the resolver checks the enabled rules by priority; the first whose targets and conditions match the
page wins. Rules live in the database, managed under **Rules** (**Layout mappings**) in the layouts administration,
and a node's **Layouts** tab shows which layout it resolves to ([Layout rules](../designers/theme-design/layout-rules.md),
[Layout mappings](../build-two-ways/layout-mappings.md)).

## Where layouts meet your templates

The pagelayout asks for the page's layout and draws it, or falls back to the normal view result:

```smarty
{def $page_layout = fetch( 'explayouts', 'resolve_layout', hash() )}
{if $page_layout}
    {include uri='design:explayouts/layout.tpl' layout=$page_layout module_result=$module_result}
{else}
    {$module_result.content}
{/if}
{undef $page_layout}
```

Blocks of a layout can show the page's own content (the full view in a layout), so a layout can frame the template
you wrote for a class rather than replace it. Other fetch functions answer which layout or which rules a node has
([Fetching a layout in templates](../designers/templates/layouts-template-fetch.md)).

## What you define as a developer

| You define | Where | Read |
|---|---|---|
| Layout types and their zones | `[LayoutType_<name>]` in `explayouts.ini` (in your extension's append file) and a layout type template | [Layout types and zones](../designers/theme-design/layout-types-and-zones.md) |
| New view types of existing blocks | the block definition's view types and a template; no PHP | [Add a view type](../build-two-ways/add-a-view-type.md) |
| New block types | a block definition and its templates | [Add a block type](../build-two-ways/add-a-block-type.md), [Block definitions](../designers/theme-design/block-definitions.md) |
| How blocks look | block view templates and stylesheets in your design | [Block view templates](../designers/theme-design/block-view-templates.md), [Styling layout blocks](../designers/theme-design/styling-layout-blocks.md) |
| How list items look | your view modes per class | [View modes](view-modes.md) |

Editors then combine what you defined. Keep the number of layout types and view types small: each one is a choice an
editor must understand.

## Template blocks: your code inside a layout

A **template block** places a template of your design in a zone. Use it for parts that need code (a search form, a
calendar fed by a fetch, a widget) while editors still decide where they appear
([Template blocks](../build-two-ways/template-blocks.md)). This is where the two ways of building meet
([Two ways to build](../build-two-ways/two-ways-to-build.md)).

## Shared layouts

A shared layout holds blocks many pages use; a zone of another layout **links** to a zone of the shared layout instead
of copying its blocks. Editing the shared layout changes every page that links to it
([Shared layouts and zone linking](../build-two-ways/shared-layouts-and-zone-linking.md)).

## Layouts between installations

Layouts and rules live in the layouts tables of the database, not in content packages. They travel as one ZIP file:

```bash
./console exp:layouts list
./console exp:layouts export --all --output=var/tmp/layouts.zip
./console exp:layouts import var/tmp/layouts.zip --dry-run
```

([Export and import layouts](../build-two-ways/export-and-import-layouts.md)). Rules that target locations refer to
node IDs, which differ between installations; check them after an import.

## Layouts or templates

| Situation | Build with |
|---|---|
| Landing pages, campaigns, a front page editors rearrange often | layouts |
| Every article, event or product page | a template per class, possibly framed by a layout |
| A list whose content follows rules (newest five news) | a layout block with a query collection, or a fetch in a template |
| Something that needs code | a template, placed with a template block if editors choose where |

The [Decision table](../build-two-ways/decision-table.md) goes through more cases.

## After a change

Layout changes are published in the editor; settings and templates need caches cleared. The resolver keeps its own
cache, and pages are cached in the view caches and in Velocity's response cache
([Caches after a change](../build-two-ways/caches-after-a-change.md),
[Why doesn't my change appear?](../build-two-ways/why-doesnt-my-change-appear.md)).

## Related pages

- [Two ways to build](../build-two-ways/README.md)
- [Layouts for designers](../designers/theme-design/layouts-for-designers.md)
- [Trainers' course: Exponential Layouts](../trainers/layouts/README.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
