---
title: Datum, Zahlen und l10n
audience: designer, developer
category: templates
order: 260
summary: Datumsangaben, Uhrzeiten, Zahlen und Preise in der Locale des Besuchers mit l10n zeigen (time, shorttime, date, shortdate, datetime, shortdatetime, currency, clean_currency, number), eigene Formate mit datetime, das Element time sowie currentdate, maketime und makedate.
keywords: l10n, datetime, Datumsformat, Zeitformat, Locale, shortdate, shortdatetime, currency, Zahlenformat, currentdate, maketime, makedate, Element time, Lokalisierung, date format, time format, locale, number format, time element, localization
type: help
---
# Datum, Zahlen und l10n

Diese Seite ist für Designer, die Datumsangaben, Uhrzeiten, Zahlen und Preise ausgeben. Formate unterscheiden sich je
Locale; die Locale-Operatoren (`eZTemplateLocaleOperator`, `lib/eztemplate/classes/eztemplatelocaleoperator.php`) verwenden
die Formate der aktuellen Locale-Datei in `share/locale/`.

## Inhalt

1. [l10n](#l10n)
2. [Eigene Formate mit datetime](#eigene-formate-mit-datetime)
3. [Das Element time](#das-element-time)
4. [Aktuelle und berechnete Daten](#aktuelle-und-berechnete-daten)

## l10n

```tpl
{$node.object.published|l10n( 'shortdate' )}
{$node.object.published|l10n( 'datetime' )}
{$price|l10n( 'currency' )}
{$count|l10n( 'number' )}
```

Die Typen, aus der Funktionstabelle von `eZLocale` (`lib/ezlocale/classes/ezlocale.php`):

| Typ | Verwendet die Locale-Einstellung |
|---|---|
| `time`, `shorttime` | `[DateTime] TimeFormat`, `ShortTimeFormat` |
| `date`, `shortdate` | `DateFormat`, `ShortDateFormat` |
| `datetime`, `shortdatetime` | `DateTimeFormat`, `ShortDateTimeFormat` |
| `currency`, `clean_currency` | Einstellungen `[Currency]`, mit und ohne Symbol |
| `number` | Einstellungen `[Numbers]` |

Für `eng-GB` ist `ShortDateFormat` `%d/%m/%Y` (aus `share/locale/eng-GB.ini`); jede Locale-Datei hat ihr eigenes.

## Eigene Formate mit datetime

Wenn ein Design ein festes Format braucht (zum Beispiel in einem Attribut `datetime=""`):

```tpl
{$timestamp|datetime( 'custom', '%Y-%m-%d' )}
```

Die Formatbuchstaben folgen den Buchstaben von PHPs `date()` mit vorangestelltem `%` (`%Y`, `%m`, `%d`, `%H`, `%i`, `%s`;
Namen von Tagen und Monaten in der Sprache der Locale mit `%l`, `%F`, `%D`, `%M`).

## Das Element time

Maschinenlesbares Datum, menschenlesbarer Text:

```tpl
<time datetime="{$node.object.published|datetime( 'custom', '%Y-%m-%dT%H:%i' )}">
    {$node.object.published|l10n( 'shortdate' )}
</time>
```

## Aktuelle und berechnete Daten

```tpl
{currentdate()}                          {* now, as a timestamp *}
{maketime( 0, 0, 0, 12, 31, 2026 )}      {* hour, minute, second, month, day, year *}
{makedate( 12, 31, 2026 )}               {* month, day, year *}
```

Vorsicht: `currentdate()` in einem Template im View-Cache oder in einem Cache-Block ist auf den Zeitpunkt eingefroren, zu dem
der Cache geschrieben wurde. Ein Zähler „Tage bis“ muss außerhalb der gecachten Ausgabe stehen oder von einem Skript
berechnet werden.

## Verwandte Seiten

- [Der Operator i18n](der-i18n-operator.md)
- [Tutorial: ein Theme internationalisieren](../theme-design/tutorial-ein-theme-internationalisieren.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
