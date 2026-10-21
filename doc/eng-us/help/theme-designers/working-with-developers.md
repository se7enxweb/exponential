---
title: "Working with developers"
audience: theme-designer
category: theme-design
order: 32
summary: How theme designers and Exponential developers divide the work on a theme - who owns what, the shared vocabulary, working in the theme's Git repository, requests for fields and settings, reviewing templates against mockups, and the rules both sides keep.
keywords: theme designer, developers, collaboration, roles, handoff, git, pull request, review, vocabulary, requests, theme extension, teamwork
type: help
---
# Working with developers

This page is for theme designers who work with one or more Exponential developers on a theme. Some designers write
templates and stylesheets themselves; others hand over designs and review the result. Either way, a theme touches the
content model, settings and caches, which are the developer's area, so a clear split of work saves time.

## Contents

1. [Who owns what](#who-owns-what)
2. [Speak the same words](#speak-the-same-words)
3. [Work in the theme's repository](#work-in-the-themes-repository)
4. [Requests to the developer](#requests-to-the-developer)
5. [Reviewing templates against mockups](#reviewing-templates-against-mockups)
6. [Rules both sides keep](#rules-both-sides-keep)
7. [When you disagree](#when-you-disagree)

## Who owns what

| Area | Usually owned by | The other side |
|---|---|---|
| Tokens, components, mockups, state matrix | designer | reviews feasibility |
| Stylesheets | designer or front-end developer | reviews packing and loading |
| Templates (TPL) | front-end developer, or designer who knows TPL | reviews against mockups |
| Content classes and fields | developer | designer requests fields ([Mapping a design to content classes](mapping-a-design-to-content-classes.md)) |
| Overrides, settings, siteaccesses | developer | designer knows which exist |
| Image aliases | developer writes them | designer specifies them ([Designing image crops and art direction](designing-image-crops-and-art-direction.md)) |
| Caches, deploys | developer | designer knows when a change needs one ([Caching for designers](../designers/theme-design/caching-for-designers.md)) |
| Layout block types | developer | designer specifies view types ([Designing layout blocks](designing-layout-blocks.md)) |

Write this table for your project in the first week; it ends most "I thought you were doing that" moments.

## Speak the same words

Use the product's words in mockups, tickets and file names: class, field (attribute), node, view mode, override,
pagelayout, siteaccess, design, block, view type, zone, token. Call the card a card everywhere: in the design tool,
the [component inventory](component-inventory.md), the template part and the CSS class. The
[Theme designer glossary](theme-designer-glossary.md) and the [Design glossary](../designers/theme-design/design-glossary.md)
define the terms.

## Work in the theme's repository

The theme is a design extension under Git ([Putting a theme under Git](../designers/extensions/putting-a-theme-under-git.md)).
Keep design artifacts that the code depends on next to the code:

- the token export table and the palette sheet;
- the component inventory;
- the mapping sheet and the view matrix;
- the image sheet and the block design sheet.

If you change stylesheets yourself, work on a branch, keep each change small, and describe what changed and why.
Large binary design files usually live elsewhere; link to them from the theme's documentation rather than committing
them. Never commit settings overrides, credentials or customer data.

## Requests to the developer

A good request is short and complete:

- **What:** "A short title field on Event, shown on cards."
- **Why:** "Titles longer than 60 characters wrap to four lines in the card grid."
- **Details:** datatype, required or optional, which views, empty design.
- **Mockup:** a link to the frame.

Requests that change the content model change editors' work too; they are decided with the client or editors, not
only between designer and developer.

## Reviewing templates against mockups

When templates are written by someone else, review each one on the development or preview siteaccess, not in
screenshots:

1. Open the page with template path comments on, so you can name the template in your comments
   ([Template path comments](../designers/templates/template-path-comments.md)).
2. Check it against the mockup at three widths, light and dark, with real and stress content.
3. Check keyboard focus and states.
4. Write each issue with the page address, the width, the template and the expected result.

The [Theme design review checklist](theme-design-review-checklist.md) lists what to look at.

## Rules both sides keep

These come from the project's design rules and are not negotiable in review:

- no `<style>` blocks and no `style=""` attributes in templates; no inline scripts or `on*=` handlers
  ([No inline CSS](../designers/theme-design/no-inline-css.md), [No inline JavaScript](../designers/theme-design/no-inline-javascript.md));
- colors, spacing, radius and fonts from tokens, each color with a dark value ([Design tokens](../designers/theme-design/design-tokens.md));
- every printed value washed, every interface text translatable ([Wash and escaping](../designers/templates/wash-and-escaping.md));
- shipped designs are never edited, only overridden in your own extension;
- no `?v=` version queries on asset links;
- tested at 1440 px, at 960 px with 200% zoom and at 375 px, in light and dark, with the keyboard.

A design that cannot be built within these rules needs to change, not the rules.

## When you disagree

Disagreements are usually about cost. Ask the developer to say what a design choice costs (templates, caches,
performance, maintenance), and offer alternatives with the same intent: a view type instead of a new block, a CSS crop
instead of a new image field. Decide with the client when the trade-off is theirs.

## Related pages

- [From mockup to theme](from-mockup-to-theme.md)
- [Handoff checklist](handoff-checklist.md)
- [Checklist: code review for templates](../designers/theme-design/checklist-code-review-for-templates.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
