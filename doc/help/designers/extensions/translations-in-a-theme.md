---
title: "Translations in a theme"
audience: designer, developer
category: theme-extensions
order: 210
summary: Ship the interface texts of a theme in several languages - translations/<locale>/translation.ts, TranslationExtensions[], the i18n operator with the theme's own context - and keep the files complete.
keywords: translations, translation.ts, i18n, TranslationExtensions, RegionalSettings, locale, ger-DE, untranslated, language
type: help
---
# Translations in a theme

This page is for designers whose theme shows texts of its own ("Read more", "Search this site", button labels) and
should work in more than one language. Writing the template side is explained in
Translating template strings; here: the files in the extension.

## The files

```
extension/mytheme/translations/
├── untranslated/translation.ts     every source string, no translations (a template for translators)
├── ger-DE/translation.ts
└── fre-FR/translation.ts
```

`sevenx_themes_simple` ships `eng-US`, `esl-ES`, `esl-MX`, `ger-DE`, `heb-IL`, `lat-VA`, `tlh-QO` and `untranslated`;
`sevenx_themes_media` adds more, for example `fre-FR`, `ita-IT`, `nor-NO` and `pol-PL`.

## Step by step

1. In templates, mark every text with a context of your theme:

   ```
   {'Read more'|i18n( 'extension/mytheme' )}
   ```

2. Create `translations/ger-DE/translation.ts` in the Qt Linguist format with a `<context>` named
   `extension/mytheme` and one `<message>` per text.
3. Register the extension for translations in `extension/mytheme/settings/site.ini.append.php`:

   ```php
   <?php /* #?ini charset="utf-8"?

   [RegionalSettings]
   TranslationExtensions[]=mytheme

   */ ?>
   ```

4. Check the XML of every file: `xmllint --noout extension/mytheme/translations/*/translation.ts` (no output
   expected).
5. Clear the caches: `php bin/php/ezcache.php --clear-tag=ini --allow-root-user` and
   `php bin/php/ezcache.php --clear-id=template,translation --allow-root-user`.
6. Open a siteaccess whose locale is `ger-DE` and check the texts.

## Good practice

- Keep one context per theme, so translators find everything in one place.
- Ship `untranslated/translation.ts` with every source string, so a new language starts from a complete list.
- Never repeat the same message twice in one context.
- A right-to-left language (`heb-IL`, `ara-SA`) needs the layout to follow too
  (Right-to-left designs).

## Related pages

- [Languages](../../languages.md)
- Tutorial: internationalize a theme
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
