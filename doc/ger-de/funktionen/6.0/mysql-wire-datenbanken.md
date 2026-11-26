# Datenbanken, die das MySQL-Protokoll sprechen

Diese Seite richtet sich an Administratoren, die Exponential auf einer Datenbank betreiben wollen, die das
MySQL-Protokoll spricht, aber weder MySQL noch MariaDB ist, und an Entwickler, die an ihren Treibern arbeiten:
**TiDB**, **Vitess** und **PlanetScale**, **SingleStore**, **MariaDB Xpand** und **Amazon Aurora MySQL**. Ab
Exponential 6.0.15 hat der Kernel für jede von ihnen einen Treiber. Jeder ist der MySQL-Treiber `eZMySQLiDB` mit einem
Profil dessen, was seine Datenbank anders macht.

**Stand.** TiDB, Vitess und SingleStore installieren und betreiben eine ganze Site (gemessen, unten). Xpand und Aurora
MySQL haben ihre Treiber und Profile, geschrieben nach der Dokumentation der Hersteller; sie ließen sich hier nicht
betreiben (für Xpand gibt es kein öffentliches Container-Image, Aurora läuft nur bei AWS) und sind daher nicht
gemessen. Die Tabellen je Datenbank sagen, was geht und was nicht.

## Inhalt

