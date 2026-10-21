---
title: "Lektion 4.2: Cron auf dem Server"
audience: trainer, hosting, admin
category: training
order: 42
summary: Die Teilnehmenden erklären Cronjob-Teile und -Skripte, schreiben eine Crontab für eine Kunden-Website unter dem Benutzer der Website mit dem richtigen PHP-Programm und finden heraus, warum Aufgaben nicht laufen. 45 Minuten.
keywords: hosting und betrieb, cron, crontab, runcronjobs.php, cronjob-teile, frequent, infrequent, cache_cleanup, session_gc, siteaccess, sperre, cronjob.ini, benachrichtigungen nicht versendet, hosting and operations, cronjob parts, lock, notifications not sent
type: help
---
# Lektion 4.2: Cron auf dem Server

Eine Exponential-Website ist auf geplante Aufgaben angewiesen: Benachrichtigungen, Workflows, zeitgesteuertes
Veröffentlichen, verzögerte Suchindizierung und Aufräumen. Fehlt Cron, geht nichts lautstark kaputt; Dinge passieren
einfach nicht mehr. Diese Lektion zeigt Hosting-Mitarbeitenden, welche Teile eine Website braucht, wie man sie korrekt
installiert und wie man herausfindet, warum eine Aufgabe nicht gelaufen ist.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Hosting und Betrieb](../README.md) |
| Modul | [Modul 4: Bereitstellung und Cron](README.md) |
| Dauer | 45 Minuten (20 Präsentation, 25 Übung) |
| Niveau | Fortgeschrittene |
| Zielgruppe | Systemadministratoren und Support-Mitarbeitende im Hosting |
| Voraussetzungen | [Lektion 4.1](eine-aenderung-bereitstellen.md); eine Schulungsinstallation und eine Shell als Benutzer der Website |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- den Unterschied zwischen einem Cronjob-Skript und einem Cronjob-Teil erklären und die Skripte eines Teils auflisten;
- die Teile nennen, die jede gehostete Website braucht (`frequent`, den Standardteil, `infrequent`), und was jeder tut;
- Crontab-Zeilen schreiben, die im Wurzelverzeichnis der Installation laufen, als Benutzer der Website, mit dem
  PHP-Programm, das die Website verwendet, und dem öffentlichen Siteaccess des Kunden;
- die üblichen Fehler diagnostizieren: Benachrichtigungen werden nicht versendet, ein Teil ist immer gesperrt, Dateien
  gehören root.

## Hinweise für Trainer

### So präsentieren Sie es

Quelle: [Cronjobs](../../../hosting/betrieb/cronjobs.md) und
[Cronjobs: ausführen, planen und verfolgen](../../../../anleitungen/cronjobs.md).

1. **Teile und Skripte** (4 Minuten). Ein Skript ist eine PHP-Datei in einem Cronjob-Verzeichnis; ein Teil ist eine
   benannte Liste von Skripten in `settings/cronjob.ini`, die zusammen laufen. `php runcronjobs.php <part>` führt einen
   Teil aus; ohne Namen führt es den Standardteil aus, den die Administration **Global** nennt.
2. **Was eine Website braucht** (5 Minuten). `frequent` immer (Benachrichtigungen, Workflows, Inhaltsaufgaben, das
   Audit-Protokoll); den Standardteil immer (zeitgesteuertes Zurückziehen und Verstecken, verzögerte Indizierung);
   `infrequent` (Linkprüfung, Aufbewahrung der E-Mail-Einstellungen); `cache_cleanup` für stark besuchte Websites;
   `session_gc.php`, wo die Sitzungsbereinigung des Betriebssystems nicht läuft; `logs`, wenn der Kernel die Logs
   verwaltet.
3. **Die Crontab** (5 Minuten). Zeigen Sie das Beispiel aus *Cronjobs*: `cd` in das Wurzelverzeichnis der
   Installation, `php runcronjobs.php -q -s <siteaccess> <part>`, Ausgabe in eine Logdatei. `-s` nennt den Siteaccess,
   auf dessen Inhalte und Adressen die Aufgaben wirken.
4. **Benutzer und PHP** (3 Minuten). Installieren Sie die Zeilen in der Crontab des Benutzers der Website, mit dem
   vollständigen Pfad des PHP-Programms, mit dem die Website läuft. Als root verweigern Skripte den Start ohne
   `--allow-root-user`, und ihre Dateien gehören root.
5. **Hoster, die Cron einschränken, Wartung, mehrere Knoten** (3 Minuten). Eine Zeile für `frequent` ist das Minimum.
   Der Wartungsmodus stoppt Cron nicht. Bei mehreren Knoten: eine Crontab auf einem Knoten.

### Häufige Fragen

**Kann der Velocity-Scheduler Cron ersetzen?** Nein. Er führt kein beliebiges PHP-Skript für die Kommandozeile aus;
behalten Sie das Cron des Systems.

**Was bedeutet „Cronjob part locked by other process“?** Ein früherer Lauf desselben Skripts läuft noch oder wurde
beendet. Eine Sperre, die älter ist als `[CronjobSettings] MaxScriptExecutionTime` (43200 Sekunden), wird übernommen.

**Wo sehe ich jeden Teil?** `php runcronjobs.php --list` oder `./console list cron`.

**Gibt es eine fertige Datei?** Das Repository liefert `exponential.cron` mit, mit einer Variablen für das
Wurzelverzeichnis der Installation und das PHP-Programm.

### Stolperfallen

