---
title: Internationalization standards
audience: developer, admin
category: standards
order: 60
summary: How Exponential handles Unicode, CLDR locale data, BCP 47 language tags, ISO codes, right-to-left text, time zones and translation catalogues, with evidence and honest gaps.
keywords: internationalization, i18n, localization, l10n, Unicode, UTF-8, utf8mb4, CLDR, ICU, intl, BCP 47, ISO 639, ISO 3166, ISO 4217, IANA time zone, tz database, RTL, bidi, UAX 9, Qt Linguist, ts, gettext, plural forms, locale
type: reference
---
# Internationalization standards

This page is for developers and administrators who need to know which internationalization standards Exponential
follows: which character encoding it uses, where its locale data comes from, how language tags reach the browser,
how right-to-left text is handled, which ISO code lists it relies on, and what is still missing. Every claim names
the file, setting or test it was checked against (Exponential 6.0.15 line, October 2026).

For the list of every locale and administration translation, see [Languages in Exponential](../languages.md).

## Summary

| Standard | Version | Status | Evidence (short) |
|---|---|---|---|
| Unicode / UTF-8 as the internal encoding | Unicode, UTF-8 (RFC 3629) | **Complies** | `settings/i18n.ini [CharacterSettings] Charset=utf-8`, `settings/site.ini [DatabaseSettings] Charset=utf-8` |
| 4-byte UTF-8 in MySQL / MariaDB (`utf8mb4`) | MySQL `utf8mb4` | **Partly complies** | `lib/ezdb/classes/ezmysqlcharset.php` maps `utf-8` to `utf8` (3 bytes); `ezxml.ini [InputSettings] SupplementaryCharacters` |
| Unicode CLDR locale data | CLDR 44 and 48 | **Complies** (as generated data) | `share/locale/*.ini` headers, [Languages in Exponential](../languages.md) |
| ICU / PHP `intl` | ICU via ext-intl | **Partly complies** (optional only) | `class_exists( 'Collator' )` guards in two kernel classes |
| BCP 47 language tags (RFC 5646) | BCP 47 | **Complies** for HTTP and HTML; internal codes are not BCP 47 | `share/locale/<code>.ini [HTTP] ContentLanguage` |
| ISO 639 language codes | ISO 639-1 / 639-2 | **Partly complies** | internal codes like `ger-DE` use ISO 639-2 (bibliographic and older forms) |
| ISO 3166-1 country codes | ISO 3166-1 alpha-2 / alpha-3 | **Complies** | `settings/country.ini` (246 entries with `Alpha2`, `Alpha3`) |
| ISO 4217 currency codes | ISO 4217 | **Complies** | `kernel/shop/classes/ezcurrencydata.php` (`/^[A-Z]{3}$/`), `[Currency] ShortName` in locale files |
| IANA time zone database | tz database | **Complies** | `settings/site.ini [TimeZoneSettings] TimeZone`, `date_default_timezone_set()` |
| Unicode Bidirectional Algorithm | UAX #9 | **Complies** (through the browser, with `dir` and isolation) | [Right-to-left languages](../features/6.0/right-to-left.md) |
| Qt Linguist `.ts` catalogues | TS 2.0 | **Complies** | `share/translations/<code>/translation.ts`, `schemas/translation/ts.rng` |
| GNU gettext | - | **Not applicable** | no `.po`/`.mo` catalogues, no gettext calls in `kernel/` or `lib/` |
| Plural forms (CLDR plural rules) | CLDR plural rules | **Does not comply** | no `numerus` messages in any `translation.ts` |

## Unicode and UTF-8

**What it is.** Unicode is the universal character set published by the Unicode Consortium; UTF-8 (RFC 3629, IETF)
is its byte-oriented encoding and the encoding of the web.

**How Exponential complies.**

- The internal character set is read from `settings/i18n.ini [CharacterSettings] Charset`, shipped as `utf-8`.
  `eZTextCodec::internalCharset()` (`lib/ezi18n/classes/eztextcodec.php`) reads exactly that setting.
- `settings/i18n.ini [CharacterSettings] MBStringExtension=enabled`, and `ext-mbstring` is a hard requirement in
  `composer.json` `require`.
- The database connection charset is `settings/site.ini [DatabaseSettings] Charset=utf-8` (the comment above it says
  an empty value falls back to `i18n.ini`).
- Translation catalogues are XML declared as `encoding="utf-8"`.
- 348 of the 368 locale files in `share/locale/` declare `[Charset] Preferred=utf-8`. The remaining 20 are older
  files that still name a legacy code page (for example `iso-8859-1`, `iso-8859-15`, `koi8-u`, `cp1251`). The site's
  internal charset still comes from `i18n.ini`; the code page names are kept for compatibility.
