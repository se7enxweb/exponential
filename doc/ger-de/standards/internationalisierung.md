---
title: Standards für Internationalisierung
audience: developer, admin
category: standards
order: 60
summary: Wie Exponential mit Unicode, CLDR-Locale-Daten, BCP-47-Sprach-Tags, ISO-Codes, Rechts-nach-links-Text, Zeitzonen und Übersetzungskatalogen umgeht, mit Nachweisen und offen benannten Lücken.
keywords: Internationalisierung, Lokalisierung, Zeichenkodierung, Sprach-Tags, Ländercodes, Währungscodes, Zeitzone, Rechts-nach-links, Übersetzungskatalog, Pluralformen, internationalization, i18n, localization, l10n, Unicode, UTF-8, utf8mb4, CLDR, ICU, intl, BCP 47, ISO 639, ISO 3166, ISO 4217, IANA time zone, tz database, RTL, bidi, UAX 9, Qt Linguist, ts, gettext, plural forms, locale
type: reference
---
# Standards für Internationalisierung

Diese Seite richtet sich an Entwickler und Administratoren, die wissen müssen, welchen Standards der
Internationalisierung Exponential folgt: welche Zeichenkodierung es verwendet, woher seine Locale-Daten stammen, wie
Sprach-Tags den Browser erreichen, wie Rechts-nach-links-Text behandelt wird, auf welche ISO-Codelisten es sich stützt
und was noch fehlt. Jede Aussage nennt die Datei, Einstellung oder den Test, woran sie geprüft wurde (Linie
Exponential 6.0.15, Oktober 2026).

Die Liste aller Locales und Übersetzungen der Administrationsoberfläche finden Sie unter
[Sprachen in Exponential](../sprachen.md).

## Zusammenfassung

| Standard | Version | Status | Nachweis (kurz) |
|---|---|---|---|
| Unicode / UTF-8 als interne Kodierung | Unicode, UTF-8 (RFC 3629) | **Erfüllt** | `settings/i18n.ini [CharacterSettings] Charset=utf-8`, `settings/site.ini [DatabaseSettings] Charset=utf-8` |
| 4-Byte-UTF-8 in MySQL / MariaDB (`utf8mb4`) | MySQL `utf8mb4` | **Teilweise erfüllt** | `lib/ezdb/classes/ezmysqlcharset.php` bildet `utf-8` auf `utf8` (3 Bytes) ab; `ezxml.ini [InputSettings] SupplementaryCharacters` |
| Unicode-CLDR-Locale-Daten | CLDR 44 und 48 | **Erfüllt** (als generierte Daten) | Köpfe von `share/locale/*.ini`, [Sprachen in Exponential](../sprachen.md) |
| ICU / PHP `intl` | ICU über ext-intl | **Teilweise erfüllt** (nur optional) | Absicherungen mit `class_exists( 'Collator' )` in zwei Kernel-Klassen |
| BCP-47-Sprach-Tags (RFC 5646) | BCP 47 | **Erfüllt** für HTTP und HTML; interne Codes sind nicht BCP 47 | `share/locale/<code>.ini [HTTP] ContentLanguage` |
| ISO-639-Sprachcodes | ISO 639-1 / 639-2 | **Teilweise erfüllt** | interne Codes wie `ger-DE` verwenden ISO 639-2 (bibliografische und ältere Formen) |
| ISO-3166-1-Ländercodes | ISO 3166-1 alpha-2 / alpha-3 | **Erfüllt** | `settings/country.ini` (246 Einträge mit `Alpha2`, `Alpha3`) |
| ISO-4217-Währungscodes | ISO 4217 | **Erfüllt** | `kernel/shop/classes/ezcurrencydata.php` (`/^[A-Z]{3}$/`), `[Currency] ShortName` in den Locale-Dateien |
| IANA-Zeitzonendatenbank | tz database | **Erfüllt** | `settings/site.ini [TimeZoneSettings] TimeZone`, `date_default_timezone_set()` |
| Unicode Bidirectional Algorithm | UAX #9 | **Erfüllt** (über den Browser, mit `dir` und Isolierung) | [Rechts-nach-links-Sprachen](../funktionen/6.0/rechts-nach-links.md) |
| Qt-Linguist-Kataloge `.ts` | TS 2.0 | **Erfüllt** | `share/translations/<code>/translation.ts`, `schemas/translation/ts.rng` |
| GNU gettext | - | **Nicht anwendbar** | keine `.po`/`.mo`-Kataloge, keine gettext-Aufrufe in `kernel/` oder `lib/` |
| Pluralformen (CLDR-Pluralregeln) | CLDR-Pluralregeln | **Nicht erfüllt** | keine `numerus`-Meldungen in irgendeiner `translation.ts` |

