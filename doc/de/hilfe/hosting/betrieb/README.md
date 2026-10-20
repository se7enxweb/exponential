---
title: Betrieb
audience: hosting, admin
category: hosting
order: 10
summary: Der tägliche Betrieb gehosteter Exponential-Websites in einem Überblick, mit einem Link zu jeder Seite über Speicher, Clustering, Backups, Überwachung, Logs, Cron, Mail, Updates, Ausrollen und Wartungsmodus.
keywords: Betrieb, Hosting, Backups, Wiederherstellung, Überwachung, Logs, Cron, Cronjobs, Mail, Updates, Upgrades, Ausrollen, Wartungsmodus, Speicher, Cluster, operations, hosting, restore, monitoring, deploy, maintenance mode, storage, cluster
type: help
---
# Betrieb

Dieser Bereich ist für die Systemadministratoren und Support-Mitarbeitenden eines Hosting-Anbieters, die
Exponential-Websites nach dem Tag ihres Starts am Laufen halten. Er beantwortet vier Fragen: Was hält eine
Exponential-Website auf der Festplatte, wie sichern Sie sie und bekommen sie zurück, wie sehen Sie, dass sie gesund ist,
und wie ändern Sie sie (Cron, Mail, Updates, Ausrollen, Wartungsfenster), ohne den Kunden zu überraschen.

Jede Seite in diesem Bereich nennt die Befehle, Einstellungen und Pfade, auf die sie sich stützt, und verlinkt die
Dokumentation im Repository, in der sie beschrieben sind, sodass Sie jede Aussage selbst prüfen können. Beginnen Sie mit
[Hosting-Anbieter: hier beginnen](../hosting-anbieter-hier-beginnen.md), wenn Exponential neu für Sie ist.

## Inhalt

