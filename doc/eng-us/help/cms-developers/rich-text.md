---
title: Rich text
audience: cms-developer
category: cms-development
order: 15
summary: For CMS developers, how rich text works in Exponential. Text is stored as XML (ezxmltext), edited in the Online Editor, drawn as HTML by one template per tag, and extended with custom tags and classes. It also covers importing HTML into it.
keywords: rich text, ezxmltext, XML block, XmlText, online editor, ezoe, TinyMCE, custom tags, factbox, embed, link, classes, ezxmltags, output handler, HTML import, simplified input parser
type: help
---
# Rich text

This page is for developers who decide how editors write long text and how it reaches the page. Rich text in
Exponential is not stored as HTML: it is stored as XML with a small set of tags, and every tag is turned into HTML by
a template you control. That is what keeps editors' text consistent across a redesign.

## Contents

1. [How it works](#how-it-works)
2. [What editors can write](#what-editors-can-write)
3. [The Online Editor](#the-online-editor)
4. [From XML to HTML](#from-xml-to-html)
5. [Custom tags: your own structure](#custom-tags-your-own-structure)
6. [Classes editors may choose](#classes-editors-may-choose)
7. [Importing HTML](#importing-html)
8. [Practical rules](#practical-rules)

## How it works

```text
Editor (Online Editor, HTML in the browser)
   -> input handler: HTML is reduced to the allowed XML tags (eZSimplifiedXMLInputParser)
      -> stored XML in the attribute (data_text)
         -> output handler (eZXHTMLXMLOutput): one template per tag
            -> HTML on the page
```

The datatype is `ezxmltext` (XML block in the class editor). Its handlers are in
`kernel/classes/datatypes/ezxmltext/handlers/`: the input parser, and output handlers for XHTML, plain text and PDF.
Because the stored form is structure rather than markup, the same text can be drawn differently by each design, as
plain text in a feed or as PDF.

## What editors can write

The tags are paragraphs, headers, `strong` and `emphasize`, links and anchors, ordered and unordered lists, tables,
`literal` blocks, line breaks, embeds (block and inline) and custom tags; the shipped templates for them are in
`design/standard/templates/content/datatype/view/ezxmltags/`. The tags that take settings (classes, view modes,
custom attributes) have a block each in `settings/content.ini`, such as `[paragraph]`, `[header]`, `[table]`,
`[link]`, `[embed]` and `[embed-inline]`. Embeds and links to content create relations
([Relations and embeds](relations-and-embeds.md)).
Scripts, styles and inline event handlers have no tag, so they cannot be stored: pasted HTML loses them by design.

## The Online Editor

Editors write rich text in the Online Editor of the `ezoe` extension. Two engines write the same XML: TinyMCE 3,
the default, and TinyMCE 8, which a siteaccess or one user can switch on (`ezoe.ini [EditorSettings]
EditorEngine=tinymce8`). Embeds, links, custom tags, tables and uploads are in its toolbar; uploads accept only the
file types the settings allow. Details and the switch for one user:
[The online editor and TinyMCE 8](../../guides/content-model-and-editing.md#4-the-online-editor-and-tinymce-8) and
[Online editor TinyMCE 8](../../features/6.0/online-editor-tinymce8.md).

## From XML to HTML

`attribute_view_gui` on an `ezxmltext` attribute runs the XHTML output handler, which draws each tag with the template
`content/datatype/view/ezxmltags/<tag>.tpl` of your design chain. To change how a tag looks on one site, override
that template in your design extension, for example a table wrapped in a scrolling container, or headers with anchor
links. Every tag template receives the tag's content and attributes; the list is in
[Rich text templates](../designers/templates/rich-text-templates.md).

```smarty
{* in a full view template *}
{if $node.data_map.body.has_content}
    <div class="exp-article__body">{attribute_view_gui attribute=$node.data_map.body}</div>
{/if}
```

For a teaser, the plain text of the XML is often what you want; output it through the attribute's content, washed,
and shorten it with a string operator ([String operators](../designers/templates/string-operators.md)).

## Custom tags: your own structure

A custom tag is a tag of your own that editors insert like any other: a fact box, a quote with its source, a
call-to-action, a video from a service. The shipped list is
`content.ini [CustomTagSettings] AvailableCustomTags[]` (`factbox`, `quote`, `strike`, `sub`, `sup`), with
`IsInline[<tag>]=true` for tags that sit inside a paragraph. To add one in your extension:

```ini
# extension/exp_mysite/settings/content.ini.append.php
<?php /* #?ini charset="utf-8"?

[CustomTagSettings]
AvailableCustomTags[]=exp_callout

[exp_callout]
CustomAttributes[]=title
*/ ?>
```

Then the template `content/datatype/view/ezxmltags/exp_callout.tpl` in your design draws it, and the Online Editor
offers it in its custom tag dialog. The full recipe, with attributes and accessibility rules:
[Custom tags templates](../designers/templates/custom-tags-templates.md), and the tutorial
[A custom tag by hand](../extension-development/part-3-extension-developer/tutorials/by-hand-custom-tag.md).

Use custom tags instead of letting editors paste markup: you keep the HTML, the styles and the content security policy
in the design ([Content security policy for extensions](../extension-development/part-3-extension-developer/content-security-policy-for-extensions.md)).

## Classes editors may choose

Each tag block in `content.ini` has `AvailableClasses[]`: the CSS classes editors may pick for that tag (a "lead"
paragraph, a "striped" table). Declare them in your extension's `content.ini.append.php` and style them in your
design's stylesheet. Keep the list short; every class is a promise your design keeps.

## Importing HTML

An import must write the stored XML, not HTML: `fromString()` of `ezxmltext` stores the string as it is. Convert HTML
first with the kernel's input parser, as the import skeleton shows
([Import and export tools, section 4](../../install/migration/exponential-tools.md#4-a-custom-import-script-the-skeleton)):
reduce the HTML to the simplified input (paragraphs, bold, italic, links, headings, lists, tables), parse it, and
store the result. Images and links inside the HTML become embeds and links to content in a second pass, once the
targets exist ([Importing content](importing-content.md)).

Coming from a platform that stores DocBook rich text: see
[Coming from eZ Platform or Ibexa](coming-from-ez-platform-or-ibexa.md#field-types-and-datatypes).

## Practical rules

- Use `ezxmltext` for any text longer than a sentence that may need a link or emphasis; use `eztext` for plain text.
- Override tag templates in your design; never edit the shipped templates in `design/standard`.
- Add custom tags for recurring structures instead of asking editors for markup.
- Wash nothing that comes from `attribute_view_gui`: the output handler already escapes text; wash everything you
  print yourself with `|wash()`.

## Related pages

- [Choosing datatypes](choosing-datatypes.md)
- [Rich text supplementary characters and TeX source](../../features/6.0/rich-text-supplementary-characters-and-tex-source.md)
- [Attribute view gui](../designers/templates/attribute-view-gui.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
