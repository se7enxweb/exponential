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

<!-- generated: exp:i18n waves 2026-11-01 (start) -->
**Measured on 1 November 2026** by `./console exp:i18n waves` from the files of this release (no database):

| What | Size |
|---|---:|
| Administration: strings of `share/translations/untranslated/translation.ts`, in 11 parts | **19,628** (132,943 words) |
| Shipped extensions (`expimageeditor`, `ezjscore`, `ezoe`) | **567** strings |
| Help: pages below `doc/eng-us/` (the generated example pages left out) | **5,853** pages, 5,234,743 words |
| Help: tables of the generated example pages (`.examples/`) | **3,763** entries, 103,791 words |
| Administration translations, complete of them | **155**, 155 |
| of them in daily use (administration, extensions and daily-use help ≥ 97 %) | **2** |
| Languages without an administration translation ([list](#languages-without-a-translation)), being translated on a branch | **22**, 0 |
| Waves to 100 % in every language | **32,345** |

### Where each language stands

Percent of the strings (administration, extensions) and of the words (help), rounded down. **Bold**: below 97 %. "Daily-use help" is the part for editors, users and administrators ([tiers](#the-help-in-tiers)).

| Language | Code | Speakers (millions) | Difficulty | Administration | Extensions | Daily-use help | All help | Waves left | Next wave |
|---|---|---:|:-:|---:|---:|---:|---:|---:|---|
| Afrikaans | `afr-ZA` | 17 | 2 | 100 | 100 | **0** | **0** | 211 | #436 `S` |
| Akan | `aka-GH` | 11 | 3 | 100 | 100 | **0** | **0** | 211 | #461 `S` |
| Albanian | `sqi-AL` | 8 | 2 | 100 | 100 | **0** | **0** | 211 | #460 `S` |
| Amharic | `amh-ET` | 60 | 4 | 100 | 100 | **0** | **0** | 211 | #420 `S` |
| Arabic | `ara-SA` | 400 | 4 | 100 | 100 | **0** | **0** | 211 | #6 `S` |
| Aramaic | `arc-IQ` | < 1 | 5 | 100 | 100 | **0** | **0** | 211 | #514 `S` |
| Armenian | `hye-AM` | 5 | 4 | 100 | 100 | **0** | **0** | 211 | #483 `S` |
| Assamese | `asm-IN` | 15 | 5 | 100 | 100 | **0** | **0** | 211 | #465 `S` |
| Asturian | `ast-ES` | < 1 | 3 | 100 | 100 | **0** | **0** | 211 | #512 `S` |
| Azerbaijani | `aze-AZ` | 10 | 3 | 100 | 100 | **0** | **0** | 211 | #463 `S` |
| Bambara | `bam-ML` | 14 | 3 | 100 | 100 | **0** | **0** | 211 | #452 `S` |
| Basque | `eus-ES` | < 1 | 3 | 100 | 100 | **0** | **0** | 211 | #505 `S` |
| Belarusian | `bel-BY` | 5 | 4 | 100 | 100 | **0** | **0** | 211 | #484 `S` |
| Bengali | `ben-BD` | 285 | 4 | 100 | 100 | **0** | **0** | 211 | #397 `S` |
| Bhojpuri | `bho-IN` | 52 | 5 | 100 | 100 | **0** | **0** | 211 | #427 `S` |
| Bodo | `brx-IN` | 2 | 5 | 100 | 100 | **0** | **0** | 211 | #503 `S` |
| Bosnian | `bos-BA` | 3 | 3 | 100 | 100 | **0** | **0** | 211 | #489 `S` |
| Breton | `bre-FR` | < 1 | 4 | 100 | 100 | **0** | **0** | 211 | #519 `S` |
| Bulgarian | `bul-BG` | 8 | 2 | 100 | 100 | **0** | **0** | 211 | #456 `S` |
| Burmese | `mya-MM` | 43 | 5 | 100 | 100 | **0** | **0** | 211 | #435 `S` |
| Catalan | `cat-ES` | 10 | 1 | 100 | 100 | **0** | **0** | 211 | #429 `S` |
| Cebuano | `ceb-PH` | 28 | 3 | 100 | 100 | **0** | **0** | 211 | #431 `S` |
| Central Kurdish (Sorani) | `ckb-IQ` | 7 | 5 | 100 | 100 | **0** | **0** | 211 | #481 `S` |
| Chechen | `che-RU` | 2 | 5 | 100 | 100 | **0** | **0** | 211 | #501 `S` |
| Chinese (Simplified) | `chi-CN` | 1,100 | 2 | 100 | 100 | **0** | **0** | 211 | #389 `S` |
| Chinese (Traditional) | `chi-TW` | 31 | 2 | 100 | 100 | **0** | **0** | 211 | #416 `S` |
| Croatian | `cro-HR` | 5 | 2 | 100 | 100 | **0** | **0** | 211 | #470 `S` |
| Czech | `cze-CZ` | 11 | 2 | 100 | 100 | **0** | **0** | 211 | #449 `S` |
| Danish | `dan-DK` | 6 | 1 | 100 | 100 | **0** | **0** | 211 | #447 `S` |
| Dari (Persian of Afghanistan) | `fas-AF` | 35 | 4 | 100 | 100 | **0** | **0** | 211 | #433 `S` |
| Dhivehi | `div-MV` | < 1 | 5 | 100 | 100 | **0** | **0** | 211 | #516 `S` |
| Dogri | `doi-IN` | 3 | 5 | 100 | 100 | **0** | **0** | 211 | #497 `S` |
| Dutch | `dut-NL` | 25 | 1 | 100 | 100 | **0** | **0** | 211 | #408 `S` |
| Dzongkha | `dzo-BT` | < 1 | 5 | 100 | 100 | **0** | **0** | 211 | #511 `S` |
| English (Australia) | `eng-AU` | 1,500 | 1 | 100 | 100 | 100 | 100 | 0 | source texts |
| English (United States) | `eng-US` | 1,500 | 1 | 100 | 100 | 100 | 100 | 0 | source texts |
| Esperanto | `epo-EO` | < 1 | 2 | 100 | 100 | **0** | **0** | 211 | #520 `S` |
| Estonian | `est-EE` | 1 | 3 | 100 | 100 | **0** | **0** | 211 | #500 `S` |
| Ewe | `ewe-GH` | 5 | 3 | 100 | 100 | **0** | **0** | 211 | #479 `S` |
| Faroese | `fao-FO` | < 1 | 4 | 100 | 100 | **0** | **0** | 211 | #523 `S` |
| Filipino | `fil-PH` | 83 | 2 | 100 | 100 | **0** | **0** | 211 | #402 `S` |
| Finnish | `fin-FI` | 6 | 2 | 100 | 100 | **0** | **0** | 211 | #467 `S` |
| French | `fre-FR` | 310 | 1 | 100 | 100 | **0** | **0** | 211 | #391 `S` |
| Fula | `ful-SN` | 37 | 4 | 100 | 100 | **0** | **0** | 211 | #432 `S` |
| Galician | `glg-ES` | 2 | 2 | 100 | 100 | **0** | **0** | 211 | #487 `S` |
| Ganda | `lug-UG` | 11 | 4 | 100 | 100 | **0** | **0** | 211 | #469 `S` |
| Georgian | `kat-GE` | 4 | 4 | 100 | 100 | **0** | **0** | 211 | #493 `S` |
| German | `ger-DE` | 135 | 2 | 100 | **76** | **89** | 99 | 9 | #1 `E` |
| Greek | `ell-GR` | 13 | 3 | 100 | 100 | **0** | **0** | 211 | #455 `S` |
| Greenlandic | `kal-GL` | < 1 | 4 | 100 | 100 | **0** | **0** | 211 | #524 `S` |
| Gujarati | `guj-IN` | 62 | 4 | 100 | 100 | **0** | **0** | 211 | #417 `S` |
| Hausa | `hau-NG` | 94 | 3 | 100 | 100 | **0** | **0** | 211 | #405 `S` |
| Hawaiian | `haw-US` | < 1 | 3 | 100 | 100 | **0** | **0** | 211 | #526 `S` |
| Hebrew | `heb-IL` | 9 | 4 | 100 | 100 | **0** | **0** | 211 | #472 `S` |
| Hindi | `hin-IN` | 610 | 4 | 100 | 100 | **0** | **0** | 211 | #394 `S` |
| Hulaulá (Jewish Neo-Aramaic) | `huy-IL` | < 1 | 5 | 100 | 100 | **0** | **0** | 211 | #530 `S` |
| Hungarian | `hun-HU` | 13 | 2 | 100 | 100 | **0** | **0** | 211 | #446 `S` |
| Icelandic | `isl-IS` | < 1 | 3 | 100 | 100 | **0** | **0** | 211 | #513 `S` |
| Igbo | `ibo-NG` | 31 | 3 | 100 | 100 | **0** | **0** | 211 | #428 `S` |
| Indonesian | `ind-ID` | 252 | 1 | 100 | 100 | **0** | **0** | 211 | #392 `S` |
| Irish | `gle-IE` | 2 | 4 | 100 | 100 | **0** | **0** | 211 | #498 `S` |
| isiXhosa | `xho-ZA` | 19 | 4 | 100 | 100 | **0** | **0** | 211 | #451 `S` |
| isiZulu | `zul-ZA` | 28 | 4 | 100 | 100 | **0** | **0** | 211 | #443 `S` |
| Italian | `ita-IT` | 68 | 1 | 100 | 100 | **0** | **0** | 211 | #398 `S` |
| Japanese | `jpn-JP` | 123 | 3 | 100 | 100 | **0** | **0** | 211 | #403 `S` |
| Javanese | `jav-ID` | 68 | 3 | 100 | 100 | **0** | **0** | 211 | #411 `S` |
| Kabyle | `kab-DZ` | 5 | 4 | 100 | 100 | **0** | **0** | 211 | #485 `S` |
| Kannada | `kan-IN` | 59 | 5 | 100 | 100 | **0** | **0** | 211 | #423 `S` |
| Kashmiri | `kas-IN` | 7 | 5 | 100 | 100 | **0** | **0** | 211 | #482 `S` |
| Kazakh | `kaz-KZ` | 16 | 4 | 100 | 100 | **0** | **0** | 211 | #457 `S` |
| Khmer | `khm-KH` | 18 | 4 | 100 | 100 | **0** | **0** | 211 | #453 `S` |
| Kikuyu | `kik-KE` | 8 | 4 | 100 | 100 | **0** | **0** | 211 | #475 `S` |
| Kinyarwanda | `kin-RW` | 15 | 4 | 100 | 100 | **0** | **0** | 211 | #458 `S` |
| Kirundi | `run-BI` | 12 | 4 | 100 | 100 | **0** | **0** | 211 | #466 `S` |
| Klingon | `tlh-QO` | < 1 | 4 | 100 | 100 | **0** | **0** | 211 | #533 `S` |
| Konkani | `kok-IN` | 2 | 5 | 100 | 100 | **0** | **0** | 211 | #499 `S` |
| Korean | `kor-KR` | 82 | 3 | 100 | 100 | **0** | **0** | 211 | #406 `S` |
| Korean (North Korea, Munhwaŏ) | `kor-KP` | 26 | 3 | 100 | 100 | **0** | **0** | 211 | #434 `S` |
| Kurdish (Kurmanji) | `kur-TR` | 16 | 3 | 100 | 100 | **0** | **0** | 211 | #12 `S` |
| Kyrgyz | `kir-KG` | 5 | 5 | 100 | 100 | **0** | **0** | 211 | #490 `S` |
| Lao | `lao-LA` | 30 | 5 | 100 | 100 | **0** | **0** | 211 | #448 `S` |
| Latin | `lat-VA` | < 1 | 4 | 100 | 100 | **0** | **0** | 211 | #531 `S` |
| Latvian | `lav-LV` | 2 | 3 | 100 | 100 | **0** | **0** | 211 | #496 `S` |
| Lingala | `lin-CD` | 40 | 4 | 100 | 100 | **0** | **0** | 211 | #430 `S` |
| Lithuanian | `lit-LT` | 3 | 3 | 100 | 100 | **0** | **0** | 211 | #491 `S` |
| Lower Sorbian | `dsb-DE` | < 1 | 4 | 100 | 100 | **0** | **0** | 211 | #529 `S` |
| Luxembourgish | `ltz-LU` | < 1 | 2 | 100 | 100 | **0** | **0** | 211 | #506 `S` |
| Macedonian | `mkd-MK` | 2 | 3 | 100 | 100 | **0** | **0** | 211 | #495 `S` |
| Maithili | `mai-IN` | 34 | 5 | 100 | 100 | **0** | **0** | 211 | #444 `S` |
| Malagasy | `mlg-MG` | 25 | 3 | 100 | 100 | **0** | **0** | 211 | #437 `S` |
| Malay | `msa-MY` | 60 | 1 | 100 | 100 | **0** | **0** | 211 | #399 `S` |
| Malayalam | `mal-IN` | 37 | 5 | 100 | 100 | **0** | **0** | 211 | #441 `S` |
| Maltese | `mlt-MT` | < 1 | 3 | 100 | 100 | **0** | **0** | 211 | #508 `S` |
| Manipuri | `mni-IN` | 2 | 5 | 100 | 100 | **0** | **0** | 211 | #502 `S` |
| Māori | `mri-NZ` | < 1 | 3 | 100 | 100 | **0** | **0** | 211 | #517 `S` |
| Marathi | `mar-IN` | 99 | 4 | 100 | 100 | **0** | **0** | 211 | #410 `S` |
| Mongolian | `mon-MN` | 6 | 5 | 100 | 100 | **0** | **0** | 211 | #488 `S` |
| Nepali | `nep-NP` | 32 | 4 | 100 | 100 | **0** | **0** | 211 | #438 `S` |
| Nigerian Pidgin | `pcm-NG` | 120 | 3 | 100 | 100 | **0** | **0** | 211 | #404 `S` |
| Northern Sami | `sme-NO` | < 1 | 4 | 100 | 100 | **0** | **0** | 211 | #525 `S` |
| Norwegian | `nor-NO` | 5 | 1 | 100 | 100 | **0** | **0** | 211 | #450 `S` |
| N’Ko | `nqo-GN` | – | 5 | 100 | 100 | **0** | **0** | 211 | #532 `S` |
| Occitan | `oci-FR` | < 1 | 3 | 100 | 100 | **0** | **0** | 211 | #510 `S` |
| Odia | `ori-IN` | 40 | 5 | 100 | 100 | **0** | **0** | 211 | #439 `S` |
| Oromo | `orm-ET` | 45 | 4 | 100 | 100 | **0** | **0** | 211 | #425 `S` |
| Pashto | `pus-AF` | 60 | 5 | 100 | 100 | **0** | **0** | 211 | #11 `S` |
| Persian | `fas-IR` | 83 | 4 | 100 | 100 | **0** | **0** | 211 | #10 `S` |
| Polish | `pol-PL` | 40 | 2 | 100 | 100 | **0** | **0** | 211 | #412 `S` |
| Portuguese (Brazil) | `por-BR` | 215 | 1 | 100 | 100 | **0** | **0** | 211 | #393 `S` |
| Portuguese (Portugal) | `por-PT` | 50 | 1 | 100 | 100 | **0** | **0** | 211 | #401 `S` |
| Punjabi | `pan-IN` | 48 | 4 | 100 | 100 | **0** | **0** | 211 | #422 `S` |
| Quechua | `que-PE` | 8 | 4 | 100 | 100 | **0** | **0** | 211 | #476 `S` |
| Romanian | `ron-RO` | 25 | 1 | 100 | 100 | **0** | **0** | 211 | #409 `S` |
| Romansh | `roh-CH` | < 1 | 3 | 100 | 100 | **0** | **0** | 211 | #522 `S` |
| Russian | `rus-RU` | 255 | 3 | 100 | 100 | **0** | **0** | 211 | #9 `S` |
| Sanskrit | `san-IN` | < 1 | 5 | 100 | 100 | **0** | **0** | 211 | #527 `S` |
| Santali | `sat-IN` | 8 | 5 | 100 | 100 | **0** | **0** | 211 | #480 `S` |
| Scottish Gaelic | `gla-GB` | < 1 | 4 | 100 | 100 | **0** | **0** | 211 | #521 `S` |
| Serbian (Cyrillic) | `srp-RS` | 10 | 4 | 100 | 100 | **0** | **0** | 211 | #471 `S` |
| Serbian (Latin) | `ser-SR` | 10 | 3 | 100 | 100 | **0** | **0** | 211 | #464 `S` |
| Shona | `sna-ZW` | 15 | 4 | 100 | 100 | **0** | **0** | 211 | #459 `S` |
| Sindhi | `snd-PK` | 34 | 5 | 100 | 100 | **0** | **0** | 211 | #445 `S` |
| Sinhala | `sin-LK` | 18 | 4 | 100 | 100 | **0** | **0** | 211 | #454 `S` |
| Slovak | `slk-SK` | 7 | 2 | 100 | 100 | **0** | **0** | 211 | #462 `S` |
| Slovenian | `slv-SI` | 3 | 3 | 100 | 100 | **0** | **0** | 211 | #494 `S` |
| Somali | `som-SO` | 24 | 3 | 100 | 100 | **0** | **0** | 211 | #440 `S` |
| Spanish (Mexico) | `esl-MX` | 130 | 1 | 100 | 100 | **0** | **0** | 211 | #395 `S` |
| Spanish (Spain) | `esl-ES` | 430 | 1 | 100 | 100 | **0** | **0** | 211 | #390 `S` |
| Standard Moroccan Tamazight | `zgh-MA` | 14 | 5 | 100 | 100 | **0** | **0** | 211 | #468 `S` |
| Sundanese | `sun-ID` | 32 | 3 | 100 | 100 | **0** | **0** | 211 | #426 `S` |
| Swahili | `swa-KE` | 80 | 3 | 100 | 100 | **0** | **0** | 211 | #407 `S` |
| Swedish | `swe-SE` | 13 | 1 | 100 | 100 | **0** | **0** | 211 | #421 `S` |
| Syriac | `syr-IQ` | < 1 | 5 | 100 | 100 | **0** | **0** | 211 | #515 `S` |
| Tajik | `tgk-TJ` | 9 | 4 | 100 | 100 | **0** | **0** | 211 | #473 `S` |
| Tamil | `tam-IN` | 87 | 5 | 100 | 100 | **0** | **0** | 211 | #415 `S` |
| Tatar | `tat-RU` | 5 | 5 | 100 | 100 | **0** | **0** | 211 | #492 `S` |
| Telugu | `tel-IN` | 96 | 5 | 100 | 100 | **0** | **0** | 211 | #413 `S` |
| Thai | `tha-TH` | 61 | 4 | 100 | 100 | **0** | **0** | 211 | #419 `S` |
| Tibetan | `bod-CN` | 6 | 5 | 100 | 100 | **0** | **0** | 211 | #486 `S` |
| Tigrinya | `tir-ET` | 10 | 5 | 100 | 100 | **0** | **0** | 211 | #477 `S` |
| Tongan | `ton-TO` | < 1 | 3 | 100 | 100 | **0** | **0** | 211 | #518 `S` |
| Turkish | `tur-TR` | 90 | 2 | 100 | 100 | **0** | **0** | 211 | #7 `S` |
| Turkmen | `tuk-TM` | 7 | 4 | 100 | 100 | **0** | **0** | 211 | #478 `S` |
| Ukrainian | `ukr-UA` | 39 | 3 | 100 | 100 | **0** | **0** | 211 | #8 `S` |
| Upper Sorbian | `hsb-DE` | < 1 | 4 | 100 | 100 | **0** | **0** | 211 | #528 `S` |
| Urdu | `urd-PK` | 232 | 4 | 100 | 100 | **0** | **0** | 211 | #400 `S` |
| Uyghur | `uig-CN` | 11 | 5 | 100 | 100 | **0** | **0** | 211 | #474 `S` |
| Uzbek | `uzb-UZ` | 35 | 3 | 100 | 100 | **0** | **0** | 211 | #424 `S` |
| Vietnamese | `vie-VN` | 86 | 1 | 100 | 100 | **0** | **0** | 211 | #396 `S` |
| Welsh | `cym-GB` | < 1 | 3 | 100 | 100 | **0** | **0** | 211 | #504 `S` |
| Western Frisian | `fry-NL` | < 1 | 3 | 100 | 100 | **0** | **0** | 211 | #509 `S` |
| Western Punjabi | `pnb-PK` | 90 | 5 | 100 | 100 | **0** | **0** | 211 | #414 `S` |
| Wolof | `wol-SN` | 22 | 3 | 100 | 100 | **0** | **0** | 211 | #442 `S` |
| Yiddish | `yid-US` | 1 | 5 | 100 | 100 | **0** | **0** | 211 | #507 `S` |
| Yoruba | `yor-NG` | 46 | 3 | 100 | 100 | **0** | **0** | 211 | #418 `S` |

### The help in tiers

| Tier | Sections | Pages | Words | Parts |
|---|---|---:|---:|---:|
| Daily use: editors, users, administrators’ how-tos | `help`, `help/manual`, `help/non-technical-people`, `guides`, `help/book-learning`, `help/book-building-a-site` | 314 | 406,374 | 16 |
| Product and audience guides | `install`, `features`, `help/adopters`, `help/advocates`, `help/business`, `help/businesses`, `help/community`, `help/creators` and 24 more | 3,735 | 2,976,612 | 118 |
| Developer reference | `help/extension-development`, `help/template-language`, `help/template-operators`, `help/build-two-ways`, `help/php-developers`, `help/cms-developers`, `help/dxp-developers`, `help/software-engineers` and 12 more | 1,804 | 1,851,757 | 72 |
| Tables of the generated examples | `examples` | 3,763 entries | 103,791 | 4 |

<details><summary>The 210 parts of the help</summary>

| Part | Tier | Pages | Words | First … last page |
|---|---|---:|---:|---|
| `H-help-01` | daily | 13 | 32,546 | `help/README.md` … `help/editing-content.md` |
| `H-help-02` | daily | 13 | 16,112 | `help/free-documentation.md` … `help/project-files.md` |
| `H-help-03` | daily | 13 | 34,697 | `help/publishing-and-versions.md` … `help/why-exponential-supports-php-7-4.md` |
| `H-help-manual-01` | daily | 44 | 17,659 | `help/manual/README.md` … `help/manual/how-do-i-let-a-translator-edit-one-language.md` |
| `H-help-manual-02` | daily | 44 | 18,751 | `help/manual/how-do-i-move-a-user-to-another-group.md` … `help/manual/why-does-search-not-find-an-item.md` |
| `H-help-non-technical-people` | daily | 47 | 28,042 | `help/non-technical-people/checklists/README.md` … `help/non-technical-people/words/the-site-and-the-administration.md` |
| `H-guides-01` | daily | 12 | 35,377 | `guides/README.md` … `guides/content-languages.md` |
| `H-guides-02` | daily | 13 | 34,316 | `guides/content-model-and-editing.md` … `guides/litespeed-page-cache.md` |
| `H-guides-03` | daily | 12 | 26,456 | `guides/mail-preferences-administrator.md` … `guides/notifications-administrator.md` |
| `H-guides-04` | daily | 13 | 37,355 | `guides/notifications-developer.md` … `guides/security-and-audit.md` |
| `H-guides-05` | daily | 12 | 30,655 | `guides/sessions.md` … `guides/smarty-templates-security.md` |
| `H-guides-06` | daily | 13 | 25,986 | `guides/smarty-templates-troubleshooting.md` … `guides/twig-templates-converter.md` |
| `H-guides-07` | daily | 12 | 13,159 | `guides/twig-templates-debugging.md` … `guides/twig-templates-troubleshooting.md` |
| `H-guides-08` | daily | 13 | 31,303 | `guides/twig-templates-tutorial.md` … `guides/your-devices-and-sessions.md` |
| `H-help-book-learning` | daily | 31 | 18,870 | `help/book-learning/README.md` … `help/book-learning/workflows-and-triggers.md` |
| `H-help-book-building-a-site` | daily | 9 | 5,090 | `help/book-building-a-site/README.md` … `help/book-building-a-site/the-news-pages.md` |
| `H-install-01` | guides | 7 | 36,128 | `install/01-introduction.md` … `install/07-console-install.md` |
| `H-install-02` | guides | 7 | 60,649 | `install/08-serving-the-site.md` … `install/14-migrating-from-4x.md` |
| `H-install-03` | guides | 7 | 39,901 | `install/15-migrating-from-5x-legacy.md` … `install/migration/checklist.md` |
| `H-install-04` | guides | 7 | 8,467 | `install/migration/coldfusion-apps.md` … `install/migration/exponential-platform-legacy.md` |
| `H-install-05` | guides | 7 | 7,934 | `install/migration/exponential-platform-nexus.md` … `install/migration/magento.md` |
| `H-install-06` | guides | 7 | 5,299 | `install/migration/older-releases.md` … `install/migration/wordpress.md` |
| `H-features-01` | guides | 21 | 36,535 | `features/4.7/cluster_events.md` … `features/6.0/benchmark.md` |
| `H-features-02` | guides | 22 | 39,619 | `features/6.0/bookmark-folders.md` … `features/6.0/exp-debug-bar.md` |
| `H-features-03` | guides | 22 | 16,907 | `features/6.0/exp-ini-command.md` … `features/6.0/extensions/ezautosave.md` |
| `H-features-04` | guides | 21 | 16,558 | `features/6.0/extensions/ezdemo.md` … `features/6.0/extensions/git_manager.md` |
| `H-features-05` | guides | 22 | 44,701 | `features/6.0/extensions/hcaptcha.md` … `features/6.0/help-system.md` |
| `H-features-06` | guides | 22 | 32,672 | `features/6.0/hidden-admin-tabs.md` … `features/6.0/online-editor-tinymce8.md` |
| `H-features-07` | guides | 21 | 21,867 | `features/6.0/order-list-sorting.md` … `features/6.0/publish-without-notification.md` |
| `H-features-08` | guides | 22 | 26,313 | `features/6.0/quality-checks.md` … `features/6.0/secret-store.md` |
| `H-features-09` | guides | 22 | 27,229 | `features/6.0/setup-files-and-config-php-commands.md` … `features/6.0/ui-switch-design.md` |
| `H-features-10` | guides | 22 | 31,249 | `features/6.0/ui-switch-languages.md` … `features/6.0/website-sidebar.md` |
| `H-help-adopters` | guides | 46 | 16,730 | `help/adopters/adopters-checklists.md` … `help/adopters/what-adoption-means.md` |
| `H-help-advocates-01` | guides | 48 | 29,465 | `help/advocates/7x-and-the-foundation-for-advocates.md` … `help/advocates/proposing-a-contribution-sprint.md` |
| `H-help-advocates-02` | guides | 48 | 29,608 | `help/advocates/proposing-a-meetup.md` … `help/advocates/your-first-week-as-an-advocate.md` |
| `H-help-business-01` | guides | 6 | 15,690 | `help/business/brand-and-naming-rules.md` … `help/business/feature-highlights-for-customers.md` |
| `H-help-business-02` | guides | 6 | 15,376 | `help/business/growing-the-community.md` … `help/business/who-exponential-is-for.md` |
| `H-help-businesses-01` | guides | 26 | 25,794 | `help/businesses/associations/README.md` … `help/businesses/costs/what-you-do-not-pay-for.md` |
| `H-help-businesses-02` | guides | 26 | 25,357 | `help/businesses/costs/worked-example-intranet.md` … `help/businesses/intranets/two-factor-and-sign-in.md` |
| `H-help-businesses-03` | guides | 27 | 26,097 | `help/businesses/libraries/README.md` … `help/businesses/mobile-and-headless/responsive-sites.md` |
| `H-help-businesses-04` | guides | 26 | 24,905 | `help/businesses/mobile-and-headless/rest-api-v1-and-v2-for-businesses.md` … `help/businesses/newsletters/opt-in-and-consent.md` |
| `H-help-businesses-05` | guides | 27 | 29,918 | `help/businesses/newsletters/writing-and-sending-editions.md` … `help/businesses/reliability/restore-drills.md` |
| `H-help-businesses-06` | guides | 26 | 26,999 | `help/businesses/reliability/static-and-response-cache.md` … `help/businesses/stores/payment-gateways-for-businesses.md` |
| `H-help-businesses-07` | guides | 27 | 22,320 | `help/businesses/stores/paypal-payments.md` … `help/businesses/why-businesses-rely-on-exponential.md` |
| `H-help-community` | guides | 52 | 27,091 | `help/community/community-checklists.md` … `help/community/your-first-week-in-the-community.md` |
| `H-help-creators-01` | guides | 46 | 23,103 | `help/creators/app-creators/access-control-in-code.md` … `help/creators/app-creators/rad-tools.md` |
| `H-help-creators-02` | guides | 47 | 26,370 | `help/creators/app-creators/remote-services-expservices.md` … `help/creators/website-creators/member-registration-and-areas.md` |
| `H-help-creators-03` | guides | 47 | 29,000 | `help/creators/website-creators/menus-and-navigation.md` … `help/creators/website-creators/your-first-site-step-by-step.md` |
| `H-help-customers-01` | guides | 37 | 25,803 | `help/customers/commerce/README.md` … `help/customers/day-to-day/analytics-and-what-to-measure.md` |
| `H-help-customers-02` | guides | 38 | 26,709 | `help/customers/day-to-day/approvals-and-review.md` … `help/customers/growth/rss-and-podcast-feeds.md` |
| `H-help-customers-03` | guides | 38 | 24,975 | `help/customers/growth/using-ai-drafts-responsibly.md` … `help/customers/partners-and-hosts/who-does-what.md` |
| `H-help-customers-04` | guides | 38 | 22,272 | `help/customers/partners-and-hosts/working-with-7x-and-the-project.md` … `help/customers/what-you-own.md` |
| `H-help-designers-01` | guides | 63 | 28,332 | `help/designers/README.md` … `help/designers/extensions/gitignore-for-themes.md` |
| `H-help-designers-02` | guides | 64 | 24,317 | `help/designers/extensions/install-and-changelog-files.md` … `help/designers/sales/assets-under-separate-licenses.md` |
| `H-help-designers-03` | guides | 63 | 23,137 | `help/designers/sales/contracts-and-terms.md` … `help/designers/templates/if-and-conditions.md` |
| `H-help-designers-04` | guides | 64 | 26,861 | `help/designers/templates/include.md` … `help/designers/theme-design/color-contrast.md` |
| `H-help-designers-05` | guides | 64 | 38,282 | `help/designers/theme-design/csp-safe-design.md` … `help/designers/theme-design/what-a-design-is.md` |
| `H-help-e-commerce-01` | guides | 26 | 17,960 | `help/e-commerce/catalog/README.md` … `help/e-commerce/operations/analytics-for-stores.md` |
| `H-help-e-commerce-02` | guides | 27 | 18,329 | `help/e-commerce/operations/commerce-audit-trail.md` … `help/e-commerce/settings/workflow-ini-for-the-shop.md` |
| `H-help-ecosystem` | guides | 45 | 18,854 | `help/ecosystem/a-short-history-of-the-ecosystem.md` … `help/ecosystem/what-the-ecosystem-is.md` |
| `H-help-government-01` | guides | 52 | 32,618 | `help/government/accessibility/README.md` … `help/government/building/documents-and-publications/pdf-export.md` |
| `H-help-government-02` | guides | 53 | 24,724 | `help/government/building/documents-and-publications/versions-of-documents.md` … `help/government/building/news-and-alerts/README.md` |
| `H-help-government-03` | guides | 53 | 28,368 | `help/government/building/news-and-alerts/archive-and-expiry.md` … `help/government/checklists/quarterly-access-review.md` |
| `H-help-government-04` | guides | 53 | 31,186 | `help/government/checklists/records-checklist.md` … `help/government/languages/removing-a-translation-safely.md` |
| `H-help-government-05` | guides | 53 | 34,369 | `help/government/languages/right-to-left-in-the-administration.md` … `help/government/levels/parliaments/what-it-publishes.md` |
| `H-help-government-06` | guides | 53 | 35,570 | `help/government/levels/public-health/README.md` … `help/government/operations/availability/high-availability-overview.md` |
| `H-help-government-07` | guides | 53 | 38,916 | `help/government/operations/availability/maintenance-mode-and-planned-downtime.md` … `help/government/operations/incidents/database-unreachable.md` |
| `H-help-government-08` | guides | 53 | 34,111 | `help/government/operations/incidents/ddos-and-probe-floods.md` … `help/government/privacy/analytics.md` |
| `H-help-government-09` | guides | 52 | 31,753 | `help/government/privacy/audit-retention.md` … `help/government/procurement/exit-plans/lock-in-clauses-to-avoid.md` |
| `H-help-government-10` | guides | 53 | 34,358 | `help/government/procurement/exit-plans/personal-data-at-exit.md` … `help/government/procurement/total-cost-of-ownership/staff-and-skills.md` |
| `H-help-government-11` | guides | 53 | 30,491 | `help/government/procurement/total-cost-of-ownership/support-contracts.md` … `help/government/security/file-uploads-and-svg.md` |
| `H-help-government-12` | guides | 53 | 31,594 | `help/government/security/forwarding-audit-to-a-siem.md` … `help/government/tasks/compliance/add-security-txt.md` |
| `H-help-government-13` | guides | 53 | 19,557 | `help/government/tasks/compliance/add-your-own-words-to-the-weak-password-list.md` … `help/government/tasks/operations/bring-the-site-back-online.md` |
| `H-help-government-14` | guides | 53 | 23,496 | `help/government/tasks/operations/check-file-and-directory-permissions.md` … `help/government/tasks/people-and-permissions/keep-editors-out-of-the-admin-siteaccess.md` |
| `H-help-government-15` | guides | 53 | 25,206 | `help/government/tasks/people-and-permissions/let-a-contractor-edit-only-one-folder.md` … `help/government/tasks/publishing/find-and-fix-broken-links.md` |
| `H-help-government-16` | guides | 53 | 24,198 | `help/government/tasks/publishing/follow-changes-with-notifications.md` … `help/government/tasks/sites-and-languages/connect-staff-sign-in-to-ldap.md` |
| `H-help-government-17` | guides | 53 | 21,481 | `help/government/tasks/sites-and-languages/export-a-list-of-content-as-csv.md` … `help/government/transparency-and-foi/withdrawing-and-correcting-published-information.md` |
| `H-help-hosting-01` | guides | 23 | 24,789 | `help/hosting/basics/README.md` … `help/hosting/business/licensing-for-hosts.md` |
| `H-help-hosting-02` | guides | 24 | 22,159 | `help/hosting/business/marketing-exponential-hosting.md` … `help/hosting/models/kubernetes.md` |
| `H-help-hosting-03` | guides | 24 | 31,355 | `help/hosting/models/managed-hosting.md` … `help/hosting/operations/several-web-nodes.md` |
| `H-help-hosting-04` | guides | 24 | 29,195 | `help/hosting/operations/storage-layout.md` … `help/hosting/performance/valkey-and-redis.md` |
| `H-help-hosting-05` | guides | 24 | 22,422 | `help/hosting/performance/varnish-and-reverse-proxies.md` … `help/hosting/security/content-security-policy.md` |
| `H-help-hosting-06` | guides | 24 | 32,997 | `help/hosting/security/demo-mode-for-hosts.md` … `help/hosting/servers/velocity-workers-and-pools.md` |
| `H-help-individuals-01` | guides | 27 | 19,732 | `help/individuals/blogs/README.md` … `help/individuals/individuals-glossary.md` |
| `H-help-individuals-02` | guides | 28 | 21,863 | `help/individuals/individuals-reading-path.md` … `help/individuals/what-exists-and-what-does-not.md` |
| `H-help-leadership-01` | guides | 9 | 19,895 | `help/leadership/accessibility-security-and-compliance-checklists.md` … `help/leadership/planning-an-exponential-project.md` |
| `H-help-leadership-02` | guides | 9 | 20,248 | `help/leadership/project-managers-start-here.md` … `help/leadership/working-with-the-community.md` |
| `H-help-local-businesses-01` | guides | 27 | 17,683 | `help/local-businesses/budget/README.md` … `help/local-businesses/getting-started/working-with-a-local-partner-or-freelancer.md` |
| `H-help-local-businesses-02` | guides | 28 | 19,105 | `help/local-businesses/local-businesses-faq.md` … `help/local-businesses/templates/website-brief-for-a-local-business.md` |
| `H-help-migrations-01` | guides | 19 | 14,869 | `help/migrations/README.md` … `help/migrations/recipes/create-or-change-a-content-class.md` |
| `H-help-migrations-02` | guides | 19 | 16,020 | `help/migrations/recipes/deploying-with-migrations.md` … `help/migrations/when-a-migration-fails.md` |
| `H-help-non-profits-01` | guides | 27 | 18,785 | `help/non-profits/accessibility/README.md` … `help/non-profits/non-profits-faq.md` |
| `H-help-non-profits-02` | guides | 28 | 19,746 | `help/non-profits/non-profits-glossary.md` … `help/non-profits/volunteers/volunteers-as-editors.md` |
| `H-help-partners-01` | guides | 37 | 15,750 | `help/partners/application-checklist.md` … `help/partners/partner-faq.md` |
| `H-help-partners-02` | guides | 38 | 17,508 | `help/partners/partner-glossary.md` … `help/partners/your-partner-page.md` |
| `H-help-podcasters-01` | guides | 24 | 17,308 | `help/podcasters/a-release-calendar.md` … `help/podcasters/podcaster-checklists.md` |
| `H-help-podcasters-02` | guides | 24 | 18,395 | `help/podcasters/podcaster-faq.md` … `help/podcasters/writing-show-notes.md` |
| `H-help-press-01` | guides | 37 | 27,177 | `help/press/7x-for-reporters.md` … `help/press/fact-checking-checklist.md` |
| `H-help-press-02` | guides | 38 | 25,010 | `help/press/fact-checking-guide.md` … `help/press/release-story-templates.md` |
| `H-help-press-03` | guides | 38 | 23,821 | `help/press/reporter-faq.md` … `help/press/story-nexus-and-legacy.md` |
| `H-help-press-04` | guides | 38 | 25,512 | `help/press/story-online-editor-tinymce-8.md` … `help/press/writing-about-the-ez-publish-years.md` |
| `H-help-project` | guides | 12 | 16,029 | `help/project/README.md` … `help/project/roadmap.md` |
| `H-help-resellers-01` | guides | 26 | 17,388 | `help/resellers/basics/choosing-a-partner-level.md` … `help/resellers/offers/demo-sites.md` |
| `H-help-resellers-02` | guides | 27 | 18,541 | `help/resellers/offers/e-commerce-offers.md` … `help/resellers/sales/sales-process.md` |
| `H-help-site-packages-01` | guides | 26 | 27,331 | `help/site-packages/README.md` … `help/site-packages/cjw-multisite/the-cjw-themes-jumper-extension.md` |
| `H-help-site-packages-02` | guides | 26 | 29,251 | `help/site-packages/cjw-multisite/the-demo-content-package.md` … `help/site-packages/requirements/the-requirements-checklist.md` |
| `H-help-site-packages-03` | guides | 26 | 26,765 | `help/site-packages/requirements/thumbnails-and-screenshots.md` … `help/site-packages/your-own/versioning-your-own-packages.md` |
| `H-help-sponsors` | guides | 11 | 5,286 | `help/sponsors/7x-the-first-sponsor.md` … `help/sponsors/what-sponsorship-funds.md` |
| `H-help-students-01` | guides | 27 | 21,052 | `help/students/asking-for-help-and-study-groups.md` … `help/students/project-04-a-page-with-layouts.md` |
| `H-help-students-02` | guides | 28 | 23,247 | `help/students/project-05-a-multilingual-site.md` … `help/students/your-first-site-three-ways.md` |
| `H-help-supporters` | guides | 12 | 5,721 | `help/supporters/advocacy.md` … `help/supporters/ways-to-support-exponential.md` |
| `H-help-theme-designers-01` | guides | 21 | 17,949 | `help/theme-designers/building-a-portfolio-and-getting-work.md` … `help/theme-designers/designing-the-front-page.md` |
| `H-help-theme-designers-02` | guides | 22 | 19,306 | `help/theme-designers/designing-view-modes.md` … `help/theme-designers/your-first-day-as-a-theme-designer.md` |
| `H-help-trainers-01` | guides | 31 | 31,213 | `help/trainers/assessments/README.md` … `help/trainers/assessments/module-05-newsletter-and-migration/README.md` |
| `H-help-trainers-02` | guides | 32 | 32,172 | `help/trainers/assessments/module-05-newsletter-and-migration/migration-practical-task.md` … `help/trainers/developers/module-03-designs-overrides-and-debugging/the-design-chain.md` |
| `H-help-trainers-03` | guides | 31 | 29,141 | `help/trainers/developers/module-04-content-classes-and-datatypes/README.md` … `help/trainers/developers/module-09-the-console-and-persistent-objects/sql-safely-with-ezdb.md` |
| `H-help-trainers-04` | guides | 32 | 30,122 | `help/trainers/developers/module-09-the-console-and-persistent-objects/the-console.md` … `help/trainers/editors-101/module-04-the-online-editor/README.md` |
| `H-help-trainers-05` | guides | 31 | 28,612 | `help/trainers/editors-101/module-04-the-online-editor/embeds-tables-and-choosing-the-editor.md` … `help/trainers/editors-201/module-02-relations-locations-and-embedding/relations-and-embedding.md` |
| `H-help-trainers-06` | guides | 32 | 26,557 | `help/trainers/editors-201/module-03-organizing-content/README.md` … `help/trainers/exponential-intelligence/module-02-the-intelligence-tab/keeping-sending-and-discarding-drafts.md` |
| `H-help-trainers-07` | guides | 31 | 29,052 | `help/trainers/exponential-intelligence/module-03-the-edit-form/README.md` … `help/trainers/hosting-operations/module-02-velocity/workers-and-pools.md` |
| `H-help-trainers-08` | guides | 32 | 30,822 | `help/trainers/hosting-operations/module-03-databases/README.md` … `help/trainers/layouts/module-01-introducing-layouts/the-five-ideas.md` |
| `H-help-trainers-09` | guides | 32 | 26,649 | `help/trainers/layouts/module-02-layouts-and-layout-types/README.md` … `help/trainers/migration/module-01-planning-a-migration/phases-and-the-checklist.md` |
| `H-help-trainers-10` | guides | 31 | 31,398 | `help/trainers/migration/module-01-planning-a-migration/rehearsal-cut-over-and-rollback.md` … `help/trainers/multisite-and-packages/module-01-siteaccesses-for-several-sites/README.md` |
| `H-help-trainers-11` | guides | 32 | 36,984 | `help/trainers/multisite-and-packages/module-01-siteaccesses-for-several-sites/a-siteaccess-per-language.md` … `help/trainers/newsletter/README.md` |
| `H-help-trainers-12` | guides | 31 | 30,359 | `help/trainers/newsletter/module-01-getting-started/README.md` … `help/trainers/search/module-01-how-search-works/the-built-in-search-engine.md` |
| `H-help-trainers-13` | guides | 32 | 31,945 | `help/trainers/search/module-01-how-search-works/where-people-search.md` … `help/trainers/site-administrators/module-02-users-and-groups/passwords-resets-and-lockouts.md` |
| `H-help-trainers-14` | guides | 31 | 32,262 | `help/trainers/site-administrators/module-02-users-and-groups/unactivated-users-and-account-checks.md` … `help/trainers/site-administrators/module-07-logs-and-the-audit-trail/the-log-viewer.md` |
| `H-help-trainers-15` | guides | 32 | 35,328 | `help/trainers/site-administrators/module-08-security/README.md` … `help/trainers/theme-designers/module-03-stylesheets-and-scripts/listing-files-in-design-ini.md` |
| `H-help-trainers-16` | guides | 31 | 30,777 | `help/trainers/theme-designers/module-03-stylesheets-and-scripts/ready-for-a-content-security-policy.md` … `help/trainers/train-the-trainer/module-01-preparing-to-teach/the-help-center-as-your-textbook.md` |
| `H-help-trainers-17` | guides | 32 | 22,473 | `help/trainers/train-the-trainer/module-01-preparing-to-teach/your-material-and-the-gfdl.md` … `help/trainers/train-the-trainer/module-06-assessment-and-follow-up/certificates-of-attendance.md` |
| `H-help-trainers-18` | guides | 32 | 19,670 | `help/trainers/train-the-trainer/module-06-assessment-and-follow-up/checking-understanding-during-class.md` … `help/trainers/workshops/module-05-capstone-projects/multilingual-event-site.md` |
| `H-reference` | guides | 1 | 1,858 | `reference/template-check-rules.md` |
| `H-root-01` | guides | 4 | 12,098 | `INSTALL.md` … `UPDATE.md` |
| `H-root-02` | guides | 4 | 34,232 | `branch-overview.md` … `languages.md` |
| `H-help-extension-development-01` | reference | 43 | 36,427 | `help/extension-development/README.md` … `help/extension-development/part-2-builder/the-exp-ini-command.md` |
| `H-help-extension-development-02` | reference | 43 | 25,222 | `help/extension-development/part-2-builder/view-modes-for-builders.md` … `help/extension-development/part-3-extension-developer/tutorials/by-hand-custom-tag.md` |
| `H-help-extension-development-03` | reference | 43 | 28,645 | `help/extension-development/part-3-extension-developer/tutorials/by-hand-datatype.md` … `help/extension-development/part-4-upgrading/old-jquery-to-jquery-4.md` |
| `H-help-extension-development-04` | reference | 43 | 30,248 | `help/extension-development/part-4-upgrading/php-8-1-to-8-5-deprecations.md` … `help/extension-development/part-7-releasing/github-releases-and-release-notes.md` |
| `H-help-extension-development-05` | reference | 44 | 24,416 | `help/extension-development/part-7-releasing/packages-ezpkg.md` … `help/extension-development/reading-paths.md` |
| `H-help-template-language-01` | reference | 16 | 18,348 | `help/template-language/README.md` … `help/template-language/moving-from-tpl-to-twig.md` |
| `H-help-template-language-02` | reference | 17 | 17,445 | `help/template-language/namespaces.md` … `help/template-language/what-the-template-language-is.md` |
| `H-help-template-operators-01` | reference | 24 | 24,150 | `help/template-operators/README.md` … `help/template-operators/menu-and-tree-operators.md` |
| `H-help-template-operators-02` | reference | 24 | 30,407 | `help/template-operators/newsletter-operators.md` … `help/template-operators/url-operators.md` |
| `H-help-build-two-ways-01` | reference | 83 | 27,828 | `help/build-two-ways/README.md` … `help/build-two-ways/examples/maintain/add-a-recipe-to-the-index.md` |
| `H-help-build-two-ways-02` | reference | 83 | 20,834 | `help/build-two-ways/examples/maintain/adjust-spacing.md` … `help/build-two-ways/examples/plan/inventory-from-the-console.md` |
| `H-help-build-two-ways-03` | reference | 84 | 30,664 | `help/build-two-ways/examples/plan/inventory-page-types.md` … `help/build-two-ways/your-own-design-extension.md` |
| `H-help-php-developers-01` | reference | 27 | 22,115 | `help/php-developers/a-php-development-checkout.md` … `help/php-developers/php-developer-checklists.md` |
| `H-help-php-developers-02` | reference | 28 | 22,463 | `help/php-developers/php-developer-faq.md` … `help/php-developers/writing-migrations-in-php.md` |
| `H-help-cms-developers-01` | reference | 25 | 23,828 | `help/cms-developers/accessibility.md` … `help/cms-developers/headless-delivery.md` |
| `H-help-cms-developers-02` | reference | 26 | 22,999 | `help/cms-developers/images-and-image-aliases.md` … `help/cms-developers/workflows-and-triggers.md` |
| `H-help-dxp-developers` | reference | 52 | 27,610 | `help/dxp-developers/application-cache-and-redis.md` … `help/dxp-developers/what-the-dxp-ships-and-what-it-does-not.md` |
| `H-help-software-engineers-01` | reference | 26 | 19,175 | `help/software-engineers/api-design-rest-ezjscore-headless.md` … `help/software-engineers/http-security-csp-and-proxies.md` |
| `H-help-software-engineers-02` | reference | 26 | 21,739 | `help/software-engineers/i18n-and-right-to-left.md` … `help/software-engineers/writing-portable-sql.md` |
| `H-help-web-framework-developers-01` | reference | 27 | 18,466 | `help/web-framework-developers/authentication-and-sessions.md` … `help/web-framework-developers/migrations-and-dba-schema-files.md` |
| `H-help-web-framework-developers-02` | reference | 27 | 19,001 | `help/web-framework-developers/mixing-exponential-and-a-framework.md` … `help/web-framework-developers/your-first-module-step-by-step.md` |
| `H-help-mobile-app-developers-01` | reference | 27 | 15,938 | `help/mobile-app-developers/auth/README.md` … `help/mobile-app-developers/media-and-images/images-in-rest-answers.md` |
| `H-help-mobile-app-developers-02` | reference | 28 | 17,528 | `help/mobile-app-developers/mobile-app-developers-faq.md` … `help/mobile-app-developers/which-api-for-your-app.md` |
| `H-help-database-administrators-01` | reference | 24 | 19,430 | `help/database-administrators/applying-the-6-0-15-database-update.md` … `help/database-administrators/mysql-read-replicas.md` |
| `H-help-database-administrators-02` | reference | 25 | 21,820 | `help/database-administrators/oracle-connections-and-tuning.md` … `help/database-administrators/upgrades-and-sql-update-files.md` |
| `H-help-ai-agents-01` | reference | 24 | 16,131 | `help/ai-agents/agentic-workflows/README.md` … `help/ai-agents/expai/chains-and-queued-work.md` |
| `H-help-ai-agents-02` | reference | 24 | 18,470 | `help/ai-agents/expai/configuring-a-provider.md` … `help/ai-agents/which-path-fits-your-task.md` |
| `H-help-bundlers-01` | reference | 27 | 19,462 | `help/bundlers/adding-and-removing-extensions.md` … `help/bundlers/hosting-panel-installers.md` |
| `H-help-bundlers-02` | reference | 28 | 20,538 | `help/bundlers/integrity-checks.md` … `help/bundlers/your-own-package-server.md` |
| `H-help-integrators-01` | reference | 28 | 21,616 | `help/integrators/ai-job-queue-and-mcp.md` … `help/integrators/interface-specification-template.md` |
| `H-help-integrators-02` | reference | 28 | 23,801 | `help/integrators/login-handlers-ldap-and-textfile.md` … `help/integrators/writing-an-import-script.md` |
| `H-help-sqli-import` | reference | 6 | 6,974 | `help/sqli-import/README.md` … `help/sqli-import/upgrading-from-1x.md` |
| `H-specifications-01` | reference | 13 | 16,727 | `specifications/6.0/README.md` … `specifications/6.0/help-standards.md` |
| `H-specifications-02` | reference | 13 | 21,936 | `specifications/6.0/help-style-guide.md` … `specifications/6.0/platform-console-commands.md` |
| `H-specifications-03` | reference | 13 | 21,393 | `specifications/6.0/platform-package-map.md` … `specifications/6.0/xrowextract.md` |
| `H-standards-01` | reference | 18 | 34,531 | `standards/README.md` … `standards/patterns/area-extension-bcgooglesitemaps.md` |
| `H-standards-02` | reference | 18 | 21,769 | `standards/patterns/area-extension-bcwebsitestatistics.md` … `standards/patterns/area-extension-explayouts-content-browser.md` |
| `H-standards-03` | reference | 19 | 15,548 | `standards/patterns/area-extension-explayouts-core.md` … `standards/patterns/area-extension-ezautosave.md` |
| `H-standards-04` | reference | 18 | 10,141 | `standards/patterns/area-extension-ezdemo.md` … `standards/patterns/area-extension-ezprestapiprovider.md` |
| `H-standards-05` | reference | 18 | 10,157 | `standards/patterns/area-extension-ezssp.md` … `standards/patterns/area-extension-sevenx-themes-simple.md` |
| `H-standards-06` | reference | 19 | 22,100 | `standards/patterns/area-extension-sevenx-themes-super.md` … `standards/patterns/area-kernel-classes-datatable.md` |
| `H-standards-07` | reference | 18 | 9,815 | `standards/patterns/area-kernel-classes-datatypes.md` … `standards/patterns/area-kernel-classes-role.md` |
| `H-standards-08` | reference | 18 | 23,036 | `standards/patterns/area-kernel-classes-secrets.md` … `standards/patterns/area-kernel-private-classes-exceptions.md` |
| `H-standards-09` | reference | 19 | 20,557 | `standards/patterns/area-kernel-private-classes-httpcache.md` … `standards/patterns/area-lib-ezsoap.md` |
| `H-standards-10` | reference | 18 | 69,242 | `standards/patterns/area-lib-eztemplate.md` … `standards/patterns/iterator.md` |
| `H-standards-11` | reference | 19 | 79,324 | `standards/patterns/module-view.md` … `standards/web-platform.md` |
| `H-bc-01` | reference | 11 | 42,573 | `bc/5.90/README.md` … `bc/6.0/admin4-styling.md` |
| `H-bc-02` | reference | 11 | 65,344 | `bc/6.0/admin4l.md` … `bc/6.0/cache-console.md` |
| `H-bc-03` | reference | 11 | 30,551 | `bc/6.0/ci.md` … `bc/6.0/draft-edit-access.md` |
| `H-bc-04` | reference | 11 | 25,260 | `bc/6.0/eZINI_PRESERVES_COMMENTS.md` … `bc/6.0/hardening.md` |
| `H-bc-05` | reference | 11 | 20,565 | `bc/6.0/help-content-dita-ebooks-reviews.md` … `bc/6.0/legacy-fork-fixes.md` |
| `H-bc-06` | reference | 11 | 9,492 | `bc/6.0/locations-tab-paging-and-sorting.md` … `bc/6.0/love/ROADMAP.md` |
| `H-bc-07` | reference | 11 | 21,044 | `bc/6.0/love/TESTING.md` … `bc/6.0/oauth-pkce.md` |
| `H-bc-08` | reference | 11 | 14,380 | `bc/6.0/oauth-token-hardening.md` … `bc/6.0/php8.md` |
| `H-bc-09` | reference | 11 | 36,010 | `bc/6.0/phpunitv10.md` … `bc/6.0/response-cache-and-navigation.md` |
| `H-bc-10` | reference | 11 | 30,461 | `bc/6.0/rest-cors.md` … `bc/6.0/trash.md` |
| `H-bc-11` | reference | 12 | 31,053 | `bc/6.0/trusted-proxies.md` … `bc/6.0/yui-removal.md` |
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
| `H-examples` | reference | 26 | 24,275 | `examples/README.md` … `examples/web-servers/velocity.md` |
| `X01` | tables | 1,138 entries | 25,742 | `descriptions/01-command-accesscheck.json` … `descriptions/05-ini-settings-content-ini.json` |
| `X02` | tables | 595 entries | 25,564 | `descriptions/06-ini-settings-debugbar-ini.json` … `descriptions/08-ini-settings-package-ini.json` |
| `X03` | tables | 690 entries | 29,351 | `descriptions/09-ini-settings-site-ini.json` … `descriptions/12-shell-checkcalltimeref.json` |
| `X04` | tables | 1,340 entries | 23,134 | `descriptions/13-command-exptemplateconvert.json` … `strings.json` |

</details>

<details><summary>The 11 parts of the administration catalogue</summary>

| Part | Strings | Words | Contexts (first … last) |
|---|---:|---:|---|
| `A01` | 1,978 | 12,759 | `contentstructuremenu/show_content_structure` … `design/admin/infocollector/confirmremoval` |
| `A02` | 2,000 | 11,504 | `design/admin/infocollector/overview` … `design/admin/preview/company` |
| `A03` | 1,772 | 12,003 | `design/admin/preview/feedbackform` … `design/admin/setup/extensions` |
| `A04` | 1,972 | 20,876 | `design/admin/setup/info` … `design/admin/setup/rad/override` |
| `A05` | 1,998 | 14,899 | `design/admin/setup/rad/settings` … `design/admin/visual/templateview` |
| `A06` | 1,984 | 8,966 | `design/admin/visual/toolbar` … `design/standard/section` |
| `A07` | 1,922 | 13,628 | `design/standard/setup` … `kernel/collaboration` |
| `A08` | 1,839 | 13,074 | `kernel/content` … `kernel/contentfinder` |
| `A09` | 1,992 | 11,165 | `design/admin/audit` … `kernel/mailpreferences` |
| `A10` | 1,964 | 12,811 | `kernel/mailpreferences/mail` … `design/admin/dashboard/migrations` |
| `A11` | 207 | 1,258 | `design/admin/migration` … `design/admin/migration` |

</details>

### Languages without a translation

The languages of the shipped locales that the administration shows in English, the ones being translated on a branch, and languages of five million speakers or more that have no locale yet (the code is a proposal). Sorted by speakers per point of difficulty.

| Language | Code | Speakers (millions) | Script | Direction | Difficulty | State |
|---|---|---:|---|---|:-:|---|
| Haitian Creole | `hat-HT` | 13 | Latn | LTR | 2 | new locale, wave #125 |
| Saraiki | `skr-PK` | 26 | Arab | RTL | 5 | new locale, wave #137 |
| Tswana | `tsn-ZA` | 14 | Latn | LTR | 3 | new locale, wave #149 |
| Dyula | `dyu-CI` | 12 | Latn | LTR | 3 | new locale, wave #161 |
| Chichewa | `nya-MW` | 15 | Latn | LTR | 4 | new locale, wave #173 |
| Northern Sotho | `nso-ZA` | 14 | Latn | LTR | 4 | new locale, wave #185 |
| Sesotho | `sot-ZA` | 14 | Latn | LTR | 4 | new locale, wave #197 |
| Mooré | `mos-BF` | 10 | Latn | LTR | 3 | new locale, wave #209 |
| South Azerbaijani | `azb-IR` | 15 | Arab | RTL | 5 | new locale, wave #221 |
| Kanuri | `kau-NG` | 10 | Latn | LTR | 4 | new locale, wave #233 |
| Makhuwa | `vmw-MZ` | 9 | Latn | LTR | 4 | new locale, wave #245 |
| Kongo | `kon-CD` | 7 | Latn | LTR | 4 | new locale, wave #257 |
| Luhya | `luy-KE` | 7 | Latn | LTR | 4 | new locale, wave #269 |
| Tsonga | `tso-ZA` | 7 | Latn | LTR | 4 | new locale, wave #281 |
| Umbundu | `umb-AO` | 7 | Latn | LTR | 4 | new locale, wave #293 |
| Dholuo | `luo-KE` | 5 | Latn | LTR | 3 | new locale, wave #305 |
| Sango | `sag-CF` | 5 | Latn | LTR | 3 | new locale, wave #317 |
| Guarani | `grn-PY` | 7 | Latn | LTR | 4 | new locale, wave #329 |
| Balochi | `bal-PK` | 8 | Arab | RTL | 5 | new locale, wave #341 |
| Kalenjin | `kln-KE` | 6 | Latn | LTR | 4 | new locale, wave #353 |
| Luba-Kasai | `lua-CD` | 6 | Latn | LTR | 4 | new locale, wave #365 |
| Sukuma | `suk-TZ` | 5 | Latn | LTR | 4 | new locale, wave #377 |

### The queue: the first 60 waves

| Phase | Waves |
|---|---:|
| 1. Catalogues of the priority group | 1 |
| 2. German: daily-use help | 4 |
| 3. Daily-use help of the priority group | 119 |
| 4. New translations | 264 |
| 5. Extensions of the other languages | 0 |
| 6. Daily-use help of the other languages | 2,465 |
| 7. Rest of the help of the priority group | 1,362 |
| 8. Rest of the help of the other languages | 28,130 |

| # | Wave | Language | Work set | Size (left) | Difficulty | Prerequisites |
|---:|---|---|---|---|:-:|---|
| 1 | `ger-DE/E` | German | Shipped extensions | 137 of 567 strings | 2 | `GUIDE.md` |
| 2 | `ger-DE/H-guides-05` | German | Help `H-guides-05` | 12 pages, 23,605 words | 2 | `GUIDE.md`, waves `S` and administration |
| 3 | `ger-DE/H-guides-06` | German | Help `H-guides-06` | 13 pages, 10,135 words | 2 | `GUIDE.md`, waves `S` and administration |
| 4 | `ger-DE/H-guides-07` | German | Help `H-guides-07` | 12 pages, 6,185 words | 2 | `GUIDE.md`, waves `S` and administration |
| 5 | `ger-DE/H-guides-08` | German | Help `H-guides-08` | 13 pages, 1,879 words | 2 | `GUIDE.md`, waves `S` and administration |
| 6 | `ara-SA/S` | Arabic | Set up the help | small | 4 | `GUIDE.md`, RTL, PDF font Arab |
| 7 | `tur-TR/S` | Turkish | Set up the help | small | 2 | `GUIDE.md` |
| 8 | `ukr-UA/S` | Ukrainian | Set up the help | small | 3 | `GUIDE.md` |
| 9 | `rus-RU/S` | Russian | Set up the help | small | 3 | `GUIDE.md` |
| 10 | `fas-IR/S` | Persian | Set up the help | small | 4 | `GUIDE.md`, RTL, PDF font Arab |
| 11 | `pus-AF/S` | Pashto | Set up the help | small | 5 | `GUIDE.md`, RTL, PDF font Arab |
| 12 | `kur-TR/S` | Kurdish (Kurmanji) | Set up the help | small | 3 | `GUIDE.md` |
| 13 | `ara-SA/H-help-01` | Arabic | Help `H-help-01` | 13 pages, 32,546 words | 4 | `GUIDE.md`, RTL, PDF font Arab, waves `S` and administration |
| 14 | `tur-TR/H-help-01` | Turkish | Help `H-help-01` | 13 pages, 32,546 words | 2 | `GUIDE.md`, waves `S` and administration |
| 15 | `ukr-UA/H-help-01` | Ukrainian | Help `H-help-01` | 13 pages, 32,546 words | 3 | `GUIDE.md`, waves `S` and administration |
| 16 | `rus-RU/H-help-01` | Russian | Help `H-help-01` | 13 pages, 32,546 words | 3 | `GUIDE.md`, waves `S` and administration |
| 17 | `fas-IR/H-help-01` | Persian | Help `H-help-01` | 13 pages, 32,546 words | 4 | `GUIDE.md`, RTL, PDF font Arab, waves `S` and administration |
| 18 | `pus-AF/H-help-01` | Pashto | Help `H-help-01` | 13 pages, 32,546 words | 5 | `GUIDE.md`, RTL, PDF font Arab, waves `S` and administration |
| 19 | `kur-TR/H-help-01` | Kurdish (Kurmanji) | Help `H-help-01` | 13 pages, 32,546 words | 3 | `GUIDE.md`, waves `S` and administration |
| 20 | `ara-SA/H-help-02` | Arabic | Help `H-help-02` | 13 pages, 16,112 words | 4 | `GUIDE.md`, RTL, PDF font Arab, waves `S` and administration |
| 21 | `tur-TR/H-help-02` | Turkish | Help `H-help-02` | 13 pages, 16,112 words | 2 | `GUIDE.md`, waves `S` and administration |
| 22 | `ukr-UA/H-help-02` | Ukrainian | Help `H-help-02` | 13 pages, 16,112 words | 3 | `GUIDE.md`, waves `S` and administration |
| 23 | `rus-RU/H-help-02` | Russian | Help `H-help-02` | 13 pages, 16,112 words | 3 | `GUIDE.md`, waves `S` and administration |
| 24 | `fas-IR/H-help-02` | Persian | Help `H-help-02` | 13 pages, 16,112 words | 4 | `GUIDE.md`, RTL, PDF font Arab, waves `S` and administration |
| 25 | `pus-AF/H-help-02` | Pashto | Help `H-help-02` | 13 pages, 16,112 words | 5 | `GUIDE.md`, RTL, PDF font Arab, waves `S` and administration |
| 26 | `kur-TR/H-help-02` | Kurdish (Kurmanji) | Help `H-help-02` | 13 pages, 16,112 words | 3 | `GUIDE.md`, waves `S` and administration |
| 27 | `ara-SA/H-help-03` | Arabic | Help `H-help-03` | 13 pages, 34,697 words | 4 | `GUIDE.md`, RTL, PDF font Arab, waves `S` and administration |
| 28 | `tur-TR/H-help-03` | Turkish | Help `H-help-03` | 13 pages, 34,697 words | 2 | `GUIDE.md`, waves `S` and administration |
| 29 | `ukr-UA/H-help-03` | Ukrainian | Help `H-help-03` | 13 pages, 34,697 words | 3 | `GUIDE.md`, waves `S` and administration |
| 30 | `rus-RU/H-help-03` | Russian | Help `H-help-03` | 13 pages, 34,697 words | 3 | `GUIDE.md`, waves `S` and administration |
| 31 | `fas-IR/H-help-03` | Persian | Help `H-help-03` | 13 pages, 34,697 words | 4 | `GUIDE.md`, RTL, PDF font Arab, waves `S` and administration |
| 32 | `pus-AF/H-help-03` | Pashto | Help `H-help-03` | 13 pages, 34,697 words | 5 | `GUIDE.md`, RTL, PDF font Arab, waves `S` and administration |
| 33 | `kur-TR/H-help-03` | Kurdish (Kurmanji) | Help `H-help-03` | 13 pages, 34,697 words | 3 | `GUIDE.md`, waves `S` and administration |
| 34 | `ara-SA/H-help-manual-01` | Arabic | Help `H-help-manual-01` | 44 pages, 17,659 words | 4 | `GUIDE.md`, RTL, PDF font Arab, waves `S` and administration |
| 35 | `tur-TR/H-help-manual-01` | Turkish | Help `H-help-manual-01` | 44 pages, 17,659 words | 2 | `GUIDE.md`, waves `S` and administration |
| 36 | `ukr-UA/H-help-manual-01` | Ukrainian | Help `H-help-manual-01` | 44 pages, 17,659 words | 3 | `GUIDE.md`, waves `S` and administration |
| 37 | `rus-RU/H-help-manual-01` | Russian | Help `H-help-manual-01` | 44 pages, 17,659 words | 3 | `GUIDE.md`, waves `S` and administration |
| 38 | `fas-IR/H-help-manual-01` | Persian | Help `H-help-manual-01` | 44 pages, 17,659 words | 4 | `GUIDE.md`, RTL, PDF font Arab, waves `S` and administration |
| 39 | `pus-AF/H-help-manual-01` | Pashto | Help `H-help-manual-01` | 44 pages, 17,659 words | 5 | `GUIDE.md`, RTL, PDF font Arab, waves `S` and administration |
| 40 | `kur-TR/H-help-manual-01` | Kurdish (Kurmanji) | Help `H-help-manual-01` | 44 pages, 17,659 words | 3 | `GUIDE.md`, waves `S` and administration |
| 41 | `ara-SA/H-help-manual-02` | Arabic | Help `H-help-manual-02` | 44 pages, 18,751 words | 4 | `GUIDE.md`, RTL, PDF font Arab, waves `S` and administration |
| 42 | `tur-TR/H-help-manual-02` | Turkish | Help `H-help-manual-02` | 44 pages, 18,751 words | 2 | `GUIDE.md`, waves `S` and administration |
| 43 | `ukr-UA/H-help-manual-02` | Ukrainian | Help `H-help-manual-02` | 44 pages, 18,751 words | 3 | `GUIDE.md`, waves `S` and administration |
| 44 | `rus-RU/H-help-manual-02` | Russian | Help `H-help-manual-02` | 44 pages, 18,751 words | 3 | `GUIDE.md`, waves `S` and administration |
| 45 | `fas-IR/H-help-manual-02` | Persian | Help `H-help-manual-02` | 44 pages, 18,751 words | 4 | `GUIDE.md`, RTL, PDF font Arab, waves `S` and administration |
| 46 | `pus-AF/H-help-manual-02` | Pashto | Help `H-help-manual-02` | 44 pages, 18,751 words | 5 | `GUIDE.md`, RTL, PDF font Arab, waves `S` and administration |
| 47 | `kur-TR/H-help-manual-02` | Kurdish (Kurmanji) | Help `H-help-manual-02` | 44 pages, 18,751 words | 3 | `GUIDE.md`, waves `S` and administration |
| 48 | `ara-SA/H-help-non-technical-people` | Arabic | Help `H-help-non-technical-people` | 47 pages, 28,042 words | 4 | `GUIDE.md`, RTL, PDF font Arab, waves `S` and administration |
| 49 | `tur-TR/H-help-non-technical-people` | Turkish | Help `H-help-non-technical-people` | 47 pages, 28,042 words | 2 | `GUIDE.md`, waves `S` and administration |
| 50 | `ukr-UA/H-help-non-technical-people` | Ukrainian | Help `H-help-non-technical-people` | 47 pages, 28,042 words | 3 | `GUIDE.md`, waves `S` and administration |
| 51 | `rus-RU/H-help-non-technical-people` | Russian | Help `H-help-non-technical-people` | 47 pages, 28,042 words | 3 | `GUIDE.md`, waves `S` and administration |
| 52 | `fas-IR/H-help-non-technical-people` | Persian | Help `H-help-non-technical-people` | 47 pages, 28,042 words | 4 | `GUIDE.md`, RTL, PDF font Arab, waves `S` and administration |
| 53 | `pus-AF/H-help-non-technical-people` | Pashto | Help `H-help-non-technical-people` | 47 pages, 28,042 words | 5 | `GUIDE.md`, RTL, PDF font Arab, waves `S` and administration |
| 54 | `kur-TR/H-help-non-technical-people` | Kurdish (Kurmanji) | Help `H-help-non-technical-people` | 47 pages, 28,042 words | 3 | `GUIDE.md`, waves `S` and administration |
| 55 | `ara-SA/H-guides-01` | Arabic | Help `H-guides-01` | 12 pages, 35,377 words | 4 | `GUIDE.md`, RTL, PDF font Arab, waves `S` and administration |
| 56 | `tur-TR/H-guides-01` | Turkish | Help `H-guides-01` | 12 pages, 35,377 words | 2 | `GUIDE.md`, waves `S` and administration |
| 57 | `ukr-UA/H-guides-01` | Ukrainian | Help `H-guides-01` | 12 pages, 35,377 words | 3 | `GUIDE.md`, waves `S` and administration |
| 58 | `rus-RU/H-guides-01` | Russian | Help `H-guides-01` | 12 pages, 35,377 words | 3 | `GUIDE.md`, waves `S` and administration |
| 59 | `fas-IR/H-guides-01` | Persian | Help `H-guides-01` | 12 pages, 35,377 words | 4 | `GUIDE.md`, RTL, PDF font Arab, waves `S` and administration |
| 60 | `pus-AF/H-guides-01` | Pashto | Help `H-guides-01` | 12 pages, 35,377 words | 5 | `GUIDE.md`, RTL, PDF font Arab, waves `S` and administration |

The whole queue and the pages of a wave: `./console exp:i18n waves --list`, `./console exp:i18n waves --wave=<wave>`.

Made by `./console exp:i18n waves`; `./console exp:i18n waves --check` and the test `expI18nTranslationWavesTest` fail when a percentage changed or a size is more than 3 % off.
<!-- exp:i18n waves data {"status":{"afr-ZA":[100,100,0,0],"aka-GH":[100,100,0,0],"amh-ET":[100,100,0,0],"ara-SA":[100,100,0,0],"arc-IQ":[100,100,0,0],"asm-IN":[100,100,0,0],"ast-ES":[100,100,0,0],"aze-AZ":[100,100,0,0],"bam-ML":[100,100,0,0],"bel-BY":[100,100,0,0],"ben-BD":[100,100,0,0],"bho-IN":[100,100,0,0],"bod-CN":[100,100,0,0],"bos-BA":[100,100,0,0],"bre-FR":[100,100,0,0],"brx-IN":[100,100,0,0],"bul-BG":[100,100,0,0],"cat-ES":[100,100,0,0],"ceb-PH":[100,100,0,0],"che-RU":[100,100,0,0],"chi-CN":[100,100,0,0],"chi-TW":[100,100,0,0],"ckb-IQ":[100,100,0,0],"cro-HR":[100,100,0,0],"cym-GB":[100,100,0,0],"cze-CZ":[100,100,0,0],"dan-DK":[100,100,0,0],"div-MV":[100,100,0,0],"doi-IN":[100,100,0,0],"dsb-DE":[100,100,0,0],"dut-NL":[100,100,0,0],"dzo-BT":[100,100,0,0],"ell-GR":[100,100,0,0],"eng-AU":[100,100,100,100],"eng-US":[100,100,100,100],"epo-EO":[100,100,0,0],"esl-ES":[100,100,0,0],"esl-MX":[100,100,0,0],"est-EE":[100,100,0,0],"eus-ES":[100,100,0,0],"ewe-GH":[100,100,0,0],"fao-FO":[100,100,0,0],"fas-AF":[100,100,0,0],"fas-IR":[100,100,0,0],"fil-PH":[100,100,0,0],"fin-FI":[100,100,0,0],"fre-FR":[100,100,0,0],"fry-NL":[100,100,0,0],"ful-SN":[100,100,0,0],"ger-DE":[100,76,89,99],"gla-GB":[100,100,0,0],"gle-IE":[100,100,0,0],"glg-ES":[100,100,0,0],"guj-IN":[100,100,0,0],"hau-NG":[100,100,0,0],"haw-US":[100,100,0,0],"heb-IL":[100,100,0,0],"hin-IN":[100,100,0,0],"hsb-DE":[100,100,0,0],"hun-HU":[100,100,0,0],"huy-IL":[100,100,0,0],"hye-AM":[100,100,0,0],"ibo-NG":[100,100,0,0],"ind-ID":[100,100,0,0],"isl-IS":[100,100,0,0],"ita-IT":[100,100,0,0],"jav-ID":[100,100,0,0],"jpn-JP":[100,100,0,0],"kab-DZ":[100,100,0,0],"kal-GL":[100,100,0,0],"kan-IN":[100,100,0,0],"kas-IN":[100,100,0,0],"kat-GE":[100,100,0,0],"kaz-KZ":[100,100,0,0],"khm-KH":[100,100,0,0],"kik-KE":[100,100,0,0],"kin-RW":[100,100,0,0],"kir-KG":[100,100,0,0],"kok-IN":[100,100,0,0],"kor-KP":[100,100,0,0],"kor-KR":[100,100,0,0],"kur-TR":[100,100,0,0],"lao-LA":[100,100,0,0],"lat-VA":[100,100,0,0],"lav-LV":[100,100,0,0],"lin-CD":[100,100,0,0],"lit-LT":[100,100,0,0],"ltz-LU":[100,100,0,0],"lug-UG":[100,100,0,0],"mai-IN":[100,100,0,0],"mal-IN":[100,100,0,0],"mar-IN":[100,100,0,0],"mkd-MK":[100,100,0,0],"mlg-MG":[100,100,0,0],"mlt-MT":[100,100,0,0],"mni-IN":[100,100,0,0],"mon-MN":[100,100,0,0],"mri-NZ":[100,100,0,0],"msa-MY":[100,100,0,0],"mya-MM":[100,100,0,0],"nep-NP":[100,100,0,0],"nor-NO":[100,100,0,0],"nqo-GN":[100,100,0,0],"oci-FR":[100,100,0,0],"ori-IN":[100,100,0,0],"orm-ET":[100,100,0,0],"pan-IN":[100,100,0,0],"pcm-NG":[100,100,0,0],"pnb-PK":[100,100,0,0],"pol-PL":[100,100,0,0],"por-BR":[100,100,0,0],"por-PT":[100,100,0,0],"pus-AF":[100,100,0,0],"que-PE":[100,100,0,0],"roh-CH":[100,100,0,0],"ron-RO":[100,100,0,0],"run-BI":[100,100,0,0],"rus-RU":[100,100,0,0],"san-IN":[100,100,0,0],"sat-IN":[100,100,0,0],"ser-SR":[100,100,0,0],"sin-LK":[100,100,0,0],"slk-SK":[100,100,0,0],"slv-SI":[100,100,0,0],"sme-NO":[100,100,0,0],"sna-ZW":[100,100,0,0],"snd-PK":[100,100,0,0],"som-SO":[100,100,0,0],"sqi-AL":[100,100,0,0],"srp-RS":[100,100,0,0],"sun-ID":[100,100,0,0],"swa-KE":[100,100,0,0],"swe-SE":[100,100,0,0],"syr-IQ":[100,100,0,0],"tam-IN":[100,100,0,0],"tat-RU":[100,100,0,0],"tel-IN":[100,100,0,0],"tgk-TJ":[100,100,0,0],"tha-TH":[100,100,0,0],"tir-ET":[100,100,0,0],"tlh-QO":[100,100,0,0],"ton-TO":[100,100,0,0],"tuk-TM":[100,100,0,0],"tur-TR":[100,100,0,0],"uig-CN":[100,100,0,0],"ukr-UA":[100,100,0,0],"urd-PK":[100,100,0,0],"uzb-UZ":[100,100,0,0],"vie-VN":[100,100,0,0],"wol-SN":[100,100,0,0],"xho-ZA":[100,100,0,0],"yid-US":[100,100,0,0],"yor-NG":[100,100,0,0],"zgh-MA":[100,100,0,0],"zul-ZA":[100,100,0,0]},"sizes":{"messages":19628,"extensions":567,"parts":210,"daily_pages":314,"daily_words":406374,"guides_pages":3735,"guides_words":2976612,"reference_pages":1804,"reference_words":1851757,"tables_pages":0,"tables_words":103791},"waves":32345,"untranslated":["hat-HT","skr-PK","tsn-ZA","dyu-CI","nya-MW","nso-ZA","sot-ZA","mos-BF","azb-IR","kau-NG","vmw-MZ","kon-CD","luy-KE","tso-ZA","umb-AO","luo-KE","sag-CF","grn-PY","bal-PK","kln-KE","lua-CD","suk-TZ"]} -->
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
