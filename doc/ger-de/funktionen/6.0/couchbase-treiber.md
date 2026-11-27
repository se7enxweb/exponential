# Couchbase-Treiber

Diese Seite ist für Administratoren, deren Organisation Daten in Couchbase hält und diese Daten in Exponential haben will
oder eine Datentabelle von Exponential in Couchbase halten will, und für Entwickler, die am Treiber arbeiten. Ab
Exponential 6.0.15 hat der Kernel einen Couchbase-Treiber, `expCouchbaseDB` in `lib/ezdb/classes/`, mit dem Dialekt
`expCouchbaseDialect`, dem Schema-Handler `expCouchbaseSchema` und dem Importbefehl `exp:couchbase:import`.

**Wofür er gedacht ist.** Couchbase ist eine Dokumentendatenbank, deren Abfragesprache SQL++ (N1QL) nahe an SQL ist:
Joins, Unterabfragen, `GROUP BY`, `UNION` und Transaktionen. Damit war sie der eine Dokumentenspeicher, bei dem sich der
Versuch einer ganzen Site lohnte, und er wurde gemacht: die aufgezeichneten Anweisungen des Kernels wurden auf Couchbase
wiedergegeben und seine Schreibvorgänge gemessen. Der Treiber erreicht zwei der drei Stufen, die eine Datenbank in
Exponential erreichen kann:

| Stufe | Couchbase |
|---|---|
| (a) Die Datenbank der Site | **Nein.** Das SQL lässt sich beantworten, die Geschwindigkeit nicht. [Warum](#warum-keine-site-auf-couchbase-läuft), mit den Messungen |
| (b) Eine Datentabellen-Verbindung | **Ja.** Persistente Klassen, Schema, Writer und Describer der Datentabellen-Schicht, der Test der Verbindung, in einem Scope eines Buckets |
| (c) Eine Importquelle | **Ja.** `exp:couchbase:import` kopiert die Collections eines Scopes in die Datenbank der Site oder in jede Datentabellen-Verbindung |

## Inhalt

1. [Voraussetzungen](#voraussetzungen)
2. [Eine Datentabellen-Verbindung](#eine-datentabellen-verbindung)
3. [Einen Scope importieren](#einen-scope-importieren)
4. [Einstellungen](#einstellungen)
5. [Wie das SQL beantwortet wird](#wie-das-sql-beantwortet-wird)
6. [Was geht und was nicht](#was-geht-und-was-nicht)
7. [Warum keine Site auf Couchbase läuft](#warum-keine-site-auf-couchbase-läuft)
8. [Wie er gebaut ist](#wie-er-gebaut-ist)
9. [Tests](#tests)

## Voraussetzungen

| Was | Woher |
|---|---|
| Couchbase Server 7.2 oder neuer, Community oder Enterprise, mit dem Query- und dem Index-Dienst | Getestet: Community 7.6.2 (das Image `couchbase/server:community-7.6.2`), und 7.2.4 in der CI |
| PHP 8.0 oder neuer mit `curl` | In jedem PHP, auf dem Exponential läuft; kein Couchbase-SDK |
| Ein Bucket und ein Benutzer mit den Query-Rollen darauf (select, insert, update, delete, manage index, manage scopes) | Der Bucket wird in der Konsole von Couchbase oder mit `couchbase-cli bucket-create` angelegt; Scopes und Collections legt der Treiber an |

**Warum die REST-API des Query-Dienstes und nicht das SDK.** Das PHP-SDK von Couchbase (die PECL-Erweiterung
`couchbase`, 4.x) ist eine C++-Erweiterung über dem C++-SDK von Couchbase, je PHP-Version gebaut; keine Distribution und
kein Hoster bietet sie als Paket an. Der Treiber braucht davon nichts außer SQL++, und der Query-Dienst beantwortet
dasselbe SQL++ über HTTP (`POST /query/service`, JSON), Transaktionen eingeschlossen (`BEGIN WORK` liefert eine `txid`,
die jede Anweisung der Transaktion mitschickt). PHPs curl-Erweiterung genügt (`expCouchbaseQueryService`, ein Handle je
Verbindung, sodass die Verbindung wiederverwendet wird). Was das SDK hinzufügen würde, ist die Key-Value-API: das
Schreiben eines Dokuments über seinen Schlüssel ohne den Query-Dienst, ein paar Millisekunden schneller.

## Eine Datentabellen-Verbindung

Eine Verbindung der Engine `couchbase` ist ein Scope eines Buckets. Legen Sie sie unter **Setup > Datentabellen >
Verbindungen** an oder deklarieren Sie sie:

```ini
[Connection_catalogue]
Name=Produktkatalog
Engine=couchbase
Server=cb1.example.com,cb2.example.com
Port=8093
Database=shop.catalogue
User=exponential
Secret=env:COUCHBASE_PASSWORD
```

`Database` ist `bucket.scope` (ein Bucket allein: sein Scope `_default`). Der Scope muss existieren, oder
`[CouchbaseSettings] AutoCreateScope=enabled`. `Server` nennt die Knoten des Query-Dienstes (ein Knoten, der nicht
antwortet, wird übersprungen); `https://cb1.example.com:18093` oder `SSL=enabled` schaltet TLS ein, `SSLCA` nennt eine
CA-Datei, und `ScanConsistency` überschreibt die gleichnamige Einstellung. **Verbindung testen** meldet Version und Edition
des Servers und die Zahl der Collections.

Alles der Datentabellen-Schicht funktioniert darauf:

```php
$db = expDataConnections::db( 'catalogue' );                                 // ein expCouchbaseDB
$plan = expDataTableSchema::plan( expDataTableSchema::fromDefinition( myItem::definition() ), $db );
expDataTableSchema::apply( $schema, $db );                                    // die Collection, ihre Indizes, die Definition
$item = new myItem( array( 'id' => null, 'sku' => 'A-1' ) );
$item->store();                                                                // id 1, 2, 3 ... aus dem Zähler
$list = myItem::fetchObjectList( myItem::definition(), null, array( 'sku' => 'A-1' ), array( 'id' => 'desc' ), 10 );
```

Eine Tabelle ist eine Collection des Scopes; eine Zeile ist ein JSON-Dokument, dessen Felder die Spalten sind und dessen
Schlüssel der Primärschlüssel der Zeile ist (eine Spalte: ihr Wert, `"17"`; mehrere: ein JSON-Array, `["17","3"]`; eine
Tabelle ohne Schlüssel bekommt eine UUID). Andere Anwendungen können diese Dokumente mit den Werkzeugen und SDKs von
Couchbase lesen und schreiben.

## Einen Scope importieren

```bash
./console exp:couchbase:import --source=catalogue --list
./console exp:couchbase:import --source=catalogue --prefix=cb_                       # der Plan: Tabellen, Spalten, SQL
./console exp:couchbase:import --source=catalogue --prefix=cb_ --apply               # in die Datenbank der Site
./console exp:couchbase:import --server=cb1,cb2 --bucket=travel-sample.inventory --user=reader --secret=env:CB_PASSWORD \
    --connection=archive --mode=replace --apply --format=json
```

Der Befehl liest jede Collection des Scopes (oder `--tables`), legt sie im Ziel über die Datentabellen-Schicht an (die
Datenbank der Site, oder `--connection`) und kopiert die Dokumente seitenweise, in der Reihenfolge ihrer Schlüssel. Eine
Collection, die der Treiber angelegt hat, behält ihre Spalten, Typen und ihren Schlüssel. Jede andere Collection bekommt
die Felder der obersten Ebene einer Stichprobe ihrer Dokumente (`--sample`, 500) und `doc_id`, den Schlüssel des
Dokuments, als Primärschlüssel; die Typen werden wie bei [CouchDB](couchdb-treiber.md#eine-datenbank-importieren)
abgeleitet (ganze Zahlen, `double`, Wahrheitswerte als 1/0, Text, Objekte und Arrays als JSON). Der Befehl nutzt die
Import-Engine von `exp:access:import`: die Modi `create`, `append` und `replace`, die Namen und die Berichte sind dieselben.

## Einstellungen

`site.ini [CouchbaseSettings]`:

| Einstellung | Vorgabe | Was |
|---|---|---|
| `Scope` | leer | Der Scope, wenn `Database` nur den Bucket nennt (leer: `_default`) |
| `ScanConsistency` | `request_plus` | `request_plus`: ein Lesen über einen Index wartet, bis der Index jeden Schreibvorgang davor hat (genau); `not_bounded`: der Index, wie er ist (schnell, kann die letzten Schreibvorgänge verpassen); `auto`: `request_plus`, nachdem diese Verbindung geschrieben hat |
| `TransactionTimeout` | `120s` | Wie lange eine Transaktion höchstens offen bleiben darf |
| `DurabilityLevel` | leer | Der Schreibvorgänge einer Transaktion (die Vorgabe des Servers ist `majority`; `none` auf einem Knoten mit eingerichteten Replikaten) |
| `TLS`, `TLSCAFile`, `TLSVerifyPeer` | `disabled`, leer, `enabled` | HTTPS zum Query-Dienst |
| `ConnectTimeout`, `RequestTimeout` | `5`, `75` | Sekunden |
| `AutoCreateScope` | `disabled` | Einen fehlenden Scope beim Öffnen der Verbindung anlegen |
| `PrimaryIndex` | `enabled` | Einen Primärindex auf jeder Collection, die der Treiber anlegt (Anweisungen, denen kein anderer Index dient, brauchen ihn) |
| `IndexWait` | `120` | Sekunden, die auf die Indizes einer neuen Collection gewartet wird, bis sie online sind |
| `TranslationCache` | `500` | SELECTs, deren SQL++ eine Verbindung behält |

## Wie das SQL beantwortet wird

Die persistenten Klassen und die Datentabellen-Schicht schreiben SQL; der Dialekt des Treibers sind die Formen von MySQL
(`sqlHelperDialect()` ist `couchbase`: `LIMIT`, `ON DUPLICATE KEY UPDATE`, `GROUP_CONCAT`), und `expCouchbaseDialect`
macht aus jeder Anweisung SQL++:

- **Namen** werden in Backticks gesetzt (SQL++ reserviert viel mehr Wörter: `value`, `user`, `path` ...) und klein
  geschrieben. Eine Spalte ohne ihre Tabelle in einer Anweisung über mehrere Tabellen bekommt die Tabelle, die sie hat
  (SQL++ liest einen unqualifizierten Namen dort als Tabelle). Eine Unterabfrage, die einen Tabellennamen der Abfrage um
  sie herum wiederholt, bekommt einen eigenen Namen (SQL++ lehnt die Wiederholung ab).
- **Typen.** SQL++ vergleicht Typen streng (`1 = '1'` ist falsch); MySQL wandelt um. Der Treiber hält die Definition jeder
  Tabelle (in der Collection `exp_couchbase_schema`), sodass ein Text, der mit einer numerischen Spalte verglichen wird,
  zur Zahl wird, und eine Zahl, die mit einer Textspalte verglichen wird, zum Text, in `=`, `IN`, `BETWEEN` und `LIKE`;
  ein `INSERT` oder `UPDATE` speichert jeden Wert im Typ seiner Spalte.
- **`NULL` und MISSING.** Ein `INSERT` schreibt jede Spalte (die Vorgaben der Definition für die, die es weglässt);
  `IS NULL` ist `IS NOT VALUED`, sodass die fehlende Seite eines `LEFT JOIN` wie in SQL NULL ist.
- **Operatoren und Funktionen**: `a & b`, `a | b` sind `BITAND()`, `BITOR()`; `MD5('literal')` berechnet der Treiber
  (SQL++ hat kein MD5); `CONCAT`, `SUBSTRING` (`SUBSTR1`), `IFNULL` und `COALESCE` (`IFMISSINGORNULL`), `CAST`, `IF`,
  `LENGTH`, `UNIX_TIMESTAMP`, `FROM_UNIXTIME`, `GROUP_CONCAT` (`CONCAT2` über `ARRAY_AGG`).
- **SELECT** `*` ist `SELECT t.*`; die Zeilen kommen in der Reihenfolge und mit den Namen der Auswahlliste zurück (SQL++
  sortiert die Felder eines Objekts), ein Ausdruck ohne Namen nach seinem Text benannt, wie MySQL ihn benennt; eine
  Unterabfrage in einem Ausdruck ist `SELECT RAW ...[0]`, nach `IN` `SELECT RAW ...`; `LIMIT o, n` ist `LIMIT n OFFSET o`.
  `COUNT` über eine ganze Collection bekommt einen Filter auf den Schlüssel: die Statistik der Collection, die es sonst
  beantwortet, hinkt den Schreibvorgängen hinterher.
- **Schlüssel.** Ein `WHERE`, das den ganzen Primärschlüssel mit `=` oder `IN` nennt, liest die Dokumente über ihre
  Schlüssel (`USE KEYS`): kein Index, kein Warten auf einen. Alles andere geht über die Indizes, die der Schema-Handler
  angelegt hat (einer je Index der Definition, und ein Primärindex).
- **INSERT** schreibt Dokumente mit ihren Schlüsseln (`INSERT INTO t (KEY, VALUE) VALUES ...`); ein vorhandener Schlüssel
  ist Fehler **1062**; `REPLACE` ist `UPSERT`, `INSERT IGNORE` überspringt ein Duplikat, `ON DUPLICATE KEY UPDATE`
  aktualisiert das Dokument über seinen Schlüssel. `INSERT ... SELECT` ist eine SQL++-Anweisung. **Neue Ids**: die
  hochgezählte Spalte nimmt die nächste Nummer des Zählerdokuments ihrer Tabelle in `exp_couchbase_sequence`
  (`UPDATE ... USE KEYS ... SET v = v + n RETURNING`, atomar, außerhalb der Transaktion, wie `auto_increment` von MySQL);
  eine Id, die eine Anweisung selbst angibt, setzt den Zähler darüber hinaus.
- **UPDATE** und **DELETE** sind die von SQL++; ein `UPDATE`, das eine Schlüsselspalte ändert, verschiebt die Dokumente
  auf ihre neuen Schlüssel.
- **Transaktionen** sind die von Couchbase: `BEGIN WORK`, jede Anweisung mit der `txid`, `COMMIT WORK`, `ROLLBACK WORK`.
  Lesevorgänge in einer Transaktion sehen ihre Schreibvorgänge; andere erst nach dem Commit. DDL und die Id-Zähler laufen
  außerhalb.
- **DDL.** `CREATE TABLE` legt die Collection, einen Primärindex und die Indizes der Definition an (verzögert angelegt und
  zusammen gebaut), wartet, bis sie online sind, und speichert die Definition; `ALTER TABLE ... ADD` gibt jedem Dokument
  das neue Feld mit seiner Vorgabe, `DROP COLUMN` entfernt es, `MODIFY` ändert die Definition. Natives SQL++ lässt sich mit
  `$db->send( $statement, $options )` senden, oder über `query()` hinter einem führenden `/*sqlpp*/`.
- **Abgelehnt** mit Namen, nie falsch beantwortet: `RIGHT` und `FULL JOIN`, `MD5` einer Spalte, `ORDER BY` einer
  Position, `DIV`, `XOR`, ein `UPDATE` oder `DELETE` mehrerer Tabellen, eine unbekannte Funktion:
  `expDBUnsupportedException` innen, `false` (oder `eZDBException`) mit Fehler 1235 außen.

## Was geht und was nicht

Getestet auf Couchbase Server Community 7.6.2 (die Live-Suiten, die Wiedergabe unten) und gegen einen aufzeichnenden
Ersatz (die Suite ohne Datenbank).

| Geht | Geht nicht, oder anders |
|---|---|
| Eine Datentabellen-Verbindung: Test, Collections, persistente Klassen (`store()`, `fetchObject()`, `fetchObjectList()` mit Bedingungen, Sortierung und Limits, `count()`, `removeObject()`) | Eine Site-Datenbank ([warum](#warum-keine-site-auf-couchbase-läuft)) |
| `expDataTableSchema` Plan und Anwenden: anlegen, und ein Upgrade, das Spalten hinzufügt; der Describer (aus der gespeicherten Definition) | Eine Tabelle anzulegen dauert ein bis zwei Minuten (eine Collection und ihre Indizes, gebaut und abgewartet) |
| `expDataTableWriter`: einfügen, aktualisieren, Upsert über jeden Schlüssel, unveränderte Zeilen bleiben unberührt | Ein eindeutiger Index außer dem Schlüssel wird nicht durchgesetzt (Couchbase kennt keinen eindeutigen Index) |
| SELECT mit Joins (Komma-Joins, `JOIN`, `LEFT JOIN`, `USING`), Unterabfragen (`IN`, `EXISTS`, skalar), abgeleiteten Tabellen, `GROUP BY`, `HAVING`, `UNION`, `DISTINCT`, `CASE`, `LIKE`, `BETWEEN`, Bitoperationen, den oben genannten Funktionen | Ein Komma-Join gefolgt von `JOIN ... ON` in einem `FROM` (SQL++ lehnt ihn ab); `RIGHT` und `FULL JOIN`; `MD5` einer Spalte; `ORDER BY` einer Position |
| Echte Transaktionen: Isolation, `ROLLBACK`, Lesevorgänge, die die Schreibvorgänge der Transaktion sehen | `LOCK TABLES` tut nichts (eine Transaktion ist die Sperre); ein Commit vieler Schreibvorgänge dauert lange |
| Neue Ids aus Zählerdokumenten: eindeutig über Verbindungen hinweg, außerhalb der Transaktion wie bei MySQL | Ein zurückgerolltes Einfügen hinterlässt eine Lücke in den Ids, wie bei MySQL |
| Ein doppelter Schlüssel wird abgelehnt (1062); `REPLACE`, `INSERT IGNORE`, `ON DUPLICATE KEY UPDATE`, `INSERT ... SELECT`; ein geänderter Schlüssel verschiebt das Dokument | `ScanConsistency=not_bounded` ist schnell, kann aber die letzten Schreibvorgänge anderer verpassen |
| Text jeder Größe (100 KB getestet), Unicode, Anführungszeichen und Backslashes; Zahlen im Typ ihrer Spalte verglichen und gespeichert | Den Typ einer Spalte ändern ändert die Definition, nicht die gespeicherten Werte |
| Der Import jeder Collection: Tabellen des Treibers mit ihren Definitionen, andere Dokumente mit den Feldern einer Stichprobe und `doc_id` | Felder, die eine Stichprobe nicht getroffen hat, werden nicht importiert (`--sample`) |

### Gemessen

Die Operationen einer Datentabelle auf einer Tabelle mit 5.300 Zeilen, Couchbase Server Community 7.6.2 auf einem Knoten
in einem Container mit zwei Kernen und 3 GB auf dem Datenbankserver der Entwicklung (12 Kerne, während des Laufs eine Last
von 20 bis 50 durch seine eigenen Sites), der Client PHP 8.5 auf demselben Host, `ScanConsistency=request_plus`. Der Knoten
hielt nur diese Tabelle; mit der Site der Wiedergabe unten im selben Bucket (154 Collections mehr, etwa 440 Indizes) kam
derselbe Lauf nicht zu Ende: ein Lesen über einen Index nach den 5.300 Einfügungen kam in 100 s nicht zurück, der Indexer
lag hinter seinen Schreibvorgängen.

| Operation | Couchbase 7.6.2 |
|---|---|
| Verbinden (die Version, der Scope) | 571 ms |
| Eine Tabelle anlegen (die Collection, ein Primärindex, ihre Indizes gebaut und online, die Definition) | 108 s |
| INSERT, 100 Zeilen je Anweisung | 3,2 ms je Zeile |
| INSERT einer Zeile mit neuer Id (der Zähler, dann das Dokument) | 15 ms |
| SELECT über den Primärschlüssel (`USE KEYS`) | 5,4 ms |
| SELECT über eine indizierte Spalte (etwa 170 Zeilen) | 34 ms |
| SELECT über eine Spalte ohne Index (der Primärindex, die ganze Collection) | 1,0 s |
| SELECT ... LIKE, ORDER BY ... LIMIT 10, GROUP BY (die ganze Collection) | 0,9 s, 1,2 s, 1,0 s |
| SELECT COUNT(*) der Tabelle | 34 ms |
| UPDATE über den Primärschlüssel | 26 ms |
| UPDATE über den Schlüssel, dann ein SELECT über einen Index (das Warten auf den Indexer) | 206 ms |
| UPDATE über eine indizierte Spalte (etwa 170 Zeilen) | 348 ms |
| DELETE über den Primärschlüssel | 9,3 ms |
| 100 Änderungen über den Schlüssel in einer Transaktion, zurückgerollt / bestätigt | 0,73 s / 0,98 s |
| Ein Join der Tabelle mit sich selbst über den Schlüssel, 50 Zeilen | 1,3 s |

Lesen über den Schlüssel und über einen Index dauert Millisekunden; ein Lesen, das ein Schreiben davor sehen muss, wartet
etwa 200 ms auf den Indexer; was eine ganze Collection braucht, kostet bei 5.300 Dokumenten etwa eine Sekunde. Für eine
Datentabelle ist das brauchbar; geben Sie ihren Bedingungen Indizes, und halten Sie Schreibvorgänge und die Lesevorgänge,
die davon abhängen, in einer Transaktion.

## Warum keine Site auf Couchbase läuft

Couchbase war der ernsthafte Kandidat: SQL++ hat die Joins, Unterabfragen, `GROUP BY` und Transaktionen, die der Kernel
nutzt, und die Community-Edition kann sie alle (Komma-Joins, `LEFT JOIN`, korrelierte `EXISTS`, `BEGIN WORK` mit
Isolation, alles auf 7.6.2 gemessen). Also wurde der Kernel gefragt. Eine Demo-Site (das Paket `sevenx_multisite`, 291
Objekte, 154 Tabellen), auf SQLite installiert, wurde über den Treiber selbst in einen Scope kopiert (sein `CREATE TABLE`
und `INSERT`; der Suchindex mit 104.943 Zeilen blieb außen vor), und die SELECTs von 13 aufgezeichneten Seitenaufrufen bei
geleertem View-Cache (die Startseite und fünf Seiten der Site, eine Suche, das Admin-Login, zwei Knotenansichten, das
Dashboard, die Klassenliste und ein Benutzer) wurden auf beiden wiedergegeben, übersetzt von `expCouchbaseDialect`:

| Gemessen | |
|---|---|
| Wiedergegebene SELECTs (SQLites eigener Katalog nicht mitgezählt) | 1.778 |
| Beantwortet | 1.774 (4 von Couchbase abgelehnt: ein Komma-Join gefolgt von einem `INNER JOIN` in einem `FROM`) |
| **Dieselben Zeilen wie SQLite** | **1.751 (98,5 %)**; von den 23 übrigen gehen 14 auf die Wiedergabe selbst zurück (die einfache SQLite-Datei hat kein `MD5()`, eine `rowid` von SQLite), 9 sind Joins über Sprachmasken, die noch anders antworten |
| Zeit auf SQLite / auf Couchbase | **1,9 s / 282 s (147-mal)**: 0,15 s gegen 21,7 s je Seitenaufruf |
| Ein SELECT auf Couchbase im Mittel | 160 ms (ein Schlüsselzugriff etwa 8 ms; über einen Index 20 bis 60 ms; ein Join mehrerer Collections bis zu Sekunden) |
| Die Tabellen der Site angelegt (154 Collections, je ein Primärindex und 436 Indizes insgesamt) | **etwa 1 h 40 min**, 20 bis 75 s je Tabelle, langsamer mit ihrer Zahl; einmal antwortete der Query-Dienst 75 s lang nicht |
| Die 20.443 Zeilen der Site geschrieben | 108 s (5,3 ms je Zeile, 200 Zeilen je Anweisung) |

- **Das SQL lässt sich beantworten.** 98,5 % der Lesevorgänge des Kernels kamen in der Wiedergabe richtig zurück,
  nach einer Runde von Korrekturen, die die Wiedergabe zeigte (eine unqualifizierte Spalte einer Unterabfrage, die SQL++
  als Feld der äußeren Zeile las). Der Rest sind Randfälle, die sich schließen ließen.
- **Die Geschwindigkeit nicht.** Schon die Lesevorgänge brauchen das 147-fache von SQLite: jede Anweisung ist eine
  HTTP-Anfrage, die der Query-Dienst zerlegt, plant und gegen den Index-Dienst ausführt. Ein Seitenaufruf, den SQLite in
  0,15 s beantwortet, brauchte 21,7 s.
- **Schreibvorgänge sind schlimmer.** Ein Index von Couchbase wird nach dem Schreiben aktualisiert. Ein Lesen, das die
  Schreibvorgänge davor sehen muss (`scan_consistency=request_plus`, was das Lesen-nach-Schreiben des Kernels braucht),
  wartet auf den Indexer: hier etwa **210 ms** nach jedem Schreiben, gegen 10 bis 14 ms für das Schreiben selbst. In einer
  Transaktion sehen die Lesevorgänge ihre Schreibvorgänge sofort, aber ein Commit von 50 Einfügungen dauerte etwa
  **1 s**. Das Veröffentlichen eines Objekts schreibt Dutzende Zeilen und liest sie zurück.
- **Das Schema ist schwer für einen kleinen Knoten.** Die 154 Tabellen des Kernels brauchen 154 Collections und etwa 440
  Indizes; auf einem Knoten mit 3 GB wurden Indexer und Query-Dienst langsamer, bis über eine Minute je Tabelle, und
  belegten den ganzen Speicher. Eine Site-Installation verbrächte fast zwei Stunden mit dem Anlegen ihrer Tabellen vor der
  ersten Zeile.

Eine ganze Site-Installation auf Couchbase wurde deshalb nicht versucht: die Messungen oben bringen schon jeden
Seitenaufruf auf zig Sekunden. Couchbase bleibt, wo es passt: eine Datentabellen-Verbindung und eine Importquelle.

## Wie er gebaut ist

| Datei | Was |
|---|---|
| `lib/ezdb/classes/expcouchbasedb.php` | `expCouchbaseDB`: die Verbindung, der Katalog der Definitionen, SELECT, INSERT mit Schlüsseln und Ids, UPDATE mit verschobenen Schlüsseln, DDL, Transaktionen |
| `lib/ezdb/classes/expcouchbasedialect.php` | `expCouchbaseDialect`: das SQL als SQL++ (Namen, Typen, Operatoren, Funktionen, Unterabfragen, Schlüsselzugriffe), INSERT und DDL zerlegt |
| `lib/ezdb/classes/expcouchbasequeryservice.php` | `expCouchbaseQueryService`: die REST-API des Query-Dienstes über curl |
| `lib/ezdb/classes/expcouchsqllexer.php` | `expCouchSQLLexer`: die Token einer Anweisung (gemeinsam mit dem CouchDB-Treiber) |
| `lib/ezdbschema/classes/expcouchbaseschema.php` | `expCouchbaseSchema` (`dbschema.ini`: `couchbase`) |
| `kernel/classes/couch/expcouchbasereader.php` | `expCouchbaseReader`: ein Scope gelesen für die Import-Engine von `expAccessImport` |
| `kernel/private/classes/commands/expcouchbaseimport.php`, `bin/php/expcouchbaseimport.php` | `exp:couchbase:import` |

Registriert in `site.ini [DatabaseSettings] ImplementationAlias[couchbase]`; `dbschema.ini`; `datatable.ini
[ConnectionSettings] Engines[]` (`couchbase`); `expDataConnections` (Engine-Art `document`), `expDataTableSchema` (Anlegen
und Upgrade), `expDataTableDescriber`, `expKernelTableSchema::tables()`; `expDBSQLHelpers` (Dialekt `couchbase`).

## Tests

- `tests/tests/lib/ezdb/couch/` und `tests/tests/kernel/classes/couch/` (Testsuite `couch`, ohne Server): der Lexer und der
  Dialekt (`expCouchbaseDialectTest`), der Treiber über einem aufzeichnenden Ersatz für den Query-Dienst
  (`expCouchbaseDBTest`: Anfragen, Zeilen, Dokumente und Schlüssel, Ids, Upserts, verschobene Schlüssel, Transaktionen,
  DDL), der Leser.
- `.../live/` (Testsuite `couch-live`): der Treiber (`expCouchLiveTest`), die Datentabellen-Verbindung
  (`expCouchDataTableLiveTest`) und der Import (`expCouchImportLiveTest`), dieselben Szenarien wie auf CouchDB. Server:
  `EXP_TEST_COUCHBASE_HOST`, `_PORT`, `_USER`, `_PASSWORD`, `_BUCKET`; ohne ihn werden die Tests übersprungen.
- CI: der Job „Couchbase and CouchDB" führt beide Suiten auf PHP 8.5 und 8.1 gegen Couchbase Server Community 7.6.2 und
  7.2.4 in Containern aus.

Verwandt: [Apache-CouchDB-Treiber](couchdb-treiber.md), [Datenbankunterstützung](../../hilfe/datenbankunterstuetzung.md),
[Eine Datenbank wählen](../../hilfe/datenbank-waehlen.md), [Datentabellen](datentabellen.md),
[MongoDB als Datenbank](mongodb-datenbankunterstuetzung.md), [MS-Access-Unterstützung](ms-access-unterstuetzung.md) (die
Import-Engine).
