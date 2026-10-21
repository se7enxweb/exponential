---
title: Gemeinsamer Speicher und der DFS-Cluster
audience: government, hosting, admin
category: public-sector
order: 40
summary: Für Infrastrukturteams in öffentlichen Stellen, die mehrere Webserver die hochgeladenen Dateien einer Exponential-Website gemeinsam nutzen lassen. Wie der DFS-Cluster-Dateihandler funktioniert, was er braucht (einen NFS-Export und eine Metadatendatenbank), die Wartungsbefehle und was Sie in Ihren Kontinuitätsplan aufnehmen sollten.
keywords: DFS, Cluster, eZDFSFileHandler, NFS, gemeinsamer Speicher, ezdfsfile, Metadatendatenbank, index_cluster.php, clusterize.php, dfscleanup.php, clusterpurge.php, ezpostgresqlcluster, file.ini, Kontinuität, cluster, shared storage, metadata database, continuity
type: help
---
# Gemeinsamer Speicher und der DFS-Cluster

Diese Seite richtet sich an Infrastruktur- und Speicherteams in öffentlichen Stellen, die die hochgeladenen Dateien
einer Exponential-Website mehr als einem Webserver zur Verfügung stellen müssen. Sie beantwortet, wie der
DFS-Cluster-Dateihandler funktioniert, was er von Ihren Speicher- und Datenbankteams braucht, welche Befehle ihn
pflegen und welche Risiken in den Kontinuitätsplan gehören. Die vollständige Einrichtung steht auf der Hosting-Seite
[Clustering und gemeinsamer Speicher](../../../hosting/betrieb/clustering-und-gemeinsamer-speicher.md).

## Inhalt

