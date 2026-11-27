# SAP-ASE-Treiber

Diese Seite ist für Administratoren, die Exponential auf SAP Adaptive Server Enterprise (ASE, früher Sybase ASE)
betreiben wollen, und für Mitwirkende am Treiber. Ab Exponential 6.0.15 hat der Kernel einen ASE-Treiber, `expASEDB` in
`lib/ezdb/classes/`, mit dem Dialekt `expASEDialect`, dem Schema-Handler `expASESchema`, Einträgen in den Installern
und einer Datentabellen-Engine.

**Stand: geschrieben, nicht gegen einen Server getestet.** Für die Tests dieses Treibers war kein SAP-ASE-Server zu
haben: SAP veröffentlicht kein Container-Image von ASE, die ASE Developer Edition ist ein Download hinter SAPs
Registrierung, und ihre Lizenz erlaubt nicht, sie als Image weiterzugeben (die Community-Images von ASE auf Docker Hub
sind solche Weitergaben und wurden deshalb nicht verwendet). Alles hier folgt den Referenzhandbüchern von ASE 16.0 und
ist durch Tests ohne Server abgedeckt (der Dialekt, der Treiber über einen aufzeichnenden Transport, der
Schema-Handler); nichts davon lief gegen ASE. Der Treiber ist ein Ausgangspunkt für eine Site, die ASE hat – bitte
melden Sie, was Sie finden.

## Inhalt

