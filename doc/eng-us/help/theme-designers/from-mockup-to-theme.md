---
title: "From mockup to theme"
audience: theme-designer
category: theme-design
order: 5
summary: The workflow from a design in a design tool to a working Exponential theme - seven stages, who does what in each, what each stage hands to the next, and where the technical steps are documented.
keywords: theme designer, workflow, mockup, prototype, handoff, design process, stages, static html, port, theme extension, review
type: help
---
# From mockup to theme

This page is for theme designers who plan a project from the first sketch to a published theme. It describes seven
stages, what each stage produces and who usually owns it. The technical steps (writing templates, packaging the
extension) are documented elsewhere; this page tells you when they happen and links to them.

## Contents

1. [The seven stages](#the-seven-stages)
2. [Stage 1: inventory](#stage-1-inventory)
3. [Stage 2: foundations](#stage-2-foundations)
4. [Stage 3: components](#stage-3-components)
5. [Stage 4: templates of record](#stage-4-templates-of-record)
6. [Stage 5: static prototype](#stage-5-static-prototype)
7. [Stage 6: the theme extension](#stage-6-the-theme-extension)
8. [Stage 7: review and launch](#stage-7-review-and-launch)
9. [When stages overlap](#when-stages-overlap)

## The seven stages

| Stage | Produces | Owner | Technical page |
|---|---|---|---|
| 1. Inventory | list of classes, views, special pages, blocks | designer with developer | [Mapping a design to content classes](mapping-a-design-to-content-classes.md) |
| 2. Foundations | tokens: color, type, space, radius | designer | [Design tokens](../designers/theme-design/design-tokens.md) |
| 3. Components | component inventory with states | designer | [Component inventory](component-inventory.md) |
| 4. Templates of record | frame + one mockup per class and view | designer | [Designing view modes](designing-view-modes.md) |
| 5. Static prototype | HTML and CSS, no CMS | designer or front-end developer | [Tutorial: port a static HTML design](../designers/theme-design/tutorial-port-a-static-html-design.md) |
| 6. Theme extension | design extension with templates and stylesheets | front-end developer | [Tutorial: create a theme extension](../designers/theme-design/tutorial-create-a-theme-extension.md) |
| 7. Review and launch | checked and released theme | both | [Checklist: theme launch](../designers/theme-design/checklist-theme-launch.md) |

## Stage 1: inventory

Before drawing, list what exists: content classes and their fields, the view modes each class appears in, the special
pages (search, sign-in, errors, forms) and, if the site uses layouts, the block types and view types. On an existing
site, the [first day plan](your-first-day-as-a-theme-designer.md) produces this list. On a new site, it comes from the
content model ([Designing a content model](../cms-developers/designing-a-content-model.md)), so sit with the developer
while the classes are decided: a field that is not in the class cannot appear in your design.

**Hand-off:** the inventory, agreed in writing.

## Stage 2: foundations

Decide the small set of values everything else uses: a type scale, a spacing scale, a color palette with roles, one
radius, one accent. Give every value a role name now, because each becomes a CSS custom property
([Type scale](type-scale.md), [Spacing and grid](spacing-and-grid.md), [Color palettes and ramps](color-palettes-and-ramps.md)).
Draw the dark values at the same time; adding them later costs twice as much.

**Hand-off:** a token table (name, light value, dark value, use), see
[From design tool variables to CSS custom properties](from-design-tool-variables-to-css-custom-properties.md).

## Stage 3: components

Draw the reusable pieces (buttons, cards, tags, form fields, pagination, alerts) with all their states: hover, focus,
active, disabled, current, error ([Designing interactive states](designing-interactive-states.md)). Each component
uses only tokens.

**Hand-off:** the [component inventory](component-inventory.md).

## Stage 4: templates of record

Now draw pages, but think in templates: the frame (header, navigation, footer) once, then one mockup per class and
view mode, then the special pages. Use real content at its shortest and longest ([Designing with real content](designing-with-real-content.md)).
Mark which parts are optional fields, so the developer knows what to hide when a field is empty
([Designing for editors](designing-for-editors.md)).

**Hand-off:** frame, views per class, special pages, each at three widths, in light and dark.

## Stage 5: static prototype

A static HTML and CSS prototype catches most layout problems before any template exists. Keep it honest:

- one stylesheet for tokens, one for components, no inline styles;
- class names with your theme's prefix, for example `mytheme-card`;
- no fonts or scripts from third-party hosts ([Fonts in a design](../designers/theme-design/fonts-in-a-design.md)).

Whoever writes the templates later can then move markup and stylesheets almost unchanged, as the
[porting tutorial](../designers/theme-design/tutorial-port-a-static-html-design.md) shows step by step.

**Hand-off:** the prototype in the theme's Git repository, see [Working with developers](working-with-developers.md).

## Stage 6: the theme extension

The prototype becomes a design extension: the frame becomes `pagelayout.tpl`, each mockup a view template, the
stylesheets are listed in the design settings and packed. The tutorials cover this:
[Create a theme extension](../designers/theme-design/tutorial-create-a-theme-extension.md) and
[Override a full view](../designers/theme-design/tutorial-override-a-full-view.md). Designers who do not write
templates review each template against its mockup in this stage.

## Stage 7: review and launch

Review on a preview siteaccess with real content ([Working with clients and reviews](working-with-clients-and-reviews.md)),
run the [design QA checklist](design-qa-checklist.md), then the project's launch, accessibility, dark mode and
performance checklists. A theme that will be published continues with
[Distributing a theme: start here](../designers/distribution/distribution-start-here.md).

## When stages overlap

Stages 2 to 5 often overlap, and that is fine. Two rules keep the overlap safe:

- a token or component changes in the inventory first, then in mockups and code;
- nothing reaches stage 6 that was never seen with real content.

## Related pages

- [Handoff checklist](handoff-checklist.md)
- [Theme design review checklist](theme-design-review-checklist.md)
- [Choosing a starting theme](../designers/theme-design/choosing-a-starting-theme.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
