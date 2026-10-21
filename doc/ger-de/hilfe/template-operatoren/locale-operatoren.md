---
title: Locale-Operatoren
audience: developer, designer, app-creator
category: development
order: 210
summary: Die 7 Operatoren von eZTemplateLocaleOperator - l10n mit allen neun Formatierungstypen, datetime mit den Klassen der datetime.ini und eigenen Formaten, locale, currentdate, maketime, makedate und gettime - mit auf alpha ausgegebenen Beispielen und dem Unterschied von gettime im kompilierten Template.
keywords: Lokalisierung, Datumsformat, Zahlenformat, Währung, Zeitstempel, Uhrzeit, Gebietsschema, l10n, datetime, locale, currentdate, maketime, makedate, gettime, date format, number format, currency, datetime.ini, eZLocale, eZTemplateLocaleOperator
type: help
---
# Locale-Operatoren

Datumsangaben, Uhrzeiten, Zahlen und Geldbeträge werden für eine Locale formatiert, nicht mit `date()` von PHP. Die 7
Operatoren auf dieser Seite registriert `eZTemplateLocaleOperator` (`lib/eztemplate/classes/eztemplatelocaleoperator.php`)
über `lib/eztemplate/classes/eztemplateautoload.php`, und sie erledigen ihre Arbeit mit `eZLocale`
(`lib/ezlocale/classes/ezlocale.php`) und den Locale-Dateien in `share/locale/`.

Alle Beispiele verwenden feste, mit `maketime` erzeugte Zeitstempel, damit sie sich nicht von Tag zu Tag ändern.
Formate ohne Locale-Code verwenden die Locale des Siteaccess (auf dem Siteaccess site von alpha: US-englische Formate,
wie die Ausgabe zeigt).

## Inhalt

