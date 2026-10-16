# Languages in Exponential

The one place that says which languages Exponential supports, at which level, and how to add more. The figures
were taken on 15 October 2026 (Exponential 6.0.15).

In short: **219 locale files** (213 languages and countries plus 6 variations, in 32 scripts, 28 of them written
right to left) can be chosen as content languages and siteaccess locales; **30 administration translations**
exist, two of them complete (`eng-US`, `ger-DE`) and three first drafts (Hebrew, Latin and, for fun, Klingon).
Every locale is plain INI: nothing here needs a PHP extension at run time.

## Contents

1. [Locales, translations, content languages and siteaccesses](#1-locales-translations-content-languages-and-siteaccesses)
2. [Support levels](#2-support-levels)
3. [Every locale](#3-every-locale)
4. [Every administration translation](#4-every-administration-translation)
5. [The world's major languages](#5-the-worlds-major-languages)
6. [Common languages still missing](#6-common-languages-still-missing)
7. [Adding a locale](#7-adding-a-locale)
8. [Adding or completing an administration translation](#8-adding-or-completing-an-administration-translation)
9. [Right-to-left languages](#9-right-to-left-languages)
10. [No PHP extension at run time](#10-no-php-extension-at-run-time)
11. [The limit of 62 content languages](#11-the-limit-of-62-content-languages)
12. [Test languages: Lorem Ipsum](#12-test-languages-lorem-ipsum)

## 1. Locales, translations, content languages and siteaccesses

Four things are easily mixed up:

| Thing | Where | What it does |
|---|---|---|
| **Locale** | `share/locale/<code>.ini`, read by `eZLocale` | Names of the language and country, the HTTP language tag, the text direction, date and time formats, day and month names, the first day of the week, number and currency formats, translated country names. |
| **Administration translation** | `share/translations/<code>/translation.ts` (and an extension's `translations/<code>/`) | The texts of the interface: the `|i18n()` strings of templates and `ezpI18n::tr()` in PHP. |
| **Content language** | Setup > Languages (`ezcontent_language`) | A language content can be written in. It is a locale code, so only a locale with a file can be added. |
| **Siteaccess locale** | `site.ini [RegionalSettings] Locale` of a siteaccess | The locale a siteaccess formats with and the translation its interface uses. `SiteLanguageList[]` says which content languages it shows. |

A locale file is all a content language needs. Adding a locale file only makes the language selectable: it adds no
content language, changes no siteaccess and uses none of the at most 62 content language slots (30 on 32-bit PHP;
see [section 11](#11-the-limit-of-62-content-languages)).

The details are elsewhere and not repeated here:

- [Content languages](guides/content-languages.md): adding, showing, translating, moving and removing the languages
  content is written in.
- [Translations and languages](features/6.0/translations-and-languages.md): the state of the administration
  translations, making your own strings translatable, the HTML language tags.
- [The interface language choice](features/6.0/ui-switch-languages.md): the list box of the right toolbar, its
  groups, its A–Z list and the coverage it shows.
- [The fallback locale of a content object](bc/6.0/content-object-fallback-locale.md).

## 2. Support levels

| Level | What works | Languages |
|---|---|---|
| **Full: interface and content** | The administration speaks the language completely (at least 99% of the current strings); content, dates, numbers and currency follow the locale. | `eng-US` (and every `eng-*` locale, English being the language of the source texts), `ger-DE` |
| **Partial interface, full content** | An administration translation exists but covers 19 to 46% of today's strings (it was complete for an older release). Strings without a translation show in English. Content as above. | the 24 older translations with strings, see [section 4](#4-every-administration-translation) |
| **First draft** | Some of the most visible strings (login, menus, buttons: 214 in Hebrew, 143 in Latin, 56 in Klingon), not yet reviewed by a native speaker; the rest in English. | `heb-IL`, `lat-VA`, `tlh-QO` (Klingon's messages still marked unfinished) |
| **Content only** | No administration translation: the interface stays English. Content can be written in the language, and a siteaccess with the locale formats dates, numbers and currency for it. | every other locale, among them nearly all 170 added in 6.0.15 |

`nno-NO` has a translation file without a single translated string, so it is content only in practice.

## 3. Every locale

`share/locale` holds 219 files on 15 October 2026: 49 that existed before 6.0.15 and 170 added in 6.0.15. The add
form of Setup > Languages offers the 213 without a variation (a variation such as `ger-DE@euro` only overrides a few
keys of its base file and is chosen in `site.ini`), sorted by English name, each with its native name, its country
(unless the name already gives it) and its code, and a search field that matches any of them: `bengali`, `বাংলা`,
`ben`, `bangladesh` all find `ben-BD`. The interface language choice offers the same locales in its A–Z list, a
native list box with type-ahead (typing "Kis" jumps to Kiswahili).

The columns:

- **Direction**: `RTL` for a language written right to left ([section 9](#9-right-to-left-languages)).
- **Script**: the ISO 15924 script the file's names are written in: ICU's likely script of the language,
  checked against the letters of the file's own native name and month names (the file wins: `ser-SR` is Serbian in
  Latin script).
- **Source**: `CLDR 44` for a file generated from Unicode CLDR 44 in 6.0.15; `older, regenerated from CLDR 44` for
  an older file that was broken and generated again; `hand-made names, CLDR 44 conventions` where CLDR has no data
  for the language and the names were written by hand; `hand-made` for a file written entirely by hand; `older,
  hand-made, fixed in 6.0.15` for an older file with single values corrected by hand; `older,
  hand-made` for the files that came with earlier releases, unchanged.
- **Translation coverage**: the share of today's interface strings the locale's own administration translation
  translates, as the interface language choice shows it (`expUISwitchLanguageCatalogue::coveragePercent()`, rounded
  down; English is the source language and always 100%); `—` without a translation.

The table is generated from the locale files; do not edit it by hand.

| Code | English name | Native name | Region | Direction | Script | Source | Translation coverage |
|---|---|---|---|---|---|---|---:|
| `afr-ZA` | Afrikaans | Afrikaans | Africa | LTR | Latin (`Latn`) | CLDR 44 | — |
| `aka-GH` | Akan | Akan | Africa | LTR | Latin (`Latn`) | CLDR 44 | — |
| `amh-ET` | Amharic | አማርኛ | Africa | LTR | Ethiopic (`Ethi`) | CLDR 44 | — |
| `ara-AE` | Arabic (United Arab Emirates) | العربية (الإمارات العربية المتحدة) | Middle East | RTL | Arabic (`Arab`) | CLDR 44 | — |
| `ara-DZ` | Arabic (Algeria) | العربية (الجزائر) | Africa | RTL | Arabic (`Arab`) | CLDR 44 | — |
| `ara-EG` | Arabic (Egypt) | العربية (مصر) | Middle East | RTL | Arabic (`Arab`) | CLDR 44 | — |
| `ara-IQ` | Arabic (Iraq) | العربية (العراق) | Middle East | RTL | Arabic (`Arab`) | CLDR 44 | — |
| `ara-JO` | Arabic (Jordan) | العربية (الأردن) | Middle East | RTL | Arabic (`Arab`) | CLDR 44 | — |
| `ara-KW` | Arabic (Kuwait) | العربية (الكويت) | Middle East | RTL | Arabic (`Arab`) | CLDR 44 | — |
| `ara-LB` | Arabic (Lebanon) | العربية (لبنان) | Middle East | RTL | Arabic (`Arab`) | CLDR 44 | — |
| `ara-MA` | Arabic (Morocco) | العربية (المغرب) | Africa | RTL | Arabic (`Arab`) | CLDR 44 | — |
| `ara-QA` | Arabic (Qatar) | العربية (قطر) | Middle East | RTL | Arabic (`Arab`) | CLDR 44 | — |
| `ara-SA` | Arabic | العربية | Middle East | RTL | Arabic (`Arab`) | older, hand-made | — |
| `ara-SY` | Arabic (Syria) | العربية (سوريا) | Middle East | RTL | Arabic (`Arab`) | CLDR 44 | — |
| `ara-TN` | Arabic (Tunisia) | العربية (تونس) | Africa | RTL | Arabic (`Arab`) | CLDR 44 | — |
| `asm-IN` | Assamese | অসমীয়া | South Asia | LTR | Bengali (`Beng`) | CLDR 44 | — |
| `ast-ES` | Asturian | Asturianu | Europe | LTR | Latin (`Latn`) | CLDR 44 | — |
| `aze-AZ` | Azerbaijani | Azərbaycan | Central Asia and Caucasus | LTR | Latin (`Latn`) | CLDR 44 | — |
| `bam-ML` | Bambara | Bamanakan | Africa | LTR | Latin (`Latn`) | CLDR 44 | — |
| `bel-BY` | Belarusian | Беларуская | Europe | LTR | Cyrillic (`Cyrl`) | CLDR 44 | — |
| `ben-BD` | Bengali (Bangladesh) | বাংলা (বাংলাদেশ) | South Asia | LTR | Bengali (`Beng`) | CLDR 44 | — |
| `ben-IN` | Bengali (India) | বাংলা (ভারত) | South Asia | LTR | Bengali (`Beng`) | CLDR 44 | — |
| `bho-IN` | Bhojpuri | भोजपुरी | South Asia | LTR | Devanagari (`Deva`) | CLDR 44 | — |
| `bod-CN` | Tibetan | བོད་སྐད་ | East Asia | LTR | Tibetan (`Tibt`) | CLDR 44 | — |
| `bos-BA` | Bosnian | Bosanski | Europe | LTR | Latin (`Latn`) | CLDR 44 | — |
| `bre-FR` | Breton | Brezhoneg | Europe | LTR | Latin (`Latn`) | CLDR 44 | — |
| `brx-IN` | Bodo | बर’ | South Asia | LTR | Devanagari (`Deva`) | CLDR 44 | — |
| `bul-BG` | Bulgarian | Български | Europe | LTR | Cyrillic (`Cyrl`) | CLDR 44 | — |
| `cat-ES` | Catalan | Català | Europe | LTR | Latin (`Latn`) | older, hand-made | 44% |
| `ceb-PH` | Cebuano | Cebuano | Southeast Asia | LTR | Latin (`Latn`) | CLDR 44 | — |
| `che-RU` | Chechen | Нохчийн | Europe | LTR | Cyrillic (`Cyrl`) | CLDR 44 | — |
| `chi-CN` | Simplified Chinese | 简体中文 | East Asia | LTR | Han (Simplified) (`Hans`) | older, hand-made, fixed in 6.0.15 | 44% |
| `chi-HK` | Traditional Chinese (HongKong) | 繁體中文 (香港) | East Asia | LTR | Han (Traditional) (`Hant`) | older, hand-made, fixed in 6.0.15 | 32% |
| `chi-SG` | Simplified Chinese (Singapore) | 简体中文 (新加坡) | Southeast Asia | LTR | Han (Simplified) (`Hans`) | CLDR 44 | — |
| `chi-TW` | Traditional Chinese (Taiwan) | 繁體中文 (臺灣) | East Asia | LTR | Han (Traditional) (`Hant`) | older, hand-made | 32% |
| `ckb-IQ` | Central Kurdish | کوردیی ناوەندی | Middle East | RTL | Arabic (`Arab`) | CLDR 44 | — |
| `cro-HR` | Croatian (Hrvatski) | Croatian (Hrvatski) | Europe | LTR | Latin (`Latn`) | older, hand-made | 38% |
| `cym-GB` | Welsh | Cymraeg | Europe | LTR | Latin (`Latn`) | CLDR 44 | — |
| `cze-CZ` | Czech | Čeština | Europe | LTR | Latin (`Latn`) | older, hand-made | 38% |
| `dan-DK` | Danish | Dansk | Europe | LTR | Latin (`Latn`) | older, hand-made | 19% |
| `div-MV` | Dhivehi | ދިވެހި | South Asia | RTL | Thaana (`Thaa`) | hand-made names, CLDR 44 conventions | — |
| `doi-IN` | Dogri | डोगरी | South Asia | LTR | Devanagari (`Deva`) | CLDR 44 | — |
| `dsb-DE` | Lower Sorbian | Dolnoserbšćina | Europe | LTR | Latin (`Latn`) | CLDR 44 | — |
| `dut-BE` | Dutch (Belgium) | Nederlands (België) | Europe | LTR | Latin (`Latn`) | CLDR 44 | — |
| `dut-NL` | Dutch | Nederlands | Europe | LTR | Latin (`Latn`) | older, hand-made | 39% |
| `dzo-BT` | Dzongkha | རྫོང་ཁ | South Asia | LTR | Tibetan (`Tibt`) | CLDR 44 | — |
| `ell-GR` | Greek (Hellenic) | Ελληνικά | Europe | LTR | Greek (`Grek`) | older, hand-made, fixed in 6.0.15 | 42% |
| `eng-AU` | English (Australia) | English (Australia) | Oceania | LTR | Latin (`Latn`) | older, hand-made | 100% |
| `eng-CA` | English (Canada) | English (Canada) | Americas | LTR | Latin (`Latn`) | older, hand-made | 100% |
| `eng-GB` | English (United Kingdom) | English (United Kingdom) | Europe | LTR | Latin (`Latn`) | older, hand-made | 100% |
| `eng-GB@euro` | Variant of `eng-GB`: Euro support |  | Europe | LTR | Latin (`Latn`) | older variation, hand-made | — |
| `eng-GH` | English (Ghana) | English (Ghana) | Africa | LTR | Latin (`Latn`) | CLDR 44 | 100% |
| `eng-IE` | English (Ireland) | English (Ireland) | Europe | LTR | Latin (`Latn`) | CLDR 44 | 100% |
| `eng-IN` | English (India) | English (India) | South Asia | LTR | Latin (`Latn`) | CLDR 44 | 100% |
| `eng-KE` | English (Kenya) | English (Kenya) | Africa | LTR | Latin (`Latn`) | CLDR 44 | 100% |
| `eng-NG` | English (Nigeria) | English (Nigeria) | Africa | LTR | Latin (`Latn`) | CLDR 44 | 100% |
| `eng-NZ` | English (New Zealand) | English (New Zealand) | Oceania | LTR | Latin (`Latn`) | older, hand-made | 100% |
| `eng-PH` | English (Philippines) | English (Philippines) | Southeast Asia | LTR | Latin (`Latn`) | CLDR 44 | 100% |
| `eng-PK` | English (Pakistan) | English (Pakistan) | South Asia | LTR | Latin (`Latn`) | CLDR 44 | 100% |
| `eng-SG` | English (Singapore) | English (Singapore) | Southeast Asia | LTR | Latin (`Latn`) | CLDR 44 | 100% |
| `eng-US` | English (American) | English (American) | Americas | LTR | Latin (`Latn`) | older, hand-made | 100% |
| `eng-ZA` | English (South Africa) | English (South Africa) | Africa | LTR | Latin (`Latn`) | CLDR 44 | 100% |
| `epo-EO` | Esperanto | Esperanto | Worldwide | LTR | Latin (`Latn`) | older, hand-made | — |
| `esl-AR` | Spanish (Argentina) | Español (Argentina) | Americas | LTR | Latin (`Latn`) | CLDR 44 | — |
| `esl-BO` | Spanish (Bolivia) | Español (Bolivia) | Americas | LTR | Latin (`Latn`) | CLDR 44 | — |
| `esl-CL` | Spanish (Chile) | Español (Chile) | Americas | LTR | Latin (`Latn`) | CLDR 44 | — |
| `esl-CO` | Spanish (Colombia) | Español (Colombia) | Americas | LTR | Latin (`Latn`) | CLDR 44 | — |
| `esl-CU` | Spanish (Cuba) | Español (Cuba) | Americas | LTR | Latin (`Latn`) | CLDR 44 | — |
| `esl-DO` | Spanish (Dominican Republic) | Español (República Dominicana) | Americas | LTR | Latin (`Latn`) | CLDR 44 | — |
| `esl-EC` | Spanish (Ecuador) | Español (Ecuador) | Americas | LTR | Latin (`Latn`) | CLDR 44 | — |
| `esl-ES` | Spanish (Spain) | Español (España) | Europe | LTR | Latin (`Latn`) | older, hand-made | 46% |
| `esl-GT` | Spanish (Guatemala) | Español (Guatemala) | Americas | LTR | Latin (`Latn`) | CLDR 44 | — |
| `esl-MX` | Spanish (Mexico) | Español (México) | Americas | LTR | Latin (`Latn`) | older, hand-made | 45% |
| `esl-PE` | Spanish (Peru) | Español (Perú) | Americas | LTR | Latin (`Latn`) | CLDR 44 | — |
| `esl-US` | Spanish (United States) | Español (Estados Unidos) | Americas | LTR | Latin (`Latn`) | CLDR 44 | — |
| `esl-UY` | Spanish (Uruguay) | Español (Uruguay) | Americas | LTR | Latin (`Latn`) | CLDR 44 | — |
| `esl-VE` | Spanish (Venezuela) | Español (Venezuela) | Americas | LTR | Latin (`Latn`) | CLDR 44 | — |
| `est-EE` | Estonian | Eesti | Europe | LTR | Latin (`Latn`) | CLDR 44 | — |
| `eus-ES` | Basque | Euskara | Europe | LTR | Latin (`Latn`) | CLDR 44 | — |
| `ewe-GH` | Ewe | Eʋegbe | Africa | LTR | Latin (`Latn`) | CLDR 44 | — |
| `fao-FO` | Faroese | Føroyskt | Europe | LTR | Latin (`Latn`) | CLDR 44 | — |
| `fas-AF` | Dari (Afghanistan) | دری (افغانستان) | Central Asia and Caucasus | RTL | Arabic (`Arab`) | CLDR 44 | — |
| `fas-IR` | Persian | فارسی | Middle East | RTL | Arabic (`Arab`) | CLDR 44 | — |
| `fil-PH` | Filipino | Filipino | Southeast Asia | LTR | Latin (`Latn`) | CLDR 44 | — |
| `fin-FI` | Finnish | Suomi | Europe | LTR | Latin (`Latn`) | older, hand-made | 38% |
| `fre-BE` | French (Belgium) | Français (Belgique) | Europe | LTR | Latin (`Latn`) | older, hand-made | — |
| `fre-CA` | French (Canada) | Français (Canada) | Americas | LTR | Latin (`Latn`) | older, hand-made | — |
| `fre-CD` | French (Congo - Kinshasa) | Français (Congo-Kinshasa) | Africa | LTR | Latin (`Latn`) | CLDR 44 | — |
| `fre-CH` | French (Switzerland) | Français (Suisse) | Europe | LTR | Latin (`Latn`) | CLDR 44 | — |
| `fre-CI` | French (Côte d’Ivoire) | Français (Côte d’Ivoire) | Africa | LTR | Latin (`Latn`) | CLDR 44 | — |
| `fre-CM` | French (Cameroon) | Français (Cameroun) | Africa | LTR | Latin (`Latn`) | CLDR 44 | — |
| `fre-FR` | French (France) | Français (France) | Europe | LTR | Latin (`Latn`) | older, hand-made | 46% |
| `fre-MA` | French (Morocco) | Français (Maroc) | Africa | LTR | Latin (`Latn`) | CLDR 44 | — |
| `fre-SN` | French (Senegal) | Français (Sénégal) | Africa | LTR | Latin (`Latn`) | CLDR 44 | — |
| `fry-NL` | Western Frisian | Frysk | Europe | LTR | Latin (`Latn`) | CLDR 44 | — |
| `ful-SN` | Fula | Pulaar | Africa | LTR | Latin (`Latn`) | CLDR 44 | — |
| `ger-AT` | German (Austria) | Deutsch (Österreich) | Europe | LTR | Latin (`Latn`) | CLDR 44 | — |
| `ger-CH` | German (Switzerland) | Deutsch (Schweiz) | Europe | LTR | Latin (`Latn`) | CLDR 44 | — |
| `ger-DE` | German | Deutsch (Deutschland) | Europe | LTR | Latin (`Latn`) | older, hand-made | 99% |
| `ger-DE@euro` | Variant of `ger-DE`: Euro support |  | Europe | LTR | Latin (`Latn`) | older variation, hand-made | — |
| `gla-GB` | Scottish Gaelic | Gàidhlig | Europe | LTR | Latin (`Latn`) | CLDR 44 | — |
| `gle-IE` | Irish | Gaeilge | Europe | LTR | Latin (`Latn`) | CLDR 44 | — |
| `glg-ES` | Galician | Galego | Europe | LTR | Latin (`Latn`) | CLDR 44 | — |
| `guj-IN` | Gujarati | ગુજરાતી | South Asia | LTR | Gujarati (`Gujr`) | CLDR 44 | — |
| `hau-NG` | Hausa | Hausa | Africa | LTR | Latin (`Latn`) | CLDR 44 | — |
| `haw-US` | Hawaiian | ʻŌlelo Hawaiʻi | Oceania | LTR | Latin (`Latn`) | CLDR 44 | — |
| `heb-IL` | Hebrew | עברית | Middle East | RTL | Hebrew (`Hebr`) | older, regenerated from CLDR 44 | 1% |
| `hin-IN` | Hindi | हिन्दी | South Asia | LTR | Devanagari (`Deva`) | older, regenerated from CLDR 44 | — |
| `hsb-DE` | Upper Sorbian | Hornjoserbšćina | Europe | LTR | Latin (`Latn`) | CLDR 44 | — |
| `hun-HU` | Hungarian | Magyar | Europe | LTR | Latin (`Latn`) | older, hand-made | — |
| `hye-AM` | Armenian | Հայերեն | Central Asia and Caucasus | LTR | Armenian (`Armn`) | CLDR 44 | — |
| `ibo-NG` | Igbo | Igbo | Africa | LTR | Latin (`Latn`) | CLDR 44 | — |
| `ind-ID` | Indonesian | Indonesian | Southeast Asia | LTR | Latin (`Latn`) | older, hand-made | — |
| `isl-IS` | Icelandic | Íslenska | Europe | LTR | Latin (`Latn`) | CLDR 44 | — |
| `ita-CH` | Italian (Switzerland) | Italiano (Svizzera) | Europe | LTR | Latin (`Latn`) | CLDR 44 | — |
| `ita-IT` | Italian | Italiano | Europe | LTR | Latin (`Latn`) | older, hand-made | 44% |
| `jav-ID` | Javanese | Jawa | Southeast Asia | LTR | Latin (`Latn`) | CLDR 44 | — |
| `jpn-JP` | Japanese | 日本語 | East Asia | LTR | Japanese (Han, Hiragana, Katakana) (`Jpan`) | older, hand-made | 44% |
| `jpn-JP@international` | Variant of `jpn-JP`: @international |  | East Asia | LTR | Japanese (Han, Hiragana, Katakana) (`Jpan`) | older variation, hand-made | — |
| `kab-DZ` | Kabyle | Taqbaylit | Africa | LTR | Latin (`Latn`) | CLDR 44 | — |
| `kal-GL` | Greenlandic | Kalaallisut | Americas | LTR | Latin (`Latn`) | CLDR 44 | — |
| `kan-IN` | Kannada | ಕನ್ನಡ | South Asia | LTR | Kannada (`Knda`) | CLDR 44 | — |
| `kas-IN` | Kashmiri | کٲشُر | South Asia | RTL | Arabic (`Arab`) | CLDR 44 | — |
| `kat-GE` | Georgian | ქართული | Central Asia and Caucasus | LTR | Georgian (`Geor`) | CLDR 44 | — |
| `kaz-KZ` | Kazakh | Қазақ тілі | Central Asia and Caucasus | LTR | Cyrillic (`Cyrl`) | CLDR 44 | — |
| `khm-KH` | Khmer | ខ្មែរ | Southeast Asia | LTR | Khmer (`Khmr`) | CLDR 44 | — |
| `kik-KE` | Kikuyu | Gikuyu | Africa | LTR | Latin (`Latn`) | CLDR 44 | — |
| `kin-RW` | Kinyarwanda | Kinyarwanda | Africa | LTR | Latin (`Latn`) | CLDR 44 | — |
| `kir-KG` | Kyrgyz | Кыргызча | Central Asia and Caucasus | LTR | Cyrillic (`Cyrl`) | CLDR 44 | — |
| `kok-IN` | Konkani | कोंकणी | South Asia | LTR | Devanagari (`Deva`) | CLDR 44 | — |
| `kor-KR` | Korean | 한국어 | East Asia | LTR | Korean (Hangul, Han) (`Kore`) | older, regenerated from CLDR 44 | — |
| `kur-TR` | Kurdish | Kurdî (kurmancî) | Middle East | LTR | Latin (`Latn`) | CLDR 44 | — |
| `lao-LA` | Lao | ລາວ | Southeast Asia | LTR | Lao (`Laoo`) | CLDR 44 | — |
| `lat-VA` | Latin | Latina | Europe | LTR | Latin (`Latn`) | hand-made names, CLDR 44 conventions | 1% |
| `lav-LV` | Latvian | Latviešu | Europe | LTR | Latin (`Latn`) | CLDR 44 | — |
| `lin-CD` | Lingala | Lingála | Africa | LTR | Latin (`Latn`) | CLDR 44 | — |
| `lit-LT` | Lithuanian | Lietuvių | Europe | LTR | Latin (`Latn`) | CLDR 44 | — |
| `ltz-LU` | Luxembourgish | Lëtzebuergesch | Europe | LTR | Latin (`Latn`) | CLDR 44 | — |
| `lug-UG` | Ganda | Luganda | Africa | LTR | Latin (`Latn`) | CLDR 44 | — |
| `mai-IN` | Maithili | मैथिली | South Asia | LTR | Devanagari (`Deva`) | CLDR 44 | — |
| `mal-IN` | Malayalam | മലയാളം | South Asia | LTR | Malayalam (`Mlym`) | CLDR 44 | — |
| `mar-IN` | Marathi | मराठी | South Asia | LTR | Devanagari (`Deva`) | CLDR 44 | — |
| `mkd-MK` | Macedonian | Македонски | Europe | LTR | Cyrillic (`Cyrl`) | CLDR 44 | — |
| `mlg-MG` | Malagasy | Malagasy | Africa | LTR | Latin (`Latn`) | CLDR 44 | — |
| `mlt-MT` | Maltese | Malti | Europe | LTR | Latin (`Latn`) | CLDR 44 | — |
| `mni-IN` | Manipuri | মৈতৈলোন্ | South Asia | LTR | Bengali (`Beng`) | CLDR 44 | — |
| `mon-MN` | Mongolian | Монгол | East Asia | LTR | Cyrillic (`Cyrl`) | CLDR 44 | — |
| `mri-NZ` | Māori | Māori | Oceania | LTR | Latin (`Latn`) | CLDR 44 | — |
| `msa-BN` | Malay (Brunei) | Melayu (Brunei) | Southeast Asia | LTR | Latin (`Latn`) | CLDR 44 | — |
| `msa-MY` | Malay (Malaysia) | Melayu (Malaysia) | Southeast Asia | LTR | Latin (`Latn`) | CLDR 44 | — |
| `msa-SG` | Malay (Singapore) | Melayu (Singapura) | Southeast Asia | LTR | Latin (`Latn`) | CLDR 44 | — |
| `mya-MM` | Burmese | မြန်မာ | Southeast Asia | LTR | Myanmar (`Mymr`) | CLDR 44 | — |
| `nep-NP` | Nepali | नेपाली | South Asia | LTR | Devanagari (`Deva`) | CLDR 44 | — |
| `nno-NO` | Norwegian (Nynorsk) | Norsk (Nynorsk) | Europe | LTR | Latin (`Latn`) | older, hand-made | 0% |
| `nor-NO` | Norwegian (Bokmal) | Norsk (Bokmål) | Europe | LTR | Latin (`Latn`) | older, hand-made | 42% |
| `nor-NO@intl` | Variant of `nor-NO`: Internasjonalt Format |  | Europe | LTR | Latin (`Latn`) | older variation, hand-made | — |
| `nor-NO@spraakraad` | Variant of `nor-NO`: Norsk Språkråd |  | Europe | LTR | Latin (`Latn`) | older variation, hand-made | — |
| `nqo-GN` | N’Ko | ߒߞߏ | Africa | RTL | N’Ko (`Nkoo`) | CLDR 44 | — |
| `oci-FR` | Occitan | Occitan | Europe | LTR | Latin (`Latn`) | CLDR 44 | — |
| `ori-IN` | Odia | ଓଡ଼ିଆ | South Asia | LTR | Oriya (Odia) (`Orya`) | CLDR 44 | — |
| `orm-ET` | Oromo | Oromoo | Africa | LTR | Latin (`Latn`) | CLDR 44 | — |
| `pan-IN` | Punjabi | ਪੰਜਾਬੀ | South Asia | LTR | Gurmukhi (`Guru`) | CLDR 44 | — |
| `pcm-NG` | Nigerian Pidgin | Naijíriá Píjin | Africa | LTR | Latin (`Latn`) | CLDR 44 | — |
| `pnb-PK` | Western Punjabi (Shahmukhi) | پنجابی | South Asia | RTL | Arabic (`Arab`) | CLDR 44 | — |
| `pol-PL` | Polish | Polski | Europe | LTR | Latin (`Latn`) | older, hand-made | 38% |
| `por-AO` | Portuguese (Angola) | Português (Angola) | Africa | LTR | Latin (`Latn`) | CLDR 44 | — |
| `por-BR` | Portuguese (Brazil) | Português | Americas | LTR | Latin (`Latn`) | older, hand-made | 45% |
| `por-CV` | Portuguese (Cape Verde) | Português (Cabo Verde) | Africa | LTR | Latin (`Latn`) | CLDR 44 | — |
| `por-MZ` | Portuguese (Mozambique) | Português | Africa | LTR | Latin (`Latn`) | older, hand-made | — |
| `por-PT` | Portuguese (Portugal) | Português | Europe | LTR | Latin (`Latn`) | older, hand-made | — |
| `por-PT@euro` | Variant of `por-PT`: Suporte para Euro |  | Europe | LTR | Latin (`Latn`) | older variation, hand-made | — |
| `pus-AF` | Pashto | پښتو | South Asia | RTL | Arabic (`Arab`) | CLDR 44 | — |
| `que-PE` | Quechua | Runasimi | Americas | LTR | Latin (`Latn`) | CLDR 44 | — |
| `roh-CH` | Romansh | Rumantsch | Europe | LTR | Latin (`Latn`) | CLDR 44 | — |
| `ron-RO` | Romanian | Română | Europe | LTR | Latin (`Latn`) | CLDR 44 | — |
| `run-BI` | Kirundi | Ikirundi | Africa | LTR | Latin (`Latn`) | CLDR 44 | — |
| `rus-RU` | Russian | Русский | Europe | LTR | Cyrillic (`Cyrl`) | older, hand-made | 38% |
| `san-IN` | Sanskrit | संस्कृत भाषा | South Asia | LTR | Devanagari (`Deva`) | CLDR 44 | — |
| `sat-IN` | Santali | ᱥᱟᱱᱛᱟᱲᱤ | South Asia | LTR | Ol Chiki (`Olck`) | CLDR 44 | — |
| `ser-SR` | Serbian (Srpski) | Serbian (Srpski) | Europe | LTR | Latin (`Latn`) | older, hand-made | 28% |
| `sin-LK` | Sinhala | සිංහල | South Asia | LTR | Sinhala (`Sinh`) | CLDR 44 | — |
| `slk-SK` | Slovak | Slovenčina | Europe | LTR | Latin (`Latn`) | older, hand-made | — |
| `slv-SI` | Slovenian | Slovenščina | Europe | LTR | Latin (`Latn`) | CLDR 44 | — |
| `sme-NO` | Northern Sami | Davvisámegiella | Europe | LTR | Latin (`Latn`) | CLDR 44 | — |
| `sna-ZW` | Shona | ChiShona | Africa | LTR | Latin (`Latn`) | CLDR 44 | — |
| `snd-PK` | Sindhi | سنڌي | South Asia | RTL | Arabic (`Arab`) | CLDR 44 | — |
| `som-SO` | Somali | Soomaali | Africa | LTR | Latin (`Latn`) | CLDR 44 | — |
| `sqi-AL` | Albanian | Shqip | Europe | LTR | Latin (`Latn`) | CLDR 44 | — |
| `srp-RS` | Serbian (Српски) | Serbian (Српски) | Europe | LTR | Cyrillic (`Cyrl`) | older, hand-made | 38% |
| `sun-ID` | Sundanese | Basa Sunda | Southeast Asia | LTR | Latin (`Latn`) | CLDR 44 | — |
| `swa-KE` | Swahili (Kenya) | Kiswahili (Kenya) | Africa | LTR | Latin (`Latn`) | CLDR 44 | — |
| `swa-TZ` | Swahili (Tanzania) | Kiswahili (Tanzania) | Africa | LTR | Latin (`Latn`) | CLDR 44 | — |
| `swe-SE` | Swedish | Svenska | Europe | LTR | Latin (`Latn`) | older, hand-made | 45% |
| `syr-IQ` | Syriac (Iraq) | ܣܘܪܝܝܐ (ܥܝܪܩ) | Middle East | RTL | Syriac (`Syrc`) | CLDR 44 | — |
| `syr-SY` | Syriac (Syria) | ܣܘܪܝܝܐ (ܣܘܪܝܐ) | Middle East | RTL | Syriac (`Syrc`) | CLDR 44 | — |
| `tam-IN` | Tamil (India) | தமிழ் (இந்தியா) | South Asia | LTR | Tamil (`Taml`) | CLDR 44 | — |
| `tam-LK` | Tamil (Sri Lanka) | தமிழ் (இலங்கை) | South Asia | LTR | Tamil (`Taml`) | CLDR 44 | — |
| `tam-SG` | Tamil (Singapore) | தமிழ் (சிங்கப்பூர்) | Southeast Asia | LTR | Tamil (`Taml`) | CLDR 44 | — |
| `tat-RU` | Tatar | Татар | Europe | LTR | Cyrillic (`Cyrl`) | CLDR 44 | — |
| `tel-IN` | Telugu | తెలుగు | South Asia | LTR | Telugu (`Telu`) | CLDR 44 | — |
| `tgk-TJ` | Tajik | Тоҷикӣ | Central Asia and Caucasus | LTR | Cyrillic (`Cyrl`) | CLDR 44 | — |
| `tha-TH` | Thai | ไทย | Southeast Asia | LTR | Thai (`Thai`) | CLDR 44 | — |
| `tir-ET` | Tigrinya | ትግርኛ | Africa | LTR | Ethiopic (`Ethi`) | CLDR 44 | — |
| `tlh-QO` | Klingon | tlhIngan Hol | Worldwide (fictional) | LTR | Latin (`Latn`) | hand-made | 0% |
| `ton-TO` | Tongan | Lea fakatonga | Oceania | LTR | Latin (`Latn`) | CLDR 44 | — |
| `tuk-TM` | Turkmen | Türkmen dili | Central Asia and Caucasus | LTR | Latin (`Latn`) | CLDR 44 | — |
| `tur-TR` | Turkish | Türkçe | Middle East | LTR | Latin (`Latn`) | older, hand-made | — |
| `uig-CN` | Uyghur | ئۇيغۇرچە | East Asia | RTL | Arabic (`Arab`) | CLDR 44 | — |
| `ukr-UA` | Ukrainian | Українська | Europe | LTR | Cyrillic (`Cyrl`) | older, hand-made, fixed in 6.0.15 | — |
| `urd-IN` | Urdu (India) | اردو (بھارت) | South Asia | RTL | Arabic (`Arab`) | CLDR 44 | — |
| `urd-PK` | Urdu (Pakistan) | اردو (پاکستان) | South Asia | RTL | Arabic (`Arab`) | CLDR 44 | — |
| `uzb-UZ` | Uzbek | O‘zbek | Central Asia and Caucasus | LTR | Latin (`Latn`) | CLDR 44 | — |
| `vie-VN` | Vietnamese | Tiếng Việt | Southeast Asia | LTR | Latin (`Latn`) | CLDR 44 | — |
| `wol-SN` | Wolof | Wolof | Africa | LTR | Latin (`Latn`) | CLDR 44 | — |
| `xho-ZA` | Xhosa | IsiXhosa | Africa | LTR | Latin (`Latn`) | CLDR 44 | — |
| `yid-US` | Yiddish | ייִדיש | Americas | RTL | Hebrew (`Hebr`) | CLDR 44 | — |
| `yor-NG` | Yoruba | Èdè Yorùbá | Africa | LTR | Latin (`Latn`) | CLDR 44 | — |
| `zgh-MA` | Standard Moroccan Tamazight | ⵜⴰⵎⴰⵣⵉⵖⵜ | Africa | LTR | Tifinagh (`Tfng`) | CLDR 44 | — |
| `zul-ZA` | Zulu | IsiZulu | Africa | LTR | Latin (`Latn`) | CLDR 44 | — |
| `qlo-ZZ` | Lorem Ipsum | Lorem Ipsum | None (test language) | LTR | Latin (`Latn`) | hand-made, test language only | — |

How the generated files were made, and where they differ from CLDR, is in [section 7](#7-adding-a-locale).
Notes on single locales:

- **Latin** (`lat-VA`) is a classical language: no country speaks it as a mother tongue, and the Holy See (Vatican
  City), where it is an official language, stands as its country. CLDR 44 has no Latin data, so its numbers,
  currency (euro) and date patterns follow the Holy See's conventions (CLDR `it_VA`) and its names are written by
  hand: `Latina`, nominative month names (`Ianuarius`), the ecclesiastical day names the Holy See uses (`Dominica`,
  `Feria secunda` ... `Sabbatum`); the long date shows the short month (`Feria tertia, 14 Nov. 2023`). Its country
  names are CLDR's English ones.
- **Hebrew** (`heb-IL`) existed before; its file named `windows-1255` and held double-encoded month and day names.
  It was regenerated from CLDR `he_IL` in 6.0.15 (UTF-8, `he-IL`, `₪`, CLDR's Hebrew country names). `hin-IN` and
  `kor-KR` were regenerated as well: `hin-IN` had English names ("Hindi (Devnagari)", English months and days) and
  country names with characters lost to `?`; `kor-KR` had an invalid long date (the year marker 년 before the year)
  and short day names equal to the long ones.
- **Fixed by hand** in 6.0.15, the rest of the older file kept: `chi-HK` (its long date printed 月 twice, and it was
  the one file with a byte order mark), `chi-CN` (short Sunday `周日`), `ell-GR` (native name `Ελληνικά` instead of
  "Greek (Hellenic)"), `ukr-UA` (`Українська` instead of "Ukrainian"). `kat-GE` lost a capital its generator had
  given it (Georgian has none in running text: `ქართული`).
- **Still to improve** in older files: `ind-ID` has only its English name ("Indonesian"; CLDR: Indonesia), `cro-HR`,
  `ser-SR` and `srp-RS` mix English and native ("Serbian (Српски)", and `srp-RS` capitalizes its day names), `ukr-UA`
  has nominative month names in dates ("15 Жовтень" for "15 жовтня"), `chi-CN` and `chi-TW` keep their old date
  patterns (`2025年 十月 15日`, readable but not CLDR's `2025年10月15日`).
- **Dhivehi** (`div-MV`): ICU 74 has no Dhivehi data. Numbers, the rufiyaa (symbol `Rf`) and date patterns follow
  CLDR `en_MV`; the language name (ދިވެހި, English "Dhivehi", the Maldivian spelling), the currency name and the
  month and day names in Thaana are written by hand and need a native speaker's review. CLDR starts the week on
  Friday in the Maldives; `eZLocale` knows only Monday and Sunday, so the week starts on Sunday.
- **Yiddish** (`yid-US`): CLDR 44 has Yiddish only as `yi_UA` (until CLDR 43 `yi_001`, "world"), and a locale code
  needs a two-letter country. The largest Yiddish-speaking community lives in the United States, so the file has
  CLDR's Yiddish names with the conventions and currency of the United States.
- **Dari** (`fas-AF`) is the Persian of Afghanistan, as in CLDR (`fa_AF`, display name "Dari", دری); `fas-IR`
  stays "Persian".
- **Central Kurdish** (`ckb-IQ`, Sorani, Arabic script, right to left) has no ISO 639-2 code (639-2 has only `kur`
  for all of Kurdish, which `kur-TR`, Kurmanji in Latin script, already uses), so it takes its ISO 639-3 code.
- **Klingon** (`tlh-QO`) is hand-made, for fun and as a test of a locale without CLDR data. `tlh` is the ISO 639-2
  code of Klingon; `QO` lies in the ISO 3166 user-assigned range (QM to QZ), so the country is
  `None (private-use region QO)` and the HTTP tag is the bare `tlh`, which claims no real place (like `eo` for
  `epo-EO`). It has no currency (ISO 4217 `XXX`, sign `¤`), numbers as `eng-US`, the 24-hour clock, Marc Okrand's
  day names (`DaSjaj` ... `lojmItjaj`) and the months as "month" plus a number (`jar wa'` ... `jar wa'maH cha'`),
  in the Latin transcription. It is offered in the A–Z list of the interface language choice only, in no group.
  The Klingon script pIqaD is not in Unicode proper (the ConScript Unicode Registry places it in the Private Use
  Area, U+F8D0 to U+F8FF) and is never used in interface texts or locale files; a site that wants it for decoration
  loads a ConScript-compatible font itself, while the text stays in the transcription.
- **Interlingua** (`ia_001`) is not offered: CLDR has it only for the region "world" (`001`), and a locale code needs
  a two-letter country.
- Test languages, marked `[LanguageSettings] TestLanguage=true` (Lorem Ipsum, `qlo-ZZ`), are listed last in the
  table, as "test language only", and counted nowhere in this document.

### Scripts, fonts and line breaking

A locale file holds text only; whether a script shows depends on the fonts of the reader's system. The
administration designs (admin4, admin4l and the classic admin share `components.css`) use system fonts only and
download nothing. Their font token `--a4-font` keeps its Latin families first (so Latin text is unchanged) and then
names, through `--a4-font-intl`, the common system fonts of the other scripts: Chinese, Japanese and Korean
(`--a4-font-cjk`: PingFang, Hiragino, Microsoft YaHei and JhengHei, Yu Gothic, Meiryo, Malgun Gothic, Apple SD
Gothic Neo, Noto Sans CJK), Thai (Leelawadee UI, Thonburi, Noto Sans Thai), Devanagari (Nirmala UI, Kohinoor
Devanagari, Noto Sans Devanagari), Arabic and Hebrew (Segoe UI, Noto Sans Arabic, Geeza Pro, Noto Sans Hebrew, Arial
Hebrew). Han characters are shared by Chinese, Japanese and Korean but drawn differently in each, so the order of
`--a4-font-cjk` follows the interface language (`:lang(ja)`, `:lang(ko)`, `:lang(zh-TW)`, `:lang(zh-HK)`: that
language's fonts first; Simplified Chinese first otherwise). The classic admin and the Admin UI add `--a4-font-intl`
after their own Latin families. A font that also carries Latin letters (Droid Sans Fallback) is left out on
purpose: on a system without the Latin families it would take over Latin text as well. Any script not covered falls
back to what the system offers.

Line breaking follows the language of the page or of an element with `lang`:

```css
html:lang(ja), [lang]:lang(ja) { line-break: strict; }
html:lang(ko), [lang]:lang(ko) { word-break: keep-all; overflow-wrap: break-word; }
html:lang(th|lo|km|my), [lang]:lang(th|lo|km|my) { overflow-wrap: break-word; }
```

Japanese avoids a break before small kana and closing punctuation; Korean breaks at the spaces between words, as
it is set; Thai, Lao, Khmer and Burmese, written without spaces, break at the word boundaries of the browser's
dictionary, with `break-word` as a safety net for a run it does not know. Chinese needs nothing: browsers break
between ideographs. The Klingon script pIqaD is never used (see Klingon above).

## 4. Every administration translation

Measured on 15 October 2026, in two ways (both rounded down):

- **Own file finished**: finished messages of the file divided by all its messages, obsolete ones left out, each
  context, source and comment counted once. `php bin/php/ezchecktranslation.php <code>` prints a similar "Percent
  finished".
- **Current strings translated**: how many of the 14,075 strings in `share/translations/untranslated/translation.ts`
  (every string the kernel and its designs ask for today) the file translates, same context, source and comment,
  finished and not empty. This is what an administrator sees, and what the interface language choice shows as
  coverage: the rest shows in English.

Most translations were complete for the strings of older releases; 6.0 added about 7,500 strings (admin4, the
new Setup pages, audit, packages, jobs and more) that only `eng-US` and `ger-DE` translate so far.

| Translation | Own file finished | Current strings translated |
|---|---:|---:|
| `eng-US` | 100% (16,626 of 16,626) | 100% (14,075 of 14,075) |
| `ger-DE` | 99% (16,504 of 16,519) | 99% (13,969 of 14,075) |
| `fre-FR` | 99% (6,510 of 6,511) | 46% (6,510 of 14,075) |
| `esl-ES` | 99% (6,509 of 6,511) | 46% (6,509 of 14,075) |
| `por-BR` | 99% (6,465 of 6,512) | 45% (6,465 of 14,075) |
| `swe-SE` | 99% (6,463 of 6,512) | 45% (6,463 of 14,075) |
| `esl-MX` | 98% (6,389 of 6,511) | 45% (6,389 of 14,075) |
| `ita-IT` | 97% (6,319 of 6,512) | 44% (6,319 of 14,075) |
| `cat-ES` | 96% (6,314 of 6,512) | 44% (6,314 of 14,075) |
| `jpn-JP` | 96% (6,281 of 6,513) | 44% (6,281 of 14,075) |
| `chi-CN` | 95% (6,210 of 6,512) | 44% (6,210 of 14,075) |
| `ell-GR` | 92% (6,019 of 6,512) | 42% (6,019 of 14,075) |
| `nor-NO` | 92% (6,003 of 6,512) | 42% (6,003 of 14,075) |
| `dut-NL` | 85% (5,565 of 6,512) | 39% (5,565 of 14,075) |
| `eng-AU` | 84% (5,499 of 6,512) | 39% (5,499 of 14,075) |
| `eng-NZ` | 84% (5,499 of 6,512) | 39% (5,499 of 14,075) |
| `cro-HR` | 83% (5,463 of 6,512) | 38% (5,463 of 14,075) |
| `pol-PL` | 83% (5,407 of 6,512) | 38% (5,407 of 14,075) |
| `fin-FI` | 82% (5,369 of 6,512) | 38% (5,369 of 14,075) |
| `cze-CZ` | 82% (5,367 of 6,512) | 38% (5,367 of 14,075) |
| `srp-RS` | 82% (5,357 of 6,511) | 38% (5,357 of 14,075) |
| `rus-RU` | 82% (5,351 of 6,512) | 38% (5,351 of 14,075) |
| `chi-HK` | 70% (4,592 of 6,509) | 32% (4,592 of 14,075) |
| `chi-TW` | 70% (4,592 of 6,509) | 32% (4,592 of 14,075) |
| `ser-SR` | 60% (3,957 of 6,512) | 28% (3,957 of 14,075) |
| `dan-DK` | 41% (2,696 of 6,512) | 19% (2,696 of 14,075) |
| `heb-IL` | 36% (214 of 581) | 1% (214 of 14,075) |
| `lat-VA` | 24% (143 of 578) | 1% (143 of 14,075) |
| `nno-NO` | 0% (0 of 6,512) | 0% (0 of 14,075) |
| `tlh-QO` | 0% (0 of 56) | 0% (0 of 14,075) |

Notes:

- **Hebrew** (`heb-IL`) and **Latin** (`lat-VA`) are first drafts of the most visible strings (login, the main
  menus, the common buttons and messages), written for 6.0.15. **Klingon** (`tlh-QO`) is a playful first draft of
  56 of those strings, taken from Marc Okrand's *The Klingon Dictionary* (`nej` Search, `mej` Logout, `pegh mu'`
  Password, `SeHlaw` Setup, `veQ` Trash, `chaw' tu'lu'be'!` Access denied). All three need a native (or fluent)
  speaker's review. Klingon's messages are all marked unfinished, so it counts 0% (and shows English) until someone
  fluent confirms them.
- Two directories have no locale file of the same meaning: `ser-SR` and `srp-RS` are both Serbian (`ser-SR` in Latin
  script, `srp-RS` in Cyrillic; both locale files exist).
- Translations of extensions live in each extension's `translations/` directory and are not counted here.

## 5. The world's major languages

The languages with 50 million or more speakers (first and second language together), as listed by
[Wikipedia's "List of languages by total number of speakers"](https://en.wikipedia.org/wiki/List_of_languages_by_total_number_of_speakers),
which takes its figures from Ethnologue (2026 edition); read on 15 October 2026. Speakers are rounded millions.

| # | Language | Speakers (M) | Locales | Administration translation |
|---:|---|---:|---|---|
| 1 | English | 1,493 | `eng-US`, `eng-GB`, `eng-CA`, `eng-AU`, `eng-NZ`, `eng-IE`, `eng-IN`, `eng-SG`, `eng-ZA`, `eng-NG`, `eng-KE`, `eng-GH`, `eng-PK`, `eng-PH` | `eng-US` full; `eng-AU`, `eng-NZ` partial |
| 2 | Mandarin Chinese | 1,183 | `chi-CN`, `chi-SG` (Simplified), `chi-TW`, `chi-HK` (Traditional) | `chi-CN`, `chi-TW`, `chi-HK` partial |
| 3 | Hindi | 611 | `hin-IN` | none |
| 4 | Spanish | 561 | `esl-ES`, `esl-MX`, `esl-AR`, `esl-CO`, `esl-CL`, `esl-PE`, `esl-US`, `esl-VE`, `esl-EC`, `esl-GT`, `esl-CU`, `esl-DO`, `esl-BO`, `esl-UY` | `esl-ES`, `esl-MX` partial |
| 5 | Modern Standard Arabic | 335 | `ara-SA`, `ara-EG`, `ara-AE`, `ara-MA`, `ara-DZ`, `ara-TN`, `ara-IQ`, `ara-JO`, `ara-LB`, `ara-SY`, `ara-KW`, `ara-QA` | none |
| 6 | French | 334 | `fre-FR`, `fre-BE`, `fre-CA`, `fre-CH`, `fre-SN`, `fre-CI`, `fre-CM`, `fre-MA`, `fre-CD` | `fre-FR` partial |
| 7 | Bengali | 274 | `ben-BD`, `ben-IN` | none |
| 8 | Portuguese | 269 | `por-PT`, `por-BR`, `por-MZ`, `por-AO`, `por-CV` | `por-BR` partial |
| 9 | Indonesian | 255 | `ind-ID` | none |
| 10 | Urdu | 246 | `urd-PK`, `urd-IN` | none |
| 11 | Russian | 210 | `rus-RU` | `rus-RU` partial |
| 12 | German | 133 | `ger-DE`, `ger-AT`, `ger-CH` | `ger-DE` full |
| 13 | Japanese | 126 | `jpn-JP` | `jpn-JP` partial |
| 14 | Nigerian Pidgin | 121 | `pcm-NG` | none |
| 15 | Egyptian Arabic | 118 | `ara-EG` (written Arabic) | none |
| 16 | Marathi | 99 | `mar-IN` | none |
| 17 | Vietnamese | 97 | `vie-VN` | none |
| 18 | Telugu | 96 | `tel-IN` | none |
| 19 | Swahili | 95 | `swa-KE`, `swa-TZ` | none |
| 20 | Hausa | 94 | `hau-NG` | none |
| 21 | Turkish | 94 | `tur-TR` | none |
| 22 | Western Punjabi | 90 | `pnb-PK` (Shahmukhi script; `pan-IN` is Punjabi in Gurmukhi script) | none |
| 23 | Tagalog | 87 | `fil-PH` (Filipino, the standard form of Tagalog) | none |
| 24 | Tamil | 86 | `tam-IN`, `tam-LK`, `tam-SG` | none |
| 25 | Yue Chinese (Cantonese) | 86 | `chi-HK` (written Traditional Chinese) | `chi-HK` partial |
| 26 | Wu Chinese | 83 | written as Simplified Chinese: `chi-CN` | `chi-CN` partial |
| 27 | Persian | 82 | `fas-IR`, `fas-AF` (Dari) | none |
| 28 | Korean | 82 | `kor-KR` | none |
| 29 | Amharic | 78 | `amh-ET` | none |
| 30 | Thai | 71 | `tha-TH` | none |
| 31 | Javanese | 69 | `jav-ID` | none |
| 32 | Italian | 66 | `ita-IT`, `ita-CH` | `ita-IT` partial |
| 33 | Gujarati | 62 | `guj-IN` | none |
| 34 | Kannada | 59 | `kan-IN` | none |
| 35 | Levantine Arabic | 58 | written Arabic: `ara-JO`, `ara-LB`, `ara-SY` | none |
| 36 | Sudanese Arabic | 54 | written Arabic: `ara-*` | none |
| 37 | Yoruba | 53 | `yor-NG` | none |
| 38 | Bhojpuri | 53 | `bho-IN` | none |

Before 6.0.15, 18 of these 38 languages had a locale (counting a spoken variety by its written language); now all
38 do. Eleven have an administration translation, two of them (English and German) complete.

Beyond them, the A–Z list now covers nearly every language with a written standard and CLDR data: the official
languages of India (all 22 scheduled languages, among them Maithili, Konkani, Manipuri, Santali, Dogri, Bodo and
Sanskrit), the large languages of Africa (Fula, Akan, Ewe, Wolof, Bambara, Lingala, Kirundi, Ganda, Kikuyu, Kabyle,
Tamazight, N'Ko), Central Asia (Turkmen, Kyrgyz, Tatar, Chechen) and the Himalaya (Tibetan, Dzongkha), the Celtic
and other regional languages of Europe (Breton, Scottish Gaelic, Faroese, Romansh, Western Frisian, Occitan,
Asturian, Northern Sami, Upper and Lower Sorbian), and Maori, Hawaiian, Tongan, Greenlandic, Sundanese and Cebuano.

## 6. Common languages still missing

Every language of the table above has a locale. Languages with a million or more speakers that still have none:

- **No data in ICU 74 (CLDR 44)**: Haitian Creole (`hat`), Tswana (`tsn`), Sesotho (`sot`), Northern Sotho (`nso`),
  Tsonga (`tso`), Swati (`ssw`), Venda (`ven`) and Southern Ndebele (`nbl`) (official languages of South Africa),
  Chichewa (`nya`), Guarani (`grn`), Aymara (`aym`), Bashkir (`bak`), Iloko (`ilo`), Hmong (`hmn`), Inuktitut
  (`iku`), Samoan (`smo`), Corsican (`cos`), Tok Pisin (`tpi`), Fijian (`fij`) and Balochi (`bal`). Each needs a
  hand-made file (as Latin and Dhivehi have), best written or checked by a speaker; CLDR 45 and later add some of
  them, and a newer ICU on the build machine would generate those.
- **With CLDR data, left out for now** (small, or a second script of a language that has a locale): Manx (`glv`),
  Cornish (`cor`), Swiss German (`gsw`, written in Standard German), Friulian (`fur`), Sardinian (`srd`), Ligurian,
  Venetian, Silesian, Cherokee (`chr`), Nheengatu, Ossetic (`oss`), Yakut (`sah`), Chuvash (`chv`), Central Atlas
  Tamazight, Sango (`sag`), Mazanderani and Northern Luri (CLDR has English day names for both), Fula in Adlam
  script, Uzbek in Arabic script, Interlingua (only for the region "world", see [section 3](#3-every-locale)).
- **More regional variants**: Spanish of Paraguay, Costa Rica, Panama, Honduras, El Salvador, Nicaragua and Puerto
  Rico; French of Luxembourg, Algeria, Tunisia and the rest of Africa; Arabic of Bahrain, Oman, Libya, Palestine,
  Yemen and Sudan; Portuguese of Guinea-Bissau; English of Hong Kong and Malaysia; German of Luxembourg and
  Liechtenstein; Swedish of Finland; Russian of Kazakhstan and Belarus; Traditional Chinese of Macao.
- Spoken varieties written in a standard language (Arabic dialects, Wu and Yue Chinese) need no locale of their own.

Each one with CLDR data is a line in the generator away; see the next section.

## 7. Adding a locale

### The file

`share/locale/<language>-<COUNTRY>.ini`, UTF-8, first line `#?ini charset="utf-8"?`. `eZLocale` reads these
groups and keys (`lib/ezlocale/classes/ezlocale.php`, `initCountry()` and `initLanguage()`):

| Group | Key | Meaning |
|---|---|---|
| `[RegionalSettings]` | `Country` | Country name (in English in most files). |
| | `LanguageName` | Name of the language in the language itself; with the country in brackets where a language has several locales: `Deutsch (Österreich)`. Shown as the native name. |
| | `InternationalLanguageName` | English name, likewise: `German (Austria)`. The name a content language gets when added. |
| | `LanguageComment`, `CountryComment` | Optional; a variation's description (`Euro support`). |
| `[LanguageSettings]` | `TextDirection` | `rtl` for a language written right to left, read by `eZLocale::textDirection()`. Only the right-to-left files have it; a file without it is left to right. See [section 9](#9-right-to-left-languages). |
| `[Charset]` | `Preferred`, `Allowed[]` | Character set. New files use `utf-8`; older files name legacy sets that a UTF-8 site ignores. |
| `[HTTP]` | `ContentLanguage` | The BCP 47 tag printed as the page's `lang`: ISO 639-1 language (or the three-letter code where there is none), ISO 3166 country (`de-AT`). |
| `[Currency]` | `Symbol`, `Name`, `ShortName` | Symbol, name and ISO 4217 code of the country's currency. |
| | `DecimalSymbol`, `ThousandsSeparator`, `FractDigits` | Number format of amounts; `FractDigits=0` for currencies without minor unit (JPY, KRW, VND, IRR). |
| | `PositiveSymbol`, `NegativeSymbol`, `PositiveFormat`, `NegativeFormat` | Sign and layout: `%c` symbol, `%p` sign, `%q` number. `%c %p%q` gives `€ 1.234,50`, `%p%q %c` gives `1.234,50 €`. |
| `[Numbers]` | `DecimalSymbol`, `ThousandsSeparator`, `FractDigits`, `PositiveSymbol`, `NegativeSymbol` | Number format. Each separator must be a single byte (a plain space, not a no-break space), because `eZLocale::internalNumber()` uses it in a regular expression. |
| `[DateTime]` | `TimeFormat`, `ShortTimeFormat`, `DateFormat`, `ShortDateFormat`, `DateTimeFormat`, `ShortDateTimeFormat` | Formats in PHP `date()` letters behind `%`: `%d`/`%j` day, `%m`/`%n` month, `%Y`/`%y` year, `%H`/`%G` 24-hour, `%h`/`%g` 12-hour, `%i` minutes, `%s` seconds, `%a`/`%A` am/pm; and the locale's names: `%l` long day, `%D` short day, `%F` long month, `%M` short month. Other letters are literal text. |
| | `MondayFirst` | `yes` when the week starts on Monday, else it starts on Sunday (`eZLocale` knows no Saturday or Friday start). |
| `[ShortMonthNames]`, `[LongMonthNames]` | `jan` ... `dec` | Month names, in the form used inside a date (genitive in Slavic languages: `января`). |
| `[ShortDayNames]`, `[LongDayNames]` | `mon` ... `sun` | Day names. |
| `[CountryNames]` | `Countries[XX]` | Country names in the language, for the country datatype. Optional. |

### The code

Three letters for the language, a dash, the two-letter ISO 3166 country: `ben-BD`. The country must be two
letters: `eZLocale::localeRegexp()` allows only letters there, so CLDR's numeric regions (`001` "world", `419`
"Latin America") cannot be used, and a language CLDR has only for the world takes the country of its largest
community (`yid-US`) or a user-assigned ISO 3166 code (`tlh-QO`).

The language part follows the codes Exponential has always used: `eng`, `ger` (German), `fre` (French), `esl`
(Spanish), `chi` (Chinese), `dut` (Dutch), `cze`, `cro`, `ita`, `jpn`, `por`, `rus`, `ara` and the rest of the older
files; a regional variant reuses that code (`ger-AT`, `esl-AR`, `chi-SG`, `ara-EG`, `fre-SN`). A language without an
existing code gets its ISO 639-2/T code (the terminological one, not the bibliographic one): `ben` Bengali, `urd`
Urdu, `vie` Vietnamese, `fas` Persian, `ron` Romanian, `msa` Malay, `hye` Armenian, `kat` Georgian, `sqi` Albanian,
`mkd` Macedonian, `isl` Icelandic, `cym` Welsh, `eus` Basque, `bod` Tibetan, `mri` Maori, `fry` Western Frisian.
`fil` (Filipino) has no 639-2/T code other than this one; a language without any ISO 639-2 code takes its ISO 639-3
code (`ckb` Central Kurdish, `pcm` Nigerian Pidgin, `pnb` Western Punjabi, `brx` Bodo). A variation of a locale is
`<code>@<name>.ini` and holds only the keys it changes.

### How the 6.0.15 files were made

They were generated, at build time only, from Unicode CLDR 44 through PHP's `intl` extension (ICU 74.2), with the
Gregorian calendar and Latin digits, by a generator that takes a list of Exponential code, ICU locale and region:

- names from CLDR's display names (English and native), the HTTP tag from the ICU locale;
- the currency of the country, its symbol, name and decimals; separators reduced to one byte (a no-break space
  becomes a space, `’` becomes `'`); a symbol that is invisible in CLDR (the Cape Verdean escudo, whose cifrão is
  the decimal separator) becomes the ISO code (`CVE`);
- the full date pattern for `DateFormat`, the short one for `ShortDateFormat` with a four-digit year, the medium and
  short time patterns; `DateTimeFormat` is date and time joined by a space; eras, time zones and quarters, which
  `eZLocale` cannot print, are left out;
- a 12-hour clock where CLDR uses one and its marker is `AM`/`am`; where the marker is another word (`ص`,
  `上午`, `a.m.`) the file uses the 24-hour clock, because `eZLocale` only prints `am`/`pm`, and says so in a comment;
- day and month names in the format context, the first day of the week, and the country names;
- English names chosen over CLDR's where readers search for another one: `Bengali` (CLDR: Bangla), `Kirundi`
  (Rundi), `Greenlandic` (Kalaallisut), `Simplified Chinese (Singapore)` (to match `chi-CN`), `Western Punjabi
  (Shahmukhi)` and `Dari`;
- the country in brackets as soon as a language has two locales: adding `msa-SG` and `tam-LK` renamed `msa-MY` to
  `Malay (Malaysia)` and `tam-IN` to `Tamil (India)` (an existing content language keeps the name it was given);
- `[LanguageSettings] TextDirection=rtl` where ICU writes the locale right to left (and the right-to-left comment);
- for languages ICU 74 lacks, a **hand-made** part: Latin (conventions of `it_VA`) and Dhivehi (conventions of
  `en_MV`) get their names written by hand in the generator and say so in the file header (see
  [section 3](#3-every-locale)).

Each generated file names its source and license in its first lines: CLDR is Copyright © Unicode, Inc., under the
[Unicode License v3](https://www.unicode.org/license.txt).

**Hand-made files** are never overwritten by the generator. Besides the older files, Klingon (`tlh-QO`) is
entirely hand-made: its header names its sources (The Klingon Dictionary, the Klingon Language Institute) and every
choice. An older file is regenerated only when it is broken, one by one (`heb-IL`, `hin-IN`, `kor-KR`).

To add another locale with CLDR data, generate it the same way with `intl`; without CLDR data, copy a similar file
and change it by hand. Then check it as below.

### The check

`tests/tests/lib/ezlocale/expLocaleFilesTest.php` (DB-less, in the `lib` suite) loads every `share/locale/*.ini`
through `eZINI` and `eZLocale` and fails when a file is not valid UTF-8, lacks a key from the table above, has an
empty day or month name, cannot format a date, time, number or currency, or cannot read its own number back. For
the generated files it also checks the code form, `utf-8`, the HTTP tag, twelve distinct months, seven distinct
days, one-byte separators, the license line and the right-to-left note. For every file it checks that
`TextDirection=rtl` is there exactly when the language is written right to left, and that no file holds
double-encoded UTF-8; the Latin, Hebrew and Klingon files are checked value by value, and every right-to-left locale
and every world language added in 6.0.15 must be offered as a content language. `expLocaleTextDirectionTest` covers
`eZLocale::textDirection()`, `isRightToLeft()`, `textDirectionOf()`, `normalizeTextDirection()` and the template
attributes. `eZLocaleFormattingTest` checks every file as well.

```bash
vendor/bin/phpunit tests/tests/lib/ezlocale
vendor/bin/phpunit tests/tests/kernel/classes/uiswitch/expUISwitchLanguageProviderTest.php
```

A new locale is offered on Setup > Languages > Add a language and in the interface language choice at once; no
cache needs clearing (the choice's catalogue is keyed by the names, sizes and times of the files).

## 8. Adding or completing an administration translation

1. **Strings.** `share/translations/untranslated/translation.ts` holds every source string. It is made by
   `ezlupdate`, built from `support/ezlupdate-qt4.5` (Qt 4.5, `qmake ezlupdate.pro && make`, binary in `bin/linux`):
   `bin/linux/ezlupdate -u` refreshes the untranslated file, `bin/linux/ezlupdate <code>` adds the new strings to a
   translation and marks the vanished ones obsolete. Extensions: `ezlupdate -e extension/<name> <code>`.
2. **A new translation.** Create `share/translations/<code>/` with the locale's code and copy the untranslated
   `translation.ts` into it. Change the `language` attribute of `<TS>` to the code.
3. **Translate** with Qt Linguist or any XML editor. A message counts when its `<translation>` has text and no
   `type="unfinished"`. Keep placeholders (`%1`, `%name`), HTML entities and keyboard hints exactly as in the
   source; write American English in `eng-US` (license, color, behavior).
4. **Measure.** `php bin/php/ezchecktranslation.php <code> --allow-root-user` prints the finished share of the file.
   Comparing with the untranslated file, as in [section 4](#4-every-administration-translation), shows what is
   missing for the current release.
5. **Review.** A second speaker reads the result in the running administration: a siteaccess (or a user's
   preference) with `Locale=<code>`, after clearing the translation cache
   (`php bin/php/ezcache.php --clear-tag=i18n --allow-root-user`).

Translations of the strings added since 6.0 (about 7,500) are the most useful work for every language but English
and German, and a review of the Hebrew, Latin and Klingon drafts is the quickest win.

## 9. Right-to-left languages

28 locale files are written right to left:

| Language | Locales | Script |
|---|---|---|
| Arabic | `ara-SA`, `ara-EG`, `ara-AE`, `ara-MA`, `ara-DZ`, `ara-TN`, `ara-IQ`, `ara-JO`, `ara-LB`, `ara-SY`, `ara-KW`, `ara-QA` | Arabic |
| Hebrew | `heb-IL` | Hebrew |
| Yiddish | `yid-US` | Hebrew |
| Persian, Dari | `fas-IR`, `fas-AF` | Arabic |
| Urdu | `urd-PK`, `urd-IN` | Arabic |
| Pashto | `pus-AF` | Arabic |
| Western Punjabi | `pnb-PK` | Arabic (Shahmukhi) |
| Sindhi | `snd-PK` | Arabic |
| Kashmiri | `kas-IN` | Arabic |
| Uyghur | `uig-CN` | Arabic |
| Central Kurdish (Sorani) | `ckb-IQ` | Arabic |
| Syriac | `syr-IQ`, `syr-SY` | Syriac |
| N’Ko | `nqo-GN` | N’Ko |
| Dhivehi | `div-MV` | Thaana |

The direction is decided by ICU's character order of the locale (and checked against the generator's list of
right-to-left languages), so Kurdish (`kur-TR`, Kurmanji) and Uzbek (`uzb-UZ`), which use the Latin script, are
left to right, as are Fula (`ful-SN`) and Tamazight (`zgh-MA`, Tifinagh).

**The locale says so.** Each of these files has

```ini
# Text direction: right-to-left ([LanguageSettings] TextDirection, see "Right-to-left languages" in doc/languages.md).
[LanguageSettings]
TextDirection=rtl
```

and no other file has the key (see [section 7](#7-adding-a-locale)). A file without it is left to right.

**eZLocale reads it.** `eZLocale::textDirection()` returns `rtl` or `ltr`, `isRightToLeft()` the same as a boolean,
and the static `eZLocale::textDirectionOf( 'heb-IL' )` gives the direction of any locale code (`ltr` for an unknown
one). Templates reach them as `$locale.text_direction` and `$locale.is_right_to_left`, for example on the
`locale_object` of a content language. Only `rtl` and `right-to-left` (any case) count as right to left;
`eZLocale::normalizeTextDirection()` turns every other value into `ltr`.

**Pages and content.** The page layout gets `$site.text_direction` (`rtl` or `ltr`), the direction of the
siteaccess locale, for the `dir` attribute of `<html>`. Content in a right-to-left language is shown with
`dir="rtl"`, also on a left-to-right page, so a Hebrew translation reads correctly inside an English site.

**The administration.** The admin page layouts set `dir="rtl"` when the interface language is right to left, and
their layout skeleton (header, left menu, content, right toolbar) is direction-safe: the browser mirrors it instead
of it being placed by left and right. The details, the CSS rules and what still needs checking are in
[Right-to-left languages](features/6.0/right-to-left.md).

**Interface translations.** Hebrew (`heb-IL`) is the one right-to-left language with an administration translation,
a first draft that needs a review by a native speaker before a site relies on it
([section 8](#8-adding-or-completing-an-administration-translation)).

## 10. No PHP extension at run time

Locales are plain INI files read by `eZINI` and `eZLocale` in PHP: no `intl`, no `mbstring`-only path, no ICU, and
nothing that depends on 64-bit PHP. `intl` was used only to generate the 6.0.15 files (the interface language
choice uses it, when present, for names in the reader's language and for sorting). The administration translations
are XML read by `eZTSTranslator` and cached as PHP. (The number of content languages is a different limit: 62 on
64-bit PHP, 30 on 32-bit, see [section 11](#11-the-limit-of-62-content-languages).)

## 11. The limit of 62 content languages

An installation holds at most **62 content languages on 64-bit PHP** (30 on 32-bit PHP;
`eZContentLanguage::maxCount()` is `8 * PHP_INT_SIZE - 2`). Setup > Languages shows how many are in use and how
many more can be added; the add form says it in its introduction, and refuses a language beyond the limit with a
message that says how to free a place.

**Why.** Each content language is one bit. Its ID is a power of two (2, 4, 8, 16 ...), and an object, a version, a
class and an object state record all their languages in one integer, `language_mask`; an attribute or a name row
carries the single bit of its language in `language_id`. Bit 0 (value 1) is reserved: it is the "always
available" flag in the same mask. PHP's integer and the database's `BIGINT` are 64-bit signed numbers, so the sign
bit and bit 0 leave 62 bits for languages. "Which objects exist in German?" is then one bit test per row
(`language_mask & 8 > 0`), which is what makes language filtering cheap in every query of the kernel.

**What counts.** Only the content languages added on Setup > Languages, the ones in `ezcontent_language`. The
locales in `share/locale` do not count: 219 of them can be offered while an installation uses two languages. A
language added and never used still takes its bit.

**Freeing a place.** Remove a content language that no object and no class has a translation in (Setup >
Languages, the language's page; [section 9 of Content languages](guides/content-languages.md) explains what
blocks a removal and how to move content out of a language first). Its bit becomes free, and the next language
added gets the lowest free one.

**Why it is not raised.** More than 62 languages would need another storage for the language set: a join table
(object, version, language), or extra mask columns (`language_mask_2` ...) in every table that has one, with
every query that tests a mask rewritten to match. Both change the database schema and the SQL that custom code,
extensions and reports run against `language_mask` and `language_id` today, a break of backward compatibility for
every installation, to serve a case no known site has reached. So it is not done; a site that needs more than 62
languages would be a project of its own.

## 12. Test languages: Lorem Ipsum

`share/locale/qlo-ZZ.ini` is **Lorem Ipsum**, a language for testing content translations and nothing else. It is
not counted among the languages above, and no installation adds it: not the clean data, the installer's seeds, the
packages or the kickstart. Setup > Languages > Add a language offers it like any other locale, under its plain name
*Lorem Ipsum*; add it for a test and remove it afterwards. How: [The test language Lorem
Ipsum](guides/test-language-lorem-ipsum.md).

**The code.** `qlo` is in the ISO 639-2 range `qaa`-`qtz`, which ISO reserves for local use, so it can never collide
with a real language; `ZZ` is the ISO 3166 / CLDR code for an unknown region, and the HTTP tag `qlo-ZZ` is a valid
private-use BCP 47 tag. The code has the usual form (three letters, a dash, two letters), so `eZLocale`, the add
form and `ezcontent_language.locale` accept it unchanged.

**The file.** English number and date formats (`1,234.50`, ISO short dates, Monday first), lorem-style month and day
names (*Ipsumdies 14 Temporember 2023*), currency `¤` with the ISO 4217 code `XXX` (no currency), no country names,
UTF-8. It is written by hand.

**The marker.** `[LanguageSettings] TestLanguage=true` makes a locale a test language
(`expTestLanguage::isTestLocale()`). It changes two things:

- the interface language choice (`uiswitch.ini [UISwitch_language]`) never puts a test language in a ranked group
  or in the A-Z list of `AllLanguages=enabled`; only naming it in `AllowedValues[]` offers it, and then A-Z;
- `exp:loremipsum` (`bin/php/exploremipsumfill.php`) fills only test languages with lorem ipsum text.

A locale of your own in the range `qaa`-`qtz` must carry the marker, and only those do;
`tests/tests/kernel/classes/expTestLanguageTest.php` checks both, the file itself, that no default installation names
`qlo-ZZ`, and the text `exp:loremipsum` writes per datatype. `expUISwitchTestLanguageTest` checks the interface
language choice.