1. [Die Seiten dieses Bereichs](#die-seiten-dieses-bereichs)
2. [Die Betriebswerkzeuge, die Exponential mitliefert](#die-betriebswerkzeuge-die-exponential-mitliefert)
3. [Wo Sie beginnen](#wo-sie-beginnen)
4. [Verwandte Seiten](#verwandte-seiten)

## Die Seiten dieses Bereichs

### Speicher und Skalierung

- [Aufbau des Speichers](aufbau-des-speichers.md): was unter `var/` liegt, was beschreibbar sein muss, was neu erzeugt werden kann.
- [Clustering und gemeinsamer Speicher](clustering-und-gemeinsamer-speicher.md): der DFS-Cluster-Datei-Handler, seine
  Datenbanktabelle, der NFS-Mount und die Befehle, die ihn pflegen.
- [Mehrere Web-Knoten](mehrere-web-knoten.md): was sich ändert, wenn mehr als eine Maschine eine Website ausliefert:
  Datenbank, Dateien, Sitzungen, Caches, vertrauenswürdige Proxys.

### Backups und Wiederherstellung

- [Was gesichert werden muss](backups-was-gesichert-werden-muss.md): die Datenbank und die Dateien, gemeinsam erstellt, und die
  Schlüssel, die nie verloren gehen dürfen.
- [Backup-Zeitpläne und Aufbewahrung](backup-zeitplaene-und-aufbewahrung.md): Optionen dafür, wie oft und wie lange, für ein
  Hosting-Angebot formuliert.
- [Datenbank-Backups je Engine](datenbank-backups-je-engine.md): die Befehle für Backup und Wiederherstellung für jede der
  sechs unterstützten Datenbanken, in der offiziellen Reihenfolge.
- [Wiederherstellung und Wiederherstellungstests](wiederherstellung-und-wiederherstellungstests.md): das Verfahren der Wiederherstellung und wie
  Sie beweisen, dass ein Backup funktioniert.

### Überwachung und Logs

- [Überblick über die Überwachung](ueberblick-ueber-die-ueberwachung.md): die Statusbefehle, Zustandsprüfungen und Logs, die Ihnen sagen,
  dass es einer Website gut geht.
- [Anfrage-Logs](anfrage-logs.md): `404.log` und `security.log`, was hineinkommt und wie sie aufbewahrt werden.
- [Sicherheitslog](sicherheitslog.md): der Request-Klassifizierer, `auth.log` und das Prüfprotokoll als Sicht eines Hosters
  auf Angriffe.
- [Log-Viewer und Log-Rotation](log-viewer-und-log-rotation.md): **Setup > Logdateien**, `exp:logs`, Rotation, Bereinigung und
  das Budget für die Festplatte.
- [Zustandsprüfungen und Verfügbarkeit](zustandspruefungen-und-verfuegbarkeit.md): was ein externer Monitor anfragen und was eine Prüfung
  im Server ausführen sollte.

### Geplante Arbeit und Mail

- [Cronjobs](cronjobs.md): die Cronjob-Teile, die Crontab-Zeilen und wer sie ausführt.
- [Die Cronjob-Konsole](die-cronjob-konsole.md): **Setup > Cronjobs** für Support-Mitarbeitende und Kunden, Teile pausieren.
- [Mailzustellung](mailzustellung.md): die Transporte sendmail, SMTP und Datei und was ein Hoster bereitstellen muss.
- [Einwilligung und Bounces bei Mails](einwilligung-und-bounces-bei-mails.md): die Mail-Sperre, die Unterdrückungsliste und der Leser
  für das Bounce-Postfach.

### Eine Website ändern

- [Automatische Updates mit ezupdate](automatische-updates-mit-ezupdate.md): der Paketmanager in der Administration, seine
  Schalter und seine Kommandozeile.
- [Kunden-Websites aktualisieren](kunden-websites-aktualisieren.md): eine Installation auf ein neues Exponential-Release bringen.
- [Änderungen ausrollen](aenderungen-ausrollen.md): `exp:velocity deploy`, die Reihenfolge der Caches und die Konfiguration über
  das Ausrollen.
- [Wartungsmodus](wartungsmodus.md): eine Website für ein Zeitfenster vom Netz nehmen und zurückbringen.

## Die Betriebswerkzeuge, die Exponential mitliefert

Die meiste Arbeit in diesem Bereich wird mit einer Handvoll Befehle und Seiten der Administration erledigt. Alle gehören zum
Kernel (die Befehle liegen in `kernel/private/classes/commands/`).

| Aufgabe | Kommandozeile | Administration |
|---|---|---|
| Eine Website vom Netz nehmen | `php bin/php/maintenance.php on\|off\|status` | **Setup > Wartung** |
| Logs lesen und rotieren | `./console exp:logs` | **Setup > Logdateien** |
| Das Prüfprotokoll prüfen | `./console exp:audit status`, `exp:audit verify` | Reiter **Audit** |
| Cronjobs ausführen | `php runcronjobs.php -s <site> <part>` | **Setup > Cronjobs** |
| Zustand und Versionen | `./console exp:systeminfo` | **Setup > Systeminformationen** |
| Caches | `./console exp:cache status`, `php bin/php/ezcache.php` | **Setup > Caches** |
| Eine Codeänderung ausliefern | `./console exp:velocity deploy` | |
| Leistung messen | `./console exp:benchmark` | |
| E-Mail-Einstellungen | `./console exp:mail:status` | **Setup > E-Mail-Einstellungen** |

`./console` ist die Abkürzung für `bin/php/console` im Wurzelverzeichnis der Installation. Führen Sie jeden Befehl als
eigenen Systembenutzer der Website aus; als `root` verweigern die Skripte den Start, sofern nicht `--allow-root-user`
hinzugefügt wird, und die Dateien, die sie anlegen, gehören dann `root`
([Kapitel 11](../../../installation/11-aktualisieren.md#111-bevor-sie-beginnen)).

> **Tipp**
> `./console list` gibt jeden Befehl der Installation mit einer einzeiligen Beschreibung aus. Auf einem Host mit vielen
> Kunden führen Sie es einmal pro unterstütztem Exponential-Release aus und bewahren die Ausgabe bei Ihren Runbooks auf.

## Wo Sie beginnen

| Sie sind dabei | Lesen Sie zuerst |
|---|---|
| den Backup-Dienst aufzubauen | [Was gesichert werden muss](backups-was-gesichert-werden-muss.md), dann [Datenbank-Backups je Engine](datenbank-backups-je-engine.md) |
| die Überwachung einzurichten | [Überblick über die Überwachung](ueberblick-ueber-die-ueberwachung.md), dann [Zustandsprüfungen und Verfügbarkeit](zustandspruefungen-und-verfuegbarkeit.md) |
| Support-Tickets zu beantworten | [Log-Viewer und Log-Rotation](log-viewer-und-log-rotation.md), [Cronjobs](cronjobs.md), [Mailzustellung](mailzustellung.md) |
| Upgrades für viele Kunden zu planen | [Kunden-Websites aktualisieren](kunden-websites-aktualisieren.md), [Wartungsmodus](wartungsmodus.md) |
| ein Angebot mit mehreren Servern zu gestalten | [Mehrere Web-Knoten](mehrere-web-knoten.md), [Clustering und gemeinsamer Speicher](clustering-und-gemeinsamer-speicher.md) |

Das Installationsbuch behandelt dasselbe Gebiet für eine einzelne Website ausführlicher: Kapitel 10,
[Nach der Installation](../../../installation/10-nach-der-installation.md), und Kapitel 12,
[Fehlersuche](../../../installation/12-fehlerbehebung.md). Die Anleitung
[Eine Website betreiben](../../../anleitungen/betrieb-einer-website.md) ist die Kurzfassung für einen Website-Administrator.

## Verwandte Seiten

- [Hosting-Anbieter: hier beginnen](../hosting-anbieter-hier-beginnen.md)
- [Grundlagen des Hostings](../grundlagen/README.md), [Sicherheit](../sicherheit/README.md), [Leistung](../leistung/README.md)
- [Checklisten](../checklisten/README.md) und [Runbooks](../runbooks/README.md)
- [Eine Website betreiben](../../../anleitungen/betrieb-einer-website.md) (Anleitung)
- [Wenn etwas schiefgeht](../../wenn-etwas-schiefgeht.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
