---
title: Theme assets, the packer and CSP
audience: designer, developer
category: extension-development
order: 60
summary: Load a theme's stylesheets, scripts, images and fonts the way Exponential expects - frontend lists, ezcss_load and ezscript_load, the ezjscore packer, ezimage - so they are fast and pass a CSP.
keywords: assets, stylesheets, scripts, images, fonts, ezcss_load, ezscript_load, ezscript_require, ezimage, packer, ezjscore, FrontendCSSFileList, FrontendJavaScriptList, CSP, DevelopmentMode
type: help
---
# Theme assets, the packer and CSP

Part 6, chapter 6. A theme's look lives in its assets: stylesheets, scripts, images and fonts. This chapter shows how
to load each kind so that ezjscore's packer bundles them into few requests, browsers cache them, and a Content
Security Policy with `'self'` lets them through.

## Stylesheets and scripts: lists and loaders

Register files in the theme's `design.ini.append.php`, by name relative to the design's `stylesheets/` or
`javascript/` folder:

```ini
[StylesheetSettings]
FrontendCSSFileList[]=mytheme.css
FrontendCSSFileList[]=mytheme-print.css

[JavaScriptSettings]
FrontendJavaScriptList[]=mytheme.js
```

The pagelayout loads the lists with ezjscore's template functions:

```text
{ezcss_load( ezini( 'StylesheetSettings', 'FrontendCSSFileList', 'design.ini' ) )}
...
{ezscript_load( array( 'ezjsc::jquery', ezini( 'JavaScriptSettings', 'FrontendJavaScriptList', 'design.ini' ) ) )}
```

| Function | Does |
|---|---|
| `ezcss_load( files )` | prints one `<link>` for the files given plus any collected with `ezcss_require` |
| `ezscript_load( files )` | prints the `<script>` tags for the files given plus any collected with `ezscript_require` |
| `ezcss_require( files )`, `ezscript_require( files )` | collect files from any template, printed later by the `_load` call |
| `ezscript( files )` | prints the script tags right here (used inside page content) |

`ezjsc::jquery` is a packer key: ezjscore resolves it to its jQuery 4 file.

Templates deeper in the page that need a script of their own call `ezscript_require`, as long as the `_load` call
comes later in the page (at the end of `<body>`, as above). Inside page content after that point, use `ezscript`.

## The packer

ezjscore's packer combines the listed files into one file per type, optionally minifies them, and writes the result
to the site's cache folder (`var/<site>/cache/public/`). The bundle's name contains a hash and the newest modification
time of its files, so a changed file gives a new name and browsers fetch it.

| Situation | What happens |
|---|---|
| `site.ini [TemplateSettings] DevelopmentMode=enabled` | files are served one by one, unpacked: good for debugging |
| `DevelopmentMode=disabled` (the default) | files are bundled and packed |
| `ezjscore.ini [eZJSCore] Packer=` | overrides the above when set (`disabled`, `enabled`, or a level 0 to 3) |

After changing an asset on a site with packing on, clear the caches that hold the packed links, then hard-reload the
browser:

```bash
php bin/php/ezcache.php --clear-id=template,template-block
```

Never add `?v=` or other version queries to asset links: the packer's names already change.

## Images

Reference images through the design chain, so a theme on top of yours can replace them:

```text
<img src={'logo.svg'|ezimage} alt="{ezini( 'SiteSettings', 'SiteName' )|wash}" width="160" height="40">
```

`ezimage` finds `images/logo.svg` in the first design that has it and prints the quoted address. In stylesheets,
refer to images relative to the stylesheet (`url( ../images/background.webp )`); the packer rewrites the paths of
bundled stylesheets.

Give every content image `width` and `height` (or an aspect ratio in CSS), so the page does not jump while loading.

## Fonts

Ship font files in the theme (for example `design/mytheme/fonts/`) and load them from your stylesheet with
`@font-face` and a relative URL. Loading fonts from a third-party service sends every visitor's address to that
service and needs its host in the policy; local files need neither. Check the font's license first: most open fonts
use the SIL Open Font License, which allows bundling ([Distributing a theme](distributing-a-theme.md)).

## The Content Security Policy

A policy with `'self'` for scripts and styles allows exactly what the above produces: files from your own site. It
refuses:

| Refused | Instead |
|---|---|
| `<script>` blocks in templates | a file in `javascript/`, values in `data-*` attributes |
| `onclick=` and other event attributes | listeners in your file |
| `style=""` attributes and `<style>` blocks | classes in your stylesheet |
| scripts, styles and fonts from other hosts | local files, or the host added to the policy for that siteaccess |

The kernel's public siteaccesses have the policy off by default; administrators may turn it on
([Content Security Policy](../../../features/6.0/content-security-policy.md)). A theme that already works under it is
one less obstacle. The conversions are explained in Part 4:
[inline scripts](../part-4-upgrading/csp-moving-inline-scripts-out.md),
[event attributes](../part-4-upgrading/csp-event-handlers-to-data-attributes.md),
[inline CSS](../part-4-upgrading/inline-css-to-stylesheets.md).

A small exception you may need: a script that must run before the page is painted (to apply a saved color scheme,
see the next chapter). Make it a tiny file loaded as a normal script in `<head>`, not an inline script.

## Related pages

- Previous: [Themes and siteaccesses](themes-and-siteaccesses.md)
- Next: [Dark mode and design tokens in themes](dark-mode-and-tokens-in-themes.md)
- [CSS and JavaScript in an extension](../part-3-extension-developer/css-and-javascript-in-an-extension.md)
- [ezjscore](../../../features/6.0/extensions/ezjscore.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
