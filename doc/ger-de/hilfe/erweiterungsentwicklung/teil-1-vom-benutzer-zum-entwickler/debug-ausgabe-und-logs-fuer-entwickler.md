---
title: Debug-Ausgabe und Logs für Entwickler
audience: user, developer, designer, app-creator
category: extension-development
order: 190
summary: Sehen, was das System tut, während es eine Seite aufbaut - die Exp Debug-Leiste und ihre Einstellungen, verwendete Templates, Pfadkommentare, die Log-Dateien unter var/log sowie eigene Debug-Zeilen und Logs aus PHP.
keywords: Debug, Debug-Leiste, Debug-Ausgabe, Logs, Log-Dateien, Fehlersuche, Log-Viewer, debug, DebugOutput, debug bar, DebugByIP, ShowUsedTemplates, ShowTemplatePathComments, error.log, warning.log, deprecated.log, eZDebug, eZLog, log viewer, troubleshooting
type: help
---
# Debug-Ausgabe und Logs für Entwickler

Teil 1, Kapitel 19. Wenn eine Erweiterung nicht tut, was Sie erwarten, sagt das System fast immer, warum, wenn Sie
wissen, wo Sie nachsehen müssen. Dieses Kapitel zeigt die drei Stellen: die Debug-Ausgabe unter jeder Seite, die
Log-Dateien und die Zeilen, die Sie selbst schreiben.

## Die Debug-Ausgabe: die Exp Debug-Leiste

Mit `[DebugSettings] DebugOutput=enabled` in `site.ini` trägt jede Seite einen Bericht darüber, wie sie entstanden ist.
In Exponential 6 ist dieser Bericht die **Exp Debug-Leiste**: eine an die Seite geheftete Leiste mit einer laufenden
Zusammenfassung der Anfrage und ein Panel mit Reitern für die Abschnitte des Berichts (Meldungen, Zeitmessung,
Templates, SQL und mehr). Ihr Reiter **Einstellungen** zeigt jede Debug-Einstellung, den geltenden Wert und die Datei,
aus der er stammt, und kann ihn in einem Geltungsbereich Ihrer Wahl ändern; jede Änderung läuft über dieselbe Engine
wie `exp:ini`, wird protokolliert und kann in der Leiste rückgängig gemacht werden. Die Einzelheiten stehen in
[Die Exp Debug-Leiste](../../../bc/6.0/debug-leiste.md).

### Die Einstellungen

Alle in `site.ini`:

| Block | Einstellung | Standard | Bedeutung |
|---|---|---|---|
| `[DebugSettings]` | `DebugOutput` | `disabled` | Den Debug-Bericht überhaupt anzeigen |
| `[DebugSettings]` | `DebugByIP` + `DebugIPList[]` | `disabled` | Ihn nur diesen Adressen anzeigen (CIDR-Bereiche erlaubt) |
| `[DebugSettings]` | `DebugByUser` + `DebugUserIDList[]` | `disabled` | Ihn nur diesen Benutzer-IDs anzeigen |
| `[DebugSettings]` | `DebugRedirection` | `disabled` | Vor einer Weiterleitung anhalten, damit Sie den Bericht der weiterleitenden Seite lesen können |
| `[DebugSettings]` | `ScriptDebugOutput` | `disabled` | Debug-Ausgabe auch für Kommandozeilenskripte |
| `[DebugSettings]` | `AlwaysLog[]` | `error` | Welche Meldungsstufen auch ohne Debug-Ausgabe in Log-Dateien geschrieben werden |
| `[DebugSettings]` | `DeprecatedLog` | `enabled` | PHP-Deprecations gehen nach `deprecated.log`, jede unterschiedliche Meldung einmal pro Anfrage |
| `[TemplateSettings]` | `ShowUsedTemplates` | `disabled` | Jedes Template auflisten, das eine Seite verwendet hat, und welcher Override es gewählt hat |
| `[TemplateSettings]` | `ShowTemplatePathComments` | `disabled` | HTML-Kommentare, die das Template jedes Seitenteils nennen |
| `[TemplateSettings]` | `Debug` | `disabled` | Mehr Einzelheiten von der Template-Engine |
| `[DatabaseSettings]` | `SQLOutput` | `disabled` | Jede SQL-Abfrage mit ihrer Dauer |

Prüfen Sie jede davon mit `./console exp:ini where site.ini/<Block>/<Setting>`.

> **Warnung**
> Die Debug-Ausgabe zeigt Dateipfade, SQL und Einstellungen. Auf jedem Rechner, den andere erreichen können, beschränken
> Sie sie mit `DebugByIP` oder `DebugByUser`, und schalten Sie sie vor dem Go-live aus.

### Den Bericht lesen

- **Fehler und Warnungen** zuerst: Jede hat eine Bezeichnung, meist die Klasse oder das Template, das sie ausgelöst hat,
  zum Beispiel `eZTemplate @ design:node/view/full.tpl:12[5]` (Datei, Zeile und Spalte des Templates).
- **Templates**: angefordertes Template, verwendetes Template und die Override-Regel, die es gewählt hat. Wenn Ihr
  Override nicht greift, sagt diese Tabelle, welches Template stattdessen gewonnen hat.
- **Zeitmesspunkte**: wohin die Zeit gegangen ist.
- **SQL**: mit `SQLOutput=enabled` jede Abfrage; eine Abfrage, die hundertmal läuft, ist eine Schleife, die einmal
  abrufen sollte.

## Die Template-Pfadkommentare

