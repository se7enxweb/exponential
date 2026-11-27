# Apache-Cassandra- und ScyllaDB-Treiber

Diese Seite ist für Administratoren, deren Organisation Daten in Apache Cassandra oder ScyllaDB hält und diese Daten in
Exponential haben will, und für Entwickler, die am Treiber arbeiten. Ab Exponential 6.0.15 hat der Kernel einen Treiber
für die Wide-Column-Datenbanken, die CQL sprechen: `expCassandraDB` (Cassandra) und `expScyllaDB` (ScyllaDB) in
`lib/ezdb/classes/`, mit dem Schema-Handler `expCassandraSchema` und dem Importbefehl `exp:cassandra:import`.

**Wofür er da ist.** Der Treiber erreicht zwei der drei Stufen, die eine Datenbank in Exponential erreichen kann:

| Stufe | Cassandra und ScyllaDB |
|---|---|
| (a) Die Datenbank der Site | **Nein.** Eine Site kann nicht auf Cassandra oder ScyllaDB laufen; [warum](#warum-keine-site-auf-cassandra-läuft), mit den Messungen |
| (b) Eine Datentabellen-Verbindung | **Ja.** Persistente Klassen, Schema, Writer und Describer der Datentabellen-Schicht, der Verbindungstest, auf einem Keyspace |
| (c) Eine Importquelle | **Ja.** `exp:cassandra:import` kopiert die Tabellen eines Keyspace in die Datenbank der Site oder in jede Datentabellen-Verbindung; eine Synchronisation `table_to_content` macht Inhalte aus den Zeilen einer Tabelle |

## Inhalt

1. [Voraussetzungen](#voraussetzungen)
2. [Eine Datentabellen-Verbindung](#eine-datentabellen-verbindung)
3. [Einen Keyspace importieren](#einen-keyspace-importieren)
4. [Einstellungen](#einstellungen)
5. [Wie das SQL beantwortet wird](#wie-das-sql-beantwortet-wird)
6. [Was geht und was nicht](#was-geht-und-was-nicht)
7. [Warum keine Site auf Cassandra läuft](#warum-keine-site-auf-cassandra-läuft)
8. [Wie er gebaut ist](#wie-er-gebaut-ist)
9. [Tests](#tests)

## Voraussetzungen

| Was | Woher |
|---|---|
| Apache Cassandra 4.0 bis 5.0 oder ScyllaDB ab 2025.1 | Getestet: Cassandra 5.0.9 und ScyllaDB 2025.1.17 (die Images `cassandra:5.0` und `scylladb/scylla:2025.1`) |
| PHP 8.1 oder neuer | Die Bibliothek braucht es; unter PHP 7.4 und 8.0 stehen die Engines als „kein Treiber auf diesem Server“ in der Liste |
| Die Bibliothek `mroosz/php-cassandra` (ab 1.2) | `composer require mroosz/php-cassandra:^1.2`; `composer.json` schlägt sie vor. Keine PHP-Erweiterung nötig |

**Warum diese Bibliothek.** Es gibt drei Wege von PHP zu CQL. DataStax' PHP-Erweiterung (`datastax/php-driver`) wird
seit 2017 nicht mehr gepflegt und baut auf keinem PHP 8. Ihr Fork für ScyllaDB-Nutzer (`he4rt/scylladb-php-driver`) ist
eine C++-Erweiterung auf einem C++-Treiber, je PHP-Version zu bauen, die kein Hoster als Paket anbietet. Die
HTTP-Gateways helfen auch nicht: Stargate ist eingestellt, die Data API gibt es nur in DataStax' eigenen Produkten, und
ScyllaDBs Alternator spricht die API von DynamoDB, nicht CQL. `mroosz/php-cassandra` ist ein Client des nativen
CQL-Protokolls (Versionen 3 bis 5) in PHP, MIT-lizenziert, gepflegt (1.2.0, November 2025), vom Autor gegen Cassandra 2.1
bis 5 und ScyllaDB 6.2 bis 2026.x getestet, mit Anmeldung, TLS, vorbereiteten Anweisungen, Paging und LZ4-Kompression.
Nichts zu kompilieren, und der Treiber lädt sie aus `vendor/mroosz/php-cassandra/src`, auch wo Composers Autoloader sie
nicht kennt. Zwei Fehler ihrer Version 1.2.0 umgeht der Treiber: sie schreibt die serielle Konsistenz einer Anweisung
falsch (der Treiber überlässt sie dem Standard des Servers, `SERIAL`), und sie kann auf Protokoll 5 keine Anweisung über
128 KiB senden (der Treiber spricht Protokoll 4, außer bei `ProtocolVersion=5`).

## Eine Datentabellen-Verbindung

Eine Verbindung der Engine `cassandra` oder `scylladb` ist ein Keyspace. Man legt sie unter **Setup > Datentabellen >
Verbindungen** an oder deklariert sie:

```ini
[Connection_events]
Name=Event store
Engine=scylladb
Server=scylla1.example.com,scylla2.example.com
Port=9042
Database=events
User=exponential
Secret=env:SCYLLA_PASSWORD
Consistency=LOCAL_QUORUM
```

`Database` ist der Keyspace und muss existieren (oder `[CassandraSettings] AutoCreateKeyspace=enabled`). `Server` darf
mehrere Knoten nennen; ein ausgefallener Knoten wird übersprungen. Die Optionen `ssl` (TLS an), `ssl_ca` (die CA-Datei)
und `consistency` werden zu den `[CassandraSettings]` der Verbindung. **Verbindung testen** meldet die Version des
Servers (bei ScyllaDB die eigene, nicht die 3.0.8, die es Cassandra-Clients nennt) und die Zahl der Tabellen.

Alles der Datentabellen-Schicht geht darauf:

```php
$db = expDataConnections::db( 'events' );                                   // ein expScyllaDB
$plan = expDataTableSchema::plan( expDataTableSchema::fromDefinition( myEvent::definition() ), $db );
expDataTableSchema::apply( $schema, $db );                                   // CREATE TABLE, Indizes, die Definition
$event = new myEvent( array( 'id' => null, 'kind' => 'login' ) );
$event->store();                                                              // ID 1, 2, 3 ... aus dem Zähler
$list = myEvent::fetchObjectList( myEvent::definition(), null, array( 'kind' => 'login' ), array( 'id' => 'desc' ), 10 );
```

## Einen Keyspace importieren

```bash
./console exp:cassandra:import --source=events --list
./console exp:cassandra:import --source=events --prefix=ev_                    # der Plan: Tabellen, Spalten, SQL
./console exp:cassandra:import --source=events --prefix=ev_ --apply            # in die Datenbank der Site
./console exp:cassandra:import --server=cass1,cass2 --keyspace=shop --user=reader --secret=env:CASSANDRA_PASSWORD \
    --connection=archive --mode=replace --apply --format=json
```

Der Befehl liest jede Tabelle des Keyspace (oder `--tables`), legt sie im Ziel über die Datentabellen-Schicht an (die
Datenbank der Site oder `--connection`) und kopiert die Zeilen Seite für Seite. Er nutzt die Import-Engine von
`exp:access:import`; Modi (`create`, `append`, `replace`), Namen (Kleinbuchstaben in ASCII, ein Präfix, Schlüsselwörter
mit angehängtem `_`) und Berichte sind dieselben. Aus den CQL-Typen wird:

| CQL | Im Ziel |
|---|---|
| `int`, `smallint`, `tinyint`, `boolean` | `int` (ein Boolean als 1 oder 0) |
| `bigint`, `counter` | `bigint` |
| `varint`, `decimal` | `decimal` (38 Stellen) |
| `float`, `double` | `double` |
| `text`, `varchar`, `ascii` | `varchar(255)` im Schlüssel, sonst langer Text |
| `uuid`, `timeuuid` | `char(38)` |
| `timestamp`, `date` | `varchar(19)`, `Y-m-d H:i:s` in UTC |
| `time`, `duration`, `inet` | `varchar(64)` |
| `blob` | langer Text, Base64 |
| `list`, `set`, `map`, Tupel, Benutzertypen, Vektoren | langer Text, JSON |

Der Schlüssel der neuen Tabelle sind Partitionsschlüssel und Clustering-Spalten; jeder Sekundärindex wird ein Index.

## Einstellungen

`site.ini [CassandraSettings]` (die Optionen einer Verbindung überschreiben `TLS`, `TLSCAFile` und `Consistency`):

| Einstellung | Standard | Was |
|---|---|---|
| `Consistency` | `LOCAL_QUORUM` | Von Lese- und Schreibzugriffen |
| `SerialConsistency` | `LOCAL_SERIAL` | Der Lesezugriffe auf die ID-Zähler |
| `ReplicationStrategy`, `ReplicationFactor`, `DataCenters` | `SimpleStrategy`, `1`, leer | Ein Keyspace, den der Treiber anlegt; `DataCenters=dc1:3,dc2:3` für NetworkTopologyStrategy |
| `Tablets` | `disabled` | ScyllaDB: Keyspaces ohne Tablets (ScyllaDB 2025.1 kennt auf Tablets keine Lightweight Transactions) |
| `AutoCreateKeyspace` | `disabled` | Einen fehlenden Keyspace beim Öffnen anlegen |
| `TLS`, `TLSCAFile`, `TLSVerifyPeer` | `disabled`, leer, `enabled` | TLS zu den Knoten |
| `ConnectTimeout`, `RequestTimeout` | `5`, `30` | Sekunden |
| `Compression` | `disabled` | LZ4 (PHPs Erweiterung `lz4`) |
| `ProtocolVersion` | `4` | 4 oder 5 (siehe die Grenze der Bibliothek oben) |
| `PageSize` | `5000` | Zeilen je Seite |
| `ScanLimit` | `100000` | Die meisten Zeilen, die eine Anweisung für das lesen darf, was CQL nicht kann; mehr wird abgelehnt, nie aus einem Teil beantwortet |
| `AllowFiltering` | `enabled` | Vergleiche anderer Spalten gehen mit `ALLOW FILTERING` an den Server |
| `InsertCheck` | `read` | Ein doppelter Schlüssel: `read` (erst nachsehen), `lwt` (`IF NOT EXISTS`, genau auch bei gleichzeitigen Schreibern), `none` (überschrieben) |
| `InListChunkSize` | `100` | Werte eines `IN` je Anweisung |
| `JournalLimit` | `10000` | Schreibzugriffe einer Transaktion, die ein Rollback zurücknehmen kann |
| `ReconnectOnLoss` | `enabled` | Eine verlorene Verbindung außerhalb einer Transaktion wird neu geöffnet |

## Wie das SQL beantwortet wird

Die persistenten Klassen und die Datentabellen-Schicht schreiben SQL; der Treiber zerlegt es (`expCassandraDialect`) und
beantwortet es mit CQL und PHP:

- **SELECT.** Ein WHERE, das den ganzen Partitionsschlüssel mit `=` oder `IN` nennt, ist ein Schlüsselzugriff (mit `=`
  auf den Clustering-Spalten der Reihe nach). `=` auf einer Spalte mit Sekundärindex ist ein Indexzugriff. Vergleiche
  anderer Spalten gehen mit `ALLOW FILTERING` an den Server. Was CQL nicht ausdrücken kann (`OR`, `LIKE`, `IN` auf
  anderen Spalten, `NOT`, `BETWEEN`, Funktionen, Arithmetik, `ORDER BY`, `OFFSET`, `DISTINCT`, Aggregate über einem
  Filter), geschieht in PHP auf den Zeilen, die der Server schickt, Seite für Seite, bis zu `ScanLimit` Zeilen. `LIMIT`
  geht an den Server, wenn für PHP nichts bleibt; `COUNT(*)` zählt der Server. Text vergleicht wie in Cassandra:
  Groß- und Kleinschreibung zählen, nach Codepunkten.
- **INSERT.** Ein vorhandener Schlüssel wird abgelehnt (`1062`, wie bei MySQL), nach einem Nachsehen
  (`InsertCheck=read`) oder durch `IF NOT EXISTS` (`lwt`). `REPLACE`, `INSERT OR REPLACE` und `INSERT IGNORE` behalten
  ihre Bedeutung; `sqlUpsert()` schreibt `REPLACE` oder `INSERT ... ON DUPLICATE KEY UPDATE`, das der Treiber mit einem
  Nachsehen und einem Update der genannten Spalten beantwortet. Die Standardwerte der Tabellendefinition füllen die
  Spalten, die eine Anweisung weglässt.
- **Neue IDs.** Eine `auto_increment`-Spalte nimmt die nächste Zahl ihrer Zählerzeile in `exp_cassandra_sequence`,
  per Compare-and-Set (eine Lightweight Transaction) weitergezählt, sodass zwei Schreiber nie dieselbe ID bekommen.
  Eine ID, die eine Anweisung selbst angibt, schiebt den Zähler über sie hinaus. `lastSerialID()` ist die letzte ID
  dieser Verbindung je Tabelle.
- **UPDATE, DELETE.** Die Zeilen, die das WHERE findet, werden gelesen und über ihren Schlüssel geschrieben: ein
  `UPDATE` einer fehlenden Zeile legt keine an (CQLs eigenes würde es). Ein geänderter Schlüssel löscht die alte Zeile und
  schreibt die neue. `DELETE` über den ganzen Schlüssel braucht kein Lesen; `DELETE` ohne WHERE ist `TRUNCATE`.
- **Transaktionen.** Cassandra hat keine über mehrere Zeilen. Schreibzugriffe gehen sofort an den Server;
  `rollback()` nimmt die Schreibzugriffe dieser Verbindung in der Transaktion durch ausgleichende Schreibzugriffe zurück
  (eine eingefügte Zeile gelöscht, eine geänderte oder gelöschte zurückgeschrieben). Andere können die Schreibzugriffe
  dazwischen sehen, und ein Prozess, der in einer Transaktion stirbt, hinterlässt sie.
- **Abgelehnt.** Joins, Unterabfragen, `GROUP BY`, `HAVING`, `UNION`, `CASE`, unbekannte Funktionen, `INSERT ... SELECT`:
  `expDBUnsupportedException` innen, `false` (oder `eZDBException`) außen, mit dem Namen dessen, was verlangt war. Nichts
  wird falsch oder mit einem leeren Ergebnis beantwortet.
- **CQL.** Jede andere Anweisung ist schon CQL (`CREATE TABLE`, `CREATE INDEX`, `DROP`, `TRUNCATE`); `cql( $cql, $values )`
  schickt CQL mit gebundenen Werten und gibt jede Seite zurück.

`expCassandraSchema` macht aus einer `.dba`-Tabelle CQL: das erste Feld des Primärschlüssels ist der Partitionsschlüssel,
die übrigen sind Clustering-Spalten (ein Zugriff über das erste Feld oder über alle ist ein Schlüsselzugriff); eine
Tabelle ohne Schlüssel bekommt `exp_row_id uuid`; jeder andere Index wird ein Sekundärindex auf seiner ersten Spalte
(eindeutige Indizes werden nicht erzwungen: Cassandra kennt keine Einschränkung außer dem Schlüssel). CQL hält kein
`NOT NULL`, keinen Standardwert, keine Länge und kein `auto_increment`; darum wird die Definition selbst als JSON in
`exp_cassandra_schema` gehalten: der Treiber liest dort die Standardwerte und die hochgezählte Spalte, und die
Upgrade-Prüfung bekommt die Tabelle so zurück, wie sie geschrieben wurde.

## Was geht und was nicht

Getestet auf Cassandra 5.0.9 und ScyllaDB 2025.1.17 (beide: die Live-Suiten, 30 Tests).

| Geht | Geht nicht, oder anders |
|---|---|
| Eine Datentabellen-Verbindung: Test, Tabellen, persistente Klassen (`store()`, `fetchObject()`, `fetchObjectList()` mit Bedingungen, Sortierung und Limit, `count()`, `removeObject()`) | Eine Site-Datenbank ([warum](#warum-keine-site-auf-cassandra-läuft)) |
| `expDataTableSchema` Plan und Anwenden: Anlegen und ein Upgrade, das Spalten hinzufügt; der Describer | Den Typ einer Spalte ändern (CQL ändert keinen: als Hinweis zurückgehalten); eine Spalte löschen nur auf Verlangen |
| `expDataTableWriter`: Einfügen, Ändern, Upsert über jeden Schlüssel, unveränderte Zeilen bleiben unberührt | Ein eindeutiger Index außer dem Schlüssel wird nicht erzwungen |
| SELECT mit `=`, `<>`, `<`, `>`, `IN`, `LIKE`, `BETWEEN`, `IS NULL`, `OR`, `NOT`, Arithmetik, `&`, `|`, `LOWER`, `UPPER`, `SUBSTRING`, `CONCAT`, `MD5`, `COALESCE`, `ORDER BY`, `LIMIT`, `OFFSET`, `DISTINCT`, `COUNT`, `MIN`, `MAX`, `SUM`, `AVG` | Joins, Unterabfragen, `GROUP BY`, `HAVING`, `UNION`, `CASE`: mit Namen abgelehnt |
| Neue IDs aus einem Zähler (Compare-and-Set): eindeutig über Verbindungen und Knoten | IDs sind über die Transaktionen mehrerer Schreiber nicht in Einfügereihenfolge; jede neue ID kostet eine Lightweight Transaction |
| Ein doppelter Schlüssel abgelehnt (`read`: Nachsehen; `lwt`: `IF NOT EXISTS`) | `read` ist bei gleichzeitigen Schreibern desselben Schlüssels nicht genau (dafür `lwt`) |
| `rollback()` durch ausgleichende Schreibzugriffe | Keine Isolation: andere sehen die Schreibzugriffe vor dem Commit; ein Absturz hinterlässt sie |
| Text jeder Größe (200 KB getestet), Unicode, Anführungszeichen | Eine Anweisung über 128 KiB auf Protokoll 5 (die Bibliothek; Standard ist Protokoll 4) |
| Anmeldung (ScyllaDB mit `PasswordAuthenticator`), mehrere Knoten, TLS per Einstellung | Temporäre Tabellen, `LOCK TABLES` (tut nichts), `GROUP_CONCAT` (`sqlGroupConcat()` wirft) |
| Der Import jeder Tabelle: zusammengesetzte Schlüssel, Clustering-Reihenfolge, `uuid`, `timestamp`, `boolean`, `decimal`, `varint`, `blob`, `list`, `map`, ein Index | Counter, Benutzertypen und Tupel kommen als Werte (JSON) an, nicht als ihre CQL-Typen |

### Gemessen

Dieselben Operationen auf einer Tabelle mit 5.300 Zeilen, je ein Knoten in einem Container auf dem Datenbankserver der
Entwicklung (12 Kerne, Lastmittel 35 bis 40 durch seine eigenen Sites während der Messung), der Client PHP 8.5 auf
demselben Rechner. SQLite (eine Datei) zum Vergleich.

| Operation | Cassandra 5.0.9 | ScyllaDB 2025.1.17 | SQLite |
|---|---|---|---|
| Verbinden (Protokoll ausgehandelt, Keyspace) | 345 ms | 220 ms | 32 ms |
| Eine Tabelle anlegen (mit einem Index und ihrer Definition) | 1.839 ms | 156 ms | 4 ms |
| INSERT mit neuer ID (Zähler, Compare-and-Set) | 17,6 ms | 17,8 ms | 0,08 ms |
| INSERT mit eigener ID, `InsertCheck=none` / `read` / `lwt` | 10,7 / 14,8 / 15,6 ms | 15,1 / 21,7 / 21,6 ms | 0,15 ms |
| SELECT über den Primärschlüssel | 2,2 ms | 0,6 ms | 0,02 ms |
| SELECT über eine indizierte Spalte (etwa 170 Zeilen) | 13,9 ms | 14,2 ms | 0,12 ms |
| SELECT vom Server gefiltert (`ALLOW FILTERING`, ganze Tabelle) | 28 ms | 22 ms | 0,46 ms |
| SELECT in PHP gefiltert (`LIKE`, ganze Tabelle gelesen) | 106 ms | 83 ms | 0,61 ms |
| SELECT ... ORDER BY ... LIMIT 10 (ganze Tabelle, in PHP sortiert) | 291 ms | 217 ms | 3,5 ms |
| SELECT COUNT(*) der Tabelle | 133 ms | 25 ms | 0,02 ms |
| UPDATE über den Primärschlüssel (lesen, dann schreiben) | 10,7 ms | 1,0 ms | 0,20 ms |
| UPDATE über eine indizierte Spalte (etwa 10 Zeilen) | 26 ms | 24 ms | 0,12 ms |
| DELETE über den Primärschlüssel | 0,8 ms | 0,8 ms | 0,06 ms |
| Rollback von 100 geänderten Zeilen | 194 ms | 68 ms | 0,04 ms |
| Import von 2.000 Zeilen nach SQLite (`exp:cassandra:import`) | 0,20 s | 0,51 s | |

Ein INSERT, das seine ID selbst angibt, schiebt außerhalb einer Transaktion den ID-Zähler sofort weiter (eine
Lightweight Transaction je Anweisung, etwa 10 ms); in einer Transaktion geschieht das einmal, beim Commit. Massenhaftes
Schreiben gehört darum in Transaktionen.

## Warum keine Site auf Cassandra läuft

Der Kernel wurde gefragt, nicht geschätzt. Eine Demo-Site (das Paket `sevenx_multisite`, 291 Objekte, 153 Tabellen)
wurde auf SQLite installiert und 13 Seitenaufrufe Anweisung für Anweisung aufgezeichnet: die Startseite und fünf
Site-Seiten, eine Suche, die Admin-Anmeldung, zwei Knotenansichten, das Dashboard, die Klassenliste und ein Benutzer.
Jede Anweisung ging durch den Parser und den Planer des Treibers gegen das Kernel-Schema, wie `expCassandraSchema` es
anlegt, und die Tabellen der Site wurden in Cassandra und ScyllaDB geladen (20.443 Zeilen; der Suchindex mit 104.943
Zeilen ausgelassen) und jedes SELECT dort wiederholt.

| Gemessen | Mit warmen Caches | Mit geleertem View-Cache |
|---|---|---|
| Anweisungen der 13 Seitenaufrufe | 1.408 | 2.087 |
| Davon SQLites eigener Katalog (`sqlite_master`, `PRAGMA`; ein anderer Treiber fragt seinen eigenen) | 301 | 303 |
| **Joins** (abgelehnt) | **208** | **559** |
| `GROUP BY`, Unterabfragen, Index-Hinweise (abgelehnt) | 5 | 10 |
| Beantwortbar: SELECTs (Schlüsselzugriff 322, Index 752, Serverfilter 116, Scan 18 im kalten Lauf), Schreibzugriffe | 893 | 1.214 |
| Seitenaufrufe ohne abgelehnte Anweisung | **0 von 13** | **0 von 13** |

- **Jeder Aufruf braucht einen Join.** Die URL einer Seite wird über `ezurlalias_ml`, mit sich selbst verknüpft,
  aufgelöst, bei jedem Aufruf, auch wenn die Seite danach aus dem View-Cache kommt: die Startseite schickte mit warmen
  Caches drei Anweisungen, alle drei Joins. Die kalte Startseite schickte 111 abgelehnte und 25 beantwortbare.
- **Was CQL beantworten kann, beantwortet es richtig, aber langsam.** Von den 2.001 SELECTs des kalten Laufs
  beantwortete Cassandra 1.208 mit denselben Zeilen wie SQLite (15 in anderer Reihenfolge, wo die Anweisung sie offen
  lässt) und lehnte 793 ab. Die beantwortbaren brauchten 12,5 s auf Cassandra und 3,8 s auf ScyllaDB, gegen 0,97 s und
  0,58 s auf SQLite für alle 2.001: 7- bis 13-mal langsamer, bevor ein einziger Join beantwortet ist.
- **MongoDBs Weg lässt sich nicht übertragen.** Der MongoDB-Adapter beantwortet Joins, indem er die Collections, die
  eine Anweisung nennt, in eine SQLite-Datenbank im Speicher kopiert (`MongoSQLFallback`), zusätzlich zu 63
  MongoDB-Zweigen in 51 Kernel-Dateien. MongoDB kann auf jedem Feld filtern und sortieren; Cassandra liest eine Tabelle
  ohne ihren Schlüssel nur ganz (etwa 100 ms für 5.300 Zeilen oben), so dass eine solche Kopie für jeden der 559 Joins
  von 13 Seitenaufrufen ganze Tabellen läse.
- **Keine Transaktionen, keine temporären Tabellen, keine Sperren.** Veröffentlichen schreibt Dutzende Zeilen in einer
  Transaktion, Suche und Baum nutzen temporäre Tabellen, der Kernel sperrt Tabellen für Zähler. Cassandra hat nichts
  davon; Ausgleich ist keine Isolation, und jede neue ID ist eine Lightweight Transaction (etwa 18 ms).

Eine Site auf Cassandra bräuchte also die Abfragen des Kernels um die Joins herum neu geschrieben, wie es MongoDBs
Portierung tat, und zahlte trotzdem eine Lightweight Transaction je neuer Zeile und gäbe Transaktionen auf. Exponential
bietet das nicht an; Cassandra und ScyllaDB sind die Orte, aus denen Daten kommen oder in die sie gehen:
Datentabellen-Verbindungen und eine Importquelle.

## Wie er gebaut ist

| Datei | Was |
|---|---|
| `lib/ezdb/classes/expcassandradb.php` | `expCassandraDB`: Verbindung, der Planer der SELECTs, Schreibzugriffe, IDs, das Rollback-Journal, der Katalog |
| `lib/ezdb/classes/expscylladb.php` | `expScyllaDB`: derselbe Treiber unter dem Namen `scylladb` |
| `lib/ezdb/classes/expcassandradialect.php` | `expCassandraDialect`: die SQL-Teilmenge zerlegt, Bedingungen und Aggregate in PHP, die Typen von CQL und `.dba` |
| `lib/ezdb/classes/expcassandratransport.php`, `expcassandraphptransport.php` | Die Schicht unter dem Treiber und ihre Umsetzung über `mroosz/php-cassandra` |
| `lib/ezdbschema/classes/expcassandraschema.php` | `expCassandraSchema` (`dbschema.ini`: `cassandra`, `scylladb`) |
| `kernel/classes/cassandra/expcassandrareader.php` | `expCassandraReader`: ein Keyspace, gelesen für die Import-Engine von `expAccessImport` |
| `kernel/private/classes/commands/expcassandraimport.php`, `bin/php/expcassandraimport.php` | `exp:cassandra:import` |

Eingetragen in `site.ini [DatabaseSettings] ImplementationAlias[cassandra]`, `[scylladb]`, `[scylla]`; `dbschema.ini`;
`datatable.ini [ConnectionSettings] Engines[]` (`cassandra`, `scylladb`); `expDataConnections` (Engine-Art
`widecolumn`), `expDataTableSchema` (Anlegen und Upgrade), `expDataTableDescriber`; `expDBSQLHelpers` (Dialekt
`cassandra`: `upsert()` wie oben, `groupConcat()` abgelehnt, `unixTimestamp()` und `fromUnixTimestamp()` in PHP
berechnet).

## Tests

- `tests/tests/lib/ezdb/cassandra/` und `tests/tests/kernel/classes/cassandra/` (Testsuite `cassandra`, kein Server):
  der Dialekt (Parser, Ablehnungen, Bedingungen, Projektion, Typen, Upsert, Fehlerkategorien), der Treiber über einem
  aufzeichnenden Transport (Pläne, gesendete Anweisungen, IDs, Prüfung auf Duplikate, Rollback, Fehler, Helfer), der
  Schema-Handler, der Leser des Imports.
- `.../live/` (Testsuite `cassandra-live`): jeder Test auf Cassandra und auf ScyllaDB: Schema und IDs, SELECT-Pläne,
  Einfügen, Ändern und Löschen, Transaktionen, Ablehnungen, gleichzeitige IDs, Text und fremde Typen; eine persistente
  Klasse, Schema-Plan und Upgrade und der Writer auf einer Datentabellen-Verbindung; der Import. Server:
  `EXP_TEST_CASSANDRA_HOST`, `_PORT`, `_USER`, `_PASSWORD`, `_KEYSPACE`, dasselbe für `EXP_TEST_SCYLLADB_`; ohne sie
  werden die Tests übersprungen.
- CI: der Job „Cassandra and ScyllaDB“ führt beide Suiten unter PHP 8.1 und 8.5 gegen Cassandra 5.0 und ScyllaDB 2025.1
  (mit Anmeldung) in Containern aus.

Siehe auch: [Datenbankunterstützung](../../hilfe/datenbankunterstuetzung.md), [Datentabellen](datentabellen.md),
[MS-Access-Unterstützung](ms-access-unterstuetzung.md) (die Import-Engine),
[MongoDB als Datenbank](mongodb-datenbankunterstuetzung.md).
