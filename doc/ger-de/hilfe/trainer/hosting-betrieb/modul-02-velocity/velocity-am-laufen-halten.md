---
title: "Lektion 2.2: Velocity am Laufen halten"
audience: trainer, hosting, admin
category: training
order: 22
summary: Die Teilnehmenden stoppen Velocity, starten ihn neu und laden ihn sanft neu, schreiben eine Unit für den Systemstart, die exp:velocity ausführt, lesen das Verhalten des Watchdogs und finden die Logs des Servers und seinen Konfigurationsbaum. 45 Minuten.
keywords: hosting und betrieb, Velocity, restart, graceful, stop, kill, systemd, dienst für den systemstart, watchdog, console.log, zugriffslog, /etc/vc, layout, debian-stil, hosting and operations, boot service, access log, Debian style
type: help
---
# Lektion 2.2: Velocity am Laufen halten

Ein lang laufender Server muss nach einem Neustart des Rechners und nach einem Absturz wiederkommen, und ein Betreiber
muss ihn neu laden können, ohne Anfragen zu verlieren. Diese Lektion behandelt die Steuerbefehle, eine Unit für den
Systemstart, den optionalen Watchdog, die Logs und den Konfigurationsbaum, der wie `/etc/apache2` bei Debian aufgebaut
ist.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Hosting und Betrieb](../README.md) |
| Modul | [Modul 2: Velocity](README.md) |
| Dauer | 45 Minuten (20 Präsentation, 25 Übung) |
| Niveau | Fortgeschrittene |
| Zielgruppe | Systemadministratoren und Support-Mitarbeitende im Hosting |
| Voraussetzungen | [Lektion 2.1](was-velocity-ist.md); root auf der eigenen virtuellen Maschine für die Unit |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- für eine Situation zwischen `stop`, `restart`, `graceful` und `kill` wählen;
- eine systemd-Unit schreiben, die `exp:velocity start`, `stop` und `graceful` ausführt;
- erklären, wann der Watchdog den Server startet und wann nicht;
- das Zugriffslog, das Fehlerlog, `console.log` und die PID-Datei finden;
- den Konfigurationsbaum mit `exp:velocity layout` lesen.

## Hinweise für Trainer

### So präsentieren Sie es

