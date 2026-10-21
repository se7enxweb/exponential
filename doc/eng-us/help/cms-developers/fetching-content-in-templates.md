---
title: Fetching content in templates
audience: cms-developer
category: cms-development
order: 25
summary: For CMS developers, the fetch functions that replace hand-written queries. It covers single nodes and objects, children and subtrees, class and attribute filters, sorting, paging with the offset view parameter, counts, relations and search, plus the rules that keep fetches fast and cache-friendly.
keywords: fetch, fetch functions, content list, content tree, list_count, tree_count, node, object, class_filter_type, class_filter_array, attribute_filter, sort_by, limit, offset, depth, main_node_only, pagination, navigator, view_parameters, performance
type: help
---
# Fetching content in templates

This page is for developers who need lists and lookups in templates: the latest news, the events of next month, the
children of a folder, related items. Templates do not run SQL; they call **fetch functions** that the modules
define, and every fetch checks permissions, visibility and languages for you.

## Contents

1. [The call](#the-call)
2. [The fetches you use most](#the-fetches-you-use-most)
3. [Children and subtrees](#children-and-subtrees)
4. [Filtering by class and by attribute](#filtering-by-class-and-by-attribute)
5. [Sorting](#sorting)
6. [Paging](#paging)
7. [Rules for fast fetches](#rules-for-fast-fetches)
8. [When a fetch is not enough](#when-a-fetch-is-not-enough)

## The call

```smarty
{def $result = fetch( '<module>', '<function>', hash( '<parameter>', <value>, ... ) )}
```

The functions and their parameters are declared in each module's `function_definition.php`, for content in
`kernel/content/function_definition.php`. Single results can be `false` (missing, or not readable by this user), so
test them: `{if $node}...{/if}` ([Fetch basics](../designers/templates/fetch-basics.md)).

## The fetches you use most

| Call | Returns |
|---|---|
| `fetch( 'content', 'node', hash( 'node_id', 2 ) )` | one node (also by `node_path` or `remote_id`) |
| `fetch( 'content', 'object', hash( 'object_id', 57 ) )` | one object |
| `fetch( 'content', 'list', hash( ... ) )`, `list_count` | children of a node, and how many |
| `fetch( 'content', 'tree', hash( ... ) )`, `tree_count` | all descendants, with depth limits |
| `fetch( 'content', 'related_objects', hash( ... ) )`, `reverse_related_objects` | relations ([Relations and embeds](relations-and-embeds.md)) |
| `fetch( 'content', 'keyword', hash( ... ) )` | objects by keyword |
| `fetch( 'content', 'search', hash( 'text', ... ) )` | search results ([Search](search.md)) |
| `fetch( 'user', 'current_user' )` | the signed-in user |
| `fetch( 'explayouts', 'resolve_layout', hash() )` | the layout of the current page ([Layouts for CMS developers](layouts-for-cms-developers.md)) |

All content fetch functions with every parameter:
[Fetch functions reference](../extension-development/part-8-reference/fetch-functions-reference.md).

## Children and subtrees

The `list` and `tree` parameters (`kernel/content/function_definition.php`): `parent_node_id`, `sort_by`, `offset`,
`limit`, `depth`, `depth_operator`, `class_filter_type`, `class_filter_array`, `attribute_filter`,
`extended_attribute_filter`, `main_node_only`, `ignore_visibility`, `language`, `only_translated`, `as_object`,
`load_data_map`, `objectname_filter`, `group_by` and `limitation`.

```smarty
{def $news = fetch( 'content', 'list', hash(
        'parent_node_id', $node.node_id,
        'class_filter_type', 'include',
        'class_filter_array', array( 'article' ),
        'sort_by', array( 'published', false() ),
        'limit', 5 ) )}
{foreach $news as $item}
    {node_view_gui content_node=$item view='line'}
{/foreach}
{undef $news}
```

`list` returns direct children; `tree` goes down the subtree (limit it with `depth`). `main_node_only` avoids an object
appearing twice when it has two locations below the same parent.

## Filtering by class and by attribute

`class_filter_type` is `include` or `exclude`, with `class_filter_array` of class identifiers. `attribute_filter` is a
list of conditions `array( <field>, <operator>, <value> )`, optionally starting with `'and'` or `'or'`; a field is
`<class>/<attribute>` or a node property such as `published`, `modified`, `section` or `name`:

```smarty
{def $upcoming = fetch( 'content', 'tree', hash(
        'parent_node_id', $node.node_id,
        'class_filter_type', 'include',
        'class_filter_array', array( 'exp_event' ),
        'attribute_filter', array( array( 'exp_event/start_date', '>=', currentdate() ) ),
        'sort_by', array( 'attribute', true(), 'exp_event/start_date' ),
        'limit', 10 ) )}
```

Operators and examples: [Fetch filters and sorting](../designers/templates/fetch-filters-and-sorting.md#attribute-filters).
A template that compares with `currentdate()` and is view-cached keeps the time of caching; give such a page a short
cache life ([Caching for CMS developers](caching-for-cms-developers.md)).

## Sorting

`sort_by` is `array( <field>, <ascending> )`, or a list of such pairs; fields include `published`, `modified`,
`name`, `priority`, `path`, `class_identifier` and `attribute` (with the attribute as third element). To follow the
order editors chose for a folder, pass the node's own order: `'sort_by', $node.sort_array`.

## Paging

The offset of a list comes from the view parameter `(offset)`:

```smarty
{def $limit = 10
     $total = fetch( 'content', 'list_count', hash( 'parent_node_id', $node.node_id ) )
     $page = fetch( 'content', 'list', hash( 'parent_node_id', $node.node_id,
                                              'offset', $view_parameters.offset,
                                              'limit', $limit,
                                              'sort_by', $node.sort_array ) )}
{foreach $page as $item}{node_view_gui content_node=$item view='line'}{/foreach}
{include name=navigator uri='design:navigator/google.tpl'
         page_uri=$node.url_alias item_count=$total
         view_parameters=$view_parameters item_limit=$limit}
```

The shipped navigators are in `design/standard/templates/navigator/` ([Pagination](../designers/templates/pagination.md)).

## Rules for fast fetches

- Always pass a `limit` on lists that can grow.
- Filter by class in the fetch, not with `{if}` in the loop.
- Avoid fetches inside loops over fetch results; fetch once, or use relations
  ([The loop trap](../designers/templates/fetch-performance.md#the-loop-trap)).
- Use `list_count` for counts instead of counting a full list.
- Put fetches that are the same on every page (menus, footers) in a cache block
  ([Cache block](../designers/templates/cache-block.md)).
- Attribute filters on large trees are SQL joins; measure with the debug output's SQL list on a copy with real data.

## When a fetch is not enough

When no fetch function answers your question, write one in your extension: a PHP method that returns data, declared
in your module's `function_definition.php`, callable from every template
([Fetch functions](../extension-development/part-3-extension-developer/fetch-functions.md),
[A fetch function by hand](../extension-development/part-3-extension-developer/tutorials/by-hand-fetch-function.md)).
For complex filters, `extended_attribute_filter` lets a PHP class add SQL conditions; see `settings/extendedattributefilter.ini`.

## Related pages

- [Content from PHP](content-from-php.md): the same queries in PHP
- [Fetch for builders](../extension-development/part-2-builder/fetch-for-builders.md)
- [Module 5: Fetch functions](../trainers/developers/module-05-fetch-functions/README.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
