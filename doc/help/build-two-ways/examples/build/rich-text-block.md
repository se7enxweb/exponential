---
title: "A paragraph of formatted text"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1107
summary: The Rich text block (Content) or the Text block for plain text; in templates, the text of a content field.
keywords: rich text, text, paragraph, Rich text block, Text block, content, formatting
type: help
---
# A paragraph of formatted text

A short formatted paragraph between two lists.

*Life cycle: Build, example 8 of 35. Worked out on the Fit & Healthy site of Exponential 6.0.15+.*

## Contents

1. [In the Layouts editor](#in-the-layouts-editor)
2. [With templates](#with-templates)
3. [Which to choose](#which-to-choose)
4. [Check](#check)

## In the Layouts editor

1. Open **Layouts > Layouts** and the layout *Landing Page / Home*; the editor opens it in a draft.
2. Open the block palette (**+** at the top left) and drag **Rich text** (group *Basic*) into the zone `main`. (or **Text** for plain text, which keeps line breaks).
3. Select the block, open the **Design** tab and fill **Content**.
4. **Publish layout**. The pages that use the layout show the block on their next view.

## With templates

1. Keep the text in a content item and print its rich text field, so translations and versions work as for any content:

```smarty
{def $intro = fetch( 'content', 'node', hash( 'node_id', 93 ) )}
{attribute_view_gui attribute=$intro.data_map.full_intro}
{undef $intro}
```

## Which to choose

Text that belongs to the page composition: the block. Text that must be translated and versioned like content: a content field.

## Check

- The paragraph shows with its formatting.

## Related pages

- [The Design tab](../../design-tab-options.md)
- [Build examples, every page](README.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
