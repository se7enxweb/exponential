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

<!-- generated: exp:i18n waves 2026-11-27 (start) -->
**Gemessen am 27. November 2026** mit `./console exp:i18n waves` aus den Dateien dieser Version (ohne Datenbank):

| Was | Umfang |
|---|---:|
| Administration: Texte in `share/translations/untranslated/translation.ts`, in 11 Teilen | **21.355** (144.038 Wörter) |
| Mitgelieferte Erweiterungen (`expimageeditor`, `ezjscore`, `ezoe`) | **567** Texte |
| Hilfe: Seiten unter `doc/eng-us/` (ohne die erzeugten Beispielseiten) | **6.042** Seiten, 5.542.032 Wörter |
| Hilfe: Tabellen der erzeugten Beispielseiten (`.examples/`) | **4.435** Einträge, 124.226 Wörter |
| Übersetzungen der Administration, davon vollständig | **156**, 3 |
| davon im täglichen Gebrauch (Administration, Erweiterungen und Hilfe des Alltags ≥ 97 %) | **2** |
| Sprachen ohne Übersetzung der Administration ([Liste](#sprachen-ohne-übersetzung)), davon auf einem Branch in Arbeit | **41**, 0 |
| Wellen bis 100 % in jeder Sprache | **34.899** |

### Wo jede Sprache steht

Prozent der Texte (Administration, Erweiterungen) und der Wörter (Hilfe), abgerundet. **Fett**: unter 97 %. „Hilfe des Alltags“ sind die Teile für Redakteure, Benutzer und Administratoren ([Stufen](#die-hilfe-in-stufen)).

| Sprache | Code | Sprecher (Mio.) | Schwierigkeit | Administration | Erweiterungen | Hilfe des Alltags | Hilfe gesamt | Wellen offen | Nächste Welle |
|---|---|---:|:-:|---:|---:|---:|---:|---:|---|
| Afrikaans | `afr-ZA` | 17 | 2 | 99 | 100 | **0** | **0** | 225 | #668 `A07` |
| Akan | `aka-GH` | 11 | 3 | 99 | 100 | **0** | **0** | 225 | #693 `A07` |
| Albanisch | `sqi-AL` | 8 | 2 | 99 | 100 | **0** | **0** | 225 | #692 `A07` |
| Amharisch | `amh-ET` | 60 | 4 | 99 | 100 | **0** | **0** | 225 | #652 `A07` |
| Arabisch | `ara-SA` | 400 | 4 | 99 | 100 | **0** | **0** | 225 | #2 `A07` |
| Aramäisch | `arc-IQ` | < 1 | 5 | 99 | 100 | **0** | **0** | 225 | #748 `A07` |
| Armenisch | `hye-AM` | 5 | 4 | 99 | 100 | **0** | **0** | 225 | #717 `A07` |
| Aserbaidschanisch | `aze-AZ` | 10 | 3 | 99 | 100 | **0** | **0** | 225 | #695 `A07` |
| Assamesisch | `asm-IN` | 15 | 5 | 99 | 100 | **0** | **0** | 225 | #697 `A07` |
| Asturisch | `ast-ES` | < 1 | 3 | 99 | 100 | **0** | **0** | 225 | #746 `A07` |
| Bambara | `bam-ML` | 14 | 3 | 99 | 100 | **0** | **0** | 225 | #684 `A07` |
| Baskisch | `eus-ES` | < 1 | 3 | 99 | 100 | **0** | **0** | 225 | #739 `A07` |
| Belarussisch | `bel-BY` | 5 | 4 | 99 | 100 | **0** | **0** | 225 | #718 `A07` |
| Bengalisch | `ben-BD` | 285 | 4 | 99 | 100 | **0** | **0** | 225 | #629 `A07` |
| Bhojpuri | `bho-IN` | 52 | 5 | 99 | 100 | **0** | **0** | 225 | #659 `A07` |
| Birmanisch | `mya-MM` | 43 | 5 | 99 | 100 | **0** | **0** | 225 | #667 `A07` |
| Bodo | `brx-IN` | 2 | 5 | 99 | 100 | **0** | **0** | 225 | #737 `A07` |
| Bosnisch | `bos-BA` | 3 | 3 | 99 | 100 | **0** | **0** | 225 | #723 `A07` |
| Bretonisch | `bre-FR` | < 1 | 4 | 99 | 100 | **0** | **0** | 225 | #753 `A07` |
| Bulgarisch | `bul-BG` | 8 | 2 | 99 | 100 | **0** | **0** | 225 | #688 `A07` |
| Cebuano | `ceb-PH` | 28 | 3 | 99 | 100 | **0** | **0** | 225 | #663 `A07` |
| Chinesisch (traditionell) | `chi-TW` | 31 | 2 | 99 | 100 | **0** | **0** | 225 | #648 `A07` |
| Chinesisch (vereinfacht) | `chi-CN` | 1.100 | 2 | 99 | 100 | **0** | **0** | 225 | #621 `A07` |
| Dänisch | `dan-DK` | 6 | 1 | 99 | 100 | **0** | **0** | 225 | #679 `A07` |
| Dari (Persisch in Afghanistan) | `fas-AF` | 35 | 4 | 99 | 100 | **0** | **0** | 225 | #665 `A07` |
| Deutsch | `ger-DE` | 135 | 2 | 100 | **76** | 100 | 100 | 1 | #1 `E` |
| Dhivehi | `div-MV` | < 1 | 5 | 99 | 100 | **0** | **0** | 225 | #750 `A07` |
| Dogri | `doi-IN` | 3 | 5 | 99 | 100 | **0** | **0** | 225 | #731 `A07` |
| Dzongkha | `dzo-BT` | < 1 | 5 | 99 | 100 | **0** | **0** | 225 | #745 `A07` |
| Englisch (Australien) | `eng-AU` | 1.500 | 1 | 100 | 100 | 100 | 100 | 0 | Quelltexte |
| Englisch (USA) | `eng-US` | 1.500 | 1 | 100 | 100 | 100 | 100 | 0 | Quelltexte |
| Esperanto | `epo-EO` | < 1 | 2 | 99 | 100 | **0** | **0** | 225 | #754 `A07` |
| Estnisch | `est-EE` | 1 | 3 | 99 | 100 | **0** | **0** | 225 | #734 `A07` |
| Ewe | `ewe-GH` | 5 | 3 | 99 | 100 | **0** | **0** | 225 | #711 `A07` |
| Färöisch | `fao-FO` | < 1 | 4 | 99 | 100 | **0** | **0** | 225 | #757 `A07` |
| Filipino | `fil-PH` | 83 | 2 | 99 | 100 | **0** | **0** | 225 | #634 `A07` |
| Finnisch | `fin-FI` | 6 | 2 | 99 | 100 | **0** | **0** | 225 | #699 `A07` |
| Französisch | `fre-FR` | 310 | 1 | 99 | 100 | **0** | **0** | 225 | #623 `A07` |
| Fulfulde | `ful-SN` | 37 | 4 | 99 | 100 | **0** | **0** | 225 | #664 `A07` |
| Galicisch | `glg-ES` | 2 | 2 | 99 | 100 | **0** | **0** | 225 | #721 `A07` |
| Georgisch | `kat-GE` | 4 | 4 | 99 | 100 | **0** | **0** | 225 | #727 `A07` |
| Griechisch | `ell-GR` | 13 | 3 | 99 | 100 | **0** | **0** | 225 | #687 `A07` |
| Grönländisch | `kal-GL` | < 1 | 4 | 99 | 100 | **0** | **0** | 225 | #758 `A07` |
| Guaraní | `grn-PY` | 7 | 4 | 99 | **80** | **0** | **0** | 226 | #712 `A07` |
| Gujarati | `guj-IN` | 62 | 4 | 99 | 100 | **0** | **0** | 225 | #649 `A07` |
| Hausa | `hau-NG` | 94 | 3 | 99 | 100 | **0** | **0** | 225 | #637 `A07` |
| Hawaiisch | `haw-US` | < 1 | 3 | 99 | 100 | **0** | **0** | 225 | #760 `A07` |
| Hebräisch | `heb-IL` | 9 | 4 | 99 | 100 | **0** | **0** | 225 | #704 `A07` |
| Hindi | `hin-IN` | 610 | 4 | 99 | 100 | **0** | **0** | 225 | #626 `A07` |
| Hulaulá (jüdisches Neuaramäisch) | `huy-IL` | < 1 | 5 | 99 | 100 | **0** | **0** | 225 | #764 `A07` |
| Igbo | `ibo-NG` | 31 | 3 | 99 | 100 | **0** | **0** | 225 | #660 `A07` |
| Indonesisch | `ind-ID` | 252 | 1 | 99 | 100 | **0** | **0** | 225 | #624 `A07` |
| Irisch | `gle-IE` | 2 | 4 | 99 | 100 | **0** | **0** | 225 | #732 `A07` |
| isiXhosa | `xho-ZA` | 19 | 4 | 99 | 100 | **0** | **0** | 225 | #683 `A07` |
| isiZulu | `zul-ZA` | 28 | 4 | 99 | 100 | **0** | **0** | 225 | #675 `A07` |
| Isländisch | `isl-IS` | < 1 | 3 | 99 | 100 | **0** | **0** | 225 | #747 `A07` |
| Italienisch | `ita-IT` | 68 | 1 | 99 | 100 | **0** | **0** | 225 | #630 `A07` |
| Japanisch | `jpn-JP` | 123 | 3 | 99 | 100 | **0** | **0** | 225 | #635 `A07` |
| Javanisch | `jav-ID` | 68 | 3 | 99 | 100 | **0** | **0** | 225 | #643 `A07` |
| Jiddisch | `yid-US` | 1 | 5 | 99 | 100 | **0** | **0** | 225 | #741 `A07` |
| Kabylisch | `kab-DZ` | 5 | 4 | 99 | 100 | **0** | **0** | 225 | #719 `A07` |
| Kannada | `kan-IN` | 59 | 5 | 99 | 100 | **0** | **0** | 225 | #655 `A07` |
| Kasachisch | `kaz-KZ` | 16 | 4 | 99 | 100 | **0** | **0** | 225 | #689 `A07` |
| Kaschmiri | `kas-IN` | 7 | 5 | 99 | 100 | **0** | **0** | 225 | #716 `A07` |
| Katalanisch | `cat-ES` | 10 | 1 | 99 | 100 | **0** | **0** | 225 | #661 `A07` |
| Khmer | `khm-KH` | 18 | 4 | 99 | 100 | **0** | **0** | 225 | #685 `A07` |
| Kikuyu | `kik-KE` | 8 | 4 | 99 | 100 | **0** | **0** | 225 | #707 `A07` |
| Kinyarwanda | `kin-RW` | 15 | 4 | 99 | 100 | **0** | **0** | 225 | #690 `A07` |
| Kirgisisch | `kir-KG` | 5 | 5 | 99 | 100 | **0** | **0** | 225 | #724 `A07` |
| Kirundi | `run-BI` | 12 | 4 | 99 | 100 | **0** | **0** | 225 | #698 `A07` |
| Klingonisch | `tlh-QO` | < 1 | 4 | 99 | 100 | **0** | **0** | 225 | #767 `A07` |
| Konkani | `kok-IN` | 2 | 5 | 99 | 100 | **0** | **0** | 225 | #733 `A07` |
| Koreanisch | `kor-KR` | 82 | 3 | 99 | 100 | **0** | **0** | 225 | #638 `A07` |
| Koreanisch (Nordkorea, Munhwaŏ) | `kor-KP` | 26 | 3 | 99 | 100 | **0** | **0** | 225 | #666 `A07` |
| Kroatisch | `cro-HR` | 5 | 2 | 99 | 100 | **0** | **0** | 225 | #702 `A07` |
| Kurdisch (Kurmandschi) | `kur-TR` | 16 | 3 | 99 | 100 | **0** | **0** | 225 | #8 `A07` |
| Laotisch | `lao-LA` | 30 | 5 | 99 | 100 | **0** | **0** | 225 | #680 `A07` |
| Latein | `lat-VA` | < 1 | 4 | 99 | 100 | **0** | **0** | 225 | #765 `A07` |
| Lettisch | `lav-LV` | 2 | 3 | 99 | 100 | **0** | **0** | 225 | #730 `A07` |
| Lingala | `lin-CD` | 40 | 4 | 99 | 100 | **0** | **0** | 225 | #662 `A07` |
| Litauisch | `lit-LT` | 3 | 3 | 99 | 100 | **0** | **0** | 225 | #725 `A07` |
| Luganda | `lug-UG` | 11 | 4 | 99 | 100 | **0** | **0** | 225 | #701 `A07` |
| Luxemburgisch | `ltz-LU` | < 1 | 2 | 99 | 100 | **0** | **0** | 225 | #740 `A07` |
| Maithili | `mai-IN` | 34 | 5 | 99 | 100 | **0** | **0** | 225 | #676 `A07` |
| Malagasy | `mlg-MG` | 25 | 3 | 99 | 100 | **0** | **0** | 225 | #669 `A07` |
| Malaiisch | `msa-MY` | 60 | 1 | 99 | 100 | **0** | **0** | 225 | #631 `A07` |
| Malayalam | `mal-IN` | 37 | 5 | 99 | 100 | **0** | **0** | 225 | #673 `A07` |
| Maltesisch | `mlt-MT` | < 1 | 3 | 99 | 100 | **0** | **0** | 225 | #742 `A07` |
| Maori | `mri-NZ` | < 1 | 3 | 99 | 100 | **0** | **0** | 225 | #751 `A07` |
| Marathi | `mar-IN` | 99 | 4 | 99 | 100 | **0** | **0** | 225 | #642 `A07` |
| Marokkanisches Standard-Tamazight | `zgh-MA` | 14 | 5 | 99 | 100 | **0** | **0** | 225 | #700 `A07` |
| Mazedonisch | `mkd-MK` | 2 | 3 | 99 | 100 | **0** | **0** | 225 | #729 `A07` |
| Meitei | `mni-IN` | 2 | 5 | 99 | 100 | **0** | **0** | 225 | #736 `A07` |
| Mongolisch | `mon-MN` | 6 | 5 | 99 | 100 | **0** | **0** | 225 | #722 `A07` |
| Nepali | `nep-NP` | 32 | 4 | 99 | 100 | **0** | **0** | 225 | #670 `A07` |
| Niederländisch | `dut-NL` | 25 | 1 | 99 | 100 | **0** | **0** | 225 | #640 `A07` |
| Niedersorbisch | `dsb-DE` | < 1 | 4 | 99 | 100 | **0** | **0** | 225 | #763 `A07` |
| Nigerianisches Pidgin | `pcm-NG` | 120 | 3 | 99 | 100 | **0** | **0** | 225 | #636 `A07` |
| Nordsamisch | `sme-NO` | < 1 | 4 | 99 | 100 | **0** | **0** | 225 | #759 `A07` |
| Norwegisch | `nor-NO` | 5 | 1 | 99 | 100 | **0** | **0** | 225 | #682 `A07` |
| N’Ko | `nqo-GN` | – | 5 | 99 | 100 | **0** | **0** | 225 | #766 `A07` |
| Obersorbisch | `hsb-DE` | < 1 | 4 | 99 | 100 | **0** | **0** | 225 | #762 `A07` |
| Okzitanisch | `oci-FR` | < 1 | 3 | 99 | 100 | **0** | **0** | 225 | #744 `A07` |
| Oriya | `ori-IN` | 40 | 5 | 99 | 100 | **0** | **0** | 225 | #671 `A07` |
| Oromo | `orm-ET` | 45 | 4 | 99 | 100 | **0** | **0** | 225 | #657 `A07` |
| Panjabi | `pan-IN` | 48 | 4 | 99 | 100 | **0** | **0** | 225 | #654 `A07` |
| Paschtu | `pus-AF` | 60 | 5 | 99 | 100 | **0** | **0** | 225 | #7 `A07` |
| Persisch | `fas-IR` | 83 | 4 | 99 | 100 | **0** | **0** | 225 | #6 `A07` |
| Polnisch | `pol-PL` | 40 | 2 | 99 | 100 | **0** | **0** | 225 | #644 `A07` |
| Portugiesisch (Brasilien) | `por-BR` | 215 | 1 | 99 | 100 | **0** | **0** | 225 | #625 `A07` |
| Portugiesisch (Portugal) | `por-PT` | 50 | 1 | 99 | 100 | **0** | **0** | 225 | #633 `A07` |
| Quechua | `que-PE` | 8 | 4 | 99 | 100 | **0** | **0** | 225 | #708 `A07` |
| Rätoromanisch | `roh-CH` | < 1 | 3 | 99 | 100 | **0** | **0** | 225 | #756 `A07` |
| Rumänisch | `ron-RO` | 25 | 1 | 99 | 100 | **0** | **0** | 225 | #641 `A07` |
| Russisch | `rus-RU` | 255 | 3 | 99 | 100 | **0** | **0** | 225 | #5 `A07` |
| Sanskrit | `san-IN` | < 1 | 5 | 99 | 100 | **0** | **0** | 225 | #761 `A07` |
| Santali | `sat-IN` | 8 | 5 | 99 | 100 | **0** | **0** | 225 | #714 `A07` |
| Schottisch-Gälisch | `gla-GB` | < 1 | 4 | 99 | 100 | **0** | **0** | 225 | #755 `A07` |
| Schwedisch | `swe-SE` | 13 | 1 | 99 | 100 | **0** | **0** | 225 | #653 `A07` |
| Serbisch (kyrillisch) | `srp-RS` | 10 | 4 | 99 | 100 | **0** | **0** | 225 | #703 `A07` |
| Serbisch (lateinisch) | `ser-SR` | 10 | 3 | 99 | 100 | **0** | **0** | 225 | #696 `A07` |
| Shona | `sna-ZW` | 15 | 4 | 99 | 100 | **0** | **0** | 225 | #691 `A07` |
| Sindhi | `snd-PK` | 34 | 5 | 99 | 100 | **0** | **0** | 225 | #677 `A07` |
| Singhalesisch | `sin-LK` | 18 | 4 | 99 | 100 | **0** | **0** | 225 | #686 `A07` |
| Slowakisch | `slk-SK` | 7 | 2 | 99 | 100 | **0** | **0** | 225 | #694 `A07` |
| Slowenisch | `slv-SI` | 3 | 3 | 99 | 100 | **0** | **0** | 225 | #728 `A07` |
| Somali | `som-SO` | 24 | 3 | 99 | 100 | **0** | **0** | 225 | #672 `A07` |
| Spanisch (Mexiko) | `esl-MX` | 130 | 1 | 99 | 100 | **0** | **0** | 225 | #627 `A07` |
| Spanisch (Spanien) | `esl-ES` | 430 | 1 | 99 | 100 | **0** | **0** | 225 | #622 `A07` |
| Sundanesisch | `sun-ID` | 32 | 3 | 99 | 100 | **0** | **0** | 225 | #658 `A07` |
| Swahili | `swa-KE` | 80 | 3 | 99 | 100 | **0** | **0** | 225 | #639 `A07` |
| Syrisch (Aramäisch) | `syr-IQ` | < 1 | 5 | 99 | 100 | **0** | **0** | 225 | #749 `A07` |
| Tadschikisch | `tgk-TJ` | 9 | 4 | 99 | 100 | **0** | **0** | 225 | #705 `A07` |
| Tamil | `tam-IN` | 87 | 5 | 99 | 100 | **0** | **0** | 225 | #647 `A07` |
| Tatarisch | `tat-RU` | 5 | 5 | 99 | 100 | **0** | **0** | 225 | #726 `A07` |
| Telugu | `tel-IN` | 96 | 5 | 99 | 100 | **0** | **0** | 225 | #645 `A07` |
| Thai | `tha-TH` | 61 | 4 | 99 | 100 | **0** | **0** | 225 | #651 `A07` |
| Tibetisch | `bod-CN` | 6 | 5 | 99 | 100 | **0** | **0** | 225 | #720 `A07` |
| Tigrinya | `tir-ET` | 10 | 5 | 99 | 100 | **0** | **0** | 225 | #709 `A07` |
| Tongaisch | `ton-TO` | < 1 | 3 | 99 | 100 | **0** | **0** | 225 | #752 `A07` |
| Tschechisch | `cze-CZ` | 11 | 2 | 99 | 100 | **0** | **0** | 225 | #681 `A07` |
| Tschetschenisch | `che-RU` | 2 | 5 | 99 | 100 | **0** | **0** | 225 | #735 `A07` |
| Türkisch | `tur-TR` | 90 | 2 | 99 | 100 | **0** | **0** | 225 | #3 `A07` |
| Turkmenisch | `tuk-TM` | 7 | 4 | 99 | 100 | **0** | **0** | 225 | #710 `A07` |
| Uigurisch | `uig-CN` | 11 | 5 | 99 | 100 | **0** | **0** | 225 | #706 `A07` |
| Ukrainisch | `ukr-UA` | 39 | 3 | 99 | 100 | **0** | **0** | 225 | #4 `A07` |
| Ungarisch | `hun-HU` | 13 | 2 | 99 | 100 | **0** | **0** | 225 | #678 `A07` |
| Urdu | `urd-PK` | 232 | 4 | 99 | 100 | **0** | **0** | 225 | #632 `A07` |
| Usbekisch | `uzb-UZ` | 35 | 3 | 99 | 100 | **0** | **0** | 225 | #656 `A07` |
| Vietnamesisch | `vie-VN` | 86 | 1 | 99 | 100 | **0** | **0** | 225 | #628 `A07` |
| Walisisch | `cym-GB` | < 1 | 3 | 99 | 100 | **0** | **0** | 225 | #738 `A07` |
| Westfriesisch | `fry-NL` | < 1 | 3 | 99 | 100 | **0** | **0** | 225 | #743 `A07` |
| Westpanjabi | `pnb-PK` | 90 | 5 | 99 | 100 | **0** | **0** | 225 | #646 `A07` |
| Wolof | `wol-SN` | 22 | 3 | 99 | 100 | **0** | **0** | 225 | #674 `A07` |
| Yoruba | `yor-NG` | 46 | 3 | 99 | 100 | **0** | **0** | 225 | #650 `A07` |
| Zentralkurdisch (Sorani) | `ckb-IQ` | 7 | 5 | 99 | 100 | **0** | **0** | 225 | #715 `A07` |

### Die Hilfe in Stufen

| Stufe | Abschnitte | Seiten | Wörter | Teile |
|---|---|---:|---:|---:|
| Alltag: Redakteure, Benutzer, Anleitungen für Administratoren | `help`, `help/manual`, `help/non-technical-people`, `guides`, `help/book-learning`, `help/book-building-a-site` | 355 | 476.061 | 19 |
| Produkt- und Zielgruppen-Leitfäden | `install`, `features`, `competition`, `help/adopters`, `help/advocates`, `help/business`, `help/businesses`, `help/community` und 28 weitere | 3.858 | 3.173.287 | 126 |
| Entwicklerreferenz | `help/extension-development`, `help/template-language`, `help/template-operators`, `help/build-two-ways`, `help/php-developers`, `help/cms-developers`, `help/dxp-developers`, `help/software-engineers` und 12 weitere | 1.829 | 1.892.684 | 73 |
| Tabellen der erzeugten Beispiele | `examples` | 4.435 Einträge | 124.226 | 5 |

<details><summary>Die 223 Teile der Hilfe</summary>

| Teil | Stufe | Seiten | Wörter | Erste … letzte Seite |
|---|---|---:|---:|---|
| `H-help-01` | daily | 12 | 37.917 | `help/README.md` … `help/demo-mode.md` |
| `H-help-02` | daily | 12 | 16.467 | `help/develop-with-git.md` … `help/logging-into-the-editor.md` |
| `H-help-03` | daily | 12 | 20.798 | `help/maintaining-help-topics.md` … `help/security-hardening.md` |
| `H-help-04` | daily | 12 | 29.096 | `help/setting-up-editor-and-admin-accounts.md` … `help/why-exponential-supports-php-7-4.md` |
| `H-help-manual-01` | daily | 44 | 17.659 | `help/manual/README.md` … `help/manual/how-do-i-let-a-translator-edit-one-language.md` |
| `H-help-manual-02` | daily | 44 | 18.763 | `help/manual/how-do-i-move-a-user-to-another-group.md` … `help/manual/why-does-search-not-find-an-item.md` |
| `H-help-non-technical-people` | daily | 48 | 28.376 | `help/non-technical-people/a-website-without-hosting-costs.md` … `help/non-technical-people/words/the-site-and-the-administration.md` |
| `H-guides-01` | daily | 13 | 34.641 | `guides/README.md` … `guides/collected-information.md` |
| `H-guides-02` | daily | 13 | 32.199 | `guides/content-history.md` … `guides/extensions.md` |
| `H-guides-03` | daily | 13 | 32.324 | `guides/finding-an-error-by-its-reference.md` … `guides/migrations-generators-and-recording.md` |
| `H-guides-04` | daily | 13 | 25.113 | `guides/migrations-packages-deploy-and-long-runs.md` … `guides/notifications-developer.md` |
| `H-guides-05` | daily | 13 | 39.136 | `guides/object-states.md` … `guides/security-and-audit.md` |
| `H-guides-06` | daily | 13 | 33.124 | `guides/sessions.md` … `guides/smarty-templates-troubleshooting.md` |
| `H-guides-07` | daily | 13 | 14.396 | `guides/smarty-templates-tutorial.md` … `guides/static-publishing.md` |
| `H-guides-08` | daily | 13 | 21.202 | `guides/system-information.md` … `guides/twig-templates-functions.md` |
| `H-guides-09` | daily | 13 | 15.234 | `guides/twig-templates-migrating.md` … `guides/ui-switch-design.md` |
| `H-guides-10` | daily | 13 | 35.252 | `guides/ui-switch-settings.md` … `guides/your-devices-and-sessions.md` |
| `H-help-book-learning` | daily | 31 | 18.901 | `help/book-learning/README.md` … `help/book-learning/workflows-and-triggers.md` |
| `H-help-book-building-a-site` | daily | 10 | 5.463 | `help/book-building-a-site/README.md` … `help/book-building-a-site/your-first-static-site-in-15-minutes.md` |
| `H-install-01` | guides | 7 | 37.553 | `install/01-introduction.md` … `install/07-console-install.md` |
| `H-install-02` | guides | 7 | 62.441 | `install/08-serving-the-site.md` … `install/14-migrating-from-4x.md` |
| `H-install-03` | guides | 7 | 39.884 | `install/15-migrating-from-5x-legacy.md` … `install/migration/checklist.md` |
| `H-install-04` | guides | 7 | 8.467 | `install/migration/coldfusion-apps.md` … `install/migration/exponential-platform-legacy.md` |
| `H-install-05` | guides | 7 | 8.004 | `install/migration/exponential-platform-nexus.md` … `install/migration/magento.md` |
| `H-install-06` | guides | 7 | 5.299 | `install/migration/older-releases.md` … `install/migration/wordpress.md` |
| `H-features-01` | guides | 19 | 32.633 | `features/4.7/cluster_events.md` … `features/6.0/adminneo-database-manager.md` |
| `H-features-02` | guides | 19 | 48.123 | `features/6.0/audit-trail.md` … `features/6.0/couchbase-driver.md` |
| `H-features-03` | guides | 20 | 31.741 | `features/6.0/couchdb-driver.md` … `features/6.0/extension-module-override.md` |
| `H-features-04` | guides | 19 | 15.201 | `features/6.0/extensions-of-a-new-installation.md` … `features/6.0/extensions/ezautosave.md` |
| `H-features-05` | guides | 19 | 14.814 | `features/6.0/extensions/ezdemo.md` … `features/6.0/extensions/ezwt.md` |
| `H-features-06` | guides | 20 | 18.912 | `features/6.0/extensions/ezxmlexport.md` … `features/6.0/file-consistency-check.md` |
| `H-features-07` | guides | 19 | 56.298 | `features/6.0/firebird-database-driver.md` … `features/6.0/log-rotation.md` |
| `H-features-08` | guides | 20 | 33.774 | `features/6.0/log-viewer.md` … `features/6.0/oracle-dfs-cluster.md` |
| `H-features-09` | guides | 19 | 25.672 | `features/6.0/order-list-sorting.md` … `features/6.0/postgresql-18-and-19.md` |
| `H-features-10` | guides | 19 | 28.269 | `features/6.0/postgresql-dfs-cluster.md` … `features/6.0/review-hold.md` |
| `H-features-11` | guides | 20 | 26.127 | `features/6.0/rich-text-supplementary-characters-and-tex-source.md` … `features/6.0/static-cache-generator.md` |
| `H-features-12` | guides | 19 | 25.900 | `features/6.0/static-publishing.md` … `features/6.0/user-audit.md` |
| `H-features-13` | guides | 20 | 27.766 | `features/6.0/user-hygiene.md` … `features/6.0/website-sidebar.md` |
| `H-competition` | guides | 4 | 13.376 | `competition/README.md` … `competition/proprietary-dxp-and-saas.md` |
| `H-help-adopters` | guides | 47 | 17.007 | `help/adopters/adopters-checklists.md` … `help/adopters/what-adoption-means.md` |
| `H-help-advocates-01` | guides | 48 | 29.506 | `help/advocates/7x-and-the-foundation-for-advocates.md` … `help/advocates/proposing-a-contribution-sprint.md` |
| `H-help-advocates-02` | guides | 48 | 29.674 | `help/advocates/proposing-a-meetup.md` … `help/advocates/your-first-week-as-an-advocate.md` |
| `H-help-business-01` | guides | 6 | 15.672 | `help/business/brand-and-naming-rules.md` … `help/business/feature-highlights-for-customers.md` |
| `H-help-business-02` | guides | 6 | 15.336 | `help/business/growing-the-community.md` … `help/business/who-exponential-is-for.md` |
| `H-help-businesses-01` | guides | 26 | 25.794 | `help/businesses/associations/README.md` … `help/businesses/costs/what-you-do-not-pay-for.md` |
| `H-help-businesses-02` | guides | 26 | 25.357 | `help/businesses/costs/worked-example-intranet.md` … `help/businesses/intranets/two-factor-and-sign-in.md` |
| `H-help-businesses-03` | guides | 27 | 26.129 | `help/businesses/libraries/README.md` … `help/businesses/mobile-and-headless/responsive-sites.md` |
| `H-help-businesses-04` | guides | 26 | 24.903 | `help/businesses/mobile-and-headless/rest-api-v1-and-v2-for-businesses.md` … `help/businesses/newsletters/opt-in-and-consent.md` |
| `H-help-businesses-05` | guides | 27 | 29.916 | `help/businesses/newsletters/writing-and-sending-editions.md` … `help/businesses/reliability/restore-drills.md` |
| `H-help-businesses-06` | guides | 26 | 27.008 | `help/businesses/reliability/static-and-response-cache.md` … `help/businesses/stores/payment-gateways-for-businesses.md` |
| `H-help-businesses-07` | guides | 27 | 22.334 | `help/businesses/stores/paypal-payments.md` … `help/businesses/why-businesses-rely-on-exponential.md` |
| `H-help-community` | guides | 53 | 27.422 | `help/community/a-free-site-for-your-user-group.md` … `help/community/your-first-week-in-the-community.md` |
| `H-help-creators-01` | guides | 47 | 23.738 | `help/creators/app-creators/access-control-in-code.md` … `help/creators/app-creators/remote-services-expservices.md` |
| `H-help-creators-02` | guides | 47 | 26.338 | `help/creators/app-creators/rest-api.md` … `help/creators/website-creators/member-registration-and-areas.md` |
| `H-help-creators-03` | guides | 47 | 29.080 | `help/creators/website-creators/menus-and-navigation.md` … `help/creators/website-creators/your-first-site-step-by-step.md` |
| `H-help-customers-01` | guides | 37 | 25.803 | `help/customers/commerce/README.md` … `help/customers/day-to-day/analytics-and-what-to-measure.md` |
| `H-help-customers-02` | guides | 38 | 26.709 | `help/customers/day-to-day/approvals-and-review.md` … `help/customers/growth/rss-and-podcast-feeds.md` |
| `H-help-customers-03` | guides | 38 | 24.975 | `help/customers/growth/using-ai-drafts-responsibly.md` … `help/customers/partners-and-hosts/who-does-what.md` |
| `H-help-customers-04` | guides | 38 | 22.272 | `help/customers/partners-and-hosts/working-with-7x-and-the-project.md` … `help/customers/what-you-own.md` |
| `H-help-designers-01` | guides | 63 | 28.340 | `help/designers/README.md` … `help/designers/extensions/gitignore-for-themes.md` |
| `H-help-designers-02` | guides | 64 | 24.321 | `help/designers/extensions/install-and-changelog-files.md` … `help/designers/sales/assets-under-separate-licenses.md` |
| `H-help-designers-03` | guides | 64 | 24.876 | `help/designers/sales/contracts-and-terms.md` … `help/designers/templates/if-and-conditions.md` |
| `H-help-designers-04` | guides | 64 | 27.082 | `help/designers/templates/include.md` … `help/designers/theme-design/color-contrast.md` |
| `H-help-designers-05` | guides | 64 | 38.389 | `help/designers/theme-design/csp-safe-design.md` … `help/designers/theme-design/what-a-design-is.md` |
| `H-help-e-commerce-01` | guides | 26 | 17.960 | `help/e-commerce/catalog/README.md` … `help/e-commerce/operations/analytics-for-stores.md` |
| `H-help-e-commerce-02` | guides | 27 | 18.329 | `help/e-commerce/operations/commerce-audit-trail.md` … `help/e-commerce/settings/workflow-ini-for-the-shop.md` |
| `H-help-ecosystem` | guides | 45 | 19.276 | `help/ecosystem/a-short-history-of-the-ecosystem.md` … `help/ecosystem/what-the-ecosystem-is.md` |
| `H-help-government-01` | guides | 53 | 32.975 | `help/government/accessibility/README.md` … `help/government/building/documents-and-publications/pdf-export.md` |
| `H-help-government-02` | guides | 53 | 24.753 | `help/government/building/documents-and-publications/versions-of-documents.md` … `help/government/building/news-and-alerts/README.md` |
| `H-help-government-03` | guides | 53 | 28.389 | `help/government/building/news-and-alerts/archive-and-expiry.md` … `help/government/checklists/quarterly-access-review.md` |
| `H-help-government-04` | guides | 53 | 31.760 | `help/government/checklists/records-checklist.md` … `help/government/languages/redirecting-to-the-visitors-language.md` |
| `H-help-government-05` | guides | 53 | 34.251 | `help/government/languages/removing-a-translation-safely.md` … `help/government/levels/parliaments/structure-sections-and-roles.md` |
| `H-help-government-06` | guides | 53 | 35.497 | `help/government/levels/parliaments/what-it-publishes.md` … `help/government/operations/availability/database-high-availability.md` |
| `H-help-government-07` | guides | 53 | 39.369 | `help/government/operations/availability/high-availability-overview.md` … `help/government/operations/incidents/data-breach-notification-framing.md` |
| `H-help-government-08` | guides | 53 | 34.593 | `help/government/operations/incidents/database-unreachable.md` … `help/government/privacy/README.md` |
| `H-help-government-09` | guides | 53 | 32.256 | `help/government/privacy/analytics.md` … `help/government/procurement/exit-plans/lock-in-clauses-to-avoid.md` |
| `H-help-government-10` | guides | 53 | 34.415 | `help/government/procurement/exit-plans/personal-data-at-exit.md` … `help/government/procurement/total-cost-of-ownership/staff-and-skills.md` |
| `H-help-government-11` | guides | 53 | 30.520 | `help/government/procurement/total-cost-of-ownership/support-contracts.md` … `help/government/security/file-uploads-and-svg.md` |
| `H-help-government-12` | guides | 53 | 32.034 | `help/government/security/forwarding-audit-to-a-siem.md` … `help/government/tasks/compliance/add-security-txt.md` |
| `H-help-government-13` | guides | 53 | 19.562 | `help/government/tasks/compliance/add-your-own-words-to-the-weak-password-list.md` … `help/government/tasks/operations/bring-the-site-back-online.md` |
| `H-help-government-14` | guides | 53 | 23.496 | `help/government/tasks/operations/check-file-and-directory-permissions.md` … `help/government/tasks/people-and-permissions/keep-editors-out-of-the-admin-siteaccess.md` |
| `H-help-government-15` | guides | 53 | 25.206 | `help/government/tasks/people-and-permissions/let-a-contractor-edit-only-one-folder.md` … `help/government/tasks/publishing/find-and-fix-broken-links.md` |
| `H-help-government-16` | guides | 53 | 24.198 | `help/government/tasks/publishing/follow-changes-with-notifications.md` … `help/government/tasks/sites-and-languages/connect-staff-sign-in-to-ldap.md` |
| `H-help-government-17` | guides | 53 | 21.481 | `help/government/tasks/sites-and-languages/export-a-list-of-content-as-csv.md` … `help/government/transparency-and-foi/withdrawing-and-correcting-published-information.md` |
| `H-help-hosting-01` | guides | 24 | 25.749 | `help/hosting/basics/README.md` … `help/hosting/business/marketing-exponential-hosting.md` |
| `H-help-hosting-02` | guides | 24 | 23.307 | `help/hosting/business/partner-programs.md` … `help/hosting/models/managed-hosting.md` |
| `H-help-hosting-03` | guides | 24 | 32.057 | `help/hosting/models/platform-as-a-service.md` … `help/hosting/operations/storage-layout.md` |
| `H-help-hosting-04` | guides | 24 | 29.824 | `help/hosting/operations/system-card-for-hosts.md` … `help/hosting/performance/varnish-and-reverse-proxies.md` |
| `H-help-hosting-05` | guides | 24 | 22.606 | `help/hosting/performance/velocity-response-cache.md` … `help/hosting/security/demo-mode-for-hosts.md` |
| `H-help-hosting-06` | guides | 24 | 32.685 | `help/hosting/security/first-sign-in-and-default-passwords.md` … `help/hosting/static-publishing-and-your-hosting.md` |
| `H-help-individuals-01` | guides | 28 | 20.557 | `help/individuals/blogs/README.md` … `help/individuals/individuals-reading-path.md` |
| `H-help-individuals-02` | guides | 28 | 21.329 | `help/individuals/individuals-start-here.md` … `help/individuals/your-personal-site-for-free.md` |
| `H-help-leadership-01` | guides | 9 | 19.970 | `help/leadership/accessibility-security-and-compliance-checklists.md` … `help/leadership/planning-an-exponential-project.md` |
| `H-help-leadership-02` | guides | 9 | 20.242 | `help/leadership/project-managers-start-here.md` … `help/leadership/working-with-the-community.md` |
| `H-help-local-businesses-01` | guides | 28 | 18.013 | `help/local-businesses/a-fast-free-brochure-site-for-your-business.md` … `help/local-businesses/getting-started/working-with-a-local-partner-or-freelancer.md` |
| `H-help-local-businesses-02` | guides | 28 | 19.105 | `help/local-businesses/local-businesses-faq.md` … `help/local-businesses/templates/website-brief-for-a-local-business.md` |
| `H-help-migrations-01` | guides | 22 | 20.689 | `help/migrations/README.md` … `help/migrations/recipes/coming-from-sql-update-files.md` |
| `H-help-migrations-02` | guides | 23 | 21.913 | `help/migrations/recipes/create-or-change-a-content-class.md` … `help/migrations/when-a-migration-fails.md` |
| `H-help-non-profits-01` | guides | 28 | 19.186 | `help/non-profits/a-free-website-for-your-nonprofit.md` … `help/non-profits/non-profits-faq.md` |
| `H-help-non-profits-02` | guides | 28 | 19.746 | `help/non-profits/non-profits-glossary.md` … `help/non-profits/volunteers/volunteers-as-editors.md` |
| `H-help-partners-01` | guides | 37 | 15.750 | `help/partners/application-checklist.md` … `help/partners/partner-faq.md` |
| `H-help-partners-02` | guides | 38 | 17.508 | `help/partners/partner-glossary.md` … `help/partners/your-partner-page.md` |
| `H-help-podcasters-01` | guides | 24 | 17.308 | `help/podcasters/a-release-calendar.md` … `help/podcasters/podcaster-checklists.md` |
| `H-help-podcasters-02` | guides | 24 | 18.395 | `help/podcasters/podcaster-faq.md` … `help/podcasters/writing-show-notes.md` |
| `H-help-press-01` | guides | 38 | 28.043 | `help/press/7x-for-reporters.md` … `help/press/fact-checking-guide.md` |
| `H-help-press-02` | guides | 38 | 25.330 | `help/press/fact-sheet-for-journalists.md` … `help/press/reporter-faq.md` |
| `H-help-press-03` | guides | 38 | 24.016 | `help/press/requesting-an-interview.md` … `help/press/story-online-editor-tinymce-8.md` |
| `H-help-press-04` | guides | 38 | 25.354 | `help/press/story-open-source-and-licenses.md` … `help/press/writing-about-the-ez-publish-years.md` |
| `H-help-project` | guides | 12 | 16.268 | `help/project/README.md` … `help/project/roadmap.md` |
| `H-help-request-shield` | guides | 11 | 13.585 | `help/request-shield/README.md` … `help/request-shield/under-velocity.md` |
| `H-help-resellers-01` | guides | 26 | 17.388 | `help/resellers/basics/choosing-a-partner-level.md` … `help/resellers/offers/demo-sites.md` |
| `H-help-resellers-02` | guides | 27 | 18.553 | `help/resellers/offers/e-commerce-offers.md` … `help/resellers/sales/sales-process.md` |
| `H-help-site-packages-01` | guides | 26 | 27.331 | `help/site-packages/README.md` … `help/site-packages/cjw-multisite/the-cjw-themes-jumper-extension.md` |
| `H-help-site-packages-02` | guides | 26 | 29.251 | `help/site-packages/cjw-multisite/the-demo-content-package.md` … `help/site-packages/requirements/the-requirements-checklist.md` |
| `H-help-site-packages-03` | guides | 26 | 26.810 | `help/site-packages/requirements/thumbnails-and-screenshots.md` … `help/site-packages/your-own/versioning-your-own-packages.md` |
| `H-help-sponsors` | guides | 11 | 5.286 | `help/sponsors/7x-the-first-sponsor.md` … `help/sponsors/what-sponsorship-funds.md` |
| `H-help-static-publishing` | guides | 25 | 13.266 | `help/static-publishing/README.md` … `help/static-publishing/who-may-publish-roles-and-policies.md` |
| `H-help-students-01` | guides | 28 | 21.897 | `help/students/asking-for-help-and-study-groups.md` … `help/students/project-05-a-multilingual-site.md` |
| `H-help-students-02` | guides | 28 | 22.758 | `help/students/project-06-search-that-finds-the-right-things.md` … `help/students/your-first-site-three-ways.md` |
| `H-help-supporters` | guides | 12 | 5.721 | `help/supporters/advocacy.md` … `help/supporters/ways-to-support-exponential.md` |
| `H-help-theme-designers-01` | guides | 22 | 18.799 | `help/theme-designers/building-a-portfolio-and-getting-work.md` … `help/theme-designers/designing-view-modes.md` |
| `H-help-theme-designers-02` | guides | 22 | 18.806 | `help/theme-designers/designing-with-real-content.md` … `help/theme-designers/your-first-day-as-a-theme-designer.md` |
| `H-help-trainers-01` | guides | 31 | 31.213 | `help/trainers/assessments/README.md` … `help/trainers/assessments/module-05-newsletter-and-migration/README.md` |
| `H-help-trainers-02` | guides | 32 | 32.178 | `help/trainers/assessments/module-05-newsletter-and-migration/migration-practical-task.md` … `help/trainers/developers/module-03-designs-overrides-and-debugging/the-design-chain.md` |
| `H-help-trainers-03` | guides | 31 | 29.233 | `help/trainers/developers/module-04-content-classes-and-datatypes/README.md` … `help/trainers/developers/module-09-the-console-and-persistent-objects/sql-safely-with-ezdb.md` |
| `H-help-trainers-04` | guides | 32 | 30.122 | `help/trainers/developers/module-09-the-console-and-persistent-objects/the-console.md` … `help/trainers/editors-101/module-04-the-online-editor/README.md` |
| `H-help-trainers-05` | guides | 32 | 29.014 | `help/trainers/editors-101/module-04-the-online-editor/embeds-tables-and-choosing-the-editor.md` … `help/trainers/editors-201/module-03-organizing-content/README.md` |
| `H-help-trainers-06` | guides | 31 | 26.155 | `help/trainers/editors-201/module-03-organizing-content/large-operations-and-the-trash.md` … `help/trainers/exponential-intelligence/module-02-the-intelligence-tab/keeping-sending-and-discarding-drafts.md` |
| `H-help-trainers-07` | guides | 32 | 29.476 | `help/trainers/exponential-intelligence/module-03-the-edit-form/README.md` … `help/trainers/hosting-operations/module-03-databases/README.md` |
| `H-help-trainers-08` | guides | 31 | 30.410 | `help/trainers/hosting-operations/module-03-databases/choosing-a-database.md` … `help/trainers/layouts/module-01-introducing-layouts/the-five-ideas.md` |
| `H-help-trainers-09` | guides | 32 | 26.649 | `help/trainers/layouts/module-02-layouts-and-layout-types/README.md` … `help/trainers/migration/module-01-planning-a-migration/phases-and-the-checklist.md` |
| `H-help-trainers-10` | guides | 32 | 32.671 | `help/trainers/migration/module-01-planning-a-migration/rehearsal-cut-over-and-rollback.md` … `help/trainers/multisite-and-packages/module-01-siteaccesses-for-several-sites/a-siteaccess-per-language.md` |
| `H-help-trainers-11` | guides | 31 | 35.761 | `help/trainers/multisite-and-packages/module-01-siteaccesses-for-several-sites/adding-a-second-site.md` … `help/trainers/newsletter/README.md` |
| `H-help-trainers-12` | guides | 32 | 31.603 | `help/trainers/newsletter/module-01-getting-started/README.md` … `help/trainers/search/module-01-how-search-works/where-people-search.md` |
| `H-help-trainers-13` | guides | 31 | 30.709 | `help/trainers/search/module-02-indexing/README.md` … `help/trainers/site-administrators/module-02-users-and-groups/passwords-resets-and-lockouts.md` |
| `H-help-trainers-14` | guides | 32 | 32.752 | `help/trainers/site-administrators/module-02-users-and-groups/unactivated-users-and-account-checks.md` … `help/trainers/site-administrators/module-08-security/README.md` |
| `H-help-trainers-15` | guides | 32 | 35.241 | `help/trainers/site-administrators/module-08-security/content-security-policy.md` … `help/trainers/theme-designers/module-03-stylesheets-and-scripts/listing-files-in-design-ini.md` |
| `H-help-trainers-16` | guides | 31 | 30.777 | `help/trainers/theme-designers/module-03-stylesheets-and-scripts/ready-for-a-content-security-policy.md` … `help/trainers/train-the-trainer/module-01-preparing-to-teach/the-help-center-as-your-textbook.md` |
| `H-help-trainers-17` | guides | 32 | 22.473 | `help/trainers/train-the-trainer/module-01-preparing-to-teach/your-material-and-the-gfdl.md` … `help/trainers/train-the-trainer/module-06-assessment-and-follow-up/certificates-of-attendance.md` |
| `H-help-trainers-18` | guides | 32 | 19.670 | `help/trainers/train-the-trainer/module-06-assessment-and-follow-up/checking-understanding-during-class.md` … `help/trainers/workshops/module-05-capstone-projects/multilingual-event-site.md` |
| `H-help-user-management-from-the-console` | guides | 20 | 7.853 | `help/user-management-from-the-console/README.md` … `help/user-management-from-the-console/when-a-command-fails.md` |
| `H-reference` | guides | 1 | 1.858 | `reference/template-check-rules.md` |
| `H-root-01` | guides | 5 | 18.146 | `COMMUNITY.md` … `HISTORY.md` |
| `H-root-02` | guides | 5 | 17.628 | `INSTALL.md` … `UNINSTALL.md` |
| `H-root-03` | guides | 5 | 38.739 | `UPDATE.md` … `languages.md` |
| `H-help-extension-development-01` | reference | 43 | 36.497 | `help/extension-development/README.md` … `help/extension-development/part-2-builder/the-exp-ini-command.md` |
| `H-help-extension-development-02` | reference | 43 | 25.325 | `help/extension-development/part-2-builder/view-modes-for-builders.md` … `help/extension-development/part-3-extension-developer/tutorials/by-hand-custom-tag.md` |
| `H-help-extension-development-03` | reference | 43 | 28.708 | `help/extension-development/part-3-extension-developer/tutorials/by-hand-datatype.md` … `help/extension-development/part-4-upgrading/old-jquery-to-jquery-4.md` |
| `H-help-extension-development-04` | reference | 43 | 30.266 | `help/extension-development/part-4-upgrading/php-8-1-to-8-5-deprecations.md` … `help/extension-development/part-7-releasing/github-releases-and-release-notes.md` |
| `H-help-extension-development-05` | reference | 44 | 24.720 | `help/extension-development/part-7-releasing/packages-ezpkg.md` … `help/extension-development/reading-paths.md` |
| `H-help-template-language-01` | reference | 16 | 18.348 | `help/template-language/README.md` … `help/template-language/moving-from-tpl-to-twig.md` |
| `H-help-template-language-02` | reference | 17 | 17.554 | `help/template-language/namespaces.md` … `help/template-language/what-the-template-language-is.md` |
| `H-help-template-operators-01` | reference | 24 | 24.150 | `help/template-operators/README.md` … `help/template-operators/menu-and-tree-operators.md` |
| `H-help-template-operators-02` | reference | 24 | 30.407 | `help/template-operators/newsletter-operators.md` … `help/template-operators/url-operators.md` |
| `H-help-build-two-ways-01` | reference | 83 | 27.828 | `help/build-two-ways/README.md` … `help/build-two-ways/examples/maintain/add-a-recipe-to-the-index.md` |
| `H-help-build-two-ways-02` | reference | 83 | 20.834 | `help/build-two-ways/examples/maintain/adjust-spacing.md` … `help/build-two-ways/examples/plan/inventory-from-the-console.md` |
| `H-help-build-two-ways-03` | reference | 84 | 30.664 | `help/build-two-ways/examples/plan/inventory-page-types.md` … `help/build-two-ways/your-own-design-extension.md` |
| `H-help-php-developers-01` | reference | 28 | 23.715 | `help/php-developers/a-php-development-checkout.md` … `help/php-developers/php-developer-faq.md` |
| `H-help-php-developers-02` | reference | 28 | 21.644 | `help/php-developers/php-developer-reading-path.md` … `help/php-developers/writing-migrations-in-php.md` |
| `H-help-cms-developers-01` | reference | 25 | 24.058 | `help/cms-developers/accessibility.md` … `help/cms-developers/headless-delivery.md` |
| `H-help-cms-developers-02` | reference | 26 | 23.152 | `help/cms-developers/images-and-image-aliases.md` … `help/cms-developers/workflows-and-triggers.md` |
| `H-help-dxp-developers` | reference | 52 | 27.695 | `help/dxp-developers/application-cache-and-redis.md` … `help/dxp-developers/what-the-dxp-ships-and-what-it-does-not.md` |
| `H-help-software-engineers-01` | reference | 26 | 19.285 | `help/software-engineers/api-design-rest-ezjscore-headless.md` … `help/software-engineers/http-security-csp-and-proxies.md` |
| `H-help-software-engineers-02` | reference | 26 | 21.758 | `help/software-engineers/i18n-and-right-to-left.md` … `help/software-engineers/writing-portable-sql.md` |
| `H-help-web-framework-developers-01` | reference | 27 | 18.466 | `help/web-framework-developers/authentication-and-sessions.md` … `help/web-framework-developers/migrations-and-dba-schema-files.md` |
| `H-help-web-framework-developers-02` | reference | 28 | 19.443 | `help/web-framework-developers/mixing-exponential-and-a-framework.md` … `help/web-framework-developers/your-first-module-step-by-step.md` |
| `H-help-mobile-app-developers-01` | reference | 27 | 15.978 | `help/mobile-app-developers/auth/README.md` … `help/mobile-app-developers/media-and-images/images-in-rest-answers.md` |
| `H-help-mobile-app-developers-02` | reference | 28 | 17.913 | `help/mobile-app-developers/mobile-app-developers-faq.md` … `help/mobile-app-developers/which-api-for-your-app.md` |
| `H-help-database-administrators-01` | reference | 24 | 20.792 | `help/database-administrators/applying-the-6-0-15-database-update.md` … `help/database-administrators/mysql-read-replicas.md` |
| `H-help-database-administrators-02` | reference | 25 | 22.541 | `help/database-administrators/oracle-connections-and-tuning.md` … `help/database-administrators/upgrades-and-sql-update-files.md` |
| `H-help-ai-agents-01` | reference | 24 | 16.260 | `help/ai-agents/agentic-workflows/README.md` … `help/ai-agents/expai/chains-and-queued-work.md` |
| `H-help-ai-agents-02` | reference | 24 | 18.470 | `help/ai-agents/expai/configuring-a-provider.md` … `help/ai-agents/which-path-fits-your-task.md` |
| `H-help-bundlers-01` | reference | 27 | 19.553 | `help/bundlers/adding-and-removing-extensions.md` … `help/bundlers/hosting-panel-installers.md` |
| `H-help-bundlers-02` | reference | 28 | 20.723 | `help/bundlers/integrity-checks.md` … `help/bundlers/your-own-package-server.md` |
| `H-help-integrators-01` | reference | 28 | 21.883 | `help/integrators/ai-job-queue-and-mcp.md` … `help/integrators/interface-specification-template.md` |
| `H-help-integrators-02` | reference | 29 | 24.566 | `help/integrators/login-handlers-ldap-and-textfile.md` … `help/integrators/writing-an-import-script.md` |
| `H-help-sqli-import` | reference | 6 | 6.974 | `help/sqli-import/README.md` … `help/sqli-import/upgrading-from-1x.md` |
| `H-specifications-01` | reference | 14 | 19.328 | `specifications/6.0/README.md` … `specifications/6.0/help-content-package.md` |
| `H-specifications-02` | reference | 15 | 26.116 | `specifications/6.0/help-standards.md` … `specifications/6.0/platform-package-map.md` |
| `H-specifications-03` | reference | 15 | 25.269 | `specifications/6.0/platform-sqlite-installer.md` … `specifications/6.0/xrowextract.md` |
| `H-standards-01` | reference | 18 | 34.620 | `standards/README.md` … `standards/patterns/area-extension-bcgooglesitemaps.md` |
| `H-standards-02` | reference | 18 | 21.769 | `standards/patterns/area-extension-bcwebsitestatistics.md` … `standards/patterns/area-extension-explayouts-content-browser.md` |
| `H-standards-03` | reference | 19 | 15.548 | `standards/patterns/area-extension-explayouts-core.md` … `standards/patterns/area-extension-ezautosave.md` |
| `H-standards-04` | reference | 18 | 10.141 | `standards/patterns/area-extension-ezdemo.md` … `standards/patterns/area-extension-ezprestapiprovider.md` |
| `H-standards-05` | reference | 19 | 10.278 | `standards/patterns/area-extension-ezssp.md` … `standards/patterns/area-extension-sevenx-themes-super.md` |
| `H-standards-06` | reference | 18 | 21.979 | `standards/patterns/area-extension-sqliimport.md` … `standards/patterns/area-kernel-classes-datatable.md` |
| `H-standards-07` | reference | 19 | 10.021 | `standards/patterns/area-kernel-classes-datatypes.md` … `standards/patterns/area-kernel-classes-secrets.md` |
| `H-standards-08` | reference | 18 | 23.028 | `standards/patterns/area-kernel-classes-shopaccounthandlers.md` … `standards/patterns/area-kernel-private-classes-httpcache.md` |
| `H-standards-09` | reference | 19 | 21.656 | `standards/patterns/area-kernel-private-classes-requestrules.md` … `standards/patterns/area-lib-eztemplate.md` |
| `H-standards-10` | reference | 18 | 90.563 | `standards/patterns/area-lib-ezutils.md` … `standards/patterns/module-view.md` |
| `H-standards-11` | reference | 19 | 57.537 | `standards/patterns/observer.md` … `standards/web-platform.md` |
| `H-bc-01` | reference | 11 | 43.224 | `bc/5.90/README.md` … `bc/6.0/admin4-styling.md` |
| `H-bc-02` | reference | 11 | 66.033 | `bc/6.0/admin4l.md` … `bc/6.0/cache-console.md` |
| `H-bc-03` | reference | 11 | 30.796 | `bc/6.0/cache-until.md` … `bc/6.0/debug-bar.md` |
| `H-bc-04` | reference | 12 | 18.619 | `bc/6.0/demo-mode.md` … `bc/6.0/ezpsessionhandlerdb-php8-bugfix-and-tests.md` |
| `H-bc-05` | reference | 11 | 28.636 | `bc/6.0/file-modes.md` … `bc/6.0/ini-save-file-permissions.md` |
| `H-bc-06` | reference | 11 | 14.575 | `bc/6.0/installer-crontab.md` … `bc/6.0/love/L01-callers.md` |
| `H-bc-07` | reference | 11 | 18.599 | `bc/6.0/love/MIGRATION.md` … `bc/6.0/middle-out-compression.md` |
| `H-bc-08` | reference | 12 | 14.967 | `bc/6.0/multi-node-edit.md` … `bc/6.0/pdf-export.md` |
| `H-bc-09` | reference | 11 | 38.312 | `bc/6.0/phar.md` … `bc/6.0/rad-extension-surface.md` |
| `H-bc-10` | reference | 11 | 11.974 | `bc/6.0/rad-security.md` … `bc/6.0/site-packages-no-extension-packages.md` |
| `H-bc-11` | reference | 11 | 30.658 | `bc/6.0/soap-entry-script-index-soap.md` … `bc/6.0/ui-switch-design.md` |
| `H-bc-12` | reference | 12 | 30.150 | `bc/6.0/ui-switch.md` … `bc/6.0/yui-removal.md` |
| `H-history-01` | reference | 23 | 9.704 | `history/2023/2023-12.md` … `history/2025/2025-09.md` |
| `H-history-02` | reference | 24 | 42.499 | `history/2025/2025-10.md` … `history/ecosystem/adminneo.md` |
| `H-history-03` | reference | 24 | 20.859 | `history/ecosystem/cjw-exponential-platform-nexus.md` … `history/ecosystem/ezplatform-query-fieldtype.md` |
| `H-history-04` | reference | 24 | 11.149 | `history/ecosystem/ezplatform-richtext.md` … `history/ecosystem/months/2021-10.md` |
| `H-history-05` | reference | 24 | 9.108 | `history/ecosystem/months/2021-11.md` … `history/ecosystem/months/2024-10.md` |
| `H-history-06` | reference | 24 | 19.259 | `history/ecosystem/months/2024-11.md` … `history/ecosystem/oss-skeleton.md` |
| `H-history-07` | reference | 24 | 20.766 | `history/ecosystem/oss.md` … `history/extensions/ezjscore.md` |
| `H-history-08` | reference | 24 | 18.075 | `history/extensions/ezmbpaex.md` … `history/extensions/months/2024-08.md` |
| `H-history-09` | reference | 24 | 24.372 | `history/extensions/months/2024-09.md` … `history/extensions/sevenx_valkey_cache.md` |
| `H-history-10` | reference | 24 | 73.419 | `history/extensions/swark.md` … `history/ledger/exp_enhanced_link.md` |
| `H-history-11` | reference | 24 | 79.951 | `history/ledger/explayouts_ui.md` … `history/ledger/ezoe.md` |
| `H-history-12` | reference | 24 | 7.378 | `history/ledger/ezpaypal.md` … `history/ledger/eztags.md` |
| `H-history-13` | reference | 24 | 33.737 | `history/ledger/ezupdate.md` … `history/ledger/recaptcha.md` |
| `H-history-14` | reference | 24 | 40.503 | `history/ledger/sevenx-recipes.md` … `history/velocity/installers-and-packages.md` |
| `H-examples` | reference | 29 | 27.339 | `examples/README.md` … `examples/web-servers/velocity.md` |
| `X01` | tables | 1.139 Einträge | 26.039 | `descriptions/01-command-accesscheck.json` … `descriptions/05-ini-settings-content-ini.json` |
| `X02` | tables | 598 Einträge | 26.290 | `descriptions/06-ini-settings-debugbar-ini.json` … `descriptions/08-ini-settings-package-ini.json` |
| `X03` | tables | 692 Einträge | 29.716 | `descriptions/09-ini-settings-site-ini.json` … `descriptions/12-shell-checkcalltimeref.json` |
| `X04` | tables | 1.373 Einträge | 28.800 | `descriptions/13-command-exptemplateconvert.json` … `descriptions/37-help-left-menu-2026-11-04.json` |
| `X05` | tables | 633 Einträge | 13.381 | `descriptions/38-help-examples-catch-up-2026-11-05.json` … `strings.json` |

</details>

<details><summary>Die 11 Teile des Administrationskatalogs</summary>

| Teil | Texte | Wörter | Kontexte (erster … letzter) |
|---|---:|---:|---|
| `A01` | 1.969 | 12.355 | `contentstructuremenu/show_content_structure` … `design/admin/error/shop` |
| `A02` | 1.922 | 11.372 | `design/admin/ezinfo/about` … `design/admin/pdf/edit` |
| `A03` | 1.946 | 12.883 | `design/admin/pdf/list` … `design/admin/setup/extensions` |
| `A04` | 1.972 | 20.876 | `design/admin/setup/info` … `design/admin/setup/rad/override` |
| `A05` | 1.998 | 14.899 | `design/admin/setup/rad/settings` … `design/admin/visual/templateview` |
| `A06` | 1.993 | 9.020 | `design/admin/visual/toolbar` … `design/standard/section` |
| `A07` | 1.935 | 13.822 | `design/standard/setup` … `kernel/collaboration` |
| `A08` | 1.839 | 13.074 | `kernel/content` … `kernel/contentfinder` |
| `A09` | 1.953 | 10.385 | `design/admin/audit` … `design/admin/mailpreferences` |
| `A10` | 1.928 | 13.144 | `design/standard/mailpreferences` … `kernel/user/security/mail` |
| `A11` | 1.900 | 12.208 | `design/admin/graphql` … `design/admin/setup/aliases` |

</details>

### Sprachen ohne Übersetzung

Die Sprachen der mitgelieferten Locales, die die Administration auf Englisch zeigt, die Sprachen, die auf einem Branch übersetzt werden, und Sprachen mit fünf Millionen Sprechern oder mehr, für die es noch keine Locale gibt (der Code ist ein Vorschlag). Sortiert nach Sprechern je Schwierigkeitspunkt.

| Sprache | Code | Sprecher (Mio.) | Schrift | Richtung | Schwierigkeit | Stand |
|---|---|---:|---|---|:-:|---|
| Haitianisch | `hat-HT` | 13 | Latn | LTR | 2 | neue Locale, Welle #149 |
| Saraiki | `skr-PK` | 26 | Arab | RTL | 5 | neue Locale, Welle #161 |
| Setswana | `tsn-ZA` | 14 | Latn | LTR | 3 | neue Locale, Welle #173 |
| Dioula | `dyu-CI` | 12 | Latn | LTR | 3 | neue Locale, Welle #185 |
| Chichewa | `nya-MW` | 15 | Latn | LTR | 4 | neue Locale, Welle #197 |
| Nord-Sotho | `nso-ZA` | 14 | Latn | LTR | 4 | neue Locale, Welle #209 |
| Sesotho | `sot-ZA` | 14 | Latn | LTR | 4 | neue Locale, Welle #221 |
| Mòoré | `mos-BF` | 10 | Latn | LTR | 3 | neue Locale, Welle #233 |
| Südaserbaidschanisch | `azb-IR` | 15 | Arab | RTL | 5 | neue Locale, Welle #245 |
| Kanuri | `kau-NG` | 10 | Latn | LTR | 4 | neue Locale, Welle #257 |
| Makua | `vmw-MZ` | 9 | Latn | LTR | 4 | neue Locale, Welle #269 |
| Kikongo | `kon-CD` | 7 | Latn | LTR | 4 | neue Locale, Welle #281 |
| Luhya | `luy-KE` | 7 | Latn | LTR | 4 | neue Locale, Welle #293 |
| Xitsonga | `tso-ZA` | 7 | Latn | LTR | 4 | neue Locale, Welle #305 |
| Umbundu | `umb-AO` | 7 | Latn | LTR | 4 | neue Locale, Welle #317 |
| Luo | `luo-KE` | 5 | Latn | LTR | 3 | neue Locale, Welle #329 |
| Sango | `sag-CF` | 5 | Latn | LTR | 3 | neue Locale, Welle #341 |
| Belutschisch | `bal-PK` | 8 | Arab | RTL | 5 | neue Locale, Welle #353 |
| Kalenjin | `kln-KE` | 6 | Latn | LTR | 4 | neue Locale, Welle #365 |
| Tshiluba | `lua-CD` | 6 | Latn | LTR | 4 | neue Locale, Welle #377 |
| Sukuma | `suk-TZ` | 5 | Latn | LTR | 4 | neue Locale, Welle #389 |
| ale-US | `ale-US` | – | Latn | LTR | 3 | Welle #401 |
| arn-CL | `arn-CL` | – | Latn | LTR | 3 | Welle #412 |
| aym-BO | `aym-BO` | – | Latn | LTR | 3 | Welle #423 |
| chr-US | `chr-US` | – | Latn | LTR | 3 | Welle #434 |
| cre-CA | `cre-CA` | – | Latn | LTR | 3 | Welle #445 |
| ems-US | `ems-US` | – | Latn | LTR | 3 | Welle #456 |
| esu-US | `esu-US` | – | Latn | LTR | 3 | Welle #467 |
| gwi-US | `gwi-US` | – | Latn | LTR | 3 | Welle #478 |
| hai-US | `hai-US` | – | Latn | LTR | 3 | Welle #489 |
| iku-CA | `iku-CA` | – | Latn | LTR | 3 | Welle #500 |
| ipk-US | `ipk-US` | – | Latn | LTR | 3 | Welle #511 |
| lkt-US | `lkt-US` | – | Latn | LTR | 3 | Welle #522 |
| moh-CA | `moh-CA` | – | Latn | LTR | 3 | Welle #533 |
| nah-MX | `nah-MX` | – | Latn | LTR | 3 | Welle #544 |
| nav-US | `nav-US` | – | Latn | LTR | 3 | Welle #555 |
| oji-CA | `oji-CA` | – | Latn | LTR | 3 | Welle #566 |
| quc-GT | `quc-GT` | – | Latn | LTR | 3 | Welle #577 |
| tli-US | `tli-US` | – | Latn | LTR | 3 | Welle #588 |
| tsi-US | `tsi-US` | – | Latn | LTR | 3 | Welle #599 |
| yua-MX | `yua-MX` | – | Latn | LTR | 3 | Welle #610 |

### Die Warteschlange: die ersten 60 Wellen

| Phase | Wellen |
|---|---:|
| 1. Kataloge der Prioritätsgruppe | 8 |
| 2. Deutsch: Hilfe des Alltags | 0 |
| 3. Hilfe des Alltags der Prioritätsgruppe | 140 |
| 4. Neue Übersetzungen | 472 |
| 5. Erweiterungen der übrigen Sprachen | 147 |
| 6. Hilfe des Alltags der übrigen Sprachen | 2.920 |
| 7. Übrige Hilfe der Prioritätsgruppe | 1.428 |
| 8. Übrige Hilfe der übrigen Sprachen | 29.784 |

| # | Welle | Sprache | Arbeitspaket | Umfang (offen) | Schwierigkeit | Voraussetzungen |
|---:|---|---|---|---|:-:|---|
| 1 | `ger-DE/E` | Deutsch | Mitgelieferte Erweiterungen | 137 von 567 Texten | 2 | `GUIDE.md` |
| 2 | `ara-SA/A07` | Arabisch | Administration, Teil A07 | 20 von 1.935 Texten | 4 | `GUIDE.md`, RTL |
| 3 | `tur-TR/A07` | Türkisch | Administration, Teil A07 | 20 von 1.935 Texten | 2 | `GUIDE.md` |
| 4 | `ukr-UA/A07` | Ukrainisch | Administration, Teil A07 | 20 von 1.935 Texten | 3 | `GUIDE.md` |
| 5 | `rus-RU/A07` | Russisch | Administration, Teil A07 | 20 von 1.935 Texten | 3 | `GUIDE.md` |
| 6 | `fas-IR/A07` | Persisch | Administration, Teil A07 | 20 von 1.935 Texten | 4 | `GUIDE.md`, RTL |
| 7 | `pus-AF/A07` | Paschtu | Administration, Teil A07 | 20 von 1.935 Texten | 5 | `GUIDE.md`, RTL |
| 8 | `kur-TR/A07` | Kurdisch (Kurmandschi) | Administration, Teil A07 | 20 von 1.935 Texten | 3 | `GUIDE.md` |
| 9 | `ara-SA/S` | Arabisch | Hilfe einrichten | klein | 4 | `GUIDE.md`, RTL, PDF-Schrift Arab |
| 10 | `tur-TR/S` | Türkisch | Hilfe einrichten | klein | 2 | `GUIDE.md` |
| 11 | `ukr-UA/S` | Ukrainisch | Hilfe einrichten | klein | 3 | `GUIDE.md` |
| 12 | `rus-RU/S` | Russisch | Hilfe einrichten | klein | 3 | `GUIDE.md` |
| 13 | `fas-IR/S` | Persisch | Hilfe einrichten | klein | 4 | `GUIDE.md`, RTL, PDF-Schrift Arab |
| 14 | `pus-AF/S` | Paschtu | Hilfe einrichten | klein | 5 | `GUIDE.md`, RTL, PDF-Schrift Arab |
| 15 | `kur-TR/S` | Kurdisch (Kurmandschi) | Hilfe einrichten | klein | 3 | `GUIDE.md` |
| 16 | `ara-SA/H-help-01` | Arabisch | Hilfe `H-help-01` | 12 Seiten, 37.917 Wörter | 4 | `GUIDE.md`, RTL, PDF-Schrift Arab, Wellen `S` und Administration |
| 17 | `tur-TR/H-help-01` | Türkisch | Hilfe `H-help-01` | 12 Seiten, 37.917 Wörter | 2 | `GUIDE.md`, Wellen `S` und Administration |
| 18 | `ukr-UA/H-help-01` | Ukrainisch | Hilfe `H-help-01` | 12 Seiten, 37.917 Wörter | 3 | `GUIDE.md`, Wellen `S` und Administration |
| 19 | `rus-RU/H-help-01` | Russisch | Hilfe `H-help-01` | 12 Seiten, 37.917 Wörter | 3 | `GUIDE.md`, Wellen `S` und Administration |
| 20 | `fas-IR/H-help-01` | Persisch | Hilfe `H-help-01` | 12 Seiten, 37.917 Wörter | 4 | `GUIDE.md`, RTL, PDF-Schrift Arab, Wellen `S` und Administration |
| 21 | `pus-AF/H-help-01` | Paschtu | Hilfe `H-help-01` | 12 Seiten, 37.917 Wörter | 5 | `GUIDE.md`, RTL, PDF-Schrift Arab, Wellen `S` und Administration |
| 22 | `kur-TR/H-help-01` | Kurdisch (Kurmandschi) | Hilfe `H-help-01` | 12 Seiten, 37.917 Wörter | 3 | `GUIDE.md`, Wellen `S` und Administration |
| 23 | `ara-SA/H-help-02` | Arabisch | Hilfe `H-help-02` | 12 Seiten, 16.467 Wörter | 4 | `GUIDE.md`, RTL, PDF-Schrift Arab, Wellen `S` und Administration |
| 24 | `tur-TR/H-help-02` | Türkisch | Hilfe `H-help-02` | 12 Seiten, 16.467 Wörter | 2 | `GUIDE.md`, Wellen `S` und Administration |
| 25 | `ukr-UA/H-help-02` | Ukrainisch | Hilfe `H-help-02` | 12 Seiten, 16.467 Wörter | 3 | `GUIDE.md`, Wellen `S` und Administration |
| 26 | `rus-RU/H-help-02` | Russisch | Hilfe `H-help-02` | 12 Seiten, 16.467 Wörter | 3 | `GUIDE.md`, Wellen `S` und Administration |
| 27 | `fas-IR/H-help-02` | Persisch | Hilfe `H-help-02` | 12 Seiten, 16.467 Wörter | 4 | `GUIDE.md`, RTL, PDF-Schrift Arab, Wellen `S` und Administration |
| 28 | `pus-AF/H-help-02` | Paschtu | Hilfe `H-help-02` | 12 Seiten, 16.467 Wörter | 5 | `GUIDE.md`, RTL, PDF-Schrift Arab, Wellen `S` und Administration |
| 29 | `kur-TR/H-help-02` | Kurdisch (Kurmandschi) | Hilfe `H-help-02` | 12 Seiten, 16.467 Wörter | 3 | `GUIDE.md`, Wellen `S` und Administration |
| 30 | `ara-SA/H-help-03` | Arabisch | Hilfe `H-help-03` | 12 Seiten, 20.798 Wörter | 4 | `GUIDE.md`, RTL, PDF-Schrift Arab, Wellen `S` und Administration |
| 31 | `tur-TR/H-help-03` | Türkisch | Hilfe `H-help-03` | 12 Seiten, 20.798 Wörter | 2 | `GUIDE.md`, Wellen `S` und Administration |
| 32 | `ukr-UA/H-help-03` | Ukrainisch | Hilfe `H-help-03` | 12 Seiten, 20.798 Wörter | 3 | `GUIDE.md`, Wellen `S` und Administration |
| 33 | `rus-RU/H-help-03` | Russisch | Hilfe `H-help-03` | 12 Seiten, 20.798 Wörter | 3 | `GUIDE.md`, Wellen `S` und Administration |
| 34 | `fas-IR/H-help-03` | Persisch | Hilfe `H-help-03` | 12 Seiten, 20.798 Wörter | 4 | `GUIDE.md`, RTL, PDF-Schrift Arab, Wellen `S` und Administration |
| 35 | `pus-AF/H-help-03` | Paschtu | Hilfe `H-help-03` | 12 Seiten, 20.798 Wörter | 5 | `GUIDE.md`, RTL, PDF-Schrift Arab, Wellen `S` und Administration |
| 36 | `kur-TR/H-help-03` | Kurdisch (Kurmandschi) | Hilfe `H-help-03` | 12 Seiten, 20.798 Wörter | 3 | `GUIDE.md`, Wellen `S` und Administration |
| 37 | `ara-SA/H-help-04` | Arabisch | Hilfe `H-help-04` | 12 Seiten, 29.096 Wörter | 4 | `GUIDE.md`, RTL, PDF-Schrift Arab, Wellen `S` und Administration |
| 38 | `tur-TR/H-help-04` | Türkisch | Hilfe `H-help-04` | 12 Seiten, 29.096 Wörter | 2 | `GUIDE.md`, Wellen `S` und Administration |
| 39 | `ukr-UA/H-help-04` | Ukrainisch | Hilfe `H-help-04` | 12 Seiten, 29.096 Wörter | 3 | `GUIDE.md`, Wellen `S` und Administration |
| 40 | `rus-RU/H-help-04` | Russisch | Hilfe `H-help-04` | 12 Seiten, 29.096 Wörter | 3 | `GUIDE.md`, Wellen `S` und Administration |
| 41 | `fas-IR/H-help-04` | Persisch | Hilfe `H-help-04` | 12 Seiten, 29.096 Wörter | 4 | `GUIDE.md`, RTL, PDF-Schrift Arab, Wellen `S` und Administration |
| 42 | `pus-AF/H-help-04` | Paschtu | Hilfe `H-help-04` | 12 Seiten, 29.096 Wörter | 5 | `GUIDE.md`, RTL, PDF-Schrift Arab, Wellen `S` und Administration |
| 43 | `kur-TR/H-help-04` | Kurdisch (Kurmandschi) | Hilfe `H-help-04` | 12 Seiten, 29.096 Wörter | 3 | `GUIDE.md`, Wellen `S` und Administration |
| 44 | `ara-SA/H-help-manual-01` | Arabisch | Hilfe `H-help-manual-01` | 44 Seiten, 17.659 Wörter | 4 | `GUIDE.md`, RTL, PDF-Schrift Arab, Wellen `S` und Administration |
| 45 | `tur-TR/H-help-manual-01` | Türkisch | Hilfe `H-help-manual-01` | 44 Seiten, 17.659 Wörter | 2 | `GUIDE.md`, Wellen `S` und Administration |
| 46 | `ukr-UA/H-help-manual-01` | Ukrainisch | Hilfe `H-help-manual-01` | 44 Seiten, 17.659 Wörter | 3 | `GUIDE.md`, Wellen `S` und Administration |
| 47 | `rus-RU/H-help-manual-01` | Russisch | Hilfe `H-help-manual-01` | 44 Seiten, 17.659 Wörter | 3 | `GUIDE.md`, Wellen `S` und Administration |
| 48 | `fas-IR/H-help-manual-01` | Persisch | Hilfe `H-help-manual-01` | 44 Seiten, 17.659 Wörter | 4 | `GUIDE.md`, RTL, PDF-Schrift Arab, Wellen `S` und Administration |
| 49 | `pus-AF/H-help-manual-01` | Paschtu | Hilfe `H-help-manual-01` | 44 Seiten, 17.659 Wörter | 5 | `GUIDE.md`, RTL, PDF-Schrift Arab, Wellen `S` und Administration |
| 50 | `kur-TR/H-help-manual-01` | Kurdisch (Kurmandschi) | Hilfe `H-help-manual-01` | 44 Seiten, 17.659 Wörter | 3 | `GUIDE.md`, Wellen `S` und Administration |
| 51 | `ara-SA/H-help-manual-02` | Arabisch | Hilfe `H-help-manual-02` | 44 Seiten, 18.763 Wörter | 4 | `GUIDE.md`, RTL, PDF-Schrift Arab, Wellen `S` und Administration |
| 52 | `tur-TR/H-help-manual-02` | Türkisch | Hilfe `H-help-manual-02` | 44 Seiten, 18.763 Wörter | 2 | `GUIDE.md`, Wellen `S` und Administration |
| 53 | `ukr-UA/H-help-manual-02` | Ukrainisch | Hilfe `H-help-manual-02` | 44 Seiten, 18.763 Wörter | 3 | `GUIDE.md`, Wellen `S` und Administration |
| 54 | `rus-RU/H-help-manual-02` | Russisch | Hilfe `H-help-manual-02` | 44 Seiten, 18.763 Wörter | 3 | `GUIDE.md`, Wellen `S` und Administration |
| 55 | `fas-IR/H-help-manual-02` | Persisch | Hilfe `H-help-manual-02` | 44 Seiten, 18.763 Wörter | 4 | `GUIDE.md`, RTL, PDF-Schrift Arab, Wellen `S` und Administration |
| 56 | `pus-AF/H-help-manual-02` | Paschtu | Hilfe `H-help-manual-02` | 44 Seiten, 18.763 Wörter | 5 | `GUIDE.md`, RTL, PDF-Schrift Arab, Wellen `S` und Administration |
| 57 | `kur-TR/H-help-manual-02` | Kurdisch (Kurmandschi) | Hilfe `H-help-manual-02` | 44 Seiten, 18.763 Wörter | 3 | `GUIDE.md`, Wellen `S` und Administration |
| 58 | `ara-SA/H-help-non-technical-people` | Arabisch | Hilfe `H-help-non-technical-people` | 48 Seiten, 28.376 Wörter | 4 | `GUIDE.md`, RTL, PDF-Schrift Arab, Wellen `S` und Administration |
| 59 | `tur-TR/H-help-non-technical-people` | Türkisch | Hilfe `H-help-non-technical-people` | 48 Seiten, 28.376 Wörter | 2 | `GUIDE.md`, Wellen `S` und Administration |
| 60 | `ukr-UA/H-help-non-technical-people` | Ukrainisch | Hilfe `H-help-non-technical-people` | 48 Seiten, 28.376 Wörter | 3 | `GUIDE.md`, Wellen `S` und Administration |

Die ganze Warteschlange und die Seiten einer Welle: `./console exp:i18n waves --list`, `./console exp:i18n waves --wave=<Welle>`.

Erzeugt mit `./console exp:i18n waves`; `./console exp:i18n waves --check` und der Test `expI18nTranslationWavesTest` schlagen fehl, sobald ein Prozentwert nicht mehr stimmt oder ein Umfang um mehr als 3 % abweicht.
<!-- exp:i18n waves data {"status":{"afr-ZA":[99,100,0,0],"aka-GH":[99,100,0,0],"amh-ET":[99,100,0,0],"ara-SA":[99,100,0,0],"arc-IQ":[99,100,0,0],"asm-IN":[99,100,0,0],"ast-ES":[99,100,0,0],"aze-AZ":[99,100,0,0],"bam-ML":[99,100,0,0],"bel-BY":[99,100,0,0],"ben-BD":[99,100,0,0],"bho-IN":[99,100,0,0],"bod-CN":[99,100,0,0],"bos-BA":[99,100,0,0],"bre-FR":[99,100,0,0],"brx-IN":[99,100,0,0],"bul-BG":[99,100,0,0],"cat-ES":[99,100,0,0],"ceb-PH":[99,100,0,0],"che-RU":[99,100,0,0],"chi-CN":[99,100,0,0],"chi-TW":[99,100,0,0],"ckb-IQ":[99,100,0,0],"cro-HR":[99,100,0,0],"cym-GB":[99,100,0,0],"cze-CZ":[99,100,0,0],"dan-DK":[99,100,0,0],"div-MV":[99,100,0,0],"doi-IN":[99,100,0,0],"dsb-DE":[99,100,0,0],"dut-NL":[99,100,0,0],"dzo-BT":[99,100,0,0],"ell-GR":[99,100,0,0],"eng-AU":[100,100,100,100],"eng-US":[100,100,100,100],"epo-EO":[99,100,0,0],"esl-ES":[99,100,0,0],"esl-MX":[99,100,0,0],"est-EE":[99,100,0,0],"eus-ES":[99,100,0,0],"ewe-GH":[99,100,0,0],"fao-FO":[99,100,0,0],"fas-AF":[99,100,0,0],"fas-IR":[99,100,0,0],"fil-PH":[99,100,0,0],"fin-FI":[99,100,0,0],"fre-FR":[99,100,0,0],"fry-NL":[99,100,0,0],"ful-SN":[99,100,0,0],"ger-DE":[100,76,100,100],"gla-GB":[99,100,0,0],"gle-IE":[99,100,0,0],"glg-ES":[99,100,0,0],"grn-PY":[99,80,0,0],"guj-IN":[99,100,0,0],"hau-NG":[99,100,0,0],"haw-US":[99,100,0,0],"heb-IL":[99,100,0,0],"hin-IN":[99,100,0,0],"hsb-DE":[99,100,0,0],"hun-HU":[99,100,0,0],"huy-IL":[99,100,0,0],"hye-AM":[99,100,0,0],"ibo-NG":[99,100,0,0],"ind-ID":[99,100,0,0],"isl-IS":[99,100,0,0],"ita-IT":[99,100,0,0],"jav-ID":[99,100,0,0],"jpn-JP":[99,100,0,0],"kab-DZ":[99,100,0,0],"kal-GL":[99,100,0,0],"kan-IN":[99,100,0,0],"kas-IN":[99,100,0,0],"kat-GE":[99,100,0,0],"kaz-KZ":[99,100,0,0],"khm-KH":[99,100,0,0],"kik-KE":[99,100,0,0],"kin-RW":[99,100,0,0],"kir-KG":[99,100,0,0],"kok-IN":[99,100,0,0],"kor-KP":[99,100,0,0],"kor-KR":[99,100,0,0],"kur-TR":[99,100,0,0],"lao-LA":[99,100,0,0],"lat-VA":[99,100,0,0],"lav-LV":[99,100,0,0],"lin-CD":[99,100,0,0],"lit-LT":[99,100,0,0],"ltz-LU":[99,100,0,0],"lug-UG":[99,100,0,0],"mai-IN":[99,100,0,0],"mal-IN":[99,100,0,0],"mar-IN":[99,100,0,0],"mkd-MK":[99,100,0,0],"mlg-MG":[99,100,0,0],"mlt-MT":[99,100,0,0],"mni-IN":[99,100,0,0],"mon-MN":[99,100,0,0],"mri-NZ":[99,100,0,0],"msa-MY":[99,100,0,0],"mya-MM":[99,100,0,0],"nep-NP":[99,100,0,0],"nor-NO":[99,100,0,0],"nqo-GN":[99,100,0,0],"oci-FR":[99,100,0,0],"ori-IN":[99,100,0,0],"orm-ET":[99,100,0,0],"pan-IN":[99,100,0,0],"pcm-NG":[99,100,0,0],"pnb-PK":[99,100,0,0],"pol-PL":[99,100,0,0],"por-BR":[99,100,0,0],"por-PT":[99,100,0,0],"pus-AF":[99,100,0,0],"que-PE":[99,100,0,0],"roh-CH":[99,100,0,0],"ron-RO":[99,100,0,0],"run-BI":[99,100,0,0],"rus-RU":[99,100,0,0],"san-IN":[99,100,0,0],"sat-IN":[99,100,0,0],"ser-SR":[99,100,0,0],"sin-LK":[99,100,0,0],"slk-SK":[99,100,0,0],"slv-SI":[99,100,0,0],"sme-NO":[99,100,0,0],"sna-ZW":[99,100,0,0],"snd-PK":[99,100,0,0],"som-SO":[99,100,0,0],"sqi-AL":[99,100,0,0],"srp-RS":[99,100,0,0],"sun-ID":[99,100,0,0],"swa-KE":[99,100,0,0],"swe-SE":[99,100,0,0],"syr-IQ":[99,100,0,0],"tam-IN":[99,100,0,0],"tat-RU":[99,100,0,0],"tel-IN":[99,100,0,0],"tgk-TJ":[99,100,0,0],"tha-TH":[99,100,0,0],"tir-ET":[99,100,0,0],"tlh-QO":[99,100,0,0],"ton-TO":[99,100,0,0],"tuk-TM":[99,100,0,0],"tur-TR":[99,100,0,0],"uig-CN":[99,100,0,0],"ukr-UA":[99,100,0,0],"urd-PK":[99,100,0,0],"uzb-UZ":[99,100,0,0],"vie-VN":[99,100,0,0],"wol-SN":[99,100,0,0],"xho-ZA":[99,100,0,0],"yid-US":[99,100,0,0],"yor-NG":[99,100,0,0],"zgh-MA":[99,100,0,0],"zul-ZA":[99,100,0,0]},"sizes":{"messages":21355,"extensions":567,"parts":223,"daily_pages":355,"daily_words":476061,"guides_pages":3858,"guides_words":3173287,"reference_pages":1829,"reference_words":1892684,"tables_pages":0,"tables_words":124226},"waves":34899,"untranslated":["hat-HT","skr-PK","tsn-ZA","dyu-CI","nya-MW","nso-ZA","sot-ZA","mos-BF","azb-IR","kau-NG","vmw-MZ","kon-CD","luy-KE","tso-ZA","umb-AO","luo-KE","sag-CF","bal-PK","kln-KE","lua-CD","suk-TZ","ale-US","arn-CL","aym-BO","chr-US","cre-CA","ems-US","esu-US","gwi-US","hai-US","iku-CA","ipk-US","lkt-US","moh-CA","nah-MX","nav-US","oji-CA","quc-GT","tli-US","tsi-US","yua-MX"]} -->
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
