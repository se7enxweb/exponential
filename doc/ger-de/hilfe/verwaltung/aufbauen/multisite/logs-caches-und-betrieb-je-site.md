---
title: Logs, Caches und Betrieb je Site
audience: government, admin
category: public-sector
order: 90
summary: Wie Sie viele Abteilungs-Sites auf einer Installation betreiben, ohne dass sie sich in die Quere kommen - Logs je Site, Caches auf eigenem Speicher, INI-Cache je Site, Cronjobs je Siteaccess, Wartungsmodus und Sicherungen.
keywords: Betrieb von Multisite, Logs je Site, Caches je Site, Cronjobs je Siteaccess, Wartungsmodus, Sicherungen, multisite operations, logs per site, UseGlobalLogDir, LogVarDir, CacheVarDir, INICacheDir, cronjobs per siteaccess, maintenance mode, backups, VarDir
type: help
---
# Logs, Caches und Betrieb je Site

Diese Seite richtet sich an Administratoren einer Multisite-Installation. Jede Site kann ihr eigenes VarDir haben; die
folgenden Einstellungen trennen Logs und Caches je Site
([Logs, INI-Cache und Ablaufzeitstempel je Site](../../../../funktionen/6.0/log-und-cache-pfade-fuer-mehrere-websites.md)).

## Inhalt

1. [Logs je Site](#logs-je-site)
2. [Caches je Site](#caches-je-site)
3. [Cronjobs](#cronjobs)
4. [Wartung und Sicherungen](#wartung-und-sicherungen)

## Logs je Site

| Einstellung (`site.ini [FileSettings]`) | Wirkung |
|---|---|
| `UseGlobalLogDir=disabled` | Debug-Logs und die Standard-Logs gehen in das Log-Verzeichnis der Site statt nach `var/log` |
| `LogDir` (absoluter Pfad) | Logs auf einem anderen Dateisystem |
| `LogVarDir=var_log` | die Logs jeder Site in `var_log/<site>/log` |

Logs je Site lassen den Betreuer einer Abteilung seine eigenen Logs lesen und ermöglichen eine Aufbewahrung je Site.

## Caches je Site

| Einstellung | Wirkung |
|---|---|
| `CacheVarDir=var_cache` | Caches jeder Site in `var_cache/<site>/cache`, zum Beispiel auf einem Dateisystem im Arbeitsspeicher |
| `INICacheDir=site` | die INI-Dateien einer Site werden in ihrem Cache-Verzeichnis zwischengespeichert; sie zu leeren, lässt andere Sites unberührt |

Jeder Wert wird beim Wechsel des Siteaccess erneut aus site.ini abgeleitet, unter Velocity und auf der Kommandozeile.

## Cronjobs

Cronjobs laufen je Siteaccess (`php runcronjobs.php -s <siteaccess> <part>`). Teile, die auf der ganzen Datenbank
arbeiten (Workflows, Benachrichtigungen, Audit), müssen einmal je Installation laufen, nicht einmal je Site; Teile, die
an die Einstellungen einer Site gebunden sind (Newsletter, Sitemaps), laufen für diese Site
([Cronjobs](../../../../anleitungen/cronjobs.md)).

## Wartung und Sicherungen

- Der Wartungsmodus kann die Installation für Updates schließen ([Wartungsmodus](../../../../anleitungen/wartungsmodus.md)).
- Eine Datenbank und ein Speicherverzeichnis bedeuten eine Sicherung, und eine Wiederherstellung stellt alle Sites
  wieder her; planen Sie Wiederherstellungen einzelner Sites stattdessen als Inhaltspakete oder Exporte
  ([Pakete](../../../../anleitungen/pakete.md)).

## Verwandte Seiten

- [Logs, INI-Cache und Ablaufzeitstempel je Site](../../../../funktionen/6.0/log-und-cache-pfade-fuer-mehrere-websites.md)
- [Eine Site betreiben](../../../../anleitungen/betrieb-einer-website.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
