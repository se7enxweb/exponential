---
summary: A measured plan, in small independent waves, that brings every language of Exponential to daily use, administration, extensions and help, so that nobody needs English to work with the product; the priority group of the home languages of refugee children in Hamburg comes first.
---
# Translation waves: every language to daily use

The goal is that a person can work with Exponential in their own language every day, without ever falling back to
English: the administration, the extensions they use and the help. This page measures where each language stands and
cuts the remaining work into **waves**. A wave is one language and one work set, small enough for one translator (a
person or an agent) to start and finish in one sitting, at any time, independently of every other wave.

The figures and the queue below are generated from the files of this release; the rest of the page explains how a
wave is done and checked. The languages, their locales and their administration translations are described in
[Languages in Exponential](languages.md).

## Contents

1. [The plan, measured](#the-plan-measured): [where each language stands](#where-each-language-stands),
   [the help in tiers](#the-help-in-tiers), [languages without a translation](#languages-without-a-translation),
   [the queue](#the-queue-the-first-60-waves)
2. [What the waves are](#what-the-waves-are)
3. [The order of the waves](#the-order-of-the-waves)
4. [Before a wave: the prerequisites](#before-a-wave-the-prerequisites)
5. [How each kind of wave is done](#how-each-kind-of-wave-is-done)
6. [The checks after every wave](#the-checks-after-every-wave)
7. [Traps](#traps)
8. [Done: daily use and the goal](#done-daily-use-and-the-goal)
9. [Speakers and difficulty](#speakers-and-difficulty)
10. [Keeping this page current](#keeping-this-page-current)

## The plan, measured

<!-- generated: exp:i18n waves 2026-10-29 (start) -->
**Measured on 29 October 2026** by `./console exp:i18n waves` from the files of this release (no database):

| What | Size |
|---|---:|
| Administration: strings of `share/translations/untranslated/translation.ts`, in 10 parts | **19,101** (129,289 words) |
| Shipped extensions (`expimageeditor`, `ezjscore`, `ezoe`) | **567** strings |
| Help: pages below `doc/eng-us/` (the generated example pages left out) | **5,738** pages, 4,984,439 words |
| Help: tables of the generated example pages (`.examples/`) | **2,778** entries, 82,381 words |
| Administration translations, complete of them | **155**, 3 |
| of them in daily use (administration, extensions and daily-use help ≥ 97 %) | **2** |
| Languages without an administration translation ([list](#languages-without-a-translation)), being translated on a branch | **22**, 0 |
| Waves to 100 % in every language | **31,812** |

### Where each language stands

Percent of the strings (administration, extensions) and of the words (help), rounded down. **Bold**: below 97 %. "Daily-use help" is the part for editors, users and administrators ([tiers](#the-help-in-tiers)).

| Language | Code | Speakers (millions) | Difficulty | Administration | Extensions | Daily-use help | All help | Waves left | Next wave |
|---|---|---:|:-:|---:|---:|---:|---:|---:|---|
| Afrikaans | `afr-ZA` | 17 | 2 | 98 | 100 | **0** | **0** | 207 | #752 `A01` |
| Akan | `aka-GH` | 11 | 3 | 98 | **80** | **0** | **0** | 208 | #943 `A01` |
| Albanian | `sqi-AL` | 8 | 2 | 98 | **80** | **0** | **0** | 208 | #935 `A01` |
| Amharic | `amh-ET` | 60 | 4 | 98 | 100 | **0** | **0** | 207 | #631 `A01` |
| Arabic | `ara-SA` | 400 | 4 | 98 | **80** | **0** | **0** | 208 | #2 `A01` |
| Aramaic | `arc-IQ` | < 1 | 5 | 98 | **80** | **0** | **0** | 208 | #1349 `A01` |
| Armenian | `hye-AM` | 5 | 4 | 98 | **80** | **0** | **0** | 208 | #1112 `A01` |
| Assamese | `asm-IN` | 15 | 5 | 98 | **80** | **0** | **0** | 208 | #974 `A01` |
| Asturian | `ast-ES` | < 1 | 3 | 98 | 100 | **0** | **0** | 207 | #1334 `A01` |
| Azerbaijani | `aze-AZ` | 10 | 3 | 98 | **80** | **0** | **0** | 208 | #959 `A01` |
| Bambara | `bam-ML` | 14 | 3 | 98 | 100 | **0** | **0** | 207 | #873 `A01` |
| Basque | `eus-ES` | < 1 | 3 | 98 | **80** | **0** | **0** | 208 | #1282 `A01` |
| Belarusian | `bel-BY` | 5 | 4 | 98 | **80** | **0** | **0** | 208 | #1120 `A01` |
| Bengali | `ben-BD` | 285 | 4 | 98 | 100 | **0** | **0** | 207 | #459 `A01` |
| Bhojpuri | `bho-IN` | 52 | 5 | 98 | **80** | **0** | **0** | 208 | #684 `A01` |
| Bodo | `brx-IN` | 2 | 5 | 98 | 100 | **0** | **0** | 207 | #1267 `A01` |
| Bosnian | `bos-BA` | 3 | 3 | 98 | **80** | **0** | **0** | 208 | #1159 `A01` |
| Breton | `bre-FR` | < 1 | 4 | 98 | 100 | **0** | **0** | 207 | #1387 `A01` |
| Bulgarian | `bul-BG` | 8 | 2 | 98 | 100 | **0** | **0** | 207 | #904 `A01` |
| Burmese | `mya-MM` | 43 | 5 | 98 | **80** | **0** | **0** | 208 | #744 `A01` |
| Catalan | `cat-ES` | 10 | 1 | 98 | 100 | **0** | **0** | 207 | #699 `A01` |
| Cebuano | `ceb-PH` | 28 | 3 | 98 | **80** | **0** | **0** | 208 | #714 `A01` |
| Central Kurdish (Sorani) | `ckb-IQ` | 7 | 5 | 98 | **80** | **0** | **0** | 208 | #1097 `A01` |
| Chechen | `che-RU` | 2 | 5 | 98 | **80** | **0** | **0** | 208 | #1252 `A01` |
| Chinese (Simplified) | `chi-CN` | 1,100 | 2 | 98 | 100 | **0** | **0** | 207 | #396 `A01` |
| Chinese (Traditional) | `chi-TW` | 31 | 2 | 98 | 100 | **0** | **0** | 207 | #602 `A01` |
| Croatian | `cro-HR` | 5 | 2 | 98 | **80** | **0** | **0** | 208 | #1013 `A01` |
| Czech | `cze-CZ` | 11 | 2 | 98 | **80** | **0** | **0** | 208 | #850 `A01` |
| Danish | `dan-DK` | 6 | 1 | 98 | **80** | **0** | **0** | 208 | #834 `A01` |
| Dari (Persian of Afghanistan) | `fas-AF` | 35 | 4 | 98 | 100 | **0** | **0** | 207 | #730 `A01` |
| Dhivehi | `div-MV` | < 1 | 5 | 98 | 100 | **0** | **0** | 207 | #1365 `A01` |
| Dogri | `doi-IN` | 3 | 5 | 98 | 100 | **0** | **0** | 207 | #1222 `A01` |
| Dutch | `dut-NL` | 25 | 1 | 98 | **80** | **0** | **0** | 208 | #541 `A01` |
| Dzongkha | `dzo-BT` | < 1 | 5 | 98 | 100 | **0** | **0** | 207 | #1327 `A01` |
| English (Australia) | `eng-AU` | 1,500 | 1 | 100 | 100 | 100 | 100 | 0 | source texts |
| English (United States) | `eng-US` | 1,500 | 1 | 100 | 100 | 100 | 100 | 0 | source texts |
| Esperanto | `epo-EO` | < 1 | 2 | 98 | 100 | **0** | **0** | 207 | #1394 `A01` |
| Estonian | `est-EE` | 1 | 3 | 98 | **80** | **0** | **0** | 208 | #1244 `A01` |
| Ewe | `ewe-GH` | 5 | 3 | 98 | 100 | **0** | **0** | 207 | #1083 `A01` |
| Faroese | `fao-FO` | < 1 | 4 | 98 | 100 | **0** | **0** | 207 | #1416 `A01` |
| Filipino | `fil-PH` | 83 | 2 | 98 | **80** | **0** | **0** | 208 | #496 `A01` |
| Finnish | `fin-FI` | 6 | 2 | 98 | **80** | **0** | **0** | 208 | #990 `A01` |
| French | `fre-FR` | 310 | 1 | 98 | **80** | **0** | **0** | 208 | #411 `A01` |
| Fula | `ful-SN` | 37 | 4 | 98 | **80** | **0** | **0** | 208 | #722 `A01` |
| Galician | `glg-ES` | 2 | 2 | 98 | **80** | **0** | **0** | 208 | #1143 `A01` |
| Ganda | `lug-UG` | 11 | 4 | 98 | **80** | **0** | **0** | 208 | #1005 `A01` |
| Georgian | `kat-GE` | 4 | 4 | 98 | **80** | **0** | **0** | 208 | #1190 `A01` |
| German | `ger-DE` | 135 | 2 | 100 | **76** | 100 | 99 | 13 | #1 `E` |
| Greek | `ell-GR` | 13 | 3 | 98 | **80** | **0** | **0** | 208 | #896 `A01` |
| Greenlandic | `kal-GL` | < 1 | 4 | 98 | **80** | **0** | **0** | 208 | #1423 `A01` |
| Gujarati | `guj-IN` | 62 | 4 | 98 | 100 | **0** | **0** | 207 | #609 `A01` |
| Hausa | `hau-NG` | 94 | 3 | 98 | 100 | **0** | **0** | 207 | #519 `A01` |
| Hawaiian | `haw-US` | < 1 | 3 | 98 | **80** | **0** | **0** | 208 | #1438 `A01` |
| Hebrew | `heb-IL` | 9 | 4 | 98 | **80** | **0** | **0** | 208 | #1028 `A01` |
| Hindi | `hin-IN` | 610 | 4 | 98 | **80** | **0** | **0** | 208 | #435 `A01` |
| Hulaulá (Jewish Neo-Aramaic) | `huy-IL` | < 1 | 5 | 98 | **80** | **0** | **0** | 208 | #1468 `A01` |
| Hungarian | `hun-HU` | 13 | 2 | 98 | **80** | **0** | **0** | 208 | #826 `A01` |
| Icelandic | `isl-IS` | < 1 | 3 | 98 | **80** | **0** | **0** | 208 | #1341 `A01` |
| Igbo | `ibo-NG` | 31 | 3 | 98 | 100 | **0** | **0** | 207 | #692 `A01` |
| Indonesian | `ind-ID` | 252 | 1 | 98 | **80** | **0** | **0** | 208 | #419 `A01` |
| Irish | `gle-IE` | 2 | 4 | 98 | **80** | **0** | **0** | 208 | #1229 `A01` |
| isiXhosa | `xho-ZA` | 19 | 4 | 98 | 100 | **0** | **0** | 207 | #866 `A01` |
| isiZulu | `zul-ZA` | 28 | 4 | 98 | 100 | **0** | **0** | 207 | #803 `A01` |
| Italian | `ita-IT` | 68 | 1 | 98 | **80** | **0** | **0** | 208 | #466 `A01` |
| Japanese | `jpn-JP` | 123 | 3 | 98 | 100 | **0** | **0** | 207 | #504 `A01` |
| Javanese | `jav-ID` | 68 | 3 | 98 | **80** | **0** | **0** | 208 | #564 `A01` |
| Kabyle | `kab-DZ` | 5 | 4 | 98 | 100 | **0** | **0** | 207 | #1128 `A01` |
| Kannada | `kan-IN` | 59 | 5 | 98 | 100 | **0** | **0** | 207 | #653 `A01` |
| Kashmiri | `kas-IN` | 7 | 5 | 98 | 100 | **0** | **0** | 207 | #1105 `A01` |
| Kazakh | `kaz-KZ` | 16 | 4 | 98 | **80** | **0** | **0** | 208 | #911 `A01` |
| Khmer | `khm-KH` | 18 | 4 | 98 | **80** | **0** | **0** | 208 | #880 `A01` |
| Kikuyu | `kik-KE` | 8 | 4 | 98 | **80** | **0** | **0** | 208 | #1052 `A01` |
| Kinyarwanda | `kin-RW` | 15 | 4 | 98 | **80** | **0** | **0** | 208 | #919 `A01` |
| Kirundi | `run-BI` | 12 | 4 | 98 | **80** | **0** | **0** | 208 | #982 `A01` |
| Klingon | `tlh-QO` | < 1 | 4 | 98 | **80** | **0** | **0** | 208 | #1491 `A01` |
| Konkani | `kok-IN` | 2 | 5 | 98 | 100 | **0** | **0** | 207 | #1237 `A01` |
| Korean | `kor-KR` | 82 | 3 | 98 | 100 | **0** | **0** | 207 | #526 `A01` |
| Korean (North Korea, Munhwaŏ) | `kor-KP` | 26 | 3 | 98 | 100 | **0** | **0** | 207 | #737 `A01` |
| Kurdish (Kurmanji) | `kur-TR` | 16 | 3 | 98 | **80** | **0** | **0** | 208 | #48 `A01` |
| Kyrgyz | `kir-KG` | 5 | 5 | 98 | **80** | **0** | **0** | 208 | #1167 `A01` |
| Lao | `lao-LA` | 30 | 5 | 98 | **80** | **0** | **0** | 208 | #842 `A01` |
| Latin | `lat-VA` | < 1 | 4 | 98 | **80** | **0** | **0** | 208 | #1476 `A01` |
| Latvian | `lav-LV` | 2 | 3 | 98 | **80** | **0** | **0** | 208 | #1214 `A01` |
| Lingala | `lin-CD` | 40 | 4 | 98 | **80** | **0** | **0** | 208 | #706 `A01` |
| Lithuanian | `lit-LT` | 3 | 3 | 98 | 100 | **0** | **0** | 207 | #1175 `A01` |
| Lower Sorbian | `dsb-DE` | < 1 | 4 | 98 | **80** | **0** | **0** | 208 | #1460 `A01` |
| Luxembourgish | `ltz-LU` | < 1 | 2 | 98 | 100 | **0** | **0** | 207 | #1290 `A01` |
| Macedonian | `mkd-MK` | 2 | 3 | 98 | **80** | **0** | **0** | 208 | #1206 `A01` |
| Maithili | `mai-IN` | 34 | 5 | 98 | **80** | **0** | **0** | 208 | #810 `A01` |
| Malagasy | `mlg-MG` | 25 | 3 | 98 | **80** | **0** | **0** | 208 | #759 `A01` |
| Malay | `msa-MY` | 60 | 1 | 98 | **80** | **0** | **0** | 208 | #474 `A01` |
| Malayalam | `mal-IN` | 37 | 5 | 98 | 100 | **0** | **0** | 207 | #789 `A01` |
| Maltese | `mlt-MT` | < 1 | 3 | 98 | **80** | **0** | **0** | 208 | #1305 `A01` |
| Manipuri | `mni-IN` | 2 | 5 | 98 | 100 | **0** | **0** | 207 | #1260 `A01` |
| Māori | `mri-NZ` | < 1 | 3 | 98 | **80** | **0** | **0** | 208 | #1372 `A01` |
| Marathi | `mar-IN` | 99 | 4 | 98 | 100 | **0** | **0** | 207 | #557 `A01` |
| Mongolian | `mon-MN` | 6 | 5 | 98 | **80** | **0** | **0** | 208 | #1151 `A01` |
| Nepali | `nep-NP` | 32 | 4 | 98 | **80** | **0** | **0** | 208 | #767 `A01` |
| Nigerian Pidgin | `pcm-NG` | 120 | 3 | 98 | **80** | **0** | **0** | 208 | #511 `A01` |
| Northern Sami | `sme-NO` | < 1 | 4 | 98 | 100 | **0** | **0** | 207 | #1431 `A01` |
| Norwegian | `nor-NO` | 5 | 1 | 98 | **80** | **0** | **0** | 208 | #858 `A01` |
| N’Ko | `nqo-GN` | – | 5 | 98 | 100 | **0** | **0** | 207 | #1484 `A01` |
| Occitan | `oci-FR` | < 1 | 3 | 98 | 100 | **0** | **0** | 207 | #1320 `A01` |
| Odia | `ori-IN` | 40 | 5 | 98 | 100 | **0** | **0** | 207 | #775 `A01` |
| Oromo | `orm-ET` | 45 | 4 | 98 | **80** | **0** | **0** | 208 | #668 `A01` |
| Pashto | `pus-AF` | 60 | 5 | 98 | **80** | **0** | **0** | 208 | #40 `A01` |
| Persian | `fas-IR` | 83 | 4 | 98 | **80** | **0** | **0** | 208 | #32 `A01` |
| Polish | `pol-PL` | 40 | 2 | 98 | **80** | **0** | **0** | 208 | #572 `A01` |
| Portuguese (Brazil) | `por-BR` | 215 | 1 | 98 | **80** | **0** | **0** | 208 | #427 `A01` |
| Portuguese (Portugal) | `por-PT` | 50 | 1 | 98 | 100 | **0** | **0** | 207 | #489 `A01` |
| Punjabi | `pan-IN` | 48 | 4 | 98 | 100 | **0** | **0** | 207 | #646 `A01` |
| Quechua | `que-PE` | 8 | 4 | 98 | 100 | **0** | **0** | 207 | #1060 `A01` |
| Romanian | `ron-RO` | 25 | 1 | 98 | **80** | **0** | **0** | 208 | #549 `A01` |
| Romansh | `roh-CH` | < 1 | 3 | 98 | 100 | **0** | **0** | 207 | #1409 `A01` |
| Russian | `rus-RU` | 255 | 3 | 98 | 100 | **0** | **0** | 207 | #25 `A01` |
| Sanskrit | `san-IN` | < 1 | 5 | 98 | 100 | **0** | **0** | 207 | #1446 `A01` |
| Santali | `sat-IN` | 8 | 5 | 98 | 100 | **0** | **0** | 207 | #1090 `A01` |
| Scottish Gaelic | `gla-GB` | < 1 | 4 | 98 | **80** | **0** | **0** | 208 | #1401 `A01` |
| Serbian (Cyrillic) | `srp-RS` | 10 | 4 | 98 | 100 | **0** | **0** | 207 | #1021 `A01` |
| Serbian (Latin) | `ser-SR` | 10 | 3 | 98 | 100 | **0** | **0** | 207 | #967 `A01` |
| Shona | `sna-ZW` | 15 | 4 | 98 | **80** | **0** | **0** | 208 | #927 `A01` |
| Sindhi | `snd-PK` | 34 | 5 | 98 | **80** | **0** | **0** | 208 | #818 `A01` |
| Sinhala | `sin-LK` | 18 | 4 | 98 | **80** | **0** | **0** | 208 | #888 `A01` |
| Slovak | `slk-SK` | 7 | 2 | 98 | **80** | **0** | **0** | 208 | #951 `A01` |
| Slovenian | `slv-SI` | 3 | 3 | 98 | **80** | **0** | **0** | 208 | #1198 `A01` |
| Somali | `som-SO` | 24 | 3 | 98 | 100 | **0** | **0** | 207 | #782 `A01` |
| Spanish (Mexico) | `esl-MX` | 130 | 1 | 98 | **80** | **0** | **0** | 208 | #443 `A01` |
| Spanish (Spain) | `esl-ES` | 430 | 1 | 98 | **80** | **0** | **0** | 208 | #403 `A01` |
| Standard Moroccan Tamazight | `zgh-MA` | 14 | 5 | 98 | 100 | **0** | **0** | 207 | #998 `A01` |
| Sundanese | `sun-ID` | 32 | 3 | 98 | **80** | **0** | **0** | 208 | #676 `A01` |
| Swahili | `swa-KE` | 80 | 3 | 98 | **80** | **0** | **0** | 208 | #533 `A01` |
| Swedish | `swe-SE` | 13 | 1 | 98 | **80** | **0** | **0** | 208 | #638 `A01` |
| Syriac | `syr-IQ` | < 1 | 5 | 98 | **80** | **0** | **0** | 208 | #1357 `A01` |
| Tajik | `tgk-TJ` | 9 | 4 | 98 | **80** | **0** | **0** | 208 | #1036 `A01` |
| Tamil | `tam-IN` | 87 | 5 | 98 | 100 | **0** | **0** | 207 | #595 `A01` |
| Tatar | `tat-RU` | 5 | 5 | 98 | **80** | **0** | **0** | 208 | #1182 `A01` |
| Telugu | `tel-IN` | 96 | 5 | 98 | 100 | **0** | **0** | 207 | #580 `A01` |
| Thai | `tha-TH` | 61 | 4 | 98 | **80** | **0** | **0** | 208 | #623 `A01` |
| Tibetan | `bod-CN` | 6 | 5 | 98 | **80** | **0** | **0** | 208 | #1135 `A01` |
| Tigrinya | `tir-ET` | 10 | 5 | 98 | **80** | **0** | **0** | 208 | #1067 `A01` |
| Tongan | `ton-TO` | < 1 | 3 | 98 | 100 | **0** | **0** | 207 | #1380 `A01` |
| Turkish | `tur-TR` | 90 | 2 | 98 | **80** | **0** | **0** | 208 | #10 `A01` |
| Turkmen | `tuk-TM` | 7 | 4 | 98 | **80** | **0** | **0** | 208 | #1075 `A01` |
| Ukrainian | `ukr-UA` | 39 | 3 | 98 | 100 | **0** | **0** | 207 | #18 `A01` |
| Upper Sorbian | `hsb-DE` | < 1 | 4 | 98 | 100 | **0** | **0** | 207 | #1453 `A01` |
| Urdu | `urd-PK` | 232 | 4 | 98 | 100 | **0** | **0** | 207 | #482 `A01` |
| Uyghur | `uig-CN` | 11 | 5 | 98 | **80** | **0** | **0** | 208 | #1044 `A01` |
| Uzbek | `uzb-UZ` | 35 | 3 | 98 | **80** | **0** | **0** | 208 | #660 `A01` |
| Vietnamese | `vie-VN` | 86 | 1 | 98 | **80** | **0** | **0** | 208 | #451 `A01` |
| Welsh | `cym-GB` | < 1 | 3 | 98 | **80** | **0** | **0** | 208 | #1274 `A01` |
| Western Frisian | `fry-NL` | < 1 | 3 | 98 | 100 | **0** | **0** | 207 | #1313 `A01` |
| Western Punjabi | `pnb-PK` | 90 | 5 | 98 | **80** | **0** | **0** | 208 | #587 `A01` |
| Wolof | `wol-SN` | 22 | 3 | 98 | 100 | **0** | **0** | 207 | #796 `A01` |
| Yiddish | `yid-US` | 1 | 5 | 98 | **80** | **0** | **0** | 208 | #1297 `A01` |
| Yoruba | `yor-NG` | 46 | 3 | 98 | 100 | **0** | **0** | 207 | #616 `A01` |

### The help in tiers

| Tier | Sections | Pages | Words | Parts |
|---|---|---:|---:|---:|
| Daily use: editors, users, administrators’ how-tos | `help`, `help/manual`, `help/non-technical-people`, `guides`, `help/book-learning`, `help/book-building-a-site` | 265 | 299,092 | 13 |
| Product and audience guides | `install`, `features`, `help/adopters`, `help/advocates`, `help/business`, `help/businesses`, `help/community`, `help/creators` and 22 more | 3,683 | 2,879,303 | 112 |
| Developer reference | `help/extension-development`, `help/template-language`, `help/template-operators`, `help/build-two-ways`, `help/php-developers`, `help/cms-developers`, `help/dxp-developers`, `help/software-engineers` and 12 more | 1,790 | 1,806,044 | 70 |
| Tables of the generated examples | `examples` | 2,778 entries | 82,381 | 4 |

<details><summary>The 199 parts of the help</summary>

| Part | Tier | Pages | Words | First … last page |
|---|---|---:|---:|---|
| `H-help-01` | daily | 12 | 26,353 | `help/README.md` … `help/editing-content.md` |
| `H-help-02` | daily | 12 | 15,166 | `help/free-documentation.md` … `help/os-support.md` |
| `H-help-03` | daily | 13 | 31,616 | `help/project-files.md` … `help/who-is-the-exponential-foundation.md` |
| `H-help-manual-01` | daily | 44 | 17,659 | `help/manual/README.md` … `help/manual/how-do-i-let-a-translator-edit-one-language.md` |
| `H-help-manual-02` | daily | 44 | 18,665 | `help/manual/how-do-i-move-a-user-to-another-group.md` … `help/manual/why-does-search-not-find-an-item.md` |
| `H-help-non-technical-people` | daily | 47 | 28,042 | `help/non-technical-people/checklists/README.md` … `help/non-technical-people/words/the-site-and-the-administration.md` |
| `H-guides-01` | daily | 10 | 26,540 | `guides/README.md` … `guides/collected-information.md` |
| `H-guides-02` | daily | 11 | 28,463 | `guides/content-history.md` … `guides/getting-started.md` |
| `H-guides-03` | daily | 10 | 28,021 | `guides/mail-preferences-administrator.md` … `guides/passkeys.md` |
| `H-guides-04` | daily | 11 | 28,454 | `guides/pdf-exports.md` … `guides/system-information.md` |
| `H-guides-05` | daily | 11 | 26,867 | `guides/templates-and-design.md` … `guides/your-devices-and-sessions.md` |
| `H-help-book-learning` | daily | 31 | 18,156 | `help/book-learning/README.md` … `help/book-learning/workflows-and-triggers.md` |
| `H-help-book-building-a-site` | daily | 9 | 5,090 | `help/book-building-a-site/README.md` … `help/book-building-a-site/the-news-pages.md` |
| `H-install-01` | guides | 7 | 34,644 | `install/01-introduction.md` … `install/07-console-install.md` |
| `H-install-02` | guides | 7 | 57,782 | `install/08-serving-the-site.md` … `install/14-migrating-from-4x.md` |
| `H-install-03` | guides | 7 | 39,234 | `install/15-migrating-from-5x-legacy.md` … `install/migration/checklist.md` |
| `H-install-04` | guides | 7 | 8,465 | `install/migration/coldfusion-apps.md` … `install/migration/exponential-platform-legacy.md` |
| `H-install-05` | guides | 7 | 7,921 | `install/migration/exponential-platform-nexus.md` … `install/migration/magento.md` |
| `H-install-06` | guides | 7 | 5,299 | `install/migration/older-releases.md` … `install/migration/wordpress.md` |
| `H-features-01` | guides | 22 | 36,504 | `features/4.7/cluster_events.md` … `features/6.0/bookmark-folders.md` |
| `H-features-02` | guides | 23 | 39,132 | `features/6.0/bookmarks-of-system-pages.md` … `features/6.0/extension-list-and-downloads.md` |
| `H-features-03` | guides | 23 | 16,924 | `features/6.0/extension-loading-order.md` … `features/6.0/extensions/ezgmaplocation.md` |
| `H-features-04` | guides | 23 | 16,721 | `features/6.0/extensions/ezie.md` … `features/6.0/extensions/owsimpleoperator.md` |
| `H-features-05` | guides | 23 | 37,620 | `features/6.0/extensions/recaptcha.md` … `features/6.0/legacy-bridge.md` |
| `H-features-06` | guides | 23 | 33,432 | `features/6.0/log-rotation.md` … `features/6.0/permission-checks-for-many-assignments.md` |
| `H-features-07` | guides | 23 | 27,546 | `features/6.0/platform-admin-ui-fork.md` … `features/6.0/robots-txt.md` |
| `H-features-08` | guides | 23 | 24,634 | `features/6.0/role-and-policy-template-operators.md` … `features/6.0/translations-and-languages.md` |
| `H-features-09` | guides | 23 | 30,829 | `features/6.0/trash-who-and-where.md` … `features/6.0/web-server-and-solr-commands.md` |
| `H-help-adopters` | guides | 46 | 16,581 | `help/adopters/adopters-checklists.md` … `help/adopters/what-adoption-means.md` |
| `H-help-advocates-01` | guides | 48 | 29,326 | `help/advocates/7x-and-the-foundation-for-advocates.md` … `help/advocates/proposing-a-contribution-sprint.md` |
| `H-help-advocates-02` | guides | 48 | 29,009 | `help/advocates/proposing-a-meetup.md` … `help/advocates/your-first-week-as-an-advocate.md` |
| `H-help-business` | guides | 12 | 29,946 | `help/business/brand-and-naming-rules.md` … `help/business/who-exponential-is-for.md` |
| `H-help-businesses-01` | guides | 30 | 28,554 | `help/businesses/associations/README.md` … `help/businesses/extranets/README.md` |
| `H-help-businesses-02` | guides | 31 | 31,000 | `help/businesses/extranets/customer-and-partner-areas.md` … `help/businesses/libraries/tagging-and-subject-headings.md` |
| `H-help-businesses-03` | guides | 31 | 28,314 | `help/businesses/members/README.md` … `help/businesses/multisite/multi-brand-checklist.md` |
| `H-help-businesses-04` | guides | 31 | 32,235 | `help/businesses/multisite/multi-brand-planning.md` … `help/businesses/reliability/health-checks-and-monitoring.md` |
| `H-help-businesses-05` | guides | 31 | 33,542 | `help/businesses/reliability/incident-response-for-small-teams.md` … `help/businesses/stores/digital-products-and-downloads.md` |
| `H-help-businesses-06` | guides | 31 | 25,917 | `help/businesses/stores/discounts-and-wishlists.md` … `help/businesses/why-businesses-rely-on-exponential.md` |
| `H-help-community` | guides | 52 | 26,819 | `help/community/community-checklists.md` … `help/community/your-first-week-in-the-community.md` |
| `H-help-creators-01` | guides | 46 | 23,103 | `help/creators/app-creators/access-control-in-code.md` … `help/creators/app-creators/rad-tools.md` |
| `H-help-creators-02` | guides | 47 | 26,126 | `help/creators/app-creators/remote-services-expservices.md` … `help/creators/website-creators/member-registration-and-areas.md` |
| `H-help-creators-03` | guides | 47 | 28,744 | `help/creators/website-creators/menus-and-navigation.md` … `help/creators/website-creators/your-first-site-step-by-step.md` |
| `H-help-customers-01` | guides | 37 | 25,803 | `help/customers/commerce/README.md` … `help/customers/day-to-day/analytics-and-what-to-measure.md` |
| `H-help-customers-02` | guides | 38 | 26,709 | `help/customers/day-to-day/approvals-and-review.md` … `help/customers/growth/rss-and-podcast-feeds.md` |
| `H-help-customers-03` | guides | 38 | 24,974 | `help/customers/growth/using-ai-drafts-responsibly.md` … `help/customers/partners-and-hosts/who-does-what.md` |
| `H-help-customers-04` | guides | 38 | 22,266 | `help/customers/partners-and-hosts/working-with-7x-and-the-project.md` … `help/customers/what-you-own.md` |
| `H-help-designers-01` | guides | 63 | 28,180 | `help/designers/README.md` … `help/designers/extensions/gitignore-for-themes.md` |
| `H-help-designers-02` | guides | 64 | 24,251 | `help/designers/extensions/install-and-changelog-files.md` … `help/designers/sales/assets-under-separate-licenses.md` |
| `H-help-designers-03` | guides | 63 | 23,137 | `help/designers/sales/contracts-and-terms.md` … `help/designers/templates/if-and-conditions.md` |
| `H-help-designers-04` | guides | 64 | 26,861 | `help/designers/templates/include.md` … `help/designers/theme-design/color-contrast.md` |
| `H-help-designers-05` | guides | 64 | 37,338 | `help/designers/theme-design/csp-safe-design.md` … `help/designers/theme-design/what-a-design-is.md` |
| `H-help-e-commerce-01` | guides | 26 | 17,960 | `help/e-commerce/catalog/README.md` … `help/e-commerce/operations/analytics-for-stores.md` |
| `H-help-e-commerce-02` | guides | 27 | 18,329 | `help/e-commerce/operations/commerce-audit-trail.md` … `help/e-commerce/settings/workflow-ini-for-the-shop.md` |
| `H-help-ecosystem` | guides | 45 | 18,516 | `help/ecosystem/a-short-history-of-the-ecosystem.md` … `help/ecosystem/what-the-ecosystem-is.md` |
| `H-help-government-01` | guides | 52 | 32,618 | `help/government/accessibility/README.md` … `help/government/building/documents-and-publications/pdf-export.md` |
| `H-help-government-02` | guides | 53 | 24,724 | `help/government/building/documents-and-publications/versions-of-documents.md` … `help/government/building/news-and-alerts/README.md` |
| `H-help-government-03` | guides | 53 | 28,368 | `help/government/building/news-and-alerts/archive-and-expiry.md` … `help/government/checklists/quarterly-access-review.md` |
| `H-help-government-04` | guides | 53 | 31,185 | `help/government/checklists/records-checklist.md` … `help/government/languages/removing-a-translation-safely.md` |
| `H-help-government-05` | guides | 53 | 34,133 | `help/government/languages/right-to-left-in-the-administration.md` … `help/government/levels/parliaments/what-it-publishes.md` |
| `H-help-government-06` | guides | 53 | 35,570 | `help/government/levels/public-health/README.md` … `help/government/operations/availability/high-availability-overview.md` |
| `H-help-government-07` | guides | 53 | 38,340 | `help/government/operations/availability/maintenance-mode-and-planned-downtime.md` … `help/government/operations/incidents/database-unreachable.md` |
| `H-help-government-08` | guides | 53 | 33,869 | `help/government/operations/incidents/ddos-and-probe-floods.md` … `help/government/privacy/analytics.md` |
| `H-help-government-09` | guides | 52 | 31,747 | `help/government/privacy/audit-retention.md` … `help/government/procurement/exit-plans/lock-in-clauses-to-avoid.md` |
| `H-help-government-10` | guides | 53 | 34,253 | `help/government/procurement/exit-plans/personal-data-at-exit.md` … `help/government/procurement/total-cost-of-ownership/staff-and-skills.md` |
| `H-help-government-11` | guides | 53 | 29,777 | `help/government/procurement/total-cost-of-ownership/support-contracts.md` … `help/government/security/file-uploads-and-svg.md` |
| `H-help-government-12` | guides | 53 | 30,493 | `help/government/security/forwarding-audit-to-a-siem.md` … `help/government/tasks/compliance/add-security-txt.md` |
| `H-help-government-13` | guides | 53 | 19,557 | `help/government/tasks/compliance/add-your-own-words-to-the-weak-password-list.md` … `help/government/tasks/operations/bring-the-site-back-online.md` |
| `H-help-government-14` | guides | 53 | 23,260 | `help/government/tasks/operations/check-file-and-directory-permissions.md` … `help/government/tasks/people-and-permissions/keep-editors-out-of-the-admin-siteaccess.md` |
| `H-help-government-15` | guides | 53 | 25,200 | `help/government/tasks/people-and-permissions/let-a-contractor-edit-only-one-folder.md` … `help/government/tasks/publishing/find-and-fix-broken-links.md` |
| `H-help-government-16` | guides | 53 | 24,198 | `help/government/tasks/publishing/follow-changes-with-notifications.md` … `help/government/tasks/sites-and-languages/connect-staff-sign-in-to-ldap.md` |
| `H-help-government-17` | guides | 53 | 21,481 | `help/government/tasks/sites-and-languages/export-a-list-of-content-as-csv.md` … `help/government/transparency-and-foi/withdrawing-and-correcting-published-information.md` |
| `H-help-hosting-01` | guides | 23 | 24,500 | `help/hosting/basics/README.md` … `help/hosting/business/licensing-for-hosts.md` |
| `H-help-hosting-02` | guides | 24 | 20,711 | `help/hosting/business/marketing-exponential-hosting.md` … `help/hosting/models/kubernetes.md` |
| `H-help-hosting-03` | guides | 24 | 30,598 | `help/hosting/models/managed-hosting.md` … `help/hosting/operations/storage-layout.md` |
| `H-help-hosting-04` | guides | 23 | 25,170 | `help/hosting/operations/system-card-for-hosts.md` … `help/hosting/performance/valkey-and-redis.md` |
| `H-help-hosting-05` | guides | 24 | 20,819 | `help/hosting/performance/varnish-and-reverse-proxies.md` … `help/hosting/security/content-security-policy.md` |
| `H-help-hosting-06` | guides | 24 | 29,752 | `help/hosting/security/demo-mode-for-hosts.md` … `help/hosting/servers/velocity-workers-and-pools.md` |
| `H-help-individuals-01` | guides | 27 | 19,254 | `help/individuals/blogs/README.md` … `help/individuals/individuals-glossary.md` |
| `H-help-individuals-02` | guides | 28 | 21,131 | `help/individuals/individuals-reading-path.md` … `help/individuals/what-exists-and-what-does-not.md` |
| `H-help-leadership-01` | guides | 9 | 19,889 | `help/leadership/accessibility-security-and-compliance-checklists.md` … `help/leadership/planning-an-exponential-project.md` |
| `H-help-leadership-02` | guides | 9 | 20,199 | `help/leadership/project-managers-start-here.md` … `help/leadership/working-with-the-community.md` |
| `H-help-local-businesses-01` | guides | 27 | 17,671 | `help/local-businesses/budget/README.md` … `help/local-businesses/getting-started/working-with-a-local-partner-or-freelancer.md` |
| `H-help-local-businesses-02` | guides | 28 | 19,105 | `help/local-businesses/local-businesses-faq.md` … `help/local-businesses/templates/website-brief-for-a-local-business.md` |
| `H-help-non-profits-01` | guides | 27 | 18,785 | `help/non-profits/accessibility/README.md` … `help/non-profits/non-profits-faq.md` |
| `H-help-non-profits-02` | guides | 28 | 19,504 | `help/non-profits/non-profits-glossary.md` … `help/non-profits/volunteers/volunteers-as-editors.md` |
| `H-help-partners-01` | guides | 37 | 15,646 | `help/partners/application-checklist.md` … `help/partners/partner-faq.md` |
| `H-help-partners-02` | guides | 38 | 17,420 | `help/partners/partner-glossary.md` … `help/partners/your-partner-page.md` |
| `H-help-podcasters-01` | guides | 24 | 17,308 | `help/podcasters/a-release-calendar.md` … `help/podcasters/podcaster-checklists.md` |
| `H-help-podcasters-02` | guides | 24 | 18,395 | `help/podcasters/podcaster-faq.md` … `help/podcasters/writing-show-notes.md` |
| `H-help-press-01` | guides | 37 | 27,150 | `help/press/7x-for-reporters.md` … `help/press/fact-checking-checklist.md` |
| `H-help-press-02` | guides | 38 | 24,830 | `help/press/fact-checking-guide.md` … `help/press/release-story-templates.md` |
| `H-help-press-03` | guides | 38 | 23,298 | `help/press/reporter-faq.md` … `help/press/story-nexus-and-legacy.md` |
| `H-help-press-04` | guides | 38 | 25,324 | `help/press/story-online-editor-tinymce-8.md` … `help/press/writing-about-the-ez-publish-years.md` |
| `H-help-project` | guides | 12 | 15,556 | `help/project/README.md` … `help/project/roadmap.md` |
| `H-help-resellers-01` | guides | 26 | 17,381 | `help/resellers/basics/choosing-a-partner-level.md` … `help/resellers/offers/demo-sites.md` |
| `H-help-resellers-02` | guides | 27 | 18,527 | `help/resellers/offers/e-commerce-offers.md` … `help/resellers/sales/sales-process.md` |
| `H-help-site-packages-01` | guides | 26 | 27,325 | `help/site-packages/README.md` … `help/site-packages/cjw-multisite/the-cjw-themes-jumper-extension.md` |
| `H-help-site-packages-02` | guides | 26 | 29,247 | `help/site-packages/cjw-multisite/the-demo-content-package.md` … `help/site-packages/requirements/the-requirements-checklist.md` |
| `H-help-site-packages-03` | guides | 26 | 26,765 | `help/site-packages/requirements/thumbnails-and-screenshots.md` … `help/site-packages/your-own/versioning-your-own-packages.md` |
| `H-help-sponsors` | guides | 11 | 5,284 | `help/sponsors/7x-the-first-sponsor.md` … `help/sponsors/what-sponsorship-funds.md` |
| `H-help-students-01` | guides | 27 | 21,038 | `help/students/asking-for-help-and-study-groups.md` … `help/students/project-04-a-page-with-layouts.md` |
| `H-help-students-02` | guides | 28 | 23,220 | `help/students/project-05-a-multilingual-site.md` … `help/students/your-first-site-three-ways.md` |
| `H-help-supporters` | guides | 12 | 5,687 | `help/supporters/advocacy.md` … `help/supporters/ways-to-support-exponential.md` |
| `H-help-theme-designers-01` | guides | 21 | 17,949 | `help/theme-designers/building-a-portfolio-and-getting-work.md` … `help/theme-designers/designing-the-front-page.md` |
| `H-help-theme-designers-02` | guides | 22 | 19,306 | `help/theme-designers/designing-view-modes.md` … `help/theme-designers/your-first-day-as-a-theme-designer.md` |
| `H-help-trainers-01` | guides | 31 | 31,213 | `help/trainers/assessments/README.md` … `help/trainers/assessments/module-05-newsletter-and-migration/README.md` |
| `H-help-trainers-02` | guides | 32 | 32,172 | `help/trainers/assessments/module-05-newsletter-and-migration/migration-practical-task.md` … `help/trainers/developers/module-03-designs-overrides-and-debugging/the-design-chain.md` |
| `H-help-trainers-03` | guides | 31 | 28,811 | `help/trainers/developers/module-04-content-classes-and-datatypes/README.md` … `help/trainers/developers/module-09-the-console-and-persistent-objects/the-console.md` |
| `H-help-trainers-04` | guides | 32 | 30,514 | `help/trainers/developers/module-10-workflows-events-and-cronjobs/README.md` … `help/trainers/editors-101/module-04-the-online-editor/embeds-tables-and-choosing-the-editor.md` |
| `H-help-trainers-05` | guides | 31 | 27,726 | `help/trainers/editors-101/module-04-the-online-editor/links-and-anchors.md` … `help/trainers/editors-201/module-03-organizing-content/README.md` |
| `H-help-trainers-06` | guides | 32 | 26,489 | `help/trainers/editors-201/module-03-organizing-content/large-operations-and-the-trash.md` … `help/trainers/exponential-intelligence/module-03-the-edit-form/README.md` |
| `H-help-trainers-07` | guides | 31 | 28,245 | `help/trainers/exponential-intelligence/module-03-the-edit-form/alt-text-for-images.md` … `help/trainers/hosting-operations/module-03-databases/README.md` |
| `H-help-trainers-08` | guides | 32 | 30,689 | `help/trainers/hosting-operations/module-03-databases/choosing-a-database.md` … `help/trainers/layouts/module-02-layouts-and-layout-types/README.md` |
| `H-help-trainers-09` | guides | 31 | 26,305 | `help/trainers/layouts/module-02-layouts-and-layout-types/creating-a-layout.md` … `help/trainers/migration/module-01-planning-a-migration/phases-and-the-checklist.md` |
| `H-help-trainers-10` | guides | 32 | 32,618 | `help/trainers/migration/module-01-planning-a-migration/rehearsal-cut-over-and-rollback.md` … `help/trainers/multisite-and-packages/module-01-siteaccesses-for-several-sites/a-siteaccess-per-language.md` |
| `H-help-trainers-11` | guides | 31 | 35,525 | `help/trainers/multisite-and-packages/module-01-siteaccesses-for-several-sites/adding-a-second-site.md` … `help/trainers/newsletter/README.md` |
| `H-help-trainers-12` | guides | 32 | 31,603 | `help/trainers/newsletter/module-01-getting-started/README.md` … `help/trainers/search/module-01-how-search-works/where-people-search.md` |
| `H-help-trainers-13` | guides | 31 | 30,689 | `help/trainers/search/module-02-indexing/README.md` … `help/trainers/site-administrators/module-02-users-and-groups/passwords-resets-and-lockouts.md` |
| `H-help-trainers-14` | guides | 32 | 32,752 | `help/trainers/site-administrators/module-02-users-and-groups/unactivated-users-and-account-checks.md` … `help/trainers/site-administrators/module-08-security/README.md` |
| `H-help-trainers-15` | guides | 31 | 34,602 | `help/trainers/site-administrators/module-08-security/content-security-policy.md` … `help/trainers/theme-designers/module-03-stylesheets-and-scripts/listing-files-in-design-ini.md` |
| `H-help-trainers-16` | guides | 32 | 31,789 | `help/trainers/theme-designers/module-03-stylesheets-and-scripts/ready-for-a-content-security-policy.md` … `help/trainers/train-the-trainer/module-01-preparing-to-teach/your-material-and-the-gfdl.md` |
| `H-help-trainers-17` | guides | 31 | 21,449 | `help/trainers/train-the-trainer/module-02-building-the-lab/README.md` … `help/trainers/train-the-trainer/module-06-assessment-and-follow-up/certificates-of-attendance.md` |
| `H-help-trainers-18` | guides | 32 | 19,506 | `help/trainers/train-the-trainer/module-06-assessment-and-follow-up/checking-understanding-during-class.md` … `help/trainers/workshops/module-05-capstone-projects/multilingual-event-site.md` |
| `H-root-01` | guides | 4 | 11,146 | `INSTALL.md` … `UPDATE.md` |
| `H-root-02` | guides | 4 | 32,716 | `branch-overview.md` … `languages.md` |
| `H-help-extension-development-01` | reference | 43 | 36,335 | `help/extension-development/README.md` … `help/extension-development/part-2-builder/the-exp-ini-command.md` |
| `H-help-extension-development-02` | reference | 43 | 25,024 | `help/extension-development/part-2-builder/view-modes-for-builders.md` … `help/extension-development/part-3-extension-developer/tutorials/by-hand-custom-tag.md` |
| `H-help-extension-development-03` | reference | 43 | 28,639 | `help/extension-development/part-3-extension-developer/tutorials/by-hand-datatype.md` … `help/extension-development/part-4-upgrading/old-jquery-to-jquery-4.md` |
| `H-help-extension-development-04` | reference | 43 | 29,974 | `help/extension-development/part-4-upgrading/php-8-1-to-8-5-deprecations.md` … `help/extension-development/part-7-releasing/github-releases-and-release-notes.md` |
| `H-help-extension-development-05` | reference | 44 | 24,398 | `help/extension-development/part-7-releasing/packages-ezpkg.md` … `help/extension-development/reading-paths.md` |
| `H-help-template-language` | reference | 29 | 28,301 | `help/template-language/README.md` … `help/template-language/what-the-template-language-is.md` |
| `H-help-template-operators-01` | reference | 24 | 24,150 | `help/template-operators/README.md` … `help/template-operators/menu-and-tree-operators.md` |
| `H-help-template-operators-02` | reference | 24 | 30,407 | `help/template-operators/newsletter-operators.md` … `help/template-operators/url-operators.md` |
| `H-help-build-two-ways-01` | reference | 83 | 27,828 | `help/build-two-ways/README.md` … `help/build-two-ways/examples/maintain/add-a-recipe-to-the-index.md` |
| `H-help-build-two-ways-02` | reference | 83 | 20,834 | `help/build-two-ways/examples/maintain/adjust-spacing.md` … `help/build-two-ways/examples/plan/inventory-from-the-console.md` |
| `H-help-build-two-ways-03` | reference | 84 | 30,664 | `help/build-two-ways/examples/plan/inventory-page-types.md` … `help/build-two-ways/your-own-design-extension.md` |
| `H-help-php-developers-01` | reference | 27 | 21,596 | `help/php-developers/a-php-development-checkout.md` … `help/php-developers/php-developer-checklists.md` |
| `H-help-php-developers-02` | reference | 27 | 20,955 | `help/php-developers/php-developer-faq.md` … `help/php-developers/writing-code-that-runs-on-php-8-0.md` |
| `H-help-cms-developers-01` | reference | 25 | 23,428 | `help/cms-developers/accessibility.md` … `help/cms-developers/headless-delivery.md` |
| `H-help-cms-developers-02` | reference | 26 | 22,878 | `help/cms-developers/images-and-image-aliases.md` … `help/cms-developers/workflows-and-triggers.md` |
| `H-help-dxp-developers` | reference | 51 | 26,801 | `help/dxp-developers/application-cache-and-redis.md` … `help/dxp-developers/what-the-dxp-ships-and-what-it-does-not.md` |
| `H-help-software-engineers-01` | reference | 25 | 18,401 | `help/software-engineers/api-design-rest-ezjscore-headless.md` … `help/software-engineers/glossary-for-software-engineers.md` |
| `H-help-software-engineers-02` | reference | 26 | 19,814 | `help/software-engineers/http-security-csp-and-proxies.md` … `help/software-engineers/writing-portable-sql.md` |
| `H-help-web-framework-developers-01` | reference | 27 | 18,432 | `help/web-framework-developers/authentication-and-sessions.md` … `help/web-framework-developers/migrations-and-dba-schema-files.md` |
| `H-help-web-framework-developers-02` | reference | 27 | 19,001 | `help/web-framework-developers/mixing-exponential-and-a-framework.md` … `help/web-framework-developers/your-first-module-step-by-step.md` |
| `H-help-mobile-app-developers-01` | reference | 27 | 15,938 | `help/mobile-app-developers/auth/README.md` … `help/mobile-app-developers/media-and-images/images-in-rest-answers.md` |
| `H-help-mobile-app-developers-02` | reference | 28 | 17,528 | `help/mobile-app-developers/mobile-app-developers-faq.md` … `help/mobile-app-developers/which-api-for-your-app.md` |
| `H-help-database-administrators-01` | reference | 24 | 19,364 | `help/database-administrators/applying-the-6-0-15-database-update.md` … `help/database-administrators/mysql-read-replicas.md` |
| `H-help-database-administrators-02` | reference | 25 | 21,313 | `help/database-administrators/oracle-connections-and-tuning.md` … `help/database-administrators/upgrades-and-sql-update-files.md` |
| `H-help-ai-agents-01` | reference | 24 | 16,131 | `help/ai-agents/agentic-workflows/README.md` … `help/ai-agents/expai/chains-and-queued-work.md` |
| `H-help-ai-agents-02` | reference | 24 | 18,470 | `help/ai-agents/expai/configuring-a-provider.md` … `help/ai-agents/which-path-fits-your-task.md` |
| `H-help-bundlers-01` | reference | 27 | 18,592 | `help/bundlers/adding-and-removing-extensions.md` … `help/bundlers/hosting-panel-installers.md` |
| `H-help-bundlers-02` | reference | 28 | 19,395 | `help/bundlers/integrity-checks.md` … `help/bundlers/your-own-package-server.md` |
| `H-help-integrators-01` | reference | 27 | 21,191 | `help/integrators/ai-job-queue-and-mcp.md` … `help/integrators/integrators-start-here.md` |
| `H-help-integrators-02` | reference | 28 | 22,241 | `help/integrators/interface-specification-template.md` … `help/integrators/writing-an-import-script.md` |
| `H-help-sqli-import` | reference | 6 | 6,974 | `help/sqli-import/README.md` … `help/sqli-import/upgrading-from-1x.md` |
| `H-specifications-01` | reference | 18 | 24,731 | `specifications/6.0/README.md` … `specifications/6.0/legacy-bridge-bundle.md` |
| `H-specifications-02` | reference | 19 | 30,138 | `specifications/6.0/mail-preferences-cli.md` … `specifications/6.0/xrowextract.md` |
| `H-standards-01` | reference | 18 | 34,401 | `standards/README.md` … `standards/patterns/area-extension-bcgooglesitemaps.md` |
| `H-standards-02` | reference | 18 | 21,766 | `standards/patterns/area-extension-bcwebsitestatistics.md` … `standards/patterns/area-extension-explayouts-content-browser.md` |
| `H-standards-03` | reference | 19 | 15,550 | `standards/patterns/area-extension-explayouts-core.md` … `standards/patterns/area-extension-ezautosave.md` |
| `H-standards-04` | reference | 18 | 10,141 | `standards/patterns/area-extension-ezdemo.md` … `standards/patterns/area-extension-ezprestapiprovider.md` |
| `H-standards-05` | reference | 18 | 10,157 | `standards/patterns/area-extension-ezssp.md` … `standards/patterns/area-extension-sevenx-themes-simple.md` |
| `H-standards-06` | reference | 19 | 22,100 | `standards/patterns/area-extension-sevenx-themes-super.md` … `standards/patterns/area-kernel-classes-datatable.md` |
| `H-standards-07` | reference | 18 | 9,815 | `standards/patterns/area-kernel-classes-datatypes.md` … `standards/patterns/area-kernel-classes-role.md` |
| `H-standards-08` | reference | 18 | 23,036 | `standards/patterns/area-kernel-classes-secrets.md` … `standards/patterns/area-kernel-private-classes-exceptions.md` |
| `H-standards-09` | reference | 19 | 20,556 | `standards/patterns/area-kernel-private-classes-httpcache.md` … `standards/patterns/area-lib-ezsoap.md` |
| `H-standards-10` | reference | 18 | 69,241 | `standards/patterns/area-lib-eztemplate.md` … `standards/patterns/iterator.md` |
| `H-standards-11` | reference | 19 | 79,264 | `standards/patterns/module-view.md` … `standards/web-platform.md` |
| `H-bc-01` | reference | 10 | 33,883 | `bc/5.90/README.md` … `bc/6.0/SUBITEMS_MENU_MORE_ACTIONS_MENU_EXPANSION_HIDE_UNHIDE.md` |
| `H-bc-02` | reference | 11 | 69,451 | `bc/6.0/admin4-styling.md` … `bc/6.0/bookmarks-of-system-pages.md` |
| `H-bc-03` | reference | 11 | 29,422 | `bc/6.0/cache-console.md` … `bc/6.0/demo-mode.md` |
| `H-bc-04` | reference | 10 | 12,430 | `bc/6.0/draft-edit-access.md` … `bc/6.0/file-modes.md` |
| `H-bc-05` | reference | 11 | 30,925 | `bc/6.0/frankenphp.md` … `bc/6.0/kickstartercli.md` |
| `H-bc-06` | reference | 11 | 9,474 | `bc/6.0/locations-tab-paging-and-sorting.md` … `bc/6.0/love/ROADMAP.md` |
| `H-bc-07` | reference | 11 | 21,044 | `bc/6.0/love/TESTING.md` … `bc/6.0/oauth-pkce.md` |
| `H-bc-08` | reference | 10 | 12,854 | `bc/6.0/oauth-token-hardening.md` … `bc/6.0/php-8.0-support.md` |
| `H-bc-09` | reference | 11 | 35,091 | `bc/6.0/php8.md` … `bc/6.0/repair.md` |
| `H-bc-10` | reference | 11 | 27,579 | `bc/6.0/response-cache-and-navigation.md` … `bc/6.0/trash.md` |
| `H-bc-11` | reference | 11 | 27,784 | `bc/6.0/trusted-proxies.md` … `bc/6.0/yui-removal.md` |
| `H-history-01` | reference | 23 | 9,704 | `history/2023/2023-12.md` … `history/2025/2025-09.md` |
| `H-history-02` | reference | 24 | 42,484 | `history/2025/2025-10.md` … `history/ecosystem/adminneo.md` |
| `H-history-03` | reference | 24 | 20,834 | `history/ecosystem/cjw-exponential-platform-nexus.md` … `history/ecosystem/ezplatform-query-fieldtype.md` |
| `H-history-04` | reference | 24 | 11,147 | `history/ecosystem/ezplatform-richtext.md` … `history/ecosystem/months/2021-10.md` |
| `H-history-05` | reference | 24 | 9,108 | `history/ecosystem/months/2021-11.md` … `history/ecosystem/months/2024-10.md` |
| `H-history-06` | reference | 24 | 19,259 | `history/ecosystem/months/2024-11.md` … `history/ecosystem/oss-skeleton.md` |
| `H-history-07` | reference | 24 | 20,764 | `history/ecosystem/oss.md` … `history/extensions/ezjscore.md` |
| `H-history-08` | reference | 24 | 18,075 | `history/extensions/ezmbpaex.md` … `history/extensions/months/2024-08.md` |
| `H-history-09` | reference | 24 | 24,372 | `history/extensions/months/2024-09.md` … `history/extensions/sevenx_valkey_cache.md` |
| `H-history-10` | reference | 24 | 73,419 | `history/extensions/swark.md` … `history/ledger/exp_enhanced_link.md` |
| `H-history-11` | reference | 24 | 79,951 | `history/ledger/explayouts_ui.md` … `history/ledger/ezoe.md` |
| `H-history-12` | reference | 24 | 7,378 | `history/ledger/ezpaypal.md` … `history/ledger/eztags.md` |
| `H-history-13` | reference | 24 | 33,737 | `history/ledger/ezupdate.md` … `history/ledger/recaptcha.md` |
| `H-history-14` | reference | 24 | 40,503 | `history/ledger/sevenx-recipes.md` … `history/velocity/installers-and-packages.md` |
| `H-examples` | reference | 26 | 19,479 | `examples/README.md` … `examples/web-servers/velocity.md` |
| `X01` | tables | 1,133 entries | 25,234 | `descriptions/01-command-accesscheck.json` … `descriptions/05-ini-settings-content-ini.json` |
| `X02` | tables | 595 entries | 25,187 | `descriptions/06-ini-settings-debugbar-ini.json` … `descriptions/08-ini-settings-package-ini.json` |
| `X03` | tables | 731 entries | 29,374 | `descriptions/09-ini-settings-site-ini.json` … `descriptions/workflow-events.json` |
| `X04` | tables | 319 entries | 2,586 | `strings.json` |

</details>

<details><summary>The 10 parts of the administration catalogue</summary>

| Part | Strings | Words | Contexts (first … last) |
|---|---:|---:|---|
| `A01` | 1,977 | 12,768 | `contentstructuremenu/show_content_structure` … `design/admin/infocollector/confirmremoval` |
| `A02` | 1,999 | 11,506 | `design/admin/infocollector/overview` … `design/admin/preview/company` |
| `A03` | 1,771 | 11,989 | `design/admin/preview/feedbackform` … `design/admin/setup/extensions` |
| `A04` | 1,971 | 20,842 | `design/admin/setup/info` … `design/admin/setup/rad/override` |
| `A05` | 1,998 | 14,899 | `design/admin/setup/rad/settings` … `design/admin/visual/templateview` |
| `A06` | 1,984 | 8,966 | `design/admin/visual/toolbar` … `design/standard/section` |
| `A07` | 1,921 | 13,622 | `design/standard/setup` … `kernel/collaboration` |
| `A08` | 1,830 | 12,975 | `kernel/content` … `kernel/contentfinder` |
| `A09` | 2,000 | 11,268 | `design/admin/audit` … `kernel/mailpreferences/mail` |
| `A10` | 1,650 | 10,454 | `kernel/mailpreferences/status` … `kernel/user/security/mail` |

</details>

### Languages without a translation

The languages of the shipped locales that the administration shows in English, the ones being translated on a branch, and languages of five million speakers or more that have no locale yet (the code is a proposal). Sorted by speakers per point of difficulty.

| Language | Code | Speakers (millions) | Script | Direction | Difficulty | State |
|---|---|---:|---|---|:-:|---|
| Haitian Creole | `hat-HT` | 13 | Latn | LTR | 2 | new locale, wave #154 |
| Saraiki | `skr-PK` | 26 | Arab | RTL | 5 | new locale, wave #165 |
| Tswana | `tsn-ZA` | 14 | Latn | LTR | 3 | new locale, wave #176 |
| Dyula | `dyu-CI` | 12 | Latn | LTR | 3 | new locale, wave #187 |
| Chichewa | `nya-MW` | 15 | Latn | LTR | 4 | new locale, wave #198 |
| Northern Sotho | `nso-ZA` | 14 | Latn | LTR | 4 | new locale, wave #209 |
| Sesotho | `sot-ZA` | 14 | Latn | LTR | 4 | new locale, wave #220 |
| Mooré | `mos-BF` | 10 | Latn | LTR | 3 | new locale, wave #231 |
| South Azerbaijani | `azb-IR` | 15 | Arab | RTL | 5 | new locale, wave #242 |
| Kanuri | `kau-NG` | 10 | Latn | LTR | 4 | new locale, wave #253 |
| Makhuwa | `vmw-MZ` | 9 | Latn | LTR | 4 | new locale, wave #264 |
| Kongo | `kon-CD` | 7 | Latn | LTR | 4 | new locale, wave #275 |
| Luhya | `luy-KE` | 7 | Latn | LTR | 4 | new locale, wave #286 |
| Tsonga | `tso-ZA` | 7 | Latn | LTR | 4 | new locale, wave #297 |
| Umbundu | `umb-AO` | 7 | Latn | LTR | 4 | new locale, wave #308 |
| Dholuo | `luo-KE` | 5 | Latn | LTR | 3 | new locale, wave #319 |
| Sango | `sag-CF` | 5 | Latn | LTR | 3 | new locale, wave #330 |
| Guarani | `grn-PY` | 7 | Latn | LTR | 4 | new locale, wave #341 |
| Balochi | `bal-PK` | 8 | Arab | RTL | 5 | new locale, wave #352 |
| Kalenjin | `kln-KE` | 6 | Latn | LTR | 4 | new locale, wave #363 |
| Luba-Kasai | `lua-CD` | 6 | Latn | LTR | 4 | new locale, wave #374 |
| Sukuma | `suk-TZ` | 5 | Latn | LTR | 4 | new locale, wave #385 |

### The queue: the first 60 waves

| Phase | Waves |
|---|---:|
| 1. Catalogues of the priority group | 55 |
| 2. German: daily-use help | 0 |
| 3. Daily-use help of the priority group | 98 |
| 4. New translations | 242 |
| 5. Extensions of the other languages | 1,103 |
| 6. Daily-use help of the other languages | 2,030 |
| 7. Rest of the help of the priority group | 1,314 |
| 8. Rest of the help of the other languages | 26,970 |

| # | Wave | Language | Work set | Size (left) | Difficulty | Prerequisites |
|---:|---|---|---|---|:-:|---|
| 1 | `ger-DE/E` | German | Shipped extensions | 137 of 567 strings | 2 | `GUIDE.md` |
| 2 | `ara-SA/A01` | Arabic | Administration, part A01 | 20 of 1,977 strings | 4 | `GUIDE.md`, RTL |
| 3 | `ara-SA/A02` | Arabic | Administration, part A02 | 20 of 1,999 strings | 4 | `GUIDE.md`, RTL |
| 4 | `ara-SA/A05` | Arabic | Administration, part A05 | 20 of 1,998 strings | 4 | `GUIDE.md`, RTL |
| 5 | `ara-SA/A07` | Arabic | Administration, part A07 | 20 of 1,921 strings | 4 | `GUIDE.md`, RTL |
| 6 | `ara-SA/A08` | Arabic | Administration, part A08 | 19 of 1,830 strings | 4 | `GUIDE.md`, RTL |
| 7 | `ara-SA/A09` | Arabic | Administration, part A09 | 20 of 2,000 strings | 4 | `GUIDE.md`, RTL |
| 8 | `ara-SA/A10` | Arabic | Administration, part A10 | 215 of 1,650 strings | 4 | `GUIDE.md`, RTL |
| 9 | `ara-SA/E` | Arabic | Shipped extensions | 114 of 567 strings | 4 | `GUIDE.md`, RTL |
| 10 | `tur-TR/A01` | Turkish | Administration, part A01 | 20 of 1,977 strings | 2 | `GUIDE.md` |
| 11 | `tur-TR/A02` | Turkish | Administration, part A02 | 20 of 1,999 strings | 2 | `GUIDE.md` |
| 12 | `tur-TR/A05` | Turkish | Administration, part A05 | 20 of 1,998 strings | 2 | `GUIDE.md` |
| 13 | `tur-TR/A07` | Turkish | Administration, part A07 | 20 of 1,921 strings | 2 | `GUIDE.md` |
| 14 | `tur-TR/A08` | Turkish | Administration, part A08 | 19 of 1,830 strings | 2 | `GUIDE.md` |
| 15 | `tur-TR/A09` | Turkish | Administration, part A09 | 20 of 2,000 strings | 2 | `GUIDE.md` |
| 16 | `tur-TR/A10` | Turkish | Administration, part A10 | 215 of 1,650 strings | 2 | `GUIDE.md` |
| 17 | `tur-TR/E` | Turkish | Shipped extensions | 114 of 567 strings | 2 | `GUIDE.md` |
| 18 | `ukr-UA/A01` | Ukrainian | Administration, part A01 | 20 of 1,977 strings | 3 | `GUIDE.md` |
| 19 | `ukr-UA/A02` | Ukrainian | Administration, part A02 | 20 of 1,999 strings | 3 | `GUIDE.md` |
| 20 | `ukr-UA/A05` | Ukrainian | Administration, part A05 | 20 of 1,998 strings | 3 | `GUIDE.md` |
| 21 | `ukr-UA/A07` | Ukrainian | Administration, part A07 | 20 of 1,921 strings | 3 | `GUIDE.md` |
| 22 | `ukr-UA/A08` | Ukrainian | Administration, part A08 | 19 of 1,830 strings | 3 | `GUIDE.md` |
| 23 | `ukr-UA/A09` | Ukrainian | Administration, part A09 | 20 of 2,000 strings | 3 | `GUIDE.md` |
| 24 | `ukr-UA/A10` | Ukrainian | Administration, part A10 | 215 of 1,650 strings | 3 | `GUIDE.md` |
| 25 | `rus-RU/A01` | Russian | Administration, part A01 | 20 of 1,977 strings | 3 | `GUIDE.md` |
| 26 | `rus-RU/A02` | Russian | Administration, part A02 | 20 of 1,999 strings | 3 | `GUIDE.md` |
| 27 | `rus-RU/A05` | Russian | Administration, part A05 | 20 of 1,998 strings | 3 | `GUIDE.md` |
| 28 | `rus-RU/A07` | Russian | Administration, part A07 | 20 of 1,921 strings | 3 | `GUIDE.md` |
| 29 | `rus-RU/A08` | Russian | Administration, part A08 | 19 of 1,830 strings | 3 | `GUIDE.md` |
| 30 | `rus-RU/A09` | Russian | Administration, part A09 | 20 of 2,000 strings | 3 | `GUIDE.md` |
| 31 | `rus-RU/A10` | Russian | Administration, part A10 | 215 of 1,650 strings | 3 | `GUIDE.md` |
| 32 | `fas-IR/A01` | Persian | Administration, part A01 | 20 of 1,977 strings | 4 | `GUIDE.md`, RTL |
| 33 | `fas-IR/A02` | Persian | Administration, part A02 | 20 of 1,999 strings | 4 | `GUIDE.md`, RTL |
| 34 | `fas-IR/A05` | Persian | Administration, part A05 | 20 of 1,998 strings | 4 | `GUIDE.md`, RTL |
| 35 | `fas-IR/A07` | Persian | Administration, part A07 | 20 of 1,921 strings | 4 | `GUIDE.md`, RTL |
| 36 | `fas-IR/A08` | Persian | Administration, part A08 | 19 of 1,830 strings | 4 | `GUIDE.md`, RTL |
| 37 | `fas-IR/A09` | Persian | Administration, part A09 | 20 of 2,000 strings | 4 | `GUIDE.md`, RTL |
| 38 | `fas-IR/A10` | Persian | Administration, part A10 | 215 of 1,650 strings | 4 | `GUIDE.md`, RTL |
| 39 | `fas-IR/E` | Persian | Shipped extensions | 114 of 567 strings | 4 | `GUIDE.md`, RTL |
| 40 | `pus-AF/A01` | Pashto | Administration, part A01 | 20 of 1,977 strings | 5 | `GUIDE.md`, RTL |
| 41 | `pus-AF/A02` | Pashto | Administration, part A02 | 20 of 1,999 strings | 5 | `GUIDE.md`, RTL |
| 42 | `pus-AF/A05` | Pashto | Administration, part A05 | 20 of 1,998 strings | 5 | `GUIDE.md`, RTL |
| 43 | `pus-AF/A07` | Pashto | Administration, part A07 | 20 of 1,921 strings | 5 | `GUIDE.md`, RTL |
| 44 | `pus-AF/A08` | Pashto | Administration, part A08 | 19 of 1,830 strings | 5 | `GUIDE.md`, RTL |
| 45 | `pus-AF/A09` | Pashto | Administration, part A09 | 20 of 2,000 strings | 5 | `GUIDE.md`, RTL |
| 46 | `pus-AF/A10` | Pashto | Administration, part A10 | 215 of 1,650 strings | 5 | `GUIDE.md`, RTL |
| 47 | `pus-AF/E` | Pashto | Shipped extensions | 114 of 567 strings | 5 | `GUIDE.md`, RTL |
| 48 | `kur-TR/A01` | Kurdish (Kurmanji) | Administration, part A01 | 20 of 1,977 strings | 3 | `GUIDE.md` |
| 49 | `kur-TR/A02` | Kurdish (Kurmanji) | Administration, part A02 | 20 of 1,999 strings | 3 | `GUIDE.md` |
| 50 | `kur-TR/A05` | Kurdish (Kurmanji) | Administration, part A05 | 20 of 1,998 strings | 3 | `GUIDE.md` |
| 51 | `kur-TR/A07` | Kurdish (Kurmanji) | Administration, part A07 | 20 of 1,921 strings | 3 | `GUIDE.md` |
| 52 | `kur-TR/A08` | Kurdish (Kurmanji) | Administration, part A08 | 19 of 1,830 strings | 3 | `GUIDE.md` |
| 53 | `kur-TR/A09` | Kurdish (Kurmanji) | Administration, part A09 | 20 of 2,000 strings | 3 | `GUIDE.md` |
| 54 | `kur-TR/A10` | Kurdish (Kurmanji) | Administration, part A10 | 215 of 1,650 strings | 3 | `GUIDE.md` |
| 55 | `kur-TR/E` | Kurdish (Kurmanji) | Shipped extensions | 114 of 567 strings | 3 | `GUIDE.md` |
| 56 | `ara-SA/S` | Arabic | Set up the help | small | 4 | `GUIDE.md`, RTL, PDF font Arab |
| 57 | `tur-TR/S` | Turkish | Set up the help | small | 2 | `GUIDE.md` |
| 58 | `ukr-UA/S` | Ukrainian | Set up the help | small | 3 | `GUIDE.md` |
| 59 | `rus-RU/S` | Russian | Set up the help | small | 3 | `GUIDE.md` |
| 60 | `fas-IR/S` | Persian | Set up the help | small | 4 | `GUIDE.md`, RTL, PDF font Arab |

The whole queue and the pages of a wave: `./console exp:i18n waves --list`, `./console exp:i18n waves --wave=<wave>`.

Made by `./console exp:i18n waves`; `./console exp:i18n waves --check` and the test `expI18nTranslationWavesTest` fail when a percentage changed or a size is more than 3 % off.
<!-- exp:i18n waves data {"status":{"afr-ZA":[98,100,0,0],"aka-GH":[98,80,0,0],"amh-ET":[98,100,0,0],"ara-SA":[98,80,0,0],"arc-IQ":[98,80,0,0],"asm-IN":[98,80,0,0],"ast-ES":[98,100,0,0],"aze-AZ":[98,80,0,0],"bam-ML":[98,100,0,0],"bel-BY":[98,80,0,0],"ben-BD":[98,100,0,0],"bho-IN":[98,80,0,0],"bod-CN":[98,80,0,0],"bos-BA":[98,80,0,0],"bre-FR":[98,100,0,0],"brx-IN":[98,100,0,0],"bul-BG":[98,100,0,0],"cat-ES":[98,100,0,0],"ceb-PH":[98,80,0,0],"che-RU":[98,80,0,0],"chi-CN":[98,100,0,0],"chi-TW":[98,100,0,0],"ckb-IQ":[98,80,0,0],"cro-HR":[98,80,0,0],"cym-GB":[98,80,0,0],"cze-CZ":[98,80,0,0],"dan-DK":[98,80,0,0],"div-MV":[98,100,0,0],"doi-IN":[98,100,0,0],"dsb-DE":[98,80,0,0],"dut-NL":[98,80,0,0],"dzo-BT":[98,100,0,0],"ell-GR":[98,80,0,0],"eng-AU":[100,100,100,100],"eng-US":[100,100,100,100],"epo-EO":[98,100,0,0],"esl-ES":[98,80,0,0],"esl-MX":[98,80,0,0],"est-EE":[98,80,0,0],"eus-ES":[98,80,0,0],"ewe-GH":[98,100,0,0],"fao-FO":[98,100,0,0],"fas-AF":[98,100,0,0],"fas-IR":[98,80,0,0],"fil-PH":[98,80,0,0],"fin-FI":[98,80,0,0],"fre-FR":[98,80,0,0],"fry-NL":[98,100,0,0],"ful-SN":[98,80,0,0],"ger-DE":[100,76,100,99],"gla-GB":[98,80,0,0],"gle-IE":[98,80,0,0],"glg-ES":[98,80,0,0],"guj-IN":[98,100,0,0],"hau-NG":[98,100,0,0],"haw-US":[98,80,0,0],"heb-IL":[98,80,0,0],"hin-IN":[98,80,0,0],"hsb-DE":[98,100,0,0],"hun-HU":[98,80,0,0],"huy-IL":[98,80,0,0],"hye-AM":[98,80,0,0],"ibo-NG":[98,100,0,0],"ind-ID":[98,80,0,0],"isl-IS":[98,80,0,0],"ita-IT":[98,80,0,0],"jav-ID":[98,80,0,0],"jpn-JP":[98,100,0,0],"kab-DZ":[98,100,0,0],"kal-GL":[98,80,0,0],"kan-IN":[98,100,0,0],"kas-IN":[98,100,0,0],"kat-GE":[98,80,0,0],"kaz-KZ":[98,80,0,0],"khm-KH":[98,80,0,0],"kik-KE":[98,80,0,0],"kin-RW":[98,80,0,0],"kir-KG":[98,80,0,0],"kok-IN":[98,100,0,0],"kor-KP":[98,100,0,0],"kor-KR":[98,100,0,0],"kur-TR":[98,80,0,0],"lao-LA":[98,80,0,0],"lat-VA":[98,80,0,0],"lav-LV":[98,80,0,0],"lin-CD":[98,80,0,0],"lit-LT":[98,100,0,0],"ltz-LU":[98,100,0,0],"lug-UG":[98,80,0,0],"mai-IN":[98,80,0,0],"mal-IN":[98,100,0,0],"mar-IN":[98,100,0,0],"mkd-MK":[98,80,0,0],"mlg-MG":[98,80,0,0],"mlt-MT":[98,80,0,0],"mni-IN":[98,100,0,0],"mon-MN":[98,80,0,0],"mri-NZ":[98,80,0,0],"msa-MY":[98,80,0,0],"mya-MM":[98,80,0,0],"nep-NP":[98,80,0,0],"nor-NO":[98,80,0,0],"nqo-GN":[98,100,0,0],"oci-FR":[98,100,0,0],"ori-IN":[98,100,0,0],"orm-ET":[98,80,0,0],"pan-IN":[98,100,0,0],"pcm-NG":[98,80,0,0],"pnb-PK":[98,80,0,0],"pol-PL":[98,80,0,0],"por-BR":[98,80,0,0],"por-PT":[98,100,0,0],"pus-AF":[98,80,0,0],"que-PE":[98,100,0,0],"roh-CH":[98,100,0,0],"ron-RO":[98,80,0,0],"run-BI":[98,80,0,0],"rus-RU":[98,100,0,0],"san-IN":[98,100,0,0],"sat-IN":[98,100,0,0],"ser-SR":[98,100,0,0],"sin-LK":[98,80,0,0],"slk-SK":[98,80,0,0],"slv-SI":[98,80,0,0],"sme-NO":[98,100,0,0],"sna-ZW":[98,80,0,0],"snd-PK":[98,80,0,0],"som-SO":[98,100,0,0],"sqi-AL":[98,80,0,0],"srp-RS":[98,100,0,0],"sun-ID":[98,80,0,0],"swa-KE":[98,80,0,0],"swe-SE":[98,80,0,0],"syr-IQ":[98,80,0,0],"tam-IN":[98,100,0,0],"tat-RU":[98,80,0,0],"tel-IN":[98,100,0,0],"tgk-TJ":[98,80,0,0],"tha-TH":[98,80,0,0],"tir-ET":[98,80,0,0],"tlh-QO":[98,80,0,0],"ton-TO":[98,100,0,0],"tuk-TM":[98,80,0,0],"tur-TR":[98,80,0,0],"uig-CN":[98,80,0,0],"ukr-UA":[98,100,0,0],"urd-PK":[98,100,0,0],"uzb-UZ":[98,80,0,0],"vie-VN":[98,80,0,0],"wol-SN":[98,100,0,0],"xho-ZA":[98,100,0,0],"yid-US":[98,80,0,0],"yor-NG":[98,100,0,0],"zgh-MA":[98,100,0,0],"zul-ZA":[98,100,0,0]},"sizes":{"messages":19101,"extensions":567,"parts":199,"daily_pages":265,"daily_words":299092,"guides_pages":3683,"guides_words":2879303,"reference_pages":1790,"reference_words":1806044,"tables_pages":0,"tables_words":82381},"waves":31812,"untranslated":["hat-HT","skr-PK","tsn-ZA","dyu-CI","nya-MW","nso-ZA","sot-ZA","mos-BF","azb-IR","kau-NG","vmw-MZ","kon-CD","luy-KE","tso-ZA","umb-AO","luo-KE","sag-CF","grn-PY","bal-PK","kln-KE","lua-CD","suk-TZ"]} -->
<!-- generated: exp:i18n waves (end) -->

## What the waves are

A **work set** is a piece of the product's text that is translated as a whole:

| Work set | What it is | Size of one wave |
|---|---|---|
| `A01` … `A10` | A part of the administration catalogue `share/translations/<code>/translation.ts`: its contexts in their order, at most 2,000 strings | up to 2,000 strings |
| `E` | The catalogues of the extensions the kernel ships (`extension/<name>/translations/<code>/translation.ts`) | a few hundred strings |
| `E1` … `E6` | The catalogues of the other extensions of an installation, in groups (see [below](#the-extensions-of-an-installation-e1-to-e6)) | up to 1,900 strings |
| `L` | A new locale (`share/locale/<code>.ini`), or a catalogue derived from a complete one (Dari, North Korean) | one file, or one copy and its glossary |
| `S` | The setup of the help in a language: its directory below `doc/`, its settings, its direction and fonts | a few settings |
| `H-…` | A part of the help: one section of `doc/eng-us/`, cut into parts of at most 100 pages and about 30,000 words | up to 100 pages |
| `X01` … | A part of the tables from which the reference examples are generated (`doc/<language>/.examples/`) | up to 30,000 words |

A **wave** is a work set for one language, named `<locale>/<work set>`: `ara-SA/H-help-manual-01` is the first part of
the user manual in Arabic. The command shows what a wave covers and what of it is done:

```bash
./console exp:i18n waves --wave=ara-SA/H-help-manual-01    # the pages, + done, - open
./console exp:i18n waves --wave=A03                         # the contexts of part A03
./console exp:i18n waves --list                             # the whole queue
```

**The help in tiers.** The sections of the help are grouped by who needs them every day:

- **Daily use**: the pages of the help center (`help/*.md`: getting started, signing in, editing content, media,
  searching, shortcuts), the user manual (`help/manual/`), the plain-language guides (`help/non-technical-people/`),
  the administrator's how-tos (`guides/`) and the two books of learning (`help/book-learning/`,
  `help/book-building-a-site/`). These come first in every language.
- **Product and audience guides**: installation, the feature descriptions, and the guides for each audience of the
  help center (government, businesses, trainers, designers, hosting and the others).
- **Developer reference**: extension development, the template language and operators, backwards compatibility,
  standards, specifications, history and the hand-written examples.
- **Tables of the generated examples**: the more than two thousand reference pages are generated from the code
  (`exp:help:examples`); a language translates their tables, not the pages (see
  [the examples](examples/README.md#how-the-generated-pages-are-made)).

## The order of the waves

The queue is computed, not chosen by hand. It runs in eight phases; within a phase a language with more speakers per
point of difficulty comes first:

1. **The catalogues of the priority group** below 100 %: the administration parts and the shipped extensions of
   German, Arabic, Turkish, Ukrainian, Russian, Persian, Pashto and Kurdish (Kurmanji), and the Dari catalogue
   (`fas-AF`, see [derived catalogues](#derived-catalogues-dari-and-north-korean)).
2. **German: the daily-use help** that is not translated yet (German has nearly all of the help).
3. **The daily-use help of the priority group**: first the setup of each language's help (`S`), then part after part,
   one language after the other: every language gets the getting-started pages before any language gets the manual.
   The order is Arabic (which serves the Syrian children too), Turkish, Ukrainian, Russian, Persian, Pashto, Kurdish.
4. **New administration translations**: the review of the derived catalogues, North Korean, then every language
   without a translation, the languages of five million speakers or more without a locale among them (their locale
   first, wave `L`). The languages being translated on a branch wait until it is merged.
5. **The shipped extensions** of every other translation below 100 %.
6. **The daily-use help of every other translation**, in the same way as phase 3.
7. **The rest of the help of the priority group**: guides, reference and tables, German first.
8. **The rest of the help of every other translation.**

**The priority group** is the most common home languages of refugee children in Hamburg: German, English, Arabic
(Syrian Arabic reads Modern Standard Arabic, `ara-SA`), Turkish, Ukrainian, Russian, Persian (Farsi `fas-IR`, Dari
`fas-AF`), Pashto and Kurdish (Kurmanji, `kur-TR`). Their administration is complete; what they lack is the help and
some extensions, so their help comes before the new languages.

## Before a wave: the prerequisites

Read these before the first wave of a language, and again when a wave touches something new:

- **`share/translations/TRANSLATING.md`**: the rules every language shares (placeholders, markup, whitespace, what is
  not translated, words that need their context, the forms of the interface).
- **The language's `GUIDE.md`** next to its catalogue (`share/translations/<code>/GUIDE.md`): register, form of
  address, script and typography, and the **glossary**, which binds the kernel, every extension and the help. A new
  language writes its `GUIDE.md` in its first wave (`A01`), before the first string; a term changes in the guide
  first. The help uses the same glossary as the administration, so a button is named in a page as it is on screen.
- **Right to left** (Arabic, Persian, Pashto, Urdu, Hebrew, Yiddish, Sindhi, Uyghur, Sorani, Dhivehi, Syriac, N’Ko):
  the locale has `TextDirection=rtl`, and the help shows a language right to left when its code is in
  `expHelpLanguage::RTL_LANGUAGES` (check it in wave `S`; Syriac and N’Ko are not listed yet). See
  [Right-to-left languages](languages.md#9-right-to-left-languages).
- **Fonts**: the administration uses the reader's system fonts; the help's PDF export embeds the font of
  `exphelp.ini [PdfSettings] FontFiles[]` (DejaVu Sans), which draws Latin, Cyrillic, Greek, Armenian, Georgian and
  Hebrew. For any other script, wave `S` adds a font that has it (a Noto font) before the first page is exported;
  scripts with complex shaping (Arabic, the Indic scripts, Thai, Khmer, Burmese, Tibetan) must be checked in a
  printed page, not only on screen. See [Scripts, fonts and line breaking](languages.md#scripts-fonts-and-line-breaking).
- **The locale**: a language needs its `share/locale/<code>.ini` before its catalogue can be chosen (wave `L`, see
  [Adding a locale](languages.md#7-adding-a-locale)).

## How each kind of wave is done

Every wave is one branch with one commit (or a few), named after the wave (`i18n-wave-ara-SA-H-help-manual-01`), so
that two translators do not take the same wave: look at the open branches first. Start from a current `main`.

### Administration part (A01 to A10)

1. `./console exp:i18n waves --wave=A03` lists the contexts of the part.
2. A new language: create `share/translations/<code>/` from `share/translations/untranslated/translation.ts`
   (change the `language` attribute of `<TS>`), write `GUIDE.md` in `A01`. An existing one: refresh it with
   `bin/linux/ezlupdate <code>` ([section 8 of the languages page](languages.md#8-adding-or-completing-an-administration-translation)).
3. Translate every message of the part's contexts: text in `<translation>`, `type="unfinished"` removed. Keep each
   `<source>` exactly as it is, byte for byte (see [traps](#traps)).
4. Measure: `php bin/php/ezchecktranslation.php <code> --allow-root-user`, and
   `./console exp:i18n waves --check` names the part when its percentage changed.
5. A language that reaches 100 % in every part joins the list of `tests/tests/lib/ezi18n/expTranslationQualityTest.php`
   and the settings of a new translation ([section 8](languages.md#8-adding-or-completing-an-administration-translation)).

### Shipped extensions (E)

The catalogues of `ezoe`, `ezjscore` and `expimageeditor` (the extensions with a catalogue in the kernel's manifest).
A missing catalogue is created from the extension's `translations/untranslated/translation.ts`; then as above, with
the extension's own context names. `ezoe` and `ezjscore` are in the quality test too.

### The extensions of an installation (E1 to E6)

The other extensions are released from their own repositories, so their catalogues are measured on an installation,
not here: `./console exp:i18n fallbacks --locale=<code>` lists each extension's coverage. On the reference
installation (38 extensions with catalogues, 9,060 strings, measured on 26 October 2026) Croatian, Czech, Danish,
Dutch, Italian, Norwegian, Polish, Portuguese (Brazil), Swedish and Turkish had 98 % of them, Spanish and Hebrew 96 %,
German, Mexican Spanish, Latin and Klingon 94 %, every other translation between 28 and 35 %, Yiddish 5 %. The groups,
done in this order after a language's daily-use help:

| Group | Extensions | Strings |
|---|---|---:|
| `E1` editing | ezoe, exp_oe_tiptap, expimageeditor, eztags, ezmultiupload, ezautosave, ezwt, expui, ezownerchange, expchangeclass, enhancedezbinaryfile, enhancedselection2, ezstarrating, ezgmaplocation, birthday, recaptcha, powercontent, ngclasslist, exp_enhanced_link, ezjscore | 1,628 |
| `E2` layouts | explayouts, explayouts_ui, explayouts_ui_api, explayouts_content_browser_ui | 1,177 |
| `E3` administration tools | ezupdate, git_manager, sevenx_authentication_2fa, expqueue, xrowmetadata, bccie, ezodf, sevenx_dse, bcwebsitestatistics | 1,294 |
| `E4` site designs | ezwebin, sevenx_themes_media, sevenx_themes_simple | 1,434 |
| `E5` newsletter | cjw_newsletter | 1,822 |
| `E6` data export | xrowextract | 1,705 |

Each group is a commit in each extension's repository and a new release of that extension, with its version raised
as every extension release does.

### New locale (L)

A language without `share/locale/<code>.ini` gets it first, as [Adding a locale](languages.md#7-adding-a-locale)
describes: generated from CLDR where CLDR has the language, else written by hand and checked by a speaker (most of
the African languages and Haitian Creole and Guarani have no CLDR data in the generator's ICU, see
[Common languages still missing](languages.md#6-common-languages-still-missing)). Its check, `expLocaleFilesTest`,
must pass. The code in the table [Languages without a translation](#languages-without-a-translation) is a proposal:
take the ISO 639-2 code the other locales use and the country with the most readers.

### Derived catalogues: Dari and North Korean

Two languages are close enough to a complete catalogue that the first wave copies it and changes only what differs.
Wave `L` makes the catalogue usable at once; the review waves (`A01` to `A10`, marked "review") read it part by part
against the glossary.

- **Dari** (`fas-AF`, the Persian of Afghanistan): the locale exists and falls back to Iranian Persian (`fas-IR`).
  Wave `L` copies `share/translations/fas-IR/` to `fas-AF/`, writes in its `GUIDE.md` the terms where Dari differs
  (vocabulary, the names of the solar months used in Afghanistan, the name of the language, دری), applies them, and
  removes the fallback line of `fas-AF` from `settings/i18n.ini`. A reader from Afghanistan reviews it.
- **North Korean** (`kor-KP`, Munhwaŏ, the standard of the Democratic People's Republic of Korea): a new locale
  derived from `kor-KR`. The differences are systematic, so they belong in the `GUIDE.md` before any string changes:
  no initial-sound rule (리용, 련결, 로동 where the South writes 이용, 연결, 노동); different spacing, compounds written
  together; the interstitial ㅅ not written; loanwords in the northern form or a native word (콤퓨터, 프로그람, 화일,
  차림표 for computer, program, file, menu); the name of the language 조선말 and of the country
  조선민주주의인민공화국, never the southern names; a different alphabetical order of the letters, which the locale's
  sorting must follow. The locale file (`share/locale/kor-KP.ini`, `ContentLanguage=ko-KP`, the North Korean won) comes
  from CLDR. A speaker of the northern standard reviews it.

### Setting up a help language (S)

1. Create the directory `doc/<locale in lower case>/` (`doc/ara-sa/`) and add it to `exphelp.ini [DocLanguages]
   Directories[]` and `Names[]`; the name is translated in the `kernel/exphelp` context of the catalogues. Other
   locales of the language read it without a `Map[]` entry (`ara-EG` reads `doc/ara-sa/`).
2. Keep the English file names: a page at the same relative path needs no `.paths.json`. Translated file names
   (as German has) are possible but cost every later wave a map entry; in a script other than Latin they are not
   recommended.
3. Check the direction (`expHelpLanguage::RTL_LANGUAGES`) and the PDF font, as in the
   [prerequisites](#before-a-wave-the-prerequisites).
4. The procedure and the settings are in [Translated documentation](bc/6.0/translated-documentation.md#adding-a-translation).

### Help part (H-…)

1. `./console exp:i18n waves --wave=<locale>/<part>` lists the pages of the part, `-` for those still open.
2. Copy each open page from `doc/eng-us/` to the same path below the language's directory and translate it: the text,
   the headings, the link texts and the image descriptions. The front matter keys, code blocks, commands, settings,
   file names and anchors written in code stay as they are; the `summary:` is translated. Links keep their targets: a
   link to a page that is not translated yet shows the English page.
3. Use the glossary: a button, menu or setting is named in the page as the administration names it in that language.
4. Commit only the pages of the part: the help shows only pages git tracks.

### Tables of the generated examples (X…)

The reference pages are made by `php bin/php/exphelpexamples.php` from tables in `doc/<language>/.examples/`
(`strings.json` and `descriptions/*.json`). A wave translates the entries of its files:
`./console exp:help:examples --language=<directory> --worklist=var/tmp/todo.json` writes the open ones, the command
then generates the pages, and `--check` must pass. The method is described in
[the examples](examples/README.md#how-the-generated-pages-are-made).

## The checks after every wave

```bash
# the rules of the catalogues (placeholders, markup, whitespace, not empty, not English)
vendor/bin/phpunit --no-coverage tests/tests/lib/ezi18n/expTranslationQualityTest.php
# the finished share of a catalogue
php bin/php/ezchecktranslation.php <code> --allow-root-user
# the links and images of the help, below the part's section
./console exp:help check --below=<section>/
# the generated examples are current
php bin/php/exphelpexamples.php --check --allow-root-user
# this page: the percentages it shows are the measured ones
./console exp:i18n waves --check
# the manifest of the shipped files, before every commit
php bin/php/checkmanifest.php --fix
```

Then read the result in the running product: the administration with a user preference or a siteaccess
`Locale=<code>` after clearing the translation cache (`php bin/php/ezcache.php --clear-tag=i18n --allow-root-user`),
and the translated pages in the help center in that language. After a wave, run `./console exp:i18n waves` and
commit the updated page with the wave, so that the queue shows the next one.

## Traps

- **A source with a Qt byte element.** One source text of the administration catalogue holds an apostrophe stored as
  `<byte value="x19"/>` inside `<source>`. Copy every `<source>` element unchanged, never retype or "repair" it: the
  translator finds a message by its exact source, and a changed source is a new, untranslated message.
- **PHP and Python count differently.** A Python script that reads `<source>` with ElementTree's `.text` stops at that
  byte element, and one that does not skip `type="vanished"` or blank translations counts them as done. The figures
  of this page, of the languages page and of the interface language switch come from the PHP reader
  (`expUISwitchLanguageCatalogue::messageKeys()`: context, source and comment; unfinished, obsolete and empty
  translations not counted; percentages rounded down). When two counts disagree, the PHP one is right.
- **A worktree with a linked vendor directory.** In a git worktree whose `vendor/` is a symbolic link to another
  checkout, Composer's autoloader loads that checkout's kernel classes, so the tests test the other code and still
  pass. Give the worktree its own `vendor/composer/` and `vendor/autoload.php` (copies; the packages may stay links)
  and confirm that the test run reports tests and assertions, not "No tests executed".
- **The manifest.** `share/filelist.md5` lists every shipped file with its checksum; the upgrade check reports every
  file that differs from it. Run `php bin/php/checkmanifest.php --fix` before each commit, after the last change.
- **Placeholders and markup.** `%1`, `%name`, `<b>`, `&nbsp;` stay exactly; nothing is glued to a placeholder
  (`TRANSLATING.md`, rule 1). The quality test catches most of it, not all of it.
- **The translation cache.** A changed catalogue shows only after `php bin/php/ezcache.php --clear-tag=i18n`.
- **Translated file names in the help** need an entry in `.paths.json`; two translators who both add entries merge
  them with `./console exp:help maps --language=<directory> --fix`.
- **The generated part of this page** is never edited by hand; `./console exp:i18n waves` writes it.

## Done: daily use and the goal

- **A wave is done** when its work set is 100 % in that language, the checks above pass and the result has been read
  in the running product.
- **A language is in daily use** when its administration, its extensions and its daily-use help are each at least
  **97 %**: what is left is rare enough that a reader does not meet English in a normal working day.
- **The goal is 100 %** in every work set, the whole help included.

## Speakers and difficulty

The speaker figures are total speakers, first and second language, in millions, rounded, after Ethnologue as
Wikipedia's "List of languages by total number of speakers" and the languages' own articles give them (state of
2025). They order the queue; they are not a census, and a better figure is welcome as a change of
`expI18nTranslationWaves::LANGUAGES`.

The **difficulty** (1 to 5) of a language is 1, plus 1 for a script other than Latin (2 for a script with complex
shaping or without spaces between words: the Indic scripts, Thai, Lao, Khmer, Burmese, Tibetan, Ol Chiki), plus 1 for
right to left, plus 1 for rich inflection or agglutination, plus 1 or 2 when little software terminology exists in the
language, at most 5. The queue orders by speakers per point of difficulty.

Languages that are written in another standard are served by it and get no waves of their own: Swiss German and
Bavarian (German), Neapolitan and Sicilian (Italian), the spoken varieties of Arabic (Modern Standard Arabic), Cantonese
and Wu (written Chinese), the regional languages of India written in Hindi or Bengali.

## Keeping this page current

`./console exp:i18n waves` measures again and writes the generated part of this page and of its German translation;
it keeps the date while nothing changes. This page itself is not counted among the help's pages. `./console exp:i18n waves --check` and the test
`tests/tests/kernel/classes/i18n/expI18nTranslationWavesTest.php` (no database) fail when a percentage of a language
changed, a language without a translation was added or got one, or a size is more than 3 % away from the measured one,
so that a small edit of a help page does not make every other change fail, while the plan cannot go stale.
