---
title: Add a custom locale for a minority language
audience: government, admin, developer
category: public-sector
order: 400
summary: Task for administrators: add a language that has no locale file yet by writing share/locale/<code>.ini from a related locale, adding it as a Custom content language, and having a speaker check it.
keywords: custom locale, minority language, share/locale, locale file, Custom translation, language code, ContentLanguage
type: help
---
# Add a custom locale for a minority language

This task is for administrators and developers. Background:
[Custom locales for unlisted languages](../../languages/custom-locales-for-unlisted-languages.md) and
[Languages in Exponential, section 7](../../../../languages.md#7-adding-a-locale).

## Before you start

- Check the language really has no file: the list in [Languages in Exponential](../../../../languages.md#3-every-locale)
  and its section 6 of missing languages.
- A speaker who can provide day and month names and check formats.

## Steps

1. Choose the code: three letters (ISO 639-2/T, or ISO 639-3 if there is none), a dash, the two-letter country.
2. Copy a related locale file to `share/locale/<code>.ini` and change the names (`LanguageName`,
   `InternationalLanguageName`, `Country`), `[HTTP] ContentLanguage`, `TextDirection` if right to left, the formats and
   the day and month names. UTF-8, first line `#?ini charset="utf-8"?`.
3. Keep the file in your repository (and offer it to the project), so updates do not lose it.
4. **Setup > Languages**, **Add language**, **Custom**: **Name of custom translation** and **Locale for custom
   translation** (the code); **Add language**.

## Check

The language's page in **Setup > Languages**, section **The locale**, shows dates and numbers as the speaker expects; a
test page shows the right `lang` on the public site.

## Problems

| Problem | Fix |
|---|---|
| *There is no locale ... in share/locale* | file name or location wrong, or not deployed to every web node |
| The form rejects the code | it must have the form `xxx-YY` |

## Related pages

- [Minority and regional languages](../../languages/minority-and-regional-languages.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
