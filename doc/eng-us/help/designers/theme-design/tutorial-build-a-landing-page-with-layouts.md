---
title: "Tutorial: build a landing page with layouts"
audience: designer, editor
category: theme-design
order: 790
summary: Step by step, from design to published page: prepare the pagelayout to draw layouts, style the blocks a landing page uses, build the layout in the layouts editor, map it to the page with a rule, and check it on phone, desktop and zoom.
keywords: tutorial, landing page, layouts editor, page builder, campaign page, hero block, rule, mapping, publish layout, design blocks, resolve_layout
type: help
---
# Tutorial: build a landing page with layouts

This tutorial is for a designer, working with an editor, who wants a landing page composed of blocks: a hero, three
features, a list of articles, a call to action. Allow one to two hours. It assumes your design draws layouts
([Layouts for designers](layouts-for-designers.md)); the media and starter designs already do.

## Contents

1. [Step 1: the pagelayout draws layouts](#step-1-the-pagelayout-draws-layouts)
2. [Step 2: decide the blocks](#step-2-decide-the-blocks)
3. [Step 3: style the blocks](#step-3-style-the-blocks)
4. [Step 4: build the layout](#step-4-build-the-layout)
5. [Step 5: map it to the page](#step-5-map-it-to-the-page)
6. [Step 6: publish and check](#step-6-publish-and-check)

## Step 1: the pagelayout draws layouts

In your `pagelayout.tpl`, where `{$module_result.content}` used to be:

```tpl
{def $page_layout = fetch( 'explayouts', 'resolve_layout', hash() )}
{if $page_layout}
    {include uri='design:explayouts/layout.tpl' layout=$page_layout module_result=$module_result}
{else}
    {$module_result.content}
{/if}
{undef $page_layout}
```

Pages without a matching rule look exactly as before.

## Step 2: decide the blocks

| Section of the page | Block (definition) | View type |
|---|---|---|
| Big picture with headline and button | `hero` | default |
| Three selling points | `three_columns` with `rich_text` children | default |
| Latest articles | `list` with a query collection | `grid` |
| Call to action | `button` | default |

Pick from the blocks your installation offers ([Block definitions](block-definitions.md)); a new look is usually a new
view type, not a new block.

## Step 3: style the blocks

Open the template of each block in your design (copy it from `extension/explayouts/design/standard/templates/explayouts/block/`
if your design has none) and give it the markup you want; then style by class in a stylesheet the pagelayout loads:

```css
.slb-hero { padding-block: var(--site-space-5); background: var(--site-card); }
.slb-hero h1 { font-size: var(--site-text-xl); }
.slb-zone > * + * { margin-block-start: var(--site-space-5); }
```

Use tokens so the page works in dark mode ([Styling layout blocks](styling-layout-blocks.md)).

## Step 4: build the layout

With the editor, in the administration's layouts editor:

1. **New layout**, layout type with a header, main and footer zone (or the type your design uses), name it "Spring
   campaign".
2. Link the header and footer zones to the shared header and footer layout, so the page keeps the site's chrome
   ([Shared layouts and linked zones](shared-layouts-and-linked-zones.md)).
3. Add the blocks to the `main` zone in order; fill their fields; give the list block its collection (for example
   the children of the News folder, limit 6).
4. Preview.

## Step 5: map it to the page

Create a rule: the layout "Spring campaign", target **node** (the landing page) or **subtree**, optionally a **time**
condition for the campaign period, a priority above the general rules ([Layout rules](layout-rules.md)).

## Step 6: publish and check

1. Publish the layout (the site reads only published layouts).
2. Open the landing page signed out.
3. Check at 1440 px, at 960 px with scale 2 and at 375 px; in light and dark mode; with the keyboard.
4. Check one `h1` (the hero) and headings in order ([Landmarks and headings](landmarks-and-headings.md)).
5. Check layout shift: images in the hero and grid have sizes ([Measuring CLS and LCP](measuring-cls-and-lcp.md)).

If the page shows another layout, a rule with a higher priority matches first; if it shows no layout, the rule is
disabled or the layout not published.

## Related pages

- [Layouts for designers](layouts-for-designers.md)
- [Block view templates](block-view-templates.md)
- [The layouts editor](../../layouts-editor.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