- The test `tests/tests/lib/ezlocale/expLocaleFilesTest.php` checks that every locale file's names are valid UTF-8
  and that no file holds double-encoded UTF-8.

**Status: Complies** for the application's internal encoding and for new installations.

### 4-byte characters on MySQL and MariaDB

MySQL's `utf8` (also called `utf8mb3`) stores at most three bytes per character, so characters above U+FFFF (emoji,
mathematical alphanumerics, many historic scripts) need `utf8mb4`.

- `lib/ezdb/classes/ezmysqlcharset.php` maps the internal name `utf-8` to MySQL's `utf8`, not to `utf8mb4`.
- The MySQL driver (`lib/ezdb/classes/ezmysqlidb.php`) sets that mapped charset on the connection and writes a
  warning to the debug log when the server reports a different one.
- Rich text has a guard: `ezxml.ini [InputSettings] SupplementaryCharacters` (`enabled`, `disabled` or `auto`).
  `eZXMLInputHandler::supplementaryCharactersAllowed()` keeps characters above U+FFFF on `auto` unless the database
  is MySQL or MariaDB on a connection charset other than `utf8mb4`; there they are replaced, because they would be
  stored as `????` or fail in strict SQL mode.
- The system report (`kernel/classes/expsystemreport.php`) accepts `utf-8`, `utf8` and `utf8mb4` as healthy
  connection charsets.

**Status: Partly complies.** SQLite 3 and PostgreSQL store full Unicode. On MySQL and MariaDB a stock connection is
3-byte `utf8`; supplementary characters are only kept when the administrator configures `utf8mb4`.

> **Note** The table charset and collation of a MySQL or MariaDB installation depend on the server defaults at
> install time: `kernel/sql/mysql/kernel_schema.sql` names `ENGINE=InnoDB` but no `CHARSET`. Comments in
> `kernel/classes/datatypes/ezurl/ezurl.php` and `kernel/classes/expuserloginintegrity.php` assume
> `utf8mb4_general_ci` columns. Which collation a given installation has is **Unverified** here.

## Unicode CLDR and ICU

**What it is.** The Unicode Common Locale Data Repository (CLDR, Unicode Consortium) is the reference source for
locale data: names of languages and countries, date, time, number and currency formats. ICU (International
Components for Unicode) is the library that implements it; PHP exposes ICU through the `intl` extension.

**How Exponential complies.**

- Locale files are plain INI read by `eZLocale` (`lib/ezlocale/classes/ezlocale.php`). For 6.0.15 they were
  generated from CLDR: for example `share/locale/ara-SA.ini` starts with "Generated from Unicode CLDR 48 data (ICU
  locale ar_SA)" and carries the Unicode License v3 notice. [Languages in Exponential](../languages.md) lists the
  source of every file (CLDR 44, CLDR 48, or hand-made names following CLDR conventions).
- No PHP extension is needed at run time: [Languages in Exponential](../languages.md), section "No PHP extension at
  run time". `intl` was used only to generate the files.
- `intl` is used optionally in two kernel classes, always behind `class_exists()`:
  - `kernel/classes/datatypes/ezcountry/expcountrygroups.php`: `Collator` for sorting country names in the reader's
    language, `Normalizer::normalize( ..., Normalizer::FORM_D )` for accent-insensitive matching.
  - `kernel/classes/uiswitch/expuiswitchlanguagecatalogue.php`: the same for the interface language list.
- No `NumberFormatter` or `IntlDateFormatter` is used in `kernel/` or `lib/`. Dates, numbers and currency are
  formatted by `eZLocale` from the INI data. `tests/tests/lib/ezlocale/eZLocaleFormattingTest.php` and
  `expLocaleFilesTest.php` check that every locale formats dates, times, numbers and currency.

**Status: Complies** for CLDR as the data source. **Partly complies** for ICU: it is optional, and where it is
missing the kernel falls back to byte-order sorting.

**Known gaps.**

- `eZLocale` prints only am/pm and cannot express every CLDR pattern; `ara-SA.ini` notes that CLDR's 12-hour clock
  was replaced by the 24-hour clock for that reason.
- The INI files are a snapshot. There is no automated job that regenerates them from a newer CLDR release.

## Language tags: BCP 47 and ISO 639

**What it is.** BCP 47 (IETF, currently RFC 5646 and RFC 4647) defines language tags such as `de-DE`, `he-IL` or
`sr-Latn-RS`, built from ISO 639 language codes, ISO 15924 script codes and ISO 3166-1 or UN M.49 region codes.

**How Exponential complies.**

