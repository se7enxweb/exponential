---
title: The ezwebin design as a base
audience: designer
category: theme-design
order: 140
summary: Using the classic ezwebin design (extension ezwebin) as a fallback under your own design: its page parts, the ezpagedata operators that let view templates steer the frame, the menu settings, and how to override only what you need.
keywords: ezwebin, website interface, base design, fallback design, ezpagedata, ezpagedata_set, ezpagedata_append, page_header, page_leftmenu, menu.ini, SelectedMenu, template_look, classic design
type: help
---
# The ezwebin design as a base

This page is for designers who build on the ezwebin design ("Exponential Website Interface LS", extension `ezwebin`),
either directly or through the simple design, which uses the same classes. It answers: how the ezwebin frame is cut
into parts, how a view template can change the frame with `ezpagedata_set`, and how to override parts in your
design. The extension's own page is [ezwebin](../../../features/6.0/extensions/ezwebin.md).

## Contents

1. [The page parts](#the-page-parts)
2. [ezpagedata: the page facts](#ezpagedata-the-page-facts)
3. [Steering the frame from a view template](#steering-the-frame-from-a-view-template)
4. [Menus from menu.ini](#menus-from-menuini)
5. [Overriding parts in your design](#overriding-parts-in-your-design)
6. [When not to use it](#when-not-to-use-it)

## The page parts

`extension/ezwebin/design/ezwebin/templates/` cuts the frame into small parts, each replaceable on its own:

| Template | Draws |
|---|---|
| `pagelayout.tpl` | the frame, includes the parts below |
| `page_head.tpl`, `page_head_style.tpl`, `page_head_script.tpl` | the head, stylesheets, scripts |
| `page_header.tpl`, `page_header_logo.tpl`, `page_header_searchbox.tpl`, `page_header_links.tpl`, `page_header_languages.tpl` | the header and its pieces |
| `page_topmenu.tpl`, `page_leftmenu.tpl`, `page_extramenu.tpl` | the menus |
| `page_toppath.tpl` | the breadcrumb |
| `page_mainarea.tpl` | the content area |
| `page_toolbar.tpl` | the website toolbar for signed-in editors |
| `page_footer.tpl`, `page_footer_script.tpl` | the footer |

## ezpagedata: the page facts

The extension registers the operators `ezpagedata`, `ezpagedata_set` and `ezpagedata_append`
(`extension/ezwebin/autoloads/ezpagedata.php`). `ezpagedata()` returns a hash the frame uses, for example:

| Key | Meaning |
|---|---|
| `node_id`, `class_identifier` | the current node and its class |
| `is_edit` | whether the page is an edit page |
| `show_path` | whether to show the breadcrumb |
| `website_toolbar` | whether to show the toolbar (signed-in user on a content page) |
| `top_menu`, `left_menu`, `extra_menu` | which menus to show (from `menu.ini`) |
| `template_look` | the "template look" object (logo, site settings editors maintain) |
| `css_classes` | classes for the page wrapper: `sidemenu`/`nosidemenu`, `extrainfo`/`noextrainfo`, `section_id_<n>` ... |
| `persistent_variable` | what view templates handed over |

The operator's own comment notes that its functions are not cache-block safe: call it outside a cache-block or
include its result in the keys.

## Steering the frame from a view template

A full view can tell the frame to hide the left menu or the breadcrumb, without the pagelayout knowing the class:

```tpl
{* in override/templates/full/landing_page.tpl of your design *}
{ezpagedata_set( 'left_menu', false() )}
{ezpagedata_set( 'show_path', false() )}
```

`ezpagedata_set` writes into the persistent variable of the view, which reaches the pagelayout through
`$module_result.content_info.persistent_variable`; `ezpagedata()` reads it there. `ezpagedata_append` adds to a list
instead of replacing it. See The persistent variable.

## Menus from menu.ini

`ezpagedata` reads `menu.ini [SelectedMenu] TopMenu` and `LeftMenu`, and `[MenuSettings] HideLeftMenuClasses[]`
(classes whose pages get no left menu). Changing the menu type of a site is a settings change in your siteaccess's
`menu.ini.append.php`, not a template change.

## Overriding parts in your design

Put your version at the same path in your design, which sits above `ezwebin` in the chain:

```
extension/mytheme/design/mytheme/templates/page_header_logo.tpl
extension/mytheme/design/mytheme/templates/page_footer.tpl
```

and list `ezwebin` after your design:

```ini
[DesignSettings]
SiteDesign=mytheme
AdditionalSiteDesignList[]
AdditionalSiteDesignList[]=ezwebin
AdditionalSiteDesignList[]=standard
AdditionalSiteDesignList[]=base
```

Start with the logo, the footer and the stylesheet; that is often all a branded site needs.

## When not to use it

- Your site does not use the ezwebin content classes: most of its templates would never run.
- You need a strict Content Security Policy on the public site: audit its templates first for inline scripts.
- You start a design system with tokens and dark mode: the ezwebin stylesheets predate them, so a fresh pagelayout may
  be less work than overriding them.

## Related pages

- [A tour of the simple theme](simple-theme-tour.md)
- [The design cascade](the-design-cascade.md)
- Menus and navigation
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
