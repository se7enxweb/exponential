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

<!-- generated: exp:i18n waves 2026-11-03 (start) -->
**Gemessen am 3. November 2026** mit `./console exp:i18n waves` aus den Dateien dieser Version (ohne Datenbank):

| Was | Umfang |
|---|---:|
| Administration: Texte in `share/translations/untranslated/translation.ts`, in 11 Teilen | **20.855** (140.414 Wörter) |
| Mitgelieferte Erweiterungen (`expimageeditor`, `ezjscore`, `ezoe`) | **567** Texte |
| Hilfe: Seiten unter `doc/eng-us/` (ohne die erzeugten Beispielseiten) | **5.963** Seiten, 5.332.621 Wörter |
| Hilfe: Tabellen der erzeugten Beispielseiten (`.examples/`) | **4.186** Einträge, 112.725 Wörter |
| Übersetzungen der Administration, davon vollständig | **156**, 156 |
| davon im täglichen Gebrauch (Administration, Erweiterungen und Hilfe des Alltags ≥ 97 %) | **2** |
| Sprachen ohne Übersetzung der Administration ([Liste](#sprachen-ohne-übersetzung)), davon auf einem Branch in Arbeit | **41**, 0 |
| Wellen bis 100 % in jeder Sprache | **33.675** |

### Wo jede Sprache steht

Prozent der Texte (Administration, Erweiterungen) und der Wörter (Hilfe), abgerundet. **Fett**: unter 97 %. „Hilfe des Alltags“ sind die Teile für Redakteure, Benutzer und Administratoren ([Stufen](#die-hilfe-in-stufen)).

| Sprache | Code | Sprecher (Mio.) | Schwierigkeit | Administration | Erweiterungen | Hilfe des Alltags | Hilfe gesamt | Wellen offen | Nächste Welle |
|---|---|---:|:-:|---:|---:|---:|---:|---:|---|
| Afrikaans | `afr-ZA` | 17 | 2 | 100 | 100 | **0** | **0** | 217 | #648 `S` |
| Akan | `aka-GH` | 11 | 3 | 100 | 100 | **0** | **0** | 217 | #673 `S` |
| Albanisch | `sqi-AL` | 8 | 2 | 100 | 100 | **0** | **0** | 217 | #672 `S` |
| Amharisch | `amh-ET` | 60 | 4 | 100 | 100 | **0** | **0** | 217 | #632 `S` |
| Arabisch | `ara-SA` | 400 | 4 | 100 | 100 | **0** | **0** | 217 | #2 `S` |
| Aramäisch | `arc-IQ` | < 1 | 5 | 100 | 100 | **0** | **0** | 217 | #727 `S` |
| Armenisch | `hye-AM` | 5 | 4 | 100 | 100 | **0** | **0** | 217 | #696 `S` |
| Aserbaidschanisch | `aze-AZ` | 10 | 3 | 100 | 100 | **0** | **0** | 217 | #675 `S` |
| Assamesisch | `asm-IN` | 15 | 5 | 100 | 100 | **0** | **0** | 217 | #677 `S` |
| Asturisch | `ast-ES` | < 1 | 3 | 100 | 100 | **0** | **0** | 217 | #725 `S` |
| Bambara | `bam-ML` | 14 | 3 | 100 | 100 | **0** | **0** | 217 | #664 `S` |
| Baskisch | `eus-ES` | < 1 | 3 | 100 | 100 | **0** | **0** | 217 | #718 `S` |
| Belarussisch | `bel-BY` | 5 | 4 | 100 | 100 | **0** | **0** | 217 | #697 `S` |
| Bengalisch | `ben-BD` | 285 | 4 | 100 | 100 | **0** | **0** | 217 | #609 `S` |
| Bhojpuri | `bho-IN` | 52 | 5 | 100 | 100 | **0** | **0** | 217 | #639 `S` |
| Birmanisch | `mya-MM` | 43 | 5 | 100 | 100 | **0** | **0** | 217 | #647 `S` |
| Bodo | `brx-IN` | 2 | 5 | 100 | 100 | **0** | **0** | 217 | #716 `S` |
| Bosnisch | `bos-BA` | 3 | 3 | 100 | 100 | **0** | **0** | 217 | #702 `S` |
| Bretonisch | `bre-FR` | < 1 | 4 | 100 | 100 | **0** | **0** | 217 | #732 `S` |
| Bulgarisch | `bul-BG` | 8 | 2 | 100 | 100 | **0** | **0** | 217 | #668 `S` |
| Cebuano | `ceb-PH` | 28 | 3 | 100 | 100 | **0** | **0** | 217 | #643 `S` |
| Chinesisch (traditionell) | `chi-TW` | 31 | 2 | 100 | 100 | **0** | **0** | 217 | #628 `S` |
| Chinesisch (vereinfacht) | `chi-CN` | 1.100 | 2 | 100 | 100 | **0** | **0** | 217 | #601 `S` |
| Dänisch | `dan-DK` | 6 | 1 | 100 | 100 | **0** | **0** | 217 | #659 `S` |
| Dari (Persisch in Afghanistan) | `fas-AF` | 35 | 4 | 100 | 100 | **0** | **0** | 217 | #645 `S` |
| Deutsch | `ger-DE` | 135 | 2 | 100 | **76** | 100 | 100 | 1 | #1 `E` |
| Dhivehi | `div-MV` | < 1 | 5 | 100 | 100 | **0** | **0** | 217 | #729 `S` |
| Dogri | `doi-IN` | 3 | 5 | 100 | 100 | **0** | **0** | 217 | #710 `S` |
| Dzongkha | `dzo-BT` | < 1 | 5 | 100 | 100 | **0** | **0** | 217 | #724 `S` |
| Englisch (Australien) | `eng-AU` | 1.500 | 1 | 100 | 100 | 100 | 100 | 0 | Quelltexte |
| Englisch (USA) | `eng-US` | 1.500 | 1 | 100 | 100 | 100 | 100 | 0 | Quelltexte |
| Esperanto | `epo-EO` | < 1 | 2 | 100 | 100 | **0** | **0** | 217 | #733 `S` |
| Estnisch | `est-EE` | 1 | 3 | 100 | 100 | **0** | **0** | 217 | #713 `S` |
| Ewe | `ewe-GH` | 5 | 3 | 100 | 100 | **0** | **0** | 217 | #691 `S` |
| Färöisch | `fao-FO` | < 1 | 4 | 100 | 100 | **0** | **0** | 217 | #736 `S` |
| Filipino | `fil-PH` | 83 | 2 | 100 | 100 | **0** | **0** | 217 | #614 `S` |
| Finnisch | `fin-FI` | 6 | 2 | 100 | 100 | **0** | **0** | 217 | #679 `S` |
| Französisch | `fre-FR` | 310 | 1 | 100 | 100 | **0** | **0** | 217 | #603 `S` |
| Fulfulde | `ful-SN` | 37 | 4 | 100 | 100 | **0** | **0** | 217 | #644 `S` |
| Galicisch | `glg-ES` | 2 | 2 | 100 | 100 | **0** | **0** | 217 | #700 `S` |
| Georgisch | `kat-GE` | 4 | 4 | 100 | 100 | **0** | **0** | 217 | #706 `S` |
| Griechisch | `ell-GR` | 13 | 3 | 100 | 100 | **0** | **0** | 217 | #667 `S` |
| Grönländisch | `kal-GL` | < 1 | 4 | 100 | 100 | **0** | **0** | 217 | #737 `S` |
| Guaraní | `grn-PY` | 7 | 4 | 100 | **80** | **0** | **0** | 218 | #600 `E` |
| Gujarati | `guj-IN` | 62 | 4 | 100 | 100 | **0** | **0** | 217 | #629 `S` |
| Hausa | `hau-NG` | 94 | 3 | 100 | 100 | **0** | **0** | 217 | #617 `S` |
| Hawaiisch | `haw-US` | < 1 | 3 | 100 | 100 | **0** | **0** | 217 | #739 `S` |
| Hebräisch | `heb-IL` | 9 | 4 | 100 | 100 | **0** | **0** | 217 | #684 `S` |
| Hindi | `hin-IN` | 610 | 4 | 100 | 100 | **0** | **0** | 217 | #606 `S` |
| Hulaulá (jüdisches Neuaramäisch) | `huy-IL` | < 1 | 5 | 100 | 100 | **0** | **0** | 217 | #743 `S` |
| Igbo | `ibo-NG` | 31 | 3 | 100 | 100 | **0** | **0** | 217 | #640 `S` |
| Indonesisch | `ind-ID` | 252 | 1 | 100 | 100 | **0** | **0** | 217 | #604 `S` |
| Irisch | `gle-IE` | 2 | 4 | 100 | 100 | **0** | **0** | 217 | #711 `S` |
| isiXhosa | `xho-ZA` | 19 | 4 | 100 | 100 | **0** | **0** | 217 | #663 `S` |
| isiZulu | `zul-ZA` | 28 | 4 | 100 | 100 | **0** | **0** | 217 | #655 `S` |
| Isländisch | `isl-IS` | < 1 | 3 | 100 | 100 | **0** | **0** | 217 | #726 `S` |
| Italienisch | `ita-IT` | 68 | 1 | 100 | 100 | **0** | **0** | 217 | #610 `S` |
| Japanisch | `jpn-JP` | 123 | 3 | 100 | 100 | **0** | **0** | 217 | #615 `S` |
| Javanisch | `jav-ID` | 68 | 3 | 100 | 100 | **0** | **0** | 217 | #623 `S` |
| Jiddisch | `yid-US` | 1 | 5 | 100 | 100 | **0** | **0** | 217 | #720 `S` |
| Kabylisch | `kab-DZ` | 5 | 4 | 100 | 100 | **0** | **0** | 217 | #698 `S` |
| Kannada | `kan-IN` | 59 | 5 | 100 | 100 | **0** | **0** | 217 | #635 `S` |
| Kasachisch | `kaz-KZ` | 16 | 4 | 100 | 100 | **0** | **0** | 217 | #669 `S` |
| Kaschmiri | `kas-IN` | 7 | 5 | 100 | 100 | **0** | **0** | 217 | #695 `S` |
| Katalanisch | `cat-ES` | 10 | 1 | 100 | 100 | **0** | **0** | 217 | #641 `S` |
| Khmer | `khm-KH` | 18 | 4 | 100 | 100 | **0** | **0** | 217 | #665 `S` |
| Kikuyu | `kik-KE` | 8 | 4 | 100 | 100 | **0** | **0** | 217 | #687 `S` |
| Kinyarwanda | `kin-RW` | 15 | 4 | 100 | 100 | **0** | **0** | 217 | #670 `S` |
| Kirgisisch | `kir-KG` | 5 | 5 | 100 | 100 | **0** | **0** | 217 | #703 `S` |
| Kirundi | `run-BI` | 12 | 4 | 100 | 100 | **0** | **0** | 217 | #678 `S` |
| Klingonisch | `tlh-QO` | < 1 | 4 | 100 | 100 | **0** | **0** | 217 | #746 `S` |
| Konkani | `kok-IN` | 2 | 5 | 100 | 100 | **0** | **0** | 217 | #712 `S` |
| Koreanisch | `kor-KR` | 82 | 3 | 100 | 100 | **0** | **0** | 217 | #618 `S` |
| Koreanisch (Nordkorea, Munhwaŏ) | `kor-KP` | 26 | 3 | 100 | 100 | **0** | **0** | 217 | #646 `S` |
| Kroatisch | `cro-HR` | 5 | 2 | 100 | 100 | **0** | **0** | 217 | #682 `S` |
| Kurdisch (Kurmandschi) | `kur-TR` | 16 | 3 | 100 | 100 | **0** | **0** | 217 | #8 `S` |
| Laotisch | `lao-LA` | 30 | 5 | 100 | 100 | **0** | **0** | 217 | #660 `S` |
| Latein | `lat-VA` | < 1 | 4 | 100 | 100 | **0** | **0** | 217 | #744 `S` |
| Lettisch | `lav-LV` | 2 | 3 | 100 | 100 | **0** | **0** | 217 | #709 `S` |
| Lingala | `lin-CD` | 40 | 4 | 100 | 100 | **0** | **0** | 217 | #642 `S` |
| Litauisch | `lit-LT` | 3 | 3 | 100 | 100 | **0** | **0** | 217 | #704 `S` |
| Luganda | `lug-UG` | 11 | 4 | 100 | 100 | **0** | **0** | 217 | #681 `S` |
| Luxemburgisch | `ltz-LU` | < 1 | 2 | 100 | 100 | **0** | **0** | 217 | #719 `S` |
| Maithili | `mai-IN` | 34 | 5 | 100 | 100 | **0** | **0** | 217 | #656 `S` |
| Malagasy | `mlg-MG` | 25 | 3 | 100 | 100 | **0** | **0** | 217 | #649 `S` |
| Malaiisch | `msa-MY` | 60 | 1 | 100 | 100 | **0** | **0** | 217 | #611 `S` |
| Malayalam | `mal-IN` | 37 | 5 | 100 | 100 | **0** | **0** | 217 | #653 `S` |
| Maltesisch | `mlt-MT` | < 1 | 3 | 100 | 100 | **0** | **0** | 217 | #721 `S` |
| Maori | `mri-NZ` | < 1 | 3 | 100 | 100 | **0** | **0** | 217 | #730 `S` |
| Marathi | `mar-IN` | 99 | 4 | 100 | 100 | **0** | **0** | 217 | #622 `S` |
| Marokkanisches Standard-Tamazight | `zgh-MA` | 14 | 5 | 100 | 100 | **0** | **0** | 217 | #680 `S` |
| Mazedonisch | `mkd-MK` | 2 | 3 | 100 | 100 | **0** | **0** | 217 | #708 `S` |
| Meitei | `mni-IN` | 2 | 5 | 100 | 100 | **0** | **0** | 217 | #715 `S` |
| Mongolisch | `mon-MN` | 6 | 5 | 100 | 100 | **0** | **0** | 217 | #701 `S` |
| Nepali | `nep-NP` | 32 | 4 | 100 | 100 | **0** | **0** | 217 | #650 `S` |
| Niederländisch | `dut-NL` | 25 | 1 | 100 | 100 | **0** | **0** | 217 | #620 `S` |
| Niedersorbisch | `dsb-DE` | < 1 | 4 | 100 | 100 | **0** | **0** | 217 | #742 `S` |
| Nigerianisches Pidgin | `pcm-NG` | 120 | 3 | 100 | 100 | **0** | **0** | 217 | #616 `S` |
| Nordsamisch | `sme-NO` | < 1 | 4 | 100 | 100 | **0** | **0** | 217 | #738 `S` |
| Norwegisch | `nor-NO` | 5 | 1 | 100 | 100 | **0** | **0** | 217 | #662 `S` |
| N’Ko | `nqo-GN` | – | 5 | 100 | 100 | **0** | **0** | 217 | #745 `S` |
| Obersorbisch | `hsb-DE` | < 1 | 4 | 100 | 100 | **0** | **0** | 217 | #741 `S` |
| Okzitanisch | `oci-FR` | < 1 | 3 | 100 | 100 | **0** | **0** | 217 | #723 `S` |
| Oriya | `ori-IN` | 40 | 5 | 100 | 100 | **0** | **0** | 217 | #651 `S` |
| Oromo | `orm-ET` | 45 | 4 | 100 | 100 | **0** | **0** | 217 | #637 `S` |
| Panjabi | `pan-IN` | 48 | 4 | 100 | 100 | **0** | **0** | 217 | #634 `S` |
| Paschtu | `pus-AF` | 60 | 5 | 100 | 100 | **0** | **0** | 217 | #7 `S` |
| Persisch | `fas-IR` | 83 | 4 | 100 | 100 | **0** | **0** | 217 | #6 `S` |
| Polnisch | `pol-PL` | 40 | 2 | 100 | 100 | **0** | **0** | 217 | #624 `S` |
| Portugiesisch (Brasilien) | `por-BR` | 215 | 1 | 100 | 100 | **0** | **0** | 217 | #605 `S` |
| Portugiesisch (Portugal) | `por-PT` | 50 | 1 | 100 | 100 | **0** | **0** | 217 | #613 `S` |
| Quechua | `que-PE` | 8 | 4 | 100 | 100 | **0** | **0** | 217 | #688 `S` |
| Rätoromanisch | `roh-CH` | < 1 | 3 | 100 | 100 | **0** | **0** | 217 | #735 `S` |
| Rumänisch | `ron-RO` | 25 | 1 | 100 | 100 | **0** | **0** | 217 | #621 `S` |
| Russisch | `rus-RU` | 255 | 3 | 100 | 100 | **0** | **0** | 217 | #5 `S` |
| Sanskrit | `san-IN` | < 1 | 5 | 100 | 100 | **0** | **0** | 217 | #740 `S` |
| Santali | `sat-IN` | 8 | 5 | 100 | 100 | **0** | **0** | 217 | #693 `S` |
| Schottisch-Gälisch | `gla-GB` | < 1 | 4 | 100 | 100 | **0** | **0** | 217 | #734 `S` |
| Schwedisch | `swe-SE` | 13 | 1 | 100 | 100 | **0** | **0** | 217 | #633 `S` |
| Serbisch (kyrillisch) | `srp-RS` | 10 | 4 | 100 | 100 | **0** | **0** | 217 | #683 `S` |
| Serbisch (lateinisch) | `ser-SR` | 10 | 3 | 100 | 100 | **0** | **0** | 217 | #676 `S` |
| Shona | `sna-ZW` | 15 | 4 | 100 | 100 | **0** | **0** | 217 | #671 `S` |
| Sindhi | `snd-PK` | 34 | 5 | 100 | 100 | **0** | **0** | 217 | #657 `S` |
| Singhalesisch | `sin-LK` | 18 | 4 | 100 | 100 | **0** | **0** | 217 | #666 `S` |
| Slowakisch | `slk-SK` | 7 | 2 | 100 | 100 | **0** | **0** | 217 | #674 `S` |
| Slowenisch | `slv-SI` | 3 | 3 | 100 | 100 | **0** | **0** | 217 | #707 `S` |
| Somali | `som-SO` | 24 | 3 | 100 | 100 | **0** | **0** | 217 | #652 `S` |
| Spanisch (Mexiko) | `esl-MX` | 130 | 1 | 100 | 100 | **0** | **0** | 217 | #607 `S` |
| Spanisch (Spanien) | `esl-ES` | 430 | 1 | 100 | 100 | **0** | **0** | 217 | #602 `S` |
| Sundanesisch | `sun-ID` | 32 | 3 | 100 | 100 | **0** | **0** | 217 | #638 `S` |
| Swahili | `swa-KE` | 80 | 3 | 100 | 100 | **0** | **0** | 217 | #619 `S` |
| Syrisch (Aramäisch) | `syr-IQ` | < 1 | 5 | 100 | 100 | **0** | **0** | 217 | #728 `S` |
| Tadschikisch | `tgk-TJ` | 9 | 4 | 100 | 100 | **0** | **0** | 217 | #685 `S` |
| Tamil | `tam-IN` | 87 | 5 | 100 | 100 | **0** | **0** | 217 | #627 `S` |
| Tatarisch | `tat-RU` | 5 | 5 | 100 | 100 | **0** | **0** | 217 | #705 `S` |
| Telugu | `tel-IN` | 96 | 5 | 100 | 100 | **0** | **0** | 217 | #625 `S` |
| Thai | `tha-TH` | 61 | 4 | 100 | 100 | **0** | **0** | 217 | #631 `S` |
| Tibetisch | `bod-CN` | 6 | 5 | 100 | 100 | **0** | **0** | 217 | #699 `S` |
| Tigrinya | `tir-ET` | 10 | 5 | 100 | 100 | **0** | **0** | 217 | #689 `S` |
| Tongaisch | `ton-TO` | < 1 | 3 | 100 | 100 | **0** | **0** | 217 | #731 `S` |
| Tschechisch | `cze-CZ` | 11 | 2 | 100 | 100 | **0** | **0** | 217 | #661 `S` |
| Tschetschenisch | `che-RU` | 2 | 5 | 100 | 100 | **0** | **0** | 217 | #714 `S` |
| Türkisch | `tur-TR` | 90 | 2 | 100 | 100 | **0** | **0** | 217 | #3 `S` |
| Turkmenisch | `tuk-TM` | 7 | 4 | 100 | 100 | **0** | **0** | 217 | #690 `S` |
| Uigurisch | `uig-CN` | 11 | 5 | 100 | 100 | **0** | **0** | 217 | #686 `S` |
| Ukrainisch | `ukr-UA` | 39 | 3 | 100 | 100 | **0** | **0** | 217 | #4 `S` |
| Ungarisch | `hun-HU` | 13 | 2 | 100 | 100 | **0** | **0** | 217 | #658 `S` |
| Urdu | `urd-PK` | 232 | 4 | 100 | 100 | **0** | **0** | 217 | #612 `S` |
| Usbekisch | `uzb-UZ` | 35 | 3 | 100 | 100 | **0** | **0** | 217 | #636 `S` |
| Vietnamesisch | `vie-VN` | 86 | 1 | 100 | 100 | **0** | **0** | 217 | #608 `S` |
| Walisisch | `cym-GB` | < 1 | 3 | 100 | 100 | **0** | **0** | 217 | #717 `S` |
| Westfriesisch | `fry-NL` | < 1 | 3 | 100 | 100 | **0** | **0** | 217 | #722 `S` |
| Westpanjabi | `pnb-PK` | 90 | 5 | 100 | 100 | **0** | **0** | 217 | #626 `S` |
| Wolof | `wol-SN` | 22 | 3 | 100 | 100 | **0** | **0** | 217 | #654 `S` |
| Yoruba | `yor-NG` | 46 | 3 | 100 | 100 | **0** | **0** | 217 | #630 `S` |
| Zentralkurdisch (Sorani) | `ckb-IQ` | 7 | 5 | 100 | 100 | **0** | **0** | 217 | #694 `S` |

### Die Hilfe in Stufen

| Stufe | Abschnitte | Seiten | Wörter | Teile |
|---|---|---:|---:|---:|
| Alltag: Redakteure, Benutzer, Anleitungen für Administratoren | `help`, `help/manual`, `help/non-technical-people`, `guides`, `help/book-learning`, `help/book-building-a-site` | 343 | 443.905 | 17 |
| Produkt- und Zielgruppen-Leitfäden | `install`, `features`, `help/adopters`, `help/advocates`, `help/business`, `help/businesses`, `help/community`, `help/creators` und 26 weitere | 3.803 | 3.024.465 | 121 |
| Entwicklerreferenz | `help/extension-development`, `help/template-language`, `help/template-operators`, `help/build-two-ways`, `help/php-developers`, `help/cms-developers`, `help/dxp-developers`, `help/software-engineers` und 12 weitere | 1.817 | 1.864.251 | 73 |
| Tabellen der erzeugten Beispiele | `examples` | 4.186 Einträge | 112.725 | 5 |

<details><summary>Die 216 Teile der Hilfe</summary>

| Teil | Stufe | Seiten | Wörter | Erste … letzte Seite |
|---|---|---:|---:|---|
| `H-help-01` | daily | 14 | 33.869 | `help/README.md` … `help/free-documentation.md` |
| `H-help-02` | daily | 14 | 17.297 | `help/getting-started.md` … `help/reading-help-in-other-languages.md` |
| `H-help-03` | daily | 14 | 35.423 | `help/recent-content.md` … `help/why-exponential-supports-php-7-4.md` |
| `H-help-manual-01` | daily | 44 | 17.659 | `help/manual/README.md` … `help/manual/how-do-i-let-a-translator-edit-one-language.md` |
| `H-help-manual-02` | daily | 44 | 18.751 | `help/manual/how-do-i-move-a-user-to-another-group.md` … `help/manual/why-does-search-not-find-an-item.md` |
| `H-help-non-technical-people` | daily | 48 | 28.376 | `help/non-technical-people/a-website-without-hosting-costs.md` … `help/non-technical-people/words/the-site-and-the-administration.md` |
| `H-guides-01` | daily | 13 | 31.813 | `guides/README.md` … `guides/content-history.md` |
| `H-guides-02` | daily | 14 | 31.783 | `guides/content-languages.md` … `guides/getting-started.md` |
| `H-guides-03` | daily | 14 | 36.373 | `guides/graphql-recipes.md` … `guides/migrations-rollback-and-captions.md` |
| `H-guides-04` | daily | 14 | 34.108 | `guides/migrations-sql-and-settings.md` … `guides/preloading-caches.md` |
| `H-guides-05` | daily | 13 | 35.910 | `guides/remote-services-and-apps.md` … `guides/smarty-templates-migrating.md` |
| `H-guides-06` | daily | 14 | 25.559 | `guides/smarty-templates-operator-reference.md` … `guides/static-publishing-history-and-rollback.md` |
| `H-guides-07` | daily | 14 | 21.885 | `guides/static-publishing-limits-and-github-terms.md` … `guides/twig-templates-compatibility.md` |
| `H-guides-08` | daily | 14 | 14.197 | `guides/twig-templates-converter.md` … `guides/twig-templates-tutorial.md` |
| `H-guides-09` | daily | 14 | 36.569 | `guides/twig-templates.md` … `guides/your-devices-and-sessions.md` |
| `H-help-book-learning` | daily | 31 | 18.870 | `help/book-learning/README.md` … `help/book-learning/workflows-and-triggers.md` |
| `H-help-book-building-a-site` | daily | 10 | 5.463 | `help/book-building-a-site/README.md` … `help/book-building-a-site/your-first-static-site-in-15-minutes.md` |
| `H-install-01` | guides | 7 | 36.149 | `install/01-introduction.md` … `install/07-console-install.md` |
| `H-install-02` | guides | 7 | 60.732 | `install/08-serving-the-site.md` … `install/14-migrating-from-4x.md` |
| `H-install-03` | guides | 7 | 39.901 | `install/15-migrating-from-5x-legacy.md` … `install/migration/checklist.md` |
| `H-install-04` | guides | 7 | 8.467 | `install/migration/coldfusion-apps.md` … `install/migration/exponential-platform-legacy.md` |
| `H-install-05` | guides | 7 | 7.934 | `install/migration/exponential-platform-nexus.md` … `install/migration/magento.md` |
| `H-install-06` | guides | 7 | 5.299 | `install/migration/older-releases.md` … `install/migration/wordpress.md` |
| `H-features-01` | guides | 20 | 32.950 | `features/4.7/cluster_events.md` … `features/6.0/audit-trail.md` |
| `H-features-02` | guides | 20 | 40.482 | `features/6.0/benchmark.md` … `features/6.0/editor-account-approval.md` |
| `H-features-03` | guides | 20 | 16.334 | `features/6.0/environment-settings.md` … `features/6.0/extensions/expai.md` |
| `H-features-04` | guides | 20 | 14.597 | `features/6.0/extensions/expimageeditor.md` … `features/6.0/extensions/ezstarrating.md` |
| `H-features-05` | guides | 20 | 16.880 | `features/6.0/extensions/eztags.md` … `features/6.0/extensions/swark.md` |
| `H-features-06` | guides | 20 | 54.829 | `features/6.0/extensions/syndication.md` … `features/6.0/mail-layout.md` |
| `H-features-07` | guides | 20 | 22.465 | `features/6.0/mail-preferences.md` … `features/6.0/pagelayout-hooks.md` |
| `H-features-08` | guides | 20 | 24.206 | `features/6.0/password-change-at-next-sign-in.md` … `features/6.0/repair-from-the-browser.md` |
| `H-features-09` | guides | 20 | 24.413 | `features/6.0/request-logs.md` … `features/6.0/soap-client-tls.md` |
| `H-features-10` | guides | 20 | 26.631 | `features/6.0/sqlite-database.md` … `features/6.0/user-audit.md` |
| `H-features-11` | guides | 20 | 27.583 | `features/6.0/user-hygiene.md` … `features/6.0/website-sidebar.md` |
| `H-help-adopters` | guides | 47 | 16.998 | `help/adopters/adopters-checklists.md` … `help/adopters/what-adoption-means.md` |
| `H-help-advocates-01` | guides | 48 | 29.486 | `help/advocates/7x-and-the-foundation-for-advocates.md` … `help/advocates/proposing-a-contribution-sprint.md` |
| `H-help-advocates-02` | guides | 48 | 29.620 | `help/advocates/proposing-a-meetup.md` … `help/advocates/your-first-week-as-an-advocate.md` |
| `H-help-business-01` | guides | 6 | 15.690 | `help/business/brand-and-naming-rules.md` … `help/business/feature-highlights-for-customers.md` |
| `H-help-business-02` | guides | 6 | 15.383 | `help/business/growing-the-community.md` … `help/business/who-exponential-is-for.md` |
| `H-help-businesses-01` | guides | 26 | 25.794 | `help/businesses/associations/README.md` … `help/businesses/costs/what-you-do-not-pay-for.md` |
| `H-help-businesses-02` | guides | 26 | 25.357 | `help/businesses/costs/worked-example-intranet.md` … `help/businesses/intranets/two-factor-and-sign-in.md` |
| `H-help-businesses-03` | guides | 27 | 26.097 | `help/businesses/libraries/README.md` … `help/businesses/mobile-and-headless/responsive-sites.md` |
| `H-help-businesses-04` | guides | 26 | 24.905 | `help/businesses/mobile-and-headless/rest-api-v1-and-v2-for-businesses.md` … `help/businesses/newsletters/opt-in-and-consent.md` |
| `H-help-businesses-05` | guides | 27 | 29.917 | `help/businesses/newsletters/writing-and-sending-editions.md` … `help/businesses/reliability/restore-drills.md` |
| `H-help-businesses-06` | guides | 26 | 26.999 | `help/businesses/reliability/static-and-response-cache.md` … `help/businesses/stores/payment-gateways-for-businesses.md` |
| `H-help-businesses-07` | guides | 27 | 22.322 | `help/businesses/stores/paypal-payments.md` … `help/businesses/why-businesses-rely-on-exponential.md` |
| `H-help-community` | guides | 53 | 27.394 | `help/community/a-free-site-for-your-user-group.md` … `help/community/your-first-week-in-the-community.md` |
| `H-help-creators-01` | guides | 47 | 23.568 | `help/creators/app-creators/access-control-in-code.md` … `help/creators/app-creators/remote-services-expservices.md` |
| `H-help-creators-02` | guides | 47 | 26.278 | `help/creators/app-creators/rest-api.md` … `help/creators/website-creators/member-registration-and-areas.md` |
| `H-help-creators-03` | guides | 47 | 29.000 | `help/creators/website-creators/menus-and-navigation.md` … `help/creators/website-creators/your-first-site-step-by-step.md` |
| `H-help-customers-01` | guides | 37 | 25.803 | `help/customers/commerce/README.md` … `help/customers/day-to-day/analytics-and-what-to-measure.md` |
| `H-help-customers-02` | guides | 38 | 26.709 | `help/customers/day-to-day/approvals-and-review.md` … `help/customers/growth/rss-and-podcast-feeds.md` |
| `H-help-customers-03` | guides | 38 | 24.975 | `help/customers/growth/using-ai-drafts-responsibly.md` … `help/customers/partners-and-hosts/who-does-what.md` |
| `H-help-customers-04` | guides | 38 | 22.272 | `help/customers/partners-and-hosts/working-with-7x-and-the-project.md` … `help/customers/what-you-own.md` |
| `H-help-designers-01` | guides | 63 | 28.332 | `help/designers/README.md` … `help/designers/extensions/gitignore-for-themes.md` |
| `H-help-designers-02` | guides | 64 | 24.321 | `help/designers/extensions/install-and-changelog-files.md` … `help/designers/sales/assets-under-separate-licenses.md` |
| `H-help-designers-03` | guides | 63 | 23.137 | `help/designers/sales/contracts-and-terms.md` … `help/designers/templates/if-and-conditions.md` |
| `H-help-designers-04` | guides | 64 | 26.861 | `help/designers/templates/include.md` … `help/designers/theme-design/color-contrast.md` |
| `H-help-designers-05` | guides | 64 | 38.389 | `help/designers/theme-design/csp-safe-design.md` … `help/designers/theme-design/what-a-design-is.md` |
| `H-help-e-commerce-01` | guides | 26 | 17.960 | `help/e-commerce/catalog/README.md` … `help/e-commerce/operations/analytics-for-stores.md` |
| `H-help-e-commerce-02` | guides | 27 | 18.329 | `help/e-commerce/operations/commerce-audit-trail.md` … `help/e-commerce/settings/workflow-ini-for-the-shop.md` |
| `H-help-ecosystem` | guides | 45 | 18.856 | `help/ecosystem/a-short-history-of-the-ecosystem.md` … `help/ecosystem/what-the-ecosystem-is.md` |
| `H-help-government-01` | guides | 53 | 32.971 | `help/government/accessibility/README.md` … `help/government/building/documents-and-publications/pdf-export.md` |
| `H-help-government-02` | guides | 53 | 24.724 | `help/government/building/documents-and-publications/versions-of-documents.md` … `help/government/building/news-and-alerts/README.md` |
| `H-help-government-03` | guides | 53 | 28.368 | `help/government/building/news-and-alerts/archive-and-expiry.md` … `help/government/checklists/quarterly-access-review.md` |
| `H-help-government-04` | guides | 53 | 31.704 | `help/government/checklists/records-checklist.md` … `help/government/languages/redirecting-to-the-visitors-language.md` |
| `H-help-government-05` | guides | 53 | 34.251 | `help/government/languages/removing-a-translation-safely.md` … `help/government/levels/parliaments/structure-sections-and-roles.md` |
| `H-help-government-06` | guides | 53 | 35.269 | `help/government/levels/parliaments/what-it-publishes.md` … `help/government/operations/availability/database-high-availability.md` |
| `H-help-government-07` | guides | 53 | 39.235 | `help/government/operations/availability/high-availability-overview.md` … `help/government/operations/incidents/data-breach-notification-framing.md` |
| `H-help-government-08` | guides | 53 | 34.233 | `help/government/operations/incidents/database-unreachable.md` … `help/government/privacy/README.md` |
| `H-help-government-09` | guides | 53 | 32.204 | `help/government/privacy/analytics.md` … `help/government/procurement/exit-plans/lock-in-clauses-to-avoid.md` |
| `H-help-government-10` | guides | 53 | 34.358 | `help/government/procurement/exit-plans/personal-data-at-exit.md` … `help/government/procurement/total-cost-of-ownership/staff-and-skills.md` |
| `H-help-government-11` | guides | 53 | 30.491 | `help/government/procurement/total-cost-of-ownership/support-contracts.md` … `help/government/security/file-uploads-and-svg.md` |
| `H-help-government-12` | guides | 53 | 31.597 | `help/government/security/forwarding-audit-to-a-siem.md` … `help/government/tasks/compliance/add-security-txt.md` |
| `H-help-government-13` | guides | 53 | 19.557 | `help/government/tasks/compliance/add-your-own-words-to-the-weak-password-list.md` … `help/government/tasks/operations/bring-the-site-back-online.md` |
| `H-help-government-14` | guides | 53 | 23.496 | `help/government/tasks/operations/check-file-and-directory-permissions.md` … `help/government/tasks/people-and-permissions/keep-editors-out-of-the-admin-siteaccess.md` |
| `H-help-government-15` | guides | 53 | 25.206 | `help/government/tasks/people-and-permissions/let-a-contractor-edit-only-one-folder.md` … `help/government/tasks/publishing/find-and-fix-broken-links.md` |
| `H-help-government-16` | guides | 53 | 24.198 | `help/government/tasks/publishing/follow-changes-with-notifications.md` … `help/government/tasks/sites-and-languages/connect-staff-sign-in-to-ldap.md` |
| `H-help-government-17` | guides | 53 | 21.481 | `help/government/tasks/sites-and-languages/export-a-list-of-content-as-csv.md` … `help/government/transparency-and-foi/withdrawing-and-correcting-published-information.md` |
| `H-help-hosting-01` | guides | 24 | 25.768 | `help/hosting/basics/README.md` … `help/hosting/business/marketing-exponential-hosting.md` |
| `H-help-hosting-02` | guides | 24 | 22.215 | `help/hosting/business/partner-programs.md` … `help/hosting/models/managed-hosting.md` |
| `H-help-hosting-03` | guides | 24 | 31.750 | `help/hosting/models/platform-as-a-service.md` … `help/hosting/operations/storage-layout.md` |
| `H-help-hosting-04` | guides | 24 | 28.982 | `help/hosting/operations/system-card-for-hosts.md` … `help/hosting/performance/varnish-and-reverse-proxies.md` |
| `H-help-hosting-05` | guides | 24 | 22.444 | `help/hosting/performance/velocity-response-cache.md` … `help/hosting/security/demo-mode-for-hosts.md` |
| `H-help-hosting-06` | guides | 24 | 32.158 | `help/hosting/security/first-sign-in-and-default-passwords.md` … `help/hosting/static-publishing-and-your-hosting.md` |
| `H-help-individuals-01` | guides | 28 | 20.557 | `help/individuals/blogs/README.md` … `help/individuals/individuals-reading-path.md` |
| `H-help-individuals-02` | guides | 28 | 21.329 | `help/individuals/individuals-start-here.md` … `help/individuals/your-personal-site-for-free.md` |
| `H-help-leadership-01` | guides | 9 | 19.895 | `help/leadership/accessibility-security-and-compliance-checklists.md` … `help/leadership/planning-an-exponential-project.md` |
| `H-help-leadership-02` | guides | 9 | 20.242 | `help/leadership/project-managers-start-here.md` … `help/leadership/working-with-the-community.md` |
| `H-help-local-businesses-01` | guides | 28 | 18.013 | `help/local-businesses/a-fast-free-brochure-site-for-your-business.md` … `help/local-businesses/getting-started/working-with-a-local-partner-or-freelancer.md` |
| `H-help-local-businesses-02` | guides | 28 | 19.105 | `help/local-businesses/local-businesses-faq.md` … `help/local-businesses/templates/website-brief-for-a-local-business.md` |
| `H-help-migrations-01` | guides | 22 | 20.677 | `help/migrations/README.md` … `help/migrations/recipes/coming-from-sql-update-files.md` |
| `H-help-migrations-02` | guides | 23 | 21.845 | `help/migrations/recipes/create-or-change-a-content-class.md` … `help/migrations/when-a-migration-fails.md` |
| `H-help-non-profits-01` | guides | 28 | 19.186 | `help/non-profits/a-free-website-for-your-nonprofit.md` … `help/non-profits/non-profits-faq.md` |
| `H-help-non-profits-02` | guides | 28 | 19.746 | `help/non-profits/non-profits-glossary.md` … `help/non-profits/volunteers/volunteers-as-editors.md` |
| `H-help-partners-01` | guides | 37 | 15.750 | `help/partners/application-checklist.md` … `help/partners/partner-faq.md` |
| `H-help-partners-02` | guides | 38 | 17.508 | `help/partners/partner-glossary.md` … `help/partners/your-partner-page.md` |
| `H-help-podcasters-01` | guides | 24 | 17.308 | `help/podcasters/a-release-calendar.md` … `help/podcasters/podcaster-checklists.md` |
| `H-help-podcasters-02` | guides | 24 | 18.395 | `help/podcasters/podcaster-faq.md` … `help/podcasters/writing-show-notes.md` |
| `H-help-press-01` | guides | 38 | 27.987 | `help/press/7x-for-reporters.md` … `help/press/fact-checking-guide.md` |
| `H-help-press-02` | guides | 38 | 25.330 | `help/press/fact-sheet-for-journalists.md` … `help/press/reporter-faq.md` |
| `H-help-press-03` | guides | 38 | 23.973 | `help/press/requesting-an-interview.md` … `help/press/story-online-editor-tinymce-8.md` |
| `H-help-press-04` | guides | 38 | 25.257 | `help/press/story-open-source-and-licenses.md` … `help/press/writing-about-the-ez-publish-years.md` |
| `H-help-project` | guides | 12 | 16.030 | `help/project/README.md` … `help/project/roadmap.md` |
| `H-help-resellers-01` | guides | 26 | 17.388 | `help/resellers/basics/choosing-a-partner-level.md` … `help/resellers/offers/demo-sites.md` |
| `H-help-resellers-02` | guides | 27 | 18.547 | `help/resellers/offers/e-commerce-offers.md` … `help/resellers/sales/sales-process.md` |
| `H-help-site-packages-01` | guides | 26 | 27.331 | `help/site-packages/README.md` … `help/site-packages/cjw-multisite/the-cjw-themes-jumper-extension.md` |
| `H-help-site-packages-02` | guides | 26 | 29.251 | `help/site-packages/cjw-multisite/the-demo-content-package.md` … `help/site-packages/requirements/the-requirements-checklist.md` |
| `H-help-site-packages-03` | guides | 26 | 26.781 | `help/site-packages/requirements/thumbnails-and-screenshots.md` … `help/site-packages/your-own/versioning-your-own-packages.md` |
| `H-help-sponsors` | guides | 11 | 5.286 | `help/sponsors/7x-the-first-sponsor.md` … `help/sponsors/what-sponsorship-funds.md` |
| `H-help-static-publishing` | guides | 25 | 13.266 | `help/static-publishing/README.md` … `help/static-publishing/who-may-publish-roles-and-policies.md` |
| `H-help-students-01` | guides | 28 | 21.897 | `help/students/asking-for-help-and-study-groups.md` … `help/students/project-05-a-multilingual-site.md` |
| `H-help-students-02` | guides | 28 | 22.743 | `help/students/project-06-search-that-finds-the-right-things.md` … `help/students/your-first-site-three-ways.md` |
| `H-help-supporters` | guides | 12 | 5.721 | `help/supporters/advocacy.md` … `help/supporters/ways-to-support-exponential.md` |
| `H-help-theme-designers-01` | guides | 22 | 18.799 | `help/theme-designers/building-a-portfolio-and-getting-work.md` … `help/theme-designers/designing-view-modes.md` |
| `H-help-theme-designers-02` | guides | 22 | 18.806 | `help/theme-designers/designing-with-real-content.md` … `help/theme-designers/your-first-day-as-a-theme-designer.md` |
| `H-help-trainers-01` | guides | 31 | 31.213 | `help/trainers/assessments/README.md` … `help/trainers/assessments/module-05-newsletter-and-migration/README.md` |
| `H-help-trainers-02` | guides | 32 | 32.172 | `help/trainers/assessments/module-05-newsletter-and-migration/migration-practical-task.md` … `help/trainers/developers/module-03-designs-overrides-and-debugging/the-design-chain.md` |
| `H-help-trainers-03` | guides | 31 | 29.141 | `help/trainers/developers/module-04-content-classes-and-datatypes/README.md` … `help/trainers/developers/module-09-the-console-and-persistent-objects/sql-safely-with-ezdb.md` |
| `H-help-trainers-04` | guides | 32 | 30.122 | `help/trainers/developers/module-09-the-console-and-persistent-objects/the-console.md` … `help/trainers/editors-101/module-04-the-online-editor/README.md` |
| `H-help-trainers-05` | guides | 32 | 29.014 | `help/trainers/editors-101/module-04-the-online-editor/embeds-tables-and-choosing-the-editor.md` … `help/trainers/editors-201/module-03-organizing-content/README.md` |
| `H-help-trainers-06` | guides | 31 | 26.155 | `help/trainers/editors-201/module-03-organizing-content/large-operations-and-the-trash.md` … `help/trainers/exponential-intelligence/module-02-the-intelligence-tab/keeping-sending-and-discarding-drafts.md` |
| `H-help-trainers-07` | guides | 32 | 29.476 | `help/trainers/exponential-intelligence/module-03-the-edit-form/README.md` … `help/trainers/hosting-operations/module-03-databases/README.md` |
| `H-help-trainers-08` | guides | 31 | 30.398 | `help/trainers/hosting-operations/module-03-databases/choosing-a-database.md` … `help/trainers/layouts/module-01-introducing-layouts/the-five-ideas.md` |
| `H-help-trainers-09` | guides | 32 | 26.649 | `help/trainers/layouts/module-02-layouts-and-layout-types/README.md` … `help/trainers/migration/module-01-planning-a-migration/phases-and-the-checklist.md` |
| `H-help-trainers-10` | guides | 32 | 32.621 | `help/trainers/migration/module-01-planning-a-migration/rehearsal-cut-over-and-rollback.md` … `help/trainers/multisite-and-packages/module-01-siteaccesses-for-several-sites/a-siteaccess-per-language.md` |
| `H-help-trainers-11` | guides | 31 | 35.761 | `help/trainers/multisite-and-packages/module-01-siteaccesses-for-several-sites/adding-a-second-site.md` … `help/trainers/newsletter/README.md` |
| `H-help-trainers-12` | guides | 32 | 31.603 | `help/trainers/newsletter/module-01-getting-started/README.md` … `help/trainers/search/module-01-how-search-works/where-people-search.md` |
| `H-help-trainers-13` | guides | 31 | 30.701 | `help/trainers/search/module-02-indexing/README.md` … `help/trainers/site-administrators/module-02-users-and-groups/passwords-resets-and-lockouts.md` |
| `H-help-trainers-14` | guides | 32 | 32.752 | `help/trainers/site-administrators/module-02-users-and-groups/unactivated-users-and-account-checks.md` … `help/trainers/site-administrators/module-08-security/README.md` |
| `H-help-trainers-15` | guides | 32 | 35.241 | `help/trainers/site-administrators/module-08-security/content-security-policy.md` … `help/trainers/theme-designers/module-03-stylesheets-and-scripts/listing-files-in-design-ini.md` |
| `H-help-trainers-16` | guides | 31 | 30.777 | `help/trainers/theme-designers/module-03-stylesheets-and-scripts/ready-for-a-content-security-policy.md` … `help/trainers/train-the-trainer/module-01-preparing-to-teach/the-help-center-as-your-textbook.md` |
| `H-help-trainers-17` | guides | 32 | 22.473 | `help/trainers/train-the-trainer/module-01-preparing-to-teach/your-material-and-the-gfdl.md` … `help/trainers/train-the-trainer/module-06-assessment-and-follow-up/certificates-of-attendance.md` |
| `H-help-trainers-18` | guides | 32 | 19.670 | `help/trainers/train-the-trainer/module-06-assessment-and-follow-up/checking-understanding-during-class.md` … `help/trainers/workshops/module-05-capstone-projects/multilingual-event-site.md` |
| `H-help-user-management-from-the-console` | guides | 20 | 7.853 | `help/user-management-from-the-console/README.md` … `help/user-management-from-the-console/when-a-command-fails.md` |
| `H-reference` | guides | 1 | 1.858 | `reference/template-check-rules.md` |
| `H-root-01` | guides | 4 | 12.136 | `INSTALL.md` … `UPDATE.md` |
| `H-root-02` | guides | 4 | 35.466 | `branch-overview.md` … `languages.md` |
| `H-help-extension-development-01` | reference | 43 | 36.427 | `help/extension-development/README.md` … `help/extension-development/part-2-builder/the-exp-ini-command.md` |
| `H-help-extension-development-02` | reference | 43 | 25.222 | `help/extension-development/part-2-builder/view-modes-for-builders.md` … `help/extension-development/part-3-extension-developer/tutorials/by-hand-custom-tag.md` |
| `H-help-extension-development-03` | reference | 43 | 28.645 | `help/extension-development/part-3-extension-developer/tutorials/by-hand-datatype.md` … `help/extension-development/part-4-upgrading/old-jquery-to-jquery-4.md` |
| `H-help-extension-development-04` | reference | 43 | 30.248 | `help/extension-development/part-4-upgrading/php-8-1-to-8-5-deprecations.md` … `help/extension-development/part-7-releasing/github-releases-and-release-notes.md` |
| `H-help-extension-development-05` | reference | 44 | 24.416 | `help/extension-development/part-7-releasing/packages-ezpkg.md` … `help/extension-development/reading-paths.md` |
| `H-help-template-language-01` | reference | 16 | 18.348 | `help/template-language/README.md` … `help/template-language/moving-from-tpl-to-twig.md` |
| `H-help-template-language-02` | reference | 17 | 17.445 | `help/template-language/namespaces.md` … `help/template-language/what-the-template-language-is.md` |
| `H-help-template-operators-01` | reference | 24 | 24.150 | `help/template-operators/README.md` … `help/template-operators/menu-and-tree-operators.md` |
| `H-help-template-operators-02` | reference | 24 | 30.407 | `help/template-operators/newsletter-operators.md` … `help/template-operators/url-operators.md` |
| `H-help-build-two-ways-01` | reference | 83 | 27.828 | `help/build-two-ways/README.md` … `help/build-two-ways/examples/maintain/add-a-recipe-to-the-index.md` |
| `H-help-build-two-ways-02` | reference | 83 | 20.834 | `help/build-two-ways/examples/maintain/adjust-spacing.md` … `help/build-two-ways/examples/plan/inventory-from-the-console.md` |
| `H-help-build-two-ways-03` | reference | 84 | 30.664 | `help/build-two-ways/examples/plan/inventory-page-types.md` … `help/build-two-ways/your-own-design-extension.md` |
| `H-help-php-developers-01` | reference | 28 | 23.394 | `help/php-developers/a-php-development-checkout.md` … `help/php-developers/php-developer-faq.md` |
| `H-help-php-developers-02` | reference | 28 | 21.569 | `help/php-developers/php-developer-reading-path.md` … `help/php-developers/writing-migrations-in-php.md` |
| `H-help-cms-developers-01` | reference | 25 | 23.828 | `help/cms-developers/accessibility.md` … `help/cms-developers/headless-delivery.md` |
| `H-help-cms-developers-02` | reference | 26 | 22.999 | `help/cms-developers/images-and-image-aliases.md` … `help/cms-developers/workflows-and-triggers.md` |
| `H-help-dxp-developers` | reference | 52 | 27.610 | `help/dxp-developers/application-cache-and-redis.md` … `help/dxp-developers/what-the-dxp-ships-and-what-it-does-not.md` |
| `H-help-software-engineers-01` | reference | 26 | 19.175 | `help/software-engineers/api-design-rest-ezjscore-headless.md` … `help/software-engineers/http-security-csp-and-proxies.md` |
| `H-help-software-engineers-02` | reference | 26 | 21.759 | `help/software-engineers/i18n-and-right-to-left.md` … `help/software-engineers/writing-portable-sql.md` |
| `H-help-web-framework-developers-01` | reference | 27 | 18.466 | `help/web-framework-developers/authentication-and-sessions.md` … `help/web-framework-developers/migrations-and-dba-schema-files.md` |
| `H-help-web-framework-developers-02` | reference | 28 | 19.394 | `help/web-framework-developers/mixing-exponential-and-a-framework.md` … `help/web-framework-developers/your-first-module-step-by-step.md` |
| `H-help-mobile-app-developers-01` | reference | 27 | 15.938 | `help/mobile-app-developers/auth/README.md` … `help/mobile-app-developers/media-and-images/images-in-rest-answers.md` |
| `H-help-mobile-app-developers-02` | reference | 28 | 17.528 | `help/mobile-app-developers/mobile-app-developers-faq.md` … `help/mobile-app-developers/which-api-for-your-app.md` |
| `H-help-database-administrators-01` | reference | 24 | 19.430 | `help/database-administrators/applying-the-6-0-15-database-update.md` … `help/database-administrators/mysql-read-replicas.md` |
| `H-help-database-administrators-02` | reference | 25 | 21.935 | `help/database-administrators/oracle-connections-and-tuning.md` … `help/database-administrators/upgrades-and-sql-update-files.md` |
| `H-help-ai-agents-01` | reference | 24 | 16.131 | `help/ai-agents/agentic-workflows/README.md` … `help/ai-agents/expai/chains-and-queued-work.md` |
| `H-help-ai-agents-02` | reference | 24 | 18.470 | `help/ai-agents/expai/configuring-a-provider.md` … `help/ai-agents/which-path-fits-your-task.md` |
| `H-help-bundlers-01` | reference | 27 | 19.462 | `help/bundlers/adding-and-removing-extensions.md` … `help/bundlers/hosting-panel-installers.md` |
| `H-help-bundlers-02` | reference | 28 | 20.538 | `help/bundlers/integrity-checks.md` … `help/bundlers/your-own-package-server.md` |
| `H-help-integrators-01` | reference | 28 | 21.616 | `help/integrators/ai-job-queue-and-mcp.md` … `help/integrators/interface-specification-template.md` |
| `H-help-integrators-02` | reference | 29 | 24.168 | `help/integrators/login-handlers-ldap-and-textfile.md` … `help/integrators/writing-an-import-script.md` |
| `H-help-sqli-import` | reference | 6 | 6.974 | `help/sqli-import/README.md` … `help/sqli-import/upgrading-from-1x.md` |
| `H-specifications-01` | reference | 14 | 17.988 | `specifications/6.0/README.md` … `specifications/6.0/help-standards.md` |
| `H-specifications-02` | reference | 14 | 24.377 | `specifications/6.0/help-style-guide.md` … `specifications/6.0/platform-package-map.md` |
| `H-specifications-03` | reference | 14 | 22.310 | `specifications/6.0/platform-sqlite-installer.md` … `specifications/6.0/xrowextract.md` |
| `H-standards-01` | reference | 18 | 34.552 | `standards/README.md` … `standards/patterns/area-extension-bcgooglesitemaps.md` |
| `H-standards-02` | reference | 18 | 21.769 | `standards/patterns/area-extension-bcwebsitestatistics.md` … `standards/patterns/area-extension-explayouts-content-browser.md` |
| `H-standards-03` | reference | 19 | 15.548 | `standards/patterns/area-extension-explayouts-core.md` … `standards/patterns/area-extension-ezautosave.md` |
| `H-standards-04` | reference | 18 | 10.141 | `standards/patterns/area-extension-ezdemo.md` … `standards/patterns/area-extension-ezprestapiprovider.md` |
| `H-standards-05` | reference | 19 | 10.278 | `standards/patterns/area-extension-ezssp.md` … `standards/patterns/area-extension-sevenx-themes-super.md` |
| `H-standards-06` | reference | 18 | 21.979 | `standards/patterns/area-extension-sqliimport.md` … `standards/patterns/area-kernel-classes-datatable.md` |
| `H-standards-07` | reference | 19 | 10.021 | `standards/patterns/area-kernel-classes-datatypes.md` … `standards/patterns/area-kernel-classes-secrets.md` |
| `H-standards-08` | reference | 18 | 23.028 | `standards/patterns/area-kernel-classes-shopaccounthandlers.md` … `standards/patterns/area-kernel-private-classes-httpcache.md` |
| `H-standards-09` | reference | 19 | 21.656 | `standards/patterns/area-kernel-private-classes-requestrules.md` … `standards/patterns/area-lib-eztemplate.md` |
| `H-standards-10` | reference | 18 | 90.563 | `standards/patterns/area-lib-ezutils.md` … `standards/patterns/module-view.md` |
| `H-standards-11` | reference | 19 | 57.198 | `standards/patterns/observer.md` … `standards/web-platform.md` |
| `H-bc-01` | reference | 10 | 34.139 | `bc/5.90/README.md` … `bc/6.0/SUBITEMS_MENU_MORE_ACTIONS_MENU_EXPANSION_HIDE_UNHIDE.md` |
| `H-bc-02` | reference | 11 | 72.739 | `bc/6.0/admin4-styling.md` … `bc/6.0/bookmarks-of-system-pages.md` |
| `H-bc-03` | reference | 10 | 29.067 | `bc/6.0/cache-console.md` … `bc/6.0/debug-bar.md` |
| `H-bc-04` | reference | 11 | 16.158 | `bc/6.0/demo-mode.md` … `bc/6.0/ezpsessionhandlerdb-php8-bugfix-and-tests.md` |
| `H-bc-05` | reference | 10 | 27.094 | `bc/6.0/file-modes.md` … `bc/6.0/ini-save-file-permissions.md` |
| `H-bc-06` | reference | 11 | 14.484 | `bc/6.0/installer-crontab.md` … `bc/6.0/love/L01-callers.md` |
| `H-bc-07` | reference | 11 | 18.567 | `bc/6.0/love/MIGRATION.md` … `bc/6.0/middle-out-compression.md` |
| `H-bc-08` | reference | 10 | 14.107 | `bc/6.0/multi-node-edit.md` … `bc/6.0/passkeys-and-verify-its-you.md` |
| `H-bc-09` | reference | 11 | 20.963 | `bc/6.0/password-change-at-next-sign-in.md` … `bc/6.0/rad-extension-points.md` |
| `H-bc-10` | reference | 10 | 26.591 | `bc/6.0/rad-extension-surface.md` … `bc/6.0/site-packages-no-extension-packages.md` |
| `H-bc-11` | reference | 11 | 30.123 | `bc/6.0/soap-entry-script-index-soap.md` … `bc/6.0/ui-switch-design.md` |
| `H-bc-12` | reference | 11 | 28.256 | `bc/6.0/ui-switch.md` … `bc/6.0/yui-removal.md` |
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
| `H-examples` | reference | 27 | 24.802 | `examples/README.md` … `examples/web-servers/velocity.md` |
| `X01` | tables | 1.139 Einträge | 25.959 | `descriptions/01-command-accesscheck.json` … `descriptions/05-ini-settings-content-ini.json` |
| `X02` | tables | 598 Einträge | 25.885 | `descriptions/06-ini-settings-debugbar-ini.json` … `descriptions/08-ini-settings-package-ini.json` |
| `X03` | tables | 690 Einträge | 29.504 | `descriptions/09-ini-settings-site-ini.json` … `descriptions/12-shell-checkcalltimeref.json` |
| `X04` | tables | 1.413 Einträge | 28.454 | `descriptions/13-command-exptemplateconvert.json` … `descriptions/workflow-events.json` |
| `X05` | tables | 346 Einträge | 2.923 | `strings.json` |

</details>

<details><summary>Die 11 Teile des Administrationskatalogs</summary>

| Teil | Texte | Wörter | Kontexte (erster … letzter) |
|---|---:|---:|---|
| `A01` | 1.966 | 12.338 | `contentstructuremenu/show_content_structure` … `design/admin/error/shop` |
| `A02` | 1.922 | 11.372 | `design/admin/ezinfo/about` … `design/admin/pdf/edit` |
| `A03` | 1.939 | 12.857 | `design/admin/pdf/list` … `design/admin/setup/extensions` |
| `A04` | 1.972 | 20.876 | `design/admin/setup/info` … `design/admin/setup/rad/override` |
| `A05` | 1.998 | 14.899 | `design/admin/setup/rad/settings` … `design/admin/visual/templateview` |
| `A06` | 1.993 | 9.020 | `design/admin/visual/toolbar` … `design/standard/section` |
| `A07` | 1.929 | 13.688 | `design/standard/setup` … `kernel/collaboration` |
| `A08` | 1.839 | 13.074 | `kernel/content` … `kernel/contentfinder` |
| `A09` | 1.948 | 10.348 | `design/admin/audit` … `design/admin/mailpreferences` |
| `A10` | 1.928 | 13.144 | `design/standard/mailpreferences` … `kernel/user/security/mail` |
| `A11` | 1.421 | 8.798 | `design/admin/graphql` … `design/admin/class/datatype` |

</details>

### Sprachen ohne Übersetzung

Die Sprachen der mitgelieferten Locales, die die Administration auf Englisch zeigt, die Sprachen, die auf einem Branch übersetzt werden, und Sprachen mit fünf Millionen Sprechern oder mehr, für die es noch keine Locale gibt (der Code ist ein Vorschlag). Sortiert nach Sprechern je Schwierigkeitspunkt.

| Sprache | Code | Sprecher (Mio.) | Schrift | Richtung | Schwierigkeit | Stand |
|---|---|---:|---|---|:-:|---|
| Haitianisch | `hat-HT` | 13 | Latn | LTR | 2 | neue Locale, Welle #128 |
| Saraiki | `skr-PK` | 26 | Arab | RTL | 5 | neue Locale, Welle #140 |
| Setswana | `tsn-ZA` | 14 | Latn | LTR | 3 | neue Locale, Welle #152 |
| Dioula | `dyu-CI` | 12 | Latn | LTR | 3 | neue Locale, Welle #164 |
| Chichewa | `nya-MW` | 15 | Latn | LTR | 4 | neue Locale, Welle #176 |
| Nord-Sotho | `nso-ZA` | 14 | Latn | LTR | 4 | neue Locale, Welle #188 |
| Sesotho | `sot-ZA` | 14 | Latn | LTR | 4 | neue Locale, Welle #200 |
| Mòoré | `mos-BF` | 10 | Latn | LTR | 3 | neue Locale, Welle #212 |
| Südaserbaidschanisch | `azb-IR` | 15 | Arab | RTL | 5 | neue Locale, Welle #224 |
| Kanuri | `kau-NG` | 10 | Latn | LTR | 4 | neue Locale, Welle #236 |
| Makua | `vmw-MZ` | 9 | Latn | LTR | 4 | neue Locale, Welle #248 |
| Kikongo | `kon-CD` | 7 | Latn | LTR | 4 | neue Locale, Welle #260 |
| Luhya | `luy-KE` | 7 | Latn | LTR | 4 | neue Locale, Welle #272 |
| Xitsonga | `tso-ZA` | 7 | Latn | LTR | 4 | neue Locale, Welle #284 |
| Umbundu | `umb-AO` | 7 | Latn | LTR | 4 | neue Locale, Welle #296 |
| Luo | `luo-KE` | 5 | Latn | LTR | 3 | neue Locale, Welle #308 |
| Sango | `sag-CF` | 5 | Latn | LTR | 3 | neue Locale, Welle #320 |
| Belutschisch | `bal-PK` | 8 | Arab | RTL | 5 | neue Locale, Welle #332 |
| Kalenjin | `kln-KE` | 6 | Latn | LTR | 4 | neue Locale, Welle #344 |
| Tshiluba | `lua-CD` | 6 | Latn | LTR | 4 | neue Locale, Welle #356 |
| Sukuma | `suk-TZ` | 5 | Latn | LTR | 4 | neue Locale, Welle #368 |
| ale-US | `ale-US` | – | Latn | LTR | 3 | Welle #380 |
| arn-CL | `arn-CL` | – | Latn | LTR | 3 | Welle #391 |
| aym-BO | `aym-BO` | – | Latn | LTR | 3 | Welle #402 |
| chr-US | `chr-US` | – | Latn | LTR | 3 | Welle #413 |
| cre-CA | `cre-CA` | – | Latn | LTR | 3 | Welle #424 |
| ems-US | `ems-US` | – | Latn | LTR | 3 | Welle #435 |
| esu-US | `esu-US` | – | Latn | LTR | 3 | Welle #446 |
| gwi-US | `gwi-US` | – | Latn | LTR | 3 | Welle #457 |
| hai-US | `hai-US` | – | Latn | LTR | 3 | Welle #468 |
| iku-CA | `iku-CA` | – | Latn | LTR | 3 | Welle #479 |
| ipk-US | `ipk-US` | – | Latn | LTR | 3 | Welle #490 |
| lkt-US | `lkt-US` | – | Latn | LTR | 3 | Welle #501 |
| moh-CA | `moh-CA` | – | Latn | LTR | 3 | Welle #512 |
| nah-MX | `nah-MX` | – | Latn | LTR | 3 | Welle #523 |
| nav-US | `nav-US` | – | Latn | LTR | 3 | Welle #534 |
| oji-CA | `oji-CA` | – | Latn | LTR | 3 | Welle #545 |
| quc-GT | `quc-GT` | – | Latn | LTR | 3 | Welle #556 |
| tli-US | `tli-US` | – | Latn | LTR | 3 | Welle #567 |
| tsi-US | `tsi-US` | – | Latn | LTR | 3 | Welle #578 |
| yua-MX | `yua-MX` | – | Latn | LTR | 3 | Welle #589 |

### Die Warteschlange: die ersten 60 Wellen

| Phase | Wellen |
|---|---:|
| 1. Kataloge der Prioritätsgruppe | 1 |
| 2. Deutsch: Hilfe des Alltags | 0 |
| 3. Hilfe des Alltags der Prioritätsgruppe | 126 |
| 4. Neue Übersetzungen | 472 |
| 5. Erweiterungen der übrigen Sprachen | 1 |
| 6. Hilfe des Alltags der übrigen Sprachen | 2.628 |
| 7. Übrige Hilfe der Prioritätsgruppe | 1.393 |
| 8. Übrige Hilfe der übrigen Sprachen | 29.054 |

| # | Welle | Sprache | Arbeitspaket | Umfang (offen) | Schwierigkeit | Voraussetzungen |
|---:|---|---|---|---|:-:|---|
| 1 | `ger-DE/E` | Deutsch | Mitgelieferte Erweiterungen | 137 von 567 Texten | 2 | `GUIDE.md` |
| 2 | `ara-SA/S` | Arabisch | Hilfe einrichten | klein | 4 | `GUIDE.md`, RTL, PDF-Schrift Arab |
| 3 | `tur-TR/S` | Türkisch | Hilfe einrichten | klein | 2 | `GUIDE.md` |
| 4 | `ukr-UA/S` | Ukrainisch | Hilfe einrichten | klein | 3 | `GUIDE.md` |
| 5 | `rus-RU/S` | Russisch | Hilfe einrichten | klein | 3 | `GUIDE.md` |
| 6 | `fas-IR/S` | Persisch | Hilfe einrichten | klein | 4 | `GUIDE.md`, RTL, PDF-Schrift Arab |
| 7 | `pus-AF/S` | Paschtu | Hilfe einrichten | klein | 5 | `GUIDE.md`, RTL, PDF-Schrift Arab |
| 8 | `kur-TR/S` | Kurdisch (Kurmandschi) | Hilfe einrichten | klein | 3 | `GUIDE.md` |
| 9 | `ara-SA/H-help-01` | Arabisch | Hilfe `H-help-01` | 14 Seiten, 33.869 Wörter | 4 | `GUIDE.md`, RTL, PDF-Schrift Arab, Wellen `S` und Administration |
| 10 | `tur-TR/H-help-01` | Türkisch | Hilfe `H-help-01` | 14 Seiten, 33.869 Wörter | 2 | `GUIDE.md`, Wellen `S` und Administration |
| 11 | `ukr-UA/H-help-01` | Ukrainisch | Hilfe `H-help-01` | 14 Seiten, 33.869 Wörter | 3 | `GUIDE.md`, Wellen `S` und Administration |
| 12 | `rus-RU/H-help-01` | Russisch | Hilfe `H-help-01` | 14 Seiten, 33.869 Wörter | 3 | `GUIDE.md`, Wellen `S` und Administration |
| 13 | `fas-IR/H-help-01` | Persisch | Hilfe `H-help-01` | 14 Seiten, 33.869 Wörter | 4 | `GUIDE.md`, RTL, PDF-Schrift Arab, Wellen `S` und Administration |
| 14 | `pus-AF/H-help-01` | Paschtu | Hilfe `H-help-01` | 14 Seiten, 33.869 Wörter | 5 | `GUIDE.md`, RTL, PDF-Schrift Arab, Wellen `S` und Administration |
| 15 | `kur-TR/H-help-01` | Kurdisch (Kurmandschi) | Hilfe `H-help-01` | 14 Seiten, 33.869 Wörter | 3 | `GUIDE.md`, Wellen `S` und Administration |
| 16 | `ara-SA/H-help-02` | Arabisch | Hilfe `H-help-02` | 14 Seiten, 17.297 Wörter | 4 | `GUIDE.md`, RTL, PDF-Schrift Arab, Wellen `S` und Administration |
| 17 | `tur-TR/H-help-02` | Türkisch | Hilfe `H-help-02` | 14 Seiten, 17.297 Wörter | 2 | `GUIDE.md`, Wellen `S` und Administration |
| 18 | `ukr-UA/H-help-02` | Ukrainisch | Hilfe `H-help-02` | 14 Seiten, 17.297 Wörter | 3 | `GUIDE.md`, Wellen `S` und Administration |
| 19 | `rus-RU/H-help-02` | Russisch | Hilfe `H-help-02` | 14 Seiten, 17.297 Wörter | 3 | `GUIDE.md`, Wellen `S` und Administration |
| 20 | `fas-IR/H-help-02` | Persisch | Hilfe `H-help-02` | 14 Seiten, 17.297 Wörter | 4 | `GUIDE.md`, RTL, PDF-Schrift Arab, Wellen `S` und Administration |
| 21 | `pus-AF/H-help-02` | Paschtu | Hilfe `H-help-02` | 14 Seiten, 17.297 Wörter | 5 | `GUIDE.md`, RTL, PDF-Schrift Arab, Wellen `S` und Administration |
| 22 | `kur-TR/H-help-02` | Kurdisch (Kurmandschi) | Hilfe `H-help-02` | 14 Seiten, 17.297 Wörter | 3 | `GUIDE.md`, Wellen `S` und Administration |
| 23 | `ara-SA/H-help-03` | Arabisch | Hilfe `H-help-03` | 14 Seiten, 35.423 Wörter | 4 | `GUIDE.md`, RTL, PDF-Schrift Arab, Wellen `S` und Administration |
| 24 | `tur-TR/H-help-03` | Türkisch | Hilfe `H-help-03` | 14 Seiten, 35.423 Wörter | 2 | `GUIDE.md`, Wellen `S` und Administration |
| 25 | `ukr-UA/H-help-03` | Ukrainisch | Hilfe `H-help-03` | 14 Seiten, 35.423 Wörter | 3 | `GUIDE.md`, Wellen `S` und Administration |
| 26 | `rus-RU/H-help-03` | Russisch | Hilfe `H-help-03` | 14 Seiten, 35.423 Wörter | 3 | `GUIDE.md`, Wellen `S` und Administration |
| 27 | `fas-IR/H-help-03` | Persisch | Hilfe `H-help-03` | 14 Seiten, 35.423 Wörter | 4 | `GUIDE.md`, RTL, PDF-Schrift Arab, Wellen `S` und Administration |
| 28 | `pus-AF/H-help-03` | Paschtu | Hilfe `H-help-03` | 14 Seiten, 35.423 Wörter | 5 | `GUIDE.md`, RTL, PDF-Schrift Arab, Wellen `S` und Administration |
| 29 | `kur-TR/H-help-03` | Kurdisch (Kurmandschi) | Hilfe `H-help-03` | 14 Seiten, 35.423 Wörter | 3 | `GUIDE.md`, Wellen `S` und Administration |
| 30 | `ara-SA/H-help-manual-01` | Arabisch | Hilfe `H-help-manual-01` | 44 Seiten, 17.659 Wörter | 4 | `GUIDE.md`, RTL, PDF-Schrift Arab, Wellen `S` und Administration |
| 31 | `tur-TR/H-help-manual-01` | Türkisch | Hilfe `H-help-manual-01` | 44 Seiten, 17.659 Wörter | 2 | `GUIDE.md`, Wellen `S` und Administration |
| 32 | `ukr-UA/H-help-manual-01` | Ukrainisch | Hilfe `H-help-manual-01` | 44 Seiten, 17.659 Wörter | 3 | `GUIDE.md`, Wellen `S` und Administration |
| 33 | `rus-RU/H-help-manual-01` | Russisch | Hilfe `H-help-manual-01` | 44 Seiten, 17.659 Wörter | 3 | `GUIDE.md`, Wellen `S` und Administration |
| 34 | `fas-IR/H-help-manual-01` | Persisch | Hilfe `H-help-manual-01` | 44 Seiten, 17.659 Wörter | 4 | `GUIDE.md`, RTL, PDF-Schrift Arab, Wellen `S` und Administration |
| 35 | `pus-AF/H-help-manual-01` | Paschtu | Hilfe `H-help-manual-01` | 44 Seiten, 17.659 Wörter | 5 | `GUIDE.md`, RTL, PDF-Schrift Arab, Wellen `S` und Administration |
| 36 | `kur-TR/H-help-manual-01` | Kurdisch (Kurmandschi) | Hilfe `H-help-manual-01` | 44 Seiten, 17.659 Wörter | 3 | `GUIDE.md`, Wellen `S` und Administration |
| 37 | `ara-SA/H-help-manual-02` | Arabisch | Hilfe `H-help-manual-02` | 44 Seiten, 18.751 Wörter | 4 | `GUIDE.md`, RTL, PDF-Schrift Arab, Wellen `S` und Administration |
| 38 | `tur-TR/H-help-manual-02` | Türkisch | Hilfe `H-help-manual-02` | 44 Seiten, 18.751 Wörter | 2 | `GUIDE.md`, Wellen `S` und Administration |
| 39 | `ukr-UA/H-help-manual-02` | Ukrainisch | Hilfe `H-help-manual-02` | 44 Seiten, 18.751 Wörter | 3 | `GUIDE.md`, Wellen `S` und Administration |
| 40 | `rus-RU/H-help-manual-02` | Russisch | Hilfe `H-help-manual-02` | 44 Seiten, 18.751 Wörter | 3 | `GUIDE.md`, Wellen `S` und Administration |
| 41 | `fas-IR/H-help-manual-02` | Persisch | Hilfe `H-help-manual-02` | 44 Seiten, 18.751 Wörter | 4 | `GUIDE.md`, RTL, PDF-Schrift Arab, Wellen `S` und Administration |
| 42 | `pus-AF/H-help-manual-02` | Paschtu | Hilfe `H-help-manual-02` | 44 Seiten, 18.751 Wörter | 5 | `GUIDE.md`, RTL, PDF-Schrift Arab, Wellen `S` und Administration |
| 43 | `kur-TR/H-help-manual-02` | Kurdisch (Kurmandschi) | Hilfe `H-help-manual-02` | 44 Seiten, 18.751 Wörter | 3 | `GUIDE.md`, Wellen `S` und Administration |
| 44 | `ara-SA/H-help-non-technical-people` | Arabisch | Hilfe `H-help-non-technical-people` | 48 Seiten, 28.376 Wörter | 4 | `GUIDE.md`, RTL, PDF-Schrift Arab, Wellen `S` und Administration |
| 45 | `tur-TR/H-help-non-technical-people` | Türkisch | Hilfe `H-help-non-technical-people` | 48 Seiten, 28.376 Wörter | 2 | `GUIDE.md`, Wellen `S` und Administration |
| 46 | `ukr-UA/H-help-non-technical-people` | Ukrainisch | Hilfe `H-help-non-technical-people` | 48 Seiten, 28.376 Wörter | 3 | `GUIDE.md`, Wellen `S` und Administration |
| 47 | `rus-RU/H-help-non-technical-people` | Russisch | Hilfe `H-help-non-technical-people` | 48 Seiten, 28.376 Wörter | 3 | `GUIDE.md`, Wellen `S` und Administration |
| 48 | `fas-IR/H-help-non-technical-people` | Persisch | Hilfe `H-help-non-technical-people` | 48 Seiten, 28.376 Wörter | 4 | `GUIDE.md`, RTL, PDF-Schrift Arab, Wellen `S` und Administration |
| 49 | `pus-AF/H-help-non-technical-people` | Paschtu | Hilfe `H-help-non-technical-people` | 48 Seiten, 28.376 Wörter | 5 | `GUIDE.md`, RTL, PDF-Schrift Arab, Wellen `S` und Administration |
| 50 | `kur-TR/H-help-non-technical-people` | Kurdisch (Kurmandschi) | Hilfe `H-help-non-technical-people` | 48 Seiten, 28.376 Wörter | 3 | `GUIDE.md`, Wellen `S` und Administration |
| 51 | `ara-SA/H-guides-01` | Arabisch | Hilfe `H-guides-01` | 13 Seiten, 31.813 Wörter | 4 | `GUIDE.md`, RTL, PDF-Schrift Arab, Wellen `S` und Administration |
| 52 | `tur-TR/H-guides-01` | Türkisch | Hilfe `H-guides-01` | 13 Seiten, 31.813 Wörter | 2 | `GUIDE.md`, Wellen `S` und Administration |
| 53 | `ukr-UA/H-guides-01` | Ukrainisch | Hilfe `H-guides-01` | 13 Seiten, 31.813 Wörter | 3 | `GUIDE.md`, Wellen `S` und Administration |
| 54 | `rus-RU/H-guides-01` | Russisch | Hilfe `H-guides-01` | 13 Seiten, 31.813 Wörter | 3 | `GUIDE.md`, Wellen `S` und Administration |
| 55 | `fas-IR/H-guides-01` | Persisch | Hilfe `H-guides-01` | 13 Seiten, 31.813 Wörter | 4 | `GUIDE.md`, RTL, PDF-Schrift Arab, Wellen `S` und Administration |
| 56 | `pus-AF/H-guides-01` | Paschtu | Hilfe `H-guides-01` | 13 Seiten, 31.813 Wörter | 5 | `GUIDE.md`, RTL, PDF-Schrift Arab, Wellen `S` und Administration |
| 57 | `kur-TR/H-guides-01` | Kurdisch (Kurmandschi) | Hilfe `H-guides-01` | 13 Seiten, 31.813 Wörter | 3 | `GUIDE.md`, Wellen `S` und Administration |
| 58 | `ara-SA/H-guides-02` | Arabisch | Hilfe `H-guides-02` | 14 Seiten, 31.783 Wörter | 4 | `GUIDE.md`, RTL, PDF-Schrift Arab, Wellen `S` und Administration |
| 59 | `tur-TR/H-guides-02` | Türkisch | Hilfe `H-guides-02` | 14 Seiten, 31.783 Wörter | 2 | `GUIDE.md`, Wellen `S` und Administration |
| 60 | `ukr-UA/H-guides-02` | Ukrainisch | Hilfe `H-guides-02` | 14 Seiten, 31.783 Wörter | 3 | `GUIDE.md`, Wellen `S` und Administration |

Die ganze Warteschlange und die Seiten einer Welle: `./console exp:i18n waves --list`, `./console exp:i18n waves --wave=<Welle>`.

Erzeugt mit `./console exp:i18n waves`; `./console exp:i18n waves --check` und der Test `expI18nTranslationWavesTest` schlagen fehl, sobald ein Prozentwert nicht mehr stimmt oder ein Umfang um mehr als 3 % abweicht.
<!-- exp:i18n waves data {"status":{"afr-ZA":[100,100,0,0],"aka-GH":[100,100,0,0],"amh-ET":[100,100,0,0],"ara-SA":[100,100,0,0],"arc-IQ":[100,100,0,0],"asm-IN":[100,100,0,0],"ast-ES":[100,100,0,0],"aze-AZ":[100,100,0,0],"bam-ML":[100,100,0,0],"bel-BY":[100,100,0,0],"ben-BD":[100,100,0,0],"bho-IN":[100,100,0,0],"bod-CN":[100,100,0,0],"bos-BA":[100,100,0,0],"bre-FR":[100,100,0,0],"brx-IN":[100,100,0,0],"bul-BG":[100,100,0,0],"cat-ES":[100,100,0,0],"ceb-PH":[100,100,0,0],"che-RU":[100,100,0,0],"chi-CN":[100,100,0,0],"chi-TW":[100,100,0,0],"ckb-IQ":[100,100,0,0],"cro-HR":[100,100,0,0],"cym-GB":[100,100,0,0],"cze-CZ":[100,100,0,0],"dan-DK":[100,100,0,0],"div-MV":[100,100,0,0],"doi-IN":[100,100,0,0],"dsb-DE":[100,100,0,0],"dut-NL":[100,100,0,0],"dzo-BT":[100,100,0,0],"ell-GR":[100,100,0,0],"eng-AU":[100,100,100,100],"eng-US":[100,100,100,100],"epo-EO":[100,100,0,0],"esl-ES":[100,100,0,0],"esl-MX":[100,100,0,0],"est-EE":[100,100,0,0],"eus-ES":[100,100,0,0],"ewe-GH":[100,100,0,0],"fao-FO":[100,100,0,0],"fas-AF":[100,100,0,0],"fas-IR":[100,100,0,0],"fil-PH":[100,100,0,0],"fin-FI":[100,100,0,0],"fre-FR":[100,100,0,0],"fry-NL":[100,100,0,0],"ful-SN":[100,100,0,0],"ger-DE":[100,76,100,100],"gla-GB":[100,100,0,0],"gle-IE":[100,100,0,0],"glg-ES":[100,100,0,0],"grn-PY":[100,80,0,0],"guj-IN":[100,100,0,0],"hau-NG":[100,100,0,0],"haw-US":[100,100,0,0],"heb-IL":[100,100,0,0],"hin-IN":[100,100,0,0],"hsb-DE":[100,100,0,0],"hun-HU":[100,100,0,0],"huy-IL":[100,100,0,0],"hye-AM":[100,100,0,0],"ibo-NG":[100,100,0,0],"ind-ID":[100,100,0,0],"isl-IS":[100,100,0,0],"ita-IT":[100,100,0,0],"jav-ID":[100,100,0,0],"jpn-JP":[100,100,0,0],"kab-DZ":[100,100,0,0],"kal-GL":[100,100,0,0],"kan-IN":[100,100,0,0],"kas-IN":[100,100,0,0],"kat-GE":[100,100,0,0],"kaz-KZ":[100,100,0,0],"khm-KH":[100,100,0,0],"kik-KE":[100,100,0,0],"kin-RW":[100,100,0,0],"kir-KG":[100,100,0,0],"kok-IN":[100,100,0,0],"kor-KP":[100,100,0,0],"kor-KR":[100,100,0,0],"kur-TR":[100,100,0,0],"lao-LA":[100,100,0,0],"lat-VA":[100,100,0,0],"lav-LV":[100,100,0,0],"lin-CD":[100,100,0,0],"lit-LT":[100,100,0,0],"ltz-LU":[100,100,0,0],"lug-UG":[100,100,0,0],"mai-IN":[100,100,0,0],"mal-IN":[100,100,0,0],"mar-IN":[100,100,0,0],"mkd-MK":[100,100,0,0],"mlg-MG":[100,100,0,0],"mlt-MT":[100,100,0,0],"mni-IN":[100,100,0,0],"mon-MN":[100,100,0,0],"mri-NZ":[100,100,0,0],"msa-MY":[100,100,0,0],"mya-MM":[100,100,0,0],"nep-NP":[100,100,0,0],"nor-NO":[100,100,0,0],"nqo-GN":[100,100,0,0],"oci-FR":[100,100,0,0],"ori-IN":[100,100,0,0],"orm-ET":[100,100,0,0],"pan-IN":[100,100,0,0],"pcm-NG":[100,100,0,0],"pnb-PK":[100,100,0,0],"pol-PL":[100,100,0,0],"por-BR":[100,100,0,0],"por-PT":[100,100,0,0],"pus-AF":[100,100,0,0],"que-PE":[100,100,0,0],"roh-CH":[100,100,0,0],"ron-RO":[100,100,0,0],"run-BI":[100,100,0,0],"rus-RU":[100,100,0,0],"san-IN":[100,100,0,0],"sat-IN":[100,100,0,0],"ser-SR":[100,100,0,0],"sin-LK":[100,100,0,0],"slk-SK":[100,100,0,0],"slv-SI":[100,100,0,0],"sme-NO":[100,100,0,0],"sna-ZW":[100,100,0,0],"snd-PK":[100,100,0,0],"som-SO":[100,100,0,0],"sqi-AL":[100,100,0,0],"srp-RS":[100,100,0,0],"sun-ID":[100,100,0,0],"swa-KE":[100,100,0,0],"swe-SE":[100,100,0,0],"syr-IQ":[100,100,0,0],"tam-IN":[100,100,0,0],"tat-RU":[100,100,0,0],"tel-IN":[100,100,0,0],"tgk-TJ":[100,100,0,0],"tha-TH":[100,100,0,0],"tir-ET":[100,100,0,0],"tlh-QO":[100,100,0,0],"ton-TO":[100,100,0,0],"tuk-TM":[100,100,0,0],"tur-TR":[100,100,0,0],"uig-CN":[100,100,0,0],"ukr-UA":[100,100,0,0],"urd-PK":[100,100,0,0],"uzb-UZ":[100,100,0,0],"vie-VN":[100,100,0,0],"wol-SN":[100,100,0,0],"xho-ZA":[100,100,0,0],"yid-US":[100,100,0,0],"yor-NG":[100,100,0,0],"zgh-MA":[100,100,0,0],"zul-ZA":[100,100,0,0]},"sizes":{"messages":20855,"extensions":567,"parts":216,"daily_pages":343,"daily_words":443905,"guides_pages":3803,"guides_words":3024465,"reference_pages":1817,"reference_words":1864251,"tables_pages":0,"tables_words":112725},"waves":33675,"untranslated":["hat-HT","skr-PK","tsn-ZA","dyu-CI","nya-MW","nso-ZA","sot-ZA","mos-BF","azb-IR","kau-NG","vmw-MZ","kon-CD","luy-KE","tso-ZA","umb-AO","luo-KE","sag-CF","bal-PK","kln-KE","lua-CD","suk-TZ","ale-US","arn-CL","aym-BO","chr-US","cre-CA","ems-US","esu-US","gwi-US","hai-US","iku-CA","ipk-US","lkt-US","moh-CA","nah-MX","nav-US","oji-CA","quc-GT","tli-US","tsi-US","yua-MX"]} -->
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
