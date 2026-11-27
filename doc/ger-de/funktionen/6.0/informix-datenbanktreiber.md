# IBM-Informix-Treiber

Diese Seite ist für Administratoren, die IBM Informix mit Exponential verwenden wollen, und für Mitwirkende, die am
Treiber arbeiten. Ab Exponential 6.0.15 hat der Kernel einen Informix-Treiber, `expInformixDB` in `lib/ezdb/classes/`,
mit dem Schema-Handler `expInformixSchema`, dem Kernel-Schema in `kernel/sql/informix/kernel_schema.sql`, Einträgen in
den Installern und einer Engine für Datentabellen. Er ist der [Db2-Treiber](db2-datenbanktreiber.md) mit dem SQL und
dem Katalog von Informix: beide erreichen ihren Server über IBMs Data Server Driver, Informix über seinen
DRDA-Listener.

**Stand.** Eine ganze Site läuft mit Informix 15: `install.php --db=informix` installiert das Multisite-Paket (16 Schritte,
0 Fehler, 9 von 9 Prüfungen), und ihre Startseite, die Admin-Anmeldung und die Suche antworten. Das Kernel-Schema mit
seinen Daten, Aktualisierungen und die portablen SQL-Helfer bestehen ihre Live-Tests auf Informix 15 und 14.10. Nicht:
ein Schema, das gleich der `.dba` zurückgelesen wird (Informix hält keine Spaltenbeschreibungen), `LIKE` jenseits der
ersten 32.739 Bytes eines Dokuments, `GROUP_CONCAT`, ein DFS-Cluster-Backend und Lesereplikate.

## Inhalt

