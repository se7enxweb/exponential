---
title: Translations
audience: developer
category: examples
order: 110
summary: Make the texts of an extension translatable with ezpI18n::tr() in PHP and the i18n operator in templates, ship a German translation.ts in the extension, register it with TranslationExtensions and check it on a siteaccess with Locale=ger-DE.
keywords: translation, translations, i18n, ezpI18n::tr, translation.ts, Qt Linguist, TranslationExtensions, RegionalSettings, Locale, TextTranslation, context, placeholders, ezlupdate, ezchecktranslation, translation cache, exp:i18n
type: example
---
# Translations

Every text an extension shows is written in English in the code and translated through a catalogue per locale. You
translate the texts of the example extension into German: the code marks them, the extension ships
`translations/ger-DE/translation.ts`, and a siteaccess with `Locale=ger-DE` shows them in German.

This page is about the texts of the interface. The languages of **content** (an article in English and German) are
content languages: see [Content languages](../../guides/content-languages.md).

Prerequisites: the extension of [An extension skeleton](extension-skeleton.md), switched on, with the views of
[A custom module and view](custom-module-view.md).

## 1. Mark the texts

In PHP, `ezpI18n::tr( $context, $source, $comment = null, $arguments = null )`:

```php
$title = ezpI18n::tr( 'extension/expexample', 'About the example' );
$line  = ezpI18n::tr( 'extension/expexample', '%count items', null, array( '%count' => $total ) );
```

In a template, the `i18n` operator, with the same context; the second parameter is the comment, the third the
arguments:

```tpl
<h1>{'About the example'|i18n( 'extension/expexample' )}</h1>
<p>{'%count items'|i18n( 'extension/expexample', '', hash( '%count', $total ) )}</p>
```

- The **context** groups the texts of one place; use `extension/<extension>` or `extension/<extension>/<part>`.
- The **source** is the English text and the key of the translation: changing a word in it makes every translation
  of it obsolete.
- **Arguments** (`%count`) are inserted after translation, so a translator can move them. Never build a sentence by
  joining translated pieces.
- Escape where you print: `|wash` after `i18n` when an argument came from a user.

## 2. The catalogue

`extension/expexample/translations/ger-DE/translation.ts`, in the Qt Linguist format the kernel uses:

```xml
<?xml version="1.0" encoding="utf-8"?>
<!DOCTYPE TS>
<TS version="2.0" language="de_DE" sourcelanguage="en">
<context>
    <name>extension/expexample</name>
    <message>
        <source>About the example</source>
        <translation>Über das Beispiel</translation>
    </message>
    <message>
        <source>Version %version</source>
        <translation>Version %version</translation>
    </message>
    <message>
        <source>%count items</source>
        <translation>%count Einträge</translation>
    </message>
    <message>
        <source>Previous</source>
        <translation>Zurück</translation>
    </message>
    <message>
        <source>Next</source>
        <translation>Weiter</translation>
    </message>
</context>
</TS>
```

The directory name is the locale code of Exponential (`ger-DE`, `fre-FR`, `esl-ES`), the `language` attribute the Qt
form (`de_DE`). A `<translation type="unfinished">` or an empty one is not used: the English source is shown.

Edit the file with Qt Linguist or any XML editor. `ezlupdate -e extension/expexample ger-DE` (built from
`support/ezlupdate-qt4.5`) collects the marked texts of the extension into the file and marks vanished ones obsolete;
see [Languages, section 8](../../languages.md#8-adding-or-completing-an-administration-translation).

## 3. Register it

The translator reads `translations/` of every extension in `site.ini [RegionalSettings] TranslationExtensions[]`.
In `extension/expexample/settings/site.ini.append.php`:

```ini
<?php /* #?ini charset="utf-8"?

[RegionalSettings]
# Look for translations/<locale>/translation.ts in this extension
TranslationExtensions[]=expexample

*/ ?>
```

## 4. See it in German

A siteaccess shows the catalogue of its `Locale`. In `settings/siteaccess/<siteaccess>/site.ini.append.php`:

```ini
[RegionalSettings]
Locale=ger-DE
```

Text translation is off when the locale is `eng-GB`, the shipped default, and when `TextTranslation=disabled`.

```bash
xmllint --noout extension/expexample/translations/ger-DE/translation.ts
php bin/php/ezcache.php --clear-tag=i18n --allow-root-user
php bin/php/ezcache.php --clear-tag=ini --allow-root-user
```

Open `/expexample/about` on that siteaccess: the heading is *Über das Beispiel*. An editor can also choose the
language of the administration for themselves: see [UI switch: languages](../../features/6.0/ui-switch-languages.md).

## 5. Measure and check

```bash
php bin/php/ezchecktranslation.php ger-DE --allow-root-user      # the finished share of the kernel catalogue
./console exp:i18n fallbacks --locale=ger-AT --allow-root-user   # the chain a locale falls back to, per extension
```

A locale without a catalogue of its own falls back along a chain (`ger-AT` to `ger-DE`, `esl-AR` to `esl-ES`),
configured in `i18n.ini [TranslationSettings]`: an Austrian siteaccess shows your German texts without a file of its
own. See [Languages, section 13](../../languages.md#13-translation-fallbacks).

Checked for this page (2026-10-18): `xmllint --noout` on the catalogue; the signature of `ezpI18n::tr()` is the one of
`kernel/common/ezpi18n.php`; the extension lookup is `eZTSTranslator`'s `TranslationExtensions` with the directory
`translations`. The German page was not viewed for this page.

## Common mistakes

| Symptom | Cause |
|---|---|
| The English text stays | the siteaccess runs `eng-GB`; the context or the source differs by one character; the translation is marked unfinished; the translation cache |
| A placeholder is shown as `%count` | the argument key in the code differs from the one in the source |
| Some texts translated, others not | the strings that are not are written without `i18n`/`ezpI18n::tr()`, or under another context |
| Broken characters | the file is not saved as UTF-8, or the XML declaration says otherwise |

## Derived from

- `kernel/common/ezpi18n.php` (`tr()`), `kernel/common/eztemplateautoload.php` (the `i18n` operator)
- `lib/ezi18n/classes/eztstranslator.php`, `lib/ezi18n/classes/eztranslationcache.php` (`TranslationExtensions`)
- `extension/ezjscore/translations/ger-DE/translation.ts`, `extension/ezjscore/settings/site.ini.append.php`
- `settings/site.ini` `[RegionalSettings]`; `doc/en/us/languages.md`, sections 7, 8 and 13

## Related pages

- [Cookbook](README.md)
- [Languages in Exponential](../../languages.md), [Translations and languages](../../features/6.0/translations-and-languages.md)
- [Several sites: siteaccesses](multisite-siteaccess.md) for a siteaccess per language
- [Template operators](../template-operators/README.md): `i18n` and its parameters
- The coding rules and the pattern catalogue: [the standards](../../standards/README.md)
- [Free documentation](../../help/free-documentation.md): this page is free to copy, translate and improve.
