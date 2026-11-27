# Spaltenorientierte Datenbanken: MonetDB und ClickHouse

Diese Seite ist für Administratoren, die eine Site oder Tabellen einer Site in einer spaltenorientierten Datenbank halten
wollen, und für Entwickler, die an den Treibern arbeiten. Exponential 6.0.15 hat für zwei spaltenorientierte
Datenbankserver je einen Treiber, beide in `lib/ezdb/classes/`, beide ohne eigene PHP-Erweiterung:

| Datenbank | `DatabaseImplementation` | Treiber | Erreicht über | Erreichte Stufe | Live getestet |
|---|---|---|---|---|---|
| MonetDB | `monetdb` | `expMonetDBDB` | MonetDBs Protokoll MAPI, in reinem PHP (`expMonetDBMapi`) | **vollständige Site-Datenbank** | ja: Dec2025-SP5 (11.55.11) auf dem Datenbankknoten des Projekts, Dec2023-SP4 und Dec2025-SP5 in der CI |
| ClickHouse | `clickhouse` | `expClickHouseDB` | ClickHouses HTTP-Schnittstelle (`expClickHouseHttp`, curl oder PHPs HTTP-Stream) | **Datentabellen-Verbindung und Importquelle** | ja: 25.8.33.6 (LTS) auf dem Datenbankknoten des Projekts, 24.8 und 25.8 in der CI |

Die Stufen bedeuten dasselbe wie bei den anderen Treibern: eine **vollständige Site-Datenbank** hält die Tabellen des
Kernels, die Inhalte eines Site-Pakets und die Tabellen der Erweiterungen, installiert mit `exp:install`, und die Site
läuft daraus. Eine **Datentabellen-Verbindung** hält die Tabellen von Datentabellen-Klassen (`datatable.ini`) neben einer
Site auf einer anderen Datenbank. Eine **Importquelle** ist eine Datenbank, aus der Zeilen in die Site gelesen werden.

Beide sind spaltenorientiert: jede Spalte wird für sich gespeichert, komprimiert und in großen Blöcken gelesen. Das macht
Durchläufe und Aggregate über viele Zeilen schnell und die Arbeit an einzelnen Zeilen vergleichsweise langsam. Worin sie
sich unterscheiden: MonetDB ist eine vollständige SQL-Datenbank mit Transaktionen, Sequenzen, Primärschlüsseln und
durchgesetzten Unique-Constraints; ClickHouse hat nichts davon, und eine Site braucht alle vier.

## Inhalt

