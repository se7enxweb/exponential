# Der Zeitplaner von Velocity

Diese Seite ist für Entwickler von Velocity-Anwendungen, die regelmäßige Arbeit brauchen (Sitzungen ablaufen lassen,
Dateien rotieren, ein Bericht um 09:00) ohne eine Cron-Tabelle. Der Zeitplaner ist ein kleines Cron im Server. Er führt
einen **Handler** in einem Intervall oder zu festen Tageszeiten aus, ohne zweiten Prozessmanager und ohne die Anfragen zu
blockieren, die der Server beantwortet. Gilt für Exponential Velocity 0.0.4.x; zuerst veröffentlicht am 21. Juli 2026
(Commit `62d9b23`). Referenz: [Engine-Einstellungen](../../spezifikationen/6.0/velocity-engine-einstellungen.md).

**Ist das für meine Exponential-Website?** Meist nicht. Eine Aufgabe nennt einen Handler (`tasks/cleanup`), den der
Server mit `Q::event()` verteilt, genau wie er ein Ereignis einer Webanfrage verteilt. Sie braucht also die
Anwendungsschicht der Engine (`--app` oder eine Anwendung, die `handlers/` bereitstellt). Sie führt kein beliebiges
PHP-Kommandozeilenskript aus; dafür verwenden Sie weiter das Cron des Systems. Die eigenen regelmäßigen Aufgaben von
Exponential laufen über `php runcronjobs.php` (siehe [Cronjobs in der Konsole](cronjobs-in-der-konsole.md)).

## Worauf Sie sich verlassen können

- Jeder Lauf wird vom Server abgezweigt (fork), sodass eine langsame Aufgabe nie die Ereignisschleife aufhält. Eine
  Aufgabe, die scheitert, wird protokolliert und endet dort.
- Keine verlorenen oder doppelten Läufe um einen Neustart: Ein Neustart in der Minute, in der eine zeitbasierte Aufgabe
  fällig ist, löst sie nicht zweimal aus, und eine Intervall-Aufgabe wartet vor ihrem ersten Lauf ein volles Intervall.
- Eine Aufgabe wird als gelaufen markiert, bevor sie abgezweigt wird. Stürzt der Server genau in diesem Moment ab, wird
  der Lauf übersprungen, nie verdoppelt.

## Eine Aufgabe konfigurieren

1. Fügen Sie die Aufgaben unter `Q.scheduler` in einer Website-Datei oder im JSON von `--config` hinzu. Jeder Schlüssel
   ist ein Aufgabenname:

   ```json
   { "Q": { "scheduler": {
       "cleanup":         { "handler": "tasks/cleanup", "every": 3600 },
       "daily-report":    { "handler": "tasks/report",  "times": ["09:00"] },
       "business-check":  { "handler": "tasks/check",   "times": ["09:00", "12:00", "17:00"], "weekdays": ["mon", "wed", "fri"] },
       "monthly-invoice": { "handler": "tasks/invoice", "times": ["00:00"], "monthdays": [1] }
   } } }
   ```

2. Schreiben Sie den Handler, eine Funktion in `handlers/tasks/cleanup.php`. Sie erhält den Aufgabennamen und ein Flag:

   ```php
   <?php
   function tasks_cleanup(&$params, &$result)
   {
       // $params['task'] is "cleanup", $params['scheduled'] is true
       MyApp_Sessions::expireOld();
   }
   ```

3. Prüfen Sie die Konfiguration, ohne einen Server zu starten:

   ```bash
   php sbin/qbixserver.php --root=web -t --config=<site file>
   ```

| Feld | Standard | Was es tut |
|---|---|---|
| `handler` | Pflicht | Handler-Pfad, verteilt von `Q::event()`; eine Aufgabe ohne ihn wird ignoriert |
| `every` | keiner | Alle so viele Sekunden ausführen, gezählt ab dem Start |
| `times` | keiner | Zu diesen Zeiten `HH:MM` ausführen (24 Stunden, Zeitzone von PHP auf dem Server) |
| `weekdays` | jeden Tag | Mit `times`: nur an diesen Tagen (`mon` bis `sun` oder der volle Name) |
| `monthdays` | jeden Tag | Mit `times`: nur an diesen Tagen des Monats (1 bis 31) |

