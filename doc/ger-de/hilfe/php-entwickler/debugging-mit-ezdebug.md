---
title: Debugging mit eZDebug
audience: php-developer, developer
category: php-development
order: 370
summary: Die Debug-API des Kernels für PHP-Entwickler - eZDebug::writeNotice, writeWarning, writeError und writeDebug mit __METHOD__ als Label, bedingte Debug-Meldungen mit eZDebugSetting und debug.ini, Zeitpunkte und Akkumulatoren, wer Debug-Ausgabe erhält (DebugByIP, DebugIPList), Debugging auf der Kommandozeile und eine Arbeitsreihenfolge, um einen Fehler zu finden.
keywords: Debugging, Debug-Ausgabe, Meldungen, Zeitpunkte, Akkumulatoren, Kommandozeile, eZDebug, writeNotice, writeWarning, writeError, writeDebug, eZDebugSetting, debug.ini, GeneralCondition, addTimingPoint, accumulatorStart, accumulatorStop, createAccumulatorGroup, DebugOutput, DebugByIP, DebugIPList, debug on command line, -d option, Xdebug
type: help
---
# Debugging mit eZDebug

Diese Seite richtet sich an PHP-Entwickler, die sehen müssen, was ihr Code innerhalb einer Anfrage tut. `eZDebug`
(`lib/ezutils/classes/ezdebug.php`) sammelt Meldungen, Zeitpunkte und Akkumulatoren, schreibt Warnungen und Fehler in
die Logs und zeigt alles in der Debug-Leiste an, wenn die Debug-Ausgabe eingeschaltet ist. Die Leiste selbst ist unter
[Die Debug-Leiste für PHP-Entwickler](die-debug-leiste-fuer-php-entwickler.md) beschrieben.

## Inhalt

