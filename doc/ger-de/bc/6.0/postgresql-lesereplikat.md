# PostgreSQL-Lesereplikat (6.0.15)

Lesen Sie diese Seite, wenn Ihre Website unter PostgreSQL läuft und ihre Seiten von einem Replikat nahe am Webserver
lesen sollen, etwa von einem Hot Standby auf derselben Maschine wie ein Knoten eines Clusters, während jeder
Schreibzugriff weiter an den Primärserver geht.

## Kurz gesagt

| | |
|---|---|
| Was sich ändert | `eZPostgreSQLDB` beachtet `[DatabaseSettings] UseSlaveServer` und die Schlüssel `SlaveServerArray[]`. Bisher tat das nur der MySQLi-Treiber; PostgreSQL schickte jede Anweisung an `Server`. |
| Wen es betrifft | Niemanden, solange für eine PostgreSQL-Website nicht `UseSlaveServer=enabled` gesetzt ist. Die mitgelieferte `settings/site.ini` hat `UseSlaveServer=disabled`. |
| Was zu tun ist | Das Replikat in `settings/override/site.ini.append.php` eintragen (unten), den INI-Cache leeren und deployen. |

## Welche Anweisungen vom Replikat lesen

Eine Anweisung läuft nur dann auf dem Replikat, wenn **alles** davon zutrifft. Alles andere läuft wie bisher auf dem
Primärserver.

1. Sie ist ein einfaches `SELECT`: kein `WITH` (ein datenändernder CTE schreibt), kein `nextval()`, `currval()`,
   `setval()`, `lastval()`, kein `FOR UPDATE` / `FOR SHARE`, keine Advisory-Sperre, kein `SELECT ... INTO`. Geprüft
   wird der Text; ein Literal, das nur eines dieser Wörter enthält, geht also ebenfalls an den Primärserver (ein
   Umweg, nie eine falsche Antwort).
2. Keine Transaktion ist offen.
3. Die Anfrage hat noch nichts geschrieben. Das erste `INSERT`, `UPDATE`, `DELETE`, `BEGIN`, `LOCK` oder jede andere
   Anweisung, die kein solches `SELECT` ist, bindet den Rest der Anfrage an den Primärserver; eine Anfrage liest also,
   was sie geschrieben hat.
4. Die Anfrage ist eine Webanfrage (PHP-FPM, Apache oder ein Velocity-Worker) mit der Methode `GET` oder `HEAD`.
   Cronjobs, Skripte und Konsolenbefehle nutzen immer den Primärserver, ebenso jedes `POST`.
5. Der Besucher ist nicht angemeldet (kein Cookie `is_logged_in`). Redakteure lesen immer vom Primärserver und sehen
   ihre eigene Arbeit sofort.
6. Das Replikat antwortet und streamt von seinem Primärserver: Die erste Anweisung der Anfrage fragt es nach
   `pg_is_in_recovery()` und `pg_stat_wal_receiver.status`. Ein Standby mit unterbrochenem Stream wird nicht gelesen.
   Ein Server, der nicht in Recovery ist (ein Primärserver), wird akzeptiert. Die Datenbankanmeldung der Website braucht
   `pg_read_all_stats`, um den Status des Empfängers zu sehen; ohne sie wird das Replikat nie genutzt.

Jeder Fehler auf dem Replikat (keine Verbindung, eine wegen eines Recovery-Konflikts abgebrochene Anweisung) führt
dieselbe Anweisung auf dem Primärserver aus und schaltet das Replikat für den Rest der Anfrage ab. Die Verbindung wird
bei der ersten Nutzung geöffnet, mit 2 s Timeout und ohne Wiederholungen. Ein Knoten, auf dem an der Adresse des
Replikats nichts lauscht, liest einfach vom Primärserver; dieselben Einstellungen passen also für jeden Knoten eines
Clusters, auch wenn nur manche Knoten ein lokales Replikat haben.

Entscheidung und Bindung gelten je Anfrage: `eZPostgreSQLDB::resetRequest()` ist ein Reset-Hook von
`expKernelServiceScope` und läuft am Anfang jeder Anfrage, auch in einem persistenten Velocity-Worker.

Das DFS-Cluster-Backend (`file.ini`, `config.cluster.php`) hat seine eigene Verbindung und liest weiter vom
Primärserver.

## Besucher nach einer Änderung: synchrones Anwenden

Die Regeln 3 und 5 decken die Anfrage des Redakteurs und seine folgenden Seiten ab. Ein anonymer Besucher, der die
Seite kurz nach dem Veröffentlichen abruft, wird von einer anderen Anfrage bedient, vielleicht auf einem anderen
Knoten. Bei asynchroner Replikation kann diese Anfrage auf dem Replikat noch die alte Fassung sehen, und der
View-Cache würde sie behalten.

Um dieses Fenster zu schließen, wartet der Primärserver, bis der Standby jeden Commit der Datenbank der Website
angewendet hat:

```sql
-- auf dem Primärserver
ALTER SYSTEM SET synchronous_standby_names = '<application_name des Standby>';
SELECT pg_reload_conf();
ALTER DATABASE <Datenbank der Website> SET synchronous_commit = remote_apply;
GRANT pg_read_all_stats TO <Anmeldung der Website>;
```

Ein Commit kehrt dann erst zurück, wenn der Standby ihn zeigt. Das kostet einen Netzwerk-Umlauf je Commit. Fällt der
Standby aus, warten Commits in dieser Datenbank, bis er zurück ist oder `synchronous_standby_names` geleert wird;
überwachen Sie den Standby daher und leeren Sie die Einstellung automatisch, sobald er nicht mehr streamt (ein
systemd-Timer genügt).

## Einstellungen

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

Ein physischer Standby trägt die Anmeldungen und Passwörter des Primärservers; die Anmeldung der Website funktioniert
also auch dort. Danach:

```bash
php bin/php/ezcache.php --clear-tag=ini --allow-root-user
./console exp:velocity deploy --allow-root-user
```

## Prüfen

- `tests/tests/lib/ezdb/eZPostgreSQLDBReadReplicaTest.php` (ohne Datenbank): welche Anweisungen und Anfragen das
  Replikat nutzen dürfen, die Bindung nach einem Schreibzugriff, der Reset-Hook.
- Auf dem Standby zeigt `pg_stat_activity` die Anmeldung der Website, während anonyme Seiten ausgeliefert werden; auf
  dem Primärserver zeigt `pg_stat_replication` den Standby als `streaming`, mit `sync_state = sync`, wenn das
  synchrone Anwenden an ist.

## Verwandte Seiten

- [Der Kernel-Dienstcontainer](kernel-dienste.md) (die Reset-Hooks)
- [Lese-Replikate](../../hilfe/verwaltung/betrieb/verfuegbarkeit/lese-replikate.md)
