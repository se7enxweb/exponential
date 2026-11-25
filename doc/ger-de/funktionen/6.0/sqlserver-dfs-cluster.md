# Clustering mit SQL Server: das DFS-Backend im Kernel

Diese Seite ist für Administratoren, die eine Website auf Microsoft SQL Server oder Azure SQL betreiben und ihre
Dateien in einem DFS-Cluster auf mehreren Webservern halten wollen, und für Entwickler, die die Cluster-Backends
pflegen. Ab Exponential 6.0.15 hält der DFS-Cluster des Kernels seine Datei-Metadaten auch in SQL Server, neben
MySQL/MariaDB, PostgreSQL, Oracle, MongoDB und SQLite: Das Backend, seine Tabellen und sein Datei-Gateway gehören zum
Kernel und nutzen die PHP-Schicht des [SQL-Server-Treibers](sqlserver-datenbanktreiber.md) des Kernels (`sqlsrv`,
`pdo_sqlsrv`, PDO_ODBC oder `pdo_dblib`).

## Inhalt

1. [Herkunft und Dank](#herkunft-und-dank)
2. [Was der Kernel mitbringt](#was-der-kernel-mitbringt)
3. [Wie es sperrt: begrenztes Warten auf Zeilensperren](#wie-es-sperrt-begrenztes-warten-auf-zeilensperren)
4. [Erprobte Beispielkonfiguration](#erprobte-beispielkonfiguration)
5. [Ein Ein-Knoten-Cluster mit exp:cluster:enable](#ein-ein-knoten-cluster-mit-expclusterenable)
6. [Tests](#tests)
7. [Grenzen](#grenzen)
8. [Verwandte Seiten](#verwandte-seiten)

## Herkunft und Dank

Das SQL-Server-Backend ist Methode für Methode nach dem [Oracle-Backend](oracle-dfs-cluster.md) des Kernels gebaut,
das dem [PostgreSQL-Backend](postgresql-dfs-cluster.md) des Kernels folgt: einer Überarbeitung und Integration von
**ezpostgresqlcluster** von **eZ Systems AS** (GNU General Public License Version 2,
[github.com/ezsystems/ezpostgresqlcluster](https://github.com/ezsystems/ezpostgresqlcluster)) und des Forks von 7x,
[se7enxweb/ezpostgresqlcluster](https://github.com/se7enxweb/ezpostgresqlcluster). Der Entwurf, den es beibehält (eine
Zeile je Cluster-Datei, die `.generating`-Zeilen, mit denen ein Prozess eine Cache-Datei erzeugt, während die anderen
warten, die Speicher- und die Cache-Tabelle), ist der Entwurf des Cluster-Codes von eZ Systems. Die SQL-Server-Klasse
ist eine neue Umsetzung desselben Vertrags, unter derselben Lizenz (GNU General Public License Version 2 oder später).
Sie enthält keinen Code der alten Erweiterung xrowmssql.

## Was der Kernel mitbringt

| Datei | Was sie ist |
|---|---|
| `kernel/private/classes/clusterfilehandlers/dfsbackends/sqlserver.php` | `expDFSFileHandlerSQLServerBackend`, das Metadaten-Backend |
| `kernel/private/classes/clusterfilehandlers/dfsbackends/sqlserverresult.php` | `expDFSSQLServerResult`, die Zeilen einer Anweisung |
| `kernel/sql/sqlserver/cluster_dfs_schema.sql` | die Tabellen `ezdfsfile` und `ezdfsfile_cache` |
| `kernel/clustering/dfssqlserver.php` | das Gateway, das `index_cluster.php` für `CLUSTER_STORAGE_BACKEND` `dfssqlserver` lädt |

Gebraucht wird SQL Server 2017 oder neuer, oder Azure SQL Database bzw. Managed Instance, mit einer der PHP-Schichten
des Treibers (unter Linux über Microsofts ODBC Driver 17 oder 18). PHP 7.4 ist nutzbar; PHP 8.0 bis 8.5 laufen sauber.
Getestet mit SQL Server 2022 (Linux-Container), in der CI mit 2017, 2019, 2022 und 2025.

Was SQL Server an den Tabellen ändert:

- **Namen sind `NVARCHAR(830)` in der binären Sortierung `Latin1_General_100_BIN2`.** Groß- und Kleinschreibung zählt,
  wie beim binären `LIKE` von MySQL, und kurz genug, dass der Namensindex mit den 34 Bytes des gruppierten
  `name_hash`, die ein nicht eindeutiger Index mitträgt, in den Schlüssel von 1.700 Bytes passt. Ein Dateiname über
  830 Zeichen lässt sich nicht speichern (die anderen Datenbanken erlauben längere).
- **`name_hash` ist `VARCHAR(34)`, ebenfalls in einer Windows-Sortierung.** Der Treiber bindet jeden Wert als
  `NVARCHAR`; nur mit einer Windows-Sortierung sucht SQL Server einen `VARCHAR`-Schlüssel mit einem `NVARCHAR`-Wert
  gezielt. Mit der englischen Standardsortierung des Servers (`SQL_Latin1_General_CP1_CI_AS`) läse jede Abfrage die
  ganze Tabelle.
- **Die Größe steht in `filesize`**, wie in der Oracle-Tabelle; das Backend gibt sie wie jedes andere als `size`
  zurück. Größen und Zeiten sind `BIGINT` (keine Grenze 2038).

## Wie es sperrt: begrenztes Warten auf Zeilensperren

- **Jede Sitzung setzt `LOCK_TIMEOUT`** auf `[eZDFSClusteringSettings] DBLockTimeout` (Sekunden, Standard 10). Eine
  Änderung sperrt ihre Zeilen zuerst mit `WITH (UPDLOCK, ROWLOCK)`. Ein abgelaufenes Warten (Fehler 1222), ein
  Deadlock (1205), ein Snapshot-Konflikt (3960) und der doppelte Schlüssel zweier Prozesse, die dieselbe neue Zeile
  zugleich einfügen (2627, 2601), wiederholt das Backend bis zu `DBExecuteRetries` Mal, mit einer kurzen, mit jedem
  Versuch wachsenden Pause (höchstens 0,2 s). Nach dem letzten Versuch scheitert das Speichern, statt zu hängen.
- **Speichern ist `MERGE ... WITH (HOLDLOCK)`**: eine Anweisung fügt die Zeile ein oder ändert sie, ohne Lücke
  zwischen Prüfen und Einfügen.
- **Eine Erzeugung je Cache-Datei.** `_startCacheGeneration()` fügt die `.generating`-Zeile mit einem einfachen
  `INSERT` ein: ihr `name_hash` ist der Primärschlüssel, also bekommt ein Prozess die Zeile, die anderen bekommen
  Fehler 2627 und warten oder liefern die alte Datei. Die Übernahme nach Ablauf der Zeit ist ein `UPDATE`, das an die
  gelesene `mtime` gebunden ist.
- **Bereinigen überspringt gesperrte Zeilen** mit `READPAST`, sodass zwei Bereinigungen nie aufeinander warten.
- **Zeilenzahlen kommen zurück.** Die Sitzungseinrichtung des Treibers schaltet `NOCOUNT` ein; die Cluster-Verbindung
  schaltet es wieder aus, weil Cache-Erzeugung und Bereinigung nach der Zahl der geänderten Zeilen entscheiden.

Namenssuchen nutzen `LIKE ... ESCAPE '\'`. Das `%` eines LIKE-Musters bleibt Platzhalter, ein Unterstrich ist ein
Literal (wie im MySQLi-Backend), und `%`, `_`, `[` (in SQL Server der Beginn einer Zeichenklasse) und `\` in einem
Dateinamen sind Literale. Ein Shell-Platzhalter mit Alternativen (`{a,b}.txt`) wird zu einem `LIKE`-Muster je
Alternative. Listen nutzen `TOP` und `OFFSET ... FETCH NEXT`.

## Erprobte Beispielkonfiguration

```ini
[ClusteringSettings]
FileHandler=eZDFSFileHandler

[eZDFSClusteringSettings]
MountPointPath=/var/www/vhosts/example.com/dfs
DBBackend=expDFSFileHandlerSQLServerBackend
# Host und Port, oder eine benannte Instanz als host\instanz (ohne Port)
DBHost=sql.example
DBPort=1433
DBName=cluster
DBUser=cluster
DBPassword=secret
DBConnectRetries=3
DBExecuteRetries=20
# optional: Sekunden, die eine Änderung auf eine Zeilensperre wartet, bevor ihre Transaktion wiederholt wird
DBLockTimeout=10
```

PHP-Schicht und Verschlüsselung der Verbindung kommen aus `site.ini [SQLServerSettings]`, wie für die Datenbank der
Website (`Layer`, `ODBCDriver`, `Encrypt`, `TrustServerCertificate`, `MultiSubnetFailover`). Die Gruppe gehört in
`settings/override/site.ini.append.php`: Der Cluster verbindet sich, bevor ein Siteaccess erkannt ist, und sieht einen
Wert nur in einem Siteaccess nicht. Der Installer schreibt sie für eine Website auf SQL Server dorthin. Die Tabellen
einmal in der Datenbank des Clusters anlegen:

```bash
sqlcmd -S sql.example,1433 -U cluster -d cluster -C -i kernel/sql/sqlserver/cluster_dfs_schema.sql
```

```php
<?php
// config.cluster.php
define( 'CLUSTER_STORAGE_BACKEND', 'dfssqlserver' );
define( 'CLUSTER_STORAGE_HOST', 'sql.example' );
define( 'CLUSTER_STORAGE_PORT', 1433 );
define( 'CLUSTER_STORAGE_USER', 'cluster' );
define( 'CLUSTER_STORAGE_PASS', 'secret' );
define( 'CLUSTER_STORAGE_DB', 'cluster' );
define( 'CLUSTER_STORAGE_CHARSET', 'utf8' );
define( 'CLUSTER_MOUNT_POINT_PATH', '/var/www/vhosts/example.com/dfs' );
// optional, wie site.ini [SQLServerSettings]: auto, sqlsrv, pdo_sqlsrv, pdo_odbc oder pdo_dblib
define( 'CLUSTER_SQLSERVER_LAYER', 'auto' );
define( 'CLUSTER_SQLSERVER_ENCRYPT', 'yes' );
// nur für ein selbst signiertes Zertifikat in einem Netz, dem Sie vertrauen
define( 'CLUSTER_SQLSERVER_TRUST_SERVER_CERTIFICATE', false );
```

Das Gateway lädt die Transportklassen des Treibers selbst (`index_cluster.php` hat keinen Autoloader) und verdeckt das
Passwort in seinen Fehlermeldungen. Jeder Webserver hängt dieselbe Freigabe ein (`MountPointPath`, ein NFS-Export) und
nutzt dieselbe Cluster-Datenbank. `bin/php/clusterpurge.php` (der Cronjob-Teil `cluster_maintenance`) bereinigt über
`expiredFilesList()` und `_purge()` des Backends, die die Paritätsprüfungen testen.

## Ein Ein-Knoten-Cluster mit exp:cluster:enable

Auf einer Website mit SQL Server als Datenbank (`DatabaseImplementation=sqlserver`) wählt
`./console exp:cluster:enable --apply` das SQL-Server-Backend selbst (`--backend=sqlserver` nennt es): Es legt die
Freigabe an, erzeugt die Tabellen aus `kernel/sql/sqlserver/cluster_dfs_schema.sql` in der eigenen Datenbank der
Website über deren Datenbanktreiber, schreibt die Einstellungen und `config.cluster.php` (`dfssqlserver`, Server, Port
und Konto der Website, dazu Schicht und Verschlüsselung aus `[SQLServerSettings]` als Konstanten
`CLUSTER_SQLSERVER_*`) und verschiebt die gespeicherten Dateien hinein. `exp:cluster:disable` nimmt es zurück.
[Ein Ein-Knoten-DFS-Cluster mit einem Befehl](cluster-befehle.md) beschreibt beide Befehle.

## Tests

| Test | Was er prüft | Braucht |
|---|---|---|
| `expDFSFileHandlerSQLServerBackendTest` | die SQL-Server-Fehler, die wiederholt werden, LIKE-Muster, Präfixe und Platzhalter mit `ESCAPE '\'` (`[` als Literal), Zeilen so, wie die anderen Backends sie liefern (`filesize` als `size`, `NULL` als `''`), `:name`-Parameter als `?`, die Ergebnisklasse, Namensstämme, Methodengleichheit mit dem MySQLi-Backend, nur SQL-Server-SQL, die Schemadatei, das Gateway und seine Verbindungseinstellungen | nichts |
| `expDFSSQLServerBackendIntegrationTest` | die Paritätsprüfungen aller Backends (`expDFSBackendParityChecks`), dazu Spaltentypen und Sortierungen, Groß- und Kleinschreibung der Namen, LIKE-Zeichen in Namen, Unicode-Namen, eine fehlende Tabelle, ein falsches Passwort (in der Meldung verdeckt) und das Gateway mit Metadaten und Byte-Bereichen | eine SQL-Server-Anmeldung, die eine Datenbank anlegen darf (`EXP_TEST_SQLSERVER_HOST`, `_PORT`, `_USER`, `_PASSWORD` oder `_PASSWORD_FILE`, `_LAYER`), sowie `pcntl`, `posix` für die Prüfungen mit mehreren Prozessen |
| `expClusterSetupTest` | der Plan von `exp:cluster:enable` auf SQL Server: Datenbank, Gateway, Port 1433, der INI-Block und die Konstanten `CLUSTER_SQLSERVER_*` | nichts |

```bash
EXP_TEST_SQLSERVER_HOST=127.0.0.1 EXP_TEST_SQLSERVER_PORT=1433 EXP_TEST_SQLSERVER_PASSWORD=secret EXP_TEST_SQLSERVER_LAYER=pdo_odbc \
  php vendor/bin/phpunit tests/tests/kernel/classes/clusterfilehandlers/dfsbackends/expDFSSQLServerBackendIntegrationTest.php
```

Der Integrationstest legt beim ersten Lauf die Datenbank `exp_dfs_test` an (`EXP_TEST_DFS_SQLSERVER_DATABASE` nennt
eine andere), erzeugt die beiden Tabellen und löscht sie nach dem letzten Test; eine Datenbank, die sie schon hat,
bleibt unberührt (die Klasse wird übersprungen). Die Paritätsprüfungen sind dieselben wie bei den anderen Backends,
darunter **vier Prozesse, die zugleich eine Cache-Datei erzeugen** (einer erzeugt, drei warten, keiner scheitert) und
**eine Zeilensperre einer anderen Verbindung**: Eine zweite Sitzung hält `WITH (UPDLOCK, ROWLOCK)` in einer offenen
Transaktion, das Speichern wartet eine Sekunde, wird wiederholt und gelingt, sobald die Sperre weg ist, oder gibt
nach `DBExecuteRetries` Versuchen auf, statt zu hängen.

**Die beiden Prüfungen mit parallelen Prozessen laufen auf PDO_ODBC.** `sqlsrv` und `pdo_sqlsrv` behalten ihre
ODBC-Umgebung für die Lebensdauer des Prozesses und damit den Socket einer Verbindung, die der Prozess geschlossen hat
(er bleibt nach `sqlsrv_close()` verbunden). Ein Kindprozess, der nach so einem Schließen abgezweigt wird, bekommt
einen Treiber, dessen neue Verbindungen der Server zurücksetzt (TCP Provider Fehler 0x2746, danach 0x20 auch im
Elternprozess). PDO_ODBC gibt seine Umgebung mit der Verbindung frei und lässt sich sauber abzweigen. Auf `sqlsrv` und
`pdo_sqlsrv` werden die beiden Prüfungen mit diesem Grund übersprungen; die CI führt sie in ihrem PDO_ODBC-Lauf aus.

Gemessen am 25. November 2026 auf cluster gegen SQL Server 2022 in einem Linux-Container: der Integrationstest 32 von
32 auf PDO_ODBC und 30 von 32 mit den beiden Übersprüngen auf `sqlsrv` und `pdo_sqlsrv` (PHP 8.5), der Unit-Test 13
von 13. Durchgehend auf einer mit `install.php --db=sqlserver` auf SQL Server installierten Website:
`exp:cluster:enable --apply` legte Tabellen und Einstellungen an und verschob 312 Dateien hinein; der echte
`eZDFSFileHandler` auf dem Backend speicherte, las, kopierte, verschob, löschte und bereinigte eine Datei von 2 MB,
erzeugte einen View-Cache einmal mit `processCache()`, den die nächste Anfrage las, und ließ ihn über seinen
Namensstamm ablaufen.

## Grenzen

- **SQL Server 2017 oder neuer** (und Azure SQL). Azure SQL Edge hat Microsoft eingestellt; es wird nicht unterstützt.
- **Namen bis 830 Zeichen**, wegen des Indexschlüssels (oben).
- **Ein Prozess, der nach dem Schließen einer Verbindung Kindprozesse abzweigt**, kann in ihnen `sqlsrv` oder
  `pdo_sqlsrv` nicht nutzen (oben). PHP-Worker eines Webservers tun das nicht; ein Skript, das nach der Nutzung des
  Clusters Worker abzweigt, verbindet sich nur in jedem Kind oder nutzt PDO_ODBC.
- **Noch keine dauerhafte Verbindung** (`DBPersistentConnection` wird nicht gelesen): eine Verbindung je Anfrage.
- **Noch kein `eZDFSFileHandlerTest` auf SQL Server**: Der Datenbank-Testlauf des Kernels richtet dafür MySQL- und
  PostgreSQL-Datenbanken ein; das Backend wird stattdessen über die Paritätsprüfungen und den durchgehenden Lauf oben
  getestet.

## Verwandte Seiten

- [Microsoft-SQL-Server-Treiber](sqlserver-datenbanktreiber.md)
- [Clustering in Exponential 6.0](clustering-ueberblick.md)
- [Clustering mit Oracle: das DFS-Backend im Kernel](oracle-dfs-cluster.md)
- [Clustering mit PostgreSQL: das DFS-Backend im Kernel](postgresql-dfs-cluster.md)
- [Ein Ein-Knoten-DFS-Cluster mit einem Befehl](cluster-befehle.md)
- [Clustering: DFS und seine Metadaten-Datenbank](../../hilfe/datenbankadministratoren/clustering-dfs-und-die-datenbank.md)
- [Änderungsprotokoll 6.0.15](../../../changelogs/6.0/6.0.15.md)