Quelle: [Velocity für Hoster](../../../hosting/server/velocity-fuer-hoster.md#am-laufen-halten-dienst-beim-systemstart-und-watchdog)
und [Kapitel 8, Abschnitte 8.3.10 bis 8.3.12](../../../../installation/08-die-website-ausliefern.md#8310-der-konfigurationsbaum-etcvc-im-debian-stil).

1. **Die Verben** (4 Minuten). `stop` bittet den Server anzuhalten und wartet; `restart` stoppt und startet und ist nur
   erfolgreich, wenn ein neuer Elternprozess antwortet; `graceful` führt den Server neu aus und behält dabei den
   lauschenden Socket, sodass keine Anfrage verloren geht; `kill` stoppt, ohne zu fragen, für einen festgefahrenen
   Worker. Ein gestoppter Server bleibt gestoppt: Der Watchdog startet ihn erst nach `start` wieder.
2. **Dienst für den Systemstart** (5 Minuten). `exp:velocity` hat keinen Befehl, der einen installiert. Die eigenen Units
   der Engine starten die Engine direkt und umgehen, was `exp:velocity` hinzufügt (die erzeugte Website-Datei,
   `KeepGlobals`, PHP-Optionen, den Worker-Benutzer). Zeigen Sie die abgeleitete Unit aus Abschnitt 8.3.11:
   `Type=forking`, `ExecStart` mit `exp:velocity start`, `ExecReload` mit `graceful`, `PIDFile` auf
   `var/vc/qbix/run/server.pid`. Sie wird nicht mitgeliefert.
3. **Der Watchdog** (4 Minuten). Aus, bis er installiert ist (root, systemd). Er startet die Engine nur, wenn kein
   Prozess von ihr läuft, niemand sie absichtlich gestoppt hat, kein Neustart im Gange ist und er nicht gerade
   zurückweicht. Er stoppt oder startet nie einen laufenden Server neu. Log: `var/log/velocity-watchdog.log`.
4. **Logs** (3 Minuten). `var/vc/qbix/log/<siteaccess>-access.log` und `-error.log`; `var/vc/qbix/run/console.log`
   für die Startzeilen; die eigenen Logs des Kernels bleiben in `var/<site>/log/`.
5. **Der Baum** (4 Minuten). `/etc/vc`, wenn als root ausgeführt, sonst `var/vc/qbix/etc`. `vc.conf`, `ports.conf`,
   `envvars`, `conf-`, `mods-` und `sites-available` mit Symlinks in `-enabled`, aktiviert mit
   `exp:velocity site|conf|mod enable`.

### Häufige Fragen

**Welches Verb nach einer PHP-Änderung?** Keines davon allein: `exp:velocity deploy` (Modul 4) führt die Schritte in
der richtigen Reihenfolge aus.

**Funktioniert `graceful` bei jeder Engine?** Es ist ein Verb für jede Engine; bei FrankenPHP lädt es die Konfiguration
über die Admin-API von Caddy neu.

**Kann ich eine erzeugte Datei im Baum bearbeiten?** Erzeugte Dateien tragen `"_generated": "exp:velocity"`; entfernen
Sie die Markierung, und die Datei wird nie wieder überschrieben, wie eine conffile bei dpkg.

**Wer darf das Zugriffslog lesen?** Es nennt Besucher, deshalb wird `[LogSettings] FileMode` mit `0640` ausgeliefert.

### Stolperfallen

- **Zwei Units für dieselben Ports.** Kombinieren Sie die abgeleitete Unit nicht mit der Unit des
  Betriebssystempakets.
- **`Instances` über 1.** Die `PIDFile` nennt dann nur Instanz 0.
- **Teilnehmende ohne root.** Ohne root liegt der Baum in `var/vc/qbix/etc`, und die Unit lässt sich nicht
  installieren; lassen Sie sie die Unit-Datei schreiben und mit dem Trainer lesen.
- **Den Vorführserver des Trainers stoppen.** Jede Person steuert nur ihre eigene Installation.

## Praktische Übung

Verwenden Sie Ihre eigene Schulungsinstallation aus Lektion 2.1, ausgeliefert von Velocity.

1. Führen Sie `php bin/php/console exp:velocity graceful` aus, dann `status`. Achten Sie darauf, dass die Website
   weiter geantwortet hat.
2. Führen Sie `php bin/php/console exp:velocity restart` aus und prüfen Sie mit `status`, dass ein neuer Elternprozess
   antwortet.
3. Listen Sie das Log-Verzeichnis auf: `ls -l var/vc/qbix/log/`. Notieren Sie die Namen des Zugriffs- und des
   Fehlerlogs und ihren Modus.
4. Lesen Sie die letzten Zugriffszeilen: `tail -n 5 var/vc/qbix/log/*-access.log`.
5. Zeigen Sie den Konfigurationsbaum: `php bin/php/console exp:velocity layout`. Notieren Sie, welches Verzeichnis
   verwendet wird und welche Module aktiviert sind.
6. Geben Sie die Kommandozeile aus, die der Server ausführen würde: `php bin/php/console exp:velocity command`.
7. Schreiben Sie auf einer virtuellen Maschine, auf der Sie root sind, die abgeleitete Unit aus
   [Abschnitt 8.3.11](../../../../installation/08-die-website-ausliefern.md#8311-velocity-als-dienst-betreiben) nach
   `/etc/systemd/system/exponential-velocity-training.service`, mit Ihrem Installationspfad.
8. Stoppen Sie den von Hand gestarteten Server (`exp:velocity stop`), führen Sie dann `systemctl daemon-reload` und
   `systemctl enable --now exponential-velocity-training` aus.
9. Prüfen Sie mit `systemctl status exponential-velocity-training` und `exp:velocity status`.
10. Lesen Sie den Zustand des Watchdogs, ohne ihn zu installieren: `php bin/php/console exp:velocity watchdog status`.

## Erwartetes Ergebnis

- Schritte 1 und 2: Die Website antwortet nach jedem Befehl; `status` zeigt nach `restart` einen neuen Elternprozess.
- Schritt 3: Die Logs existieren mit dem Modus `-rw-r-----` (`0640`).
- Schritt 5 nennt `/etc/vc` (als root) oder `var/vc/qbix/etc`.
- Schritt 9: systemd zeigt die Unit als aktiv, und `exp:velocity status` zeigt den laufenden Server.
- Schritt 10 meldet, dass der Watchdog nicht installiert ist (er ist aus, bis er installiert wird).
- Der Trainer prüft `systemctl status` einer Person am Beamer.

## Quiz

1. Welches Verb lädt Velocity neu, ohne eine Anfrage zu verlieren?
   A. `restart` B. `kill` C. `graceful` D. `stop`
2. Warum führt die abgeleitete Unit `exp:velocity start` aus statt der Engine direkt?
3. Sie haben Velocity absichtlich mit `exp:velocity stop` gestoppt. Startet der Watchdog es wieder?
4. Wohin werden die Startzeilen wie `Workers as: ...` geschrieben?
5. Was bewirkt `exp:velocity conf enable <name>`?

<details>
<summary>Antworten</summary>

1. **C.** Es führt den Server neu aus und behält dabei den lauschenden Socket.
2. Ein direkter Start der Engine umgeht, was `exp:velocity` hinzufügt: die erzeugte Website-Datei, `KeepGlobals`, die
   PHP-Optionen, den Worker-Benutzer, das Engine-Archiv und die Instanzen (Abschnitt 8.3.11).
3. Nein. Er startet keinen absichtlich gestoppten Server, bis `start` ausgeführt wird.
4. In `var/vc/qbix/run/console.log`.
5. Es verlinkt `conf-available/<name>.conf` nach `conf-enabled/`, wie es `a2enconf` tut.

</details>

## Weiterführende Informationen

- [Velocity für Hoster](../../../hosting/server/velocity-fuer-hoster.md): Dienst für den Systemstart, Watchdog und Logs.
- [Velocity startet nicht](../../../hosting/runbooks/velocity-startet-nicht.md): was zu tun ist, wenn es nicht
  hochkommt.
- [Velocity-Bedienfeld und Dashboard](../../../../../eng-us/features/6.0/velocity-control-panel.md): die `/Q/`-Seiten.
- [Velocity-Scheduler](../../../../../eng-us/features/6.0/velocity-scheduler.md): Aufgaben, die der Server nach Zeitplan ausführt.

## Verwandte Seiten

- [Modul 2: Velocity](README.md)
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
