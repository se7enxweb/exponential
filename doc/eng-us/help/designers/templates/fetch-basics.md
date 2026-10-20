---
title: Fetch basics
audience: designer, developer
category: templates
order: 400
summary: How templates read data with fetch( module, function, parameters ): the modules with fetch functions, the most used content and user fetches, parameters as a hash, what comes back, and where each module's functions are defined.
keywords: fetch, fetch function, fetch content node, fetch content list, fetch user current_user, function_definition, template data, read content, fetch parameters
type: help
---
# Fetch basics

This page is for designers who need data a template was not given: the children of a folder, the latest news, the
current user. Templates read data only through **fetch functions**.

## Contents

1. [The call](#the-call)
2. [The fetches designers use most](#the-fetches-designers-use-most)
3. [Where fetch functions are defined](#where-fetch-functions-are-defined)
4. [What comes back](#what-comes-back)
5. [Rules](#rules)

## The call

```tpl
{def $news = fetch( 'content', 'list', hash( 'parent_node_id', 61,
                                             'sort_by', array( 'published', false() ),
                                             'limit', 5 ) )}
```

`fetch( <module>, <function>, <parameters as a hash> )`. Parameters not given take their defaults; required ones must be
given.

## The fetches designers use most

| Call | Returns |
|---|---|
| `fetch( 'content', 'node', hash( 'node_id', 2 ) )` | one node (also by `node_path` or `remote_id`) |
| `fetch( 'content', 'list', hash( 'parent_node_id', ..., ... ) )` | the children of a node |
| `fetch( 'content', 'list_count', hash( ... ) )` | how many children (for paging) |
| `fetch( 'content', 'tree', hash( ... ) )`, `tree_count` | all descendants, with depth limits |
| `fetch( 'content', 'object', hash( 'object_id', ... ) )` | one content object |
| `fetch( 'content', 'related_objects', hash( ... ) )` | related objects |
| `fetch( 'content', 'search', hash( 'text', ... ) )` | search results |
| `fetch( 'content', 'translation_list' )` | the languages of the site |
| `fetch( 'user', 'current_user' )` | the signed-in user (also `$current_user` in the pagelayout) |
| `fetch( 'user', 'has_access_to', hash( 'module', ..., 'function', ... ) )` | permission test |
| `fetch( 'explayouts', 'resolve_layout', hash() )` | the layout of the page ([Fetching a layout in templates](layouts-template-fetch.md)) |

## Where fetch functions are defined

Each module defines its fetch functions in `function_definition.php`: `kernel/content/function_definition.php` (node,
list, tree, search and about fifty more), `kernel/user/function_definition.php` (current_user, has_access_to, can_open,
member_of ...), and extensions in `extension/<ext>/modules/<module>/function_definition.php` (for example the layouts'
`extension/explayouts/modules/explayouts/function_definition.php`). Each entry lists its parameters with names, types
and whether they are required.

## What comes back

- `node` returns a node object, or `false` when it does not exist or the user may not read it.
- `list` and `tree` return arrays of nodes (or plain arrays with `'as_object', false()`).
- Count functions return numbers.

Always test single results before use: `{if $node}...{/if}`.

## Rules

- Fetch once and keep the result in a variable ([Fetch performance](fetch-performance.md)).
- Every list fetch has a `limit`.
- Content the user may not read is left out of results by default; do not work around it.
- Find special nodes by `remote_id`, not by a hard-coded node id that differs between installations.

## Related pages

- [Fetching nodes and lists](fetching-nodes-and-lists.md)
- [Fetch filters and sorting](fetch-filters-and-sorting.md)
- [Nodes, objects and attributes](nodes-objects-attributes.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
