---
title: Fetch performance
audience: designer, developer
category: templates
order: 430
summary: Keeping templates fast when they read content: fetch once, always limit, filter in the fetch, avoid fetches in loops and children on large folders, prefer content_info, use counts for paging, cache expensive parts, and measure with the debug output.
keywords: fetch performance, slow templates, database queries, fetch in loop, limit, children, data_map, load_data_map, cache-block, debug output, sql count, n plus one
type: help
---
# Fetch performance

This page is for designers whose pages got slow. In templates, almost all cost comes from fetches and the attributes
that quietly fetch (`children`, `parent`, `data_map`). The rules below keep the number of database queries small.

## Contents

1. [The rules](#the-rules)
2. [Attributes that fetch](#attributes-that-fetch)
3. [The loop trap](#the-loop-trap)
4. [Measuring](#measuring)

## The rules

1. **Fetch once.** Store the result in a variable; pass it to parts as a parameter.
2. **Always a `limit`** on `list` and `tree`; paginate with `offset` and a `list_count`.
3. **Filter in the fetch** (`class_filter_array`, `attribute_filter`), not with `{if}` in the loop.
4. **Prefer what you already have.** In the pagelayout, `$module_result.content_info` holds the class, node id, depth
   and URL alias of the page without a fetch.
5. **Cache what is the same for many visitors**: menus and footers in cache-blocks ([Cache-block](cache-block.md)).
6. **Keep full views cacheable** so the view cache serves them ([Caching for designers](../theme-design/caching-for-designers.md)).

## Attributes that fetch

| Attribute | Cost |
|---|---|
| `$node.children` | all children, no limit, no filter |
| `$node.children_count` | a count query |
| `$node.parent`, `$node.path` | one or more node fetches |
| `$node.data_map` | the object's fields (loaded once per object) |
| `$node.object.owner`, `$node.creator` | an object fetch |

`$node.children` on a folder with thousands of items is the most common cause of a slow page: use a fetch with a
limit instead.

## The loop trap

```tpl
{* slow: one fetch per child *}
{foreach $children as $child}
    {def $sub = fetch( 'content', 'list', hash( 'parent_node_id', $child.node_id ) )}
    ...
{/foreach}
```

Ten children mean eleven queries, a hundred mean a hundred and one. Instead, fetch the descendants once with `tree` and
`depth` 2 and group them in the template, or show the sub-level only on the child's own page.

## Measuring

On a test siteaccess, enable the debug output for your address (`[DebugSettings] DebugOutput=enabled`,
`DebugByIP=enabled`, `DebugIPList[]`): it shows the time of the page and the database queries. Compare before and after a
change, with caches cleared and again warm.

## Related pages

- [Fetching nodes and lists](fetching-nodes-and-lists.md)
- [Theme performance](../theme-design/theme-performance.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
