---
title: Rich text templates
audience: designer, developer
category: templates
order: 630
summary: How rich text (the XML text datatype) becomes HTML through one template per tag in content/datatype/view/ezxmltags: paragraph, header, link, table, lists, embed, custom; the variables tag templates receive; classes editors may choose; and overriding a tag for a design.
keywords: rich text, ezxmltext, ezxmltags, paragraph.tpl, header.tpl, link.tpl, table.tpl, tag templates, AvailableClasses, rich text markup, editor classes, output_text
type: help
---
# Rich text templates

This page is for designers who control the markup of rich text: body texts, intros, descriptions. Rich text is stored as
XML; when it is shown, every tag is rendered by a template of its own in `content/datatype/view/ezxmltags/`
(`TemplatesPath` in `kernel/classes/datatypes/ezxmltext/ezxmloutputhandler.php`).

## Contents

1. [The tag templates](#the-tag-templates)
2. [What a tag template receives](#what-a-tag-template-receives)
3. [Changing a tag for your design](#changing-a-tag-for-your-design)
4. [Classes editors can choose](#classes-editors-can-choose)
5. [Headings](#headings)

## The tag templates

`design/standard/templates/content/datatype/view/ezxmltags/` has, among others: `paragraph.tpl`, `header.tpl`,
`strong.tpl`, `emphasize.tpl`, `link.tpl`, `anchor.tpl`, `ul.tpl`, `ol.tpl`, `li.tpl`, `table.tpl`, `tr.tpl`, `td.tpl`,
`th.tpl`, `literal.tpl`, `line.tpl`, `embed.tpl`, `embed-inline.tpl`, and the custom tags `factbox.tpl`, `quote.tpl`,
`strike.tpl`, `sub.tpl`, `sup.tpl`.

## What a tag template receives

- `$content`: the already rendered inner HTML (print it without `wash`).
- `$classification`: the class the editor chose.
- `$align` where the tag supports alignment.
- tag-specific values: `$href`, `$title`, `$target` for links; `$level` for headings; custom attributes for custom tags.

The shipped `paragraph.tpl`:

```tpl
{set $classification = cond( and( is_set( $align ), $align ), concat( $classification, ' text-', $align ), $classification )}
<p{if $classification|trim} class="{$classification|wash}"{/if}>
{if eq( $content|trim(), '' )}&nbsp;{else}{$content}{/if}
</p>
```

## Changing a tag for your design

Copy the tag template into your design at the same path, or use an override on
`Source=content/datatype/view/ezxmltags/<tag>.tpl` (with `Match[class_identifier]` to limit it to one class). For
example a table wrapped in a scroll container:

```tpl
{* design/mytheme/templates/content/datatype/view/ezxmltags/table.tpl *}
<div class="table-scroll">
<table{if $classification|trim} class="{$classification|wash}"{/if}>
{$rows}
</table>
</div>
```

(Read the original first: each tag receives its own variables; the table template receives the rendered rows.)

The kernel's `factbox.tpl` still uses the old `bgcolor` attribute, and `table.tpl` prints a `style="width: ..."` when an
editor set a table width: a design for a site with a strict Content Security Policy (`style-src 'self'`) overrides both
with classed elements.

## Classes editors can choose

`content.ini [<tag>] AvailableClasses[]` lists the classes editors may pick for a tag in the online editor, for example:

```ini
[paragraph]
AvailableClasses[]=lead
AvailableClasses[]=note
```

Style each class in your stylesheet; the paragraph template puts it into `class=""`.

## Headings

`header.tpl` maps rich text levels to HTML: level 1 becomes `h2`, level 2 `h3` ... (the full view's own `h1` is the
page title). It also prints `eztoc` anchors for tables of contents. Keep this mapping, or the page gets two `h1`.

## Related pages

- [Custom tags templates](custom-tags-templates.md)
- [Embed view](embed-view.md)
- [Datatype view templates](datatype-view-templates.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
