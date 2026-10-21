---
title: Datum, Zahlen und Währung pro Sprache
audience: government, admin, website-creator
category: public-sector
order: 260
summary: Wie jede Sprachfassung einer Exponential-Website Datum, Uhrzeit, Zahlen und Beträge schreibt (aus der Locale-Datei des Siteaccess), wie Sie eine Locale prüfen und anpassen, und die bekannten Grenzen (eine Zeitzone pro Siteaccess, eine 12-Stunden-Uhr nur mit am und pm).
keywords: Datumsformat, Zahlenformat, Währung, Locale, share/locale, DateFormat, TimeZone, Zeitzone, MondayFirst, Dezimaltrennzeichen, Lokalisierung, l10n, date format, number format, currency, time zone, decimal separator
type: help
---
# Datum, Zahlen und Währung pro Sprache

Diese Seite richtet sich an Administratoren und Websiteersteller. Jeder Siteaccess formatiert Datum, Zahlen und
Geldbeträge mit seiner `site.ini [RegionalSettings] Locale`, gelesen aus `share/locale/<code>.ini`
([Sprachen in Exponential, Abschnitt 7](../../../../eng-us/languages.md#7-adding-a-locale)).

## Inhalt

1. [Was die Locale entscheidet](#was-die-locale-entscheidet)
2. [Die Formate einer Locale sehen](#die-formate-einer-locale-sehen)
3. [Ein Format anpassen](#ein-format-anpassen)
4. [Zeitzonen](#zeitzonen)
5. [Bekannte Grenzen](#bekannte-grenzen)

## Was die Locale entscheidet

| Gruppe | Entscheidet |
|---|---|
| `[DateTime]` | Datums- und Zeitformate, lang und kurz; `MondayFirst` |
| `[ShortMonthNames]`, `[LongMonthNames]`, Tagesnamen | die Namen in der Sprache |
| `[Numbers]` | Dezimalzeichen, Tausendertrennzeichen, Nachkommastellen |
| `[Currency]` | Symbol, Name, Code nach ISO 4217, Format von Beträgen |
| `[HTTP] ContentLanguage` | die `lang`-Angabe der Seite |

Die Locale-Dateien von 6.0.15 wurden aus den Daten von Unicode CLDR erzeugt, sie folgen also dem üblichen Gebrauch in
jedem Land ([Standards zur Internationalisierung](../../../../eng-us/standards/internationalization.md#unicode-cldr-and-icu)).

## Die Formate einer Locale sehen

Die Seite der Sprache unter **Setup > Sprachen** hat einen Abschnitt **The locale**, der zeigt, wie Datum, Zahlen und
Geldbeträge geschrieben werden. Prüfen Sie das vor dem Start mit jemandem aus Ihrem Sprachdienst.

## Ein Format anpassen

Weicht der Stilleitfaden Ihrer Stelle ab (zum Beispiel ein Datumsformat für amtliche Bekanntmachungen), legen Sie eine
Überschreibung der Locale-Datei als Variante an (`<code>@<name>.ini`, die nur die geänderten Schlüssel enthält), oder
ändern Sie Ihre Templates so, dass sie ein bestimmtes Format verwenden. Bewahren Sie solche Änderungen in Ihrem
eigenen Repository auf; eine geänderte mitgelieferte Datei wird bei Updates überschrieben.

## Zeitzonen

`site.ini [TimeZoneSettings] TimeZone` setzt eine Zeitzone pro Installation oder Siteaccess, aus der IANA-Zeitzonendatenbank;
es gibt keine Zeitzone pro Benutzer
([Standards zur Internationalisierung](../../../../eng-us/standards/internationalization.md#time-zones-the-iana-tz-database)).
Eine Stelle, die mehrere Zeitzonen bedient, sollte die Zeitzone neben Uhrzeiten angeben, etwa bei Fristen.

## Bekannte Grenzen

- Eine 12-Stunden-Uhr wird nur mit `am`/`pm` ausgegeben; Locales, deren Kennzeichen andere Wörter sind, verwenden die
  24-Stunden-Uhr.
- `eZLocale` kennt keinen Wochenbeginn am Samstag oder Freitag.
- Keine Pluralformen in den Oberflächenübersetzungen, deshalb lesen sich einige Zahlenangaben in manchen Sprachen
  holprig.
- Trennzeichen in Zahlen müssen ein einzelnes Byte sein (ein einfaches Leerzeichen, kein geschütztes Leerzeichen).

## Verwandte Seiten

- [Sprachen in Exponential](../../../../eng-us/languages.md)
- [Standards zur Internationalisierung](../../../../eng-us/standards/internationalization.md)
- [Eigene Locales für nicht gelistete Sprachen](eigene-locales-fuer-nicht-gelistete-sprachen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
