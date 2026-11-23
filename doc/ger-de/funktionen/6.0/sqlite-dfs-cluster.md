# Clustering mit SQLite: das Ein-Knoten-DFS-Backend im Kernel

Diese Seite ist für Administratoren, die die Dateien einer SQLite-Website in einem DFS-Cluster auf einem Server
halten wollen, und für Entwickler, die die Cluster-Tests ohne Datenbankserver ausführen. Ab Exponential 6.0.15 hält
der DFS-Cluster des Kernels seine Datei-Metadaten auch in SQLite, neben MySQL/MariaDB und PostgreSQL: Das Backend,
seine Tabellen und sein Datei-Gateway gehören zum Kernel.

**SQLite clustert eine Maschine, nie mehrere.** Eine SQLite-Datenbank ist eine Datei, und ihre Sperren sind die des
Dateisystems. Auf einer Netzwerkfreigabe (NFS, SMB) sind diese Sperren nicht verlässlich, und die Dokumentation von
SQLite selbst rät davon ab, eine Datenbank von mehreren Maschinen aus zu schreiben. Das SQLite-Backend ist deshalb für:

- einen **Ein-Knoten-Cluster**: die PHP-Server eines Hosts (PHP-FPM und Velocity oder zwei FPM-Pools) auf einer
  Freigabe und einer Datenbankdatei, sodass die Website schon im Cluster-Modus läuft und ein späterer Wechsel zu
  MySQL/MariaDB oder PostgreSQL eine Änderung der Einstellungen ist;
- **Tests**: Die Paritätsprüfungen aller Cluster-Backends laufen mit SQLite, ohne dass etwas zu installieren ist.

Eine Website auf mehreren Webservern hält ihre Cluster-Metadaten in MySQL/MariaDB oder
[PostgreSQL](postgresql-dfs-cluster.md).

## Inhalt

