---
title: "Fetching content in templates"
audience: builder, designer, developer
category: books
order: 420
summary: The functions of the content module that templates use to get data (a node, an object, lists, trees, counts, versions, the current user) written as today's fetch calls.
keywords: fetch, content module, node, object, list, tree, list_count, tree_count, class_filter, sort_by, current_user, version, Learning book
type: help
---
# Fetching content in templates

A template can ask the kernel's modules for data with `fetch( 'module', 'function', hash( ... ) )`. The result is a value
you assign to a variable. The functions of the `content` module do most of the work.

## One node or object

```
{def $node=fetch( 'content', 'node', hash( 'node_id', 2 ) )
     $object=fetch( 'content', 'object', hash( 'object_id', 4 ) )}
```

The same function takes `fetch( 'content', 'node', hash( 'node_path', 'about-us' ) )` for a node by its address
(see [Fetch for builders](../extension-development/part-2-builder/fetch-for-builders.md)).

## Lists and trees

`list` gets the children of a node; `tree` gets everything below it:

```
{def $items=fetch( 'content', 'list', hash( 'parent_node_id', $node.node_id,
                                            'class_filter_type', 'include',
                                            'class_filter_array', array( 'article' ),
                                            'sort_by', array( 'published', false() ),
                                            'limit', 10 ) )}
```

The usual parameters:

| Parameter | Meaning |
|---|---|
| `parent_node_id` | the node whose children (or tree) you want |
| `class_filter_type`, `class_filter_array` | `include` or `exclude` the classes (by identifier) |
| `sort_by` | an array: the key (`published`, `name`, `priority`, `modified`, or an attribute) and ascending as `true()` or `false()` |
| `limit`, `offset` | how many, from where (for paging) |
| `depth`, `main_node_only` | how deep, and only main locations (for `tree`) |
| `attribute_filter` | filter on attribute values |

Count first when you need the total: `list_count` and `tree_count` take the same filter parameters and return only the
number (the book calls this counting the objects of certain types). Combining `limit` with the count gives paging.

## Other functions the book lists

- **Version information**: `fetch( 'content', 'version', hash( 'object_id', ..., 'version_id', ... ) )` and the list of versions
  (`version_list`) for an audit trail.
- **The current user**: `fetch( 'user', 'current_user' )`; its content object is `$user.contentobject`.
- **Related objects** (`related_objects`), **reverse related** (`reverse_related_objects`), **keyword-related** objects.
- **Search**: `fetch( 'content', 'search', hash( 'text', $q ) )`.
- **Trash, drafts, pending, bookmarks, recent items, section list** and many more.

Every function and its parameters are in [Fetch functions: how they work](../template-operators/fetch-functions-how-they-work.md)
and [Fetch aliases](../template-operators/fetch-aliases.md).

## Cost

Each fetch is a database query. Fetch only what you show, set `limit`, never fetch a tree to count it, and put the result
of expensive fetches in a [cache block](caching-in-templates.md).

Next: [caching in templates](caching-in-templates.md).

## Source and license

Adapted from chapter 3, "Displaying Content with eZ publish Templates", of *Learning eZ publish: Building Content Management Solutions* by Paul Borgermans, Tony Wood,
Paul Forsyth, Martin Bauer, Björn Dieding and Ben Pirt (additional material by Bruce Morrison), second edition, 2024,
[Book-Learning-eZPublish](https://github.com/se7enxweb/Book-Learning-eZPublish). Copyright (c) 2004-2024 7x and Graham
Brookins. Licensed under the GNU Free Documentation License, version 1.3 or any later version, with no Invariant
Sections, no Front-Cover Texts and no Back-Cover Texts ([the license](../../LICENSE-GFDL-1.3.txt)).
Changes: rearranged and shortened; syntax, operators, settings and examples updated to Exponential 6 (foreach, current settings, no inline style or script).
