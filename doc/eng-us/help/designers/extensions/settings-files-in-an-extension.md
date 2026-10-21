---
title: "Settings files in an extension"
audience: designer, developer
category: theme-extensions
order: 140
summary: How a theme ships settings as .ini.append.php files in its settings/ folder, the file format with its PHP comment guard, which settings belong in a theme, and how to see which file wins.
keywords: settings, ini, ini.append.php, extension settings, override, exp:ini where, charset, design.ini, override.ini, site.ini
type: help
---
# Settings files in an extension

This page is for designers who add settings to a theme. Every active extension's `settings/` folder is read by the
settings system, after the kernel's defaults and before the installation's own overrides.

## The file format

A settings file of an extension is named after the settings file it changes, with `.append.php`:

```php
<?php /* #?ini charset="utf-8"?

[ExtensionSettings]
DesignExtensions[]=mytheme

*/ ?>
```

- The `<?php /* ... */ ?>` around the settings keeps a web server from showing them as text if someone requests
  the file directly.
- `#?ini charset="utf-8"?` states the encoding. Use UTF-8 for new files.
- A line `Variable[]` alone (with nothing after the brackets) empties an array before new entries are added.

## Which settings a theme usually ships

| File | What a theme puts there |
|---|---|
| `design.ini.append.php` | `DesignExtensions[]`, its stylesheet and script lists |
| `override.ini.append.php` | Template override rules ([Override basics](../templates/override-ini-basics.md)) |
| `image.ini.append.php` | [Image aliases](../theme-design/image-aliases.md) the theme's templates use (Image aliases) |
| `site.ini.append.php` | Only what is safe for every siteaccess, for example a template operator's registration |
| `<theme>.ini` | The theme's own options ([Giving a theme its own settings file](your-own-ini-file.md)) |

## Where settings win

From weakest to strongest: the kernel's `settings/*.ini`, siteaccess settings (first those inside extensions, then
`settings/siteaccess/<siteaccess>/`), the active extensions' `settings/` (earlier extensions win over later ones),
and finally the installation's `settings/override/`. So a value in your theme's `settings/` wins over the same value in
a siteaccess; only `settings/override/` wins over it. The full list is in
[Settings override placements](../../../specifications/6.0/ini-override-placements.md).

## Step by step: add and check a setting

1. Create or edit the file in `extension/mytheme/settings/`.
2. Clear the INI cache: `php bin/php/ezcache.php --clear-tag=ini --allow-root-user`.
3. See every file that sets the variable and the value in effect:

   ```bash
   ./console exp:ini where image.ini/AliasSettings/AliasList --allow-root-user
   ```

   Expected: your extension's file among the files, in load order, and the value in effect.

## Rules

- Never put passwords, API keys or tokens into a theme's settings. An extension holding secrets cannot even be
  exported as a package ([The secret check](package-secret-check.md)).
- Never ship `settings/override/` in a theme: that folder belongs to the installation.
- Comment each block: the next maintainer (or you in two years) needs the reason.

## Related pages

- [Siteaccess settings inside a theme](siteaccess-settings-in-an-extension.md)
- [Extensions guide, section 5: configure it](../../../guides/extensions.md#5-configure-it)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