## Unicode und UTF-8

**Was es ist.** Unicode ist der universelle Zeichensatz, herausgegeben vom Unicode Consortium; UTF-8 (RFC 3629, IETF)
ist seine byteorientierte Kodierung und die Kodierung des Webs.

**Wie Exponential den Standard erfüllt.**

- Der interne Zeichensatz wird aus `settings/i18n.ini [CharacterSettings] Charset` gelesen, ausgeliefert als `utf-8`.
  `eZTextCodec::internalCharset()` (`lib/ezi18n/classes/eztextcodec.php`) liest genau diese Einstellung.
- `settings/i18n.ini [CharacterSettings] MBStringExtension=enabled`, und `ext-mbstring` ist eine feste Voraussetzung in
  `require` von `composer.json`.
- Der Zeichensatz der Datenbankverbindung ist `settings/site.ini [DatabaseSettings] Charset=utf-8` (der Kommentar
  darüber besagt, dass ein leerer Wert auf `i18n.ini` zurückfällt).
- Übersetzungskataloge sind XML, deklariert als `encoding="utf-8"`.
- 335 der 355 Locale-Dateien in `share/locale/` deklarieren `[Charset] Preferred=utf-8`. Die übrigen 20 sind ältere
  Dateien, die noch eine veraltete Codepage nennen (zum Beispiel `iso-8859-1`, `iso-8859-15`, `koi8-u`, `cp1251`). Der
  interne Zeichensatz der Site kommt weiterhin aus `i18n.ini`; die Namen der Codepages bleiben aus
  Kompatibilitätsgründen erhalten.
- Der Test `tests/tests/lib/ezlocale/expLocaleFilesTest.php` prüft, dass die Namen in jeder Locale-Datei gültiges
  UTF-8 sind und keine Datei doppelt kodiertes UTF-8 enthält.

**Status: Erfüllt** für die interne Kodierung der Anwendung und für neue Installationen.

### 4-Byte-Zeichen unter MySQL und MariaDB

Das `utf8` von MySQL (auch `utf8mb3` genannt) speichert höchstens drei Bytes pro Zeichen, sodass Zeichen oberhalb von
U+FFFF (Emoji, mathematische alphanumerische Zeichen, viele historische Schriften) `utf8mb4` brauchen.

- `lib/ezdb/classes/ezmysqlcharset.php` bildet den internen Namen `utf-8` auf das `utf8` von MySQL ab, nicht auf
  `utf8mb4`.
- Der MySQL-Treiber (`lib/ezdb/classes/ezmysqlidb.php`) setzt diesen abgebildeten Zeichensatz auf der Verbindung und
  schreibt eine Warnung ins Debug-Log, wenn der Server einen anderen meldet.
- Rich Text hat eine Absicherung: `ezxml.ini [InputSettings] SupplementaryCharacters` (`enabled`, `disabled` oder
  `auto`). `eZXMLInputHandler::supplementaryCharactersAllowed()` behält Zeichen oberhalb von U+FFFF bei `auto`, es sei
  denn, die Datenbank ist MySQL oder MariaDB mit einem anderen Verbindungszeichensatz als `utf8mb4`; dort werden sie
  ersetzt, weil sie als `????` gespeichert würden oder im strikten SQL-Modus fehlschlügen.
- Der Systembericht (`kernel/classes/expsystemreport.php`) akzeptiert `utf-8`, `utf8` und `utf8mb4` als gesunde
  Verbindungszeichensätze.

**Status: Teilweise erfüllt.** SQLite 3 und PostgreSQL speichern vollständiges Unicode. Unter MySQL und MariaDB ist
eine Standardverbindung 3-Byte-`utf8`; ergänzende Zeichen bleiben nur erhalten, wenn der Administrator `utf8mb4`
konfiguriert.

