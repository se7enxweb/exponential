---
title: Fetching nodes and lists
audience: designer, developer
category: templates
order: 410
summary: The content fetches for pages: node by id, path or remote id; list and tree with their parameters (parent_node_id, sort_by, offset, limit, depth, class filters, attribute filters, main_node_only, ignore_visibility, load_data_map); counts for paging; worked examples.
keywords: fetch content node, fetch content list, fetch content tree, list_count, tree_count, parent_node_id, offset, limit, depth, class_filter_type, class_filter_array, main_node_only, ignore_visibility, load_data_map, remote_id
type: help
---
# Fetching nodes and lists

This page is for designers who fetch pages and lists of pages. All parameters below are from
`kernel/content/function_definition.php`.

## Contents

1. [One node](#one-node)
2. [Children: list](#children-list)
3. [Descendants: tree](#descendants-tree)
4. [Counts for paging](#counts-for-paging)
5. [Examples](#examples)

## One node

```tpl
{def $home = fetch( 'content', 'node', hash( 'node_id', 2 ) )}
{def $about = fetch( 'content', 'node', hash( 'node_path', 'about-us' ) )}
{def $info = fetch( 'content', 'node', hash( 'remote_id', 'mytheme-site-info' ) )}
```

Parameters: `node_id`, `node_path`, `remote_id`, `language_code`. The result is `false` when the node does not exist or
may not be read.

## Children: list

```tpl
{def $children = fetch( 'content', 'list', hash( 'parent_node_id', $node.node_id,
                                                 'sort_by', $node.sort_array,
                                                 'class_filter_type', 'include',
                                                 'class_filter_array', array( 'article', 'blog_post' ),
                                                 'offset', $offset,
                                                 'limit', 10 ) )}
```

| Parameter | Meaning |
|---|---|
| `parent_node_id` | whose children (required) |
| `sort_by` | sort fields ([Fetch filters and sorting](fetch-filters-and-sorting.md)); `$node.sort_array` uses the order the editor set |
| `offset`, `limit` | paging |
| `class_filter_type`, `class_filter_array` | `include` or `exclude` these class identifiers |
| `attribute_filter`, `extended_attribute_filter` | filter on field values or custom joins |
| `depth`, `depth_operator` | for `tree`: how deep |
| `main_node_only` | only the main location of each object |
| `ignore_visibility` | include hidden nodes (avoid in public designs) |
| `only_translated`, `language` | language handling |
| `objectname_filter` | names starting with a letter (A to Z lists) |
| `group_by`, `limitation`, `as_object`, `load_data_map` | advanced: grouping, permission override, plain arrays, whether field data is loaded at once |

## Descendants: tree

The same parameters, for every level below the parent; with `depth` to limit it:

```tpl
{def $all_articles = fetch( 'content', 'tree', hash( 'parent_node_id', 2,
                                                     'class_filter_type', 'include',
                                                     'class_filter_array', array( 'article' ),
                                                     'sort_by', array( 'published', false() ),
                                                     'limit', 5 ) )}
```

A tree fetch over a large site is expensive: always filter by class and limit.

## Counts for paging

`list_count` and `tree_count` take the same filter parameters (without `sort_by`, `offset`, `limit`):

```tpl
{def $total = fetch( 'content', 'list_count', hash( 'parent_node_id', $node.node_id,
                                                    'class_filter_type', 'include',
                                                    'class_filter_array', array( 'article' ) ) )}
```

## Examples

The latest three news on the front page:

```tpl
{def $news_folder = fetch( 'content', 'node', hash( 'remote_id', 'mytheme-news-folder' ) )}
{if $news_folder}
    {def $latest = fetch( 'content', 'list', hash( 'parent_node_id', $news_folder.node_id,
                                                   'sort_by', array( 'published', false() ),
                                                   'limit', 3 ) )}
    {foreach $latest as $item}{node_view_gui content_node=$item view='line'}{/foreach}
    {undef $latest}
{/if}
{undef $news_folder}
```

## Related pages

- [Fetch basics](fetch-basics.md)
- [Fetch filters and sorting](fetch-filters-and-sorting.md)
- Pagination
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
