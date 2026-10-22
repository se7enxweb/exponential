---
title: Logs, der Log-Viewer und Anfrage-Logs
audience: php-developer, developer
category: php-development
order: 390
summary: Die Log-Dateien, die ein PHP-Entwickler liest und schreibt - error.log, warning.log, deprecated.log, 404.log, security.log und eigene über eZLog::write() - der Log-Viewer unter Setup > Logs und auf der Kommandozeile mit explogs.php, Fehlerreferenzen, Klassifizierung von Anfragen, Rotation und Regeln für Log-Zeilen.
keywords: Logs, Log-Viewer, Anfrage-Logs, Fehlerreferenz, Log-Rotation, Datenschutz, Maskierung, logs, eZLog, error.log, warning.log, notice.log, deprecated.log, 404.log, security.log, request logs, log viewer, explogs.php, error reference, log rotation, request classifier, expRequestLog, privacy, masking
type: help
---
# Logs, der Log-Viewer und Anfrage-Logs

Diese Seite richtet sich an PHP-Entwickler, die Log-Zeilen schreiben oder suchen, was schiefgegangen ist. Exponential
schreibt seine Logs unter `var/log/` (oder in das Log-Verzeichnis der Site) und zeigt sie unter **Setup > Logs** und auf
der Kommandozeile an. Seit 6.0.15 haben Antworten „nicht gefunden“ und verdächtige Anfragen eigene Logs, sodass
`error.log` die echten Fehler behält.

## Inhalt