- **Eine Crontab unter root.** Die Aufgaben schreiben dann Dateien, die die Website nicht ersetzen kann. Immer
  `crontab -e -u <site user>`.
- **Das falsche PHP.** `php -v` in Cron kann sich auf einem Rechner mit mehreren Versionen von dem der Shell
  unterscheiden. Verwenden Sie den vollständigen Pfad.
- **`-s` vergessen.** Die Aufgaben laufen dann im Standard-Siteaccess, der vielleicht nicht der öffentliche des Kunden
  ist.
- **Logdateien, die endlos wachsen.** Rotieren Sie die Dateien `cron-*.log` mit logrotate des Systems.

## Praktische Übung

Verwenden Sie Ihre eigene Schulungsinstallation und eine Shell als der Benutzer, dem ihre Dateien gehören.

1. Listen Sie im Wurzelverzeichnis der Installation jeden Teil und seine Skripte auf: `php runcronjobs.php --list`.
2. Suchen Sie die Skripte von `frequent` und des Standardteils und notieren Sie sie.
3. Ermitteln Sie den vollständigen Pfad Ihres PHP-Programms: `command -v php` und `php -v | head -1`.
4. Ermitteln Sie den Namen Ihres öffentlichen Siteaccess: Öffnen Sie in der Administration **Setup**,
   **Systeminformationen** und lesen Sie den Abschnitt **Siteaccesses** (oder lesen Sie die Website-Adressen, die
   `php bin/php/console exp:velocity status` auflistet).

5. Führen Sie den Teil `frequent` einmal von Hand und ohne Ausgabe aus und sehen Sie sich den Rückgabewert an:

   ```bash
   php runcronjobs.php -q -s <siteaccess> frequent; echo "exit $?"
   ```

6. Starten Sie denselben Befehl zweimal gleichzeitig (hängen Sie an den ersten `&` an) und lesen Sie die Meldung des
   zweiten Laufs.
7. Öffnen Sie Ihre Crontab mit `crontab -e` und fügen Sie mit Ihren Pfaden hinzu:

   ```text
   */5  * * * *  cd /path/to/installation && /path/to/php runcronjobs.php -q -s <siteaccess> frequent >> var/log/cron-frequent.log 2>&1
   */15 * * * *  cd /path/to/installation && /path/to/php runcronjobs.php -q -s <siteaccess> >> var/log/cron-default.log 2>&1
   ```

8. Warten Sie auf die nächste Fünf-Minuten-Marke und prüfen Sie `var/log/cron-frequent.log` und seinen Eigentümer mit
   `ls -l var/log/`.
9. Listen Sie neue Dateien unter `var/` mit `find var -newer var/log/cron-frequent.log -user root` auf und prüfen Sie,
   dass nichts root gehört.

## Erwartetes Ergebnis

- Schritt 1 listet die Teile mit ihren Skripten auf, einschließlich `frequent` und des Standardteils.
- Schritt 5 endet mit `exit 0`.
- Schritt 6: Ein Lauf meldet, dass der Teil oder das Skript von einem anderen Prozess gesperrt ist, oder endet nach dem
  ersten.
- Schritt 8: Die Logdatei existiert und gehört dem Benutzer der Website.
- Schritt 9 gibt nichts aus.
- Der Trainer prüft das `crontab -l` einer Person am Beamer.

## Quiz

1. Was ist der Unterschied zwischen einem Cronjob-Skript und einem Cronjob-Teil?
2. Benachrichtigungen werden nie versendet. Welcher Teil fehlt höchstwahrscheinlich in der Crontab?
   A. `infrequent` B. `cache_cleanup` C. `frequent` D. `logs`
3. Was entscheidet `-s <siteaccess>`?
4. Warum muss die Crontab dem Benutzer der Website gehören?
5. Richtig oder falsch: Der Wartungsmodus stoppt Cronjobs.

<details>
<summary>Antworten</summary>

1. Ein Skript ist eine PHP-Datei; ein Teil ist eine benannte Liste von Skripten in `settings/cronjob.ini`, die nach
   einem Zeitplan zusammen laufen.
2. **C.** Ohne `frequent` werden keine Benachrichtigungen versendet, und die Alarme des Audits lösen nie aus
   ([Cronjobs](../../../hosting/betrieb/cronjobs.md#was-eine-gehostete-website-braucht)).
3. Den Siteaccess, in dem die Aufgaben laufen: Indizierung, Sitemaps und Benachrichtigungen wirken auf seine Inhalte und
   Adressen.
4. Damit die Dateien, die die Aufgaben schreiben, ihm gehören; Dateien, die root gehören, machen die Website später
   kaputt.
5. **Falsch.** Cronjobs laufen in einer Shell und sind nicht betroffen; halten Sie stattdessen Teile unter **Setup**,
   **Cronjobs** an.

</details>

## Weiterführende Informationen

- [Cronjobs](../../../hosting/betrieb/cronjobs.md): Teile, Crontab, Benutzer, Grenzen.
- [Cronjobs: ausführen, planen und verfolgen](../../../../anleitungen/cronjobs.md): die vollständige Anleitung.
- [Cron und Shell-Zugang](../../../hosting/control-panels/cron-und-shell-zugang.md): Cron in Hosting-Panels.
- [Kapitel 10, Cronjobs](../../../../installation/10-nach-der-installation.md#103-cronjobs): die Fassung des
  Installationsbuchs.

## Verwandte Seiten

- [Modul 4: Bereitstellung und Cron](README.md)
- [Hosting und Betrieb: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../../../../eng-us/help/trainers/trainers-start-here.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
