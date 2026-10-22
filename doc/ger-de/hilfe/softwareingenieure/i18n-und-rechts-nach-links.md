---
title: Internationalisierung und Rechts-nach-links
audience: software-engineer, developer
category: software-engineering
order: 440
summary: Das Internationalisierungsmodell von Exponential für Ingenieure - Gebietsschemata aus CLDR-Daten, Inhaltssprachen als Bits einer Sprachmaske mit einer Grenze von 62, Übersetzungskataloge im Format von Qt Linguist, Rückfälle, UTF-8 überall und seine Grenze unter MySQL, Zeitzonen und Text von rechts nach links in Inhalten und der Administration - mit den Regeln für Code, der Text anzeigt.
keywords: Internationalisierung, i18n, l10n, Gebietsschema, Locale, CLDR, Inhaltssprache, language_mask, translation.ts, ezpI18n, Rückfall, UTF-8, utf8mb4, Zeitzone, Rechts-nach-links, RTL, dir, eZLocale, Softwareingenieur, internationalization, locale, content language, fallback, time zone, right to left
type: help
---
# Internationalisierung und Rechts-nach-links

Diese Seite richtet sich an Ingenieure, die mehrsprachige Websites bauen oder Code schreiben, der Text, Datumsangaben
oder Zahlen anzeigt. Sie fasst den Entwurf und seine Grenzen zusammen; der Stand der Standards steht in
[Standards zur Internationalisierung](../../standards/internationalisierung.md) und die vollständige Sprachliste in
[Sprachen in Exponential](../../sprachen.md).

## Inhalt

