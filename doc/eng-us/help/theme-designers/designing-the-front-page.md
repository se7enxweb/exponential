---
title: "Designing the front page"
audience: theme-designer
category: theme-design
order: 40
summary: How theme designers approach the front page of an Exponential site - the three ways a front page is built (a layout, a class of its own, an override of the root node), how to choose with the client, designing for content that changes daily, and the front page's role in the frame.
keywords: theme designer, front page, home page, landing page, layout, frontpage class, override, root node, editors, featured content, hero
type: help
---
# Designing the front page

This page is for theme designers who design a site's front page. It is the page clients care most about and the one
that changes most often, so how it is built decides what editors can change without you. Exponential offers three ways
to build it; the choice is a design decision as much as a technical one, and it should be made before the mockup is
final.

## Contents

1. [Three ways to build it](#three-ways-to-build-it)
2. [Choosing with the client](#choosing-with-the-client)
3. [Designing a front page built from blocks](#designing-a-front-page-built-from-blocks)
4. [Designing a fixed front page](#designing-a-fixed-front-page)
5. [Content that changes every day](#content-that-changes-every-day)
6. [The front page and the frame](#the-front-page-and-the-frame)
7. [Checks](#checks)

## Three ways to build it

| Way | How | Who changes the arrangement |
|---|---|---|
| A layout | the root page gets a layout from the layouts editor; editors place and order blocks | editors |
| A class of its own | a front page class with fields (hero, featured items, intro), drawn by its own full view template | the template; editors fill the fields |
| An override of the root node | the root node's full view gets a dedicated template, matched by remote id | the template; content comes from fetches |

The template-based ways are explained in [Override a single node](../designers/templates/override-a-single-node.md)
and its alternatives; the layouts way in [Tutorial: build a landing page with layouts](../designers/theme-design/tutorial-build-a-landing-page-with-layouts.md).

## Choosing with the client

| Question | Points toward |
|---|---|
| Will editors want to change the order or add sections themselves? | a layout |
| Is the front page one strict composition the brand depends on? | a class or an override |
| Will several pages (campaign pages, section fronts) share the same structure? | a layout, or a class used for all of them |
| Is the front page mostly automatic (latest news, upcoming events)? | an override with fetches, or list blocks with queries in a layout |

Most content sites end up with a layout: editors get freedom within the blocks and view types you designed. Write
down the choice and the reason; it decides what the client can do without a designer later.

## Designing a front page built from blocks

Design the front page as an **example arrangement** of your blocks, not as a fixed composition, and say so when you
present it. Then:

- check that each block in it also works in other positions and next to other blocks
  ([Designing layout blocks](designing-layout-blocks.md#any-order-any-width));
- design two or three alternative arrangements (a campaign week, a quiet week), to show the system holds;
- decide which blocks belong to a shared layout (often a pre-footer area) and appear on many pages
  ([Shared layouts and linked zones](../designers/theme-design/shared-layouts-and-linked-zones.md)).

## Designing a fixed front page

A fixed composition must still take changing content. Map each area to its source: a field of the front page object,
a fetch of the latest items of a class, or a relation list editors fill
([Mapping a design to content classes](mapping-a-design-to-content-classes.md)). Draw each area with zero, one and the
maximum number of items.

## Content that changes every day

- The hero image changes; design its slot for any picture editors pick, with a crop rule and text that stays readable
  on any image (text beside the image, or on a solid band, rather than on top of the photo)
  ([Designing image crops and art direction](designing-image-crops-and-art-direction.md)).
- Lists of the latest items vary in length of titles and presence of images ([Designing content listings and cards](designing-content-listings-and-cards.md)).
- Avoid areas that only look right with exactly three items.
- Front pages are cached; a developer decides how freshly automatic lists update
  ([Caching for designers](../designers/theme-design/caching-for-designers.md)).

## The front page and the frame

The front page sits inside the same pagelayout as every other page. If the front page needs a different header (for
example a transparent header over the hero), that is a variation of the frame for one page, which costs a condition
in the pagelayout and testing in both forms. Prefer a front page that works with the normal header
([Designing headers and footers](designing-headers-and-footers.md)).

## Checks

- [ ] The way of building it is chosen and written down.
- [ ] Each area mapped to a source; zero, one and many items drawn.
- [ ] Two alternative arrangements, if it is a layout.
- [ ] Hero readable with a dark and a light photo, in light and dark themes.
- [ ] The first screen meets the performance budget ([Designing for performance budgets](designing-for-performance-budgets.md#the-first-screen)).

## Related pages

- [Layouts for designers](../designers/theme-design/layouts-for-designers.md)
- [Layout rules](../designers/theme-design/layout-rules.md)
- [The layouts editor](../layouts-editor.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