1. [Unterstützte Server und PHP](#unterstützte-server-und-php)
2. [Einstellungen](#einstellungen)
3. [Den Server und die Datenbank vorbereiten](#den-server-und-die-datenbank-vorbereiten)
4. [Eine Site installieren](#eine-site-installieren)
5. [Wie das SQL des Kernels zu dem von Informix wird](#wie-das-sql-des-kernels-zu-dem-von-informix-wird)
6. [Was geht und was nicht](#was-geht-und-was-nicht)
7. [Verbindungen für Datentabellen](#verbindungen-für-datentabellen)
8. [Lizenzen](#lizenzen)
9. [Tests](#tests)

## Unterstützte Server und PHP

| | Unterstützt | Warum |
|---|---|---|
| Informix 15 (15.0.1) | ja | bei jedem Push getestet (GitHub-Job `db2`) und mit einer ganzen Site |
| Informix 14.10 | ja | die Untergrenze: ein `SELECT` ohne `FROM`, `DBINFO('utc_current')`, `MERGE`; bei jedem Push getestet (14.10.FC9W1) |
| Informix 12.10 und älter | nein | ohne IBMs Support, nicht getestet |
| PHP mit `ibm_db2` | ja | IBMs Data Server Driver spricht DRDA, das Informix an einem `drsoctcp`-Listener beantwortet; getestet mit PHP 8.5 und in der CI mit 8.3 |
| PHP nur mit `pdo_ibm` | **nein** für eine Site | `pdo_ibm` 1.7 beendet PHP mit einem Speicherzugriffsfehler, wenn es ein CLOB oder TEXT von Informix liest (gemessen mit Informix 15); eine Datentabellen-Verbindung ohne solche Spalten funktioniert damit |
| `pdo_informix` (PECL) und Informix' eigener ODBC-Treiber (CSDK) | nicht verwendet | `pdo_informix` wird für PHP 8 nicht gepflegt; das CSDK braucht zum Herunterladen ein IBM-Konto, IBMs Data Server Driver ist ein freier Download |

`Layer=auto` nimmt `ibm_db2`, sonst `pdo_ibm`. Beide kommen aus PECL und brauchen IBMs Data Server Driver for ODBC and
CLI; sie und PHPs ODBC-Erweiterungen schließen sich aus, siehe [die Db2-Seite](db2-datenbanktreiber.md#unterstützte-server-und-php).

## Einstellungen

```ini
[DatabaseSettings]
DatabaseImplementation=informix
Server=ifx.example.com
Port=9089
User=informix
Password=...
Database=exponential
```

`ImplementationAlias[informix]` (und `ifx`) nennt `expInformixDB`. `Port` ist ein Listener des Protokolls `drsoctcp` in
den `sqlhosts` des Servers (9089 in IBMs Container), nicht der `onsoctcp`-Port (9088) der eigenen Clients von Informix.

| `[InformixSettings]` | Bedeutung |
|---|---|
| `Layer` | `auto` (`ibm_db2`, sonst `pdo_ibm`), `ibm_db2`, `pdo_ibm` oder `pdo_odbc` |
| `Security`, `ConnectionOptions`, `StatementConcentrator` | wie bei Db2; der Concentrator ist voreingestellt `disabled` (Informix gewinnt wenig damit: 400 Anweisungen in 2.107 ms mit ihm, 2.818 ms ohne) |
| `LockWait` | Sekunden, die eine Anweisung auf eine Sperre wartet (`SET LOCK MODE TO WAIT`; Informix wartet von sich aus gar nicht) (30) |
| `DeadlockRetries`, `DeadlockRetryWait`, `ReconnectOnLoss`, `InListChunkSize` | wie bei Db2 |

Jede Verbindung liest außerdem die zuletzt festgeschriebene Fassung einer Zeile, die ein Schreiber hält (`SET ISOLATION
TO COMMITTED READ LAST COMMITTED`, wie InnoDB), und erlaubt Zeilenumbrüche in Zeichenketten-Literalen
(`IFX_ALLOW_NEWLINE`), die das XML des Kernels hat.

## Den Server und die Datenbank vorbereiten

**Ein Dbspace mit 16-KB-Seiten.** Informix nimmt je Index höchstens etwa ein Fünftel einer Seite als Schlüssel; im
Root-Dbspace mit 2-KB-Seiten sind das unter 400 Bytes, zu kurz für die Textindizes des Kernels. Ein Dbspace mit
16-KB-Seiten braucht einen Bufferpool dieser Größe, und der einen physischen Log-Puffer von mindestens 256 KB:

```bash
# onconfig: PHYSBUFF 512 und ein Bufferpool für 16-KB-Seiten; dann den Server neu starten
BUFFERPOOL size=16k,buffers=6000,lrus=8,lru_min_dirty=50,lru_max_dirty=60
onspaces -c -d dbs16k -k 16 -p /pfad/dbs16k.000 -o 0 -s 2000000
```

**Ein voreingestellter Sbspace.** Die Spalten, die Dokumente beliebiger Länge halten, sind `CLOB`s, die in einem
Smart-Blob-Space liegen; um eines mit `=` zu vergleichen, braucht der Server einen voreingestellten Sbspace
(`SBSPACENAME`):

```bash
onspaces -c -S sbs1 -p /pfad/sbs1.000 -o 0 -s 500000 -Df LOGGING=ON
onmode -wf SBSPACENAME=sbs1
```

**Die Datenbank** muss protokolliert sein (DRDA-Clients brauchen Transaktionen) und UTF-8 sprechen:

```bash
export DB_LOCALE=en_us.utf8 CLIENT_LOCALE=en_us.utf8
echo "CREATE DATABASE exponential IN dbs16k WITH LOG;" | dbaccess sysmaster -
```

## Eine Site installieren

```bash
EXP_INSTALL_DB_PASSWORD=... php bin/php/install.php --apply --db=informix \
    --db-host=ifx.example.com --db-port=9089 --db-name=exponential --db-user=informix
```

Der Kickstarter nimmt `Type=informix`, der Setup-Assistent bietet IBM Informix an, wenn PHP `ibm_db2` oder `pdo_ibm`
hat.

## Wie das SQL des Kernels zu dem von Informix wird

Die Regeln, die Informix mit Db2 teilt, kommen aus `expDb2Dialect` (der Lexer, die Bitoperatoren als `BITAND()`,
`BITOR()`, `BITNOT()`, `ESCAPE '\'` für ein LIKE-Muster mit Backslash, `CONCAT()` mit mehr als zwei Argumenten als
`||`, `TRUNCATE` als `DELETE`). Was sich unterscheidet (`expInformixDialect`):

| Der Kernel schreibt | Informix bekommt |
|---|---|
| `` `name` ``, `"name"` | `name`, ohne Anführungszeichen, klein geschrieben: Informix liest `"x"` als Zeichenkette, solange der Client kein `DELIMIDENT` setzt; daher wird nie ein Name in Anführungszeichen gesetzt |
| `... LIMIT 10 OFFSET 20`, `LIMIT 20, 10` | `SELECT SKIP 20 FIRST 10 ...` (Informix blättert direkt nach `SELECT`) |
| `LENGTH(x)`, `SUBSTRING(s, f, n)` | `CHAR_LENGTH(x)`, `SUBSTRING(s FROM f FOR n)` |
| `IFNULL(a, b)`, `NOW()`, `CURDATE()` | `NVL(a, b)`, `CURRENT YEAR TO SECOND`, `TODAY` |
| `MD5('Literal')` | der Hash, von PHP berechnet (Informix hat kein MD5; die URL-Aliase fragen nur nach dem MD5 eines Literals) |
| `MD5(Spalte)`, `GROUP_CONCAT` | mit `expDBUnsupportedException` abgelehnt |
| `CREATE TEMPORARY TABLE` | `CREATE TEMP TABLE ... WITH NO LOG` (Informix' eigene, der Sitzung) |
| `INSERT INTO t (id) VALUES (NULL)` (eine Zählertabelle) | `... VALUES (0)`: ein `SERIAL` lehnt `NULL` ab und nummeriert `0` |

**Typen.** `auto_increment` ist ein `SERIAL`: Informix nummeriert eine Zeile ohne ID oder mit `0` und rückt seinen
Zähler hinter eine ID vor, die sich ein Insert selbst gibt; nach einer Datendatei muss daher keine Sequenz korrigiert
werden. `lastSerialID()` gibt zurück, was `DBINFO('sqlca.sqlerrd1')` direkt nach dem Insert der Verbindung sagte.
`varchar(n)` ist `VARCHAR(4n)` bis 255 Bytes und `LVARCHAR(4n)` darüber (Informix zählt Bytes; UTF-8 braucht bis zu
vier je Zeichen). `text` und `longtext` sind ebenfalls `LVARCHAR`, und eine Zeile hat höchstens 32.767 Bytes; die
Textspalten einer Tabelle teilen sich daher, was ihre übrigen Spalten übrig lassen (`expInformixSchema::fitRow()`;
mindestens 4.000 Bytes je Spalte, `ezcollab_item.data_text1` bis `3` bekommen 10.565). Die Spalten, die Dokumente
beliebiger Länge halten (`ezcontentobject_attribute.data_text`, `ezsession.data` ...:
`expDb2Schema::LARGE_TEXT_COLUMNS`), sind `CLOB`s im voreingestellten Sbspace: ein Literal füllt eines, `=` vergleicht
es, `LIKE` nicht (Informix hat kein `LIKE` für ein `CLOB`). Informix' `TEXT` wird nicht verwendet: ein Literal kann es
nicht füllen (`-617`), und `pdo_ibm` kann es nicht lesen. Informix hat keine Spaltenbeschreibungen; ein zurückgelesenes
Schema hat daher `varchar`, wo die `.dba` `longtext` hat.

## Was geht und was nicht

Gemessen am 26.11.2026 mit den Live-Tests (unten) und einer vollständigen Installation, auf Informix Developer Edition
15.0.1.0.3 (2 Kerne, 3 GB) und 14.10.FC9W1 (1 Kern) in IBMs Containern, mit PHP 8.5.13, `ibm_db2` 2.3.1 und `pdo_ibm`
1.7.0 über IBMs Data Server Driver 12.1.4, auf einem Testserver unter Last (Load 15 bis 28 auf 12 Kernen).

| | Informix 15 | Informix 14.10 |
|---|---|---|
| Verbinden über DRDA, Version, Katalog | geht | geht |
| Kernel-Schema: 153 Tabellen und ihre Indizes | geht, 2,5 bis 3,3 s | geht |
| Kernel-Daten (`share/db_data.dba`, 3.837 Zeilen mit eigenen IDs), nächste ID nach den Daten | geht, 26 bis 28 s | geht |
| Schema gleich der `.dba` zurückgelesen (Aktualisierungsprüfung) | **geht nicht**: keine Spaltenbeschreibungen, Texte und Längen kommen so zurück, wie Informix sie hat | ebenso |
| Aktualisierungen: eine Spalte mit Vorgabe zu einer Tabelle mit Zeilen hinzufügen, ihren Typ ändern, sie löschen; einen Index hinzufügen und löschen | geht | geht |
| `lastSerialID()` je Tabelle und je Verbindung | geht | geht |
| Paging (`SKIP`/`FIRST`), Transaktionen (Rollback, Commit, verschachtelt), `FOR UPDATE`, temporäre Tabellen | geht | geht |
| `sqlUpsert()` (MERGE), `sqlLimit()`, `sqlUnixTimestamp()`, `md5()` eines Literals | geht | geht |
| `sqlGroupConcat()`, `md5()` einer Spalte | **geht nicht**: mit `expDBUnsupportedException` abgelehnt | ebenso |
| Text: Unicode, 15.000 Bytes in einem `LVARCHAR`, Dokumente beliebiger Länge in einem `CLOB` (eine Paketseite mit 69.238 Zeichen) | geht | geht |
| `LIKE` auf einem Dokument (`CLOB`) | geht innerhalb seiner ersten 32.739 Bytes | ebenso |
| eine ganze Site: `install.php --db=informix`, das Multisite-Paket (291 Objekte) | geht: 16 Schritte, 0 Fehler, 9 von 9 Prüfungen, 2.016 s | nicht ausgeführt |
| die Seiten der Site über PHPs eingebauten Server: Startseite, Admin-Anmeldung (die Anmeldung antwortet mit 302 zur Einrichtung der Zwei-Faktor-Anmeldung), Suchseite | je 200; Startseite 13,7 s kalt, 1,9 s warm, Admin-Anmeldung 0,1 bis 1,9 s, Suchseite 1,2 bis 2,5 s auf dem belasteten Testserver | nicht ausgeführt |
| die Suche des Kernels (`eZSearch::search()`: media, exponential, the) | 15, 7 und 115 Treffer | nicht ausgeführt |
| `pdo_ibm` | eine Datentabellen-Verbindung ohne `CLOB`- oder `TEXT`-Spalten; **keine** Site: PHP endet beim Lesen eines `CLOB` mit einem Speicherzugriffsfehler | ebenso |
| DFS-Cluster-Backend, Lesereplikate | **nein** | nein |
| `createDatabase()`, `removeDatabase()` | **nein**: die Datenbank wird vor der Installation mit `dbaccess` angelegt | nein |

## Verbindungen für Datentabellen

Die Datentabellen-Schicht hat die Engine `informix`: eine benannte Verbindung öffnet sich über `expInformixDB`, und
Tabellen werden über `expInformixSchema` angelegt und aktualisiert. Sie ist auch der Weg, aus Informix zu importieren:
ihre Tabellen lassen sich als Datentabellen lesen oder mit den Werkzeugen der Datentabellen kopieren.

```ini
[Connection_legacy]
Engine=informix
Server=ifx.example.com
Port=9089
Database=stores
User=reader
Secret=env:IFX_PASSWORD
```

## Lizenzen

| Teil | Lizenz |
|---|---|
| `expInformixDB`, `expInformixDialect`, `expInformixSchema` | GNU GPL v2.0 oder später, wie der Kernel |
| Informix Developer Edition (der Container `icr.io/informix/informix-developer-database`, von den Tests verwendet) | kostenlos, nur für Entwicklung und Tests, IBMs Lizenz für die Developer Edition; angenommen mit `LICENSE=accept`. Ein Produktiveinsatz braucht eine bezahlte Edition |
| IBM Data Server Driver for ODBC and CLI, `ibm_db2`, `pdo_ibm` | wie auf [der Db2-Seite](db2-datenbanktreiber.md#lizenzen) |

## Tests

Die Informix-Tests sind die der Db2-Tests (Testsuites `db2` und `db2-live`): der Dialekt, der Treiber über einen
aufzeichnenden Transport und der Schema-Handler ohne Server, und der Live-Test gegen einen Informix-Server
(`EXP_TEST_INFORMIX_HOST`, `_PORT`, `_USER`, `_PASSWORD` oder `_PASSWORD_FILE`, `_DATABASE`). Siehe
[die Db2-Seite](db2-datenbanktreiber.md#tests).
