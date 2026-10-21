---
title: "Handoff checklist"
audience: theme-designer
category: theme-design
order: 37
summary: The checklist of everything a theme designer hands to developers for an Exponential theme - inventory and mapping sheet, tokens with dark values, components with states, view matrix, image and block sheets, special pages, motion, budget and content guidance - so templates can be written without guessing.
keywords: theme designer, handoff, hand-off, checklist, deliverables, specification, tokens, components, view matrix, image sheet, block sheet, developer handoff
type: help
---
# Handoff checklist

This page is for theme designers handing a design to the developers who will build the theme, or to themselves in a
later role. A complete handoff means templates and stylesheets can be written without guessing, and reviews can check
against something written down. Each line names the page that explains the item.

## Contents

1. [Before you hand off](#before-you-hand-off)
2. [Content](#content)
3. [Foundations](#foundations)
4. [Components](#components)
5. [Pages and views](#pages-and-views)
6. [Images, blocks and behavior](#images-blocks-and-behavior)
7. [Quality targets](#quality-targets)
8. [Where it all lives](#where-it-all-lives)

## Before you hand off

- [ ] The inventory and foundations are signed off ([Working with clients and reviews](working-with-clients-and-reviews.md#sign-off)).
- [ ] The developer has seen the inventory and flagged anything costly ([Working with developers](working-with-developers.md)).
- [ ] Every mockup uses real content, including extremes ([Designing with real content](designing-with-real-content.md)).

## Content

- [ ] Mapping sheet: every visible element to a class, field, datatype, views and empty design
      ([Mapping a design to content classes](mapping-a-design-to-content-classes.md#the-mapping-sheet)).
- [ ] Requests for new fields written, with reasons ([Working with developers](working-with-developers.md#requests-to-the-developer)).
- [ ] Field guidance text for editors, where it helps ([Designing for editors](designing-for-editors.md#telling-editors-what-the-design-expects)).
- [ ] Interface texts listed for translation (buttons, labels, messages).

## Foundations

- [ ] Token table: name, light value, dark value, use ([From design tool variables to CSS custom properties](from-design-tool-variables-to-css-custom-properties.md#the-export-table)).
- [ ] Palette sheet with measured contrast ratios in both themes ([Color palettes and ramps](color-palettes-and-ramps.md#a-palette-sheet)).
- [ ] Type scale with line heights and measure ([Type scale](type-scale.md)).
- [ ] Spacing scale, inside and between rules, page layouts by intent ([Spacing and grid](spacing-and-grid.md)).
- [ ] Fonts: files, licenses, weights, fallback stack ([Fonts in a design](../designers/theme-design/fonts-in-a-design.md)).
- [ ] Motion tokens (durations, easing) ([Motion and interaction](motion-and-interaction.md)).

## Components

- [ ] Component inventory with variants, tokens, class names ([Component inventory](component-inventory.md)).
- [ ] State matrix per component, light and dark ([Designing interactive states](designing-interactive-states.md#the-state-matrix)).
- [ ] Icons as SVG files with names ([Icons in a design](../designers/theme-design/icons-in-a-design.md)).

## Pages and views

- [ ] Frame: header and footer at three widths, signed in and out, small-screen menu open
      ([Designing headers and footers](designing-headers-and-footers.md)).
- [ ] Navigation: sources, depth, current state ([Designing navigation and menus](designing-navigation-and-menus.md)).
- [ ] View matrix: every class against every view mode ([Designing view modes](designing-view-modes.md#the-view-matrix)).
- [ ] One mockup per "yes" in the matrix, with shortest and longest content.
- [ ] Lists and pagination ([Designing content listings and cards](designing-content-listings-and-cards.md)).
- [ ] Search with results and none ([Designing search results](designing-search-results.md)).
- [ ] Error pages, empty states ([Designing empty and error states](designing-empty-and-error-states.md)).
- [ ] Forms and account pages ([Designing forms](designing-forms.md), [Designing login and user pages](designing-login-and-user-pages.md)).
- [ ] Right-to-left versions of key screens, if needed ([Designing for many languages](designing-for-many-languages.md)).

## Images, blocks and behavior

- [ ] Image sheet: slot, alias, filter, ratio and crop, srcset partners, empty design
      ([Designing image crops and art direction](designing-image-crops-and-art-direction.md#the-image-sheet)).
- [ ] Share image and default share image ([Social share and metadata images](social-share-and-metadata-images.md)).
- [ ] Block design sheet per block and view type, if the site uses layouts
      ([Designing layout blocks](designing-layout-blocks.md#the-block-design-sheet)).
- [ ] Motion table with reduced-motion versions ([Motion and interaction](motion-and-interaction.md#specifying-motion-in-a-handoff)).
- [ ] Brand matrix, for multi-brand installations ([Designing for several brands in multisite](designing-for-several-brands-in-multisite.md#the-brand-matrix)).
- [ ] Newsletter skin design, if the site sends newsletters ([Designing newsletter emails](designing-newsletter-emails.md)).

## Quality targets

- [ ] Performance budget agreed ([Designing for performance budgets](designing-for-performance-budgets.md)).
- [ ] Accessibility target stated (contrast, keyboard, zoom).
- [ ] Widths and themes to test: 1440 px, 960 px at 200% zoom, 375 px; light and dark.
- [ ] The rules restated: no inline CSS or scripts, tokens only, dark values, no `?v=` on links
      ([Working with developers](working-with-developers.md#rules-both-sides-keep)).

## Where it all lives

All written items go into the theme's repository (or a folder it links to), so the developer, reviewers and the next
designer find them. Mockup files may live in the design tool; link each from the theme's documentation.

## Related pages

- [From mockup to theme](from-mockup-to-theme.md)
- [Design QA checklist](design-qa-checklist.md)
- [Checklist: the theme extension is ready](../designers/extensions/checklist-theme-extension-ready.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
