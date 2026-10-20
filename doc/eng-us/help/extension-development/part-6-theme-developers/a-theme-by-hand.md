---
title: A theme by hand
audience: designer, developer
category: extension-development
order: 40
summary: Build the smallest complete theme file by file - metadata, the design registration, a siteaccess, a pagelayout, a stylesheet and an override - and check each step, so you know what every file of a theme does.
keywords: theme, by hand, pagelayout.tpl, design.ini, DesignExtensions, FrontendCSSFileList, siteaccess, override.ini, ezcss_load, ezscript_load, module_result
type: help
---
# A theme by hand

Part 6, chapter 4. The wizard of the previous chapter is fast; writing a theme by hand once teaches you what every
file is for. This chapter builds `mytheme`, the smallest theme that changes a whole site, in six files. Each sample
was checked: the pagelayout was rendered through the template engine, and the INI sections and settings exist in
the kernel's `design.ini` and `site.ini`.

## The files

```text
extension/mytheme/
  ezinfo.php
  extension.xml
  settings/design.ini.append.php
  settings/siteaccess/mysite/site.ini.append.php
  design/mytheme/templates/pagelayout.tpl
  design/mytheme/stylesheets/mytheme.css
```

Replace `mysite` with the name of your public siteaccess.

## 1. Who you are: ezinfo.php and extension.xml

Write them as [ezinfo.php](../part-3-extension-developer/ezinfo-php.md) and
[extension.xml](../part-3-extension-developer/extension-xml.md) describe: a readable name, version `1.0.0`, your
copyright, the license, a website.

## 2. Register the design: settings/design.ini.append.php

```ini
<?php /* #?ini charset="utf-8"?

[ExtensionSettings]
DesignExtensions[]=mytheme

[StylesheetSettings]
FrontendCSSFileList[]=mytheme.css

*/ ?>
```

`DesignExtensions[]` adds `extension/mytheme/design/` to the folders where designs are looked for.
`FrontendCSSFileList[]` names the stylesheet for the pagelayout to load.

## 3. Use the design: the siteaccess's site.ini

`settings/siteaccess/mysite/site.ini.append.php`:

```ini
<?php /* #?ini charset="utf-8"?

[DesignSettings]
SiteDesign=mytheme
AdditionalSiteDesignList[]
AdditionalSiteDesignList[]=base

*/ ?>
```

`SiteDesign` is searched first; then the designs of `AdditionalSiteDesignList[]`; the kernel's `StandardDesign`
(`standard`) last. The empty `AdditionalSiteDesignList[]` line clears what the siteaccess had before, so the list is
exactly what you wrote. More in [Themes and siteaccesses](themes-and-siteaccesses.md).

## 4. The frame: design/mytheme/templates/pagelayout.tpl

```text
<!DOCTYPE html>
<html lang="{ezini( 'RegionalSettings', 'ContentObjectLocale' )|extract_left( 2 )|wash}">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>{ezini( 'SiteSettings', 'SiteName' )|wash}</title>
{ezcss_load( ezini( 'StylesheetSettings', 'FrontendCSSFileList', 'design.ini' ) )}
</head>
<body class="mytheme">
<header class="mytheme-header">
  <a class="mytheme-logo" href={'/'|ezurl}>{ezini( 'SiteSettings', 'SiteName' )|wash}</a>
</header>
<main id="content" class="mytheme-content">
{$module_result.content}
</main>
<footer class="mytheme-footer">
  <p>{currentdate()|datetime( 'custom', '%Y' )} {ezini( 'SiteSettings', 'SiteName' )|wash}</p>
</footer>
{ezscript_load( array( 'ezjsc::jquery', ezini( 'JavaScriptSettings', 'FrontendJavaScriptList', 'design.ini' ) ) )}
</body>
</html>
```

What each part does:

| Line | Purpose |
|---|---|
| `ezini( 'RegionalSettings', 'ContentObjectLocale' )` | the site's locale (for example `eng-US`); `extract_left( 2 )` gives the language for `lang` |
| `ezcss_load( ... )` | ezjscore's function: bundles the listed stylesheets into one file and prints its `<link>` |
| `{'/'\|ezurl}` | the address of the site's front page, already quoted |
| `$module_result.content` | the output of the module that answered the request: the page's main content |
| `ezscript_load( ... )` | bundles and prints the scripts, here jQuery from ezjscore and your frontend list |

Every value printed into HTML goes through `wash`.

> **Note**
> Rendered outside a real request, this pagelayout prints everything but the content, and the template engine
> reports that `$module_result` is unknown. In a page request the kernel sets it.

## 5. The look: design/mytheme/stylesheets/mytheme.css

```css
:root {
    --mytheme-ink: #1f2328;
    --mytheme-surface: #ffffff;
    --mytheme-accent: #0b6bcb;
    --mytheme-space: 1rem;
}
body.mytheme { margin: 0; color: var(--mytheme-ink); background: var(--mytheme-surface); font-family: system-ui, sans-serif; }
.mytheme-header, .mytheme-footer { padding: var(--mytheme-space); }
.mytheme-content { max-width: 60rem; margin: 0 auto; padding: var(--mytheme-space); }
.mytheme-logo { color: var(--mytheme-accent); font-weight: 700; text-decoration: none; }
```

Variables first: the chapter [Dark mode and design tokens in themes](dark-mode-and-tokens-in-themes.md) builds on
them.

## 6. Switch it on and look

```bash
./console exp:ini add 'site.ini/ExtensionSettings/ActiveExtensions[]' mytheme global
php bin/php/ezcache.php --clear-all
```

Open the site: your header, the content as before, your footer, your colors. Every content page still uses the
templates of `base` and `standard` inside your frame. From here, replace them one at a time
([A theme with the RAD wizard](a-theme-with-the-rad-wizard.md), section "From skeleton to theme").

## Checking which template was used

Turn on template debugging for your development siteaccess (`site.ini [TemplateSettings] ShowUsedTemplates=enabled`
with the debug output on). The debug output then lists every template the page used and the design it came from
([Debugging templates](../part-2-builder/debugging-templates.md)).

## Related pages

- Previous: [A theme with the RAD wizard](a-theme-with-the-rad-wizard.md)
- Next: [Themes and siteaccesses](themes-and-siteaccesses.md)
- [The design chain](../part-2-builder/the-design-chain.md)
- [Siteaccess settings](../part-2-builder/siteaccess-settings.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
