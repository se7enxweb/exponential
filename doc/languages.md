# Languages in Exponential

The one place that says which languages Exponential supports, at which level, and how to add more. The figures
were taken on 15 October 2026 (Exponential 6.0.15).

In short: **138 locale files** (132 languages and countries plus 6 variations) can be chosen as content languages
and siteaccess locales; **27 administration translations** exist, two of them complete (`eng-US`, `ger-DE`). Every
locale is plain INI: nothing here needs a PHP extension at run time.

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

## 1. Locales, translations, content languages and siteaccesses

Four things are easily mixed up:

| Thing | Where | What it does |
|---|---|---|
| **Locale** | `share/locale/<code>.ini`, read by `eZLocale` | Names of the language and country, the HTTP language tag, date and time formats, day and month names, the first day of the week, number and currency formats, translated country names. |
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
- [The fallback locale of a content object](bc/6.0/content-object-fallback-locale.md).

## 2. Support levels

| Level | What works | Languages |
|---|---|---|
| **Full: interface and content** | The administration speaks the language completely (at least 99% of the current strings); content, dates, numbers and currency follow the locale. | `eng-US`, `ger-DE` |
| **Partial interface, full content** | An administration translation exists but covers 20 to 48% of today's strings (it was complete for an older release). Strings without a translation show in English. Content as above. | the 24 other translations with strings, see [section 4](#4-every-administration-translation) |
| **Content only** | No administration translation: the interface stays English. Content can be written in the language, and a siteaccess with the locale formats dates, numbers and currency for it. | every other locale, among them all 89 added in 6.0.15 |

`nno-NO` has a translation file without a single translated string, so it is content only in practice.

## 3. Every locale