1. [l10n](#l10n)
2. [datetime](#datetime)
3. [maketime, makedate und gettime](#maketime-makedate-und-gettime)
4. [currentdate](#currentdate)
5. [locale](#locale)
6. [Verwandte Seiten](#verwandte-seiten)

## l10n

### l10n

Formatiert den über die Pipe übergebenen Wert mit einer der Formatierungsfunktionen von `eZLocale` (ihrer
`$functionMap`):

| Typ | Methode von eZLocale | Eingabe |
|---|---|---|
| `time` | `formatTime()` | Zeitstempel |
| `shorttime` | `formatShortTime()` | Zeitstempel |
| `date` | `formatDate()` | Zeitstempel |
| `shortdate` | `formatShortDate()` | Zeitstempel |
| `datetime` | `formatDateTime()` | Zeitstempel |
| `shortdatetime` | `formatShortDateTime()` | Zeitstempel |
| `currency` | `formatCurrencyWithSymbol()` | Zahl |
| `clean_currency` | `formatCleanCurrency()` | Zahl |
| `number` | `formatNumber()` | Zahl |

Ein unbekannter Typ protokolliert "Unknown locale type".

| Parameter | Typ | Pflicht | Standard |
|---|---|---|---|
| `type` | Zeichenkette | ja | `false` |
| `locale` | Zeichenkette, ein Locale-Code wie `ger-DE` | nein | `false` (die Locale des Siteaccess) |
| `param` | Zeichenkette, das Währungssymbol für `currency` | nein | `false` (das Symbol der Locale) |

Gibt eine Zeichenkette zurück. Erzeugte Referenz: [l10n](../../beispiele/template-operatoren/l10n.md).

```tpl
{def $t = maketime( 14, 5, 0, 10, 19, 2026 )}
time          {$t|l10n( 'time' )}
shorttime     {$t|l10n( 'shorttime' )}
date          {$t|l10n( 'date' )}
shortdate     {$t|l10n( 'shortdate' )}
datetime      {$t|l10n( 'datetime' )}
shortdatetime {$t|l10n( 'shortdatetime' )}
number        {1234567.891|l10n( 'number' )}
currency      {1234.5|l10n( 'currency' )}
clean_currency {1234.5|l10n( 'clean_currency' )}
```

Ausgabe auf alpha:

```text
time          02:05:00 pm
shorttime     02:05 pm
date          Monday October 19 2026
shortdate     10/19/2026
datetime      Monday October 19 2026 02:05:00 pm
shortdatetime 10/19/2026 02:05 pm
number        1,234,567.89
currency      $1,234.50
clean_currency 1,234.50
```

Dieselben Werte für eine andere Locale und mit einem eigenen Währungssymbol:

```tpl
{def $t = maketime( 14, 5, 0, 10, 19, 2026 )}
ger-DE date     {$t|l10n( 'date', 'ger-DE' )}
ger-DE number   {1234567.891|l10n( 'number', 'ger-DE' )}
ger-DE currency {1234.5|l10n( 'currency', 'ger-DE' )}
currency with symbol 'EUR' {1234.5|l10n( 'currency', 'eng-GB', 'EUR' )}
```

Ausgabe auf alpha:

```text
ger-DE date     Montag, 19. Oktober 2026
ger-DE number   1.234.567,89
ger-DE currency € 1.234,50
currency with symbol 'EUR' EUR 1,234.50
```

## datetime

### datetime

Formatiert einen Zeitstempel mit einer benannten Klasse aus `settings/datetime.ini` `[ClassSettings] Formats[]` oder,
wenn die Klasse `custom` ist, mit einem eigenen Format. `eZLocale::formatDateTimeType()` macht daraus ein Format für
`date()` von PHP: Die Buchstaben sind die Datumsbuchstaben von PHP mit einem vorangestellten `%` (`%Y` Jahr, `%m`
Monat mit Null, `%n` Monat ohne Null, `%d` und `%j` Tag, `%H` und `%h` Stunden, `%i` Minuten, `%s` Sekunden), andere
Buchstaben bleiben als Text erhalten. Die Namen `%D`, `%l` (Tag), `%M`, `%F` (Monat) und `%a`, `%A` (am/pm) füllt
die Locale in ihrer Sprache aus.

| Parameter | Typ | Pflicht | Standard |
|---|---|---|---|
| `class` | Zeichenkette: ein Klassenname oder `custom` | ja | `false` |
| `data` | Zeichenkette: das Format, mit `custom` | nein | `false` |

Die Klassen auf alpha (`datetime.ini` des Kernels; ezfind fügt `solr` hinzu, ezfind ist hier aber nicht aktiv):

| Klasse | Format |
|---|---|
| `short_date` | `%j.%n.%Y` |
| `long_date` | `%j %F %Y` |
| `short_date_usa` | `%n/%j/%Y` |
| `time_12` | `%h:%i:%s %a` |
| `time_24` | `%H:%i:%s` |
| `all` | jeder Buchstabe, zum Testen |

Legen Sie eine Klasse in `settings/datetime.ini.append.php` Ihrer Erweiterung unter `[ClassSettings]` an und
verwenden Sie sie überall. Gibt eine Zeichenkette zurück.
Erzeugte Referenz: [datetime](../../beispiele/template-operatoren/datetime.md).

```tpl
{def $t = maketime( 14, 5, 0, 10, 19, 2026 )}
short_date      {$t|datetime( 'short_date' )}
long_date       {$t|datetime( 'long_date' )}
short_date_usa  {$t|datetime( 'short_date_usa' )}
time_12         {$t|datetime( 'time_12' )}
time_24         {$t|datetime( 'time_24' )}
custom ISO 8601 {$t|datetime( 'custom', '%Y-%m-%dT%H:%i:%s' )}
custom words    {$t|datetime( 'custom', '%l %j %F %Y' )}
```

Ausgabe auf alpha:

```text
short_date      19.10.2026
long_date       19 October 2026
short_date_usa  10/19/2026
time_12         02:05:00 pm
time_24         14:05:00
custom ISO 8601 2026-10-19T14:05:00
custom words    Monday 19 October 2026
```

Eine Klasse, die es nicht gibt, protokolliert einen Fehler und lässt den Zeitstempel, wie er war:

```tpl
[{maketime( 14, 5, 0, 10, 19, 2026 )|datetime( 'no_such_class' )}]
```

Ausgabe auf alpha:

```text
[1792443900]
```

Von der Template-Engine protokolliert:

```text
ERROR datetime: DateTime class 'no_such_class' is not defined
```

## maketime, makedate und gettime

### maketime

PHP `mktime()` mit den angegebenen Parametern in der Reihenfolge von mktime. Weggelassene Parameter werden nicht
übergeben, PHP verwendet für sie also den aktuellen Wert; ganz ohne Parameter gibt er `time()` zurück. Gibt einen
Unix-Zeitstempel zurück.

| Parameter | Typ | Pflicht | Standard |
|---|---|---|---|
| `hour` | Ganzzahl | nein | jetzt |
| `minute` | Ganzzahl | nein | jetzt |
| `second` | Ganzzahl | nein | jetzt |
| `month` | Ganzzahl | nein | jetzt |
| `day` | Ganzzahl | nein | jetzt |
| `year` | Ganzzahl | nein | jetzt |
| `dst` | Ganzzahl | nein | nicht übergeben |

Erzeugte Referenz: [maketime](../../beispiele/template-operatoren/maketime.md).

### makedate

`mktime( 0, 0, 0, month, day, year )`: Mitternacht eines Datums.

| Parameter | Typ | Pflicht | Standard |
|---|---|---|---|
| `month` | Ganzzahl | nein | jetzt |
| `day` | Ganzzahl | nein | jetzt |
| `year` | Ganzzahl | nein | jetzt |
| `dst` | Ganzzahl | nein | nicht übergeben |

Erzeugte Referenz: [makedate](../../beispiele/template-operatoren/makedate.md).

### gettime

Zerlegt einen Zeitstempel in einen Hash mit den Schlüsseln `seconds`, `minutes`, `hours`, `day`, `month`, `year`,
`weeknumber` (ISO 8601, Wochen beginnen am Montag), `weekday` (0 ist Sonntag), `yearday` (0 ist der 1. Januar) und
`epoch`. Ohne Zeitstempel verwendet er die aktuelle Zeit.

| Parameter | Typ | Pflicht | Standard |
|---|---|---|---|
| `timestamp` | Ganzzahl | nein | jetzt |

Erzeugte Referenz: [gettime](../../beispiele/template-operatoren/gettime.md).

```tpl
{def $t = maketime( 14, 5, 0, 10, 19, 2026 )
     $d = makedate( 12, 25, 2026 )
     $info = gettime( $t )}
maketime( 14, 5, 0, 10, 19, 2026 ) = {$t}
makedate( 12, 25, 2026 ) = {$d} ({$d|datetime( 'custom', '%l %j %F %Y, %H:%i' )})
gettime( $t ): {foreach $info as $key => $value}{$key}={$value} {/foreach}

gettime( $d ).weekday = {gettime( $d ).weekday}
```

Ausgabe auf alpha:

```text
maketime( 14, 5, 0, 10, 19, 2026 ) = 1792443900
makedate( 12, 25, 2026 ) = 1798185600 (Friday 25 December 2026, 00:00)
gettime( $t ): seconds=0 minutes=5 hours=14 day=19 month=10 year=2026 weeknumber=43 weekday=1 yearday=291 epoch=1792443900 
gettime( $d ).weekday = 5
```

**Übergeben Sie den Zeitstempel als Parameter.** Der Interpreter nimmt ihn auch aus der Eingabe über die Pipe:

```tpl
{def $t = maketime( 14, 5, 0, 10, 19, 2026 )}
$t|gettime.hours = {$t|gettime.hours}
```

Ausgabe auf alpha:

```text
$t|gettime.hours = 14
```

der kompilierte Code (`getTimeTransformation()`) beachtet aber nur die Parameter, daher verwendete dieselbe Zeile
kompiliert auf alpha die aktuelle Zeit und gab statt 14 die aktuelle Stunde aus. `gettime( $t )` liefert in beiden
Fällen dieselbe Antwort.

Ausgabe auf alpha mit dem Template-Compiler (in einen Arbeitsordner kompiliert):

```text
$t|gettime.hours = 2
```

## currentdate

### currentdate

Der aktuelle Unix-Zeitstempel (`time()`). Keine Parameter, keine Eingabe. In einem Cache-Block oder auf einer Seite
mit Ansichtscache ist der Wert die Zeit, zu der der Cache erstellt wurde.
Erzeugte Referenz: [currentdate](../../beispiele/template-operatoren/currentdate.md).

Das Beispiel prüft den Wert, statt ihn auszugeben, damit sich die Seite nicht jeden Tag ändert; außerdem berechnet es
ein Alter in Tagen aus dem Veröffentlichungsdatum des Artikels Privacy Policy auf alpha (Knoten 90):

```tpl
{def $now = currentdate()}
currentdate() is a Unix timestamp: {$now|is_integer|choose( 'no', 'yes' )}, after 2026-01-01: {$now|gt( makedate( 1, 1, 2026 ) )|choose( 'no', 'yes' )}
Published age of the Privacy Policy article in whole days is a number: {div( sub( $now, fetch( 'content', 'node', hash( 'node_id', 90 ) ).object.published ), 86400 )|floor|is_numeric|choose( 'no', 'yes' )}
```

Ausgabe auf alpha:

```text
currentdate() is a Unix timestamp: yes, after 2026-01-01: yes
Published age of the Privacy Policy article in whole days is a number: yes
```

## locale

### locale

Gibt das Objekt `eZLocale` für einen Locale-Code zurück, der über die Pipe oder als Parameter übergeben wird. Zu seinen
Attributen gehören `locale_code`, `language_name`, `country_name`, `currency_symbol`, `currency_name` und die Formate.
Ohne Code meldet er den fehlenden Parameter `localestring`; die aktuelle Locale erreichen Sie stattdessen über die
Formatierungsoperatoren.

| Parameter | Typ | Pflicht | Standard |
|---|---|---|---|
| Locale-Code | Zeichenkette | ja, außer bei Übergabe über die Pipe | keiner |

Gibt ein Objekt zurück. Erzeugte Referenz: [locale](../../beispiele/template-operatoren/locale.md).

```tpl
{def $de = locale( 'ger-DE' )
     $no = 'nor-NO'|locale}
locale( 'ger-DE' ): {$de.locale_code}, {$de.language_name}, currency {$de.currency_symbol}
'nor-NO'|locale: {$no.locale_code}, {$no.language_name}
```

Ausgabe auf alpha:

```text
locale( 'ger-DE' ): ger-DE, Deutsch (Deutschland), currency €
'nor-NO'|locale: nor-NO, Norsk (Bokmål)
```

## Verwandte Seiten

- [Datumsangaben, Zahlen und Lokalisierung für Designer](../designer/vorlagen/datum-zahlen-und-l10n.md)
- [Arithmetische Operatoren](arithmetische-operatoren.md): mit Zeitstempeln rechnen
- [PHP-Zeichenkettenfunktionen: kodieren und formatieren](php-zeichenkettenfunktionen-kodieren-und-formatieren.md): `number_format`, `sprintf`
- [Alle Template-Operatoren (erzeugt)](../../beispiele/template-operatoren/README.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