1. [Herkunft und Dank](#herkunft-und-dank)
2. [Was der Kernel mitbringt](#was-der-kernel-mitbringt)
3. [Wie es sperrt: ein Schreiber zur Zeit](#wie-es-sperrt-ein-schreiber-zur-zeit)
4. [Erprobte Beispielkonfiguration](#erprobte-beispielkonfiguration)
5. [Ein Ein-Knoten-Cluster mit exp:cluster:enable](#ein-ein-knoten-cluster-mit-expclusterenable)
6. [Tests](#tests)
7. [Grenzen](#grenzen)
8. [Verwandte Seiten](#verwandte-seiten)

## Herkunft und Dank

Das SQLite-Backend folgt dem Aufbau des [PostgreSQL-Backends](postgresql-dfs-cluster.md) des Kernels, das eine
Überarbeitung und Integration von **ezpostgresqlcluster** von **eZ Systems AS** ist (GNU General Public License
Version 2, [github.com/ezsystems/ezpostgresqlcluster](https://github.com/ezsystems/ezpostgresqlcluster)) und des
Forks von 7x, [se7enxweb/ezpostgresqlcluster](https://github.com/se7enxweb/ezpostgresqlcluster). Der Entwurf, den es
beibehält (eine Zeile je Cluster-Datei, die `.generating`-Zeilen, mit denen ein Prozess eine Cache-Datei erzeugt,
während die anderen warten, die Speicher- und die Cache-Tabelle), ist der Entwurf des Cluster-Codes von eZ Systems.
Die SQLite-Klasse ist eine neue Umsetzung desselben Vertrags, Methode für Methode am MySQLi-Backend des Kernels
geschrieben, unter derselben Lizenz (GNU General Public License Version 2 oder später).

## Was der Kernel mitbringt

| Datei | Was sie ist |
|---|---|
| `kernel/private/classes/clusterfilehandlers/dfsbackends/sqlite.php` | `expDFSFileHandlerSQLiteBackend`, das Metadaten-Backend |
| `kernel/sql/sqlite/cluster_dfs_schema.sql` | die Tabellen `ezdfsfile` und `ezdfsfile_cache` |
| `kernel/clustering/dfssqlite.php` | das Gateway, das `index_cluster.php` für `CLUSTER_STORAGE_BACKEND` `dfssqlite` lädt |

Nötig ist SQLite 3.24 oder neuer (`INSERT ... ON CONFLICT DO UPDATE`) mit der PHP-Erweiterung `pdo_sqlite`. PHP 7.4
ist nutzbar; PHP 8.0 bis 8.5 laufen sauber (ab PHP 8.4 werden die Konstanten von `Pdo\Sqlite` verwendet, sodass
PHP 8.5 keine Deprecation meldet).

## Wie es sperrt: ein Schreiber zur Zeit

SQLite hat keine Zeilensperren: Ein Schreibvorgang sperrt die ganze Datenbank. Das Backend nutzt das anstelle des
`SELECT ... FOR UPDATE` der Server-Datenbanken:

- **Jede Transaktion ist `BEGIN IMMEDIATE`.** Sie nimmt die Schreibsperre gleich zu Beginn, sodass sich nichts, was
  die Transaktion gelesen hat, ändern kann, bevor sie schreibt, und zwei Schreiber treffen sich nie mitten in einer
  Transaktion (kein Deadlock, kein `SQLITE_BUSY_SNAPSHOT`).
- **Das Warten ist begrenzt.** Eine Verbindung wartet bis zu ihrem Busy-Timeout auf die Sperre,
  `[eZDFSClusteringSettings] DBLockTimeout` Sekunden (Standard 10). Eine Transaktion, die die Sperre in dieser Zeit
  nicht bekam (`SQLITE_BUSY`, Ergebniscode 5, oder `SQLITE_LOCKED`, 6), versucht das Backend erneut, bis zu
  `DBExecuteRetries` Versuche, mit einer kurzen, wachsenden Pause (höchstens 0,2 s). Die Arbeit einer Transaktion
  läuft nie außerhalb von ihr: Bekommt `BEGIN IMMEDIATE` die Sperre nicht, wartet die Arbeit auf den nächsten Versuch.
- **Leser warten nie.** Die Datenbank wird bei der ersten Verbindung auf den Journal-Modus WAL gestellt (der Modus
  bleibt in der Datei), sodass Seiten und das Datei-Gateway lesen, während ein Schreiber schreibt.
- **Eine Erzeugung je Cache-Datei.** `_startCacheGeneration()` legt die `.generating`-Zeile mit
  `INSERT ... ON CONFLICT (name_hash) DO NOTHING` an: Ein Prozess bekommt die Zeile, die anderen sollen warten oder
  die veraltete Datei ausliefern, wie bei den anderen Datenbanken.

Namenssuchen verwenden `GLOB` statt `LIKE`. `LIKE` ignoriert in SQLite die Groß- und Kleinschreibung und hat
standardmäßig kein Escape-Zeichen; `GLOB` vergleicht mit Groß- und Kleinschreibung, wie MySQLs `LIKE` auf der binären
Spalte `name`, und ein Präfix (`var/site/cache/*`) nutzt den Index auf `name`. Aus dem `%` eines LIKE-Musters wird
`*`, ein Unterstrich bleibt ein Zeichen (wie im MySQLi-Backend), und `*`, `?` und `[` in einem Dateinamen sind
Zeichen. Ein Shell-Wildcard mit Alternativen (`{a,b}.txt`) wird zu einem `GLOB`-Muster je Alternative.

## Erprobte Beispielkonfiguration

```ini
[ClusteringSettings]
FileHandler=eZDFSFileHandler

[eZDFSClusteringSettings]
MountPointPath=/var/www/vhosts/example.com/dfs
DBBackend=expDFSFileHandlerSQLiteBackend
# die Datenbankdatei: ein absoluter Pfad oder ein Name in var/storage/sqlite3 der Installation
DBName=/var/www/vhosts/example.com/dfs-db/cluster.db
# optional: Sekunden, die ein Schreibvorgang auf die Sperre der Datenbank wartet, bevor seine Transaktion erneut läuft
DBLockTimeout=10
DBExecuteRetries=20
```

`DBHost`, `DBPort`, `DBSocket`, `DBUser` und `DBPassword` werden nicht verwendet. Die Datenbankdatei wird ohne das
Create-Flag von SQLite geöffnet: Ein falscher `DBName` ist ein Verbindungsfehler, nie eine neue leere Datenbank.
Einmal mit den Tabellen anlegen:

```bash
sqlite3 /var/www/vhosts/example.com/dfs-db/cluster.db < kernel/sql/sqlite/cluster_dfs_schema.sql
```

Der Ordner der Datei muss für jeden PHP-Benutzer der Website beschreibbar sein (der WAL-Modus legt `cluster.db-wal`
und `cluster.db-shm` daneben) und darf vom Webserver nicht ausgeliefert werden: unter `var/storage` wird er es nicht,
und ein Ordner außerhalb des Document Root ist noch sicherer.

```php
<?php
// config.cluster.php
define( 'CLUSTER_STORAGE_BACKEND', 'dfssqlite' );
define( 'CLUSTER_STORAGE_DB', '/var/www/vhosts/example.com/dfs-db/cluster.db' );
define( 'CLUSTER_STORAGE_CHARSET', 'utf8' );
define( 'CLUSTER_MOUNT_POINT_PATH', '/var/www/vhosts/example.com/dfs' );
```

Die Datenbank der Website und die des Clusters sind getrennte Entscheidungen: Eine Website mit MySQL kann ihre
Cluster-Metadaten auf einem Ein-Knoten-Server in einer SQLite-Datei halten, und eine Website mit SQLite in ihrer
eigenen Datenbankdatei.

## Ein Ein-Knoten-Cluster mit exp:cluster:enable

Auf einer Website mit SQLite als Datenbank (`DatabaseImplementation=sqlite3`) wählt
`./console exp:cluster:enable --apply` das SQLite-Backend von selbst (`--backend=sqlite` nennt es ausdrücklich): Der
Befehl legt die Freigabe an, erzeugt die Cluster-Tabellen in der Datenbankdatei der Website, schreibt die
Einstellungen und `config.cluster.php` (`dfssqlite`, die `Database` der Website als `CLUSTER_STORAGE_DB`) und
verschiebt die gespeicherten Dateien hinein. `exp:cluster:disable` nimmt es zurück.
[Ein DFS-Cluster mit einem Knoten in einem Befehl](cluster-befehle.md) beschreibt beide Befehle.

Website und Cluster teilen dann eine Datenbankdatei und ihre eine Schreibsperre. Bei einer viel schreibenden Website
hält eine eigene Cluster-Datei (`DBName` oben) die Schreibvorgänge des Clusters aus der Warteschlange der Website.

## Tests

| Test | Was er prüft | Braucht |
|---|---|---|
| `expDFSFileHandlerSQLiteBackendTest` | Ergebniscodes und das erneute Versuchen eines belegten `BEGIN IMMEDIATE`, die Datenbankdatei aus `DBName`, LIKE- und Wildcard-Muster als `GLOB`-Muster, Name Trunks, die Verbindungsoptionen, die Methodenparität mit dem MySQLi-Backend, die Schemadatei, das Gateway | nichts |
| `expDFSSQLiteBackendIntegrationTest` | die Paritätsprüfungen aller Backends (`expDFSBackendParityChecks`), dazu WAL-Modus, Indexnutzung bei Präfixsuchen, Groß- und Kleinschreibung von Namen, `GLOB`-Zeichen in Namen, eine fehlende Datenbankdatei und das Gateway mit Metadaten und Byte-Bereichen | `pdo_sqlite` (und `pcntl`, `posix` für die Prüfungen mit mehreren Prozessen) |

Die Paritätsprüfungen sind dieselben, die das MySQLi- und das PostgreSQL-Backend bestehen, darunter **vier Prozesse,
die gleichzeitig eine Cache-Datei erzeugen wollen** (einer erzeugt, drei warten, keiner scheitert) und **eine Sperre
einer anderen Verbindung**: Ein zweiter Prozess hält `BEGIN IMMEDIATE`, das Speichern wartet eine Sekunde, wird
erneut versucht und gelingt, sobald die Sperre weg ist, oder gibt nach `DBExecuteRetries` Versuchen mit `SQLITE_BUSY`
auf, statt zu hängen. Alle 29 bestehen. Beide Testklassen brauchen keinen Server; sie laufen in der Standard-Suite
und im Job "DFS cluster backends" von GitHub Actions.

Gemessen am 19. November 2026 auf alpha: die Paritätssuite 29 von 29; ein Ein-Knoten-Cluster über den echten
`eZDFSFileHandler` (Speichern und Holen einer 2-MB-Datei, Kopieren, Umbenennen, Löschen, Purge, ein View-Cache,
erzeugt, über seinen Name Trunk abgelaufen und neu erzeugt); das Gateway `index_cluster.php` mit `dfssqlite` (eine
ganze Datei, ein Byte-Bereich, ein 404); **zwei PHP-Server eines Hosts** (PHP 8.5 und PHP 8.4, jeder mit eigenem
lokalem `var`, eine Freigabe, eine Datenbankdatei) fragen gleichzeitig nach einem Cache-Schlüssel, 20 Runden: genau
eine Erzeugung je Runde, kein Fehler, die langsamste Antwort etwa 1 Sekunde; und eine Laufzeitprüfung des Backends
unter PHP 7.4, 8.0, 8.1, 8.2, 8.3, 8.4 und 8.5 ohne Notice oder Deprecation.

## Grenzen

- **Eine Maschine.** Mehrere Webserver können eine SQLite-Datei nicht sicher teilen; für sie MySQL/MariaDB oder
  PostgreSQL verwenden. Die Freigabe (`MountPointPath`) eines Ein-Knoten-Clusters kann trotzdem ein NFS-Export sein,
  damit ein zweiter Server sie einhängen kann, sobald die Metadaten auf eine Server-Datenbank umziehen.
- **Ein Schreiber zur Zeit.** Schreibvorgänge warten auf die Sperre der Datenbank; auf einer viel schreibenden
  Website warten die Schreibvorgänge des Clusters und die der Website (wenn sie eine Datei teilen) aufeinander. Leser
  warten nie.
- **Noch kein `eZDFSFileHandlerTest` mit SQLite**: Der Datenbank-Testlauf des Kernels richtet MySQL-, PostgreSQL-,
  Oracle- und MongoDB-Datenbanken ein; das SQLite-Backend wird stattdessen mit den Paritätsprüfungen oben getestet.

## Verwandte Seiten

- [Clustering in Exponential 6.0](clustering-ueberblick.md)
- [Clustering mit PostgreSQL: das DFS-Backend im Kernel](postgresql-dfs-cluster.md)
- [Ein DFS-Cluster mit einem Knoten in einem Befehl](cluster-befehle.md)
- [Unterstützung für die Datenbank SQLite](sqlite-datenbank.md)
- [Clustering: DFS und die Datenbank](../../hilfe/datenbankadministratoren/clustering-dfs-und-die-datenbank.md)
- [Clustering und gemeinsamer Speicher](../../hilfe/hosting/betrieb/clustering-und-gemeinsamer-speicher.md)
- [Changelog 6.0.15](../../../changelogs/6.0/6.0.15.md)
