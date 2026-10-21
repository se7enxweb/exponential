---
title: "Rich text tags"
audience: editor, builder, developer
category: books
order: 270
summary: The XML tags of the rich text datatype (headings, lists, emphasis, literal, links, anchors, tables, embedded objects, custom tags), the class parameter, and how the online editor and templates relate to them.
keywords: ezxmltext, XML text, tags, header, paragraph, link, anchor, table, object, embed, custom tag, class parameter, online editor, Learning book
type: help
---
# Rich text tags

The `ezxmltext` datatype stores structured blocks of text as XML. Editors rarely see the XML, because the online editor
writes it, but a builder needs the vocabulary: it is what templates turn into HTML, and what an import or a script
writes. The tags that the kernel accepts are listed in `kernel/classes/datatypes/ezxmltext/ezxmlschema.php`; the set is
the one the book describes.

Every tag can take a `class` parameter. It is normally used in the tag's template to put a CSS class on the HTML, but
the template may do more with it (the book's example: a header of class `chapter` rendered differently).

## The tags

| Tag | Use |
|---|---|
| `section` | The container of the whole text; sections can nest. |
| `header` | A heading. Parameters `level` (1 to 6) and `class`. |
| `paragraph` | A paragraph. |
| `ul`, `ol`, `li` | Unordered and ordered lists. |
| `strong` | Bold. |
| `emphasize` | Emphasis, usually italic. |
| `literal` | Unformatted text: source code, HTML or XML shown as written, rendered without being interpreted. |
| `link` | A hyperlink. Takes `href` (or the ID of a stored link), `target`, and more. |
| `anchor` | A named place in the text that a link can point to. Takes `name`. |
| `table`, `tr`, `th`, `td` | A table. Parameters such as `border` and `width` on the table; `width`, `colspan`, `rowspan` on cells. |
| `embed`, `embed-inline` | An object of the site (an image, a file, an article) placed in the text, as a block or inside a line. Parameters: `object_id` or `node_id`, `view`, `align`, `class`. |
| `custom` | A tag you define yourself by name. Needs a template. |
| `line` | A line inside a heading. |

The book also lists alternative spellings (`b`, `bold`, `i`, `em`, `p`, `a`, `h` and others); the input parser accepts
the familiar HTML names and stores the tags in the table above. The book's `object` tag is today's `embed`.

## Links are stored once

Every hyperlink in an XML text is kept in a link table of its own, with the address. That is what makes the link check
possible ([Links, URL aliases and wildcards](../../guides/urls-and-aliases.md)): the same address in many texts is one
row, and a change of the address can be made in one place. A link to an object of the site is stored by object or node
ID, so it survives a rename or a move.

## Embedded objects

An embedded object shows another object inside the text, using the **view** you name (`embed` for a block, or
`embed-inline` for inline). The template for that view and class decides what appears: an image in a size, a file
as a download link. See [Embedding another object](../manual/how-do-i-embed-another-object.md).

## Custom tags

Custom tags carry a name (`<custom name="quote">`), and each has a template under `design/<design>/templates/content/datatype/view/ezxmltags/`
(for example a "quote" or "factbox" tag). Their names are listed in `content.ini` (`[CustomTagSettings] AvailableCustomTags[]`)
and made available to editors in the online editor ([Content model and editing](../../guides/content-model-and-editing.md#4-the-online-editor-and-tinymce-8)).

## In the editor

An editor does not type tags. The online editor offers formatting buttons that write the right tags, and the
administration can show the result. To see the XML itself, a developer reads
`$attribute.content.xml_data`; see [Attribute access](../template-language/attribute-access.md).

Next: [creating and editing objects](creating-and-editing-objects.md).

## Source and license

Adapted from chapter 2, "Content Management with eZ publish", of *Learning eZ publish: Building Content Management Solutions* by Paul Borgermans, Tony Wood,
Paul Forsyth, Martin Bauer, Björn Dieding and Ben Pirt (additional material by Bruce Morrison), second edition, 2024,
[Book-Learning-eZPublish](https://github.com/se7enxweb/Book-Learning-eZPublish). Copyright (c) 2004-2024 7x and Graham
Brookins. Licensed under the GNU Free Documentation License, version 1.3 or any later version, with no Invariant
Sections, no Front-Cover Texts and no Back-Cover Texts ([the license](../../LICENSE-GFDL-1.3.txt)).
Changes: rearranged and shortened; settings, labels, datatypes, tags and URLs checked against Exponential 6 and corrected; links to the current guides added.
