---
title: Den Zustand der Logs prüfen
audience: government, admin, hosting
category: public-sector
order: 140
summary: Schritt für Schritt - unter Setup > Logdateien > Zustand der Logs die Speicherbelegung gegenüber dem Budget, schnell wachsende Logs, Logs über ihrer Grenze, Logs, die der Webserver nicht schreiben kann, und die geltenden Rotationseinstellungen lesen und auf jeden Befund reagieren.
keywords: Zustand der Logs, Speicherbudget, Wachstum der Logs, beschreibbare Logs, gemischte Eigentümer, Rotationseinstellungen, logshealth, DiskBudget, Log-Größe, Berechtigungen, log health, disk budget, log growth, writable logs, mixed owners, rotation settings, log size, permissions
type: help
---
# Den Zustand der Logs prüfen

Diese Aufgabe ist für Administratoren bei einer wöchentlichen Prüfung. Am Ende wissen Sie, dass die Logs im Budget
liegen, rotieren und beschreibbar sind. Referenz:
[Log-Viewer, „Zustand der Logs“](../../../../../eng-us/features/6.0/log-viewer.md#log-health).

## Inhalt
1. [Bevor Sie beginnen](#bevor-sie-beginnen)
2. [Schritte](#schritte)
3. [Auf Befunde reagieren](#auf-befunde-reagieren)
4. [Verwandte Seiten](#verwandte-seiten)

## Bevor Sie beginnen

- `setup/logs`; `setup/managelogs`, um von der Seite aus zu handeln.

## Schritte

1. **Setup > Logdateien**, dann **Zustand der Logs** (`/setup/logshealth`).
2. **Speicherbelegung**: alle Logs und Archive gegenüber `logviewer.ini [HealthSettings] DiskBudget` (standardmäßig
   256 MB); gelb ab 80 %, rot darüber.
3. **Am schnellsten wachsend**: Zeilen und Bytes je Stunde.
4. **Über ihrer Größengrenze**: Logs, die hätten rotieren sollen.
5. **Vom Webserver nicht beschreibbar**: mit einer Warnung, wenn Logs gemischte Eigentümer haben.
6. **Alle Logs** und **Geltende Rotationseinstellungen**: die Höchstgröße jedes Logs, aufbewahrte Archive, Kompression
   und Alter, mit der Datei und der Einstellung, aus der jeder Wert stammt.

## Auf Befunde reagieren

| Befund | Tun |
|---|---|
| Über dem Budget | Archive bereinigen ([Logs rotieren und bereinigen](logs-rotieren-und-bereinigen.md)); die Aufbewahrung überprüfen |
| Ein Log wächst schnell | die Ursache in seinen neuesten Zeilen finden; eine eingeschaltet gebliebene Debug-Einstellung ist häufig |
| Nicht beschreibbar / gemischte Eigentümer | die Logs dem Benutzer des Webservers übereignen; Befehle als dieser Benutzer ausführen, nicht als root ([Datei- und Verzeichnisberechtigungen prüfen](datei-und-verzeichnisberechtigungen-pruefen.md)) |
| Rotation nicht wie beabsichtigt | `logviewer.ini [RotationSettings]` je Log setzen, über Ihren Änderungsprozess |

## Verwandte Seiten
- [Die Fehlerübersicht lesen](die-fehleruebersicht-lesen.md)
- [Aufbewahrung und Rotation der Logs](../../../../../eng-us/help/government/operations/monitoring/log-retention-and-rotation.md)
- [Datenträger voll](../../../../../eng-us/help/government/operations/incidents/disk-full.md)
- [Log-Viewer und Rotation (Hosting)](../../../hosting/betrieb/log-viewer-und-log-rotation.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
