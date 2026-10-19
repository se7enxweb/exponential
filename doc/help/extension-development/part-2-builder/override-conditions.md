---
title: "Override conditions: class, node, section, view mode and more"
audience: developer, designer, app-creator
category: extension-development
order: 110
summary: Every Match key an override can use - for full views, for node_view_gui lists and embeds, and for fields drawn with attribute_view_gui - taken from the kernel code that sets them, with examples and combination rules.
keywords: override conditions, Match, class_identifier, node, object, parent_node, section_identifier, state_identifier, depth, url_alias, remote_id, viewmode, attribute_identifier, class_group, parent_class_identifier
type: help
---
# Override conditions: class, node, section, view mode and more

Part 2, chapter 11. An override's `Match[<key>]` lines decide which content it applies to. This chapter lists every
key, taken from the code that sets them: `eZNodeviewfunctions::generateNodeViewData()` in
`kernel/classes/eznodeviewfunctions.php` for full views, and the forward rules in `kernel/common/eztemplateautoload.php`
for `node_view_gui` and `attribute_view_gui`.

## Keys for a node view (full view of a page, and node_view_gui)

| Key | Value | Example | Use for |
|---|---|---|---|
| `class_identifier` | the class identifier | `article` | the look of a content type (the most used key) |
| `class` | the numeric class ID | `16` | the same, by ID (prefer identifiers: they survive reinstalls) |
| `class_group` | the IDs of the class groups the class is in | `1` | every class of a group |
| `node` | the node ID | `2` | one page, for example the front page |
| `object` | the object ID | `1234` | one object, in every location |
| `parent_node` | the parent's node ID | `59` | everything directly under one folder |
| `parent_class_identifier` | the parent's class identifier | `folder` | children of a kind of container |
| `parent_class` | the parent's class ID | `1` | the same, by ID |
| `section` | the section ID | `1` | one section, by ID |
| `section_identifier` | the section identifier | `standard` | one section (prefer this) |
| `state` | object state IDs | `2` | content in a state, by ID |
| `state_identifier` | `<group>/<state>` identifiers | `ez_lock/locked` | content in a state |
| `depth` | the node's depth in the tree | `3` | one level of the tree |
| `url_alias` | the node's URL alias | `about-us` | one page, by address |
| `remote_id` | the object's remote ID | `a1b2...` | one object, stable across installations |
| `node_remote_id` | the node's remote ID | `c3d4...` | one location, stable across installations |
| `parent_node_remote_id`, `parent_object_remote_id` | the parent's remote IDs | | children of one place, stable across installations |

A full view also knows `viewmode` (`full`, or the mode in the address), `view_offset` (the page offset of a paged
view) and `navigation_part_identifier` (which menu part of the administration it belongs to).

Remote IDs are the right choice for rules that ship in an extension: node and object IDs differ between installations,
remote IDs do not.

## Keys for a field (attribute_view_gui and attribute_edit_gui)

`{attribute_view_gui attribute=$node.data_map.image}` draws a field with the template
`content/datatype/view/<datatype>.tpl`. An override on that `Source` can match:

| Key | Value | Example |
|---|---|---|
| `attribute_identifier` | the class attribute's identifier | `image` |
| `attribute` | the class attribute's ID | `245` |
| `class_identifier` | the class identifier of the object | `article` |
| `class` | the class ID of the object | `16` |

```ini
[view_article_image]
Source=content/datatype/view/ezimage.tpl
MatchFile=datatype/view/article_image.tpl
Subdir=templates
Match[class_identifier]=article
Match[attribute_identifier]=image
```

## Combining conditions

All `Match` lines of one block must be true:

```ini
[line_article_in_news]
Source=node/view/line.tpl
MatchFile=line/news_article.tpl
Subdir=templates
Match[class_identifier]=article
Match[parent_node]=59
```

There is no "or": write two blocks. For a value that is a list (`class_group`, `state`, `state_identifier`), the
condition is met when the list contains the value.

## Examples by task

**The front page:**

```ini
[full_frontpage]
Source=node/view/full.tpl
MatchFile=full/frontpage.tpl
Subdir=templates
Match[node]=2
```

**Every article in list views:**

```ini
[line_article]
Source=node/view/line.tpl
MatchFile=line/article.tpl
Subdir=templates
Match[class_identifier]=article
```

**A section with its own look:**

```ini
[full_folder_intranet]
Source=node/view/full.tpl
MatchFile=full/intranet_folder.tpl
Subdir=templates
Match[class_identifier]=folder
Match[section_identifier]=intranet
```

## Override keys of other templates

Other templates set their own keys; the debug output's template table shows what each request offered. Embedded
objects in rich text, for example, add keys for the embed's view and class (the media theme matches
`Match[classification]` on embeds). When in doubt, search the code that renders the template for `setKeys` or
`attribute_keys`:

```bash
grep -rn "setKeys\|'attribute_keys'" kernel/ | head
```

## Related pages

- Previous: [Template overrides: the basics](template-overrides-basics.md)
- Next: [View modes for builders](view-modes-for-builders.md)
- [Object states](../../../guides/object-states.md), [Sections](../../../guides/sections.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