1. [Wann Sie ihn brauchen](#wann-sie-ihn-brauchen)
2. [Wie der DFS-Cluster funktioniert](#wie-der-dfs-cluster-funktioniert)
3. [Was Ihre Teams bereitstellen](#was-ihre-teams-bereitstellen)
4. [Wartungsbefehle](#wartungsbefehle)
5. [Risiken für den Kontinuitätsplan](#risiken-für-den-kontinuitätsplan)
6. [Checkliste](#checkliste)

## Wann Sie ihn brauchen

`file.ini [ClusteringSettings] FileHandler` entscheidet, wie Dateien in `var/` gelesen und geschrieben werden. Der
ausgelieferte `eZFSFileHandler` verwendet einfache Dateien auf der lokalen Festplatte und ist für einen Webserver
richtig. `eZDFSFileHandler` ist für mehrere Webserver gedacht, die eine Website ausliefern. „Ein einzelner Server
braucht keinen Cluster-Dateihandler, ganz gleich, wie groß er ist“
([Clustering und gemeinsamer Speicher](../../../hosting/betrieb/clustering-und-gemeinsamer-speicher.md)).

## Wie der DFS-Cluster funktioniert

- Die Referenzkopie jeder Cluster-Datei liegt auf einem **NFS-Export**, der auf jedem Webserver unter demselben Pfad
  eingehängt ist.
- Ihre Metadaten (Name, Größe, Änderungszeit, Ablauf) werden in einer **Datenbanktabelle** gehalten, `ezdfsfile`.
- **Cache-Dateien** werden auf die lokale Festplatte des Servers kopiert, der sie verwendet.
- **Bilder und Binärdateien** werden von `index_cluster.php` aus dem Mount gestreamt.
- Die **var-Verzeichnisse werden nicht geteilt**; jeder Server behält sein eigenes, und der Handler hält sie synchron.

Die Metadatendatenbank kann der Datenbankserver der Website oder ein separater sein:

| Backend (`[eZDFSClusteringSettings] DBBackend`) | Stammt aus |
|---|---|
| `eZDFSFileHandlerMySQLiBackend` | dem Kernel (ausgelieferter Standard), MySQL und MariaDB |
| `eZDFSFileHandlerPostgresqlBackend` | dem Paket `ezpostgresqlcluster` |
| `eZDFSFileHandlerOracleBackend` | der Extension `ezoracle` |

Eine Website unter SQLite kann überhaupt nicht die Website-Datenbank einer Umgebung mit mehreren Servern sein.

## Was Ihre Teams bereitstellen

| Team | Stellt bereit |
|---|---|
| Speicher | einen NFS-Export, auf jedem Webserver unter demselben Pfad eingehängt, für den Benutzer der Website beschreibbar, mit eigener Redundanz und eigenen Datensicherungen |
| Datenbank | die Tabelle `ezdfsfile` in einer Metadatendatenbank, gesichert und überwacht wie die Datenbank der Website |
| Web oder Plattform | Cluster-Einstellungen in der globalen `settings/override/file.ini.append.php`, die `CLUSTER_*`-Konstanten für `index_cluster.php`, Rewrite-Regeln für Dateien |
| Sicherheit | `config.cluster.php` und die Cluster-Einstellungen enthalten Datenbank-Zugangsdaten; halten Sie sie aus der Versionsverwaltung und aus Downloads heraus |

Cluster-Einstellungen müssen wegen der Reihenfolge, in der Einstellungen gelesen werden, in der globalen
Override-Datei stehen, nicht in einer Extension oder einem Siteaccess.

## Wartungsbefehle

| Befehl | Was er tut |
|---|---|
| `php bin/php/clusterize.php` | verschiebt die Dateien aus `var/` in den Cluster (einmal, bei der Einrichtung); `-u` verschiebt sie zurück |
| `php bin/php/dfscleanup.php -S` und `-B` | prüft den Mount gegen die Tabelle und umgekehrt; `-D` löscht, was nicht existiert |
| `php bin/php/clusterpurge.php --dry-run` | zeigt die abgelaufenen Cluster-Dateien, die es entfernen würde; ohne `--dry-run` entfernt es sie |

Führen Sie `dfscleanup.php` zuerst mit `-S` und `-B` aus und lesen Sie das Ergebnis, bevor Sie `-D` hinzufügen.
`clusterpurge.php` ist auch der Cronjob-Teil `cluster_maintenance`; die ausgelieferten Einstellungen halten ihn von den
Schaltflächen unter **Setup > Cronjobs** fern, weil er Daten löscht. Führen Sie ihn planmäßig nur von einem Server
aus.

## Risiken für den Kontinuitätsplan

- **Der Mount.** Fällt der NFS-Mount weg, kann kein Server mehr Dateien lesen. Überwachen Sie den Mount und den
  NFS-Server als Teil der Website.
- **Die Metadatendatenbank** liegt auf dem Weg jedes Dateizugriffs.
- **Konsistenz nach einem Vorfall.** Führen Sie nach einem Vorfall und vor einer Migration `dfscleanup.php -S` und
  `-B` aus.
- **Datensicherungen.** Sichern Sie den NFS-Export und die Metadatendatenbank gemeinsam, im selben Zeitfenster wie die
  Datenbank der Website ([Was zu sichern ist](../datensicherung-und-wiederherstellung/was-gesichert-werden-muss.md)).
- **Aktualisierungen.** Spielen Sie die Datei `dbupdate-cluster-...` jedes Aktualisierungsschritts ein, der eine
  solche hat ([11 Aktualisieren](../../../../installation/11-aktualisieren.md)).

## Checkliste

- [ ] Nur ein Webserver: Der Cluster-Handler wird nicht gebraucht.
- [ ] Mehrere: Der NFS-Export ist überall unter demselben Pfad eingehängt und hat eine eigene Redundanz.
- [ ] `ezdfsfile` existiert in einer Metadatendatenbank, die gesichert und überwacht wird.
- [ ] Die Cluster-Einstellungen stehen im globalen Override; Zugangsdaten stehen nicht in der Versionsverwaltung.
- [ ] `dfscleanup.php -S` und `-B` melden nach `clusterize.php` keine Abweichungen.
- [ ] `clusterpurge.php` läuft nur von einem Server aus.

## Verwandte Seiten

- [Verfügbarkeit](README.md): Übersicht dieses Ordners
- [Mehrere Webserver hinter einem Load Balancer](mehrere-webserver-hinter-einem-load-balancer.md)
- [Aufbau des Speichers](../../../hosting/betrieb/aufbau-des-speichers.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
