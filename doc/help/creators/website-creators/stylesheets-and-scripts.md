---
title: Stylesheets and scripts for your site
audience: website-creator, developer
category: website-creation
order: 140
summary: Add your CSS and JavaScript the Exponential way - lists in design.ini, ezcss_load and ezscript_load, the ezjscore packer, cache busting, and keeping the site ready for a Content Security Policy.
keywords: stylesheet, CSS, JavaScript, scripts, design.ini, CSSFileList, JavaScriptList, ezcss_load, ezscript_load, ezjscore, packer, jQuery, cache, CSP, website creator
type: help
---
# Stylesheets and scripts for your site

This page is for website creators who add their own CSS and JavaScript to a design. In Exponential they are not linked
by hand in the pagelayout: they are listed in `design.ini` and loaded by the `ezjscore` packer, which combines them,
minifies them and gives each version its own file name.

## Contents

1. [The lists in design.ini](#the-lists-in-designini)
2. [Loading them in the pagelayout](#loading-them-in-the-pagelayout)
3. [The packer](#the-packer)
4. [After a change](#after-a-change)
5. [Scripts that work under a Content Security Policy](#scripts-that-work-under-a-content-security-policy)
6. [Checklist](#checklist)

## The lists in design.ini

In your design extension, `settings/design.ini.append.php`:

```ini
<?php /* #?ini charset="utf-8"?
[ExtensionSettings]
DesignExtensions[]=mysite_design

[StylesheetSettings]
CSSFileList[]=main.css
CSSFileList[]=print.css

[JavaScriptSettings]
JavaScriptList[]=ezjsc::jquery
JavaScriptList[]=main.js
*/ ?>
```

| Setting | Holds |
|---|---|
| `[StylesheetSettings] CSSFileList[]` | stylesheets, found in `stylesheets/` of the design chain |
| `[StylesheetSettings] FrontendCSSFileList[]` | more stylesheets for the public site |
| `[JavaScriptSettings] JavaScriptList[]` | scripts, found in `javascript/` of the design chain |
| `[JavaScriptSettings] FrontendJavaScriptList[]` | more scripts for the public site |

`ezjsc::jquery` is ezjscore's own jQuery, so you do not ship a copy. The shipped simple theme is a real example:
`extension/sevenx_themes_simple/settings/design.ini.append.php`.

## Loading them in the pagelayout

```text
{ezcss_load( array( ezini( 'StylesheetSettings', 'CSSFileList', 'design.ini' ),
                    ezini( 'StylesheetSettings', 'FrontendCSSFileList', 'design.ini' ) ) )}
...
{ezscript_load( array( ezini( 'JavaScriptSettings', 'JavaScriptList', 'design.ini' ),
                       ezini( 'JavaScriptSettings', 'FrontendJavaScriptList', 'design.ini' ) ) )}
```

A template that needs one more file for itself asks for it with `{ezcss_require( 'gallery.css' )}` or
`{ezscript_require( 'gallery.js' )}`; the pagelayout's `ezcss_load`/`ezscript_load` then includes it. The four
functions are defined in `extension/ezjscore/autoloads/ezjscpackertemplatefunctions.php`.

## The packer

The packer is set in `extension/ezjscore/settings/ezjscore.ini`:

| Setting | Meaning |
|---|---|
| `[eZJSCore] Packer` | normally follows `[TemplateSettings] DevelopmentMode`; set `disabled` or a level `0` to `3` to force it |
| `[Packer] AppendLastModifiedTime=enabled` | the file's modification time is part of the packed file's name, so browsers fetch a new copy after a change |
| `[Packer] CustomHosts[.css]`, `[.js]` | serve packed files from another host |
| `JavaScriptOptimizer[]`, `CssOptimizer[]` | the minifiers |

With the packer on, the page links one combined file per list; with it off (development), each file separately, which
is easier to debug.

## After a change

Because packed files and the pagelayout's cache blocks remember the old file names, a stylesheet change needs:

```bash
php bin/php/ezcache.php --clear-id=ezjscore-packer --allow-root-user
php bin/php/ezcache.php --clear-tag=template --allow-root-user
php bin/php/ezcache.php --clear-id=template-block --allow-root-user
```

Then reload the page with a hard reload. Do not add `?v=...` to links; the modification time in the file name already
does that.

## Scripts that work under a Content Security Policy

The shipped policy, when you turn it on for a site, allows script files and stylesheet files of the site itself only
(`script-src 'self'`, `style-src 'self'`): no inline `<script>` code, no `on...=` attributes, no `style=""`
([Content Security Policy](../../../features/6.0/content-security-policy.md)). Write the site that way from the start:

| Instead of | Write |
|---|---|
| `<a onclick="openGallery()">` | `<a class="js-gallery">` and `addEventListener` in `main.js` |
| `<script>var root = "...";</script>` | a `<script type="application/json">` data block read by `main.js` ([The pagelayout](the-pagelayout.md#scripts-without-inline-code)) |
| `<div style="width: 40%">` | a CSS class, or a `data-*` attribute your script reads |
| a script from another host | a copy in your design, or `AdditionalSources[]=script-src https://<host>` in `[ContentSecurityPolicy]` |

## Checklist

- [ ] Every CSS and JS file is listed in `design.ini`, none is linked by hand.
- [ ] No inline scripts, `on...` attributes or `style=""` in your templates.
- [ ] The packer is on for production (`DevelopmentMode` off).
- [ ] After a change: packer, template and template-block caches cleared, hard reload.
- [ ] Third-party scripts and fonts are reviewed for privacy and allowed in the policy.

## Related pages

- [The pagelayout: your site's frame](the-pagelayout.md)
- [ezjscore](../../../features/6.0/extensions/ezjscore.md)
- [jQuery 4 and the removal of YUI](../../../features/6.0/jquery4-and-yui-removal.md)
- Content Security Policy for your code
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
