---
title: "Deprecating a setting or a template"
audience: designer, developer
category: theme-lifecycle
order: 230
summary: Step by step - rename a theme setting while still reading the old name, replace a template while keeping an include of the old one, and remove both later - with the changelog entries for each step.
keywords: deprecate setting, rename setting, fallback, deprecate template, include, compatibility, removal, changelog
type: help
---
# Deprecating a setting or a template

This page is for designers following their [deprecation policy](deprecation-policy.md).

## A setting

Example: `mytheme.ini [Front] Teasers` becomes `[FrontPage] TeaserCount`.

1. Add the new setting with its default to the theme's settings file.
2. Read the new one first, then the old one, in templates:

   ```
   {def $count = ezini( 'FrontPage', 'TeaserCount', 'mytheme.ini' )}
   {if ezini_hasvariable( 'Front', 'Teasers', 'mytheme.ini' )}
       {set $count = ezini( 'Front', 'Teasers', 'mytheme.ini' )}
   {/if}
   ```

   (Order the reads so a user's override of the old name still wins until they switch.)
3. Changelog: `Renamed: mytheme.ini [Front] Teasers to [FrontPage] TeaserCount; the old name is read until 2.0.0`.
4. In the announced release, remove the old read: `Removed: mytheme.ini [Front] Teasers (use [FrontPage] TeaserCount)`.

## A template

Example: `parts/teaser.tpl` is replaced by `parts/card.tpl`.

1. Create `parts/card.tpl`.
2. Make `parts/teaser.tpl` a one-line include of the new one, with a comment:

   ```
   {* Deprecated since 1.4.0, removed in 2.0.0: include design:parts/card.tpl instead. *}
   {include uri='design:parts/card.tpl' node=$node}
   ```

3. Changelog: `Added: parts/card.tpl` and `Updated: parts/teaser.tpl is deprecated, use parts/card.tpl`.
4. Remove `parts/teaser.tpl` in the announced release, with the upgrade step for users who override it.

## Related pages

- [Giving a theme its own settings file](../extensions/your-own-ini-file.md)
- The ezini operator
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
