---
title: "Designing content listings and cards"
audience: theme-designer
category: theme-design
order: 10
summary: How theme designers design lists, grids and cards for Exponential content - folder lists, mixed classes in one list, sort order set by editors, pagination, cards with and without images, and the accessibility of whole-card links.
keywords: theme designer, listing, list view, grid, cards, teaser, folder, pagination, sorting, mixed classes, card link, line view
type: help
---
# Designing content listings and cards

This page is for theme designers who design the lists on a site: the children of a folder, news archives, related
items, grids of cards. Lists are where most visitors find content, and where a design meets the largest variety of
objects. The template side is in [Fetching nodes and lists](../designers/templates/fetching-nodes-and-lists.md),
[Line view](../designers/templates/line-view.md) and [Pagination](../designers/templates/pagination.md).

## Contents

1. [Where lists come from](#where-lists-come-from)
2. [Mixed classes in one list](#mixed-classes-in-one-list)
3. [Order is the editors' choice](#order-is-the-editors-choice)
4. [Designing the card](#designing-the-card)
5. [Making a whole card clickable](#making-a-whole-card-clickable)
6. [Pagination](#pagination)
7. [Grids that keep their shape](#grids-that-keep-their-shape)
8. [A listing checklist](#a-listing-checklist)

## Where lists come from

| List | Typical source | Design note |
|---|---|---|
| Children of a folder | the folder's full view fetches its children | can hold any class editors may create there |
| A news archive | a fetch filtered by class, sorted by date | dates matter; group by month or year? |
| Related content | relation fields of the object | usually short: 1 to 5 items |
| A layout block list | a block's collection, picked by hand or by a query | editors choose the view type ([Designing layout blocks](designing-layout-blocks.md)) |
| Search results | the search module | see [Designing search results](designing-search-results.md) |

## Mixed classes in one list

A folder can contain articles, files, links, other folders and images at once. Each item is drawn in its class's
line (or card) template, so a list mixes shapes. Design the line or card of every class that can appear in that
folder, and make them share a skeleton: the same title style, the same metadata row, the same spacing. A visitor
should see one list, not five.

## Order is the editors' choice

Every node stores a sort order for its children (for example by publication date, by name, or manually by priority),
which editors set in the administration. Folder templates usually follow it. Do not design a list that only makes
sense in one order, such as a timeline, unless the template fixes that order and the editors know.

## Designing the card

Draw the card in at least four states: with image and long title, with image and short title, without image, and
with every optional field empty. Decide:

- the **image ratio**, and whether images are cut to it ([Designing image crops and art direction](designing-image-crops-and-art-direction.md));
- what carries the **link**: the title, at least;
- what happens on **hover and focus** ([Designing interactive states](designing-interactive-states.md));
- the **heading level** of the title, which depends on where the list sits on the page
  ([Landmarks and headings](../designers/theme-design/landmarks-and-headings.md)).

Cards use the tokens for surface, line, radius and space; do not give cards a color of their own outside the palette
([Design tokens](../designers/theme-design/design-tokens.md)).

## Making a whole card clickable

A common request is "the whole card is a link". Wrapping the whole card in one `<a>` makes screen readers read the
image, title, text and date as one long link. The accessible pattern keeps one real link on the title and stretches
its clickable area over the card with CSS:

```css
/* design/mytheme/stylesheets/components.css */
.mytheme-card { position: relative; }
.mytheme-card__link::after { content: ""; position: absolute; inset: 0; }
.mytheme-card__link:focus-visible { outline: none; }
.mytheme-card:focus-within { outline: 2px solid var(--site-focus); outline-offset: 2px; }
```

Other links inside the card (a tag, an author) then need `position: relative` and a stacking order above the
stretched area. Show this in the mockup, so the developer knows which elements stay separately clickable.

## Pagination

Long lists are split into pages with the shipped navigator templates (`navigator/google.tpl` and others in
`design/standard/templates/navigator/`), which a design may override. Design:

- the current page, previous and next, and the first and last pages;
- the state on page one (no "previous") and on the last page;
- the small-screen version, usually previous, "page 3 of 12", next.

## Grids that keep their shape

- Prefer CSS grid with `repeat( auto-fill, minmax( <min>, 1fr ) )` over fixed column counts, so the grid adapts to the
  container, including narrow layout zones ([Breakpoints and container widths](../designers/theme-design/breakpoints-and-container-widths.md)).
- A grid with one item should not look like a broken grid of three; decide whether a single card spans the row.
- Equal-height cards come from the grid, not from fixed heights.

## A listing checklist

- [ ] A line or card design for every class that can appear in the list.
- [ ] Cards with and without image, long and short title.
- [ ] Focus visible on every card; one link per card for screen readers.
- [ ] Pagination at first, middle and last page; on small screens.
- [ ] The empty list ([Designing empty and error states](designing-empty-and-error-states.md)).
- [ ] Light and dark; 200% zoom; right to left if the site has such languages.

## Related pages

- [Designing view modes](designing-view-modes.md)
- [Fetch filters and sorting](../designers/templates/fetch-filters-and-sorting.md)
- [Styling layout blocks](../designers/theme-design/styling-layout-blocks.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