`share/locale` holds 138 files on 15 October 2026: 49 that existed before 6.0.15 and 89 added in 6.0.15. The add
form of Setup > Languages offers the 132 without a variation (a variation such as `ger-DE@euro` only overrides a few
keys of its base file and is chosen in `site.ini`), sorted by English name, each with its native name, its country
(unless the name already gives it) and its code, and a search field that matches any of them: `bengali`, `বাংলা`,
`ben`, `bangladesh` all find `ben-BD`. "RTL" marks a language written right to left
([section 9](#9-right-to-left-languages)).

| Code | English name | Native name | Region | Direction | Added |
|---|---|---|---|---|---|
| `afr-ZA` | Afrikaans | Afrikaans | Africa |  | 6.0.15 |
| `amh-ET` | Amharic | አማርኛ | Africa |  | 6.0.15 |
| `ara-AE` | Arabic (United Arab Emirates) | العربية (الإمارات العربية المتحدة) | Middle East | RTL | 6.0.15 |
| `ara-EG` | Arabic (Egypt) | العربية (مصر) | Middle East | RTL | 6.0.15 |
| `ara-SA` | Arabic | العربية | Middle East | RTL | older |
| `asm-IN` | Assamese | অসমীয়া | South Asia |  | 6.0.15 |
| `aze-AZ` | Azerbaijani | Azərbaycan | Central Asia and Caucasus |  | 6.0.15 |
| `bel-BY` | Belarusian | Беларуская | Europe |  | 6.0.15 |
| `ben-BD` | Bengali (Bangladesh) | বাংলা (বাংলাদেশ) | South Asia |  | 6.0.15 |
| `ben-IN` | Bengali (India) | বাংলা (ভারত) | South Asia |  | 6.0.15 |
| `bho-IN` | Bhojpuri | भोजपुरी | South Asia |  | 6.0.15 |
| `bos-BA` | Bosnian | Bosanski | Europe |  | 6.0.15 |
| `bul-BG` | Bulgarian | Български | Europe |  | 6.0.15 |
| `cat-ES` | Catalan | Català | Europe |  | older |
| `chi-CN` | Simplified Chinese | 简体中文 | East Asia |  | older |
| `chi-HK` | Traditional Chinese (HongKong) | 繁體中文 (香港) | East Asia |  | older |
| `chi-SG` | Simplified Chinese (Singapore) | 简体中文 (新加坡) | Southeast Asia |  | 6.0.15 |
| `chi-TW` | Traditional Chinese (Taiwan) | 繁體中文 (臺灣) | East Asia |  | older |
| `cro-HR` | Croatian (Hrvatski) | Croatian (Hrvatski) | Europe |  | older |
| `cym-GB` | Welsh | Cymraeg | Europe |  | 6.0.15 |
| `cze-CZ` | Czech | Čeština | Europe |  | older |
| `dan-DK` | Danish | Dansk | Europe |  | older |
| `dut-BE` | Dutch (Belgium) | Nederlands (België) | Europe |  | 6.0.15 |
| `dut-NL` | Dutch | Nederlands | Europe |  | older |
| `ell-GR` | Greek (Hellenic) | Greek (Hellenic) | Europe |  | older |
| `eng-AU` | English (Australia) | English (Australia) | Oceania |  | older |
| `eng-CA` | English (Canada) | English (Canada) | Americas |  | older |
| `eng-GB` | English (United Kingdom) | English (United Kingdom) | Europe |  | older |
| `eng-GB@euro` | Variant of eng-GB: Euro support |  | Europe |  | older |
| `eng-IE` | English (Ireland) | English (Ireland) | Europe |  | 6.0.15 |
| `eng-IN` | English (India) | English (India) | South Asia |  | 6.0.15 |
| `eng-NZ` | English (New Zealand) | English (New Zealand) | Oceania |  | older |
| `eng-SG` | English (Singapore) | English (Singapore) | Southeast Asia |  | 6.0.15 |
| `eng-US` | English (American) | English (American) | Americas |  | older |
| `eng-ZA` | English (South Africa) | English (South Africa) | Africa |  | 6.0.15 |
| `epo-EO` | Esperanto | Esperanto | Worldwide |  | older |
| `esl-AR` | Spanish (Argentina) | Español (Argentina) | Americas |  | 6.0.15 |
| `esl-CL` | Spanish (Chile) | Español (Chile) | Americas |  | 6.0.15 |
| `esl-CO` | Spanish (Colombia) | Español (Colombia) | Americas |  | 6.0.15 |
| `esl-ES` | Spanish (Spain) | Español (España) | Europe |  | older |
| `esl-MX` | Spanish (Mexico) | Español (México) | Americas |  | older |
| `esl-PE` | Spanish (Peru) | Español (Perú) | Americas |  | 6.0.15 |
| `esl-US` | Spanish (United States) | Español (Estados Unidos) | Americas |  | 6.0.15 |
| `est-EE` | Estonian | Eesti | Europe |  | 6.0.15 |
| `eus-ES` | Basque | Euskara | Europe |  | 6.0.15 |
| `fas-IR` | Persian | فارسی | Middle East | RTL | 6.0.15 |
| `fil-PH` | Filipino | Filipino | Southeast Asia |  | 6.0.15 |
| `fin-FI` | Finnish | Suomi | Europe |  | older |
| `fre-BE` | French (Belgium) | Français (Belgique) | Europe |  | older |
| `fre-CA` | French (Canada) | Français (Canada) | Americas |  | older |
| `fre-CH` | French (Switzerland) | Français (Suisse) | Europe |  | 6.0.15 |
| `fre-FR` | French (France) | Français (France) | Europe |  | older |
| `ger-AT` | German (Austria) | Deutsch (Österreich) | Europe |  | 6.0.15 |
| `ger-CH` | German (Switzerland) | Deutsch (Schweiz) | Europe |  | 6.0.15 |
| `ger-DE` | German | Deutsch (Deutschland) | Europe |  | older |
| `ger-DE@euro` | Variant of ger-DE: Euro support |  | Europe |  | older |
| `gle-IE` | Irish | Gaeilge | Europe |  | 6.0.15 |
| `glg-ES` | Galician | Galego | Europe |  | 6.0.15 |
| `guj-IN` | Gujarati | ગુજરાતી | South Asia |  | 6.0.15 |
| `hau-NG` | Hausa | Hausa | Africa |  | 6.0.15 |
| `heb-IL` | Hebrew | Hebrew | Middle East | RTL | older |
| `hin-IN` | Hindi (India) | Hindi (Devnagari) | South Asia |  | older |
| `hun-HU` | Hungarian | Magyar | Europe |  | older |
| `hye-AM` | Armenian | Հայերեն | Central Asia and Caucasus |  | 6.0.15 |
| `ibo-NG` | Igbo | Igbo | Africa |  | 6.0.15 |
| `ind-ID` | Indonesian | Indonesian | Southeast Asia |  | older |
| `isl-IS` | Icelandic | Íslenska | Europe |  | 6.0.15 |
| `ita-CH` | Italian (Switzerland) | Italiano (Svizzera) | Europe |  | 6.0.15 |
| `ita-IT` | Italian | Italiano | Europe |  | older |
| `jav-ID` | Javanese | Jawa | Southeast Asia |  | 6.0.15 |
| `jpn-JP` | Japanese | 日本語 | East Asia |  | older |
| `jpn-JP@international` | Variant of jpn-JP: @international |  | East Asia |  | older |
| `kan-IN` | Kannada | ಕನ್ನಡ | South Asia |  | 6.0.15 |
| `kat-GE` | Georgian | Ქართული | Central Asia and Caucasus |  | 6.0.15 |
| `kaz-KZ` | Kazakh | Қазақ тілі | Central Asia and Caucasus |  | 6.0.15 |
| `khm-KH` | Khmer | ខ្មែរ | Southeast Asia |  | 6.0.15 |
| `kin-RW` | Kinyarwanda | Kinyarwanda | Africa |  | 6.0.15 |
| `kor-KR` | Korean | 한국어 | East Asia |  | older |
| `kur-TR` | Kurdish | Kurdî (kurmancî) | Middle East |  | 6.0.15 |
| `lao-LA` | Lao | ລາວ | Southeast Asia |  | 6.0.15 |
| `lav-LV` | Latvian | Latviešu | Europe |  | 6.0.15 |
| `lit-LT` | Lithuanian | Lietuvių | Europe |  | 6.0.15 |
| `ltz-LU` | Luxembourgish | Lëtzebuergesch | Europe |  | 6.0.15 |
| `mal-IN` | Malayalam | മലയാളം | South Asia |  | 6.0.15 |
| `mar-IN` | Marathi | मराठी | South Asia |  | 6.0.15 |
| `mkd-MK` | Macedonian | Македонски | Europe |  | 6.0.15 |
| `mlg-MG` | Malagasy | Malagasy | Africa |  | 6.0.15 |
| `mlt-MT` | Maltese | Malti | Europe |  | 6.0.15 |
| `mon-MN` | Mongolian | Монгол | East Asia |  | 6.0.15 |
| `msa-MY` | Malay | Melayu | Southeast Asia |  | 6.0.15 |
| `mya-MM` | Burmese | မြန်မာ | Southeast Asia |  | 6.0.15 |
| `nep-NP` | Nepali | नेपाली | South Asia |  | 6.0.15 |
| `nno-NO` | Norwegian (Nynorsk) | Norsk (Nynorsk) | Europe |  | older |
| `nor-NO` | Norwegian (Bokmal) | Norsk (Bokmål) | Europe |  | older |
| `nor-NO@intl` | Variant of nor-NO: Internasjonalt Format |  | Europe |  | older |
| `nor-NO@spraakraad` | Variant of nor-NO: Norsk Språkråd |  | Europe |  | older |
| `ori-IN` | Odia | ଓଡ଼ିଆ | South Asia |  | 6.0.15 |
| `orm-ET` | Oromo | Oromoo | Africa |  | 6.0.15 |
| `pan-IN` | Punjabi | ਪੰਜਾਬੀ | South Asia |  | 6.0.15 |
| `pcm-NG` | Nigerian Pidgin | Naijíriá Píjin | Africa |  | 6.0.15 |
| `pnb-PK` | Western Punjabi (Shahmukhi) | پنجابی | South Asia | RTL | 6.0.15 |
| `pol-PL` | Polish | Polski | Europe |  | older |
| `por-AO` | Portuguese (Angola) | Português (Angola) | Africa |  | 6.0.15 |
| `por-BR` | Portuguese (Brazil) | Português | Americas |  | older |
| `por-MZ` | Portuguese (Mozambique) | Português | Africa |  | older |
| `por-PT` | Portuguese (Portugal) | Português | Europe |  | older |
| `por-PT@euro` | Variant of por-PT: Suporte para Euro |  | Europe |  | older |
| `pus-AF` | Pashto | پښتو | South Asia | RTL | 6.0.15 |
| `que-PE` | Quechua | Runasimi | Americas |  | 6.0.15 |
| `ron-RO` | Romanian | Română | Europe |  | 6.0.15 |
| `rus-RU` | Russian | Русский | Europe |  | older |
| `ser-SR` | Serbian (Srpski) | Serbian (Srpski) | Europe |  | older |
| `sin-LK` | Sinhala | සිංහල | South Asia |  | 6.0.15 |
| `slk-SK` | Slovak | Slovenčina | Europe |  | older |
| `slv-SI` | Slovenian | Slovenščina | Europe |  | 6.0.15 |
| `sna-ZW` | Shona | ChiShona | Africa |  | 6.0.15 |
| `snd-PK` | Sindhi | سنڌي | South Asia | RTL | 6.0.15 |
| `som-SO` | Somali | Soomaali | Africa |  | 6.0.15 |
| `sqi-AL` | Albanian | Shqip | Europe |  | 6.0.15 |
| `srp-RS` | Serbian (Српски) | Serbian (Српски) | Europe |  | older |
| `swa-KE` | Swahili (Kenya) | Kiswahili (Kenya) | Africa |  | 6.0.15 |
| `swa-TZ` | Swahili (Tanzania) | Kiswahili (Tanzania) | Africa |  | 6.0.15 |
| `swe-SE` | Swedish | Svenska | Europe |  | older |
| `tam-IN` | Tamil | தமிழ் | South Asia |  | 6.0.15 |
| `tel-IN` | Telugu | తెలుగు | South Asia |  | 6.0.15 |
| `tgk-TJ` | Tajik | Тоҷикӣ | Central Asia and Caucasus |  | 6.0.15 |
| `tha-TH` | Thai | ไทย | Southeast Asia |  | 6.0.15 |
| `tir-ET` | Tigrinya | ትግርኛ | Africa |  | 6.0.15 |
| `tur-TR` | Turkish | Türkçe | Middle East |  | older |
| `uig-CN` | Uyghur | ئۇيغۇرچە | East Asia | RTL | 6.0.15 |
| `ukr-UA` | Ukrainian | Ukrainian | Europe |  | older |
| `urd-IN` | Urdu (India) | اردو (بھارت) | South Asia | RTL | 6.0.15 |
| `urd-PK` | Urdu (Pakistan) | اردو (پاکستان) | South Asia | RTL | 6.0.15 |
| `uzb-UZ` | Uzbek | O‘zbek | Central Asia and Caucasus |  | 6.0.15 |
| `vie-VN` | Vietnamese | Tiếng Việt | Southeast Asia |  | 6.0.15 |
| `xho-ZA` | Xhosa | IsiXhosa | Africa |  | 6.0.15 |
| `yor-NG` | Yoruba | Èdè Yorùbá | Africa |  | 6.0.15 |
| `zul-ZA` | Zulu | IsiZulu | Africa |  | 6.0.15 |

The files added in 6.0.15 are generated from Unicode CLDR 44 (through ICU 74.2) and say so in their first lines;
see [section 7](#7-adding-a-locale) for how their values were taken and where they differ from CLDR.

## 4. Every administration translation

Measured on 15 October 2026 in two ways:

- **Own file finished**: finished messages of the file divided by its finished and unfinished ones, obsolete ones
  left out. This is the "Percent finished" that `php bin/php/ezchecktranslation.php <code>` prints.
- **Current strings translated**: how many of the 13,620 strings in `share/translations/untranslated/translation.ts`
  (every string the kernel and its designs ask for today) the file translates, same context and source, finished
  and not empty. This is what an administrator sees: the rest shows in English.

Most translations were complete for the strings of older releases; 6.0 added about 7,000 strings (admin4, the
new Setup pages, audit, packages, jobs and more) that only `eng-US` and `ger-DE` translate so far.

| Translation | Own file finished | Current strings translated |
|---|---:|---:|
| `eng-US` | 100% (16,009 of 16,009) | 100% (13,620 of 13,620) |
| `ger-DE` | 100% (15,869 of 15,914) | 99% (13,496 of 13,620) |
| `fre-FR` | 100% (6,511 of 6,512) | 48% (6,480 of 13,620) |
| `esl-ES` | 100% (6,510 of 6,512) | 48% (6,479 of 13,620) |
| `por-BR` | 99% (6,466 of 6,513) | 47% (6,435 of 13,620) |
| `swe-SE` | 99% (6,465 of 6,513) | 47% (6,433 of 13,620) |
| `esl-MX` | 98% (6,391 of 6,513) | 47% (6,360 of 13,620) |
| `ita-IT` | 97% (6,323 of 6,513) | 46% (6,289 of 13,620) |
| `cat-ES` | 97% (6,315 of 6,513) | 46% (6,284 of 13,620) |
| `jpn-JP` | 97% (6,287 of 6,514) | 46% (6,251 of 13,620) |
| `chi-CN` | 95% (6,211 of 6,513) | 45% (6,180 of 13,620) |
| `ell-GR` | 92% (6,021 of 6,513) | 44% (5,992 of 13,620) |
| `nor-NO` | 92% (6,011 of 6,513) | 44% (5,976 of 13,620) |
| `dut-NL` | 86% (5,573 of 6,513) | 41% (5,538 of 13,620) |
| `eng-AU` | 84% (5,500 of 6,513) | 40% (5,469 of 13,620) |
| `eng-NZ` | 84% (5,500 of 6,513) | 40% (5,469 of 13,620) |
| `cro-HR` | 84% (5,465 of 6,513) | 40% (5,433 of 13,620) |
| `pol-PL` | 83% (5,410 of 6,513) | 40% (5,382 of 13,620) |
| `fin-FI` | 82% (5,373 of 6,513) | 39% (5,339 of 13,620) |
| `cze-CZ` | 82% (5,371 of 6,513) | 39% (5,337 of 13,620) |
| `srp-RS` | 82% (5,364 of 6,513) | 39% (5,328 of 13,620) |
| `rus-RU` | 82% (5,352 of 6,513) | 39% (5,321 of 13,620) |
| `chi-HK` | 71% (4,592 of 6,513) | 34% (4,592 of 13,620) |
| `chi-TW` | 71% (4,592 of 6,513) | 34% (4,592 of 13,620) |
| `ser-SR` | 61% (3,964 of 6,513) | 29% (3,935 of 13,620) |
| `dan-DK` | 41% (2,697 of 6,513) | 20% (2,673 of 13,620) |
| `nno-NO` | 0% (0 of 6,513) | 0% (0 of 13,620) |

Two directories have no locale file of the same name: `ser-SR` and `srp-RS` are both Serbian (the locale files
`ser-SR.ini` and `srp-RS.ini` exist as well). Translations of extensions live in each extension's
`translations/` directory and are not counted here.

## 5. The world's major languages

The languages with 50 million or more speakers (first and second language together), as listed by
[Wikipedia's "List of languages by total number of speakers"](https://en.wikipedia.org/wiki/List_of_languages_by_total_number_of_speakers),
which takes its figures from Ethnologue (2026 edition); read on 15 October 2026. Speakers are rounded millions.

| # | Language | Speakers (M) | Locales | Administration translation |
|---:|---|---:|---|---|
| 1 | English | 1,493 | `eng-US`, `eng-GB`, `eng-CA`, `eng-AU`, `eng-NZ`, `eng-IE`, `eng-IN`, `eng-SG`, `eng-ZA` | `eng-US` full; `eng-AU`, `eng-NZ` partial |
| 2 | Mandarin Chinese | 1,183 | `chi-CN`, `chi-SG` (Simplified), `chi-TW`, `chi-HK` (Traditional) | `chi-CN`, `chi-TW`, `chi-HK` partial |
| 3 | Hindi | 611 | `hin-IN` | none |
| 4 | Spanish | 561 | `esl-ES`, `esl-MX`, `esl-AR`, `esl-CO`, `esl-CL`, `esl-PE`, `esl-US` | `esl-ES`, `esl-MX` partial |
| 5 | Modern Standard Arabic | 335 | `ara-SA`, `ara-EG`, `ara-AE` | none |
| 6 | French | 334 | `fre-FR`, `fre-BE`, `fre-CA`, `fre-CH` | `fre-FR` partial |
| 7 | Bengali | 274 | `ben-BD`, `ben-IN` | none |
| 8 | Portuguese | 269 | `por-PT`, `por-BR`, `por-MZ`, `por-AO` | `por-BR` partial |
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
| 24 | Tamil | 86 | `tam-IN` | none |
| 25 | Yue Chinese (Cantonese) | 86 | `chi-HK` (written Traditional Chinese) | `chi-HK` partial |
| 26 | Wu Chinese | 83 | written as Simplified Chinese: `chi-CN` | `chi-CN` partial |
| 27 | Persian | 82 | `fas-IR` | none |
| 28 | Korean | 82 | `kor-KR` | none |
| 29 | Amharic | 78 | `amh-ET` | none |
| 30 | Thai | 71 | `tha-TH` | none |
| 31 | Javanese | 69 | `jav-ID` | none |
| 32 | Italian | 66 | `ita-IT`, `ita-CH` | `ita-IT` partial |
| 33 | Gujarati | 62 | `guj-IN` | none |
| 34 | Kannada | 59 | `kan-IN` | none |
| 35 | Levantine Arabic | 58 | written Arabic: `ara-*` | none |
| 36 | Sudanese Arabic | 54 | written Arabic: `ara-*` | none |
| 37 | Yoruba | 53 | `yor-NG` | none |
| 38 | Bhojpuri | 53 | `bho-IN` | none |

Before 6.0.15, 18 of these 38 languages had a locale (counting a spoken variety by its written language); now all
38 do. Eleven have an administration translation, two of them (English and German) complete.

## 6. Common languages still missing

Every language of the table above now has a locale. Languages with millions of speakers that still have none:

- **Haitian Creole** (`hat`): ICU 74 has no data for it; a hand-made file would be needed.
- **Sorani Kurdish** (`ckb`, Arabic script, right to left), **Fula** (`ful`), **Akan** (`aka`), **Tswana** (`tsn`),
  **Sesotho** (`sot`), **Turkmen** (`tuk`), **Kyrgyz** (`kir`), **Tatar** (`tat`), **Dzongkha** (`dzo`), **Tibetan**
  (`bod`), **Maori** (`mri`), **Samoan** (`smo`), **Hawaiian** (`haw`), **Breton** (`bre`), **Corsican** (`cos`),
  **Faroese** (`fao`), **Romansh** (`roh`), **Yiddish** (`yid`, right to left): CLDR has data for most; they were
  left for a later round.
- More regional variants: Spanish of Venezuela, Ecuador, Guatemala, Cuba and the rest of Latin America, French of
  Africa (Senegal, Côte d'Ivoire, Cameroon, Morocco), Arabic of the Maghreb, the Levant and the other Gulf states,
  Portuguese of Cape Verde and Guinea-Bissau, English of Nigeria, Kenya, Pakistan and the Philippines, Malay of
  Singapore and Brunei, Tamil of Sri Lanka and Singapore.
- Spoken varieties written in a standard language (Arabic dialects, Wu and Yue Chinese) need no locale of their own.

Each one is a generated file away; see the next section.

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
| `[Charset]` | `Preferred`, `Allowed[]` | Character set. New files use `utf-8`; older files name legacy sets that a UTF-8 site ignores. |
| `[HTTP]` | `ContentLanguage` | The BCP 47 tag printed as the page's `lang`: ISO 639-1 language, ISO 3166 country (`de-AT`). |
| `[Currency]` | `Symbol`, `Name`, `ShortName` | Symbol, name and ISO 4217 code of the country's currency. |
| | `DecimalSymbol`, `ThousandsSeparator`, `FractDigits` | Number format of amounts; `FractDigits=0` for currencies without minor unit (JPY, KRW, VND, IRR). |
| | `PositiveSymbol`, `NegativeSymbol`, `PositiveFormat`, `NegativeFormat` | Sign and layout: `%c` symbol, `%p` sign, `%q` number. `%c %p%q` gives `€ 1.234,50`, `%p%q %c` gives `1.234,50 €`. |
| `[Numbers]` | `DecimalSymbol`, `ThousandsSeparator`, `FractDigits`, `PositiveSymbol`, `NegativeSymbol` | Number format. Each separator must be a single byte (a plain space, not a no-break space), because `eZLocale::internalNumber()` uses it in a regular expression. |
| `[DateTime]` | `TimeFormat`, `ShortTimeFormat`, `DateFormat`, `ShortDateFormat`, `DateTimeFormat`, `ShortDateTimeFormat` | Formats in PHP `date()` letters behind `%`: `%d`/`%j` day, `%m`/`%n` month, `%Y`/`%y` year, `%H`/`%G` 24-hour, `%h`/`%g` 12-hour, `%i` minutes, `%s` seconds, `%a`/`%A` am/pm; and the locale's names: `%l` long day, `%D` short day, `%F` long month, `%M` short month. Other letters are literal text. |
| | `MondayFirst` | `yes` when the week starts on Monday, else it starts on Sunday (`eZLocale` knows no Saturday start). |
| `[ShortMonthNames]`, `[LongMonthNames]` | `jan` ... `dec` | Month names, in the form used inside a date (genitive in Slavic languages: `января`). |
| `[ShortDayNames]`, `[LongDayNames]` | `mon` ... `sun` | Day names. |
| `[CountryNames]` | `Countries[XX]` | Country names in the language, for the country datatype. Optional. |

There is no key for the text direction; see [section 9](#9-right-to-left-languages).

### The code

Three letters for the language, a dash, the two-letter ISO 3166 country: `ben-BD`. The language part follows the
codes Exponential has always used: `eng`, `ger` (German), `fre` (French), `esl` (Spanish), `chi` (Chinese), `dut`
(Dutch), `cze`, `cro`, `ita`, `jpn`, `por`, `rus`, `ara` and the rest of the older files; a regional variant reuses
that code (`ger-AT`, `esl-AR`, `chi-SG`, `ara-EG`). A language without an existing code gets its ISO 639-2/T code:
`ben` Bengali, `urd` Urdu, `vie` Vietnamese, `fas` Persian, `ron` Romanian, `msa` Malay, `hye` Armenian, `kat`
Georgian, `sqi` Albanian, `mkd` Macedonian, `isl` Icelandic, `cym` Welsh, `eus` Basque. `fil` (Filipino) has no
639-2/T code other than this one. A variation of a locale is `<code>@<name>.ini` and holds only the keys it
changes.

### How the 6.0.15 files were made

They were generated, at build time only, from Unicode CLDR 44 through PHP's `intl` extension (ICU 74.2), with the
Gregorian calendar and Latin digits:

- names from CLDR's display names (English and native), the HTTP tag from the ICU locale;
- the currency of the country, its symbol, name and decimals; separators reduced to one byte (a no-break space
  becomes a space, `’` becomes `'`);
- the full date pattern for `DateFormat`, the short one for `ShortDateFormat` with a four-digit year, the medium and
  short time patterns; `DateTimeFormat` is date and time joined by a space; eras, time zones and quarters, which
  `eZLocale` cannot print, are left out;
- a 12-hour clock where CLDR uses one and its marker is `AM`/`am`; where the marker is another word (`ص`,
  `上午`, `a.m.`) the file uses the 24-hour clock, because `eZLocale` only prints `am`/`pm`, and says so in a comment;
- day and month names in the format context, the first day of the week, and the country names;
- two English names chosen over CLDR's: `Bengali` (CLDR: Bangla) and `Simplified Chinese (Singapore)` (to match
  `chi-CN`).

Each file names its source and license in its first lines: CLDR is Copyright © Unicode, Inc., under the
[Unicode License v3](https://www.unicode.org/license.txt). To add another locale, copy a similar file and change it
by hand, or generate one the same way with `intl`; then check it as below. A hand-made file is never overwritten.

### The check

`tests/tests/lib/ezlocale/expLocaleFilesTest.php` (DB-less, in the `lib` suite) loads every `share/locale/*.ini`
through `eZINI` and `eZLocale` and fails when a file is not valid UTF-8, lacks a key from the table above, has an
empty day or month name, cannot format a date, time, number or currency, or cannot read its own number back. For
the generated files it also checks the code form, `utf-8`, the HTTP tag, twelve distinct months, seven distinct
days, one-byte separators, the license line and the right-to-left note. `eZLocaleFormattingTest` checks every file
as well.

```bash
vendor/bin/phpunit tests/tests/lib/ezlocale/expLocaleFilesTest.php
vendor/bin/phpunit tests/tests/lib/ezlocale/eZLocaleFormattingTest.php
```

A new locale is offered on Setup > Languages > Add a language at once; no cache needs clearing.

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

Translations of the strings added since 6.0 (about 7,000) are the most useful work for every language but English
and German.

## 9. Right-to-left languages

Arabic (`ara-*`), Hebrew (`heb-IL`), Persian (`fas-IR`), Urdu (`urd-*`), Pashto (`pus-AF`), Western Punjabi (`pnb-PK`),
Sindhi (`snd-PK`) and Uyghur (`uig-CN`) are written right to left. **Content works**: the text is stored and shown as
written, and a site design sets `dir="rtl"` for those
pages itself. The generated files carry the comment `Text direction: right-to-left`; `eZLocale` has no direction
setting.

**The administration has no right-to-left layout.** None of these has an administration translation, and the
admin designs set no `dir` attribute and use physical CSS (left and right) in places. An RTL administration needs
`dir="rtl"` from the locale, CSS with logical properties (`margin-inline-start` and so on) and a review of icons
and the content tree. That is a separate, future task.

## 10. No PHP extension at run time

Locales are plain INI files read by `eZINI` and `eZLocale` in PHP: no `intl`, no `mbstring`-only path, no ICU, and
nothing that depends on 64-bit PHP. `intl` was used only to generate the 6.0.15 files. The administration
translations are XML read by `eZTSTranslator` and cached as PHP. (The number of content languages is a different
limit: 62 on 64-bit PHP, 30 on 32-bit, see [section 11](#11-the-limit-of-62-content-languages).)

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
locales in `share/locale` do not count: 138 of them can be offered while an installation uses two languages. A
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
