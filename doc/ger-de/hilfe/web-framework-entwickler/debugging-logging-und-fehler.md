---
title: Debugging, Logging und Fehler
audience: web-framework-developer, developer
category: web-frameworks
order: 370
summary: Die Gegenstücke in Exponential zum Symfony-Profiler, zu Laravel Telescope und Debugbar, zur Django Debug Toolbar und zu den Loggern der Frameworks – Debug-Ausgabe und die Exp-Debug-Leiste, Kommentare mit Template-Pfaden, eZDebug und eZLog, die Logdateien und der Log-Viewer, Fehlerreferenzen, das Audit-Protokoll und Debugging bei persistenten Workern.
keywords: Debugging, debugging, Debug-Leiste, debug bar, Profiler, profiler, DebugOutput, eZDebug, writeNotice, writeWarning, writeError, eZLog, Logs, logs, error.log, warning.log, Log-Viewer, log viewer, Template-Pfad-Kommentare, template path comments, Fehlerreferenz, error reference, Audit-Protokoll, audit trail, Monolog, Telescope, Django Debug Toolbar, Web-Framework-Entwickler, web framework developer
type: help
---
# Debugging, Logging und Fehler

Diese Seite richtet sich an Entwickler, die sich auf eine Profiler-Toolbar und einen PSR-3-Logger verlassen. Exponential
hat eine eigene Debug-Ausgabe, die zu einer angehefteten **Debug-Leiste** geworden ist, eigene Logging-Aufrufe und eine
Seite in der Administrationsoberfläche, die die Logs liest
([Debugging und Logs](../ersteller/anwendungsentwickler/fehlersuche-und-logs.md)).

## Inhalt

1. [Debug-Ausgabe und die Debug-Leiste](#debug-ausgabe-und-die-debug-leiste)
2. [Debug- und Logzeilen schreiben](#debug--und-logzeilen-schreiben)
3. [Die Logdateien](#die-logdateien)
4. [Von der Fehlerseite zur Logzeile](#von-der-fehlerseite-zur-logzeile)
5. [Debugging unter Velocity](#debugging-unter-velocity)
6. [Im Vergleich mit anderen Frameworks](#im-vergleich-mit-anderen-frameworks)

## Debug-Ausgabe und die Debug-Leiste

```ini
# settings/override/site.ini.append.php, or the dev variant of it (development only)
[DebugSettings]
DebugOutput=enabled

[TemplateSettings]
ShowTemplatePathComments=enabled
```

Die Debug-Leiste zeigt für die Seite die Abfragen, die verwendeten Templates, Einstellungen, Zeitmessungen und Caches
und bietet Bedienelemente für die Debug-Einstellungen und die Caches ([Exp-Debug-Leiste](../../funktionen/6.0/exp-debug-leiste.md)).
Sie lässt sich nur für Ihre eigene Adresse einblenden. Kommentare mit Template-Pfaden geben aus, welche Datei welches
Markup geschrieben hat ([Kommentare mit Template-Pfaden](../../funktionen/6.0/template-pfad-kommentare.md)).
Schalten Sie die Debug-Ausgabe auf einem Produktiv-Siteaccess nie für alle ein; sie zeigt Interna. Legen Sie sie in die
`dev`-Variante Ihrer Einstellungen ([Umgebungen und Geheimnisse](umgebungen-und-geheimnisse.md)).

## Debug- und Logzeilen schreiben

```php
eZDebug::writeNotice( 'Imported 12 items', __METHOD__ );
eZDebug::writeWarning( 'Item 42 had no title', __METHOD__ );
eZDebug::writeError( 'Feed could not be read: ' . $message, __METHOD__ );
eZLog::write( 'archive sent for object ' . (int)$objectID, 'expdemo.log' );
```

Warnungen und Fehler landen unabhängig von der Debug-Einstellung in `var/log/warning.log` und `var/log/error.log`;
`eZLog::write()` schreibt ein eigenes Log im Log-Verzeichnis. Das sind keine PSR-3-Logger; wenn Sie Monolog in Ihrer
Erweiterung verwenden, schreibt es eigene Dateien. Schreiben Sie nie Passwörter, Schlüssel, Token oder personenbezogene
Daten ins Log.

## Die Logdateien

| Datei | Enthält |
|---|---|
| `var/log/error.log`, `warning.log` | Fehler und Warnungen |
| `var/log/security.log` | Sicherheitsereignisse |
| `var/log/csp.log` | Berichte der Content Security Policy |
| `var/log/requestrules.log` | die Aktion `log` der Request-Regeln |
| Ihre eigenen `*.log` | was Ihr Code schreibt |

**Setup > Logs** zeigt sie mit einer Zusammenfassung wiederholter Meldungen an ([Log-Viewer](../../funktionen/6.0/log-betrachter.md));
Logs werden rotiert ([Log-Rotation](../../funktionen/6.0/log-rotation.md)); Request-Logs zeichnen Requests mit
Zeitmessungen auf ([Request-Logs](../../funktionen/6.0/anfrageprotokolle.md)). Sicherheitsrelevante Aktionen gehören in das
Audit-Protokoll ([Audit-Protokoll](../../funktionen/6.0/pruefprotokoll.md)). In einer Multisite-Installation können die
Log-Pfade je Site verschieden sein ([Log- und Cache-Pfade bei mehreren Sites](../../funktionen/6.0/log-und-cache-pfade-fuer-mehrere-websites.md)).

## Von der Fehlerseite zur Logzeile

Fehlerseiten tragen eine Referenz; durchsuchen Sie die Logs danach, um die vollständige Meldung zu finden
([Einen Fehler anhand seiner Referenz finden](../../anleitungen/fehler-anhand-der-referenz-finden.md)). Die erste Seite bei
jedem Problem ist [Wenn etwas schiefgeht](../wenn-etwas-schiefgeht.md).

## Debugging unter Velocity

- Worker halten Klassen und Einstellungen im Speicher: Führen Sie nach einer Änderung `./console exp:velocity deploy`
  (oder `restart`) aus, sonst debuggen Sie alten Code.
- Vergleichen Sie mit der Engine des eingebauten PHP-Servers (`--engine=php`), die jeden Request frisch ausführt, wenn
  Sie vermuten, dass Zustand zwischen Requests erhalten bleibt ([Velocity-Engines](../../bc/6.0/velocity-engines.md)).
- Umschließen Sie `eZExecution::cleanExit()` nie mit `catch ( Exception $e )`: Unter Velocity beendet es den Request
  mit einer Exception.

## Im Vergleich mit anderen Frameworks

| Framework | Werkzeug | Exponential |
|---|---|---|
| Symfony | Web Profiler, Monolog | Debug-Leiste, `eZDebug`, `eZLog`, **Setup > Logs** |
| Laravel | Debugbar, Telescope, `Log::` | dasselbe |
| Django | Debug Toolbar, `logging` | dasselbe |
| Rails | Logs, `byebug` | dasselbe; Xdebug funktioniert wie in jedem PHP-Projekt |
| Spring | Actuator, SLF4J | **Setup > Systeminformationen**, Request-Logs |

## Verwandte Seiten

- [Anleitung zur Debug-Leiste](../../bc/6.0/debug-leiste.md)
- [Verbesserungen der Debug-Ausgabe](../../funktionen/6.0/verbesserte-debug-ausgabe.md)
- [Template-Fehler und Debugging](../template-sprache/fehler-und-fehlersuche.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
