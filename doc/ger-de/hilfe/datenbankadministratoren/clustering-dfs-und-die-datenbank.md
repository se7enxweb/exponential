---
title: "Clustering: DFS und seine Metadatendatenbank"
audience: dba
category: database-administration
order: 41
summary: Die Datenbankseite eines Exponential-Clusters auf mehreren Webservern - der DFS-Datei-Handler und seine Metadatentabelle ezdfsfile, die Backends für MySQL und MariaDB (im Kernel), PostgreSQL (ein eigenes Paket) und Oracle (ezoracle), die Einstellungen in file.ini und ihre unsicheren Standards, die Cluster-Update-Dateien, Wartungsbefehle und was Sie überwachen.
keywords: Cluster, Clustering, DFS, eZDFSFileHandler, eZFSFileHandler, ezdfsfile, ezdfsfile_cache, DBBackend, eZDFSFileHandlerMySQLiBackend, eZDFSFileHandlerPostgresqlBackend, eZDFSFileHandlerOracleBackend, file.ini, eZDFSClusteringSettings, config.cluster.php, clusterize, clusterpurge, dfscleanup, cluster_maintenance, NFS, cluster, clustering
type: help
---
# Clustering: DFS und seine Metadatendatenbank

Diese Seite richtet sich an Datenbankadministratoren einer Exponential-Website, die von mehreren Webservern ausgeliefert wird.
Eine solche Website teilt ihre hochgeladenen Dateien und Caches über den DFS-Cluster-Datei-Handler, der die Dateien auf einem
gemeinsamen Mount und ihre Metadaten in einer Datenbanktabelle hält. Diese Seite behandelt diese Datenbank: wo sie liegt, welche
Backends es gibt, die Einstellungen, die Wartungsbefehle und worauf Sie achten. Der Gesamtentwurf (der NFS-Mount,
`index_cluster.php`) steht unter [Clustering und geteilter Speicher](../hosting/betrieb/clustering-und-gemeinsamer-speicher.md).

## Inhalt

