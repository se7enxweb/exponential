---
title: eZDebug und Protokollierung
audience: developer
category: extension-development
order: 30
summary: Aus einer Erweiterung in die Debug-Ausgabe und in Logdateien schreiben - writeNotice, writeWarning, writeError, writeDebug, Zeitmessung und Akkumulatoren, eZLog::write - und die Debug-Einstellungen.
keywords: Debug-Ausgabe, Protokollierung, Logdateien, Zeitmessung, Akkumulatoren, Debug-Einstellungen, eZDebug, writeNotice, writeWarning, writeError, writeDebug, accumulatorStart, accumulatorStop, addTimingPoint, eZLog, log files, var/log, DebugSettings, AlwaysLog
type: help
---
# eZDebug und Protokollierung

Teil 8, Referenz 3. `eZDebug` (`lib/ezutils/classes/ezdebug.php`) sammelt Meldungen für die Debug-Ausgabe und schreibt
sie in Logdateien; `eZLog` (`lib/ezfile/classes/ezlog.php`) schreibt Zeilen in eine Logdatei Ihrer Wahl. Wie Sie die
Ausgabe lesen, steht in [Debug-Ausgabe und Logs für Entwickler](../teil-1-vom-benutzer-zum-entwickler/debug-ausgabe-und-logs-fuer-entwickler.md)
und [Erweiterungen debuggen](../teil-3-erweiterungsentwickler/erweiterungen-debuggen.md).

## Meldungen

Alle sind statisch und nehmen die Meldung, eine Bezeichnung (meist `__METHOD__`) und optional eine CSS-Klasse für die
Debug-Ausgabe entgegen:

| Methode | Stufe | Logdatei (unter `var/log/` oder im Log-Verzeichnis der Website) |
|---|---|---|
| `eZDebug::writeNotice( $string, $label = "", $backgroundClass = "" )` | Hinweis | `notice.log` |
| `eZDebug::writeWarning( $string, $label = "", $backgroundClass = "" )` | Warnung | `warning.log` |
| `eZDebug::writeError( $string, $label = "", $backgroundClass = "" )` | Fehler | `error.log` |
| `eZDebug::writeDebug( $string, $label = "", $backgroundClass = "" )` | Debug | `debug.log` |
| `eZDebug::writeStrict( $string, $label = "", $backgroundClass = "" )` | strict | `strict.log` |

`$string` darf ein Array oder Objekt sein; es wird lesbar ausgegeben.

```php
<?php
eZDebug::writeNotice( 'Imported 12 items', __METHOD__ );
eZDebug::writeWarning( "Setting [ListSettings] Limit missing, using 10", __METHOD__ );
eZDebug::writeError( "Item $id not found", __METHOD__ );
```

Welche Stufen in den Logdateien landen, legt `site.ini [DebugSettings]` fest: `AlwaysLog[]` listet die Stufen auf, die
auch bei ausgeschalteter Debug-Ausgabe geschrieben werden (standardmäßig `error`).

## Zeitmessung

```php
<?php
eZDebug::accumulatorStart( 'myext_import', 'myext', 'Import' );
// ... the work ...
eZDebug::accumulatorStop( 'myext_import' );

eZDebug::addTimingPoint( 'myext: before rendering' );
```

| Methode | Zeigt |
|---|---|
| `accumulatorStart( $key, $inGroup = false, $name = false, $recursive = false )` und `accumulatorStop( $key, $recursive = false )` | die zwischen Start und Stopp verbrachte Gesamtzeit, über alle Aufrufe summiert, in den Zeitakkumulatoren der Debug-Ausgabe |
| `addTimingPoint( $description = "" )` | einen Punkt in der Zeittabelle mit Zeit und Speicher |

## Ist die Debug-Ausgabe eingeschaltet?

`eZDebug::isDebugEnabled()` gibt zurück, ob für diesen Request Debug-Ausgabe gesammelt wird. Verwenden Sie es, um
aufwendige Vorbereitungen von Debug-Meldungen zu überspringen.

## Ihre eigene Logdatei

```php
<?php
eZLog::write( "Order $orderID sent to the warehouse", 'myext.log' );
```

`eZLog::write( $message, $logName = 'common.log', $dir = 'var/log' )` hängt eine Zeile mit Datum an. Übergeben Sie das
Log-Verzeichnis der Website, wenn Ihre Installation eines verwendet (`eZSys::varDirectory() . '/log'`). Schreiben Sie
nie Passwörter, Tokens oder personenbezogene Daten in Logs.

## Die Einstellungen

`site.ini [DebugSettings]`:

| Einstellung | Wirkung |
|---|---|
| `DebugOutput` | `enabled` zeigt die Debug-Ausgabe an |
| `DebugByIP`, `DebugIPList[]` | sie nur den aufgeführten Adressen anzeigen |
| `DebugByUser`, `DebugUserIDList[]` | sie nur den aufgeführten Benutzer-IDs anzeigen |
| `AlwaysLog[]` | Stufen, die unabhängig von den obigen Einstellungen in Logdateien geschrieben werden |

Schalten Sie die Debug-Ausgabe nur auf Entwicklungskopien oder für Ihre eigene Adresse ein.

## Verwandte Seiten

- Zurück: [eZINI: Einstellungen lesen](ezini.md)
- Weiter: [eZDB: die Datenbankschicht](ezdb.md)
- [Debug-Leiste](../../../bc/6.0/debug-leiste.md)
- [Wenn etwas schiefgeht](../../wenn-etwas-schiefgeht.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website mit Dashboard
ist für Mitte 2027 geplant: Bitte werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen
früher kommen. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
