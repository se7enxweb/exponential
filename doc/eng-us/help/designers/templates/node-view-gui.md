---
title: node_view_gui
audience: designer, developer
category: templates
order: 470
summary: Drawing a node in a view mode with node_view_gui: lists of line views, cards and teasers, the node/view templates, passing extra parameters, the override keys it sets (class, parent, depth, section, remote ids) and why it beats include for content.
keywords: node_view_gui, view mode, line view, listitem, teaser, node/view, content_node, override keys, parent_node, class_identifier, render node, list of nodes
type: help
---
# node_view_gui

This page is for designers who draw lists of content: the children of a folder, search results, related articles. Use
`{node_view_gui}` to draw each node in a view mode; the override system then picks the right template per class.

## Contents

1. [Use](#use)
2. [What the template receives](#what-the-template-receives)
3. [Override keys](#override-keys)
4. [Extra parameters](#extra-parameters)
5. [Why not include?](#why-not-include)

## Use

```tpl
<ul class="article-list">
{foreach $children as $child}
    <li>{node_view_gui content_node=$child view='line'}</li>
{/foreach}
</ul>
```

The template used is `node/view/line.tpl`, or an override for that view and the node's class.

## What the template receives

`$node` (the node), and `$view_parameters` with `offset` 0 by default (from the function's definition in
`kernel/common/eztemplateautoload.php`). Note: not `$module_result`, and not the variables of the calling template other than
those passed as parameters.

## Override keys

`node`, `object`, `class`, `class_identifier`, `class_group`, `section`, `section_identifier`, `state`,
`state_identifier`, `parent_node`, `depth`, `url_alias`, `remote_id`, `node_remote_id`, `parent_class_identifier`,
`parent_node_remote_id`, `parent_object_remote_id`. So a rule can give articles inside a "News" folder a different line
view than articles elsewhere ([Override by parent](override-by-parent.md)).

## Extra parameters

Every other parameter becomes a variable in the view template:

```tpl
{node_view_gui content_node=$child view='line' image_class='small' show_date=true()}
```

and in the line template: `{default image_class='medium' show_date=false()}`.

## Why not include?

| | `node_view_gui` | `include` |
|---|---|---|
| per class templates through overrides | yes | no |
| other designs can restyle one class | yes | only by replacing the whole part |
| view mode reused elsewhere (layouts blocks, search) | yes | no |

## Related pages

- [View modes](view-modes.md)
- [Line view](line-view.md)
- [Node templates by class](node-templates-by-class.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
