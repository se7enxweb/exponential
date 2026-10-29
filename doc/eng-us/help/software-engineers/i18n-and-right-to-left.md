---
title: Internationalization and right to left
audience: software-engineer, developer
category: software-engineering
order: 440
summary: The internationalization model of Exponential for engineers - locales from CLDR data, content languages stored as bits of a language mask with a limit of 62, translation catalogues in Qt Linguist format, fallbacks, UTF-8 everywhere and its MySQL limit, time zones, and right-to-left text in content and the administration - with the rules for code that shows text.
keywords: internationalization, i18n, l10n, locale, CLDR, content language, language_mask, translation.ts, ezpI18n, fallback, UTF-8, utf8mb4, time zone, right to left, RTL, dir, eZLocale, software engineer
type: help
---
# Internationalization and right to left

This page is for engineers who build multilingual sites or write code that shows text, dates or numbers. It
summarizes the design and its limits; the standards status is in [Internationalization standards](../../standards/internationalization.md)
and the full language list in [Languages in Exponential](../../languages.md).

## Contents

1. [Four separate things](#four-separate-things)
2. [Content languages and the 62 limit](#content-languages-and-the-62-limit)
3. [Interface translations](#interface-translations)
4. [Encoding](#encoding)
5. [Right to left](#right-to-left)
6. [Rules for code](#rules-for-code)
7. [Known gaps](#known-gaps)

## Four separate things

| Thing | What it is | Where |
|---|---|---|
| Locale | formats for dates, numbers, currencies and the text direction of a language and country | `share/locale/<code>.ini`, generated from CLDR data |
| Content language | a language content can be written in, added on Setup > Languages | the `ezcontent_language` table |
| Interface translation | the administration's and templates' text in a language | `share/translations/<locale>/translation.ts` |
| Siteaccess | which languages a site shows and in which order of fallback | siteaccess settings |

The README counts 219 locales in the 6.0.15 line; the administration is complete in English and German and partly
translated into more ([Languages in Exponential](../../languages.md)).

## Content languages and the 62 limit

Each content language is one bit: its id is a power of two, and an object, a version, a class and an object state
record all their languages in one integer, `language_mask`. That makes "which objects exist in German?" a bit test per
row, which is why language filtering is cheap in every kernel query. The price is a limit: **at most 62 content
languages on 64-bit PHP** (30 on 32-bit), from `eZContentLanguage::maxCount()`. Locales that are offered but not added
do not count ([The limit of 62 content languages](../../languages.md#11-the-limit-of-62-content-languages)).

Fallbacks decide what a visitor sees when a translation is missing ([Translation fallbacks](../../languages.md#13-translation-fallbacks),
[Content object fallback locale](../../bc/6.0/content-object-fallback-locale.md)).

## Interface translations

- PHP: `ezpI18n::tr( $context, $text )`; templates: `{'Text'|i18n( 'design/admin4/content/view' )}`.
- The context is the path of the code that uses it: `kernel/<module>` for PHP views,
  `design/<design>/<module>/<view>` for templates. New strings must be added to the context the code uses, so the
  translation tools and tests find them.
- Catalogues are Qt Linguist `.ts` files; there are no gettext catalogues.

## Encoding

- UTF-8 is the internal encoding (`i18n.ini [CharacterSettings] Charset=utf-8`).
- On MySQL and MariaDB the connection setting `utf-8` is sent as `utf8`, the three-byte form, so characters outside
  the Basic Multilingual Plane (emoji, for example) cannot pass through the connection even with `utf8mb4` tables.
  SQLite, PostgreSQL, Oracle (`AL32UTF8`) and MongoDB store them.
- Time zones come from the IANA database through `site.ini [TimeZoneSettings] TimeZone`.

## Right to left

The text direction has one source: `share/locale/<locale>.ini [LanguageSettings] TextDirection=rtl` (43 locale files
carry it). `eZLocale::textDirection()`, `isRightToLeft()` and the static `textDirectionOf( 'heb-IL' )` read it. When the
interface language is right to left, the administration says `dir="rtl"` on `<html>` and mirrors its layout; content in
a right-to-left content language is marked with `dir` where it is shown and edited, whatever the interface language. A
left-to-right page is byte for byte what it was before ([Right-to-left languages in the administration](../../features/6.0/right-to-left.md)).

## Rules for code

- Never build user-visible text by concatenation of translated fragments; translate whole sentences.
- Format dates, numbers and currencies through the locale (`eZLocale`), not with fixed formats.
- In CSS, prefer logical properties (start and end) over left and right, so a right-to-left page mirrors.
- Use the language filters of fetch functions rather than SQL on `language_mask`.

## Known gaps

From the standard: no CLDR plural rules in the catalogues, internal language codes such as `ger-DE` that are not BCP 47
(the HTTP and HTML output is), `intl` optional only, and the MySQL four-byte limit above.

## Related pages

- [Content languages](../../guides/content-languages.md)
- [Translations in an extension](../extension-development/part-3-extension-developer/translations-in-an-extension.md)
- [Multi-site installations](multi-site-installations.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