1. [Meldungen schreiben](#meldungen-schreiben)
2. [Bedingte Debug-Meldungen](#bedingte-debug-meldungen)
3. [Zeitpunkte und Akkumulatoren](#zeitpunkte-und-akkumulatoren)
4. [Die Debug-Ausgabe einschalten](#die-debug-ausgabe-einschalten)
5. [Wer Debug-Ausgabe erhält](#wer-debug-ausgabe-erhält)
6. [Debugging auf der Kommandozeile](#debugging-auf-der-kommandozeile)
7. [Andere Werkzeuge](#andere-werkzeuge)
8. [Eine Arbeitsreihenfolge](#eine-arbeitsreihenfolge)

## Meldungen schreiben

```php
eZDebug::writeNotice( 'Imported ' . $count . ' items', __METHOD__ );
eZDebug::writeWarning( 'Item ' . (int)$id . ' had no title; used the id', __METHOD__ );
eZDebug::writeError( 'The feed could not be read: ' . $message, __METHOD__ );
eZDebug::writeDebug( $details, __METHOD__ );
```

| Methode | Stufe | Angezeigt und protokolliert |
|---|---|---|
| `writeError()` | error | in der Debug-Ausgabe; immer nach `var/log/error.log` geschrieben (das mitgelieferte `AlwaysLog[]=error`) |
| `writeWarning()` | warning | in der Debug-Ausgabe; nach `var/log/warning.log` geschrieben, wenn die Stufe protokolliert wird |
| `writeNotice()` | notice | in der Debug-Ausgabe; nur protokolliert, wenn in `AlwaysLog[]` aufgeführt (es füllt Logs schnell) |
| `writeDebug()` | debug | in der Debug-Ausgabe; nur protokolliert, wenn in `AlwaysLog[]` aufgeführt |

`site.ini [DebugSettings] AlwaysLog[]` listet die Stufen auf, die auch bei ausgeschalteter Debug-Ausgabe protokolliert
werden; der mitgelieferte Wert ist `error`. PHP-Deprecations gehen nach `var/log/deprecated.log`
(`DeprecatedLog=enabled`), jede unterschiedliche Meldung einmal pro Anfrage. Übergeben Sie `__METHOD__` als Label: Es
nennt die Klasse und die Methode in jeder Zeile, sodass ein Leser des Logs den Code findet. Schreiben Sie niemals
Passwörter, Tokens oder personenbezogene Daten in eine Meldung.

## Bedingte Debug-Meldungen

Ausführliche Diagnosen, die Sie nur auf Anfrage möchten, verwenden eine **Debug-Bedingung**:

```php
eZDebugSetting::writeDebug( 'extension-acmereport', $details, __METHOD__ );
```

Die Meldung wird nur geschrieben, wenn die Bedingung in `debug.ini` aktiviert ist:

```ini
# settings/override/debug.ini.append.php (development)
[GeneralCondition]
extension-acmereport=enabled
```

Der Kernel verwendet Bedingungen wie `kernel-content-view`, `kernel-urltranslator` und `workflow-event`; `ezformtoken`
verwendet `ezformtoken`. Die Debug-Leiste findet jede Bedingung von `[GeneralCondition]` und zeigt sie als Schalter an.

## Zeitpunkte und Akkumulatoren

```php
eZDebug::addTimingPoint( 'acmereport: before import' );

eZDebug::createAccumulatorGroup( 'acmereport_total', 'Acme report' );
eZDebug::accumulatorStart( 'acmereport_fetch', 'acmereport_total', 'Fetch the feed' );
$feed = acmeReportFeed::fetch( $url );
eZDebug::accumulatorStop( 'acmereport_fetch' );
```

Zeitpunkte markieren Momente der Anfrage; Akkumulatoren summieren die Zeit eines Codestücks über alle seine Aufrufe
(und zählen sie). Beide erscheinen im Reiter **Zeiten** der Leiste. Die Datenbanktreiber erfassen ihre Anweisungen als
Akkumulatoren, und daher stammen die SQL-Werte der Leiste.

## Die Debug-Ausgabe einschalten

Nur auf einer Entwicklungsmaschine (besser in einer `site.ini.dev.append.php`, siehe
[Einen PHP-Entwicklungs-Checkout einrichten](ein-php-entwicklungs-checkout.md#debug-einstellungen-für-die-entwicklung)):

```ini
[DebugSettings]
DebugOutput=enabled

[TemplateSettings]
ShowUsedTemplates=enabled
ShowTemplatePathComments=enabled

[DatabaseSettings]
SQLOutput=enabled
```

`ShowTemplatePathComments` schreibt den Template-Pfad als HTML-Kommentar um die Ausgabe jedes Templates
([Kommentare mit Template-Pfaden](../../funktionen/6.0/template-pfad-kommentare.md)). Schalten Sie die Debug-Ausgabe niemals
für Besucher einer Produktions-Site ein: Sie zeigt Interna.

## Wer Debug-Ausgabe erhält

| Einstellung | Bedeutung |
|---|---|
| `[DebugSettings] DebugByIP=enabled` mit `DebugIPList[]` | nur diese Adressen oder Netze (IPv4 und IPv6, CIDR, mit optionalem Label und Ablauf) |
| `DebugByUser=enabled` mit `DebugUserIDList[]` | nur diese Benutzer |
| `DebugIPList[]=commandline` | auch die Kommandozeile |

```ini
DebugIPList[]=203.0.113.7/32 ; Laptop ; expires=2026-10-02T15:00
```

([Debug-Leiste: die IP-Liste](../../bc/6.0/debug-leiste.md#die-ip-liste)).

## Debugging auf der Kommandozeile

Jeder `eZScript`-Befehl nimmt `-d` (oder `--debug`) mit den anzuzeigenden Elementen, zum Beispiel `-d all` oder
`-d error,warning,timing`:

```bash
php bin/php/console ext:acmereport:report --month=2026-09 -d all
```

## Andere Werkzeuge

- **Xdebug**: funktioniert wie gewohnt unter PHP-FPM und auf der Kommandozeile; `tests/xdebug.ini` ist die
  Konfiguration, die die PHPUnit-Kindprozesse verwenden. Lassen Sie es aus, wenn Sie Performance messen.
- **Die Logs**: Alles, was ohne Debug-Ausgabe schiefging, steht in `var/log/`
  ([Logs, der Log-Viewer und Anfrage-Logs](logs-log-viewer-und-anfrage-logs.md)).
- **Eine Fehlerreferenz**: Fehlerseiten zeigen eine Referenz, die `php bin/php/explogs.php --reference=<reference>` in
  den Logs findet ([Einen Fehler anhand seiner Referenz finden](../../anleitungen/fehler-anhand-der-referenz-finden.md)).

## Eine Arbeitsreihenfolge

1. Reproduzieren Sie auf einer Entwicklungskopie mit eingeschalteter Debug-Ausgabe.
2. Lesen Sie den Reiter **Meldungen** auf Fehler und Warnungen aus Ihren Dateien.
3. Prüfen Sie den Reiter **Templates** und die Kommentare mit Template-Pfaden: Ist Ihr Code überhaupt der, der läuft?
4. Fügen Sie `eZDebug::writeDebug()` oder eine Bedingung um den verdächtigen Code ein.
5. Vergleichen Sie unter Velocity mit dem eingebauten Server von PHP (`--engine=php`): Wenn der Fehler verschwindet,
   suchen Sie nach Zustand, der zwischen Anfragen erhalten bleibt ([Fallen durch statischen Zustand](fallen-durch-statischen-zustand.md)).
6. Schreiben Sie einen Test, der fehlschlägt, beheben Sie den Fehler und behalten Sie den Test.

## Verwandte Seiten

- [Referenz zu eZDebug und Logging](../erweiterungsentwicklung/teil-8-referenz/ezdebug-und-protokollierung.md)
- [Debugging und Logs (Anwendungsentwickler)](../ersteller/anwendungsentwickler/fehlersuche-und-logs.md)
- [Verbesserungen der Debug-Ausgabe](../../funktionen/6.0/verbesserte-debug-ausgabe.md)
- [Freie Dokumentation](../freie-dokumentation.md): diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
