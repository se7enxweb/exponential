# IBM-Db2-Treiber

Diese Seite ist für Administratoren, die Exponential mit IBM Db2 betreiben wollen, und für Mitwirkende, die am Treiber
arbeiten. Ab Exponential 6.0.15 hat der Kernel einen Db2-Treiber, `expDb2DB` in `lib/ezdb/classes/`, mit dem
Schema-Handler `expDb2Schema`, dem Kernel-Schema in `kernel/sql/db2/kernel_schema.sql`, Einträgen in den Installern
und einer Engine für Datentabellen. Sein Geschwister für IBM Informix beschreibt der
[Informix-Treiber](informix-datenbanktreiber.md).

**Stand.** Eine ganze Site läuft mit Db2 12.1: `install.php --db=db2` installiert das Multisite-Paket (16 Schritte,
0 Fehler, 9 von 9 Prüfungen), und ihre Startseite, die Admin-Anmeldung und die Suche antworten. Das Kernel-Schema mit
seinen Daten, das zurückgelesene Schema, Aktualisierungen und die portablen SQL-Helfer bestehen ihre Live-Tests auf
Db2 12.1 und 11.5, auf beiden PHP-Schichten. Noch nicht: ein DFS-Cluster-Backend (mehrere Webserver an einer
Db2-Datenbank) und Lesereplikate.

## Inhalt

