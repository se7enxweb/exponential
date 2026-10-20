---
title: Pagination
audience: designer, developer
category: templates
order: 690
summary: Paging long lists: the offset view parameter, a fetch with offset and limit plus a count, the shipped navigator templates (navigator/google.tpl and others) and their parameters, an accessible pager of your own, and how paging interacts with the view cache.
keywords: pagination, paging, pager, offset, view_parameters, navigator, google.tpl, item_count, item_limit, page_uri, page_uri_suffix, list paging, accessible pagination
type: help
---
# Pagination

This page is for designers who split long lists into pages: folder contents, search results, archives.

## Contents

1. [The offset view parameter](#the-offset-view-parameter)
2. [Fetch a page](#fetch-a-page)
3. [The shipped navigator](#the-shipped-navigator)
4. [A pager of your own](#a-pager-of-your-own)
5. [Caching](#caching)

## The offset view parameter

Page 2 of a folder with 10 items per page is `/news/(offset)/10`. The `(name)/value` parts of a URL arrive in view
templates as `$view_parameters` (`$view_parameters.offset` here).

## Fetch a page

```tpl
{def $limit = 10
     $offset = first_set( $view_parameters.offset, 0 )|int
     $items = fetch( 'content', 'list', hash( 'parent_node_id', $node.node_id, 'sort_by', $node.sort_array,
                                              'offset', $offset, 'limit', $limit ) )
     $total = fetch( 'content', 'list_count', hash( 'parent_node_id', $node.node_id ) )}
```

Use the same filters in `list` and `list_count`, or the page numbers do not match the items.

## The shipped navigator

`design/standard/templates/navigator/` has `google.tpl` (page numbers with previous and next), `simple.tpl`,
`alphabetical.tpl` and `monthview.tpl`. The kernel's search template includes it like this:

```tpl
{include name=Navigator
         uri='design:navigator/google.tpl'
         page_uri='/content/search'
         page_uri_suffix=concat( '?SearchText=', $search_text|urlencode )
         item_count=$search_count
         view_parameters=$view_parameters
         item_limit=$page_limit}
```

For a folder: `page_uri=$node.url_alias`, `item_count=$total`, `item_limit=$limit`. `left_max` and `right_max` limit how
many page numbers show around the current one (defaults 7 and 6). Copy `navigator/google.tpl` into your design to change
its markup.

## A pager of your own

Markup that works for everyone:

```tpl
{def $pages = ceil( div( $total, $limit ) ) $current = sum( div( $offset, $limit )|floor, 1 )}
{if gt( $pages, 1 )}
<nav class="pager" aria-label="{'Pages'|i18n( 'design/mytheme' )}">
    <ul>
    {for 1 to $pages as $p}
        <li>{if eq( $p, $current )}<a aria-current="page">{$p}</a>{else}<a href={concat( $node.url_alias, '/(offset)/', mul( $p|dec, $limit ) )|ezurl}>{$p}</a>{/if}</li>
    {/for}
    </ul>
</nav>
{/if}
```

Give the previous and next links visible text ("Previous", "Next"), not only arrows.

## Caching

The offset is part of the view cache key, so each page of a list is cached separately and cleared when the folder's
content changes.

## Related pages

- [Fetching nodes and lists](fetching-nodes-and-lists.md)
- [Full view](full-view.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
