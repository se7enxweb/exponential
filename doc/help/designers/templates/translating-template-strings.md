---
title: Translating template strings
audience: designer, developer
category: templates
order: 360
summary: How texts in templates reach translators and back: contexts and sources in i18n calls, translation.ts files in the theme extension, TranslationExtensions, the untranslated and English catalogues, checking coverage, caches, and fallbacks between languages.
keywords: translation, translation.ts, i18n strings, translate templates, TranslationExtensions, ezchecktranslation, translation cache, fallback language, theme translation, translators
type: help
---
# Translating template strings

This page is for designers who prepare a theme's texts for translation and maintain the translations. For the
step-by-step first time, see [Tutorial: internationalize a theme](../theme-design/tutorial-internationalize-a-theme.md).

## Contents

1. [From template to translation](#from-template-to-translation)
2. [Where theme translations live](#where-theme-translations-live)
3. [Writing good source texts](#writing-good-source-texts)
4. [Checking and caches](#checking-and-caches)
5. [Fallbacks](#fallbacks)

## From template to translation

1. A template prints `{'Read more'|i18n( 'design/mytheme/line' )}`.
2. The translation file of the locale has a `<context>` named `design/mytheme/line` with a `<message>` whose `<source>`
   is exactly `Read more`.
3. Its `<translation>` is shown; without one, the source text is shown.

## Where theme translations live

```
extension/mytheme/translations/ger-DE/translation.ts
extension/mytheme/translations/fre-FR/translation.ts
extension/mytheme/translations/untranslated/translation.ts   (optional: every source, empty translations, for translators)
```

The siteaccess reads them when `site.ini [RegionalSettings] TranslationExtensions[]` lists the extension. The media
theme ships eleven catalogues in this layout; the starter theme ships `ger-DE`. See
[Translations in a theme](../extensions/translations-in-a-theme.md).

## Writing good source texts

- Whole sentences with placeholders, never fragments joined in the template.
- One meaning per context: "Open" (a verb on a button) and "Open" (a state) in different contexts.
- A comment for translators when the text is ambiguous (the second `i18n` parameter).
- American English source texts ("color", "license"), as in the rest of the product.

## Checking and caches

```bash
./console exp:ezchecktranslation ger-DE          # statistics for one translation
php bin/php/ezcache.php --clear-id=translation   # after changing a .ts file
xmllint --noout extension/mytheme/translations/ger-DE/translation.ts
```

`site.ini [RegionalSettings] TranslationCache=enabled` caches translations; `TranslationCheckMTime=enabled` (default)
notices changed `.ts` files by their time.

## Fallbacks

A locale can fall back to another for missing texts (for example a regional variant to the main language);
`./console exp:i18n fallbacks` shows each locale's chain and how much of the kernel and of each extension it shows
translated (`doc/languages.md`, "Translation fallbacks").

## Related pages

- [The i18n operator](i18n-operator.md)
- [Languages in Exponential](../../../languages.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
