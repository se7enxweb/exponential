---
title: "Designing search results"
audience: theme-designer
category: theme-design
order: 20
summary: How theme designers design the search page of an Exponential site - the form, the result count, one result per class through view modes, no results, long queries, pagination, the advanced search page, and filters when the site uses ezfind with Solr.
keywords: theme designer, search, search results, search form, no results, result count, line view, advanced search, ezfind, solr, facets, filters
type: help
---
# Designing search results

This page is for theme designers who design the search page. Search is a module view, so its page is drawn inside your
frame by a search template rather than by a content template. Results are content objects of many classes, drawn one
by one in a view mode. The template, its variables and the form fields that must stay are documented in
[Search results template](../designers/templates/search-results-template.md); this page covers the design decisions.

## Contents

1. [The pieces of the page](#the-pieces-of-the-page)
2. [The form](#the-form)
3. [One result, many classes](#one-result-many-classes)
4. [Counts and feedback](#counts-and-feedback)
5. [No results](#no-results)
6. [Pagination](#pagination)
7. [Advanced search](#advanced-search)
8. [Filters with ezfind](#filters-with-ezfind)
9. [Checks](#checks)

## The pieces of the page

| Piece | Notes |
|---|---|
| Heading | "Search", or "Results for ..." |
| Form | the query, kept in the field after searching |
| Result count | announced to screen readers when it changes |
| Results | an ordered list, one view mode per hit |
| Pagination | with the query kept in the page links |
| No results | help, not a dead end |

The kernel's search page is `content/search.tpl`, which your design overrides; the advanced search page is
`content/advancedsearch.tpl`.

## The form

The search field may also live in the header ([Designing headers and footers](designing-headers-and-footers.md)).
On the search page, show the full form with a visible label, the query already in the field, and a button with a
text label. The form's field names (`SearchText` and others) must stay as the module expects them; the design changes
markup and styles only.

## One result, many classes

Each hit is drawn with a view mode (usually `line`), so an article, a file and an event can each look like
themselves. Design a shared skeleton so the list still reads as one list
([Designing content listings and cards](designing-content-listings-and-cards.md#mixed-classes-in-one-list)):

- the title as a link;
- a type label (article, PDF, event) where it helps people choose;
- a date where it matters;
- a short excerpt, or the intro.

If the search line should differ from the folder line, a separate view mode for search hits is cleaner than
conditions inside `line` ([Designing view modes](designing-view-modes.md#adding-a-mode-of-your-own)).

## Counts and feedback

Show how many results were found and for what query: "24 results for 'opening hours'". The query is visitor input:
the template must wash it wherever it is printed. Long queries must wrap, not overflow the heading.

## No results

The empty result is the most common search page on many sites. Design it as help:

- repeat the query and say clearly that nothing was found;
- suggest checking the spelling, using fewer words, or browsing from the main sections;
- offer the search field again, with the query in it;
- optionally link to the front page and the most visited sections.

See also [Designing empty and error states](designing-empty-and-error-states.md).

## Pagination

Search results are paged with the shipped navigator templates; the query travels in the page links. Design the
pagination like other lists ([Designing content listings and cards](designing-content-listings-and-cards.md#pagination)),
and show "Page 2 of 5" so people know where they are.

## Advanced search

`content/advancedsearch.tpl` offers more fields (for example phrase and class). Many sites do not link it at all;
decide with the client whether it is needed before designing it.

## Filters with ezfind

When an installation uses the ezfind extension with a Solr server, search is answered by Solr and the ezfind search
template can show **facets**, for example the number of results per content class. Design filters only after the
developer confirms which search engine the site uses and which facets it will offer; the default kernel search has
none ([Search: legacy and Solr](../dxp-developers/search-legacy-and-solr.md) covers the DXP side,
[Searching](../searching.md) the visitor's view).

Filter design notes:

- show the count per option, and the active filters with a way to remove each;
- a filter that would give zero results is better hidden or disabled with an explanation;
- on small screens, filters go into a panel opened by a button that shows how many are active.

## Checks

- [ ] Results, no results, one result and many results drawn.
- [ ] Results of every class that can be found look like one list.
- [ ] Long queries wrap; the query is shown in the field.
- [ ] The count is announced (`role="status"`) and headings make sense to screen readers.
- [ ] Light and dark, three widths, keyboard only.

## Related pages

- [Search results template](../designers/templates/search-results-template.md)
- [Line view](../designers/templates/line-view.md)
- [Pagination](../designers/templates/pagination.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