> **Hinweis** Zeichensatz und Sortierung der Tabellen einer MySQL- oder MariaDB-Installation hängen von den
> Standardwerten des Servers zum Zeitpunkt der Installation ab: `kernel/sql/mysql/kernel_schema.sql` nennt
> `ENGINE=InnoDB`, aber kein `CHARSET`. Kommentare in `kernel/classes/datatypes/ezurl/ezurl.php` und
> `kernel/classes/expuserloginintegrity.php` gehen von Spalten mit `utf8mb4_general_ci` aus. Welche Sortierung eine
> bestimmte Installation hat, ist hier **nicht geprüft**.

## Unicode CLDR und ICU

**Was es ist.** Das Unicode Common Locale Data Repository (CLDR, Unicode Consortium) ist die Referenzquelle für
Locale-Daten: Namen von Sprachen und Ländern, Formate für Datum, Uhrzeit, Zahlen und Währungen. ICU (International
Components for Unicode) ist die Bibliothek, die es umsetzt; PHP stellt ICU über die Extension `intl` bereit.

**Wie Exponential den Standard erfüllt.**

- Locale-Dateien sind einfache INI-Dateien, gelesen von `eZLocale` (`lib/ezlocale/classes/ezlocale.php`). Für 6.0.15
  wurden sie aus CLDR generiert: Zum Beispiel beginnt `share/locale/ara-SA.ini` mit "Generated from Unicode CLDR 48
  data (ICU locale ar_SA)" und trägt den Hinweis auf die Unicode License v3. [Sprachen in Exponential](../sprachen.md)
  nennt die Quelle jeder Datei (CLDR 44, CLDR 48 oder von Hand erstellte Namen nach den Konventionen von CLDR).
- Zur Laufzeit ist keine PHP-Extension nötig: [Sprachen in Exponential](../sprachen.md), Abschnitt "No PHP extension
  at run time". `intl` wurde nur zum Generieren der Dateien verwendet.
- `intl` wird optional in zwei Kernel-Klassen verwendet, immer hinter `class_exists()`:
  - `kernel/classes/datatypes/ezcountry/expcountrygroups.php`: `Collator` zum Sortieren von Ländernamen in der Sprache
    des Lesers, `Normalizer::normalize( ..., Normalizer::FORM_D )` für einen Abgleich ohne Berücksichtigung von
    Akzenten.
  - `kernel/classes/uiswitch/expuiswitchlanguagecatalogue.php`: dasselbe für die Liste der Oberflächensprachen.
- In `kernel/` oder `lib/` wird weder `NumberFormatter` noch `IntlDateFormatter` verwendet. Datum, Zahlen und
  Währungen formatiert `eZLocale` aus den INI-Daten. `tests/tests/lib/ezlocale/eZLocaleFormattingTest.php` und
  `expLocaleFilesTest.php` prüfen, dass jede Locale Datum, Uhrzeit, Zahlen und Währungen formatiert.

**Status: Erfüllt** für CLDR als Datenquelle. **Teilweise erfüllt** für ICU: Es ist optional, und wo es fehlt, fällt
der Kernel auf eine Sortierung nach Bytereihenfolge zurück.

**Bekannte Lücken.**

- `eZLocale` gibt nur am/pm aus und kann nicht jedes CLDR-Muster ausdrücken; `ara-SA.ini` vermerkt, dass die
  12-Stunden-Uhr von CLDR aus diesem Grund durch die 24-Stunden-Uhr ersetzt wurde.
- Die INI-Dateien sind eine Momentaufnahme. Es gibt keinen automatischen Job, der sie aus einem neueren CLDR-Release
  neu erzeugt.

## Sprach-Tags: BCP 47 und ISO 639

**Was es ist.** BCP 47 (IETF, derzeit RFC 5646 und RFC 4647) definiert Sprach-Tags wie `de-DE`, `he-IL` oder
`sr-Latn-RS`, zusammengesetzt aus ISO-639-Sprachcodes, ISO-15924-Schriftcodes und Regionscodes nach ISO 3166-1 oder
UN M.49.

**Wie Exponential den Standard erfüllt.**

