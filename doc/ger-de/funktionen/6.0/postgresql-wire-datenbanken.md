# Datenbanken mit dem PostgreSQL-Protokoll

Diese Seite richtet sich an Administratoren, die Exponential auf einer Datenbank betreiben wollen, die das Protokoll
von PostgreSQL spricht, ohne PostgreSQL zu sein, oder auf PostgreSQL bei einem Cloud-Dienst mit eigenen Regeln, und an
alle, die am Treiber mitarbeiten. Exponential 6.0.15 hat für sie alle einen Treiber, `expPostgreSQLWireDB` in
`lib/ezdb/classes/`, eine Unterklasse des PostgreSQL-Treibers `eZPostgreSQLDB`, mit einem Profil je Datenbank in
`expPostgreSQLWireProfile`:

| Datenbank | `DatabaseImplementation` | Treiberklasse | Erreichte Stufe | Live getestet |
|---|---|---|---|---|
| CockroachDB 23.2 und neuer | `cockroachdb` (`cockroach`, `crdb`) | `expCockroachDB` | vollständige Site-Datenbank | ja: CockroachDB 25.2 in einem Container |
| YugabyteDB 2.20 und neuer (YSQL) | `yugabytedb` (`yugabyte`, `ysql`) | `expYugabyteDB` | vollständige Site-Datenbank | ja: YugabyteDB 2.25 in einem Container |
| Google Cloud Spanner, PostgreSQL-Dialekt | `spanner` (`spannerpg`, `pgadapter`) | `expSpannerPGDB` | Datentabellen-Verbindung und Import | ja: der Spanner-Emulator hinter PGAdapter |
| Neon | `neon` | `expNeonDB` | vollständige Site-Datenbank (PostgreSQL) | **nein**: Verbindungsprofil, Unit-Tests und diese Seite |
| Supabase | `supabase` | `expSupabaseDB` | vollständige Site-Datenbank (PostgreSQL) | **nein**: Verbindungsprofil, Unit-Tests und diese Seite |
| Amazon Aurora PostgreSQL | `aurorapostgresql` (`aurora-postgresql`; `aurora` ist Aurora MySQL) | `expAuroraPostgreSQLDB` | vollständige Site-Datenbank (PostgreSQL) | **nein**: Verbindungsprofil, Unit-Tests und diese Seite |

"Vollständige Site-Datenbank" heißt dasselbe wie bei PostgreSQL selbst: Der Installer legt darin das Kernel-Schema und
die Daten eines Site-Pakets an, und die Site läuft daraus. Neon, Supabase und Aurora sind PostgreSQL-Server; eine Site
läuft dort wie auf PostgreSQL. Was ihre Profile hinzufügen (TLS, Pooler, Kaltstarts, Failover), ist nach der
Dokumentation der Dienste geschrieben und nur mit Unit-Tests geprüft. Für diese Arbeit wurde bei keinem der Dienste ein
Konto angelegt.

## Inhalt