1. [Unterstützte Server und PHP](#unterstützte-server-und-php)
2. [Einstellungen](#einstellungen)
3. [Den Server vorbereiten](#den-server-vorbereiten)
4. [Eine Site installieren](#eine-site-installieren)
5. [Wie das SQL des Kernels zu dem von ASE wird](#wie-das-sql-des-kernels-zu-dem-von-ase-wird)
6. [Was gehen sollte und was nicht](#was-gehen-sollte-und-was-nicht)
7. [Datentabellen-Verbindungen](#datentabellen-verbindungen)
8. [Tests](#tests)

## Unterstützte Server und PHP

| | Unterstützt | Warum |
|---|---|---|
| ASE 16.0, 15.7 | dafür geschrieben, ungetestet | `MERGE`, `int`-Identity-Spalten und `hash()` brauchen 15.7 |
| ASE 15.5 und älter | nein | kein `MERGE`; der Treiber meldet sie als nicht unterstützt |
| PHP 8.0 bis 8.5 mit `pdo_dblib` (FreeTDS) | dafür geschrieben | TDS 5.0, UTF-8 |
| PHP mit `PDO_ODBC` und dem ODBC-Treiber von FreeTDS oder dem ASE-ODBC-Treiber von SAP | dafür geschrieben | `[ASESettings] Layer=pdo_odbc` |

ASE und Microsoft SQL Server haben denselben Ursprung und das TDS-Protokoll, deshalb erweitert `expASEDB` den
[SQL-Server-Treiber](sqlserver-datenbanktreiber.md) (`expSQLServerDB`): die Wiederholung nach einem Deadlock, das neue
Öffnen einer verlorenen Verbindung, `#temporäre` Tabellen, `IDENTITY_INSERT` um ein Insert mit eigener ID und der
Transport über PDO sind die des SQL-Server-Treibers. Anders sind das SQL, der Katalog und die Verbindung.

## Einstellungen

```ini
[DatabaseSettings]
DatabaseImplementation=ase
Server=ase.example.com
Port=5000
User=exponential
Password=...
Database=exponential

[ASESettings]
Layer=auto
```

`ImplementationAlias[ase]` (und `sybase`) nennt `expASEDB`.

| `[ASESettings]` | Bedeutung |
|---|---|
| `Layer` | `auto` (pdo_dblib, dann PDO_ODBC), `pdo_dblib` oder `pdo_odbc` |
| `ODBCDriver` | der ODBC-Treiber für PDO_ODBC: `FreeTDS` (mit `TDS_Version=5.0`) oder SAPs `Adaptive Server Enterprise` |
| `ApplicationName` | der Programmname, den der Server zeigt (`sp_who`) |
| `LoginTimeout` | Sekunden, die auf die Anmeldung gewartet wird (15) |
| `LockTimeout` | Millisekunden Wartezeit auf eine Sperre, gesendet als `set lock wait` in ganzen Sekunden; -1: die Einstellung des Servers |
| `DeadlockRetries`, `DeadlockRetryWait` | eine einzelne Anweisung außerhalb einer Transaktion wird nach einem Deadlock (1205) erneut gesendet |
| `ReconnectOnLoss` | `enabled`: eine verlorene Verbindung außerhalb einer Transaktion wird neu geöffnet |
| `InListChunkSize` | Werte je `IN`-Liste (1.000) |
| `EmptyStringSpace` | `enabled`: ein Wert aus genau einem Leerzeichen wird als `''` gelesen (siehe unten) |

Jede Verbindung sendet zuerst `set quoted_identifier on`, `set chained off` (Autocommit; eine Transaktion ist
`BEGIN TRANSACTION` bis `COMMIT`), `set textsize 2147483647`, `set ansinull on` und `set string_rtruncation off`.

## Den Server vorbereiten

- **Zeichensatz utf8, logische Seiten von 16 KB.** Der Schema-Handler schreibt Text als `UNIVARCHAR`/`UNITEXT`
  (Unicode unabhängig vom Zeichensatz des Servers), der Server sollte trotzdem `utf8` als Standardzeichensatz haben,
  damit das UTF-8 von FreeTDS sauber umgewandelt wird, und Seiten von 16 KB: eine Textspalte in einem Index ist
  `UNIVARCHAR(1000)` (2.000 Bytes), was ein Server mit 2-KB-Seiten nicht indizieren kann.
- **Die Datenbank muss existieren.** `createDatabase()` sendet `CREATE DATABASE <name>` (auf dem Standard-Device), was
  ein Administrator meist auf eigene Devices legen will: legen Sie sie vorher an, mit eigenem Log.
- **„allow nulls by default“ ist nicht nötig**: jede Spalte, die der Schema-Handler schreibt, sagt `NULL` oder
  `NOT NULL`.
- **Sperrschema.** `SELECT ... FOR UPDATE` sperrt Zeilen nur in Tabellen mit `datarows`-Sperren (ab ASE 15.7);
  `sp_configure 'lock scheme', 0, datarows` vor der Installation macht das zum Standard für neue Tabellen.

## Eine Site installieren

```bash
EXP_INSTALL_DB_PASSWORD=... php bin/php/install.php --apply --db=ase \
    --db-host=ase.example.com --db-name=exponential --db-user=exponential
```

Der Kickstarter nimmt `Type=ase` in `[database_choice]` (auch `sybase`). Der Setup-Assistent listet ASE nicht auf: es
hat keine eigene PHP-Erweiterung, an der er es erkennen könnte (pdo_dblib dient auch SQL Server). `--db-action=remove`
(der Standard) löscht zuerst die Tabellen der Datenbank.

## Wie das SQL des Kernels zu dem von ASE wird

Der Kernel schreibt MySQLs Wörter, wo er seine portablen Helfer nicht verwendet; `expASEDialect::rewrite()` liest jede
Anweisung mit dem Lexer des SQL-Server-Treibers, sodass ein Wort in einem String-Literal oder Kommentar nie
angefasst wird:

| Der Kernel schreibt | ASE bekommt |
|---|---|
| `` `name` `` | `[name]` |
| eine Spalte mit dem Namen eines reservierten Worts von ASE (`count`, `level`, `role`, `key` ...) | `[count]`, wo es als Name steht, nicht als Aufruf (`COUNT(*)`) |
| `CONCAT(a, b)`, `LENGTH()`, `SUBSTR()`, `SUBSTRING(s, f)`, `IFNULL()` | `(a + b)`, `char_length()`, `substring()`, `substring(s, f, char_length(s))`, `isnull()` |
| `NOW()`, `CURDATE()`, `MD5(x)` | `getdate()`, `current_date()`, `lower(hash(x, 'md5'))` |
| `UNIX_TIMESTAMP()`, `FROM_UNIXTIME(t)` | `datediff(ss, '19700101', getutcdate())`, `dateadd(ss, t, '19700101')` |
| `LIKE 'a\_%'`, `LIKE '[x]%'` | `LIKE 'a\_%' ESCAPE '\'`, `LIKE '\[x]%' ESCAPE '\'` (ASE liest `[...]` als Zeichenmenge) |
| `SELECT ... LIMIT 10 OFFSET 20` (und Limit und Offset von arrayQuery()) | `SELECT TOP 30 ...`, die ersten 20 Zeilen verwirft der Treiber (kein `OFFSET` vor ASE 16.0 SP02) |
| `INSERT ... VALUES (...), (...)` | ein `INSERT` je Zeile in einem Batch |
| `TRUNCATE TABLE t` | `DELETE FROM t` (bleibt in der Transaktion) |
| `CREATE TEMPORARY TABLE t (...)` | `CREATE TABLE #t (...)` mit den Typen von ASE, `NULL` ausgeschrieben |
| `GROUP_CONCAT`, `sqlGroupConcat()` | abgelehnt (`expDBUnsupportedException`): ASE hat keine String-Aggregation |
| `sqlUpsert()` | `MERGE INTO ... USING (SELECT ...) AS s ON ...` |

**Neue IDs.** Eine `auto_increment`-Spalte ist `INT IDENTITY`. Ein Insert, das ihr `NULL` oder `0` gibt, lässt die
Spalte weg, eines mit eigener ID läuft zwischen `SET IDENTITY_INSERT t ON` und `OFF`; `SELECT @@identity` geht im
selben Batch mit, und `lastSerialID()` liefert es.

**Leere Strings.** ASE speichert `''` in einer Textspalte variabler Länge als ein Leerzeichen. Mit
`EmptyStringSpace=enabled` liest der Treiber einen Wert aus genau einem Leerzeichen als `''`, also so, wie der Kernel
ihn geschrieben hat; ein echtes einzelnes Leerzeichen wird ebenfalls als `''` gelesen.

## Was gehen sollte und was nicht

Nichts in dieser Tabelle ist an einem Server gemessen; es ist das, wofür der Treiber geschrieben ist.

| | Erwartet |
|---|---|
| Verbindung (pdo_dblib mit TDS 5.0, PDO_ODBC), `@@version`, der Katalog (`sysobjects`, `syscolumns`, `sysindexes`) | geschrieben, durch Tests ohne Server abgedeckt |
| Kernel-Schema (153 Tabellen, `INT IDENTITY`, `UNIVARCHAR`/`UNITEXT`) und Daten | geschrieben, die DDL durch Tests ohne Server abgedeckt |
| Paging, `lastSerialID()`, `IDENTITY_INSERT`, Inserts mehrerer Zeilen, `#temporäre` Tabellen, Sperren (`LOCK TABLE ... IN EXCLUSIVE MODE`) | geschrieben, durch Tests ohne Server abgedeckt |
| `sqlUpsert()` (MERGE), `sqlLimit()` ohne Offset, Zeitstempel, `md5()` | geschrieben, durch Tests ohne Server abgedeckt |
| `sqlLimit()` mit Offset, `sqlGroupConcat()` | **nein**: mit `expDBUnsupportedException` abgelehnt |
| Sequenzen | **nein**: ASE hat keine (stattdessen Identity-Spalten) |
| Anwendungssperren (`getAppLock()`) | **nein**: liefert false |
| Lesereplikate, DFS-Cluster-Backend | **nein** |
| Vergleiche ohne Groß-/Kleinschreibung | hängen von der Sortierfolge des Servers ab (MySQLs Standard-Kollation unterscheidet nicht, die binäre Standardfolge von ASE schon) |
| eine ganze Site | **ungetestet** |

## Datentabellen-Verbindungen

Die Datentabellen-Schicht hat die Engine `ase` (`datatable.ini [ConnectionSettings] Engines[]`), als ungetestet
markiert: eine benannte Verbindung öffnet sich über `expASEDB` (Optionen `layer` und `odbc_driver`), und
`expDataTableSchema` legt Tabellen darauf über `expASESchema` an. Eine solche Verbindung ist auch der Weg, aus einer
bestehenden ASE-Datenbank zu lesen.

```ini
[Connection_legacy]
Engine=ase
Server=ase.example.com
Port=5000
Database=legacy
User=reader
Secret=env:LEGACY_DB_PASSWORD
```

## Tests

| Datei | Was |
|---|---|
| `tests/tests/lib/ezdb/ase/expASEDialectTest.php` | Namen, Funktionen, LIKE, Paging, Anweisungen, temporäre Tabellen, MERGE, Versionen, Fehler, DSNs |
| `tests/tests/lib/ezdb/ase/expASEDBTest.php` | der Treiber über einen aufzeichnenden Transport: Sitzungs-Setup, Paging, IDs, Sperren, temporäre Tabellen, Relationen |
| `tests/tests/lib/ezdbschema/expASESchemaTest.php` | Typen, ausgeschriebenes `NULL`, Identity, Indexschlüssel, Upgrade-Anweisungen |

Sie laufen in der Testsuite `ase` (der Schema-Test in `lib`); der GitHub-Job `hana` führt sie ebenfalls aus.
Live-Tests gibt es nicht: es gab keinen Server, gegen den sie laufen könnten.
