---
title: attribute_view_gui
audience: designer, developer
category: templates
order: 450
summary: Drawing one field of content with attribute_view_gui: how it picks the datatype template, extra parameters such as image_class, override keys (attribute_identifier, class_identifier) to restyle one field of one class, and information collector fields.
keywords: attribute_view_gui, datatype template, field output, image_class, content/datatype/view, override attribute, attribute_identifier, collect, information collector, field template
type: help
---
# attribute_view_gui

This page is for designers who print fields of content. `{attribute_view_gui}` hands a field to the template of its
datatype, which knows how to draw it: rich text as HTML, an image in an alias, a date in the locale's format.

## Contents

1. [Basic use](#basic-use)
2. [Which template it uses](#which-template-it-uses)
3. [Extra parameters](#extra-parameters)
4. [Restyling one field with an override](#restyling-one-field-with-an-override)
5. [Information collector fields](#information-collector-fields)

## Basic use

```tpl
{attribute_view_gui attribute=$node.data_map.body}
{attribute_view_gui attribute=$node.data_map.image image_class='medium'}
```

Test optional fields first: `{if $node.data_map.image.has_content}...{/if}`.

## Which template it uses

The function is defined in `kernel/common/eztemplateautoload.php` (`eZObjectForwardInit`). It takes the template folder
`content/datatype/view/` and the field's view template name, which is its datatype: a text line uses
`content/datatype/view/ezstring.tpl`, rich text `ezxmltext.tpl`, an image `ezimage.tpl` (the folder lists them all).
Like every template, these are found through the design cascade, so your design can replace the one for a datatype.

## Extra parameters

Every other parameter is passed to the datatype template. The kernel's `ezimage.tpl` documents its own at the top:
`image_class` (the alias, default `large`), `css_class`, `alignment`, `link_to_image`, `href`, `alt_text`, `title` and
more. Read the header of a datatype template to see what it accepts.

## Restyling one field with an override

The function sets override keys you can match on: `attribute_identifier`, `attribute` (the class attribute id),
`class_identifier`, `class`. To draw the `image` field of articles differently:

```ini
[mytheme_article_image]
Source=content/datatype/view/ezimage.tpl
MatchFile=datatype/article_image.tpl
Subdir=templates
Match[class_identifier]=article
Match[attribute_identifier]=image
```

The override template receives `$attribute` like the original ([Override basics](override-ini-basics.md)).

## Information collector fields

A field marked as an information collector (a feedback form) is drawn from `content/datatype/collect/` instead of
`view/`, so it renders as an input. See [Forms in templates](forms-in-templates.md).

## Related pages

- [Datatype view templates](datatype-view-templates.md)
- [Nodes, objects and attributes](nodes-objects-attributes.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
