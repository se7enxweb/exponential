---
title: "Story: Exponential Layouts, the page builder"
audience: press
category: press-media
order: 53
summary: A feature story about Exponential Layouts, the visual page builder that brings the concept of the upstream layouts packages to the Exponential kernel - layouts, zones, blocks, rules and shared layouts - with printable facts, credit to Netgen, what not to claim and headline ideas.
keywords: story, Exponential Layouts, explayouts, page builder, layouts, zones, blocks, rules, shared layouts, press
type: help
---
# Story: Exponential Layouts, the page builder

This page is for journalists and creators who want to write about how editors build pages in Exponential without
template work. It gives the story, the dates, facts with sources, the credit you owe Netgen, and the claims to avoid.

## Contents

1. [The story in one paragraph](#the-story-in-one-paragraph)
2. [What is new, and when](#what-is-new-and-when)
3. [Facts you can print](#facts-you-can-print)
4. [Credit where it is due](#credit-where-it-is-due)
5. [What not to claim](#what-not-to-claim)
6. [Angles and headline ideas](#angles-and-headline-ideas)
7. [Who to ask](#who-to-ask)
8. [Related pages](#related-pages)

## The story in one paragraph

"Exponential Layouts is a visual page builder for Exponential. It brings the concept of the upstream layouts packages to the
Exponential kernel: a **layout** is a saved arrangement of **blocks** in **zones**, and a **rule** decides which pages
get it, so editors rearrange pages without touching templates" ([README, Exponential Layouts](https://github.com/se7enxweb/exponential/blob/main/README.md#exponential-layouts-revolutionary)).
Layouts have drafts like content does, a header or footer can be built once in a shared layout and linked from every
other layout, and layouts move between installations by export and import. The suite is required by the kernel's
`composer.json` in the 6.0.15 development line, so every new installation has it
([Exponential Layouts](../who-is-the-exponential-foundation.md#exponential-layouts)).

## What is new, and when

| What | When | Source |
|---|---|---|
| The repository `explayouts` is created | 31 July 2026 | [Who is 7x?, timeline](../who-is-7x.md#nearly-thirty-years-the-timeline) |
| A Layouts tab in the admin node view | July and August 2026 | [6.0.15 changelog](../../../changelogs/6.0/6.0.15.md#july-and-august-2026) |
| Copy and duplicate blocks with their collections; the Layouts tab and breadcrumbs | 10 to 12 October 2026 | [6.0.15 changelog](../../../changelogs/6.0/6.0.15.md#10-to-12-october-2026-exponential-layouts) |
| Export and import of layouts | 17 October 2026 | [6.0.15 changelog](../../../changelogs/6.0/6.0.15.md#17-october-2026-export-and-import-of-layouts) |
| The layout editor page meets WCAG 2 AA (as the changelog states it) | 17 October 2026 | [6.0.15 changelog](../../../changelogs/6.0/6.0.15.md#17-october-2026-the-layout-editor-page-meets-wcag-2-aa) |

The 6.0.15 line is in development and not released.

## Facts you can print

| Fact | Source |
|---|---|
| A layout is a saved arrangement of blocks in zones; a rule decides which pages get it. | [README](https://github.com/se7enxweb/exponential/blob/main/README.md#exponential-layouts-revolutionary) |
| Layouts are stored as drafts and published versions, "so unfinished work is never visible to visitors". | same |
| Block types include text, title, HTML, Markdown, image, button, card, list, grid, gallery, carousel, tabs, accordion, map and video. | same |
| Rules map layouts by node, subtree or path, with conditions such as siteaccess, content class, query parameter and time. | same |
| A header, footer or prefooter can be built once in a shared layout and linked from every page layout. | same |
| Editors drag blocks into zones and configure each block. | [The layouts editor](../layouts-editor.md) |
| The suite is about a dozen Composer packages, `se7enxweb/explayouts` and the `explayouts-*` family. | [Who is 7x?, extension ecosystem](../who-is-7x.md#the-extension-ecosystem) |
| Exponential Platform Nexus ships a layouts editor on the Symfony side; Exponential Platform DXP does not. | [Knowing the product family](../advocates/knowing-the-product-family.md#common-mix-ups) |

## Credit where it is due

Exponential Layouts ports the **concept** of the upstream layouts packages to the Exponential kernel. Credit the upstream packages when you describe it:
"It brings the concept of the upstream layouts packages to Exponential." Do not call the product "Netgen Layouts for Exponential"; it has
its own name ([Talking points: Exponential Layouts](../advocates/talking-points-exponential-layouts.md#credit-where-it-is-due)).
Netgen is a separate company; the sources do not describe any endorsement by Netgen, so do not imply one.

## What not to claim

- "Editors never need a developer again." Developers still build designs and block templates.
- "It works the same on every distribution." DXP ships no layouts editor.
- "Revolutionary" as your own judgment: it is the README's own heading. Quote it as theirs if you use it.
- "Anyone can change any page." Roles decide who may edit layouts.

## Angles and headline ideas

- Editors take over the page: arranging without code.
- Build once, used everywhere: shared headers and footers.
- Open source credit: how one project ports another's concept and names it.

```text
"Drag, drop, publish: Exponential Layouts gives editors the page"
"One footer, every page: shared layouts in Exponential"
"From Netgen's concept to Exponential's kernel: a page builder with credits"
```

## Who to ask

| Question | Ask | Where |
|---|---|---|
| A demonstration, the roadmap of the suite | 7x | info@se7enx.com, or <https://exponential.earth/about/contact-us> |
| Questions about Netgen Layouts itself | Netgen, not 7x | their own channels |
| How sites use it | the community, in public | <https://share.exponential.earth/forums> |

## Related pages

- [Talking points: Exponential Layouts](../advocates/talking-points-exponential-layouts.md)
- [The layouts editor](../layouts-editor.md)
- [Story: editing content](story-editing-content.md)
- [Feature stories: index](feature-stories-index.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
