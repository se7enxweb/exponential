---
title: Dates, numbers and currency per language
audience: government, admin, website-creator
category: public-sector
order: 260
summary: How each language version of an Exponential site writes dates, times, numbers and amounts (from the locale file of the siteaccess), how to check and adjust a locale, and the known limits (one time zone per siteaccess, a 12-hour clock only with am and pm).
keywords: date format, number format, currency, locale, share/locale, DateFormat, TimeZone, time zone, MondayFirst, decimal separator, l10n
type: help
---
# Dates, numbers and currency per language

This page is for administrators and site builders. Each siteaccess formats dates, numbers and money with its
`site.ini [RegionalSettings] Locale`, read from `share/locale/<code>.ini`
([Languages in Exponential, section 7](../../../languages.md#7-adding-a-locale)).

## Contents

1. [What the locale decides](#what-the-locale-decides)
2. [Seeing a locale's formats](#seeing-a-locales-formats)
3. [Adjusting a format](#adjusting-a-format)
4. [Time zones](#time-zones)
5. [Known limits](#known-limits)

## What the locale decides

| Group | Decides |
|---|---|
| `[DateTime]` | date and time formats, long and short; `MondayFirst` |
| `[ShortMonthNames]`, `[LongMonthNames]`, day names | the names in the language |
| `[Numbers]` | decimal symbol, thousands separator, decimals |
| `[Currency]` | symbol, name, ISO 4217 code, format of amounts |
| `[HTTP] ContentLanguage` | the `lang` tag of the page |

The locale files of 6.0.15 were generated from Unicode CLDR data, so they follow common usage of each country
([Internationalization standards](../../../standards/internationalization.md#unicode-cldr-and-icu)).

## Seeing a locale's formats

The language's page in **Setup > Languages** has a section **The locale** that shows how dates, numbers and money
are written. Check it with someone from your language service before launch.

## Adjusting a format

If your body's style guide differs (for example a date format for official notices), put an override of the locale
file in a variation (`<code>@<name>.ini`, which holds only the keys it changes) or change your templates to use a
specific format. Keep such changes in your own repository; a changed shipped file is overwritten by updates.

## Time zones

`site.ini [TimeZoneSettings] TimeZone` sets one time zone per installation or siteaccess, from the IANA time zone
database; there is no time zone per user ([Internationalization standards](../../../standards/internationalization.md#time-zones-the-iana-tz-database)).
A body serving several time zones should state the time zone next to times, such as deadlines.

## Known limits

- A 12-hour clock is printed only with `am`/`pm`; locales whose markers are other words use the 24-hour clock.
- `eZLocale` knows no Saturday or Friday start of the week.
- No plural forms in interface translations, so a few counts read awkwardly in some languages.
- Number separators must be single bytes (a plain space, not a no-break space).

## Related pages

- [Languages in Exponential](../../../languages.md)
- [Internationalization standards](../../../standards/internationalization.md)
- [Custom locales for unlisted languages](custom-locales-for-unlisted-languages.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