- Intern kennzeichnet Exponential eine Locale mit einem eigenen Code, `<ISO 639-2 code>-<ISO 3166-1 alpha-2 code>`,
  zum Beispiel `eng-GB`, `ger-DE`, `nor-NO`, optional mit einer Variante (`ger-DE@euro`). Diese Codes benennen die
  Dateien in `share/locale/` und `share/translations/`, die Inhaltssprachen (`ezcontent_language`) und
  `site.ini [RegionalSettings] Locale`. Sie sind **keine** BCP-47-Tags, und einige verwenden bibliografische oder
  ältere Codes nach ISO 639-2 (`ger`, `fre`, `dut`, `esl`).
- Jede Locale-Datei bildet ihren Code in `[HTTP] ContentLanguage` auf ein BCP-47-Tag ab (zum Beispiel `ara-SA` auf
  `ar-SA`). `eZLocale::httpLocaleCode()` liefert es, und die Seitenlayouts geben es in `<html lang="...">` und im
  Header `Content-Language` aus. `settings/site.ini [RegionalSettings] HTTPLocale` kann es pro Siteaccess überschreiben.
- In 6.0.15 wurden zwanzig Locale-Dateien korrigiert, die ungültige oder veraltete Tags angaben (`no-bokmaal`,
  `swe-SE`, `ua-UA` ...); die Tabelle steht in
  [Übersetzungen und Sprachen](../funktionen/6.0/uebersetzungen-und-sprachen.md), "HTML language tags".
- Eine für diese Seite durchgeführte Prüfung der ausgelieferten Dateien: Jeder Wert von `[HTTP] ContentLanguage` in
  `share/locale/` entspricht der BCP-47-Form Sprache, optionale Schrift, optionale Region, optionale Varianten.
- `expLocaleFilesTest.php` prüft die Codeform und die Konventionen der HTTP-Codes der generierten Dateien.

**Status: Erfüllt** für die an Browser gesendeten Tags. Die internen Codes sind ein separates, dokumentiertes Schema.

**Bekannte Lücken.**

- Das Attribut `language` der Qt-Dateien `.ts` mischt Formen: `de_DE`, `en_GB`, `heb-IL`, `lat-VA`, `tlh-QO`
  (geprüft in `share/translations/*/translation.ts`). Der Übersetzer hängt nicht davon ab, aber es ist uneinheitlich.
- Die internen Codes mit drei Buchstaben können kein Schrift-Subtag ausdrücken; eine Schriftvariante braucht einen
  eigenen Locale-Code.

## ISO-3166-Ländercodes

**Was es ist.** ISO 3166-1 (ISO) definiert die zweibuchstabigen, dreibuchstabigen und numerischen Codes von Ländern.

**Wie Exponential den Standard erfüllt.** `settings/country.ini` enthält einen Abschnitt pro Land, benannt nach
seinem Alpha-2-Code, mit `Alpha2`, `Alpha3` und `IDC` (internationale Vorwahl); die Datei besagt, dass der
Gruppenname der Code nach ISO 3166-1 alpha-2 ist. Sie hat 246 Einträge. Der Datentyp für Länder
(`kernel/classes/datatypes/ezcountry/`) speichert diese Codes und validiert gegen sie; `settings/countrygroups.ini`
gruppiert sie.

**Status: Erfüllt.** Ob die Liste Eintrag für Eintrag der aktuellen Ausgabe von ISO 3166-1 entspricht, ist
**nicht geprüft**.

## ISO-4217-Währungscodes

**Was es ist.** ISO 4217 (ISO) definiert dreibuchstabige Währungscodes wie `EUR`, `USD`, `SAR`.

**Wie Exponential den Standard erfüllt.**

- Jede Locale-Datei nennt ihre Währung in `[Currency] ShortName` (zum Beispiel `SAR` in `ara-SA.ini`), mit Symbol,
  Name, Dezimal- und Tausendertrennzeichen und der Anzahl der Nachkommastellen.
- Die Währungstabelle des Shops weist jeden Code zurück, der nicht aus drei Großbuchstaben besteht
  (`kernel/shop/classes/ezcurrencydata.php`, `preg_match( "/^[A-Z]{3}$/", $code )`).
- Der Kommentarblock von `lib/ezlocale/classes/ezlocale.php` nennt ISO 639 und ISO 4217 als seine Referenzen.

**Status: Erfüllt** für das Format. Ob jeder `ShortName` ein derzeit gültiger Code nach ISO 4217 ist, ist
**nicht geprüft**.

## Zeitzonen: die IANA-tz-Datenbank

