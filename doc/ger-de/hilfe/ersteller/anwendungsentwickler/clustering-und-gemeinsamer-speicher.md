---
title: Clustering und gemeinsamer Speicher
audience: app-creator, developer, admin
category: app-creation
order: 620
summary: Exponential auf mehreren Webservern betreiben - die Cluster-Datei-Handler (eZFSFileHandler, eZDFSFileHandler) in file.ini, der DFS-Mount und seine Datenbank, Sitzungen, Cron- und Warteschlangen-Worker und wie Anwendungscode clustertauglich bleibt.
keywords: Cluster, Clustering, gemeinsamer Speicher, Lastverteiler, Sitzungen, clustertauglicher Code, Anwendungsentwickler, cluster, clustering, eZDFSFileHandler, eZFSFileHandler, file.ini, DFS, NFS, shared storage, load balancer, sessions, cluster-safe code, eZClusterFileHandler, app creator
type: help
---
# Clustering und gemeinsamer Speicher

Diese Seite ist für Anwendungsentwickler und Administratoren, deren Website auf mehr als einem Webserver läuft. Alle
Server müssen dieselben Dateien und dieselbe Datenbank sehen; die Cluster-Datei-Handler von Exponential machen den
Speicher gemeinsam nutzbar.

## Inhalt

1. [Die Datei-Handler](#die-datei-handler)
2. [DFS einrichten](#dfs-einrichten)
3. [Was außerdem geteilt werden muss](#was-außerdem-geteilt-werden-muss)
4. [Clustertauglicher Anwendungscode](#clustertauglicher-anwendungscode)

## Die Datei-Handler

`settings/file.ini [ClusteringSettings] FileHandler`:

| Handler | Verwendung |
|---|---|
| `eZFSFileHandler` (mitgeliefert) | ein Server, Dateien auf der lokalen Festplatte |
| `eZDFSFileHandler` | mehrere Server: Dateien auf einem gemeinsamen Mount (NFS) mit einer Datenbanktabelle, die den Zustand jeder Datei kennt |

## DFS einrichten

`file.ini [eZDFSClusteringSettings]` nennt den gemeinsamen Mount (`MountPointPath`), das Datenbank-Backend
(`DBBackend`, zum Beispiel `eZDFSFileHandlerMySQLiBackend`), das DFS-Backend sowie Host, Name und Benutzer der
Cluster-Datenbank (Wiederholungen: `DBConnectRetries`, `DBExecuteRetries`). Legen Sie das Passwort in
`settings/override/` ab. Verschieben Sie dann die vorhandenen Dateien mit den Werkzeugen des Kernels in den Cluster
(`bin/php/clusterize.php`) und bereinigen Sie per Cron mit `clusterpurge`
(die Befehle finden Sie unter `doc/eng-us/examples/commands/`).

## Was außerdem geteilt werden muss

| Geteilt wird | Wie |
|---|---|
| Die Datenbank | ein Server oder ein Datenbank-Cluster |
| Code und Einstellungen | dasselbe Release, auf jeden Server ausgeliefert |
| Sitzungen | der Datenbank-Session-Handler, damit jeder Server sie lesen kann ([Sitzungen](../../../anleitungen/sitzungen.md)) |
| Inhalts-Jobs | das Job-Verzeichnis wird geteilt ([Inhalts-Jobs](inhalts-jobs.md)) |
| Cron | auf einem Server oder mit Sperren |
| Worker der Warteschlange | beliebig viele Server; expqueue vergibt jeden Job an genau einen Worker |
| Antwort-Caches von Velocity | pro Server; nach einem Deployment auf jedem Server leeren |
| Der Proxy | die Adresse des Lastverteilers in `TrustedProxies[]` |

## Clustertauglicher Anwendungscode

- Schreiben und lesen Sie Dateien über `eZClusterFileHandler::instance( $path )` (`storeContents()`,
  `fetchContents()`, `processCache()`), niemals mit `file_put_contents()` in `var/`
  ([Caching in Anwendungen](caching-in-anwendungen.md)).
- Speichern Sie Uploads über die Datentypen, die den Cluster-Handler verwenden.
- Halten Sie keinen Zustand auf der Festplatte eines einzelnen Servers oder im Speicher eines einzelnen Prozesses.
- Machen Sie Hintergrundarbeit idempotent und gesperrt, damit nicht zwei Server sie doppelt erledigen.

## Verwandte Seiten

- [Anwendungen skalieren](anwendungen-skalieren.md)
- [Log- und Cache-Pfade bei Multisite](../../../../eng-us/features/6.0/multi-site-log-and-cache-paths.md)
- [Fehlende Dateien](../../../anleitungen/fehlende-dateien.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
