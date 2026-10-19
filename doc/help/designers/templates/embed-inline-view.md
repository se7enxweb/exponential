---
title: Embed inline view
audience: designer, developer
category: templates
order: 540
summary: Objects embedded inside a line of rich text: the embed-inline tag and view mode, its templates, keeping the output inline (no block elements), and typical uses such as small icons, links to files and inline mentions.
keywords: embed-inline, inline embed, rich text inline, embed-inline.tpl, inline object, inline link, inline image, content/view/embed-inline.tpl
type: help
---
# Embed inline view

This page is for designers who style objects embedded **inside a sentence** of rich text, not as a block. The tag
`embed-inline` uses the view mode of the same name.

## Contents

1. [Templates](#templates)
2. [Stay inline](#stay-inline)
3. [Typical uses](#typical-uses)

## Templates

- `content/datatype/view/ezxmltags/embed-inline.tpl` (and `embed-inline_node.tpl` for nodes) is the tag template.
- `content/view/embed-inline.tpl` and `node/view/embed-inline.tpl` draw the object; override them per class with
  `Source=content/view/embed-inline.tpl`.
- `content.ini [embed-inline]` holds `AvailableViewModes[]`, `AvailableClasses[]` and `CustomAttributes[]` for the
  editor.

## Stay inline

The output goes inside a paragraph, so it must contain only inline elements: `<a>`, `<span>`, `<img>`, `<abbr>`. A
`<div>` or `<figure>` would break the paragraph (browsers close it before the block).

```tpl
{* override/templates/embed-inline/file.tpl *}
<span class="inline-file">{attribute_view_gui attribute=$object.data_map.file}</span>
```

The shipped `content/datatype/view/ezbinaryfile.tpl` prints an inline download link with the file name and size
(`content/download/<object>/<attribute>/version/<version>/file/<name>`), so it can be reused here as it is.

## Typical uses

- A small linked name of another article.
- A file link with its type.
- A tiny image (an icon or a flag) with `alt` text.

## Related pages

- [Embed view](embed-view.md)
- [Rich text templates](rich-text-templates.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
