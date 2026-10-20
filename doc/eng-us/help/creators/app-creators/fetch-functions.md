---
title: "Fetch functions: reading data in templates"
audience: app-creator, developer
category: app-creation
order: 150
summary: Read data in templates with fetch() - the content fetches (node, object, list, tree, list_count, related objects), their parameters (filters, sorting, limits, attribute filters), the other modules' fetches, and performance rules.
keywords: fetch, fetch functions, content list, content tree, list_count, class_filter, attribute_filter, sort_by, limit, offset, depth, function_definition.php, templates, app creator
type: help
---
# Fetch functions: reading data in templates

This page is for application creators and template authors who read data in templates. `fetch( '<module>', '<function>',
hash( ... ) )` calls a function a module declares in its `function_definition.php`; the content module's are the ones
you use most (`kernel/content/function_definition.php`).

## Contents

1. [The most used content fetches](#the-most-used-content-fetches)
2. [Parameters of list and tree](#parameters-of-list-and-tree)
3. [Examples](#examples)
4. [Fetches of other modules](#fetches-of-other-modules)
5. [Performance rules](#performance-rules)

## The most used content fetches

| Fetch | Returns |
|---|---|
| `fetch( 'content', 'node', hash( 'node_id', 2 ) )` | one node |
| `fetch( 'content', 'object', hash( 'object_id', 57 ) )` | one object |
| `fetch( 'content', 'list', hash( 'parent_node_id', 2, ... ) )` | children of a node |
| `fetch( 'content', 'tree', hash( 'parent_node_id', 2, ... ) )` | the whole subtree |
| `fetch( 'content', 'list_count', ... )`, `'tree_count'` | the number, for paging |
| `fetch( 'content', 'related_objects', ... )`, `'reverse_related_objects'` | relations ([Relations](../website-creators/relations-and-reuse.md)) |
| `fetch( 'content', 'class', hash( 'class_id', 'article' ) )` | a class |
| `fetch( 'content', 'view_top_list', ... )` | most viewed nodes |

All content fetches return only what the current user may read.

## Parameters of list and tree

| Parameter | Example |
|---|---|
| `class_filter_type`, `class_filter_array` | `'include'`, `array( 'article', 'blog_post' )` |
| `sort_by` | `array( 'published', false() )`, `$node.sort_array` |
| `limit`, `offset` | `10`, `$view_parameters.offset` |
| `depth` (tree) | `3` |
| `attribute_filter` | `array( array( 'article/featured', '=', 1 ) )` |
| `extended_attribute_filter` | filters of extensions, such as eztags |
| `main_node_only` | `true()` |
| `ignore_visibility` | `true()` to include hidden nodes (admin use) |

## Examples

Paging a folder:

```text
{def $page_limit = 10
     $children = fetch( 'content', 'list', hash( 'parent_node_id', $node.node_id,
                                                  'offset', $view_parameters.offset,
                                                  'limit', $page_limit,
                                                  'sort_by', $node.sort_array ) )
     $count = fetch( 'content', 'list_count', hash( 'parent_node_id', $node.node_id ) )}
{foreach $children as $child}{node_view_gui content_node=$child view='line'}{/foreach}
{include uri='design:navigator/google.tpl' page_uri=$node.url_alias item_count=$count
         view_parameters=$view_parameters item_limit=$page_limit}
{undef $page_limit $children $count}
```

Featured articles anywhere below a section:

```text
{def $featured = fetch( 'content', 'tree', hash( 'parent_node_id', 2,
    'class_filter_type', 'include', 'class_filter_array', array( 'article' ),
    'attribute_filter', array( array( 'article/featured', '=', 1 ) ),
    'sort_by', array( 'published', false() ), 'limit', 3 ) )}
```

## Fetches of other modules

Every module with a `function_definition.php` offers fetches: `user` (`current_user`, `has_access_to`), `section`,
`shop` (`basket`), `layout`, `url`, `search`, `exphelp` and more; see the files in `kernel/*/function_definition.php`.
Extensions add their own ([Writing your own fetch function](writing-a-fetch-function.md)).

## Performance rules

- Always set a `limit`; a tree without one can load thousands of nodes.
- Count with `list_count`, do not count a fetched list.
- Use `class_filter_array` and `depth` to narrow trees.
- Put expensive fetches in cache blocks ([View cache and cache blocks](../website-creators/view-cache-and-cache-blocks.md)).
- In PHP, call the class methods directly (`eZContentObjectTreeNode::subTreeByNodeID()`), not fetch.

## Related pages

- [Writing your own fetch function](writing-a-fetch-function.md)
- [Fetch function pattern](../../../standards/patterns/fetch-function.md)
- [Templates and design](../../../guides/templates-and-design.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
