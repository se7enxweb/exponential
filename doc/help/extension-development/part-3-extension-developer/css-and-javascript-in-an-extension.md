---
title: CSS and JavaScript in an extension
audience: developer, app-creator, designer
category: extension-development
order: 230
summary: Load stylesheets and scripts the way the kernel expects: design.ini lists, ezscript_require and ezcss_require, the packer and its cache, CSP-safe scripts, no version queries.
keywords: css, javascript, stylesheet, script, design.ini, FrontendCSSFileList, BackendCSSFileList, FrontendJavaScriptList, BackendJavaScriptList, ezscript_require, ezcss_require, ezcss_load, ezscript_load, packer, ezjscore, data attributes, CSP
type: help
---
# CSS and JavaScript in an extension

Part 3, chapter 23. An extension's look and behavior in the browser come from files in its design:
`stylesheets/` and `javascript/`. They are loaded through settings and bundled by the **ezjscore packer**, never
written into templates. This chapter shows the ways to load them and the rules a Content Security Policy imposes.

## Files

```text
extension/mysite_hello/design/standard/stylesheets/mysite_hello.css
extension/mysite_hello/design/standard/javascript/mysite_hello.js
extension/mysite_hello/design/admin/stylesheets/mysite_hello_admin.css
```

## Load on every page: design.ini lists

`extension/mysite_hello/settings/design.ini.append.php` (the `expqueue` extension does exactly this for its admin
files):

```php
<?php /* #?ini charset="utf-8"?

[ExtensionSettings]
DesignExtensions[]=mysite_hello

[StylesheetSettings]
FrontendCSSFileList[]=mysite_hello.css
BackendCSSFileList[]=mysite_hello_admin.css

[JavaScriptSettings]
FrontendJavaScriptList[]=mysite_hello.js

*/ ?>
```

| List | Loaded by |
|---|---|
| `FrontendCSSFileList[]`, `FrontendJavaScriptList[]` | site designs (their pagelayout calls `ezcss_load` and `ezscript_load` with these lists) |
| `BackendCSSFileList[]`, `BackendJavaScriptList[]` | the administration designs |
| `CSSFileList[]`, `JavaScriptList[]` | deprecated: both; use the two lists above |
| `AdminComponentCSSFileList[]` | the administration's own shared components; extensions add to `BackendCSSFileList[]` instead, as `design.ini` says |

The file names are paths below `stylesheets/` or `javascript/` of a design base; the packer finds them through the
design chain.

## Load on one page: require

When only one template needs a file, ask for it there:

```text
{ezscript_require( 'mysite_hello.js' )}
{ezcss_require( 'mysite_hello.css' )}
```

The page head prints the required files with the rest (the pagelayout's `ezscript_load` and `ezcss_load` collect them).
The `cjw_newsletter` and `cjw_themes_jumper` extensions use this for scripts that only some pages need.

## The packer

ezjscore combines and compresses the listed files into one stylesheet and one script per page type, stores them
under the cache, and gives them a name with the modification time (`[Packer] AppendLastModifiedTime=enabled` in
`ezjscore.ini`), so browsers fetch a new copy after a change. Consequences:

- **Never add `?v=...` to an asset link.** The time in the name already does it.
- After changing a stylesheet or script, clear the packer cache and the template block cache (the admin pagelayout keeps
  the link in a cache block): `php bin/php/ezcache.php --clear-id=ezjscore-packer,template-block`, or
  `./console exp:velocity deploy --packer`.
- While developing, `[TemplateSettings] DevelopmentMode=enabled` (or `[eZJSCore] Packer=disabled` in `ezjscore.ini`)
  serves the files one by one, which is easier to debug.

## Scripts that work under a Content Security Policy

The administration sends a Content Security Policy that allows scripts only from files of the site (and a per-request
nonce). So:

| Not allowed | Instead |
|---|---|
| `<script>...</script>` in a template | a file in `javascript/`, loaded as above |
| `onclick="..."`, `onsubmit="..."`, any `on...=` | a `data-*` attribute, and a listener in your file |
| `href="javascript:..."` | a button with a `data-*` attribute |
| `eval()`, `new Function()`, string timers | normal functions |
| `style="..."`, `<style>` | a class, styled in your stylesheet |

The `expqueue` extension's `expqueue_confirm.js` is a complete example: the template's form carries
`data-a4-confirm="<question>"`, and the script, loaded through `BackendJavaScriptList[]`, listens for `submit`:

```javascript
(function ( window, document ) {
    'use strict';
    document.addEventListener( 'submit', function ( e ) {
        var form = e.target;
        if ( !form || !form.hasAttribute || !form.hasAttribute( 'data-a4-confirm' ) )
            return;
        if ( !window.confirm( form.getAttribute( 'data-a4-confirm' ) ) )
            e.preventDefault();
    } );
})( window, document );
```

(Shortened from the real file, which also defers to admin4's own handler.) Values go from template to script through
data attributes, escaped: `data-name="{$name|wash}"`.

## jQuery and expui

The administration runs jQuery 4 and the `Exp` namespace of the `expui` extension; YUI was removed. Use those rather
than bringing your own copy of a library; see [The expui JavaScript API](../part-8-reference/expui-javascript-api.md)
and [From old jQuery to jQuery 4](../part-4-upgrading/old-jquery-to-jquery-4.md).

## Styling admin pages

In the administration, reuse the shared component classes (`.a4-card`, `.a4-btn`, `.a4-table` ...) and the design
tokens before writing CSS of your own; see [Admin pages in the admin4 style](admin-pages-in-admin4-style.md).

## Related pages

- Previous: [A design inside an extension](design-in-an-extension.md)
- Next: [Ajax with ezjscore server functions](ajax-with-ezjscore-server-functions.md)
- [Content Security Policy for extension authors](content-security-policy-for-extensions.md)
- [Theme assets, the packer and CSP](../part-6-theme-developers/theme-assets-the-packer-and-csp.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