**Was es ist.** Die IANA-Zeitzonendatenbank (tz, zoneinfo) benennt Zonen wie `Europe/Berlin` und hält ihre Regeln
fest; die Datumsfunktionen von PHP verwenden sie.

**Wie Exponential den Standard erfüllt.**

- `settings/site.ini [TimeZoneSettings] TimeZone` nimmt einen IANA-Zonennamen an (der Kommentar verweist auf
  `/usr/share/zoneinfo/zone.tab`). Standardmäßig leer.
- Der Web-Kernel (`kernel/private/classes/ezpkernelweb.php`) und Kommandozeilenskripte (`kernel/classes/ezscript.php`)
  rufen `date_default_timezone_set()` mit diesem Wert auf, wenn er gesetzt ist.
- `index.php` setzt `UTC`, wenn PHP kein `date.timezone` hat, sodass PHP nie rät.
- Die Datenbanktests in CI laufen mit `date.timezone=America/Los_Angeles` (`.github/workflows/phpunit.yml`), was Code
  aufdeckt, der von UTC ausgeht.

**Status: Erfüllt.**

**Bekannte Lücken.** Es gibt eine Zeitzone pro Installation (oder pro Siteaccess); eine Zeitzone pro Benutzer gibt es
im Kernel nicht. Der Kommentar in `site.ini` warnt noch vor Webservern mit Threads; ob diese Warnung für PHP-FPM und
Exponential Velocity noch gilt, ist **nicht geprüft**.

## Rechts-nach-links-Text und der Unicode Bidirectional Algorithm

