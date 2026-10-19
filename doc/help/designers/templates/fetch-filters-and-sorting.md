---
title: Fetch filters and sorting
audience: designer, developer
category: templates
order: 420
summary: Sorting content lists (published, modified, name, priority, path, depth, class_identifier, section, attribute ...) and filtering them by class and by field values with attribute_filter and its operators (=, !=, <, >, <=, >=, in, not_in, between, not_between, like, not_like).
keywords: sort_by, sorting, attribute_filter, filter, class_filter_array, published, priority, name, like, between, in, not_in, fetch filter, sort order
type: help
---
# Fetch filters and sorting

This page is a reference for designers who sort and filter lists of nodes in `fetch( 'content', 'list' | 'tree' ... )`.

## Contents

1. [Sorting](#sorting)
2. [Class filters](#class-filters)
3. [Attribute filters](#attribute-filters)
4. [Examples](#examples)

## Sorting

`sort_by` is an array `array( <field>, <ascending> )`, or an array of such pairs for several levels:

```tpl
'sort_by', array( 'published', false() )                                  {* newest first *}
'sort_by', array( array( 'priority', true() ), array( 'name', true() ) )  {* priority, then name *}
'sort_by', $node.sort_array                                               {* the order the editor chose for this folder *}
```

Fields handled by `eZContentObjectTreeNode` (`kernel/classes/ezcontentobjecttreenode.php`): `path`, `path_string`,
`published`, `modified`, `modified_subnode`, `section`, `node_id`, `contentobject_id`, `depth`, `class_identifier`,
`class_name`, `priority`, `name`, and `attribute` (by a field's value: `array( 'attribute', false(), 'event/from_time' )`).

## Class filters

```tpl
'class_filter_type', 'include', 'class_filter_array', array( 'article', 'blog_post' )
'class_filter_type', 'exclude', 'class_filter_array', array( 'image' )
```

Filter by class in the fetch, not with `{if}` in the loop: the database then returns only what you show.

## Attribute filters

`attribute_filter` is an array of conditions, optionally starting with `'and'` or `'or'`:

```tpl
'attribute_filter', array( 'and',
                           array( 'event/from_time', '>=', currentdate() ),
                           array( 'section', '=', 1 ) )
```

Each condition is `array( <field>, <operator>, <value> )`, where the field is `<class>/<attribute>` or a node property
such as `published`, `modified`, `section`, `name`. Operators: `=`, `!=`, `<`, `>`, `<=`, `>=`, `in`, `not_in`,
`between`, `not_between` (value: `array( from, to )`), `like`, `not_like` (with `*` as wildcard).

## Examples

Upcoming events, soonest first:

```tpl
{def $events = fetch( 'content', 'tree', hash( 'parent_node_id', $node.node_id,
                                               'class_filter_type', 'include',
                                               'class_filter_array', array( 'event' ),
                                               'attribute_filter', array( array( 'event/from_time', '>=', currentdate() ) ),
                                               'sort_by', array( 'attribute', true(), 'event/from_time' ),
                                               'limit', 10 ) )}
```

Note: `currentdate()` in a view-cached template is frozen at cache time; a full view showing upcoming events should set
a short `cache_ttl` ([Caching for designers](../theme-design/caching-for-designers.md)).

## Related pages

- [Fetching nodes and lists](fetching-nodes-and-lists.md)
- [Fetch performance](fetch-performance.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
