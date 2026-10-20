---
title: Nodes, objects and attributes
audience: designer, developer
category: templates
order: 440
summary: The three things content templates work with: the node (a place in the tree), the content object (the content with its versions) and its attributes (the fields in data_map), with the node attributes designers use most and how to read a field's content.
keywords: node, content object, attribute, data_map, contentobject_attribute, has_content, content, url_alias, class_identifier, children, parent, path, can_edit, main node, locations
type: help
---
# Nodes, objects and attributes

This page is for designers who write view templates. Content in Exponential has three layers; templates move between
them with the dot.

## Contents

1. [The three layers](#the-three-layers)
2. [Node attributes](#node-attributes)
3. [Object attributes](#object-attributes)
4. [Fields: data_map](#fields-data_map)
5. [Several locations](#several-locations)

## The three layers

| Layer | What it is | In templates |
|---|---|---|
| **Node** | a place of the content in the tree (one object can have several) | `$node` in view templates |
| **Content object** | the content itself, with versions and languages | `$node.object` |
| **Attribute** | one field of the object: title, body, image | `$node.data_map.<identifier>` |

## Node attributes

From `eZContentObjectTreeNode` (`kernel/classes/ezcontentobjecttreenode.php`), the ones designers use most:

| Attribute | Gives |
|---|---|
| `name` | the object's name in the current language |
| `node_id`, `parent_node_id`, `contentobject_id`, `depth` | ids and depth |
| `url_alias` | the path for `ezurl` |
| `class_identifier`, `class_name` | the content class |
| `data_map` | the fields |
| `object` | the content object |
| `children`, `children_count`, `subtree` | below it (fetches; see [Fetch performance](fetch-performance.md)) |
| `parent`, `path`, `path_array`, `path_with_names` | above it |
| `sort_array` | the sort order the editor chose for its children |
| `can_read`, `can_edit`, `can_create`, `can_remove`, `can_hide`, `can_move` | the current user's permissions on it |
| `is_main`, `is_hidden`, `is_invisible` | main location, hidden states |
| `creator`, `remote_id` | the creator; the stable remote id |

## Object attributes

`$node.object.published` and `modified` (timestamps), `owner`, `current_language`, `available_languages`,
`main_node`, `assigned_nodes`, `section_id`, `remote_id`.

## Fields: data_map

`$node.data_map` is a hash of the fields by identifier. Each field (an `eZContentObjectAttribute`) has:

| Attribute | Gives |
|---|---|
| `has_content` | whether it is filled |
| `content` | its value, in a form that depends on the datatype (a string, an image object, a date ...) |
| `data_text`, `data_int`, `data_float` | the stored raw values |
| `contentclass_attribute_identifier`, `contentclass_attribute.name` | its identifier and label |
| `data_type_string` | the datatype (`ezstring`, `ezxmltext`, `ezimage` ...) |

Draw a field with its datatype template, which knows its markup:

```tpl
{if $node.data_map.intro.has_content}
    {attribute_view_gui attribute=$node.data_map.intro}
{/if}
```

Read `content` yourself only when you need a part of it (an image alias URL, a date for `l10n`).

## Several locations

An object placed in two folders has two nodes. Links and canonical URLs should use the main node:
`$node.object.main_node.url_alias`. `$module_result.content_info.main_node_url_alias` has it in the pagelayout.

## Related pages

- [Attributes and dot notation](attributes-and-dot-notation.md)
- [attribute_view_gui](attribute-view-gui.md)
- [Datatype view templates](datatype-view-templates.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
