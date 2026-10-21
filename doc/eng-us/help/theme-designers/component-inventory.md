---
title: "Component inventory"
audience: theme-designer
category: theme-design
order: 11
summary: How theme designers keep an inventory of the components of an Exponential theme - what counts as a component, a template for each entry with states, tokens and template, how the inventory maps to templates and CSS, and how to keep it current.
keywords: theme designer, component inventory, components, design system, pattern library, states, tokens, BEM, class names, templates, include
type: help
---
# Component inventory

This page is for theme designers who want one agreed list of every reusable piece of their theme. A component
inventory is a table, not a tool: one row per component, with its states, the tokens it uses, the template that draws
it and the CSS class that styles it. It is the contract between design and code, and it is what reviewers check
against. The administration has its own shared components ([admin4 components](../designers/theme-design/admin4-components.md)),
which you reuse and never copy; this page is about the components of a public theme.

## Contents

1. [What counts as a component](#what-counts-as-a-component)
2. [One entry](#one-entry)
3. [A starter inventory](#a-starter-inventory)
4. [From inventory to code](#from-inventory-to-code)
5. [Naming](#naming)
6. [Keeping it current](#keeping-it-current)

## What counts as a component

Something is a component when it appears in more than one template, or is likely to. A card that appears in a folder
list, a related list and a layout block is a component. A headline style used only in the article full view is not;
it belongs to that template.

## One entry

| Field | Example |
|---|---|
| Name | Card |
| Purpose | One content item in a grid |
| Variants | with image, without image, featured (wide) |
| States | default, hover, focus-within, visited title |
| Tokens | `--site-card`, `--site-line`, `--site-radius`, `--site-space-3`, `--site-space-4`, `--site-text-l` |
| Markup owner | `design/mytheme/templates/parts/card.tpl` |
| CSS class | `.mytheme-card`, `.mytheme-card--featured` |
| Used in | folder full view, related list, layout block list view type `mytheme_cards` |
| Accessibility notes | one link on the title, stretched; heading level passed in |
| Dark mode | checked |
| Right to left | checked |

## A starter inventory

Most content sites need about twenty components. Use this list as a start and remove what the site does not use:

| Group | Components |
|---|---|
| Actions | button (primary, secondary, quiet), text link, icon button |
| Content | card, line item, byline, date, tag or badge, figure with caption, quote, table |
| Navigation | main menu, sub menu, breadcrumbs, pagination, skip link, language switcher |
| Forms | text field, text area, select, checkbox, radio, field error, form summary |
| Feedback | alert (info, success, warning, error), empty state |
| Frame | header, footer, search box |

Each component has the interactive states from [Designing interactive states](designing-interactive-states.md).

## From inventory to code

- **Markup.** Components used by several templates live in one template part, included where needed
  ([Include](../designers/templates/include.md)): `{include uri='design:parts/card.tpl' node=$child heading_level=3}`.
  One component, one template part, so markup changes in one place.
- **Styles.** Components go in one components stylesheet of the design, listed in the design's stylesheet settings and
  packed ([Loading stylesheets](../designers/theme-design/loading-stylesheets.md)); never a `<style>` block or a
  `style=""` attribute ([No inline CSS](../designers/theme-design/no-inline-css.md)).
- **Values.** Components use tokens only ([Design tokens](../designers/theme-design/design-tokens.md)). A component
  that needs a value with no token is a signal to add a token, with its dark value, not to write the value in.
- **Variants.** A variant is a modifier class, never an inline style or a per-page stylesheet.

## Naming

- One prefix for the whole theme (`mytheme-`), so your classes never collide with the kernel's, an extension's or
  another theme's.
- Name by role: `mytheme-card`, `mytheme-alert--error`, not `mytheme-blue-box`.
- The same name in the inventory, the design tool, the template part and the CSS class. If the design tool calls it
  "Teaser" and the CSS calls it "card", someone will build it twice.

## Keeping it current

- The inventory lives in the theme's repository next to the code, as a Markdown table or a page in the theme's
  documentation ([Writing the README of a theme](../designers/extensions/readme-for-a-theme.md)).
- A change to a component changes the inventory in the same commit.
- Review it once per release: remove components no template uses, merge near-duplicates.
- A [style guide page](building-a-style-guide-page.md) on a development site shows every entry live, which is the
  easiest way to see what drifted.

## Related pages

- [Building a style guide page](building-a-style-guide-page.md)
- [From mockup to theme](from-mockup-to-theme.md)
- [Template style guide](../designers/templates/template-style-guide.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
