---
summary: Ein gemessener Plan in kleinen, voneinander unabhängigen Wellen, der jede Sprache von Exponential in den täglichen Gebrauch bringt, Administration, Erweiterungen und Hilfe, damit niemand für die Arbeit mit dem Produkt Englisch braucht; zuerst die Prioritätsgruppe der Herkunftssprachen geflüchteter Kinder in Hamburg.
---
# Übersetzungswellen: jede Sprache in den täglichen Gebrauch

Das Ziel ist, dass jeder Mensch jeden Tag in seiner eigenen Sprache mit Exponential arbeiten kann, ohne je auf
Englisch auszuweichen: in der Administration, in den Erweiterungen, die er benutzt, und in der Hilfe. Diese Seite misst,
wo jede Sprache steht, und teilt die verbleibende Arbeit in **Wellen**. Eine Welle ist eine Sprache und ein
Arbeitspaket, so klein, dass ein Übersetzer (ein Mensch oder ein Agent) sie in einem Zug beginnen und abschließen kann,
jederzeit und unabhängig von jeder anderen Welle.

Die Zahlen und die Warteschlange unten werden aus den Dateien dieser Version erzeugt; der Rest der Seite erklärt, wie
eine Welle gemacht und geprüft wird. Die Sprachen, ihre Locales und ihre Übersetzungen der Administration beschreibt
[Sprachen in Exponential](sprachen.md).

## Inhalt