1. [Vier getrennte Dinge](#vier-getrennte-dinge)
2. [Inhaltssprachen und die Grenze von 62](#inhaltssprachen-und-die-grenze-von-62)
3. [Übersetzungen der Oberfläche](#übersetzungen-der-oberfläche)
4. [Kodierung](#kodierung)
5. [Rechts-nach-links](#rechts-nach-links)
6. [Regeln für Code](#regeln-für-code)
7. [Bekannte Lücken](#bekannte-lücken)

## Vier getrennte Dinge

| Ding | Was es ist | Wo |
|---|---|---|
| Gebietsschema | Formate für Datum, Zahlen, Währungen und die Schreibrichtung einer Sprache und eines Landes | `share/locale/<code>.ini`, aus CLDR-Daten erzeugt |
| Inhaltssprache | eine Sprache, in der Inhalte geschrieben werden können, unter Setup > Sprachen hinzugefügt | die Tabelle `ezcontent_language` |
| Übersetzung der Oberfläche | die Texte der Administration und der Templates in einer Sprache | `share/translations/<locale>/translation.ts` |
| Siteaccess | welche Sprachen eine Website zeigt und in welcher Rückfallreihenfolge | Siteaccess-Einstellungen |

Das README zählt in der Linie 6.0.15 219 Gebietsschemata; die Administration ist auf Englisch und Deutsch vollständig und
in weitere Sprachen teilweise übersetzt ([Sprachen in Exponential](../../sprachen.md)).

## Inhaltssprachen und die Grenze von 62

Jede Inhaltssprache ist ein Bit: Ihre ID ist eine Zweierpotenz, und ein Objekt, eine Version, eine Klasse und ein
Objektzustand halten alle ihre Sprachen in einer Ganzzahl fest, `language_mask`. Das macht "Welche Objekte gibt es auf
Deutsch?" zu einem Bit-Test je Zeile, weshalb das Filtern nach Sprache in jeder Kernel-Abfrage günstig ist. Der Preis ist
eine Grenze: **höchstens 62 Inhaltssprachen unter 64-Bit-PHP** (30 unter 32 Bit), aus `eZContentLanguage::maxCount()`.
Angebotene, aber nicht hinzugefügte Gebietsschemata zählen nicht
([Die Grenze von 62 Inhaltssprachen](../../sprachen.md#11-die-grenze-von-62-inhaltssprachen)).

Rückfälle entscheiden, was ein Besucher sieht, wenn eine Übersetzung fehlt
([Rückfälle bei Übersetzungen](../../sprachen.md#13-übersetzungs-fallbacks),
[Rückfall-Gebietsschema für Inhaltsobjekte](../../bc/6.0/ersatz-locale-fuer-inhaltsobjekte.md)).

## Übersetzungen der Oberfläche

- PHP: `ezpI18n::tr( $context, $text )`; Templates: `{'Text'|i18n( 'design/admin4/content/view' )}`.
- Der Kontext ist der Pfad des Codes, der ihn verwendet: `kernel/<module>` für PHP-Views,
  `design/<design>/<module>/<view>` für Templates. Neue Texte müssen dem Kontext hinzugefügt werden, den der Code
  verwendet, damit die Übersetzungswerkzeuge und Tests sie finden.
- Kataloge sind `.ts`-Dateien von Qt Linguist; es gibt keine gettext-Kataloge.

## Kodierung

- UTF-8 ist die interne Kodierung (`i18n.ini [CharacterSettings] Charset=utf-8`).
- Unter MySQL und MariaDB wird die Verbindungseinstellung `utf-8` als `utf8` gesendet, die Drei-Byte-Form; Zeichen
  außerhalb der Basic Multilingual Plane (zum Beispiel Emoji) können die Verbindung also selbst mit `utf8mb4`-Tabellen
  nicht passieren. SQLite, PostgreSQL, Oracle (`AL32UTF8`) und MongoDB speichern sie.
- Zeitzonen kommen aus der IANA-Datenbank über `site.ini [TimeZoneSettings] TimeZone`.

## Rechts-nach-links

Die Schreibrichtung hat eine Quelle: `share/locale/<locale>.ini [LanguageSettings] TextDirection=rtl` (40
Gebietsschema-Dateien tragen sie). `eZLocale::textDirection()`, `isRightToLeft()` und das statische
`textDirectionOf( 'heb-IL' )` lesen sie. Ist die Oberflächensprache rechts nach links, setzt die Administration
`dir="rtl"` an `<html>` und spiegelt ihr Layout; Inhalte in einer Inhaltssprache mit Schreibrichtung rechts nach links
werden dort, wo sie angezeigt und bearbeitet werden, mit `dir` gekennzeichnet, gleich welche Oberflächensprache gilt. Eine
Seite von links nach rechts ist Byte für Byte, was sie vorher war
([Sprachen mit Schreibrichtung rechts nach links in der Administration](../../funktionen/6.0/rechts-nach-links.md)).

## Regeln für Code

- Bauen Sie für Benutzer sichtbaren Text nie durch Verketten übersetzter Bruchstücke; übersetzen Sie ganze Sätze.
- Formatieren Sie Datum, Zahlen und Währungen über das Gebietsschema (`eZLocale`), nicht mit festen Formaten.
- Bevorzugen Sie in CSS logische Eigenschaften (Anfang und Ende) statt links und rechts, damit sich eine Seite von rechts
  nach links spiegelt.
- Verwenden Sie die Sprachfilter der Fetch-Funktionen statt SQL auf `language_mask`.

## Bekannte Lücken

Aus dem Standard: keine CLDR-Pluralregeln in den Katalogen, interne Sprachcodes wie `ger-DE`, die nicht BCP 47 sind (die
Ausgabe in HTTP und HTML ist es), `intl` nur optional und die oben genannte Vier-Byte-Grenze unter MySQL.

## Verwandte Seiten

- [Inhaltssprachen](../../anleitungen/inhaltssprachen.md)
- [Übersetzungen in einer Erweiterung](../erweiterungsentwicklung/teil-3-erweiterungsentwickler/uebersetzungen-in-einer-erweiterung.md)
- [Installationen mit mehreren Websites](installationen-mit-mehreren-websites.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