Eine Aufgabe braucht `every` oder `times`; `weekdays` und `monthdays` gelten nur für Aufgaben mit `times`. Der
Zeitplaner sieht einmal pro Sekunde auf die Uhr.

## Testen

1. Starten Sie den Server im Vordergrund mit einer Aufgabe, die `"every": 5` hat, und einem Handler, der eine Zeile in
   eine Datei schreibt.
2. Warten Sie zehn Sekunden. Die Datei hat eine Zeile, nicht zwei (der erste Lauf kommt nach einem Intervall).
3. Lassen Sie den Handler eine Ausnahme werfen. Die Fehlerausgabe des Servers zeigt
   `scheduler: <name> failed: <message>`, und der nächste Lauf findet trotzdem statt.

## Eingebaute Aufgaben

Der Server fügt zwei eigene Aufgaben hinzu, wenn er sie braucht (Namen, die mit einem Unterstrich beginnen):

- `_cacheSweep` entfernt abgelaufene Einträge des [Antwort-Caches](velocity-antwort-cache.md)
  (`Q.web.cache.sweep.every`, Standard 300 Sekunden);
- `_certRenewal` erneuert im Hintergrund Zertifikate [automatischer Hosts](velocity-https-zertifikate.md).

Geben Sie einer eigenen Aufgabe keinen Namen mit führendem Unterstrich.

## Sicherheit

- Ein als root gestarteter Server gibt root im abgezweigten Kindprozess ab, bevor der Handler läuft
  ([Worker-Pool](../../spezifikationen/6.0/velocity-worker-pool.md), „The user workers run as“). Ein Kindprozess, der
  nicht wechseln kann, wird beendet.
- Eine abgezweigte Aufgabe wird wie ein Worker verfolgt, sodass `Q.webserver.requestTimeout` (Standard 30 Sekunden) für
  sie gilt.
- Ohne `pcntl` (Windows) läuft eine Aufgabe im Serverprozess und blockiert die Ereignisschleife, so lange sie dauert.
  Halten Sie solche Aufgaben kurz.

## Grenzen

- Aufgaben laufen nur, solange der Server läuft; verpasste Zeiten werden nicht nachgeholt.
- Zeiten werden auf die Minute genau verglichen und verwenden die Zeitzone von PHP auf dem Server.
- Eine Aufgabe, die länger läuft als ihr Intervall, kann sich selbst überlappen; der Zeitplaner wartet nicht auf den
  vorigen Lauf.

## Verwandte Seiten

- [Velocity-Webserver](velocity-webserver.md), [Engine-Einstellungen](../../spezifikationen/6.0/velocity-engine-einstellungen.md)
- [Cronjobs in der Konsole](cronjobs-in-der-konsole.md), [Befehle, Cronjob-Teile und Modulansichten als Klassen](../../spezifikationen/6.0/ausfuehrbare-befehle-cronjobs-ansichten.md)
- [Hinweise zur Aktualisierung der Velocity-Engine (0.0.4.27 bis 0.0.4.42)](../../../eng-us/bc/6.0/velocity-engine-upgrade-notes.md)
- [Changelog: Engine Exponential Velocity](../../../changelogs/extensions/exponential-velocity.md)
- Chronik: [Juli 2026](../../../eng-us/history/velocity/2026-07.md) (der Zeitplaner kommt), [25. bis 30. September](../../../eng-us/history/velocity/2026-09e.md) (Aufgaben geben root ab)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, tragen zum Projekt
bei und entwickeln die Produkte mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein Dashboard
sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen
früher bereitstehen. Beginnen Sie mit [Partner: hier beginnen](../../hilfe/partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