1. [Unterstützte Server und PHP](#unterstützte-server-und-php)
2. [Einstellungen](#einstellungen)
3. [Die Datenbank anlegen](#die-datenbank-anlegen)
4. [Eine Site installieren](#eine-site-installieren)
5. [Wie das SQL des Kernels zu dem von Db2 wird](#wie-das-sql-des-kernels-zu-dem-von-db2-wird)
6. [Was geht und was nicht](#was-geht-und-was-nicht)
7. [Verbindungen für Datentabellen](#verbindungen-für-datentabellen)
8. [Lizenzen](#lizenzen)
9. [Tests](#tests)

## Unterstützte Server und PHP

| | Unterstützt | Warum |
|---|---|---|
| Db2 für Linux, UNIX und Windows 12.1 | ja | bei jedem Push getestet (GitHub-Job `db2`, 12.1.6.0) und mit einer ganzen Site |
| Db2 11.5 | ja | die Untergrenze: `LIMIT ... OFFSET`, `DROP ... IF EXISTS`, `HASH()` und `LISTAGG()`; bei jedem Push getestet (11.5.9.0) |
| Db2 11.1 und älter | nein | älter als die Untergrenze, nicht getestet |
| Db2 für z/OS, Db2 für i | nein | anderes SQL und anderer Katalog |
| PHP mit `pdo_ibm` oder `ibm_db2` | ja | die PECL-Erweiterungen, gebaut gegen IBMs Data Server Driver for ODBC and CLI; getestet mit PHP 8.5 und in der CI mit 8.3 |
| PHPs `odbc` oder `pdo_odbc` mit IBMs ODBC- und CLI-Treiber unter Linux | **nein** | die `libdb2.so` des Treibers gibt unixODBC 32-Bit-Längen: ein NULL kommt als Datenmüll zurück und Zeilenzahlen als `-4294967295` (gemessen). `Layer=pdo_odbc` ist für einen ODBC-Treiber mit 64-Bit-Längen da (Windows, oder `libdb2o.so` eines vollen Db2-Clients) |

`pdo_ibm` und `ibm_db2` kommen aus PECL (`pecl install pdo_ibm ibm_db2`, oder `phpize && ./configure
--with-pdo-ibm=<clidriver>` bzw. `--with-IBM_DB2=<clidriver>`); sie brauchen IBMs Data Server Driver for ODBC and CLI
(den Ordner `clidriver`, ein kostenloser Download von IBM: `linuxx64_odbc_cli.tar.gz`). `shivammathur/setup-php`
installiert beides mit `extensions: pdo_ibm, ibm_db2`. `Layer=auto` nimmt `pdo_ibm`, sonst `ibm_db2`.

**pdo_ibm und die ODBC-Erweiterungen schließen sich aus.** PHP lädt Erweiterungen mit globalen Symbolen, und `pdo_ibm`
(über `libdb2.so`) wie PHPs `odbc`/`pdo_odbc` (über unixODBC) exportieren die Namen der ODBC-Funktionen: die zuerst
geladene beantwortet die Aufrufe der anderen (gemessen: `pdo_ibm` scheitert dann mit „Can't open lib 'IBM DB2 ODBC
DRIVER'“ von unixODBC). Ein PHP, das eine Db2-Site ausliefert, darf `odbc` und `pdo_odbc` nicht laden; geben Sie ihm ein
eigenes Scan-Verzeichnis (`PHP_INI_SCAN_DIR`) oder einen Pool ohne sie.

## Einstellungen

```ini
[DatabaseSettings]
DatabaseImplementation=db2
Server=db.example.com
Port=50000
User=db2inst1
Password=...
Database=EXP

[Db2Settings]
Schema=
```

`ImplementationAlias[db2]` (und `ibm_db2`) nennt `expDb2DB`. Ohne `Server` ist `Database` ein im Client katalogisierter
Datenbank-Alias (`db2cli.ini` oder `db2dsdriver.cfg`).

| `[Db2Settings]` | Bedeutung |
|---|---|
| `Layer` | `auto` (`pdo_ibm`, sonst `ibm_db2`), `pdo_ibm`, `ibm_db2` oder `pdo_odbc` |
| `Driver` | Name oder Pfad des ODBC-Treibers für `Layer=pdo_odbc` |
| `Schema` | das Schema der Tabellen der Site (`SET SCHEMA` bei jeder Verbindung); leer: das des Benutzers |
| `Security` | `SSL` für eine verschlüsselte Verbindung |
| `ConnectionOptions` | weitere Schlüsselwörter der Verbindungszeichenkette (`KEY=value;KEY=value`) |
| `StatementConcentrator` | `enabled` (die Vorgabe): die Verbindung bittet um Db2s Statement Concentrator (`StmtConcentrator=WITHLITERALS`), der Anweisungen, die sich nur in ihren Literalen unterscheiden, einmal übersetzt. Der Kernel schreibt seine Werte als Literale; ohne ihn wird jede Anweisung neu übersetzt (gemessen: 400 Inserts und Selects in 1.822 ms ohne, 413 ms mit). `db2 update db cfg using STMT_CONC LITERALS` tut dasselbe für jeden Client |
| `DeadlockRetries` | wie oft eine einzelne Anweisung außerhalb einer Transaktion nach einem Deadlock erneut gesendet wird (3) |
| `DeadlockRetryWait` | Millisekunden vor dem ersten erneuten Versuch, verdoppelt, verdreifacht ... für die nächsten (50) |
| `ReconnectOnLoss` | `enabled`: eine verlorene Verbindung außerhalb einer Transaktion wird neu geöffnet (dauerhafte Velocity-Worker) |
| `InListChunkSize` | Werte je `IN`-Liste (1.000) |

Die Meldung einer fehlgeschlagenen Verbindung enthält nie das Passwort.

## Die Datenbank anlegen

Db2 legt eine Datenbank mit einem Befehl seiner Instanz an, nicht mit SQL; daher können weder der Installer noch
`createDatabase()` eine anlegen. Als Eigentümer der Instanz:

```bash
db2 "CREATE DATABASE EXP USING CODESET UTF-8 TERRITORY US PAGESIZE 32 K"
db2 "CONNECT TO EXP"
db2 "CREATE USER TEMPORARY TABLESPACE USERTEMP PAGESIZE 32 K"
```

**Die Seitengröße zählt.** Db2 nimmt je Index höchstens ein Viertel einer Seite als Schlüssel (8 KB bei Seiten von
32 KB) und zählt ein `VARCHAR(n CODEUNITS32)` als 4n Bytes. Mit 32-KB-Seiten behalten die URL-Alias-Texte, Pfade und
Bildpfade des Kernels 2.019 bis 2.022 Zeichen in ihren Indizes; mit den voreingestellten 4-KB-Seiten passen die
eindeutigen Schlüssel des Kernels gar nicht. **Der benutzertemporäre Tablespace** hält die temporären Tabellen des
Kernels (Suche, Knotenzählungen): ohne ihn schlagen sie fehl. `EXTENDED_ROW_SZ` muss `ENABLE` bleiben (die Vorgabe seit
10.5): die Zeilen des Kernels mit mehreren langen Texten sind länger als eine Seite.

## Eine Site installieren

```bash
EXP_INSTALL_DB_PASSWORD=... php bin/php/install.php --apply --db=db2 \
    --db-host=db.example.com --db-name=EXP --db-user=db2inst1
```

Der Kickstarter nimmt `Type=db2` in `[database_choice]`, der Setup-Assistent bietet IBM Db2 an, wenn PHP `pdo_ibm` oder `ibm_db2` hat.
`--db-action=remove` (die Vorgabe) löscht zuerst alle Tabellen und Sequenzen des Schemas.

## Wie das SQL des Kernels zu dem von Db2 wird

Db2 nimmt viel vom SQL von MySQL, wie es ist: `LIMIT n OFFSET m` und `LIMIT m, n`, `IFNULL()`, `NOW()`, `GREATEST()`,
`LEAST()`, `CONCAT(a, b)`, `DROP ... IF EXISTS`, `CREATE ... IF NOT EXISTS` und den Vergleich einer Ganzzahl mit einer
Zeichenkette. Den Rest schreibt der Treiber auf einem Lexer um (`expDb2Dialect::rewrite()`), sodass ein Wort in einem
Zeichenketten-Literal oder einem Kommentar nie berührt wird:

| Der Kernel schreibt | Db2 bekommt |
|---|---|
| `` `name` `` | `"NAME"` (Db2 speichert einen Namen ohne Anführungszeichen in Großbuchstaben) |
| eine Spalte namens `user` | `"USER"`, wo sie als Name steht (ohne Anführungszeichen ist es das Sonderregister) |
| `LENGTH(x)`, `SUBSTRING(s, f, n)`, `SUBSTR(s, f, n)` | `CHARACTER_LENGTH(x, CODEUNITS32)`, `SUBSTRING(s, f, n, CODEUNITS32)` (Zeichen, nicht Bytes; `SUBSTR()` schlägt hinter dem Ende eines Texts fehl) |
| `CONCAT(a, b, c)` | `(a \|\| b \|\| c)` (Db2s `CONCAT()` nimmt zwei) |
| `MD5('Literal')`, `MD5(x)` | der Hash des Literals, von PHP berechnet; `LOWER(HEX(HASH(x, 0)))` |
| `a & b`, `a \| b`, `~a` (die Sprachmasken) | `BITAND(a, b)`, `BITOR(a, b)`, `BITNOT(a)` |
| `LIKE 'a\_%'` | `LIKE 'a\_%' ESCAPE '\'` (Db2 hat kein voreingestelltes Escape-Zeichen) |
| `SELECT ... FOR UPDATE` | `SELECT ... WITH RS USE AND KEEP UPDATE LOCKS` (Db2 lehnt `FOR UPDATE` mit `ORDER BY` oder einem Join ab) |
| `SELECT 1` | `SELECT 1 FROM SYSIBM.SYSDUMMY1` |
| `TRUNCATE TABLE t` | `DELETE FROM t` (Db2s `TRUNCATE` muss die erste Anweisung einer Arbeitseinheit sein) |
| `INSERT INTO t DEFAULT VALUES` | nicht geschrieben: `supportsDefaultValuesInsertion()` ist false, der Kernel fügt `(id) VALUES (NULL)` ein |
| ein Zeichenketten-Literal über 16.000 Bytes | ein gebundener Parameter (Db2 lehnt ein Literal über 32.672 Bytes ab) |
| `CREATE TEMPORARY TABLE` | eine angelegte globale temporäre Tabelle, `ON COMMIT PRESERVE ROWS NOT LOGGED` |

**Typen.** `varchar(n)` und `char(n)` sind `VARCHAR(n CODEUNITS32)`: n Zeichen, wie MySQL zählt. `text` und `longtext`
sind `VARCHAR(32672)` (Bytes), das sich vergleichen, sortieren und mit `DISTINCT` nehmen lässt wie jede Zeichenkette;
die Spalten, die Dokumente beliebiger Länge halten (`ezcontentobject_attribute.data_text`, `ezsession.data`, die
Nutzdaten von Workflow und Audit: `expDb2Schema::LARGE_TEXT_COLUMNS`), sind `CLOB(1G)`, das sich mit `=` und `LIKE`
vergleichen, aber nicht sortieren oder indizieren lässt. Eine Textspalte in einem Index ist `VARCHAR(n CODEUNITS32)` mit
dem n, das in den Schlüssel passt. Spaltenbeschreibungen (`COMMENT ON COLUMN ... IS 'exp:longtext'`, `'exp:char'`,
`'exp:tinyint'`) bewahren den Typ der `.dba`, sodass das Schema gleich der `.dba` zurückgelesen wird.

**Neue IDs.** Eine `auto_increment`-Spalte ist ein `INTEGER` mit der Sequenz `S_<Tabelle>` (beschrieben als
`tabelle.spalte`) und dem Trigger `T_<Tabelle>_BI`, der eine Zeile ohne ID, mit `NULL` oder `0` nummeriert.
`lastSerialID()` liest `PREVIOUS VALUE FOR` die Sequenz, die Db2 je Verbindung führt: immer die ID des eigenen Inserts
dieser Verbindung. Nachdem eine Datendatei mit eigenen IDs geladen wurde, setzt `correctSequenceValues()` jede Sequenz
hinter die höchste ID ihrer Tabelle (der Installer ruft es auf, wie bei PostgreSQL).

**Temporäre Tabellen.** Ein `CREATE TEMPORARY TABLE` des Kernels (Suche, Knotenzählungen, Rechte) wird eine angelegte
globale temporäre Tabelle: ihre Definition ist dauerhaft und gemeinsam, ihre Zeilen gehören der Verbindung. Die
Definition wird einmal angelegt, über eine zweite Verbindung, damit sie sofort festgeschrieben ist; spätere Anfragen
leeren stattdessen ihre eigenen Zeilen. Die Namen sind je Muster fest (`ezsearch_tmp_0`), sodass keine Definition je
Anfrage zurückbleibt.

**Verbindungen.** Eine Verbindung zu Db2 zu öffnen kostet auf dem Testserver etwa 0,8 s (Db2s `AUTHENTICATION=SERVER`
prüft das Passwort bei jeder Verbindung gegen das Betriebssystem; eine Verbindung zu Informix dauert auf demselben Host
17 ms). Schalten Sie dauerhafte Verbindungen ein (`[DatabaseSettings] UsePersistentConnection=enabled`): `pdo_ibm` und
`ibm_db2` halten eine je PHP-FPM- oder Velocity-Worker, und die gemessenen Seiten wurden drei- bis vierzigmal schneller.

**Transaktionen und Sperren.** pdo_ibm läuft im Autocommit-Modus; eine eZDB-Transaktion ist eine Db2-Arbeitseinheit von
`begin()` bis `commit()`. `lock()` (MySQLs `LOCK TABLES`) öffnet eine Transaktion und hält die Zeilen, die sie schreibt.
Ein Deadlock oder ein Rollback nach einem Sperr-Timeout (`SQL0911N`) einer einzelnen Anweisung außerhalb einer
Transaktion wird erneut gesendet.

## Was geht und was nicht

Gemessen am 26.11.2026 mit den Live-Tests (unten) und einer vollständigen Installation, auf Db2 Community Edition
12.1.6.0 und 11.5.9.0 (die offiziellen Container, 3 Kerne und 6 GB für 12.1) mit PHP 8.5.13, `pdo_ibm` 1.7.0 und
`ibm_db2` 2.3.1 über IBMs Data Server Driver 12.1.4, auf einem Testserver unter Last (Load 15 bis 28 auf 12 Kernen).

| | Db2 12.1 | Db2 11.5 |
|---|---|---|
| Verbinden, Version, Katalog (`relationList()`, `eZTableList()`) | geht | geht |
| Kernel-Schema: 153 Tabellen, ihre Indizes, Sequenzen und Trigger | geht, 71 s | geht |
| Kernel-Daten (`share/db_data.dba`, 3.837 Zeilen mit eigenen IDs), nächste ID nach den Daten (`correctSequenceValues()`) | geht, 57 s (vor dem Statement Concentrator) | geht |
| Schema gleich der `.dba` zurückgelesen (Aktualisierungsprüfung) | geht | geht |
| Aktualisierungen: eine Spalte mit Vorgabe zu einer Tabelle mit Zeilen hinzufügen, ihren Typ ändern, sie löschen; einen Index hinzufügen und löschen | geht (ein `REORG` nach den `ALTER`s, die eines brauchen) | geht |
| `lastSerialID()` je Tabelle und je Verbindung | geht | geht |
| Paging, Transaktionen (Rollback, Commit, verschachtelt), `FOR UPDATE`-Zeilensperren, temporäre Tabellen | geht | geht |
| `sqlUpsert()` (MERGE), `sqlLimit()`, `sqlGroupConcat()` (LISTAGG, sortiert), `sqlUnixTimestamp()` in beide Richtungen über 2038 hinaus, `md5()` | geht | geht |
| Text: Unicode, 30.000 Bytes in einem `VARCHAR(32672)`, Dokumente beliebiger Länge in einem `CLOB` (eine Paketseite mit 69.238 Zeichen) | geht | geht |
| eine ganze Site: `install.php --db=db2`, das Multisite-Paket (291 Objekte) | geht: 16 Schritte, 0 Fehler, 9 von 9 Prüfungen; 1.401 s mit dem Statement Concentrator (über eine installierte Site, `--db-action=remove`), 3.760 s, als die ersten 100 Objekte ohne ihn gespeichert wurden | nicht ausgeführt |
| die Seiten der Site über PHPs eingebauten Server: Startseite, Admin-Anmeldung (die Anmeldung antwortet mit 302 zur Einrichtung der Zwei-Faktor-Anmeldung), Suchseite | je 200. Mit dauerhaften Verbindungen: Startseite 3,8 s kalt, 2,0 s warm, Admin-Anmeldung 0,05 bis 0,25 s, Suchseite 0,5 bis 1,6 s; ohne sie zahlt jede Anfrage die Verbindung (Startseite 8,2 bis 13,9 s, Admin-Anmeldung 2,4 bis 3,3 s) | nicht ausgeführt |
| die Suche des Kernels (`eZSearch::search()`: media, exponential, the) | 15, 7 und 115 Treffer | nicht ausgeführt |
| beide PHP-Schichten, `pdo_ibm` und `ibm_db2` | geht | geht |
| ein Text in einem Index, der länger ist als sein `VARCHAR` (ein URL-Alias mit mehr als 2.019 Zeichen) | **geht nicht**: abgelehnt (Wert zu lang) | ebenso |
| Datenbankseiten von 4 KB (Db2s Vorgabe) | **geht nicht**: die eindeutigen Schlüssel des Kernels passen nicht; es braucht Seiten von 32 KB | ebenso |
| PHPs `odbc`/`pdo_odbc` mit dem ODBC- und CLI-Treiber unter Linux | **geht nicht**: 32-Bit-Längen unter unixODBC (siehe oben) | ebenso |
| DFS-Cluster-Backend (mehrere Webserver) | **noch nicht** | noch nicht |
| Lesereplikate | **nein** | nein |
| `createDatabase()`, `removeDatabase()` | **nein**: Db2 legt Datenbanken mit Befehlen seiner Instanz an und löscht sie so | nein |

## Verbindungen für Datentabellen

Die Datentabellen-Schicht hat die Engine `db2` (`datatable.ini [ConnectionSettings] Engines[]`): eine benannte
Verbindung zu einer Db2-Datenbank öffnet sich über `expDb2DB`, und `expDataTableSchema` legt Tabellen darauf über
`expDb2Schema` an und aktualisiert sie. Eine solche Verbindung ist auch der Weg, aus Db2 zu importieren: ihre Tabellen
lassen sich als Datentabellen lesen oder mit den Werkzeugen der Datentabellen kopieren.

```ini
[Connection_erp]
Engine=db2
Server=db2.example.com
Port=50000
Database=ERP
User=reader
Secret=env:ERP_DB_PASSWORD
```

## Lizenzen

| Teil | Lizenz |
|---|---|
| `expDb2DB`, `expDb2Dialect`, `expDb2Schema` und die Transporte | GNU GPL v2.0 oder später, wie der Kernel |
| Db2 Community Edition (der Container `icr.io/db2_community/db2`, von den Tests verwendet) | kostenlos, IBMs Lizenz für die Db2 Community Edition: höchstens 4 Kerne und 16 GB Speicher je Instanz, kein Support; angenommen mit `LICENSE=accept`. Ein Einsatz über diese Grenzen hinaus braucht eine bezahlte Edition |
| IBM Data Server Driver for ODBC and CLI (`clidriver`) | kostenlos, IBMs Lizenzbedingungen in `clidriver/license`; darf unter diesen Bedingungen mit einer Anwendung weitergegeben werden |
| `pdo_ibm`, `ibm_db2` (PECL) | Apache License 2.0 |

## Tests

| Datei | Was |
|---|---|
| `tests/tests/lib/ezdb/db2/expDb2DialectTest.php` | die Umschreiberegeln von Db2 und Informix, Paging, Helfer, temporäre Tabellen, Fehler, DSN, Versionen (ohne Datenbank) |
| `tests/tests/lib/ezdb/db2/expDb2DBTest.php` | beide Treiber über einen aufzeichnenden Transport: Paging, IDs, Transaktionen, Wiederholungen, Wiederverbinden, temporäre Tabellen (ohne Datenbank) |
| `tests/tests/lib/ezdbschema/expDb2SchemaTest.php` | Typen, Sequenzen und Trigger, Indexschlüssel, Aktualisierungen, `kernel/sql/db2` und `kernel/sql/informix` (ohne Datenbank) |
| `tests/tests/lib/ezdb/db2/live/expDb2LiveTest.php` | echte Server: IDs je Verbindung, Paging, die Wörter des Kernels, Transaktionen, Unicode, lange Texte, temporäre Tabellen, das Kernel-Schema mit Daten, das zurückgelesene Schema, Aktualisierungen, die Helfer |

Die Tests ohne Datenbank laufen in der Testsuite `db2`, die Live-Tests in `db2-live`: setzen Sie `EXP_TEST_DB2_HOST`,
`_PORT`, `_USER`, `_PASSWORD` (oder `_PASSWORD_FILE`), `_DATABASE` (eine eigene Datenbank: die Tests leeren sie) und
`_LAYER` (`pdo_ibm` oder `ibm_db2`), für Informix ebenso `EXP_TEST_INFORMIX_...`. Der GitHub-Job `db2` führt sie gegen
Db2 12.1 und Informix 15 mit PHP 8.5 sowie Db2 11.5 und Informix 14.10 mit PHP 8.3 aus, Db2 auf beiden Schichten, und
installiert eine ganze Site auf Db2.
