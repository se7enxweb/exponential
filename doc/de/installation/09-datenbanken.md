# 9. Datenbanken

Exponential speichert sein Inhalts-Repository in einem von sechs Datenbanksystemen über fünf Treiber: SQLite 3,
PostgreSQL, MongoDB, MariaDB, Oracle über die Extension `ezoracle` und MySQL (MariaDB und MySQL teilen sich einen
Treiber). Dieses Kapitel erklärt jedes von ihnen so, wie ein
Betreiber ihm begegnet: was der Treiber tut, wie Sie die Datenbank und ihren Benutzer anlegen, den genauen
Einstellungsblock, was schiefgeht und woran Sie es erkennen. Die meisten Seiten widmet es SQLite, denn SQLite ist
die Voreinstellung des Installers und mit dem Treiber, den Exponential mitliefert, eine vollwertige
Produktionsdatenbank, die hohe Leselast trägt und gleichzeitige Schreibvorgänge in eine Warteschlange stellt, statt
sie scheitern zu lassen. Das Kapitel schließt mit einer Vergleichstabelle und den Hinweisen, auf die es ankommt, wenn
Code oder Inhalte von einer Engine zu einer anderen wechseln.

> **Unterstützte Datenbanken (Exponential 6.0.x), in der Reihenfolge der Empfehlung:** 1. SQLite 3 (die
> Voreinstellung) · 2. PostgreSQL · 3. MongoDB · 4. MariaDB · 5. Oracle · 6. MySQL. Microsoft SQL Server ist derzeit
> bekanntermaßen mit zusätzlichem Aufwand möglich. Details: [Datenbankunterstützung](../../en/us/help/database-support.md).

Die folgenden Abschnitte halten die Reihenfolge der Treiber ein (MariaDB und MySQL teilen sich Abschnitt 9.3); die
oben genannte Unterstützungsseite nennt die gegen den Code geprüften Versionen, die Testabdeckung jeder Datenbank und
was Microsoft SQL Server benötigen würde.

[Inhalt](README.md) · Zurück: [8. Die Site ausliefern](08-die-website-ausliefern.md) · Weiter: [10. Nach der Installation](10-nach-der-installation.md)

## Inhalt

