# SAP-HANA-Treiber

Diese Seite ist für Administratoren, die Exponential auf SAP HANA betreiben wollen, und für Mitwirkende am Treiber. Ab
Exponential 6.0.15 hat der Kernel einen SAP-HANA-Treiber, `expHANADB` in `lib/ezdb/classes/`, mit dem Dialekt
`expHANADialect`, dem Schema-Handler `expHANASchema`, Einträgen in den Installern und einer Datentabellen-Engine.

**Stand.** Der Treiber, das Kernel-Schema mit seinen Daten (gleich der `.dba` zurückgelesen), Upgrades und die
portablen SQL-Helfer bestehen ihre Live-Tests auf SAP HANA Express Edition 2.00.088 (SPS 08), auf beiden ODBC-Schichten.
`install.php --db=hana` installiert das Multisite-Paket (wie weit und wie schnell: siehe die Tabelle unten). Noch nicht:
ein DFS-Cluster-Backend und Lesereplikate.

## Inhalt

1. [Unterstützte Server und PHP](#unterstützte-server-und-php)
2. [Einstellungen](#einstellungen)
3. [Der ODBC-Treiber](#der-odbc-treiber)
4. [Eine Site installieren](#eine-site-installieren)
5. [Wie das SQL des Kernels zu dem von HANA wird](#wie-das-sql-des-kernels-zu-dem-von-hana-wird)
6. [Was geht und was nicht](#was-geht-und-was-nicht)
7. [Datentabellen-Verbindungen](#datentabellen-verbindungen)
8. [Tests](#tests)

## Unterstützte Server und PHP

| | Unterstützt | Warum |
|---|---|---|
| SAP HANA 2.0 SPS 05 bis SPS 08, die Express Edition eingeschlossen | ja | gemessen an HANA Express Edition 2.00.088 (SPS 08) |
| SAP HANA Cloud | dafür geschrieben, ungetestet | dasselbe SQL; Verbindung mit `Encrypt=enabled` über Port 443 |
| SAP HANA 2.0 vor SPS 05, HANA 1.0 | nein | der Treiber meldet sie als nicht unterstützt |
| PHP 8.0 bis 8.5 mit der Erweiterung `odbc` | ja | die Standardschicht (`Layer=auto` nimmt sie zuerst) |
| PHP nur mit `PDO_ODBC` | ja, für alles außer dem Schema | PDO liest `:name` als Parameter, und die Trigger der `auto_increment`-Spalten lesen ihre Zeile als `:new_row.id`: eine Anweisung mit `:` geht über die Erweiterung `odbc`, PDO_ODBC allein kann das Kernel-Schema also nicht anlegen |

PHP hat keine eigene HANA-Erweiterung; der Treiber spricht mit SAPs HANA-ODBC-Treiber (`libodbcHDB.so` des SAP HANA
Client, HDBODBC) über unixODBC unter Linux oder den ODBC-Treibermanager unter Windows.

**Lizenzen.** Der SAP HANA Client und SAP HANA Express Edition kommen mit SAPs eigenen Lizenzen (die Express Edition:
die Entwicklerlizenz der SAP HANA Express Edition, kostenlos für die Entwicklung und für den Betrieb bis 32 GB
Speicher). Lesen Sie sie, bevor Sie eines davon herunterladen; Exponential liefert keines davon mit.

## Einstellungen

```ini
[DatabaseSettings]
DatabaseImplementation=hana
Server=hana.example.com
Port=39041
User=EXPONENTIAL
Password=...
Database=EXPONENTIAL

[HANASettings]
Driver=HDBODBC
```

`ImplementationAlias[hana]` (und `saphana`) nennt `expHANADB`. `Port` ist der SQL-Port der Tenant-Datenbank
(`3<Instanz>41`: 39041 bei HANA Express, 30041 bei Instanz 00; 443 bei HANA Cloud). `Database` ist das **Schema**, in
dem die Tabellen der Site liegen; es wird bei der ersten Verbindung angelegt, wenn es fehlt (`CreateSchema`).

| `[HANASettings]` | Bedeutung |
|---|---|
| `Driver` | der ODBC-Treiber: sein Name in `odbcinst.ini` (`HDBODBC`) oder der Pfad von `libodbcHDB.so` |
| `Layer` | `auto` (die Erweiterung `odbc`, dann PDO_ODBC), `odbc` oder `pdo_odbc` |
| `TenantDatabase` | ein Tenant, der über den Port der Systemdatenbank erreicht wird (`DATABASENAME`); leer: der des Ports |
| `Encrypt`, `ValidateCertificate` | eine verschlüsselte Verbindung (`ENCRYPT=TRUE`; HANA Cloud braucht sie), das Zertifikat des Servers geprüft |
| `ConnectionString` | weitere `Schlüssel=Wert`-Paare des HANA-ODBC-Treibers |
| `CreateSchema` | `enabled`: das Schema aus `Database` wird angelegt, wenn es fehlt |
| `BindLiterals` | `enabled`: die Literalwerte einer Anweisung gehen als Parameter (siehe unten) |
| `TableType` | `column` (HANAs Standard) oder `row`: der Speicher der Tabellen, die der Schema-Handler anlegt; eine Row-Tabelle schreibt eine einzelne Zeile etwa dreimal so schnell (der Suchindex schreibt Wort für Wort) und liegt ganz im Speicher |
| `DeadlockRetries`, `DeadlockRetryWait` | eine einzelne Anweisung außerhalb einer Transaktion wird nach einem Deadlock (133) erneut gesendet |
| `ReconnectOnLoss` | `enabled`: eine verlorene Verbindung außerhalb einer Transaktion wird neu geöffnet (persistente Velocity-Worker) |
| `InListChunkSize` | Werte je `IN`-Liste (5.000) |

Der Verbindungsstring hat immer `CHAR_AS_UTF8=TRUE` (Text als UTF-8) und `CURRENTSCHEMA`. Die Meldung einer
gescheiterten Verbindung enthält nie das Passwort.

## Der ODBC-Treiber

Unter Linux den SAP HANA Client installieren und seinen Treiber bei unixODBC eintragen:

```ini
# /etc/odbcinst.ini (oder $ODBCSYSINI/odbcinst.ini)
[HDBODBC]
Description=SAP HANA ODBC driver
Driver=/usr/sap/hdbclient/libodbcHDB.so
```

PHP braucht die Erweiterung `odbc` (empfohlen) oder `PDO_ODBC`. Mit PDO_ODBC verlangt der Treiber die eigenen Cursor
des HANA-Treibers (die Cursor-Bibliothek von unixODBC fehlt oft).

## Eine Site installieren

```bash
EXP_INSTALL_DB_PASSWORD=... php bin/php/install.php --apply --db=hana \
    --db-host=hana.example.com --db-port=39041 --db-name=EXPONENTIAL --db-user=EXPONENTIAL
```

Der Benutzer braucht `CREATE SCHEMA` (oder ein für ihn angelegtes Schema). Der Kickstarter nimmt `Type=hana` in
`[database_choice]`. Der Setup-Assistent listet HANA nicht auf: es hat keine eigene PHP-Erweiterung, an der er es
erkennen könnte. `--db-action=remove` (der Standard) löscht zuerst die Tabellen und Sequenzen des Schemas.

## Wie das SQL des Kernels zu dem von HANA wird

Der Kernel schreibt MySQLs Wörter, wo er seine portablen Helfer (`sqlLimit()`, `sqlUpsert()` ...) nicht verwendet; der
Treiber schreibt jede Anweisung auf einem Lexer um (`expHANADialect::rewrite()`), sodass ein Wort in einem
String-Literal oder Kommentar nie angefasst wird:

| Der Kernel schreibt | HANA bekommt |
|---|---|
| `` `name` `` | `"NAME"` (HANA speichert einen Namen ohne Anführungszeichen in Großbuchstaben) |
| einen Alias, der ein reserviertes Wort von HANA ist (`AS order`) | `AS "ORDER"` |
| `CONCAT(a, b, c)`, `CHAR_LENGTH()`, `SUBSTR()`, `CURDATE()` | `(a \|\| b \|\| c)` (HANAs `CONCAT` nimmt zwei), `LENGTH()`, `SUBSTRING()`, `CURRENT_DATE` |
| `MD5(x)` | `LOWER(BINTOHEX(HASH_MD5(TO_VARBINARY(TO_NVARCHAR(x)))))` |
| `UNIX_TIMESTAMP()`, `FROM_UNIXTIME(t)` | `SECONDS_BETWEEN(TO_TIMESTAMP('1970-01-01 00:00:00'), CURRENT_UTCTIMESTAMP)`, `ADD_SECONDS(...)` |
| `a & b`, `a \| b`, `~a` (die Sprachmasken) | `BITAND(a, b)`, `BITOR(a, b)`, `BITNOT(a)` |
| `a >> n`, `a << n` | `TO_BIGINT(FLOOR((a) / POWER(2, n)))`, `TO_BIGINT((a) * POWER(2, n))` (HANA hat keine Verschiebung) |
| `SELECT DISTINCT` über einem NCLOB (`cc.*` der Inhaltsklassen) | die Anweisung ohne `DISTINCT`, die doppelten Zeilen verwirft der Treiber |
| `LIKE 'a\_%'` | `LIKE 'a\_%' ESCAPE '\'` (HANA hat kein Standard-Escape-Zeichen) |
| `... LIMIT 20, 10` | `LIMIT 10 OFFSET 20` (`LIMIT n OFFSET m` ist HANAs eigenes) |
| `SELECT 1` | `SELECT 1 FROM DUMMY` |
| `INSERT ... VALUES (...), (...)` | ein `INSERT` je Zeile (HANA nimmt eine Zeile je `VALUES`) |
| `TRUNCATE TABLE t` | `DELETE FROM t` (HANAs `TRUNCATE` schließt die Transaktion ab, in der es läuft) |
| `DROP TABLE IF EXISTS t`, `CREATE ... IF NOT EXISTS` | zuerst wird der Katalog gefragt |
| ein Vergleich, `ORDER BY` oder `GROUP BY` einer Textspalte (`NCLOB`) | `TO_NVARCHAR(spalte)` (HANA kann ein NCLOB nicht vergleichen oder sortieren) |
| `CREATE TEMPORARY TABLE` | eine globale temporäre Tabelle |

**Literale als Parameter.** HANA übersetzt jeden Anweisungstext, den es noch nicht gesehen hat, auf dem Testserver je
etwa 20 ms, und der Kernel schreibt seine Werte in seine Anweisungen. Mit `BindLiterals=enabled` sendet der Treiber die
Werte eines `INSERT` und die in einem `WHERE` verglichenen Werte (`= 5`, `IN (...)`, `LIKE 'x%'`) als Parameter, sodass
eine Anweisung einmal für alle ihre Werte übersetzt wird (`expHANADialect::parameterize()`). Das brachte Kernel-Schema
und Daten von 8,5 auf 2,5 Minuten und das Indizieren eines Artikels für die Suche von etwa 150 ms auf 6 ms je Wort.

**Unicode im Text einer Anweisung.** HANAs ODBC-Treiber liest den Text einer Anweisung (nicht ihre Parameter) in einer
Ein-Byte-Codepage, ein `ä` im Text einer Anweisung kommt also als zwei Zeichen an. Die Literale, die nach dem Binden
noch im Text stehen und mehr als ASCII enthalten, gehen als `TO_NVARCHAR(BINTOSTR(X'...'))` ihrer Bytes in CESU-8,
HANAs eigenem UTF-8 (`expHANADialect::unicodeLiterals()`).

**Neue IDs.** Eine `auto_increment`-Spalte ist ein `INTEGER` mit dem Kommentar `exp:auto_increment`, der Sequenz
`S_<tabelle>` und dem Trigger `T_<tabelle>_BI`. Der Trigger nummeriert eine Zeile ohne ID, mit `NULL` oder `0`
(MySQLs Arten, nach der nächsten Nummer zu fragen) und schiebt die Sequenz über eine ID hinaus, die ein Insert selbst
mitgibt; `lastSerialID()` liest `CURRVAL` der Sequenz, immer die letzte Nummer dieser Sitzung. Ein Insert mit eigener
ID unterhalb der Sequenz verbraucht ebenfalls eine Nummer, IDs können also Lücken haben.

**Textspalten.** `text` und `longtext` sind `NCLOB`; eine Textspalte in einem Index ist `NVARCHAR(5000)` (der Kommentar
`exp:longtext` sagt, dass sie Text war), da HANA kein NCLOB indiziert. Eine Spalte, die in jeder Tabelle, die sie hat,
ein NCLOB ist, wird als `TO_NVARCHAR(spalte)` verglichen und sortiert; die Liste dieser Spalten liest der Treiber
einmal je Prozess und Schema.

**Indexnamen.** HANAs Indexnamen sind in einem Schema eindeutig, die der `.dba` je Tabelle: ein Index heißt
`<TABELLE>$<INDEX>` und wird ohne die Tabelle zurückgelesen.

**Temporäre Tabellen.** Ein `CREATE TEMPORARY TABLE` des Kernels wird eine globale temporäre Tabelle: ihre Definition
ist dauerhaft und geteilt, ihre Zeilen gehören der Sitzung. Die Definition wird einmal angelegt, über eine zweite
Verbindung (DDL schließt in HANA ab); spätere Anfragen löschen stattdessen ihre eigenen Zeilen.

## Was geht und was nicht

Gemessen am 26.11.2026 mit den Live-Tests (unten) und einer vollständigen Installation, auf SAP HANA Express
Edition 2.00.088.00 (das Image `saplabs/hanaexpress`, 12 GB und 4 CPUs für den Container, etwa 4 GB belegt) auf einem
ausgelasteten Testserver (Last 17 bis 40 bei 12 CPUs), PHP 8.5.13, der HANA-ODBC-Treiber desselben Release über
unixODBC 2.3.12.

| | SAP HANA 2.0 SPS 08 |
|---|---|
| Verbindung (odbc-Erweiterung und PDO_ODBC), Version, Katalog (`relationList()`, `eZTableList()`), ein fehlendes Schema angelegt | geht |
| Kernel-Schema: 153 Tabellen, ihre Indizes, Sequenzen und Trigger | geht |
| Kernel-Daten (`share/db_data.dba`, Zeilen mit eigenen IDs), nächste ID nach den Daten | geht (eine eigene ID unterhalb der Sequenz verbraucht eine Nummer: IDs können Lücken haben) |
| das Schema gleich der `.dba` zurückgelesen (Upgrade-Prüfung) | geht |
| Upgrades: eine Spalte mit Standardwert zu einer Tabelle mit Zeilen hinzufügen, ändern, löschen; einen Index anlegen und löschen | geht |
| `lastSerialID()` je Tabelle und je Sitzung | geht |
| Paging, Transaktionen (Rollback, Commit, verschachtelt), `FOR UPDATE`-Zeilensperren, `LOCK TABLE` | geht |
| `sqlUpsert()` (MERGE), `sqlLimit()`, `sqlGroupConcat()` (sortiert), `sqlUnixTimestamp()` in beide Richtungen nach 2038 | geht |
| `md5()` | geht; für ein Zeichen außerhalb der Basic Multilingual Plane (ein Emoji) ist der Hash der von HANAs CESU-8, nicht von UTF-8 |
| Unicode-Text hinein und heraus (mit Emoji), 340 KB über einen Parameter, ein NCLOB verglichen und sortiert | geht |
| `LENGTH()` von Text mit Emoji | zählt jedes solche Zeichen doppelt (HANA hält es als zwei UTF-16-Einheiten) |
| globale temporäre Tabellen des Kernels (Suche) | geht |
| Kernel-Schema und Daten in einer Transaktion | 2,5 Minuten mit `BindLiterals` (8,5 ohne) |
| eine Anweisung auf dem Testserver | etwa 3 ms für `SELECT 1`, 6 bis 14 ms für ein Insert einer Zeile in eine Column-Tabelle mit ihrem Trigger |
| eine ganze Site: `install.php --db=hana`, das Multisite-Paket | geht: 16 Schritte, 9 von 9 Prüfungen, in 109 Minuten auf dem ausgelasteten Testserver (291 Objekte; das meiste davon der Suchindex, der Wort für Wort mit 6 bis 14 ms je Insert schreibt). Startseite, Admin-Anmeldung und eine Suche antworten mit 200 (0,2 bis 0,7 s warm); das Admin-Dashboard braucht dort 18 bis 40 s (Hunderte Anweisungen zu je 3 ms und mehr). Der Testbaum hatte keine paketierten Erweiterungen (Layouts, Tags ...), deren Zeilen wurden also nicht geladen; keine Anweisung des Kernels schlug fehl |
| nur PDO_ODBC | die Live-Tests des Treibers bestehen; die Trigger des Schemas anzulegen braucht die odbc-Erweiterung daneben |
| DFS-Cluster-Backend (mehrere Webserver), Lesereplikate | **noch nicht** |
| `createDatabase()` | legt ein Schema an; `removeDatabase()` löscht eines mit `CASCADE` |

## Datentabellen-Verbindungen

Die Datentabellen-Schicht hat die Engine `hana` (`datatable.ini [ConnectionSettings] Engines[]`): eine benannte
Verbindung öffnet sich über `expHANADB` (Optionen `odbc_driver`, `layer`, `tenant`, `encrypt`, `connection_string`;
die Datenbank ist das Schema), und `expDataTableSchema` legt Tabellen darauf über `expHANASchema` an und aktualisiert
sie. Eine solche Verbindung ist auch der Weg, aus HANA zu importieren: ihre Tabellen lassen sich als Datentabellen
lesen oder mit den Datentabellen-Werkzeugen kopieren.

```ini
[Connection_erp]
Engine=hana
Server=hana.example.com
Port=30041
Database=ERP
User=READER
Secret=env:ERP_HANA_PASSWORD
```

## Tests

| Datei | Was |
|---|---|
| `tests/tests/lib/ezdb/hana/expHANADialectTest.php` | die Umschreibregeln, Paging, Binden, Unicode-Literale, NCLOB-Spalten, Helfer, Fehler, Verbindungsstring, Versionen (ohne Server) |
| `tests/tests/lib/ezdb/hana/expHANADBTest.php` | der Treiber über einen aufzeichnenden Transport: fehlendes Schema, Paging, IDs, Binden, Transaktionen, Sperren, Wiederholungen, neue Verbindungen, temporäre Tabellen (ohne Server) |
| `tests/tests/lib/ezdbschema/expHANASchemaTest.php` | Typen, Sequenzen und Trigger, Indexnamen, die Markierungen von char-, Text- und auto_increment-Spalten, Upgrade-Anweisungen (ohne Server) |
| `tests/tests/lib/ezdb/hana/live/expHANALiveTest.php` | ein Live-Server: IDs je Sitzung, Paging, die Wörter des Kernels, Transaktionen, Unicode und langer Text, NCLOB-Vergleiche, temporäre Tabellen, Fehler, neue Verbindung |
| `tests/tests/lib/ezdb/hana/live/expHANASchemaLiveTest.php` | ein Live-Server: Kernel-Schema und Daten, das zurückgelesene Schema, Upgrades, die Helfer |

Die Tests ohne Server laufen in der Testsuite `hana` (und `lib`), die Live-Tests in `hana-live`: setzen Sie
`EXP_TEST_HANA_HOST`, `_PORT`, `_USER`, `_PASSWORD` (oder `_PASSWORD_FILE`), `_SCHEMA` (ein eigenes Schema: der
Schema-Test leert es), `_DRIVER` und `_LAYER`. Der GitHub-Job `hana` führt sie gegen SAP HANA Express Edition
(`saplabs/hanaexpress`) aus, mit dem HANA-ODBC-Treiber aus demselben Image, unter PHP 8.5 und auf beiden Schichten.
