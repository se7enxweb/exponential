---
title: Fehlersuche und Logs
audience: app-creator, developer, admin
category: app-creation
order: 520
summary: Herausfinden, was Ihre Anwendung tut - Debug-Ausgabe und Debug-Leiste, Kommentare zu Template-Pfaden, eZDebug im Code, die Logdateien und Setup > Logs, Fehlerreferenzen, Anfrage-Logs und Fehlersuche unter Velocity.
keywords: Debug, Fehlersuche, Debug-Leiste, Logs, Log-Betrachter, Fehlerreferenz, Anfrage-Logs, Kommentare zu Template-Pfaden, Anwendungsentwickler, debug, debugging, debug bar, DebugOutput, eZDebug, writeError, logs, error.log, warning.log, log viewer, error reference, request logs, template path comments, Velocity, app creator
type: help
---
# Fehlersuche und Logs

Diese Seite ist für Anwendungsentwickler, die in eine Anfrage hineinsehen müssen. Exponential bietet für die
Entwicklung eine Debug-Ausgabe und eine Debug-Leiste, Logdateien für alles und eine Fehlerreferenz, die das, was ein
Benutzer sieht, mit der Logzeile verbindet.

## Inhalt

1. [Debug-Ausgabe und die Debug-Leiste](#debug-ausgabe-und-die-debug-leiste)
2. [Debug- und Logzeilen schreiben](#debug--und-logzeilen-schreiben)
3. [Die Logdateien](#die-logdateien)
4. [Von einer Fehlerseite zur Logzeile](#von-einer-fehlerseite-zur-logzeile)
5. [Fehlersuche unter Velocity](#fehlersuche-unter-velocity)

## Debug-Ausgabe und die Debug-Leiste

Auf einem Entwicklungsrechner:

```ini
# settings/override/site.ini.append.php (development only)
[DebugSettings]
DebugOutput=enabled

[TemplateSettings]
ShowTemplatePathComments=enabled
```

Die Debug-Leiste zeigt Abfragen, verwendete Templates, Einstellungen, Laufzeiten und Caches der Seite
([Exp-Debug-Leiste](../../../../eng-us/features/6.0/exp-debug-bar.md), [Debug-Leiste](../../../bc/6.0/debug-leiste.md)). Kommentare zu
Template-Pfaden zeigen, welche Datei welches Markup geschrieben hat
([Kommentare zu Template-Pfaden](../../../../eng-us/features/6.0/template-path-comments.md)). Aktivieren Sie die Debug-Ausgabe nie
in einem Siteaccess der Produktivumgebung; sie kann Interna zeigen.

## Debug- und Logzeilen schreiben

```php
eZDebug::writeNotice( 'Imported 12 items', __METHOD__ );
eZDebug::writeWarning( 'Item 42 had no title', __METHOD__ );
eZDebug::writeError( 'Feed could not be read: ' . $message, __METHOD__ );
eZLog::write( 'archive sent for object ' . (int)$objectID, 'expdemo.log' );
```

Warnungen und Fehler landen unabhängig von der Debug-Einstellung in `var/log/warning.log` und `var/log/error.log`;
`eZLog::write()` schreibt ein eigenes Log in das Log-Verzeichnis. Protokollieren Sie niemals Passwörter, Schlüssel,
Tokens oder personenbezogene Daten.

## Die Logdateien

| Datei | Enthält |
|---|---|
| `var/log/error.log`, `warning.log` | Fehler und Warnungen |
| `var/log/csp.log` | Berichte der Content Security Policy |
| `var/log/security.log` | Sicherheitsereignisse |
| Ihre eigenen `*.log` | was Ihr Code schreibt |

**Setup > Logs** zeigt sie mit einer Zusammenfassung wiederholter Meldungen ([Log-Betrachter](../../../../eng-us/features/6.0/log-viewer.md));
Logs werden rotiert ([Log-Rotation](../../../../eng-us/features/6.0/log-rotation.md)). Anfrage-Logs zeichnen Anfragen mit
Laufzeiten auf ([Anfrage-Logs](../../../../eng-us/features/6.0/request-logs.md)).

## Von einer Fehlerseite zur Logzeile

Fehlerseiten tragen eine Referenz; suchen Sie in den Logs danach, um die vollständige Meldung zu finden
([Einen Fehler über seine Referenz finden](../../../anleitungen/fehler-anhand-der-referenz-finden.md)).

## Fehlersuche unter Velocity

- Worker halten Klassen und Einstellungen im Speicher: Nach einer Änderung `exp:velocity deploy` (oder `restart`),
  sonst untersuchen Sie alten Code.
- Die eigenen Logs des Servers liegen unter `var/vc/qbix/run/` und im konfigurierten Log-Verzeichnis
  ([8.3.12 Logs](../../../installation/08-die-website-ausliefern.md#8312-logs)).
- Vergleichen Sie mit dem eingebauten Server von PHP (`--engine=php`), der jede Anfrage frisch ausführt, wenn Sie
  vermuten, dass Zustand zwischen Anfragen erhalten bleibt
  ([Persistenter Worker-Server Velocity](../../../../eng-us/features/6.0/velocity-persistent-worker-server.md)).

## Verwandte Seiten

- [Anwendungen testen](anwendungen-testen.md)
- [Verbesserungen der Debug-Ausgabe](../../../../eng-us/features/6.0/debug-output-improvements.md)
- [Wenn etwas schiefgeht](../../wenn-etwas-schiefgeht.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website mit Dashboard
ist für Mitte 2027 geplant: Bitte werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen
früher kommen. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
