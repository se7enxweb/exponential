# Lesereplikate: PostgreSQL, MySQL und MariaDB, Oracle (6.0.15)

Lesen Sie diese Seite, wenn die Seiten einer Website von einem Replikat nahe am Webserver lesen sollen, etwa von einem
Hot Standby auf derselben Maschine wie ein Knoten eines Clusters, während jeder Schreibzugriff weiter an den
Primärserver geht. Dieselbe Regel gilt für PostgreSQL, MySQL und MariaDB sowie Oracle (die Erweiterung ezoracle).
SQLite hat kein Replikat (siehe unten).

## Kurz gesagt

| | |
|---|---|
| Was sich ändert | `eZPostgreSQLDB` beachtet `[DatabaseSettings] UseSlaveServer` und die Schlüssel `SlaveServerArray[]` (bisher schickte es jede Anweisung an `Server`). `eZMySQLiDB` tat das schon, schickte aber jedes `SELECT` außerhalb einer Transaktion an das Replikat, sodass eine Anfrage ihre eigene Änderung veraltet zurücklesen konnte; es folgt jetzt derselben sicheren Regel. `eZOracleDB` aus ezoracle liest mit derselben Regel von einem schreibgeschützten Standby. |
| Wen es betrifft | Niemanden, solange `UseSlaveServer=enabled` nicht gesetzt ist. Die mitgelieferte `settings/site.ini` hat `UseSlaveServer=disabled`. **MySQL- und MariaDB-Websites, die `UseSlaveServer=enabled` schon nutzen, lesen ab 6.0.15 seltener vom Replikat und nie veraltet: siehe [MySQL und MariaDB](#mysql-und-mariadb).** |
| Was zu tun ist | Das Replikat in `settings/override/site.ini.append.php` eintragen (je Datenbank unten), den INI-Cache leeren und deployen. |

## Welche Anweisungen vom Replikat lesen

Eine Anweisung läuft nur dann auf dem Replikat, wenn **alles** davon zutrifft. Alles andere läuft wie bisher auf dem
Primärserver.

1. Sie ist ein einfaches `SELECT`, das nichts schreibt und nichts sperrt. Kein `WITH` (ein datenändernder CTE
   schreibt), kein `SELECT ... INTO`, kein `FOR UPDATE` / `FOR SHARE` (MySQL: `LOCK IN SHARE MODE`), keine
   Sequenzfunktion, keine Advisory- oder benannte Sperre. Jeder Treiber nennt die Formen seines Dialekts (unten).
   Geprüft wird der Text; ein Literal, das nur eines dieser Wörter enthält, geht also ebenfalls an den Primärserver:
   ein Umweg, nie eine falsche Antwort.
2. Keine Transaktion ist offen.
3. Die Anfrage hat noch nichts geschrieben. Das erste `INSERT`, `UPDATE`, `DELETE`, `BEGIN`, `LOCK`, `SET` oder jede
   andere Anweisung, die kein solches `SELECT` ist, bindet den Rest der Anfrage an den Primärserver; eine Anfrage liest
   also, was sie geschrieben hat.
4. Die Anfrage ist eine Webanfrage (PHP-FPM, Apache oder ein Velocity-Worker) mit der Methode `GET` oder `HEAD`.
   Cronjobs, Skripte und Konsolenbefehle nutzen immer den Primärserver, ebenso jedes `POST`.
5. Der Besucher ist nicht angemeldet (kein Cookie `is_logged_in`). Redakteure lesen immer vom Primärserver und sehen
   ihre eigene Arbeit sofort.
6. Das Replikat ist aktuell. Jede Datenbank prüft das auf ihre Weise (unten), mit der ersten Anweisung der Anfrage,
   die das Replikat nutzen könnte. Antwortet das Replikat nicht oder hängt es zurück, liest die ganze Anfrage vom
   Primärserver.

Jeder Fehler auf dem Replikat (keine Verbindung, eine wegen eines Recovery-Konflikts abgebrochene Anweisung, ein
Server, der weg ist) führt dieselbe Anweisung ohne Fehler für den Aufrufer auf dem Primärserver aus und schaltet das
Replikat für den Rest der Anfrage ab. Die Verbindung wird bei der ersten Nutzung geöffnet, mit 2 s Timeout und ohne
Wiederholungen. Ein Knoten, auf dem an der Adresse des Replikats nichts lauscht, liest einfach vom Primärserver;
dieselben Einstellungen passen also für jeden Knoten eines Clusters, auch wenn nur manche Knoten ein lokales Replikat
haben.

Entscheidung und Bindung gelten je Anfrage: `eZDBInterface::resetReplicaRequest()` ist der Reset-Hook `dbreplica` von
`expKernelServiceScope` und läuft am Anfang jeder Anfrage für jeden Treiber, auch in einem persistenten
Velocity-Worker. Ein ausdrückliches `eZDBInterface::SERVER_MASTER` liest immer vom Primärserver; `SERVER_SLAVE` (von
der Suche und von Rechteprüfungen mit temporären Tabellen übergeben) ist nur ein Wunsch, es entscheidet die Regel oben.

Das DFS-Cluster-Backend (`file.ini`, `config.cluster.php`) hat seine eigene Verbindung und liest weiter vom
Primärserver.

## Einstellungen für jede Datenbank

```ini
[DatabaseSettings]
UseSlaveServer=enabled
SlaveServerArray[]
SlaveServerArray[]=127.0.0.1
SlaveServerPort[]
SlaveServerPort[]=5413
SlaverServerUser[]
SlaverServerUser[]=<Anmeldung der Website>
SlaverServerPassword[]
SlaverServerPassword[]=<ihr Passwort>
SlaverServerDatabase[]
SlaverServerDatabase[]=<Datenbank der Website>
```

Mit mehr als einem Eintrag `SlaveServerArray[]` wählt jede Anfrage einen zufällig. Für MySQL, MariaDB und Oracle legen
diese Einstellungen fest, wie aktuell das Replikat sein muss (PostgreSQL nutzt stattdessen das synchrone Anwenden, siehe
unten):

| Einstellung | Vorgabe | Bedeutung |
|---|---|---|
| `SlaveServerStatusCheck` | `enabled` | Das Replikat nach seinem Replikationsstatus fragen. `disabled` vertraut dem Replikat ohne Frage (für eine Anmeldung, die den Status nicht lesen darf). |
| `SlaveServerMaxLag` | `0` | Höchstens so viele Sekunden darf das Replikat hinter seinem Primärserver liegen, um gelesen zu werden. |
| `SlaveServerCatchUp` | `enabled` | MySQL/MariaDB: Das Replikat muss die GTID-Position des Primärservers vom Anfang der Anfrage angewendet haben. Oracle: `STANDBY_MAX_DATA_DELAY` in der Sitzung des Standby. |
| `SlaveServerCatchUpWait` | `100` | MariaDB: wie lange (ms) die erste Anweisung darauf wartet, dass das Replikat diese Position erreicht, bevor die Anfrage vom Primärserver liest. |

Danach:

```bash
php bin/php/ezcache.php --clear-tag=ini --allow-root-user
./console exp:velocity deploy --allow-root-user
```

## PostgreSQL

Anweisungen, die dieser Treiber auf dem Primärserver hält: `nextval()`, `currval()`, `setval()`, `lastval()`,
`pg_advisory*`, `txid_current*`, `pg_current_*`, `pg_notify()`, `set_config()`, Large-Object- und dblink-Funktionen.

**Aktuell:** Die erste Anweisung der Anfrage fragt das Replikat nach `pg_is_in_recovery()` und
`pg_stat_wal_receiver.status`. Ein Standby mit unterbrochenem Stream wird nicht gelesen. Ein Server, der nicht in
Recovery ist (ein Primärserver), wird akzeptiert. Die Datenbankanmeldung der Website braucht `pg_read_all_stats`, um den
Status des Empfängers zu sehen; ohne sie wird das Replikat nie genutzt.

Ein physischer Standby trägt die Anmeldungen und Passwörter des Primärservers; die Anmeldung der Website funktioniert
also auch dort.

### Besucher nach einer Änderung: synchrones Anwenden

Die Regeln 3 und 5 decken die Anfrage des Redakteurs und seine folgenden Seiten ab. Ein anonymer Besucher, der die
Seite kurz nach dem Veröffentlichen abruft, wird von einer anderen Anfrage bedient, vielleicht auf einem anderen
Knoten. Bei asynchroner Replikation kann diese Anfrage auf dem Replikat noch die alte Fassung sehen, und der
View-Cache würde sie behalten.

Um dieses Fenster zu schließen, wartet der Primärserver, bis der Standby jeden Commit der Datenbank der Website
angewendet hat:

```sql
-- auf dem Primärserver
ALTER SYSTEM SET synchronous_commit = 'local';   -- nur die Datenbank der Website wartet, nicht jede
ALTER SYSTEM SET synchronous_standby_names = '<application_name des Standby>';
SELECT pg_reload_conf();
ALTER DATABASE <Datenbank der Website> SET synchronous_commit = remote_apply;
GRANT pg_read_all_stats TO <Anmeldung der Website>;
```

Ein Commit kehrt dann erst zurück, wenn der Standby ihn zeigt. Das kostet einen Netzwerk-Umlauf je Commit. Fällt der
Standby aus, warten Commits in dieser Datenbank, bis er zurück ist oder `synchronous_standby_names` geleert wird;
überwachen Sie den Standby daher und leeren Sie die Einstellung automatisch, sobald er nicht mehr streamt (ein
systemd-Timer genügt).

## MySQL und MariaDB

Anweisungen, die dieser Treiber auf dem Primärserver hält: `LOCK IN SHARE MODE`, `GET_LOCK()` und die anderen
Funktionen benannter Sperren, `LAST_INSERT_ID()`, die Sequenzfunktionen (`NEXTVAL()`, `NEXT VALUE FOR`), die
Wartefunktionen der Replikation, `LOAD_FILE()`. `SQL_CALC_FOUND_ROWS` und das `FOUND_ROWS()` danach bleiben zusammen auf
dem Server, von dem die Anfrage liest.

**Aktuell**, geprüft mit der ersten Anweisung der Anfrage, die das Replikat nutzen könnte:

1. **Status** (`SlaveServerStatusCheck`): `SHOW REPLICA STATUS` (`SHOW SLAVE STATUS` bei MySQL vor 8.0.22 und MariaDB
   vor 10.5.1). Beide Replikations-Threads müssen laufen, und `Seconds_Behind_Master` (`Seconds_Behind_Source`) muss
   bekannt und höchstens `SlaveServerMaxLag` sein. Ein Server ganz ohne Replikationsstatus (ein Primärserver) wird
   gelesen, wie er ist. Die Anmeldung für das Replikat braucht `REPLICATION CLIENT` (MySQL) oder `REPLICA MONITOR`
   (MariaDB ab 10.5.9); ohne sie wird das Replikat nicht gelesen, außer mit `SlaveServerStatusCheck=disabled`.
2. **Aufholen** (`SlaveServerCatchUp`): Bevor der Treiber das Replikat fragt, liest er die GTID-Position des
   Primärservers (`@@gtid_binlog_pos` bei MariaDB, `@@gtid_executed` bei MySQL). Das Replikat muss sie angewendet haben:
   MariaDB wartet bis zu `SlaveServerCatchUpWait` ms (`MASTER_GTID_WAIT()`), MySQL vergleicht sofort
   (`GTID_SUBSET()`). Was der Primärserver beim Start der Anfrage festgeschrieben hatte, auf welchem Knoten auch immer,
   sieht die Anfrage; ein Besucher direkt nach einer Änderung bekommt also nie die alte Seite (und der View-Cache behält
   nie eine). Das kostet eine kleine Anweisung auf dem Primärserver je anonymer Anfrage statt jeder Anweisung der
   Seite. Ohne GTID-Position (kein Binärlog, MySQL mit `gtid_mode=OFF`) entscheidet nur der Rückstand.

Die Verbindung zum Replikat nutzt den Zeichensatz des Primärservers; ein Replikat, das ihn nicht kann, wird nicht
gelesen. Eine aus einer früheren Anfrage behaltene Verbindung zum Replikat (persistente Verbindungen, ein
Velocity-Worker), die inzwischen abgebrochen ist, etwa weil das Replikat neu gestartet wurde, wird in der nächsten
Anfrage einmal neu geöffnet.

### Verhaltensänderungen für Websites, die UseSlaveServer schon nutzten

Die Einstellung und ihre Schlüssel bleiben gleich; was sich ab 6.0.15 ändert:

| Bisher | Ab 6.0.15 |
|---|---|
| Die Verbindung zum Replikat wurde im Konstruktor geöffnet, und ein ausgefallenes Replikat legte die Website still (`eZDBNoConnectionException`). | Geöffnet bei der ersten Nutzung; bei einem ausgefallenen Replikat liest die Anfrage vom Primärserver. |
| Jedes `SELECT` außerhalb einer Transaktion ging an das Replikat, auch direkt nach einem Schreibzugriff in derselben Anfrage, auch für angemeldete Redakteure, Cronjobs und `POST`-Anfragen. | Nur die Anfragen und Anweisungen der Regel oben. |
| Keine Prüfung von Rückstand oder Threads des Replikats. | `SlaveServerStatusCheck`, `SlaveServerMaxLag`, `SlaveServerCatchUp`. **Geben Sie der Anmeldung für das Replikat `REPLICATION CLIENT` (MySQL) oder `REPLICA MONITOR` (MariaDB), oder setzen Sie `SlaveServerStatusCheck=disabled`, um dem Replikat weiter zu vertrauen.** |
| `SERVER_SLAVE` erzwang das Replikat; temporäre Tabellen der Suche und der Rechteprüfung entstanden dort. | Es entscheidet die Regel; eine temporäre Tabelle entsteht auf dem Primärserver und bindet die Anfrage. |
| `eZTableList( SERVER_SLAVE )` listete die Tabellen des Replikats, und `$db->DBConnection` war die Verbindung zum Replikat. | Beides ist der Primärserver; die Verbindung zum Replikat ist `$db->ReplicaConnection`. |

Die meisten Seiten eines anonymen Besuchers lesen wie bisher vom Replikat; Redakteure, Formulare und Skripte lesen vom
Primärserver.

## Oracle (ezoracle)

`eZOracleDB` aus der Erweiterung ezoracle liest mit derselben Regel von einem schreibgeschützten Standby (Active Data
Guard). `SlaveServerArray[]` ist der Verbindungsstring des Standby (Easy Connect oder ein TNS-Name, wie `Database` für
den Primärserver), dazu `SlaverServerUser[]` und `SlaverServerPassword[]`; `SlaveServerPort[]` und
`SlaverServerDatabase[]` werden nicht genutzt. Die Sitzung auf dem Standby bekommt dieselben Sitzungseinstellungen wie
die des Primärservers (NLS, `CURSOR_SHARING`).

Anweisungen, die dieser Treiber auf dem Primärserver hält: `WITH`, `seq.NEXTVAL` und `seq.CURRVAL`, `INTO`, Aufrufe von
`DBMS_*` und `UTL_*`, `SYS_GUID()`; PL/SQL-Blöcke sind keine `SELECT`s.

**Aktuell:** `v$database` des Standby. `PRIMARY` (eine schreibgeschützte Anmeldung am Primärserver als Ersatz) wird
gelesen, wie er ist. Ein `PHYSICAL STANDBY` wird nur gelesen, wenn er `READ ONLY WITH APPLY` geöffnet ist und sein
`apply lag` in `v$dataguard_stats` höchstens `SlaveServerMaxLag` beträgt. Mit `SlaveServerCatchUp=enabled` setzt die
Sitzung auf dem Standby `STANDBY_MAX_DATA_DELAY = <SlaveServerMaxLag>`; Oracle selbst lehnt dann ein älteres Lesen ab
(`ORA-03172`, und die Anweisung läuft auf dem Primärserver). `STANDBY_MAX_DATA_DELAY = 0` braucht SYNC-Redo-Transport;
bei ASYNC setzen Sie `SlaveServerMaxLag` über 0. Die Anmeldung für den Standby braucht:

```sql
GRANT SELECT ON sys.v_$database TO <Anmeldung für den Standby>;
GRANT SELECT ON sys.v_$dataguard_stats TO <Anmeldung für den Standby>;
```

Ohne sie wird der Standby nicht gelesen, außer mit `SlaveServerStatusCheck=disabled`. Eine Anmeldung für den Standby,
die nicht Eigentümerin des Schemas ist, braucht außerdem `SELECT` auf die Tabellen der Website und Synonyme dafür.
Einzelheiten: `INSTALL` von ezoracle, "7. Read-only standby".

## SQLite: nicht anwendbar

Eine SQLite-Datenbank ist eine Datei auf einem Server, die jede Anweisung liest und schreibt. `eZSQLite3DB` ignoriert
`UseSlaveServer` und die Schlüssel `SlaveServerArray[]` (es schaltet die Einstellung beim Verbinden ab); von einer
MySQL- oder PostgreSQL-Website übernommene Einstellungen funktionieren also unverändert. Der geteilte Abfrage-Cache und
der HTTP-Cache arbeiten unter SQLite wie unter den anderen Datenbanken.

MongoDB (`expMongoDB`) liest `UseSlaveServer` ebenfalls nicht; ein MongoDB-Replica-Set hat eigene Lesepräferenzen.

## Prüfen

- Tests (ohne Datenbank): `tests/tests/lib/ezdb/eZPostgreSQLDBReadReplicaTest.php`,
  `eZMySQLiDBReadReplicaTest.php` und `eZSQLite3DBReadReplicaTest.php`; `tests/eZOracleReadReplicaTest.php` von
  ezoracle.
- PostgreSQL: Auf dem Standby zeigt `pg_stat_activity` die Anmeldung der Website, während anonyme Seiten ausgeliefert
  werden; auf dem Primärserver zeigt `pg_stat_replication` den Standby als `streaming`, mit `sync_state = sync`, wenn
  das synchrone Anwenden an ist.
- MySQL/MariaDB: Auf dem Replikat zeigt `SHOW PROCESSLIST` die Anmeldung der Website, während anonyme Seiten
  ausgeliefert werden; eine Anweisung `SELECT @@server_id` aus einer Seite zeigt, welcher Server geantwortet hat.
- Oracle: `SYS_CONTEXT('USERENV', 'SESSION_USER')` oder `v$session` auf dem Standby.

## Verwandte Seiten

- [Der Kernel-Dienstcontainer](kernel-dienste.md) (die Reset-Hooks)
- [Der SQL-Abfrage-Cache](sql-abfrage-cache.md)
- [Lese-Replikate](../../hilfe/verwaltung/betrieb/verfuegbarkeit/lese-replikate.md)
