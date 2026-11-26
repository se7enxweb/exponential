# Firebird-Treiber

Diese Seite ist für Administratoren, die Exponential mit Firebird betreiben wollen, und für Mitwirkende, die am
Treiber arbeiten. Ab Exponential 6.0.15 hat der Kernel einen Firebird-Treiber, `expFirebirdDB` in `lib/ezdb/classes/`,
mit dem Schema-Handler `expFirebirdSchema`, dem Kernel-Schema in `kernel/sql/firebird/kernel_schema.sql`, Einträgen
in den Installern und einer Engine für Datentabellen.

**Stand.** Eine ganze Site läuft mit Firebird 4.0 und 5.0: `install.php --db=firebird` installiert das
Multisite-Paket (unten), und das Kernel-Schema mit seinen Daten, Aktualisierungen und die portablen SQL-Helfer bestehen
ihre Live-Tests auf beiden Versionen. Noch nicht: ein DFS-Cluster-Backend (mehrere Webserver an einer
Firebird-Datenbank) und Lesereplikate.

## Inhalt

1. [Unterstützte Server und PHP](#unterstützte-server-und-php)
2. [Einstellungen](#einstellungen)
3. [Die Datenbank anlegen](#die-datenbank-anlegen)
4. [Eine Site installieren](#eine-site-installieren)
5. [Wie das SQL des Kernels zu dem von Firebird wird](#wie-das-sql-des-kernels-zu-dem-von-firebird-wird)
6. [Was geht und was nicht](#was-geht-und-was-nicht)
7. [Verbindungen für Datentabellen](#verbindungen-für-datentabellen)
8. [InterBase](#interbase)
9. [Tests](#tests)

## Unterstützte Server und PHP

| | Unterstützt | Warum |
|---|---|---|
| Firebird 5.0, 4.0 | ja | bei jedem Push getestet (GitHub-Job `firebird`) |
| Firebird 3.0 und älter | nein | Namen von höchstens 31 Zeichen (der Kernel hat längere Indexnamen), kein `CRYPT_HASH()`, keine Zeitzonen |
| PHP 8.4, 8.5 mit `pdo_firebird` | ja | |
| PHP 8.3 und älter mit `pdo_firebird` | nein | ihr `pdo_firebird` hält im Autocommit-Modus eine implizite Transaktion offen: `beginTransaction()` schlägt fehl („There is already an active transaction“), ein Rollback macht nichts rückgängig, und was andere Verbindungen schreiben, bleibt unsichtbar (gemessen mit 8.3.37) |
| PHPs Erweiterung `interbase`/`fbird_*` | nein | der Treiber spricht nur PDO |

`pdo_firebird` gehört zu PHP; es braucht die Client-Bibliothek von Firebird (`libfbclient2`, unter Debian und Ubuntu
das Paket `firebird-dev`, um PHP damit zu bauen). Die 3.0-Client-Bibliothek von Debian 12 und Ubuntu 24.04 spricht mit
Firebird 4 und 5.

## Einstellungen

```ini
[DatabaseSettings]
DatabaseImplementation=firebird
Server=db.example.com
Port=3050
User=EXPONENTIAL
Password=...
Database=exponential

[FirebirdSettings]
Role=
Charset=UTF8
```

`ImplementationAlias[firebird]` (und `fb`) nennt `expFirebirdDB`. `Database` ist ein Alias aus der `databases.conf`
des Servers (empfohlen) oder der Pfad der Datenbankdatei auf dem Server; ohne `Server` öffnet die Client-Bibliothek die
Datei selbst (eingebettet) oder über den lokalen Server.

| `[FirebirdSettings]` | Bedeutung |
|---|---|
| `Role` | die SQL-Rolle der Verbindung; leer: keine |
| `Charset` | der Zeichensatz der Verbindung; `UTF8`, den der Text von Exponential braucht |
| `DeadlockRetries` | wie oft eine einzelne Anweisung außerhalb einer Transaktion nach einem Update-Konflikt oder Deadlock erneut gesendet wird (3) |
| `DeadlockRetryWait` | Millisekunden vor dem ersten neuen Versuch, verdoppelt, verdreifacht ... für die nächsten (50) |
| `ReconnectOnLoss` | `enabled`: eine verlorene Verbindung außerhalb einer Transaktion wird neu geöffnet (dauerhafte Velocity-Worker) |
| `InListChunkSize` | Werte je `IN`-Liste (1.500, die Grenze von Firebird 4) |

Die Meldung einer gescheiterten Verbindung enthält nie das Passwort.

## Die Datenbank anlegen

Firebird legt eine Datenbank außerhalb einer Verbindung an, daher können weder der Installer noch `createDatabase()`
eine anlegen. Legen Sie sie mit `isql` an (oder mit `FIREBIRD_DATABASE` des offiziellen Containers), mit Seiten von
16 KB und UTF8:

```sql
CREATE DATABASE 'exponential' USER 'SYSDBA' PASSWORD '...' PAGE_SIZE 16384 DEFAULT CHARACTER SET UTF8;
```

**Die Seitengröße zählt.** Firebird indiziert höchstens ein Viertel einer Seite je Schlüssel und rechnet vier Bytes je
Zeichen in UTF8. Mit 16 KB bekommen der URL-Alias-Text, die Pfade und die Bildpfade des Kernels 811 bis 1.021 Zeichen
in ihren Indizes, mit den voreingestellten 8 KB nur etwa 400. Der Schema-Handler liest die Seitengröße der Datenbank
und bemisst die Textspalten der Indizes danach.

## Eine Site installieren

```bash
EXP_INSTALL_DB_PASSWORD=... php bin/php/install.php --apply --db=firebird \
    --db-host=db.example.com --db-name=exponential --db-user=EXPONENTIAL
```

Der Kickstarter nimmt `Type=firebird` in `[database_choice]`, der Setup-Assistent bietet Firebird an, wenn PHP
`pdo_firebird` hat. `--db-action=remove` (die Voreinstellung) entfernt zuerst die Tabellen, Sequenzen und Sichten der
Datenbank.

## Wie das SQL des Kernels zu dem von Firebird wird

Der Kernel schreibt die Wörter von MySQL, wo er seine portablen Helfer (`sqlLimit()`, `sqlUpsert()`,
`sqlForUpdate()` ...) nicht benutzt; der Treiber schreibt jede Anweisung auf einem Lexer um
(`expFirebirdDialect::rewrite()`), so dass ein Wort in einem Zeichenkettenliteral oder einem Kommentar nie berührt
wird:

| Der Kernel schreibt | Firebird bekommt |
|---|---|
| `` `name` `` | `"NAME"` (Firebird speichert einen Namen ohne Anführungszeichen in Großbuchstaben) |
| eine Spalte mit dem Namen eines reservierten Worts: `value`, `count`, `time`, `day`, `position` | `"VALUE"` usw., wo das Wort als Name steht, nicht wo es eine Funktion oder ein Typ ist |
| `LENGTH()`, `IFNULL()`, `NOW()`, `GREATEST()`, `LEAST()` | `CHAR_LENGTH()`, `COALESCE()`, `LOCALTIMESTAMP`, `MAXVALUE()`, `MINVALUE()` |
| `CONCAT(a, b)`, `SUBSTRING(s, f, n)`, `MD5(x)` | `(a \|\| b)`, `SUBSTRING(s FROM f FOR n)`, `LOWER(HEX_ENCODE(CRYPT_HASH(x USING MD5)))` |
| `a & b`, `a \| b`, `~a`, `a >> n`, `a << n` (die Sprachmasken) | `BIN_AND(a, b)`, `BIN_OR(a, b)`, `BIN_NOT(a)`, `BIN_SHR(a, n)`, `BIN_SHL(a, n)` |
| `LIKE 'a\_%'` | `LIKE 'a\_%' ESCAPE '\'` (Firebird hat kein voreingestelltes Escape-Zeichen) |
| `... LIMIT 10 OFFSET 20`, `LIMIT 20, 10` | `OFFSET 20 ROWS FETCH NEXT 10 ROWS ONLY` |
| `SELECT ... FOR UPDATE` | `SELECT ... FOR UPDATE WITH LOCK` (ohne `WITH LOCK` sperrt Firebird nichts) |
| `SELECT 1` | `SELECT 1 FROM RDB$DATABASE` |
| `TRUNCATE TABLE t` | `DELETE FROM t` |
| `DROP TABLE IF EXISTS t`, `CREATE ... IF NOT EXISTS` | zuerst wird der Katalog gefragt (Firebird 4 und 5 haben beides nicht) |
| ein Zeichenkettenliteral über 16.000 Bytes | ein gebundener Parameter (Firebird lehnt ein Literal über 64 KB ab) |
| `CREATE TEMPORARY TABLE` | eine globale temporäre Tabelle, `ON COMMIT PRESERVE ROWS` |

**Neue IDs.** Eine `auto_increment`-Spalte ist ein `INTEGER` mit der Sequenz `S_<Tabelle>` und dem Trigger
`T_<Tabelle>_BI`. Der Trigger nummeriert eine Zeile ohne ID, mit `NULL` oder `0` (die Arten von MySQL, nach der
nächsten Nummer zu fragen), schiebt die Sequenz über eine ID hinaus, die ein Insert selbst mitbringt (die Zeilen der
Datendateien und die Kopien, die der Kernel für eine neue Version anlegt), und legt die ID in den Kontextvariablen der
Verbindung ab, wo `lastSerialID()` sie liest: immer die ID des eigenen Inserts dieser Verbindung.

**Temporäre Tabellen.** Firebird 4 und 5 haben keine temporären Tabellen einer Sitzung. Ein `CREATE TEMPORARY TABLE`
des Kernels (Suche, Knotenzählungen, Rechte) wird zu einer globalen temporären Tabelle: ihre Definition ist dauerhaft
und gemeinsam, ihre Zeilen gehören der Verbindung. Die Definition wird einmal angelegt, über eine zweite Verbindung,
damit sie sofort festgeschrieben ist; spätere Anfragen leeren stattdessen ihre eigenen Zeilen. Die Namen stehen je
Muster fest (`ezsearch_tmp_0`), so bleibt je Anfrage keine Definition zurück.

**Transaktionen.** pdo_firebird läuft im Autocommit-Modus (read committed); eine eZDB-Transaktion ist eine
Firebird-Transaktion von `begin()` bis `commit()`. `lock()` (MySQLs `LOCK TABLES`) öffnet eine Transaktion und hält
die Zeilen, die sie schreibt; ganze Tabellen reserviert Firebird nur beim Start einer Transaktion
(`SET TRANSACTION ... RESERVING`), was PDO nicht anbietet.

## Was geht und was nicht

Gemessen am 26.11.2026 mit den Live-Tests (unten) und einer vollständigen Installation, mit Firebird 5.0.4 und 4.0
(die offiziellen Images `firebirdsql/firebird`) und PHP 8.4.28 und 8.5.13, pdo_firebird über die 3.0-Client-Bibliothek.

| | Firebird 5.0 | Firebird 4.0 |
|---|---|---|
| verbinden, Version, Katalog (`relationList()`, `eZTableList()`) | geht | geht |
| Kernel-Schema: 153 Tabellen, ihre Indizes, Sequenzen und Trigger | geht | geht |
| Kernel-Daten (`share/db_data.dba`, Zeilen mit eigenen IDs), nächste ID nach den Daten | geht | geht |
| Schema zurückgelesen gleich der `.dba` (Upgrade-Prüfung) | geht | geht |
| Aktualisierungen: Spalte mit Vorgabe zu einer Tabelle mit Zeilen hinzufügen, ändern, entfernen; Index anlegen und entfernen | geht | geht |
| `lastSerialID()` je Tabelle und je Verbindung | geht | geht |
| Seiten, Transaktionen (Rollback, Commit, verschachtelt), Zeilensperren mit `FOR UPDATE` | geht | geht |
| `sqlUpsert()` (MERGE), `sqlLimit()`, `sqlUnixTimestamp()` hin und zurück über 2038 hinaus, `md5()` | geht | geht |
| `sqlGroupConcat()` | geht, **ohne Reihenfolge**: `LIST()` hat kein `ORDER BY` | ebenso |
| Text in einem Index länger als sein `VARCHAR` (ein URL-Alias über 811 Zeichen) | **geht nicht**: abgelehnt (String-Kürzung) | ebenso |
| eine ganze Site: `install.php --db=firebird`, das Multisite-Paket | geht | geht |
| Sperrwartezeiten (`FOR UPDATE`, eine Zeile, die eine andere Transaktion schreibt) | gehen, **ohne Zeitgrenze**: pdo_firebird startet seine Transaktionen mit `WAIT` und ohne `LOCK TIMEOUT` und hat keine Einstellung dafür (`SET TRANSACTION` lässt sich darüber nicht senden) | ebenso |
| Geschwindigkeit | eine Anweisung in eigener Transaktion kostet auf dem Testserver etwa 20 ms (das Commit wird auf die Platte geschrieben), eine innerhalb einer Transaktion etwa 4 ms; das Multisite-Paket (291 Objekte) installierte sich auf einem ausgelasteten Testserver in 21 und 22 Minuten: 16 Schritte, 0 Fehler, 9 von 9 Prüfungen | ebenso |
| DFS-Cluster-Backend (mehrere Webserver) | **noch nicht**: die anderen Backends begrenzen ihre Sperrwartezeit (`lock_timeout` von PostgreSQL, `LOCK_TIMEOUT` von SQL Server), was pdo_firebird nicht kann | noch nicht |
| Lesereplikate | **nein** | nein |
| `createDatabase()`, `removeDatabase()` | **nein**: Firebird legt Datenbanken außerhalb einer Verbindung an und entfernt sie dort | nein |
| PHP 8.3 und älter | **nein** (siehe oben) | nein |

## Verbindungen für Datentabellen

Die Datentabellen-Schicht hat die Engine `firebird` (`datatable.ini [ConnectionSettings] Engines[]`): eine benannte
Verbindung zu einer Firebird-Datenbank öffnet sich über `expFirebirdDB`, und `expDataTableSchema` legt Tabellen über
`expFirebirdSchema` darauf an und aktualisiert sie. Eine solche Verbindung ist auch der Weg, aus einer
Firebird-Datenbank zu importieren: ihre Tabellen lassen sich als Datentabellen lesen oder mit den Werkzeugen der
Datentabellen kopieren.

```ini
[Connection_erp]
Engine=firebird
Server=fb.example.com
Port=3050
Database=erp
User=READER
Secret=env:ERP_DB_PASSWORD
```

## InterBase

InterBase wird nicht unterstützt. Sein SQL hat sich vor langer Zeit von dem von Firebird getrennt: kein `IDENTITY` und
kein `OFFSET ... FETCH`, kein `CRYPT_HASH()`, keine globalen temporären Tabellen dieser Art, andere Spalten im Katalog
und eine eigene Client-Bibliothek, gegen die pdo_firebird nicht gebaut ist. Der Treiber meldet einen InterBase-Server
als nicht unterstützt.

## Tests

| Datei | Was |
|---|---|
| `tests/tests/lib/ezdb/firebird/expFirebirdDialectTest.php` | die Umschreibregeln, Seiten, Helfer, temporäre Tabellen, Fehler, DSN, Versionen (ohne Datenbank) |
| `tests/tests/lib/ezdb/firebird/expFirebirdDBTest.php` | der Treiber über einen aufzeichnenden Transport: Seiten, IDs, Transaktionen, Wiederholungen, neues Verbinden, temporäre Tabellen (ohne Datenbank) |
| `tests/tests/lib/ezdbschema/expFirebirdSchemaTest.php` | Typen, Sequenzen und Trigger, Indexschlüssel je Seitengröße, Aktualisierungsanweisungen (ohne Datenbank) |
| `tests/tests/lib/ezdb/firebird/live/expFirebirdLiveTest.php` | ein Live-Server: IDs, Seiten, Transaktionen, Sperren, Unicode, langer Text, temporäre Tabellen, reservierte Namen |
| `tests/tests/lib/ezdb/firebird/live/expFirebirdSchemaLiveTest.php` | ein Live-Server: das Kernel-Schema und seine Daten, das zurückgelesene Schema, Aktualisierungen, die Helfer |

Die Tests ohne Datenbank laufen in der Testsuite `firebird` (und `lib`), die Live-Tests in `firebird-live`: setzen Sie
`EXP_TEST_FIREBIRD_HOST`, `_PORT`, `_USER`, `_PASSWORD` (oder `_PASSWORD_FILE`) und `_DATABASE` (eine eigene
Datenbank: der Schema-Test leert sie). Der GitHub-Job `firebird` führt sie gegen `firebirdsql/firebird:5` und `:4` als
Service-Container mit PHP 8.4 und 8.5 aus.