Mit `ShowTemplatePathComments=enabled` sehen Sie sich den Seitenquelltext an: Jeder Teil des HTML ist von Kommentaren
umschlossen, die sein Template nennen. Das ist der schnellste Weg von „dieser Teil der Seite“ zu „diese Datei“.
Siehe [Template-Pfadkommentare](../../../../eng-us/features/6.0/template-path-comments.md).

## Die Log-Dateien

Log-Dateien werden unter `var/log/` geschrieben (manche Installationen verwenden `var/<site>/log/` für Website-spezifische
Logs). Diejenigen, die ein Entwickler am häufigsten liest:

| Datei | Was darin steht |
|---|---|
| `error.log` | Fehler: werden immer protokolliert (`AlwaysLog[]=error`) |
| `warning.log` | Warnungen, wenn sie protokolliert werden |
| `notice.log`, `debug.log`, `strict.log` | Die niedrigeren Stufen, wenn sie protokolliert werden |
| `deprecated.log` | PHP-Deprecations: die Aufgabenliste für die nächste PHP-Version |
| `storage.log` | Vorgänge im Dateispeicher |
| `404.log` | Nicht gefundene Seiten (`[DebugSettings] NotFoundLog`) |
| `csp.log` | Verstöße gegen die Content Security Policy: Ihr Inline-Skript steht hier |
| `security.log`, `auth.log` | Sicherheitsereignisse und Anmeldungen |

```bash
tail -f var/log/error.log            # follow it while you reload the page
grep -c "" var/log/deprecated.log    # how many deprecation lines
```

Die Administration hat einen Viewer für dieselben Dateien, mit Suche und Permalinks: **Setup > Logs**, beschrieben in
[Der Log-Viewer](../../../../eng-us/features/6.0/log-viewer.md). Jeder Fehler trägt eine Referenz, die zu jeder Zeile seiner
Anfrage führt: [Einen Fehler anhand seiner Referenz finden](../../../anleitungen/fehler-anhand-der-referenz-finden.md). Auf
der Kommandozeile heißt das `./console exp:explogs`.

## Eigene Debug-Zeilen schreiben

Aus PHP verwenden Sie `eZDebug`. Jede Methode ist statisch und nimmt die Meldung, eine optionale Bezeichnung und eine
optionale Stilklasse (Signaturen aus `lib/ezutils/classes/ezdebug.php`):

```php
<?php
eZDebug::writeDebug( 'Value of $nodeID: ' . $nodeID, __METHOD__ );
eZDebug::writeNotice( 'Cache miss for ' . $key, 'mysite_hello' );
eZDebug::writeWarning( 'Setting MySettings/Limit is missing, using 10', __METHOD__ );
eZDebug::writeError( 'Could not load node ' . $nodeID, __METHOD__ );
```

| Methode | Stufe | Wofür |
|---|---|---|
| `writeDebug()` | debug | vorübergehende Werte während der Arbeit; entfernen Sie sie danach |
| `writeNotice()` | notice | Informationen, die beim Lesen eines Berichts helfen |
| `writeWarning()` | warning | etwas Unerwartetes, von dem sich der Code erholt hat |
| `writeError()` | error | etwas ist fehlgeschlagen; wird immer in `error.log` geschrieben |

Verwenden Sie `__METHOD__` als Bezeichnung: Der Bericht nennt dann Klasse und Methode.

Aus einem Template schreibt `{debug-log var=$node msg='node in my template'}` einen Wert in den Debug-Bericht (siehe
[Templates debuggen](../teil-2-builder/templates-debuggen.md)).

## Eine eigene Log-Datei schreiben

Für Dinge, die Sie unabhängig von den Debug-Einstellungen aufbewahren möchten, schreiben Sie mit `eZLog::write()` eine
eigene Log-Datei (`lib/ezfile/classes/ezlog.php`):

```php
<?php
// static function write( $message, $logName = 'common.log', $dir = 'var/log' )
eZLog::write( 'Imported ' . $count . ' items', 'mysite_hello.log' );
```

Die Datei erscheint unter `var/log/`. Schreiben Sie nie personenbezogene Daten, Passwörter oder Schlüssel in ein Log.

## Eine Routine für die Fehlersuche

1. Reproduzieren Sie das Problem auf Ihrer Entwicklungsinstallation.
2. Lesen Sie den Debug-Bericht: zuerst die Fehler, dann die Template-Tabelle.
3. Verfolgen Sie das Fehler-Log mit `tail`, während Sie neu laden.
4. Fügen Sie `eZDebug::writeDebug()`-Zeilen um den Code ein, den Sie verdächtigen; laden Sie neu; lesen Sie.
5. Wenn Sie es verstanden haben, entfernen Sie die Debug-Zeilen, korrigieren Sie den Code und prüfen Sie, dass das Log
   ruhig bleibt.

[Erweiterungen debuggen](../teil-3-erweiterungsentwickler/erweiterungen-debuggen.md) geht weiter: Laden von Klassen,
Autoloads, Caches und der RAD-Gesundheitscheck.

## Verwandte Seiten

- Zurück: [Schritt für Schritt von einer Erweiterung lernen](schritt-fuer-schritt-von-einer-erweiterung-lernen.md)
- Weiter: [Die Community um Hilfe bitten](die-community-fragen.md)
- [Die Exp Debug-Leiste](../../../bc/6.0/debug-leiste.md)
- [eZDebug und Logging](../teil-8-referenz/ezdebug-und-protokollierung.md)
- [Wenn etwas schiefgeht](../../wenn-etwas-schiefgeht.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
