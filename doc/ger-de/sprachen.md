---
summary: Welche Sprachen Exponential 6.0.15 unterstützt und wie gut, von den Übersetzungen der Administration und den Locales bis zu Schriften von rechts nach links, Fallbacks und den Sprachen der Hilfe. Mit den Zahlen einer Standardinstallation, einer Weltkarte, der Wahl, dem Wechsel und dem Hinzufügen einer Sprache, dem Melden eines Übersetzungsfehlers und den Referenztabellen.
---
# Sprachen in Exponential

Diese Seite fasst zusammen, mit welchen Sprachen Exponential 6.0.15 ausgeliefert wird: was unterstützt wird und wie
gut, die Zahlen einer Standardinstallation und eine Karte der Länder und Gebiete, deren Sprache die Administration
spricht. Sie erklärt, wie Sie eine Sprache wählen, wechseln und hinzufügen und wie Sie einen Übersetzungsfehler
melden, und sie enthält die Referenztabellen aller Locales und aller Übersetzungen der Administrationsoberfläche.

## Was Exponential unterstützt

| Bereich | Was mit 6.0.15 ausgeliefert wird | Mehr |
|---|---|---|
| **Übersetzungen der Administration** | Ein Katalog je Sprache in `share/translations/<code>/`, für den Kernel und seine Designs, dazu die Kataloge der mitgelieferten Extensions ezoe und ezjscore. Jeder übersetzt mindestens 99 % der aktuellen Texte. `eng-US` ist die Sprache der Quelltexte, `ger-DE` wurde von Hand übersetzt; alle anderen Übersetzungen sind maschinell unterstützt entstanden und offen für eine Durchsicht. | [Abschnitt 4](#4-alle-übersetzungen-der-administrationsoberfläche) |
| **Locales** | Eine `share/locale/<code>.ini` je Sprache und Land mit den Namen, Datums- und Zeitformaten, Zahlen, Währung, dem ersten Tag der Woche und der Schreibrichtung. Jede Locale lässt sich als Inhaltssprache und als Locale eines Siteaccess wählen. Locales sind reine INI-Dateien und brauchen zur Laufzeit keine PHP-Extension. | [Abschnitte 3](#3-alle-locales), [7](#7-eine-locale-hinzufügen), [10](#10-keine-php-extension-zur-laufzeit) |
| **Von rechts nach links** | Die Locales für Arabisch, Hebräisch, Persisch, Urdu, Paschtu, Sindhi, Kaschmiri, Uigurisch, Zentralkurdisch, Syrisch, Jiddisch, N’Ko, Dhivehi und weitere sind als rechts nach links markiert. Die Administration wechselt in diesen Sprachen auf rechts nach links, und Inhalte in ihnen erscheinen auf jeder Seite von rechts nach links. | [Abschnitt 9](#9-sprachen-die-von-rechts-nach-links-geschrieben-werden) |
| **Fallbacks** | Eine Locale ohne eigenen Katalog zeigt den ihrer Sprache, für den Kernel und für jede Extension getrennt: `fre-CA` zeigt `fre-FR`, jedes arabische Land `ara-SA`. Einstellbar in `i18n.ini`. | [Abschnitt 13](#13-übersetzungs-fallbacks) |
| **Inhaltssprachen** | Inhalte lassen sich in der Sprache jeder Locale schreiben. Eine Installation fasst bis zu 62 Inhaltssprachen (30 mit 32-Bit-PHP). | [Abschnitt 11](#11-die-grenze-von-62-inhaltssprachen), [Inhaltssprachen](anleitungen/inhaltssprachen.md) |
| **Hilfe und Dokumentation** | Englisch ist das Original, Deutsch die erste Übersetzung. Die Hilfe folgt der Oberflächensprache und zeigt die englische Seite, wo eine Übersetzung fehlt. | [Die Hilfe in Ihrer Sprache lesen](hilfe/hilfe-in-anderen-sprachen-lesen.md) |
| **Testsprache** | Lorem Ipsum (`qlo-ZZ`), zum Testen von Inhaltsübersetzungen. Sie wird nie von selbst hinzugefügt. | [Abschnitt 12](#12-testsprachen-lorem-ipsum) |

Es gibt drei Stufen der Unterstützung (Einzelheiten in [Abschnitt 2](#2-unterstützungsstufen)). Auf der ersten
zeigt die Administration eine Sprache aus einer eigenen Übersetzung. Auf der zweiten zeigt sie die Übersetzung
derselben Sprache über einen Fallback. Auf der dritten, nur Inhalt, bleibt die Oberfläche englisch, während Inhalte,
Datumsangaben, Zahlen und Währung der Locale folgen. In einer Standardinstallation von 6.0.15 erreicht jede
mitgelieferte Sprache die erste oder die zweite Stufe; die Tabelle unten zählt sie.

## Die Zahlen einer Standardinstallation

<!-- generated: exp:i18n coverage 2026-10-29 (start) -->
**Auf einen Blick, Stand 29. Oktober 2026.** Eine Standardinstallation (die mitgelieferten `share/locale`,
`share/translations` und `settings/i18n.ini`, ohne Erweiterungen) hat:

| Was | Anzahl |
|---|---:|
| Sprachen der Administration: Übersetzungen in `share/translations` | **155**, davon 155 vollständig |
| Locales: Dateien `share/locale/*.ini` | **368**: 361 Sprachen und Länder, 1 Testsprache, 6 Varianten |
| Bekannte Sprachen: die Sprachcodes der Locales (ohne Varianten und Testsprache) | **150** |
| davon von der Administration übersetzt gezeigt (eigene Übersetzung, Fallback oder Englisch) | **150** |
| davon nicht übersetzt ([Liste](#die-noch-nicht-übersetzten-sprachen)) | **0** |
| Länder und Gebiete der Locales | **247** |
| davon mit der Administration in ihrer Sprache (grün auf der Karte) | **247** |
| Codes aus ISO 3166-1 (`settings/country.ini`) ohne Locale | **4**, alle mit Absicht: unbewohnt oder zurückgezogen (Liste unter der Karte) |
| Locales, die von rechts nach links geschrieben werden | **43** |

![Weltkarte: 247 Länder und Gebiete in Grün, für die eine mitgelieferte Locale die Administration in ihrer Sprache zeigt, dazu 3 unbewohnte Gebiete ohne Locale mit Absicht; die übrigen in Grau](bilder/sprachen/abdeckungskarte.webp)

Ein Land ist grün, wenn für es eine Locale mitgeliefert wird, deren Sprache die Administration übersetzt zeigt:
mit einer eigenen Übersetzung oder der ersten Übersetzung ihrer Fallback-Kette (`i18n.ini [TranslationSettings]`,
`FallbackMode=first`, siehe [Abschnitt 13](#13-übersetzungs-fallbacks)). Englisch zählt als übersetzt: Es ist die
Sprache der Quelltexte. Die Karte als Vektorgrafik: `doc/ger-de/bilder/sprachen/abdeckungskarte.svg`; Umrisse: Natural Earth, gemeinfrei.

Ebenfalls grün sind die unbewohnten Gebiete ohne Locale mit Absicht: Antarktis (`AQ`), Bouvetinsel (`BV`) und Heard- und McDonald-Inseln (`HM`).
Exponential bedient diese Gebiete in jeder Sprache; lokale Konventionen gibt es keine.

<details><summary>Die 247 Länder und Gebiete in Grün</summary>

| Land oder Gebiet | Code | Locales und die Übersetzung, die sie zeigen |
|---|---|---|
| Afghanistan | AF | `fas-AF`, `pus-AF` |
| Ägypten | EG | `ara-EG` → `ara-SA` |
| Alandinseln | AX | `swe-AX` → `swe-SE` |
| Albanien | AL | `sqi-AL` |
| Algerien | DZ | `ara-DZ` → `ara-SA`, `fre-DZ` → `fre-FR`, `kab-DZ` |
| Amerikanisch-Ozeanien | UM | `eng-UM` (Quelltexte) |
| Amerikanisch-Samoa | AS | `eng-AS` (Quelltexte) |
| Amerikanische Jungferninseln | VI | `eng-VI` (Quelltexte) |
| Andorra | AD | `cat-AD` → `cat-ES` |
| Angola | AO | `por-AO` → `por-PT` |
| Anguilla | AI | `eng-AI` (Quelltexte) |
| Antigua und Barbuda | AG | `eng-AG` (Quelltexte) |
| Äquatorialguinea | GQ | `esl-GQ` → `esl-ES`, `fre-GQ` → `fre-FR` |
| Argentinien | AR | `esl-AR` → `esl-ES` |
| Armenien | AM | `hye-AM` |
| Aruba | AW | `dut-AW` → `dut-NL` |
| Aserbaidschan | AZ | `aze-AZ` |
| Äthiopien | ET | `amh-ET`, `orm-ET`, `tir-ET` |
| Australien | AU | `eng-AU` |
| Bahamas | BS | `eng-BS` (Quelltexte) |
| Bahrain | BH | `ara-BH` → `ara-SA` |
| Bangladesch | BD | `ben-BD` |
| Barbados | BB | `eng-BB` (Quelltexte) |
| Belarus | BY | `bel-BY`, `rus-BY` → `rus-RU` |
| Belgien | BE | `dut-BE` → `dut-NL`, `fre-BE` → `fre-FR`, `ger-BE` → `ger-DE` |
| Belize | BZ | `eng-BZ` (Quelltexte) |
| Benin | BJ | `fre-BJ` → `fre-FR` |
| Bermuda | BM | `eng-BM` (Quelltexte) |
| Bhutan | BT | `dzo-BT` |
| Birma (Myanmar) | MM | `mya-MM` |
| Bolivien | BO | `esl-BO` → `esl-ES` |
| Bonaire, Saint Eustatius and Saba | BQ | `dut-BQ` → `dut-NL` |
| Bosnien und Herzegowina | BA | `bos-BA` |
| Botsuana | BW | `eng-BW` (Quelltexte) |
| Brasilien | BR | `por-BR` |
| Britische Jungferninseln | VG | `eng-VG` (Quelltexte) |
| Britisches Territorium im Indischen Ozean | IO | `eng-IO` (Quelltexte) |
| Brunei | BN | `msa-BN` → `msa-MY` |
| Bulgarien | BG | `bul-BG` |
| Burkina Faso | BF | `fre-BF` → `fre-FR` |
| Burundi | BI | `fre-BI` → `fre-FR`, `run-BI` |
| Chile | CL | `esl-CL` → `esl-ES` |
| China | CN | `bod-CN`, `chi-CN`, `uig-CN` |
| Cookinseln | CK | `eng-CK` (Quelltexte) |
| Costa Rica | CR | `esl-CR` → `esl-ES` |
| Curaçao | CW | `dut-CW` → `dut-NL` |
| Dänemark | DK | `dan-DK` |
| Deutschland | DE | `dsb-DE`, `ger-DE`, `hsb-DE` |
| Dominica | DM | `eng-DM` (Quelltexte) |
| Dominikanische Republik | DO | `esl-DO` → `esl-ES` |
| Dschibuti | DJ | `ara-DJ` → `ara-SA`, `fre-DJ` → `fre-FR` |
| Ecuador | EC | `esl-EC` → `esl-ES` |
| Ehemalige jugoslawische Republik Mazedonien | MK | `mkd-MK` |
| El Salvador | SV | `esl-SV` → `esl-ES` |
| Elfenbeinküste | CI | `fre-CI` → `fre-FR` |
| Eritrea | ER | `ara-ER` → `ara-SA` |
| Estland | EE | `est-EE` |
| Falklandinseln | FK | `eng-FK` (Quelltexte) |
| Färöer-Inseln | FO | `dan-FO` → `dan-DK`, `fao-FO` |
| Fidschi | FJ | `eng-FJ` (Quelltexte) |
| Finnland | FI | `fin-FI` |
| Frankreich | FR | `bre-FR`, `fre-FR`, `oci-FR` |
| Französisch-Guayana | GF | `fre-GF` → `fre-FR` |
| Französisch-Polynesien | PF | `fre-PF` → `fre-FR` |
| Französische Süd- und Antarktisgebiete | TF | `fre-TF` → `fre-FR` |
| Gabun | GA | `fre-GA` → `fre-FR` |
| Gambia | GM | `eng-GM` (Quelltexte) |
| Georgien | GE | `kat-GE` |
| Ghana | GH | `aka-GH`, `eng-GH` (Quelltexte), `ewe-GH` |
| Gibraltar | GI | `eng-GI` (Quelltexte) |
| Grenada | GD | `eng-GD` (Quelltexte) |
| Griechenland | GR | `ell-GR` |
| Grönland | GL | `kal-GL` |
| Guadeloupe | GP | `fre-GP` → `fre-FR` |
| Guam | GU | `eng-GU` (Quelltexte) |
| Guatemala | GT | `esl-GT` → `esl-ES` |
| Guernsey | GG | `eng-GG` (Quelltexte) |
| Guinea | GN | `fre-GN` → `fre-FR`, `nqo-GN` |
| Guinea-Bissau | GW | `por-GW` → `por-PT` |
| Guyana | GY | `eng-GY` (Quelltexte) |
| Haiti | HT | `fre-HT` → `fre-FR` |
| Honduras | HN | `esl-HN` → `esl-ES` |
| Hongkong | HK | `chi-HK` → `chi-TW` |
| Indien | IN | `asm-IN`, `ben-IN` → `ben-BD`, `bho-IN`, `brx-IN`, `doi-IN`, `eng-IN` (Quelltexte), `guj-IN`, `hin-IN`, `kan-IN`, `kas-IN`, `kok-IN`, `mai-IN`, `mal-IN`, `mar-IN`, `mni-IN`, `ori-IN`, `pan-IN`, `san-IN`, `sat-IN`, `tam-IN`, `tel-IN`, `urd-IN` → `urd-PK` |
| Indonesien | ID | `ind-ID`, `jav-ID`, `sun-ID` |
| Insel Man | IM | `eng-IM` (Quelltexte) |
| Irak | IQ | `ara-IQ` → `ara-SA`, `arc-IQ`, `ckb-IQ`, `syr-IQ` |
| Iran (Islamische Republik) | IR | `fas-IR` |
| Irland | IE | `eng-IE` (Quelltexte), `gle-IE` |
| Island | IS | `isl-IS` |
| Israel | IL | `heb-IL`, `huy-IL` |
| Italien | IT | `ita-IT` |
| Jamaika | JM | `eng-JM` (Quelltexte) |
| Japan | JP | `jpn-JP` |
| Jemen | YE | `ara-YE` → `ara-SA` |
| Jersey | JE | `eng-JE` (Quelltexte) |
| Jordanien | JO | `ara-JO` → `ara-SA` |
| Kaimaninseln | KY | `eng-KY` (Quelltexte) |
| Kambodscha | KH | `khm-KH` |
| Kamerun | CM | `fre-CM` → `fre-FR` |
| Kanada | CA | `eng-CA` (Quelltexte), `fre-CA` → `fre-FR` |
| Kap Verde | CV | `por-CV` → `por-PT` |
| Kasachstan | KZ | `kaz-KZ`, `rus-KZ` → `rus-RU` |
| Katar | QA | `ara-QA` → `ara-SA` |
| Kenia | KE | `eng-KE` (Quelltexte), `kik-KE`, `swa-KE` |
| Kirgistan | KG | `kir-KG`, `rus-KG` → `rus-RU` |
| Kiribati | KI | `eng-KI` (Quelltexte) |
| Kokosinseln | CC | `eng-CC` → `eng-AU` |
| Kolumbien | CO | `esl-CO` → `esl-ES` |
| Komoren | KM | `ara-KM` → `ara-SA`, `fre-KM` → `fre-FR` |
| Kongo (Republik Kongo) | CG | `fre-CG` → `fre-FR` |
| Kosovo | XK | `sqi-XK` → `sqi-AL` |
| Kroatien | HR | `cro-HR` |
| Kuba | CU | `esl-CU` → `esl-ES` |
| Kuwait | KW | `ara-KW` → `ara-SA` |
| Land Sint Maarten | SX | `dut-SX` → `dut-NL` |
| Laos | LA | `lao-LA` |
| Lesotho | LS | `eng-LS` (Quelltexte) |
| Lettland | LV | `lav-LV` |
| Libanon | LB | `ara-LB` → `ara-SA` |
| Liberia | LR | `eng-LR` (Quelltexte) |
| Libysch-Arabische Dschamahirija | LY | `ara-LY` → `ara-SA` |
| Liechtenstein | LI | `ger-LI` → `ger-DE` |
| Litauen | LT | `lit-LT` |
| Luxemburg | LU | `fre-LU` → `fre-FR`, `ger-LU` → `ger-DE`, `ltz-LU` |
| Macau | MO | `chi-MO` → `chi-TW` |
| Madagaskar | MG | `fre-MG` → `fre-FR`, `mlg-MG` |
| Malawi | MW | `eng-MW` (Quelltexte) |
| Malaysien | MY | `msa-MY` |
| Malediven | MV | `div-MV` |
| Mali | ML | `bam-ML`, `fre-ML` → `fre-FR` |
| Malta | MT | `mlt-MT` |
| Marokko | MA | `ara-MA` → `ara-SA`, `fre-MA` → `fre-FR`, `zgh-MA` |
| Marshall-Inseln | MH | `eng-MH` (Quelltexte) |
| Martinique | MQ | `fre-MQ` → `fre-FR` |
| Mauretanien | MR | `ara-MR` → `ara-SA`, `fre-MR` → `fre-FR` |
| Mauritius | MU | `fre-MU` → `fre-FR` |
| Mayotte | YT | `fre-YT` → `fre-FR` |
| Mexiko | MX | `esl-MX` |
| Mikronesien | FM | `eng-FM` (Quelltexte) |
| Moldau (Republik Moldau) | MD | `ron-MD` → `ron-RO`, `rus-MD` → `rus-RU` |
| Monaco | MC | `fre-MC` → `fre-FR` |
| Mongolei | MN | `mon-MN` |
| Montenegro | ME | `srp-ME` → `srp-RS` |
| Montserrat | MS | `eng-MS` (Quelltexte) |
| Mosambik | MZ | `por-MZ` → `por-PT` |
| Namibia | NA | `eng-NA` (Quelltexte) |
| Nauru | NR | `eng-NR` (Quelltexte) |
| Nepal | NP | `nep-NP` |
| Neukaledonien | NC | `fre-NC` → `fre-FR` |
| Neuseeland | NZ | `eng-NZ` → `eng-AU`, `mri-NZ` |
| Niederlande | NL | `dut-NL`, `fry-NL` |
| Niger | NE | `fre-NE` → `fre-FR` |
| Nigeria | NG | `eng-NG` (Quelltexte), `hau-NG`, `ibo-NG`, `pcm-NG`, `yor-NG` |
| Nikaragua | NI | `esl-NI` → `esl-ES` |
| Niue | NU | `eng-NU` (Quelltexte) |
| Nordkorea | KP | `kor-KP` |
| Nördliche Marianen | MP | `eng-MP` (Quelltexte) |
| Norfolkinsel | NF | `eng-NF` → `eng-AU` |
| Norwegen | NO | `nno-NO` → `nor-NO`, `nor-NO`, `sme-NO` |
| Oman | OM | `ara-OM` → `ara-SA` |
| Österreich | AT | `ger-AT` → `ger-DE` |
| Pakistan | PK | `eng-PK` (Quelltexte), `pnb-PK`, `snd-PK`, `urd-PK` |
| Palästinensische Autonomiegebiete | PS | `ara-PS` → `ara-SA` |
| Palau | PW | `eng-PW` (Quelltexte) |
| Panama | PA | `esl-PA` → `esl-ES` |
| Papua-Neuguinea | PG | `eng-PG` (Quelltexte) |
| Paraguay | PY | `esl-PY` → `esl-ES` |
| Peru | PE | `esl-PE` → `esl-ES`, `que-PE` |
| Philippinen | PH | `ceb-PH`, `eng-PH` (Quelltexte), `fil-PH` |
| Pitcairn | PN | `eng-PN` (Quelltexte) |
| Polen | PL | `pol-PL` |
| Portugal | PT | `por-PT` |
| Puerto Rico | PR | `esl-PR` → `esl-ES` |
| Republik Südafrika | ZA | `afr-ZA`, `eng-ZA` (Quelltexte), `xho-ZA`, `zul-ZA` |
| Réunion | RE | `fre-RE` → `fre-FR` |
| Ruanda | RW | `fre-RW` → `fre-FR`, `kin-RW` |
| Rumänien | RO | `ron-RO` |
| Russland | RU | `che-RU`, `rus-RU`, `tat-RU` |
| Saint Kitts und Nevis | KN | `eng-KN` (Quelltexte) |
| Sambia | ZM | `eng-ZM` (Quelltexte) |
| Samoa | WS | `eng-WS` (Quelltexte) |
| San Marino | SM | `ita-SM` → `ita-IT` |
| Sao Tomé und Principe | ST | `por-ST` → `por-PT` |
| Saudi-Arabien | SA | `ara-SA` |
| Schweden | SE | `swe-SE` |
| Schweiz | CH | `fre-CH` → `fre-FR`, `ger-CH` → `ger-DE`, `ita-CH` → `ita-IT`, `roh-CH` |
| Senegal | SN | `fre-SN` → `fre-FR`, `ful-SN`, `wol-SN` |
| Serbien | RS | `ser-SR`, `srp-RS` |
| Seychellen | SC | `fre-SC` → `fre-FR` |
| Sierra Leone | SL | `eng-SL` (Quelltexte) |
| Simbabwe | ZW | `eng-ZW` (Quelltexte), `sna-ZW` |
| Singapur | SG | `chi-SG` → `chi-CN`, `eng-SG` (Quelltexte), `msa-SG` → `msa-MY`, `tam-SG` → `tam-IN` |
| Slowakei (Slowakische Republik) | SK | `slk-SK` |
| Slowenien | SI | `slv-SI` |
| Solomon-Inseln | SB | `eng-SB` (Quelltexte) |
| Somalia | SO | `ara-SO` → `ara-SA`, `som-SO` |
| Spanien | ES | `ast-ES`, `cat-ES`, `esl-ES`, `eus-ES`, `glg-ES` |
| Sri Lanka | LK | `sin-LK`, `tam-LK` → `tam-IN` |
| St. Barthélemy | BL | `fre-BL` → `fre-FR` |
| St. Helena | SH | `eng-SH` (Quelltexte) |
| St. Lucia | LC | `eng-LC` (Quelltexte) |
| St. Martin | MF | `fre-MF` → `fre-FR` |
| St. Pierre und Miquelon | PM | `fre-PM` → `fre-FR` |
| St. Vincent und die Grenadinen | VC | `eng-VC` (Quelltexte) |
| Staat der Vatikanstadt | VA | `lat-VA` |
| Sudan | SD | `ara-SD` → `ara-SA` |
| Südgeorgien und die Südlichen Sandwichinseln | GS | `eng-GS` (Quelltexte) |
| Südkorea | KR | `kor-KR` |
| Südsudan | SS | `eng-SS` (Quelltexte) |
| Suriname | SR | `dut-SR` → `dut-NL` |
| Svalbard und Jan Mayen | SJ | `nor-SJ` → `nor-NO` |
| Swasiland | SZ | `eng-SZ` (Quelltexte) |
| Syrien | SY | `ara-SY` → `ara-SA`, `syr-SY` → `syr-IQ` |
| Tadschikistan | TJ | `tgk-TJ` |
| Taiwan | TW | `chi-TW` |
| Tansania | TZ | `swa-TZ` → `swa-KE` |
| Thailand | TH | `tha-TH` |
| Timor-Leste | TL | `por-TL` → `por-PT` |
| Togo | TG | `fre-TG` → `fre-FR` |
| Tokelau | TK | `eng-TK` (Quelltexte) |
| Tonga | TO | `eng-TO` (Quelltexte), `ton-TO` |
| Trinidad und Tobago | TT | `eng-TT` (Quelltexte) |
| Tschad | TD | `ara-TD` → `ara-SA`, `fre-TD` → `fre-FR` |
| Tschechien | CZ | `cze-CZ` |
| Tunesien | TN | `ara-TN` → `ara-SA`, `fre-TN` → `fre-FR` |
| Türkei | TR | `kur-TR`, `tur-TR` |
| Turkmenistan | TM | `tuk-TM` |
| Turks- und Caicosinseln | TC | `eng-TC` (Quelltexte) |
| Tuwalu | TV | `eng-TV` (Quelltexte) |
| Uganda | UG | `eng-UG` (Quelltexte), `lug-UG` |
| Ukraine | UA | `ukr-UA` |
| Ungarn | HU | `hun-HU` |
| Uruguay | UY | `esl-UY` → `esl-ES` |
| USA | US | `eng-US`, `esl-US` → `esl-ES`, `haw-US`, `yid-US` |
| Usbekistan | UZ | `uzb-UZ` |
| Vanuatu | VU | `fre-VU` → `fre-FR` |
| Venezuela | VE | `esl-VE` → `esl-ES` |
| Vereinigte Arabische Emirate | AE | `ara-AE` → `ara-SA` |
| Vereinigtes Königreich | GB | `cym-GB`, `eng-GB` (Quelltexte), `gla-GB` |
| Vietnam | VN | `vie-VN` |
| Wallis und Futuna | WF | `fre-WF` → `fre-FR` |
| Weihnachtsinsel | CX | `eng-CX` → `eng-AU` |
| Westsahara | EH | `ara-EH` → `ara-SA` |
| Zaire | CD | `fre-CD` → `fre-FR`, `lin-CD` |
| Zentralafrikanische Republik | CF | `fre-CF` → `fre-FR` |
| Zypern | CY | `ell-CY` → `ell-GR`, `tur-CY` → `tur-TR` |

</details>

<details><summary>Die 0 Länder und Gebiete mit einer Locale, aber der Administration auf Englisch</summary>

| Land oder Gebiet | Code | Locales |
|---|---|---|

</details>

<details><summary>Die 4 Codes aus ISO 3166-1 ohne Locale</summary>

| Land oder Gebiet | Code | Warum |
|---|---|---|
| Niederländische Antillen | AN | Keine Locale, mit Absicht: Code 2010 zurückgezogen; seine Nachfolger Bonaire, Saint Eustatius and Saba (BQ), Curaçao (CW) und Land Sint Maarten (SX) haben je eine eigene Locale |
| Antarktis | AQ | Keine Locale, mit Absicht: unbewohnt, CLDR definiert keine Locale dafür (grün dargestellt: Exponential bedient diese Gebiete in jeder Sprache; lokale Konventionen gibt es keine) |
| Bouvetinsel | BV | Keine Locale, mit Absicht: unbewohnt, CLDR definiert keine Locale dafür (grün dargestellt: Exponential bedient diese Gebiete in jeder Sprache; lokale Konventionen gibt es keine) |
| Heard- und McDonald-Inseln | HM | Keine Locale, mit Absicht: unbewohnt, CLDR definiert keine Locale dafür (grün dargestellt: Exponential bedient diese Gebiete in jeder Sprache; lokale Konventionen gibt es keine) |

</details>

Erzeugt mit `./console exp:i18n coverage` aus den mitgelieferten Dateien; `./console exp:i18n coverage --check` und der Test `expI18nCoverageMapTest` schlagen fehl, sobald Zahlen oder Karte veraltet sind.
<!-- generated: exp:i18n coverage (end) -->

### Die Karte lesen

- **Grün** ist ein Land oder Gebiet, für das Exponential eine Locale mitliefert, deren Sprache die Administration
  übersetzt zeigt. Das kann eine eigene Übersetzung der Locale sein, die erste Übersetzung ihrer Fallback-Kette oder
  Englisch, die Sprache der Quelltexte. Die Tabelle „Länder und Gebiete in Grün“ unter der Karte nennt für jedes
  Land seine Locales und die Übersetzung, die jede zeigt: `→` markiert einen Fallback, „(Quelltexte)“ Englisch.
- **Grün mit Absicht** sind drei unbewohnte Gebiete: die Antarktis (`AQ`), die Bouvetinsel (`BV`) sowie die Heard-
  und McDonaldinseln (`HM`). CLDR definiert für sie keine Locale, deshalb liefert Exponential keine mit. Sie sind
  grün, weil es keine lokalen Konventionen gibt, die eine Locale ergänzen könnte: Jede Sprache bedient sie.
- **Grau** wäre ein Land, dessen Locale die Administration mangels Übersetzung auf Englisch zeigt, oder ein Code aus
  ISO 3166-1 ohne Locale und ohne Grund mit Absicht. In 6.0.15 ist kein Land grau. Die Legende behält die Farbe,
  damit eine Locale, die ohne Übersetzung hinzukommt, auffällt.
- Die Niederländischen Antillen (`AN`) fehlen auf der Karte ganz. Ihr Code wurde 2010 zurückgezogen, und jeder
  ihrer Nachfolger (`BQ`, `CW`, `SX`) hat eine eigene Locale.

Tabelle und Karte zeigen eine Standardinstallation: die mitgelieferten Dateien ohne Erweiterungen und ohne
überschriebene Einstellungen. Eigene Locales, Übersetzungen und `i18n.ini`-Einstellungen einer Site zählen nicht mit.

<!-- generated: exp:i18n coverage translations (start) -->
Kurz gefasst: **368 Locale-Dateien** (361 Sprachen und Länder, die Testsprache Lorem Ipsum und 6 Varianten, 43 davon
von rechts nach links geschrieben) lassen sich als Inhaltssprachen und Siteaccess-Locales wählen; es gibt **155
Übersetzungen der Administrationsoberfläche**, alle vollständig: Afrikaans (`afr-ZA`), Akan (`aka-GH`), Albanian
(`sqi-AL`), Amharic (`amh-ET`), Arabic (Saudi Arabia) (`ara-SA`), Armenian (`hye-AM`), Assamese (`asm-IN`), Asturian
(`ast-ES`), Azerbaijani (`aze-AZ`), Bambara (`bam-ML`), Basque (`eus-ES`), Belarusian (`bel-BY`), Bengali (Bangladesh)
(`ben-BD`), Bhojpuri (`bho-IN`), Bodo (`brx-IN`), Bosnian (`bos-BA`), Breton (`bre-FR`), Bulgarian (`bul-BG`), Burmese
(`mya-MM`), Catalan (`cat-ES`), Cebuano (`ceb-PH`), Central Kurdish (`ckb-IQ`), Chechen (`che-RU`), Croatian
(`cro-HR`), Czech (`cze-CZ`), Danish (`dan-DK`), Dari (Afghanistan) (`fas-AF`), Dhivehi (`div-MV`), Dogri (`doi-IN`),
Dutch (Netherlands) (`dut-NL`), Dzongkha (`dzo-BT`), English (Australia) (`eng-AU`), English (United States)
(`eng-US`), Esperanto (`epo-EO`), Estonian (`est-EE`), Ewe (`ewe-GH`), Faroese (`fao-FO`), Filipino (`fil-PH`),
Finnish (`fin-FI`), French (France) (`fre-FR`), Fula (`ful-SN`), Galician (`glg-ES`), Ganda (`lug-UG`), Georgian
(`kat-GE`), German (Germany) (`ger-DE`), Greek (Hellenic) (`ell-GR`), Greenlandic (`kal-GL`), Gujarati (`guj-IN`),
Hausa (`hau-NG`), Hawaiian (`haw-US`), Hebrew (`heb-IL`), Hindi (`hin-IN`), Hulaulá (`huy-IL`), Hungarian (`hun-HU`),
Icelandic (`isl-IS`), Igbo (`ibo-NG`), Imperial Aramaic (`arc-IQ`), Indonesian (`ind-ID`), Irish (`gle-IE`), Italian
(Italy) (`ita-IT`), Japanese (`jpn-JP`), Javanese (`jav-ID`), Kabyle (`kab-DZ`), Kannada (`kan-IN`), Kashmiri
(`kas-IN`), Kazakh (`kaz-KZ`), Khmer (`khm-KH`), Kikuyu (`kik-KE`), Kinyarwanda (`kin-RW`), Kirundi (`run-BI`),
Klingon (`tlh-QO`), Konkani (`kok-IN`), Korean (`kor-KR`), Korean (North Korea) (`kor-KP`), Kurdish (`kur-TR`), Kyrgyz
(`kir-KG`), Lao (`lao-LA`), Latin (`lat-VA`), Latvian (`lav-LV`), Lingala (`lin-CD`), Lithuanian (`lit-LT`), Lower
Sorbian (`dsb-DE`), Luxembourgish (`ltz-LU`), Macedonian (`mkd-MK`), Maithili (`mai-IN`), Malagasy (`mlg-MG`), Malay
(Malaysia) (`msa-MY`), Malayalam (`mal-IN`), Maltese (`mlt-MT`), Manipuri (`mni-IN`), Māori (`mri-NZ`), Marathi
(`mar-IN`), Mongolian (`mon-MN`), Nepali (`nep-NP`), Nigerian Pidgin (`pcm-NG`), Northern Sami (`sme-NO`), Norwegian
Bokmål (`nor-NO`), N’Ko (`nqo-GN`), Occitan (`oci-FR`), Odia (`ori-IN`), Oromo (`orm-ET`), Pashto (`pus-AF`),
Persian (`fas-IR`), Polish (`pol-PL`), Portuguese (Brazil) (`por-BR`), Portuguese (Portugal) (`por-PT`), Punjabi
(`pan-IN`), Quechua (`que-PE`), Romanian (`ron-RO`), Romansh (`roh-CH`), Russian (`rus-RU`), Sanskrit (`san-IN`),
Santali (`sat-IN`), Scottish Gaelic (`gla-GB`), Serbian (Srpski) (`ser-SR`), Serbian (Српски) (`srp-RS`), Shona
(`sna-ZW`), Simplified Chinese (`chi-CN`), Sindhi (`snd-PK`), Sinhala (`sin-LK`), Slovak (`slk-SK`), Slovenian
(`slv-SI`), Somali (`som-SO`), Spanish (Mexico) (`esl-MX`), Spanish (Spain) (`esl-ES`), Standard Moroccan Tamazight
(`zgh-MA`), Sundanese (`sun-ID`), Swahili (Kenya) (`swa-KE`), Swedish (`swe-SE`), Syriac (Iraq) (`syr-IQ`), Tajik
(`tgk-TJ`), Tamil (India) (`tam-IN`), Tatar (`tat-RU`), Telugu (`tel-IN`), Thai (`tha-TH`), Tibetan (`bod-CN`),
Tigrinya (`tir-ET`), Tongan (`ton-TO`), Traditional Chinese (Taiwan) (`chi-TW`), Turkish (`tur-TR`), Turkmen
(`tuk-TM`), Ukrainian (`ukr-UA`), Upper Sorbian (`hsb-DE`), Urdu (Pakistan) (`urd-PK`), Uyghur (`uig-CN`), Uzbek
(`uzb-UZ`), Vietnamese (`vie-VN`), Welsh (`cym-GB`), Western Frisian (`fry-NL`), Western Punjabi (Shahmukhi)
(`pnb-PK`), Wolof (`wol-SN`), Xhosa (`xho-ZA`), Yiddish (`yid-US`), Yoruba (`yor-NG`), Zulu (`zul-ZA`). Die Namen sind
die englischen der Locale-Dateien.
<!-- generated: exp:i18n coverage translations (end) -->

## Inhalt

- [Was Exponential unterstützt](#was-exponential-unterstützt)
- [Die Zahlen einer Standardinstallation](#die-zahlen-einer-standardinstallation)
- [Eine Sprache wählen und wechseln](#eine-sprache-wählen-und-wechseln)
- [Eine Sprache oder eine Locale hinzufügen](#eine-sprache-oder-eine-locale-hinzufügen)
- [Einen Übersetzungsfehler melden](#einen-übersetzungsfehler-melden)

Referenz:

1. [Locales, Übersetzungen, Inhaltssprachen und Siteaccesses](#1-locales-übersetzungen-inhaltssprachen-und-siteaccesses)
2. [Unterstützungsstufen](#2-unterstützungsstufen)
3. [Alle Locales](#3-alle-locales)
4. [Alle Übersetzungen der Administrationsoberfläche](#4-alle-übersetzungen-der-administrationsoberfläche)
5. [Die großen Sprachen der Welt](#5-die-großen-sprachen-der-welt)
6. [Verbreitete Sprachen, die noch fehlen](#6-verbreitete-sprachen-die-noch-fehlen)
7. [Eine Locale hinzufügen](#7-eine-locale-hinzufügen)
8. [Eine Übersetzung der Administrationsoberfläche hinzufügen oder vervollständigen](#8-eine-übersetzung-der-administrationsoberfläche-hinzufügen-oder-vervollständigen)
9. [Sprachen, die von rechts nach links geschrieben werden](#9-sprachen-die-von-rechts-nach-links-geschrieben-werden)
10. [Keine PHP-Extension zur Laufzeit](#10-keine-php-extension-zur-laufzeit)
11. [Die Grenze von 62 Inhaltssprachen](#11-die-grenze-von-62-inhaltssprachen)
12. [Testsprachen: Lorem Ipsum](#12-testsprachen-lorem-ipsum)
13. [Übersetzungs-Fallbacks](#13-übersetzungs-fallbacks)

- [Wie die Übersetzungen entstanden sind](#wie-die-übersetzungen-entstanden-sind)
- [Die noch nicht übersetzten Sprachen](#die-noch-nicht-übersetzten-sprachen)

## Eine Sprache wählen und wechseln

Drei Einstellungen entscheiden, welche Sprache jemand sieht:

1. **Die Oberflächensprache der Administration.** Jeder Benutzer wählt sie in der Wahl der Oberflächensprache der
   Administration (dem Auswahlfeld in der rechten Werkzeugleiste). Die meistgebrauchten Sprachen stehen vorn, dann
   folgen alle Sprachen von A bis Z, jede mit der Angabe, wie vollständig ihre Übersetzung ist. Die Wahl gilt für
   die Sitzung. Mit `uiswitch.ini [UISwitch_language] RememberPreference=enabled` wird sie auch in den Einstellungen
   des Benutzers gespeichert und gilt bei der nächsten Anmeldung wieder. Welche Sprachen die Liste anbietet und in
   welcher Reihenfolge: [Die Liste der Oberflächensprachen](funktionen/6.0/ui-umschalter-sprachen.md) und
   [Einstellungen der UI-Umschalter](anleitungen/ui-umschalter-einstellungen.md#uiswitch_language).
2. **Die Locale eines Siteaccess.** `site.ini [RegionalSettings] Locale` legt die Sprache der Oberflächentexte eines
   Siteaccess und seine Datums-, Zahlen- und Währungsformate fest. `SiteLanguageList[]` legt fest, welche
   Inhaltssprachen er zeigt und in welcher Reihenfolge. Üblich ist ein Siteaccess je Sprache:
   [Eine Sprache anzeigen](anleitungen/inhaltssprachen.md#5-eine-sprache-anzeigen-sitelanguagelist-und-siteaccesses).
3. **Die Inhaltssprachen.** Unter Setup > Sprachen kommen die Sprachen hinzu, in denen Inhalte geschrieben werden
   können, und Redakteure übersetzen jedes Element in sie: [Inhaltssprachen](anleitungen/inhaltssprachen.md).

Die Hilfe folgt der Oberflächensprache: [Die Hilfe in Ihrer Sprache lesen](hilfe/hilfe-in-anderen-sprachen-lesen.md).

## Eine Sprache oder eine Locale hinzufügen

- **Eine Sprache, die schon eine Locale hat,** braucht keine Dateien. Fügen Sie sie unter Setup > Sprachen als
  Inhaltssprache hinzu ([Eine Sprache hinzufügen](anleitungen/inhaltssprachen.md#4-eine-sprache-hinzufügen)), oder
  wählen Sie sie als Oberflächensprache. Die Administration zeigt dann die eigene Übersetzung der Sprache, die
  Übersetzung, auf die sie zurückfällt, oder Englisch.
- **Eine Sprache oder ein Land ohne Locale** braucht eine Locale-Datei: [Abschnitt 7](#7-eine-locale-hinzufügen).
- **Eine neue oder bessere Übersetzung der Administration**:
  [Abschnitt 8](#8-eine-übersetzung-der-administrationsoberfläche-hinzufügen-oder-vervollständigen), mit den
  gemeinsamen Regeln in `share/translations/TRANSLATING.md` und der eigenen `GUIDE.md` der Sprache.
- **Ein Fallback** für eine Locale ohne eigene Übersetzung: `i18n.ini [TranslationSettings] FallbackLanguages[]`
  ([Abschnitt 13](#13-übersetzungs-fallbacks)).
- **Eine Sprache der Hilfe**: [Übersetzte Dokumentation](bc/6.0/uebersetzte-dokumentation.md).

## Einen Übersetzungsfehler melden

Alle Übersetzungen außer `eng-US` und `ger-DE` sind maschinell unterstützt entstanden. Ein falscher Begriff, eine
falsche Anrede oder ein holpriger Satz steckt deshalb wahrscheinlich irgendwo, und jede Meldung hilft. So melden Sie
einen Fehler:

1. Notieren Sie den Sprachcode (die Wahl der Oberflächensprache zeigt ihn, zum Beispiel `fre-FR`), die Seite (ihre
   Adresse ohne den Hostnamen) und den Text, wie er erscheint.
2. Schreiben Sie, wie der Text lauten sollte, und warum, wenn das nicht offensichtlich ist: ein Begriff aus dem
   Glossar, die Anrede oder ein Wort, das seinen Kontext braucht („Store“ als Reiter des Shops oder als Speichern).
3. Eröffnen Sie ein Issue unter
   [github.com/se7enxweb/exponential/issues](https://github.com/se7enxweb/exponential/issues) für den Kernel, seine
   Designs, ezoe, ezjscore und die Dokumentation. Ein Text einer anderen Extension gehört in das Repository dieser
   Extension.

Eine Korrektur ist als Pull Request willkommen. Ändern Sie den Katalog `share/translations/<code>/translation.ts`
und, bei einem Begriff, zuerst die `GUIDE.md` der Sprache
([Abschnitt 8](#8-eine-übersetzung-der-administrationsoberfläche-hinzufügen-oder-vervollständigen)). Wie Sie eine
ganze Übersetzung durchsehen, steht in
[Eine maschinell unterstützte Übersetzung gegenlesen](hilfe/fuersprecher/maschinell-unterstuetzte-uebersetzung-gegenlesen.md);
wie gute Meldungen allgemein aussehen, in [Fehler gut melden](hilfe/fuersprecher/fehler-gut-melden.md).

## 1. Locales, Übersetzungen, Inhaltssprachen und Siteaccesses

Vier Dinge werden leicht verwechselt:

| Begriff | Ort | Was er tut |
|---|---|---|
| **Locale** | `share/locale/<code>.ini`, gelesen von `eZLocale` | Namen der Sprache und des Landes, das HTTP-Sprach-Tag, die Schreibrichtung, Datums- und Zeitformate, Tages- und Monatsnamen, der erste Tag der Woche, Zahlen- und Währungsformate, übersetzte Ländernamen. |
| **Übersetzung der Administrationsoberfläche** | `share/translations/<code>/translation.ts` (und `translations/<code>/` einer Extension) | Die Texte der Oberfläche: die `|i18n()`-Texte der Templates und `ezpI18n::tr()` in PHP. |
| **Inhaltssprache** | Setup > Sprachen (`ezcontent_language`) | Eine Sprache, in der Inhalte geschrieben werden können. Sie ist ein Locale-Code, daher lässt sich nur eine Locale mit Datei hinzufügen. |
| **Siteaccess-Locale** | `site.ini [RegionalSettings] Locale` eines Siteaccess | Die Locale, mit der ein Siteaccess formatiert, und die Übersetzung, die seine Oberfläche verwendet. `SiteLanguageList[]` gibt an, welche Inhaltssprachen er zeigt. |

Eine Locale-Datei ist alles, was eine Inhaltssprache braucht. Das Hinzufügen einer Locale-Datei macht die Sprache nur
auswählbar: Es fügt keine Inhaltssprache hinzu, ändert keinen Siteaccess und belegt keinen der höchstens 62 Plätze für
Inhaltssprachen (30 auf 32-Bit-PHP; siehe [Abschnitt 11](#11-die-grenze-von-62-inhaltssprachen)).

Die Einzelheiten stehen an anderer Stelle und werden hier nicht wiederholt:

- [Inhaltssprachen](anleitungen/inhaltssprachen.md): Hinzufügen, Anzeigen, Übersetzen, Verschieben und Entfernen der
  Sprachen, in denen Inhalte geschrieben werden.
- [Übersetzungen und Sprachen](funktionen/6.0/uebersetzungen-und-sprachen.md): der Stand der Übersetzungen der
  Administrationsoberfläche, eigene Texte übersetzbar machen, die HTML-Sprach-Tags.
- [Die Wahl der Oberflächensprache](funktionen/6.0/ui-umschalter-sprachen.md): das Auswahlfeld der rechten Werkzeugleiste,
  seine Gruppen, seine A–Z-Liste und die angezeigte Abdeckung.
- [Die Fallback-Locale eines Inhaltsobjekts](bc/6.0/ersatz-locale-fuer-inhaltsobjekte.md).

## 2. Unterstützungsstufen

| Stufe | Was funktioniert | Locales |
|---|---|---|
| **Eigene Übersetzung** | Die Administration spricht die Sprache aus einem eigenen Katalog, der mindestens 99 % der aktuellen Texte übersetzt. Inhalte, Datumsangaben, Zahlen und Währung folgen der Locale. | Jeder Code in der Tabelle von [Abschnitt 4](#4-alle-übersetzungen-der-administrationsoberfläche). Alle außer `eng-US` und `ger-DE` sind maschinell unterstützt entstanden. |
| **Über einen Fallback** | Dasselbe, aus dem Katalog einer anderen Locale der Sprache oder einer eng verwandten. | Die Locales mit `→` in der Ländertabelle unter der Karte, etwa `fre-CA` → `fre-FR`, `ara-EG` → `ara-SA` und `nno-NO` → `nor-NO`. Die englischen Locales ohne eigenen Katalog (`eng-GB`, `eng-IN` ...) zeigen die Quelltexte, die englisch sind. |
| **Nur Inhalt** | Weder Übersetzung noch Fallback, die Oberfläche bleibt also englisch. Inhalte lassen sich in der Sprache schreiben, und ein Siteaccess mit der Locale formatiert Datumsangaben, Zahlen und Währung für sie. | Keine in einer Standardinstallation. Eine später hinzugefügte Locale bleibt auf dieser Stufe, bis sie eine Übersetzung oder einen Fallback bekommt. |

Ein Land ohne eigene Übersetzung spricht die Sprache, die `i18n.ini [TranslationSettings] FallbackLanguages[]`
nennt, auf derselben Stufe. Jedes deutsch-, französisch- und spanischsprachige Land fällt auf `ger-DE`, `fre-FR` oder
`esl-ES` zurück, Lateinamerika eingeschlossen (`esl-MX` nimmt `esl-ES` für die Extensions, für die es keinen eigenen
Katalog hat). Die arabischen Länder fallen auf `ara-SA` zurück, und die anderen Sprachen funktionieren genauso
(`por-MZ` auf `por-PT`, `ita-CH` auf `ita-IT`). Der Übersetzer schlägt den Fallback für den Kernel und für jede
Extension getrennt nach. `expUISwitchLanguageProviderTest` schlägt fehl, wenn ein Land einer übersetzten Sprache
weder eine Übersetzung noch einen Fallback hat. Die Spalte „Übersetzungsabdeckung“ in
[Abschnitt 3](#3-alle-locales) nennt die Locale, zu der ein Fallback führt. Ketten, der automatische Fallback
innerhalb derselben Sprache, Regeln von Extensions, `./console exp:i18n` und Setup > Oberflächensprachen erklärt
[Abschnitt 13](#13-übersetzungs-fallbacks).

## 3. Alle Locales

`share/locale` enthält eine Datei je Sprache und Land (die Anzahl steht in der Tabelle oben auf dieser Seite), die
Testsprache `qlo-ZZ` ([Abschnitt 12](#12-testsprachen-lorem-ipsum)) und einige Varianten. Eine Variante wie
`ger-DE@euro` überschreibt nur einige Schlüssel ihrer Basisdatei und wird in `site.ini` gewählt. Das Formular zum
Hinzufügen unter Setup > Sprachen bietet jede Datei außer den Varianten an, sortiert nach englischem Namen. Jeder
Eintrag zeigt seinen Eigennamen, sein Land (sofern der Name es nicht schon nennt) und seinen Code, und ein Suchfeld
passt auf jedes davon: `bengali`, `বাংলা`, `ben` und `bangladesh` finden alle `ben-BD`. Die Wahl der
Oberflächensprache bietet dieselben Locales in ihrer A–Z-Liste an, einem nativen Auswahlfeld mit Schnellsuche beim
Tippen (die Eingabe „Kis“ springt zu Kiswahili).

Die Spalten:

- **Richtung**: `RTL` für eine Sprache, die von rechts nach links geschrieben wird ([Abschnitt 9](#9-sprachen-die-von-rechts-nach-links-geschrieben-werden)).
- **Schrift**: die Schrift nach ISO 15924, in der die Namen der Datei geschrieben sind: die wahrscheinliche Schrift der
  Sprache laut ICU, geprüft an den Buchstaben des eigenen Eigennamens und der Monatsnamen der Datei (die Datei hat
  Vorrang: `ser-SR` ist Serbisch in lateinischer Schrift).
- **Quelle**: `CLDR 44` oder `CLDR 48` für eine Datei, die in 6.0.15 aus dieser Version von Unicode CLDR erzeugt
  wurde; `älter, neu erzeugt aus CLDR 44` (oder `48`) für eine ältere Datei, die fehlerhaft war und neu erzeugt wurde; `Namen von Hand, Konventionen aus CLDR 44`,
  wo CLDR keine Daten für die Sprache hat und die Namen von Hand geschrieben wurden; `von Hand erstellt` für eine
  vollständig von Hand geschriebene Datei; `älter, von Hand erstellt, korrigiert in 6.0.15` für eine ältere Datei mit
  einzelnen von Hand korrigierten Werten; `älter, von Hand erstellt` für die Dateien, die mit früheren Releases kamen,
  unverändert.
- **Übersetzungsabdeckung**: der Anteil der aktuellen Oberflächentexte, den die Übersetzung übersetzt, die die
  Locale zeigt, so wie ihn die Wahl der Oberflächensprache anzeigt (`expUISwitchLanguageCatalogue::coveragePercent()`,
  abgerundet; Englisch ist die Quellsprache und immer 100 %). „Fällt auf ... zurück“ nennt den Katalog einer anderen
  Locale; `—` steht für eine Locale ohne Übersetzung.

Die Tabelle wird aus den Locale-Dateien erzeugt; bearbeiten Sie sie nicht von Hand.

| Code | Englischer Name | Eigenname | Region | Richtung | Schrift | Quelle | Übersetzungsabdeckung |
|---|---|---|---|---|---|---|---:|
| `afr-ZA` | Afrikaans | Afrikaans | Afrika | LTR | Lateinisch (`Latn`) | CLDR 44 | 100 % |
| `aka-GH` | Akan | Akan | Afrika | LTR | Lateinisch (`Latn`) | CLDR 44 | 100 % |
| `amh-ET` | Amharic | አማርኛ | Afrika | LTR | Äthiopisch (`Ethi`) | CLDR 44 | 100 % |
| `ara-AE` | Arabic (United Arab Emirates) | العربية (الإمارات العربية المتحدة) | Naher Osten | RTL | Arabisch (`Arab`) | CLDR 44 | 100 % (fällt auf `ara-SA` zurück) |
| `ara-BH` | Arabic (Bahrain) | العربية (البحرين) | Naher Osten | RTL | Arabisch (`Arab`) | CLDR 48 | 100 % (fällt auf `ara-SA` zurück) |
| `ara-DJ` | Arabic (Djibouti) | العربية (جيبوتي) | Afrika | RTL | Arabisch (`Arab`) | CLDR 48 | 100 % (fällt auf `ara-SA` zurück) |
| `ara-DZ` | Arabic (Algeria) | العربية (الجزائر) | Afrika | RTL | Arabisch (`Arab`) | CLDR 44 | 100 % (fällt auf `ara-SA` zurück) |
| `ara-EG` | Arabic (Egypt) | العربية (مصر) | Naher Osten | RTL | Arabisch (`Arab`) | CLDR 44 | 100 % (fällt auf `ara-SA` zurück) |
| `ara-EH` | Arabic (Western Sahara) | العربية (الصحراء الغربية) | Afrika | RTL | Arabisch (`Arab`) | CLDR 44 | 100 % (fällt auf `ara-SA` zurück) |
| `ara-ER` | Arabic (Eritrea) | العربية (إريتريا) | Afrika | RTL | Arabisch (`Arab`) | CLDR 48 | 100 % (fällt auf `ara-SA` zurück) |
| `ara-IQ` | Arabic (Iraq) | العربية (العراق) | Naher Osten | RTL | Arabisch (`Arab`) | CLDR 44 | 100 % (fällt auf `ara-SA` zurück) |
| `ara-JO` | Arabic (Jordan) | العربية (الأردن) | Naher Osten | RTL | Arabisch (`Arab`) | CLDR 44 | 100 % (fällt auf `ara-SA` zurück) |
| `ara-KM` | Arabic (Comoros) | العربية (جزر القمر) | Afrika | RTL | Arabisch (`Arab`) | CLDR 48 | 100 % (fällt auf `ara-SA` zurück) |
| `ara-KW` | Arabic (Kuwait) | العربية (الكويت) | Naher Osten | RTL | Arabisch (`Arab`) | CLDR 44 | 100 % (fällt auf `ara-SA` zurück) |
| `ara-LB` | Arabic (Lebanon) | العربية (لبنان) | Naher Osten | RTL | Arabisch (`Arab`) | CLDR 44 | 100 % (fällt auf `ara-SA` zurück) |
| `ara-LY` | Arabic (Libya) | العربية (ليبيا) | Afrika | RTL | Arabisch (`Arab`) | CLDR 48 | 100 % (fällt auf `ara-SA` zurück) |
| `ara-MA` | Arabic (Morocco) | العربية (المغرب) | Afrika | RTL | Arabisch (`Arab`) | CLDR 44 | 100 % (fällt auf `ara-SA` zurück) |
| `ara-MR` | Arabic (Mauritania) | العربية (موريتانيا) | Afrika | RTL | Arabisch (`Arab`) | CLDR 48 | 100 % (fällt auf `ara-SA` zurück) |
| `ara-OM` | Arabic (Oman) | العربية (عُمان) | Naher Osten | RTL | Arabisch (`Arab`) | CLDR 48 | 100 % (fällt auf `ara-SA` zurück) |
| `ara-PS` | Arabic (Palestinian Territories) | العربية (الأراضي الفلسطينية) | Naher Osten | RTL | Arabisch (`Arab`) | CLDR 48 | 100 % (fällt auf `ara-SA` zurück) |
| `ara-QA` | Arabic (Qatar) | العربية (قطر) | Naher Osten | RTL | Arabisch (`Arab`) | CLDR 44 | 100 % (fällt auf `ara-SA` zurück) |
| `ara-SA` | Arabic (Saudi Arabia) | العربية (المملكة العربية السعودية) | Naher Osten | RTL | Arabisch (`Arab`) | älter, neu erzeugt aus CLDR 48 | 100 % |
| `ara-SD` | Arabic (Sudan) | العربية (السودان) | Afrika | RTL | Arabisch (`Arab`) | CLDR 48 | 100 % (fällt auf `ara-SA` zurück) |
| `ara-SO` | Arabic (Somalia) | العربية (الصومال) | Afrika | RTL | Arabisch (`Arab`) | CLDR 48 | 100 % (fällt auf `ara-SA` zurück) |
| `ara-SY` | Arabic (Syria) | العربية (سوريا) | Naher Osten | RTL | Arabisch (`Arab`) | CLDR 44 | 100 % (fällt auf `ara-SA` zurück) |
| `ara-TD` | Arabic (Chad) | العربية (تشاد) | Afrika | RTL | Arabisch (`Arab`) | CLDR 48 | 100 % (fällt auf `ara-SA` zurück) |
| `ara-TN` | Arabic (Tunisia) | العربية (تونس) | Afrika | RTL | Arabisch (`Arab`) | CLDR 44 | 100 % (fällt auf `ara-SA` zurück) |
| `ara-YE` | Arabic (Yemen) | العربية (اليمن) | Naher Osten | RTL | Arabisch (`Arab`) | CLDR 48 | 100 % (fällt auf `ara-SA` zurück) |
| `arc-IQ` | Imperial Aramaic | ארמית | Naher Osten | RTL | Hebräisch (`Hebr`) | Namen von Hand, Konventionen aus CLDR 44 | 100 % |
| `asm-IN` | Assamese | অসমীয়া | Südasien | LTR | Bengalisch (`Beng`) | CLDR 44 | 100 % |
| `ast-ES` | Asturian | Asturianu | Europa | LTR | Lateinisch (`Latn`) | CLDR 44 | 100 % |
| `aze-AZ` | Azerbaijani | Azərbaycan | Zentralasien und Kaukasus | LTR | Lateinisch (`Latn`) | CLDR 44 | 100 % |
| `bam-ML` | Bambara | Bamanakan | Afrika | LTR | Lateinisch (`Latn`) | CLDR 44 | 100 % |
| `bel-BY` | Belarusian | Беларуская | Europa | LTR | Kyrillisch (`Cyrl`) | CLDR 44 | 100 % |
| `ben-BD` | Bengali (Bangladesh) | বাংলা (বাংলাদেশ) | Südasien | LTR | Bengalisch (`Beng`) | CLDR 44 | 100 % |
| `ben-IN` | Bengali (India) | বাংলা (ভারত) | Südasien | LTR | Bengalisch (`Beng`) | CLDR 44 | 100 % (fällt auf `ben-BD` zurück) |
| `bho-IN` | Bhojpuri | भोजपुरी | Südasien | LTR | Devanagari (`Deva`) | CLDR 44 | 100 % |
| `bod-CN` | Tibetan | བོད་སྐད་ | Ostasien | LTR | Tibetisch (`Tibt`) | CLDR 44 | 100 % |
| `bos-BA` | Bosnian | Bosanski | Europa | LTR | Lateinisch (`Latn`) | CLDR 44 | 100 % |
| `bre-FR` | Breton | Brezhoneg | Europa | LTR | Lateinisch (`Latn`) | CLDR 44 | 100 % |
| `brx-IN` | Bodo | बर’ | Südasien | LTR | Devanagari (`Deva`) | CLDR 44 | 100 % |
| `bul-BG` | Bulgarian | Български | Europa | LTR | Kyrillisch (`Cyrl`) | CLDR 44 | 100 % |
| `cat-AD` | Catalan (Andorra) | Català (Andorra) | Europa | LTR | Lateinisch (`Latn`) | CLDR 44 | 100 % (fällt auf `cat-ES` zurück) |
| `cat-ES` | Catalan | Català | Europa | LTR | Lateinisch (`Latn`) | älter, von Hand erstellt | 100 % |
| `ceb-PH` | Cebuano | Cebuano | Südostasien | LTR | Lateinisch (`Latn`) | CLDR 44 | 100 % |
| `che-RU` | Chechen | Нохчийн | Europa | LTR | Kyrillisch (`Cyrl`) | CLDR 44 | 100 % |
| `chi-CN` | Simplified Chinese | 简体中文 | Ostasien | LTR | Han (vereinfacht) (`Hans`) | älter, von Hand erstellt, korrigiert in 6.0.15 | 100 % |
| `chi-HK` | Traditional Chinese (HongKong) | 繁體中文 (香港) | Ostasien | LTR | Han (traditionell) (`Hant`) | älter, von Hand erstellt, korrigiert in 6.0.15 | 100 % (fällt auf `chi-TW` zurück) |
| `chi-MO` | Chinese (Macao SAR China) | 中文 (中國澳門特別行政區) | Ostasien | LTR | Han (traditionell) (`Hant`) | CLDR 44 | 100 % (fällt auf `chi-TW` zurück) |
| `chi-SG` | Simplified Chinese (Singapore) | 简体中文 (新加坡) | Südostasien | LTR | Han (vereinfacht) (`Hans`) | CLDR 44 | 100 % (fällt auf `chi-CN` zurück) |
| `chi-TW` | Traditional Chinese (Taiwan) | 繁體中文 (臺灣) | Ostasien | LTR | Han (traditionell) (`Hant`) | älter, von Hand erstellt | 100 % |
| `ckb-IQ` | Central Kurdish | کوردیی ناوەندی | Naher Osten | RTL | Arabisch (`Arab`) | CLDR 44 | 100 % |
| `cro-HR` | Croatian | Hrvatski | Europa | LTR | Lateinisch (`Latn`) | älter, von Hand erstellt | 100 % |
| `cym-GB` | Welsh | Cymraeg | Europa | LTR | Lateinisch (`Latn`) | CLDR 44 | 100 % |
| `cze-CZ` | Czech | Čeština | Europa | LTR | Lateinisch (`Latn`) | älter, von Hand erstellt | 100 % |
| `dan-DK` | Danish | Dansk | Europa | LTR | Lateinisch (`Latn`) | älter, von Hand erstellt | 100 % |
| `dan-FO` | Danish (Faroe Islands) | Dansk (Færøerne) | Europa | LTR | Lateinisch (`Latn`) | CLDR 44 | 100 % (fällt auf `dan-DK` zurück) |
| `div-MV` | Dhivehi | ދިވެހި | Südasien | RTL | Thaana (`Thaa`) | Namen von Hand, Konventionen aus CLDR 44 | 100 % |
| `doi-IN` | Dogri | डोगरी | Südasien | LTR | Devanagari (`Deva`) | CLDR 44 | 100 % |
| `dsb-DE` | Lower Sorbian | Dolnoserbšćina | Europa | LTR | Lateinisch (`Latn`) | CLDR 44 | 100 % |
| `dut-AW` | Dutch (Aruba) | Nederlands (Aruba) | Amerika | LTR | Lateinisch (`Latn`) | CLDR 44 | 100 % (fällt auf `dut-NL` zurück) |
| `dut-BE` | Dutch (Belgium) | Nederlands (België) | Europa | LTR | Lateinisch (`Latn`) | CLDR 44 | 100 % (fällt auf `dut-NL` zurück) |
| `dut-BQ` | Dutch (Caribbean Netherlands) | Nederlands (Caribisch Nederland) | Amerika | LTR | Lateinisch (`Latn`) | CLDR 44 | 100 % (fällt auf `dut-NL` zurück) |
| `dut-CW` | Dutch (Curaçao) | Nederlands (Curaçao) | Amerika | LTR | Lateinisch (`Latn`) | CLDR 44 | 100 % (fällt auf `dut-NL` zurück) |
| `dut-NL` | Dutch (Netherlands) | Nederlands (Nederland) | Europa | LTR | Lateinisch (`Latn`) | älter, von Hand erstellt | 100 % |
| `dut-SR` | Dutch (Suriname) | Nederlands (Suriname) | Amerika | LTR | Lateinisch (`Latn`) | CLDR 44 | 100 % (fällt auf `dut-NL` zurück) |
| `dut-SX` | Dutch (Sint Maarten) | Nederlands (Sint-Maarten) | Amerika | LTR | Lateinisch (`Latn`) | CLDR 44 | 100 % (fällt auf `dut-NL` zurück) |
| `dzo-BT` | Dzongkha | རྫོང་ཁ | Südasien | LTR | Tibetisch (`Tibt`) | CLDR 44 | 100 % |
| `ell-CY` | Greek (Cyprus) | Ελληνικά (Κύπρος) | Europa | LTR | Griechisch (`Grek`) | CLDR 44 | 100 % (fällt auf `ell-GR` zurück) |
| `ell-GR` | Greek (Hellenic) | Ελληνικά | Europa | LTR | Griechisch (`Grek`) | älter, von Hand erstellt, korrigiert in 6.0.15 | 100 % |
| `eng-AG` | English (Antigua & Barbuda) | English (Antigua & Barbuda) | Amerika | LTR | Lateinisch (`Latn`) | CLDR 44 | 100 % |
| `eng-AI` | English (Anguilla) | English (Anguilla) | Amerika | LTR | Lateinisch (`Latn`) | CLDR 44 | 100 % |
| `eng-AS` | English (American Samoa) | English (American Samoa) | Ozeanien | LTR | Lateinisch (`Latn`) | CLDR 44 | 100 % |
| `eng-AU` | English (Australia) | English (Australia) | Ozeanien | LTR | Lateinisch (`Latn`) | älter, von Hand erstellt | 100 % |
| `eng-BB` | English (Barbados) | English (Barbados) | Amerika | LTR | Lateinisch (`Latn`) | CLDR 44 | 100 % |
| `eng-BM` | English (Bermuda) | English (Bermuda) | Amerika | LTR | Lateinisch (`Latn`) | CLDR 44 | 100 % |
| `eng-BS` | English (Bahamas) | English (Bahamas) | Amerika | LTR | Lateinisch (`Latn`) | CLDR 44 | 100 % |
| `eng-BW` | English (Botswana) | English (Botswana) | Afrika | LTR | Lateinisch (`Latn`) | CLDR 44 | 100 % |
| `eng-BZ` | English (Belize) | English (Belize) | Amerika | LTR | Lateinisch (`Latn`) | CLDR 44 | 100 % |
| `eng-CA` | English (Canada) | English (Canada) | Amerika | LTR | Lateinisch (`Latn`) | älter, von Hand erstellt | 100 % |
| `eng-CC` | English (Cocos (Keeling) Islands) | English (Cocos (Keeling) Islands) | Südostasien | LTR | Lateinisch (`Latn`) | CLDR 44 | 100 % (fällt auf `eng-AU` zurück) |
| `eng-CK` | English (Cook Islands) | English (Cook Islands) | Ozeanien | LTR | Lateinisch (`Latn`) | CLDR 44 | 100 % |
| `eng-CX` | English (Christmas Island) | English (Christmas Island) | Südostasien | LTR | Lateinisch (`Latn`) | CLDR 44 | 100 % (fällt auf `eng-AU` zurück) |
| `eng-DM` | English (Dominica) | English (Dominica) | Amerika | LTR | Lateinisch (`Latn`) | CLDR 44 | 100 % |
| `eng-FJ` | English (Fiji) | English (Fiji) | Ozeanien | LTR | Lateinisch (`Latn`) | CLDR 44 | 100 % |
| `eng-FK` | English (Falkland Islands) | English (Falkland Islands) | Amerika | LTR | Lateinisch (`Latn`) | CLDR 44 | 100 % |
| `eng-FM` | English (Micronesia) | English (Micronesia) | Ozeanien | LTR | Lateinisch (`Latn`) | CLDR 44 | 100 % |
| `eng-GB` | English (United Kingdom) | English (United Kingdom) | Europa | LTR | Lateinisch (`Latn`) | älter, von Hand erstellt | 100 % |
| `eng-GB@euro` | Variant of `eng-GB`: Euro support |  | Europa | LTR | Lateinisch (`Latn`) | ältere Variante, von Hand erstellt | 100 % |
| `eng-GD` | English (Grenada) | English (Grenada) | Amerika | LTR | Lateinisch (`Latn`) | CLDR 44 | 100 % |
| `eng-GG` | English (Guernsey) | English (Guernsey) | Europa | LTR | Lateinisch (`Latn`) | CLDR 44 | 100 % |
| `eng-GH` | English (Ghana) | English (Ghana) | Afrika | LTR | Lateinisch (`Latn`) | CLDR 44 | 100 % |
| `eng-GI` | English (Gibraltar) | English (Gibraltar) | Europa | LTR | Lateinisch (`Latn`) | CLDR 44 | 100 % |
| `eng-GM` | English (Gambia) | English (Gambia) | Afrika | LTR | Lateinisch (`Latn`) | CLDR 44 | 100 % |
| `eng-GS` | English (South Georgia & South Sandwich Islands) | English (South Georgia & South Sandwich Islands) | Amerika | LTR | Lateinisch (`Latn`) | CLDR 44 | 100 % |
| `eng-GU` | English (Guam) | English (Guam) | Ozeanien | LTR | Lateinisch (`Latn`) | CLDR 44 | 100 % |
| `eng-GY` | English (Guyana) | English (Guyana) | Amerika | LTR | Lateinisch (`Latn`) | CLDR 44 | 100 % |
| `eng-IE` | English (Ireland) | English (Ireland) | Europa | LTR | Lateinisch (`Latn`) | CLDR 44 | 100 % |
| `eng-IM` | English (Isle of Man) | English (Isle of Man) | Europa | LTR | Lateinisch (`Latn`) | CLDR 44 | 100 % |
| `eng-IN` | English (India) | English (India) | Südasien | LTR | Lateinisch (`Latn`) | CLDR 44 | 100 % |
| `eng-IO` | English (British Indian Ocean Territory) | English (British Indian Ocean Territory) | Südasien | LTR | Lateinisch (`Latn`) | CLDR 44 | 100 % |
| `eng-JE` | English (Jersey) | English (Jersey) | Europa | LTR | Lateinisch (`Latn`) | CLDR 44 | 100 % |
| `eng-JM` | English (Jamaica) | English (Jamaica) | Amerika | LTR | Lateinisch (`Latn`) | CLDR 44 | 100 % |
| `eng-KE` | English (Kenya) | English (Kenya) | Afrika | LTR | Lateinisch (`Latn`) | CLDR 44 | 100 % |
| `eng-KI` | English (Kiribati) | English (Kiribati) | Ozeanien | LTR | Lateinisch (`Latn`) | CLDR 44 | 100 % |
| `eng-KN` | English (St. Kitts & Nevis) | English (St Kitts & Nevis) | Amerika | LTR | Lateinisch (`Latn`) | CLDR 44 | 100 % |
| `eng-KY` | English (Cayman Islands) | English (Cayman Islands) | Amerika | LTR | Lateinisch (`Latn`) | CLDR 44 | 100 % |
| `eng-LC` | English (St. Lucia) | English (St Lucia) | Amerika | LTR | Lateinisch (`Latn`) | CLDR 44 | 100 % |
| `eng-LR` | English (Liberia) | English (Liberia) | Afrika | LTR | Lateinisch (`Latn`) | CLDR 44 | 100 % |
| `eng-LS` | English (Lesotho) | English (Lesotho) | Afrika | LTR | Lateinisch (`Latn`) | CLDR 44 | 100 % |
| `eng-MH` | English (Marshall Islands) | English (Marshall Islands) | Ozeanien | LTR | Lateinisch (`Latn`) | CLDR 44 | 100 % |
| `eng-MP` | English (Northern Mariana Islands) | English (Northern Mariana Islands) | Ozeanien | LTR | Lateinisch (`Latn`) | CLDR 44 | 100 % |
| `eng-MS` | English (Montserrat) | English (Montserrat) | Amerika | LTR | Lateinisch (`Latn`) | CLDR 44 | 100 % |
| `eng-MW` | English (Malawi) | English (Malawi) | Afrika | LTR | Lateinisch (`Latn`) | CLDR 44 | 100 % |
| `eng-NA` | English (Namibia) | English (Namibia) | Afrika | LTR | Lateinisch (`Latn`) | CLDR 44 | 100 % |
| `eng-NF` | English (Norfolk Island) | English (Norfolk Island) | Ozeanien | LTR | Lateinisch (`Latn`) | CLDR 44 | 100 % (fällt auf `eng-AU` zurück) |
| `eng-NG` | English (Nigeria) | English (Nigeria) | Afrika | LTR | Lateinisch (`Latn`) | CLDR 44 | 100 % |
| `eng-NR` | English (Nauru) | English (Nauru) | Ozeanien | LTR | Lateinisch (`Latn`) | CLDR 44 | 100 % |
| `eng-NU` | English (Niue) | English (Niue) | Ozeanien | LTR | Lateinisch (`Latn`) | CLDR 44 | 100 % |
| `eng-NZ` | English (New Zealand) | English (New Zealand) | Ozeanien | LTR | Lateinisch (`Latn`) | älter, von Hand erstellt | 100 % (fällt auf `eng-AU` zurück) |
| `eng-PG` | English (Papua New Guinea) | English (Papua New Guinea) | Ozeanien | LTR | Lateinisch (`Latn`) | CLDR 44 | 100 % |
| `eng-PH` | English (Philippines) | English (Philippines) | Südostasien | LTR | Lateinisch (`Latn`) | CLDR 44 | 100 % |
| `eng-PK` | English (Pakistan) | English (Pakistan) | Südasien | LTR | Lateinisch (`Latn`) | CLDR 44 | 100 % |
| `eng-PN` | English (Pitcairn Islands) | English (Pitcairn Islands) | Ozeanien | LTR | Lateinisch (`Latn`) | CLDR 44 | 100 % |
| `eng-PW` | English (Palau) | English (Palau) | Ozeanien | LTR | Lateinisch (`Latn`) | CLDR 44 | 100 % |
| `eng-SB` | English (Solomon Islands) | English (Solomon Islands) | Ozeanien | LTR | Lateinisch (`Latn`) | CLDR 44 | 100 % |
| `eng-SG` | English (Singapore) | English (Singapore) | Südostasien | LTR | Lateinisch (`Latn`) | CLDR 44 | 100 % |
| `eng-SH` | English (St. Helena) | English (St Helena) | Afrika | LTR | Lateinisch (`Latn`) | CLDR 44 | 100 % |
| `eng-SL` | English (Sierra Leone) | English (Sierra Leone) | Afrika | LTR | Lateinisch (`Latn`) | CLDR 44 | 100 % |
| `eng-SS` | English (South Sudan) | English (South Sudan) | Afrika | LTR | Lateinisch (`Latn`) | CLDR 44 | 100 % |
| `eng-SZ` | English (Eswatini) | English (Eswatini) | Afrika | LTR | Lateinisch (`Latn`) | CLDR 44 | 100 % |
| `eng-TC` | English (Turks & Caicos Islands) | English (Turks & Caicos Islands) | Amerika | LTR | Lateinisch (`Latn`) | CLDR 44 | 100 % |
| `eng-TK` | English (Tokelau) | English (Tokelau) | Ozeanien | LTR | Lateinisch (`Latn`) | CLDR 44 | 100 % |
| `eng-TO` | English (Tonga) | English (Tonga) | Ozeanien | LTR | Lateinisch (`Latn`) | CLDR 44 | 100 % |
| `eng-TT` | English (Trinidad & Tobago) | English (Trinidad & Tobago) | Amerika | LTR | Lateinisch (`Latn`) | CLDR 44 | 100 % |
| `eng-TV` | English (Tuvalu) | English (Tuvalu) | Ozeanien | LTR | Lateinisch (`Latn`) | CLDR 44 | 100 % |
| `eng-UG` | English (Uganda) | English (Uganda) | Afrika | LTR | Lateinisch (`Latn`) | CLDR 44 | 100 % |
| `eng-UM` | English (U.S. Outlying Islands) | English (U.S. Outlying Islands) | Ozeanien | LTR | Lateinisch (`Latn`) | CLDR 44 | 100 % |
| `eng-US` | English (United States) | English (United States) | Amerika | LTR | Lateinisch (`Latn`) | älter, von Hand erstellt | 100 % |
| `eng-VC` | English (St. Vincent & Grenadines) | English (St Vincent & the Grenadines) | Amerika | LTR | Lateinisch (`Latn`) | CLDR 44 | 100 % |
| `eng-VG` | English (British Virgin Islands) | English (British Virgin Islands) | Amerika | LTR | Lateinisch (`Latn`) | CLDR 44 | 100 % |
| `eng-VI` | English (U.S. Virgin Islands) | English (U.S. Virgin Islands) | Amerika | LTR | Lateinisch (`Latn`) | CLDR 44 | 100 % |
| `eng-WS` | English (Samoa) | English (Samoa) | Ozeanien | LTR | Lateinisch (`Latn`) | CLDR 44 | 100 % |
| `eng-ZA` | English (South Africa) | English (South Africa) | Afrika | LTR | Lateinisch (`Latn`) | CLDR 44 | 100 % |
| `eng-ZM` | English (Zambia) | English (Zambia) | Afrika | LTR | Lateinisch (`Latn`) | CLDR 44 | 100 % |
| `eng-ZW` | English (Zimbabwe) | English (Zimbabwe) | Afrika | LTR | Lateinisch (`Latn`) | CLDR 44 | 100 % |
| `epo-EO` | Esperanto | Esperanto | Weltweit | LTR | Lateinisch (`Latn`) | älter, von Hand erstellt | 100 % |
| `esl-AR` | Spanish (Argentina) | Español (Argentina) | Amerika | LTR | Lateinisch (`Latn`) | CLDR 44 | 100 % (fällt auf `esl-ES` zurück) |
| `esl-BO` | Spanish (Bolivia) | Español (Bolivia) | Amerika | LTR | Lateinisch (`Latn`) | CLDR 44 | 100 % (fällt auf `esl-ES` zurück) |
| `esl-CL` | Spanish (Chile) | Español (Chile) | Amerika | LTR | Lateinisch (`Latn`) | CLDR 44 | 100 % (fällt auf `esl-ES` zurück) |
| `esl-CO` | Spanish (Colombia) | Español (Colombia) | Amerika | LTR | Lateinisch (`Latn`) | CLDR 44 | 100 % (fällt auf `esl-ES` zurück) |
| `esl-CR` | Spanish (Costa Rica) | Español (Costa Rica) | Amerika | LTR | Lateinisch (`Latn`) | CLDR 48 | 100 % (fällt auf `esl-ES` zurück) |
| `esl-CU` | Spanish (Cuba) | Español (Cuba) | Amerika | LTR | Lateinisch (`Latn`) | CLDR 44 | 100 % (fällt auf `esl-ES` zurück) |
| `esl-DO` | Spanish (Dominican Republic) | Español (República Dominicana) | Amerika | LTR | Lateinisch (`Latn`) | CLDR 44 | 100 % (fällt auf `esl-ES` zurück) |
| `esl-EC` | Spanish (Ecuador) | Español (Ecuador) | Amerika | LTR | Lateinisch (`Latn`) | CLDR 44 | 100 % (fällt auf `esl-ES` zurück) |
| `esl-ES` | Spanish (Spain) | Español (España) | Europa | LTR | Lateinisch (`Latn`) | älter, von Hand erstellt | 100 % |
| `esl-GQ` | Spanish (Equatorial Guinea) | Español (Guinea Ecuatorial) | Afrika | LTR | Lateinisch (`Latn`) | CLDR 48 | 100 % (fällt auf `esl-ES` zurück) |
| `esl-GT` | Spanish (Guatemala) | Español (Guatemala) | Amerika | LTR | Lateinisch (`Latn`) | CLDR 44 | 100 % (fällt auf `esl-ES` zurück) |
| `esl-HN` | Spanish (Honduras) | Español (Honduras) | Amerika | LTR | Lateinisch (`Latn`) | CLDR 48 | 100 % (fällt auf `esl-ES` zurück) |
| `esl-MX` | Spanish (Mexico) | Español (México) | Amerika | LTR | Lateinisch (`Latn`) | älter, von Hand erstellt | 100 % |
| `esl-NI` | Spanish (Nicaragua) | Español (Nicaragua) | Amerika | LTR | Lateinisch (`Latn`) | CLDR 48 | 100 % (fällt auf `esl-ES` zurück) |
| `esl-PA` | Spanish (Panama) | Español (Panamá) | Amerika | LTR | Lateinisch (`Latn`) | CLDR 48 | 100 % (fällt auf `esl-ES` zurück) |
| `esl-PE` | Spanish (Peru) | Español (Perú) | Amerika | LTR | Lateinisch (`Latn`) | CLDR 44 | 100 % (fällt auf `esl-ES` zurück) |
| `esl-PR` | Spanish (Puerto Rico) | Español (Puerto Rico) | Amerika | LTR | Lateinisch (`Latn`) | CLDR 48 | 100 % (fällt auf `esl-ES` zurück) |
| `esl-PY` | Spanish (Paraguay) | Español (Paraguay) | Amerika | LTR | Lateinisch (`Latn`) | CLDR 48 | 100 % (fällt auf `esl-ES` zurück) |
| `esl-SV` | Spanish (El Salvador) | Español (El Salvador) | Amerika | LTR | Lateinisch (`Latn`) | CLDR 48 | 100 % (fällt auf `esl-ES` zurück) |
| `esl-US` | Spanish (United States) | Español (Estados Unidos) | Amerika | LTR | Lateinisch (`Latn`) | CLDR 44 | 100 % (fällt auf `esl-ES` zurück) |
| `esl-UY` | Spanish (Uruguay) | Español (Uruguay) | Amerika | LTR | Lateinisch (`Latn`) | CLDR 44 | 100 % (fällt auf `esl-ES` zurück) |
| `esl-VE` | Spanish (Venezuela) | Español (Venezuela) | Amerika | LTR | Lateinisch (`Latn`) | CLDR 44 | 100 % (fällt auf `esl-ES` zurück) |
| `est-EE` | Estonian | Eesti | Europa | LTR | Lateinisch (`Latn`) | CLDR 44 | 100 % |
| `eus-ES` | Basque | Euskara | Europa | LTR | Lateinisch (`Latn`) | CLDR 44 | 100 % |
| `ewe-GH` | Ewe | Eʋegbe | Afrika | LTR | Lateinisch (`Latn`) | CLDR 44 | 100 % |
| `fao-FO` | Faroese | Føroyskt | Europa | LTR | Lateinisch (`Latn`) | CLDR 44 | 100 % |
| `fas-AF` | Dari (Afghanistan) | دری (افغانستان) | Zentralasien und Kaukasus | RTL | Arabisch (`Arab`) | CLDR 44 | 100 % |
| `fas-IR` | Persian | فارسی | Naher Osten | RTL | Arabisch (`Arab`) | CLDR 44 | 100 % |
| `fil-PH` | Filipino | Filipino | Südostasien | LTR | Lateinisch (`Latn`) | CLDR 44 | 100 % |
| `fin-FI` | Finnish | Suomi | Europa | LTR | Lateinisch (`Latn`) | älter, von Hand erstellt | 100 % |
| `fre-BE` | French (Belgium) | Français (Belgique) | Europa | LTR | Lateinisch (`Latn`) | älter, von Hand erstellt | 100 % (fällt auf `fre-FR` zurück) |
| `fre-BF` | French (Burkina Faso) | Français (Burkina Faso) | Afrika | LTR | Lateinisch (`Latn`) | CLDR 48 | 100 % (fällt auf `fre-FR` zurück) |
| `fre-BI` | French (Burundi) | Français (Burundi) | Afrika | LTR | Lateinisch (`Latn`) | CLDR 48 | 100 % (fällt auf `fre-FR` zurück) |
| `fre-BJ` | French (Benin) | Français (Bénin) | Afrika | LTR | Lateinisch (`Latn`) | CLDR 48 | 100 % (fällt auf `fre-FR` zurück) |
| `fre-BL` | French (St. Barthélemy) | Français (Saint-Barthélemy) | Amerika | LTR | Lateinisch (`Latn`) | CLDR 44 | 100 % (fällt auf `fre-FR` zurück) |
| `fre-CA` | French (Canada) | Français (Canada) | Amerika | LTR | Lateinisch (`Latn`) | älter, von Hand erstellt | 100 % (fällt auf `fre-FR` zurück) |
| `fre-CD` | French (Congo - Kinshasa) | Français (Congo-Kinshasa) | Afrika | LTR | Lateinisch (`Latn`) | CLDR 44 | 100 % (fällt auf `fre-FR` zurück) |
| `fre-CF` | French (Central African Republic) | Français (République centrafricaine) | Afrika | LTR | Lateinisch (`Latn`) | CLDR 48 | 100 % (fällt auf `fre-FR` zurück) |
| `fre-CG` | French (Congo - Brazzaville) | Français (Congo-Brazzaville) | Afrika | LTR | Lateinisch (`Latn`) | CLDR 48 | 100 % (fällt auf `fre-FR` zurück) |
| `fre-CH` | French (Switzerland) | Français (Suisse) | Europa | LTR | Lateinisch (`Latn`) | CLDR 44 | 100 % (fällt auf `fre-FR` zurück) |
| `fre-CI` | French (Côte d’Ivoire) | Français (Côte d’Ivoire) | Afrika | LTR | Lateinisch (`Latn`) | CLDR 44 | 100 % (fällt auf `fre-FR` zurück) |
| `fre-CM` | French (Cameroon) | Français (Cameroun) | Afrika | LTR | Lateinisch (`Latn`) | CLDR 44 | 100 % (fällt auf `fre-FR` zurück) |
| `fre-DJ` | French (Djibouti) | Français (Djibouti) | Afrika | LTR | Lateinisch (`Latn`) | CLDR 48 | 100 % (fällt auf `fre-FR` zurück) |
| `fre-DZ` | French (Algeria) | Français (Algérie) | Afrika | LTR | Lateinisch (`Latn`) | CLDR 48 | 100 % (fällt auf `fre-FR` zurück) |
| `fre-FR` | French (France) | Français (France) | Europa | LTR | Lateinisch (`Latn`) | älter, von Hand erstellt | 100 % |
| `fre-GA` | French (Gabon) | Français (Gabon) | Afrika | LTR | Lateinisch (`Latn`) | CLDR 48 | 100 % (fällt auf `fre-FR` zurück) |
| `fre-GF` | French (French Guiana) | Français (Guyane française) | Amerika | LTR | Lateinisch (`Latn`) | CLDR 44 | 100 % (fällt auf `fre-FR` zurück) |
| `fre-GN` | French (Guinea) | Français (Guinée) | Afrika | LTR | Lateinisch (`Latn`) | CLDR 48 | 100 % (fällt auf `fre-FR` zurück) |
| `fre-GP` | French (Guadeloupe) | Français (Guadeloupe) | Amerika | LTR | Lateinisch (`Latn`) | CLDR 44 | 100 % (fällt auf `fre-FR` zurück) |
| `fre-GQ` | French (Equatorial Guinea) | Français (Guinée équatoriale) | Afrika | LTR | Lateinisch (`Latn`) | CLDR 48 | 100 % (fällt auf `fre-FR` zurück) |
| `fre-HT` | French (Haiti) | Français (Haïti) | Amerika | LTR | Lateinisch (`Latn`) | CLDR 48 | 100 % (fällt auf `fre-FR` zurück) |
| `fre-KM` | French (Comoros) | Français (Comores) | Afrika | LTR | Lateinisch (`Latn`) | CLDR 48 | 100 % (fällt auf `fre-FR` zurück) |
| `fre-LU` | French (Luxembourg) | Français (Luxembourg) | Europa | LTR | Lateinisch (`Latn`) | CLDR 48 | 100 % (fällt auf `fre-FR` zurück) |
| `fre-MA` | French (Morocco) | Français (Maroc) | Afrika | LTR | Lateinisch (`Latn`) | CLDR 44 | 100 % (fällt auf `fre-FR` zurück) |
| `fre-MC` | French (Monaco) | Français (Monaco) | Europa | LTR | Lateinisch (`Latn`) | CLDR 48 | 100 % (fällt auf `fre-FR` zurück) |
| `fre-MF` | French (St. Martin) | Français (Saint-Martin) | Amerika | LTR | Lateinisch (`Latn`) | CLDR 44 | 100 % (fällt auf `fre-FR` zurück) |
| `fre-MG` | French (Madagascar) | Français (Madagascar) | Afrika | LTR | Lateinisch (`Latn`) | CLDR 48 | 100 % (fällt auf `fre-FR` zurück) |
| `fre-ML` | French (Mali) | Français (Mali) | Afrika | LTR | Lateinisch (`Latn`) | CLDR 48 | 100 % (fällt auf `fre-FR` zurück) |
| `fre-MQ` | French (Martinique) | Français (Martinique) | Amerika | LTR | Lateinisch (`Latn`) | CLDR 44 | 100 % (fällt auf `fre-FR` zurück) |
| `fre-MR` | French (Mauritania) | Français (Mauritanie) | Afrika | LTR | Lateinisch (`Latn`) | CLDR 48 | 100 % (fällt auf `fre-FR` zurück) |
| `fre-MU` | French (Mauritius) | Français (Maurice) | Afrika | LTR | Lateinisch (`Latn`) | CLDR 48 | 100 % (fällt auf `fre-FR` zurück) |
| `fre-NC` | French (New Caledonia) | Français (Nouvelle-Calédonie) | Ozeanien | LTR | Lateinisch (`Latn`) | CLDR 44 | 100 % (fällt auf `fre-FR` zurück) |
| `fre-NE` | French (Niger) | Français (Niger) | Afrika | LTR | Lateinisch (`Latn`) | CLDR 48 | 100 % (fällt auf `fre-FR` zurück) |
| `fre-PF` | French (French Polynesia) | Français (Polynésie française) | Ozeanien | LTR | Lateinisch (`Latn`) | CLDR 44 | 100 % (fällt auf `fre-FR` zurück) |
| `fre-PM` | French (St. Pierre & Miquelon) | Français (Saint-Pierre-et-Miquelon) | Amerika | LTR | Lateinisch (`Latn`) | CLDR 44 | 100 % (fällt auf `fre-FR` zurück) |
| `fre-RE` | French (Réunion) | Français (La Réunion) | Afrika | LTR | Lateinisch (`Latn`) | CLDR 44 | 100 % (fällt auf `fre-FR` zurück) |
| `fre-RW` | French (Rwanda) | Français (Rwanda) | Afrika | LTR | Lateinisch (`Latn`) | CLDR 48 | 100 % (fällt auf `fre-FR` zurück) |
| `fre-SC` | French (Seychelles) | Français (Seychelles) | Afrika | LTR | Lateinisch (`Latn`) | CLDR 48 | 100 % (fällt auf `fre-FR` zurück) |
| `fre-SN` | French (Senegal) | Français (Sénégal) | Afrika | LTR | Lateinisch (`Latn`) | CLDR 44 | 100 % (fällt auf `fre-FR` zurück) |
| `fre-TD` | French (Chad) | Français (Tchad) | Afrika | LTR | Lateinisch (`Latn`) | CLDR 48 | 100 % (fällt auf `fre-FR` zurück) |
| `fre-TF` | French (French Southern Territories) | Français (Terres australes françaises) | Afrika | LTR | Lateinisch (`Latn`) | CLDR 44 | 100 % (fällt auf `fre-FR` zurück) |
| `fre-TG` | French (Togo) | Français (Togo) | Afrika | LTR | Lateinisch (`Latn`) | CLDR 48 | 100 % (fällt auf `fre-FR` zurück) |
| `fre-TN` | French (Tunisia) | Français (Tunisie) | Afrika | LTR | Lateinisch (`Latn`) | CLDR 48 | 100 % (fällt auf `fre-FR` zurück) |
| `fre-VU` | French (Vanuatu) | Français (Vanuatu) | Ozeanien | LTR | Lateinisch (`Latn`) | CLDR 48 | 100 % (fällt auf `fre-FR` zurück) |
| `fre-WF` | French (Wallis & Futuna) | Français (Wallis-et-Futuna) | Ozeanien | LTR | Lateinisch (`Latn`) | CLDR 44 | 100 % (fällt auf `fre-FR` zurück) |
| `fre-YT` | French (Mayotte) | Français (Mayotte) | Afrika | LTR | Lateinisch (`Latn`) | CLDR 44 | 100 % (fällt auf `fre-FR` zurück) |
| `fry-NL` | Western Frisian | Frysk | Europa | LTR | Lateinisch (`Latn`) | CLDR 44 | 100 % |
| `ful-SN` | Fula | Pulaar | Afrika | LTR | Lateinisch (`Latn`) | CLDR 44 | 100 % |
| `ger-AT` | German (Austria) | Deutsch (Österreich) | Europa | LTR | Lateinisch (`Latn`) | CLDR 44 | 100 % (fällt auf `ger-DE` zurück) |
| `ger-BE` | German (Belgium) | Deutsch (Belgien) | Europa | LTR | Lateinisch (`Latn`) | CLDR 48 | 100 % (fällt auf `ger-DE` zurück) |
| `ger-CH` | German (Switzerland) | Deutsch (Schweiz) | Europa | LTR | Lateinisch (`Latn`) | CLDR 44 | 100 % (fällt auf `ger-DE` zurück) |
| `ger-DE` | German (Germany) | Deutsch (Deutschland) | Europa | LTR | Lateinisch (`Latn`) | älter, von Hand erstellt | 100 % |
| `ger-DE@euro` | Variant of `ger-DE`: Euro support |  | Europa | LTR | Lateinisch (`Latn`) | ältere Variante, von Hand erstellt | 100 % (fällt auf `ger-DE` zurück) |
| `ger-LI` | German (Liechtenstein) | Deutsch (Liechtenstein) | Europa | LTR | Lateinisch (`Latn`) | CLDR 48 | 100 % (fällt auf `ger-DE` zurück) |
| `ger-LU` | German (Luxembourg) | Deutsch (Luxemburg) | Europa | LTR | Lateinisch (`Latn`) | CLDR 48 | 100 % (fällt auf `ger-DE` zurück) |
| `gla-GB` | Scottish Gaelic | Gàidhlig | Europa | LTR | Lateinisch (`Latn`) | CLDR 44 | 100 % |
| `gle-IE` | Irish | Gaeilge | Europa | LTR | Lateinisch (`Latn`) | CLDR 44 | 100 % |
| `glg-ES` | Galician | Galego | Europa | LTR | Lateinisch (`Latn`) | CLDR 44 | 100 % |
| `guj-IN` | Gujarati | ગુજરાતી | Südasien | LTR | Gujarati (`Gujr`) | CLDR 44 | 100 % |
| `hau-NG` | Hausa | Hausa | Afrika | LTR | Lateinisch (`Latn`) | CLDR 44 | 100 % |
| `haw-US` | Hawaiian | ʻŌlelo Hawaiʻi | Ozeanien | LTR | Lateinisch (`Latn`) | CLDR 44 | 100 % |
| `heb-IL` | Hebrew | עברית | Naher Osten | RTL | Hebräisch (`Hebr`) | älter, neu erzeugt aus CLDR 44 | 100 % |
| `hin-IN` | Hindi | हिन्दी | Südasien | LTR | Devanagari (`Deva`) | älter, neu erzeugt aus CLDR 44 | 100 % |
| `hsb-DE` | Upper Sorbian | Hornjoserbšćina | Europa | LTR | Lateinisch (`Latn`) | CLDR 44 | 100 % |
| `hun-HU` | Hungarian | Magyar | Europa | LTR | Lateinisch (`Latn`) | älter, von Hand erstellt | 100 % |
| `huy-IL` | Hulaulá | הולאולא | Naher Osten | RTL | Hebräisch (`Hebr`) | Namen von Hand, Konventionen aus CLDR 44 | 100 % |
| `hye-AM` | Armenian | Հայերեն | Zentralasien und Kaukasus | LTR | Armenisch (`Armn`) | CLDR 44 | 100 % |
| `ibo-NG` | Igbo | Igbo | Afrika | LTR | Lateinisch (`Latn`) | CLDR 44 | 100 % |
| `ind-ID` | Indonesian | Indonesian | Südostasien | LTR | Lateinisch (`Latn`) | älter, von Hand erstellt | 100 % |
| `isl-IS` | Icelandic | Íslenska | Europa | LTR | Lateinisch (`Latn`) | CLDR 44 | 100 % |
| `ita-CH` | Italian (Switzerland) | Italiano (Svizzera) | Europa | LTR | Lateinisch (`Latn`) | CLDR 44 | 100 % (fällt auf `ita-IT` zurück) |
| `ita-IT` | Italian (Italy) | Italiano (Italia) | Europa | LTR | Lateinisch (`Latn`) | älter, von Hand erstellt | 100 % |
| `ita-SM` | Italian (San Marino) | Italiano (San Marino) | Europa | LTR | Lateinisch (`Latn`) | CLDR 44 | 100 % (fällt auf `ita-IT` zurück) |
| `jav-ID` | Javanese | Jawa | Südostasien | LTR | Lateinisch (`Latn`) | CLDR 44 | 100 % |
| `jpn-JP` | Japanese | 日本語 | Ostasien | LTR | Japanisch (Han, Hiragana, Katakana) (`Jpan`) | älter, von Hand erstellt | 100 % |
| `jpn-JP@international` | Variant of `jpn-JP`: @international |  | Ostasien | LTR | Japanisch (Han, Hiragana, Katakana) (`Jpan`) | ältere Variante, von Hand erstellt | 100 % (fällt auf `jpn-JP` zurück) |
| `kab-DZ` | Kabyle | Taqbaylit | Afrika | LTR | Lateinisch (`Latn`) | CLDR 44 | 100 % |
| `kal-GL` | Greenlandic | Kalaallisut | Amerika | LTR | Lateinisch (`Latn`) | CLDR 44 | 100 % |
| `kan-IN` | Kannada | ಕನ್ನಡ | Südasien | LTR | Kannada (`Knda`) | CLDR 44 | 100 % |
| `kas-IN` | Kashmiri | کٲشُر | Südasien | RTL | Arabisch (`Arab`) | CLDR 44 | 100 % |
| `kat-GE` | Georgian | ქართული | Zentralasien und Kaukasus | LTR | Georgisch (`Geor`) | CLDR 44 | 100 % |
| `kaz-KZ` | Kazakh | Қазақ тілі | Zentralasien und Kaukasus | LTR | Kyrillisch (`Cyrl`) | CLDR 44 | 100 % |
| `khm-KH` | Khmer | ខ្មែរ | Südostasien | LTR | Khmer (`Khmr`) | CLDR 44 | 100 % |
| `kik-KE` | Kikuyu | Gikuyu | Afrika | LTR | Lateinisch (`Latn`) | CLDR 44 | 100 % |
| `kin-RW` | Kinyarwanda | Kinyarwanda | Afrika | LTR | Lateinisch (`Latn`) | CLDR 44 | 100 % |
| `kir-KG` | Kyrgyz | Кыргызча | Zentralasien und Kaukasus | LTR | Kyrillisch (`Cyrl`) | CLDR 44 | 100 % |
| `kok-IN` | Konkani | कोंकणी | Südasien | LTR | Devanagari (`Deva`) | CLDR 44 | 100 % |
| `kor-KP` | Korean (North Korea) | 한국어 (조선민주주의인민공화국) | Ostasien | LTR | Koreanisch (Hangul, Han) (`Kore`) | CLDR 44 | 100 % |
| `kor-KR` | Korean | 한국어 | Ostasien | LTR | Koreanisch (Hangul, Han) (`Kore`) | älter, neu erzeugt aus CLDR 44 | 100 % |
| `kur-TR` | Kurdish | Kurdî (kurmancî) | Naher Osten | LTR | Lateinisch (`Latn`) | CLDR 44 | 100 % |
| `lao-LA` | Lao | ລາວ | Südostasien | LTR | Laotisch (`Laoo`) | CLDR 44 | 100 % |
| `lat-VA` | Latin | Latina | Europa | LTR | Lateinisch (`Latn`) | Namen von Hand, Konventionen aus CLDR 44 | 100 % |
| `lav-LV` | Latvian | Latviešu | Europa | LTR | Lateinisch (`Latn`) | CLDR 44 | 100 % |
| `lin-CD` | Lingala | Lingála | Afrika | LTR | Lateinisch (`Latn`) | CLDR 44 | 100 % |
| `lit-LT` | Lithuanian | Lietuvių | Europa | LTR | Lateinisch (`Latn`) | CLDR 44 | 100 % |
| `ltz-LU` | Luxembourgish | Lëtzebuergesch | Europa | LTR | Lateinisch (`Latn`) | CLDR 44 | 100 % |
| `lug-UG` | Ganda | Luganda | Afrika | LTR | Lateinisch (`Latn`) | CLDR 44 | 100 % |
| `mai-IN` | Maithili | मैथिली | Südasien | LTR | Devanagari (`Deva`) | CLDR 44 | 100 % |
| `mal-IN` | Malayalam | മലയാളം | Südasien | LTR | Malayalam (`Mlym`) | CLDR 44 | 100 % |
| `mar-IN` | Marathi | मराठी | Südasien | LTR | Devanagari (`Deva`) | CLDR 44 | 100 % |
| `mkd-MK` | Macedonian | Македонски | Europa | LTR | Kyrillisch (`Cyrl`) | CLDR 44 | 100 % |
| `mlg-MG` | Malagasy | Malagasy | Afrika | LTR | Lateinisch (`Latn`) | CLDR 44 | 100 % |
| `mlt-MT` | Maltese | Malti | Europa | LTR | Lateinisch (`Latn`) | CLDR 44 | 100 % |
| `mni-IN` | Manipuri | মৈতৈলোন্ | Südasien | LTR | Bengalisch (`Beng`) | CLDR 44 | 100 % |
| `mon-MN` | Mongolian | Монгол | Ostasien | LTR | Kyrillisch (`Cyrl`) | CLDR 44 | 100 % |
| `mri-NZ` | Māori | Māori | Ozeanien | LTR | Lateinisch (`Latn`) | CLDR 44 | 100 % |
| `msa-BN` | Malay (Brunei) | Melayu (Brunei) | Südostasien | LTR | Lateinisch (`Latn`) | CLDR 44 | 100 % (fällt auf `msa-MY` zurück) |
| `msa-MY` | Malay (Malaysia) | Melayu (Malaysia) | Südostasien | LTR | Lateinisch (`Latn`) | CLDR 44 | 100 % |
| `msa-SG` | Malay (Singapore) | Melayu (Singapura) | Südostasien | LTR | Lateinisch (`Latn`) | CLDR 44 | 100 % (fällt auf `msa-MY` zurück) |
| `mya-MM` | Burmese | မြန်မာ | Südostasien | LTR | Myanmar (`Mymr`) | CLDR 44 | 100 % |
| `nep-NP` | Nepali | नेपाली | Südasien | LTR | Devanagari (`Deva`) | CLDR 44 | 100 % |
| `nno-NO` | Norwegian (Nynorsk) | Norsk (Nynorsk) | Europa | LTR | Lateinisch (`Latn`) | älter, von Hand erstellt | 100 % (fällt auf `nor-NO` zurück) |
| `nor-NO` | Norwegian Bokmål | Norsk (Bokmål) | Europa | LTR | Lateinisch (`Latn`) | älter, von Hand erstellt | 100 % |
| `nor-NO@intl` | Variant of `nor-NO`: Internasjonalt Format |  | Europa | LTR | Lateinisch (`Latn`) | ältere Variante, von Hand erstellt | 100 % (fällt auf `nor-NO` zurück) |
| `nor-NO@spraakraad` | Variant of `nor-NO`: Norsk Språkråd |  | Europa | LTR | Lateinisch (`Latn`) | ältere Variante, von Hand erstellt | 100 % (fällt auf `nor-NO` zurück) |
| `nor-SJ` | Norwegian Bokmål (Svalbard & Jan Mayen) | Norsk bokmål (Svalbard og Jan Mayen) | Europa | LTR | Lateinisch (`Latn`) | CLDR 44 | 100 % (fällt auf `nor-NO` zurück) |
| `nqo-GN` | N’Ko | ߒߞߏ | Afrika | RTL | N’Ko (`Nkoo`) | CLDR 44 | 100 % |
| `oci-FR` | Occitan | Occitan | Europa | LTR | Lateinisch (`Latn`) | CLDR 44 | 100 % |
| `ori-IN` | Odia | ଓଡ଼ିଆ | Südasien | LTR | Oriya (Odia) (`Orya`) | CLDR 44 | 100 % |
| `orm-ET` | Oromo | Oromoo | Afrika | LTR | Lateinisch (`Latn`) | CLDR 44 | 100 % |
| `pan-IN` | Punjabi | ਪੰਜਾਬੀ | Südasien | LTR | Gurmukhi (`Guru`) | CLDR 44 | 100 % |
| `pcm-NG` | Nigerian Pidgin | Naijíriá Píjin | Afrika | LTR | Lateinisch (`Latn`) | CLDR 44 | 100 % |
| `pnb-PK` | Western Punjabi (Shahmukhi) | پنجابی | Südasien | RTL | Arabisch (`Arab`) | CLDR 44 | 100 % |
| `pol-PL` | Polish | Polski | Europa | LTR | Lateinisch (`Latn`) | älter, von Hand erstellt | 100 % |
| `por-AO` | Portuguese (Angola) | Português (Angola) | Afrika | LTR | Lateinisch (`Latn`) | CLDR 44 | 100 % (fällt auf `por-PT` zurück) |
| `por-BR` | Portuguese (Brazil) | Português (Brasil) | Amerika | LTR | Lateinisch (`Latn`) | älter, von Hand erstellt | 100 % |
| `por-CV` | Portuguese (Cape Verde) | Português (Cabo Verde) | Afrika | LTR | Lateinisch (`Latn`) | CLDR 44 | 100 % (fällt auf `por-PT` zurück) |
| `por-GW` | Portuguese (Guinea-Bissau) | Português (Guiné-Bissau) | Afrika | LTR | Lateinisch (`Latn`) | CLDR 44 | 100 % (fällt auf `por-PT` zurück) |
| `por-MZ` | Portuguese (Mozambique) | Português | Afrika | LTR | Lateinisch (`Latn`) | älter, von Hand erstellt | 100 % (fällt auf `por-PT` zurück) |
| `por-PT` | Portuguese (Portugal) | Português | Europa | LTR | Lateinisch (`Latn`) | älter, von Hand erstellt | 100 % |
| `por-PT@euro` | Variant of `por-PT`: Suporte para Euro |  | Europa | LTR | Lateinisch (`Latn`) | ältere Variante, von Hand erstellt | 100 % (fällt auf `por-PT` zurück) |
| `por-ST` | Portuguese (São Tomé & Príncipe) | Português (São Tomé e Príncipe) | Afrika | LTR | Lateinisch (`Latn`) | CLDR 44 | 100 % (fällt auf `por-PT` zurück) |
| `por-TL` | Portuguese (Timor-Leste) | Português (Timor-Leste) | Südostasien | LTR | Lateinisch (`Latn`) | CLDR 44 | 100 % (fällt auf `por-PT` zurück) |
| `pus-AF` | Pashto | پښتو | Südasien | RTL | Arabisch (`Arab`) | CLDR 44 | 100 % |
| `qlo-ZZ` | Lorem Ipsum | Lorem Ipsum | Keine (Testsprache) | LTR | Lateinisch (`Latn`) | von Hand erstellt, nur Testsprache | — |
| `que-PE` | Quechua | Runasimi | Amerika | LTR | Lateinisch (`Latn`) | CLDR 44 | 100 % |
| `roh-CH` | Romansh | Rumantsch | Europa | LTR | Lateinisch (`Latn`) | CLDR 44 | 100 % |
| `ron-MD` | Romanian (Moldova) | Română (Republica Moldova) | Europa | LTR | Lateinisch (`Latn`) | CLDR 44 | 100 % (fällt auf `ron-RO` zurück) |
| `ron-RO` | Romanian | Română | Europa | LTR | Lateinisch (`Latn`) | CLDR 44 | 100 % |
| `run-BI` | Kirundi | Ikirundi | Afrika | LTR | Lateinisch (`Latn`) | CLDR 44 | 100 % |
| `rus-BY` | Russian (Belarus) | Русский (Беларусь) | Europa | LTR | Kyrillisch (`Cyrl`) | CLDR 44 | 100 % (fällt auf `rus-RU` zurück) |
| `rus-KG` | Russian (Kyrgyzstan) | Русский (Киргизия) | Zentralasien und Kaukasus | LTR | Kyrillisch (`Cyrl`) | CLDR 44 | 100 % (fällt auf `rus-RU` zurück) |
| `rus-KZ` | Russian (Kazakhstan) | Русский (Казахстан) | Zentralasien und Kaukasus | LTR | Kyrillisch (`Cyrl`) | CLDR 44 | 100 % (fällt auf `rus-RU` zurück) |
| `rus-MD` | Russian (Moldova) | Русский (Молдова) | Europa | LTR | Kyrillisch (`Cyrl`) | CLDR 44 | 100 % (fällt auf `rus-RU` zurück) |
| `rus-RU` | Russian | Русский | Europa | LTR | Kyrillisch (`Cyrl`) | älter, von Hand erstellt | 100 % |
| `san-IN` | Sanskrit | संस्कृत भाषा | Südasien | LTR | Devanagari (`Deva`) | CLDR 44 | 100 % |
| `sat-IN` | Santali | ᱥᱟᱱᱛᱟᱲᱤ | Südasien | LTR | Ol Chiki (`Olck`) | CLDR 44 | 100 % |
| `ser-SR` | Serbian (Srpski) | Serbian (Srpski) | Europa | LTR | Lateinisch (`Latn`) | älter, von Hand erstellt | 100 % |
| `sin-LK` | Sinhala | සිංහල | Südasien | LTR | Singhalesisch (`Sinh`) | CLDR 44 | 100 % |
| `slk-SK` | Slovak | Slovenčina | Europa | LTR | Lateinisch (`Latn`) | älter, von Hand erstellt | 100 % |
| `slv-SI` | Slovenian | Slovenščina | Europa | LTR | Lateinisch (`Latn`) | CLDR 44 | 100 % |
| `sme-NO` | Northern Sami | Davvisámegiella | Europa | LTR | Lateinisch (`Latn`) | CLDR 44 | 100 % |
| `sna-ZW` | Shona | ChiShona | Afrika | LTR | Lateinisch (`Latn`) | CLDR 44 | 100 % |
| `snd-PK` | Sindhi | سنڌي | Südasien | RTL | Arabisch (`Arab`) | CLDR 44 | 100 % |
| `som-SO` | Somali | Soomaali | Afrika | LTR | Lateinisch (`Latn`) | CLDR 44 | 100 % |
| `sqi-AL` | Albanian | Shqip | Europa | LTR | Lateinisch (`Latn`) | CLDR 44 | 100 % |
| `sqi-XK` | Albanian (Kosovo) | Shqip (Kosovë) | Europa | LTR | Lateinisch (`Latn`) | CLDR 44 | 100 % (fällt auf `sqi-AL` zurück) |
| `srp-ME` | Serbian (Montenegro) | Српски (Црна Гора) | Europa | LTR | Kyrillisch (`Cyrl`) | CLDR 44 | 100 % (fällt auf `srp-RS` zurück) |
| `srp-RS` | Serbian (Српски) | Serbian (Српски) | Europa | LTR | Kyrillisch (`Cyrl`) | älter, von Hand erstellt | 100 % |
| `sun-ID` | Sundanese | Basa Sunda | Südostasien | LTR | Lateinisch (`Latn`) | CLDR 44 | 100 % |
| `swa-KE` | Swahili (Kenya) | Kiswahili (Kenya) | Afrika | LTR | Lateinisch (`Latn`) | CLDR 44 | 100 % |
| `swa-TZ` | Swahili (Tanzania) | Kiswahili (Tanzania) | Afrika | LTR | Lateinisch (`Latn`) | CLDR 44 | 100 % (fällt auf `swa-KE` zurück) |
| `swe-AX` | Swedish (Åland Islands) | Svenska (Åland) | Europa | LTR | Lateinisch (`Latn`) | CLDR 44 | 100 % (fällt auf `swe-SE` zurück) |
| `swe-SE` | Swedish | Svenska | Europa | LTR | Lateinisch (`Latn`) | älter, von Hand erstellt | 100 % |
| `syr-IQ` | Syriac (Iraq) | ܣܘܪܝܝܐ (ܥܝܪܩ) | Naher Osten | RTL | Syrisch (`Syrc`) | CLDR 44 | 100 % |
| `syr-SY` | Syriac (Syria) | ܣܘܪܝܝܐ (ܣܘܪܝܐ) | Naher Osten | RTL | Syrisch (`Syrc`) | CLDR 44 | 100 % (fällt auf `syr-IQ` zurück) |
| `tam-IN` | Tamil (India) | தமிழ் (இந்தியா) | Südasien | LTR | Tamil (`Taml`) | CLDR 44 | 100 % |
| `tam-LK` | Tamil (Sri Lanka) | தமிழ் (இலங்கை) | Südasien | LTR | Tamil (`Taml`) | CLDR 44 | 100 % (fällt auf `tam-IN` zurück) |
| `tam-SG` | Tamil (Singapore) | தமிழ் (சிங்கப்பூர்) | Südostasien | LTR | Tamil (`Taml`) | CLDR 44 | 100 % (fällt auf `tam-IN` zurück) |
| `tat-RU` | Tatar | Татар | Europa | LTR | Kyrillisch (`Cyrl`) | CLDR 44 | 100 % |
| `tel-IN` | Telugu | తెలుగు | Südasien | LTR | Telugu (`Telu`) | CLDR 44 | 100 % |
| `tgk-TJ` | Tajik | Тоҷикӣ | Zentralasien und Kaukasus | LTR | Kyrillisch (`Cyrl`) | CLDR 44 | 100 % |
| `tha-TH` | Thai | ไทย | Südostasien | LTR | Thai (`Thai`) | CLDR 44 | 100 % |
| `tir-ET` | Tigrinya | ትግርኛ | Afrika | LTR | Äthiopisch (`Ethi`) | CLDR 44 | 100 % |
| `tlh-QO` | Klingon | tlhIngan Hol | Weltweit (fiktiv) | LTR | Lateinisch (`Latn`) | von Hand erstellt | 100 % |
| `ton-TO` | Tongan | Lea fakatonga | Ozeanien | LTR | Lateinisch (`Latn`) | CLDR 44 | 100 % |
| `tuk-TM` | Turkmen | Türkmen dili | Zentralasien und Kaukasus | LTR | Lateinisch (`Latn`) | CLDR 44 | 100 % |
| `tur-CY` | Turkish (Cyprus) | Türkçe (Kıbrıs) | Europa | LTR | Lateinisch (`Latn`) | CLDR 44 | 100 % (fällt auf `tur-TR` zurück) |
| `tur-TR` | Turkish | Türkçe | Naher Osten | LTR | Lateinisch (`Latn`) | älter, von Hand erstellt | 100 % |
| `uig-CN` | Uyghur | ئۇيغۇرچە | Ostasien | RTL | Arabisch (`Arab`) | CLDR 44 | 100 % |
| `ukr-UA` | Ukrainian | Українська | Europa | LTR | Kyrillisch (`Cyrl`) | älter, von Hand erstellt, korrigiert in 6.0.15 | 100 % |
| `urd-IN` | Urdu (India) | اردو (بھارت) | Südasien | RTL | Arabisch (`Arab`) | CLDR 44 | 100 % (fällt auf `urd-PK` zurück) |
| `urd-PK` | Urdu (Pakistan) | اردو (پاکستان) | Südasien | RTL | Arabisch (`Arab`) | CLDR 44 | 100 % |
| `uzb-UZ` | Uzbek | O‘zbek | Zentralasien und Kaukasus | LTR | Lateinisch (`Latn`) | CLDR 44 | 100 % |
| `vie-VN` | Vietnamese | Tiếng Việt | Südostasien | LTR | Lateinisch (`Latn`) | CLDR 44 | 100 % |
| `wol-SN` | Wolof | Wolof | Afrika | LTR | Lateinisch (`Latn`) | CLDR 44 | 100 % |
| `xho-ZA` | Xhosa | IsiXhosa | Afrika | LTR | Lateinisch (`Latn`) | CLDR 44 | 100 % |
| `yid-US` | Yiddish | ייִדיש | Amerika | RTL | Hebräisch (`Hebr`) | CLDR 44 | 100 % |
| `yor-NG` | Yoruba | Èdè Yorùbá | Afrika | LTR | Lateinisch (`Latn`) | CLDR 44 | 100 % |
| `zgh-MA` | Standard Moroccan Tamazight | ⵜⴰⵎⴰⵣⵉⵖⵜ | Afrika | LTR | Tifinagh (`Tfng`) | CLDR 44 | 100 % |
| `zul-ZA` | Zulu | IsiZulu | Afrika | LTR | Lateinisch (`Latn`) | CLDR 44 | 100 % |

Wie die erzeugten Dateien entstanden sind und wo sie von CLDR abweichen, steht in [Abschnitt 7](#7-eine-locale-hinzufügen).
Hinweise zu einzelnen Locales:

- **Latein** (`lat-VA`) ist eine klassische Sprache: Kein Land spricht sie als Muttersprache, und der Heilige Stuhl
  (Vatikanstadt), wo sie Amtssprache ist, steht für ihr Land. CLDR 44 hat keine Daten für Latein, daher folgen ihre
  Zahlen, ihre Währung (Euro) und ihre Datumsmuster den Konventionen des Heiligen Stuhls (CLDR `it_VA`), und ihre Namen
  sind von Hand geschrieben: `Latina`, Monatsnamen im Nominativ (`Ianuarius`), die kirchlichen Tagesnamen, die der
  Heilige Stuhl verwendet (`Dominica`, `Feria secunda` ... `Sabbatum`); das lange Datum zeigt den kurzen Monat
  (`Feria tertia, 14 Nov. 2023`). Ihre Ländernamen sind die englischen von CLDR.
- **Hebräisch** (`heb-IL`) gab es schon zuvor; seine Datei nannte `windows-1255` und enthielt doppelt kodierte Monats-
  und Tagesnamen. Sie wurde in 6.0.15 aus CLDR `he_IL` neu erzeugt (UTF-8, `he-IL`, `₪`, die hebräischen Ländernamen
  von CLDR). Auch `hin-IN` und `kor-KR` wurden neu erzeugt: `hin-IN` hatte englische Namen („Hindi (Devnagari)“,
  englische Monate und Tage) und Ländernamen, deren Zeichen zu `?` verloren gegangen waren; `kor-KR` hatte ein
  ungültiges langes Datum (das Jahreszeichen 년 vor dem Jahr) und kurze Tagesnamen, die den langen glichen.
- **Von Hand korrigiert** in 6.0.15, der Rest der älteren Datei beibehalten: `chi-HK` (sein langes Datum gab 月 zweimal
  aus, und es war die einzige Datei mit einem Byte Order Mark), `chi-CN` (kurzer Sonntag `周日`), `ell-GR` (Eigenname
  `Ελληνικά` statt „Greek (Hellenic)“), `ukr-UA` (`Українська` statt „Ukrainian“; Monatsnamen in der Form innerhalb
  eines Datums, `15 жовтня` statt `15 Жовтень`, mit der alleinstehenden Form daneben). `kat-GE` verlor einen
  Großbuchstaben, den sein Generator ihm gegeben hatte (Georgisch kennt im fortlaufenden Text keine: `ქართული`).
- **Bekannte Grenzen** älterer Dateien: `ser-SR` und `srp-RS` mischen Englisch und Eigensprache („Serbian (Српски)“, und `srp-RS`
  schreibt seine Tagesnamen groß), `chi-CN` und `chi-TW` behalten ihre alten Datumsmuster (`2025年 十月 15日`,
  lesbar, aber nicht das `2025年10月15日` von CLDR). Die kurzen Monatsnamen von `rus-RU` (`Янв`, `Май`)
  sind keine der beiden Formen von CLDR, und `som-SO` benennt die Monate auf die
  traditionelle Weise (`Bisha Tobnaad`); diese Dateien haben dafür noch keine alleinstehende Gruppe.
- **Dhivehi** (`div-MV`): ICU 74 hat keine Daten für Dhivehi. Zahlen, die Rufiyaa (Symbol `Rf`) und die Datumsmuster
  folgen CLDR `en_MV`; der Name der Sprache (ދިވެހި, englisch „Dhivehi“, die maledivische Schreibung), der Name der
  Währung und die Monats- und Tagesnamen in Thaana sind von Hand geschrieben und brauchen eine Prüfung durch einen
  Muttersprachler. CLDR lässt die Woche auf den Malediven am Freitag beginnen; `eZLocale` kennt nur Montag und
  Sonntag, daher beginnt die Woche am Sonntag.
- **Jiddisch** (`yid-US`): CLDR 44 hat Jiddisch nur als `yi_UA` (bis CLDR 43 `yi_001`, „Welt“), und ein Locale-Code
  braucht ein zweibuchstabiges Land. Die größte jiddischsprachige Gemeinschaft lebt in den Vereinigten Staaten, daher
  enthält die Datei die jiddischen Namen von CLDR mit den Konventionen und der Währung der Vereinigten Staaten.
- **Reichsaramäisch** (`arc-IQ`) und **Hulaulá** (`huy-IL`, jüdisches Neuaramäisch) haben keine CLDR-Daten und werden
  in hebräischer Quadratschrift geschrieben. `arc-IQ` übernimmt die Konventionen von CLDR `syr_IQ` (irakischer Dinar)
  und dessen aramäische Monats- und Ländernamen, aus der syrischen Schrift transliteriert; die Tagesnamen sind die
  aramäischen (חד בשבא ... שבתא). `huy-IL` übernimmt Konventionen, Monats- und Ländernamen von CLDR `he_IL` (Neuer
  Schekel) und die jüdisch-aramäischen Tagesnamen (חד בשבא ... מעלי שבתא, שבתא). Sprach- und Währungsnamen sind von
  Hand geschrieben; jede Datei nennt die Quelle jedes Werts und braucht eine Prüfung.
- **Dari** (`fas-AF`) ist das Persisch Afghanistans, wie in CLDR (`fa_AF`, Anzeigename „Dari“, دری); `fas-IR`
  bleibt „Persian“.
- **Zentralkurdisch** (`ckb-IQ`, Sorani, arabische Schrift, von rechts nach links) hat keinen Code nach ISO 639-2
  (639-2 hat nur `kur` für das gesamte Kurdische, das `kur-TR`, Kurmandschi in lateinischer Schrift, bereits
  verwendet), daher nimmt es seinen Code nach ISO 639-3.
- **Klingonisch** (`tlh-QO`) ist von Hand erstellt, zum Spaß und als Test einer Locale ohne CLDR-Daten. `tlh` ist der
  Code nach ISO 639-2 für Klingonisch; `QO` liegt im benutzerdefinierten Bereich von ISO 3166 (QM bis QZ), daher ist
  das Land `None (private-use region QO)`, und das HTTP-Tag ist das bloße `tlh`, das keinen realen Ort beansprucht
  (wie `eo` für `epo-EO`). Es hat keine Währung (ISO 4217 `XXX`, Zeichen `¤`), Zahlen wie `eng-US`, die 24-Stunden-Uhr,
  die Tagesnamen von Marc Okrand (`DaSjaj` ... `lojmItjaj`) und die Monate als „Monat“ plus Zahl (`jar wa'` ...
  `jar wa'maH cha'`), in der lateinischen Transkription. Es wird nur in der A–Z-Liste der Wahl der Oberflächensprache
  angeboten, in keiner Gruppe. Die klingonische Schrift pIqaD ist nicht im eigentlichen Unicode enthalten (die
  ConScript Unicode Registry legt sie in den Bereich zur privaten Nutzung, U+F8D0 bis U+F8FF) und wird nie in
  Oberflächentexten oder Locale-Dateien verwendet; eine Website, die sie zur Dekoration möchte, lädt selbst eine
  ConScript-kompatible Schrift, während der Text in der Transkription bleibt.
- **Interlingua** (`ia_001`) wird nicht angeboten: CLDR hat es nur für die Region „Welt“ (`001`), und ein Locale-Code
  braucht ein zweibuchstabiges Land.
- Testsprachen, gekennzeichnet mit `[LanguageSettings] TestLanguage=true` (Lorem Ipsum, `qlo-ZZ`), stehen am Ende der
  Tabelle, als „nur Testsprache“, und werden in diesem Dokument nirgends mitgezählt.

### Schriften, Fonts und Zeilenumbruch

Eine Locale-Datei enthält nur Text; ob eine Schrift angezeigt wird, hängt von den Fonts des Systems des Lesers ab.
Die Designs der Administrationsoberfläche (admin4, admin4l und das klassische Admin teilen sich `components.css`)
verwenden nur Systemschriften und laden nichts herunter. Ihr Font-Token `--a4-font` behält seine lateinischen
Schriftfamilien vorn (sodass lateinischer Text unverändert bleibt) und nennt danach über `--a4-font-intl` die
verbreiteten Systemschriften der anderen Schriftsysteme: Chinesisch, Japanisch und Koreanisch (`--a4-font-cjk`:
PingFang, Hiragino, Microsoft YaHei und JhengHei, Yu Gothic, Meiryo, Malgun Gothic, Apple SD Gothic Neo, Noto Sans
CJK), Thai (Leelawadee UI, Thonburi, Noto Sans Thai), Devanagari (Nirmala UI, Kohinoor Devanagari, Noto Sans
Devanagari), Arabisch und Hebräisch (Segoe UI, Noto Sans Arabic, Geeza Pro, Noto Sans Hebrew, Arial Hebrew).
Han-Zeichen werden von Chinesisch, Japanisch und Koreanisch geteilt, aber in jeder Sprache anders gezeichnet, daher
folgt die Reihenfolge von `--a4-font-cjk` der Oberflächensprache (`:lang(ja)`, `:lang(ko)`, `:lang(zh-TW)`,
`:lang(zh-HK)`: die Schriften dieser Sprache zuerst; sonst vereinfachtes Chinesisch zuerst). Das klassische Admin und
die Admin UI fügen `--a4-font-intl` nach ihren eigenen lateinischen Schriftfamilien hinzu. Eine Schrift, die auch
lateinische Buchstaben enthält (Droid Sans Fallback), ist absichtlich weggelassen: Auf einem System ohne die
lateinischen Schriftfamilien würde sie auch lateinischen Text übernehmen. Jedes nicht abgedeckte Schriftsystem fällt
auf das zurück, was das System anbietet.

Der Zeilenumbruch folgt der Sprache der Seite oder eines Elements mit `lang`:

```css
html:lang(ja), [lang]:lang(ja) { line-break: strict; }
html:lang(ko), [lang]:lang(ko) { word-break: keep-all; overflow-wrap: break-word; }
html:lang(th|lo|km|my), [lang]:lang(th|lo|km|my) { overflow-wrap: break-word; }
```

Japanisch vermeidet einen Umbruch vor kleinem Kana und schließender Interpunktion; Koreanisch bricht an den
Leerzeichen zwischen Wörtern um, wie es gesetzt wird; Thai, Laotisch, Khmer und Birmanisch, die ohne Leerzeichen
geschrieben werden, brechen an den Wortgrenzen des Browser-Wörterbuchs um, mit `break-word` als Sicherheitsnetz für
eine Zeichenfolge, die es nicht kennt. Chinesisch braucht nichts: Browser brechen zwischen Ideogrammen um. Die
klingonische Schrift pIqaD wird nie verwendet (siehe Klingonisch oben).

## 4. Alle Übersetzungen der Administrationsoberfläche

Jede Übersetzung wurde für das Release 6.0.15 auf zwei Arten gemessen, beide abgerundet:

- **Eigene Datei fertig**: fertige Meldungen der Datei geteilt durch alle ihre Meldungen, veraltete nicht
  mitgerechnet, jede Kombination aus Kontext, Quelle und Kommentar einmal gezählt.
  `php bin/php/ezchecktranslation.php <code>` gibt einen ähnlichen Wert „Percent finished“ aus.
- **Aktuelle Texte übersetzt**: wie viele der 18.839 Texte, die `share/translations/untranslated/translation.ts` zum
  Zeitpunkt der Messung enthielt (jeder Text, den der Kernel und seine Designs anfordern), die Datei übersetzt, mit
  gleichem Kontext, gleicher Quelle und gleichem Kommentar, fertig und nicht leer. Das ist es, was ein Administrator
  sieht. Der Rest erscheint auf Englisch.

Die Wahl der Oberflächensprache berechnet ihre Abdeckung beim Anzeigen aus den installierten Dateien, deshalb
stimmt sie auch, wenn später Texte hinzukommen. Englisch ist die Quellsprache und zählt dort als 100 %.

| Übersetzung | Eigene Datei fertig | Aktuelle Texte übersetzt |
|---|---:|---:|
| `afr-ZA` | 100 % (18.844 von 18.844) | 100 % (18.839 von 18.839) |
| `amh-ET` | 100 % (18.839 von 18.839) | 100 % (18.839 von 18.839) |
| `ara-SA` | 100 % (18.844 von 18.844) | 100 % (18.839 von 18.839) |
| `ben-BD` | 100 % (18.839 von 18.839) | 100 % (18.839 von 18.839) |
| `bos-BA` | 100 % (18.844 von 18.844) | 100 % (18.839 von 18.839) |
| `bul-BG` | 100 % (18.844 von 18.844) | 100 % (18.839 von 18.839) |
| `cat-ES` | 100 % (18.844 von 18.844) | 100 % (18.839 von 18.839) |
| `chi-CN` | 100 % (18.839 von 18.839) | 100 % (18.839 von 18.839) |
| `chi-TW` | 100 % (18.839 von 18.839) | 100 % (18.839 von 18.839) |
| `cro-HR` | 100 % (18.840 von 18.840) | 100 % (18.839 von 18.839) |
| `cze-CZ` | 100 % (18.841 von 18.841) | 100 % (18.839 von 18.839) |
| `dan-DK` | 100 % (18.841 von 18.841) | 100 % (18.839 von 18.839) |
| `dsb-DE` | 100 % (18.844 von 18.844) | 100 % (18.839 von 18.839) |
| `dut-NL` | 100 % (18.840 von 18.840) | 100 % (18.839 von 18.839) |
| `ell-GR` | 100 % (18.844 von 18.844) | 100 % (18.839 von 18.839) |
| `eng-AU` | 100 % (18.844 von 18.844) | 100 % (18.839 von 18.839) |
| `eng-US` | 100 % (18.844 von 18.844) | 100 % (18.839 von 18.839) |
| `esl-ES` | 100 % (18.844 von 18.844) | 100 % (18.839 von 18.839) |
| `esl-MX` | 100 % (18.844 von 18.844) | 100 % (18.839 von 18.839) |
| `est-EE` | 100 % (18.844 von 18.844) | 100 % (18.839 von 18.839) |
| `fas-IR` | 100 % (18.839 von 18.839) | 100 % (18.839 von 18.839) |
| `fin-FI` | 100 % (18.844 von 18.844) | 100 % (18.839 von 18.839) |
| `fre-FR` | 100 % (18.844 von 18.844) | 100 % (18.839 von 18.839) |
| `ger-DE` | 100 % (18.850 von 18.850) | 100 % (18.839 von 18.839) |
| `gle-IE` | 100 % (18.844 von 18.844) | 100 % (18.839 von 18.839) |
| `guj-IN` | 100 % (18.839 von 18.839) | 100 % (18.839 von 18.839) |
| `hau-NG` | 100 % (18.839 von 18.839) | 100 % (18.839 von 18.839) |
| `heb-IL` | 100 % (18.844 von 18.844) | 100 % (18.839 von 18.839) |
| `hin-IN` | 100 % (18.844 von 18.844) | 100 % (18.839 von 18.839) |
| `hun-HU` | 100 % (18.844 von 18.844) | 100 % (18.839 von 18.839) |
| `ibo-NG` | 100 % (18.839 von 18.839) | 100 % (18.839 von 18.839) |
| `ind-ID` | 100 % (18.839 von 18.839) | 100 % (18.839 von 18.839) |
| `isl-IS` | 100 % (18.844 von 18.844) | 100 % (18.839 von 18.839) |
| `ita-IT` | 100 % (18.840 von 18.840) | 100 % (18.839 von 18.839) |
| `jpn-JP` | 100 % (18.844 von 18.844) | 100 % (18.839 von 18.839) |
| `kal-GL` | 100 % (18.844 von 18.844) | 100 % (18.839 von 18.839) |
| `kan-IN` | 100 % (18.839 von 18.839) | 100 % (18.839 von 18.839) |
| `kor-KR` | 100 % (18.844 von 18.844) | 100 % (18.839 von 18.839) |
| `kur-TR` | 100 % (18.839 von 18.839) | 100 % (18.839 von 18.839) |
| `lao-LA` | 100 % (18.839 von 18.839) | 100 % (18.839 von 18.839) |
| `lat-VA` | 100 % (18.844 von 18.844) | 100 % (18.839 von 18.839) |
| `lav-LV` | 100 % (18.844 von 18.844) | 100 % (18.839 von 18.839) |
| `lit-LT` | 100 % (18.844 von 18.844) | 100 % (18.839 von 18.839) |
| `mal-IN` | 100 % (18.839 von 18.839) | 100 % (18.839 von 18.839) |
| `mar-IN` | 100 % (18.839 von 18.839) | 100 % (18.839 von 18.839) |
| `mlt-MT` | 100 % (18.844 von 18.844) | 100 % (18.839 von 18.839) |
| `msa-MY` | 100 % (18.839 von 18.839) | 100 % (18.839 von 18.839) |
| `nor-NO` | 100 % (18.840 von 18.840) | 100 % (18.839 von 18.839) |
| `ori-IN` | 100 % (18.839 von 18.839) | 100 % (18.839 von 18.839) |
| `pan-IN` | 100 % (18.839 von 18.839) | 100 % (18.839 von 18.839) |
| `pol-PL` | 100 % (18.840 von 18.840) | 100 % (18.839 von 18.839) |
| `por-BR` | 100 % (18.841 von 18.841) | 100 % (18.839 von 18.839) |
| `por-PT` | 100 % (18.844 von 18.844) | 100 % (18.839 von 18.839) |
| `pus-AF` | 100 % (18.839 von 18.839) | 100 % (18.839 von 18.839) |
| `ron-RO` | 100 % (18.844 von 18.844) | 100 % (18.839 von 18.839) |
| `rus-RU` | 100 % (18.844 von 18.844) | 100 % (18.839 von 18.839) |
| `ser-SR` | 100 % (18.844 von 18.844) | 100 % (18.839 von 18.839) |
| `slk-SK` | 100 % (18.844 von 18.844) | 100 % (18.839 von 18.839) |
| `slv-SI` | 100 % (18.844 von 18.844) | 100 % (18.839 von 18.839) |
| `som-SO` | 100 % (18.839 von 18.839) | 100 % (18.839 von 18.839) |
| `sqi-AL` | 100 % (18.844 von 18.844) | 100 % (18.839 von 18.839) |
| `srp-RS` | 100 % (18.844 von 18.844) | 100 % (18.839 von 18.839) |
| `swa-KE` | 100 % (18.839 von 18.839) | 100 % (18.839 von 18.839) |
| `swe-SE` | 100 % (18.840 von 18.840) | 100 % (18.839 von 18.839) |
| `tam-IN` | 100 % (18.839 von 18.839) | 100 % (18.839 von 18.839) |
| `tel-IN` | 100 % (18.839 von 18.839) | 100 % (18.839 von 18.839) |
| `tha-TH` | 100 % (18.839 von 18.839) | 100 % (18.839 von 18.839) |
| `tlh-QO` | 100 % (18.844 von 18.844) | 100 % (18.839 von 18.839) |
| `tur-TR` | 100 % (18.840 von 18.840) | 100 % (18.839 von 18.839) |
| `ukr-UA` | 100 % (18.844 von 18.844) | 100 % (18.839 von 18.839) |
| `urd-PK` | 100 % (18.839 von 18.839) | 100 % (18.839 von 18.839) |
| `vie-VN` | 100 % (18.839 von 18.839) | 100 % (18.839 von 18.839) |
| `xho-ZA` | 100 % (18.839 von 18.839) | 100 % (18.839 von 18.839) |
| `yid-US` | 100 % (18.839 von 18.839) | 100 % (18.839 von 18.839) |
| `yor-NG` | 100 % (18.839 von 18.839) | 100 % (18.839 von 18.839) |
| `zul-ZA` | 100 % (18.839 von 18.839) | 100 % (18.839 von 18.839) |
| `jav-ID` | 100 % (18.811 von 18.811) | 99 % (18.809 von 18.839) |
| `syr-IQ` | 100 % (18.812 von 18.812) | 100 % (18.812 von 18.812) |
| `arc-IQ` | 100 % (18.812 von 18.812) | 100 % (18.812 von 18.812) |
| `huy-IL` | 100 % (18.812 von 18.812) | 100 % (18.812 von 18.812) |
| `sun-ID` | 100 % (18.812 von 18.812) | 100 % (18.812 von 18.812) |
| `nep-NP` | 100 % (18.812 von 18.812) | 100 % (18.812 von 18.812) |
| `bho-IN` | 100 % (18.812 von 18.812) | 100 % (18.812 von 18.812) |
| `mai-IN` | 100 % (18.812 von 18.812) | 100 % (18.812 von 18.812) |
| `pcm-NG` | 100 % (18.812 von 18.812) | 100 % (18.812 von 18.812) |
| `kat-GE` | 100 % (18.839 von 18.839) | 100 % (18.839 von 18.839) |
| `sna-ZW` | 100 % (18.839 von 18.839) | 100 % (18.839 von 18.839) |
| `kaz-KZ` | 100 % (18.839 von 18.839) | 100 % (18.839 von 18.839) |
| `gla-GB` | 100 % (18.839 von 18.839) | 100 % (18.839 von 18.839) |
| `mri-NZ` | 100 % (18.839 von 18.839) | 100 % (18.839 von 18.839) |
| `fil-PH` | 100 % (18.839 von 18.839) | 100 % (18.839 von 18.839) |
| `che-RU` | 100 % (18.839 von 18.839) | 100 % (18.839 von 18.839) |
| `snd-PK` | 100 % (18.839 von 18.839) | 100 % (18.839 von 18.839) |
| `pnb-PK` | 100 % (18.839 von 18.839) | 100 % (18.839 von 18.839) |
| `uzb-UZ` | 100 % (18.839 von 18.839) | 100 % (18.839 von 18.839) |
| `mya-MM` | 100 % (18.839 von 18.839) | 100 % (18.839 von 18.839) |
| `haw-US` | 100 % (18.839 von 18.839) | 100 % (18.839 von 18.839) |
| `hye-AM` | 100 % (18.839 von 18.839) | 100 % (18.839 von 18.839) |
| `lin-CD` | 100 % (18.839 von 18.839) | 100 % (18.839 von 18.839) |
| `ful-SN` | 100 % (18.839 von 18.839) | 100 % (18.839 von 18.839) |
| `orm-ET` | 100 % (18.839 von 18.839) | 100 % (18.839 von 18.839) |
| `aze-AZ` | 100 % (18.839 von 18.839) | 100 % (18.839 von 18.839) |
| `aka-GH` | 100 % (18.839 von 18.839) | 100 % (18.839 von 18.839) |
| `sin-LK` | 100 % (18.839 von 18.839) | 100 % (18.839 von 18.839) |
| `khm-KH` | 100 % (18.839 von 18.839) | 100 % (18.839 von 18.839) |
| `asm-IN` | 100 % (18.839 von 18.839) | 100 % (18.839 von 18.839) |
| `kik-KE` | 100 % (18.839 von 18.839) | 100 % (18.839 von 18.839) |
| `uig-CN` | 100 % (18.839 von 18.839) | 100 % (18.839 von 18.839) |
| `kin-RW` | 100 % (18.839 von 18.839) | 100 % (18.839 von 18.839) |
| `ceb-PH` | 100 % (18.839 von 18.839) | 100 % (18.839 von 18.839) |
| `ckb-IQ` | 100 % (18.839 von 18.839) | 100 % (18.839 von 18.839) |
| `tir-ET` | 100 % (18.839 von 18.839) | 100 % (18.839 von 18.839) |
| `mlg-MG` | 100 % (18.839 von 18.839) | 100 % (18.839 von 18.839) |
| `run-BI` | 100 % (18.839 von 18.839) | 100 % (18.839 von 18.839) |
| `tgk-TJ` | 100 % (18.839 von 18.839) | 100 % (18.839 von 18.839) |
| `bod-CN` | 100 % (18.839 von 18.839) | 100 % (18.839 von 18.839) |
| `bel-BY` | 100 % (18.839 von 18.839) | 100 % (18.839 von 18.839) |
| `mon-MN` | 100 % (18.839 von 18.839) | 100 % (18.839 von 18.839) |
| `glg-ES` | 100 % (18.839 von 18.839) | 100 % (18.839 von 18.839) |
| `kir-KG` | 100 % (18.839 von 18.839) | 100 % (18.839 von 18.839) |
| `eus-ES` | 100 % (18.839 von 18.839) | 100 % (18.839 von 18.839) |
| `tat-RU` | 100 % (18.839 von 18.839) | 100 % (18.839 von 18.839) |
| `cym-GB` | 100 % (18.839 von 18.839) | 100 % (18.839 von 18.839) |
| `tuk-TM` | 100 % (18.839 von 18.839) | 100 % (18.839 von 18.839) |
| `mkd-MK` | 100 % (18.839 von 18.839) | 100 % (18.839 von 18.839) |
| `lug-UG` | 100 % (18.839 von 18.839) | 100 % (18.839 von 18.839) |
| `ltz-LU` | 100 % (18.839 von 18.839) | 100 % (18.839 von 18.839) |
| `wol-SN` | 100 % (18.839 von 18.839) | 100 % (18.839 von 18.839) |
| `kor-KP` | 100 % (18.839 von 18.839) | 100 % (18.839 von 18.839) |
| `ast-ES` | 100 % (18.839 von 18.839) | 100 % (18.839 von 18.839) |
| `fas-AF` | 100 % (18.839 von 18.839) | 100 % (18.839 von 18.839) |
| `bam-ML` | 100 % (18.839 von 18.839) | 100 % (18.839 von 18.839) |
| `dzo-BT` | 100 % (18.839 von 18.839) | 100 % (18.839 von 18.839) |
| `bre-FR` | 100 % (18.839 von 18.839) | 100 % (18.839 von 18.839) |
| `ewe-GH` | 100 % (18.839 von 18.839) | 100 % (18.839 von 18.839) |
| `oci-FR` | 100 % (18.839 von 18.839) | 100 % (18.839 von 18.839) |
| `kab-DZ` | 100 % (18.839 von 18.839) | 100 % (18.839 von 18.839) |
| `div-MV` | 100 % (18.839 von 18.839) | 100 % (18.839 von 18.839) |
| `kok-IN` | 100 % (18.839 von 18.839) | 100 % (18.839 von 18.839) |
| `roh-CH` | 100 % (18.839 von 18.839) | 100 % (18.839 von 18.839) |
| `zgh-MA` | 100 % (18.839 von 18.839) | 100 % (18.839 von 18.839) |
| `hsb-DE` | 100 % (18.839 von 18.839) | 100 % (18.839 von 18.839) |
| `mni-IN` | 100 % (18.839 von 18.839) | 100 % (18.839 von 18.839) |
| `fry-NL` | 100 % (18.839 von 18.839) | 100 % (18.839 von 18.839) |
| `doi-IN` | 100 % (18.839 von 18.839) | 100 % (18.839 von 18.839) |
| `fao-FO` | 100 % (18.839 von 18.839) | 100 % (18.839 von 18.839) |
| `nqo-GN` | 100 % (18.839 von 18.839) | 100 % (18.839 von 18.839) |
| `kas-IN` | 100 % (18.839 von 18.839) | 100 % (18.839 von 18.839) |
| `sme-NO` | 100 % (18.839 von 18.839) | 100 % (18.839 von 18.839) |
| `que-PE` | 100 % (18.839 von 18.839) | 100 % (18.839 von 18.839) |
| `epo-EO` | 100 % (18.839 von 18.839) | 100 % (18.839 von 18.839) |
| `brx-IN` | 100 % (18.839 von 18.839) | 100 % (18.839 von 18.839) |
| `ton-TO` | 100 % (18.839 von 18.839) | 100 % (18.839 von 18.839) |
| `sat-IN` | 100 % (18.839 von 18.839) | 100 % (18.839 von 18.839) |
| `san-IN` | 100 % (18.839 von 18.839) | 100 % (18.839 von 18.839) |

Hinweise:

- **Wie sie entstanden sind.** `eng-US` ist das Englisch der Quelltexte, und `ger-DE` wurde von Hand übersetzt und
  durchgesehen. Alle anderen Übersetzungen sind maschinell unterstützt entstanden: mit einem Glossar und einem
  Stilleitfaden je Sprache, danach geprüft von den Qualitätstests aus
  [Abschnitt 8](#8-eine-übersetzung-der-administrationsoberfläche-hinzufügen-oder-vervollständigen). Die
  `share/translations/<code>/GUIDE.md` jeder Sprache hält ihre Entscheidungen fest: die Anrede, Schrift und
  Typografie, die Formulierung von Schaltflächen und die Begriffe, die für den Kernel und jede Extension gelten. Eine
  Durchsicht durch Muttersprachler ist bei allen willkommen
  ([Einen Übersetzungsfehler melden](#einen-übersetzungsfehler-melden)).
- **Aus einer anderen Übersetzung angepasst.** `fas-AF` (Dari) ist der persische Katalog, Zeile für Zeile an den
  afghanischen Sprachgebrauch angepasst, und `kor-KP` der südkoreanische Katalog, angepasst an die nordkoreanische
  Norm (문화어). Keine von beiden fällt auf ihre Vorlage zurück. `jav-ID` und `sun-ID` teilen ihre Fachbegriffe mit
  `ind-ID`. `eng-AU` sind die Quelltexte mit einer geprüften Wortliste (australische Schreibung, GST statt VAT).
- **Transliteriert.** `ser-SR` ist `srp-RS`, Buchstabe für Buchstabe in lateinische Schrift übertragen. `arc-IQ` und
  `huy-IL` sind `syr-IQ`, in hebräische Quadratschrift übertragen; ihre `GUIDE.md` nennt die Zuordnung. Serbisch hat
  deshalb zwei Verzeichnisse: `ser-SR` (lateinisch) und `srp-RS` (kyrillisch), und beide Locale-Dateien gibt es.
- **Über einen Fallback gezeigt.** Einige Locales haben keinen eigenen Katalog und zeigen den ihrer Sprache:
  `chi-HK` zeigt `chi-TW`, `chi-SG` zeigt `chi-CN`, `nno-NO` zeigt `nor-NO`, `eng-NZ` zeigt `eng-AU`, `swa-TZ`
  zeigt `swa-KE`, `syr-SY` zeigt `syr-IQ`, und jedes arabische Land zeigt `ara-SA`. Siehe
  [Abschnitt 13](#13-übersetzungs-fallbacks).
- **Bekannte Grenzen.** Der Übersetzer kennt keine Pluralformen, deshalb liest sich eine Anzahl in Sprachen mit mehr
  als zwei Formen holprig (im Ukrainischen für 2 bis 4); die meisten solchen Texte lauten „Bezeichnung: %count“. In
  einigen Sprachen bleiben ein paar Texte ohne verlässlichen Begriff auf Englisch, bis ein Muttersprachler sie
  durchgesehen hat. Die Ausnahmeliste des Qualitätstests nennt jeden mit seinem Grund.
- **Extensions.** Jede Extension veröffentlicht ihre eigenen Kataloge in ihrem Verzeichnis `translations/`; sie
  zählen hier nicht mit. Die mitgelieferten ezoe und ezjscore bringen für jede Übersetzung oben einen vollständigen
  Katalog mit. Eine andere Extension zeigt ihre Texte in einer Sprache, für die sie keinen Katalog hat, auf Englisch
  oder in ihrem Fallback.

## 5. Die großen Sprachen der Welt

Die Sprachen mit 50 Millionen oder mehr Sprechern (Erst- und Zweitsprache zusammen), wie sie
[Wikipedias „List of languages by total number of speakers“](https://en.wikipedia.org/wiki/List_of_languages_by_total_number_of_speakers)
aufführt, die ihre Zahlen aus dem Ethnologue (Ausgabe 2026) bezieht; abgerufen am 15. Oktober 2026. Die Sprecherzahlen
sind auf Millionen gerundet.

| # | Sprache | Sprecher (Mio.) | Locales | Übersetzung der Administrationsoberfläche |
|---:|---|---:|---|---|
| 1 | Englisch | 1.493 | `eng-US`, `eng-GB`, `eng-CA`, `eng-AU`, `eng-NZ`, `eng-IE`, `eng-IN`, `eng-SG`, `eng-ZA`, `eng-NG`, `eng-KE`, `eng-GH`, `eng-PK`, `eng-PH` | `eng-US` vollständig; `eng-AU` vollständig, `eng-NZ` fällt darauf zurück |
| 2 | Mandarin-Chinesisch | 1.183 | `chi-CN`, `chi-SG` (vereinfacht), `chi-TW`, `chi-HK` (traditionell) | `chi-CN`, `chi-TW` vollständig; `chi-SG` fällt auf `chi-CN`, `chi-HK` auf `chi-TW` zurück |
| 3 | Hindi | 611 | `hin-IN` | `hin-IN` vollständig |
| 4 | Spanisch | 561 | `esl-ES`, `esl-MX`, `esl-AR`, `esl-CO`, `esl-CL`, `esl-PE`, `esl-US`, `esl-VE`, `esl-EC`, `esl-GT`, `esl-CU`, `esl-DO`, `esl-BO`, `esl-UY`, `esl-PY`, `esl-CR`, `esl-PA`, `esl-HN`, `esl-SV`, `esl-NI`, `esl-PR`, `esl-GQ` | `esl-ES`, `esl-MX` vollständig |
| 5 | Modernes Hocharabisch | 335 | `ara-SA`, `ara-EG`, `ara-AE`, `ara-EH`, `ara-MA`, `ara-DZ`, `ara-TN`, `ara-IQ`, `ara-JO`, `ara-LB`, `ara-SY`, `ara-KW`, `ara-QA`, `ara-BH`, `ara-OM`, `ara-LY`, `ara-PS`, `ara-YE`, `ara-SD`, `ara-MR`, `ara-DJ`, `ara-KM`, `ara-SO`, `ara-TD`, `ara-ER` | `ara-SA` vollständig; die übrigen `ara-*`-Locales fallen darauf zurück |
| 6 | Französisch | 334 | `fre-FR`, `fre-BE`, `fre-CA`, `fre-CH`, `fre-SN`, `fre-CI`, `fre-CM`, `fre-MA`, `fre-CD`, `fre-LU`, `fre-MC`, `fre-HT`, `fre-BJ`, `fre-BF`, `fre-BI`, `fre-CF`, `fre-TD`, `fre-KM`, `fre-CG`, `fre-DJ`, `fre-GQ`, `fre-GA`, `fre-GN`, `fre-MG`, `fre-ML`, `fre-NE`, `fre-RW`, `fre-SC`, `fre-TG`, `fre-VU`, `fre-DZ`, `fre-TN`, `fre-MR`, `fre-MU` | `fre-FR` vollständig |
| 7 | Bengalisch | 274 | `ben-BD`, `ben-IN` | `ben-BD` vollständig; `ben-IN` fällt darauf zurück |
| 8 | Portugiesisch | 269 | `por-PT`, `por-BR`, `por-MZ`, `por-AO`, `por-CV` | `por-BR`, `por-PT` vollständig; `por-MZ`, `por-AO`, `por-CV` fallen auf `por-PT` zurück |
| 9 | Indonesisch | 255 | `ind-ID` | `ind-ID` vollständig |
| 10 | Urdu | 246 | `urd-PK`, `urd-IN` | `urd-PK` vollständig; `urd-IN` fällt darauf zurück |
| 11 | Russisch | 210 | `rus-RU` | `rus-RU` vollständig |
| 12 | Deutsch | 133 | `ger-DE`, `ger-AT`, `ger-CH`, `ger-LU`, `ger-LI`, `ger-BE` | `ger-DE` vollständig |
| 13 | Japanisch | 126 | `jpn-JP` | `jpn-JP` vollständig |
| 14 | Nigerianisches Pidgin | 121 | `pcm-NG` | `pcm-NG` vollständig |
| 15 | Ägyptisches Arabisch | 118 | `ara-EG` (geschriebenes Arabisch) | `ara-EG` fällt auf `ara-SA` zurück |
| 16 | Marathi | 99 | `mar-IN` | `mar-IN` vollständig |
| 17 | Vietnamesisch | 97 | `vie-VN` | `vie-VN` vollständig |
| 18 | Telugu | 96 | `tel-IN` | `tel-IN` vollständig |
| 19 | Swahili | 95 | `swa-KE`, `swa-TZ` | `swa-KE` vollständig, `swa-TZ` fällt darauf zurück |
| 20 | Hausa | 94 | `hau-NG` | `hau-NG` vollständig |
| 21 | Türkisch | 94 | `tur-TR` | `tur-TR` vollständig |
| 22 | Westpandschabi | 90 | `pnb-PK` (Shahmukhi-Schrift; `pan-IN` ist Pandschabi in Gurmukhi-Schrift) | `pnb-PK` vollständig (und `pan-IN` für die Gurmukhi-Schrift) |
| 23 | Tagalog | 87 | `fil-PH` (Filipino, die Standardform des Tagalog) | `fil-PH` vollständig |
| 24 | Tamil | 86 | `tam-IN`, `tam-LK`, `tam-SG` | `tam-IN` vollständig; `tam-LK`, `tam-SG` fallen darauf zurück |
| 25 | Yue-Chinesisch (Kantonesisch) | 86 | `chi-HK` (geschriebenes traditionelles Chinesisch) | `chi-HK` fällt auf `chi-TW` zurück |
| 26 | Wu-Chinesisch | 83 | geschrieben als vereinfachtes Chinesisch: `chi-CN` | `chi-CN` vollständig |
| 27 | Persisch | 82 | `fas-IR`, `fas-AF` (Dari) | `fas-IR`, `fas-AF` vollständig |
| 28 | Koreanisch | 82 | `kor-KR`, `kor-KP` | `kor-KR`, `kor-KP` vollständig |
| 29 | Amharisch | 78 | `amh-ET` | `amh-ET` vollständig |
| 30 | Thai | 71 | `tha-TH` | `tha-TH` vollständig |
| 31 | Javanisch | 69 | `jav-ID` | `jav-ID` vollständig |
| 32 | Italienisch | 66 | `ita-IT`, `ita-CH` | `ita-IT` vollständig |
| 33 | Gujarati | 62 | `guj-IN` | `guj-IN` vollständig |
| 34 | Kannada | 59 | `kan-IN` | `kan-IN` vollständig |
| 35 | Levantinisches Arabisch | 58 | geschriebenes Arabisch: `ara-JO`, `ara-LB`, `ara-SY` | `ara-JO`, `ara-LB`, `ara-SY` fallen auf `ara-SA` zurück |
| 36 | Sudanesisches Arabisch | 54 | geschriebenes Arabisch: `ara-*` | `ara-*` fallen auf `ara-SA` zurück |
| 37 | Yoruba | 53 | `yor-NG` | `yor-NG` vollständig |
| 38 | Bhojpuri | 53 | `bho-IN` | `bho-IN` vollständig |

Alle 38 haben eine Locale (eine gesprochene Varietät nach ihrer Schriftsprache gezählt; vor 6.0.15 hatten 18 von
ihnen eine) und eine vollständige Übersetzung der Administrationsoberfläche: eine eigene oder, bei den arabischen
Varietäten, die von `ara-SA`.

Darüber hinaus deckt die A–Z-Liste fast jede Sprache mit einer Schriftnorm und CLDR-Daten ab: die Amtssprachen
Indiens (alle 22 Sprachen des Achten Anhangs der Verfassung, darunter Maithili, Konkani, Manipuri, Santali, Dogri, Bodo
und Sanskrit), die großen Sprachen Afrikas (Fulfulde, Akan, Ewe, Wolof, Bambara, Lingala, Kirundi, Luganda, Kikuyu,
Kabylisch, Tamazight, N'Ko), Zentralasiens (Turkmenisch, Kirgisisch, Tatarisch, Tschetschenisch) und des Himalaya
(Tibetisch, Dzongkha), die keltischen und anderen Regionalsprachen Europas (Bretonisch, Schottisch-Gälisch,
Färöisch, Rätoromanisch, Westfriesisch, Okzitanisch, Asturisch, Nordsamisch, Ober- und Niedersorbisch) sowie Maori,
Hawaiisch, Tongaisch, Grönländisch, Sundanesisch und Cebuano.

## 6. Verbreitete Sprachen, die noch fehlen

Jede Sprache der Tabelle oben hat eine Locale. Sprachen mit einer Million oder mehr Sprechern, die noch keine haben:

- **Keine Daten in ICU 74 (CLDR 44)**: Haitianisches Kreolisch (`hat`), Setswana (`tsn`), Sesotho (`sot`),
  Nord-Sotho (`nso`), Xitsonga (`tso`), Siswati (`ssw`), Tshivenda (`ven`) und Süd-Ndebele (`nbl`) (Amtssprachen
  Südafrikas), Chichewa (`nya`), Guaraní (`grn`), Aymara (`aym`), Baschkirisch (`bak`), Ilokano (`ilo`), Hmong
  (`hmn`), Inuktitut (`iku`), Samoanisch (`smo`), Korsisch (`cos`), Tok Pisin (`tpi`), Fidschianisch (`fij`) und
  Belutschisch (`bal`). Jede braucht eine von Hand erstellte Datei (wie Latein und Dhivehi sie haben), am besten von
  einem Sprecher geschrieben oder geprüft; CLDR 45 und spätere Versionen fügen einige davon hinzu, und ein neueres ICU
  auf dem Build-Rechner würde diese erzeugen.
- **Mit CLDR-Daten, vorerst weggelassen** (klein oder eine zweite Schrift einer Sprache, die eine Locale hat): Manx
  (`glv`), Kornisch (`cor`), Schweizerdeutsch (`gsw`, in Standarddeutsch geschrieben), Friaulisch (`fur`), Sardisch
  (`srd`), Ligurisch, Venetisch, Schlesisch, Cherokee (`chr`), Nheengatu, Ossetisch (`oss`), Jakutisch (`sah`),
  Tschuwaschisch (`chv`), Zentralatlas-Tamazight, Sango (`sag`), Masanderanisch und Nord-Luri (CLDR hat für beide
  englische Tagesnamen), Fulfulde in Adlam-Schrift, Usbekisch in arabischer Schrift, Interlingua (nur für die Region
  „Welt“, siehe [Abschnitt 3](#3-alle-locales)).
- **Weitere regionale Varianten**: Englisch von Hongkong und Malaysia sowie Schwedisch von Finnland. Ein Land, das
  eine Locale in einer anderen seiner Sprachen hat, ist auf der Karte trotzdem grün.
- Gesprochene Varietäten, die in einer Standardsprache geschrieben werden (arabische Dialekte, Wu- und
  Yue-Chinesisch), brauchen keine eigene Locale.

Jede davon mit CLDR-Daten ist nur eine Zeile im Generator entfernt; siehe den nächsten Abschnitt.

## 7. Eine Locale hinzufügen

### Die Datei

`share/locale/<language>-<COUNTRY>.ini`, UTF-8, erste Zeile `#?ini charset="utf-8"?`. `eZLocale` liest diese
Gruppen und Schlüssel (`lib/ezlocale/classes/ezlocale.php`, `initCountry()` und `initLanguage()`):

| Gruppe | Schlüssel | Bedeutung |
|---|---|---|
| `[RegionalSettings]` | `Country` | Name des Landes (in den meisten Dateien auf Englisch). |
| | `LanguageName` | Name der Sprache in der Sprache selbst; mit dem Land in Klammern, wo eine Sprache mehrere Locales hat: `Deutsch (Österreich)`. Wird als Eigenname angezeigt. |
| | `InternationalLanguageName` | Englischer Name, ebenso: `German (Austria)`. Der Name, den eine Inhaltssprache beim Hinzufügen erhält. |
| | `LanguageComment`, `CountryComment` | Optional; die Beschreibung einer Variante (`Euro support`). |
| `[LanguageSettings]` | `TextDirection` | `rtl` für eine Sprache, die von rechts nach links geschrieben wird, gelesen von `eZLocale::textDirection()`. Nur die Dateien für Rechts-nach-links-Sprachen haben ihn; eine Datei ohne ihn ist links nach rechts. Siehe [Abschnitt 9](#9-sprachen-die-von-rechts-nach-links-geschrieben-werden). |
| `[Charset]` | `Preferred`, `Allowed[]` | Zeichensatz. Neue Dateien verwenden `utf-8`; ältere Dateien nennen alte Zeichensätze, die eine UTF-8-Website ignoriert. |
| `[HTTP]` | `ContentLanguage` | Das BCP-47-Tag, das als `lang` der Seite ausgegeben wird: Sprache nach ISO 639-1 (oder der Code aus drei Buchstaben, wo es keinen gibt), Land nach ISO 3166 (`de-AT`). |
| `[Currency]` | `Symbol`, `Name`, `ShortName` | Symbol, Name und Code nach ISO 4217 der Währung des Landes. |
| | `DecimalSymbol`, `ThousandsSeparator`, `FractDigits` | Zahlenformat von Beträgen; `FractDigits=0` für Währungen ohne Untereinheit (JPY, KRW, VND, IRR). |
| | `PositiveSymbol`, `NegativeSymbol`, `PositiveFormat`, `NegativeFormat` | Vorzeichen und Anordnung: `%c` Symbol, `%p` Vorzeichen, `%q` Zahl. `%c %p%q` ergibt `€ 1.234,50`, `%p%q %c` ergibt `1.234,50 €`. |
| `[Numbers]` | `DecimalSymbol`, `ThousandsSeparator`, `FractDigits`, `PositiveSymbol`, `NegativeSymbol` | Zahlenformat. Jedes Trennzeichen muss ein einzelnes Byte sein (ein normales Leerzeichen, kein geschütztes Leerzeichen), weil `eZLocale::internalNumber()` es in einem regulären Ausdruck verwendet. |
| `[DateTime]` | `TimeFormat`, `ShortTimeFormat`, `DateFormat`, `ShortDateFormat`, `DateTimeFormat`, `ShortDateTimeFormat` | Formate in Buchstaben von PHP `date()` hinter `%`: `%d`/`%j` Tag, `%m`/`%n` Monat, `%Y`/`%y` Jahr, `%H`/`%G` 24 Stunden, `%h`/`%g` 12 Stunden, `%i` Minuten, `%s` Sekunden, `%a`/`%A` am/pm; und die Namen der Locale: `%l` langer Tag, `%D` kurzer Tag, `%F` langer Monat, `%M` kurzer Monat. Andere Buchstaben sind wörtlicher Text. |
| | `MondayFirst` | `yes`, wenn die Woche am Montag beginnt, sonst beginnt sie am Sonntag (`eZLocale` kennt keinen Wochenbeginn am Samstag oder Freitag). |
| `[ShortMonthNames]`, `[LongMonthNames]` | `jan` ... `dec` | Monatsnamen, in der Form, die innerhalb eines Datums verwendet wird (Genitiv in slawischen Sprachen: `января`). |
| `[ShortMonthNamesStandalone]`, `[LongMonthNamesStandalone]` | `jan` ... `dec` | Optional. Monatsnamen, wie sie für sich allein lauten, in einer Kalenderüberschrift oder einer Monatsliste (`январь`). `eZLocale::longMonthNameStandalone()`, `shortMonthNameStandalone()` und das Template-Attribut `month_name_standalone_list` lesen sie und fallen Monat für Monat auf die Gruppe darüber zurück. |
| `[ShortDayNames]`, `[LongDayNames]` | `mon` ... `sun` | Tagesnamen. |
| `[CountryNames]` | `Countries[XX]` | Ländernamen in der Sprache, für den Datentyp Land. Optional. |

CLDR kennt für einen Monat zwei Formen: die Formatform (Muster `MMMM`), innerhalb eines Datums, und die
alleinstehende Form (`LLLL`), wo der Monat für sich steht. Im Englischen sind sie gleich; in Sprachen, die den
Monatsnamen beugen, nicht (Polnisch `15 października 2025`, aber `październik 2025` über einem Kalender), und auch
manche Abkürzungen unterscheiden sich (österreichisches Deutsch `März` im Datum, `Mär` allein). Eine Datei braucht
dann die alleinstehenden Gruppen. Katalanisch nimmt die Präposition in die Formatform auf (`d’octubre`, `de març`);
ein eigenes Format, das `%F` allein ausgibt, zeigt sie deshalb auch dort. Für den alleinstehenden Namen gibt es keinen
`%`-Buchstaben, dafür `month_name_standalone_list` oder `longMonthNameStandalone()`.

### Der Code

Drei Buchstaben für die Sprache, ein Bindestrich, das Land nach ISO 3166 in zwei Buchstaben: `ben-BD`. Das Land muss
aus zwei Buchstaben bestehen: `eZLocale::localeRegexp()` erlaubt dort nur Buchstaben, daher lassen sich die
numerischen Regionen von CLDR (`001` „Welt“, `419` „Lateinamerika“) nicht verwenden, und eine Sprache, die CLDR nur
für die Welt hat, nimmt das Land ihrer größten Gemeinschaft (`yid-US`) oder einen benutzerdefinierten Code nach
ISO 3166 (`tlh-QO`).

Der Sprachteil folgt den Codes, die Exponential schon immer verwendet hat: `eng`, `ger` (Deutsch), `fre`
(Französisch), `esl` (Spanisch), `chi` (Chinesisch), `dut` (Niederländisch), `cze`, `cro`, `ita`, `jpn`, `por`, `rus`,
`ara` und die übrigen der älteren Dateien; eine regionale Variante verwendet diesen Code wieder (`ger-AT`, `esl-AR`,
`chi-SG`, `ara-EG`, `fre-SN`). Eine Sprache ohne bestehenden Code erhält ihren Code nach ISO 639-2/T (den
terminologischen, nicht den bibliografischen): `ben` Bengalisch, `urd` Urdu, `vie` Vietnamesisch, `fas` Persisch,
`ron` Rumänisch, `msa` Malaiisch, `hye` Armenisch, `kat` Georgisch, `sqi` Albanisch, `mkd` Mazedonisch, `isl`
Isländisch, `cym` Walisisch, `eus` Baskisch, `bod` Tibetisch, `mri` Maori, `fry` Westfriesisch. `fil` (Filipino) hat
keinen anderen Code nach 639-2/T als diesen; eine Sprache ganz ohne Code nach ISO 639-2 nimmt ihren Code nach
ISO 639-3 (`ckb` Zentralkurdisch, `pcm` Nigerianisches Pidgin, `pnb` Westpandschabi, `brx` Bodo). Eine Variante einer
Locale ist `<code>@<name>.ini` und enthält nur die Schlüssel, die sie ändert.

### Wie die Dateien von 6.0.15 entstanden sind

Sie wurden, nur zur Build-Zeit, aus Unicode CLDR 44 über die PHP-Extension `intl` (ICU 74.2) erzeugt, mit dem
gregorianischen Kalender und lateinischen Ziffern, von einem Generator, der eine Liste aus Exponential-Code,
ICU-Locale und Region entgegennimmt:

- Namen aus den Anzeigenamen von CLDR (englisch und in Eigensprache), das HTTP-Tag aus der ICU-Locale;
- die Währung des Landes, ihr Symbol, ihr Name und ihre Dezimalstellen; Trennzeichen auf ein Byte reduziert (ein
  geschütztes Leerzeichen wird zu einem Leerzeichen, `’` wird zu `'`); ein Symbol, das in CLDR unsichtbar ist (der
  Kap-Verde-Escudo, dessen Cifrão das Dezimaltrennzeichen ist), wird zum ISO-Code (`CVE`);
- das vollständige Datumsmuster für `DateFormat`, das kurze für `ShortDateFormat` mit vierstelliger Jahreszahl, die
  mittleren und kurzen Zeitmuster; `DateTimeFormat` ist Datum und Uhrzeit, durch ein Leerzeichen verbunden; Ären,
  Zeitzonen und Quartale, die `eZLocale` nicht ausgeben kann, sind weggelassen;
- eine 12-Stunden-Uhr, wo CLDR eine verwendet und ihre Kennzeichnung `AM`/`am` ist; wo die Kennzeichnung ein anderes
  Wort ist (`ص`, `上午`, `a.m.`), verwendet die Datei die 24-Stunden-Uhr, weil `eZLocale` nur `am`/`pm` ausgibt, und
  sagt das in einem Kommentar;
- Tages- und Monatsnamen im Formatkontext, der erste Tag der Woche und die Ländernamen;
- später, aus ICU 78 (CLDR 48), die alleinstehenden Monatsgruppen, in jeder Datei, deren Monatsnamen die Formatnamen
  von CLDR sind und wo sich die alleinstehenden Namen von CLDR unterscheiden: slawisch (`pol-PL`, `cze-CZ`, `slk-SK`,
  `cro-HR`, `rus-RU`, `ukr-UA`, `bel-BY`, `dsb-DE`, `hsb-DE`), baltisch (`lit-LT`), Griechisch, Finnisch, Armenisch,
  Katalanisch, Asturisch, Rätoromanisch, Schottisch-Gälisch, Grönländisch und weitere (51 Dateien:
  `grep -l MonthNamesStandalone share/locale/*.ini`). Eine Datei, deren Monatsnamen nicht die Formatnamen von CLDR
  sind, behält eine einzige Form, bis sie korrigiert ist;
- englische Namen, die denen von CLDR vorgezogen werden, wo Leser nach einem anderen suchen: `Bengali` (CLDR: Bangla),
  `Kirundi` (Rundi), `Greenlandic` (Kalaallisut), `Simplified Chinese (Singapore)` (passend zu `chi-CN`), `Western
  Punjabi (Shahmukhi)` und `Dari`;
- das Land in Klammern, sobald eine Sprache zwei Locales hat: Das Hinzufügen von `msa-SG` und `tam-LK` benannte
  `msa-MY` in `Malay (Malaysia)` und `tam-IN` in `Tamil (India)` um (eine bestehende Inhaltssprache behält den Namen,
  den sie erhalten hat);
- `[LanguageSettings] TextDirection=rtl`, wo ICU die Locale von rechts nach links schreibt (und der Kommentar zur
  Schreibrichtung von rechts nach links);
- für Sprachen, die ICU 74 fehlen, ein **von Hand erstellter** Teil: Latein (Konventionen von `it_VA`) und Dhivehi
  (Konventionen von `en_MV`) erhalten ihre Namen von Hand im Generator geschrieben und sagen das im Dateikopf (siehe
  [Abschnitt 3](#3-alle-locales)).

Jede erzeugte Datei nennt in ihren ersten Zeilen ihre Quelle und Lizenz: CLDR ist Copyright © Unicode, Inc., unter
der [Unicode License v3](https://www.unicode.org/license.txt).

**Von Hand erstellte Dateien** werden vom Generator nie überschrieben. Neben den älteren Dateien ist Klingonisch
(`tlh-QO`) vollständig von Hand erstellt: Sein Kopf nennt seine Quellen (The Klingon Dictionary, das Klingon Language
Institute) und jede Entscheidung. Eine ältere Datei wird nur neu erzeugt, wenn sie fehlerhaft ist, eine nach der
anderen (`heb-IL`, `hin-IN`, `kor-KR` und `ara-SA`, das Marokko als sein Land, den marokkanischen Dirham als seine
Währung, ISO-8859-1 als seinen Zeichensatz und eine 12-Stunden-Uhr mit lateinischem am/pm nannte).

Um eine weitere Locale mit CLDR-Daten hinzuzufügen, erzeugen Sie sie auf dieselbe Weise mit `intl`; ohne CLDR-Daten
kopieren Sie eine ähnliche Datei und ändern sie von Hand. Prüfen Sie sie dann wie unten beschrieben.

### Die Prüfung

`tests/tests/lib/ezlocale/expLocaleFilesTest.php` (ohne Datenbank, in der Suite `lib`) lädt jede
`share/locale/*.ini` über `eZINI` und `eZLocale` und schlägt fehl, wenn eine Datei kein gültiges UTF-8 ist, ihr ein
Schlüssel aus der Tabelle oben fehlt, sie einen leeren Tages- oder Monatsnamen hat, ein Datum, eine Uhrzeit, eine Zahl
oder eine Währung nicht formatieren oder ihre eigene Zahl nicht zurücklesen kann. Für die erzeugten Dateien prüft er
außerdem die Form des Codes, `utf-8`, das HTTP-Tag, zwölf verschiedene Monate, sieben verschiedene Tage,
Ein-Byte-Trennzeichen, die Lizenzzeile und den Hinweis zur Schreibrichtung von rechts nach links. Für jede Datei prüft
er, dass `TextDirection=rtl` genau dann vorhanden ist, wenn die Sprache von rechts nach links geschrieben wird, und
dass keine Datei doppelt kodiertes UTF-8 enthält; die Dateien für Latein, Hebräisch und Klingonisch werden Wert für
Wert geprüft, und jede Rechts-nach-links-Locale und jede Weltsprache, die in 6.0.15 hinzukam, muss als Inhaltssprache
angeboten werden. `expLocaleTextDirectionTest` deckt `eZLocale::textDirection()`, `isRightToLeft()`,
`textDirectionOf()`, `normalizeTextDirection()` und die Template-Attribute ab. `eZLocaleFormattingTest` prüft
ebenfalls jede Datei. `expLocaleStandaloneMonthNamesTest` prüft, dass Datumsangaben die Formatform verwenden und
`longMonthNameStandalone()` die alleinstehende Form, den Rückfall auf die Formatform und dass jede alleinstehende
Gruppe zwölf Namen hat und sich von ihrer Formatgruppe unterscheidet.

```bash
vendor/bin/phpunit tests/tests/lib/ezlocale
vendor/bin/phpunit tests/tests/kernel/classes/uiswitch/expUISwitchLanguageProviderTest.php
```

Eine neue Locale wird sofort unter Setup > Sprachen > Sprache hinzufügen und in der Wahl der Oberflächensprache
angeboten; kein Cache muss geleert werden (der Katalog der Auswahl ist nach Namen, Größen und Zeitstempeln der Dateien
verschlüsselt).

## 8. Eine Übersetzung der Administrationsoberfläche hinzufügen oder vervollständigen

1. **Texte.** `share/translations/untranslated/translation.ts` enthält jeden Quelltext. Sie wird von `ezlupdate`
   erzeugt, gebaut aus `support/ezlupdate-qt4.5` (Qt 4.5, `qmake ezlupdate.pro && make`, Binärdatei in `bin/linux`):
   `bin/linux/ezlupdate -u` aktualisiert die unübersetzte Datei, `bin/linux/ezlupdate <code>` fügt die neuen Texte zu
   einer Übersetzung hinzu und markiert die verschwundenen als veraltet. Extensions:
   `ezlupdate -e extension/<name> <code>`.
2. **Eine neue Übersetzung.** Legen Sie `share/translations/<code>/` mit dem Code der Locale an und kopieren Sie die
   unübersetzte `translation.ts` hinein. Ändern Sie das Attribut `language` von `<TS>` auf den Code.
3. **Übersetzen** Sie mit Qt Linguist oder einem beliebigen XML-Editor. Eine Meldung zählt, wenn ihr `<translation>`
   Text hat und kein `type="unfinished"`. Behalten Sie Platzhalter (`%1`, `%name`), HTML-Entitäten und
   Tastaturhinweise genau wie in der Quelle bei; schreiben Sie in `eng-US` amerikanisches Englisch (license, color,
   behavior).
4. **Messen.** `php bin/php/ezchecktranslation.php <code> --allow-root-user` gibt den fertigen Anteil der Datei aus.
   Der Vergleich mit der unübersetzten Datei, wie in [Abschnitt 4](#4-alle-übersetzungen-der-administrationsoberfläche), zeigt, was
   für das aktuelle Release fehlt.
5. **Prüfen.** Ein zweiter Sprecher liest das Ergebnis in der laufenden Administrationsoberfläche: ein Siteaccess
   (oder die Einstellung eines Benutzers) mit `Locale=<code>`, nachdem der Übersetzungs-Cache geleert wurde
   (`php bin/php/ezcache.php --clear-tag=i18n --allow-root-user`).

**Übersetzungsleitfäden.** `share/translations/TRANSLATING.md` enthält die Regeln, die für jede Sprache gelten
(Platzhalter, Markup, Wörter, die ihren Kontext brauchen, Prüfen); jede vollständige Sprache hat neben ihrem Katalog
eine `GUIDE.md` (`share/translations/<code>/GUIDE.md`) mit ihrer Anrede, Schrift und Typografie und einem Glossar,
das für den Kernel und jede Extension verbindlich ist. Beide auf Englisch. Vor einer Änderung an einem Katalog beide
lesen, und einen Begriff zuerst im Leitfaden ändern.

**Qualitätstest.** `tests/tests/lib/ezi18n/expTranslationQualityTest.php` (keine Datenbank nötig) prüft die
maschinell unterstützten Kataloge und die von ezoe und ezjscore: Eine fertige Übersetzung behält die Platzhalter,
HTML-Tags und Entitäten, führende und abschließende Leerzeichen und die Zeilenumbrüche ihrer Quelle und ist weder leer
noch der englische Text selbst. Wo der englische Text richtig ist (ein Produktname, ein Code, ein gleich geschriebenes
Wort), fügen Sie ihn mit Begründung zu `tests/tests/lib/ezi18n/translation-identical-allowlist.json` hinzu;
schwächen Sie den Test nie ab. Eine neu vervollständigte Sprache kommt auf die Liste der Sprachen des Tests; jede
vollständige maschinell unterstützte Übersetzung steht darauf. Deutsch (`ger-DE`) steht seit 6.0.15 darauf, mit den
Katalogen des Kernels, von ezoe und von ezjscore; die Wörter, die das Deutsche aus dem Englischen übernimmt (Cache,
Layout, Login, Siteaccess, Extension ...), stehen in der Liste `ger-DE` der Allowlist.

Jede Übersetzung deckt die Texte von 6.0.15 ab; die nützlichste Arbeit ist daher jetzt die Durchsicht der maschinell
unterstützten durch Muttersprachler, das heißt jeder Übersetzung außer `eng-US` und `ger-DE`. Siehe
[Einen Übersetzungsfehler melden](#einen-übersetzungsfehler-melden).

## 9. Sprachen, die von rechts nach links geschrieben werden

43 Locale-Dateien werden von rechts nach links geschrieben:

| Sprache | Locales | Schrift |
|---|---|---|
| Arabisch | `ara-SA`, `ara-EG`, `ara-AE`, `ara-MA`, `ara-DZ`, `ara-TN`, `ara-IQ`, `ara-JO`, `ara-LB`, `ara-SY`, `ara-KW`, `ara-QA`, `ara-BH`, `ara-OM`, `ara-LY`, `ara-PS`, `ara-YE`, `ara-SD`, `ara-MR`, `ara-DJ`, `ara-KM`, `ara-SO`, `ara-TD`, `ara-ER` | Arabisch |
| Hebräisch | `heb-IL` | Hebräisch |
| Jiddisch | `yid-US` | Hebräisch |
| Reichsaramäisch | `arc-IQ` | Hebräisch |
| Hulaulá (jüdisches Neuaramäisch) | `huy-IL` | Hebräisch |
| Persisch, Dari | `fas-IR`, `fas-AF` | Arabisch |
| Urdu | `urd-PK`, `urd-IN` | Arabisch |
| Paschtu | `pus-AF` | Arabisch |
| Westpandschabi | `pnb-PK` | Arabisch (Shahmukhi) |
| Sindhi | `snd-PK` | Arabisch |
| Kaschmiri | `kas-IN` | Arabisch |
| Uigurisch | `uig-CN` | Arabisch |
| Zentralkurdisch (Sorani) | `ckb-IQ` | Arabisch |
| Syrisch (Aramäisch) | `syr-IQ`, `syr-SY` | Syrisch |
| N’Ko | `nqo-GN` | N’Ko |
| Dhivehi | `div-MV` | Thaana |

Die Richtung wird durch die Zeichenreihenfolge der Locale laut ICU bestimmt (und gegen die Liste der
Rechts-nach-links-Sprachen des Generators geprüft), daher sind Kurdisch (`kur-TR`, Kurmandschi) und Usbekisch
(`uzb-UZ`), die die lateinische Schrift verwenden, links nach rechts, ebenso wie Fulfulde (`ful-SN`) und Tamazight
(`zgh-MA`, Tifinagh).

**Die Locale sagt es.** Jede dieser Dateien hat

```ini
# Text direction: right-to-left ([LanguageSettings] TextDirection, see "Right-to-left languages" in doc/eng-us/languages.md).
[LanguageSettings]
TextDirection=rtl
```

und keine andere Datei hat den Schlüssel (siehe [Abschnitt 7](#7-eine-locale-hinzufügen)). Eine Datei ohne ihn ist links nach
rechts.

**eZLocale liest es.** `eZLocale::textDirection()` gibt `rtl` oder `ltr` zurück, `isRightToLeft()` dasselbe als
Boolean, und das statische `eZLocale::textDirectionOf( 'heb-IL' )` liefert die Richtung jedes Locale-Codes (`ltr` für
einen unbekannten). Templates erreichen sie als `$locale.text_direction` und `$locale.is_right_to_left`, zum Beispiel
über das `locale_object` einer Inhaltssprache. Nur `rtl` und `right-to-left` (in beliebiger Schreibweise) gelten als
rechts nach links; `eZLocale::normalizeTextDirection()` macht aus jedem anderen Wert `ltr`.

**Seiten und Inhalte.** Das Seitenlayout erhält `$site.text_direction` (`rtl` oder `ltr`), die Richtung der
Siteaccess-Locale, für das Attribut `dir` von `<html>`. Inhalte in einer Rechts-nach-links-Sprache werden mit
`dir="rtl"` angezeigt, auch auf einer Links-nach-rechts-Seite, sodass sich eine hebräische Übersetzung innerhalb einer
englischen Website richtig liest.

**Adressen.** Die ausgelieferte Transformation für URL-Aliase `urlalias` hat keine Transliterationstabelle für die
arabische Schrift, deshalb wird ein arabischer, persischer, paschtunischer oder Urdu-Titel stattdessen mit
`urlalias_iri` umgewandelt (`دليل المستخدم` wird zu `دليل-المستخدم`, nicht zu `node_<ID>`); Hebräisch wird wie bisher
transliteriert. Die Regel und die Einstellung `[URLTranslator] ScriptFallbackTransformationGroup` stehen unter
[Titel in Schriften ohne Transliterationstabelle](anleitungen/urls-und-aliase.md#10-titel-in-schriften-ohne-transliterationstabelle).

**Die Administrationsoberfläche.** Die Seitenlayouts des Admin setzen `dir="rtl"`, wenn die Oberflächensprache von
rechts nach links geschrieben wird, und ihr Layoutgerüst (Kopf, linkes Menü, Inhalt, rechte Werkzeugleiste) ist
richtungssicher: Der Browser spiegelt es, statt dass es über links und rechts platziert wird. Die Einzelheiten, die
CSS-Regeln und was noch zu prüfen ist, stehen unter
[Sprachen, die von rechts nach links geschrieben werden](funktionen/6.0/rechts-nach-links.md).

**Übersetzungen der Oberfläche.** Jede Rechts-nach-links-Locale zeigt die Administration übersetzt. Siebzehn haben
einen eigenen Katalog: Hebräisch (`heb-IL`), Arabisch (`ara-SA`, modernes Hocharabisch, auf das jedes arabische Land
zurückfällt), Persisch (`fas-IR`), Dari (`fas-AF`), Paschtu (`pus-AF`), Urdu (`urd-PK`, auf das `urd-IN`
zurückfällt), Sindhi (`snd-PK`), Westpunjabi (`pnb-PK`, Shahmukhi-Schrift), Kaschmiri (`kas-IN`), Uigurisch
(`uig-CN`), Zentralkurdisch (`ckb-IQ`), Jiddisch (`yid-US`), N’Ko (`nqo-GN`), Dhivehi (`div-MV`) und Aramäisch,
einmal in syrischer Schrift (`syr-IQ`, auf das `syr-SY` zurückfällt) und zweimal in hebräischer Schrift (`arc-IQ`
und `huy-IL`, daraus transliteriert). Alle decken den Kernel und die Kataloge der mitgelieferten ezoe und ezjscore
ab, und alle sind maschinell unterstützt entstanden; sie brauchen daher eine Prüfung durch einen Muttersprachler,
bevor sich eine Website auf sie verlässt
([Abschnitt 8](#8-eine-übersetzung-der-administrationsoberfläche-hinzufügen-oder-vervollständigen)). Die Wahl
einer davon als Oberflächensprache stellt die gesamte Administrationsoberfläche (admin4l, admin4 und das
klassische Admin) auf rechts nach links um. Ihre Platzhalter (`%name`, `%1`) bleiben innerhalb des
Rechts-nach-links-Textes, wie sie sind; der bidirektionale Algorithmus des Browsers platziert sie, sodass eine
Übersetzung keine eigenen Richtungszeichen braucht. Der Übersetzer kennt keine Pluralformen (keine
`numerus`-Meldungen), daher schreibt man eine Anzahl am besten neutral formuliert statt im Plural.
`expTranslationQualityTest` prüft Platzhalter, Markup und unübersetztes Englisch (mit den Begründungen in
`tests/tests/lib/ezi18n/translation-identical-allowlist.json`), und `expAdminTranslationsRegisteredTest` prüft, dass
jeder Katalog vollständig, registriert und in seiner Schrift geschrieben ist und dass ein Wort mit zwei Bedeutungen
(„Store“ der Shop-Reiter, „Login“ der Benutzername) die richtige erhält.

## 10. Keine PHP-Extension zur Laufzeit

Locales sind reine INI-Dateien, die von `eZINI` und `eZLocale` in PHP gelesen werden: kein `intl`, kein Pfad, der nur
mit `mbstring` funktioniert, kein ICU und nichts, das von 64-Bit-PHP abhängt. `intl` wurde nur verwendet, um die
Dateien von 6.0.15 zu erzeugen (die Wahl der Oberflächensprache verwendet es, wenn vorhanden, für Namen in der Sprache
des Lesers und zum Sortieren). Die Übersetzungen der Administrationsoberfläche sind XML, das von `eZTSTranslator`
gelesen und als PHP gecacht wird. (Die Anzahl der Inhaltssprachen ist eine andere Grenze: 62 auf 64-Bit-PHP, 30 auf
32-Bit, siehe [Abschnitt 11](#11-die-grenze-von-62-inhaltssprachen).)

## 11. Die Grenze von 62 Inhaltssprachen

Eine Installation enthält höchstens **62 Inhaltssprachen auf 64-Bit-PHP** (30 auf 32-Bit-PHP;
`eZContentLanguage::maxCount()` ist `8 * PHP_INT_SIZE - 2`). Setup > Sprachen zeigt, wie viele in Gebrauch sind und
wie viele noch hinzugefügt werden können; das Formular zum Hinzufügen sagt es in seiner Einleitung und weist eine
Sprache über der Grenze mit einer Meldung zurück, die erklärt, wie man einen Platz freimacht.

**Warum.** Jede Inhaltssprache ist ein Bit. Ihre ID ist eine Zweierpotenz (2, 4, 8, 16 ...), und ein Objekt, eine
Version, eine Klasse und ein Objektzustand speichern alle ihre Sprachen in einer Ganzzahl, `language_mask`; ein
Attribut oder eine Namenszeile trägt das einzelne Bit seiner Sprache in `language_id`. Bit 0 (Wert 1) ist reserviert:
Es ist das Kennzeichen „immer verfügbar“ in derselben Maske. Die Ganzzahl von PHP und `BIGINT` der Datenbank sind
vorzeichenbehaftete 64-Bit-Zahlen, daher lassen das Vorzeichenbit und Bit 0 62 Bits für Sprachen übrig. „Welche
Objekte gibt es auf Deutsch?“ ist dann ein Bittest pro Zeile (`language_mask & 8 > 0`), und das macht das Filtern
nach Sprache in jeder Abfrage des Kernels günstig.

**Was zählt.** Nur die unter Setup > Sprachen hinzugefügten Inhaltssprachen, die in `ezcontent_language`. Die Locales
in `share/locale` zählen nicht: Alle können angeboten werden, während eine Installation zwei Sprachen verwendet.
Eine Sprache, die hinzugefügt und nie verwendet wurde, belegt trotzdem ihr Bit.

**Einen Platz freimachen.** Entfernen Sie eine Inhaltssprache, in der kein Objekt und keine Klasse eine Übersetzung
hat (Setup > Sprachen, die Seite der Sprache; [Abschnitt 9 von Inhaltssprachen](anleitungen/inhaltssprachen.md)
erklärt, was ein Entfernen verhindert und wie Sie Inhalte zuerst aus einer Sprache herausverschieben). Ihr Bit wird
frei, und die nächste hinzugefügte Sprache erhält das niedrigste freie.

**Warum die Grenze nicht angehoben wird.** Mehr als 62 Sprachen bräuchten eine andere Speicherung der Sprachmenge:
eine Verknüpfungstabelle (Objekt, Version, Sprache) oder zusätzliche Maskenspalten (`language_mask_2` ...) in jeder
Tabelle, die eine hat, wobei jede Abfrage, die eine Maske prüft, entsprechend umgeschrieben werden müsste. Beides
ändert das Datenbankschema und das SQL, das eigener Code, Extensions und Berichte heute gegen `language_mask` und
`language_id` ausführen, ein Bruch der Abwärtskompatibilität für jede Installation, um einen Fall zu bedienen, den
keine bekannte Website erreicht hat. Daher wird es nicht getan; eine Website, die mehr als 62 Sprachen braucht, wäre
ein eigenes Projekt.

## 12. Testsprachen: Lorem Ipsum

`share/locale/qlo-ZZ.ini` ist **Lorem Ipsum**, eine Sprache zum Testen von Inhaltsübersetzungen und für nichts
anderes. Sie wird nicht zu den Sprachen oben gezählt, und keine Installation fügt sie hinzu: weder die sauberen
Daten noch die Seeds des Installers, die Pakete oder der Kickstart. Setup > Sprachen > Sprache hinzufügen bietet sie
wie jede andere Locale an, unter ihrem schlichten Namen *Lorem Ipsum*; fügen Sie sie für einen Test hinzu und
entfernen Sie sie danach. Wie: [Die Testsprache Lorem
Ipsum](anleitungen/testsprache-lorem-ipsum.md).

**Der Code.** `qlo` liegt im Bereich `qaa`-`qtz` von ISO 639-2, den ISO für die lokale Nutzung reserviert, daher kann
er nie mit einer realen Sprache kollidieren; `ZZ` ist der Code nach ISO 3166 / CLDR für eine unbekannte Region, und
das HTTP-Tag `qlo-ZZ` ist ein gültiges BCP-47-Tag zur privaten Nutzung. Der Code hat die übliche Form (drei
Buchstaben, ein Bindestrich, zwei Buchstaben), sodass `eZLocale`, das Formular zum Hinzufügen und
`ezcontent_language.locale` ihn unverändert akzeptieren.

**Die Datei.** Englische Zahlen- und Datumsformate (`1,234.50`, kurze ISO-Datumsangaben, Montag zuerst), Monats- und
Tagesnamen im Lorem-Stil (*Ipsumdies 14 Temporember 2023*), Währung `¤` mit dem Code `XXX` nach ISO 4217 (keine
Währung), keine Ländernamen, UTF-8. Sie ist von Hand geschrieben.

**Die Kennzeichnung.** `[LanguageSettings] TestLanguage=true` macht eine Locale zur Testsprache
(`expTestLanguage::isTestLocale()`). Das ändert zwei Dinge:

- Die Wahl der Oberflächensprache (`uiswitch.ini [UISwitch_language]`) setzt eine Testsprache nie in eine Ranggruppe
  oder in die A-Z-Liste von `AllLanguages=enabled`; nur wenn sie in `AllowedValues[]` genannt wird, wird sie
  angeboten, und dann in A-Z;
- `exp:loremipsum` (`bin/php/exploremipsumfill.php`) füllt nur Testsprachen mit Lorem-Ipsum-Text.

Eine eigene Locale im Bereich `qaa`-`qtz` muss die Kennzeichnung tragen, und nur solche dürfen es;
`tests/tests/kernel/classes/expTestLanguageTest.php` prüft beides, die Datei selbst, dass keine Standardinstallation
`qlo-ZZ` nennt, und den Text, den `exp:loremipsum` pro Datentyp schreibt. `expUISwitchTestLanguageTest` prüft die Wahl
der Oberflächensprache.

## 13. Übersetzungs-Fallbacks

Eine Locale ohne eigene Übersetzung bleibt nicht auf Englisch: Der Übersetzer (`eZTSTranslator`) probiert die
Übersetzungen, auf die sie **zurückfällt**, der Reihe nach, für jede Übersetzungswurzel getrennt (das
`share/translations` des Kernels, dann das Verzeichnis `translations` jeder Extension in `site.ini [RegionalSettings]
TranslationExtensions[]`). Die einzige maßgebliche Quelle für diese Reihenfolge ist `expI18nFallbackResolver`
(`lib/ezi18n/classes/`); die Wahl der Oberflächensprache, `./console exp:i18n` und Setup > Oberflächensprachen
verwenden dieselbe Klasse, sodass das, was sie zeigen, das ist, was der Übersetzer lädt.

### Die Kette

Jeder Schritt wird von der ersten Regel entschieden, die antwortet:

1. `i18n.ini [TranslationSettings] FallbackLanguages[]`: Was konfiguriert ist, gewinnt immer.
2. Die Klassen aus `FallbackRules[]`, in ihrer Reihenfolge: Regeln, die Extensions hinzufügen (siehe
   [Eine Fallback-Regel schreiben](funktionen/6.0/ui-umschalter-sprachen.md#eine-ausweichregel-schreiben)).
3. Mit `AutomaticLanguageFallback=enabled` (der Standard) der **automatische Fallback innerhalb derselben Sprache**:
   Eine Locale ohne eigene Kernel-Übersetzung und ohne konfigurierten Fallback nimmt die vollständigste Übersetzung
   ihrer Sprache (die drei Buchstaben vor dem Bindestrich): `fre-NC` auf `fre-FR`, `ger-IT` auf `ger-DE`. So braucht
   eine neue Länder-Locale einer übersetzten Sprache keine INI-Zeile. `AutomaticFallbackExclude[]` nennt Sprachen
   (`eng`, der Standard: Englisch ist die Sprache der Quelltexte) oder Locales, die nie automatisch zurückfallen. Jedes
   mitgelieferte Land einer übersetzten Sprache hat seine Zeile `FallbackLanguages[]`, daher ändert der automatische
   Fallback für sie nichts (`expI18nFallbackResolverTest` prüft das); er ist für später hinzugefügte Locales gedacht.

Der Resolver fragt dann erneut nach der Antwort, wodurch eine **Kette** entsteht:

```ini
FallbackLanguages[esl-AR]=esl-MX
FallbackLanguages[esl-MX]=esl-ES
```

ergibt `esl-AR -> esl-MX -> esl-ES`. Eine Kette endet bei einer Locale, für die keine Regel antwortet, bei den
Quelltexten (`untranslated`, auch `eng-GB` geschrieben, wie schon immer), vor einer Locale, die bereits darin
enthalten ist (ein **Zyklus**, der gemeldet wird), oder nach `FallbackMaxDepth` Schritten (Standard 4, höchstens 16;
eine längere Kette wird abgeschnitten und gemeldet). Eine Variante (`ger-AT@euro`) lädt zuerst ihre Locale (`ger-AT`)
und deren Kette, wie bisher.

### First oder merge

`FallbackMode=first` (der Standard, das Verhalten vor 6.0.15) lädt in jeder Wurzel die erste Datei der Kette, die
diese Wurzel hat: `esl-MX` hat eine eigene Kernel-Übersetzung, daher zeigt der Kernel `esl-MX`; eine Extension ohne
`esl-MX`-Datei zeigt ihr `esl-ES`. `FallbackMode=merge` lädt jede Datei der Kette in einer Wurzel, die allgemeinste
zuerst, sodass eine Meldung, die der spezifischen Übersetzung fehlt oder die sie leer lässt, aus der nächsten kommt:
`esl-MX` mit `esl-ES` dahinter zeigt überhaupt kein Englisch. Eine leere Übersetzung ersetzt nie eine zuvor geladene.

Der Übersetzungs-Cache folgt den Einstellungen: Sein Verzeichnisname enthält die Signatur des Resolvers, und mit
`site.ini [RegionalSettings] TranslationCheckMTime=enabled` erneuert eine geänderte Datei eines beliebigen Schritts
der Kette den Cache der Locales, die auf sie zurückfallen.

### Die Fallbacks ansehen und prüfen

```bash
./console exp:i18n fallbacks                         # every locale: kernel coverage, extensions, chain
./console exp:i18n fallbacks --only=fallback         # only those translated through a fallback (own, untranslated)
./console exp:i18n fallbacks --locale=esl-AR,ger-AT  # each step with its rule, and every extension
./console exp:i18n fallbacks --format=json
./console exp:i18n check [--strict]                  # exit 1 on a problem: for CI
./console exp:i18n coverage [--check]                # die Zahlen und die Karte oben auf dieser Seite
```

`check` meldet als Fehler einen Zyklus, eine Kette länger als `FallbackMaxDepth`, ein Ziel, das kein Locale-Code ist
oder weder eine Locale-Datei noch eine Übersetzung hat, und einen Eintrag in `FallbackRules[]`, der keine Klasse ist,
die `expI18nFallbackRuleInterface` implementiert; als Warnungen (Fehler mit `--strict`) eine konfigurierte Locale
ohne Locale-Datei und eine Kette, deren letzter Schritt keine Übersetzung hat (sie zeigt die englischen Quelltexte;
eine Kette, die mit Absicht bei `untranslated` endet, wird nicht gemeldet). Die Abdeckung der Extensions wird in
`var/<site>/cache/i18n/fallback-report.json` gecacht und neu erstellt, wenn sich eine Datei oder eine Einstellung
ändert; der erste Lauf liest jeden Extension-Katalog und dauert einige Sekunden.

`coverage` listet auch die Codes aus `settings/country.ini` (ISO 3166-1), die keine Locale nennt. Die Codes, die mit
Absicht keine Locale haben, stehen mit dem Grund in `expI18nCoverageMap::NO_LOCALE_BY_DESIGN`: die Antarktis (`AQ`),
die Bouvetinsel (`BV`) und die Heard- und McDonald-Inseln (`HM`) sind unbewohnt, und CLDR definiert für sie keine
Locale (die Karte zeigt sie trotzdem grün: Exponential bedient diese Gebiete in jeder Sprache; lokale Konventionen
gibt es keine); der Code der Niederländischen Antillen (`AN`) wurde 2010 zurückgezogen, seine Nachfolger `BQ`, `CW` und `SX`
haben Locales. Jeder andere Code ohne Locale wird als „noch nicht“ geführt, und der Test `expI18nCoverageMapTest`
schlägt dann fehl.

**Setup > Oberflächensprachen** (`setup/languages`, Richtlinie `setup/system_info`, neben Sprachen im Menü Setup) zeigt
dieselbe Liste in der Administrationsoberfläche: jede Locale mit der Übersetzung, die sie zeigt, die Abdeckung des
Kernels und der Extensions, die Kette (ein Schritt des automatischen Fallbacks wird hervorgehoben), die Probleme und
die geltenden Einstellungen. Der Code einer Locale öffnet ihre Kette Schritt für Schritt mit der Regel jedes Schritts
und jeder Extension. Die Seite ist schreibgeschützt.

### Einstellungen

| `i18n.ini [TranslationSettings]` | Standard | Bedeutung |
|---|---|---|
| `FallbackLanguages[<locale>]` | 146 Einträge | Die Übersetzung, auf die eine Locale zurückfällt; `eng-GB` oder `untranslated` für die Quelltexte. |
| `FallbackRules[]` | leer | Klassen, die `expI18nFallbackRuleInterface` implementieren, abgefragt nach `FallbackLanguages[]`. |
| `AutomaticLanguageFallback` | `enabled` | Der Fallback innerhalb derselben Sprache für Locales ohne Übersetzung und Konfiguration. |
| `AutomaticFallbackExclude[]` | `eng` | Sprachen oder Locales, die der automatische Fallback in Ruhe lässt. |
| `FallbackMaxDepth` | `4` | Die meisten Schritte einer Kette (1 bis 16). |
| `FallbackMode` | `first` | `first` oder `merge`, siehe oben. |

## Wie die Übersetzungen entstanden sind

Vor 6.0.15 lieferte Exponential 49 Locale-Dateien und die Übersetzungen der Administration aus früheren Releases
mit. Die rund 10.000 Texte, die 6.0 hinzugefügt hatte (admin4, die neuen Setup-Seiten, Audit, Pakete, Jobs und mehr),
waren nur ins Englische und Deutsche übersetzt.

Für 6.0.15, zwischen dem 15. und dem 28. Oktober 2026:

- **Locales.** Die fehlenden Locales wurden beim Erstellen aus Unicode CLDR 44 (ICU 74.2) erzeugt, spätere
  Länder-Locales und die eigenständigen Monatsnamen aus CLDR 48. Die Sprachen, für die CLDR keine Daten hat (Latein,
  Dhivehi, Klingonisch, Reichsaramäisch und Hulaulá), bekamen von Hand erstellte Dateien, und vier fehlerhafte ältere
  Dateien wurden neu erzeugt ([Abschnitt 7](#7-eine-locale-hinzufügen)). Zuletzt kamen die übrigen Gebiete hinzu,
  sodass jeder bewohnte Code aus ISO 3166-1 eine Locale hat.
- **Übersetzungen.** Die Administration wurde Sprache für Sprache übersetzt, maschinell unterstützt. Für jede Sprache
  wurden nach einem ersten Durchgang ein Glossar und ein Stilleitfaden festgelegt, und ein zweiter Durchgang prüfte
  den Katalog dagegen. Zuerst wurden Französisch und Ukrainisch vervollständigt (17. Oktober). Es folgten zehn weitere
  europäische Sprachen (18. Oktober), dann Persisch, Paschtu und Kurdisch (19. Oktober), Suaheli (23. Oktober),
  achtzehn europäische und asiatische Sprachen und Chinesisch (24. Oktober) sowie sieben weitere (25. Oktober).
  Danach kamen die großen Sprachen Südasiens, Afrikas und Südostasiens (26. Oktober) und zuletzt die Regional- und
  Minderheitensprachen (27. und 28. Oktober). Ältere Übersetzungen blieben erhalten, wo sie richtig waren und zum
  Glossar passten, und wurden sonst neu gemacht.
- **Fallbacks.** Jedes Land einer übersetzten Sprache bekam einen Fallback auf den Katalog dieser Sprache, sodass
  die Karte Land für Land grün wurde.
- **Prüfungen.** Die Qualitäts- und Registrierungstests aus
  [Abschnitt 8](#8-eine-übersetzung-der-administrationsoberfläche-hinzufügen-oder-vervollständigen) liefen über jeden
  Katalog. `./console exp:i18n coverage` hält die Zahlen und die Karte oben auf dieser Seite mit den mitgelieferten
  Dateien im Einklang.

Die verbleibende Arbeit, vor allem die Übersetzung der Hilfe und die Durchsicht der maschinell unterstützten
Kataloge, ist in [Übersetzungswellen](uebersetzungswellen.md) gemessen und geplant.

<!-- generated: exp:i18n coverage languages (start) -->
## Die noch nicht übersetzten Sprachen

Exponential kennt **150 Sprachen**: so viele verschiedene Sprachcodes (die drei Buchstaben vor dem
Bindestrich) haben die mitgelieferten Locales `share/locale/*.ini`, ohne Varianten und Testsprache. **150** davon
zeigt die Administration übersetzt: mindestens eine ihrer Locales hat eine eigene Übersetzung, fällt auf die erste
Übersetzung ihrer Kette zurück (`FallbackMode=first`) oder ist Englisch, die Sprache der Quelltexte. Für die
übrigen **0** zeigt sie die Quelltexte auf Englisch; jede ist ein Kandidat für eine neue Übersetzung
(siehe [Abschnitt 8](#8-eine-übersetzung-der-administrationsoberfläche-hinzufügen-oder-vervollständigen)). Die Namen sind die englischen der Locale-Dateien.

<details><summary>Die 0 nicht übersetzten Sprachen</summary>

| Sprache | Eigener Name | Code | Locales (Länder) |
|---|---|---|---|

</details>
<!-- generated: exp:i18n coverage languages (end) -->