- Internally Exponential identifies a locale by its own code, `<ISO 639-2 code>-<ISO 3166-1 alpha-2 code>`, for
  example `eng-GB`, `ger-DE`, `nor-NO`, optionally with a variation (`ger-DE@euro`). These codes name the files in
  `share/locale/` and `share/translations/`, the content languages (`ezcontent_language`) and
  `site.ini [RegionalSettings] Locale`. They are **not** BCP 47 tags, and some use ISO 639-2 bibliographic or older
  codes (`ger`, `fre`, `dut`, `esl`).
- Every locale file maps its code to a BCP 47 tag in `[HTTP] ContentLanguage` (for example `ara-SA` to `ar-SA`).
  `eZLocale::httpLocaleCode()` returns it, and the page layouts print it in `<html lang="...">` and the
  `Content-Language` header. `settings/site.ini [RegionalSettings] HTTPLocale` can override it per siteaccess.
- In 6.0.15 twenty locale files that gave invalid or obsolete tags (`no-bokmaal`, `swe-SE`, `ua-UA` ...) were
  corrected; the table is in [Translations and languages](../features/6.0/translations-and-languages.md), "HTML
  language tags".
- A check made for this page against the shipped files: every `[HTTP] ContentLanguage` value in `share/locale/`
  matches the BCP 47 shape language, optional script, optional region, optional variants.
- `expLocaleFilesTest.php` checks the code form and HTTP code conventions of the generated files.

**Status: Complies** for the tags sent to browsers. The internal codes are a separate, documented scheme.

**Known gaps.**

- The `language` attribute of the Qt `.ts` files mixes forms: `de_DE`, `en_GB`, `heb-IL`, `lat-VA`, `tlh-QO`
  (checked in `share/translations/*/translation.ts`). The translator does not depend on it, but it is inconsistent.
- The internal three-letter codes cannot express a script subtag; a script variant needs a separate locale code.

## ISO 3166 country codes

**What it is.** ISO 3166-1 (ISO) defines the two-letter, three-letter and numeric codes of countries.

**How Exponential complies.** `settings/country.ini` holds one section per country, named by its alpha-2 code, with
`Alpha2`, `Alpha3` and `IDC` (international dialing code); the file states that the group name is the ISO 3166-1
alpha-2 code. It has 246 entries. The country datatype (`kernel/classes/datatypes/ezcountry/`) stores and validates
against these codes; `settings/countrygroups.ini` groups them.

**Status: Complies.** Whether the list matches the current ISO 3166-1 edition entry by entry is **Unverified**.

## ISO 4217 currency codes

**What it is.** ISO 4217 (ISO) defines three-letter currency codes such as `EUR`, `USD`, `SAR`.

**How Exponential complies.**

- Every locale file names its currency in `[Currency] ShortName` (for example `SAR` in `ara-SA.ini`), with symbol,
  name, decimal and thousands separators and the number of fraction digits.
- The shop's currency table refuses any code that is not three upper-case letters
  (`kernel/shop/classes/ezcurrencydata.php`, `preg_match( "/^[A-Z]{3}$/", $code )`).
- The comment block of `lib/ezlocale/classes/ezlocale.php` names ISO 639 and ISO 4217 as its references.

**Status: Complies** for the format. Whether every `ShortName` is a currently valid ISO 4217 code is
**Unverified**.

## Time zones: the IANA tz database

**What it is.** The IANA time zone database (tz, zoneinfo) names zones such as `Europe/Berlin` and records their
rules; PHP's date functions use it.

**How Exponential complies.**

- `settings/site.ini [TimeZoneSettings] TimeZone` takes an IANA zone name (the comment points to
  `/usr/share/zoneinfo/zone.tab`). Empty by default.
- The web kernel (`kernel/private/classes/ezpkernelweb.php`) and command line scripts (`kernel/classes/ezscript.php`)
  call `date_default_timezone_set()` with that value when it is set.
- `index.php` sets `UTC` when PHP has no `date.timezone`, so PHP never guesses.
- The CI database tests run with `date.timezone=America/Los_Angeles` (`.github/workflows/phpunit.yml`), which
  catches code that assumes UTC.

**Status: Complies.**

**Known gaps.** The time zone is one per installation (or per siteaccess); there is no per-user time zone in the
kernel. The comment in `site.ini` still warns about threaded web servers; whether that warning still applies to
PHP-FPM and Exponential Velocity is **Unverified**.

## Right-to-left text and the Unicode Bidirectional Algorithm

