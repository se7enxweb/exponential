---
title: Custom tags templates
audience: designer, developer
category: templates
order: 640
summary: Custom tags in rich text (factbox, quote, strike, sub, sup and your own): declaring them in content.ini, inline or block, custom attributes, the template named after the tag, and keeping their markup accessible and CSP-safe.
keywords: custom tags, custom tag, CustomTagSettings, AvailableCustomTags, IsInline, CustomAttributes, factbox, quote, rich text custom, ezxmltags custom template
type: help
---
# Custom tags templates

This page is for designers who give editors extra building blocks inside rich text: a fact box, a quote, a call-out, a
formula. A **custom tag** is a named tag in the XML; it is rendered by a template named after it.

## Contents

1. [Declaring a custom tag](#declaring-a-custom-tag)
2. [Its template](#its-template)
3. [Custom attributes](#custom-attributes)
4. [Rules](#rules)

## Declaring a custom tag

`settings/content.ini [CustomTagSettings]` lists the tags; the kernel ships `factbox`, `quote`, `strike`, `sub`, `sup`
(`strike`, `sub` and `sup` with `IsInline[...]=true`). A theme adds its own in its `content.ini.append.php`:

```ini
[CustomTagSettings]
AvailableCustomTags[]=callout
IsInline[callout]=false

[callout]
CustomAttributes[]
CustomAttributes[]=kind
```

Declare custom tags in settings that **every** siteaccess reads (the extension's global settings), not only the public
siteaccess: otherwise the administration's editor drops tags it does not know when content is saved. The starter theme
keeps its custom tags global for exactly this reason (its README).

## Its template

The output handler uses the tag's name as the template name (`initHandlerCustom` in
`kernel/classes/datatypes/ezxmltext/handlers/output/ezxhtmlxmloutput.php`; only plain names are allowed):

```tpl
{* design/mytheme/templates/content/datatype/view/ezxmltags/callout.tpl *}
<aside class="callout callout-{first_set( $kind, 'note' )|wash}">
    {$content}
</aside>
```

Provide the template in a design every siteaccess reaches (for example your extension's `design/standard`), so the
administration's preview can draw it too; the starter theme ships plain renderings of its tags in `design/standard/`.

## Custom attributes

Each name in `CustomAttributes[]` becomes a variable of the template (`$kind` above). Wash attribute values; they are
editor input. Give every attribute a sensible default with `first_set`.

## Rules

- Inline tags (`IsInline`) produce inline markup only (`<span>`, `<sub>`, `<s>`).
- No `style=""` built from attributes: map an attribute to a class instead.
- Semantic elements: `<aside>` for a fact box, `<blockquote>` for a quote, `<s>` for strike.

## Related pages

- [Rich text templates](rich-text-templates.md)
- [Designs and siteaccesses](../theme-design/designs-and-siteaccesses.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
