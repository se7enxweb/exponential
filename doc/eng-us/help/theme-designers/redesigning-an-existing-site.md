---
title: "Redesigning an existing site"
audience: theme-designer
category: theme-design
order: 41
summary: How theme designers redesign a running Exponential site - auditing the current design and its overrides, deciding what to keep, introducing tokens into an old theme, building the new design beside the old one on a preview siteaccess, and switching over without surprises.
keywords: theme designer, redesign, refresh, audit, existing site, overrides, legacy theme, tokens, preview siteaccess, migration, switch over, incremental
type: help
---
# Redesigning an existing site

This page is for theme designers asked to redesign a site that already runs on Exponential. A redesign is easier than
a new site in one way (the content exists, so you can design with it from day one) and harder in another (years of
overrides, special cases and editor habits exist too). This page is a plan for the design side; the upgrade of the
software itself is a separate project for the developer.

## Contents

1. [Audit before you draw](#audit-before-you-draw)
2. [Decide what to keep](#decide-what-to-keep)
3. [New design or refreshed design](#new-design-or-refreshed-design)
4. [Introducing tokens into an old theme](#introducing-tokens-into-an-old-theme)
5. [Building beside the live design](#building-beside-the-live-design)
6. [Editors and the redesign](#editors-and-the-redesign)
7. [Switching over](#switching-over)

## Audit before you draw

Collect, with the developer:

1. **The design cascade** of each siteaccess: which designs, in which order ([Designs and siteaccesses](../designers/theme-design/designs-and-siteaccesses.md)).
2. **The overrides**: every rule in the design's `override.ini`, which tells you every class, node and section that
   has its own template. The administration's template editor lists them per template
   ([The template editor](../designers/templates/template-editor-overrides.md)).
3. **The content**: classes, fields and how they are really used ([Your first day as a theme designer](your-first-day-as-a-theme-designer.md)).
4. **Analytics, if the client shares them**: which pages are visited, and which never.
5. **Problems**: accessibility, dark mode, performance, small screens, known complaints.

Comparing the site's overrides with the current standard templates shows where the old design has drifted
([Comparing your overrides with the standard templates](../designers/lifecycle/comparing-overrides-with-the-standard.md)).

## Decide what to keep

For each override and special page, decide: keep as is, redesign, or remove. Overrides for pages nobody visits are
cost without value. Things worth keeping are often invisible: URLs, the content structure, and the habits of editors.
Changing the tree changes addresses, which affects search engines and links; that is a content decision, not a design
one.

## New design or refreshed design

| | Refresh | New design |
|---|---|---|
| Templates | kept, restyled | rewritten |
| Content model | unchanged | may change |
| Risk | low | higher |
| Typical scope | tokens, type, color, components, dark mode | everything in [From mockup to theme](from-mockup-to-theme.md) |

A refresh can deliver much of the visible gain at a fraction of the risk, especially if the old theme gets tokens.

## Introducing tokens into an old theme

1. List every distinct color, font size, spacing and radius value in the old stylesheets.
2. Group near-duplicates and give each group a role name ([Color palettes and ramps](color-palettes-and-ramps.md),
   [Type scale](type-scale.md), [Spacing and grid](spacing-and-grid.md)).
3. Write the token file, with dark values ([Design tokens](../designers/theme-design/design-tokens.md)).
4. Replace literal values with tokens, stylesheet by stylesheet, and check pages after each.
5. While doing so, move any inline styles from templates into classes ([Removing inline scripts and styles](../designers/lifecycle/removing-inline-scripts-and-styles.md)).

After this, dark mode and later brand changes are mostly token work
([Tutorial: add dark mode](../designers/theme-design/tutorial-add-dark-mode.md)).

## Building beside the live design

Build the new design as a new design extension, and serve it on a **preview siteaccess** with the live content while
visitors keep the old one ([Previewing a design on a siteaccess](../designers/theme-design/previewing-a-design-on-a-siteaccess.md)).
Review there with editors and the client ([Working with clients and reviews](working-with-clients-and-reviews.md)).
Never edit the live design in place; keep the old design untouched until the switch.

## Editors and the redesign

- Tell editors early what changes for them: new block view types, new image shapes, new field guidance.
- Ask them which content looks worst today; it is usually the best stress test.
- Plan content clean-up (missing alternative texts, oversized images, empty intros) as a task with an owner, not as a
  hope.

## Switching over

The switch is a settings change made by the developer or administrator: the siteaccess gets the new design list.
Before it:

- [ ] [Design QA checklist](design-qa-checklist.md) passed on the preview siteaccess.
- [ ] Caches planned: the developer clears the relevant caches after the switch ([Caching for designers](../designers/theme-design/caching-for-designers.md)).
- [ ] Old overrides that are no longer needed listed for removal later.
- [ ] A way back agreed: the old design stays installed until the new one has run for a while.

## Related pages

- [Tutorial: port a static HTML design](../designers/theme-design/tutorial-port-a-static-html-design.md)
- [Visual regression checks](../designers/lifecycle/visual-regression-checks.md)
- [Breaking changes and the next major version](../designers/lifecycle/breaking-changes.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