1. [Wann ein Cluster nötig ist](#wann-ein-cluster-nötig-ist)
2. [Die Metadatentabellen](#die-metadatentabellen)
3. [Backends](#backends)
4. [Die Einstellungen](#die-einstellungen)
5. [Upgrades](#upgrades)
6. [Wartungsbefehle](#wartungsbefehle)
7. [Worauf Sie achten](#worauf-sie-achten)
8. [Checkliste](#checkliste)

## Wann ein Cluster nötig ist

`settings/file.ini [ClusteringSettings] FileHandler` entscheidet:

| Handler | Verwendung |
|---|---|
| `eZFSFileHandler` (mitgelieferter Standard) | ein Webserver, einfache Dateien |
| `eZDFSFileHandler` | mehrere Webserver, die eine Website ausliefern: Dateien auf einem NFS-Mount, Metadaten in einer Datenbank |

Ein einzelner Server braucht keinen Cluster, wie groß er auch ist. Die eigene Datenbank der Website und die Metadatendatenbank
des Clusters sind getrennte Entscheidungen. SQLite kann gar nicht die Website-Datenbank einer Website mit mehreren Servern sein,
weil WAL jeden Prozess auf einer Maschine braucht.

## Die Metadatentabellen

`kernel/sql/mysql/cluster_dfs_schema.sql` legt `ezdfsfile` (eine Zeile je Cluster-Datei: Name, Größe, Änderungszeit, Ablauf) und
`ezdfsfile_cache` an. `file.ini [eZDFSClusteringSettings] MetaDataTableNameCache` kann eine andere Tabelle für Caches nennen, für
Staging-Umgebungen, die Speichereinträge teilen, aber keine Caches. `cluster_db_schema.sql` (`ezdbfile`, `ezdbfile_data`) gehört
zum älteren Datenbank-Datei-Handler, der entfernt wurde; eine Website, die ihn noch verwendet, wird wie in
[Kapitel 14, der Cluster](../../installation/14-migration-von-4x.md#der-cluster) beschrieben umgezogen.

Die Metadaten können im Datenbankserver der Website oder auf einem eigenen liegen. Das Upstream-Dokument zu DFS merkt an, dass
ein eigener Server großen Websites hilft; das `README.cluster` der Oracle-Erweiterung empfiehlt ein eigenes Schema für
Cluster-Daten, damit sich die beiden Mengen von Transaktionen nicht vermischen.

## Backends

| `DBBackend` | Engine | Woher es kommt |
|---|---|---|
| `eZDFSFileHandlerMySQLiBackend` | MySQL, MariaDB | der Kernel (`kernel/private/classes/clusterfilehandlers/dfsbackends/mysqli.php`), der mitgelieferte Standard |
| `eZDFSFileHandlerPostgresqlBackend` | PostgreSQL | nicht im Kernel: Der Kommentar in `file.ini` nennt es, und es kommt aus dem eigenen Paket `ezpostgresqlcluster` |
| `eZDFSFileHandlerOracleBackend` | Oracle | die Erweiterung `ezoracle` (`clusterfilehandlers/dfsbackends/oracle.php`); nur `DBName` wird verwendet, nicht `DBHost`, `DBPort` oder `DBSocket` |

Es gibt kein Backend für SQLite oder MongoDB.

## Die Einstellungen

Im globalen Override `settings/override/file.ini.append.php` (nicht in einer Erweiterung oder einem Siteaccess, wegen der
Reihenfolge, in der Einstellungen gelesen werden):

```ini
[ClusteringSettings]
FileHandler=eZDFSFileHandler

[eZDFSClusteringSettings]
MountPointPath=/path/to/nfs
DBBackend=eZDFSFileHandlerMySQLiBackend
DBHost=<cluster database host>
DBPort=3306
DBName=<cluster database>
DBUser=<cluster user>
DBPassword=<the password>
```

Die mitgelieferten Werte sind `DBHost=dbhost`, `DBName=cluster`, `DBUser=root` und ein leeres Passwort: Platzhalter, nie für die
Produktion. Ebenfalls mitgeliefert: `DBConnectRetries=3`, `DBExecuteRetries=20`, `MaxCopyRetries=5`. `index_cluster.php`, das
Cluster-Dateien über HTTP ausliefert, liest seine eigene Verbindung aus den Konstanten `CLUSTER_*` in `config.cluster.php`
(`CLUSTER_STORAGE_BACKEND`, `CLUSTER_STORAGE_HOST`, `CLUSTER_STORAGE_USER`, `CLUSTER_STORAGE_PASS`, `CLUSTER_STORAGE_DB` und
andere). Halten Sie beide Dateien aus der Versionsverwaltung heraus: Sie enthalten Zugangsdaten.

## Upgrades

Wenden Sie bei einem Upgrade die Datei `dbupdate-cluster-...` jedes Schritts an, der eine hat (MySQL: 4.3, 4.7, 5.2, 5.4)
([Upgrades und die SQL-Update-Dateien](upgrades-und-sql-update-dateien.md#regeln)).

## Wartungsbefehle

| Befehl | Was er tut |
|---|---|
| `php bin/php/clusterize.php` (`exp:clusterize`) | verschiebt die Dateien von `var/` in den Cluster; `-u` verschiebt sie zurück |
| `php bin/php/dfscleanup.php` (`exp:dfscleanup`) | prüft den Mount gegen `ezdfsfile` (`-S`) und die Tabelle gegen den Mount (`-B`); `-D` löscht, was nicht existiert |
| `php bin/php/clusterpurge.php` (`exp:clusterpurge`) | entfernt abgelaufene Cluster-Dateien physisch (`--scopes`, `--expiry`, `--dry-run`) |

Führen Sie `dfscleanup.php` zuerst mit `-S` und `-B` aus und lesen Sie das Ergebnis, bevor Sie `-D` hinzufügen. `clusterpurge.php`
ist der Cronjob-Teil `cluster_maintenance`, den `cronjob.ini [AdminSettings] ForbiddenParts[]` vom Browser fernhält; führen Sie
ihn aus Cron aus. Mit dem Oracle-Backend ist dieser Teil nicht wirksam; die Erweiterung liefert stattdessen
`extension/ezoracle/bin/php/oracleclusterbinarypurge.php`.

## Worauf Sie achten

- die Größe von `ezdfsfile` und die Rate abgelaufener Zeilen;
- Verbindungsfehler zur Cluster-Datenbank in `var/log/error.log` (jeder Webserver verbindet sich mit ihr);
- Konsistenz zwischen Mount und Tabelle (`dfscleanup.php -S -B`, nur lesend);
- die Cluster-Datenbank in Ihren Sicherungen, zusammen mit dem Mount.

## Checkliste

- [ ] Die Cluster-Datenbank hat ihr eigenes Konto; `DBUser` ist nicht `root`.
- [ ] Einstellungen in `settings/override/file.ini.append.php` und `config.cluster.php`, beide außerhalb der Versionsverwaltung.
- [ ] Cluster-Update-Dateien bei jedem Upgrade-Schritt angewendet, der eine hat.
- [ ] `cluster_maintenance` (oder das Oracle-Purge-Skript) aus Cron eingeplant.
- [ ] Mount und Tabelle zusammen gesichert.

## Verwandte Seiten

- [Clustering und geteilter Speicher](../hosting/betrieb/clustering-und-gemeinsamer-speicher.md)
- [Mehrere Webknoten](../hosting/betrieb/mehrere-web-knoten.md)
- [Sessions in der Datenbank](sessions-in-der-datenbank.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
