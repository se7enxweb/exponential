---
title: content_view_gui
audience: designer, developer
category: templates
order: 460
summary: Drawing a content object (without a node) in a view mode with content_view_gui: when you have an object rather than a node (relations, embeds, search hits), the content/view templates, and the override keys it sets.
keywords: content_view_gui, content object view, content/view, related objects, object relation, view mode, embed object, override keys, object template
type: help
---
# content_view_gui

This page is for designers who draw objects that come without a node: related objects, objects in relation fields,
embedded objects. `{content_view_gui}` is the object counterpart of [node_view_gui](node-view-gui.md).

## Contents

1. [Use](#use)
2. [Templates](#templates)
3. [Override keys](#override-keys)
4. [Node or object?](#node-or-object)

## Use

```tpl
{foreach $node.object.related_contentobject_array as $related}
    {content_view_gui content_object=$related view='text_linked'}
{/foreach}
```

`content_object` is the object, `view` the view mode. The template receives the object as `$object`.

## Templates

`content/view/<view>.tpl`, through the design cascade. The kernel's `design/standard/templates/content/view/` has
`embed.tpl`, `embed-inline.tpl`, `full.tpl`, `line.tpl`, `plain.tpl`, `text.tpl`, `text_linked.tpl`, `tiny.tpl` and a few
more for the administration.

## Override keys

From the function's definition in `kernel/common/eztemplateautoload.php`: `object`, `class`, `class_identifier`,
`class_group`, `section`, `remote_id`, `state`, `state_identifier`. An override with
`Source=content/view/embed.tpl` and `Match[class_identifier]=image` gives images their own embed template.

## Node or object?

If you have a node, use `node_view_gui`: it knows the node's place (URL, parent) and its overrides can match on the
parent and depth. From an object, `$object.main_node` gives its main node when it has one.

## Related pages

- [node_view_gui](node-view-gui.md)
- [Embed view](embed-view.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
