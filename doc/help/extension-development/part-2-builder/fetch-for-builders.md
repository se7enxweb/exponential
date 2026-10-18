---
title: Fetching content in templates
audience: developer, designer, app-creator
category: extension-development
order: 130
summary: fetch() reads content in templates: a node, children, a subtree, counts, relations, with class and attribute filters, depth and sorting. Parameters from the kernel, examples rendered by the engine.
keywords: fetch, content list, tree, list_count, tree_count, node, object, class_filter_type, class_filter_array, attribute_filter, sort_by, depth, limit, offset, pagination, performance
type: help
---
# Fetching content in templates

Part 2, chapter 13. Templates get content with `fetch( <module>, <function>, hash( <parameters> ) )`. Each module
declares its fetch functions and their parameters in a `function_definition.php`; the `content` module's are in
`kernel/content/function_definition.php`, and every parameter on this page is taken from there. The examples were
rendered by the template engine on an installation with sample content.

## The everyday functions

| Call | Returns |
|---|---|
| `fetch( 'content', 'node', hash( 'node_id', 2 ) )` | one node |
| `fetch( 'content', 'object', hash( 'object_id', 1 ) )` | one object |
| `fetch( 'content', 'list', hash( 'parent_node_id', 2 ) )` | the children of a node |
| `fetch( 'content', 'list_count', hash( 'parent_node_id', 2 ) )` | how many children |
| `fetch( 'content', 'tree', hash( 'parent_node_id', 2 ) )` | the whole subtree below a node |
| `fetch( 'content', 'tree_count', hash( 'parent_node_id', 2 ) )` | how many nodes in the subtree |
| `fetch( 'content', 'related_objects', hash( 'object_id', ... ) )` | related objects |
| `fetch( 'content', 'reverse_related_objects', hash( 'object_id', ... ) )` | objects that relate to this one |

The full list of fetch functions of every module is in Built-in fetch functions by module.

## Parameters of list and tree

| Parameter | Meaning | Example |
|---|---|---|
| `parent_node_id` | where to start | `$node.node_id` |
| `limit`, `offset` | paging | `'limit', 10, 'offset', $view_parameters.offset` |
| `sort_by` | sorting: one pair or a list of pairs; `true()` ascending | `array( 'published', false() )` |
| `class_filter_type`, `class_filter_array` | only (`include`) or not (`exclude`) these classes | `'include'`, `array( 'article' )` |
| `attribute_filter` | conditions on attributes | see below |
| `depth`, `depth_operator` | how deep in a tree | `3`, `'le'` |
| `main_node_only` | only main locations | `true()` |
| `ignore_visibility` | include hidden nodes | `true()` (admin views only) |
| `language`, `only_translated` | translations | `'ger-DE'` |
| `load_data_map` | load attributes with the nodes | `false()` for large lists you only name |
| `as_object` | objects or arrays | `false()` for raw rows |

## Examples

**The five newest articles anywhere below the top node, and how many there are:**

```text
{def $news = fetch( 'content', 'tree', hash( 'parent_node_id', 2,
                                             'class_filter_type', 'include',
                                             'class_filter_array', array( 'article' ),
                                             'sort_by', array( 'published', false() ),
                                             'limit', 5 ) )
     $count = fetch( 'content', 'tree_count', hash( 'parent_node_id', 2,
                                                    'class_filter_type', 'include',
                                                    'class_filter_array', array( 'article' ) ) )}
<p>{$count} articles, the newest five:</p>
<ul>
{foreach $news as $item}
    <li><a href={$item.url_alias|ezurl}>{$item.name|wash}</a></li>
{/foreach}
</ul>
```

**Content of the last year, three levels deep, sorted by priority then name:**

```text
{def $recent = fetch( 'content', 'tree', hash( 'parent_node_id', 2,
                     'attribute_filter', array( array( 'published', '>', sub( currentdate(), 31536000 ) ) ),
                     'depth', 3,
                     'limit', 3,
                     'sort_by', array( array( 'priority', true() ), array( 'name', true() ) ) ) )}
```

`attribute_filter` takes a list of conditions `array( <attribute>, <operator>, <value> )`. The attribute is a node
property (`published`, `modified`, `name`, `priority`, `section`) or `<class>/<attribute>` for a field of a class
(`article/rating`). Operators are `=`, `!=`, `<`, `>`, `<=`, `>=`, `in`, `not_in`, `between`, `not_between`, `like` (`createAttributeFilterSQLStrings()` in
`kernel/classes/ezcontentobjecttreenode.php`). A first element
`'or'` makes it a disjunction: `array( 'or', array( ... ), array( ... ) )`.

**Paging a folder's children:**

```text
{def $page_limit = 10
     $children = fetch( 'content', 'list', hash( 'parent_node_id', $node.node_id,
                                                 'offset', $view_parameters.offset,
                                                 'limit', $page_limit,
                                                 'sort_by', $node.sort_array ) )
     $children_count = fetch( 'content', 'list_count', hash( 'parent_node_id', $node.node_id ) )}
{include uri='design:navigator/google.tpl'
         page_uri=$node.url_alias
         item_count=$children_count
         view_parameters=$view_parameters
         item_limit=$page_limit}
```

`$node.sort_array` uses the sorting the editor chose for the folder in the administration, which is almost always what
the visitor should see.

## Access rights are applied

Fetch returns only what the current user may read: unpublished, hidden (unless `ignore_visibility`) and forbidden
content is left out. A list that looks short for anonymous visitors usually is correct; check the roles before you
suspect the fetch.

## Performance

| Do | Why |
|---|---|
| Fetch once, before the loop | a fetch inside a `foreach` runs once per item |
| Use `limit` | a tree fetch without one can load thousands of nodes |
| Use the `_count` functions for numbers | they do not load the nodes |
| Use `class_filter_array` and `depth` | the database does the filtering |
| Set `load_data_map` to `false()` when you only print names | attributes are not loaded |
| Keep results in a `cache-block` when they change rarely | see [Caching and templates](caching-and-templates.md) |

Turn on `[DatabaseSettings] SQLOutput` while you develop to see the queries a page makes.

## Related pages

- Previous: [View modes for builders](view-modes-for-builders.md)
- Next: [The template operators builders use most](template-operators-builders-use-most.md)
- Built-in fetch functions by module
- Fetch functions: writing your own
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
