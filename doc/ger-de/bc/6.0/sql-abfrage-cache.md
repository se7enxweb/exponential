# Der SQL-Abfrage-Cache

Lesen Sie diese Seite, wenn Ihre Seiten bei einem vollständigen Rendering langsam sind (das erste Rendering nach einem
Leeren, Seiten angemeldeter Benutzer, die Administrationsoberfläche, Skripte) und Sie den SQL-Abfrage-Cache einschalten
möchten, oder wenn Sie Datenbanken wiederherstellen oder von außerhalb von Exponential in sie schreiben. Der
Abfrage-Cache beantwortet ein `SELECT` erneut aus dem Speicher, bis ein Schreibvorgang in eine der Tabellen, die es
gelesen hat, das Ergebnis veralten lässt. Er ist **standardmäßig aus** (`settings/querycache.ini`, `Mode=off`), daher
ändert ein Upgrade nichts, bis Sie ihn einschalten.

## Kurz gesagt

| | |
|---|---|
| Was sich geändert hat | Neue Klasse `eZDBQueryCache` (`lib/ezdb/classes/ezdbquerycache.php`), eingehängt in die Treiber für MySQL/MariaDB (`eZMySQLiDB`), PostgreSQL (`eZPostgreSQLDB`), SQLite (`eZSQLite3DB`) und Oracle (`eZOracleDB` der Erweiterung ezoracle, ab deren Hooks für den Abfrage-Cache). Neue Einstellungsdatei `querycache.ini`; neue Schaltflächen in Setup > Caches und ein Kasten in Setup > Systeminformationen. Der MongoDB-Treiber verwendet ihn nicht. |
| Wer betroffen ist | Niemand, bis `Mode` geändert wird. Ist er eingeschaltet: Jeder, der außerhalb von Exponential in die Datenbank schreibt (ein SQL-Client, eine Wiederherstellung, ein anderes Programm), muss danach den Abfrage-Cache leeren. Velocity muss nach dem Update, das die Klasse hinzufügt, neu gestartet werden. |
| Wie Sie es prüfen | `grep -n "^Mode" settings/querycache.ini` und Setup > Systeminformationen, Kasten **Datenbankabfragen** |
| Wie Sie es beheben | Folgen Sie [Einschalten](#einschalten). Nach einer Wiederherstellung: `php bin/php/ezcache.php --clear-id=querycache --allow-root-user`. |

Er ist das Gegenstück zum [HTTP-Cache](httpcache.md). Der HTTP-Cache erspart ganze Renderings
für die Seiten, die er speichern kann; der Abfrage-Cache beschleunigt **alles, was jener nicht kann**: das erste
Rendering nach einem Leeren, Seiten mit Query-Strings, Antworten auf POST, die Administrationsoberfläche, Seiten
angemeldeter Benutzer mit einem privaten Berechtigungskontext, Cronjobs und Skripte.

Unter Oracle wird außer dem, was nirgends gecacht wird (siehe [Was nie gecacht wird](#was-nie-gecacht-wird)), eine
Anweisung immer ausgeführt, die eine Sequenz liest (`<sequence>.NEXTVAL`, `.CURRVAL`: So liest der Treiber die ID
jeder neuen Zeile), die Uhr (`SYSDATE`, `SYSTIMESTAMP`), die SCN, `SYS_GUID`, `SYS_CONTEXT`/`USERENV` oder
`DBMS_RANDOM`, sowie alles, was den Katalog liest (`USER_*`, `ALL_*`, `DBA_*`, `V$*`). Ein anonymer PL/SQL-Block
(`DECLARE ...`, `BEGIN ... END;`) zählt als Schreibvorgang, dessen Tabellen sich nicht lesen lassen: Er lässt jedes
Ergebnis veralten.

### Sitzungseinstellungen (Oracle `ALTER SESSION`)

ezoracle führt bei jeder Verbindung ein `ALTER SESSION SET` aus (`initializeSession()`): das Zahlenformat
(`NLS_NUMERIC_CHARACTERS`), die Semantik der Zeichenlänge (`NLS_LENGTH_SEMANTICS`), `CURSOR_SHARING` und mit
`site.ini [DatabaseSettings] OracleCaseInsensitive=enabled` außerdem `NLS_COMP=LINGUISTIC` und `NLS_SORT`. Eine
solche Anweisung ist **kein Schreibvorgang**. Ihre Parameter werden Teil des Schlüssels jeder Anweisung dieser
Verbindung, sodass ein Ergebnis nur einer Verbindung geantwortet wird, deren Sitzung dieselben Einstellungen hat, und
jede Verbindung einer Installation, die dieselbe Anweisung ausführt, teilt die Ergebnisse über Anfragen hinweg (auch
mit `OracleCaseInsensitive`).

| `ALTER SESSION ...` | Was der Cache tut |
|---|---|
| `SET` nur von Parametern `NLS_*`, `CURSOR_SHARING`, `TIME_ZONE` | kein Schreibvorgang; die Werte gehen in den Schlüssel der Verbindung ein (Namen und Werte ohne Anführungszeichen in Großbuchstaben, der letzte Wert eines Parameters gewinnt) |
| `SET` von irgendetwas anderem, allein oder darunter gemischt (`CURRENT_SCHEMA`, `EDITION`, `CONTAINER`, `EVENTS` ...), jede andere Form (`ENABLE PARALLEL DML`), eine zweite Anweisung nach `;` | ein Schreibvorgang, dessen Tabellen sich nicht lesen lassen: jedes Ergebnis veraltet |

Die Sitzungsparameter werden pro Treiberobjekt gehalten (eine `WeakMap`), nicht pro Anfrage: Ein persistenter
Worker, der seine Verbindung behält, behält seine Sitzung. `eZDBQueryCache::sessionSettings( $sql )` gibt zurück, was
eine Anweisung setzt (oder null), `eZDBQueryCache::sessionKey( $db )`, was der Schlüssel einer Verbindung trägt (''
für MySQL, PostgreSQL, SQLite).

Die anderen Treiber setzen ihre Sitzung über die Client-Bibliothek (`mysqli_set_charset()`, der Connection-String),
nicht über `query()`; ein `SET ...` oder `PRAGMA ...`, das doch `query()` erreicht, ist für den Cache kein
Schreibvorgang und ändert den Schlüssel nicht.

## Was er bringt (gemessen auf einer Testinstallation, 2026-09-27)

Jede Seite führt bei jeder Anfrage dasselbe SQL aus, Wort für Wort: Über zwei Anfragen der Startseite hatten 405 ihrer
405 verschiedenen Anweisungen denselben Text, und ein Viertel (26 %) wiederholt sich sogar innerhalb einer Anfrage. Im
Modus `shared` sendet eine warme Seite **überhaupt keine Anweisung** an die Datenbank.

### Einfaches Apache (php-fpm), gerenderte Seiten

Dieselben Seiten direkt nacheinander mit `Mode=off` und `Mode=shared`, auf einer Testinstallation. Ein Query-String
hält den HTTP-Cache heraus, sodass jede Anfrage ein vollständiges Rendering ist. `ab -c 8`, 80 Anfragen anonym und 40
angemeldet; „allein“ ist eine Anfrage, während nichts anderes läuft (Median aus 5). Angemeldet ist eine Sitzung eines
Administrators.

| Seite | Besucher | Aus: Seiten/s | Shared: Seiten/s | Gewinn | Aus: allein | Shared: allein | SQL aus → shared |
|---|---|---|---|---|---|---|---|
| `/` (Startseite, Layouts) | anonym | 9,4–13,3 | 41,4–43,0 | **3,2–4,4×** | 485–608 ms | 164–177 ms | 545 → 0 |
| `/fitness` (Bereich) | anonym | 30,4–30,7 | 64,9–76,9 | **2,1–2,5×** | 294–299 ms | 101–104 ms | 273 → 0 |
| `/workout` (Bereich) | anonym | 28,5–28,8 | 60,3–68,9 | **2,1–2,4×** | 263–303 ms | 94–117 ms | 269 → 0 |
| `/` | angemeldet | 2,05 | 8,17 | **4,0×** | 582 ms | 191 ms | 544 → 0 |
| `/fitness` | angemeldet | 4,23 | 16,98 | **4,0×** | 263 ms | 111 ms | 272 → 0 |
| `/workout` | angemeldet | 4,60 | 18,61 | **4,0×** | 288 ms | 98 ms | 268 → 0 |

Kurz gesagt: Eine gerenderte Seite braucht **ein Drittel der Zeit** (Startseite allein ~0,6 s → ~0,17 s), und der
Server rendert **2- bis 4-mal so viele Seiten pro Sekunde**. Die Datenbank arbeitet für diese Seiten überhaupt nicht
mehr, was die CPU von MariaDB freigibt, die auf dieser Maschine vollständige Renderings begrenzte (0,2–0,3 s pro
Seite).

Hinweise zum Lesen der Tabelle:

- Die Bereiche sind zwei Läufe (2026-09-27, 02:3x und 02:4x); die Zeilen angemeldeter Benutzer einer.
- Angemeldet teilen sich acht gleichzeitige Anfragen **eine** Sitzung, und die Sitzungssperre von PHP führt sie
  nacheinander aus: p50 ist etwa achtmal „allein“ (aus 1,9 s, shared 0,45 s auf `/fitness`). Verschiedene Benutzer
  warten nicht aufeinander; „allein“ ist, was ein angemeldeter Besucher spürt.
- Was von einem Rendering bleibt, sind Templates und PHP. Für die Seiten, die der HTTP-Cache speichern kann, ist ein
  Treffer (380–520 Seiten/s unter Apache) immer noch weit schneller; der Abfrage-Cache macht die Seiten schneller, die
  jener nicht speichern kann: das erste Rendering nach einem Leeren, Query-Strings, POST-Anfragen, private Kontexte,
  die Administrationsoberfläche, Skripte.
- Ein kalter Cache (die erste Anfrage nach einem Leeren oder einem Schreibvorgang in die Tabellen, die eine Seite liest)
  führt jede Anweisung aus wie mit `Mode=off`, plus die Abfragen und das Speichern im Cache; dieser Mehraufwand wurde
  nicht gesondert gemessen.

### Korrektheit, geprüft auf einer Testinstallation

- Das gerenderte HTML ist mit ausgeschaltetem, kaltem und warmem Cache byte-genau gleich;
- ein Schreibvorgang aus einem CLI-Skript wird von den Webservern sofort gesehen (das nächste Rendering der Startseite
  führte die 125 Anweisungen erneut aus, die die beschriebene Tabelle lesen, und das darauffolgende keine);
- die Schreibvorgänge einer Transaktion zählen beim `COMMIT` und werden beim `ROLLBACK` vergessen;
- das Veröffentlichen eines Objekts in der Administrationsoberfläche zeigt den neuen Titel beim nächsten Lesen;
- temporäre Tabellen (die Liste verwandter Käufe des Shops, die Suche) werden nie gecacht.


## Einstellungen: `settings/querycache.ini`

Überschreiben Sie sie in `settings/override/querycache.ini.append.php` (oder in einem Siteaccess).

```ini
[QueryCacheSettings]
Mode=off          # off | request | shared
Storage=auto      # where shared keeps results: auto | apcu | file
ApcuReserve=25    # percent of APCu kept free for the pool's other users; 0 = off
MaxAge=300        # longest life of a result, in seconds
MaxRows=5000      # results with more rows are not kept
ExcludeTables[]
ExcludeTables[]=ezsession
ExcludeTables[]=ezpending_actions
ExcludeTables[]=ezcollab_notification_rule

[DebugSettings]
Report=enabled    # with the debug output on: accumulators, timing point, report (see "In the debug output")
```

| Modus | Was behalten wird | Wo |
|---|---|---|
| `off` | nichts; die Hooks kehren sofort zurück | — |
| `request` | ein Memo für eine Anfrage: Ein identisches `SELECT` wird darin nicht zweimal ausgeführt | PHP-Speicher |
| `shared` | das Memo und Ergebnisse über Anfragen hinweg | das Memo, dann APCu der Gruppe von Serverprozessen oder eine Datei pro Ergebnis (`Storage`) |

Die Wahl:

- `request` ist überall sicher und braucht nichts: Es entfernt die Wiederholungen innerhalb einer Anfrage (etwa ein
  Viertel der Anweisungen einer Seite mit Layouts).
- `shared` hält Ergebnisse in APCu (`apc.enabled=1`; für Skripte außerdem `apc.enable_cli=1`) oder in Dateien, wie
  `Storage` es sagt. Das ist der Modus, der die Anweisungen zwischen Anfragen entfernt.
- `Storage=auto` (der Standard) nimmt APCu, wo der PHP-Prozess es hat, und sonst Dateien, in jedem Prozess, der
  Webanfragen bedient (FPM, und die Worker von Exponential Velocity, deren SAPI `cli` ist); Cronjobs und Skripte ohne
  APCu behalten nur das Memo der Anfrage (ihre Dateien würden nur Festplattenverkehr hinzufügen); `Storage=apcu` nur
  APCu (ohne es arbeitet `shared` wie `request`, das Verhalten, bevor es Dateien gab); `Storage=file` immer Dateien,
  auch mit APCu. Dateien sind `var/<site>/cache/querycache/entries/<2>/<key>`, je ein serialisiertes Ergebnis (gelesen
  mit ausgeschaltetem `allowed_classes`), in eine temporäre Datei im selben Verzeichnis geschrieben und umbenannt
  (atomar, auch unter NFS). Eine Datei ist nur nach den Hex-Ziffern des Schlüssels benannt, sodass kein Name aus
  `entries/` hinausreichen kann. Verzeichnisse und Dateien sind unabhängig von der umask für die Gruppe beschreibbar,
  und ein root-Prozess (Velocity, ein als root ausgeführtes Skript) übergibt sie dem Besitzer des Zustandsverzeichnisses,
  sodass das FPM des Website-Benutzers sie lesen und ersetzen kann. Jeder Webserver, der `var/` einhängt, teilt sie
  (ein Cluster auf NFS oder einem SAN), während APCu pro Server ist. Ein Treffer kostet das Lesen einer Datei und
  `unserialize()`. Gemessen auf einer Testinstallation (PHP 8.4, APCu 5.1.28, Dateien auf einer lokalen Festplatte),
  die eigenen Kosten des Caches pro Anweisung:

  | Ergebnis | Treffer, APCu | Treffer, Datei | Fehlschlag und Speichern, APCu | Fehlschlag und Speichern, Datei |
  |---|---|---|---|---|
  | 1 Zeile | 0,02–0,03 ms | 0,05–0,08 ms | 0,02–0,04 ms | 0,17–0,23 ms |
  | 26 Zeilen | 0,03–0,05 ms | 0,07–0,09 ms | 0,04–0,05 ms | 0,18–0,21 ms |
  | 500 Zeilen | 0,31–0,42 ms | 0,35–0,41 ms | 0,39–0,42 ms | 0,47–0,63 ms |

  Ein Treffer aus einer Datei dauert bei kleinen Ergebnissen etwa doppelt so lange wie einer aus APCu und bei großen
  gleich lange, wo `unserialize()` den größten Teil der Kosten ausmacht; das Speichern einer Datei (temporäre Datei,
  Modus, Umbenennen) kostet bei kleinen Ergebnissen 0,15–0,2 ms mehr und bei 500 Zeilen 0,1–0,2 ms mehr. Beides liegt
  weit unter einer Anweisung auf der Datenbank (über den Oracle-Treiber auf dieser Installation: 0,2–0,3 ms für eine
  einfache Anweisung, etwa 60 ms für 500 sortierte Zeilen, gegenüber 0,05–0,1 ms und 0,4 ms für einen Treffer). Auf NFS
  oder einem SAN fügt jeder Dateizugriff die Latenz des Speichers hinzu; das wurde nicht gemessen.

  Der Cronjob-Teil `cache_cleanup` (`cronjobs/querycache_cleanup.php`) entfernt die Dateien, die älter als `MaxAge`
  sind oder von vor dem letzten Leeren stammen; das Leeren des Abfrage-Caches entfernt sie sofort. Planen Sie ihn mit
  `Storage=file` oder `auto` ohne APCu ein. Ohne ihn räumen die Anfragen die Dateien selbst auf: Eine Anfrage, die eine
  Datei gespeichert hat, führt an ihrem Ende dieselbe Bereinigung aus, höchstens alle 10 Minuten (oder `MaxAge`, wenn
  das länger ist) für die Installation, ein Prozess zur Zeit (`entries/.swept`, `entries/.sweep.lock`) und höchstens
  eine halbe Sekunde lang, sodass die Dateien nicht unbegrenzt wachsen können (`eZDBQueryCache::sweepFiles()`).
- `ApcuReserve` (Standard 25): APCu ist ein Speicher für jede Website des PHP-FPM-Pools (der HTTP-Cache, API-Schlüssel,
  die Ratenbegrenzungen eines Anfragefilters ...). Voll mit gültigen Einträgen leert APCu sich vollständig, und alles
  darin geht verloren, nicht nur die Ergebnisse des Abfrage-Caches (`apc.ttl` hilft nur, wenn es Einträge gibt, die so
  lange niemand gelesen hat). Ist dieser Anteil von APCu oder weniger frei, fügt der Modus shared APCu keine
  Ergebnisse mehr hinzu und behält sie nur für die Anfrage. Der freie Anteil kommt aus `apcu_sma_info()`, gelesen vor
  jedem Speichern (gemessen auf einer Testinstallation mit PHP 8.5: 0,17 bis 0,22 µs bei einem Segment von 64 MB oder
  1,2 GB und 20.000 Einträgen, gegenüber 0,6 µs für ein `apcu_fetch()`), sodass ein lang laufender Prozess ihn nicht
  überschreiten kann. APCu gibt einen abgelaufenen Eintrag nur frei, wenn er über seinen Schlüssel gelöscht wird oder
  eine Allokation fehlschlägt, und unter der Reserve alloziert der Cache nichts: Daher löscht ein Fehlschlag unter der
  Reserve den alten Eintrag seines Schlüssels, und einmal pro Minute löscht `eZDBQueryCache::purgeExpired()` für den
  ganzen Pool jeden abgelaufenen Eintrag des Abfrage-Caches aus der Eintragsliste von APCu, solange APCu höchstens
  30.000 Einträge hält und die Liste unter 80 % von `memory_limit` passt (sie kostet etwa 0,8 KB PHP-Speicher pro
  Eintrag, bei 130.000 etwa 200 ms und 100 MB). In einer Webanfrage läuft sie am Ende, einmal pro Anfrage: auch in
  einem Worker von Exponential Velocity, dessen Shutdown-Funktionen am Ende jeder Anfrage laufen; ein Skript (Cronjob,
  CLI) führt sie sofort aus. Sie liest die ganze Eintragsliste von APCu (ein `APCUIterator` führt abgelaufene Einträge
  nicht auf). Darüber hilft nur ein größeres `apc.shm_size`. Mit einem kleinen APCu (der Standard von `apc.shm_size`
  ist 32 MB) sind 25 % Reserve 8 MB, und ein anderer Benutzer, der drei Viertel davon belegt, hält den Abfrage-Cache aus
  APCu heraus; der Zähler zeigt es. Setup > Systeminformationen und der Tab Cache der Exp-Debug-Leiste zählen die
  zurückgehaltenen Ergebnisse; der Debug-Bericht führt sie als *Not cached: APCu reserve* auf. Gemessen mit einem APCu
  von 32 MB, das ein anderer Benutzer zu 85 % gefüllt hatte: Mit `ApcuReserve=10` speicherte der Cache 34 Ergebnisse
  und hielt den Rest zurück, die Einträge des anderen Benutzers blieben; mit `0` leerte APCu sich fünfmal, und die
  Einträge des anderen Benutzers gingen verloren. Mit abgelaufenen und nie wieder angefragten eigenen Einträgen des
  Caches (einmalige Anweisungen, 32 MB APCu bei 25 % frei) speicherte er eine Minute später wieder, als das Bereinigen
  sie freigegeben hatte. Dateien (`Storage=file`) haben keine Reserve.
- Mehrere Webserver auf einem `var/` über NFS: Ob ein Ergebnis aktuell ist, entscheidet `state.ser`, wie bei APCu.
  NFS-Clients cachen Dateiattribute und Verzeichnisabfragen (`acregmin`/`acdirmin` standardmäßig 3 bis 30 s), daher
  kann es so lange dauern, bis ein Schreibvorgang oder Leeren auf einem Server auf einem anderen gesehen wird. Hängen
  Sie `var/` mit `lookupcache=none` und `actimeo=0` (oder `noac`) ein, wo das wichtig ist; die Sperre der Zustandsdatei
  braucht NFSv4 oder `lockd`. Eine Abweichung der Uhren zwischen den Servern verschiebt nur, wann der Cronjob eine
  Datei entfernt, nie, was geantwortet wird.
- `MaxAge` begrenzt, was der Cache nicht sehen kann: einen Schreibvorgang außerhalb von Exponential (ein anderes
  Programm, ein SQL-Client, eine Wiederherstellung). Innerhalb von Exponential macht jeder Schreibvorgang sofort
  ungültig, egal wie groß `MaxAge` ist.
- `ExcludeTables` sind Tabellen, deren Lesevorgänge nie gecacht werden — Tabellen pro Besucher oder ständig
  beschriebene Tabellen, bei denen eine gecachte Antwort nie wiederverwendet würde.

Nach dem Ändern der Datei: Leeren Sie den INI-Cache (`php bin/php/ezcache.php --clear-tag=ini --allow-root-user`),
laden Sie PHP-FPM neu und starten Sie Velocity neu (`./console exp:velocity restart`).


## Wie er funktioniert

Klasse `eZDBQueryCache`, `lib/ezdb/classes/ezdbquerycache.php`. Jeder SQL-Treiber ruft sie an drei Stellen auf (eine
Abfrage trifft zuerst auf den gespeicherten Eintrag und verwendet die mit ihm gespeicherten Tabellen; der Text der
Anweisung wird nur bei einem Fehlschlag geparst, da das Parsen etwa ein Drittel der CPU einer gerenderten Seite
ausmachte):

- `arrayQuery()` fragt `lookup()`, bevor es eine Anweisung ausführt, und erhält die Zeilen oder ein Ticket;
- `arrayQuery()` übergibt die Zeilen nach einer Anweisung, die zurückgekehrt ist, an `store()`;
- `query()` teilt `noteWrite()` jede Anweisung mit, die erfolgreich war.

`eZDBInterface::commit()` und `rollback()` rufen `afterCommit()` und `afterRollback()` auf. `ezpKernelWeb` ruft zu
Beginn jeder Anfrage `resetRequest()` auf, sodass ein persistenter Worker (Velocity, Worker-Modus von FrankenPHP) jede
mit einem leeren Memo beginnt.

### Gültigkeit

Eine gemeinsame Zustandsdatei, `var/<site>/cache/querycache/state.ser`, enthält

```
{ generation, genTime, tables: { table => time of its last write } }
```

Jeder Prozess liest und schreibt sie: Apache/php-fpm, Velocity, Skripte und Cronjobs haben jeweils ein eigenes APCu,
daher kann der Zustand dort nicht liegen. Sie wird unter `flock` geschrieben und atomar ersetzt; ein Prozess liest sie
erneut, wenn seine Kopie älter als eine Sekunde ist.

Ein Eintrag wird mit dem Zeitpunkt gehalten, an dem seine Abfrage **begonnen** hat, den Tabellen, die sie gelesen hat,
und ihren Zeilen. Er wird nur geantwortet, solange

- er nach `genTime` (dem letzten vollständigen Leeren) erzeugt wurde,
- jede Tabelle, die er gelesen hat, zuletzt vor seiner Erzeugung beschrieben wurde, und
- er jünger als `MaxAge` ist.

Ein Lesevorgang, der mit einem Schreibvorgang um die Wette lief, ist daher immer veraltet, nie vertrauenswürdig.

### Was nie gecacht wird

- Lesevorgänge innerhalb einer Transaktion (sie können die eigenen Schreibvorgänge der Transaktion sehen);
- Anweisungen, deren Tabellen sich nicht aus dem Text lesen lassen (Unterabfragen in `FROM`, alles, was kein einfaches
  `SELECT` ist);
- Anweisungen, deren Ergebnis keine Funktion ihrer Tabellen ist: `NOW()`, `UNIX_TIMESTAMP()` ohne Argument,
  `CURRENT_DATE` und Verwandte, `RAND()`, `UUID()`, `LAST_INSERT_ID()`, `FOUND_ROWS()`, Sperren (`FOR UPDATE`,
  `LOCK IN SHARE MODE`, `GET_LOCK()`), Variablen (`@x`, `@@x`), `SQL_NO_CACHE`, `INTO OUTFILE`;
- Lesevorgänge temporärer Tabellen: der in diesem Prozess mit `CREATE TEMPORARY TABLE` angelegten und der so benannten,
  wie `eZDBInterface::generateUniqueTempTableName()` sie benennt (`ezsearch_tmp_12`, `ezproductcoll_tmp_972`,
  `eznode_count_3`). Eine temporäre Tabelle gehört zu einer Verbindung, und ihr Name wird wiederverwendet, daher dürfen
  die Zeilen einer anderen Anfrage nie für sie antworten. Schreibvorgänge in temporäre Tabellen machen nichts ungültig
  und bleiben aus der Zustandsdatei heraus;
- Ergebnisse mit mehr als `MaxRows` Zeilen;
- Lesevorgänge der `ExcludeTables`.

### Schreibvorgänge

`INSERT`, `REPLACE`, `UPDATE` (auch über mehrere Tabellen), `DELETE` (mit `JOIN`s), `TRUNCATE`,
`CREATE`/`ALTER`/`DROP TABLE`, `CREATE`/`DROP INDEX` und `RENAME TABLE` kennzeichnen die Tabellen, die sie nennen,
als jetzt beschrieben. Ein Schreibvorgang, dessen Tabellen sich nicht aus dem Text lesen lassen, lässt **alles**
veralten (eine Erhöhung der Generation), was immer sicher ist. Innerhalb einer Transaktion werden die Tabellen
gesammelt und beim äußersten `COMMIT` gekennzeichnet; bei `ROLLBACK` werden sie verworfen.

### Schlüssel

`xxh128` (wo PHP es hat; sonst `md5`) aus der Treiberklasse, dem Datenbanknamen, dem Server, dem Text der Anweisung und
den Parametern von `arrayQuery()` (Offset, Limit, Spalte). In APCu sind die Einträge `ezqc:<key>`, mit `MaxAge` als
TTL, sodass APCu sie bei Speicherdruck verdrängen kann. Die Zähler sind `ezqcstat:*` (nur in APCu: Mit der
Dateispeicherung und ohne APCu gibt es keine Zähler). Als Dateien sind die Einträge
`entries/<first two characters of the key>/<key>`; eine Abfrage prüft ihr Alter und den Zustand wie bei einem
APCu-Eintrag, der Cronjob entfernt die alten.


## In der Administrationsoberfläche

### Setup > Caches

Zwei Zeilen oben, mit den anderen meistgenutzten Schaltflächen:

- **Query-Cache leeren** (`ClearQueryCacheButton`) — eine Erhöhung der Generation: Jedes gespeicherte Ergebnis ist
  sofort veraltet, auf jedem Server, der `var/` teilt, ohne APCu zu durchlaufen. Die Zeile zeigt den Modus.
- **HTTP-Cache leeren** (`ClearHttpCacheButton`) — verwirft jede gecachte Seite für jeden Berechtigungskontext,
  einschließlich der Kopien von Velocity. Ausgegraut, wenn der HTTP-Cache aus ist.

Der Abfrage-Cache steht außerdem unten in der Liste der Caches (`querycache`, Tags `content` und `ini`), daher leeren
ihn auch **Den kompletten Cache leeren**, **Cache des Inhalts leeren**, `ezcache.php --clear-all`,
`--clear-tag=content` und `--clear-id=querycache`.

### Setup > Systeminformationen

Der Kasten **Datenbankabfragen** zeigt für den Server, der die Seite beantwortet:

- den Modus, `MaxAge`, `MaxRows`, `ExcludeTables`;
- im Modus `shared`, wie viele Ergebnisse dieser Server in APCu hält und wie viel Speicher sie belegen, oder mit der
  Dateispeicherung, wie viele Ergebnisdateien es gibt, ihre Größe auf der Festplatte und ihr Verzeichnis;
- die Generation, wann der Cache zuletzt geleert wurde und wie viele Tabellen die Zustandsdatei verfolgt;
- die Zähler dieses Servers seit ihrem letzten Zurücksetzen: Anfragen, Treffer, Fehlschläge, Trefferquote, Anweisungen,
  die nicht gecacht werden konnten, Schreibvorgänge;
- die zuletzt beschriebenen Tabellen und wie lange das her ist;
- **Query-Cache leeren** und **Die Zähler zurücksetzen**.

Zähler und APCu-Einträge gelten **pro Server**: Apache/php-fpm und Velocity haben jeweils ihr eigenes APCu, daher
unterscheiden sich die Zahlen zwischen der von Apache und der von Velocity (Port 8080) ausgelieferten Administration.
Der Zustand (Generation, Tabellen) wird geteilt.

Darunter das SQL-Profil (nächster Abschnitt) und sein Schalter.


## In der Debug-Ausgabe

Mit eingeschalteter Debug-Ausgabe (`site.ini [DebugSettings] DebugOutput=enabled`) und
`querycache.ini [DebugSettings] Report=enabled` (der Standard) zeigt jede Anfrage, was der Cache für sie getan hat.
`Report=disabled` hält alle drei folgenden Teile aus der Debug-Ausgabe heraus; der Cache selbst arbeitet genauso.

**Zeitakkumulatoren**, Gruppe *Query cache* (die Zeit ist die der eigenen Arbeit des Caches, nicht die der Datenbank):

| Akkumulator | Zählt | Zeit |
|---|---|---|
| *Hits* | Anweisungen, die aus dem Cache beantwortet wurden; sie erreichen den Treiber nie und fehlen daher in dessen Akkumulator (z. B. *Oracle_queries*, *mysqli_query*) | die Abfrage, die geantwortet hat |
| *Misses (lookup only)* | cachebare Anweisungen, die nicht im Cache waren (oder veraltet) und an die Datenbank gingen | nur die Abfrage; die Zeit der Datenbank bleibt beim Akkumulator des Treibers |
| *Stores* | nach einem Fehlschlag behaltene Ergebnisse | das Kopieren der Zeilen in das Memo (und APCu) |
| *Not cached* | Anweisungen, die weder beantwortet noch gespeichert wurden, aus einem der Gründe, die der Bericht aufführt | die Abfrage |

**Zeitpunkt** *Query cache: state read (generation n)*: wo die Anfrage (oder, nach `eZDebug::reset()`, der Job) zum
ersten Mal auf die gemeinsame Zustandsdatei schaut; *n* ist die gefundene Generation, die Zahl, die jedes Leeren und
jeder Schreibvorgang unbekannter Tabellen erhöht. Nach `eZDebug::reset()` kommt der Zeitpunkt sofort mit der nächsten
Anweisung, wenn der Prozess den Zustand bereits hält (die Anweisungen des Jobs lesen ihn vielleicht nie wieder), sonst
mit dem ersten Blick darauf. Der Bericht selbst liest keinen Zustand und setzt keinen Zeitpunkt.

**Bericht** unten, *Query cache* (Text und HTML, jeder Wert maskiert):

| Zeile | Was sie bedeutet |
|---|---|
| Mode | `off`, `request` oder `shared`; im Modus `shared` mit seiner Speicherung, `shared (apcu)` oder `shared (file)`, oder `shared (no APCu: works as request)`, wo er keine hat |
| Generation | die Generation des Zustands, wie diese Anfrage sie zuletzt gelesen hat; *not read*, wenn noch keine Anweisung auf den Zustand geschaut hat |
| Hits, Misses, Hit rate | wie die Akkumulatoren; die Quote ist Treffer von Treffern plus Fehlschlägen |
| Stored | gespeicherte Ergebnisse |
| Not cached | Anweisungen, die nicht beantwortet und nicht gespeichert wurden, dann eine Zeile *Not cached: reason* pro Grund: *inside a transaction*, *statement (volatile or tables not readable)*, *excluded table* (`ExcludeTables`), *temporary table*, *catalogue table*, *more rows than MaxRows* (dieser zählt außerdem als Fehlschlag) |
| Writes | Schreibvorgänge, die der Cache gesehen hat (jeder lässt die Tabellen veralten, die er nennt) |
| Database time saved (estimated) | die Summe der Zeit, die jede Anweisung bei ihrem Speichern der Datenbank samt Abholen ihrer Zeilen gekostet hat, über die Treffer; eine obere Schätzung. Einträge, die gespeichert wurden, bevor das festgehalten wurde, fügen „+ n hits on entries without a duration“ hinzu. Dann ihr Anteil an der Seite: `186 ms = 186 % of the page so far (100 ms; without the cache about 286 ms, 3 times as long)`; der Faktor erst ab 100 %, wo ein Anteil allein wie ein Teil der Seite gelesen würde. Der Chip der Exp-Debug-Leiste gibt denselben Anteil an, `QC 83 % · -186 ms (186 %)`, und denselben Satz in seinem Titel |
| Made everything stale | bis zu 20 Schreibvorgänge, deren Tabellen sich nicht lesen lassen (ein PL/SQL-Block, `CALL`, ein `ALTER SESSION`, das keine [Sitzungseinstellung](#sitzungseinstellungen-oracle-alter-session) ist ...), dann „... and n more“ |

Der Bericht zählt, was seit seinem Beginn geschehen ist: ab der ersten Anweisung der Anfrage mit eingeschalteter
Debug-Ausgabe oder erneut ab der ersten Anweisung nach `eZDebug::reset()`. Seine Zähler sind seine eigenen, daher
ändert es sie nicht, wenn Setup > Systeminformationen die Zähler der Anfrage schreibt (`eZDBQueryCache::flushStats()`).
*Not cached* ist größer als der Zähler *uncacheable* von Setup > Systeminformationen, der ausgeschlossene Tabellen und
Transaktionen weglässt.

**Die Zeit der Seite für den Anteil** (`eZDBQueryCache::requestMs()`, eine Zeitbasis für die Berichtszeile und den
Chip): vom Skriptstart von eZDebug, den der Web-Kernel zu Beginn jeder Anfrage setzt, auch in einem persistenten
Worker von Exponential Velocity (sodass frühere Anfragen eines Workers nie zählen), sonst ab `REQUEST_TIME_FLOAT` von
PHP (Skripte; Velocity setzt es ebenfalls für jede Anfrage); bis dorthin, wo die Ausgabe der Exp-Debug-Leiste beginnt
(`setRequestEnd()`, von `resetRequest()` gelöscht), sonst bis zum Skriptstopp von eZDebug, sonst bis jetzt. Ein Start,
der eine negative Zeit oder mehr als einen Tag ergäbe, wird nicht verwendet (kein Anteil wird angezeigt): Er kann nur
von einer früheren Anfrage übrig sein. Der Zeit-Chip der Leiste misst bis zur Erstellung ihrer Zusammenfassung, ein
paar Millisekunden später.

**Literale maskiert.** In einer Anweisung unter *Made everything stale* ist jedes String-Literal durch `'?'` ersetzt,
sodass Werte aus Anfragen und Inhalten aus der Debug-Ausgabe herausbleiben (`eZDBQueryCache::maskLiterals()`). Wo ein
Literal endet, hängt von der Datenbank ab: Die Anführungszeichen nach SQL-Standard verdoppeln ein Anführungszeichen
(`'it''s'`: Oracle, PostgreSQL, SQLite), MySQL maskiert außerdem mit einem Backslash (`'it\'s'`, was sein
`escapeString()` schreibt), und `'C:\'` ist in Standard-SQL ein ganzes Literal, in MySQL aber nicht. Beide Lesarten
werden maskiert, und ein nicht abgeschlossenes Anführungszeichen wird bis zum Ende maskiert, sodass unter keiner Lesart
ein Teil eines Literals sichtbar wird; wo sie sich widersprechen, wird mehr als das Literal maskiert
(`CALL p( 'it\'s', 1 )` erscheint als `CALL p( '?'`). Nur die ersten 4 KB einer Anweisung werden gelesen, und die Zeile
wird bei 200 Zeichen abgeschnitten. Text in doppelten Anführungszeichen (ein Bezeichner, oder ein String in MySQL ohne
`ANSI_QUOTES`) und Zahlen werden unverändert angezeigt.

**Persistente Worker** (Exponential Velocity): `resetRequest()`, das der Web-Kernel für jede Anfrage ausführt, verwirft
den Bericht, sodass nichts von einer Anfrage in die nächste getragen wird; nach `eZDebug::reset()` registriert die
nächste Anweisung die Akkumulatorgruppe und den Bericht erneut, beginnt neue Zähler und setzt den Zeitpunkt erneut.

**Kosten.** Mit ausgeschalteter Debug-Ausgabe prüft der Cache pro Anweisung nur, ob sie eingeschaltet ist (kein
Aufruf, gemessen im Rauschen eines Abfragetreffers von ~2,7 µs); die mit jedem Eintrag gespeicherte Dauer ist ein
`microtime()` pro Speichern. Eingeschaltet entstehen die Kosten der Akkumulatoren.

In der **Exp-Debug-Leiste** sind dieselben Zahlen ein Chip in der angehefteten Leiste (Trefferquote und gesparte Zeit,
die gesparte Zeit auch in Prozent der Zeit der Seite: `QC 83 % · -186 ms (186 %)` auf einer Seite von 100 ms bedeutet
etwa 286 ms ohne den Cache; gelb unter 50 %, rot unter 10 % oder im Modus `shared` ohne Speicherung,
`debugbar.ini Thresholds[querycache_hit_rate]`), und der Bericht steht oben im Tab SQL, neben den Akkumulatoren der
Datenbank. Der **Tab Cache** (setup/managecache) zeigt, was dieser Server hält: Einträge und Speicher in APCu (oder die
Ergebnisdateien und ihre Größe auf der Festplatte; Dateien zählen keine Treffer), die Zähler seit dem letzten
Zurücksetzen, den Zustand, die gecachten Ergebnisse nach den Tabellen gruppiert, die sie gelesen haben (Einträge,
Zeilen, Größe, Treffer, wie APCu sie zählt), und die größten Einträge, mit Schaltflächen zum Leeren des Abfrage-Caches
und zum Zurücksetzen der Zähler.

## Der SQL-Profiler

`eZDBInterface::profileSQL()`, vom MySQL-Treiber nach jeder Anweisung aufgerufen. Aus, sofern `var/tmp/sql_profile.on`
nicht existiert — ein `file_exists()` pro Anfrage. Der Schalter in Setup > Systeminformationen legt diese Datei an und
entfernt sie. Eingeschaltet geht pro Anfrage eine Zeile nach `var/tmp/sql_profile.log`: Anweisungen, `SELECT`s,
verschiedene `SELECT`s, exakte Wiederholungen, Zeit in der Datenbank und in den Wiederholungen.

Er zählt die Anweisungen, die **die Datenbank erreicht haben**: Bei eingeschaltetem Abfrage-Cache ist eine gecachte
Antwort nicht darin. Das macht ihn zum Werkzeug, um zu sehen, was der Cache übrig lässt: Eine warme Seite mit
Shared-Cache zeigt 0 Anweisungen.

Zwei weitere Markierungsdateien, für Untersuchungen: `var/tmp/sql_profile.shapes` fügt dem Log die am häufigsten
wiederholten Anweisungen hinzu (Werte maskiert), und `var/tmp/sql_profile.hashes` schreibt die Hashes der Anweisungen
jeder Anfrage in eine eigene Datei, um zwei Anfragen zu vergleichen. Entfernen Sie sie, wenn Sie fertig sind; sie
schreiben bei jeder Anfrage.


## Support und Wartung

### Einschalten

1. Prüfen Sie APCu: Setup > Systeminformationen, Abschnitt PHP, oder `php -m | grep apcu` (und das
   `php_admin_value[apc.enabled]` des FPM-Pools).
2. Setzen Sie `Mode=request` in `settings/override/querycache.ini.append.php`; leeren Sie den INI-Cache, laden Sie FPM
   neu, starten Sie Velocity neu. Arbeiten Sie einen Tag lang damit.
3. Setzen Sie auf dieselbe Weise `Mode=shared`. Beobachten Sie die Trefferquote in Setup > Systeminformationen.

### Ausschalten

`Mode=off`, dann dasselbe Leeren der INI, Neuladen und Neustarten. Nichts muss gelöscht werden: Veraltete
APCu-Einträge laufen innerhalb von `MaxAge` ab und werden nie gelesen, solange der Modus aus ist.

### Was normal ist

- Eine Trefferquote von 30–40 % direkt nach einem Leeren oder Neustart, die steigt, während Seiten besucht werden. Auf
  einer ruhigen Website mit warmen Seiten sind die meisten Anweisungen Treffer.
- Ein Schub von Schreibvorgängen nach einem Leeren des Caches oder einem Neustart von Velocity: Das Vorwärmen rendert
  Seiten, und manche Renderings schreiben (beim ersten Anzeigen erzeugte Bildaliase aktualisieren
  `ezcontentobject_attribute`).
- `state.ser` ein paar KB; es führt jede jemals beschriebene Tabelle auf, mit je einem Zeitstempel.

### Worauf Sie achten

| Symptom | Ursache und Lösung |
|---|---|
| Eine Seite zeigt nach einer Änderung mit einem SQL-Client, `mysql < dump.sql` oder einem anderen Programm alte Daten | Der Cache kann Schreibvorgänge außerhalb von Exponential nicht sehen. Leeren Sie ihn (Setup > Caches, oder `php bin/php/ezcache.php --clear-id=querycache --allow-root-user`), oder warten Sie `MaxAge`. Tun Sie das nach jeder Wiederherstellung. |
| `Class "eZDBQueryCache" not found` in `var/vc/qbix/log/error.log`, Admin-Seiten mit 500 nur unter Velocity | Velocity hat das Autoload-Array des Kernels geladen, bevor die Klasse existierte. Starten Sie es neu: `./console exp:velocity restart --allow-root-user`. Jedes Update, das Kernel-Klassen hinzufügt, braucht das. |
| Die Trefferquote bleibt niedrig | Eine Tabelle, die die Seiten lesen, wird bei jeder Anfrage beschrieben. Setup > Systeminformationen führt die zuletzt beschriebenen Tabellen auf; finden Sie heraus, wer schreibt (meist ein Aufrufzähler, eine sessionartige Tabelle oder ein defekter Bildalias, der bei jeder Ansicht neu erzeugt wird), und beheben Sie es, oder fügen Sie die Tabelle zu `ExcludeTables` hinzu. |
| „APCu is not available to this server“ im Kasten | `Storage=apcu` ohne APCu: `shared` fällt auf `request` zurück. Schalten Sie APCu für dieses PHP ein (FPM-Pool, oder `apc.enable_cli=1` für Velocity und Skripte), oder setzen Sie `Storage=auto` oder `file`. |
| Viele Dateien in `var/<site>/cache/querycache/entries/` | Der Cronjob-Teil `cache_cleanup` läuft nicht. Planen Sie `php runcronjobs.php -s <siteaccess> cache_cleanup` ein; ein Leeren des Abfrage-Caches entfernt sie ebenfalls. |
| APCu-Speicher voll, andere Caches verdrängt | Halten Sie `ApcuReserve` über 0, senken Sie `MaxRows` oder `MaxAge`, oder erhöhen Sie `apc.shm_size`. Setup > Systeminformationen zeigt, was der Abfrage-Cache hält. |
| „results not kept in APCu“ im Kasten oder im Tab Cache | APCu hatte nicht mehr als `ApcuReserve` Prozent frei. Erhöhen Sie `apc.shm_size`, oder finden Sie die anderen Benutzer von APCu (Setup > Systeminformationen, Abschnitt PHP). |
| `state.ser` nicht beschreibbar (Warnungen im Log) | `var/<site>/cache/querycache/` muss für jeden Benutzer beschreibbar sein, unter dem die Website läuft. Von root geschriebene Dateien werden dem Besitzer des Verzeichnisses übergeben. |
| Ein Ergebnis sieht falsch aus, und Sie verdächtigen den Cache | Setzen Sie `Mode=off` und vergleichen Sie. Ist die Ausgabe dieselbe, ist es nicht der Cache. |

### Tests

**Unit-Tests, ohne Datenbank:** `tests/tests/lib/ezdb/eZDBQueryCacheTest.php` (`@group querycache`, in der Suite
`lib`). Der Treiber ist ein Ersatzobjekt, und der Zustand liegt in einem eigenen Verzeichnis unter `var/tmp`
(`eZDBQueryCache::setStateDir()`), sodass die Tests den Cache der Website nie berühren.

| | Was er beweist |
|---|---|
| QC-01 | Die Tabellen, die ein `SELECT` liest (Joins, Aliase, `db.table`, Namen in Anführungszeichen, Tabellennamen innerhalb von Strings ignoriert); nicht cachebar: kein einfaches `SELECT`, volatile Funktionen, Sperren, `SQL_CALC_FOUND_ROWS`, eine Unterabfrage in `FROM`, Variablen |
| QC-02 | Die Tabellen, die ein Schreibvorgang nennt, für jedes Verb (`INSERT`, `INSERT IGNORE`, `REPLACE`, `UPDATE` über mehrere Tabellen, `DELETE` mit `JOIN`, `TRUNCATE`, `CREATE`/`ALTER`/`DROP TABLE`, `CREATE INDEX`, `RENAME TABLE`); ein unlesbarer Schreibvorgang ist null |
| QC-03 | Ein Fehlschlag, ein Speichern, dann ein Treffer mit denselben Zeilen, und die Zähler |
| QC-04 | Ein Schreibvorgang in eine Tabelle, die das Ergebnis gelesen hat, lässt es veralten; einer in eine andere Tabelle nicht |
| QC-05 | Ein unlesbarer Schreibvorgang und ein Leeren lassen alles veralten; ein Leeren erhöht die Generation |
| QC-06 | In einer Transaktion werden Lesevorgänge weder beantwortet noch gespeichert; Schreibvorgänge zählen beim `COMMIT` und werden bei `ROLLBACK` vergessen |
| QC-07 | Temporäre Tabellen, nach Name und nach `CREATE TEMPORARY TABLE`: nie gecacht, nie im Zustand |
| QC-08 | `ExcludeTables`, `MaxRows`, `MaxAge` |
| QC-09 | Schlüssel unterscheiden sich nach Anweisung, Parametern von `arrayQuery()`, Datenbank und Server |
| QC-10 | Ein gespeichertes Ergebnis ist eine Kopie: Was ein Aufrufer ändert, ändert nichts |
| QC-11 | `Mode=off` speichert nichts, schreibt keinen Zustand und zählt nichts |
| QC-12 | Ein Schreibvorgang eines anderen Prozesses, über die Zustandsdatei gesehen, lässt ein Ergebnis innerhalb einer Sekunde veralten, selbst eines, das noch im Memo der Anfrage ist; andere Tabellen bleiben aktuell |
| QC-13 | Ein gespeichertes Ergebnis wird beantwortet, ohne die Anweisung erneut zu parsen, aber nicht mehr, sobald eine Tabelle, die es gelesen hat, ausgeschlossen wurde |
| QC-14 | Der eigene Katalog der Datenbank (`sqlite_master`, `sqlite_stat1`, `pg_*`, `information_schema`) wird nie gecacht |
| QC-15 | Oracle: Sequenzen, die Uhr, die SCN, `SYS_GUID`, `SYS_CONTEXT`, `DBMS_RANDOM` und der Katalog werden nie gecacht; ein PL/SQL-Block lässt alles veralten, ein bloßes `BEGIN` nicht |
| QC-16 | Oracle: Ein `ALTER SESSION SET` von `NLS_*`, `CURSOR_SHARING`, `TIME_ZONE` (die Anweisung, die ezoracle beim Verbinden ausführt, mit oder ohne `OracleCaseInsensitive`) schreibt nichts; jedes andere `ALTER SESSION` (`CURRENT_SCHEMA`, `CONTAINER`, `EVENTS`, `ENABLE ...`, eine zweite Anweisung) lässt alles veralten |
| QC-17 | Mit eingeschalteter Debug-Ausgabe: die Akkumulatoren der Gruppe *Query cache*, ein Zeitpunkt, der Bericht unten als Text und HTML (maskiert) mit Treffern, Fehlschlägen, Trefferquote, Gründen und den Schreibvorgängen, die alles veralten ließen |
| QC-18 | Ohne Debug-Ausgabe oder mit `Report=disabled` kein Akkumulator, kein Bericht |
| QC-19 | Oracle: Ein Ergebnis wird den nächsten Verbindungen mit denselben Sitzungseinstellungen geantwortet, und das Verbinden erhöht keine Generation, auch mit `NLS_COMP`/`NLS_SORT` |
| QC-20 | Oracle: Ein Ergebnis wird nie einer Sitzung mit anderen Einstellungen geantwortet (`NLS_SORT=GERMAN` gegenüber `BINARY_CI`); ein späteres `ALTER SESSION` gibt der Verbindung ihren neuen Schlüssel; mit `Mode=off` wird nichts vermerkt |
| QC-21 | Literale werden unter beiden Regeln für Anführungszeichen maskiert (`''`, `\'`, `'C:\'`, nicht abgeschlossen), und kein Teil davon erreicht den Bericht, auch aus einer Anweisung, die länger als 4 KB ist |
| QC-22 | Nach `eZDebug::reset()` im selben Prozess beginnen der Bericht, seine eigenen Zähler und der Zeitpunkt erneut; `flushStats()` lässt sie in Ruhe; die nächste Anfrage trägt nichts mit; bei ausgeschalteter Debug-Ausgabe wird nichts registriert |
| QC-23 | `debugSnapshot()`: die Zahlen des Berichts (seine eigenen Zähler) mit eingeschalteter Debug-Ausgabe, null ohne |
| QC-24 | `inventory()` (mit APCu): Einträge nach Tabelle gruppiert, ein Eintrag zweier Tabellen für jede gezählt, Treffer aus APCu, die größten zuerst, kein Text von Anweisungen |
| QC-25 | `storage()`: `Storage=file` auch mit APCu, `auto` APCu (auf der Kommandozeile ohne APCu keine), `apcu` ohne APCu keine, Modus `request` keine |
| QC-26 | Die Dateispeicherung: eine Datei pro Ergebnis unter ihrem Verzeichnis aus zwei Zeichen, ein Treffer in der nächsten Anfrage, veraltet nach einem Schreibvorgang in ihre Tabelle |
| QC-27 | Eine Ergebnisdatei erzeugt nie Objekte (`__PHP_Incomplete_Class`), eine defekte Datei ist ein Fehlschlag |
| QC-28 | `cleanupFiles()` entfernt Dateien, die älter als `MaxAge` sind, und übrig gebliebene temporäre Dateien und behält die anderen; `status()` und `inventory()` zählen die Dateien (keine Treffer); ein Leeren entfernt sie |
| QC-29 | Eine Ergebnisdatei wird immer nur nach einem Schlüssel (Hex-Ziffern) benannt: `../`, Großbuchstaben, ein leerer oder ein NUL enthaltender Name schreiben und lesen nichts |
| QC-30 | Die Anfragen räumen die Ergebnisdateien ohne den Cronjob auf: abgelaufene entfernt, höchstens einmal pro Intervall, ein Prozess zur Zeit, innerhalb eines Zeitbudgets |
| QC-31 | `Storage=auto` verwendet die Dateien in jedem Webprozess ohne APCu, einschließlich eines Velocity-Workers (SAPI cli), und keine in einem Skript |
| QC-32 | `apcuRoom()`: Platz nur bei mehr als `ApcuReserve` Prozent frei, `0` hält nie an, standardmäßig 25 |
| QC-33 | Unter der Reserve bleibt ein Ergebnis in der Anfrage, wird gezählt (`reserve`) und nicht in APCu gelegt; mit wieder freiem Platz doch; Dateien haben keine Reserve |
| QC-34 | `purgeExpired()` löscht die abgelaufenen APCu-Einträge des Abfrage-Caches und gibt ihren Speicher frei, behält lebende Einträge und die anderer Benutzer, läuft einmal pro Minute für den Pool, und `apcuRoom()` startet es unter der Reserve |
| QC-35 | In einer Webanfrage, einschließlich eines Velocity-Workers, läuft das Bereinigen am Ende der Anfrage und wird einmal registriert; ein Skript führt es sofort aus |
| QC-36 | Eine Zeitbasis für den gesparten Anteil: der Skriptstart (ohne ihn `REQUEST_TIME_FLOAT`) bis zu dem Ende, das die Leiste setzt; ein veralteter oder späterer Start ergibt keinen Anteil; eine neue Anfrage misst wieder bis jetzt |
| QC-37 | Die Berichtszeile sagt, wie viel länger die Seite ohne den Cache gedauert hätte, ab einem Anteil von 100 %, und enthält kein Inline-CSS |

```bash
php vendor/bin/phpunit --testsuite lib --filter eZDBQueryCacheTest
# OK (37 tests; QC-24, QC-33, QC-34 and QC-35 need apc.enable_cli=1), about 7 s (QC-08, QC-12, QC-28 and QC-34 wait)
```

**Auf einer laufenden Website** (MySQL, die echten Treiber), jeweils geprüft auf einer Testinstallation am 2026-09-27:

- die Hooks der Treiber auf der Live-Datenbank: temporäre Tabellen nie gecacht und ihre Schreibvorgänge aus dem Zustand
  herausgehalten, eine normale Tabelle gecacht;
- Transaktionen über `eZDB::begin()`/`commit()`/`rollback()`;
- ein Schreibvorgang über die CLI, der ungültig macht, was die Webserver halten;
- die gerenderte Seite byte-genau gleich mit ausgeschaltetem, kaltem und warmem Cache;
- die Zeittabelle oben: dieselben Seiten, `Mode=off`, dann `Mode=shared`, anonym und angemeldet, mit den Anweisungen pro
  Seite aus dem SQL-Profiler.

Um die Zeitmessung auf einer anderen Installation zu wiederholen: Schalten Sie den Profiler ein (Setup >
Systeminformationen), und setzen Sie für jeden Modus diesen in `settings/override/querycache.ini.append.php`, leeren Sie
den INI-Cache und laden Sie PHP-FPM neu, fragen Sie jede Seite dreimal an, um sie zu wärmen, und messen Sie sie dann
mit `ab -n 80 -c 8 'https://<host>/<page>?bench=1'` (der Query-String hält den HTTP-Cache heraus). Die Zeile des
Profilers für eine Testanfrage nennt die Anweisungen; keine Zeile bedeutet, dass keine die Datenbank erreicht hat.

### Dateien

| Datei | Was sie ist |
|---|---|
| `lib/ezdb/classes/ezdbquerycache.php` | der Cache |
| `lib/ezdb/classes/ezmysqlidb.php`, `ezpostgresqldb.php`, `ezsqlite3db.php` | die Hooks der Treiber |
| `lib/ezdb/classes/ezdbinterface.php` | Hooks für `COMMIT`/`ROLLBACK`, der SQL-Profiler |
| `kernel/private/classes/ezpkernelweb.php` | `resetRequest()` pro Anfrage |
| `kernel/classes/ezcache.php` | der Eintrag `querycache` in der Liste der Caches |
| `kernel/setup/cache.php`, `design/admin/templates/setup/cache.tpl` | Setup > Caches |
| `kernel/setup/info.php`, `design/admin/templates/setup/info.tpl` | Setup > Systeminformationen |
| `cronjobs/querycache_cleanup.php`, `kernel/private/classes/cronjobs/querycache_cleanup.php` | entfernt alte Ergebnisdateien (Teil `cache_cleanup`) |
| `settings/querycache.ini` | Einstellungen |
| `var/<site>/cache/querycache/state.ser` | der gemeinsame Zustand |
| `var/<site>/cache/querycache/entries/` | die Ergebnisdateien der Dateispeicherung |

## Verwandte Seiten

- [Verhaltensänderungen, 16. bis 30. September 2026: SQL-Abfrage-Cache](../../../eng-us/bc/6.0/behaviour-changes-2026-09b.md#sql-query-cache-querycacheini) (Abfrage vor dem Parsen, Schlüssel mit `xxh128`, Katalog nie gecacht)
- [HTTP-Cache](httpcache.md)
- [Velocity](../../../eng-us/features/6.0/velocity-persistent-worker-server.md) und der [Antwort-Cache von Velocity](../../../eng-us/features/6.0/velocity-response-cache.md)
- [Cache-Leerungen, die Verzeichnisse beiseiteschieben](../../../eng-us/features/6.0/cache-clear-rename-aside.md)
- [Chronik, 16. bis 30. September 2026](../../../eng-us/history/2026/2026-09b.md)
- [Changelog 6.0.15](../../../changelogs/6.0/6.0.15.md)