1. [MonetDB](#monetdb)
   1. [Voraussetzungen und eine Site installieren](#voraussetzungen-und-eine-site-installieren)
   2. [Einstellungen](#einstellungen)
   3. [Gleichzeitigkeit: immer nur ein Schreiber](#gleichzeitigkeit-immer-nur-ein-schreiber)
   4. [Das SQL](#das-sql)
   5. [Was geht und was nicht](#was-geht-und-was-nicht)
2. [ClickHouse](#clickhouse)
   1. [Warum keine Site-Datenbank](#warum-keine-site-datenbank)
   2. [Wie PHP ClickHouse erreicht](#wie-php-clickhouse-erreicht)
   3. [Eine Datentabellen-Verbindung](#eine-datentabellen-verbindung)
   4. [Was der Treiber tut](#was-der-treiber-tut)
   5. [Aus ClickHouse importieren](#aus-clickhouse-importieren)
   6. [Was geht und was nicht](#was-geht-und-was-nicht-1)
3. [Gemessen](#gemessen)
4. [Wie sie aufgebaut sind](#wie-sie-aufgebaut-sind)
5. [Tests](#tests)

## MonetDB

MonetDB (monetdb.org) ist ein spaltenorientierter SQL-Datenbankserver mit Transaktionen, Sequenzen, Primär- und
Fremdschlüsseln und `CREATE SEQUENCE`; sein SQL ist Standard-SQL nahe an dem von PostgreSQL. Der Treiber `expMonetDBDB`
betreibt eine ganze Site darauf.

### Voraussetzungen und eine Site installieren

| Was | Woher |
|---|---|
| Ein MonetDB-Server: `monetdbd` mit einer Datenbank | Pakete für Debian, Ubuntu, RHEL und Windows von monetdb.org, oder das Image `monetdb/monetdb`. Getestet: Dec2025-SP5 (11.55.11) und Dec2023-SP4 |
| PHP | nichts außer PHP selbst: der Treiber spricht MAPI über einen TCP- oder Unix-Socket (`stream_socket_client`) |

Warum reines PHP: MonetDBs eigener PHP-Client wurde für PHP 5 geschrieben und wird nicht mehr gepflegt, es gibt keine
PECL-Erweiterung, und sein ODBC-Treiber brächte einen Treibermanager und `libMonetODBC` auf jeden Host. MAPI ist ein
kleines, dokumentiertes Protokoll (Blöcke von höchstens 8190 Bytes, eine Herausforderung, die mit einem gesalzenen
SHA-512-Hash beantwortet wird, Ergebnisse als Textzeilen), das `expMonetDBMapi` in etwa 500 Zeilen umsetzt.

Die Datenbank wird auf dem Server angelegt, nicht über SQL. Geben Sie der Site einen Benutzer mit einem eigenen Schema
(sonst landen die Tabellen im Schema `sys` neben MonetDBs eigenen):

```
monetdb create exponential && monetdb release exponential
mclient -u monetdb -d exponential -s "CREATE USER \"exponential\" WITH PASSWORD 'geheim' NAME 'Exponential' SCHEMA \"sys\";
  CREATE SCHEMA \"exponential\" AUTHORIZATION \"exponential\"; ALTER USER \"exponential\" SET SCHEMA \"exponential\""
```

Dann installieren:

```
EXP_INSTALL_DB_PASSWORD=geheim php bin/php/install.php --db=monetdb --db-host=127.0.0.1 --db-user=exponential \
    --db-name=exponential --apply
```

Mit `kickstart.ini` ist die Datenbank `[database_choice] Type=monetdb`. Der Web-Einrichtungsassistent führt MonetDB
nicht auf: er bietet die Datenbanken an, deren PHP-Erweiterung er findet, und MonetDB braucht keine. „Vorhandene Daten
entfernen" (die Vorgabe) löscht alle Tabellen, Sichten und Sequenzen im Schema des Benutzers. Der Installer legt die
Tabellen des Kernels aus `share/db_schema.dba` über `expMonetDBSchema` an, fügt die Daten ein, startet die Sequenzen hinter
den eingefügten IDs neu und schreibt `DatabaseImplementation=monetdb`.

### Einstellungen

```ini
[DatabaseSettings]
DatabaseImplementation=monetdb
Server=127.0.0.1
Port=50000
User=exponential
Password=geheim
Database=exponential

[MonetDBSettings]
Schema=
WriteLock=enabled
```

| Einstellung in `[MonetDBSettings]` | Vorgabe | Bedeutung |
|---|---|---|
| `Schema` | leer | das Schema der Tabellen der Site; leer: das Standardschema des Benutzers. Gesetzt: `SET SCHEMA` bei jeder Verbindung |
| `ConnectTimeout` | `10` | Sekunden zum Verbinden und Anmelden |
| `ReadTimeout` | `300` | Sekunden, die eine Anweisung dauern darf |
| `WriteLock` | `enabled` | immer nur ein Schreiber (unten); `disabled` überlässt Konflikte MonetDB |
| `LockWait` | leer | Sekunden, die ein Schreiber auf die Schreibsperre wartet; leer: `LockWaitWeb` in einer Web-Anfrage, `LockWaitCommandLine` auf der Kommandozeile |
| `LockWaitWeb` | `10` | |
| `LockWaitCommandLine` | `120` | |
| `ConflictRetries` | `3` | wie oft eine einzelne Anweisung (ohne Schreibsperre) oder `retryTransaction()` nach einem Konflikt erneut versucht wird |

`Server` darf der Pfad eines Unix-Sockets sein (`/tmp/.s.monetdb.50000`). Ein `monetdbd`, der die Verbindung zum Server
der Datenbank weiterreicht oder umleitet, wird befolgt.

### Gleichzeitigkeit: immer nur ein Schreiber

MonetDBs Transaktionen sind optimistisch: eine Transaktion liest einen Schnappschuss und schreibt ohne Sperren, und ein
Konflikt wird erkannt, wenn er auftritt. MonetDB unterscheidet Konflikte grob; das wurde mit zwei Verbindungen gemessen:

| Zwei offene Transaktionen | MonetDB |
|---|---|
| ändern dieselbe Zeile einer Tabelle | das zweite `UPDATE` scheitert sofort: „Update failed due to conflict with another transaction" |
| ändern **verschiedene** Zeilen derselben Tabelle | das zweite `UPDATE` scheitert ebenfalls sofort |
| fügen verschiedene Zeilen in eine Tabelle mit Primärschlüssel ein | beide Einfügungen gelingen; das zweite `COMMIT` scheitert: „transaction is aborted because of concurrency conflicts" |
| eine schreibt, die andere liest | kein Konflikt: die lesende sieht ihren Schnappschuss |

Der Kernel schreibt bei jeder Anfrage in dieselben Tabellen (Sitzungen des Datenbank-Handlers, Zähler, die
Inhaltstabellen), deshalb würde von zwei Redakteuren, die zugleich speichern, eine der beiden Anfragen scheitern. Der
Treiber lässt darum immer nur einen Schreiber schreiben, wie es DuckDBs Dateisperre tut (`WriteLock=enabled`, die
Vorgabe):

- Eine Transaktion beginnt damit, die Zeile `write` der Tabelle `monetdb_write_lock` zu ändern (beim ersten Schreiben im
  Schema der Site angelegt). Solange eine andere offene Transaktion sie geändert hat, lehnt MonetDB die Änderung sofort
  ab; der Treiber rollt zurück, wartet (10 ms, verdoppelt bis 250 ms) und versucht es erneut, `LockWait` Sekunden lang.
  Dann scheitert die Anweisung mit der Fehlernummer `55P03` (Sperre nicht verfügbar).
- Ein Schreiben außerhalb einer Transaktion (ein `INSERT`, `UPDATE`, `DELETE` oder eine DDL-Anweisung für sich) wird in
  einer solchen eigenen Transaktion geschickt, so dass es auf einen laufenden Schreiber wartet, statt zu scheitern.
- Lesen wartet nie und nimmt nie die Sperre.
- `lock()` und `unlock()` sind die Schreibsperre; `LOCK TABLE` und `SELECT ... FOR UPDATE` werden nicht geschickt.
- `retryTransaction( function ( $db ) { ... } )` führt eine Transaktion aus und wiederholt sie nach einem Konflikt
  (`40001`) oder einer abgelaufenen Sperrwartezeit (`55P03`), für Code, der schreibt, während ein anderes Programm ohne
  die Sperre in dieselbe Datenbank schreibt.

| Situation | Was geschieht |
|---|---|
| Besucher lesen Seiten | parallel; MonetDB beantwortet jeden Leser aus seinem Schnappschuss |
| Eine Anfrage, die schreibt | sie wartet auf einen laufenden Schreiber, bis zu `LockWait`, dann schreibt sie |
| Zwei Redakteure veröffentlichen zugleich | der zweite wartet, bis die erste Transaktion endet |
| Ein langer Kommandozeilen-Auftrag (ein Import) | jedes Schreiben einer Web-Anfrage wartet, solange die Transaktion des Auftrags offen ist; halten Sie Transaktionen kurz oder lassen Sie Aufträge laufen, wenn die Site ruhig ist |
| Mehrere Webserver | möglich: die Sperre ist eine Zeile in der Datenbank, keine Datei |

`WriteLock=disabled` schickt jede Anweisung, wie sie ist (eine einzelne Anweisung mit Konflikt wird `ConflictRetries`-mal
erneut versucht). Nutzen Sie das nur, wo ein einziger Prozess schreibt, etwa beim Bauen einer Site, die danach nur
gelesen ausgeliefert wird.

### Das SQL

`databaseName()` ist `monetdb`, und die portablen Helfer haben einen Dialekt `monetdb`: `LIMIT n OFFSET m` (ein Offset
allein ohne `LIMIT`), `MERGE INTO` für Upserts, `GROUP_CONCAT( ... ORDER BY ...)`, `epoch()` für UNIX-Zeitstempel,
`SELECT ... FOR UPDATE` weggelassen. `expMonetDBDialect` ändert eine Anweisung unterwegs nur, wo MonetDB abweicht:

| Der Kernel schreibt | MonetDB bekommt | Warum |
|---|---|---|
| `` `name` `` | `"name"` | |
| `LIMIT 20, 10` | `LIMIT 10 OFFSET 20` | |
| `SELECT ... FOR UPDATE` (`FOR SHARE`, `LOCK IN SHARE MODE`) | das `SELECT` ohne | keine Zeilensperren; stattdessen die Schreibsperre |
| `LOCK TABLE ...`, `UNLOCK TABLES` | nichts | stattdessen die Schreibsperre |
| `day` in einer Anweisung auf `ezgeneral_digest_user_settings`, `records` auf `expaudit_file`, `chain` und `snapshot` auf `expai_chain_run`, `details` auf `expai_probe_result`, `next` auf `sqliimport_scheduled` | `"day"`, `"records"`, `"chain"`, `"snapshot"`, `"details"`, `"next"` | die Spalten des Kernels und seiner Erweiterungen, die in MonetDB reservierte Wörter sind (`sys.keywords` nennt `details` und `snapshot` nicht); `NEXT VALUE FOR` bleibt, wie es ist |
| `DROP TABLE` | die Tabelle, dann die Sequenzen ihrer Serienspalten, die sonst nichts liest | in MonetDB überlebt die Sequenz einer Serienspalte ihre Tabelle, und die Tabelle ließ sich nicht wieder anlegen |

Zeichenketten-Literale und Namen in Anführungszeichen werden nie verändert. MonetDB liest C-Escapes in
Zeichenketten-Literalen (`'a\nb'` ist ein Zeilenumbruch), deshalb verdoppelt `escapeString()` einen Backslash wie ein
Anführungszeichen. Werte kommen als Text zurück, wie von MySQL und PostgreSQL: `BOOLEAN` als 1 oder 0, `TIMESTAMP` als
`Y-m-d H:i:s`. Neue IDs kommen aus der Sequenz der Spalte `<tabelle>_<spalte>_seq` (`DEFAULT NEXT VALUE FOR`); MonetDB
meldet die ID mit dem Einfügen, und `lastSerialID()` liefert die, die das letzte Einfügen in die Tabelle bekommen hat.
Fehlernummern sind der SQLSTATE, den PostgreSQL für denselben Fehler gibt (`23505` doppelter Schlüssel, `42P01` fehlende
Tabelle, `42703` fehlende Spalte, `40001` Konflikt, `55P03` Schreibsperre), so dass Code, der einen doppelten Schlüssel
von anderen Fehlern unterscheidet, unverändert funktioniert.

Der Schema-Handler `expMonetDBSchema` schreibt die Typen von PostgreSQL mit diesen Unterschieden: der Primärschlüssel
steht in `CREATE TABLE`; ein Unique-Index ist ein `UNIQUE`-Constraint (MonetDB nimmt `CREATE UNIQUE INDEX` an, **setzt
ihn aber nicht durch**, gemessen); Vorgabewerte sind einfache Literale (keine `::`-Casts); `BLOB` für binäre Spalten;
Indexnamen sind je Schema eindeutig, ein Name, den eine andere Tabelle schon hat, wird deshalb zu `<tabelle>__<index>`.
MonetDB kann den Typ einer Spalte nicht an Ort und Stelle ändern (kein `ALTER ... TYPE`): der Handler kopiert die Spalte
in eine neue des neuen Typs, löscht die alte und benennt die neue um. `kernel/sql/monetdb/kernel_schema.sql` ist das
von ihm geschriebene Kernel-Schema.

### Was geht und was nicht

Gemessen am 26.11.2026 mit den Tests unten auf MonetDB Dec2025-SP5 (11.55.11; PHP 8.5, Linux x86-64, der
Datenbankknoten des Projekts bei einer Last von 35 bis 40 durch andere Arbeit), und eine ganze Site, installiert mit
`exp:install --db=monetdb`.

| Bereich | MonetDB | Hinweise |
|---|---|---|
| Kernel-Schema (153 Tabellen) und saubere Daten | geht | `kernel/sql/monetdb/kernel_schema.sql` und `cleandata.sql`: 17 bis 31 s; über den Schema-Handler aus `share/db_schema.dba` und `db_data.dba`: 7 bis 16 s |
| Installation mit `exp:install` / Kickstarter, Paket und Erweiterungen | geht | [die Installation](#die-installation-auf-monetdb): 29 Minuten auf einem ausgelasteten Host |
| Seiten lesen, der Admin | geht | Startseite und Admin-Anmeldung 200; mit View-Cache 0,05 bis 0,09 s |
| Web-Einrichtungsassistent | geht nicht | nicht angeboten (keine PHP-Erweiterung zu erkennen); nehmen Sie `exp:install` |
| Transaktionen, verschachteltes `begin()`/`commit()`, Rollback | geht | eine gescheiterte Anweisung beendet MonetDBs Transaktion; der Kernel rollt zurück wie auf PostgreSQL |
| Mehrere Schreiber zugleich | geht, nacheinander | die Schreibsperre; ohne sie lässt MonetDB den zweiten von zwei Schreibern einer Tabelle scheitern |
| Mehrere Leser zugleich, Leser während eines Schreibens | geht | Schnappschüsse |
| Neue IDs, beim Einfügen gegebene IDs, neu gestartete Sequenzen | geht | `ALTER SEQUENCE ... RESTART WITH` nach den Daten |
| Unicode-Text, lange Texte, `NULL` gegenüber dem Text `NULL` | geht | |
| Primärschlüssel und Unique-Constraints | geht, durchgesetzt | `CREATE UNIQUE INDEX` allein würde nicht durchgesetzt: der Handler schreibt Constraints |
| Doppelte Schlüssel, fehlende Tabellen (Fehlernummern) | geht | PostgreSQLs SQLSTATE |
| Portable Helfer: Blättern, Upsert (`MERGE`), Gruppen-Verkettung, Unix-Zeit | geht | |
| `SELECT ... FOR UPDATE`, `LOCK TABLE` | geht | weggelassen: stattdessen die Schreibsperre |
| `INSERT INTO t DEFAULT VALUES` | geht | der ID-Zähler der URL-Aliase nutzt es |
| Schema-Upgrades: `ADD COLUMN`, `RENAME`, `DROP COLUMN` | geht | |
| Schema-Upgrades: Typ einer Spalte geändert | geht, durch Kopieren | scheitert bei einer Spalte eines Schlüssels oder Index: diese vorher löschen und danach neu anlegen |
| Datentabellen-Verbindung (Engine `monetdb`) | geht | unten |
| DFS-Cluster-Backend | geht nicht | kein `expDFSFileHandler`-Backend für MonetDB; Dateien auf einem Server oder NFS |
| Lesereplikat (`UseSlaveServer`) | geht nicht | ignoriert; MonetDB repliziert mit eigenen Werkzeugen |
| Upgrade-Sicherungspunkte (der Datenbank-Dump vor einem Upgrade) | geht nicht | selbst mit `msqldump` sichern und mit `--no-backup` ausführen |
| PostgreSQLs Volltextspalte des Audit-Index | geht nicht | die Audit-Suche nutzt `LIKE`, wie auf SQLite |
| Importquelle | geht über eine Datentabellen-Verbindung | eine Datentabellen-Synchronisierung liest die Zeilen mit persistenten Klassen; die SQL-Quelle von SQLI Import führt MonetDB nicht |
| Jede Anweisung | geht, langsam | 4 ms für einen Zugriff über den Schlüssel, wo MySQL und PostgreSQL Bruchteile einer Millisekunde brauchen (unten). Lassen Sie den View-Cache an |

#### Die Installation auf MonetDB

Installiert am 26.11.2026 mit `exp:install --db=monetdb` (Paket `sevenx_multisite`, eng-US) auf MonetDB Dec2025-SP5 in
einem Container auf dem Datenbankknoten des Projekts, bei einer Last von 35 bis 40 durch andere Aufträge:

| Was | Ergebnis |
|---|---|
| Die Installation | **INSTALLIERT**: 16 Schritte, 291 Inhaltsobjekte, Prüfungen 9 bestanden und 0 gescheitert, 3 Migrationen angewendet; 29 Minuten (1.741 s) |
| Fehler im Installationsprotokoll | 398, alle von Erweiterungen, die das Paket nennt und die im Testbaum fehlten (`sevenx_themes_media`, `eztags`, `explayouts` und ihre Tabellen); keiner vom Treiber |
| Startseite | 200: 2,6 s mit allen Caches leer (Templates übersetzt), 0,05 bis 0,09 s mit gefülltem View-Cache |
| Seite der Admin-Anmeldung | 200 in 0,3 bis 0,5 s |

Die Zeit ist MonetDBs Kosten je Anweisung (oben), nicht die Schreibsperre: das Paket wird in einer langen Transaktion
installiert, die die Sperre einmal nimmt. Die Seiten lieferte PHPs eingebauter Server ohne OPcache aus.

#### Eine Datentabellen-Verbindung

```ini
[Connection_reports]
Engine=monetdb
Server=monetdb.example.org
Port=50000
Database=analytics
User=reports
Secret=monetdb_reports
Options[schema]=reports
```

`expDataConnections::test()` öffnet sie, `expDataTableSchema` legt Tabellen darauf über `expMonetDBSchema` an und
aktualisiert sie, der Describer liest ihre Spalten aus `information_schema.columns`.

## ClickHouse

ClickHouse (clickhouse.com) ist eine spaltenorientierte Datenbank für Auswertungen: Milliarden von Zeilen, in großen
Stapeln eingefügt, mit Aggregaten gelesen. Der Treiber `expClickHouseDB` macht sie zu einer Datentabellen-Verbindung und
einer Importquelle.

### Warum keine Site-Datenbank

Eine Site kann nicht auf ClickHouse laufen. Das wurde gemessen entschieden, nicht angenommen (`expClickHouseLiveTest`,
CL-02 bis CL-05):

| Der Kernel verlässt sich auf | ClickHouse | Gemessen |
|---|---|---|
| Eindeutige Schlüssel: eine zweite Zeile mit demselben Schlüssel scheitert | der Primärschlüssel ordnet nur die Daten; nichts wird durchgesetzt | zwei Einfügungen desselben Schlüssels und desselben „eindeutigen" Namens: beide Zeilen bleiben |
| Transaktionen über Anweisungen: ein Veröffentlichen schreibt Dutzende Zeilen oder keine | keine (`BEGIN` antwortet „Transactions are not supported"); jedes `INSERT` ist für sich atomar | `begin()`, einfügen, `rollback()`: die Zeile bleibt |
| Neue IDs aus der Datenbank (`AUTO_INCREMENT`, Sequenzen) | keine; `generateSerialID()` braucht ClickHouse Keeper | der Treiber nimmt selbst das Maximum plus eins |
| Billige `UPDATE` und `DELETE` einzelner Zeilen | Zeilen werden geändert, indem Teile einer Tabelle neu geschrieben werden: ein leichtgewichtiges `UPDATE` oder `DELETE` schreibt einen Patch oder eine Maske | 16 bis 122 ms je geänderter Zeile, gegenüber 4 bis 16 ms auf MonetDB |
| Billiges Lesen einzelner Zeilen | ein Zugriff über den Schlüssel liest ein Granulat (8.192 Zeilen) | 30 bis 48 ms je über den Schlüssel gelesener Zeile |

Das Schema des Kernels und seine Daten lassen sich über den Schema-Handler laden (153 MergeTree-Tabellen in 4,6 bis 8 s),
der Handler ist also nicht, was eine Site verhindert: eine Seite schreibt und liest Hunderte einzelner Zeilen, und ein
verlorener eindeutiger Schlüssel oder eine halb geschriebene Veröffentlichung bliebe unbemerkt.

### Wie PHP ClickHouse erreicht

ClickHouse spricht vier Protokolle. Die Wahl wurde gegen den Server gemessen:

| Weg | Ergebnis |
|---|---|
| **HTTP-Schnittstelle (Port 8123, 8443 mit TLS)** | **gewählt**: ClickHouses eigenes Protokoll für jeden Client außer dem in C++; Einstellungen je Anweisung (`mutations_sync`, `max_execution_time`), Fehlernummern in einem Header, typisierte Ergebnisse; braucht curl oder nichts (PHPs HTTP-Stream) |
| MySQL-Protokoll (Port 9004), über `mysqli` | geht für Abfragen; keine Transaktionen, kein `LAST_INSERT_ID()`, keine Einstellungen je Anweisung; wäre ein `eZMySQLiDB`, das kein MySQL ist |
| PostgreSQL-Protokoll (Port 9005), über `pgsql` | verbindet sich und beantwortet einfache Abfragen; gebundene Parameter scheitern (`pg_query_params()`: „Unknown expression identifier") |
| Natives TCP-Protokoll (Port 9000) | keine gepflegte PHP-Erweiterung (SeasClick wird nicht gepflegt); ein Client in PHP müsste ClickHouses Spaltenblöcke nachbauen |

`expClickHouseHttp` schickt jede Anweisung als Rumpf eines POST, Benutzer und Passwort in den Headern
`X-ClickHouse-User` und `X-ClickHouse-Key` (nie in der Adresse), die Datenbank in `X-ClickHouse-Database`, und liest
Ergebnisse in `TabSeparatedWithNamesAndTypes` (`default_format`), so dass eine Anweisung geschickt wird, wie sie ist.

### Eine Datentabellen-Verbindung

```ini
[Connection_events]
Engine=clickhouse
Server=clickhouse.example.org
Port=8443
Database=analytics
User=exponential
Secret=clickhouse_password
Options[secure]=enabled
```

| Option | Bedeutung |
|---|---|
| `secure` | `enabled`: HTTPS (Port 8443, wenn keiner angegeben ist) |
| `verify_tls` | `disabled`: jedes Zertifikat annehmen (ein Testserver) |

Die Vorgaben sind `[ClickHouseSettings]` der `site.ini` (`Secure`, `VerifyTLS`, `ConnectTimeout`, `Timeout`,
`MutationsSync`, `Settings[]`, die mit jeder Anweisung geschickt werden). `expDataTableSchema` legt die Tabelle einer
Klasse über `expClickHouseSchema` an:

- `ENGINE = MergeTree ORDER BY (<Primärschlüssel>)` (`tuple()` ohne einen), mit den Blocknummer-Spalten, die ein
  leichtgewichtiges `UPDATE` braucht.
- Eine Spalte ohne `NOT NULL` ist `Nullable(...)`; Typen: `Int32`, `Int64`, `Int16`, `String` (varchar, char und text
  gleichermaßen), `Float32`, `Float64`, `Decimal(p, s)`, `DateTime`, `Date`.
- Ein nicht eindeutiger Index ist ein Data-Skipping-Index (`minmax`); ein eindeutiger Index wird mit einem Hinweis in der
  Debug-Ausgabe **weggelassen**, da ClickHouse ihn nicht durchsetzen würde.
- Eine Serial-Spalte ist `Int32` mit dem Kommentar `auto_increment`; ihre Werte kommen aus `nextSerialID()`.

Schreiben Sie Zeilen von Datentabellen in Blöcken (`expDataTableWriter`, `chunk`): ein `INSERT` von tausend Zeilen kostet
etwa so viel wie ein `INSERT` einer Zeile.

### Was der Treiber tut

| Der Code tut | ClickHouse bekommt |
|---|---|
| `begin()`, `commit()`, `rollback()` | nichts; ein Rollback nach einem Schreiben protokolliert eine Warnung (nichts lässt sich rückgängig machen) |
| `UPDATE t SET ... WHERE ...` | ein leichtgewichtiges `UPDATE` (ClickHouse 25.7 und später, eine Tabelle mit Blocknummern); abgelehnt, die Mutation `ALTER TABLE t UPDATE ... WHERE ...`, auf die gewartet wird (`MutationsSync`) |
| `DELETE FROM t WHERE ...` | ein leichtgewichtiges `DELETE` |
| `DELETE FROM t` | `TRUNCATE TABLE t` |
| `SELECT ... FOR UPDATE`, `LOCK TABLE` | das `SELECT` ohne; nichts |
| ein Einfügen in eine Klasse mit Auto-Increment-Schlüssel | `nextSerialID()`: das Maximum der Spalte plus eins, nie zweimal in einer Verbindung (immer nur ein Schreiber neuer Zeilen) |
| `sqlUpsert()` | abgelehnt (`expDBUnsupportedException`): ohne durchgesetzten Schlüssel gibt es nichts, worauf es passen könnte |

Beim Escapen wird ein Backslash verdoppelt und ein Anführungszeichen als `\'` geschrieben. `databaseName()` ist
`clickhouse`; Fehlernummern sind PostgreSQLs SQLSTATE, wo es einen gibt (`42P01` unbekannte Tabelle, `42703` unbekannte
Spalte, `42601` Syntax, `28000` Anmeldung), sonst `CH` und ClickHouses eigene Nummer.

### Aus ClickHouse importieren

Zwei Wege, beide nur lesend:

- **Die ClickHouse-Quelle von SQLI Import** (`expImportClickHouseSource` in der Erweiterung `sqliimport`) liest die
  Zeilen eines SELECT über dieselbe HTTP-Schnittstelle mit `readonly=2`, Seite für Seite, und macht Inhalte daraus. Sie
  lehnt jede Anweisung außer `SELECT`/`WITH` und die Tabellenfunktionen ab, die über den Server hinausreichen. Sie gehört
  zu `sqliimport`, nicht zu diesem Treiber, und wurde in diesem Release nicht geändert.
- **Eine Datentabellen-Verbindung** (Engine `clickhouse`) und eine Datentabellen-Klasse darauf: eine
  Datentabellen-Synchronisierung (`table_to_content`) liest ihre Zeilen mit der Klasse und schreibt Inhalte. Lesen und
  Schreiben der Klasse wurden auf ClickHouse getestet (CL-07); die Synchronisierung selbst lief dort nicht.

### Was geht und was nicht

| Bereich | ClickHouse | Hinweise |
|---|---|---|
| Eine Site-Datenbank | **geht nicht** | [warum](#warum-keine-site-datenbank); `exp:install` bietet es nicht an |
| Datentabellen-Verbindung (Engine `clickhouse`): Test, Tabellen angelegt und erneut geplant, Zeilen geschrieben und gelesen, der Describer | geht | CL-07 |
| Werte: alle Ganzzahl- und Gleitkommatypen, `Decimal`, Text mit Tabulatoren, Zeilenumbrüchen, Backslashes und Unicode, `NULL`, `Bool`, `Date`, `DateTime`, `DateTime64` | geht | CL-01 |
| Das Kernel-Schema und seine Daten über den Schema-Handler | geht | als Beleg für den Handler, nicht für eine Site |
| Eindeutige Schlüssel, Transaktionen | geht nicht | gemessen, CL-03 |
| `UPDATE`, `DELETE` | geht, langsam | leichtgewichtig, wo die Tabelle Blocknummern hat, sonst eine Mutation |
| Auto-Increment-IDs | geht über den Treiber | das Maximum plus eins; immer nur ein Schreiber neuer Zeilen |
| Portable Helfer: Blättern, Gruppen-Verkettung mit Ordnung, Unix-Zeit | geht | Upsert abgelehnt |
| Import in Inhalte | geht | die ClickHouse-Quelle von SQLI Import; eine Datentabellen-Synchronisierung |
| TLS (HTTPS), ein Passwort nie in der Adresse | geht | |

## Gemessen

Auf dem Datenbankknoten des Projekts (12 CPUs, eine Last von 35 bis 40 durch andere Arbeit während der Läufe), PHP 8.5,
über die Treiber (aus den Live-Tests und einem Zeitlauf von je 50 bis 200 Anweisungen):

| Was | MonetDB Dec2025-SP5 | ClickHouse 25.8 |
|---|---|---|
| Verbinden (und Anmelden) | 25 ms | 10 ms (erste Anfrage) |
| eine Zeile außerhalb einer Transaktion eingefügt | 22 ms (Schreibsperre und Commit); 6,5 ms mit `WriteLock=disabled` | 9 bis 18 ms |
| eine Zeile innerhalb einer Transaktion eingefügt | 3,9 ms | (keine Transaktionen) |
| eine Zeile über ihren Primärschlüssel gelesen | 3,7 bis 4,2 ms | 30 bis 48 ms |
| eine Zeile geändert | 15 ms (außerhalb einer Transaktion) | 16 bis 122 ms (leichtgewichtig), 26 bis 55 ms (Mutation) |
| eine Zeile gelöscht | | 17 bis 76 ms (leichtgewichtig) |
| 1.000.000 Zeilen eingefügt mit `INSERT ... SELECT` | | 214 ms |
| `count()` und `sum()` über 1.000.050 Zeilen | | 90 ms |
| `begin()` und `commit()` mit der Schreibsperre | 16 ms | |

MonetDB übersetzt jede Anweisung, die es bekommt, und der Kernel schickt seine Werte im Text der Anweisung, deshalb kostet
selbst ein Zugriff über den Schlüssel Millisekunden; eine Seite mit Hunderten von Abfragen braucht ohne View-Cache eine
Sekunde oder mehr. ClickHouse ist umgekehrt gebaut: eine Million Zeilen kosten kaum mehr als eine.

## Wie sie aufgebaut sind

| Datei | Was |
|---|---|
| `lib/ezdb/classes/expmonetdbmapi.php` | `expMonetDBMapi`: MAPI in reinem PHP; Anmeldung (Herausforderung, gesalzener Hash, Proxy und Umleitung), Blöcke, Ergebnisse gelesen und entschlüsselt |
| `lib/ezdb/classes/expmonetdbdb.php` | `expMonetDBDB`: der eZDB-Treiber; die Schreibsperre, Transaktionen, IDs, der Katalog, Sequenzen |
| `lib/ezdb/classes/expmonetdbdialect.php` | `expMonetDBDialect`: was an einer Anweisung geändert wird, was als Schreiben gilt, Escapen, Fehlernummern |
| `lib/ezdbschema/classes/expmonetdbschema.php` | `expMonetDBSchema`: DDL aus `.dba`, ein Schema zurück in `.dba` gelesen |
| `kernel/sql/monetdb/kernel_schema.sql` | das Kernel-Schema, von `expMonetDBSchema` aus `share/db_schema.dba` geschrieben |
| `lib/ezdb/classes/expclickhousehttp.php` | `expClickHouseHttp`: die HTTP-Schnittstelle; Anfragen, `TabSeparatedWithNamesAndTypes` zurückgelesen, Fehler |
| `lib/ezdb/classes/expclickhousedb.php` | `expClickHouseDB`: der eZDB-Treiber; Anweisungen, Mutationen, IDs, der Katalog |
| `lib/ezdb/classes/expclickhousedialect.php` | `expClickHouseDialect`: was an einer Anweisung geändert wird, Escapen, Fehlernummern |
| `lib/ezdbschema/classes/expclickhouseschema.php` | `expClickHouseSchema`: MergeTree-Tabellen aus `.dba`, eine Datenbank zurückgelesen |

## Tests

`tests/tests/lib/ezdb/columnar/` (ohne Datenbank, Testsuite `columnar`) und `.../columnar/live/` (Testsuite
`columnar-live`; jede Testklasse wird ohne ihren Server übersprungen: `EXP_TEST_MONETDB_HOST`,
`EXP_TEST_CLICKHOUSE_HOST` und ihre `_PORT`, `_USER`, `_PASSWORD`, `_DATABASE`). Der GitHub-Job `columnar` führt beide
gegen MonetDB Dec2025-SP5 mit ClickHouse 25.8 (PHP 8.5) und MonetDB Dec2023-SP4 mit ClickHouse 24.8 (PHP 8.1) aus und
installiert mit dem ersten Paar eine ganze Site auf MonetDB und ruft ihre Startseite und die Admin-Anmeldung ab.

| Datei | Was |
|---|---|
| `expMonetDBMapiTest` | die Antwort bei der Anmeldung, Ergebnisse, Zeilen mit jedem Escape, Fehler, kein Server |
| `expMonetDBDialectTest` | die Umschreibungen, unangetastete Literale, Schreibanweisungen, Escapen, Fehlernummern |
| `expMonetDBDBTest` | der Treiber auf einem Ersatz für MAPI: das Schema, die Schreibsperre um Schreiben und Transaktionen, das Warten auf sie, Wiederholungen, Werte, IDs, eine verlorene Verbindung |
| `expMonetDBSchemaTest` | DDL einer Tabelle und des Kernel-Schemas (gleich `kernel/sql/monetdb/kernel_schema.sql`), zurückgelesene Typen und Vorgabewerte |
| `expClickHouseHttpTest`, `expClickHouseDialectTest`, `expClickHouseDBTest`, `expClickHouseSchemaTest` | dasselbe für ClickHouse: Anfragen und Header, Ergebnisse, Fehler, Umschreibungen, der Rückfall auf die Mutation, Transaktionen, die nichts schicken, IDs, MergeTree-DDL |
| `live/expMonetDBLiveTest` | gegen MonetDB: Werte, 5.000 Zeilen, das Kernel-Schema und saubere Daten, die Hin- und Rückrunde des Schema-Handlers, Transaktionen, IDs, Fehler, Helfer, Konflikte ohne Schreibsperre, die Schreibsperre mit einem zweiten Prozess, reservierte Spalten, Schema-Upgrades, eine Datentabellen-Verbindung |
| `live/expClickHouseLiveTest` | gegen ClickHouse: Werte, 5.000 Zeilen, Fehler, das Kernel-Schema über den Handler, was ClickHouse nicht tut, Zeilen ändern mit Zeiten, IDs, Helfer, eine Datentabellen-Verbindung |
