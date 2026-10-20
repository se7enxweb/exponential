---
title: The ezini operator
audience: designer, developer
category: templates
order: 270
summary: Reading settings in templates with ezini and ezini_hasvariable: section, setting, file and directory parameters, arrays from INI lists, a theme's own settings file, and when settings in templates are the right tool.
keywords: ezini, ezini_hasvariable, settings in templates, ini file, read setting, theme settings, design.ini, site.ini, own ini file, configuration
type: help
---
# The ezini operator

This page is for designers who read settings in templates: the site name, a list of stylesheets, a theme option.

## Contents

1. [The call](#the-call)
2. [Testing first](#testing-first)
3. [Lists](#lists)
4. [A settings file for your theme](#a-settings-file-for-your-theme)
5. [When to use settings](#when-to-use-settings)

## The call

```tpl
{ezini( 'SiteSettings', 'SiteName' )}                         {* site.ini when no file is given *}
{ezini( 'StylesheetSettings', 'CSSFileList', 'design.ini' )}
{ezini( 'NodeSettings', 'RootNode', 'content.ini' )}
```

Parameters (`kernel/common/ezurloperator.php`): section, setting, file (default `site.ini`), and an optional directory.
The value is read for the current siteaccess, with every override applied.

## Testing first

Reading a setting that does not exist is a template warning. When a setting is optional (for example one your theme
adds, which an installation may not have yet):

```tpl
{if ezini_hasvariable( 'SiteInfo', 'PrivacyPolicyID', 'menu.ini' )}
    ...
{/if}
```

The media design's `pagelayout/head/script.tpl` does this before reading its privacy page setting.

## Lists

An INI list (`Setting[]=a`, `Setting[]=b`) comes back as an array, so it can go straight into an operator that takes
arrays:

```tpl
{ezcss_load( array( 'mytheme.css', ezini( 'StylesheetSettings', 'FrontendCSSFileList', 'design.ini' ) ) )}
{if ezini( 'Header', 'LandingClasses', 'mytheme.ini' )|contains( $node.class_identifier )} ... {/if}
```

## A settings file for your theme

Design choices that may differ per site belong in a settings file of the theme, not in templates. The starter theme
has `cjw_jumper.ini` with `[Header]`, `[Menu]` and `[LayoutSettings]`, read by its templates with
`ezini( ..., 'cjw_jumper.ini' )`. Your theme's `settings/mytheme.ini.append.php` with defaults, overridden per
siteaccess, works the same way ([Giving a theme its own settings file](../extensions/your-own-ini-file.md)).

## When to use settings

- Yes: ids of special content (by remote id), lists of classes, on/off switches, file lists.
- No: texts (use `i18n`), anything secret (templates must never print secrets), values that change per request.

## Related pages

- [Operators overview](operators-overview.md)
- [The ezhttp operator](ezhttp-operator.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
