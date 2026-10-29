---
title: Clustering und gemeinsamer Speicher
audience: hosting, admin
category: hosting
order: 30
summary: Wie Exponential hochgeladene Dateien und Caches mit dem DFS-Cluster-Datei-Handler zwischen mehreren Webservern teilt - der NFS-Mount, die Metadaten-Datenbank, index_cluster.php, die Wartungsbefehle und worauf Sie achten müssen.
keywords: Cluster, Clustering, DFS, eZDFSFileHandler, NFS, gemeinsamer Speicher, ezdfsfile, index_cluster.php, clusterize, clusterpurge, dfscleanup, file.ini, config.cluster.php, cluster, clustering, shared storage
type: help
---
# Clustering und gemeinsamer Speicher

Diese Seite ist für Systemadministratoren, die ein Exponential-Angebot mit mehr als einem Webserver gestalten oder betreiben,
und für Produktmanager, die wissen müssen, was ein solches Angebot verlangt. Sie beantwortet: wie mehrere Server hochgeladene
Dateien und Caches teilen, was der DFS-Cluster braucht (einen gemeinsamen Mount und eine Metadaten-Datenbank), wie Dateien
ausgeliefert werden, welche Befehle den Cluster pflegen und wo die Grenzen liegen.

Die Fakten stammen aus `settings/file.ini`, `config.php-RECOMMENDED`, den Cluster-Dokumenten in `doc/eng-us/features/` und den
Migrationskapiteln des Installationsbuchs. Die andere Hälfte einer Einrichtung mit mehreren Servern (Sitzungen, Caches vor PHP,
Proxys) steht auf [Mehrere Web-Knoten](mehrere-web-knoten.md).

## Inhalt

