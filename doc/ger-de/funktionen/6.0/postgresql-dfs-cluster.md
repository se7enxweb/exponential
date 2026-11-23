# Clustering mit PostgreSQL: das DFS-Backend im Kernel

Diese Seite ist für Administratoren, die eine Exponential-Website auf mehreren Webservern betreiben oder das vorhaben,
und für Entwickler, die den Cluster-Code pflegen oder erweitern. Ab Exponential 6.0.15 clustert der Kernel mit
PostgreSQL ebenso wie mit MySQL und MariaDB: Das PostgreSQL-Metadaten-Backend, seine Tabellen und sein Datei-Gateway
gehören zum Kernel, und eine Standardinstallation braucht dafür keine Erweiterung.

## Inhalt

1. [Herkunft und Dank](#herkunft-und-dank)
2. [Was der Kernel mitbringt](#was-der-kernel-mitbringt)
3. [Was überarbeitet, behoben und erweitert wurde](#was-überarbeitet-behoben-und-erweitert-wurde)
4. [Erprobte Beispielkonfigurationen](#erprobte-beispielkonfigurationen)
5. [Umstieg von der Erweiterung ezpostgresqlcluster](#umstieg-von-der-erweiterung-ezpostgresqlcluster)
6. [Tests und die abgedeckten PostgreSQL-Versionen](#tests-und-die-abgedeckten-postgresql-versionen)
7. [Weitere Datenbanken: Oracle, MongoDB und SQLite](#weitere-datenbanken-oracle-mongodb-und-sqlite)
8. [Verwandte Seiten](#verwandte-seiten)

## Herkunft und Dank

Das PostgreSQL-Cluster-Backend des Kernels ist eine Überarbeitung und Integration früherer Arbeit, und das sagen wir
deutlich:

- **ezpostgresqlcluster** von **eZ Systems AS** (Copyright 1999-2013), veröffentlicht unter der **GNU General Public
  License Version 2**. Die Erweiterung brachte PostgreSQL in den DFS-Cluster von eZ Publish 4.7, dem Produkt, aus dem
  Exponential hervorging: die Klasse `eZDFSFileHandlerPostgresqlBackend`, ein Gateway für `index_cluster.php` und die
  PostgreSQL-Tabellen. Ihr Entwurf (eine Zeile je Cluster-Datei, die `.generating`-Zeilen, mit denen ein Prozess eine
  Cache-Datei erzeugt, während die anderen warten, die Trennung von Speicher- und Cache-Tabelle) ist der Entwurf, den
  der Kernel beibehält. Original: [github.com/ezsystems/ezpostgresqlcluster](https://github.com/ezsystems/ezpostgresqlcluster).
- **se7enxweb/ezpostgresqlcluster**, der Fork von 7x, der die Erweiterung auf Exponential 6 mit Composer installierbar
  hielt und Ausgangspunkt und Referenz dieser Arbeit war:
  [github.com/se7enxweb/ezpostgresqlcluster](https://github.com/se7enxweb/ezpostgresqlcluster).

Der Code im Kernel ist eine neue Implementierung, Methode für Methode am MySQLi-Backend des Kernels ausgerichtet, mit
der Erweiterung als Quelle und Vorbild für die PostgreSQL-Seite. Er steht unter derselben Lizenz, der GNU General
Public License Version 2 (oder einer späteren Version). Die Erweiterung selbst wurde nicht verändert; Installationen,
die sie noch nutzen, laufen weiter.

## Was der Kernel mitbringt

| Datei | Was sie ist |
|---|---|
| `kernel/private/classes/clusterfilehandlers/dfsbackends/postgresql.php` | `expDFSFileHandlerPostgresqlBackend`, das Metadaten-Backend |
| `kernel/private/classes/clusterfilehandlers/dfsbackends/ezdfsfilehandlerpostgresqlbackend.php` | `eZDFSFileHandlerPostgresqlBackend`, der Klassenname der Erweiterung, für bestehende Einstellungen |
| `kernel/private/classes/clusterfilehandlers/interfaces/expdfsfilehandlerdbbackendinterface.php` | `expDFSFileHandlerDBBackendInterface`, der Vertrag jedes Metadaten-Backends (MySQLi und PostgreSQL implementieren ihn) |
| `kernel/sql/postgresql/cluster_dfs_schema.sql` | die Tabellen `ezdfsfile` und `ezdfsfile_cache` |
| `kernel/sql/postgresql/cluster_dfs_schema_upgrade.sql` | aktualisiert Tabellen, die die Erweiterung angelegt hat |
| `kernel/clustering/dfspostgresql.php` | das Gateway, das `index_cluster.php` für `CLUSTER_STORAGE_BACKEND` `dfspostgresql` lädt (`dfspostgres` ebenso) |

Nötig ist PostgreSQL 13 oder neuer; alles, was das Backend nutzt (`ON CONFLICT`, `SKIP LOCKED`, `lock_timeout`,
`text_pattern_ops`), gibt es ab 13. PHP 7.4 ist nutzbar, PHP 8.0 bis 8.5 laufen sauber. Jeder Webserver braucht die
PHP-Erweiterung `pdo_pgsql`.

## Was überarbeitet, behoben und erweitert wurde

**Behoben**

- **Keine `pg_*`-Funktion auf einem PDO-Objekt.** Die Erweiterung rief `pg_escape_string()` und `pg_result_error()`
  mit PDO-Objekten auf, unter PHP 8 ein fataler Fehler. Das Kernel-Backend nutzt nur PDO, jeder Wert ist ein
  gebundener Parameter.
- **Deadlocks werden wiederholt.** Die Erweiterung prüfte die MySQL-Fehlernummern 1205 und 1213, die PostgreSQL nie
  meldet. Das Kernel-Backend liest den SQLSTATE und wiederholt Deadlocks (`40P01`), nicht rechtzeitig gewährte Sperren
  (`55P03`) und Serialisierungsfehler (`40001`), bis zu `DBExecuteRetries`-mal, mit einer kurzen, wachsenden Pause
  (höchstens 0,2 Sekunden).
- **Kein stilles Zurückrollen.** In PostgreSQL bricht eine fehlgeschlagene Anweisung ihre Transaktion ab, und das
  folgende `COMMIT` antwortet ohne Fehler mit `ROLLBACK`. Das Backend bemerkt das, rollt zurück und meldet den Fehler.
- **Keine offene Transaktion:** Ein Umbenennen, dessen Dateikopie scheitert, rollt jetzt zurück.
- **Zeiten nach 2038:** Größen und Zeiten sind `bigint` (die Erweiterung nutzte `integer`).
- **Cluster-Ereignisse funktionieren:** Das Backend implementiert `eZClusterEventNotifier`.
- **Das Gateway** nutzt standardmäßig Port 5432, eine vorbereitete Anweisung, keinen Pfad in ein
  Erweiterungsverzeichnis und überträgt Dateien in Stücken.

**Überarbeitet**

- Speichern ist eine Anweisung, `INSERT ... ON CONFLICT (name_hash) DO UPDATE`, statt `SELECT` und danach `INSERT`
  oder `UPDATE`.
- Die Cache-Erzeugung beginnt mit `INSERT ... ON CONFLICT DO NOTHING`: Ein Prozess bekommt die `.generating`-Zeile,
  die anderen erfahren, wie lange sie warten. Das Übernehmen einer abgelaufenen Erzeugung ist ein `UPDATE`, das nur ein
  Prozess gewinnen kann.
- **Begrenzte Sperren:** Jede Verbindung setzt `lock_timeout` (`file.ini [eZDFSClusteringSettings] DBLockTimeout`,
  Sekunden, Standard 10). Bereinigungen nehmen ihre Zeilen mit `FOR UPDATE SKIP LOCKED`.
- Die Spalten `name` haben einen Index mit `text_pattern_ops`, sodass Präfixsuchen (`name LIKE 'var/site/cache/%'`)
  unabhängig von der Sortierung der Datenbank einen Index nutzen.

**Erweitert**

- Ein Vertrag für jede Datenbank, `expDFSFileHandlerDBBackendInterface`, und eine Paritäts-Testreihe, die dieselben
  Prüfungen auf jedem Backend ausführt.
- Die Testreihe fand auch einen Fehler im MySQLi-Backend des Kernels, inzwischen behoben: `_deleteByWildcard()`
  verlor die Maskierung eines Punkts, sodass `a.txt` auch `aXtxt` traf.

## Erprobte Beispielkonfigurationen

Die Einstellungen gehören in die globale Überschreibung `settings/override/file.ini.append.php`, die Konstanten in
`config.cluster.php`; beide nicht in die Versionsverwaltung.

### MySQL oder MariaDB

```ini
[ClusteringSettings]
FileHandler=eZDFSFileHandler

[eZDFSClusteringSettings]
MountPointPath=/mnt/exp-dfs
DBBackend=eZDFSFileHandlerMySQLiBackend
DBHost=cluster-db.internal
DBPort=3306
DBName=exp_cluster
DBUser=exp_cluster
DBPassword=<das Passwort>
```

Tabellen: `kernel/sql/mysql/cluster_dfs_schema.sql`; in `config.cluster.php`
`define( 'CLUSTER_STORAGE_BACKEND', 'dfsmysqli' );` mit Host, Port 3306, Benutzer, Passwort, Datenbank und
`CLUSTER_MOUNT_POINT_PATH`.

### PostgreSQL

```ini
[ClusteringSettings]
FileHandler=eZDFSFileHandler

[eZDFSClusteringSettings]
MountPointPath=/mnt/exp-dfs
DBBackend=expDFSFileHandlerPostgresqlBackend
DBHost=cluster-db.internal
DBPort=5432
DBName=exp_cluster
DBUser=exp_cluster
DBPassword=<das Passwort>
DBLockTimeout=10
# optional: eine Verbindung je PHP-Worker zwischen Anfragen behalten (siehe unten)
# DBPersistentConnection=enabled
```

Tabellen: `psql -v ON_ERROR_STOP=1 -h cluster-db.internal -U exp_cluster -d exp_cluster -f kernel/sql/postgresql/cluster_dfs_schema.sql`.

```php
<?php
// config.cluster.php
define( 'CLUSTER_STORAGE_BACKEND', 'dfspostgresql' );
define( 'CLUSTER_STORAGE_HOST', 'cluster-db.internal' );
define( 'CLUSTER_STORAGE_PORT', 5432 );
define( 'CLUSTER_STORAGE_USER', 'exp_cluster' );
define( 'CLUSTER_STORAGE_PASS', '<das Passwort>' );
define( 'CLUSTER_STORAGE_DB', 'exp_cluster' );
define( 'CLUSTER_STORAGE_CHARSET', 'utf8' );
define( 'CLUSTER_MOUNT_POINT_PATH', '/mnt/exp-dfs' );
```

### Wege zum Server und persistente Verbindungen

Das Backend bindet seine Werte in PHP (emulierte Prepares von PDO; die Werte sind nur Ganzzahlen, Zeichenketten und
Nullwerte), eine Anweisung ist also ein Weg zur Datenbank statt drei (Prepare, Execute und das `DEALLOCATE`, wenn die
Anweisung freigegeben wird). In einem Cluster, dessen Datenbank auf einer anderen Maschine liegt, sank so die Zeit jeder
Prüfung einer Cache-Datei auf ein Drittel. Dafür ist nichts einzustellen.

`[eZDFSClusteringSettings] DBPersistentConnection=enabled` (Standard aus) behält eine Verbindung je PHP-Worker zwischen
Anfragen (persistente Verbindungen von PDO) und spart die Anmelde-Wege einer neuen Verbindung. Zählen Sie zuerst die
Worker jedes Knotens (Kinder von PHP-FPM, Velocity-Worker) gegen `max_connections` des Servers. PDO rollt eine
Transaktion zurück, die am Ende einer Anfrage offen blieb. Das Gateway `index_cluster.php` öffnet weiter eine
Verbindung je Anfrage.

Ein Knoten behält und liefert nie eine zu kurze oder gescheiterte Kopie einer Datei vom Share: Die Größe wird an der
temporären Kopie geprüft, bevor sie an ihren Platz umbenannt wird, und ein Abruf, der bei jedem Versuch scheitert,
entfernt die alte lokale Kopie (jedes Backend, seit 6.0.15).

### Ein Cluster mit einem Knoten

Ein einzelner Server kann im Cluster-Modus laufen (Ein-Knoten-Cluster); ein zweiter Webserver bindet später dieselbe
Freigabe ein und nutzt dieselben Einstellungen ([Ein DFS-Cluster mit einem Knoten in einem Befehl](cluster-befehle.md)).

## Umstieg von der Erweiterung ezpostgresqlcluster

1. `kernel/sql/postgresql/cluster_dfs_schema_upgrade.sql` auf der Cluster-Datenbank ausführen, während kein Webserver
   schreibt (die Zeilen bleiben).
2. `DBBackend=expDFSFileHandlerPostgresqlBackend` und `DBPort` setzen.
3. In `config.cluster.php` `CLUSTER_STORAGE_BACKEND` auf `dfspostgresql` setzen und `CLUSTER_STORAGE_GATEWAY_PATH`
   entfernen.
4. Die Erweiterung deaktivieren und entfernen, die Autoloads neu erzeugen und die Caches leeren.

Einstellungen, die noch `eZDFSFileHandlerPostgresqlBackend` nennen, funktionieren weiter: Ohne die Erweiterung ist das
die Klasse des Kernels.

## Tests und die abgedeckten PostgreSQL-Versionen

`expDFSFileHandlerPostgresqlBackendTest` läuft ohne Datenbank; `expDFSPostgresqlBackendIntegrationTest`
(`EXP_TEST_PGCLUSTER_DSN`) und `expDFSMySQLiBackendParityTest` (`EXP_TEST_MYCLUSTER_DSN`) führen dieselben
Paritätsprüfungen auf PostgreSQL und MySQL aus, darunter vier Prozesse, die gleichzeitig dieselbe Cache-Datei
anfordern (einer erzeugt, drei warten), und auf PostgreSQL eine von einer anderen Transaktion gehaltene Sperre.
Gemessen am 19. November 2026: PostgreSQL 13, 14, 15, 16, 17, 18 und 19 (Release Candidate 1, seit der Veröffentlichung auch 19.0) bestanden alle 27
Prüfungen, MySQL 8.0 die 21, die dort gelten. GitHub Actions führt sie bei jedem Push aus (Job "DFS cluster backends").

## Weitere Datenbanken: Oracle, MongoDB und SQLite

Ein neues Backend ist eine Klasse (die `expDFSFileHandlerDBBackendInterface` und `eZClusterEventNotifier`
implementiert, nach dem Vorbild des PostgreSQL-Backends), eine Schemadatei, ein Gateway und eine Testklasse mit dem
Trait `expDFSBackendParityChecks`. Reihenfolge laut Vorgabe: zuerst PostgreSQL, danach im Kernel Oracle (`MERGE`,
`SKIP LOCKED`, `WAIT n`; bis dahin die Erweiterung `ezoracle`), MongoDB (eindeutiger Index auf `name_hash`,
`findOneAndUpdate` mit `upsert`) und SQLite (nur ein Knoten und Tests). SQLite, MongoDB und Oracle sind gebaut
([Clustering mit SQLite](sqlite-dfs-cluster.md), [Clustering mit MongoDB](mongodb-dfs-cluster.md),
[Clustering mit Oracle](oracle-dfs-cluster.md)): Jede Datenbank, die Exponential unterstützt, hat ein Cluster-Backend im Kernel.

## Verwandte Seiten

- [Clustering in Exponential 6.0](clustering-ueberblick.md): der Überblick über alle Teile eines Clusters
- [PostgreSQL 18 und 19](postgresql-18-und-19.md): Versionen, Clients, Aktualisieren und ein Cluster auf 18 und 19
- [Ein DFS-Cluster mit einem Knoten in einem Befehl](cluster-befehle.md)
- [Lesereplikate](../../bc/6.0/postgresql-lesereplikat.md)
- [Clustering: DFS und die Datenbank](../../hilfe/datenbankadministratoren/clustering-dfs-und-die-datenbank.md)
- [Clustering und gemeinsamer Speicher](../../hilfe/hosting/betrieb/clustering-und-gemeinsamer-speicher.md)
- [Datenbankunterstützung](../../hilfe/datenbankunterstuetzung.md)
- [Changelog 6.0.15](../../../changelogs/6.0/6.0.15.md)
