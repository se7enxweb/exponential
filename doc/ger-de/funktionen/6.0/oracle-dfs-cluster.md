# Clustering mit Oracle: das DFS-Backend im Kernel

Diese Seite ist für Administratoren, die eine Website auf Oracle Database betreiben und ihre Dateien in einem
DFS-Cluster auf mehreren Webservern halten wollen, und für Entwickler, die die Cluster-Backends pflegen. Ab
Exponential 6.0.15 hält der DFS-Cluster des Kernels seine Datei-Metadaten auch in Oracle, neben MySQL/MariaDB,
PostgreSQL, MongoDB und SQLite: Das Backend, seine Tabellen und sein Datei-Gateway gehören zum Kernel und brauchen nur
die PHP-Erweiterung `oci8`. Die Erweiterung `ezoracle` braucht eine Website weiterhin für Oracle als ihre eigene
Datenbank; ihr Cluster-Backend `eZDFSFileHandlerOracleBackend` wird durch das des Kernels ersetzt, und die Tabelle, die
es angelegt hat, wird mit einer SQL-Datei aktualisiert.

## Inhalt

1. [Herkunft und Dank](#herkunft-und-dank)
2. [Was der Kernel mitbringt](#was-der-kernel-mitbringt)
3. [Wie es sperrt: begrenztes Warten auf Zeilensperren](#wie-es-sperrt-begrenztes-warten-auf-zeilensperren)
4. [Erprobte Beispielkonfiguration](#erprobte-beispielkonfiguration)
5. [Vom Backend der Erweiterung ezoracle kommend](#vom-backend-der-erweiterung-ezoracle-kommend)
6. [Ein Ein-Knoten-Cluster mit exp:cluster:enable](#ein-ein-knoten-cluster-mit-expclusterenable)
7. [Tests](#tests)
8. [Grenzen](#grenzen)
9. [Verwandte Seiten](#verwandte-seiten)

## Herkunft und Dank

Das Oracle-Backend folgt dem Aufbau des [PostgreSQL-Backends](postgresql-dfs-cluster.md) des Kernels, das eine
Überarbeitung und Integration von **ezpostgresqlcluster** von **eZ Systems AS** ist (GNU General Public License
Version 2, [github.com/ezsystems/ezpostgresqlcluster](https://github.com/ezsystems/ezpostgresqlcluster)) und des
Forks von 7x, [se7enxweb/ezpostgresqlcluster](https://github.com/se7enxweb/ezpostgresqlcluster). Seine
Oracle-Besonderheiten (die Spalte `filesize`, die Verbindungszeichenkette in `DBName`, das Bereinigen mit
`FOR UPDATE`, ORA-00060 als Fehler, der wiederholt wird) stammen aus dem Cluster-Backend der Erweiterung **ezoracle**
von **eZ Systems AS** (GNU General Public License Version 2), die 7x für Exponential pflegt. Der Entwurf, den es
beibehält (eine Zeile je Cluster-Datei, die `.generating`-Zeilen, mit denen ein Prozess eine Cache-Datei erzeugt,
während die anderen warten, die Speicher- und die Cache-Tabelle), ist der Entwurf des Cluster-Codes von eZ Systems.
Die Kernel-Klasse ist eine neue Umsetzung desselben Vertrags, Methode für Methode am MySQLi- und am SQLite-Backend des
Kernels geschrieben, unter derselben Lizenz (GNU General Public License Version 2 oder später).

## Was der Kernel mitbringt

| Datei | Was sie ist |
|---|---|
| `kernel/private/classes/clusterfilehandlers/dfsbackends/oracle.php` | `expDFSFileHandlerOracleBackend`, das Metadaten-Backend |
| `kernel/sql/oracle/cluster_dfs_schema.sql` | die Tabellen `ezdfsfile` und `ezdfsfile_cache` |
| `kernel/sql/oracle/cluster_dfs_schema_upgrade.sql` | die Aktualisierung der Tabelle, die die Erweiterung `ezoracle` angelegt hat |
| `kernel/clustering/dfsoracle.php` | das Gateway, das `index_cluster.php` für `CLUSTER_STORAGE_BACKEND` `dfsoracle` lädt |

Nötig ist Oracle Database 12.1 oder neuer (die Listen verwenden `OFFSET ... FETCH NEXT`) mit der PHP-Erweiterung
`oci8` und einem Oracle-Client (Instant Client). PHP 7.4 ist nutzbar; PHP 8.0 bis 8.5 laufen sauber. Getestet mit
Oracle AI Database 26ai Free (23.26).

Zwei Eigenheiten von Oracle prägen die Tabellen:

- **`SIZE` ist ein reserviertes Wort**, deshalb heißt die Spalte für die Größe `filesize`, wie in der Tabelle der
  Erweiterung `ezoracle`. Das Backend gibt sie wie jedes andere Backend als `size` zurück und verwendet nie einen
  Spaltennamen als Namen einer Bindevariablen (`:size` ergibt ORA-01745).
- **Oracle speichert die leere Zeichenkette als `NULL`.** `datatype` und `scope` dürfen `NULL` sein (eine
  `.generating`-Zeile hat keins von beiden) und werden als `''` gelesen. Eine Liste „jeder Scope außer diesen“ behält
  eine Zeile ohne Scope, wie die anderen Datenbanken eine Zeile mit dem Scope `''` behalten.

Größen und Zeiten sind `NUMBER(19)` (keine Grenze im Jahr 2038). Namen werden binär und mit Unterscheidung der Groß-
und Kleinschreibung verglichen (Oracles Voreinstellung für `NLS_COMP`), sodass der Namensindex sowohl `name = ...` als
auch die Präfixsuchen der Lösch- und Bereinigungsvorgänge bedient.

## Wie es sperrt: begrenztes Warten auf Zeilensperren

Oracle hat Zeilensperren wie MySQL und PostgreSQL, aber ein `UPDATE` oder `DELETE` wartet auf eine gesperrte Zeile
**ohne Grenze**. Das Backend lässt deshalb nie eine Änderung von selbst warten:

- **Jede Änderung sperrt ihre Zeilen zuerst** mit `SELECT ... FOR UPDATE WAIT n`, wobei n `[eZDFSClusteringSettings]
  DBLockTimeout` ist (Sekunden, Standard 10). Ein abgelaufenes Warten löst ORA-30006 aus (ORA-00054 ab Oracle 23ai),
  das das Backend bis zu `DBExecuteRetries`-mal erneut versucht, mit einer kurzen Pause, die mit jedem Versuch wächst
  (höchstens 0,2 s). Ein Deadlock (ORA-00060), ein Serialisierungsfehler (ORA-08177) und der doppelte Schlüssel zweier
  Prozesse, die gleichzeitig dieselbe neue Zeile einfügen (ORA-00001), werden ebenso wiederholt. Nach dem letzten
  Versuch schlägt das Speichern fehl, statt hängen zu bleiben.
- **Speichern ist `MERGE`**: Eine Anweisung fügt die Zeile ein oder aktualisiert sie.
- **Eine Erzeugung je Cache-Datei.** `_startCacheGeneration()` fügt die `.generating`-Zeile mit einem einfachen
  `INSERT` ein: Ihr `name_hash` ist der Primärschlüssel, also erhält ein Prozess die Zeile, und die anderen erhalten
  ORA-00001 und warten oder liefern die veraltete Datei aus, wie auf den anderen Datenbanken. Eine Übernahme nach der
  Zeitgrenze ist ein `UPDATE`, das an die gelesene `mtime` gebunden ist.
- **Bereinigen überspringt gesperrte Zeilen**: `_purgeByLike()` wählt mit `FOR UPDATE SKIP LOCKED` aus, so warten zwei
  Bereinigungen nie aufeinander; die Zeilen, die ein anderer Prozess hält, bleiben für den nächsten Lauf.

Namenssuchen verwenden `LIKE ... ESCAPE '\'`. Das `%` eines LIKE-Musters bleibt ein Platzhalter, ein Unterstrich ist
ein Zeichen (wie im MySQLi-Backend), und `%`, `_` und `\` in einem Dateinamen stehen für sich selbst. Ein
Shell-Platzhalter mit Alternativen (`{a,b}.txt`) wird ein `LIKE`-Muster je Alternative.

## Erprobte Beispielkonfiguration

```ini
[ClusteringSettings]
FileHandler=eZDFSFileHandler

[eZDFSClusteringSettings]
MountPointPath=/var/www/vhosts/example.com/dfs
DBBackend=expDFSFileHandlerOracleBackend
# die Verbindungszeichenkette: Easy Connect host:port/service, ein TNS-Alias oder ein ganzer Deskriptor
DBName=db.example:1521/EXAMPLEPDB
DBUser=cluster
DBPassword=secret
DBConnectRetries=3
DBExecuteRetries=20
# optional: Sekunden, die eine Änderung auf eine Zeilensperre wartet, bevor ihre Transaktion wiederholt wird
DBLockTimeout=10
# optional: oci_pconnect() statt einer neuen Verbindung je Anfrage (enabled|disabled)
DBPersistentConnection=disabled
```

`DBHost`, `DBPort` und `DBSocket` werden nicht verwendet. Der Client spricht AL32UTF8. Legen Sie die Tabellen einmal im
Schema von `DBUser` an (ein eigenes Schema für den Cluster empfiehlt auch die Erweiterung `ezoracle`, für Sicherungen
und damit sich die Transaktionen der Website und die des Clusters nie mischen):

```bash
sqlplus cluster/secret@db.example:1521/EXAMPLEPDB @kernel/sql/oracle/cluster_dfs_schema.sql
```

```php
<?php
// config.cluster.php
define( 'CLUSTER_STORAGE_BACKEND', 'dfsoracle' );
define( 'CLUSTER_STORAGE_USER', 'cluster' );
define( 'CLUSTER_STORAGE_PASS', 'secret' );
define( 'CLUSTER_STORAGE_DB', 'db.example:1521/EXAMPLEPDB' );
define( 'CLUSTER_STORAGE_CHARSET', 'utf8' );
define( 'CLUSTER_MOUNT_POINT_PATH', '/var/www/vhosts/example.com/dfs' );
```

Jeder Webserver bindet dieselbe Freigabe ein (`MountPointPath`, ein NFS-Export) und verwendet dasselbe
Cluster-Schema. `bin/php/clusterpurge.php` (der Cronjob-Teil `cluster_maintenance`) funktioniert mit dem
Kernel-Backend; `oracleclusterbinarypurge.php` der Erweiterung `ezoracle` braucht es nicht.

## Vom Backend der Erweiterung ezoracle kommend

Das `eZDFSFileHandlerOracleBackend` der Erweiterung `ezoracle` hielt eine Tabelle, `ezdfsfile`, ohne `name_trunk` und
mit den Cache-Dateien darin. Zum Wechsel auf das Kernel-Backend:

1. Die Website in den Wartungsmodus versetzen (oder die Webserver anhalten).
2. Die Aktualisierung im Cluster-Schema ausführen:
   `sqlplus cluster/secret@db.example:1521/EXAMPLEPDB @kernel/sql/oracle/cluster_dfs_schema_upgrade.sql`. Sie fügt
   `name_trunk` (mit dem Namen gefüllt) und seinen Index hinzu, entfernt die Zeilen der Cache-Dateien (sie werden neu
   erzeugt) und legt `ezdfsfile_cache` an. Die Spalten `filesize`, `mtime`, `expired` (dort `CHAR(1)`, vom
   Kernel-Backend als Zahl verglichen) und `status` bleiben, wie sie sind.
3. `DBBackend=expDFSFileHandlerOracleBackend` setzen und in `config.cluster.php` `CLUSTER_STORAGE_BACKEND` `dfsoracle`.
4. Die Caches leeren, dann `php bin/php/dfscleanup.php -S -B` ausführen und, nachdem Sie das Ergebnis gelesen haben,
   mit `-D`, um die Cache-Dateien ohne Zeile von der Freigabe zu entfernen.

## Ein Ein-Knoten-Cluster mit exp:cluster:enable

Auf einer Website mit Oracle als Datenbank (`DatabaseImplementation=ezoracle`) wählt `./console exp:cluster:enable
--apply` das Oracle-Backend von selbst (`--backend=oracle` nennt es): Es legt die Freigabe an, erstellt die Tabellen
aus `kernel/sql/oracle/cluster_dfs_schema.sql` im eigenen Schema der Website über deren Datenbanktreiber, schreibt die
Einstellungen und `config.cluster.php` (`dfsoracle`, die `Database` der Website als Verbindungszeichenkette, ihr Konto)
und verschiebt die gespeicherten Dateien hinein. `exp:cluster:disable` nimmt es zurück.
[Ein Ein-Knoten-DFS-Cluster mit einem Befehl](cluster-befehle.md) beschreibt beide Befehle.

## Tests

| Test | Was er prüft | Braucht |
|---|---|---|
| `expDFSFileHandlerOracleBackendTest` | die wiederholten Oracle-Fehler, LIKE-Muster, Präfixe und Platzhalter mit `ESCAPE '\'`, Zeilen so, wie die anderen Backends sie zurückgeben (`filesize` als `size`, `NULL` als `''`), Namensstämme, die Methodengleichheit mit dem MySQLi-Backend, nur Oracle-SQL und keine Bindevariable mit dem Namen eines reservierten Worts, die Schema- und die Aktualisierungsdatei, das Gateway | nichts |
| `expDFSOracleBackendIntegrationTest` | die Paritätsprüfungen jedes Backends (`expDFSBackendParityChecks`), dazu die Spaltentypen, Groß- und Kleinschreibung in Namen, LIKE-Zeichen in Namen, die leere Zeichenkette als `NULL` in Scopes, eine fehlende Tabelle, ein falsches Passwort und das Gateway mit Metadaten und Bytebereichen | ein Oracle-Konto mit `CREATE TABLE` (`EXP_TEST_DFS_ORACLE_USER`, `EXP_TEST_DFS_ORACLE_PASSWORD`, `EXP_TEST_DFS_ORACLE_CONNECT`), `oci8` und `pcntl`, `posix` für die Prüfungen mit mehreren Prozessen |

```bash
EXP_TEST_DFS_ORACLE_USER=dfstest EXP_TEST_DFS_ORACLE_PASSWORD=secret EXP_TEST_DFS_ORACLE_CONNECT=127.0.0.1:1521/FREEPDB1 \
  php vendor/bin/phpunit tests/tests/kernel/classes/clusterfilehandlers/dfsbackends/expDFSOracleBackendIntegrationTest.php
```

Der Integrationstest legt die beiden Tabellen im Schema des Kontos an und löscht sie nach dem letzten Test; ein
Schema, das sie schon hat, bleibt unberührt (die Klasse wird übersprungen), deshalb ein eigenes Konto verwenden. Die
Paritätsprüfungen sind dieselben, die die anderen Backends bestehen, darunter **vier Prozesse, die im selben Moment
eine Cache-Datei erzeugen wollen** (einer erzeugt, drei warten, keiner scheitert) und **eine Zeilensperre, die eine
andere Verbindung hält**: Eine zweite Sitzung hält `SELECT ... FOR UPDATE`, das Speichern wartet eine Sekunde, wird
wiederholt und gelingt, sobald die Sperre weg ist, oder gibt nach `DBExecuteRetries` Versuchen auf, statt hängen zu
bleiben.

Gemessen am 20. November 2026 auf alpha, gegen Oracle AI Database 26ai Free (23.26) in einem Wegwerf-Konto: der
Integrationstest 30 von 30 (12 Läufe, in einem davon eine Zeitprüfung verfehlt, während die Last des Servers hoch war),
der Unit-Test 10 von 10, auf PHP 8.5; der echte `eZDFSFileHandler` auf dem Backend, von Anfang bis Ende: die zehn
Schema-Anweisungen, wie `exp:cluster:enable` sie ausführt, eine 2-MB-Datei gespeichert, geholt, kopiert, verschoben,
gelöscht und bereinigt, ein View-Cache von `processCache()` einmal erzeugt und von der nächsten Anfrage gelesen, in
`ezdfsfile_cache` gehalten, über seinen Namensstamm abgelaufen; und die Aktualisierung von der Tabelle der Erweiterung
`ezoracle` (ihre alte Speicherzeile gelesen, Speichern und Löschen auf der `CHAR(1)`-Spalte `expired`, die alte
Cache-Zeile entfernt). Backend und Gateway bestehen `php -l` auf PHP 7.4 bis 8.5.

## Grenzen

- **Oracle 12.1 oder neuer** (`OFFSET ... FETCH NEXT`). Ältere Versionen behalten das Backend der Erweiterung
  `ezoracle`.
- **Eine Verbindung je Anfrage**, außer mit `DBPersistentConnection=enabled`; mit DRCP oder vielen Webservern die
  Sitzungen der Datenbank darauf auslegen.
- **Das Cluster-Schema sollte ein eigenes sein**, wie die Erweiterung `ezoracle` empfiehlt: Die Transaktionen der
  Website und die des Clusters mischen sich dann nie.
- **Noch kein `eZDFSFileHandlerTest` auf Oracle**: Der Datenbank-Testlauf des Kernels richtet dafür MySQL- und
  PostgreSQL-Datenbanken ein; das Oracle-Backend wird stattdessen über die Paritätsprüfungen und den Lauf von Anfang
  bis Ende oben getestet.

## Verwandte Seiten

- [Clustering mit PostgreSQL: das DFS-Backend im Kernel](postgresql-dfs-cluster.md)
- [Clustering mit MongoDB: das DFS-Backend im Kernel](mongodb-dfs-cluster.md)
- [Clustering mit SQLite: das Ein-Knoten-DFS-Backend im Kernel](sqlite-dfs-cluster.md)
- [Ein Ein-Knoten-DFS-Cluster mit einem Befehl](cluster-befehle.md)
- [Clustering: DFS und seine Metadaten-Datenbank](../../hilfe/datenbankadministratoren/clustering-dfs-und-die-datenbank.md)
- [Clustering und gemeinsamer Speicher](../../hilfe/hosting/betrieb/clustering-und-gemeinsamer-speicher.md)
- [Änderungsprotokoll 6.0.15](../../../changelogs/6.0/6.0.15.md)
