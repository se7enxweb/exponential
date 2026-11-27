# libSQL- und Turso-Treiber

Diese Seite ist für Administratoren, die die Datenbank einer Site auf einem libSQL-Server oder in Turso's Cloud halten
wollen, und für Entwickler, die am Treiber arbeiten. Ab Exponential 6.0.15 erreicht der Kernel einen libSQL-Server
über dessen HTTP-API: `expLibSQLDB` (`DatabaseImplementation=libsql`) für einen eigenen libSQL-Server (`sqld`, der
Container `libsql-server`) und `expTursoDB` (`turso`) für Turso. Keine Bibliothek und keine eigene PHP-Erweiterung:
PHPs curl-Erweiterung, oder ohne sie `allow_url_fopen`.

**Was er ist.** libSQL ist ein Fork von SQLite: dasselbe SQL, dasselbe Dateiformat, dieselben Antworten, mit einem
Server (`sqld`), der eine Datenbank über HTTP anbietet. Der Treiber ist deshalb der SQLite-Treiber (`eZSQLite3DB`) mit
einem anderen Transport: das SQLite-Schema des Kernels, der SQLite-Schema-Handler, die SQL-Helfer und jede Stelle im
Kernel, die SQLite auf eigene Weise behandelt, behandeln einen libSQL-Server genauso, und `databaseName()` bleibt
`sqlite`. Anders ist, wo die Datenbank liegt: auf einem Server, den sich mehrere Webserver teilen können, statt in
einer Datei neben einem PHP.

**Wofür er gedacht ist.** Eine Site, die die Einfachheit von SQLite ohne dessen Grenze auf eine Maschine will (mehrere
Webserver auf einer Datenbank), eine Site, deren Datenbank in Turso's Cloud läuft, und eine Datentabellen-Verbindung zu
einer libSQL-Datenbank. Jede Anweisung ist eine HTTP-Anfrage: ein Lesen kostet etwa 2 ms statt der Zehntel
Millisekunden von SQLite, und ein Schreiben außerhalb einer Transaktion kostet, was der Server braucht, um es dauerhaft
zu machen (unten gemessen). Lassen Sie den View-Cache an.

## Inhalt

