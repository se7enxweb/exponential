# Apache-CouchDB-Treiber

Diese Seite ist für Administratoren, deren Organisation Daten in Apache CouchDB hält und diese Daten in Exponential haben
will oder eine Datentabelle von Exponential in CouchDB halten will, und für Entwickler, die am Treiber arbeiten. Ab
Exponential 6.0.15 hat der Kernel einen CouchDB-Treiber, `expCouchDBDB` in `lib/ezdb/classes/`, mit dem Schema-Handler
`expCouchDBSchema` und dem Importbefehl `exp:couchdb:import`.

**Wofür er gedacht ist.** CouchDB ist ein Dokumentenspeicher, der über HTTP erreicht wird: JSON-Dokumente mit Revisionen,
abgefragt mit Mango (Selektoren auf Feldern) oder mit Views; es gibt kein SQL, keine Joins und keine Transaktionen. Der
Treiber erreicht zwei der drei Stufen, die eine Datenbank in Exponential erreichen kann:

| Stufe | CouchDB |
|---|---|
| (a) Die Datenbank der Site | **Nein.** [Warum](#warum-keine-site-auf-couchdb-läuft), mit den Messungen |
| (b) Eine Datentabellen-Verbindung | **Ja.** Persistente Klassen, Schema, Writer und Describer der Datentabellen-Schicht, der Test der Verbindung |
| (c) Eine Importquelle | **Ja.** `exp:couchdb:import` kopiert die Dokumente einer Datenbank (eine Tabelle, oder eine je Wert eines Typfelds) in die Datenbank der Site oder in jede Datentabellen-Verbindung |

## Inhalt

1. [Voraussetzungen](#voraussetzungen)
2. [Eine Datentabellen-Verbindung](#eine-datentabellen-verbindung)
3. [Eine Datenbank importieren](#eine-datenbank-importieren)
4. [Einstellungen](#einstellungen)
5. [Wie das SQL beantwortet wird](#wie-das-sql-beantwortet-wird)
6. [Was geht und was nicht](#was-geht-und-was-nicht)
7. [Warum keine Site auf CouchDB läuft](#warum-keine-site-auf-couchdb-läuft)
8. [Wie er gebaut ist](#wie-er-gebaut-ist)
9. [Tests](#tests)

## Voraussetzungen

| Was | Woher |
|---|---|
| Apache CouchDB 3.3 oder 3.4 | Getestet: CouchDB 3.4.3 (das Image `couchdb:3.4.3`), und 3.3.3 in der CI |
| PHP 8.0 oder neuer mit `curl` und `sqlite3` | Beide sind in jedem PHP, auf dem Exponential läuft; keine Bibliothek, keine eigene Erweiterung für CouchDB |

**Warum HTTP.** CouchDB hat eine einzige Schnittstelle, seine HTTP-API; es gibt kein binäres Protokoll und keine
PHP-Erweiterung, zwischen denen man wählen könnte. Der Treiber spricht sie mit PHPs curl-Erweiterung
(`expCouchDBClient`, ein Handle je Verbindung, sodass die TCP-Verbindung wiederverwendet wird). Eine SQL-Schnittstelle für
CouchDB gibt es nicht; deshalb beantwortet der Treiber SQL auf einer SQLite-Kopie im Speicher (unten), und deshalb braucht
er PHPs `sqlite3`.

## Eine Datentabellen-Verbindung

Eine Verbindung der Engine `couchdb` ist eine CouchDB-Datenbank. Legen Sie sie unter **Setup > Datentabellen >
Verbindungen** an oder deklarieren Sie sie:

```ini
[Connection_orders]
Name=Bestellungen
Engine=couchdb
Server=couch1.example.com
Port=5984
Database=orders
User=exponential
Secret=env:COUCHDB_PASSWORD
```

`Database` ist die CouchDB-Datenbank und muss existieren (oder `[CouchDBSettings] AutoCreateDatabase=enabled`). `Server`
kann für TLS als `https://couch1.example.com:6984` geschrieben werden (oder `SSL=enabled` setzen; `SSLCA` nennt eine
CA-Datei). **Verbindung testen** meldet die Version von CouchDB und die Zahl der Tabellen.

Alles der Datentabellen-Schicht funktioniert darauf:

```php
$db = expDataConnections::db( 'orders' );                                    // ein expCouchDBDB
$plan = expDataTableSchema::plan( expDataTableSchema::fromDefinition( myOrder::definition() ), $db );
expDataTableSchema::apply( $schema, $db );                                    // die Definition gespeichert, Mango-Indizes angelegt
$order = new myOrder( array( 'id' => null, 'state' => 'new' ) );
$order->store();                                                               // id 1, 2, 3 ... aus dem Zähler
$list = myOrder::fetchObjectList( myOrder::definition(), null, array( 'state' => 'new' ), array( 'id' => 'desc' ), 10 );
```

Eine Tabelle ist die Menge der Dokumente, deren Feld `exp_table` sie nennt; eine Zeile ist ein Dokument, seine `_id` die
Tabelle und der Primärschlüssel (`order:17`; ein Schlüssel aus mehreren Spalten als JSON-Array, `line:["17","3"]`; eine
Tabelle ohne Schlüssel bekommt eine UUID). Andere Anwendungen können diese Dokumente mit den Werkzeugen von CouchDB lesen
und schreiben.

## Eine Datenbank importieren

```bash
./console exp:couchdb:import --source=orders --list
./console exp:couchdb:import --source=orders --type-field=type --prefix=shop_              # der Plan
./console exp:couchdb:import --source=orders --type-field=type --prefix=shop_ --apply      # in die Datenbank der Site
./console exp:couchdb:import --server=couch1 --database=shop --user=reader --secret=env:COUCH_PASSWORD \
    --connection=archive --mode=replace --apply --format=json
```

Welche Dokumente eine Tabelle bilden:

- **`--type-field=type`**: eine Tabelle je Wert dieses Felds (ein Dokument ohne das Feld bleibt außen vor);
- **eine Datenbank, die der CouchDB-Treiber geschrieben hat**: eine Tabelle je `exp_table`, mit der Definition, die der
  Treiber gespeichert hat (Spalten, Typen, Schlüssel);
- **sonst**: eine Tabelle, nach der Datenbank benannt, aus allen Dokumenten. Design-Dokumente bleiben immer außen vor.

Eine Tabelle ohne gespeicherte Definition bekommt die Felder der obersten Ebene einer Stichprobe ihrer Dokumente
(`--sample`, 500) und `doc_id`, die `_id` des Dokuments, als Primärschlüssel. `_rev`, `_attachments` und `_conflicts`
bleiben außen vor. Die Typen werden abgeleitet:

| In den Dokumenten | Im Ziel |
|---|---|
| nur ganze Zahlen | `int` (`bigint` jenseits von 32 Bit) |
| Zahlen | `double` |
| `true`, `false` | `int`, 1 oder 0 |
| Text bis 255 Zeichen | `varchar(255)` |
| längerer Text | langer Text |
| Objekte, Arrays | langer Text, JSON |

Der Befehl nutzt die Import-Engine von `exp:access:import`; die Modi (`create`, `append`, `replace`), die Namen
(Kleinbuchstaben in ASCII, ein Präfix, Schlüsselwörter mit angehängtem `_`) und die Berichte sind deshalb dieselben. Ein
Feld, das ein späteres Dokument hat, die Stichprobe aber nicht, wird nicht importiert: `--sample` erhöhen.

## Einstellungen

`site.ini [CouchDBSettings]` (`SSL` und `SSLCA` einer Verbindung überschreiben `TLS` und `TLSCAFile`):

| Einstellung | Vorgabe | Was |
|---|---|---|
| `TLS`, `TLSCAFile`, `TLSVerifyPeer` | `disabled`, leer, `enabled` | HTTPS zum Knoten |
| `ConnectTimeout`, `RequestTimeout` | `5`, `60` | Sekunden |
| `AutoCreateDatabase` | `disabled` | Eine fehlende Datenbank beim Öffnen der Verbindung anlegen |
| `ScanLimit` | `100000` | Die meisten Dokumente, die eine Anweisung lesen darf; mehr wird abgelehnt, nie aus einem Teil beantwortet |
| `BatchSize` | `1000` | Dokumente, die eine Anfrage liest oder schreibt |
| `JournalLimit` | `10000` | Schreibvorgänge einer Transaktion, die ein Rollback rückgängig machen kann |

## Wie das SQL beantwortet wird

Die persistenten Klassen und die Datentabellen-Schicht schreiben SQL; der Dialekt des Treibers ist der von SQLite
(`sqlHelperDialect()` ist `sqlite`), ihre Upserts, Limits und `GROUP_CONCAT` werden also für SQLite geschrieben. Jede
Anweisung wird in zwei Schritten beantwortet:

1. **Die Dokumente, die sie braucht, werden aus CouchDB gelesen**: jede Tabelle, die die Anweisung nennt. Liest sie eine
   Tabelle und vergleicht Spalten mit `=` mit Literalen, nur diese: über ihre `_id`, wenn die Vergleiche den ganzen
   Primärschlüssel nennen (eine Anfrage), mit Mango, wenn sie die erste Spalte eines Index der Tabelle nennen (der Treiber
   legt einen Mango-Index je Index der Definition an, und einen auf `exp_table`), sonst über den Bereich der Ids der
   Tabelle (`_all_docs`, `table:` ...), gefiltert vom Treiber. Mango ohne Index wertet seinen Selektor an jedem Dokument
   der Datenbank aus, hier um ein Mehrfaches langsamer als der Id-Bereich.
2. **Die Anweisung läuft auf einer SQLite-Kopie dieser Dokumente im Speicher** (die Spalten mit den Typen von SQLite, die
   Vorgaben der Definition, der Primärschlüssel und die eindeutigen Indizes). Die Antwort ist die von SQLite, genau: Joins,
   `GROUP BY`, Unterabfragen, `CASE`, Funktionen.

Ein **Schreibvorgang** läuft ebenfalls auf der Kopie. Trigger auf der Kopie halten die Zeilen fest, die er eingefügt,
geändert und gelöscht hat, und der Treiber schreibt diese mit `_bulk_docs` zurück, jede Änderung mit der Revision, mit der
sie gelesen wurde:

- ein Dokument, das jemand anders dazwischen geändert hat, ist ein **Konflikt**: die Anweisung schlägt fehl, nichts geht
  still verloren;
- ein `INSERT` eines vorhandenen Schlüssels ist Fehler **1062** (der Konflikt von CouchDB auf der `_id`); `INSERT OR
  IGNORE` überspringt ihn; `REPLACE` und SQLites `ON CONFLICT ... DO UPDATE` (das Upsert der Helfer) lesen die Tabelle,
  damit die Kopie die Zeile sieht;
- ein geänderter Schlüssel verschiebt das Dokument (das alte wird gelöscht, das neue geschrieben);
- **neue Ids**: die hochgezählte Spalte nimmt die nächsten Nummern des Zählerdokuments `exp_couchdb_sequence:<table>`,
  weitergesetzt mit seiner Revision (Compare-and-Set, bei einem Konflikt wiederholt), sodass zwei Schreiber nie dieselbe
  Id bekommen; eine Id, die eine Anweisung selbst angibt, setzt den Zähler darüber hinaus.

**Transaktionen.** CouchDB hat keine. Schreibvorgänge erreichen den Server sofort; `rollback()` macht die Schreibvorgänge
dieser Verbindung in der Transaktion durch ausgleichende Schreibvorgänge rückgängig (ein eingefügtes Dokument wird
gelöscht, ein geändertes oder gelöschtes zurückgeschrieben), bis `JournalLimit`. Andere sehen die Schreibvorgänge
dazwischen, und ein Prozess, der in einer Transaktion abbricht, hinterlässt sie.

**DDL.** `CREATE TABLE` (der Schema-Handler schreibt die Form von MySQL; SQLites `INTEGER PRIMARY KEY AUTOINCREMENT` wird
auch gelesen) speichert die Definition im Dokument `exp_couchdb_schema:<table>` und legt die Mango-Indizes an; `DROP TABLE`
löscht die Dokumente der Tabelle, ihre Definition, ihren Zähler und ihre Indizes; `ALTER TABLE ... ADD` gibt jedem Dokument
das neue Feld mit seiner Vorgabe, `DROP COLUMN` entfernt es, `MODIFY` ändert nur die Definition; `TRUNCATE` löscht die
Dokumente. Eine Spalte, deren Name mit `_` beginnt, wird abgelehnt (die eigenen Felder von CouchDB).

## Was geht und was nicht

Getestet auf CouchDB 3.4.3 (die Live-Suiten) und gegen einen Ersatz im Speicher (die Suite ohne Datenbank).

| Geht | Geht nicht, oder anders |
|---|---|
| Eine Datentabellen-Verbindung: Test, Tabellen, persistente Klassen (`store()`, `fetchObject()`, `fetchObjectList()` mit Bedingungen, Sortierung und Limits, `count()`, `removeObject()`) | Eine Site-Datenbank ([warum](#warum-keine-site-auf-couchdb-läuft)) |
| `expDataTableSchema` Plan und Anwenden: anlegen, und ein Upgrade, das Spalten hinzufügt; der Describer (aus der gespeicherten Definition) | Den Typ einer Spalte ändern ändert die Definition, nicht die Dokumente |
| `expDataTableWriter`: einfügen, aktualisieren, Upsert über jeden Schlüssel, unveränderte Zeilen bleiben unberührt | Isolation: andere sehen die Schreibvorgänge einer Transaktion sofort; ein Rollback ist Ausgleich |
| Das ganze SQL von SQLite auf den Tabellen, die eine Anweisung nennt: Joins, `GROUP BY`, `HAVING`, Unterabfragen, `UNION`, `CASE`, Funktionen | Anweisungen, die mehr als `ScanLimit` Dokumente brauchen, werden abgelehnt |
| Neue Ids aus einem Zählerdokument (Compare-and-Set): eindeutig über Verbindungen hinweg | Jede Anweisung liest die Dokumente, die sie braucht, neu: zwischen Anweisungen gibt es keinen Cache |
| Ein doppelter Schlüssel wird abgelehnt (1062), ein Schreibkonflikt gemeldet | Daten in Anhängen (werden nicht gelesen) |
| Text jeder Größe (100 KB getestet), Unicode, Anführungszeichen und Backslashes | Ein Feld namens `_...` |
| Der Import jeder Datenbank: eine Tabelle, nach einem Typfeld, oder die eigenen Tabellen des Treibers | Anhänge werden nicht importiert |

### Gemessen

Die Operationen einer Datentabelle auf einer Tabelle mit 5.300 Zeilen, CouchDB 3.4.3 in einem Container mit zwei Kernen
und 512 MB auf dem Datenbankserver der Entwicklung (12 Kerne, während des Laufs eine Last von 20 bis 50 durch seine eigenen
Sites), der Client PHP 8.5 auf demselben Host:

| Operation | CouchDB 3.4.3 |
|---|---|
| Verbinden (Begrüßung und Datenbank) | 86 ms |
| Eine Tabelle anlegen (ihre Definition und ein Mango-Index) | 141 ms |
| INSERT, 100 Zeilen je Anweisung | 6,6 ms je Zeile |
| INSERT einer Zeile mit neuer Id (Compare-and-Set des Zählers, dann das Dokument) | 305 ms |
| SELECT über den Primärschlüssel (das Dokument über seine Id) | 15 ms |
| SELECT über eine indizierte Spalte (etwa 170 Zeilen, Mango) | 961 ms |
| SELECT über eine Spalte ohne Index (der Id-Bereich der Tabelle, gefiltert) | 5,9 s |
| SELECT ... LIKE, ORDER BY ... LIMIT 10, COUNT(*), GROUP BY (die ganze Tabelle gelesen) | 8,1 s, 9,4 s, 9,6 s, 10,2 s |
| UPDATE über den Primärschlüssel | 267 ms |
| UPDATE über den Schlüssel, dann ein SELECT über den Index | 1,8 s |
| UPDATE über eine indizierte Spalte (etwa 170 Zeilen) | 2,7 s |
| DELETE über den Primärschlüssel | 137 ms |
| 100 Änderungen über den Schlüssel in einer Transaktion, zurückgerollt / bestätigt | 22,8 s / 22,0 s |
| Ein Join der Tabelle mit sich selbst über den Schlüssel, 50 Zeilen | 9,0 s |

Was über den Schlüssel oder einen Index gelesen wird, ist in Millisekunden bis einer Sekunde beantwortet; was die ganze
Tabelle braucht, kostet die Zeit, sie zu lesen (hier etwa 900 Dokumente je Sekunde). Halten Sie CouchDB-Tabellen klein
oder geben Sie ihren Bedingungen einen Index, und schreiben Sie in Stapeln: eine Anweisung mit 100 Zeilen kostet etwa so
viel wie zwei einzelne Einfügungen.

## Warum keine Site auf CouchDB läuft

Der Kernel wurde gefragt, nicht geraten. Eine Demo-Site (das Paket `sevenx_multisite`, 291 Objekte, 154 Tabellen), auf
SQLite installiert, wurde über den Treiber nach CouchDB kopiert (154 Tabellen in 121 s; 20.443 Zeilen in 179 s, 8,8 ms
je Zeile; der Suchindex mit 104.943 Zeilen blieb außen vor), und die SELECTs von 13 aufgezeichneten Seitenaufrufen bei
geleertem View-Cache (die Startseite und fünf Seiten der Site, eine Suche, das Admin-Login, zwei Knotenansichten, das
Dashboard, die Klassenliste und ein Benutzer) wurden auf beiden wiedergegeben:

| Gemessen | |
|---|---|
| Wiedergegebene SELECTs (SQLites eigener Katalog nicht mitgezählt) | 1.778 |
| Mit den Zeilen von SQLite beantwortet | 1.537 (die übrigen: `MD5()` fehlte in der Kopie, `SELECT *` lieferte die eigenen Spalten der Kopie, ein Index-Hinweis von SQLite; die ersten beiden sind inzwischen behoben) |
| Zeit auf SQLite / auf CouchDB | **2,3 s / 803 s (352-mal)**: 0,18 s gegen 62 s je Seitenaufruf |
| Ein SELECT auf CouchDB im Mittel | 450 ms |

- **Jede Anweisung liest ihre Tabellen neu.** CouchDB hat keine Joins; eine Anweisung über drei Tabellen liest die
  Dokumente aller drei (über den Id-Bereich oder Mango), und von einer Anweisung zur nächsten bleibt nichts. Der Kernel
  sendet bei kalten Caches rund 160 Anweisungen je Seitenaufruf, meist Joins; das Lesen ihrer Tabellen sind die 803
  Sekunden.
- **Mango ist kein SQL-Index.** Ein Selektor, dem ein Index dient, liest die Dokumente trotzdem über den Query-Server von
  CouchDB; ohne Index wertet er jedes Dokument der Datenbank aus. 5.300 Dokumente brauchten hier 6 bis 10 s über den
  Id-Bereich und 11 bis 17 s mit Mango (CouchDB in einem Container mit zwei Kernen, auf einem Host mit einer Last von 20
  bis 50).
- **Keine Transaktionen.** Das Veröffentlichen schreibt Dutzende Zeilen, die zusammen erscheinen müssen oder gar nicht;
  der Ausgleich des Treibers macht seine eigenen Schreibvorgänge rückgängig, gibt aber keine Isolation, und ein
  abbrechender Prozess hinterlässt eine halbe Version.

CouchDB hält deshalb Daten neben einer Site, nicht die Site: eine Datentabellen-Verbindung oder die Quelle eines Imports.

## Wie er gebaut ist

| Datei | Was |
|---|---|
| `lib/ezdb/classes/expcouchdbdb.php` | `expCouchDBDB`: die Verbindung, das Lesen der Dokumente (über die Id, Mango, den Id-Bereich), die Kopie im Speicher, Schreibvorgänge mit Revisionen, Ids, das Journal des Rollbacks, DDL |
| `lib/ezdb/classes/expcouchdbclient.php` | `expCouchDBClient`: die HTTP-API von CouchDB über curl |
| `lib/ezdb/classes/expcouchsqllexer.php` | `expCouchSQLLexer`: die Token einer Anweisung (gemeinsam mit dem Couchbase-Treiber); der DDL-Parser ist `expCouchbaseDialect::ddl()` |
| `lib/ezdbschema/classes/expcouchdbschema.php` | `expCouchDBSchema` (`dbschema.ini`: `couchdb`) |
| `kernel/classes/couch/expcouchdbreader.php` | `expCouchDBReader`: eine Datenbank gelesen für die Import-Engine von `expAccessImport` |
| `kernel/private/classes/commands/expcouchdbimport.php`, `bin/php/expcouchdbimport.php` | `exp:couchdb:import` |

Registriert in `site.ini [DatabaseSettings] ImplementationAlias[couchdb]`; `dbschema.ini`; `datatable.ini
[ConnectionSettings] Engines[]` (`couchdb`); `expDataConnections` (Engine-Art `document`), `expDataTableSchema` (Anlegen und
Upgrade), `expDataTableDescriber`, `expKernelTableSchema::tables()`.

## Tests

- `tests/tests/lib/ezdb/couch/expCouchDBDBTest.php` und `tests/tests/kernel/classes/couch/expCouchReadersTest.php`
  (Testsuite `couch`, ohne Server): der Treiber über `expCouchDBFake`, ein kleines CouchDB im Speicher (Revisionen,
  Konflikte, `_find`, `_bulk_docs`, `_all_docs`, Indizes); der Leser.
- `.../live/` (Testsuite `couch-live`): der Treiber (`expCouchLiveTest`: Schema, Ids, Abfragen mit Joins und
  Unterabfragen, Schreibvorgänge, Transaktionen, Upserts, ALTER, Text), die Datentabellen-Verbindung
  (`expCouchDataTableLiveTest`) und der Import (`expCouchImportLiveTest`). Server: `EXP_TEST_COUCHDB_HOST`, `_PORT`,
  `_USER`, `_PASSWORD`; ohne ihn werden die Tests übersprungen.
- CI: der Job „Couchbase and CouchDB" führt beide Suiten auf PHP 8.5 und 8.1 gegen CouchDB 3.4.3 und 3.3.3 in Containern aus.

Verwandt: [Couchbase-Treiber](couchbase-treiber.md), [Datenbankunterstützung](../../hilfe/datenbankunterstuetzung.md),
[Eine Datenbank wählen](../../hilfe/datenbank-waehlen.md), [Datentabellen](datentabellen.md),
[MS-Access-Unterstützung](ms-access-unterstuetzung.md) (die Import-Engine), [MongoDB als Datenbank](mongodb-datenbankunterstuetzung.md).
