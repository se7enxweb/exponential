---
title: "By hand: a rich text custom tag"
audience: developer, app-creator, designer
category: extension-development
order: 200
summary: A custom tag for rich text: AvailableCustomTags, a custom attribute with an online editor control, and the ezxmltags template that renders it with $content, verified.
keywords: tutorial, by hand, custom tag, rich text, ezxmltext, AvailableCustomTags, CustomAttributes, IsInline, ezxmltags, online editor, ezoe_attributes.ini, factbox
type: help
---
# By hand: a rich text custom tag

Tutorial 19. Editors write rich text (the `ezxmltext` datatype) with the online editor. A **custom tag** gives them a
block of your own, here a note box with a variant "info" or "warn". The settings follow `settings/content.ini`, whose
own custom tags (`factbox`, `quote`, `strike`, `sub`, `sup`) are configured the same way; the template was rendered by
the template engine.

## 1. Declare the tag

`extension/mysite_hello/settings/content.ini.append.php` (add to the datatype settings of tutorial 14):

```ini
[CustomTagSettings]
AvailableCustomTags[]=mysite_note

[mysite_note]
CustomAttributes[]
CustomAttributes[]=variant
```

A tag used inside a line of text would also get `IsInline[mysite_note]=true` in `[CustomTagSettings]`; this one is a
block.

## 2. A control for the attribute in the online editor

`extension/mysite_hello/settings/ezoe_attributes.ini.append.php`:

```ini
[CustomAttribute_mysite_note_variant]
Name=Variant
Type=select
Selection[]
Selection[info]=Information
Selection[warn]=Warning
Default=info
```

The keys (`Name`, `Type`, `Default`, `Selection[]`) are documented at the top of `extension/ezoe/settings/ezoe_attributes.ini`.

## 3. The template

`extension/mysite_hello/design/standard/templates/content/datatype/view/ezxmltags/mysite_note.tpl`:

```text
{* The custom tag mysite_note: $content is the rendered body, $variant a custom attribute (info, warn). *}
{def $kind = cond( and( is_set( $variant ), eq( $variant, 'warn' ) ), 'warn', 'info' )}
<aside class="mysite-note is-{$kind}" role="note">{$content}</aside>
{undef $kind}
```

The output handler renders the tag with the template named after the tag (it accepts only plain names, see
`initHandlerCustom()` in `kernel/classes/datatypes/ezxmltext/handlers/output/ezxhtmlxmloutput.php`); `$content` is the
already rendered body and each custom attribute is a variable. With `variant="warn"` it rendered
`<aside class="mysite-note is-warn" role="note"><p>Mind the gap.</p></aside>`, and without the attribute the `info`
variant. The variant is never printed directly: it is mapped onto a fixed list, so an editor cannot inject a class.

Style `.mysite-note` in your design's stylesheet, not in the template.

## 4. Use it

```bash
php bin/php/ezcache.php --clear-all
```

Edit an article: the online editor offers the custom tag "mysite_note" with a Variant control. Publish and view the
article.

## Related pages

- Previous: [By hand: an ezjscore server function](by-hand-ajax-server-function.md)
- Next: [By hand: translating an extension](by-hand-translation.md)
- [The content model and editing content](../../../../guides/content-model-and-editing.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
