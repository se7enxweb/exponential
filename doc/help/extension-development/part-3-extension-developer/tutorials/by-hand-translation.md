---
title: "By hand: translating an extension"
audience: developer, app-creator, designer
category: extension-development
order: 210
summary: Translate an extension into German: collect the texts, write the translation.ts files, register them with TranslationExtensions, clear the cache and see the German texts.
keywords: tutorial, by hand, translation, i18n, translation.ts, ger-DE, untranslated, TranslationExtensions, ezpI18n, context, placeholders, translation cache, xmllint
type: help
---
# By hand: translating an extension

Tutorial 20. Every text of `mysite_hello` went through `i18n` or `ezpI18n::tr()` with the context
`extension/mysite_hello`. Now you add German. With the files below and the extension registered,
`ezpI18n::tr( 'extension/mysite_hello', 'Hello, %name!', null, array( '%name' => 'Ada' ) )` returned `Hallo, Ada!` for
the locale `ger-DE`.

## 1. Collect the texts

```bash
grep -rhoE "'[^']+'\|i18n\( 'extension/mysite_hello'" extension/mysite_hello/design | sort -u
grep -rhoE "ezpI18n::tr\( 'extension/mysite_hello', '[^']+'" extension/mysite_hello --include=*.php | sort -u
```

Each line is one source text of the context.

## 2. The German file

`extension/mysite_hello/translations/ger-DE/translation.ts`:

```xml
<?xml version="1.0" encoding="utf-8"?>
<!DOCTYPE TS>
<TS version="2.0" language="de_DE">
<context>
    <name>extension/mysite_hello</name>
    <message>
        <source>Hello, %name!</source>
        <translation>Hallo, %name!</translation>
    </message>
    <message>
        <source>Newest articles</source>
        <translation>Neueste Artikel</translation>
    </message>
    <message>
        <source>Compose a greeting</source>
        <translation>Einen Gruß verfassen</translation>
    </message>
</context>
</TS>
```

The `source` must match the text in the code exactly, placeholders included. Add every text; a missing one shows in
English.

Keep `translations/untranslated/translation.ts` with the same `source` lines and empty `<translation type="unfinished"></translation>`
elements, as a template for the next translator.

## 3. Check and register

```bash
xmllint --noout extension/mysite_hello/translations/ger-DE/translation.ts
```

In `extension/mysite_hello/settings/site.ini.append.php`:

```ini
[RegionalSettings]
TranslationExtensions[]=mysite_hello
```

Then:

```bash
php bin/php/ezcache.php --clear-id=translation
php bin/php/ezcache.php --clear-tag=ini
```

## 4. See it

Open `/hello/world/Ada` in a siteaccess whose `Locale` is `ger-DE` (or switch the administration's language to German):
"Hallo, Ada!" and "Neueste Artikel". Check the coverage:

```bash
./console exp:i18n fallbacks --locale=ger-DE
```

lists, per extension, how much is translated for the locale and its fallbacks.

## Related pages

- Previous: [By hand: a rich text custom tag](by-hand-custom-tag.md)
- Next: [By hand: help pages for your extension](by-hand-help-pages-for-your-extension.md)
- [Translations in an extension](../translations-in-an-extension.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