- [9.1 Wie Exponential einen Datenbanktreiber auswählt](#91-wie-exponential-einen-datenbanktreiber-auswählt)
- [9.2 SQLite 3: eine Produktionsdatenbank in einer Datei](#92-sqlite-3-eine-produktionsdatenbank-in-einer-datei)
  - [Was der Treiber bei jeder Verbindung tut](#was-der-treiber-bei-jeder-verbindung-tut)
  - [WAL: Leser und Schreiber nebeneinander](#wal-leser-und-schreiber-nebeneinander)
  - [Schreibvorgänge in der Warteschlange: BEGIN IMMEDIATE und die Schreibsperre](#schreibvorgänge-in-der-warteschlange-begin-immediate-und-die-schreibsperre)
  - [Der Einstellungsblock](#der-sqlite-einstellungsblock)
  - [Was die Leselast trägt: die Cache-Schichten](#was-die-leselast-trägt-die-cache-schichten)
  - [Skalieren mit den persistenten Workern von Velocity](#skalieren-mit-den-persistenten-workern-von-velocity)
  - [Wie er sich unter Last verhält](#wie-er-sich-unter-last-verhält)
  - [Dateien, Eigentümer und gemischte Benutzer](#dateien-eigentümer-und-gemischte-benutzer)
  - [Sicherung und Wiederherstellung](#sqlite-sicherung-und--wiederherstellung)
  - [ANALYZE, VACUUM und die WAL-Datei](#analyze-vacuum-und-die-wal-datei)
  - [Überwachung](#überwachung-einer-sqlite-site)
  - [Grenzen, und wann eine andere Engine die bessere Wahl ist](#grenzen-und-wann-eine-andere-engine-die-bessere-wahl-ist)
- [9.3 MySQL und MariaDB](#93-mysql-und-mariadb)
- [9.4 PostgreSQL](#94-postgresql)
- [9.5 MongoDB](#95-mongodb)
- [9.6 Oracle 19c und neuer (ezoracle)](#96-oracle-19c-und-neuer-ezoracle)
- [9.7 Vergleich](#97-vergleich)
- [9.8 Hinweise über Engines hinweg](#98-hinweise-über-engines-hinweg)
- [9.9 Eine bestehende Site auf eine andere Engine umstellen](#99-eine-bestehende-site-auf-eine-andere-engine-umstellen)
- [Quellen](#quellen)

## 9.1 Wie Exponential einen Datenbanktreiber auswählt

Jede Datenbankeinstellung steht in `settings/site.ini`, Block `[DatabaseSettings]`. Diese Datei bearbeiten Sie nie;
Sie überschreiben sie in `settings/override/site.ini.append.php` (für die gesamte Installation) oder in
`settings/siteaccess/<name>/site.ini.append.php` (für einen einzelnen Siteaccess). Die Installer schreiben den
Override für Sie.

Warum der Override und nicht `settings/site.ini` selbst: Ein Upgrade ersetzt die mitgelieferte Datei durch ihre neue
Version, und jede darin vorgenommene Änderung geht ohne Warnung verloren. Die Override-Dateien gehören Ihnen; nichts,
was mit Exponential ausgeliefert wird, schreibt in sie außer den Installern.

Der Schlüssel `DatabaseImplementation` benennt den Treiber über einen Alias. `eZDB::instance()`
(`lib/ezdb/classes/ezdb.php`) liest den Alias, schlägt ihn in `ImplementationAlias[]` nach und instanziiert die dort
genannte Klasse. Ein Alias, der auf nichts verweist, bricht die Anfrage nicht sofort ab: `eZDB::instance()` gibt eine
Null-Datenbank (`eZNullDB`) zurück, die die Meldung „No database handler was found for '...'“ trägt, und schreibt
„Database implementation not supported: ...“ nach `var/log/error.log`. Danach scheitert jede Seite, die Inhalte
braucht. Ein Tippfehler in `DatabaseImplementation` sieht deshalb aus wie eine Datenbank, die nicht erreichbar ist;
lesen Sie zuerst `error.log`.

| Alias in `DatabaseImplementation` | Klasse | Datei |
|---|---|---|
| `sqlite3` | `eZSQLite3DB` | `lib/ezdb/classes/ezsqlite3db.php` |
| `ezmysqli`, `mysqli`, `ezmysql`, `mysql` | `eZMySQLiDB` | `lib/ezdb/classes/ezmysqlidb.php` |
| `ezpostgresql`, `postgresql`, `pgsql` | `eZPostgreSQLDB` | `lib/ezdb/classes/ezpostgresqldb.php` |
| `mongodb` | `expMongoDB` | `lib/ezdb/classes/expmongodb.php` |
| `ezoracle`, `oracle` | `eZOracleDB` | in der Extension `ezoracle`, registriert durch ihre eigene `site.ini.append.php` |

Die Aliase sind die aus `settings/site.ini` (`ImplementationAlias[...]`); die Oracle-Aliase fügt
`extension/ezoracle/settings/site.ini.append.php` hinzu, wenn die Extension aktiv ist.

Die übrigen Schlüssel des Blocks gelten für die Server-Engines; SQLite ignoriert Server, Benutzer und Passwort:

| Schlüssel | Standardwert | Verwendet von | Bedeutung |
|---|---|---|---|
| `Server` | `localhost` | MySQL, PostgreSQL, MongoDB | Hostname oder Adresse des Datenbankservers. |
| `Port` | leer | MySQL, PostgreSQL, MongoDB | Leer bedeutet den Standardport des Treibers. |
| `Socket` | `disabled` | MySQL | Ein Unix-Socket-Pfad statt Host und Port. |
| `User`, `Password` | `root`, leer | alle außer SQLite | Die Anmeldedaten. Belassen Sie in der Produktion nie das mitgelieferte `root`. |
| `Database` | `nextgen` | alle | Der Datenbankname; bei SQLite die Datei; bei Oracle der Connect-String. |
| `Charset` | `utf-8` | MySQL, Oracle | Der Zeichensatz der Verbindung (siehe die Abschnitte zu den Engines). |
| `ConnectRetries` | `0` | MySQL, PostgreSQL, SQLite | Wie oft ein neuer Versuch unternommen wird, wenn die erste Verbindung scheitert. MySQL und PostgreSQL warten zwischen den Versuchen; SQLite versucht es sofort erneut. Oracle hat einen eigenen `RetryCount` (Abschnitt 9.6) und greift ersatzweise auf diesen Wert zurück. |
| `UsePersistentConnection` | `disabled` | MySQL, PostgreSQL, Oracle | Eine Verbindung über mehrere Anfragen desselben PHP-Prozesses hinweg wiederverwenden. |
| `UseSlaveServer`, `SlaveServerArray[]` ... | `disabled` | MySQL | Lesezugriffe an Replikate senden. |
| `Transactions` | `enabled` | alle | Zusammengehörige Schreibvorgänge in Transaktionen ausführen. Lassen Sie dies eingeschaltet. |
| `SQLOutput`, `SlowQueriesOutput` | `disabled`, `0` | alle | Anweisungen in der Debug-Ausgabe anzeigen; mit einem Wert über 0 nur die langsameren (Millisekunden). |
| `QueryAnalysisOutput` | `disabled` | MySQL | Mit `SQLOutput=enabled` das `EXPLAIN` von MySQL für jede Anweisung in der Debug-Ausgabe. |
| `DebugTransactions` | `disabled` | alle | Für jedes Begin und Commit einen Stacktrace aufzeichnen, um eine nicht abgeschlossene Transaktion zu finden. |
| `SQLitePragmas[]`, `SQLiteTransactionWait` | leer, `25` | SQLite | Die PRAGMAs der Verbindung und die Wartezeit auf die Schreibsperre; siehe [den SQLite-Einstellungsblock](#der-sqlite-einstellungsblock). |

Den wirksamen Wert eines beliebigen Schlüssels, mit allen angewendeten Overrides, lesen Sie so:

```bash
php bin/php/console exp:ini get site.ini/DatabaseSettings/DatabaseImplementation --allow-root-user
```

Die Konsole gibt zuerst eine Zeile aus, die das ausgeführte Skript nennt (`running exp:ini → bin/php/ini.php`, auf
dem Fehlerstrom), dann den Wert allein, zum Beispiel `sqlite3`. Zeigt ein Wert, den Sie geändert zu haben glaubten,
noch den alten, ist der Override in einer Datei oder einem Block gelandet, die nicht gelesen werden; `exp:ini where`
(siehe [exp:ini](../../en/us/bc/6.0/console-exp-ini.md)) zeigt, aus welcher Datei jeder Wert stammt.

Nachdem Sie eine Datenbankeinstellung geändert haben, leeren Sie den INI-Cache, damit jeder Prozess sie liest:

```bash
php bin/php/ezcache.php --clear-tag=ini --allow-root-user
```

Unter Velocity lesen die Worker die Einstellungen beim Start; starten Sie sie ebenfalls neu
(`php bin/php/console exp:velocity restart --allow-root-user`, siehe [Kapitel 10](10-nach-der-installation.md)).

## 9.2 SQLite 3: eine Produktionsdatenbank in einer Datei

SQLite ist keine Spielzeugdatenbank, auf der Exponential zufällig startet. Das SQLite-Projekt selbst beschreibt es
als Datenbank für Websites mit bis zu einigen hunderttausend Anfragen am Tag und mehr (siehe
[Appropriate uses for SQLite](https://www.sqlite.org/whentouse.html)), und der Exponential-Treiber ergänzt die
Bibliothek um das, was eine Webanwendung braucht: Write-Ahead-Logging, für einen Webserver gewählte
Verbindungseinstellungen und eine Schreibwarteschlange, die gleichzeitiges Veröffentlichen warten lässt, bis es an
der Reihe ist, statt es scheitern zu lassen. Die meisten Anfragen einer laufenden Site berühren die Datenbank
überhaupt nicht, weil drei Cache-Schichten sie zuerst beantworten. Dieser Abschnitt erklärt jeden Teil, damit Sie
selbst beurteilen können, wo die Grenzen liegen.

Die Voreinstellung des Installers (`php bin/php/console exp:install`, die vorausgewählte Option des
Setup-Assistenten, `settings/setup.ini` `[DatabaseSettings] DefaultType=sqlite3`) ist SQLite. Voraussetzungen:

- Die PHP-Extension `sqlite3` (`php -m | grep -i sqlite3` gibt `sqlite3` aus).
- Eine SQLite-Bibliothek der Version **3.33 oder neuer** in PHP (`php --ri sqlite3` gibt `SQLite Library => ...`
  aus). Der Setup-Assistent akzeptiert 3.0.1 (`kernel/setup/ezsetupcommon.php`), aber der Treiber wandelt das
  `UPDATE ... JOIN` von MySQL, mit dem die Content-Engine Sprachmasken neu aufbaut, in `UPDATE ... FROM` um, das
  SQLite ab 3.33 unterstützt (`eZSQLite3DB::rewriteJoinedUpdate()`).
- Ein beschreibbares Verzeichnis für die Datenbank: standardmäßig `var/storage/sqlite3/`.
- Das Kommandozeilenprogramm `sqlite3` ist optional, macht aber Sicherungen, `ANALYZE` und Prüfungen einfach
  (`sqlite3 --version`).

### Was der Treiber bei jeder Verbindung tut

Die Treiberklasse ist `eZSQLite3DB` in `lib/ezdb/classes/ezsqlite3db.php`. Fehlt PHP die Extension `sqlite3`, bricht
sie vor allem anderen ab und protokolliert „SQLite3 extension was not found, the DB handler will not be
initialized.“; denken Sie unter PHP-FPM daran, dass das PHP des Webservers und das PHP der Kommandozeile getrennt
konfiguriert werden, und prüfen Sie daher beide (`php -m` und eine `phpinfo()`-Seite oder Setup >
Systeminformationen). Andernfalls geschieht beim Öffnen einer Verbindung Folgendes, in dieser Reihenfolge:

1. **Die Datei auflösen.** Der Wert von `Database` ist ein Dateiname. Ein Name ohne Schrägstrich wird in
   `var/storage/sqlite3/` abgelegt (die Konstante `eZSQLite3DB::STORAGE_DIRECTORY`); ein Name, der mit `/` beginnt,
   ist ein absoluter Pfad und wird unverändert verwendet; `:memory:` ist eine Wegwerf-Datenbank im Arbeitsspeicher.
   Ein fehlendes Verzeichnis wird mit dem Modus `0775` angelegt; eine fehlende Datei legt SQLite selbst an, leer.
   Eine Datei, die sich (nach `ConnectRetries` weiteren Versuchen) nicht öffnen lässt, löst
   `eZDBNoConnectionException` mit dem vollständigen Pfad in der Meldung aus, die der Setup-Assistent und
   `index.php` als nicht erreichbare Datenbank melden. Weil eine fehlende Datei einfach angelegt wird, scheitert ein
   falscher Wert für `Database` nicht: Er öffnet eine neue, leere Datenbank, und die Site meldet dann fehlende
   Tabellen. Vergleichen Sie in diesem Fall den Pfad in der Fehlermeldung mit `ls var/storage/sqlite3/`.
2. **Funktionen registrieren**, die SQLite fehlen: `md5()` sowie die Aggregatfunktionen `BIT_OR` und `BIT_AND`, mit
   denen die Content-Engine Sprachmasken zusammenfasst.
3. **Die PRAGMAs anwenden** (`applyPragmas()`), zuerst das Busy-Timeout, damit jede folgende Anweisung bereits auf
   eine Sperre wartet, statt sofort zu scheitern.
4. **Auf WAL umschalten** (`useWAL()`), aber nur, wenn die Datei noch nicht im WAL-Modus ist: Der Modus wird in der
   Datei gespeichert, also einmal gesetzt, nicht von jeder Verbindung.

Die PRAGMA-Standardwerte und warum jeder gewählt wurde:

| PRAGMA | Standardwert des Treibers | Eigener Standardwert von SQLite | Warum |
|---|---|---|---|
| `synchronous` | `NORMAL` | `FULL` | Mit WAL ist `NORMAL` bei einem Absturz weiterhin sicher gegen Beschädigung; nur die letzten Commits vor einem Stromausfall können verloren gehen. `FULL` synchronisiert bei jedem Commit. |
| `cache_size` | `-65536` (64 MB) | etwa 2 MB | Der Seiten-Cache jeder Verbindung. Negative Zahlen sind Kibibyte. |
| `mmap_size` | `268435456` (256 MB) | `0` | Lesezugriffe laufen über speicherabgebildetes I/O statt über einen `read()`-Aufruf und eine Kopie pro Seite. Die abgebildeten Seiten sind der Datei-Cache des Betriebssystems, den alle Prozesse teilen. |
| `temp_store` | `MEMORY` | Datei | Sortierungen und temporäre Tabellen bleiben im Arbeitsspeicher. |
| `busy_timeout` | `5000` ms | `0` | Eine Anweisung, die auf eine Sperre trifft, wartet bis zu 5 Sekunden. Mit 0 scheiterte eine Anfrage, die auf den Schreibvorgang einer anderen traf, sofort. |

Mit `SQLitePragmas[]` können Sie PRAGMAs ändern oder hinzufügen, ein `name=value` pro Zeile (nächster Abschnitt).
Zwei Regeln setzt der Treiber durch: Der Name darf nur Buchstaben und Unterstriche enthalten und der Wert nur
Buchstaben, Ziffern, `_` und `-`; alles andere wird übersprungen. Und WAL gewinnt immer: Ein Eintrag `journal_mode`
wird zuerst angewendet und dann von `useWAL()` wieder auf WAL umgeschaltet.

Denken Sie daran, dass `cache_size` pro Verbindung gilt. Jeder PHP-FPM-Worker und jeder Velocity-Worker, der die
Datenbank geöffnet hat, kann einen Seiten-Cache von bis zu 64 MB aufbauen. Planen Sie den Arbeitsspeicher dafür ein,
oder senken Sie den Wert (`SQLitePragmas[]=cache_size=-16384` sind 16 MB).

### WAL: Leser und Schreiber nebeneinander

Im WAL-Modus ([Write-Ahead Logging](https://www.sqlite.org/wal.html)) ändert ein Schreibvorgang die Datenbankdatei
nicht direkt. Er hängt die geänderten Seiten an `<database>-wal` an; Leser sehen einen konsistenten Schnappschuss aus
der Datenbankdatei und dem Teil des WAL, der zu Beginn ihres Lesevorgangs festgeschrieben war. Die Folgen für eine
Website:

- **Leser blockieren den Schreiber nie, und der Schreiber blockiert Leser nie.** Seitenaufrufe laufen weiter,
  während ein Redakteur veröffentlicht.
- **Es gibt trotzdem immer nur einen Schreiber zur selben Zeit.** Das ist eine Eigenschaft von SQLite, nicht des
  Treibers. Die Aufgabe des Treibers ist es, den zweiten Schreiber gut warten zu lassen (nächster Teil).
- **Ein Checkpoint** kopiert das WAL zurück in die Datenbankdatei. SQLite führt ihn automatisch aus, wenn das WAL
  etwa 1000 Seiten erreicht (`wal_autocheckpoint`); ein Checkpoint kann nicht über eine Seite hinaus abgeschlossen
  werden, die ein laufender Leser noch braucht, sodass die WAL-Datei bei ständigem Lesen eine Weile wachsen kann
  (siehe [ANALYZE, VACUUM und die WAL-Datei](#analyze-vacuum-und-die-wal-datei)).
- **Zwei zusätzliche Dateien** liegen neben der Datenbank: `<database>-wal` und der Shared-Memory-Index
  `<database>-shm`. Beide gehören zur Datenbank; löschen Sie sie nie, solange irgendein Prozess sie geöffnet hat.
- **Alle Prozesse müssen auf einer Maschine laufen.** WAL verwendet gemeinsamen Speicher und funktioniert deshalb
  nicht über ein Netzwerkdateisystem. Ein Server (mit beliebig vielen Prozessen) ist in Ordnung; mehrere Webserver,
  die sich eine Datenbankdatei über NFS teilen, sind es nicht.

### Schreibvorgänge in der Warteschlange: BEGIN IMMEDIATE und die Schreibsperre

Dies ist der Teil, der SQLite für eine Redaktionssite geeignet macht. Er wurde mit 6.0.15 eingeführt (1. und
2. Oktober 2026); lesen Sie [SQLite: Transaktionen warten in einer Schlange auf die Schreibsperre](../../en/us/bc/6.0/sqlite-transactions.md)
für die Upgrade-Hinweise.

**Das Problem, das er löst.** Ein einfaches `BEGIN` ist in SQLite *deferred* (aufgeschoben): Die Transaktion liest
zuerst und fordert die Schreibsperre erst bei ihrem ersten Schreibvorgang an. Hat eine andere Verbindung in der
Zwischenzeit festgeschrieben, ist der Schnappschuss, den die Transaktion gelesen hat, veraltet, und SQLite antwortet
sofort mit `SQLITE_BUSY`, ohne zu warten, weil Warten den Schnappschuss nicht aktuell machen kann. Eine
Veröffentlichung scheiterte mit „database is locked“, egal wie lang das Busy-Timeout war.

**Was der Treiber stattdessen tut.** `eZSQLite3DB::beginQuery()` startet jede Transaktion so:

1. Sie öffnet (einmal pro Verbindung und erneut in jedem geforkten Prozess) eine Sperrdatei neben der Datenbank: **`<database file>.writer-lock`**, zum Beispiel
   `var/storage/sqlite3/exponential.db.writer-lock`.
2. Sie setzt ein exklusives `flock()` auf diese Datei. Hält ein anderer Schreiber sie, versucht sie es alle 1 bis
   15 ms erneut, mit wachsender Wartezeit seltener; ein Schreiber, der schon länger als eine Sekunde wartet, versucht
   es häufiger als später hinzugekommene, sodass die Schreiber ungefähr in der Reihenfolge ihres Eintreffens bedient
   werden.
3. Mit der Sperre in der Hand führt sie **`BEGIN IMMEDIATE`** aus, das die Schreibsperre von SQLite zu Beginn der
   Transaktion nimmt, wo das Busy-Timeout greift. Auf Schreiber, die die Warteschlange nicht nutzen (eine einzelne
   Anweisung außerhalb einer Transaktion, ein anderes Programm), wartet der Busy-Handler von SQLite innerhalb der
   verbleibenden Zeit.
4. Bei `COMMIT` oder `ROLLBACK` wird die Sperrdatei freigegeben. Stirbt der Prozess, gibt das Betriebssystem sie
   frei, sodass ein Absturz die Warteschlange nie blockieren kann.

**Warum das Warten zu Beginn sicher ist.** Bevor `BEGIN IMMEDIATE` erfolgreich war, hat die Transaktion nichts
geschrieben. Sobald es erfolgreich war, kann nichts in ihr ein Wettrennen um eine Sperre verlieren, und WAL macht
den Commit zu einem Alles-oder-nichts-Vorgang. Der Beginn ist daher die einzige Stelle, an der eine Transaktion an
einer Sperre scheitern kann, und die einzige Stelle, an der Warten nichts kostet außer Zeit.

**Wie lange er wartet.** Bis zu `[DatabaseSettings] SQLiteTransactionWait` Sekunden (Standardwert `25`; Werte unter 1
werden auf 1 angehoben). Halten Sie den Wert unter dem Anfrage-Timeout Ihres Webservers, damit eine wartende Anfrage
mit einer klaren Meldung scheitert, statt abgebrochen zu werden. Der Standardwert ist auf Velocity abgestimmt:
Velocity ersetzt einen Worker, dessen Anfrage länger läuft als seine Engine-Einstellung `requestTimeout`
(`Q.webserver.requestTimeout`), die **30 Sekunden** beträgt, sofern Sie sie nicht ändern (der Client erhält einen
504; siehe die [Spezifikation des Worker-Pools](../spezifikationen/6.0/velocity-worker-pool.md)). 25 Sekunden lassen dem
Treiber Zeit, vorher aufzugeben und den Fehler zu melden. Releases vor dieser lieferten `60` aus, wodurch der Server
eine wartende Veröffentlichung zuerst abbrechen konnte; setzt Ihr Override noch `60`, senken Sie den Wert auf `25`.
Um länger zu warten, erhöhen Sie `requestTimeout` und `SQLiteTransactionWait` gemeinsam, die Wartezeit immer einige
Sekunden unter dem Timeout. Unter PHP-FPM ist die Grenze das `request_terminate_timeout` des Pools (aus, sofern nicht
gesetzt); `max_execution_time` von PHP zählt die Wartezeit unter Linux nicht mit, weil es CPU-Zeit misst.

**Wie ein Timeout aussieht.** Konnte eine Transaktion innerhalb der Wartezeit nicht beginnen, meldet der Treiber in
`var/log/error.log` und auf der Fehlerseite:

```
database is busy: the transaction could not start within 25 s, another write held the lock all that time; nothing was written
```

(`25` ist Ihr `SQLiteTransactionWait`.) Jeder andere Fehler beim Beginn lautet
`the transaction could not start: <SQLite's message>; nothing was written`. In beiden Fällen wurde nichts
geschrieben; wiederholen Sie die Aktion. Ein Rollback wird nur gesendet, wenn SQLite tatsächlich eine Transaktion
offen hat, sodass Sie nicht mehr „cannot rollback - no transaction is active“ sehen, das den eigentlichen Fehler
verdeckt.

**Wenn sich Timeouts wiederholen.** Ein einzelner Schreibvorgang hielt die Sperre während der gesamten Wartezeit.
Suchen Sie in `var/log/error.log` und im Cron-Protokoll, was zu dieser Zeit lief: ein `VACUUM`, ein großer Import,
ein Skript, das eine Transaktion geöffnet und dann auf etwas anderes gewartet hat, oder eine innerhalb von `BEGIN`
offen gelassene `sqlite3`-Shell. Ein höheres `SQLiteTransactionWait` hilft nur, wenn der Halter berechtigterweise
lange lief; der Wert muss unter dem Anfrage-Timeout des Webservers bleiben (`request_terminate_timeout` von
PHP-FPM, `requestTimeout` von Velocity), sonst wird die Anfrage abgebrochen, bevor der Treiber den Fehler melden kann.

**Geforkte Prozesse.** `flock()` gehört zu einer geöffneten Datei, deshalb darf ein geforkter Prozess das Handle
seines Elternprozesses nicht wiederverwenden. Der Treiber merkt sich die Prozess-ID, die die Sperrdatei geöffnet
hat, und öffnet in jedem geforkten Prozess eine neue (`writerGate()`); Velocity-Worker und die Kindprozesse des
asynchronen Veröffentlichers reihen sich daher korrekt gegeneinander ein. Der asynchrone Veröffentlicher schließt
außerdem seine Datenbankverbindung, bevor er für jede Veröffentlichung forkt.

**Sperrdatei im Besitz eines anderen Benutzers.** Der Treiber öffnet `.writer-lock` zum Schreiben und, falls das
scheitert, schreibgeschützt: `flock()` funktioniert auch mit einem schreibgeschützten Handle, sodass eine von einem
anderen Benutzer angelegte Sperrdatei trotzdem korrekt in die Warteschlange einreiht.

### Der SQLite-Einstellungsblock

Alle SQLite-Einstellungen, so wie sie in `settings/override/site.ini.append.php` gehören:

```ini
[DatabaseSettings]
# Use the SQLite driver.
DatabaseImplementation=sqlite3

# The database file: a plain name lives in var/storage/sqlite3/,
# a name starting with / is an absolute path.
Database=exponential.db

# Seconds a transaction waits at its start for the writers ahead of it
# (default 25). Keep it below the web server's request timeout: Velocity's
# requestTimeout is 30 s unless changed; raise both together.
SQLiteTransactionWait=25

# PRAGMAs over the driver's defaults, one per line as name=value.
# An empty list keeps the defaults (synchronous=NORMAL, cache_size=-65536,
# mmap_size=268435456, temp_store=MEMORY, busy_timeout=5000).
SQLitePragmas[]
#SQLitePragmas[]=cache_size=-131072
#SQLitePragmas[]=busy_timeout=10000
#SQLitePragmas[]=journal_size_limit=67108864

# How often to try again (at once, without a pause) when the file cannot
# be opened at first.
ConnectRetries=0
```

Der Setup-Assistent akzeptiert einen einfachen Dateinamen mit der Endung `.db`, `.db3`, `.sqlite` oder `.sqlite3` in
`var/storage/sqlite3/`, der aus Buchstaben, Ziffern, Punkten, Bindestrichen und Unterstrichen besteht und mit einem
Buchstaben oder einer Ziffer beginnt (alles andere erhält „The database file name is not valid. ...“); ein absoluter
Pfad ist über `kickstart.ini` und über `exp:install` möglich. Der Assistent schlägt `sqlite.db` vor; `exp:install`
verwendet `exponential.db`. Prüfen Sie, was Ihre Site verwendet:

```bash
php bin/php/console exp:ini get site.ini/DatabaseSettings/Database --allow-root-user
php bin/php/console exp:ini get site.ini/DatabaseSettings/SQLiteTransactionWait --allow-root-user
ls -l var/storage/sqlite3/
```

Leeren Sie nach jeder Änderung den INI-Cache (`php bin/php/ezcache.php --clear-tag=ini --allow-root-user`) und
starten Sie unter Velocity die Worker neu.

### Was die Leselast trägt: die Cache-Schichten

Eine Datenbank, die jeden Seitenaufruf beantwortet, wäre bei jeder Engine der Engpass. Exponential hält die meisten
Anfragen mit drei Schichten von ihr fern, von außen nach innen:

| Schicht | Einstellung | Was ein Treffer kostet | Geleert durch |
|---|---|---|---|
| **Velocity-Antwort-Cache** | `settings/velocity.ini` `[CacheSettings] Enabled=enabled`, `DefaultTtl=30` | Beantwortet vom Velocity-Elternprozess: kein PHP-Worker, keine Datenbank. Nur anonyme Besucher; angemeldete Besucher werden nie daraus bedient. | `php bin/php/console exp:velocity cache clear --allow-root-user` |
| **Inhalts-View-Cache** | `site.ini` `[ContentSettings] ViewCaching=enabled` | Der gerenderte Hauptbereich einer Inhaltsseite wird aus einer Datei gelesen; das Pagelayout darum herum läuft weiterhin. | `php bin/php/ezcache.php --clear-id=content --allow-root-user`, und automatisch beim Veröffentlichen |
| **Template-Block-Cache** | `site.ini` `[TemplateSettings] TemplateCache=enabled` | `{cache-block}`-Abschnitte des Pagelayouts (Menüs, Fußzeilen) werden aus Dateien gelesen. | `php bin/php/ezcache.php --clear-id=template-block --allow-root-user` |

Darunter verringern zwei weitere Caches, was bei SQLite ankommt:

- Der **SQL-Abfrage-Cache** (`settings/querycache.ini`, `[QueryCacheSettings] Mode=off` als Standardwert; `request`
  oder `shared`) beantwortet ein wiederholtes `SELECT`, bis ein Schreibvorgang in eine seiner Tabellen es veralten
  lässt. Der SQLite-Treiber teilt ihm jeden Schreibvorgang mit (`eZDBQueryCache::noteWrite()`). Siehe
  [SQL-Abfrage-Cache](../../en/us/bc/6.0/sql-query-cache.md).
- Der **Datei-Cache des Betriebssystems** und die Speicherabbildung von SQLite: Eine Datenbank von einigen hundert
  Megabyte wird nach dem ersten Durchlauf aus dem Arbeitsspeicher gelesen.

Der statische Cache (`[ContentSettings] StaticCache`) ist im mitgelieferten Installer **deaktiviert** und sollte aus
bleiben: War er eingeschaltet, erzeugte jede Veröffentlichung jede betroffene Seite über HTTP innerhalb der Anfrage
des Redakteurs neu.

Die Datenbank sieht also die nicht gecachten Seitenaufrufe (erste Aufrufe nach dem Leeren, angemeldete Redakteure,
Suche) und die Schreibvorgänge. Das ist die Last, die die nächsten beiden Teile messen.

### Skalieren mit den persistenten Workern von Velocity

Unter PHP-FPM öffnet jeder Worker-Prozess seine eigene SQLite-Verbindung, wenn eine Anfrage eine braucht. Unter
Velocity mit persistenten Workern (`settings/velocity.ini` `[ServerSettings] ForkPerRequest=disabled`, der
mitgelieferte Wert) hält jeder Worker die Anwendung zwischen den Anfragen geladen; die Datenbankverbindung, ihr
Seiten-Cache und die kompilierten Templates bleiben im Worker warm. Was das für SQLite bedeutet:

- **Lesezugriffe skalieren mit der Zahl der Worker.** Jeder Worker liest seinen eigenen Schnappschuss; WAL lässt sie
  alle lesen, während einer schreibt. Die Größe des Pools ist `[ServerSettings] Workers` (mitgeliefert `4`), optional
  dynamisch mit `SpareWorkers` und `IdleWorkerTimeout`.
- **Schreibvorgänge reihen sich an der Schreibsperre ein**, über Worker, PHP-FPM, Cronjobs und
  Kommandozeilenskripte hinweg gleichermaßen, weil alle dieselbe `.writer-lock` nehmen.
- **Arbeitsspeicher**: Rechnen Sie mit bis zu `cache_size` (64 MB) pro Worker, der die Datenbank benutzt hat, plus
  der gemeinsamen Speicherabbildung.
- **Nach einer Änderung der Einstellungen** starten Sie die Worker neu: Sie lesen `site.ini` beim Start.

Lesen Sie [Velocity-Server mit persistenten Workern](../../en/us/features/6.0/velocity-persistent-worker-server.md) und
[Velocity-Worker-Pool](../spezifikationen/6.0/velocity-worker-pool.md) zum Pool selbst.

### Wie er sich unter Last verhält

Der Treiber wurde auf dem Entwicklungsserver mit gleichzeitigen Prozessen lastgetestet, die dieselbe Datenbank lasen
und beschrieben. Allgemein gesagt:

- **Gleichzeitige Leser skalierten** mit der Zahl der Prozesse: ein Vielfaches der Rate eines einzelnen Prozesses
  bei 16 bis 32 Lesern, danach setzten die Kerne der Maschine, nicht die Datenbank, die Obergrenze. Kein Leser
  scheiterte.
- **Kurze Schreibtransaktionen** blieben bei 16 gleichzeitigen Schreibern fehlerfrei, bei etwa der Rate, die ein
  einzelner Schreiber erreicht: Schreiber stellen sich an, sie scheitern nicht.
- **Gemischte Last** (viele Leser und mehrere Schreiber gleichzeitig) hielt die Lesezugriffe schnell, während sich
  die Schreibvorgänge einreihten.
- **Lange Transaktionen** (die jeweils die Schreibsperre 50 bis 300 ms lang hielten, wie es eine große
  Veröffentlichung tut) scheiterten mit dem früheren Treiber, sobald das Busy-Timeout von 5 Sekunden abgelaufen war;
  mit der Schreibwarteschlange wurden alle abgeschlossen und warteten nur länger. Im eigenen Test der Änderung wurden
  192 gleichzeitige Veröffentlichungen aus 64 Prozessen alle vollständig abgeschlossen, wo der frühere Start 6 davon
  verlor; 48 Transaktionen von je 300 ms, gleichzeitig gestartet, waren nach 14,7 Sekunden fertig, etwa den
  14,4 Sekunden, die sie nacheinander brauchen. Die Warteschlange kostet über das Warten selbst hinaus fast nichts.
- **Die WAL-Datei wuchs** bei anhaltend starkem Schreiben mit laufenden Lesern auf deutlich über hundert Megabyte
  und wurde danach per Checkpoint zurückgeführt (siehe die nächsten Teile zu `journal_size_limit`).

Die praktische Lesart: SQLite bewältigt eine stark besuchte öffentliche Site und ein normales Redaktionsteam
bequem. Wo viele Redakteure im selben Moment große Änderungen veröffentlichen, wartet jeder auf die anderen; dann
lohnt sich eine Server-Datenbank trotz ihres zusätzlichen Betriebsaufwands (siehe
[Grenzen](#grenzen-und-wann-eine-andere-engine-die-bessere-wahl-ist)).

### Dateien, Eigentümer und gemischte Benutzer

Eine SQLite-Datenbank besteht aus vier Dateien in einem Verzeichnis:

| Datei | Was sie ist | Wer sie schreiben können muss |
|---|---|---|
| `exponential.db` | die Datenbank | jeder Prozess, der schreibt |
| `exponential.db-wal` | das Write-Ahead-Log | jeder Prozess, der schreibt |
| `exponential.db-shm` | der Shared-Memory-Index des WAL | **jeder Prozess, auch Leser** |
| `exponential.db.writer-lock` | die Schreibwarteschlange des Treibers | jeder Prozess (schreibgeschützt genügt für `flock()`) |

SQLite legt die Dateien `-wal` und `-shm` außerdem an und entfernt sie wieder, deshalb muss das **Verzeichnis** für
jeden Prozess beschreibbar sein. Der Treiber legt `var/storage/sqlite3/` mit dem Modus `0775` an, setzt aber weder
Modus noch Eigentümer der Dateien; sie erhalten Eigentümer und umask des Prozesses, der sie zuerst angelegt hat.

Auf einem typischen Server verwenden drei Arten von Prozessen die Datenbank: das PHP des Webservers (PHP-FPM oder
mod_php, als Site-Benutzer), Velocity-Worker sowie Cronjobs und Konsolenbefehle (oft ein anderer Benutzer, manchmal
`root`). Legt einer von ihnen `-shm` mit dem Modus `0644` an, scheitern die anderen mit „attempt to write a readonly
database“. Richten Sie es einmal ein:

```bash
# one group that every process runs with (the web server's group is usual)
chgrp -R <web group> var/storage/sqlite3
# the setgid bit: files created in the directory inherit its group
chmod 2775 var/storage/sqlite3
chmod 664 var/storage/sqlite3/*
```

und geben Sie jedem Prozess eine umask von `0002`, damit neue Dateien für die Gruppe beschreibbar sind
(`UMask = 0002` in einer systemd-Unit oder `umask 002` am Anfang einer Cron-Zeile). Prüfen Sie das Ergebnis nach
einem Tag normalen Betriebs:

```bash
ls -l var/storage/sqlite3/
```

Jede Datei sollte für die Gruppe beschreibbar sein und die gemeinsame Gruppe tragen. Die erwartete Ausgabe sieht so
aus (Eigentümer, Gruppe und Größen sind Ihre eigenen):

```text
-rw-rw-r-- 1 example www-data 187678720 Oct  5 10:14 exponential.db
-rw-rw-r-- 1 example www-data     32768 Oct  5 10:20 exponential.db-shm
-rw-rw-r-- 1 example www-data   4124152 Oct  5 10:20 exponential.db-wal
-rw-rw-r-- 1 example www-data         0 Oct  5 09:58 exponential.db.writer-lock
```

Greifen Sie hier **nicht** zu `bin/modfix.sh` (`bin:modfix`): Es macht `var/storage` und andere Verzeichnisse für
alle beschreibbar (Modus `777`, Dateien `666`), und das Skript selbst warnt, dass das nicht sicher ist. Korrigieren
Sie stattdessen Gruppe und Modus wie oben; [Kapitel 8](08-die-website-ausliefern.md#87-dateiberechtigungen-und-eigentümer)
behandelt die Benutzer, unter denen die Server laufen.

**Liefern Sie die Datenbank nie über HTTP aus.** Sie liegt unterhalb von `var/storage`, das die mitgelieferte
`.htaccess` an `index.php` weiterleitet und das die Liste statischer Dateien von Velocity auslässt. Prüfen Sie es auf
Ihrem eigenen Host; alles außer `200` ist richtig:

```bash
curl -sI https://www.example.com/var/storage/sqlite3/exponential.db | head -1
```

### SQLite-Sicherung und -Wiederherstellung

**Kopieren Sie nie eine Datenbankdatei, die in Benutzung ist.** Eine Kopie, die während eines laufenden
Schreibvorgangs oder ohne ihre `-wal`-Datei erstellt wurde, kann inkonsistent sein. Verwenden Sie die
[Online-Backup-API](https://www.sqlite.org/backup.html) von SQLite über das Programm `sqlite3`; sie erstellt eine
konsistente Kopie, während die Site weiterläuft und schreibt:

```bash
sqlite3 var/storage/sqlite3/exponential.db ".backup '/backup/exponential-$(date +%F).db'"
```

Alternativ schreibt `VACUUM INTO` (SQLite 3.27 und neuer) mit einer einzigen Anweisung eine komprimierte Kopie
([VACUUM](https://www.sqlite.org/lang_vacuum.html#vacuuminto)):

```bash
sqlite3 var/storage/sqlite3/exponential.db "VACUUM INTO '/backup/exponential-$(date +%F).db'"
```

Prüfen Sie eine Sicherung, bevor Sie sich auf sie verlassen:

```bash
sqlite3 /backup/exponential-2026-10-05.db "PRAGMA integrity_check"
```

Die Ausgabe ist `ok`. Eine vollständige Sicherung enthält auch die Dateien (`var/storage`, `settings/override`,
`settings/siteaccess`); [Kapitel 10](10-nach-der-installation.md#109-sicherung-und-wiederherstellung) beschreibt das vollständige
Verfahren.

**Wiederherstellen.** Prozesse halten die Datenbank geöffnet: PHP-FPM-Worker, während sie eine Anfrage ausführen,
Velocity-Worker während ihrer gesamten Lebensdauer. Stellen Sie über die eigenen Sperrmechanismen von SQLite wieder
her, statt die Datei unter ihnen auszutauschen:

```bash
php bin/php/maintenance.php on --message="Restoring" --allow-root-user
sqlite3 var/storage/sqlite3/exponential.db ".restore '/backup/exponential-2026-10-05.db'"
php bin/php/maintenance.php off --allow-root-user
php bin/php/ezcache.php --clear-all --allow-root-user
php bin/php/console exp:velocity cache clear --allow-root-user
```

`.restore` kopiert die Sicherung Seite für Seite unter einer Schreibsperre in die laufende Datenbank. Bei Erfolg gibt
es nichts aus. Meldet es stattdessen einen Fehler (zum Beispiel weil ein Schreiber die Sperre hielt oder weil die
Sicherung mit einer anderen Seitengröße erstellt wurde, die eine Datenbank im WAL-Modus nicht übernehmen kann), ist
die laufende Datenbank unverändert; verwenden Sie dann den unten beschriebenen Austausch der Datei. Leeren Sie danach
in jedem Fall die Caches: Sie enthalten noch Seiten, die aus dem Inhalt vor der Wiederherstellung gerendert wurden.
Müssen Sie stattdessen die Datei
selbst austauschen, stoppen Sie zuerst jeden Prozess, der sie geöffnet haben könnte (Velocity mit
`exp:velocity stop`, PHP-FPM, Cron), verschieben Sie die alte Datenbank zusammen mit ihren Dateien `-wal` und `-shm`
beiseite, legen Sie die Sicherung an ihre Stelle, korrigieren Sie Eigentümer und Modus und starten Sie die Prozesse
wieder.

### ANALYZE, VACUUM und die WAL-Datei

**ANALYZE.** Der Abfrageplaner von SQLite wählt Indizes mit Statistiken besser aus. Führen Sie `ANALYZE` einmal nach
einer Installation oder einem großen Import aus und erneut, wenn der Inhalt stark gewachsen ist:

```bash
sqlite3 var/storage/sqlite3/exponential.db "ANALYZE"
```

`PRAGMA optimize` führt nur die nötige Analyse aus und ist günstig genug für einen wöchentlichen Cron-Eintrag
([ANALYZE](https://www.sqlite.org/lang_analyze.html), [PRAGMA optimize](https://www.sqlite.org/pragma.html#pragma_optimize)).

**VACUUM.** Gelöschte Inhalte hinterlassen freie Seiten in der Datei; SQLite verwendet sie wieder, gibt sie aber
nicht an das Dateisystem zurück. `VACUUM` baut die Datei kompakt neu auf. Es braucht etwa das Doppelte der
Datenbankgröße an freiem Speicherplatz und hält die Schreibsperre während des gesamten Laufs, sodass sich Schreiber
dahinter einreihen und nach `SQLiteTransactionWait` in ein Timeout laufen können. Führen Sie es im Wartungsmodus aus
und nur dann, wenn die Datei viel größer geworden ist als ihr Inhalt (nachdem ein großer Teil der Site entfernt
wurde):

```bash
php bin/php/maintenance.php on --allow-root-user
sqlite3 var/storage/sqlite3/exponential.db "VACUUM"
php bin/php/maintenance.php off --allow-root-user
```

**Die WAL-Datei.** Nach einem Checkpoint verwendet SQLite die WAL-Datei ab ihrem Anfang wieder, verkleinert sie aber
nicht. Eine Phase starken Schreibens kann eine große `-wal`-Datei hinterlassen. Zwei Abhilfen:

- `SQLitePragmas[]=journal_size_limit=67108864` kürzt das WAL nach jedem Checkpoint, der es leert, auf 64 MB.
- Ein manueller Checkpoint, der die Datei kürzt:
  `sqlite3 var/storage/sqlite3/exponential.db "PRAGMA wal_checkpoint(TRUNCATE)"`. Er gibt drei Zahlen aus; eine
  erste Zahl `0` bedeutet, dass der Checkpoint nicht blockiert wurde.

### Überwachung einer SQLite-Site

| Was | Wie | Gesund |
|---|---|---|
| Sperr-Timeouts | `grep -c "database is busy" var/log/error.log` | `0`, oder selten und durch einen bekannten langen Job erklärt |
| Andere Datenbankfehler | `grep -i "eZSQLite3DB" var/log/error.log \| tail` | nichts Neues |
| Integrität | `sqlite3 var/storage/sqlite3/exponential.db "PRAGMA quick_check"` (schnell) oder `"PRAGMA integrity_check"` (gründlich) | `ok` |
| WAL-Größe | `ls -l var/storage/sqlite3/` | die `-wal`-Datei wächst nach arbeitsreichen Phasen nicht weiter; sie schrumpft nur mit `journal_size_limit` oder einem `TRUNCATE`-Checkpoint (siehe oben) |
| Journal-Modus | `sqlite3 var/storage/sqlite3/exponential.db "PRAGMA journal_mode"` | `wal` |
| Speicherplatz | `df -h var/storage/sqlite3` | Platz für die doppelte Datenbankgröße (für `VACUUM` und Sicherungen) |
| Eigentümer | `ls -l var/storage/sqlite3/` | alle Dateien für die Gruppe beschreibbar, eine gemeinsame Gruppe |

`exp:benchmark kernel` misst unter seinen Messpunkten auch den Datenbank-Roundtrip, und `exp:benchmark` mit `--save`
und `--baseline` erkennt eine Verlangsamung zwischen zwei Läufen ([Benchmark](../../en/us/features/6.0/benchmark.md)).

### Grenzen, und wann eine andere Engine die bessere Wahl ist

SQLite passt, wenn die Site auf **einer Maschine** läuft und ihre Schreiblast die eines Redaktionsteams ist, ganz
gleich, wie viele Besucher sie lesen. Wählen Sie MySQL, MariaDB oder PostgreSQL, wenn:

- **Mehr als eine Maschine** dieselbe Datenbank beschreiben muss. WAL braucht gemeinsamen Speicher auf einem Host;
  ein Netzwerkdateisystem ist kein Ersatz.
- **Viele Redakteure im selben Moment große Änderungen veröffentlichen**, den ganzen Tag. Jede Veröffentlichung
  wartet auf die vorherigen; ist die Warteschlange regelmäßig länger als `SQLiteTransactionWait`, bedient eine
  Server-Datenbank mit Sperren auf Zeilenebene sie besser.
- Sie **Replikation**, einen Hot Standby oder eine Point-in-Time-Wiederherstellung aus der Datenbank selbst
  brauchen. SQLite bringt nichts davon selbst mit.
- Sie **Datenbankbenutzer und Berechtigungen (Grants)** brauchen. Die Zugriffskontrolle von SQLite ist die des
  Dateisystems.

Die Datenbankgröße ist selten der Grund: SQLite bewältigt Datenbanken, die weit größer sind als der Inhalt einer
Exponential-Site ([Implementation limits](https://www.sqlite.org/limits.html)). Ein späterer Umzug ist möglich
(siehe [9.9](#99-eine-bestehende-site-auf-eine-andere-engine-umstellen)).

## 9.3 MySQL und MariaDB

Der Treiber ist `eZMySQLiDB` (`lib/ezdb/classes/ezmysqlidb.php`) auf Basis der PHP-Extension `mysqli`. MySQL und
MariaDB sind die klassische Wahl für die Produktion und die Engine, auf die die meisten Extensions ausgerichtet sind.

### Zeichensatz und Speicher-Engine

- **Legen Sie die Datenbank in `utf8mb4` an.** Der Setup-Assistent prüft den Standardzeichensatz der Datenbank und
  akzeptiert `utf8mb4` als UTF-8 (`kernel/setup/steps/ezstep_installer.php`); der Installer legt seine Tabellen ohne
  eigenen Zeichensatz an, sodass sie den Standard der Datenbank übernehmen.
- **Der Zeichensatz der Verbindung.** `Charset=utf-8` (der Standardwert) wird für die Verbindung auf `utf8` von MySQL
  abgebildet (`lib/ezdb/classes/ezmysqlcharset.php`); MySQL behandelt diesen Namen als `utf8mb3`, drei Byte pro
  Zeichen, sodass Zeichen außerhalb der Basic Multilingual Plane (Emoji, einige CJK-Erweiterungen) die Verbindung
  nicht passieren können. Halten Sie die Datenbank trotzdem in `utf8mb4`: Es kostet nichts und ist für eine Änderung
  der Verbindung vorbereitet.
- **Wenn sich der Zeichensatz der Verbindung nicht setzen lässt.** Der Treiber fragt die Verbindung, welchen
  Zeichensatz sie tatsächlich hat, versucht es noch einmal und zeichnet, wenn er immer noch abweicht, die Warnung
  „Connection warning: could not set the connection charset to 'utf8'; it is '...'. Text will be transliterated to
  that character set on its way out of the database.“ auf. Die Site läuft weiter, und Text kommt mit `?` an Stelle
  der Zeichen zurück, die dem anderen Zeichensatz fehlen. Die Warnung ist eine Warnung, kein Fehler: Sie erscheint in
  der Debug-Ausgabe und in `var/log/warning.log`, aber nur, wenn Warnungen protokolliert werden
  (`site.ini [DebugSettings] AlwaysLog[]=warning`; der mitgelieferte Wert protokolliert nur Fehler). Schalten Sie das
  ein, während Sie eine MySQL-Site einrichten.
- **InnoDB.** Der Installer legt die Tabellen als InnoDB an, wenn der Server es anbietet
  (`kernel/setup/steps/ezstep_create_sites.php`). Transaktionen benötigen es. `php bin/php/ezconvertmysqltabletype.php`
  (`exp:ezconvertmysqltabletype`) listet mit `--list` die Tabellentypen einer älteren Installation auf und wandelt sie
  mit `--newtype=InnoDB` um. Die MySQL-Update-Dateien eines Upgrades beginnen mit
  `SET default_storage_engine=InnoDB;`, was jede aktuelle MySQL- und MariaDB-Version akzeptiert
  ([Kapitel 11](11-aktualisieren.md#113-die-update-dateien)).

### Datenbank und Benutzer anlegen

```sql
CREATE DATABASE exponential CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'exponential'@'localhost' IDENTIFIED BY '<a strong password>';
GRANT SELECT, INSERT, UPDATE, DELETE, CREATE, DROP, INDEX, ALTER,
      LOCK TABLES, CREATE TEMPORARY TABLES
   ON exponential.* TO 'exponential'@'localhost';
```

Der Benutzer braucht kein globales Privileg, kein `SUPER` und kein Recht, Datenbanken aufzulisten: Der Assistent
arbeitet mit gewöhnlichen Privilegien pro Datenbank. `LOCK TABLES` und `CREATE TEMPORARY TABLES` sind aufgeführt,
weil `lock()` des Treibers `LOCK TABLES` verwendet und der Kernel für einige Vorgänge temporäre Tabellen anlegt.

### Einstellungen

```ini
[DatabaseSettings]
DatabaseImplementation=ezmysqli
Server=localhost
# empty: 3306
Port=
User=exponential
Password=<the password>
Database=exponential
# a Unix socket instead of host and port, e.g. /var/lib/mysql/mysql.sock
Socket=disabled
Charset=utf-8
# enabled: mysqli persistent connections (the driver prefixes the host with p:)
UsePersistentConnection=disabled
ConnectRetries=0
```

Installieren Sie über die Konsole (das Passwort aus der Umgebung, damit es nicht in der Shell-Historie landet):

```bash
EXP_INSTALL_DB_PASSWORD='<the password>' php bin/php/console exp:install --db=mysql \
    --db-host=127.0.0.1 --db-name=exponential --db-user=exponential --allow-root-user
```

`--db=mariadb` ist dasselbe. Der Kickstarter und der Assistent nehmen dieselben Werte.

### Replikate

`UseSlaveServer=enabled` mit `SlaveServerArray[]`, `SlaveServerPort[]`, `SlaverServerUser[]`,
`SlaverServerPassword[]` und `SlaverServerDatabase[]` (die Schlüssel heißen tatsächlich `Slaver...`) sendet
Lesezugriffe an ein pro Anfrage zufällig gewähltes Replikat; Schreibvorgänge gehen an `Server`. Durch die
Replikationsverzögerung kann eine Seite Inhalte zeigen, die einige Sekunden alt sind.

### Stolperfallen

| Symptom | Ursache |
|---|---|
| Die Seite „Datenbank nicht erreichbar“ | Falscher Host, Benutzer, falsches Passwort oder falsche Datenbank. Seit PHP 8.1 wirft `mysqli` eine Ausnahme; der Treiber macht daraus `eZDBNoConnectionException`. |
| Text mit `?` an Stelle von Buchstaben mit Akzenten | Die Verbindung ist auf den Standardzeichensatz des Servers zurückgefallen. Setzen Sie `AlwaysLog[]=warning`, laden Sie eine Seite und suchen Sie in `var/log/warning.log` nach „could not set the connection charset“. |
| Der Assistent lehnt den Zeichensatz der Datenbank ab | Die Datenbank wurde in `latin1` oder einem anderen Einzelbyte-Zeichensatz angelegt. Legen Sie sie in `utf8mb4` neu an. |
| Langsame Abfragen | `[DatabaseSettings] QueryAnalysisOutput=enabled` mit `SQLOutput=enabled` zeigt die Analyse von MySQL in der Debug-Ausgabe (nur MySQL). |

Sicherungen: `mysqldump --single-transaction -u exponential -p exponential > backup.sql`, konsistent, ohne
InnoDB-Tabellen zu sperren ([mysqldump](https://dev.mysql.com/doc/refman/8.4/en/mysqldump.html)).

## 9.4 PostgreSQL

Der Treiber ist `eZPostgreSQLDB` (`lib/ezdb/classes/ezpostgresqldb.php`) auf Basis der PHP-Extension `pgsql`. Der
Assistent prüft auf PostgreSQL 8.0, verwenden Sie aber eine unterstützte Release; `pgcrypto` ist ab PostgreSQL 13
eine vertrauenswürdige (trusted) Extension, worauf sich der Installer verlässt (siehe unten).

### Datenbank und Benutzer anlegen

```bash
sudo -u postgres createuser --pwprompt exponential
sudo -u postgres createdb --owner=exponential --encoding=UTF8 --template=template0 exponential
```

Dass der Benutzer **Eigentümer** der Datenbank ist, ist wichtig: Ab PostgreSQL 15 dürfen gewöhnliche Benutzer keine
Tabellen mehr im Schema `public` einer Datenbank anlegen, die ihnen nicht gehört.

### Extensions: pgcrypto

Der Treiber berechnet MD5 mit `encode(digest(..., 'md5'), 'hex')`, und `digest()` stammt aus
[pgcrypto](https://www.postgresql.org/docs/current/pgcrypto.html). Der Installer führt
`CREATE EXTENSION IF NOT EXISTS pgcrypto` aus, wenn der Server sie mitbringt; als vertrauenswürdige Extension darf
der Eigentümer der Datenbank sie anlegen. Fehlt sie dem Server, installieren Sie das PostgreSQL-contrib-Paket Ihrer
Distribution und führen dann als Eigentümer oder Superuser Folgendes aus:

```bash
sudo -u postgres psql -d exponential -c 'CREATE EXTENSION IF NOT EXISTS pgcrypto;'
```

### Sequenzen

Auto-Increment-Spalten sind Integer mit dem Standardwert `nextval('<table>_<column>_seq')`; der Schema-Handler legt
jede Sequenz vor ihrer Tabelle an (`CREATE SEQUENCE IF NOT EXISTS ...`), und `lastSerialID()` liest
`currval('<table>_<column>_seq')`. Werden Daten mit expliziten IDs geladen (ein Import, eine Wiederherstellung
ausgewählter Tabellen), bleiben die Sequenzen zurück, und das nächste Insert scheitert mit einem doppelten
Schlüssel. Der Schema-Installer korrigiert sie nach dem Laden der Daten (`correctSequenceValues()`); für eine
einzelne Tabelle von Hand:

```sql
SELECT setval('ezcontentobject_id_seq', (SELECT max(id) FROM ezcontentobject));
```

### Einstellungen

```ini
[DatabaseSettings]
DatabaseImplementation=ezpostgresql
Server=localhost
# 5432 is the server's default
Port=5432
User=exponential
Password=<the password>
Database=exponential
# enabled: pg_pconnect instead of pg_connect
UsePersistentConnection=disabled
```

```bash
EXP_INSTALL_DB_PASSWORD='<the password>' php bin/php/console exp:install --db=pgsql \
    --db-host=127.0.0.1 --db-name=exponential --db-user=exponential --allow-root-user
```

Der Assistent füllt Port 5432 und den Benutzer `postgres` vor (`settings/setup.ini` `DefaultPort_pgsql`,
`DefaultUser_pgsql`) und installiert in die Datenbank, die Sie angeben; `template1` öffnet er nur, um eine Liste
anzubieten, wenn kein Name angegeben ist.

### Stolperfallen

| Symptom | Ursache |
|---|---|
| „The 'digest' function is not available in your database, and Exponential cannot run without it. ...“ (der Setup-Assistent) | `pgcrypto` konnte nicht angelegt werden. Installieren Sie das contrib-Paket und legen Sie die Extension wie oben gezeigt an; klicken Sie dann erneut auf „Weiter“. |
| Verbindung abgelehnt | `listen_addresses` in `postgresql.conf` oder eine fehlende Zeile in `pg_hba.conf` ([Client-Authentifizierung](https://www.postgresql.org/docs/current/client-authentication.html)). |
| Doppelter Schlüssel beim Insert nach einem Import | Sequenzen hinken ihren Tabellen hinterher; korrigieren Sie sie wie oben. |
| Das SQL einer Extension scheitert nur unter PostgreSQL | MySQL-Syntax wie Backtick-Quoting; siehe [9.8](#98-hinweise-über-engines-hinweg). |

Sicherungen: `pg_dump -U exponential -Fc exponential > backup.dump`, wiederhergestellt mit `pg_restore`
([pg_dump](https://www.postgresql.org/docs/current/app-pgdump.html)).

## 9.5 MongoDB

Der Adapter ist `expMongoDB` (`lib/ezdb/classes/expmongodb.php`) auf Basis der PHP-Extension `mongodb` und des
`MongoDB\Client` der MongoDB-PHP-Bibliothek. Er übersetzt die SQL-förmigen Aufrufe des Kernels in `find`-,
`aggregate`-, `insert`-, Upsert- und Delete-Operationen. MongoDB kam mit 6.0.14 hinzu; lesen Sie die Grenzen, bevor
Sie es für die Produktion wählen.

### Voraussetzungen

| | Minimum | Getestet |
|---|---|---|
| PHP | 8.2 | 8.5 |
| MongoDB-Server | 6.0 (der Assistent prüft auf 4.0) | 8.3 |
| PHP-Extension `mongodb` | 1.18 | aktuell |

### Den Datenbankbenutzer anlegen

Der Adapter verbindet sich mit der URI `mongodb://<user>:<password>@<server>:<port>/<database>`. MongoDB verwendet die
Datenbank im Pfad der URI als Authentifizierungsdatenbank, wenn kein `authSource` angegeben ist
([Connection Strings](https://www.mongodb.com/docs/manual/reference/connection-string/)), legen Sie den Benutzer
daher **in der Datenbank der Site** an:

```bash
mongosh --eval '
  db.getSiblingDB("exponential").createUser({
    user: "exponential",
    pwd: passwordPrompt(),
    roles: [ { role: "readWrite", db: "exponential" }, { role: "dbAdmin", db: "exponential" } ]
  })'
```

Die Datenbank selbst wird durch den ersten Schreibvorgang angelegt; ein `CREATE DATABASE` gibt es nicht.

Der Benutzer ist erforderlich, auch auf einem Testserver, der keine Zugriffskontrolle erzwingt: Der Adapter schreibt
immer `<user>:<password>@` in die URI, und bei leerem `User` lehnt der MongoDB-Treiber das Ergebnis
(`mongodb://:@...`) mit „Failed to parse MongoDB URI: ... 'default' authentication mechanism requires a username“ ab.

### Einstellungen

```ini
[DatabaseSettings]
DatabaseImplementation=mongodb
Server=localhost
# empty: 27017
Port=27017
User=exponential
Password=<the password>
Database=exponential
```

Benutzername und Passwort werden vom Adapter URL-kodiert. Pro Verbindungs-URI wird ein Client für die Lebensdauer des
Prozesses vorgehalten, sodass ein Velocity-Worker seine Verbindungen wiederverwendet.

### Installieren

1. Geben Sie dem Setup-Assistenten Zeit: Unter MongoDB kann der Konfigurationsschritt ein bis drei Minuten dauern.
   Erhöhen Sie vorher das `request_terminate_timeout` von PHP-FPM, oder installieren Sie über die Konsole:

   ```bash
   EXP_INSTALL_DB_PASSWORD='<the password>' php bin/php/console exp:install --db=mongodb \
       --db-host=127.0.0.1 --db-name=exponential --db-user=exponential --allow-root-user
   ```

2. Legen Sie die Indizes an, auf die sich die Abfragen des Kernels verlassen; ohne sie durchsuchen Abfragen auf
   Teilbäume und Nachschlagevorgänge für URL-Aliase ganze Collections:

   ```bash
   mongosh "mongodb://exponential@localhost:27017/exponential" --file bin/mongodb/create_indexes.js
   ```

3. Führen Sie die Cronjobs einmal aus, um den Suchindex aufzubauen (`php runcronjobs.php --allow-root-user`).

### Grenzen

Aus [MongoDB als Datenbank](../../en/us/features/6.0/mongodb-database-support.md) und Abschnitt 26 der
[Portierungsreferenz](../../en/us/bc/6.0/MONGODB_KERNEL_SUPPORT_EXPANSION.md):

- Der Adapter führt nie echtes SQL aus. Von Hand geschriebenes SQL, das über `arrayQuery()` gesendet wird und das er
  nicht übersetzen kann, liefert ein leeres Ergebnis und protokolliert eine Warnung; suchen Sie in den Protokollen
  nach `MONGO TODO`. Extensions mit eigenem SQL brauchen unter Umständen einen MongoDB-Pfad.
- Die tiefe Neuerzeugung von URL-Aliasen nach dem Verschieben von Teilbäumen ist unvollständig; die Listenseiten für
  Rollen und Richtlinien können leer sein, obwohl die Berechtigungen durchgesetzt werden; einige Cronjobs sind nicht
  portiert; `eztags` ist nicht nutzbar; der Shop-Checkout und die Zeitplanung von `ezflow` sind nicht durchgängig
  getestet.
- Der SQL-Abfrage-Cache gilt nicht für MongoDB.

Eine bestehende MySQL-Site nach MongoDB umziehen: die Skripte in `bin/mongodb/` (`export_mysql.sh`,
`mysql2ndjson.py`, `import_all.sh`, `validate_ndjson.sh`) und Abschnitt 18 der Portierungsreferenz.

Sicherungen: `mongodump --uri="mongodb://exponential@localhost:27017/exponential" --out=/backup/$(date +%F)`
([mongodump](https://www.mongodb.com/docs/database-tools/mongodump/)).

## 9.6 Oracle 19c und neuer (ezoracle)

Die Oracle-Unterstützung stammt aus der Extension **`ezoracle`** (Version 2.3.7, die Version, die `composer.json` mit `~2.3.7` verlangt): der Treiber
`eZOracleDB`, der Schema-Handler `eZOracleSchema`, Konsolenbefehle und Cronjobs. Sie ist eine Abhängigkeit in
`composer.json` und liegt daher nach `composer install` in `extension/ezoracle`. Ihre eigenen Dateien `README.md` und
`INSTALL` sind die vollständige Referenz; dieser Abschnitt ist der Weg des Betreibers durch sie.

### Voraussetzungen

- Exponential 6.0.15 oder neuer (frühere Versionen bieten Oracle in den Installern nicht an).
- **Oracle Database 19c oder neuer** (der Assistent prüft auf 19.0). Der Treiber verwendet Funktionen von 12.1
  (`STANDARD_HASH`, `OFFSET`/`FETCH`); 11g und älter werden nicht unterstützt. Getestet mit Oracle AI Database 26ai
  Free (23.26).
- Datenbankzeichensatz **AL32UTF8**. Der Treiber verbindet sich in AL32UTF8; `NLS_LANG` wird nicht benötigt.
- PHP 8.0 bis 8.5 mit **oci8** (PECL oci8 3.4 für PHP 8.4 und 8.5, 3.2 für 8.1 bis 8.3), gebaut gegen einen
  Oracle-Client; der Instant Client genügt. `ORACLE_HOME` wird mit dem Instant Client nicht benötigt; setzen Sie
  `LD_LIBRARY_PATH` (und `TNS_ADMIN` für TNS-Aliase) nur, wenn der Client außerhalb des Bibliothekspfads des Systems
  liegt, und dann auch im PHP-FPM-Pool (`env[...]`) und in der Umgebung von Velocity.
- Ein Oracle-Benutzer, der Sessions, Tabellen, Views, Trigger, Sequenzen und Prozeduren anlegen darf.

### Den Benutzer anlegen

Die Extension gibt das SQL aus, das Sie als DBA ausführen:

```bash
ORACLE_NEW_PASSWORD='<a strong password>' php extension/ezoracle/bin/php/ora-grant.php exponential - USERS
```

(`-` nimmt das Passwort aus `ORACLE_NEW_PASSWORD`, sodass es nicht in der Prozessliste erscheint; `USERS` ist der
Tablespace.) Dasselbe steht als `php bin/php/console ext:ezoracle:ora-grant` zur Verfügung. Für die Befehle
`health` und `report` gewähren Sie außerdem `SELECT_CATALOG_ROLE`.

### Installieren

```bash
EXP_INSTALL_DB_PASSWORD='<the password>' php bin/php/console exp:install --db=oracle \
    --db-host=127.0.0.1 --db-port=1521 --db-name=FREEPDB1 --db-user=exponential --allow-root-user
```

`--db-name` ist der **Service-Name**; daraus wird `host:port/service` gebildet. Ein vollständiger Connect-String oder
`@<tns alias>` wird unverändert verwendet. Die Installer aktivieren die Extension für die neue Site. Setzen Sie für
den Kickstarter `[database_choice] Type=oci8` (`oracle` wird ebenfalls akzeptiert) und den Connect-String als
`Database` in `[database_init]` und `[site_details]`.

### Einstellungen

Die Verbindung steht in `site.ini`:

```ini
[DatabaseSettings]
DatabaseImplementation=ezoracle
User=exponential
Password=<the password>
# The connect string: Easy Connect host:port/service_name,
# Easy Connect Plus tcp://host:port/service?connect_timeout=5,
# a TNS alias, or a full (DESCRIPTION=...) descriptor.
# Server and Port are not used by this driver.
Database=127.0.0.1:1521/FREEPDB1
Charset=utf-8
# Oracle stores '' as NULL; enabled returns '' for NULL in text columns
OracleEmptyStringForNull=disabled
# enabled: ALTER SESSION SET NLS_COMP=LINGUISTIC NLS_SORT=<sort> (case-insensitive =, LIKE, ORDER BY)
OracleCaseInsensitive=disabled
OracleCaseInsensitiveSort=BINARY_CI

[ExtensionSettings]
ActiveExtensions[]=ezoracle
```

Alles über die einfache Verbindung hinaus steht in `ezoracle.ini` (`extension/ezoracle/settings/ezoracle.ini`) und
wird in `settings/override/ezoracle.ini.append.php` überschrieben. Jede Funktion ist standardmäßig ausgeschaltet.

### Persistente Verbindungen und DRCP

Eine neue dedizierte Oracle-Session kostet pro Anfrage einige zehn Millisekunden. Zwei Einstellungen beseitigen
diese Kosten:

```ini
# settings/override/ezoracle.ini.append.php
[ConnectionSettings]
# oci_pconnect: inherit (site.ini UsePersistentConnection, the shipped value),
# enabled or disabled
Persistent=enabled
# Database Resident Connection Pooling (shipped: disabled): appends :POOLED to an Easy Connect string,
# SERVER=POOLED to a descriptor built from Hosts[]; for a TNS alias put
# (SERVER=POOLED) into tnsnames.ora yourself
DRCP=enabled
# oci8.connection_class: sessions of one class are shared
ConnectionClass=EXPONENTIAL
# for long-running processes (Velocity workers, cronjobs): after this many idle
# seconds a round trip checks the connection first and replaces a dead one
# (shipped: 0, never)
KeepAliveInterval=60
```

- **Persistente Verbindungen** leben so lange wie der PHP-Prozess: ein PHP-FPM-Worker, ein Velocity-Worker, ein
  CLI-Skript. `oci8.persistent_timeout`, `oci8.max_persistent` und `oci8.ping_interval` in `php.ini` steuern sie; sie
  lassen sich nicht zur Laufzeit setzen.
- **DRCP** ([Database Resident Connection Pooling](https://docs.oracle.com/en/database/oracle/oracle-database/19/admin/managing-processes.html))
  teilt einen Pool von Serverprozessen zwischen all diesen PHP-Prozessen, sodass Hunderte von Workern keine Hunderte
  dedizierter Serverprozesse brauchen. Der Pool muss einmalig in der Datenbank gestartet werden, als DBA:
  `EXECUTE DBMS_CONNECTION_POOL.START_POOL;`. Die PHP-Seite beschreibt das Kapitel
  [oci8 connection handling](https://www.php.net/manual/en/oci8.connection.php).

**Für diese Einstellungen ist die Reihenfolge der Extensions wichtig.** Liefern zwei aktive Extensions beide
Einstellungen für `ezoracle.ini`, gewinnt die in `ActiveExtensions[]` **früher** aufgeführte:
`eZExtension::activateExtensions()` stellt das Einstellungsverzeichnis jeder Extension der Reihe nach voran, sodass
eine spätere Extension am Ende eine niedrigere Priorität hat. `ezoracle` liefert eine vollständige `ezoracle.ini`
mit; halten Sie Ihre Verbindungseinstellungen statt in `settings/override/` in einer eigenen Site-Extension, führen
Sie diese Extension **vor** `ezoracle` auf:

```ini
[ExtensionSettings]
ActiveExtensions[]=mysite_settings
ActiveExtensions[]=ezoracle
```

`settings/override/` gewinnt immer gegenüber jeder Extension. Prüfen Sie die wirksamen Werte nach einer Änderung mit
`php bin/php/console exp:ini get ezoracle.ini/ConnectionSettings/DRCP --allow-root-user`. Die Ladereihenfolge lässt
sich auch in der Administration unter **Setup > Extensions** ändern
([Ladereihenfolge der Extensions](../../en/us/features/6.0/extension-loading-order.md)).

### Weitere Einstellungen

| Block | Schlüssel | Was er bewirkt |
|---|---|---|
| `[ConnectionSettings]` | `ConnectString`, `TnsAdmin` | Ein Connect-String, der `Database` aus `site.ini` ersetzt (leer: `Database` verwenden); das Verzeichnis von `tnsnames.ora` für TNS-Aliase (`env[TNS_ADMIN]` des PHP-FPM-Pools oder die Umgebung von Velocity ist der sicherere Ort, weil der Client es einmal pro Prozess liest). |
| `[ConnectionSettings]` | `Hosts[]`, `ServiceName`, `Failover`, `LoadBalance`, `ConnectTimeout` | Einen Deskriptor über mehrere Listener bilden (wenn `ConnectString` leer ist). |
| `[ConnectionSettings]` | `RetryCount`, `RetryDelay`, `RetryBackoff` | Verbindungsversuche mit Backoff (Standardwert: `site.ini ConnectRetries`). |
| `[ConnectionSettings]` | `ReconnectErrors[]`, `RetryReads` | Bei einer verlorenen Verbindung außerhalb einer Transaktion neu verbinden und einen Lesezugriff erneut ausführen; ein Schreibvorgang wird gemeldet. |
| `[ConnectionSettings]` | `CallTimeout` | Millisekunden, die ein Roundtrip dauern darf (Client 18c oder neuer); 0 = keine Grenze. |
| `[PerformanceSettings]` | `Prefetch`, `LobPrefetch`, `CursorSharing` | Zeilen pro Roundtrip; LOB-Bytes mit der Zeile (mitgeliefert 2000, und vom Treiber dort begrenzt); `CURSOR_SHARING` (mitgeliefert `FORCE`). |
| `[TraceSettings]` | `ClientIdentifier`, `ModuleName`, `Action`, `ClientInfo` | Was ein DBA in `V$SESSION` sieht, mit Mustern wie `%siteaccess%`, `%module%/%view%`. |
| `[LogSettings]` | `SlowQueryThreshold`, `SlowQueryLog`, `MaskLiterals` | Langsame Anweisungen nach `var/log/oracle-slow.log`, String-Literale maskiert. |

### Wartung

Konsolenbefehle (`php bin/php/console ext:ezoracle:<name>`): `health`, `gather-stats`, `recompile`,
`purge-recyclebin`, `sequence-sync`, `schema-diff`, `indexes`, `report`, `datapump`, `ci-indexes`. Jeder gibt pro
Prüfung eine Zeile PASS/WARN/FAIL/INFO aus. Die Cronjob-Teile `ezoracle` (nächtlich) und `ezoraclehealth`
(stündlich) führen sie aus; jeder Job tut nichts, bis er in `ezoracle.ini [CronjobSettings]` aktiviert ist:

```
15 3 * * *  cd /path/to/installation && php runcronjobs.php -q -s <admin siteaccess> ezoracle
0 * * * *   cd /path/to/installation && php runcronjobs.php -q -s <admin siteaccess> ezoraclehealth
```

### Stolperfallen

| Symptom | Ursache |
|---|---|
| `ORA-00933` im SQL einer Extension | Ein Tabellenalias mit `AS`; Oracle erlaubt `AS` nur für Spaltenaliase. |
| Ein leerer String kommt als NULL zurück | Oracle speichert `''` als NULL. `OracleEmptyStringForNull=enabled` gibt `''` für Textspalten zurück. |
| Eine Anmeldung funktioniert nur in einer Schreibweise | Oracle vergleicht Strings binär. `OracleCaseInsensitive=enabled` setzen, dann linguistische Indizes mit `ext:ezoracle:ci-indexes --create` anlegen. |
| CLOB-Werte abgeschnitten | Eine ältere `ezoracle` oder eine Client-Version, die lange CLOBs abschneidet, wenn `LobPrefetch` über 2000 liegt. Der aktuelle Treiber begrenzt das Prefetch selbst auf 2000 und schreibt einen Hinweis, wenn mehr eingestellt ist (oci8 3.4.1 mit dem Oracle-23.26-Client schnitt jeden längeren CLOB ohne Fehler ab). Aktualisieren Sie die Extension. |
| Einstellungen in Ihrer Site-Extension werden ignoriert | Sie steht in `ActiveExtensions[]` nach `ezoracle`; verschieben Sie sie davor. |

## 9.7 Vergleich

| | SQLite 3 | MySQL / MariaDB | PostgreSQL | MongoDB | Oracle |
|---|---|---|---|---|---|
| `DatabaseImplementation` | `sqlite3` | `ezmysqli` | `ezpostgresql` | `mongodb` | `ezoracle` |
| PHP-Extension | `sqlite3` | `mysqli` | `pgsql` | `mongodb` | `oci8` |
| Zu betreibender Server | keiner | ja | ja | ja | ja |
| Voreinstellung des Installers | ja | | | | |
| Gleichzeitige Schreiber | einer zur Zeit, in der Warteschlange (`SQLiteTransactionWait`) | Zeilensperren (InnoDB) | Zeilensperren (MVCC) | auf Dokumentebene | Zeilensperren |
| Mehrere Anwendungsserver | nein (ein Host) | ja | ja | ja | ja |
| Replikate für Lesezugriffe | nein | `UseSlaveServer` | extern | Replica Sets | extern (Data Guard) |
| Persistente Verbindungen | nicht nötig (eine Datei) | `UsePersistentConnection` | `UsePersistentConnection` | Client pro Prozess vorgehalten | `Persistent`, DRCP |
| SQL-Abfrage-Cache | ja | ja | ja | nein | ja |
| Benutzer und Berechtigungen | Dateisystem | ja | ja | ja | ja |
| Sicherung | `.backup`, `VACUUM INTO` | `mysqldump --single-transaction` | `pg_dump` | `mongodump` | `ext:ezoracle:datapump`, `expdp` |
| Bekannte Lücken | ein Host; Schreibvorgänge in der Warteschlange | 4-Byte-Zeichen über die `utf8`-Verbindung | | siehe [Grenzen](#grenzen) | `''` ist NULL; Groß-/Kleinschreibung wird unterschieden |
| Am besten für | einen Server, beliebig viele Leser, ein Redaktionsteam | die klassische Produktionsumgebung, die meisten Extensions | Produktion, striktes SQL | Organisationen, die auf MongoDB standardisiert sind | Organisationen, die auf Oracle standardisiert sind |

## 9.8 Hinweise über Engines hinweg

Das eigene SQL von Exponential läuft auf jeder Engine; Extension-Code und von Hand geschriebenes SQL oft nicht. Was
Sie wissen sollten, wenn Sie solchen Code schreiben oder prüfen (es gibt kein eigenes Dokument zur Semantik von
Abfragen über Engines hinweg; diese Punkte stammen aus den Treibern):

- **Fragen Sie den Treiber, welche Engine er ist**: `eZDB::instance()->databaseName()` gibt `mysql`, `postgresql`,
  `sqlite`, `mongo` oder `oracle` zurück.
- **Keine Backticks.** MySQL und SQLite akzeptieren `` `column` ``; PostgreSQL und Oracle nicht, und der Parser des
  MongoDB-Adapters erwartet sie nicht. Benennen Sie Tabellen und Spalten ohne Quoting.
- **Kein `AS` vor einem Tabellenalias** (Oracle lehnt es ab); `AS` vor einem Spaltenalias ist überall in Ordnung.
- **Verwenden Sie die Hilfsfunktionen des Treibers** für das, was sich unterscheidet: `subString()`,
  `concatString()`, `md5()`, `bitAnd()`, `bitOr()`, `escapeString()`, `lastSerialID()`. Der SQLite-Treiber
  registriert außerdem die Aggregatfunktionen `BIT_OR`/`BIT_AND` und schreibt `UPDATE ... JOIN` um; PostgreSQL
  berechnet MD5 über `pgcrypto`; Oracle verwendet `STANDARD_HASH` und `BITAND`.
- **MySQL-Session-Anweisungen**: Der SQLite-Treiber akzeptiert `SET FOREIGN_KEY_CHECKS`, `SET NAMES`,
  `SET CHARACTER SET`, `SET AUTOCOMMIT`, `SET SQL_MODE` und `SET UNIQUE_CHECKS` (auch mit `SESSION` oder `GLOBAL`)
  und tut nichts, weil SQLite keine solchen Schalter hat. PostgreSQL versteht `SET NAMES` (als `client_encoding`),
  lehnt die anderen aber ab; Oracle hat keine `SET`-Anweisung dieser Art. Lassen Sie sie in Code weg, der für jede
  Engine gedacht ist.
- **Leere Strings**: Oracle gibt für `''` NULL zurück, außer bei `OracleEmptyStringForNull=enabled`.
- **Groß-/Kleinschreibung**: Die Standard-Kollationen von MySQL vergleichen ohne Beachtung der Groß-/Kleinschreibung.
  PostgreSQL und Oracle beachten sie beim Vergleich. SQLite beachtet sie bei `=`, ignoriert sie aber bei
  ASCII-Buchstaben in `LIKE`. Eine für MySQL geschriebene Suche nach einer Anmeldung oder einem Bezeichner findet
  deshalb anderswo womöglich nichts; vergleichen Sie `lower(...)` auf beiden Seiten, wenn die Groß-/Kleinschreibung
  keine Rolle spielen darf.
- **Schließen Sie aus einem Ergebnis nicht auf etwas, das das Ergebnis nicht aussagen kann.** Eine gescheiterte
  Anweisung und ein leeres Ergebnis werden nicht auf jeder Engine gleich gemeldet; der MongoDB-Adapter liefert für
  SQL, das er nicht übersetzen kann, ein leeres Ergebnis. Um zu erfahren, ob eine Tabelle existiert, fragen Sie den
  Katalog (`eZTableList()` des Treibers), nicht ein `SELECT`, das scheitern kann.
- **Indexnamen** gelten unter SQLite und PostgreSQL datenbankweit; die Schema-Handler legen einen kollidierenden
  Namen als `<table>__<name>` an.

## 9.9 Eine bestehende Site auf eine andere Engine umstellen

Warum überhaupt umstellen: Die Gründe sind die aus
[Grenzen, und wann eine andere Engine die bessere Wahl ist](#grenzen-und-wann-eine-andere-engine-die-bessere-wahl-ist).
Der Inhalt bleibt derselbe; nur der Ort, an dem er gespeichert wird, ändert sich. Planen Sie die Umstellung als
Wartungsfenster: Zwischen dem Dump und dem Moment, in dem die Site auf der neuen Datenbank läuft, darf kein Redakteur
veröffentlichen, sonst geht diese Arbeit verloren.

Die Schema-Werkzeuge übertragen eine Datenbank zwischen den SQL-Engines über das Engine-neutrale `.dba`-Format von
Exponential. `--type` nimmt einen Schema-Handler aus `settings/dbschema.ini`: `mysql`, `postgresql`, `sqlite3` und,
mit der Extension `ezoracle`, `oracle` (das eigene `--help` der Skripte listet nur einige davon auf). Für SQLite ist
das Datenbankargument der Dateiname, wie in `Database`:

```bash
# dump schema and data of the current database (MySQL in this example):
# ezsqldumpschema.php [OPTION]... [DATABASE] [FILENAME]
php bin/php/ezsqldumpschema.php --type=mysql --host=localhost --user=exponential --password='<password>' \
    --format=generic --output-array --output-types=all exponential var/backup/site.dba --allow-root-user
# load them into the new one:
# ezsqlinsertschema.php [OPTION]... [FILENAME] [DATABASE]
php bin/php/ezsqlinsertschema.php --type=postgresql --host=localhost --user=exponential --password='<password>' \
    --insert-types=all --schema-file=var/backup/site.dba var/backup/site.dba exponential --allow-root-user
```

`--password=` nimmt seinen Wert auf der Kommandozeile entgegen, wo andere Benutzer der Maschine ihn in der
Prozessliste sehen können; führen Sie die Übertragung auf einer Maschine aus, die Sie kontrollieren, oder mit einem
vorübergehenden Passwort, das Sie danach ändern. Führen Sie beide Skripte zuerst mit `--help` aus: Dort sind die
akzeptierten Typen und die Optionen pro Engine (Host, Port, Socket, Tabellentyp, Tabellenzeichensatz) aufgeführt.
Setzen Sie dann
`DatabaseImplementation` und die Verbindungsschlüssel in `settings/override/site.ini.append.php` auf die neue Engine,
leeren Sie alle Caches und starten Sie die Worker neu. Ein vergessenes `DatabaseImplementation` ist der übliche
Grund, warum nach einer Umstellung keine Seite lädt. Für Oracle hat die Extension eigene Übertragungsbefehle
(`ext:ezoracle:mysql2oracle-schema`, `ext:ezoracle:mysql2oracle-data`); zu MongoDB siehe [9.5](#95-mongodb).

**Prüfen Sie das Ergebnis, bevor Sie die Site wieder öffnen.** Zählen Sie die Zeilen einiger großer Tabellen auf
beiden Seiten (`ezcontentobject`, `ezcontentobject_tree`, `ezcontentobject_attribute`); die Zahlen müssen
übereinstimmen. Korrigieren Sie unter PostgreSQL die Sequenzen, falls `ezsqlinsertschema.php` ein Problem mit ihnen
gemeldet hat (siehe [9.4](#94-postgresql)). Melden Sie sich dann in der Administration an, öffnen Sie einige Seiten
und veröffentlichen Sie ein Testobjekt; eine Veröffentlichung beansprucht die Schreibvorgänge, die ein Seitenaufruf
nicht auslöst.

**Schema-Updates gelten pro Engine.** Wenn Sie Exponential später aktualisieren, werden die Datenbank-Update-Dateien
nach der Engine gewählt, auf der die Site jetzt läuft, nicht nach der, auf der sie installiert wurde:
`update/database/mysql/`, `update/database/postgresql/` und `update/database/sqlite/`, und für Oracle die Dateien in
der Extension `ezoracle`. MongoDB hat keine SQL-Update-Dateien. [Kapitel 11](11-aktualisieren.md#113-die-update-dateien)
führt sie auf.

## 9.10 Microsoft SQL Server

Exponential 6.0.x hat keinen Treiber für Microsoft SQL Server; er ist derzeit bekanntermaßen mit zusätzlichem
Aufwand möglich. Die Datenbankschicht ist die Schnittstelle `eZDBInterface` mit austauschbaren Treiber-Aliasen und
Schema-Handlern; auf diesem Weg kamen auch die Unterstützung für Oracle und MongoDB hinzu. Die Liste der Arbeiten
(ein Treiber auf Basis von `sqlsrv`, die Aggregatfunktion `BIT_OR`, die SQL Server fehlt, ein Schema-Handler, SQL-
und Update-Dateien, Einträge in den Installern, Tests) steht unter
[Datenbankunterstützung](../../en/us/help/database-support.md#microsoft-sql-server-possible-with-additional-work).

**SQLite für öffentliche Demos.** SQLite ist die übliche Datenbank einer öffentlichen Demo, die alle paar Stunden von Grund auf neu aufgebaut wird: `EXP_DEMO_MODE=1 ./console exp:install --db=sqlite --password=<the published password>` installiert eine in etwa einer Minute, ohne dass zwischen den Builds etwas gelöscht werden muss. Der Demo-Modus selbst hängt nicht von der Datenbank-Engine ab ([Demo-Modus](../../en/us/bc/6.0/demo-mode.md)).

## Quellen

In diesem Repository:

- [Datenbankunterstützung](../../en/us/help/database-support.md): die unterstützten Datenbanken in ihrer offiziellen
  Reihenfolge, die gegen den Code geprüften Versionen, die Unterstützung durch die Installer pro Weg und die
  CI-Abdeckung.
- [SQLite-Datenbankunterstützung](../../en/us/features/6.0/sqlite-database.md): die aufgabenorientierte Einführung.
- [Spezifikation: der SQLite3-Datenbanktreiber](../spezifikationen/6.0/sqlite3-datenbanktreiber.md): Verbindung,
  PRAGMAs, Transaktionen und Schreiber, die SQL-Kompatibilitätsschicht.
- [SQLite: Transaktionen warten in einer Schlange auf die Schreibsperre](../../en/us/bc/6.0/sqlite-transactions.md): die
  Änderung in 6.0.15 und Upgrade-Hinweise.
- [Datenbanktreiber und Installer, 16. bis 30. September 2026](../spezifikationen/6.0/datenbanktreiber-2026-09.md):
  Änderungen an SQLite, PostgreSQL, MySQL und Oracle, der Setup-Assistent.
- [SQLite für Exponential Platform](../../en/us/features/6.0/platform-sqlite-install.md) und
  [SQLite-Installer für Platform](../spezifikationen/6.0/platform-sqlite-installer.md).
- [SQL-Abfrage-Cache](../../en/us/bc/6.0/sql-query-cache.md).
- [MongoDB als Datenbank](../../en/us/features/6.0/mongodb-database-support.md) und die
  [Portierungsreferenz zur MongoDB-Unterstützung im Kernel](../../en/us/bc/6.0/MONGODB_KERNEL_SUPPORT_EXPANSION.md)
  (Abschnitte 18, 24, 26).
- [Installation mit einem Befehl](../../en/us/features/6.0/install-in-one-command.md), [Kickstarter](../../en/us/features/6.0/kickstarter-cli.md).
- [Velocity-Server mit persistenten Workern](../../en/us/features/6.0/velocity-persistent-worker-server.md),
  [Velocity-Antwort-Cache](../../en/us/features/6.0/velocity-response-cache.md),
  [Velocity-Worker-Pool](../spezifikationen/6.0/velocity-worker-pool.md).
- [Ladereihenfolge der Extensions](../../en/us/features/6.0/extension-loading-order.md).
- [Eine Site betreiben](../anleitungen/betrieb-einer-website.md), Abschnitt 7 (Datenbanktreiber) und Abschnitt 6
  (Sicherungen).
- [Kapitel 11, die Update-Dateien](11-aktualisieren.md#113-die-update-dateien), zu Schema-Updates pro Engine.
- Der Code: [`ezsqlite3db.php`](../../../lib/ezdb/classes/ezsqlite3db.php), [`ezmysqlidb.php`](../../../lib/ezdb/classes/ezmysqlidb.php),
  [`ezpostgresqldb.php`](../../../lib/ezdb/classes/ezpostgresqldb.php), [`expmongodb.php`](../../../lib/ezdb/classes/expmongodb.php),
  [`settings/site.ini`](../../../settings/site.ini) (`[DatabaseSettings]`), [`settings/setup.ini`](../../../settings/setup.ini),
  [`settings/dbschema.ini`](../../../settings/dbschema.ini), [`bin/modfix.sh`](../../../bin/modfix.sh),
  [`bin/mongodb/create_indexes.js`](../../../bin/mongodb/create_indexes.js).
- Die Oracle-Extension: `extension/ezoracle/README.md` und `extension/ezoracle/INSTALL` in einer Installation sowie
  [se7enxweb/ezoracle](https://github.com/se7enxweb/ezoracle).

Extern:

- SQLite: [Write-Ahead Logging](https://www.sqlite.org/wal.html), [File locking and concurrency](https://www.sqlite.org/lockingv3.html),
  [BEGIN TRANSACTION (DEFERRED, IMMEDIATE)](https://www.sqlite.org/lang_transaction.html),
  [PRAGMA statements](https://www.sqlite.org/pragma.html), [Online backup API](https://www.sqlite.org/backup.html),
  [VACUUM](https://www.sqlite.org/lang_vacuum.html), [ANALYZE](https://www.sqlite.org/lang_analyze.html),
  [Appropriate uses for SQLite](https://www.sqlite.org/whentouse.html), [Implementation limits](https://www.sqlite.org/limits.html),
  [der LIKE-Operator](https://www.sqlite.org/lang_expr.html#like).
- MySQL: [Unicode-Unterstützung (utf8mb4)](https://dev.mysql.com/doc/refman/8.4/en/charset-unicode-utf8mb4.html),
  [GRANT](https://dev.mysql.com/doc/refman/8.4/en/grant.html), [InnoDB](https://dev.mysql.com/doc/refman/8.4/en/innodb-storage-engine.html),
  [mysqldump](https://dev.mysql.com/doc/refman/8.4/en/mysqldump.html); MariaDB: [Zeichensätze](https://mariadb.com/docs/server/reference/data-types/string-data-types/character-sets).
- PostgreSQL: [CREATE DATABASE](https://www.postgresql.org/docs/current/sql-createdatabase.html),
  [pgcrypto](https://www.postgresql.org/docs/current/pgcrypto.html), [Sequenzen](https://www.postgresql.org/docs/current/sql-createsequence.html),
  [Client-Authentifizierung](https://www.postgresql.org/docs/current/client-authentication.html),
  [SET (einschließlich SET NAMES)](https://www.postgresql.org/docs/current/sql-set.html),
  [pg_dump](https://www.postgresql.org/docs/current/app-pgdump.html).
- MongoDB: [PHP-Bibliothek](https://www.mongodb.com/docs/php-library/current/), [Connection Strings](https://www.mongodb.com/docs/manual/reference/connection-string/),
  [db.createUser()](https://www.mongodb.com/docs/manual/reference/method/db.createuser/), [mongodump](https://www.mongodb.com/docs/database-tools/mongodump/).
- Oracle: [Database Resident Connection Pooling](https://docs.oracle.com/en/database/oracle/oracle-database/19/admin/managing-processes.html),
  [DBMS_CONNECTION_POOL](https://docs.oracle.com/en/database/oracle/oracle-database/19/arpls/DBMS_CONNECTION_POOL.html),
  PHP [oci8 connection handling](https://www.php.net/manual/en/oci8.connection.php) und
  [oci8-Konfiguration](https://www.php.net/manual/en/oci8.configuration.php).

[Inhalt](README.md) · Zurück: [8. Die Site ausliefern](08-die-website-ausliefern.md) · Weiter: [10. Nach der Installation](10-nach-der-installation.md)
