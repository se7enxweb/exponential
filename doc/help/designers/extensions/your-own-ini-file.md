---
title: "Giving a theme its own settings file"
audience: designer, developer
category: theme-extensions
order: 160
summary: Give a theme its own settings file (mytheme.ini) with documented defaults, read it from templates with ezini() and from PHP with eZINI, and let installations override it safely.
keywords: own ini, settings file, ezini, eZINI, theme options, defaults, cjw_jumper.ini, override
type: help
---
# Giving a theme its own settings file

This page is for designers who want options in their theme (a color scheme, the number of teasers on the front page,
Open Graph defaults) without asking users to edit templates.

## A real example

`cjw_themes_jumper` ships `settings/cjw_jumper.ini.append.php`. Its 0.1.2 release, for example, reads the classes
treated as articles for Open Graph from `cjw_jumper.ini [OpenGraph] ArticleClasses`.

## Step by step

1. Create `extension/mytheme/settings/mytheme.ini.append.php` with a documented block:

   ```php
   <?php /* #?ini charset="utf-8"?

   [FrontPage]
   # How many teasers the front page shows (a whole number from 1 to 12)
   TeaserCount=6

   */ ?>
   ```

   An installation without your theme has no `mytheme.ini` at all, so the append file is the only place the defaults
   live. Comment every setting.

2. Read it in a template with the `ezini` operator:

   ```
   {def $count = ezini( 'FrontPage', 'TeaserCount', 'mytheme.ini' )}
   ```

   See [The ezini operator](../templates/ezini-operator.md).

3. Read it in PHP (an operator or a module of the theme):

   ```php
   $count = (int) eZINI::instance( 'mytheme.ini' )->variable( 'FrontPage', 'TeaserCount' );
   ```

4. Clear the INI cache and check:

   ```bash
   php bin/php/ezcache.php --clear-tag=ini --allow-root-user
   ./console exp:ini where mytheme.ini/FrontPage/TeaserCount --allow-root-user
   ```

5. Tell users in the README how to override it: in `settings/override/mytheme.ini.append.php` or a siteaccess
   folder, never in the theme's own file (an update would overwrite it).

## Good practice

- Validate what you read: cast numbers, compare against a list of allowed values, fall back to the default.
- Never read secrets from a theme's settings file; secrets belong to the installation's override folder.
- When you rename or remove a setting, keep reading the old name for a while
  ([Deprecating a setting or a template](../lifecycle/deprecating-a-setting-or-template.md)).

## Related pages

- [Settings files in an extension](settings-files-in-an-extension.md)
- [Change settings from the command line](../../../features/6.0/exp-ini-command.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
