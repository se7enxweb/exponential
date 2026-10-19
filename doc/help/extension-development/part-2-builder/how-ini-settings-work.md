---
title: How INI settings work
audience: developer, designer, app-creator
category: extension-development
order: 10
summary: The INI files that configure Exponential - files, blocks, variables, arrays and hashes, comments, the .ini.append.php form, how values from many files are merged, and the INI cache that must be cleared after a change.
keywords: ini, settings, block, variable, array, hash, reset, append, override, merge, comment, cache, clear-tag ini, eZINI
type: help
---
# How INI settings work

Part 2, chapter 1. Almost every behavior of Exponential is decided by a setting in an INI file. This chapter explains
the format and the merge rules exactly, because nearly every "my change has no effect" problem comes from one of them.

## Files

The kernel's default settings are in `settings/*.ini`: `site.ini` (the main one), `content.ini`, `design.ini`,
`image.ini`, `module.ini`, `override.ini`, `template.ini`, `workflow.ini`, `cronjob.ini` and dozens more:

```bash
ls settings/*.ini | wc -l
ls settings/*.ini
```

You **never edit these files**. They are replaced at every upgrade. Instead, you add files with the same name and the
suffix `.ini.append.php` in a place that is read after them (the next chapter lists the places). Each such file adds
to or changes the default.

## Blocks and variables

A file holds **blocks** (sections) in square brackets, and **variables** inside them:

```ini
[SiteSettings]
SiteName=My site
DefaultPage=/content/view/full/2

[DebugSettings]
DebugOutput=disabled
```

A setting is named by file, block and variable: "`site.ini`, `[SiteSettings]`, `SiteName`", written in this book as
`site.ini [SiteSettings] SiteName` or, for the settings command, `site.ini/SiteSettings/SiteName`.

Switches are usually `enabled`/`disabled`, sometimes `true`/`false`. Use the form the default file uses; code compares
the exact string.

## Arrays

A variable written with `[]` is an array. Each line adds one value:

```ini
[ExtensionSettings]
ActiveExtensions[]=ezjscore
ActiveExtensions[]=ezoe
```

A later file **adds** to an array. To **replace** it, start with an empty `[]` line, which empties what earlier files
set:

```ini
[RegionalSettings]
SiteLanguageList[]
SiteLanguageList[]=eng-US
SiteLanguageList[]=ger-DE
```

Forgetting the reset line is the most common reason an array has values you did not expect.

## Hashes

A variable written with a key in brackets is a hash (an associative array), for example in `exphelp.ini`:

```ini
[AudienceSettings]
AudienceNames[]
AudienceNames[editor]=Editors
AudienceNames[admin]=Administrators
```

A later file can change one key (`AudienceNames[editor]=Content editors`) or empty the whole hash with the reset line.

## Comments

A line starting with `#` is a comment. The kernel's default files are full of them: read them, they are the best
documentation of each setting.

```bash
grep -n -B5 "^ShowUsedTemplates=" settings/site.ini
```

## The .ini.append.php form

Override files are PHP files so that a web server never sends their contents to a browser, even if someone requests
them directly. The INI text sits inside a PHP comment:

```php
<?php /* #?ini charset="utf-8"?

[SiteSettings]
SiteName=My site

*/ ?>
```

The first line opens a comment and declares the charset; the last closes it. Everything between is plain INI. A file
called `site.ini.append` (without `.php`) is still read, but the kernel marks that suffix as deprecated
(`lib/ezutils/classes/ezini.php`), and it could be served as text by a web server: always use `.ini.append.php`.

## How many files make one value

For one request the kernel reads `settings/site.ini` and then every `site.ini.append.php` of the active scopes, in a
fixed order. Each later file:

- **replaces** a plain variable,
- **adds** to an array (unless it resets it),
- **adds or replaces** keys of a hash.

The order is the subject of the next chapter. To see it for any setting:

```bash
./console exp:ini where site.ini/SiteSettings/SiteName
```

## Settings from PHP and templates

Code reads settings with `eZINI`:

```php
<?php
$ini = eZINI::instance( 'site.ini' );
$name = $ini->variable( 'SiteSettings', 'SiteName' );
if ( $ini->hasVariable( 'MySettings', 'Limit' ) )
{
    $limit = (int)$ini->variable( 'MySettings', 'Limit' );
}
```

Templates read them with the `ezini` operator:

```text
{ezini( 'SiteSettings', 'SiteName', 'site.ini' )|wash}
```

Both are covered in detail in [eZINI: reading settings](../part-8-reference/ezini.md).

## The INI cache

Parsing dozens of files on every request would be slow, so the merged result is cached as PHP files (by default under
`var/cache/ini/`). After you change a settings file by hand, clear that cache:

```bash
php bin/php/ezcache.php --clear-tag=ini
```

`./console exp:ini` and **Setup > Ini settings** clear it for you after a write. Use `--clear-tag=ini`, not
`--clear-id=ini`: the tag also clears the global INI cache. An installation whose `config.php` turns off the file time
check of the INI cache sees a changed file **only** after that clear.

> **Note**
> A persistent application server (Velocity) keeps the settings its workers loaded at start for some values. When a
> setting seems ignored there, clear the INI cache and, if the documentation of the setting says so, restart:
> `./console exp:velocity restart`.

## Checklist when a setting has no effect

1. Is the file name right (`site.ini.append.php`, not `site.append.ini.php`)?
2. Is the block name spelled exactly like in the default file, including case?
3. Is the file in a place that is read for this siteaccess (next chapter)?
4. Does a later file override it? `exp:ini where` shows the chain.
5. Did you clear the INI cache?
6. Is it an array that needed a reset line?

## Related pages

- Previous: [Part 2: The builder: settings, templates and overrides](README.md)
- Next: [Settings scopes and the order they win in](settings-scopes-and-their-order.md)
- [The settings page](../../../guides/settings-page.md)
- [INI preserves comments](../../../bc/6.0/eZINI_PRESERVES_COMMENTS.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