**Was es ist.** Der Unicode Bidirectional Algorithm (Unicode Standard Annex #9, UAX #9) entscheidet über die
Anzeigereihenfolge gemischten Texts von links nach rechts und von rechts nach links. HTML stellt ihn über das Attribut
`dir`, `dir="auto"`, `<bdi>` und die Isolierungszeichen U+2066 bis U+2069 bereit.

**Wie Exponential den Standard erfüllt.** Browser führen den Algorithmus aus; Exponential gibt ihnen korrekte
Richtungsinformationen:

- Die Richtung kommt ausschließlich aus `share/locale/<code>.ini [LanguageSettings] TextDirection=rtl`. 43
  Locale-Dateien tragen sie (Arabisch, Hebräisch, Jiddisch, Persisch, Urdu und weitere), für diese Seite in
  `share/locale/` gezählt.
- `eZLocale::textDirection()`, `eZLocale::isRightToLeft()` und `eZLocale::textDirectionOf()` stellen sie bereit;
  `$site.text_direction` bringt sie in die Templates.
- Jedes Seitenlayout der Administrationsoberfläche gibt bei einer Oberflächensprache von rechts nach links
  `dir="rtl"` auf `<html>` aus (`design/admin4/templates/pagelayout.tpl`, `design/admin/templates/pagelayout.tpl` und
  die anderen, die in [Rechts-nach-links-Sprachen](../funktionen/6.0/rechts-nach-links.md) aufgeführt sind).
- Inhalte in einer Inhaltssprache von rechts nach links erhalten `lang` und `dir="rtl"`
  (`design/standard/templates/parts/exp_content_direction.tpl`), auch in den Bearbeitungsformularen und den
  Online-Editoren.
- Von Redakteuren eingegebene Namen werden mit `dir="auto"` oder `<bdi>` isoliert, und der Seitentitel verwendet
  U+2068 FIRST STRONG ISOLATE und U+2069 POP DIRECTIONAL ISOLATE, sodass Namen mit gemischter Richtung ihre
  Satzzeichen an der richtigen Stelle behalten.
- Die Stylesheets der Administrationsoberfläche verwenden logische CSS-Eigenschaften (`margin-inline-start` usw.).
- `expLocaleFilesTest.php` prüft, dass jede Datei für eine Sprache von rechts nach links, und keine andere,
  `TextDirection=rtl` hat.

**Status: Erfüllt** für die Administrationsoberfläche und das Markup der Inhalte.

**Bekannte Lücken** (aus [Rechts-nach-links-Sprachen](../funktionen/6.0/rechts-nach-links.md), "Follow-ups"): Mehrere
Stylesheets von Extensions verwenden noch physisches links und rechts. Site-Designs (Frontend-Themes) sind von dieser
Arbeit nicht abgedeckt.

## Übersetzungskataloge: Qt Linguist `.ts`

**Was es ist.** Das Format `.ts` ist das XML-Quellformat für Übersetzungen von Qt Linguist (The Qt Company), hier in
Version 2.0 (`<TS version="2.0">`).

**Wie Exponential den Standard erfüllt.**

- Die Oberflächentexte von Templates (`'text'|i18n( 'context' )`) und PHP (`ezpI18n::tr()`) werden in
  `share/translations/<code>/translation.ts` und in der `translations/<code>/translation.ts` einer Extension
  nachgeschlagen.
- In `share/translations/` existieren 156 Verzeichnisse (155 Sprachen plus `untranslated`, die Vorlage für neue).
- `eZTSTranslator` (`lib/ezi18n/classes/eztstranslator.php`) validiert jede Datei gegen `schemas/translation/ts.rng`
  und cacht sie als PHP (`eztranslationcache.php`).
- Sprachrückfälle: `settings/i18n.ini [TranslationSettings]` (`FallbackLanguages[]`,
  `AutomaticLanguageFallback`, `FallbackMaxDepth`).
- Tests: `tests/tests/design/TranslationFilesValidTest.php` (Gültigkeit gegen das Schema, jeder Template-Text in
  `eng-US`, `ger-DE` und `untranslated` vorhanden), `tests/tests/lib/ezi18n/expTranslationQualityTest.php`,
  `tests/tests/lib/ezi18n/expAdminTranslationsRegisteredTest.php` sowie die Designtests
  `AdminUsedTranslationsTest.php`, `AdminJobTranslationsTest.php`, `AdminSubitemsTranslationsTest.php`.
- `bin/php/ezchecktranslation.php` meldet den Zustand einer Übersetzungsdatei.

**Status: Erfüllt.**

### GNU gettext

Exponential verwendet kein gettext: Es gibt keine `.po`- oder `.mo`-Kataloge und keine gettext-Aufrufe in `kernel/`
oder `lib/`. **Status: Nicht anwendbar.**

### Pluralformen

**Was es ist.** CLDR-Pluralregeln legen fest, wie viele Pluralformen eine Sprache hat (eine im Japanischen, zwei im
Englischen, drei im Ukrainischen, sechs im Arabischen). Qt-Dateien `.ts` drücken sie mit Meldungen `numerus="yes"` aus.

**Wie Exponential den Standard erfüllt.** Gar nicht. Keine `translation.ts` in `share/translations/` enthält eine
`numerus`-Meldung, und der Übersetzer hat keine Pluralauswahl. [Sprachen in Exponential](../sprachen.md) sagt das und
empfiehlt eine neutrale Formulierung ("label: %count") statt eines Plurals.

**Status: Nicht erfüllt.** Sprachen mit mehr als zwei Pluralformen lesen sich bei manchen Anzahlen holprig.

## Bekannte Lücken

1. Eine Standardverbindung zu MySQL oder MariaDB verwendet 3-Byte-`utf8`; vollständiges Unicode braucht ein vom
   Administrator konfiguriertes `utf8mb4`.
2. 20 Locale-Dateien nennen in `[Charset] Preferred` noch eine veraltete Codepage.
3. Keine Pluralformen im Übersetzungssystem.
4. ICU ist optional; ohne `intl` sortieren die Länder- und Sprachlisten nach Bytes.
5. Interne Locale-Codes sind nicht BCP 47 und verwenden einige bibliografische Codes nach ISO 639-2; die Attribute
   `language` der `.ts`-Dateien sind uneinheitlich.
6. Eine Zeitzone pro Installation oder Siteaccess, keine pro Benutzer.
7. Einige Stylesheets von Extensions berücksichtigen Rechts-nach-links noch nicht.

## Verwandte Seiten

- [Sprachen in Exponential](../sprachen.md)
- [Übersetzungen und Sprachen](../funktionen/6.0/uebersetzungen-und-sprachen.md)
- [Rechts-nach-links-Sprachen](../funktionen/6.0/rechts-nach-links.md)
- [Inhaltssprachen](../anleitungen/inhaltssprachen.md)
- [Oberflächensprache](../funktionen/6.0/ui-umschalter-sprachen.md)
- [Programmierstandards](programmierstandards.md)
