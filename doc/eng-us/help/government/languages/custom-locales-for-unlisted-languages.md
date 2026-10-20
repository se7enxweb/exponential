---
title: Custom locales for unlisted languages
audience: government, admin, developer
category: public-sector
order: 140
summary: How to add a language Exponential does not ship a locale for: write share/locale/<code>.ini with names, HTTP tag, direction, formats and day and month names, choose the code, add it as a custom content language, and check it.
keywords: custom locale, new locale, share/locale, locale file, language code, ISO 639, BCP 47, ContentLanguage, TextDirection, custom translation, unlisted language
type: help
---
# Custom locales for unlisted languages

This page is for administrators and developers who need a language that has no locale file yet. A content language
needs a file `share/locale/<code>.ini`, because the locale decides the language's name, its HTTP language tag, its
direction and how dates and numbers are written. The file format is described in
[Languages in Exponential, section 7](../../../languages.md#7-adding-a-locale).

## Contents

1. [Choose the code](#choose-the-code)
2. [Write the file](#write-the-file)
3. [Add it as a content language](#add-it-as-a-content-language)
4. [Check it](#check-it)
5. [Share it](#share-it)

## Choose the code

Three letters for the language, a dash, the two-letter ISO 3166 country: `xxx-YY`. Exponential keeps its historic
codes for older languages (`eng`, `ger`, `fre`, `esl` ...) and uses the ISO 639-2/T code for others; a language
without an ISO 639-2 code takes its ISO 639-3 code. Numeric regions cannot be used. Check the list of shipped files
first, and section 6 of [Languages in Exponential](../../../languages.md#6-common-languages-still-missing) for
languages known to be missing.

## Write the file

Start from the locale of a closely related language or the same country, copy it to the new name, and change:

| Group | Keys to change |
|---|---|
| `[RegionalSettings]` | `Country`, `LanguageName` (own name), `InternationalLanguageName` (English name) |
| `[LanguageSettings]` | `TextDirection=rtl` only if written right to left |
| `[HTTP]` | `ContentLanguage`: the BCP 47 tag printed as the page's `lang` |
| `[Currency]`, `[Numbers]` | the country's currency and number format; separators must be single bytes |
| `[DateTime]` | date and time formats, `MondayFirst` |
| `[ShortMonthNames]`, `[LongMonthNames]`, `[ShortDayNames]`, `[LongDayNames]` | the names in the language |

The file is UTF-8 with the first line `#?ini charset="utf-8"?`. Ask a speaker to provide or check the names.

## Add it as a content language

**Setup > Languages > Add language**, choose **Custom**, fill in **Name of custom translation** and **Locale for
custom translation**. If the file is missing, the form says *There is no locale ... in share/locale* and keeps your
input ([Content languages, section 4](../../../guides/content-languages.md#4-adding-a-language)).

## Check it

- The language's page in **Setup > Languages** has a folding section **The locale** that shows how dates, numbers and
  money are written; compare with what a speaker expects.
- Publish a test page and check `<html lang="...">` on the public site.
- The locale files are covered by tests that check valid UTF-8 (`tests/tests/lib/ezlocale/expLocaleFilesTest.php`);
  run them if you keep your file in a fork.

## Share it

A reviewed locale file helps every other user of the language. Offer it to the project (see "Contributing back as a
public body" in the sovereignty section); otherwise keep it in your own repository, because a file added only on the
server can be lost in an update.

## Related pages

- [Languages in Exponential](../../../languages.md)
- [Adding a content language](adding-a-content-language.md)
- [Internationalization standards](../../../standards/internationalization.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
