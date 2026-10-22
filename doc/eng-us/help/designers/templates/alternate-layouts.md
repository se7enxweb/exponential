---
title: Alternate layouts (print, popup and your own)
audience: designer, developer
category: templates
order: 728
summary: Show any page in another page layout with the address prefix layout/set/<name> - the layouts layout.ini defines, how to add your own with PageLayout, ContentType, UseAccessPass and UseFullUrl, the layout override key, and a print link.
keywords: layout/set, alternate layout, print layout, print_pagelayout.tpl, popup, fullscreen, layout.ini, PageLayout, ContentType, UseAccessPass, UseFullUrl, Match[layout], printer friendly
type: help
---
# Alternate layouts (print, popup and your own)

This page is for designers who need the same content in another frame: a printer-friendly page, a page for a small
popup window, or a bare fragment for an Ajax call. Put `layout/set/<name>/` in front of any address, and the page is
rendered with the page layout that `layout.ini` names for `<name>` instead of `pagelayout.tpl`.

## Contents

- [The layouts that ship](#the-layouts-that-ship)
- [Linking to a layout](#linking-to-a-layout)
- [Adding a layout of your own](#adding-a-layout-of-your-own)
- [Different templates inside the layout](#different-templates-inside-the-layout)
- [Things to keep in mind](#things-to-keep-in-mind)

## The layouts that ship

`settings/layout.ini` defines three, each with a page layout template in `design/standard/templates`:

| Name | `PageLayout` | Prints |
|---|---|---|
| `print` | `print_pagelayout.tpl` | the content and the copyright line, with the site's style sheets and scripts, without navigation and without the print link in the head |
| `popup` | `popup_pagelayout.tpl` | the content and the copyright line with the core style sheet only, for a small window |
| `fullscreen` | `fullscreen_pagelayout.tpl` | the content and the copyright line without the menus of the page frame |

`/layout/set/print/content/view/full/96` and `/layout/set/print/news/my-article` show the same page in the print
layout. Your design can override the three templates like any other.

## Linking to a layout

```
<a href={concat( 'layout/set/print/', $node.url_alias )|ezurl} rel="nofollow">
    {'Print this page'|i18n( 'design/mysite/full' )}
</a>
```

While a page is shown through `layout/set/<name>`, links made with `ezurl` keep the prefix, so the visitor stays in
the layout as they move on. Set `UseAccessPass=false` for a layout where that is not wanted.

## Adding a layout of your own

Add a block to `layout.ini` in an extension or an override file, named after the layout:

```ini
# extension/mysite/settings/layout.ini.append.php
[plain]
PageLayout=plain_pagelayout.tpl
UseAccessPass=false

[feed]
PageLayout=feed_pagelayout.tpl
ContentType=application/xml
```

| Setting | Effect |
|---|---|
| `PageLayout` | the page layout template, looked up in `templates/` of the design chain |
| `ContentType` | sent as the `Content-Type` header, with the character set of the site added |
| `UseAccessPass` | `false` keeps `layout/set/<name>` out of links made on the page |
| `UseFullUrl` | `true` makes links made on the page absolute, with the host name |

A minimal page layout prints at least the result of the view:

```
{* design/mysite/templates/plain_pagelayout.tpl *}
<!DOCTYPE html>
<html lang="{$site.http_equiv.Content-language|wash}">
<head>
    <meta charset="utf-8" />
    <title>{$site.title|wash}</title>
</head>
<body>
{$module_result.content}
</body>
</html>
```

A name that `layout.ini` does not define gives an empty page and the error "No such layout style" in the debug
output.

## Different templates inside the layout

The view sets the override key `layout` to the name of the layout, so a rule can give a node a template of its own in
one layout only:

```ini
# settings/siteaccess/<site>/override.ini.append.php
[article_print]
Source=node/view/full.tpl
MatchFile=print/article.tpl
Subdir=templates
Match[class_identifier]=article
Match[layout]=print
```

Without such a rule, the normal full view template is used inside the print layout; a print style sheet
([Print stylesheets](../theme-design/print-stylesheets.md)) is often all that is needed.

## Things to keep in mind

- **Caching.** The name of the layout is part of the view cache key, so the print version and the normal page are
  cached separately and both are cleared when the content changes.
- **Search engines.** Print versions duplicate content. Mark the links `rel="nofollow"` and give the print layout a
  `<meta name="robots" content="noindex" />`.
- **No permissions of its own.** `layout/set` is open to everyone; the page inside it checks its own permissions as
  usual.
- **Not for the administration.** The layouts change the frame of the public site; the administration designs have
  their own page layouts.

## Related pages

- [The pagelayout](../theme-design/the-pagelayout.md)
- [Pagelayout variables](../theme-design/pagelayout-variables.md)
- [Override match keys](override-match-keys.md)
- [Templates and design](../../../guides/templates-and-design.md)
- [Module view templates](../../template-language/module-view-templates.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