**What it is.** The Unicode Bidirectional Algorithm (Unicode Standard Annex #9, UAX #9) decides the display order of
mixed left-to-right and right-to-left text. HTML exposes it through the `dir` attribute, `dir="auto"`, `<bdi>` and
the isolate characters U+2066 to U+2069.

**How Exponential complies.** Browsers run the algorithm; Exponential gives them correct direction information:

- The direction comes only from `share/locale/<code>.ini [LanguageSettings] TextDirection=rtl`. 43 locale files carry
  it (Arabic, Hebrew, Yiddish, Persian, Urdu and others), counted in `share/locale/` for this page.
- `eZLocale::textDirection()`, `eZLocale::isRightToLeft()` and `eZLocale::textDirectionOf()` expose it;
  `$site.text_direction` carries it to templates.
- Every administration page layout prints `dir="rtl"` on `<html>` for a right-to-left interface language
  (`design/admin4/templates/pagelayout.tpl`, `design/admin/templates/pagelayout.tpl` and the others listed in
  [Right-to-left languages](../features/6.0/right-to-left.md)).
- Content in a right-to-left content language gets `lang` and `dir="rtl"`
  (`design/standard/templates/parts/exp_content_direction.tpl`), also in the edit forms and the online editors.
- Names typed by editors are isolated with `dir="auto"` or `<bdi>`, and the page title uses U+2068 FIRST STRONG
  ISOLATE and U+2069 POP DIRECTIONAL ISOLATE, so mixed-direction names keep their punctuation in place.
- The administration stylesheets use CSS logical properties (`margin-inline-start` and so on).
- `expLocaleFilesTest.php` checks that every right-to-left file, and no other, has `TextDirection=rtl`.

**Status: Complies** for the administration and the content markup.

**Known gaps** (from [Right-to-left languages](../features/6.0/right-to-left.md), "Follow-ups"): several extension
stylesheets still use physical left and right. Site designs (front-end themes) are not covered by that work.

## Translation catalogues: Qt Linguist `.ts`

**What it is.** The `.ts` format is the XML translation source format of Qt Linguist (The Qt Company), here in
version 2.0 (`<TS version="2.0">`).

**How Exponential complies.**

- The interface texts of templates (`'text'|i18n( 'context' )`) and PHP (`ezpI18n::tr()`) are looked up in
  `share/translations/<code>/translation.ts` and in an extension's `translations/<code>/translation.ts`.
- 156 directories exist in `share/translations/` (155 languages plus `untranslated`, the template for new ones).
- `eZTSTranslator` (`lib/ezi18n/classes/eztstranslator.php`) validates each file against
  `schemas/translation/ts.rng` and caches it as PHP (`eztranslationcache.php`).
- Language fallbacks: `settings/i18n.ini [TranslationSettings]` (`FallbackLanguages[]`,
  `AutomaticLanguageFallback`, `FallbackMaxDepth`).
- Tests: `tests/tests/design/TranslationFilesValidTest.php` (schema validity, every template text present in
  `eng-US`, `ger-DE` and `untranslated`), `tests/tests/lib/ezi18n/expTranslationQualityTest.php`,
  `tests/tests/lib/ezi18n/expAdminTranslationsRegisteredTest.php`, and the design tests
  `AdminUsedTranslationsTest.php`, `AdminJobTranslationsTest.php`, `AdminSubitemsTranslationsTest.php`.
- `bin/php/ezchecktranslation.php` reports the state of a translation file.

**Status: Complies.**

### GNU gettext

Exponential does not use gettext: there are no `.po` or `.mo` catalogues and no gettext calls in `kernel/` or `lib/`.
**Status: Not applicable.**

### Plural forms

**What it is.** CLDR plural rules define how many plural forms a language has (one for Japanese, two for English,
three for Ukrainian, six for Arabic). Qt `.ts` files express them with `numerus="yes"` messages.

**How Exponential complies.** It does not. No `translation.ts` in `share/translations/` contains a `numerus` message,
and the translator has no plural selection. [Languages in Exponential](../languages.md) says so and recommends
neutral wording ("label: %count") instead of a plural.

**Status: Does not comply.** Languages with more than two plural forms read awkwardly for some counts.

## Known gaps

1. A stock MySQL or MariaDB connection uses 3-byte `utf8`; full Unicode needs `utf8mb4` configured by the
   administrator.
2. 20 locale files still name a legacy code page in `[Charset] Preferred`.
3. No plural forms in the translation system.
4. ICU is optional; without `intl` the country and language lists sort by bytes.
5. Internal locale codes are not BCP 47 and use some ISO 639-2 bibliographic codes; `.ts` `language` attributes are
   inconsistent.
6. One time zone per installation or siteaccess, none per user.
7. Some extension stylesheets are not yet right-to-left aware.

## Related pages

- [Languages in Exponential](../languages.md)
- [Translations and languages](../features/6.0/translations-and-languages.md)
- [Right-to-left languages](../features/6.0/right-to-left.md)
- [Content languages](../guides/content-languages.md)
- [Interface language](../features/6.0/ui-switch-languages.md)
- [Coding standards](coding-standards.md)