1. [Der Plan, gemessen](#der-plan-gemessen): [wo jede Sprache steht](#wo-jede-sprache-steht),
   [die Hilfe in Stufen](#die-hilfe-in-stufen), [Sprachen ohne Übersetzung](#sprachen-ohne-übersetzung),
   [die Warteschlange](#die-warteschlange-die-ersten-60-wellen)
2. [Was die Wellen sind](#was-die-wellen-sind)
3. [Die Reihenfolge der Wellen](#die-reihenfolge-der-wellen)
4. [Vor einer Welle: die Voraussetzungen](#vor-einer-welle-die-voraussetzungen)
5. [Wie jede Art von Welle gemacht wird](#wie-jede-art-von-welle-gemacht-wird)
6. [Die Prüfungen nach jeder Welle](#die-prüfungen-nach-jeder-welle)
7. [Fallen](#fallen)
8. [Fertig: täglicher Gebrauch und Ziel](#fertig-täglicher-gebrauch-und-ziel)
9. [Sprecher und Schwierigkeit](#sprecher-und-schwierigkeit)
10. [Diese Seite aktuell halten](#diese-seite-aktuell-halten)

## Der Plan, gemessen

<!-- generated: exp:i18n waves 2026-10-29 (start) -->
**Gemessen am 29. Oktober 2026** mit `./console exp:i18n waves` aus den Dateien dieser Version (ohne Datenbank):

| Was | Umfang |
|---|---:|
| Administration: Texte in `share/translations/untranslated/translation.ts`, in 10 Teilen | **19.101** (129.281 Wörter) |
| Mitgelieferte Erweiterungen (`expimageeditor`, `ezjscore`, `ezoe`) | **567** Texte |
| Hilfe: Seiten unter `doc/eng-us/` (ohne die erzeugten Beispielseiten) | **5.738** Seiten, 4.982.988 Wörter |
| Hilfe: Tabellen der erzeugten Beispielseiten (`.examples/`) | **2.705** Einträge, 80.642 Wörter |
| Übersetzungen der Administration, davon vollständig | **155**, 3 |
| davon im täglichen Gebrauch (Administration, Erweiterungen und Hilfe des Alltags ≥ 97 %) | **2** |
| Sprachen ohne Übersetzung der Administration ([Liste](#sprachen-ohne-übersetzung)), davon auf einem Branch in Arbeit | **22**, 0 |
| Wellen bis 100 % in jeder Sprache | **31.204** |

### Wo jede Sprache steht

Prozent der Texte (Administration, Erweiterungen) und der Wörter (Hilfe), abgerundet. **Fett**: unter 97 %. „Hilfe des Alltags“ sind die Teile für Redakteure, Benutzer und Administratoren ([Stufen](#die-hilfe-in-stufen)).

| Sprache | Code | Sprecher (Mio.) | Schwierigkeit | Administration | Erweiterungen | Hilfe des Alltags | Hilfe gesamt | Wellen offen | Nächste Welle |
|---|---|---:|:-:|---:|---:|---:|---:|---:|---|
| Afrikaans | `afr-ZA` | 17 | 2 | 98 | 100 | **0** | **0** | 203 | #536 `A02` |
| Akan | `aka-GH` | 11 | 3 | 98 | **80** | **0** | **0** | 204 | #627 `A02` |
| Albanisch | `sqi-AL` | 8 | 2 | 98 | **80** | **0** | **0** | 204 | #623 `A02` |
| Amharisch | `amh-ET` | 60 | 4 | 98 | 100 | **0** | **0** | 203 | #479 `A02` |
| Arabisch | `ara-SA` | 400 | 4 | 98 | **80** | **0** | **0** | 204 | #2 `A02` |
| Aramäisch | `arc-IQ` | < 1 | 5 | 98 | **80** | **0** | **0** | 204 | #821 `A02` |
| Armenisch | `hye-AM` | 5 | 4 | 98 | **80** | **0** | **0** | 204 | #708 `A02` |
| Aserbaidschanisch | `aze-AZ` | 10 | 3 | 98 | **80** | **0** | **0** | 204 | #635 `A02` |
| Assamesisch | `asm-IN` | 15 | 5 | 98 | **80** | **0** | **0** | 204 | #642 `A02` |
| Asturisch | `ast-ES` | < 1 | 3 | 98 | 100 | **0** | **0** | 203 | #814 `A02` |
| Bambara | `bam-ML` | 14 | 3 | 98 | 100 | **0** | **0** | 203 | #593 `A02` |
| Baskisch | `eus-ES` | < 1 | 3 | 98 | **80** | **0** | **0** | 204 | #790 `A02` |
| Belarussisch | `bel-BY` | 5 | 4 | 98 | **80** | **0** | **0** | 204 | #712 `A02` |
| Bengalisch | `ben-BD` | 285 | 4 | 98 | 100 | **0** | **0** | 203 | #399 `A02` |
| Bhojpuri | `bho-IN` | 52 | 5 | 98 | **80** | **0** | **0** | 204 | #504 `A02` |
| Birmanisch | `mya-MM` | 43 | 5 | 98 | **80** | **0** | **0** | 204 | #532 `A02` |
| Bodo | `brx-IN` | 2 | 5 | 98 | 100 | **0** | **0** | 203 | #783 `A02` |
| Bosnisch | `bos-BA` | 3 | 3 | 98 | **80** | **0** | **0** | 204 | #731 `A02` |
| Bretonisch | `bre-FR` | < 1 | 4 | 98 | 100 | **0** | **0** | 203 | #839 `A02` |
| Bulgarisch | `bul-BG` | 8 | 2 | 98 | 100 | **0** | **0** | 203 | #608 `A02` |
| Cebuano | `ceb-PH` | 28 | 3 | 98 | **80** | **0** | **0** | 204 | #518 `A02` |
| Chinesisch (traditionell) | `chi-TW` | 31 | 2 | 98 | 100 | **0** | **0** | 203 | #466 `A02` |
| Chinesisch (vereinfacht) | `chi-CN` | 1.100 | 2 | 98 | 100 | **0** | **0** | 203 | #368 `A02` |
| Dänisch | `dan-DK` | 6 | 1 | 98 | **80** | **0** | **0** | 204 | #574 `A02` |
| Dari (Persisch in Afghanistan) | `fas-AF` | 35 | 4 | 98 | 100 | **0** | **0** | 203 | #526 `A02` |
| Deutsch | `ger-DE` | 135 | 2 | 100 | **76** | 100 | 99 | 13 | #1 `E` |
| Dhivehi | `div-MV` | < 1 | 5 | 98 | 100 | **0** | **0** | 203 | #829 `A02` |
| Dogri | `doi-IN` | 3 | 5 | 98 | 100 | **0** | **0** | 203 | #762 `A02` |
| Dzongkha | `dzo-BT` | < 1 | 5 | 98 | 100 | **0** | **0** | 203 | #811 `A02` |
| Englisch (Australien) | `eng-AU` | 1.500 | 1 | 100 | 100 | 100 | 100 | 0 | Quelltexte |
| Englisch (USA) | `eng-US` | 1.500 | 1 | 100 | 100 | 100 | 100 | 0 | Quelltexte |
| Esperanto | `epo-EO` | < 1 | 2 | 98 | 100 | **0** | **0** | 203 | #842 `A02` |
| Estnisch | `est-EE` | 1 | 3 | 98 | **80** | **0** | **0** | 204 | #772 `A02` |
| Ewe | `ewe-GH` | 5 | 3 | 98 | 100 | **0** | **0** | 203 | #695 `A02` |
| Färöisch | `fao-FO` | < 1 | 4 | 98 | 100 | **0** | **0** | 203 | #852 `A02` |
| Filipino | `fil-PH` | 83 | 2 | 98 | **80** | **0** | **0** | 204 | #416 `A02` |
| Finnisch | `fin-FI` | 6 | 2 | 98 | **80** | **0** | **0** | 204 | #650 `A02` |
| Französisch | `fre-FR` | 310 | 1 | 98 | **80** | **0** | **0** | 204 | #375 `A02` |
| Fulfulde | `ful-SN` | 37 | 4 | 98 | **80** | **0** | **0** | 204 | #522 `A02` |
| Galicisch | `glg-ES` | 2 | 2 | 98 | **80** | **0** | **0** | 204 | #723 `A02` |
| Georgisch | `kat-GE` | 4 | 4 | 98 | **80** | **0** | **0** | 204 | #746 `A02` |
| Griechisch | `ell-GR` | 13 | 3 | 98 | **80** | **0** | **0** | 204 | #604 `A02` |
| Grönländisch | `kal-GL` | < 1 | 4 | 98 | **80** | **0** | **0** | 204 | #855 `A02` |
| Gujarati | `guj-IN` | 62 | 4 | 98 | 100 | **0** | **0** | 203 | #469 `A02` |
| Hausa | `hau-NG` | 94 | 3 | 98 | 100 | **0** | **0** | 203 | #427 `A02` |
| Hawaiisch | `haw-US` | < 1 | 3 | 98 | **80** | **0** | **0** | 204 | #862 `A02` |
| Hebräisch | `heb-IL` | 9 | 4 | 98 | **80** | **0** | **0** | 204 | #668 `A02` |
| Hindi | `hin-IN` | 610 | 4 | 98 | **80** | **0** | **0** | 204 | #387 `A02` |
| Hulaulá (jüdisches Neuaramäisch) | `huy-IL` | < 1 | 5 | 98 | **80** | **0** | **0** | 204 | #876 `A02` |
| Igbo | `ibo-NG` | 31 | 3 | 98 | 100 | **0** | **0** | 203 | #508 `A02` |
| Indonesisch | `ind-ID` | 252 | 1 | 98 | **80** | **0** | **0** | 204 | #379 `A02` |
| Irisch | `gle-IE` | 2 | 4 | 98 | **80** | **0** | **0** | 204 | #765 `A02` |
| isiXhosa | `xho-ZA` | 19 | 4 | 98 | 100 | **0** | **0** | 203 | #590 `A02` |
| isiZulu | `zul-ZA` | 28 | 4 | 98 | 100 | **0** | **0** | 203 | #559 `A02` |
| Isländisch | `isl-IS` | < 1 | 3 | 98 | **80** | **0** | **0** | 204 | #817 `A02` |
| Italienisch | `ita-IT` | 68 | 1 | 98 | **80** | **0** | **0** | 204 | #402 `A02` |
| Japanisch | `jpn-JP` | 123 | 3 | 98 | 100 | **0** | **0** | 203 | #420 `A02` |
| Javanisch | `jav-ID` | 68 | 3 | 98 | **80** | **0** | **0** | 204 | #448 `A02` |
| Jiddisch | `yid-US` | 1 | 5 | 98 | **80** | **0** | **0** | 204 | #797 `A02` |
| Kabylisch | `kab-DZ` | 5 | 4 | 98 | 100 | **0** | **0** | 203 | #716 `A02` |
| Kannada | `kan-IN` | 59 | 5 | 98 | 100 | **0** | **0** | 203 | #489 `A02` |
| Kasachisch | `kaz-KZ` | 16 | 4 | 98 | **80** | **0** | **0** | 204 | #611 `A02` |
| Kaschmiri | `kas-IN` | 7 | 5 | 98 | 100 | **0** | **0** | 203 | #705 `A02` |
| Katalanisch | `cat-ES` | 10 | 1 | 98 | 100 | **0** | **0** | 203 | #511 `A02` |
| Khmer | `khm-KH` | 18 | 4 | 98 | **80** | **0** | **0** | 204 | #596 `A02` |
| Kikuyu | `kik-KE` | 8 | 4 | 98 | **80** | **0** | **0** | 204 | #680 `A02` |
| Kinyarwanda | `kin-RW` | 15 | 4 | 98 | **80** | **0** | **0** | 204 | #615 `A02` |
| Kirgisisch | `kir-KG` | 5 | 5 | 98 | **80** | **0** | **0** | 204 | #735 `A02` |
| Kirundi | `run-BI` | 12 | 4 | 98 | **80** | **0** | **0** | 204 | #646 `A02` |
| Klingonisch | `tlh-QO` | < 1 | 4 | 98 | **80** | **0** | **0** | 204 | #887 `A02` |
| Konkani | `kok-IN` | 2 | 5 | 98 | 100 | **0** | **0** | 203 | #769 `A02` |
| Koreanisch | `kor-KR` | 82 | 3 | 98 | 100 | **0** | **0** | 203 | #430 `A02` |
| Koreanisch (Nordkorea, Munhwaŏ) | `kor-KP` | 26 | 3 | 98 | 100 | **0** | **0** | 203 | #529 `A02` |
| Kroatisch | `cro-HR` | 5 | 2 | 98 | **80** | **0** | **0** | 204 | #661 `A02` |
| Kurdisch (Kurmandschi) | `kur-TR` | 16 | 3 | 98 | **80** | **0** | **0** | 204 | #24 `A02` |
| Laotisch | `lao-LA` | 30 | 5 | 98 | **80** | **0** | **0** | 204 | #578 `A02` |
| Latein | `lat-VA` | < 1 | 4 | 98 | **80** | **0** | **0** | 204 | #880 `A02` |
| Lettisch | `lav-LV` | 2 | 3 | 98 | **80** | **0** | **0** | 204 | #758 `A02` |
| Lingala | `lin-CD` | 40 | 4 | 98 | **80** | **0** | **0** | 204 | #514 `A02` |
| Litauisch | `lit-LT` | 3 | 3 | 98 | 100 | **0** | **0** | 203 | #739 `A02` |
| Luganda | `lug-UG` | 11 | 4 | 98 | **80** | **0** | **0** | 204 | #657 `A02` |
| Luxemburgisch | `ltz-LU` | < 1 | 2 | 98 | 100 | **0** | **0** | 203 | #794 `A02` |
| Maithili | `mai-IN` | 34 | 5 | 98 | **80** | **0** | **0** | 204 | #562 `A02` |
| Malagasy | `mlg-MG` | 25 | 3 | 98 | **80** | **0** | **0** | 204 | #539 `A02` |
| Malaiisch | `msa-MY` | 60 | 1 | 98 | **80** | **0** | **0** | 204 | #406 `A02` |
| Malayalam | `mal-IN` | 37 | 5 | 98 | 100 | **0** | **0** | 203 | #553 `A02` |
| Maltesisch | `mlt-MT` | < 1 | 3 | 98 | **80** | **0** | **0** | 204 | #801 `A02` |
| Maori | `mri-NZ` | < 1 | 3 | 98 | **80** | **0** | **0** | 204 | #832 `A02` |
| Marathi | `mar-IN` | 99 | 4 | 98 | 100 | **0** | **0** | 203 | #445 `A02` |
| Marokkanisches Standard-Tamazight | `zgh-MA` | 14 | 5 | 98 | 100 | **0** | **0** | 203 | #654 `A02` |
| Mazedonisch | `mkd-MK` | 2 | 3 | 98 | **80** | **0** | **0** | 204 | #754 `A02` |
| Meitei | `mni-IN` | 2 | 5 | 98 | 100 | **0** | **0** | 203 | #780 `A02` |
| Mongolisch | `mon-MN` | 6 | 5 | 98 | **80** | **0** | **0** | 204 | #727 `A02` |
| Nepali | `nep-NP` | 32 | 4 | 98 | **80** | **0** | **0** | 204 | #543 `A02` |
| Niederländisch | `dut-NL` | 25 | 1 | 98 | **80** | **0** | **0** | 204 | #437 `A02` |
| Niedersorbisch | `dsb-DE` | < 1 | 4 | 98 | **80** | **0** | **0** | 204 | #872 `A02` |
| Nigerianisches Pidgin | `pcm-NG` | 120 | 3 | 98 | **80** | **0** | **0** | 204 | #423 `A02` |
| Nordsamisch | `sme-NO` | < 1 | 4 | 98 | 100 | **0** | **0** | 203 | #859 `A02` |
| Norwegisch | `nor-NO` | 5 | 1 | 98 | **80** | **0** | **0** | 204 | #586 `A02` |
| N’Ko | `nqo-GN` | – | 5 | 98 | 100 | **0** | **0** | 203 | #884 `A02` |
| Obersorbisch | `hsb-DE` | < 1 | 4 | 98 | 100 | **0** | **0** | 203 | #869 `A02` |
| Okzitanisch | `oci-FR` | < 1 | 3 | 98 | 100 | **0** | **0** | 203 | #808 `A02` |
| Oriya | `ori-IN` | 40 | 5 | 98 | 100 | **0** | **0** | 203 | #547 `A02` |
| Oromo | `orm-ET` | 45 | 4 | 98 | **80** | **0** | **0** | 204 | #496 `A02` |
| Panjabi | `pan-IN` | 48 | 4 | 98 | 100 | **0** | **0** | 203 | #486 `A02` |
| Paschtu | `pus-AF` | 60 | 5 | 98 | **80** | **0** | **0** | 204 | #20 `A02` |
| Persisch | `fas-IR` | 83 | 4 | 98 | **80** | **0** | **0** | 204 | #16 `A02` |
| Polnisch | `pol-PL` | 40 | 2 | 98 | **80** | **0** | **0** | 204 | #452 `A02` |
| Portugiesisch (Brasilien) | `por-BR` | 215 | 1 | 98 | **80** | **0** | **0** | 204 | #383 `A02` |
| Portugiesisch (Portugal) | `por-PT` | 50 | 1 | 98 | 100 | **0** | **0** | 203 | #413 `A02` |
| Quechua | `que-PE` | 8 | 4 | 98 | 100 | **0** | **0** | 203 | #684 `A02` |
| Rätoromanisch | `roh-CH` | < 1 | 3 | 98 | 100 | **0** | **0** | 203 | #849 `A02` |
| Rumänisch | `ron-RO` | 25 | 1 | 98 | **80** | **0** | **0** | 204 | #441 `A02` |
| Russisch | `rus-RU` | 255 | 3 | 98 | 100 | **0** | **0** | 203 | #13 `A02` |
| Sanskrit | `san-IN` | < 1 | 5 | 98 | 100 | **0** | **0** | 203 | #866 `A02` |
| Santali | `sat-IN` | 8 | 5 | 98 | 100 | **0** | **0** | 203 | #698 `A02` |
| Schottisch-Gälisch | `gla-GB` | < 1 | 4 | 98 | **80** | **0** | **0** | 204 | #845 `A02` |
| Schwedisch | `swe-SE` | 13 | 1 | 98 | **80** | **0** | **0** | 204 | #482 `A02` |
| Serbisch (kyrillisch) | `srp-RS` | 10 | 4 | 98 | 100 | **0** | **0** | 203 | #665 `A02` |
| Serbisch (lateinisch) | `ser-SR` | 10 | 3 | 98 | 100 | **0** | **0** | 203 | #639 `A02` |
| Shona | `sna-ZW` | 15 | 4 | 98 | **80** | **0** | **0** | 204 | #619 `A02` |
| Sindhi | `snd-PK` | 34 | 5 | 98 | **80** | **0** | **0** | 204 | #566 `A02` |
| Singhalesisch | `sin-LK` | 18 | 4 | 98 | **80** | **0** | **0** | 204 | #600 `A02` |
| Slowakisch | `slk-SK` | 7 | 2 | 98 | **80** | **0** | **0** | 204 | #631 `A02` |
| Slowenisch | `slv-SI` | 3 | 3 | 98 | **80** | **0** | **0** | 204 | #750 `A02` |
| Somali | `som-SO` | 24 | 3 | 98 | 100 | **0** | **0** | 203 | #550 `A02` |
| Spanisch (Mexiko) | `esl-MX` | 130 | 1 | 98 | **80** | **0** | **0** | 204 | #391 `A02` |
| Spanisch (Spanien) | `esl-ES` | 430 | 1 | 98 | **80** | **0** | **0** | 204 | #371 `A02` |
| Sundanesisch | `sun-ID` | 32 | 3 | 98 | **80** | **0** | **0** | 204 | #500 `A02` |
| Swahili | `swa-KE` | 80 | 3 | 98 | **80** | **0** | **0** | 204 | #433 `A02` |
| Syrisch (Aramäisch) | `syr-IQ` | < 1 | 5 | 98 | **80** | **0** | **0** | 204 | #825 `A02` |
| Tadschikisch | `tgk-TJ` | 9 | 4 | 98 | **80** | **0** | **0** | 204 | #672 `A02` |
| Tamil | `tam-IN` | 87 | 5 | 98 | 100 | **0** | **0** | 203 | #463 `A02` |
| Tatarisch | `tat-RU` | 5 | 5 | 98 | **80** | **0** | **0** | 204 | #742 `A02` |
| Telugu | `tel-IN` | 96 | 5 | 98 | 100 | **0** | **0** | 203 | #456 `A02` |
| Thai | `tha-TH` | 61 | 4 | 98 | **80** | **0** | **0** | 204 | #475 `A02` |
| Tibetisch | `bod-CN` | 6 | 5 | 98 | **80** | **0** | **0** | 204 | #719 `A02` |
| Tigrinya | `tir-ET` | 10 | 5 | 98 | **80** | **0** | **0** | 204 | #687 `A02` |
| Tongaisch | `ton-TO` | < 1 | 3 | 98 | 100 | **0** | **0** | 203 | #836 `A02` |
| Tschechisch | `cze-CZ` | 11 | 2 | 98 | **80** | **0** | **0** | 204 | #582 `A02` |
| Tschetschenisch | `che-RU` | 2 | 5 | 98 | **80** | **0** | **0** | 204 | #776 `A02` |
| Türkisch | `tur-TR` | 90 | 2 | 98 | **80** | **0** | **0** | 204 | #6 `A02` |
| Turkmenisch | `tuk-TM` | 7 | 4 | 98 | **80** | **0** | **0** | 204 | #691 `A02` |
| Uigurisch | `uig-CN` | 11 | 5 | 98 | **80** | **0** | **0** | 204 | #676 `A02` |
| Ukrainisch | `ukr-UA` | 39 | 3 | 98 | 100 | **0** | **0** | 203 | #10 `A02` |
| Ungarisch | `hun-HU` | 13 | 2 | 98 | **80** | **0** | **0** | 204 | #570 `A02` |
| Urdu | `urd-PK` | 232 | 4 | 98 | 100 | **0** | **0** | 203 | #410 `A02` |
| Usbekisch | `uzb-UZ` | 35 | 3 | 98 | **80** | **0** | **0** | 204 | #492 `A02` |
| Vietnamesisch | `vie-VN` | 86 | 1 | 98 | **80** | **0** | **0** | 204 | #395 `A02` |
| Walisisch | `cym-GB` | < 1 | 3 | 98 | **80** | **0** | **0** | 204 | #786 `A02` |
| Westfriesisch | `fry-NL` | < 1 | 3 | 98 | 100 | **0** | **0** | 203 | #805 `A02` |
| Westpanjabi | `pnb-PK` | 90 | 5 | 98 | **80** | **0** | **0** | 204 | #459 `A02` |
| Wolof | `wol-SN` | 22 | 3 | 98 | 100 | **0** | **0** | 203 | #556 `A02` |
| Yoruba | `yor-NG` | 46 | 3 | 98 | 100 | **0** | **0** | 203 | #472 `A02` |
| Zentralkurdisch (Sorani) | `ckb-IQ` | 7 | 5 | 98 | **80** | **0** | **0** | 204 | #701 `A02` |

### Die Hilfe in Stufen

| Stufe | Abschnitte | Seiten | Wörter | Teile |
|---|---|---:|---:|---:|
| Alltag: Redakteure, Benutzer, Anleitungen für Administratoren | `help`, `help/manual`, `help/non-technical-people`, `guides`, `help/book-learning`, `help/book-building-a-site` | 265 | 299.045 | 13 |
| Produkt- und Zielgruppen-Leitfäden | `install`, `features`, `help/adopters`, `help/advocates`, `help/business`, `help/businesses`, `help/community`, `help/creators` und 22 weitere | 3.683 | 2.879.071 | 112 |
| Entwicklerreferenz | `help/extension-development`, `help/template-language`, `help/template-operators`, `help/build-two-ways`, `help/php-developers`, `help/cms-developers`, `help/dxp-developers`, `help/software-engineers` und 12 weitere | 1.790 | 1.804.872 | 70 |
| Tabellen der erzeugten Beispiele | `examples` | 2.705 Einträge | 80.642 | 4 |

<details><summary>Die 199 Teile der Hilfe</summary>

| Teil | Stufe | Seiten | Wörter | Erste … letzte Seite |
|---|---|---:|---:|---|
| `H-help-01` | daily | 12 | 26.349 | `help/README.md` … `help/editing-content.md` |
| `H-help-02` | daily | 12 | 15.164 | `help/free-documentation.md` … `help/os-support.md` |
| `H-help-03` | daily | 13 | 31.616 | `help/project-files.md` … `help/who-is-the-exponential-foundation.md` |
| `H-help-manual-01` | daily | 44 | 17.658 | `help/manual/README.md` … `help/manual/how-do-i-let-a-translator-edit-one-language.md` |
| `H-help-manual-02` | daily | 44 | 18.665 | `help/manual/how-do-i-move-a-user-to-another-group.md` … `help/manual/why-does-search-not-find-an-item.md` |
| `H-help-non-technical-people` | daily | 47 | 28.042 | `help/non-technical-people/checklists/README.md` … `help/non-technical-people/words/the-site-and-the-administration.md` |
| `H-guides-01` | daily | 10 | 26.536 | `guides/README.md` … `guides/collected-information.md` |
| `H-guides-02` | daily | 11 | 28.453 | `guides/content-history.md` … `guides/getting-started.md` |
| `H-guides-03` | daily | 10 | 28.000 | `guides/mail-preferences-administrator.md` … `guides/passkeys.md` |
| `H-guides-04` | daily | 11 | 28.451 | `guides/pdf-exports.md` … `guides/system-information.md` |
| `H-guides-05` | daily | 11 | 26.866 | `guides/templates-and-design.md` … `guides/your-devices-and-sessions.md` |
| `H-help-book-learning` | daily | 31 | 18.155 | `help/book-learning/README.md` … `help/book-learning/workflows-and-triggers.md` |
| `H-help-book-building-a-site` | daily | 9 | 5.090 | `help/book-building-a-site/README.md` … `help/book-building-a-site/the-news-pages.md` |
| `H-install-01` | guides | 7 | 34.636 | `install/01-introduction.md` … `install/07-console-install.md` |
| `H-install-02` | guides | 7 | 57.773 | `install/08-serving-the-site.md` … `install/14-migrating-from-4x.md` |
| `H-install-03` | guides | 7 | 39.225 | `install/15-migrating-from-5x-legacy.md` … `install/migration/checklist.md` |
| `H-install-04` | guides | 7 | 8.465 | `install/migration/coldfusion-apps.md` … `install/migration/exponential-platform-legacy.md` |
| `H-install-05` | guides | 7 | 7.921 | `install/migration/exponential-platform-nexus.md` … `install/migration/magento.md` |
| `H-install-06` | guides | 7 | 5.299 | `install/migration/older-releases.md` … `install/migration/wordpress.md` |
| `H-features-01` | guides | 22 | 36.504 | `features/4.7/cluster_events.md` … `features/6.0/bookmark-folders.md` |
| `H-features-02` | guides | 23 | 39.128 | `features/6.0/bookmarks-of-system-pages.md` … `features/6.0/extension-list-and-downloads.md` |
| `H-features-03` | guides | 23 | 16.924 | `features/6.0/extension-loading-order.md` … `features/6.0/extensions/ezgmaplocation.md` |
| `H-features-04` | guides | 23 | 16.721 | `features/6.0/extensions/ezie.md` … `features/6.0/extensions/owsimpleoperator.md` |
| `H-features-05` | guides | 23 | 37.605 | `features/6.0/extensions/recaptcha.md` … `features/6.0/legacy-bridge.md` |
| `H-features-06` | guides | 23 | 33.431 | `features/6.0/log-rotation.md` … `features/6.0/permission-checks-for-many-assignments.md` |
| `H-features-07` | guides | 23 | 27.546 | `features/6.0/platform-admin-ui-fork.md` … `features/6.0/robots-txt.md` |
| `H-features-08` | guides | 23 | 24.634 | `features/6.0/role-and-policy-template-operators.md` … `features/6.0/translations-and-languages.md` |
| `H-features-09` | guides | 23 | 30.829 | `features/6.0/trash-who-and-where.md` … `features/6.0/web-server-and-solr-commands.md` |
| `H-help-adopters` | guides | 46 | 16.581 | `help/adopters/adopters-checklists.md` … `help/adopters/what-adoption-means.md` |
| `H-help-advocates-01` | guides | 48 | 29.324 | `help/advocates/7x-and-the-foundation-for-advocates.md` … `help/advocates/proposing-a-contribution-sprint.md` |
| `H-help-advocates-02` | guides | 48 | 29.008 | `help/advocates/proposing-a-meetup.md` … `help/advocates/your-first-week-as-an-advocate.md` |
| `H-help-business` | guides | 12 | 29.944 | `help/business/brand-and-naming-rules.md` … `help/business/who-exponential-is-for.md` |
| `H-help-businesses-01` | guides | 30 | 28.554 | `help/businesses/associations/README.md` … `help/businesses/extranets/README.md` |
| `H-help-businesses-02` | guides | 31 | 30.998 | `help/businesses/extranets/customer-and-partner-areas.md` … `help/businesses/libraries/tagging-and-subject-headings.md` |
| `H-help-businesses-03` | guides | 31 | 28.314 | `help/businesses/members/README.md` … `help/businesses/multisite/multi-brand-checklist.md` |
| `H-help-businesses-04` | guides | 31 | 32.234 | `help/businesses/multisite/multi-brand-planning.md` … `help/businesses/reliability/health-checks-and-monitoring.md` |
| `H-help-businesses-05` | guides | 31 | 33.539 | `help/businesses/reliability/incident-response-for-small-teams.md` … `help/businesses/stores/digital-products-and-downloads.md` |
| `H-help-businesses-06` | guides | 31 | 25.917 | `help/businesses/stores/discounts-and-wishlists.md` … `help/businesses/why-businesses-rely-on-exponential.md` |
| `H-help-community` | guides | 52 | 26.819 | `help/community/community-checklists.md` … `help/community/your-first-week-in-the-community.md` |
| `H-help-creators-01` | guides | 46 | 23.103 | `help/creators/app-creators/access-control-in-code.md` … `help/creators/app-creators/rad-tools.md` |
| `H-help-creators-02` | guides | 47 | 26.126 | `help/creators/app-creators/remote-services-expservices.md` … `help/creators/website-creators/member-registration-and-areas.md` |
| `H-help-creators-03` | guides | 47 | 28.743 | `help/creators/website-creators/menus-and-navigation.md` … `help/creators/website-creators/your-first-site-step-by-step.md` |
| `H-help-customers-01` | guides | 37 | 25.803 | `help/customers/commerce/README.md` … `help/customers/day-to-day/analytics-and-what-to-measure.md` |
| `H-help-customers-02` | guides | 38 | 26.709 | `help/customers/day-to-day/approvals-and-review.md` … `help/customers/growth/rss-and-podcast-feeds.md` |
| `H-help-customers-03` | guides | 38 | 24.974 | `help/customers/growth/using-ai-drafts-responsibly.md` … `help/customers/partners-and-hosts/who-does-what.md` |
| `H-help-customers-04` | guides | 38 | 22.266 | `help/customers/partners-and-hosts/working-with-7x-and-the-project.md` … `help/customers/what-you-own.md` |
| `H-help-designers-01` | guides | 63 | 28.180 | `help/designers/README.md` … `help/designers/extensions/gitignore-for-themes.md` |
| `H-help-designers-02` | guides | 64 | 24.251 | `help/designers/extensions/install-and-changelog-files.md` … `help/designers/sales/assets-under-separate-licenses.md` |
| `H-help-designers-03` | guides | 63 | 23.137 | `help/designers/sales/contracts-and-terms.md` … `help/designers/templates/if-and-conditions.md` |
| `H-help-designers-04` | guides | 64 | 26.860 | `help/designers/templates/include.md` … `help/designers/theme-design/color-contrast.md` |
| `H-help-designers-05` | guides | 64 | 37.336 | `help/designers/theme-design/csp-safe-design.md` … `help/designers/theme-design/what-a-design-is.md` |
| `H-help-e-commerce-01` | guides | 26 | 17.960 | `help/e-commerce/catalog/README.md` … `help/e-commerce/operations/analytics-for-stores.md` |
| `H-help-e-commerce-02` | guides | 27 | 18.329 | `help/e-commerce/operations/commerce-audit-trail.md` … `help/e-commerce/settings/workflow-ini-for-the-shop.md` |
| `H-help-ecosystem` | guides | 45 | 18.516 | `help/ecosystem/a-short-history-of-the-ecosystem.md` … `help/ecosystem/what-the-ecosystem-is.md` |
| `H-help-government-01` | guides | 52 | 32.618 | `help/government/accessibility/README.md` … `help/government/building/documents-and-publications/pdf-export.md` |
| `H-help-government-02` | guides | 53 | 24.723 | `help/government/building/documents-and-publications/versions-of-documents.md` … `help/government/building/news-and-alerts/README.md` |
| `H-help-government-03` | guides | 53 | 28.365 | `help/government/building/news-and-alerts/archive-and-expiry.md` … `help/government/checklists/quarterly-access-review.md` |
| `H-help-government-04` | guides | 53 | 31.184 | `help/government/checklists/records-checklist.md` … `help/government/languages/removing-a-translation-safely.md` |
| `H-help-government-05` | guides | 53 | 34.129 | `help/government/languages/right-to-left-in-the-administration.md` … `help/government/levels/parliaments/what-it-publishes.md` |
| `H-help-government-06` | guides | 53 | 35.568 | `help/government/levels/public-health/README.md` … `help/government/operations/availability/high-availability-overview.md` |
| `H-help-government-07` | guides | 53 | 38.338 | `help/government/operations/availability/maintenance-mode-and-planned-downtime.md` … `help/government/operations/incidents/database-unreachable.md` |
| `H-help-government-08` | guides | 53 | 33.862 | `help/government/operations/incidents/ddos-and-probe-floods.md` … `help/government/privacy/analytics.md` |
| `H-help-government-09` | guides | 52 | 31.746 | `help/government/privacy/audit-retention.md` … `help/government/procurement/exit-plans/lock-in-clauses-to-avoid.md` |
| `H-help-government-10` | guides | 53 | 34.251 | `help/government/procurement/exit-plans/personal-data-at-exit.md` … `help/government/procurement/total-cost-of-ownership/staff-and-skills.md` |
| `H-help-government-11` | guides | 53 | 29.772 | `help/government/procurement/total-cost-of-ownership/support-contracts.md` … `help/government/security/file-uploads-and-svg.md` |
| `H-help-government-12` | guides | 53 | 30.490 | `help/government/security/forwarding-audit-to-a-siem.md` … `help/government/tasks/compliance/add-security-txt.md` |
| `H-help-government-13` | guides | 53 | 19.549 | `help/government/tasks/compliance/add-your-own-words-to-the-weak-password-list.md` … `help/government/tasks/operations/bring-the-site-back-online.md` |
| `H-help-government-14` | guides | 53 | 23.257 | `help/government/tasks/operations/check-file-and-directory-permissions.md` … `help/government/tasks/people-and-permissions/keep-editors-out-of-the-admin-siteaccess.md` |
| `H-help-government-15` | guides | 53 | 25.199 | `help/government/tasks/people-and-permissions/let-a-contractor-edit-only-one-folder.md` … `help/government/tasks/publishing/find-and-fix-broken-links.md` |
| `H-help-government-16` | guides | 53 | 24.196 | `help/government/tasks/publishing/follow-changes-with-notifications.md` … `help/government/tasks/sites-and-languages/connect-staff-sign-in-to-ldap.md` |
| `H-help-government-17` | guides | 53 | 21.475 | `help/government/tasks/sites-and-languages/export-a-list-of-content-as-csv.md` … `help/government/transparency-and-foi/withdrawing-and-correcting-published-information.md` |
| `H-help-hosting-01` | guides | 23 | 24.499 | `help/hosting/basics/README.md` … `help/hosting/business/licensing-for-hosts.md` |
| `H-help-hosting-02` | guides | 24 | 20.708 | `help/hosting/business/marketing-exponential-hosting.md` … `help/hosting/models/kubernetes.md` |
| `H-help-hosting-03` | guides | 24 | 30.593 | `help/hosting/models/managed-hosting.md` … `help/hosting/operations/storage-layout.md` |
| `H-help-hosting-04` | guides | 23 | 25.168 | `help/hosting/operations/system-card-for-hosts.md` … `help/hosting/performance/valkey-and-redis.md` |
| `H-help-hosting-05` | guides | 24 | 20.812 | `help/hosting/performance/varnish-and-reverse-proxies.md` … `help/hosting/security/content-security-policy.md` |
| `H-help-hosting-06` | guides | 24 | 29.752 | `help/hosting/security/demo-mode-for-hosts.md` … `help/hosting/servers/velocity-workers-and-pools.md` |
| `H-help-individuals-01` | guides | 27 | 19.254 | `help/individuals/blogs/README.md` … `help/individuals/individuals-glossary.md` |
| `H-help-individuals-02` | guides | 28 | 21.130 | `help/individuals/individuals-reading-path.md` … `help/individuals/what-exists-and-what-does-not.md` |
| `H-help-leadership-01` | guides | 9 | 19.888 | `help/leadership/accessibility-security-and-compliance-checklists.md` … `help/leadership/planning-an-exponential-project.md` |
| `H-help-leadership-02` | guides | 9 | 20.198 | `help/leadership/project-managers-start-here.md` … `help/leadership/working-with-the-community.md` |
| `H-help-local-businesses-01` | guides | 27 | 17.671 | `help/local-businesses/budget/README.md` … `help/local-businesses/getting-started/working-with-a-local-partner-or-freelancer.md` |
| `H-help-local-businesses-02` | guides | 28 | 19.105 | `help/local-businesses/local-businesses-faq.md` … `help/local-businesses/templates/website-brief-for-a-local-business.md` |
| `H-help-non-profits-01` | guides | 27 | 18.785 | `help/non-profits/accessibility/README.md` … `help/non-profits/non-profits-faq.md` |
| `H-help-non-profits-02` | guides | 28 | 19.503 | `help/non-profits/non-profits-glossary.md` … `help/non-profits/volunteers/volunteers-as-editors.md` |
| `H-help-partners-01` | guides | 37 | 15.646 | `help/partners/application-checklist.md` … `help/partners/partner-faq.md` |
| `H-help-partners-02` | guides | 38 | 17.420 | `help/partners/partner-glossary.md` … `help/partners/your-partner-page.md` |
| `H-help-podcasters-01` | guides | 24 | 17.308 | `help/podcasters/a-release-calendar.md` … `help/podcasters/podcaster-checklists.md` |
| `H-help-podcasters-02` | guides | 24 | 18.395 | `help/podcasters/podcaster-faq.md` … `help/podcasters/writing-show-notes.md` |
| `H-help-press-01` | guides | 37 | 27.147 | `help/press/7x-for-reporters.md` … `help/press/fact-checking-checklist.md` |
| `H-help-press-02` | guides | 38 | 24.830 | `help/press/fact-checking-guide.md` … `help/press/release-story-templates.md` |
| `H-help-press-03` | guides | 38 | 23.293 | `help/press/reporter-faq.md` … `help/press/story-nexus-and-legacy.md` |
| `H-help-press-04` | guides | 38 | 25.322 | `help/press/story-online-editor-tinymce-8.md` … `help/press/writing-about-the-ez-publish-years.md` |
| `H-help-project` | guides | 12 | 15.556 | `help/project/README.md` … `help/project/roadmap.md` |
| `H-help-resellers-01` | guides | 26 | 17.381 | `help/resellers/basics/choosing-a-partner-level.md` … `help/resellers/offers/demo-sites.md` |
| `H-help-resellers-02` | guides | 27 | 18.527 | `help/resellers/offers/e-commerce-offers.md` … `help/resellers/sales/sales-process.md` |
| `H-help-site-packages-01` | guides | 26 | 27.323 | `help/site-packages/README.md` … `help/site-packages/cjw-multisite/the-cjw-themes-jumper-extension.md` |
| `H-help-site-packages-02` | guides | 26 | 29.246 | `help/site-packages/cjw-multisite/the-demo-content-package.md` … `help/site-packages/requirements/the-requirements-checklist.md` |
| `H-help-site-packages-03` | guides | 26 | 26.764 | `help/site-packages/requirements/thumbnails-and-screenshots.md` … `help/site-packages/your-own/versioning-your-own-packages.md` |
| `H-help-sponsors` | guides | 11 | 5.284 | `help/sponsors/7x-the-first-sponsor.md` … `help/sponsors/what-sponsorship-funds.md` |
| `H-help-students-01` | guides | 27 | 21.038 | `help/students/asking-for-help-and-study-groups.md` … `help/students/project-04-a-page-with-layouts.md` |
| `H-help-students-02` | guides | 28 | 23.218 | `help/students/project-05-a-multilingual-site.md` … `help/students/your-first-site-three-ways.md` |
| `H-help-supporters` | guides | 12 | 5.687 | `help/supporters/advocacy.md` … `help/supporters/ways-to-support-exponential.md` |
| `H-help-theme-designers-01` | guides | 21 | 17.949 | `help/theme-designers/building-a-portfolio-and-getting-work.md` … `help/theme-designers/designing-the-front-page.md` |
| `H-help-theme-designers-02` | guides | 22 | 19.306 | `help/theme-designers/designing-view-modes.md` … `help/theme-designers/your-first-day-as-a-theme-designer.md` |
| `H-help-trainers-01` | guides | 31 | 31.213 | `help/trainers/assessments/README.md` … `help/trainers/assessments/module-05-newsletter-and-migration/README.md` |
| `H-help-trainers-02` | guides | 32 | 32.170 | `help/trainers/assessments/module-05-newsletter-and-migration/migration-practical-task.md` … `help/trainers/developers/module-03-designs-overrides-and-debugging/the-design-chain.md` |
| `H-help-trainers-03` | guides | 31 | 28.811 | `help/trainers/developers/module-04-content-classes-and-datatypes/README.md` … `help/trainers/developers/module-09-the-console-and-persistent-objects/the-console.md` |
| `H-help-trainers-04` | guides | 32 | 30.511 | `help/trainers/developers/module-10-workflows-events-and-cronjobs/README.md` … `help/trainers/editors-101/module-04-the-online-editor/embeds-tables-and-choosing-the-editor.md` |
| `H-help-trainers-05` | guides | 31 | 27.725 | `help/trainers/editors-101/module-04-the-online-editor/links-and-anchors.md` … `help/trainers/editors-201/module-03-organizing-content/README.md` |
| `H-help-trainers-06` | guides | 32 | 26.487 | `help/trainers/editors-201/module-03-organizing-content/large-operations-and-the-trash.md` … `help/trainers/exponential-intelligence/module-03-the-edit-form/README.md` |
| `H-help-trainers-07` | guides | 31 | 28.236 | `help/trainers/exponential-intelligence/module-03-the-edit-form/alt-text-for-images.md` … `help/trainers/hosting-operations/module-03-databases/README.md` |
| `H-help-trainers-08` | guides | 32 | 30.684 | `help/trainers/hosting-operations/module-03-databases/choosing-a-database.md` … `help/trainers/layouts/module-02-layouts-and-layout-types/README.md` |
| `H-help-trainers-09` | guides | 31 | 26.302 | `help/trainers/layouts/module-02-layouts-and-layout-types/creating-a-layout.md` … `help/trainers/migration/module-01-planning-a-migration/phases-and-the-checklist.md` |
| `H-help-trainers-10` | guides | 32 | 32.615 | `help/trainers/migration/module-01-planning-a-migration/rehearsal-cut-over-and-rollback.md` … `help/trainers/multisite-and-packages/module-01-siteaccesses-for-several-sites/a-siteaccess-per-language.md` |
| `H-help-trainers-11` | guides | 31 | 35.515 | `help/trainers/multisite-and-packages/module-01-siteaccesses-for-several-sites/adding-a-second-site.md` … `help/trainers/newsletter/README.md` |
| `H-help-trainers-12` | guides | 32 | 31.600 | `help/trainers/newsletter/module-01-getting-started/README.md` … `help/trainers/search/module-01-how-search-works/where-people-search.md` |
| `H-help-trainers-13` | guides | 31 | 30.687 | `help/trainers/search/module-02-indexing/README.md` … `help/trainers/site-administrators/module-02-users-and-groups/passwords-resets-and-lockouts.md` |
| `H-help-trainers-14` | guides | 32 | 32.745 | `help/trainers/site-administrators/module-02-users-and-groups/unactivated-users-and-account-checks.md` … `help/trainers/site-administrators/module-08-security/README.md` |
| `H-help-trainers-15` | guides | 31 | 34.596 | `help/trainers/site-administrators/module-08-security/content-security-policy.md` … `help/trainers/theme-designers/module-03-stylesheets-and-scripts/listing-files-in-design-ini.md` |
| `H-help-trainers-16` | guides | 32 | 31.783 | `help/trainers/theme-designers/module-03-stylesheets-and-scripts/ready-for-a-content-security-policy.md` … `help/trainers/train-the-trainer/module-01-preparing-to-teach/your-material-and-the-gfdl.md` |
| `H-help-trainers-17` | guides | 31 | 21.442 | `help/trainers/train-the-trainer/module-02-building-the-lab/README.md` … `help/trainers/train-the-trainer/module-06-assessment-and-follow-up/certificates-of-attendance.md` |
| `H-help-trainers-18` | guides | 32 | 19.506 | `help/trainers/train-the-trainer/module-06-assessment-and-follow-up/checking-understanding-during-class.md` … `help/trainers/workshops/module-05-capstone-projects/multilingual-event-site.md` |
| `H-root-01` | guides | 4 | 11.136 | `INSTALL.md` … `UPDATE.md` |
| `H-root-02` | guides | 4 | 32.713 | `branch-overview.md` … `languages.md` |
| `H-help-extension-development-01` | reference | 43 | 36.332 | `help/extension-development/README.md` … `help/extension-development/part-2-builder/the-exp-ini-command.md` |
| `H-help-extension-development-02` | reference | 43 | 25.019 | `help/extension-development/part-2-builder/view-modes-for-builders.md` … `help/extension-development/part-3-extension-developer/tutorials/by-hand-custom-tag.md` |
| `H-help-extension-development-03` | reference | 43 | 28.639 | `help/extension-development/part-3-extension-developer/tutorials/by-hand-datatype.md` … `help/extension-development/part-4-upgrading/old-jquery-to-jquery-4.md` |
| `H-help-extension-development-04` | reference | 43 | 29.974 | `help/extension-development/part-4-upgrading/php-8-1-to-8-5-deprecations.md` … `help/extension-development/part-7-releasing/github-releases-and-release-notes.md` |
| `H-help-extension-development-05` | reference | 44 | 24.398 | `help/extension-development/part-7-releasing/packages-ezpkg.md` … `help/extension-development/reading-paths.md` |
| `H-help-template-language` | reference | 29 | 28.301 | `help/template-language/README.md` … `help/template-language/what-the-template-language-is.md` |
| `H-help-template-operators-01` | reference | 24 | 24.150 | `help/template-operators/README.md` … `help/template-operators/menu-and-tree-operators.md` |
| `H-help-template-operators-02` | reference | 24 | 30.407 | `help/template-operators/newsletter-operators.md` … `help/template-operators/url-operators.md` |
| `H-help-build-two-ways-01` | reference | 83 | 27.828 | `help/build-two-ways/README.md` … `help/build-two-ways/examples/maintain/add-a-recipe-to-the-index.md` |
| `H-help-build-two-ways-02` | reference | 83 | 20.834 | `help/build-two-ways/examples/maintain/adjust-spacing.md` … `help/build-two-ways/examples/plan/inventory-from-the-console.md` |
| `H-help-build-two-ways-03` | reference | 84 | 30.664 | `help/build-two-ways/examples/plan/inventory-page-types.md` … `help/build-two-ways/your-own-design-extension.md` |
| `H-help-php-developers-01` | reference | 27 | 21.596 | `help/php-developers/a-php-development-checkout.md` … `help/php-developers/php-developer-checklists.md` |
| `H-help-php-developers-02` | reference | 27 | 20.955 | `help/php-developers/php-developer-faq.md` … `help/php-developers/writing-code-that-runs-on-php-8-0.md` |
| `H-help-cms-developers-01` | reference | 25 | 23.426 | `help/cms-developers/accessibility.md` … `help/cms-developers/headless-delivery.md` |
| `H-help-cms-developers-02` | reference | 26 | 22.875 | `help/cms-developers/images-and-image-aliases.md` … `help/cms-developers/workflows-and-triggers.md` |
| `H-help-dxp-developers` | reference | 51 | 26.801 | `help/dxp-developers/application-cache-and-redis.md` … `help/dxp-developers/what-the-dxp-ships-and-what-it-does-not.md` |
| `H-help-software-engineers-01` | reference | 25 | 18.400 | `help/software-engineers/api-design-rest-ezjscore-headless.md` … `help/software-engineers/glossary-for-software-engineers.md` |
| `H-help-software-engineers-02` | reference | 26 | 19.814 | `help/software-engineers/http-security-csp-and-proxies.md` … `help/software-engineers/writing-portable-sql.md` |
| `H-help-web-framework-developers-01` | reference | 27 | 18.428 | `help/web-framework-developers/authentication-and-sessions.md` … `help/web-framework-developers/migrations-and-dba-schema-files.md` |
| `H-help-web-framework-developers-02` | reference | 27 | 18.999 | `help/web-framework-developers/mixing-exponential-and-a-framework.md` … `help/web-framework-developers/your-first-module-step-by-step.md` |
| `H-help-mobile-app-developers-01` | reference | 27 | 15.938 | `help/mobile-app-developers/auth/README.md` … `help/mobile-app-developers/media-and-images/images-in-rest-answers.md` |
| `H-help-mobile-app-developers-02` | reference | 28 | 17.528 | `help/mobile-app-developers/mobile-app-developers-faq.md` … `help/mobile-app-developers/which-api-for-your-app.md` |
| `H-help-database-administrators-01` | reference | 24 | 19.363 | `help/database-administrators/applying-the-6-0-15-database-update.md` … `help/database-administrators/mysql-read-replicas.md` |
| `H-help-database-administrators-02` | reference | 25 | 21.311 | `help/database-administrators/oracle-connections-and-tuning.md` … `help/database-administrators/upgrades-and-sql-update-files.md` |
| `H-help-ai-agents-01` | reference | 24 | 16.131 | `help/ai-agents/agentic-workflows/README.md` … `help/ai-agents/expai/chains-and-queued-work.md` |
| `H-help-ai-agents-02` | reference | 24 | 18.470 | `help/ai-agents/expai/configuring-a-provider.md` … `help/ai-agents/which-path-fits-your-task.md` |
| `H-help-bundlers-01` | reference | 27 | 18.589 | `help/bundlers/adding-and-removing-extensions.md` … `help/bundlers/hosting-panel-installers.md` |
| `H-help-bundlers-02` | reference | 28 | 19.389 | `help/bundlers/integrity-checks.md` … `help/bundlers/your-own-package-server.md` |
| `H-help-integrators-01` | reference | 27 | 21.189 | `help/integrators/ai-job-queue-and-mcp.md` … `help/integrators/integrators-start-here.md` |
| `H-help-integrators-02` | reference | 28 | 22.239 | `help/integrators/interface-specification-template.md` … `help/integrators/writing-an-import-script.md` |
| `H-help-sqli-import` | reference | 6 | 6.974 | `help/sqli-import/README.md` … `help/sqli-import/upgrading-from-1x.md` |
| `H-specifications-01` | reference | 18 | 24.726 | `specifications/6.0/README.md` … `specifications/6.0/legacy-bridge-bundle.md` |
| `H-specifications-02` | reference | 19 | 30.138 | `specifications/6.0/mail-preferences-cli.md` … `specifications/6.0/xrowextract.md` |
| `H-standards-01` | reference | 18 | 34.399 | `standards/README.md` … `standards/patterns/area-extension-bcgooglesitemaps.md` |
| `H-standards-02` | reference | 18 | 21.766 | `standards/patterns/area-extension-bcwebsitestatistics.md` … `standards/patterns/area-extension-explayouts-content-browser.md` |
| `H-standards-03` | reference | 19 | 15.550 | `standards/patterns/area-extension-explayouts-core.md` … `standards/patterns/area-extension-ezautosave.md` |
| `H-standards-04` | reference | 18 | 10.141 | `standards/patterns/area-extension-ezdemo.md` … `standards/patterns/area-extension-ezprestapiprovider.md` |
| `H-standards-05` | reference | 18 | 10.157 | `standards/patterns/area-extension-ezssp.md` … `standards/patterns/area-extension-sevenx-themes-simple.md` |
| `H-standards-06` | reference | 19 | 22.100 | `standards/patterns/area-extension-sevenx-themes-super.md` … `standards/patterns/area-kernel-classes-datatable.md` |
| `H-standards-07` | reference | 18 | 9.815 | `standards/patterns/area-kernel-classes-datatypes.md` … `standards/patterns/area-kernel-classes-role.md` |
| `H-standards-08` | reference | 18 | 23.036 | `standards/patterns/area-kernel-classes-secrets.md` … `standards/patterns/area-kernel-private-classes-exceptions.md` |
| `H-standards-09` | reference | 19 | 20.556 | `standards/patterns/area-kernel-private-classes-httpcache.md` … `standards/patterns/area-lib-ezsoap.md` |
| `H-standards-10` | reference | 18 | 69.241 | `standards/patterns/area-lib-eztemplate.md` … `standards/patterns/iterator.md` |
| `H-standards-11` | reference | 19 | 79.264 | `standards/patterns/module-view.md` … `standards/web-platform.md` |
| `H-bc-01` | reference | 10 | 33.882 | `bc/5.90/README.md` … `bc/6.0/SUBITEMS_MENU_MORE_ACTIONS_MENU_EXPANSION_HIDE_UNHIDE.md` |
| `H-bc-02` | reference | 11 | 68.562 | `bc/6.0/admin4-styling.md` … `bc/6.0/bookmarks-of-system-pages.md` |
| `H-bc-03` | reference | 11 | 29.419 | `bc/6.0/cache-console.md` … `bc/6.0/demo-mode.md` |
| `H-bc-04` | reference | 10 | 12.356 | `bc/6.0/draft-edit-access.md` … `bc/6.0/file-modes.md` |
| `H-bc-05` | reference | 11 | 30.832 | `bc/6.0/frankenphp.md` … `bc/6.0/kickstartercli.md` |
| `H-bc-06` | reference | 11 | 9.474 | `bc/6.0/locations-tab-paging-and-sorting.md` … `bc/6.0/love/ROADMAP.md` |
| `H-bc-07` | reference | 11 | 20.982 | `bc/6.0/love/TESTING.md` … `bc/6.0/oauth-pkce.md` |
| `H-bc-08` | reference | 10 | 12.854 | `bc/6.0/oauth-token-hardening.md` … `bc/6.0/php-8.0-support.md` |
| `H-bc-09` | reference | 11 | 35.090 | `bc/6.0/php8.md` … `bc/6.0/repair.md` |
| `H-bc-10` | reference | 11 | 27.579 | `bc/6.0/response-cache-and-navigation.md` … `bc/6.0/trash.md` |
| `H-bc-11` | reference | 11 | 27.781 | `bc/6.0/trusted-proxies.md` … `bc/6.0/yui-removal.md` |
| `H-history-01` | reference | 23 | 9.704 | `history/2023/2023-12.md` … `history/2025/2025-09.md` |
| `H-history-02` | reference | 24 | 42.484 | `history/2025/2025-10.md` … `history/ecosystem/adminneo.md` |
| `H-history-03` | reference | 24 | 20.834 | `history/ecosystem/cjw-exponential-platform-nexus.md` … `history/ecosystem/ezplatform-query-fieldtype.md` |
| `H-history-04` | reference | 24 | 11.147 | `history/ecosystem/ezplatform-richtext.md` … `history/ecosystem/months/2021-10.md` |
| `H-history-05` | reference | 24 | 9.108 | `history/ecosystem/months/2021-11.md` … `history/ecosystem/months/2024-10.md` |
| `H-history-06` | reference | 24 | 19.259 | `history/ecosystem/months/2024-11.md` … `history/ecosystem/oss-skeleton.md` |
| `H-history-07` | reference | 24 | 20.764 | `history/ecosystem/oss.md` … `history/extensions/ezjscore.md` |
| `H-history-08` | reference | 24 | 18.075 | `history/extensions/ezmbpaex.md` … `history/extensions/months/2024-08.md` |
| `H-history-09` | reference | 24 | 24.372 | `history/extensions/months/2024-09.md` … `history/extensions/sevenx_valkey_cache.md` |
| `H-history-10` | reference | 24 | 73.419 | `history/extensions/swark.md` … `history/ledger/exp_enhanced_link.md` |
| `H-history-11` | reference | 24 | 79.951 | `history/ledger/explayouts_ui.md` … `history/ledger/ezoe.md` |
| `H-history-12` | reference | 24 | 7.378 | `history/ledger/ezpaypal.md` … `history/ledger/eztags.md` |
| `H-history-13` | reference | 24 | 33.737 | `history/ledger/ezupdate.md` … `history/ledger/recaptcha.md` |
| `H-history-14` | reference | 24 | 40.503 | `history/ledger/sevenx-recipes.md` … `history/velocity/installers-and-packages.md` |
| `H-examples` | reference | 26 | 19.476 | `examples/README.md` … `examples/web-servers/velocity.md` |
| `X01` | tables | 1.062 Einträge | 23.768 | `descriptions/01-command-accesscheck.json` … `descriptions/05-ini-settings-content-ini.json` |
| `X02` | tables | 594 Einträge | 24.994 | `descriptions/06-ini-settings-debugbar-ini.json` … `descriptions/08-ini-settings-package-ini.json` |
| `X03` | tables | 730 Einträge | 29.294 | `descriptions/09-ini-settings-site-ini.json` … `descriptions/workflow-events.json` |
| `X04` | tables | 319 Einträge | 2.586 | `strings.json` |

</details>

<details><summary>Die 10 Teile des Administrationskatalogs</summary>

| Teil | Texte | Wörter | Kontexte (erster … letzter) |
|---|---:|---:|---|
| `A01` | 1.977 | 12.767 | `contentstructuremenu/show_content_structure` … `design/admin/infocollector/confirmremoval` |
| `A02` | 1.999 | 11.506 | `design/admin/infocollector/overview` … `design/admin/preview/company` |
| `A03` | 1.771 | 11.989 | `design/admin/preview/feedbackform` … `design/admin/setup/extensions` |
| `A04` | 1.971 | 20.842 | `design/admin/setup/info` … `design/admin/setup/rad/override` |
| `A05` | 1.998 | 14.899 | `design/admin/setup/rad/settings` … `design/admin/visual/templateview` |
| `A06` | 1.984 | 8.966 | `design/admin/visual/toolbar` … `design/standard/section` |
| `A07` | 1.921 | 13.620 | `design/standard/setup` … `kernel/collaboration` |
| `A08` | 1.830 | 12.973 | `kernel/content` … `kernel/contentfinder` |
| `A09` | 2.000 | 11.265 | `design/admin/audit` … `kernel/mailpreferences/mail` |
| `A10` | 1.650 | 10.454 | `kernel/mailpreferences/status` … `kernel/user/security/mail` |

</details>

### Sprachen ohne Übersetzung

Die Sprachen der mitgelieferten Locales, die die Administration auf Englisch zeigt, die Sprachen, die auf einem Branch übersetzt werden, und Sprachen mit fünf Millionen Sprechern oder mehr, für die es noch keine Locale gibt (der Code ist ein Vorschlag). Sortiert nach Sprechern je Schwierigkeitspunkt.

| Sprache | Code | Sprecher (Mio.) | Schrift | Richtung | Schwierigkeit | Stand |
|---|---|---:|---|---|:-:|---|
| Haitianisch | `hat-HT` | 13 | Latn | LTR | 2 | neue Locale, Welle #126 |
| Saraiki | `skr-PK` | 26 | Arab | RTL | 5 | neue Locale, Welle #137 |
| Setswana | `tsn-ZA` | 14 | Latn | LTR | 3 | neue Locale, Welle #148 |
| Dioula | `dyu-CI` | 12 | Latn | LTR | 3 | neue Locale, Welle #159 |
| Chichewa | `nya-MW` | 15 | Latn | LTR | 4 | neue Locale, Welle #170 |
| Nord-Sotho | `nso-ZA` | 14 | Latn | LTR | 4 | neue Locale, Welle #181 |
| Sesotho | `sot-ZA` | 14 | Latn | LTR | 4 | neue Locale, Welle #192 |
| Mòoré | `mos-BF` | 10 | Latn | LTR | 3 | neue Locale, Welle #203 |
| Südaserbaidschanisch | `azb-IR` | 15 | Arab | RTL | 5 | neue Locale, Welle #214 |
| Kanuri | `kau-NG` | 10 | Latn | LTR | 4 | neue Locale, Welle #225 |
| Makua | `vmw-MZ` | 9 | Latn | LTR | 4 | neue Locale, Welle #236 |
| Kikongo | `kon-CD` | 7 | Latn | LTR | 4 | neue Locale, Welle #247 |
| Luhya | `luy-KE` | 7 | Latn | LTR | 4 | neue Locale, Welle #258 |
| Xitsonga | `tso-ZA` | 7 | Latn | LTR | 4 | neue Locale, Welle #269 |
| Umbundu | `umb-AO` | 7 | Latn | LTR | 4 | neue Locale, Welle #280 |
| Luo | `luo-KE` | 5 | Latn | LTR | 3 | neue Locale, Welle #291 |
| Sango | `sag-CF` | 5 | Latn | LTR | 3 | neue Locale, Welle #302 |
| Guaraní | `grn-PY` | 7 | Latn | LTR | 4 | neue Locale, Welle #313 |
| Belutschisch | `bal-PK` | 8 | Arab | RTL | 5 | neue Locale, Welle #324 |
| Kalenjin | `kln-KE` | 6 | Latn | LTR | 4 | neue Locale, Welle #335 |
| Tshiluba | `lua-CD` | 6 | Latn | LTR | 4 | neue Locale, Welle #346 |
| Sukuma | `suk-TZ` | 5 | Latn | LTR | 4 | neue Locale, Welle #357 |

### Die Warteschlange: die ersten 60 Wellen

| Phase | Wellen |
|---|---:|
| 1. Kataloge der Prioritätsgruppe | 27 |
| 2. Deutsch: Hilfe des Alltags | 0 |
| 3. Hilfe des Alltags der Prioritätsgruppe | 98 |
| 4. Neue Übersetzungen | 242 |
| 5. Erweiterungen der übrigen Sprachen | 523 |
| 6. Hilfe des Alltags der übrigen Sprachen | 2.030 |
| 7. Übrige Hilfe der Prioritätsgruppe | 1.314 |
| 8. Übrige Hilfe der übrigen Sprachen | 26.970 |

| # | Welle | Sprache | Arbeitspaket | Umfang (offen) | Schwierigkeit | Voraussetzungen |
|---:|---|---|---|---|:-:|---|
| 1 | `ger-DE/E` | Deutsch | Mitgelieferte Erweiterungen | 137 von 567 Texten | 2 | `GUIDE.md` |
| 2 | `ara-SA/A02` | Arabisch | Administration, Teil A02 | 20 von 1.999 Texten | 4 | `GUIDE.md`, RTL |
| 3 | `ara-SA/A05` | Arabisch | Administration, Teil A05 | 20 von 1.998 Texten | 4 | `GUIDE.md`, RTL |
| 4 | `ara-SA/A10` | Arabisch | Administration, Teil A10 | 215 von 1.650 Texten | 4 | `GUIDE.md`, RTL |
| 5 | `ara-SA/E` | Arabisch | Mitgelieferte Erweiterungen | 114 von 567 Texten | 4 | `GUIDE.md`, RTL |
| 6 | `tur-TR/A02` | Türkisch | Administration, Teil A02 | 20 von 1.999 Texten | 2 | `GUIDE.md` |
| 7 | `tur-TR/A05` | Türkisch | Administration, Teil A05 | 20 von 1.998 Texten | 2 | `GUIDE.md` |
| 8 | `tur-TR/A10` | Türkisch | Administration, Teil A10 | 215 von 1.650 Texten | 2 | `GUIDE.md` |
| 9 | `tur-TR/E` | Türkisch | Mitgelieferte Erweiterungen | 114 von 567 Texten | 2 | `GUIDE.md` |
| 10 | `ukr-UA/A02` | Ukrainisch | Administration, Teil A02 | 20 von 1.999 Texten | 3 | `GUIDE.md` |
| 11 | `ukr-UA/A05` | Ukrainisch | Administration, Teil A05 | 20 von 1.998 Texten | 3 | `GUIDE.md` |
| 12 | `ukr-UA/A10` | Ukrainisch | Administration, Teil A10 | 215 von 1.650 Texten | 3 | `GUIDE.md` |
| 13 | `rus-RU/A02` | Russisch | Administration, Teil A02 | 20 von 1.999 Texten | 3 | `GUIDE.md` |
| 14 | `rus-RU/A05` | Russisch | Administration, Teil A05 | 20 von 1.998 Texten | 3 | `GUIDE.md` |
| 15 | `rus-RU/A10` | Russisch | Administration, Teil A10 | 215 von 1.650 Texten | 3 | `GUIDE.md` |
| 16 | `fas-IR/A02` | Persisch | Administration, Teil A02 | 20 von 1.999 Texten | 4 | `GUIDE.md`, RTL |
| 17 | `fas-IR/A05` | Persisch | Administration, Teil A05 | 20 von 1.998 Texten | 4 | `GUIDE.md`, RTL |
| 18 | `fas-IR/A10` | Persisch | Administration, Teil A10 | 215 von 1.650 Texten | 4 | `GUIDE.md`, RTL |
| 19 | `fas-IR/E` | Persisch | Mitgelieferte Erweiterungen | 114 von 567 Texten | 4 | `GUIDE.md`, RTL |
| 20 | `pus-AF/A02` | Paschtu | Administration, Teil A02 | 20 von 1.999 Texten | 5 | `GUIDE.md`, RTL |
| 21 | `pus-AF/A05` | Paschtu | Administration, Teil A05 | 20 von 1.998 Texten | 5 | `GUIDE.md`, RTL |
| 22 | `pus-AF/A10` | Paschtu | Administration, Teil A10 | 215 von 1.650 Texten | 5 | `GUIDE.md`, RTL |
| 23 | `pus-AF/E` | Paschtu | Mitgelieferte Erweiterungen | 114 von 567 Texten | 5 | `GUIDE.md`, RTL |
| 24 | `kur-TR/A02` | Kurdisch (Kurmandschi) | Administration, Teil A02 | 20 von 1.999 Texten | 3 | `GUIDE.md` |
| 25 | `kur-TR/A05` | Kurdisch (Kurmandschi) | Administration, Teil A05 | 20 von 1.998 Texten | 3 | `GUIDE.md` |
| 26 | `kur-TR/A10` | Kurdisch (Kurmandschi) | Administration, Teil A10 | 215 von 1.650 Texten | 3 | `GUIDE.md` |
| 27 | `kur-TR/E` | Kurdisch (Kurmandschi) | Mitgelieferte Erweiterungen | 114 von 567 Texten | 3 | `GUIDE.md` |
| 28 | `ara-SA/S` | Arabisch | Hilfe einrichten | klein | 4 | `GUIDE.md`, RTL, PDF-Schrift Arab |
| 29 | `tur-TR/S` | Türkisch | Hilfe einrichten | klein | 2 | `GUIDE.md` |
| 30 | `ukr-UA/S` | Ukrainisch | Hilfe einrichten | klein | 3 | `GUIDE.md` |
| 31 | `rus-RU/S` | Russisch | Hilfe einrichten | klein | 3 | `GUIDE.md` |
| 32 | `fas-IR/S` | Persisch | Hilfe einrichten | klein | 4 | `GUIDE.md`, RTL, PDF-Schrift Arab |
| 33 | `pus-AF/S` | Paschtu | Hilfe einrichten | klein | 5 | `GUIDE.md`, RTL, PDF-Schrift Arab |
| 34 | `kur-TR/S` | Kurdisch (Kurmandschi) | Hilfe einrichten | klein | 3 | `GUIDE.md` |
| 35 | `ara-SA/H-help-01` | Arabisch | Hilfe `H-help-01` | 12 Seiten, 26.349 Wörter | 4 | `GUIDE.md`, RTL, PDF-Schrift Arab, Wellen `S` und Administration |
| 36 | `tur-TR/H-help-01` | Türkisch | Hilfe `H-help-01` | 12 Seiten, 26.349 Wörter | 2 | `GUIDE.md`, Wellen `S` und Administration |
| 37 | `ukr-UA/H-help-01` | Ukrainisch | Hilfe `H-help-01` | 12 Seiten, 26.349 Wörter | 3 | `GUIDE.md`, Wellen `S` und Administration |
| 38 | `rus-RU/H-help-01` | Russisch | Hilfe `H-help-01` | 12 Seiten, 26.349 Wörter | 3 | `GUIDE.md`, Wellen `S` und Administration |
| 39 | `fas-IR/H-help-01` | Persisch | Hilfe `H-help-01` | 12 Seiten, 26.349 Wörter | 4 | `GUIDE.md`, RTL, PDF-Schrift Arab, Wellen `S` und Administration |
| 40 | `pus-AF/H-help-01` | Paschtu | Hilfe `H-help-01` | 12 Seiten, 26.349 Wörter | 5 | `GUIDE.md`, RTL, PDF-Schrift Arab, Wellen `S` und Administration |
| 41 | `kur-TR/H-help-01` | Kurdisch (Kurmandschi) | Hilfe `H-help-01` | 12 Seiten, 26.349 Wörter | 3 | `GUIDE.md`, Wellen `S` und Administration |
| 42 | `ara-SA/H-help-02` | Arabisch | Hilfe `H-help-02` | 12 Seiten, 15.164 Wörter | 4 | `GUIDE.md`, RTL, PDF-Schrift Arab, Wellen `S` und Administration |
| 43 | `tur-TR/H-help-02` | Türkisch | Hilfe `H-help-02` | 12 Seiten, 15.164 Wörter | 2 | `GUIDE.md`, Wellen `S` und Administration |
| 44 | `ukr-UA/H-help-02` | Ukrainisch | Hilfe `H-help-02` | 12 Seiten, 15.164 Wörter | 3 | `GUIDE.md`, Wellen `S` und Administration |
| 45 | `rus-RU/H-help-02` | Russisch | Hilfe `H-help-02` | 12 Seiten, 15.164 Wörter | 3 | `GUIDE.md`, Wellen `S` und Administration |
| 46 | `fas-IR/H-help-02` | Persisch | Hilfe `H-help-02` | 12 Seiten, 15.164 Wörter | 4 | `GUIDE.md`, RTL, PDF-Schrift Arab, Wellen `S` und Administration |
| 47 | `pus-AF/H-help-02` | Paschtu | Hilfe `H-help-02` | 12 Seiten, 15.164 Wörter | 5 | `GUIDE.md`, RTL, PDF-Schrift Arab, Wellen `S` und Administration |
| 48 | `kur-TR/H-help-02` | Kurdisch (Kurmandschi) | Hilfe `H-help-02` | 12 Seiten, 15.164 Wörter | 3 | `GUIDE.md`, Wellen `S` und Administration |
| 49 | `ara-SA/H-help-03` | Arabisch | Hilfe `H-help-03` | 13 Seiten, 31.616 Wörter | 4 | `GUIDE.md`, RTL, PDF-Schrift Arab, Wellen `S` und Administration |
| 50 | `tur-TR/H-help-03` | Türkisch | Hilfe `H-help-03` | 13 Seiten, 31.616 Wörter | 2 | `GUIDE.md`, Wellen `S` und Administration |
| 51 | `ukr-UA/H-help-03` | Ukrainisch | Hilfe `H-help-03` | 13 Seiten, 31.616 Wörter | 3 | `GUIDE.md`, Wellen `S` und Administration |
| 52 | `rus-RU/H-help-03` | Russisch | Hilfe `H-help-03` | 13 Seiten, 31.616 Wörter | 3 | `GUIDE.md`, Wellen `S` und Administration |
| 53 | `fas-IR/H-help-03` | Persisch | Hilfe `H-help-03` | 13 Seiten, 31.616 Wörter | 4 | `GUIDE.md`, RTL, PDF-Schrift Arab, Wellen `S` und Administration |
| 54 | `pus-AF/H-help-03` | Paschtu | Hilfe `H-help-03` | 13 Seiten, 31.616 Wörter | 5 | `GUIDE.md`, RTL, PDF-Schrift Arab, Wellen `S` und Administration |
| 55 | `kur-TR/H-help-03` | Kurdisch (Kurmandschi) | Hilfe `H-help-03` | 13 Seiten, 31.616 Wörter | 3 | `GUIDE.md`, Wellen `S` und Administration |
| 56 | `ara-SA/H-help-manual-01` | Arabisch | Hilfe `H-help-manual-01` | 44 Seiten, 17.658 Wörter | 4 | `GUIDE.md`, RTL, PDF-Schrift Arab, Wellen `S` und Administration |
| 57 | `tur-TR/H-help-manual-01` | Türkisch | Hilfe `H-help-manual-01` | 44 Seiten, 17.658 Wörter | 2 | `GUIDE.md`, Wellen `S` und Administration |
| 58 | `ukr-UA/H-help-manual-01` | Ukrainisch | Hilfe `H-help-manual-01` | 44 Seiten, 17.658 Wörter | 3 | `GUIDE.md`, Wellen `S` und Administration |
| 59 | `rus-RU/H-help-manual-01` | Russisch | Hilfe `H-help-manual-01` | 44 Seiten, 17.658 Wörter | 3 | `GUIDE.md`, Wellen `S` und Administration |
| 60 | `fas-IR/H-help-manual-01` | Persisch | Hilfe `H-help-manual-01` | 44 Seiten, 17.658 Wörter | 4 | `GUIDE.md`, RTL, PDF-Schrift Arab, Wellen `S` und Administration |

Die ganze Warteschlange und die Seiten einer Welle: `./console exp:i18n waves --list`, `./console exp:i18n waves --wave=<Welle>`.

Erzeugt mit `./console exp:i18n waves`; `./console exp:i18n waves --check` und der Test `expI18nTranslationWavesTest` schlagen fehl, sobald ein Prozentwert nicht mehr stimmt oder ein Umfang um mehr als 3 % abweicht.
<!-- exp:i18n waves data {"status":{"afr-ZA":[98,100,0,0],"aka-GH":[98,80,0,0],"amh-ET":[98,100,0,0],"ara-SA":[98,80,0,0],"arc-IQ":[98,80,0,0],"asm-IN":[98,80,0,0],"ast-ES":[98,100,0,0],"aze-AZ":[98,80,0,0],"bam-ML":[98,100,0,0],"bel-BY":[98,80,0,0],"ben-BD":[98,100,0,0],"bho-IN":[98,80,0,0],"bod-CN":[98,80,0,0],"bos-BA":[98,80,0,0],"bre-FR":[98,100,0,0],"brx-IN":[98,100,0,0],"bul-BG":[98,100,0,0],"cat-ES":[98,100,0,0],"ceb-PH":[98,80,0,0],"che-RU":[98,80,0,0],"chi-CN":[98,100,0,0],"chi-TW":[98,100,0,0],"ckb-IQ":[98,80,0,0],"cro-HR":[98,80,0,0],"cym-GB":[98,80,0,0],"cze-CZ":[98,80,0,0],"dan-DK":[98,80,0,0],"div-MV":[98,100,0,0],"doi-IN":[98,100,0,0],"dsb-DE":[98,80,0,0],"dut-NL":[98,80,0,0],"dzo-BT":[98,100,0,0],"ell-GR":[98,80,0,0],"eng-AU":[100,100,100,100],"eng-US":[100,100,100,100],"epo-EO":[98,100,0,0],"esl-ES":[98,80,0,0],"esl-MX":[98,80,0,0],"est-EE":[98,80,0,0],"eus-ES":[98,80,0,0],"ewe-GH":[98,100,0,0],"fao-FO":[98,100,0,0],"fas-AF":[98,100,0,0],"fas-IR":[98,80,0,0],"fil-PH":[98,80,0,0],"fin-FI":[98,80,0,0],"fre-FR":[98,80,0,0],"fry-NL":[98,100,0,0],"ful-SN":[98,80,0,0],"ger-DE":[100,76,100,99],"gla-GB":[98,80,0,0],"gle-IE":[98,80,0,0],"glg-ES":[98,80,0,0],"guj-IN":[98,100,0,0],"hau-NG":[98,100,0,0],"haw-US":[98,80,0,0],"heb-IL":[98,80,0,0],"hin-IN":[98,80,0,0],"hsb-DE":[98,100,0,0],"hun-HU":[98,80,0,0],"huy-IL":[98,80,0,0],"hye-AM":[98,80,0,0],"ibo-NG":[98,100,0,0],"ind-ID":[98,80,0,0],"isl-IS":[98,80,0,0],"ita-IT":[98,80,0,0],"jav-ID":[98,80,0,0],"jpn-JP":[98,100,0,0],"kab-DZ":[98,100,0,0],"kal-GL":[98,80,0,0],"kan-IN":[98,100,0,0],"kas-IN":[98,100,0,0],"kat-GE":[98,80,0,0],"kaz-KZ":[98,80,0,0],"khm-KH":[98,80,0,0],"kik-KE":[98,80,0,0],"kin-RW":[98,80,0,0],"kir-KG":[98,80,0,0],"kok-IN":[98,100,0,0],"kor-KP":[98,100,0,0],"kor-KR":[98,100,0,0],"kur-TR":[98,80,0,0],"lao-LA":[98,80,0,0],"lat-VA":[98,80,0,0],"lav-LV":[98,80,0,0],"lin-CD":[98,80,0,0],"lit-LT":[98,100,0,0],"ltz-LU":[98,100,0,0],"lug-UG":[98,80,0,0],"mai-IN":[98,80,0,0],"mal-IN":[98,100,0,0],"mar-IN":[98,100,0,0],"mkd-MK":[98,80,0,0],"mlg-MG":[98,80,0,0],"mlt-MT":[98,80,0,0],"mni-IN":[98,100,0,0],"mon-MN":[98,80,0,0],"mri-NZ":[98,80,0,0],"msa-MY":[98,80,0,0],"mya-MM":[98,80,0,0],"nep-NP":[98,80,0,0],"nor-NO":[98,80,0,0],"nqo-GN":[98,100,0,0],"oci-FR":[98,100,0,0],"ori-IN":[98,100,0,0],"orm-ET":[98,80,0,0],"pan-IN":[98,100,0,0],"pcm-NG":[98,80,0,0],"pnb-PK":[98,80,0,0],"pol-PL":[98,80,0,0],"por-BR":[98,80,0,0],"por-PT":[98,100,0,0],"pus-AF":[98,80,0,0],"que-PE":[98,100,0,0],"roh-CH":[98,100,0,0],"ron-RO":[98,80,0,0],"run-BI":[98,80,0,0],"rus-RU":[98,100,0,0],"san-IN":[98,100,0,0],"sat-IN":[98,100,0,0],"ser-SR":[98,100,0,0],"sin-LK":[98,80,0,0],"slk-SK":[98,80,0,0],"slv-SI":[98,80,0,0],"sme-NO":[98,100,0,0],"sna-ZW":[98,80,0,0],"snd-PK":[98,80,0,0],"som-SO":[98,100,0,0],"sqi-AL":[98,80,0,0],"srp-RS":[98,100,0,0],"sun-ID":[98,80,0,0],"swa-KE":[98,80,0,0],"swe-SE":[98,80,0,0],"syr-IQ":[98,80,0,0],"tam-IN":[98,100,0,0],"tat-RU":[98,80,0,0],"tel-IN":[98,100,0,0],"tgk-TJ":[98,80,0,0],"tha-TH":[98,80,0,0],"tir-ET":[98,80,0,0],"tlh-QO":[98,80,0,0],"ton-TO":[98,100,0,0],"tuk-TM":[98,80,0,0],"tur-TR":[98,80,0,0],"uig-CN":[98,80,0,0],"ukr-UA":[98,100,0,0],"urd-PK":[98,100,0,0],"uzb-UZ":[98,80,0,0],"vie-VN":[98,80,0,0],"wol-SN":[98,100,0,0],"xho-ZA":[98,100,0,0],"yid-US":[98,80,0,0],"yor-NG":[98,100,0,0],"zgh-MA":[98,100,0,0],"zul-ZA":[98,100,0,0]},"sizes":{"messages":19101,"extensions":567,"parts":199,"daily_pages":265,"daily_words":299045,"guides_pages":3683,"guides_words":2879071,"reference_pages":1790,"reference_words":1804872,"tables_pages":0,"tables_words":80642},"waves":31204,"untranslated":["hat-HT","skr-PK","tsn-ZA","dyu-CI","nya-MW","nso-ZA","sot-ZA","mos-BF","azb-IR","kau-NG","vmw-MZ","kon-CD","luy-KE","tso-ZA","umb-AO","luo-KE","sag-CF","grn-PY","bal-PK","kln-KE","lua-CD","suk-TZ"]} -->
<!-- generated: exp:i18n waves (end) -->

## Was die Wellen sind

Ein **Arbeitspaket** ist ein Stück Text des Produkts, das als Ganzes übersetzt wird:

| Arbeitspaket | Was es ist | Umfang einer Welle |
|---|---|---|
| `A01` … `A10` | Ein Teil des Administrationskatalogs `share/translations/<Code>/translation.ts`: seine Kontexte in ihrer Reihenfolge, höchstens 2.000 Texte | bis 2.000 Texte |
| `E` | Die Kataloge der Erweiterungen, die der Kernel mitliefert (`extension/<Name>/translations/<Code>/translation.ts`) | einige hundert Texte |
| `E1` … `E6` | Die Kataloge der übrigen Erweiterungen einer Installation, in Gruppen (siehe [unten](#die-erweiterungen-einer-installation-e1-bis-e6)) | bis 1.900 Texte |
| `L` | Eine neue Locale (`share/locale/<Code>.ini`) oder ein aus einem vollständigen abgeleiteter Katalog (Dari, Nordkoreanisch) | eine Datei, oder eine Kopie und ihr Glossar |
| `S` | Die Einrichtung der Hilfe in einer Sprache: ihr Verzeichnis unter `doc/`, ihre Einstellungen, Schreibrichtung und Schriften | wenige Einstellungen |
| `H-…` | Ein Teil der Hilfe: ein Abschnitt von `doc/eng-us/`, in Teile von höchstens 100 Seiten und etwa 30.000 Wörtern geschnitten | bis 100 Seiten |
| `X01` … | Ein Teil der Tabellen, aus denen die Referenzbeispiele erzeugt werden (`doc/<Sprache>/.examples/`) | bis 30.000 Wörter |

Eine **Welle** ist ein Arbeitspaket für eine Sprache, benannt `<Locale>/<Arbeitspaket>`: `ara-SA/H-help-manual-01` ist der
erste Teil des Benutzerhandbuchs auf Arabisch. Der Befehl zeigt, was eine Welle umfasst und was davon fertig ist:

```bash
./console exp:i18n waves --wave=ara-SA/H-help-manual-01    # die Seiten, + fertig, - offen
./console exp:i18n waves --wave=A03                         # die Kontexte von Teil A03
./console exp:i18n waves --list                             # die ganze Warteschlange
```

**Die Hilfe in Stufen.** Die Abschnitte der Hilfe sind danach gruppiert, wer sie jeden Tag braucht:

- **Alltag**: die Seiten des Hilfecenters (`help/*.md`: erste Schritte, Anmelden, Inhalte bearbeiten, Medien, Suchen,
  Tastenkürzel), das Benutzerhandbuch (`help/manual/`), die Leitfäden in einfacher Sprache
  (`help/non-technical-people/`), die Anleitungen für Administratoren (`guides/`) und die beiden Lernbücher
  (`help/book-learning/`, `help/book-building-a-site/`). Sie kommen in jeder Sprache zuerst.
- **Produkt- und Zielgruppen-Leitfäden**: Installation, die Beschreibungen der Funktionen und die Leitfäden für jede
  Zielgruppe des Hilfecenters (Verwaltung, Unternehmen, Trainer, Designer, Hosting und die übrigen).
- **Entwicklerreferenz**: Erweiterungsentwicklung, Template-Sprache und Operatoren, Rückwärtskompatibilität,
  Standards, Spezifikationen, Geschichte und die handgeschriebenen Beispiele.
- **Tabellen der erzeugten Beispiele**: die mehr als zweitausend Referenzseiten werden aus dem Code erzeugt
  (`exp:help:examples`); eine Sprache übersetzt ihre Tabellen, nicht die Seiten (siehe
  [die Beispiele](beispiele/README.md#so-entstehen-die-generierten-seiten)).

## Die Reihenfolge der Wellen

Die Warteschlange wird berechnet, nicht von Hand gewählt. Sie läuft in acht Phasen; innerhalb einer Phase kommt eine
Sprache mit mehr Sprechern je Schwierigkeitspunkt zuerst:

1. **Die Kataloge der Prioritätsgruppe** unter 100 %: die Teile der Administration und die mitgelieferten
   Erweiterungen von Deutsch, Arabisch, Türkisch, Ukrainisch, Russisch, Persisch, Paschtu und Kurdisch (Kurmandschi),
   und der Dari-Katalog (`fas-AF`, siehe [abgeleitete Kataloge](#abgeleitete-kataloge-dari-und-nordkoreanisch)).
2. **Deutsch: die Hilfe des Alltags**, soweit sie noch nicht übersetzt ist (Deutsch hat fast die ganze Hilfe).
3. **Die Hilfe des Alltags der Prioritätsgruppe**: zuerst die Einrichtung der Hilfe jeder Sprache (`S`), dann Teil
   für Teil, eine Sprache nach der anderen: jede Sprache bekommt die ersten Schritte, bevor eine Sprache das Handbuch
   bekommt. Die Reihenfolge ist Arabisch (das auch den syrischen Kindern dient), Türkisch, Ukrainisch, Russisch,
   Persisch, Paschtu, Kurdisch.
4. **Neue Übersetzungen der Administration**: die Prüfung der abgeleiteten Kataloge, Nordkoreanisch, dann jede
   Sprache ohne Übersetzung, darunter die Sprachen mit fünf Millionen Sprechern oder mehr ohne Locale (ihre Locale
   zuerst, Welle `L`). Die Sprachen, die auf einem Branch übersetzt werden, warten, bis er zusammengeführt ist.
5. **Die mitgelieferten Erweiterungen** jeder anderen Übersetzung unter 100 %.
6. **Die Hilfe des Alltags jeder anderen Übersetzung**, wie in Phase 3.
7. **Die übrige Hilfe der Prioritätsgruppe**: Leitfäden, Referenz und Tabellen, Deutsch zuerst.
8. **Die übrige Hilfe jeder anderen Übersetzung.**

**Die Prioritätsgruppe** sind die häufigsten Herkunftssprachen geflüchteter Kinder in Hamburg: Deutsch, Englisch,
Arabisch (syrisches Arabisch liest Hocharabisch, `ara-SA`), Türkisch, Ukrainisch, Russisch, Persisch (Farsi `fas-IR`,
Dari `fas-AF`), Paschtu und Kurdisch (Kurmandschi, `kur-TR`). Ihre Administration ist vollständig; es fehlen die
Hilfe und einige Erweiterungen, daher kommt ihre Hilfe vor den neuen Sprachen.

## Vor einer Welle: die Voraussetzungen

Lesen Sie diese vor der ersten Welle einer Sprache, und wieder, wenn eine Welle etwas Neues berührt:

- **`share/translations/TRANSLATING.md`**: die Regeln, die alle Sprachen teilen (Platzhalter, Markup, Leerraum, was
  nicht übersetzt wird, Wörter, die ihren Kontext brauchen, die Formen der Oberfläche).
- **Die `GUIDE.md` der Sprache** neben ihrem Katalog (`share/translations/<Code>/GUIDE.md`): Register, Anrede, Schrift
  und Typografie und das **Glossar**, das für den Kernel, jede Erweiterung und die Hilfe verbindlich ist. Eine neue
  Sprache schreibt ihre `GUIDE.md` in ihrer ersten Welle (`A01`), vor dem ersten Text; ein Begriff ändert sich zuerst
  im Leitfaden. Die Hilfe benutzt dasselbe Glossar wie die Administration, sodass eine Schaltfläche auf einer Seite so
  heißt wie auf dem Bildschirm.
- **Von rechts nach links** (Arabisch, Persisch, Paschtu, Urdu, Hebräisch, Jiddisch, Sindhi, Uigurisch, Sorani,
  Dhivehi, Syrisch, N’Ko): die Locale hat `TextDirection=rtl`, und die Hilfe zeigt eine Sprache von rechts nach
  links, wenn ihr Code in `expHelpLanguage::RTL_LANGUAGES` steht (in Welle `S` prüfen; Syrisch und N’Ko stehen noch
  nicht darin). Siehe [Sprachen, die von rechts nach links geschrieben werden](sprachen.md#9-sprachen-die-von-rechts-nach-links-geschrieben-werden).
- **Schriften**: die Administration benutzt die Systemschriften des Lesers; der PDF-Export der Hilfe bettet die Schrift
  aus `exphelp.ini [PdfSettings] FontFiles[]` ein (DejaVu Sans), die Lateinisch, Kyrillisch, Griechisch, Armenisch,
  Georgisch und Hebräisch zeichnet. Für jede andere Schrift fügt Welle `S` eine Schrift hinzu, die sie hat (eine
  Noto-Schrift), bevor die erste Seite exportiert wird; Schriften mit komplexer Formung (Arabisch, die indischen
  Schriften, Thai, Khmer, Birmanisch, Tibetisch) werden an einer gedruckten Seite geprüft, nicht nur am Bildschirm.
  Siehe [Schriften, Fonts und Zeilenumbruch](sprachen.md#schriften-fonts-und-zeilenumbruch).
- **Die Locale**: eine Sprache braucht ihre `share/locale/<Code>.ini`, bevor ihr Katalog gewählt werden kann (Welle
  `L`, siehe [Eine Locale hinzufügen](sprachen.md#7-eine-locale-hinzufügen)).

## Wie jede Art von Welle gemacht wird

Jede Welle ist ein Branch mit einem Commit (oder wenigen), benannt nach der Welle
(`i18n-wave-ara-SA-H-help-manual-01`), damit nicht zwei Übersetzer dieselbe Welle nehmen: sehen Sie zuerst die offenen
Branches an. Beginnen Sie mit einem aktuellen `main`.

### Teil der Administration (A01 bis A10)

1. `./console exp:i18n waves --wave=A03` listet die Kontexte des Teils.
2. Eine neue Sprache: legen Sie `share/translations/<Code>/` aus `share/translations/untranslated/translation.ts` an
   (das Attribut `language` von `<TS>` ändern), schreiben Sie die `GUIDE.md` in `A01`. Eine vorhandene: mit
   `bin/linux/ezlupdate <Code>` auffrischen
   ([Abschnitt 8 der Sprachenseite](sprachen.md#8-eine-übersetzung-der-administrationsoberfläche-hinzufügen-oder-vervollständigen)).
3. Übersetzen Sie jede Nachricht der Kontexte des Teils: Text in `<translation>`, `type="unfinished"` entfernt. Jedes
   `<source>` bleibt genau, wie es ist, Byte für Byte (siehe [Fallen](#fallen)).
4. Messen: `php bin/php/ezchecktranslation.php <Code> --allow-root-user`, und `./console exp:i18n waves --check`
   nennt den Teil, wenn sich sein Prozentwert geändert hat.
5. Eine Sprache, die in jedem Teil 100 % erreicht, kommt auf die Liste von
   `tests/tests/lib/ezi18n/expTranslationQualityTest.php` und in die Einstellungen einer neuen Übersetzung
   ([Abschnitt 8](sprachen.md#8-eine-übersetzung-der-administrationsoberfläche-hinzufügen-oder-vervollständigen)).

### Mitgelieferte Erweiterungen (E)

Die Kataloge von `ezoe`, `ezjscore` und `expimageeditor` (die Erweiterungen mit einem Katalog im Manifest des
Kernels). Ein fehlender Katalog wird aus `translations/untranslated/translation.ts` der Erweiterung angelegt; dann wie
oben, mit den Kontextnamen der Erweiterung. `ezoe` und `ezjscore` stehen auch im Qualitätstest.

### Die Erweiterungen einer Installation (E1 bis E6)

Die übrigen Erweiterungen erscheinen aus ihren eigenen Repositories, daher werden ihre Kataloge an einer Installation
gemessen, nicht hier: `./console exp:i18n fallbacks --locale=<Code>` listet die Abdeckung jeder Erweiterung. Auf der
Referenzinstallation (38 Erweiterungen mit Katalogen, 9.060 Texte, gemessen am 26. Oktober 2026) hatten Kroatisch,
Tschechisch, Dänisch, Niederländisch, Italienisch, Norwegisch, Polnisch, Portugiesisch (Brasilien), Schwedisch und
Türkisch 98 % davon, Spanisch und Hebräisch 96 %, Deutsch, mexikanisches Spanisch, Latein und Klingonisch 94 %, jede
andere Übersetzung zwischen 28 und 35 %, Jiddisch 5 %. Die Gruppen, in dieser Reihenfolge nach der Hilfe des Alltags
einer Sprache:

| Gruppe | Erweiterungen | Texte |
|---|---|---:|
| `E1` Bearbeiten | ezoe, exp_oe_tiptap, expimageeditor, eztags, ezmultiupload, ezautosave, ezwt, expui, ezownerchange, expchangeclass, enhancedezbinaryfile, enhancedselection2, ezstarrating, ezgmaplocation, birthday, recaptcha, powercontent, ngclasslist, exp_enhanced_link, ezjscore | 1.628 |
| `E2` Layouts | explayouts, explayouts_ui, explayouts_ui_api, explayouts_content_browser_ui | 1.177 |
| `E3` Werkzeuge der Administration | ezupdate, git_manager, sevenx_authentication_2fa, expqueue, xrowmetadata, bccie, ezodf, sevenx_dse, bcwebsitestatistics | 1.294 |
| `E4` Site-Designs | ezwebin, sevenx_themes_media, sevenx_themes_simple | 1.434 |
| `E5` Newsletter | cjw_newsletter | 1.822 |
| `E6` Datenexport | xrowextract | 1.705 |

Jede Gruppe ist ein Commit im Repository jeder Erweiterung und eine neue Version dieser Erweiterung, mit erhöhter
Versionsnummer wie bei jeder Version einer Erweiterung.

### Neue Locale (L)

Eine Sprache ohne `share/locale/<Code>.ini` bekommt sie zuerst, wie [Eine Locale hinzufügen](sprachen.md#7-eine-locale-hinzufügen)
beschreibt: aus CLDR erzeugt, wo CLDR die Sprache hat, sonst von Hand geschrieben und von einem Sprecher geprüft (die
meisten afrikanischen Sprachen sowie Haitianisch und Guaraní haben im ICU des Generators keine CLDR-Daten, siehe
[Verbreitete Sprachen, die noch fehlen](sprachen.md#6-verbreitete-sprachen-die-noch-fehlen)). Ihre Prüfung,
`expLocaleFilesTest`, muss bestehen. Der Code in der Tabelle [Sprachen ohne Übersetzung](#sprachen-ohne-übersetzung)
ist ein Vorschlag: nehmen Sie den ISO-639-2-Code, den die übrigen Locales benutzen, und das Land mit den meisten Lesern.

### Abgeleitete Kataloge: Dari und Nordkoreanisch

Zwei Sprachen stehen einem vollständigen Katalog so nahe, dass die erste Welle ihn kopiert und nur ändert, was
abweicht. Welle `L` macht den Katalog sofort benutzbar; die Prüfwellen (`A01` bis `A10`, als „prüfen“ markiert) lesen
ihn Teil für Teil gegen das Glossar.

- **Dari** (`fas-AF`, das Persisch Afghanistans): die Locale gibt es, sie fällt auf iranisches Persisch (`fas-IR`)
  zurück. Welle `L` kopiert `share/translations/fas-IR/` nach `fas-AF/`, schreibt in deren `GUIDE.md` die Begriffe,
  in denen Dari abweicht (Wortschatz, die in Afghanistan gebrauchten Namen der Sonnenmonate, der Name der Sprache,
  دری), wendet sie an und entfernt die Fallback-Zeile von `fas-AF` aus `settings/i18n.ini`. Ein Leser aus Afghanistan
  prüft das Ergebnis.
- **Nordkoreanisch** (`kor-KP`, Munhwaŏ, der Standard der Demokratischen Volksrepublik Korea): eine neue, aus `kor-KR`
  abgeleitete Locale. Die Unterschiede sind systematisch und gehören deshalb in die `GUIDE.md`, bevor sich ein Text
  ändert: keine Anlautregel (리용, 련결, 로동, wo der Süden 이용, 연결, 노동 schreibt); andere Getrennt- und
  Zusammenschreibung, Komposita zusammen; das Zwischen-ㅅ wird nicht geschrieben; Lehnwörter in der nördlichen Form oder
  ein eigenes Wort (콤퓨터, 프로그람, 화일, 차림표 für Computer, Programm, Datei, Menü); der Name der Sprache 조선말 und des
  Landes 조선민주주의인민공화국, nie die südlichen Namen; eine andere alphabetische Reihenfolge der Buchstaben, der die
  Sortierung der Locale folgen muss. Die Locale-Datei (`share/locale/kor-KP.ini`, `ContentLanguage=ko-KP`, der
  nordkoreanische Won) kommt aus CLDR. Ein Sprecher des nördlichen Standards prüft das Ergebnis.

### Eine Sprache der Hilfe einrichten (S)

1. Legen Sie das Verzeichnis `doc/<Locale in Kleinbuchstaben>/` an (`doc/ara-sa/`) und tragen Sie es in
   `exphelp.ini [DocLanguages] Directories[]` und `Names[]` ein; der Name wird im Kontext `kernel/exphelp` der Kataloge
   übersetzt. Andere Locales der Sprache lesen es ohne Eintrag in `Map[]` (`ara-EG` liest `doc/ara-sa/`).
2. Behalten Sie die englischen Dateinamen: eine Seite unter demselben relativen Pfad braucht keine `.paths.json`.
   Übersetzte Dateinamen (wie im Deutschen) sind möglich, kosten aber jede spätere Welle einen Eintrag; in einer
   anderen als der lateinischen Schrift sind sie nicht zu empfehlen.
3. Prüfen Sie die Schreibrichtung (`expHelpLanguage::RTL_LANGUAGES`) und die PDF-Schrift, wie in den
   [Voraussetzungen](#vor-einer-welle-die-voraussetzungen).
4. Ablauf und Einstellungen stehen in [Übersetzte Dokumentation](bc/6.0/uebersetzte-dokumentation.md#eine-übersetzung-hinzufügen).

### Teil der Hilfe (H-…)

1. `./console exp:i18n waves --wave=<Locale>/<Teil>` listet die Seiten des Teils, `-` für die noch offenen.
2. Kopieren Sie jede offene Seite aus `doc/eng-us/` unter denselben Pfad im Verzeichnis der Sprache und übersetzen Sie
   sie: den Text, die Überschriften, die Linktexte und die Bildbeschreibungen. Die Schlüssel der Front Matter,
   Codeblöcke, Befehle, Einstellungen, Dateinamen und in Code geschriebene Anker bleiben, wie sie sind; das
   `summary:` wird übersetzt. Links behalten ihre Ziele: ein Link auf eine noch nicht übersetzte Seite zeigt die
   englische Seite.
3. Benutzen Sie das Glossar: eine Schaltfläche, ein Menü oder eine Einstellung heißt auf der Seite so, wie die
   Administration sie in dieser Sprache nennt.
4. Committen Sie nur die Seiten des Teils: die Hilfe zeigt nur Seiten, die git verfolgt.

### Tabellen der erzeugten Beispiele (X…)

Die Referenzseiten erzeugt `php bin/php/exphelpexamples.php` aus Tabellen in `doc/<Sprache>/.examples/`
(`strings.json` und `descriptions/*.json`). Eine Welle übersetzt die Einträge ihrer Dateien:
`./console exp:help:examples --language=<Verzeichnis> --worklist=var/tmp/todo.json` schreibt die offenen, danach
erzeugt der Befehl die Seiten, und `--check` muss bestehen. Das Verfahren beschreiben
[die Beispiele](beispiele/README.md#so-entstehen-die-generierten-seiten).

## Die Prüfungen nach jeder Welle

```bash
# die Regeln der Kataloge (Platzhalter, Markup, Leerraum, nicht leer, nicht Englisch)
vendor/bin/phpunit --no-coverage tests/tests/lib/ezi18n/expTranslationQualityTest.php
# der fertige Anteil eines Katalogs
php bin/php/ezchecktranslation.php <Code> --allow-root-user
# die Links und Bilder der Hilfe, unterhalb des Abschnitts des Teils
./console exp:help check --below=<Abschnitt>/
# die erzeugten Beispiele sind aktuell
php bin/php/exphelpexamples.php --check --allow-root-user
# diese Seite: die gezeigten Prozentwerte sind die gemessenen
./console exp:i18n waves --check
# das Manifest der mitgelieferten Dateien, vor jedem Commit
php bin/php/checkmanifest.php --fix
```

Lesen Sie danach das Ergebnis im laufenden Produkt: die Administration mit einer Benutzereinstellung oder einem
Siteaccess `Locale=<Code>` nach dem Leeren des Übersetzungscaches
(`php bin/php/ezcache.php --clear-tag=i18n --allow-root-user`), und die übersetzten Seiten im Hilfecenter in dieser
Sprache. Nach einer Welle führen Sie `./console exp:i18n waves` aus und committen die aktualisierte Seite mit der
Welle, damit die Warteschlange die nächste zeigt.

## Fallen

- **Eine Quelle mit einem Qt-Byte-Element.** Ein Quelltext des Administrationskatalogs enthält einen als
  `<byte value="x19"/>` gespeicherten Apostroph in `<source>`. Kopieren Sie jedes `<source>`-Element unverändert,
  tippen oder „reparieren“ Sie es nie neu: der Übersetzer findet eine Nachricht an ihrer genauen Quelle, und eine
  geänderte Quelle ist eine neue, unübersetzte Nachricht.
- **PHP und Python zählen verschieden.** Ein Python-Skript, das `<source>` mit `.text` von ElementTree liest, bricht an
  diesem Byte-Element ab, und eines, das `type="vanished"` oder leere Übersetzungen nicht überspringt, zählt sie als
  fertig. Die Zahlen dieser Seite, der Sprachenseite und der Sprachumschaltung der Oberfläche kommen vom PHP-Leser
  (`expUISwitchLanguageCatalogue::messageKeys()`: Kontext, Quelle und Kommentar; unfertige, veraltete und leere
  Übersetzungen nicht gezählt; Prozente abgerundet). Weichen zwei Zählungen ab, hat die PHP-Zählung recht.
- **Ein Worktree mit verlinktem vendor-Verzeichnis.** In einem git-Worktree, dessen `vendor/` ein symbolischer Link auf
  einen anderen Checkout ist, lädt der Autoloader von Composer die Kernel-Klassen jenes Checkouts, sodass die Tests den
  anderen Code testen und trotzdem bestehen. Geben Sie dem Worktree ein eigenes `vendor/composer/` und
  `vendor/autoload.php` (Kopien; die Pakete dürfen Links bleiben) und prüfen Sie, dass der Testlauf Tests und
  Assertions meldet, nicht „No tests executed“.
- **Das Manifest.** `share/filelist.md5` listet jede mitgelieferte Datei mit ihrer Prüfsumme; die
  Aktualisierungsprüfung meldet jede Datei, die davon abweicht. Führen Sie vor jedem Commit, nach der letzten Änderung,
  `php bin/php/checkmanifest.php --fix` aus.
- **Platzhalter und Markup.** `%1`, `%name`, `<b>`, `&nbsp;` bleiben genau so; an einen Platzhalter wird nichts
  angehängt (`TRANSLATING.md`, Regel 1). Der Qualitätstest fängt das meiste davon, nicht alles.
- **Der Übersetzungscache.** Ein geänderter Katalog zeigt sich erst nach `php bin/php/ezcache.php --clear-tag=i18n`.
- **Übersetzte Dateinamen in der Hilfe** brauchen einen Eintrag in `.paths.json`; zwei Übersetzer, die beide Einträge
  hinzufügen, führen sie mit `./console exp:help maps --language=<Verzeichnis> --fix` zusammen.
- **Der erzeugte Teil dieser Seite** wird nie von Hand bearbeitet; `./console exp:i18n waves` schreibt ihn.

## Fertig: täglicher Gebrauch und Ziel

- **Eine Welle ist fertig**, wenn ihr Arbeitspaket in dieser Sprache bei 100 % steht, die Prüfungen oben bestehen und
  das Ergebnis im laufenden Produkt gelesen wurde.
- **Eine Sprache ist im täglichen Gebrauch**, wenn ihre Administration, ihre Erweiterungen und ihre Hilfe des Alltags
  je mindestens **97 %** erreichen: was übrig bleibt, ist so selten, dass ein Leser an einem normalen Arbeitstag nicht
  auf Englisch trifft.
- **Das Ziel sind 100 %** in jedem Arbeitspaket, die ganze Hilfe eingeschlossen.

## Sprecher und Schwierigkeit

Die Sprecherzahlen sind Sprecher insgesamt, Erst- und Zweitsprache, in Millionen, gerundet, nach Ethnologue, wie
Wikipedias „List of languages by total number of speakers“ und die Artikel der Sprachen sie angeben (Stand 2025). Sie
ordnen die Warteschlange; sie sind keine Volkszählung, und eine bessere Zahl ist als Änderung von
`expI18nTranslationWaves::LANGUAGES` willkommen.

Die **Schwierigkeit** (1 bis 5) einer Sprache ist 1, plus 1 für eine andere als die lateinische Schrift (2 für eine
Schrift mit komplexer Formung oder ohne Leerzeichen zwischen den Wörtern: die indischen Schriften, Thai, Laotisch,
Khmer, Birmanisch, Tibetisch, Ol Chiki), plus 1 für rechts nach links, plus 1 für reiche Flexion oder Agglutination,
plus 1 oder 2, wenn es in der Sprache wenig Software-Terminologie gibt, höchstens 5. Die Warteschlange ordnet nach
Sprechern je Schwierigkeitspunkt.

Sprachen, die in einem anderen Standard geschrieben werden, werden von ihm bedient und bekommen keine eigenen Wellen:
Schweizerdeutsch und Bairisch (Deutsch), Neapolitanisch und Sizilianisch (Italienisch), die gesprochenen Varietäten
des Arabischen (Hocharabisch), Kantonesisch und Wu (geschriebenes Chinesisch), die Regionalsprachen Indiens, die auf
Hindi oder Bengalisch geschrieben werden.

## Diese Seite aktuell halten

`./console exp:i18n waves` misst neu und schreibt den erzeugten Teil dieser Seite und ihres englischen Originals; das
Datum bleibt, solange sich nichts ändert. Diese Seite selbst zählt nicht zu den Seiten der Hilfe. `./console exp:i18n waves --check` und der Test
`tests/tests/kernel/classes/i18n/expI18nTranslationWavesTest.php` (ohne Datenbank) schlagen fehl, wenn sich ein
Prozentwert einer Sprache geändert hat, eine Sprache ohne Übersetzung hinzugekommen ist oder eine bekommen hat, oder ein
Umfang mehr als 3 % vom gemessenen abweicht, sodass eine kleine Änderung an einer Hilfeseite nicht jede andere Änderung
scheitern lässt, der Plan aber nicht veralten kann.