1. [Voraussetzungen](#voraussetzungen)
2. [Eine Site auf einem libSQL-Server installieren](#eine-site-auf-einem-libsql-server-installieren)
3. [Einstellungen](#einstellungen)
4. [Wie eine Anweisung den Server erreicht](#wie-eine-anweisung-den-server-erreicht)
5. [Was anders ist als bei SQLite](#was-anders-ist-als-bei-sqlite)
6. [Was funktioniert und was nicht](#was-funktioniert-und-was-nicht)
7. [Eingebetteter Dateimodus und eingebettete Replikate](#eingebetteter-dateimodus-und-eingebettete-replikate)
8. [Eine Datentabellen-Verbindung zu einer libSQL-Datenbank](#eine-datentabellen-verbindung-zu-einer-libsql-datenbank)
9. [Wie er gebaut ist](#wie-er-gebaut-ist)
10. [Tests](#tests)

## Voraussetzungen

| Was | Woher es kommt |
|---|---|
| Ein libSQL-Server: `sqld` 0.22 oder neuer (Hrana 3; mit älteren Servern spricht der Treiber Hrana 2), oder eine Turso-Datenbank | der Container `ghcr.io/tursodatabase/libsql-server` (getestet: sqld 0.24.33, SQLite 3.47.0); Turso's Dashboard oder `turso db create` |
| PHPs curl-Erweiterung (empfohlen: Keep-Alive), oder `allow_url_fopen=On` | in jeder Distribution bei PHP dabei; der Stream-Wrapper ist der Rückfall |
| Das SQLite, auf dem der Server gebaut ist (oder das von PHP, im eingebetteten Dateimodus): 3.33 oder neuer | der Installer prüft `sqlite_version()` |

PHPs Erweiterung `sqlite3` wird auf dem Webserver **nicht** gebraucht. Die Clients auf Packagist wurden angesehen und
nicht genommen: die PHP-Clients für libSQL laden entweder Turso's native Bibliothek über FFI oder eine in Rust
geschriebene PHP-Erweiterung (keines von beiden lässt sich aus den Paketen eines Hosts installieren), oder sie sind
HTTP-Clients, die dieselben drei Anfragen in ein eigenes Framework packen. Das Protokoll ist klein (eine JSON-Anfrage
pro Anweisung), also spricht der Treiber es selbst.

## Eine Site auf einem libSQL-Server installieren

Einen Server starten, zum Beispiel in einem Container (die Daten in einem Volume):

```bash
docker run -d --name sqld -p 127.0.0.1:8080:8080 -v sqld-data:/var/lib/sqld ghcr.io/tursodatabase/libsql-server:latest
```

Dann:

```bash
php bin/php/install.php --db=libsql --db-host=127.0.0.1 --db-port=8080 --apply
```

Der Server hält eine Datenbank (ohne Namespaces), `--db-name` ist also nur der Name, den die Einstellungen der Site
behalten. Mit einem Auth-Token (`sqld --auth-jwt-key-file`, oder Turso) geben Sie das Token als Passwort an:
`EXP_INSTALL_DB_PASSWORD=<token>` oder `--db-password`. Turso:

```bash
EXP_INSTALL_DB_PASSWORD="$(turso db tokens create exponential)" \
  php bin/php/install.php --db=turso --db-url=libsql://exponential-acme.turso.io --apply
```

`--db-url` ersetzt `--db-host` und `--db-port` (`http://host:8080`, `https://host`, `libsql://<datenbank>-<org>.turso.io`),
`--db-tls=enabled|disabled|auto` wählt https (auto: Turso, oder Port 443). Der Kickstarter nimmt `Type=libsql` (auch
`sqld`) oder `Type=turso`, mit `URL`, `TLS`, `Mode` und `CAFile` in `[database_init]`; `--db-mode=file` und
`--db-cafile` sind die Optionen von `exp:install` dafür.

**Der Web-Setup-Assistent** bietet libSQL, und danach Turso, an, wenn das Web-PHP `curl` hat. Seine Datenbankseite fragt
nach dem Modus (ein Server oder der eingebettete Dateimodus), der Server-URL oder Servername und Port, TLS, einer
optionalen CA-Datei und dem Auth-Token, im Dateimodus nach einem Dateinamen; die Seite prüft sie und verbindet sich dann,
und eine abgelehnte Verbindung wird mit dem Grund des Servers gezeigt. Das Tokenfeld ist immer leer: das Token wird
zwischen den Seiten nur versiegelt aufbewahrt (verschlüsselt mit einem Schlüssel in `var/setup/secret.key`), nie im
Klartext in der Seite, und als `Password` der Einstellungen geschrieben, wie `exp:install` es schreibt
([Kapitel 5](../../installation/05-setup-assistent.md)). Gemessen: eine ganze Site über den Assistenten installiert, von
Playwright gesteuert, auf einem lokalen sqld und im Dateimodus (unten).

Der Installer leert die Datenbank des Servers zuerst (`DatabaseAction=remove`), wie er eine SQLite-Datei leert: jede
Tabelle, jede Sicht, jeder Index und jeder Trigger wird gelöscht.

## Einstellungen

`settings/site.ini` (der Installer schreibt `[LibSQLSettings]` in die Siteaccesses und nach `settings/override`):

```ini
[DatabaseSettings]
DatabaseImplementation=libsql     # turso für Turso; sqld geht auch
Server=10.0.0.5
Port=8080
Database=exponential
Password=                         # das Auth-Token, wenn der Server eines will

[LibSQLSettings]
URL=                              # http(s)://host:port oder libsql://host, ersetzt Server und Port
TLS=auto                          # enabled, disabled, auto (Turso, oder Port 443)
AuthToken=                        # statt Password; sonst LIBSQL_AUTH_TOKEN (TURSO_AUTH_TOKEN) aus der Umgebung
Timeout=30                        # Sekunden pro Anfrage (mindestens die Lock-Wartezeit der Anweisungen + 5)
ConnectAttempts=3                 # Versuche der ersten Verbindung
ConnectWait=200                   # ms vor dem zweiten Versuch, nach jedem Fehlschlag verdoppelt
KeepStream=enabled                # ein Server-Stream für alle Anweisungen (unten)
Mode=remote                       # file: der eingebettete Dateimodus (Database ist eine Datei in var/storage/sqlite3)
CAFile=                           # eine PEM-Datei, die das Zertifikat des Servers über https prüft
```

`[DatabaseSettings] SQLiteBusyTimeout`, `SQLiteTransactionWait` und ihre Gegenstücke für die Kommandozeile behalten
ihre Bedeutung: wie lange eine Anweisung und der Start einer Transaktion warten dürfen. `SQLitePragmas[]` wird nicht
gesendet: die Einstellungen des Servers sind seine eigenen. `UseSlaveServer` wird übergangen (die Replikate von Turso
antworten unter derselben URL).

## Wie eine Anweisung den Server erreicht

Jede Anweisung ist ein `POST /v3/pipeline` (Hrana 3 über HTTP; `/v2/pipeline` bei einem Server ohne) mit der Anweisung
und einer `get_autocommit`-Anfrage, auf einem **Stream** des Servers, der die Verbindung des Servers zur Datenbank ist.
Der Stream bleibt von Anweisung zu Anweisung offen (`KeepStream=enabled`): ein neuer Stream ist eine neue Verbindung
des Servers zu seiner Datenbank, und die kostete 18 der 20 ms eines Lesens und 70 der 76 ms eines Schreibens (sqld 0.24
über Loopback), bevor der Stream behalten wurde. Eine Transaktion läuft auf einem Stream von `BEGIN IMMEDIATE` bis zu
ihrem Ende, wie auf einer SQLite-Verbindung. `get_autocommit` sagt dem Treiber nach jeder Anweisung, ob der Server die
Transaktion noch offen hat; eine Transaktion, die der Server beendet hat (ein Fehler, sein Transaktions-Timeout), ist so
sofort bekannt.

Der Server gibt einen untätigen Stream nach 10 Sekunden auf. Eine Anweisung außerhalb einer Transaktion, die ihren
Stream nicht mehr vorfindet (HTTP 400 "invalid baton"), oder deren Anfrage den Server nicht erreichte, wird einmal auf
einem neuen Stream wiederholt; in einer Transaktion ist die Transaktion verloren, und die Anweisung scheitert mit
diesem Grund. `close()` am Ende jeder Anfrage schließt den Stream.

## Was anders ist als bei SQLite

| | SQLite (eine Datei) | libSQL (ein Server) |
|---|---|---|
| Wer auf die Schreibsperre wartet | das Schreibtor (eine Sperrdatei neben der Datenbank) und SQLite's Busy-Handler | der Server; keine Sperrdatei |
| Eine lange Schreibtransaktion, während ein anderer schreiben will | der andere wartet (bis `SQLiteTransactionWait`) | **sqld beendet die erste nach seinem Transaktions-Timeout (etwa 5 s)**: ihre nächste Anweisung scheitert mit "the libSQL server ended the transaction", und der Kernel rollt sie zurück |
| `md5()`, `BIT_OR()`, `BIT_AND()`, `MOD()` | in PHP registrierte Funktionen | `md5()` eines Literals rechnet PHP, einer Spalte ist es das `md5()` des Servers als Hex; `BIT_OR()` und `BIT_AND()` werden als `MAX()`/`MIN()` jedes Bits geschrieben; `MOD()` ist das des Servers (mathematische Funktionen eingebaut) |
| Temporäre Tabellen | `CREATE TEMPORARY TABLE` | vom Server abgelehnt: `createTempTable()` legt eine gewöhnliche Tabelle unter dem eindeutigen Namen des Aufrufers an, die `dropTempTable()` oder das Ende der Verbindung löscht |
| PRAGMAs (WAL, Cache, mmap) | pro Verbindung gesetzt | die des Servers; keine gesendet |
| Mehrere Webserver | nein (die Datei einer Maschine) | ja: eine Datenbank für alle |
| Sicherung | `VACUUM INTO`, eine Kopie der Datei | die des Servers: eine Kopie des Datenverzeichnisses von sqld, Turso's Point-in-Time-Restore |

## Was funktioniert und was nicht

Gemessen am 2026-11-27 mit den Tests unten gegen sqld 0.24.33 (SQLite 3.47.0) in einem Container, PHP 8.5 mit curl,
auf dem Datenbankknoten des Projekt-Clusters (12 CPUs, Last 40 bis 50 durch andere Arbeit während der Läufe), und eine
ganze Site mit `exp:install --db=libsql` installiert.

| Bereich | libSQL (sqld) | Anmerkungen |
|---|---|---|
| Kernel-Schema (153 Tabellen) und saubere Daten | funktioniert | das SQLite-Schema des Kernels über `eZSQLiteSchema`: 4.205 Anweisungen in 22 bis 49 s |
| Installation mit `exp:install` / Kickstarter, Paket und Erweiterungen | funktioniert | das Multisite-Paket mit seinen Inhalten und Erweiterungen: 16 Schritte, 0 Fehler, 0 Warnungen, 9 Prüfungen bestanden; 1.289 s (22 Minuten) auf einem ausgelasteten Host, das meiste davon Schreiben außerhalb einer Transaktion |
| Web-Setup-Assistent | funktioniert | von Playwright gesteuert auf sqld 0.24.33: 12 Seiten, das Multisite-Paket in 1.447 s installiert (26 Minuten insgesamt), 0 Fehler, 9 Prüfungen bestanden; Startseite und Anmeldung der Administration 200. Das Token stand in keiner Seite und keinem Log, nur in den geschriebenen Einstellungen (und ihrem übersetzten Cache) |
| Eingebetteter Dateimodus (`Mode=file`) | funktioniert | derselbe Lauf des Assistenten im Dateimodus: in 98 s installiert (160 s insgesamt), 0 Fehler, 9 Prüfungen bestanden; Startseite und Anmeldung der Administration 200 |
| Seiten lesen, die Administration, Anmelden | funktioniert | Startseite, Site und Anmeldung der Administration antworten mit 200 (PHPs eingebauter Server); die Anmeldung als Administrator funktioniert. Jede Anweisung ist ein Umlauf, also den View-Cache anlassen |
| Transaktionen, verschachteltes `begin()`/`commit()`, Rollback, eine scheiternde Anweisung darin | funktioniert | |
| Neue IDs (`lastSerialID()`), ein Schlüssel aus zwei Spalten | funktioniert | die rowid des Servers; MAX + 1, wo SQLite keine Spalte nummeriert |
| Unicode-Text, langer Text (800 KB), BLOB, 64-Bit-Ganzzahlen | funktioniert | |
| Doppelte Schlüssel, fehlende Tabellen (Fehlernummern) | funktioniert | die Ergebniscodes von SQLite: 19, 1 |
| Portable Helfer: Blättern, Upsert, Group-Concat, Unix-Zeit | funktioniert | |
| Temporäre Tabellen | funktioniert | als gewöhnliche Tabellen (oben) |
| Mehrere Leser und Schreiber zugleich | funktioniert | der Server reiht die Schreiber ein; eine Schreibtransaktion, die länger als sein Transaktions-Timeout dauert, während ein anderer wartet, beendet der Server |
| Mehrere Webserver auf einer Datenbank | funktioniert | eine URL für alle; die Dateien brauchen weiter ein geteiltes Verzeichnis oder ein DFS-Backend |
| DFS-Cluster-Backend auf libSQL | nein | kein Backend; ein geteiltes Verzeichnis nehmen, oder ein DFS-Backend auf MySQL, PostgreSQL, Oracle, SQL Server oder MongoDB |
| Migrationen nach der Installation | funktioniert | 4 Migrationen angewendet, 0 gescheitert (der Lauf des Installers) |
| Upgrade-Sicherungspunkte (der Dump vor einem Upgrade) | **nein** | "no built-in dump for a libSQL server": den Server mit seinen eigenen Werkzeugen sichern und mit `--no-backup` laufen lassen; ein Rollback sagt dasselbe |
| Migrations-Sicherungspunkte (die Markierung des Datenbankzustands) | funktioniert | die Zeilen jeder Tabelle, gehasht |
| Abgelaufener Stream, verlorene Verbindung, außerhalb einer Transaktion | funktioniert | einmal wiederholt |
| Ohne curl (`allow_url_fopen`) | funktioniert | die Live-Tests laufen einmal über PHPs Stream-Wrapper |
| Ein Token, das der Server ablehnt | funktioniert | die Verbindung scheitert mit "HTTP 401", das Token nie in der Meldung |
| Turso's Cloud | **nicht getestet** | dasselbe Protokoll über HTTPS mit einem Token; kein Konto wurde benutzt. `expTursoDB` verbindet sich ohne Token nicht |
| Eingebettete Replikate | **nein** | siehe unten |
| Datentabellen-Verbindung (Engines `libsql`, `turso`) | funktioniert | unten |
| Der eigene SQLite-Handler der REST-API (`ezcDbHandlerSqlite`) | nein | er öffnet eine Datei; die REST-API selbst läuft auf dem Treiber |

### Gemessen

| Was | Zeit |
|---|---|
| Verbinden (erste Anweisung auf einem neuen Stream) | 67 ms |
| eine Zeile nach ihrem Schlüssel lesen (Stream behalten) | 2,1 ms |
| eine Zeile außerhalb einer Transaktion schreiben (der Server committet jede) | 76 bis 101 ms |
| eine Zeile innerhalb einer Transaktion schreiben | 2,3 bis 6,7 ms |
| das Schema des Kernels und saubere Daten (4.205 Anweisungen) | 22 bis 49 s |
| eine ganze Site mit `exp:install` | 22 Minuten (1.335 s, mit den Migrationen) |
| die Startseite, erster Aufruf (leere Caches, kein OPcache, PHPs eingebauter Server) | 26,9 s |
| dieselbe Seite, View-Cache gefüllt | 1,9 s |
| die Anmeldeseite der Administration | 3,3 s |

Ein Lesen kostet einen Umlauf; ein Schreiben außerhalb einer Transaktion kostet das Commit des Servers, das `sqld`
dauerhaft macht (WAL und sein eigenes Replikations-Log), bevor er antwortet. Schreiben innerhalb einer Transaktion
kostet je einen Umlauf. Die Zahlen stammen von einem ausgelasteten Host; ein Server ohne Last antwortet schneller, einer
in einem anderen Rechenzentrum um die Umlaufzeit des Netzes langsamer.

## Eingebetteter Dateimodus und eingebettete Replikate

- **Der eingebettete Dateimodus** (`[LibSQLSettings] Mode=file`, `exp:install --db=libsql --db-mode=file` oder die
  Wahl im Assistenten): die Datenbank ist eine lokale Datei in `var/storage/sqlite3`, und `expLibSQLDB` ist dann der
  SQLite-Treiber selbst, über PHPs `sqlite3`, ohne Server und ohne HTTP. Turso ist immer entfernt.

- **Eine libSQL-Datei, gelesen vom SQLite-Treiber**: eine Datei, die `sqld` schreibt (`<datenverzeichnis>/dbs/default/data`),
  ist eine SQLite-Datei, und `DatabaseImplementation=sqlite` öffnet sie, solange keine Funktion nur von libSQL
  (Vektorspalten, zufällige rowids) darin steckt, und nie, während `sqld` auf ihr läuft (sein WAL und sein
  Replikations-Log gehören ihm). Nützlich, um eine Kopie der Datenbank eines Servers auf eine eigene Maschine zu
  nehmen; kein Weg, eine zu teilen.
- **Eingebettete Replikate** (eine lokale Datei, die ein libSQL-Client mit dem Primärserver abgleicht und durch die er
  schreibt) macht die Client-Bibliothek von libSQL, die PHP ohne Turso's native Bibliothek (FFI) oder eine eigene
  PHP-Erweiterung nicht laden kann. Der Treiber bietet sie nicht an.

## Eine Datentabellen-Verbindung zu einer libSQL-Datenbank

Setup > Datenverbindungen, Engine `libsql` (oder `turso`): Server und Port (8080, 443), der Name der Datenbank, das
Auth-Token als Secret der Verbindung, und die Optionen `url` (statt Server und Port; ihr Host muss in
`datatable.ini [ConnectionSettings] AllowedHosts[]` stehen) und `tls`. Dort angelegte Datentabellen sind
SQLite-Tabellen auf dem Server: der Schema-Planer, der Beschreiber und die Tabellenlisten behandeln sie als SQLite.
Beide Engines stehen standardmäßig in `Engines[]`; `turso` ist als ungetestet markiert.

## Wie er gebaut ist

| Datei | Was |
|---|---|
| `lib/ezdb/classes/explibsqldb.php` | `expLibSQLDB extends eZSQLite3DB`: die Verbindung, die umgeschriebenen Funktionen, temporäre Tabellen, kein Schreibtor |
| `lib/ezdb/classes/exptursodb.php` | `expTursoDB`: das Profil `turso` |
| `lib/ezdb/classes/explibsqlconnection.php` | `expLibSQLConnection`: Hrana über HTTP mit dem Teil von PHPs Klasse `SQLite3`, den der SQLite-Treiber benutzt (`exec()`, `query()`, `querySingle()`, `lastErrorCode()`, `lastInsertRowID()` ...); curl oder der Stream-Wrapper |
| `lib/ezdb/classes/explibsqlresult.php` | `expLibSQLResult`: die Zeilen, gelesen wie ein `SQLite3Result` |
| `lib/ezdb/classes/explibsqlprofile.php` | `expLibSQLProfile`: die Profile `libsql` und `turso`, die URL und das Token aus den Einstellungen |

Die Werte von Hrana kommen zurück, wie PHPs SQLite3 sie gibt (Ganzzahlen als `int`, ein 64-Bit-Wert jenseits des
Bereichs von PHP als String, Gleitkommazahlen, Text, Blobs als Bytes, `NULL`); seine Fehler als Ergebniscodes von
SQLite (`SQLITE_CONSTRAINT` 19, `TRANSACTION_TIMEOUT` 4) mit der Meldung von SQLite.

## Tests

| Suite | Was | Aufruf |
|---|---|---|
| `libsql` | der Transport, die Profile, der Treiber auf einem Ersatzserver (PHPs sqlite3 antwortet in Hrana wie sqld), die Datenverbindung | `php vendor/bin/phpunit --testsuite libsql` |
| `libsql-live` | gegen einen Server: das Kernel-Schema, Werte, Transaktionen, IDs, Fehler, die Funktionen, die Helfer, temporäre Tabellen, zwei Verbindungen, ein abgelaufener Stream, eine Datentabelle, der Stream-Wrapper | `EXP_TEST_LIBSQL_URL=http://127.0.0.1:8080 php vendor/bin/phpunit --testsuite libsql-live` (`EXP_TEST_LIBSQL_TOKEN` für einen Server mit Token) |

Der GitHub-Job `libsql` lässt beide auf sqld 0.24.33 laufen (PHP 8.5 und 8.3) und installiert auf PHP 8.5 eine ganze
Site auf dem Server und ruft ihre Startseite und die Anmeldung der Administration ab.

Siehe auch: [Datenbank wählen](../../hilfe/datenbank-waehlen.md), [Datenbankunterstützung](../../hilfe/datenbankunterstuetzung.md).