1. [Die Logs](#die-logs)
2. [Ein eigenes Log schreiben](#ein-eigenes-log-schreiben)
3. [Der Log-Viewer](#der-log-viewer)
4. [Der Log-Viewer auf der Kommandozeile](#der-log-viewer-auf-der-kommandozeile)
5. [Fehlerreferenzen](#fehlerreferenzen)
6. [Anfrage-Logs: 404 und Sicherheit](#anfrage-logs-404-und-sicherheit)
7. [Rotation](#rotation)
8. [Regeln für Log-Zeilen](#regeln-für-log-zeilen)

## Die Logs

| Datei | Enthält |
|---|---|
| `error.log` | echte Fehler: ein Datenbankfehler, ein Template, das nicht kompiliert, ein PHP-Fehler, `eZDebug::writeError()` |
| `warning.log` | Warnungen, einschließlich abgelehnter Formular-Tokens, wenn die Stufe protokolliert wird |
| `deprecated.log` | PHP-Deprecations, jede unterschiedliche Meldung einmal pro Anfrage (`DeprecatedLog=enabled`) |
| `404.log` | Antworten 404 und 410, mit dem Grund, den der Kernel gefunden hat |
| `notice.log` | Antworten 401 und 403 sowie Hinweise, wenn sie protokolliert werden |
| `security.log` | Anfragen, die der Anfrage-Klassifizierer als Sondierung, Traversal, Injection, Angriffswerkzeug oder Burst einstuft |
| `debugbar.log` | Schreibvorgänge aus der Debug-Leiste (in `var/<site>/log/`) |
| Ihre eigenen `*.log` | was Ihr Code mit `eZLog::write()` schreibt |

## Ein eigenes Log schreiben

```php
eZLog::write( 'archived object ' . (int)$objectID . ' version ' . (int)$version, 'acmereport.log' );
```

`eZLog::write( $message, $logName = 'common.log', $dir = 'var/log' )` hängt eine Zeile mit der Uhrzeit an. Ein Befehl
kann `$this->log( $message )` verwenden, das standardmäßig `var/log/<script name>.log` schreibt
([Einen Konsolenbefehl schreiben](einen-konsolenbefehl-schreiben.md#die-hilfsmethoden)).

## Der Log-Viewer

**Setup > Logs** listet jedes Log des Kernels, der Erweiterungen und des Webservers auf, mit Suche, einem Navigator,
Permalinks zu Zeilen, Live-Verfolgung, einer Fehlerübersicht wiederholter Meldungen, Log-Zustand, Archiven und Downloads.
E-Mail-Adressen, IP-Adressen und Geheimnisse sind standardmäßig maskiert; ein Benutzer mit `setup/unmasklogs` kann die
Maskierung für sich selbst ausschalten, und der Audit-Trail hält das fest ([Log-Viewer](../../funktionen/6.0/log-betrachter.md)).

## Der Log-Viewer auf der Kommandozeile

`bin/php/explogs.php` ist derselbe Viewer, nur lesend:

```bash
php bin/php/explogs.php --list                                        # the log files: group, size, time, lines a minute
php bin/php/explogs.php --tail=var/log/error.log --lines=50           # the last 50 entries
php bin/php/explogs.php --tail=var/log/error.log --follow             # follow it
php bin/php/explogs.php --search=timeout --file=var/log/error.log --level=error,warning
php bin/php/explogs.php --reference=<reference>                       # where an error page's reference is
php bin/php/explogs.php --related=var/log/error.log:<offset>          # the lines of the same request
```

Er beendet mit 0, wenn etwas gefunden wurde, mit 1, wenn nichts gefunden wurde, mit 2 bei einem Fehler, sodass Skripte
ihn verwenden können.

## Fehlerreferenzen

Eine Fehlerseite zeigt eine Referenz. Dieselbe Referenz steht in der Log-Zeile, mit der Anfrage, zu der sie gehört,
sodass ein Bildschirmfoto eines Benutzers direkt zur Meldung führt: `--reference=<reference>` auf der Kommandozeile
oder die Suche des Log-Viewers ([Einen Fehler anhand seiner Referenz finden](../../anleitungen/fehler-anhand-der-referenz-finden.md)).

## Anfrage-Logs: 404 und Sicherheit

`expRequestLog` klassifiziert jede Anfrage einmal, gleich auf welche Weise sie endet (normales Ende,
`eZExecution::cleanExit()`, das Fehlermodul, ein fataler Fehler), und schreibt **eine Zeile pro Anfrage in ein Log**:

- eine fehlende Seite geht nach `404.log` mit dem Grund (ein undefiniertes Modul oder eine undefinierte View, ein Knoten,
  der nicht da ist, eine Anfrageregel);
- eine Sondierung oder ein Angriff geht nach `security.log` mit seiner Kategorie und Regel, unabhängig vom Status;
- ein echter Fehler bleibt in `error.log`.

Nichts wird blockiert; der Klassifizierer klassifiziert nur. Client-Adressen werden maskiert, Werte in Query-Strings
durch `***` ersetzt. Listener können auf ein Urteil reagieren ([Anfrage-Logs](../../funktionen/6.0/anfrageprotokolle.md)). Für
Ihren Code bedeutet das: Eine View, die mit `$Module->handleError( eZError::KERNEL_NOT_FOUND, 'kernel' )` „nicht
gefunden“ antwortet, landet in `404.log`, nicht in `error.log`, also protokollieren Sie sie nicht noch einmal als Fehler.

## Rotation

Wenn ein Log über `site.ini [FileSettings] LogFileMaxSize` (2 MB) hinauswächst, rotiert es beim nächsten Schreiben und
behält `LogFileRotateCount` (3) Archive. `eZDebug` und `eZLog` verwenden denselben Rotator, sodass auch Ihr eigenes Log
rotiert, und zwar sicher, selbst wenn Velocity und PHP-FPM dieselbe Datei schreiben
([Log-Rotation](../../funktionen/6.0/log-rotation.md)):

```bash
php bin/php/explogs.php --archives
php bin/php/explogs.php --rotate=error
php bin/php/explogs.php --prune --dry-run
```

## Regeln für Log-Zeilen

- **Ein Ereignis, eine Zeile.** Entfernen Sie Zeilenumbrüche aus Werten, damit ein Eintrag keinen anderen vortäuschen kann.
- **Nennen Sie die Quelle.** `__METHOD__` als Label von `eZDebug`; der Name Ihrer Erweiterung in Ihrem eigenen Log.
- **IDs, keine Daten.** Protokollieren Sie IDs und Zähler, keine Inhalte, E-Mail-Adressen oder Namen.
- **Niemals Geheimnisse.** Keine Passwörter, Tokens, Schlüssel, Sitzungs-IDs.
- **Stufen bedeuten etwas.** `writeError()` für Fehler, die jemand beheben muss, `writeWarning()` für Probleme, die der
  Code behandelt hat, Hinweise für Informationen.

## Verwandte Seiten

- [Debugging mit eZDebug](debugging-mit-ezdebug.md)
- [Debug-Ausgabe und Logs für Entwickler](../erweiterungsentwicklung/teil-1-vom-benutzer-zum-entwickler/debug-ausgabe-und-logs-fuer-entwickler.md)
- [Wenn etwas schiefgeht](../wenn-etwas-schiefgeht.md)
- [Freie Dokumentation](../freie-dokumentation.md): diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