1. [Zwei Datei-Handler](#zwei-datei-handler)
2. [Wie der DFS-Cluster funktioniert](#wie-der-dfs-cluster-funktioniert)
3. [Ihn einrichten](#ihn-einrichten)
4. [Dateien ausliefern: index_cluster.php](#dateien-ausliefern-index_clusterphp)
5. [Wartungsbefehle](#wartungsbefehle)
6. [Worauf Sie achten](#worauf-sie-achten)
7. [Checkliste](#checkliste)

## Zwei Datei-Handler

`file.ini [ClusteringSettings] FileHandler` entscheidet, wie Exponential die Dateien von `var/` liest und schreibt:

| Handler | Was er tut | Nutzen Sie ihn für |
|---|---|---|
| `eZFSFileHandler` (mitgelieferter Standard) | einfache Dateien auf der lokalen Festplatte | einen Webserver |
| `eZDFSFileHandler` | Dateien auf einem gemeinsamen NFS-Mount, ihre Metadaten in einer Datenbanktabelle, Caches auf jeden Server kopiert | mehrere Webserver, die eine Website ausliefern |

Die älteren Handler `eZDBFileHandler` und `eZFS2FileHandler` wurden entfernt; eine Website, die sie noch nutzt, wird so
umgezogen, wie in [Kapitel 14, der Cluster](../../../installation/14-migration-von-4x.md#der-cluster) beschrieben.

> **Hinweis**
> Ein einzelner Server braucht keinen Cluster-Datei-Handler, wie groß er auch ist. Clustering ist für eine Website, die
> mehrere Maschinen gleichzeitig ausliefern. Es fügt einen Mount und eine Datenbank hinzu, die betrieben, gesichert und
> überwacht werden müssen.

## Wie der DFS-Cluster funktioniert

Aus [Anleitung zur Konfiguration des eZDFS-Clusters](../../../../eng-us/features/4.2/eZDFS-INSTALL.txt):

- Die Referenzkopie jeder geclusterten Datei liegt auf einem **NFS-Export**, auf jedem Webserver unter demselben Pfad
  eingehängt.
- Ihre Metadaten (Name, Größe, Änderungszeit, Ablauf) werden in einer **Datenbanktabelle**, `ezdfsfile`, gehalten.
- **Cache-Dateien** werden auf die lokale Festplatte des Servers kopiert, der sie nutzt.
- **Bilder und Binärdateien**, die ein Browser anfordert, werden vom Mount gestreamt.
- Die **var-Verzeichnisse dürfen nicht** zwischen den Servern **geteilt werden**: Jeder behält sein eigenes, und der
  Cluster-Handler hält sie mit der zentralen Kopie im Gleichschritt.

Die Metadaten-Datenbank kann der eigene Datenbankserver der Website oder ein getrennter sein; das Dokument merkt an, dass ein
dedizierter MySQL-Server bei großen Websites die Leistung deutlich verbessern kann.

### Datenbank-Backends

| Backend (`[eZDFSClusteringSettings] DBBackend`) | Woher es kommt |
|---|---|
| `eZDFSFileHandlerMySQLiBackend` | der Kernel (der mitgelieferte Standard), für MySQL und MariaDB |
| `eZDFSFileHandlerPostgresqlBackend` | das Paket `ezpostgresqlcluster` |
| `eZDFSFileHandlerOracleBackend` | die Erweiterung `ezoracle` |

Quelle: [15.17 Cluster und DFS](../../../installation/15-migration-von-5x-legacy.md#1517-cluster-und-dfs) und die in
`config.php-RECOMMENDED` aufgeführten Backends (`dfsmysqli`, `dfspostgres`, `dfsoracle`). Auch die Speicherseite lässt sich
ersetzen: `[eZDFSClusteringSettings] DFSBackend` nennt eine Klasse, die `eZDFSFileHandlerDFSBackendInterface` implementiert
([Konfigurierbares Backend für den DFS-Cluster](../../../funktionen/5.4/konfigurierbares-dfs-cluster-backend.md)).

> **Hinweis**
> Die eigene Datenbank der Website und die Metadaten-Datenbank des Clusters sind getrennte Entscheidungen. Eine Website auf
> PostgreSQL kann ihre Inhalte in PostgreSQL behalten; die Cluster-Tabelle braucht eines der Backends oben. SQLite kann
> überhaupt nicht die Datenbank einer Website mit mehreren Servern sein: Sein WAL-Modus braucht jeden Prozess auf einer
> Maschine ([Kapitel 9](../../../installation/09-datenbanken.md#wal-leser-und-schreiber-nebeneinander)).

## Ihn einrichten

1. **Legen Sie die Tabelle an.** Ihre Definition steht in der Klasse des MySQLi-Backends und ist im eZDFS-Dokument abgedruckt
   (`CREATE TABLE ezdfsfile ...`, InnoDB).
2. **Hängen Sie den Export** auf jedem Webserver unter demselben Pfad ein, für den Benutzer der Website beschreibbar.
3. **Konfigurieren Sie den Handler** in der globalen Überschreibung `settings/override/file.ini.append.php`.
   Cluster-Einstellungen müssen in dieser globalen Datei stehen, nicht in einer Erweiterung oder einem Siteaccess, wegen der
   Reihenfolge, in der Einstellungen gelesen werden:

   ```ini
   # settings/override/file.ini.append.php
   [ClusteringSettings]
   FileHandler=eZDFSFileHandler

   [eZDFSClusteringSettings]
   MountPointPath=/path/to/nfs
   DBBackend=eZDFSFileHandlerMySQLiBackend
   DBHost=<cluster database host>
   DBPort=3306
   DBName=<cluster database>
   DBUser=<user>
   DBPassword=<password>
   ```

   Die mitgelieferte Datei hat außerdem `DBConnectRetries=3`, `DBExecuteRetries=20` und `MaxCopyRetries=5`.
4. **Verschieben Sie die vorhandenen Dateien** einmal **in den Cluster**: `php bin/php/clusterize.php` (siehe unten).
5. **Richten Sie die Rewrite-Regeln für Dateien auf `index_cluster.php`** und geben Sie ihr ihre Konstanten (nächster Abschnitt).

Bei einem Upgrade spielen Sie die Datei `dbupdate-cluster-...` jedes Schritts ein, der eine hat
([11.3 Die Update-Dateien](../../../installation/11-aktualisieren.md#113-die-update-dateien)).

## Dateien ausliefern: index_cluster.php

Seit 4.7 liefert eine Datei im Document Root, `index_cluster.php`, Cluster-Dateien über HTTP aus
([Überarbeiteter Cluster-Index](../../../funktionen/4.7/cluster-index.md)). Sie liest `config.php` und, falls vorhanden,
`config.cluster.php` und erwartet die Konstanten `CLUSTER_*`, die `config.php-RECOMMENDED` dokumentiert:
`CLUSTER_STORAGE_BACKEND` (zum Beispiel `dfsmysqli`), `CLUSTER_STORAGE_HOST`, `CLUSTER_STORAGE_PORT`,
`CLUSTER_STORAGE_USER`, `CLUSTER_STORAGE_PASS`, `CLUSTER_STORAGE_DB` und `CLUSTER_MOUNT_POINT_PATH`, dazu optionale
Schalter wie `CLUSTER_ENABLE_HTTP_CACHE` und `CLUSTER_EXPIRY_TIMEOUT`.

Der Webserver schickt ihr nur die Pfade, die seine Rewrite-Regeln nennen. Mit Caches pro Website in `CacheVarDir` fügen Sie
auch deren öffentliche Caches hinzu ([Cluster (DFS)](../../../funktionen/6.0/log-und-cache-pfade-fuer-mehrere-websites.md#cluster-dfs)):

```apache
RewriteRule ^/var_cache/([^/]+/)?cache/(texttoimage|public)/.* /index_cluster.php [L]
```

> **Warnung**
> `config.cluster.php` und `settings/override/file.ini.append.php` enthalten Zugangsdaten zur Datenbank. Halten Sie sie aus
> der Versionsverwaltung heraus und aus jeder Datei, die ein Kunde herunterladen kann.

## Wartungsbefehle

| Befehl | Was er tut | Optionen (aus der eigenen Hilfe des Befehls) |
|---|---|---|
| `php bin/php/clusterize.php` | verschiebt die Dateien von `var/` in den Cluster; `-u` verschiebt sie zurück | `-u`, `--skip-binary-files`, `--skip-media-files`, `--skip-images`, `-r` (nach dem Kopieren entfernen), `-n` (nicht warten) |
| `php bin/php/dfscleanup.php` | prüft den Mount gegen die Tabelle `ezdfsfile` und umgekehrt | `-S` (Freigabe gegen Datenbank), `-B` (Datenbank gegen Freigabe), `-D` (löschen, was nicht existiert), `--path`, `--iteration-limit` |
| `php bin/php/clusterpurge.php --apply` | entfernt abgelaufene Cluster-Dateien physisch | `--scopes=...`, `--expiry=<days>` (Standard 30), `--dry-run`, `--iteration-sleep`, `--iteration-limit`, `--memory-monitoring` |

Führen Sie `dfscleanup.php` zuerst mit `-S` und `-B` aus und lesen Sie das Ergebnis, bevor Sie `-D` hinzufügen
([15.17](../../../installation/15-migration-von-5x-legacy.md#1517-cluster-und-dfs)). `clusterpurge.php` ist auch der Cronjob-Teil
`cluster_maintenance`; die mitgelieferten `cronjob.ini [AdminSettings] ForbiddenParts[]` halten ihn von den Schaltflächen auf
**Setup > Cronjobs** fern, weil er zerstörerisch ist und in die Hände eines Administrators an einer Shell gehört
([Cronjobs](cronjobs.md)).

## Worauf Sie achten

- **Der Mount.** Wenn der NFS-Mount wegfällt, kann kein Server mehr Dateien lesen. Überwachen Sie den Mount und den
  NFS-Server als Teil der Website.
- **Die Metadaten-Datenbank.** Sie liegt auf dem Weg jedes Dateizugriffs; sie braucht dasselbe Backup und dieselbe
  Überwachung wie die Datenbank der Website.
- **Konsistenz.** Führen Sie `dfscleanup.php -S` und `-B` nach einem Vorfall und vor einer Migration aus.
- **Cron pausieren.** Die Pausendateien der Cronjobs sind auf jedem Server lokal, es sei denn, `cronjob.ini [PauseSettings] Directory`
  zeigt auf ein gemeinsames Verzeichnis ([Cronjob-Teile pausieren](../../../funktionen/6.0/cronjobs-pausieren.md)).
- **Cluster-Ereignisse.** `file.ini [ClusterEventsSettings] ClusterEvents=disabled` ist ausgeschaltet ausgeliefert; mit einer
  Listener-Klasse lassen sie eine Erweiterung auf Cluster-Operationen reagieren, nur für den DFS-Cluster mit dem
  MySQLi-Backend ([Cluster-Ereignisse](../../../funktionen/4.7/cluster-ereignisse.md)).

## Checkliste

- [ ] Der NFS-Export ist auf jedem Webserver unter demselben Pfad eingehängt, für den Benutzer der Website beschreibbar.
- [ ] `ezdfsfile` existiert in der Metadaten-Datenbank, die gesichert und überwacht wird.
- [ ] Die Cluster-Einstellungen stehen in der globalen `settings/override/file.ini.append.php`.
- [ ] Jeder Server hat sein eigenes Verzeichnis `var/`; außer dem Mount wird nichts geteilt.
- [ ] Die Rewrite-Regeln schicken Bilder und Binärdateien an `index_cluster.php`, die ihre Konstanten `CLUSTER_*` hat.
- [ ] `dfscleanup.php -S` und `-B` melden nach `clusterize.php` keine Unterschiede.
- [ ] `clusterpurge.php` läuft nach Zeitplan von nur einem Server.

## Verwandte Seiten

- [Mehrere Web-Knoten](mehrere-web-knoten.md)
- [Aufbau des Speichers](aufbau-des-speichers.md)
- [Was gesichert werden muss](backups-was-gesichert-werden-muss.md)
- [Datenbanken in der offiziellen Reihenfolge](../grundlagen/datenbanken-in-der-offiziellen-reihenfolge.md)
- [Ein Hosting-Modell wählen](../modelle/ein-hosting-modell-waehlen.md)
- [Datenbankunterstützung](../../datenbankunterstuetzung.md), [Betriebssystemunterstützung](../../betriebssystemunterstuetzung.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