1. [Warum ein Profil und kein neuer Treiber](#warum-ein-profil-und-kein-neuer-treiber)
2. [Einstellungen](#einstellungen)
3. [Was der Treiber anders macht](#was-der-treiber-anders-macht)
4. [CockroachDB](#cockroachdb)
5. [YugabyteDB](#yugabytedb)
6. [Google Cloud Spanner (PostgreSQL-Dialekt)](#google-cloud-spanner-postgresql-dialekt)
7. [Neon](#neon)
8. [Supabase](#supabase)
9. [Amazon Aurora PostgreSQL](#amazon-aurora-postgresql)
10. [Eine Site installieren](#eine-site-installieren)
11. [Cluster](#cluster)
12. [Datentabellen-Verbindungen](#datentabellen-verbindungen)
13. [Tests](#tests)

## Warum ein Profil und kein neuer Treiber

Alle sechs Datenbanken verstehen das SQL von PostgreSQL, und PHPs Erweiterung `pgsql` spricht mit ihnen. Was sich
unterscheidet, ist eine Liste von Einzelheiten: ob es `LOCK TABLE` gibt, ob eine Transaktion wiederholt werden muss, wie
eine Sequenz aussieht, ob ein Primärschlüssel nach `CREATE TABLE` hinzugefügt werden darf, ob ein Pooler davorsteht. Der
Treiber behält `databaseName()` als `postgresql`, damit Kernel, Schema-Handler (`eZPgsqlSchema`), SQL-Helfer und
Installer jede von ihnen als PostgreSQL behandeln, und das Profil ändert die Einzelheiten, wo die Datenbank es braucht.
`databaseVariant()` nennt die Datenbank, `wireProfile()` liefert ihr Profil mit den wirksamen Einstellungen.

## Einstellungen

```ini
[DatabaseSettings]
DatabaseImplementation=cockroachdb
Server=crdb1.example.com
Port=26257
User=exponential
Password=...
Database=exponential

[PostgreSQLWireSettings]
SSLMode=verify-full
SSLRootCert=/etc/ssl/crdb/ca.crt
```

`[PostgreSQLWireSettings]` der `site.ini` (ein leerer Wert behält den Standard des Profils):

| Einstellung | Bedeutung |
|---|---|
| `SSLMode`, `SSLRootCert` | `sslmode` und CA-Datei von libpq. Neon, Supabase und Aurora verlangen standardmäßig `require` |
| `ConnectTimeout` | Sekunden je Verbindungsversuch (libpq `connect_timeout`) |
| `ConnectAttempts`, `ConnectWait` | Verbindungsversuche und die Wartezeit vor dem zweiten (ms, jedes Mal verdoppelt): ein Neon-Compute, der aufwacht, ein Aurora-Writer im Failover |
| `SerializationRetries`, `RetryWait` | wie oft eine einzelne Anweisung außerhalb einer Transaktion nach `40001` (Serialisierungsfehler) oder `40P01` (Deadlock) erneut gesendet wird, und die erste Wartezeit (ms) |
| `ReconnectOnLoss` | `enabled`: Eine Anweisung außerhalb einer Transaktion, deren Verbindung verloren ging, läuft auf einer neuen Verbindung erneut |
| `Pooler` | `auto` (aus Host und Port), `none`, `session` oder `transaction` |
| `Isolation` | `default` (die des Profils), `read committed`, `repeatable read`, `serializable` |
| `ApplicationName` | was `pg_stat_activity` anzeigt |
| `Endpoint` | Neon: die Endpoint-ID, für ein libpq ohne SNI (`options=endpoint=<id>`) |
| `Options` | weitere Startoptionen von libpq (`-c name=wert`); hinter einem Transaktions-Pooler nicht gesendet |
| `SessionStatements[]` | weitere Anweisungen nach jedem Verbindungsaufbau |

Der Installer schreibt die ihm genannten TLS- und Pooler-Einstellungen (`--db-sslmode`, `--db-sslrootcert`,
`--db-pooler`, `--db-endpoint`) in jeden Siteaccess und in `settings/override`, wie er es für die Verschlüsselung von SQL
Server tut.

## Was der Treiber anders macht

- **Verbindung.** Die Verbindungszeichenkette trägt `sslmode`, `sslrootcert`, `connect_timeout` und
  `application_name` aus Profil und Einstellungen, jeder Wert für libpq maskiert. Ein fehlgeschlagener
  Verbindungsaufbau wird `ConnectAttempts`-mal mit wachsender Wartezeit versucht. Danach laufen die Sitzungsanweisungen
  des Profils (CockroachDB: `READ COMMITTED` und temporäre Tabellen); eine Anweisung, die der Server ablehnt, wird
  protokolliert, und die Verbindung wird ohne sie genutzt.
- **Pooler.** Hinter einem Pooler im Transaktionsmodus (PgBouncer, Supavisor auf Port 6543, Neons `-pooler`-Host) kann
  die nächste Anweisung auf einer anderen Server-Verbindung laufen. Deshalb wird keine Sitzungsanweisung gesendet,
  Startoptionen entfallen (außer Neons Endpoint), und ein `INSERT` in eine Tabelle mit Serial-Spalte bekommt
  `RETURNING <spalte>`: `lastSerialID()` antwortet daraus, statt `currval()` zu fragen, das eine andere Sitzung fragen
  könnte.
- **Wiederholungen.** Eine einzelne Anweisung außerhalb einer Transaktion, die mit `40001` oder `40P01` scheitert, wird
  bis zu `SerializationRetries`-mal erneut gesendet; eine, deren Verbindung verloren ging (`57P01` "terminating
  connection due to administrator command", `08006`, "server closed the connection unexpectedly"), auf einer neuen
  Verbindung. Innerhalb einer Transaktion wird nichts wiederholt: Die Transaktion ist als Ganzes verloren, der Kernel
  rollt sie zurück, und nur der Code, der sie angelegt hat, kann sie erneut ausführen. `retryTransaction( $work )` tut
  das für eine Closure.
- **Sperren.** `lock()` (MySQLs `LOCK TABLES`) führt `LOCK TABLE` aus, wo es das gibt. CockroachDB kennt kein
  `LOCK TABLE`, und YugabyteDB lehnt dessen Standardmodus (`ACCESS EXCLUSIVE`) ab; dort hält der Treiber eine Zeile je
  Tabelle in `exp_wire_lock` und sperrt sie mit `SELECT ... FOR UPDATE`: Ein zweites `lock()` derselben Tabelle wartet
  bis zum ersten `unlock()`. Die Zeilen entstehen vor der Transaktion, damit ein zweiter Sperrender die Zeile immer
  findet, auf die er warten muss. Advisory Locks werden nie verwendet: CockroachDBs `pg_try_advisory_lock()` antwortet
  mit true, ohne etwas zu sperren.
- **Neue IDs.** `lastSerialID()` liest `currval()` wie bei PostgreSQL oder den Wert von `RETURNING` (oben).
- **`md5()`** ist das eingebaute `md5()` von PostgreSQL. `eZPostgreSQLDB` nutzt `digest()` aus pgcrypto, das
  YugabyteDB, Neon und Aurora standardmäßig nicht haben; die `digest()`-Prüfung des Installers entfällt für diese
  Treiber.
- **`BEGIN`, `COMMIT`, `ROLLBACK`** ohne das optionale `WORK` von PostgreSQL, das CockroachDB nicht annimmt.
- **Schema.** `eZPgsqlSchema` fragt das Profil: Der Primärschlüssel steht bei CockroachDB, YugabyteDB und Spanner in
  `CREATE TABLE` (nachträglich ersetzt er CockroachDBs verborgenen Schlüssel `rowid`, schreibt eine YugabyteDB-Tabelle
  neu, und Spanner nimmt ihn gar nicht an); kein `USING btree` bei YugabyteDB (dessen Indexmethode ist `lsm`) und
  Spanner; `int4` für `INTEGER` bei CockroachDB (dessen `INTEGER` 64 Bit hat); bit-umgekehrte Sequenzen, `bigint`-IDs
  und kein `setval()` bei Spanner. Die Serial-Vorgaben, die CockroachDB und Spanner zurückmelden, werden als
  `auto_increment` gelesen.
- **Versionen.** `databaseServerVersion()` liest CockroachDBs `CockroachDB CCL v25.2.4` und YugabyteDBs
  `15.12-YB-2.25.2.0` richtig.
- **Replikate.** Auroras Reader haben keinen WAL-Empfänger (sie replizieren im Speicher): Die Replikatprüfung fragt dort
  nur `pg_is_in_recovery()`.

## CockroachDB

**Gemessen** (CockroachDB 25.2.4, ein Knoten in einem Container, 2 CPUs; PHP 8.5): `exp:install --apply
--db=cockroachdb` installierte `sevenx_multisite` in 16 Schritten, alle 9 Prüfungen bestanden, in 47 Minuten (vor
allem DDL und Katalogabfragen). Die Site enthielt danach die 291 Objekte des Pakets (und die Hilfe-Handbücher, die danach im Hintergrund entstehen:
eine halbe Stunde später 2100 Objekte insgesamt); geprüft
wurden das Veröffentlichen eines Artikels, sein URL-Alias, die Suche, das Verschieben in den Papierkorb und der
Transaktionszähler. Die Fehler dieses Laufs stammten von Erweiterungen, die der Testbaum nicht hatte (xrowmetadata,
eztags, explayouts ...), keiner von der Datenbank.

| Geht | Geht nicht oder anders |
|---|---|
| Eine vollständige Site: Kernel-Schema, die Daten eines Site-Pakets, Inhalte, Suche, Administration | `LOCK TABLE`: ersetzt durch Zeilensperren in `exp_wire_lock` |
| Sequenzen und `nextval()`-Vorgaben wie bei PostgreSQL; `currval()`, `setval()` | `pg_advisory_lock()` gibt es nicht; `pg_try_advisory_lock()` sperrt nichts |
| `ON CONFLICT`, `STRING_AGG`, `FOR UPDATE`, `EXTRACT(EPOCH ...)`, `md5()` | Temporäre Tabellen nur mit `experimental_enable_temp_tables` (der Treiber setzt es) |
| `READ COMMITTED` (ab 23.2, Standard des Treibers); `SERIALIZABLE` mit `Isolation=serializable` | `SERIALIZABLE` (Standard des Servers) startet Transaktionen neu (`40001`): einzelne Anweisungen werden wiederholt, Transaktionen brauchen `retryTransaction()` |
| Zeilensperren, `SELECT ... FOR UPDATE`, Transaktionen, der Query-Cache | DDL ist langsam (eine Schemaänderung je Anweisung, etwa eine Sekunde): Das Kernel-Schema anzulegen dauert Minuten |
| Mehrere Knoten hinter einer Adresse (jeder Knoten nimmt jede Anweisung) | DDL in einer Transaktion schreibt diese vorher fest (`autocommit_before_ddl`) |
| | `INTEGER` ist `INT8`: Das Schema schreibt `int4`; `DROP DATABASE` braucht `CASCADE` (der Treiber ergänzt es) |

## YugabyteDB

**Gemessen** (YugabyteDB 2.25.2, `yugabyted` in einem Container, 2 CPUs; PHP 8.5): `exp:install --apply
--db=yugabytedb` installierte `sevenx_multisite` in 16 Schritten, alle 9 Prüfungen bestanden, in 42 Minuten; 310
Inhaltsobjekte, 311 Knoten; dieselben Prüfungen von Artikel, URL-Alias, Suche und Papierkorb bestanden. Die Fehler des
Laufs stammten wie bei CockroachDB von Erweiterungen, die der Testbaum nicht hatte. Wichtig: Die Sicherungskopie eines
Upgrades ("Sicherungspunkt") entsteht mit `pg_dump` des Servers; ein Dump des Clients von PostgreSQL 17 setzt
`transaction_timeout`, das YugabyteDB 2.25 (PostgreSQL 15) nicht kennt, und ließ sich nicht zurückspielen. Nutzen Sie
für Backups YugabyteDBs eigenes `ysql_dump`.

| Geht | Geht nicht oder anders |
|---|---|
| Eine vollständige Site: Kernel-Schema, Daten, Inhalte, Administration | `LOCK TABLE ... ACCESS EXCLUSIVE`: ersetzt durch Zeilensperren in `exp_wire_lock` |
| SQL von PostgreSQL 15 (2.25), Sequenzen, `currval()`, `setval()`, temporäre Tabellen | Advisory Locks nur mit dem Preview-Flag `ysql_yb_enable_advisory_locks` |
| `ON CONFLICT`, `STRING_AGG`, `FOR UPDATE`, `md5()` | Standardmäßig kein pgcrypto: kein `digest()` (der Treiber nutzt `md5()`) |
| Transaktionen, Zeilensperren | `READ COMMITTED` braucht das Flag `yb_enable_read_committed_isolation`; ohne läuft es als `REPEATABLE READ`, und Konflikte enden in `40001` |
| | Sequenzen vergeben Nummern blockweise je Knoten (mindestens `CACHE 100`): IDs sind eindeutig, aber nicht in Einfügereihenfolge |
| | Ein Primärschlüssel nach `CREATE TABLE` schreibt die Tabelle neu: Das Schema schreibt ihn direkt hinein; `USING btree` wird zu `lsm` |
| | DDL ist langsam (etwa eine Sekunde je Anweisung) |

## Google Cloud Spanner (PostgreSQL-Dialekt)

Spanner spricht das Protokoll von PostgreSQL über **PGAdapter**, einen Proxy von Google (ein Java-Programm oder ein
Container), mit dem sich die Anwendung verbindet, als wäre er PostgreSQL. Getestet wurde der Treiber gegen den
Spanner-Emulator hinter PGAdapter (`gcr.io/cloud-spanner-pg-adapter/pgadapter-emulator`).

**Stufe: Datentabellen-Verbindung und Import.** Eine Site kann nicht auf Spanner laufen: Die IDs des Kernels sind
32-Bit-Zahlen, die er oft selbst wählt, Spanner kennt nur bit-umgekehrte Sequenzen (64-Bit-Zahlen ohne Reihenfolge,
kein `currval()`), und Suche und Baum-Code des Kernels nutzen temporäre Tabellen und Tabellensperren, die Spanner nicht
hat.

| Geht | Geht nicht |
|---|---|
| Eine Datentabellen-Verbindung (`datatable.ini`, Engine `spanner`): Tabellen vom Schema-Handler angelegt, Zeilen von persistenten Klassen gelesen und geschrieben | Eine Site-Datenbank (siehe oben) |
| Tabellen mit Primärschlüssel in `CREATE TABLE` (ohne Constraint-Namen), Indizes, eindeutige Indizes | `CREATE SEQUENCE` mit `START`, `CACHE` ...: nur `BIT_REVERSED_POSITIVE` |
| Neue IDs aus einer bit-umgekehrten Sequenz, gelesen mit `RETURNING` | `currval()`, `lastval()`, `setval()` |
| `ON CONFLICT` (Upsert), `STRING_AGG`, `LIMIT`/`OFFSET`, `UNION`, `GROUP BY ... HAVING`, Transaktionen | `LIMIT ALL` (der Treiber schreibt die größte Zahl), `FOR UPDATE` außerhalb einer Lese-Schreib-Transaktion (der Treiber lässt es weg) |
| Zeilen über die Verbindung in die Site importieren (eine Datentabellen-Synchronisation `table_to_content` liest sie mit persistenten Klassen; die Klassen sind auf Spanner getestet, die Synchronisation selbst lief dort nicht) | Temporäre Tabellen, `LOCK TABLE`, Advisory Locks, `md5()` |
| Das Schema zurücklesen (aus `information_schema`; PGAdapter bildet `pg_class` nur nach) | `smallint`, `real`, `timestamp without time zone` (das Schema schreibt `bigint`, `double precision`, `timestamptz`) |
| | DDL in einer Transaktion. (`DROP TABLE` einer Tabelle mit Indizes geht: Der Treiber setzt PGAdapters `spanner.support_drop_cascade`) |

## Neon

Neon ist PostgreSQL (16 und 17) mit getrenntem Speicher und Compute; ein Compute, der untätig war, fährt auf null
herunter.

| Geht (laut Dokumentation; nicht live getestet) | Geht nicht oder anders |
|---|---|
| Eine vollständige Site wie auf PostgreSQL | Ein angehaltener Compute startet beim ersten Verbindungsaufbau: Das Profil wartet und versucht es erneut (5 Versuche, 0,5 s verdoppelt, je 15 s) |
| TLS (standardmäßig `sslmode=require`) | Der `-pooler`-Host ist PgBouncer im Transaktionsmodus: keine Sitzungsanweisungen, `RETURNING` für neue IDs, keine temporären Tabellen über Anweisungen hinweg |
| Lesereplikate (`UseSlaveServer` mit einem Read-Replica-Compute) | Ein libpq ohne SNI braucht die Endpoint-ID: `Endpoint=<id>` |
| | Kein pgcrypto ohne `CREATE EXTENSION pgcrypto` (dieser Treiber braucht es nicht) |

## Supabase

Supabase gibt jedem Projekt eine PostgreSQL-Datenbank (15 und 17).

| Geht (laut Dokumentation; nicht live getestet) | Geht nicht oder anders |
|---|---|
| Eine vollständige Site wie auf PostgreSQL, über den direkten Host `db.<projekt>.supabase.co` Port 5432 | Der direkte Host antwortet nur über IPv6, es sei denn, das IPv4-Add-on ist gebucht |
| Supavisor im Sitzungsmodus (`<region>.pooler.supabase.com`, Port 5432): wie eine direkte Verbindung | Supavisor im Transaktionsmodus (Port 6543): keine Sitzungsanweisungen, `RETURNING` für neue IDs, keine temporären Tabellen über Anweisungen hinweg |
| TLS (standardmäßig `sslmode=require`) | Der Pooler erwartet den Benutzer als `postgres.<projekt-ref>` |

## Amazon Aurora PostgreSQL

| Geht (laut Dokumentation; nicht live getestet) | Geht nicht oder anders |
|---|---|
| Eine vollständige Site wie auf PostgreSQL, über den Cluster-Endpunkt (Writer) | Ein Failover trennt alle Verbindungen: Außerhalb einer Transaktion verbindet sich der Treiber neu und wiederholt die Anweisung; eine laufende Transaktion ist verloren |
| Reader über den Reader-Endpunkt (`UseSlaveServer`, `SlaveServerArray`): Die Replikatprüfung fragt `pg_is_in_recovery()` | Reader haben keinen WAL-Empfänger: Die Replikatprüfung von PostgreSQL würde sie nie lesen |
| TLS (`sslmode=require`; `verify-full` mit dem CA-Bündel von RDS in `SSLRootCert`) | IAM-Anmeldung: Das Token ist das Passwort und läuft nach 15 Minuten ab; ein lange laufender Prozess braucht ein neues |
| RDS Proxy (Session Pinning, erkannt am Host mit `.proxy-`) | |

## Eine Site installieren

`exp:install` (bin/php/install.php), der Kickstarter und der Setup-Assistent kennen CockroachDB, YugabyteDB, Neon,
Supabase und Aurora. Die Datenbank muss existieren, außer bei CockroachDB und YugabyteDB, wo der Benutzer sie anlegen
darf.

```bash
./console exp:install --apply --db=cockroachdb --db-host=crdb1 --db-user=root
./console exp:install --apply --db=yugabytedb --db-host=yb1 --db-user=yugabyte
./console exp:install --apply --db=neon --db-host=ep-cool-1.eu-central-1.aws.neon.tech --db-user=app --db-name=neondb
./console exp:install --apply --db=supabase --db-host=db.abcd.supabase.co --db-name=postgres
./console exp:install --apply --db=aurorapostgresql --db-host=site.cluster-x.eu-west-1.rds.amazonaws.com \
    --db-sslmode=verify-full --db-sslrootcert=/etc/ssl/rds-global-bundle.pem
```

`--db-sslmode`, `--db-sslrootcert`, `--db-pooler` und `--db-endpoint` setzen `[PostgreSQLWireSettings]`. Der Kickstarter
nimmt `Type=cockroachdb` (auch `cockroach`, `crdb`), `yugabytedb` (`yugabyte`, `ysql`), `neon`, `supabase` oder
`aurorapostgresql`, mit `SSLMode`, `SSLRootCert`, `Pooler` und `Endpoint` in `[database_init]`. Der Setup-Assistent listet sie nach
PostgreSQL, wenn PHP die Erweiterung `pgsql` hat. Installieren Sie über eine direkte Verbindung oder einen
Sitzungs-Pooler: Das Schema entsteht Anweisung für Anweisung, ein Transaktions-Pooler bringt dabei nichts.

## Cluster

`exp:cluster:enable` gibt CockroachDB, YugabyteDB, Neon, Supabase und Aurora PostgreSQL das PostgreSQL-DFS-Backend
(`eZDFSFileHandlerPostgresqlBackend` / `expDFSFileHandlerPostgresqlBackend`, Gateway `dfspostgresql`), mit seinen
Tabellen in der eigenen Datenbank der Site. Ohne Einstellung `Port` nimmt der Cluster den Port der Datenbank
(CockroachDB 26257, YugabyteDB 5433). Auf CockroachDB wird das Cluster-Schema ohne `text_pattern_ops` angelegt:
CockroachDB erlaubt Operatorklassen nur auf invertierten und Vektor-Indizes, und seine Textindizes vergleichen Bytes,
also bedient der einfache Index die Präfixsuchen. Spanner hält keine Site und bekommt keinen Cluster.

| Datenbank | Stand | Gemessene Unterschiede |
|---|---|---|
| CockroachDB v25.2.4 | alle 26 DFS-Prüfungen bestanden (`expCockroachDBDFSClusterLiveTest`) | `FOR UPDATE SKIP LOCKED` kann eine Zeile überspringen, deren Write Intent noch nicht aufgelöst ist; ein Cache-Purge direkt nach einem Schreiben findet diese Zeile dann erst beim nächsten Lauf |
| YugabyteDB 2.25.2 | bestanden (`expYugabyteDBDFSClusterLiveTest`) | `lock_timeout` wird nicht angewandt: Ein Speichern wartet, solange eine andere Transaktion die Zeile hält, statt nach `DBLockTimeout` aufzugeben, und läuft weiter, sobald die Sperre frei ist. Bei einem Wettlauf kann eine Cache-Datei zweimal nacheinander erzeugt werden (ein zweiter Prozess erhält „ok“, sobald die erste Erzeugung beendet ist); nie schreiben zwei Prozesse gleichzeitig über eine `.generating`-Zeile |
| Neon, Supabase, Aurora PostgreSQL | **ungetestet** | ebenso zugeordnet (sie sind PostgreSQL selbst), nicht gegen den Dienst ausgeführt |

Die Live-Prüfungen sind die Paritätsprüfungen, die jedes DFS-Backend besteht: Speichern, Abrufen, eine Datei hin und
zurück über die DFS-Freigabe, Umbenennen, Löschen, Purge, Cache-Erzeugung und der Cluster-Plan. Sie liegen in der
Testsuite `pgwire-live` (`tests/tests/lib/ezdb/pgwire/live/`).

## Datentabellen-Verbindungen

`datatable.ini [ConnectionSettings] Engines[]` enthält `cockroachdb`, `yugabytedb`, `spanner`, `neon`, `supabase` und
`aurorapostgresql`. Eine Verbindung öffnet sich über den Treiber (PHPs `pgsql`); ihre Optionen `sslmode` und `pooler` werden zu den
`[PostgreSQLWireSettings]` der Verbindung. Neon, Supabase und Aurora sind in der Liste der Engines als "hier nicht
getestet" markiert. Die SQL-Quelle von SQLI Import liest jede von ihnen über eine benannte Verbindung
(PostgreSQL-Quoting, `crdb_internal` wird abgelehnt), live getestet auf CockroachDB und
YugabyteDB.

## Tests

- `tests/tests/lib/ezdb/pgwire/` (Testsuite `pgwire`, ohne Server): die Profile (`expPostgreSQLWireProfileTest`), das
  DDL jeder Datenbank (`expPostgreSQLWireSchemaTest`), Wiederholungen, Neuverbindungen, IDs und Helfer des Treibers mit
  ersetztem Server (`expPostgreSQLWireDBTest`).
- `tests/tests/lib/ezdb/pgwire/live/` (Testsuite `pgwire-live`): dieselben Prüfungen auf jeder Datenbank mit Server
  (Schema und Daten über den Schema-Handler, neue IDs, Helfer, Transaktionen, `lock()` gegen eine zweite Verbindung,
  temporäre Tabellen) und je Datenbank, was sie ablehnt. Server: `EXP_TEST_PGWIRE_COCKROACHDB_HOST`, `_PORT`, `_USER`,
  `_PASSWORD`, `_DATABASE`, dasselbe für `YUGABYTEDB` und `SPANNER`; ohne sie werden die Tests übersprungen.
- CI: Der Job "PostgreSQL wire protocol" führt die Live-Suite gegen CockroachDB, YugabyteDB und den Spanner-Emulator mit
  PGAdapter in Containern aus.

Siehe auch: [Datenbankunterstützung](../../hilfe/datenbankunterstuetzung.md),
[PostgreSQL 18 und 19](postgresql-18-und-19.md), [Microsoft-SQL-Server-Treiber](sqlserver-datenbanktreiber.md).
