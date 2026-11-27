# DuckDB-Treiber

Diese Seite ist für Administratoren, die eine Site auf DuckDB betreiben wollen, und für Entwickler, die am Treiber
arbeiten. Ab Exponential 6.0.15 hat der Kernel einen DuckDB-Treiber, `expDuckDBDB` in `lib/ezdb/classes/`: eine ganze
Site (die Tabellen des Kernels, die Inhalte eines Pakets, die Tabellen der Erweiterungen) in einer DuckDB-Datei, ohne
Datenbankserver.

**Wofür er gedacht ist.** DuckDB ist eine eingebettete, spaltenorientierte SQL-Datenbank: eine Datei, die der
PHP-Prozess selbst liest und schreibt, schnell bei Durchläufen und Aggregaten. Ihre Regel für diese Datei entscheidet,
wo sie passt: **es schreibt immer nur ein Prozess**. Ein Prozess, der die Datei zum Schreiben geöffnet hat, hat sie für
sich allein; beliebig viele Prozesse dürfen sie zugleich lesen, aber nie, während einer schreibt. DuckDB passt deshalb
zu einer Site mit einem oder wenigen Redakteuren, zu einer Site, die vor allem gelesen wird, zu Build- und Demo-Sites,
zu einer Site, die einmal erstellt und dann ausgeliefert wird, und zu Auswertungen über die Daten einer Site. Für eine
viel besuchte Site, auf der viele Redakteure gleichzeitig schreiben, ist es keine Datenbank: deren Anfragen stehen
hintereinander an (siehe [Gleichzeitigkeit](#gleichzeitigkeit-immer-nur-ein-schreibender-prozess)). Einen DFS-Cluster
gibt es auf DuckDB nicht; ein Cluster aus Webservern braucht einen Datenbankserver.

## Inhalt

1. [Voraussetzungen](#voraussetzungen)
2. [Eine Site auf DuckDB installieren](#eine-site-auf-duckdb-installieren)
3. [Einstellungen](#einstellungen)
4. [Gleichzeitigkeit: immer nur ein schreibender Prozess](#gleichzeitigkeit-immer-nur-ein-schreibender-prozess)
5. [Das SQL](#das-sql)
6. [Was geht und was nicht](#was-geht-und-was-nicht)
7. [Eine Datentabellen-Verbindung zu einer DuckDB-Datei](#eine-datentabellen-verbindung-zu-einer-duckdb-datei)
8. [Wie er aufgebaut ist](#wie-er-aufgebaut-ist)
9. [Tests](#tests)

## Voraussetzungen

| Was | Woher |
|---|---|
| DuckDBs C-Bibliothek, `libduckdb.so` (`libduckdb.dylib` unter macOS, `duckdb.dll` unter Windows) | DuckDBs Releases auf GitHub: `duckdb-shared-libs-linux-amd64.tar.gz` (2.0), `libduckdb-linux-amd64.zip` (1.x). Getestet: 1.5.6 und 2.0.1 |
| PHPs FFI-Erweiterung, `ffi` | gehört zu PHP (`--with-ffi`); RHEL-Familie und Remi `php-ffi`, Debian und Ubuntu `php<Version>-ffi` |
| `ffi.enable=true` in der `php.ini` von PHP-FPM (oder Velocity) | PHPs Standard `ffi.enable=preload` erlaubt FFI nur auf der Kommandozeile; eine Web-Anfrage scheitert dann mit einer Meldung, die das sagt |

PHP erreicht DuckDB über FFI, weil das der robusteste der drei möglichen Wege ist. Es gibt keine gepflegte
PHP-Erweiterung für DuckDB, die ein Host aus einem Paket installieren kann; das Kommandozeilenprogramm `duckdb` wäre ein
Prozess je Anweisung (oder eine Pipe, deren Textausgabe wieder in Werte zurückgelesen werden müsste). FFI lädt DuckDBs
eigene C-Bibliothek in den PHP-Prozess, ruft ihre stabile C-API auf und liest die Werte in ihren eigenen Typen. Der
Treiber nutzt nur den Teil der C-API, den DuckDB stabil nennt (`duckdb_open_ext`, `duckdb_query`, `duckdb_fetch_chunk`
und die Vektoren eines Chunks), nicht die Einzelwert-Funktionen, die DuckDB abgekündigt hat; derselbe Code läuft so
auf DuckDB 1.x und 2.0.

Bibliothek und Erweiterung nie ungeprüft in ein PHP bringen, das andere Sites mitbenutzen: mit FFI kann PHP-Code jede
C-Bibliothek laden, darum schaltet PHP es außerhalb der Kommandozeile standardmäßig ab. Schalten Sie es im Pool der Site
ein, die es braucht (`php_admin_value[ffi.enable]=true` in ihrem PHP-FPM-Pool).

## Eine Site auf DuckDB installieren

```
php bin/php/install.php --db=duckdb --apply
php bin/php/install.php --db=duckdb --db-name=/srv/sites/beispiel/data/site.duckdb --apply
```

Die Standarddatei ist `var/storage/duckdb/exponential.duckdb`; ein Name ohne Pfad liegt in `var/storage/duckdb/`
(`.duckdb` wird an einen Namen ohne Endung angehängt), ein Pfad, der mit `/` beginnt, gilt so, wie er ist.
`var/storage` schickt die mitgelieferte `.htaccess` an `index.php`, und Velocity lässt es bei den statischen Dateien
aus; die Datei wird also nie ausgeliefert. Mit `kickstart.ini` ist die Datenbank `[database_choice] Type=duckdb` und
`[database_init] Database=<Datei>`; Server, Port, Benutzer und Passwort werden nicht beachtet. Die Bibliothek wird so
gefunden, wie `[DuckDBSettings] Library` es sagt, sonst über `DUCKDB_LIBRARY` aus der Umgebung, sonst unter dem Namen
`libduckdb.so` im Bibliothekspfad des Systems.

Der Einrichtungsassistent im Browser listet DuckDB auf, wenn die PHP-Erweiterung `ffi` im Web-Prozess verwendet werden
darf (`ffi.enable=true`; `[database_extensions]` zählt `ffi`) und `libduckdb` gefunden wird. Seine Datenbankseite fragt
nur nach einem Dateinamen (Standard `exponential.duckdb` in `var/storage/duckdb`), ohne Server, Benutzer und Passwort,
wie bei SQLite. Mit `ffi.enable=preload`, dem PHP-Standard, geht FFI nur auf der Kommandozeile; der Assistent im Web
bietet DuckDB dann nicht an. Nehmen Sie dann `exp:install` oder den Kickstarter.

Der Installer legt die Tabellen des Kernels aus `share/db_schema.dba` über den Schema-Handler `expDuckDBSchema` an,
fügt die Grunddaten ein, setzt die Sequenzen hinter die eingefügten IDs, installiert das Paket und schreibt
`DatabaseImplementation=duckdb` in die Einstellungen. „Vorhandene Daten entfernen" (`--db-action=remove`, der Standard)
leert die Datei zuerst: jede Sicht, jeder Index, jede Tabelle und jede Sequenz darin wird gelöscht.

## Einstellungen

```ini
[DatabaseSettings]
DatabaseImplementation=duckdb
Database=site.duckdb

[DuckDBSettings]
Library=/usr/local/lib/libduckdb.so
AccessMode=auto
```

`ImplementationAlias[duckdb]` nennt `expDuckDBDB`. `[DuckDBSettings]` der `site.ini`:

| Einstellung | Standard | Bedeutung |
|---|---|---|
| `Library` | leer | die C-Bibliothek, ein absoluter Pfad oder ein Name, den der Lader findet; leer: `DUCKDB_LIBRARY` aus der Umgebung, sonst `libduckdb.so` (`libduckdb.dylib`, `duckdb.dll`) |
| `AccessMode` | `auto` | `auto`: die Datei nur lesend öffnen und erst für den ersten Schreibvorgang oder die erste Transaktion der Anfrage schreibend; `read_write`: immer schreibend; `read_only`: nie schreiben (eine veröffentlichte Kopie einer Site) |
| `LockWait` | leer | Sekunden, die ein Öffnen wartet, solange ein anderer Prozess die Datei hält; leer: `LockWaitWeb` in einer Web-Anfrage, `LockWaitCommandLine` auf der Kommandozeile |
| `LockWaitWeb` | `10` | |
| `LockWaitCommandLine` | `120` | |
| `Threads` | `0` | DuckDBs Threads je Verbindung; 0: DuckDB entscheidet (die CPUs). Auf einem Host mit vielen PHP-Workern 1 oder 2 |
| `MemoryLimit` | leer | DuckDBs Speichergrenze, z. B. `512MB`; leer: DuckDBs Standard (80 % des Speichers) |
| `TempDirectory` | leer | wohin DuckDB eine große Sortierung oder einen großen Join auslagert; leer: neben die Datenbankdatei |
| `IntegerDivision` | `enabled` | `7/2` ist 3, wie in PostgreSQL und MySQL; DuckDBs eigenes `/` teilt zu einem DOUBLE |
| `Config[<Name>]` | | weitere DuckDB-Einstellungen für das Öffnen (`Config[preserve_insertion_order]=false`) |

Im Code: `new expDuckDBDB( array( 'database' => '/pfad/site.duckdb', 'duckdb' => array( 'AccessMode' => 'read_only' ) ) )`.

## Gleichzeitigkeit: immer nur ein schreibender Prozess

DuckDB sperrt die Datei mit der Dateisperre des Betriebssystems: ein Prozess, der sie zum Schreiben öffnet, hält eine
exklusive Sperre, einer, der sie zum Lesen öffnet, eine geteilte. Der Treiber macht das für eine Website so nutzbar:

- **Zuerst lesen.** Mit `AccessMode=auto` öffnet eine Anfrage die Datei nur lesend. Beliebig viele Anfragen lesen
  gleichzeitig.
- **Schreiben, wenn nötig.** Die erste Anweisung, die schreibt (`INSERT`, `UPDATE`, `DELETE`, DDL,
  `CREATE TEMPORARY TABLE`), oder das erste `begin()` schließt die Datei und öffnet sie schreibend neu. Die
  `SET`-Anweisungen der Verbindung werden wiederholt. Eine Anweisung, die der Treiber nicht als Schreibvorgang erkannt
  hat und die DuckDB als nur lesend ablehnt, wird nach dem neuen Öffnen noch einmal gesendet.
- **Warten, dann aufgeben.** Solange ein anderer Prozess die Datei schreibend hält, wird das Öffnen wiederholt (10 ms,
  verdoppelt bis 250 ms), `LockWait` Sekunden lang; dann scheitert es mit der Fehlernummer `55P03` (Sperre nicht
  verfügbar), und die Anfrage bekommt die Datenbank-Fehlerseite.
- **Am Ende loslassen.** Jede Web-Anfrage schließt die Datei, wenn sie endet (eine Shutdown-Funktion), damit die nächste
  Anfrage in einem anderen PHP-FPM-Worker sie bekommt. Eine noch offene Transaktion wird zurückgerollt.

Was das in der Praxis heißt:

| Lage | Was passiert |
|---|---|
| Besucher lesen Seiten | sie lesen parallel |
| Eine Anfrage, die schreibt (eine Anmeldung, ein Formular, die Sitzung, ein Zähler) | sie wartet, bis die Leser des Augenblicks die Datei geschlossen haben, und hat sie dann für den Rest ihrer Anfrage allein; Leser, die inzwischen kommen, warten auf sie |
| Zwei Redakteure veröffentlichen gleichzeitig | der zweite wartet, bis die Anfrage des ersten endet (bis zu `LockWait`) |
| Ein langer Kommandozeilen-Job (ein Import, ein Cronjob) | er hält die Datei schreibend, solange er läuft: schreibende Web-Anfragen warten und scheitern nach `LockWait`. Lassen Sie solche Jobs laufen, wenn die Site ruhig ist, oder geben Sie `LockWaitWeb` mehr Zeit |
| Velocitys dauerhafte Worker | dieselben Regeln: jede Anfrage öffnet und schließt die Datei |
| Mehrere Webserver | nicht möglich: DuckDBs Dateisperre gilt nur auf einer Maschine, und ein Netzwerkdateisystem trägt sie nicht zuverlässig |

Sitzungen in der Datenbank würden die meisten Anfragen zu schreibenden machen. Der Standard-Sitzungshandler
(`[Session] Handler=` leer: PHPs eigener, `ezpSessionHandlerPHP`) hält sie heraus; lassen Sie es auf DuckDB dabei,
damit Seiten Lesevorgänge bleiben.

## Das SQL

DuckDBs SQL ist in fast allem, was der Kernel schreibt, das von PostgreSQL: Sequenzen und `nextval()`, `||`,
`SUBSTRING( ... FROM ... FOR ... )`, `ON CONFLICT`, `STRING_AGG`, `EXTRACT( EPOCH ... )`, `information_schema`,
`UPDATE ... FROM`. `databaseName()` ist `duckdb`, und die portablen Helfer (`sqlLimit()`, `sqlUpsert()`,
`sqlGroupConcat()`, `sqlUnixTimestamp()`, `sqlFromUnixTimestamp()`, `sqlForUpdate()`, `sqlQuoteIdentifier()`) haben
einen Dialekt `duckdb`. `expDuckDBDialect` ändert eine Anweisung unterwegs nur dort, wo DuckDB abweicht:

| Der Kernel schreibt | DuckDB bekommt | Warum |
|---|---|---|
| `SELECT ... FOR UPDATE` (auch `FOR SHARE`, `LOCK IN SHARE MODE`) | das `SELECT` ohne | DuckDB hat keine Zeilensperren: ein Prozess schreibt, und in ihm werden Transaktionen optimistisch geprüft |
| `LOCK TABLE ...` | nichts | nichts zu sperren; `lock()` und `unlock()` sind eine Transaktion |
| `INSERT IGNORE INTO` | `INSERT OR IGNORE INTO` | MySQLs Worte |
| `REPLACE INTO` | `INSERT OR REPLACE INTO` | |
| `` `name` `` | `"name"` | |
| `LIMIT 20, 10` | `LIMIT 10 OFFSET 20` | |
| `7 / 2` | 3 | `SET integer_division = true` bei jedem Öffnen (`IntegerDivision`) |

Zeichenketten-Literale und Namen in Anführungszeichen werden nie geändert. Das Escapen verdoppelt Anführungszeichen und
lässt NUL-Bytes weg; ein Backslash ist ein gewöhnliches Zeichen. Neue IDs kommen aus der Sequenz der Spalte
`<Tabelle>_<Spalte>_seq` (`currval()`), wie bei PostgreSQL; Fehlernummern sind der SQLSTATE, den PostgreSQL demselben
Fehler gibt (`23505` doppelter Schlüssel, `42P01` fehlende Tabelle, `42703` fehlende Spalte, `42601` Syntax, `55P03`
Datei gesperrt), so dass Code, der einen doppelten Schlüssel von anderen Fehlern unterscheidet, unverändert
funktioniert. Werte kommen als Text zurück, wie bei MySQL und PostgreSQL (`BOOLEAN` als 1 oder 0, `TIMESTAMP` als
`Y-m-d H:i:s`).

Der Schema-Handler `expDuckDBSchema` schreibt PostgreSQLs DDL mit drei Unterschieden: der Primärschlüssel ist Teil von
`CREATE TABLE` (DuckDB 1.x lehnt es ab, einer Tabelle mit Index einen hinzuzufügen, 2.0 kennt kein
`ALTER TABLE ONLY`), Sequenzen haben kein `CACHE`, Indizes kein `USING btree`. Indexnamen sind je Datenbank eindeutig,
wie in PostgreSQL: ein Indexname, den eine andere Tabelle schon hat, wird zu `<Tabelle>__<Index>`.
`kernel/sql/duckdb/kernel_schema.sql` ist das von ihm geschriebene Kernel-Schema.

## Was geht und was nicht

Gemessen am 26.11.2026 mit den Tests unten auf DuckDB 1.5.6 und 2.0.1 (PHP 8.5 mit FFI, Linux x86-64) und einer ganzen
Site, installiert mit `exp:install --db=duckdb` auf DuckDB 2.0.1.

| Bereich | DuckDB | Hinweise |
|---|---|---|
| Kernel-Schema (153 Tabellen) und Grunddaten | geht | `kernel/sql/duckdb/kernel_schema.sql`, `share/db_schema.dba` über den Schema-Handler |
| Installation mit `exp:install` / Kickstarter, Paket und Erweiterungen | geht | das Multisite-Paket mit seinen Inhalten: 16 Schritte, 0 Fehler, 0 Warnungen, 9 Prüfungen bestanden; 31 Minuten auf einem ausgelasteten geteilten Host (etwa 118 MB). Der Installer lässt die Datei los, bevor er einen eigenen PHP-Prozess startet (Cluster, Hilfe-Handbücher, Migrationen), der sonst auf ihn warten würde |
| Einrichtungsassistent im Browser | geht | angeboten mit `ffi` und `ffi.enable=true` im Web-PHP; mit `ffi.enable=preload` nicht angeboten, `exp:install` nehmen |
| Seiten lesen, die Administration, Bearbeiten und Veröffentlichen | geht, langsam | Startseite, Anmeldung der Administration und Inhaltsansichten antworten mit 200. **Jede Anweisung kostet Millisekunden** (unten gemessen), wo SQLite und MySQL für einen Zugriff über den Schlüssel einige zehn Mikrosekunden brauchen: eine Seite mit Hunderten von Abfragen braucht ohne View-Cache Sekunden |
| Transaktionen, verschachteltes `begin()`/`commit()`, Rollback | geht | eine gescheiterte Anweisung beendet DuckDBs Transaktion; der Kernel rollt zurück wie bei PostgreSQL |
| Neue IDs (`lastSerialID()`), beim Einfügen gegebene IDs, nachgeführte Sequenzen | geht | `setval()` auf 2.0, `nextval()` über `range()` auf 1.x |
| Unicode-Text, langer Text, BLOB | geht | Text muss gültiges UTF-8 sein (DuckDB lehnt alles andere ab) |
| Doppelte Schlüssel, fehlende Tabellen (Fehlernummern) | geht | PostgreSQLs SQLSTATE |
| Portable Helfer: Blättern, Upsert, Gruppen verketten, Unix-Zeit | geht | |
| `SELECT ... FOR UPDATE`, `LOCK TABLE` | geht | weggelassen: nichts zu sperren |
| Mehrere Leser gleichzeitig | geht | `AccessMode=auto` |
| Mehrere Schreiber gleichzeitig | **geht nicht** | sie stehen an; ein Schreiber wartet bis zu `LockWait` und scheitert dann mit `55P03` |
| Mehrere Webserver, DFS-Cluster | **geht nicht** | kein DFS-Backend: die Dateisperre gilt nur auf einer Maschine |
| Lesereplikat (`UseSlaveServer`) | geht nicht | wird nicht beachtet, wie bei SQLite |
| Migrationen nach der Installation (`exp:migration:migrate`) | geht | 49 Migrationen: 4 angewendet, 40 nicht nötig, 0 gescheitert. Fünf, die die Indizes einer Tabelle lesen müssen, werden übersprungen („cannot tell"), weil die Migrationsprüfungen für DuckDB noch keine Katalogabfrage haben. Der Installer führt sie vor den Hilfe-Handbüchern aus, deren Aufbau ein Hintergrund-Worker ist, der die Datei 20 bis 30 Minuten hält |
| Upgrade-Sicherungspunkte (der Datenbank-Dump vor einem Upgrade oder einem Migrationslauf) | **geht nicht** | kein eingebauter Dump für DuckDB: die Datei selbst kopieren (oder `EXPORT DATABASE`) und mit `--no-backup` ausführen |
| Schema-Updates: `ALTER TABLE ... ADD COLUMN` | geht | |
| Schema-Updates: Spaltentyp ändern, Tabelle umbenennen, Spalte löschen, bei einer Tabelle mit Index | **geht nicht** | DuckDB lehnt ab (`Cannot alter entry ... because there are entries that depend on it`): die Indizes der Tabelle löschen, ändern, wieder anlegen. Die mitgelieferten Dateien in `update/database/` sind für MySQL, PostgreSQL und SQLite geschrieben |
| PostgreSQLs Volltextspalte des Audit-Index (`tsvector`) | geht nicht | die Audit-Suche nimmt `LIKE`, wie bei SQLite |
| Typen, die der Kernel nicht nutzt (LIST, STRUCT, MAP, UNION, INTERVAL) | geht nicht | nur mit `CAST( ... AS VARCHAR )` in der Abfrage zurücklesbar |
| Datentabellen-Verbindung (Engine `duckdb`) | geht | unten |
| Importquelle | geht | `expImportDuckDbSource` der Erweiterung `sqliimport` liest DuckDB-Dateien |
| Sicherung | geht | die Datei kopieren, während kein Prozess schreibt, oder `EXPORT DATABASE` aus dem Programm `duckdb` |


### Gemessen

Auf dem Datenbankknoten des Projektclusters (12 CPUs, Last 20 bis 40 durch andere Arbeit während des Laufs), DuckDB
2.0.1, PHP 8.5 mit FFI, 2 DuckDB-Threads, über `expDuckDBDB`:

| Was | Zeit |
|---|---|
| eine Zeile außerhalb einer Transaktion eingefügt (je ein Commit) | 7,4 ms |
| eine Zeile innerhalb einer Transaktion eingefügt | 2,7 ms |
| eine Zeile über ihren Primärschlüssel gelesen | 2,7 ms |
| eine Zeile außerhalb einer Transaktion geändert | 7,9 ms |
| Öffnen, erste Anweisung, Schließen (eine kleine Datei) | 42 ms |
| Öffnen der installierten Site nur lesend (118 MB) | 122 ms |
| Startseite der installierten Site, View-Cache gefüllt (PHPs eingebauter Server, ohne OPcache) | 2,3 bis 3,5 s |
| dieselbe Seite mit leerem Cache | 17 s |

DuckDB ist für Durchläufe über viele Zeilen gebaut, nicht für viele kleine Anweisungen; der Kernel schickt Hunderte je
Seite. Lassen Sie den View-Cache an, und rechnen Sie damit, dass DuckDB zu Sites passt, bei denen das hinnehmbar ist.

## Eine Datentabellen-Verbindung zu einer DuckDB-Datei

Die Datentabellen-Schicht hat die Engine `duckdb` (`datatable.ini [ConnectionSettings] Engines[]`): eine DuckDB-Datei
in einem der `SQLiteDirectories[]`, mit der Endung `.duckdb`, `.ddb` oder `.db`:

```ini
[Connection_analytics]
Engine=duckdb
Database=var/storage/datatables/analytics.duckdb
```

Sie ist verfügbar, wo PHP FFI nutzen kann. `expDataConnections::test()` öffnet sie, `expDataTableSchema` legt Tabellen
darauf über `expDuckDBSchema` an und aktualisiert sie, und der Describer liest ihre Spalten aus
`information_schema.columns`. Eine Site auf einer anderen Datenbank kann so ihre Auswertungen in DuckDB halten.

## Wie er aufgebaut ist

| Datei | Was |
|---|---|
| `lib/ezdb/classes/expduckdbffi.php` | `expDuckDBFFI`: die C-Deklarationen der stabilen C-API, Öffnen und Schließen mit einer Konfiguration, Anweisungen, das Ergebnis Chunk für Chunk und Vektor für Vektor gelesen, Werte als Text |
| `lib/ezdb/classes/expduckdbdb.php` | `expDuckDBDB`: der eZDB-Treiber; Zugriffsarten, das Warten auf die Sperre, Transaktionen, IDs, der Katalog, Sequenzen |
| `lib/ezdb/classes/expduckdbdialect.php` | `expDuckDBDialect`: was an einer Anweisung geändert wird, was als Schreibvorgang zählt, Escapen, Fehlernummern |
| `lib/ezdbschema/classes/expduckdbschema.php` | `expDuckDBSchema`: DDL aus `.dba`, eine Datenbank zurück in `.dba` gelesen |
| `kernel/sql/duckdb/kernel_schema.sql` | das Kernel-Schema, von `expDuckDBSchema` aus `share/db_schema.dba` geschrieben |

## Tests

`tests/tests/lib/ezdb/duckdb/` (ohne Datenbank, Testsuite `duckdb`) und `.../duckdb/live/` (Testsuite `duckdb-live`;
übersprungen ohne FFI oder ohne `EXP_TEST_DUCKDB_LIBRARY`, den Pfad der `libduckdb.so`). Der GitHub-Job `duckdb`
führt beide auf DuckDB 2.0.1 (PHP 8.5) und 1.5.6 (PHP 8.3) aus und installiert auf 2.0.1 eine ganze Site mit
`exp:install --db=duckdb` und ruft ihre Startseite und die Anmeldung der Administration ab.

| Datei | Was |
|---|---|
| `expDuckDBDialectTest` | Einordnung der Anweisungen, die Umschreibungen, unberührte Literale, Escapen, Fehlernummern, die Helfer |
| `expDuckDBDBTest` | der Treiber auf einem Ersatz für die Bibliothek: Dateinamen, Einstellungen, zuerst lesend, das neue Öffnen zum Schreiben, Warten auf die Sperre, wiederholte Einstellungen, Blättern, Fehler, IDs, Katalog, Sequenzen |
| `expDuckDBSchemaTest` | DDL einer Tabelle und des Kernel-Schemas (gleich `kernel/sql/duckdb/kernel_schema.sql`), Typen und Standardwerte zurückgelesen, Wertumwandlungen der Anbindung |
| `live/expDuckDBLiveTest` | gegen DuckDB: jeder Typ zurückgelesen, 5.000 Zeilen über mehrere Chunks, das Kernel-Schema und die Grunddaten, der Rundweg von `share/db_schema.dba` durch den Schema-Handler, Transaktionen, IDs, Fehler, die Helfer, zwei Prozesse, die gleichzeitig lesen, ein zweiter Prozess, der auf die Sperre wartet und nach `LockWait` scheitert |

Nach einer Änderung an `share/db_schema.dba` die Schemadatei neu schreiben: der Test
`expDuckDBSchemaTest::testKernelSchema` scheitert, bis `kernel/sql/duckdb/kernel_schema.sql` dem gleicht, was
`expDuckDBSchema::generateSchemaFile()` dafür schreibt.
