---
title: Aufbewahrung von Protokollen
audience: government, admin, hosting
category: public-sector
order: 100
summary: Für Administratoren öffentlicher Websites - wie Exponential seine Protokolle rotiert und bereinigt, die mitgelieferten Altersgrenzen für 404-, Sicherheits- und CSP-Protokolle, warum andere Kernel-Protokolle standardmäßig keine haben, und wie Sie MaxAgeDays pro Protokoll setzen, die Bereinigung nach Zeitplan ausführen und sie prüfen.
keywords: Aufbewahrung von Protokollen, Protokollrotation, Bereinigung, MaxAgeDays, LogFileMaxAge, LogFileRotateCount, LogFileMaxSize, logviewer.ini RotationSettings, exp:logs, Cronjob logs, logrotate, Webserver-Protokolle, Speicherbegrenzung, öffentlicher Sektor, log retention, log rotation, pruning, storage limitation
type: help
---
# Aufbewahrung von Protokollen

Diese Seite richtet sich an Administratoren und Hosting-Teams. Protokolle enthalten personenbezogene Daten (maskierte
Adressen, User-Agents, URLs). Exponential rotiert jedes Protokoll nach Größe; nur die Anfrageprotokolle haben
standardmäßig Altersgrenzen. Das Projekt führt „Protokolle standardmäßig ohne Altersgrenze“ als bekannte Lücke
([Datenschutzstandards: Bekannte Lücken](../../../../eng-us/standards/privacy.md#known-gaps)).

> **Hinweis** Diese Seite beschreibt Funktionen und gängige Praxis; sie ist keine Rechtsberatung. Prüfen Sie das für
> Ihre Organisation geltende Recht mit Ihrer Rechtsberatung.

## Inhalt

1. [Wie die Rotation funktioniert](#wie-die-rotation-funktioniert)
2. [Die Voreinstellungen](#die-voreinstellungen)
3. [Altersgrenzen setzen](#altersgrenzen-setzen)
4. [Bereinigung nach Zeitplan](#bereinigung-nach-zeitplan)
5. [Protokolle außerhalb von Exponential](#protokolle-außerhalb-von-exponential)
6. [Verwandte Seiten](#verwandte-seiten)

## Wie die Rotation funktioniert

Wenn ein Protokoll über `site.ini [FileSettings] LogFileMaxSize` (2 MB) hinauswächst, rotiert es beim nächsten
Schreiben: Aus `.1` wird `.2` und so weiter, und Archive über `LogFileRotateCount` (3) hinaus werden entfernt. Eine
**Bereinigung** (Prune) entfernt Archive über die Anzahl hinaus und solche, die älter als die Altersgrenze sind; eine
Rotation entfernt nie nach Alter ([Protokollrotation](../../../../eng-us/features/6.0/log-rotation.md)).

## Die Voreinstellungen

| Protokoll | Archive | Altersgrenze |
|---|---|---|
| `404.log` | 5, komprimiert, je 10 MB | 30 Tage |
| `security.log` | 5, komprimiert, je 10 MB | 90 Tage |
| `csp.log` | 5, komprimiert, je 10 MB | 30 Tage |
| `error.log`, `warning.log`, `notice.log`, `debug.log`, `auth.log`, `storage.log`, Cronjob- und Erweiterungsprotokolle | 3, nach Größe | keine (`LogFileMaxAge=0`) |

## Altersgrenzen setzen

Pro Protokoll, über seinen Schlüssel (den Schlüssel der Permalinks des Protokollbetrachters, etwa `error`, `auth`,
`site-storage`):

```ini
# settings/override/logviewer.ini.append.php
[RotationSettings]
MaxAgeDays[error]=90
MaxAgeDays[auth]=90
MaxAgeDays[notice]=30
Compress[error]=enabled
```

oder eine Grenze für jedes Protokoll ohne eigenen Eintrag, in `settings/override/site.ini.append.php`:

```ini
[FileSettings]
LogFileMaxAge=90
```

Wählen Sie die Werte mit Ihrer bzw. Ihrem Datenschutzbeauftragten: lang genug, um Vorfälle zu untersuchen, kurz genug
für die Speicherbegrenzung.

## Bereinigung nach Zeitplan

Altersgrenzen wirken, wenn eine Bereinigung läuft:

```bash
./console exp:logs --archives --allow-root-user          # each log, its archives, sizes and limits
./console exp:logs --prune --dry-run --allow-root-user   # what would go
./console exp:logs --prune --allow-root-user             # remove it
```

Oder der Cronjob-Teil `logs` (in keiner Standardgruppe, fügen Sie ihn also hinzu), als Benutzer der Website:

```
15 3 * * * cd /path/to/exponential && php runcronjobs.php -q logs
```

Rotationen und Bereinigungen werden im Audit als `system.log.rotate` und `system.log.delete` aufgezeichnet. Wenn
stattdessen das `logrotate` des Systems `var/log` verwaltet, setzen Sie `LogFileRotateCount=0`, damit der Kernel nicht
zusätzlich rotiert.

## Protokolle außerhalb von Exponential

Die Protokolle des Webservers und von PHP-FPM, die Protokolle von Velocity, das Protokoll von request-shield und die
Protokolle des Mailservers werden von ihren eigenen Programmen rotiert; legen Sie ihre Aufbewahrung dort fest oder
fragen Sie Ihren Hoster.

## Verwandte Seiten

- [Protokollrotation und Bereinigung](../../../../eng-us/features/6.0/log-rotation.md)
- [Maskierung in Protokollen und im Protokollbetrachter](maskierung-in-protokollen.md)
- [Aufbewahrung von Protokollen und Daten für Hoster](../../hosting/sicherheit/aufbewahrung-von-logs-und-daten.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
