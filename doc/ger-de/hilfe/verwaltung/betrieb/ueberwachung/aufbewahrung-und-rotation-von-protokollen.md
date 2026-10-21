---
title: Aufbewahrung und Rotation von Logs
audience: government, admin, hosting
category: public-sector
order: 130
summary: Wie lange Exponential seine Logs und Audit-Einträge standardmäßig aufbewahrt, wie Rotation, Komprimierung und Bereinigung funktionieren, wie Sie die Aufbewahrung je Log an die Schriftgut- und Datenschutzregeln einer öffentlichen Stelle anpassen und wie Sie die Aufzeichnung erhalten, wenn Logs gelöscht werden müssen.
keywords: Aufbewahrung von Logs, Log-Rotation, bereinigen, komprimieren, LogFileMaxSize, LogFileRotateCount, LogFileMaxAge, Aufbewahrung des Audits, LiveDays, ArchiveDays, Datenschutz, Aufbewahrung von Schriftgut, exp:logs, log retention, log rotation, prune, compress, audit retention, data protection, records retention
type: help
---
# Aufbewahrung und Rotation von Logs

> **Hinweis** Diese Seite beschreibt Funktionen und übliche Praxis; sie ist keine Rechtsberatung. Klären Sie das für
> Ihre Organisation geltende Recht mit Ihrer Rechtsberatung.

Diese Seite richtet sich an die Administration, die Zuständigen für Schriftgut und Datenschutzzuständige, die Logs lange
genug für Sicherheit und Audit aufbewahren müssen und nicht länger als erlaubt. Quellen:
[Rotation und Bereinigung von Logs](../../../../funktionen/6.0/log-rotation.md),
[Prüfprotokoll](../../../../funktionen/6.0/pruefprotokoll.md#was-aufgezeichnet-wird) und
[Aufbewahrung von Logs und Daten (Hosting)](../../../hosting/sicherheit/aufbewahrung-von-logs-und-daten.md).

## Inhalt
1. [Die Standardwerte](#die-standardwerte)
2. [Die Aufbewahrung je Log festlegen](#die-aufbewahrung-je-log-festlegen)
3. [Die eigene Aufbewahrung des Prüfprotokolls](#die-eigene-aufbewahrung-des-prüfprotokolls)
4. [Rotation auf Anforderung und per Cron](#rotation-auf-anforderung-und-per-cron)
5. [Ihre Fristen festlegen](#ihre-fristen-festlegen)
6. [Verwandte Seiten](#verwandte-seiten)

## Die Standardwerte

| Log | Standard | Quelle |
|---|---|---|
| `error.log`, `warning.log`, `debug.log`, `storage.log`, Logs von Cronjobs und Erweiterungen | Rotation bei 2 MB, 3 Archive, keine Altersgrenze | `site.ini [FileSettings] LogFileMaxSize`, `LogFileRotateCount`, `LogFileMaxAge=0` |
| `404.log` | 10 MB, 5 Archive, komprimiert, bereinigt nach 30 Tagen | `logviewer.ini [RotationSettings]` |
| `security.log` | 10 MB, 5 Archive, komprimiert, bereinigt nach 90 Tagen | dieselbe |
| `csp.log` | 10 MB, 5 Archive, komprimiert, bereinigt nach 30 Tagen | dieselbe |
| Audit-Kanäle `content`, `access`, `system`, `commerce` | 90 Tage laufend, 730 Tage archiviert | `settings/audit.ini` |
| Audit-Kanal `read` (nur wenn Lesezugriffe eingeschaltet sind) | 30 Tage laufend, 90 archiviert | dieselbe |

Mit den Standardwerten belegt ein Kernel-Log höchstens etwa 8 MB auf der Festplatte. Beachten Sie, dass eine rein
größenbasierte Rotation Logs so lange aufbewahrt, wie 3 Archive reichen; auf einer stark besuchten Website können das
Tage sein, auf einer ruhigen Monate.

## Die Aufbewahrung je Log festlegen

```ini
# settings/override/logviewer.ini.append.php
[RotationSettings]
MaxSize[error]=10485760
Keep[error]=10
Compress[error]=enabled
MaxAgeDays[error]=180
```

`Keep` legt die Anzahl der Archive fest, `MaxAgeDays` eine Altersgrenze, die beim Bereinigen angewendet wird. Logs ohne
Eintrag verwenden die Werte aus `site.ini [FileSettings]`. **Zustand der Logs** zeigt die für jedes Log geltenden
Einstellungen und woher sie jeweils stammen.

## Die eigene Aufbewahrung des Prüfprotokolls

Je Kanal in `settings/override/audit.ini.append.php`: `LiveDays`, `ArchiveDays`, `MaxFileSize`, `ArchiveFormat`.
Archive werden komprimiert, zurückgelesen und verglichen, bevor die laufende Datei entfernt wird, und in einem
signierten Manifest aufgeführt. Werkzeuge für den Datenschutz (Pseudonymisierung, Löschung) beschreibt
[der Audit-Leitfaden, Abschnitt 4.12](../../../../bc/6.0/audit.md#412-datenschutz-und-dsgvo-aufgaben).

## Rotation auf Anforderung und per Cron

```bash
./console exp:logs --archives                # each log, its archives, sizes and limits
./console exp:logs --rotate-all --dry-run    # what would be rotated
./console exp:logs --prune --dry-run         # archives beyond Keep and MaxAgeDays
./console exp:logs --prune                   # remove them
```

Der Cronjob-Teil `logs` (in keinem Standardteil enthalten) rotiert, bereinigt und komprimiert nach Zeitplan:
`php runcronjobs.php -q logs`. Rotationen und Bereinigungen werden als `system.log.rotate` und `system.log.delete`
aufgezeichnet. Wenn stattdessen das `logrotate` des Systems `var/log` verwaltet, setzen Sie `LogFileRotateCount=0`,
damit nicht beide rotieren.

## Ihre Fristen festlegen

| Gesichtspunkt | Spricht für |
|---|---|
| Untersuchung spät entdeckter Vorfälle | längere Aufbewahrung von `security.log` und des Audit-Kanals `access` |
| Datenschutz (Adressen, User-Agents, auch maskiert) | kürzere Aufbewahrung der Anfrage-Logs |
| Schriftgutregeln für Entscheidungen und Veröffentlichungen | Audit-Kanal `content` und Versionsverlauf, nicht die technischen Logs |
| Speicherbudget | **Zustand der Logs**, `DiskBudget` |

Schreiben Sie die Fristen in Ihren Aufbewahrungsplan und konfigurieren Sie sie in den Einstellungen unter
Änderungskontrolle.

## Verwandte Seiten
- [Logs rotieren und bereinigen](../../aufgaben/betrieb/logs-rotieren-und-bereinigen.md)
- [Prüfprotokollarchive und Datensicherungen](../datensicherung-und-wiederherstellung/audit-trail-archive-und-datensicherungen.md)
- [Datenschutzstandard](../../../../standards/datenschutz.md)
- [Fehlerübersicht und Zustand der Logs](fehleruebersicht-und-zustand-der-logs.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