1. [Die Treiber](#die-treiber)
2. [Was geht und was nicht](#was-geht-und-was-nicht)
3. [Einstellungen](#einstellungen)
4. [Was die Treiber anders machen](#was-die-treiber-anders-machen)
5. [Eine Site installieren](#eine-site-installieren)
6. [Datentabellen-Verbindungen](#datentabellen-verbindungen)
7. [Clustering](#clustering)
8. [Tests](#tests)

## Die Treiber

| Datenbank | `DatabaseImplementation` | Treiberklasse | Erreichte Stufe | Gemessen auf |
|---|---|---|---|---|
| TiDB | `tidb` | `expTiDBDB` | ganze Site-Datenbank | TiDB 8.5.1 (`pingcap/tidb:v8.5.1`) |
| Vitess, PlanetScale | `vitess`, `planetscale` | `expVitessDB` | ganze Site-Datenbank (ein ungesplitteter Keyspace) | Vitess 25 `vttestserver` (darunter MySQL 8.4) |
| SingleStore | `singlestore`, `memsql` | `expSingleStoreDB` | ganze Site-Datenbank | SingleStore 10.10 (`singlestoredb-dev`, ohne Lizenzschlüssel) |
| MariaDB Xpand | `xpand` | `expXpandDB` | Profil, nicht gemessen | keins: kein öffentliches Container-Image |
| Amazon Aurora MySQL | `aurora`, `auroramysql` | `expAuroraMySQLDB` | Profil, nicht gemessen | keins: nur als verwalteter Dienst |

„Ganze Site-Datenbank“ heißt dasselbe wie beim [SQL-Server-Treiber](sqlserver-datenbanktreiber.md): `exp:install`
installiert das Site-Paket mit seinen Inhalten, die Site läuft darauf, und Datentabellen-Verbindungen und Importe
erreichen sie.

Alle Treiber brauchen PHPs `mysqli`. `databaseName()` ist bei allen `mysql`: die MySQL-Anweisungen des Kernels, der
MySQL-Schema-Handler (`eZMysqlSchema`), das MySQL-DFS-Cluster-Backend und die portablen SQL-Helfer
(`sqlHelperDialect()` `mysql`) arbeiten unverändert, und der Treiber schickt jede Anweisung so, wie seine Datenbank sie
annimmt. Die Profile stehen in `expMySQLWireProfile` (`lib/ezdb/classes/expmysqlwireprofile.php`), einer Klasse reiner
Funktionen.

## Was geht und was nicht

### TiDB

TiDB 6.5 und später, selbst betrieben oder TiDB Cloud (mit `SSL=verify`). Gemessen auf TiDB 8.5.1.

| Geht | Geht nicht oder anders |
|---|---|
| eine ganze Site: `exp:install --db=tidb`, der Einrichtungsassistent über den Kickstarter (`Type=tidb`) | `BEGIN WORK` wird abgelehnt: der Treiber schickt `BEGIN` |
| Fremdschlüssel, durchgesetzt (TiDB 8.5) | `LOCK IN SHARE MODE` und `FOR SHARE` werden abgelehnt: der Treiber schickt `FOR UPDATE`, das mehr sperrt, nie weniger |
| Transaktionen, Savepoints | die Isolationsstufen `READ UNCOMMITTED` und `SERIALIZABLE` werden abgelehnt; `READ COMMITTED` und `REPEATABLE READ` (Snapshot) gehen |
| `AUTO_INCREMENT` in MySQLs Reihenfolge: neue Tabellen bekommen `AUTO_ID_CACHE=1` | ohne das sind IDs eindeutig, aber je TiDB-Server zwischengespeichert: über mehrere Server nicht in Einfügereihenfolge |
| `GET_LOCK()`; Tabellensperren werden benannte Sperren | `LOCK TABLES` tut nichts, außer der Server läuft mit `enable-table-lock` |
| Text ohne Unterschied der Groß- und Kleinschreibung: neue Tabellen bekommen `COLLATE=utf8mb4_general_ci` | TiDBs Standardsortierung ist `utf8mb4_bin` (unterscheidet Groß- und Kleinschreibung); Cluster, die mit `new_collations_enabled_on_first_bootstrap=false` angelegt wurden, behandeln jede Sortierung binär |
| `FULLTEXT` wird angenommen | es wird kein Volltextindex gebaut; die Konsole des Audit-Protokolls sucht mit `LIKE` |
| `ALTER TABLE` mit mehreren Änderungen, `MODIFY`, `CHANGE`, `RENAME TABLE` | `OPTIMIZE TABLE` wird abgelehnt: der Treiber schickt `ANALYZE TABLE` |
| Upsert, `GROUP_CONCAT`, Zeitstempel nach 2038, `utf8mb4` | `XA`-Transaktionen, `CHECKSUM TABLE`, `SQL_CALC_FOUND_ROWS` (der Kernel braucht keins davon für eine Site) |
| das MySQL-DFS-Cluster-Backend | den Primärschlüssel einer Tabelle mit Clustered Index entfernen |

### Vitess und PlanetScale

Vitess mit einem **ungesplitteten** Keyspace, und PlanetScales MySQL-Datenbanken (die Vitess sind). Gemessen auf einem
Vitess-25-`vttestserver` mit `--foreign_key_mode=disallow`, wie PlanetScale es standardmäßig betreibt.

| Geht | Geht nicht oder anders |
|---|---|
| eine ganze Site in einem ungesplitteten Keyspace: `exp:install --db=vitess` | der Keyspace entsteht nicht durch `CREATE DATABASE`: zuerst in Vitess (`vtctldclient`) oder in PlanetScales Konsole anlegen; `--db-action=remove` löscht seine Tabellen, nie den Keyspace |
| alles, was MySQL 8 kann: darunter läuft MySQL | Fremdschlüssel werden mit `foreign_key_mode=disallow` abgelehnt: der Treiber lässt sie aus `CREATE TABLE` und `ALTER TABLE` weg (das Kernel-Schema hat keine) |
| Transaktionen, Savepoints, jede Isolationsstufe, `LOCK TABLES`, `GET_LOCK()` | **eine Transaktion, die länger dauert als die Transaktionszeitgrenze des Tablets, wird abgebrochen** (vttablet 30 Sekunden, PlanetScale 20 Sekunden). Das Site-Paket installiert sich in einer langen Transaktion: für die Installation `--queryserver-config-transaction-timeout` erhöhen (hier 600s); bei PlanetScale woanders installieren und importieren |
| `AUTO_INCREMENT` wie MySQL in einem ungesplitteten Keyspace | ein **gesplitteter** Keyspace braucht je Tabelle einen Vindex und eine Sequenztabelle: für eine Site nicht unterstützt; eine Datentabellen-Verbindung kann eine so eingerichtete gesplittete Tabelle nutzen |
| `FULLTEXT`, Upsert, `GROUP_CONCAT`, Zeitstempel nach 2038 | `BEGIN WORK`, `XA` und `CHECKSUM TABLE` werden abgelehnt: für das erste schickt der Treiber `BEGIN` |
| TLS (`SSL=verify`), das PlanetScale verlangt | PlanetScales Deploy-Requests ändern das Schema von Produktions-Branches: auf einem Entwicklungs-Branch installieren |

### SingleStore

SingleStore 8.5 und später, selbst betrieben oder SingleStore Helios (mit `SSL=verify`). Gemessen auf SingleStore
10.10, dem kostenlosen Container `singlestoredb-dev`.

| Geht | Geht nicht oder anders |
|---|---|
| eine ganze Site: `exp:install --db=singlestore` | ein eindeutiger Schlüssel muss den Shard-Schlüssel enthalten: der Treiber legt jede Tabelle mit Primärschlüssel als **Referenztabelle** an (`CREATE ROWSTORE REFERENCE TABLE`), auf jeden Leaf kopiert, und eine ohne als Rowstore-Tabelle. Richtig für ein CMS; eine sehr große Tabelle würde man lieber von Hand splitten |
| `AUTO_INCREMENT` in Einfügereihenfolge (Referenztabellen, vom Master-Aggregator vergeben) | Fremdschlüssel werden abgelehnt: weggelassen |
| Transaktionen | die Isolation ist `READ COMMITTED`, was immer eingestellt ist; `SAVEPOINT` wird angenommen und **tut nichts** (ein Rollback dorthin rollt nichts zurück) |
| Upsert, `GROUP_CONCAT`, `utf8mb4` | `FULLTEXT` auf Rowstore-Tabellen wird abgelehnt: weggelassen, die Konsole des Audit-Protokolls sucht mit `LIKE` |
| Zeitstempel nach 2038: `sqlUnixTimestamp()` und `sqlFromUnixTimestamp()` rechnen mit `TIMESTAMPDIFF` und `TIMESTAMPADD` | SingleStores eigenes `UNIX_TIMESTAMP()` und `FROM_UNIXTIME()` enden 2038 |
| Text ohne Unterschied der Groß- und Kleinschreibung: neue Tabellen bekommen `COLLATE=utf8mb4_general_ci` | die Standardsortierung ist `utf8mb4_bin` |
| `LOCK TABLES` wird angenommen | `GET_LOCK()` gibt es nicht; `RENAME TABLE` (der Treiber schickt `ALTER TABLE ... RENAME TO`), `ALTER TABLE ... CHANGE` des Typs einer Spalte (`MODIFY` nehmen), `DELETE ... ORDER BY ... LIMIT`, `SHOW CREATE DATABASE` (der Treiber liest `information_schema`), `CHECKSUM TABLE`, `XA` |
| Präfix-Indizes werden angenommen | die Präfixlänge wird übergangen: die ganze Spalte wird indiziert |

### MariaDB Xpand

Nicht gemessen: MariaDB veröffentlicht kein Container-Image von Xpand, und es wird nur als Teil von SkySQL und
Enterprise-Abonnements verkauft. Das Profil folgt der Dokumentation von Xpand 6.

| Sollte gehen | Geht nicht oder anders (dokumentiert) |
|---|---|
| eine ganze Site: `exp:install --db=xpand` (nicht geprüft) | `FULLTEXT`-Indizes werden nicht unterstützt: weggelassen |
| Fremdschlüssel, Transaktionen, Savepoints, `READ COMMITTED`, `REPEATABLE READ`, `SERIALIZABLE` | `AUTO_INCREMENT`-Werte sind eindeutig, aber nicht in Einfügereihenfolge (je Knoten vergeben) |
| `GET_LOCK()`: Tabellensperren werden benannte Sperren | `LOCK TABLES` wird angenommen und übergangen; `XA` |

### Amazon Aurora MySQL

Nicht gemessen: Aurora läuft nur bei AWS, und es wurde nichts abonniert. Aurora MySQL 3 ist MySQL 8.0, der Treiber ist
also der MySQL-Treiber mit einer Ergänzung.

| Sollte gehen | Geht nicht oder anders (dokumentiert) |
|---|---|
| alles, was MySQL 8.0 kann: `exp:install --db=aurora` (nicht geprüft) | **nach einem Failover** kann der Cluster-Endpunkt noch einige Sekunden zum alten Writer führen, der jetzt ein Reader ist: jedes Schreiben scheitert mit 1290 oder 1836. Der Treiber prüft beim Verbinden `@@innodb_read_only` und verbindet sich neu (`FailoverRetries`, `FailoverWait`) |
| der Reader-Endpunkt als Lesereplikat (`UseSlaveServer`, `SlaveServerArray`) | die Prüfung des Replikat-Rückstands liest MySQLs Replikatstatus, den Auroras Reader nicht zeigen: `SlaveServerStatusCheck=disabled` setzen |
| TLS (`SSL=verify` mit Amazons CA-Bündel als `SSLCA`) | IAM-Authentifizierungstoken werden nicht unterstützt: einen Datenbankbenutzer mit Passwort nehmen |

## Einstellungen

`[DatabaseSettings]` wie für MySQL (`Server`, `Port`, `User`, `Password`, `Database`, `Socket`), mit der
`DatabaseImplementation` des Treibers, und `[MySQLWireSettings]` in `site.ini`:

```ini
[DatabaseSettings]
DatabaseImplementation=tidb
Server=tidb.example.com
Port=4000
User=exponential
Password=...
Database=site

[MySQLWireSettings]
# leer: das Profil des Treibers; tidb, vitess, singlestore, xpand, aurora, mysql oder mariadb
Profile=
# warning: jede Verbindung fragt den Server, was er ist, und warnt, wenn er nicht die Datenbank des Profils ist
VerifyServer=disabled
# READ COMMITTED, REPEATABLE READ ... wie die Datenbank es hält; leer: die Voreinstellung des Servers
IsolationLevel=
# auto (die des Profils), native (LOCK TABLES), named (GET_LOCK), none
TableLocks=auto
NamedLockTimeout=30
# TiDB: AUTO_ID_CACHE neuer Tabellen; 1 vergibt IDs in MySQLs Reihenfolge, 0 lässt TiDBs Cache
TiDBAutoIdCache=1
# disabled, enabled (TLS, das Zertifikat wird nicht geprüft) oder verify (gegen SSLCA geprüft, sonst die CAs des Systems)
SSL=disabled
SSLCA=
SSLCert=
SSLKey=
# Aurora: ein Primärserver, der nach dem Verbinden nur lesbar ist, wird neu verbunden
FailoverCheck=auto
FailoverRetries=5
FailoverWait=1
```

Der Installer schreibt `SSL` und `SSLCA` in `settings/override/site.ini.append.php` und in jeden Siteaccess, weil der
Cluster-Dateihandler sich verbindet, bevor ein Siteaccess erkannt ist.

## Was die Treiber anders machen

`expMySQLWireDB::query()` gibt jede Anweisung an `expMySQLWireProfile::rewriteStatement()`, das die zu sendenden
Anweisungen zurückgibt: meist eine, keine, wenn die Datenbank nichts damit anfangen kann, zwei für ein `RENAME TABLE`
von zwei Tabellen auf SingleStore. Datenanweisungen (`INSERT`, `UPDATE`, `DELETE`, ein `SELECT` ohne `SHARE`) werden
nach ihrem ersten Wort nicht weiter angesehen. Die Regeln, alle ohne Server getestet:

| Anweisung | TiDB | Vitess | SingleStore | Xpand | Aurora |
|---|---|---|---|---|---|
| `BEGIN WORK` | `BEGIN` | `BEGIN` | `BEGIN` | `BEGIN` | `BEGIN` |
| `CREATE TABLE` | `COLLATE=utf8mb4_general_ci`, `AUTO_ID_CACHE=1` | Fremdschlüssel weggelassen | `ROWSTORE REFERENCE` (mit Primärschlüssel), Fremdschlüssel und `FULLTEXT` weggelassen, `COLLATE=utf8mb4_general_ci` | `FULLTEXT` weggelassen | unverändert |
| `ALTER TABLE ... ADD FOREIGN KEY` | unverändert | nicht gesendet | nicht gesendet | unverändert | unverändert |
| `ALTER TABLE ... ADD FULLTEXT`, `CREATE FULLTEXT INDEX` | unverändert | unverändert | nicht gesendet | nicht gesendet | unverändert |
| `SELECT ... LOCK IN SHARE MODE` / `FOR SHARE` | `FOR UPDATE` | unverändert | `LOCK IN SHARE MODE` | unverändert | unverändert |
| `RENAME TABLE a TO b, c TO d` | unverändert | unverändert | je ein `ALTER TABLE ... RENAME TO` | unverändert | unverändert |
| `OPTIMIZE TABLE` | `ANALYZE TABLE` | unverändert | unverändert | unverändert | unverändert |

Eine Sortierung kommt nur hinzu, wenn die Anweisung keine nennt und ihr Zeichensatz `utf8mb4` oder `utf8` ist (oder
keiner: der der Datenbank). `CREATE TABLE ... LIKE` und temporäre Tabellen bleiben unverändert. Der Parser beachtet
Anführungszeichen und Klammern: ein Vorgabewert `'FOREIGN KEY, (x)'` bleibt, wie er ist.

Neben den Anweisungen:

- **Tabellensperren** (`lock()`, genutzt für die Sprachmaske, Bestellstatus, das Suchprotokoll, Bezeichner): `named`
  nimmt je Tabelle ein `GET_LOCK('exp:<datenbank>.<tabelle>')`, sortiert, damit zwei Sitzungen nicht aufeinander
  warten, und `unlock()` gibt sie in umgekehrter Reihenfolge frei.
- **Datenbanken**: `createDatabase()` legt eine TiDB-Datenbank mit `utf8mb4_general_ci` an. Auf Vitess legt es nichts
  an: es prüft, dass der Keyspace da ist, und `removeDatabase()` löscht die Tabellen und Views des Keyspaces. Die
  eigenen Datenbanken der Server (`cluster`, `memsql`, `METRICS_SCHEMA`, `_vt` ...) werden nicht aufgeführt.
- **Beziehungen**: Fremdschlüssel werden nur dort als Beziehungsart angeboten, wo sie erhalten bleiben (TiDB, Xpand,
  Aurora).
- **TLS**: `SSL=enabled` oder `verify` öffnet die Verbindung mit `mysqli_ssl_set()` und `MYSQLI_CLIENT_SSL`.

## Eine Site installieren

Zuerst die Datenbank anlegen, wie für MySQL (auf Vitess und PlanetScale den Keyspace oder die Datenbank mit deren
eigenen Werkzeugen; auf TiDB `CREATE DATABASE site CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci`). Dann, mit
`mysqli` im PHP der Kommandozeile:

```bash
EXP_INSTALL_DB_PASSWORD=geheim ./console exp:install --apply --db=tidb --db-host=tidb.example.com \
  --db-port=4000 --db-name=site --db-user=exponential
EXP_INSTALL_DB_PASSWORD=geheim ./console exp:install --apply --db=planetscale --db-host=aws.connect.psdb.cloud \
  --db-port=3306 --db-name=site --db-user=... --db-ssl=verify
EXP_INSTALL_DB_PASSWORD=geheim ./console exp:install --apply --db=singlestore --db-host=svc.singlestore.com \
  --db-port=3306 --db-name=site --db-user=admin --db-ssl=verify
```

`--db=` nimmt `tidb`, `vitess` (oder `planetscale`), `singlestore` (oder `memsql`), `xpand` und `aurora` (oder
`aurora-mysql`); `--db-ssl` nimmt `enabled` oder `verify`, `--db-ssl-ca` eine CA-Datei. Der Kickstarter nimmt dieselben
Namen als `[database_choice] Type` und `SSL` und `SSLCA` in `[database_init]`. Der Einrichtungsassistent im Browser
zeigt die Datenbankarten, deren PHP-Erweiterung er findet, und das ist für alle diese die von MySQL: sie über den
Kickstarter oder `exp:install` wählen.

Gemessen am 26.11.2026, das Site-Paket `sevenx_multisite` mit seinen Demo-Inhalten, PHP 8.5, jede Datenbank in einem
Container mit zwei bis vier Kernen, auf einem mit anderer Arbeit ausgelasteten Rechner (die Zeiten sind schlecht
vergleichbar). Nach jeder Installation antworteten die Startseite und die Anmeldeseite der Verwaltung über PHPs
eingebauten Server mit 200, `exp:cluster:enable --apply` legte einen DFS-Cluster aus einem Knoten an (312 Dateien, die
Metadaten in derselben Datenbank), und die Startseite antwortete wieder mit 200:

| Datenbank | Ergebnis | Schritte | Fehler | Prüfungen | Paketobjekte | Zeit |
|---|---|---|---|---|---|---|
| TiDB 8.5.1 | installiert | 16 | 0 | 9 bestanden | 291 | 764 s |
| Vitess 25 (`vttestserver`, Transaktionszeitgrenze 600 s) | installiert | 16 | 0 | 9 bestanden | 291 | 526 s |
| SingleStore 10.10 | installiert | 16 | 0 | 9 bestanden | 291 | 279 s |

Mit der Voreinstellung von vttablet (30 Sekunden Transaktionszeitgrenze) blieb die Vitess-Installation im
Demo-Inhaltspaket stehen („transaction ... in use: for tx killer rollback“). Die Migrationen nach einer Installation
nehmen auf diesen dreien keine genaue Datenbankmarke (sie lehnen `CHECKSUM TABLE` ab) und laufen ohne weiter, wie auf
einer Datenbank ohne Sicherungspunkt.

## Datentabellen-Verbindungen

Eine [Datentabellen-Verbindung](datentabellen.md) nennt eine dieser Datenbanken als ihre Engine (`tidb`, `vitess`,
`planetscale`, `singlestore`, `xpand`, `aurora`) oder die Engine `mysql` mit der Option `profile`. Sie wird vom Treiber
der Datenbank geöffnet, es gelten also dieselben Regeln; die Optionen `ssl` und `ssl_ca` stellen TLS ein:

```ini
[Connection_analytics]
Engine=singlestore
Server=svc.singlestore.com
Port=3306
Database=analytics
User=reader
Secret=env:ANALYTICS_PASSWORD
Options[ssl]=verify
```

Ohne `mysqli` fällt die Verbindung auf PDO MySQL zurück, als einfache MySQL-Verbindung ohne das Profil.

## Clustering

`exp:cluster:enable` nimmt für alle diese das MySQL-DFS-Backend (`eZDFSFileHandlerMySQLiBackend`): sein Schema läuft
über den Treiber (`kernel/sql/mysql/cluster_dfs_schema.sql`, umgeschrieben wie oben), und die eigenen Anweisungen des
Backends (`BEGIN`, `SELECT ... FOR UPDATE`) nimmt jede von ihnen an. Gemessen auf TiDB, Vitess und SingleStore (oben).
Das Backend öffnet eine eigene `mysqli`-Verbindung, ohne TLS: auf PlanetScale, TiDB Cloud und SingleStore Helios, die
TLS verlangen, die DFS-Metadaten in einer anderen Datenbank halten.

## Tests

`tests/tests/lib/ezdb/mysqlwire/` (ohne Datenbank, Testsuite `mysqlwire`) und `.../mysqlwire/live/` (Testsuite
`mysqlwire-live`; jede Datenbank wird übersprungen ohne `EXP_TEST_<TIDB|VITESS|SINGLESTORE|XPAND|AURORA>_HOST` und
`_PORT`, `_USER`, `_PASSWORD` oder `_PASSWORD_FILE`, `_DATABASE`). Der GitHub-Job `mysqlwire` führt die Live-Tests gegen
TiDB 8.5.1, Vitess (`vttestserver`) und SingleStore (`singlestoredb-dev`) in Containern aus; Xpand und Aurora werden
übersprungen.

| Datei | Was |
|---|---|
| `expMySQLWireProfileTest` | die Fakten jedes Profils, das Erkennen eines Servers, jede Umschreiberegel, das Kernel- und das Cluster-Schema für jede Datenbank umgeschrieben |
| `expMySQLWireDBTest` | der Treiber über einer aufzeichnenden Verbindung: Einstellungen, Anweisungen in Reihenfolge, `BEGIN`, benannte und native Tabellensperren, Beziehungen, Zeitstempel, Datenbanken und Keyspaces, der Zeichensatz, Isolationsstufen, Auroras Failover-Prüfung, der Installer, der Einrichtungsassistent, das Cluster-Setup und Datentabellen-Verbindungen |
| `live/expMySQLWireLiveTest` | gegen jeden Server: er wird erkannt, MySQLs DDL mit Fremdschlüsseln, `FULLTEXT` und Präfix-Indizes, Text ohne Unterschied der Groß- und Kleinschreibung und Emoji, IDs nach einer ausdrücklichen ID, Transaktionen, Lese- und Tabellensperren, Upsert, `GROUP_CONCAT`, Zeitstempel nach 2038, Schemaänderungen und das zurückgelesene Schema |

Die Funktionsprobe hinter den Tabellen oben schickte dieselben Anweisungen an jeden Container, außerhalb der Testsuite.
