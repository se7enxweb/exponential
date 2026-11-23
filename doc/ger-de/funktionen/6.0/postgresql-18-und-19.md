# PostgreSQL 18 und 19

Diese Seite ist für Administratoren und Datenbankadministratoren, die Exponential auf PostgreSQL 18 oder 19 betreiben,
eine bestehende Installation auf eine dieser Versionen aktualisieren wollen oder einen Cluster aus Webservern darauf
betreiben. Ab Exponential 6.0.15 werden PostgreSQL 13 bis 19 unterstützt und getestet; 18 und 19, die beiden neuesten
Hauptversionen, sind von jeder Datenbank-Testsuite, von den Tests des Cluster-Backends und von Upgrades im
Produktivbetrieb abgedeckt. Die Seite sagt, wie die Unterstützung getestet wird, welche Client-Programme eine Website
braucht, wie Sie aktualisieren, wie Sie einen Cluster auf 18 und 19 mit den genauen Einstellungen betreiben und wo die
bekannten Grenzen liegen.

## Inhalt

1. [Kurz gesagt](#kurz-gesagt)
2. [Unterstützte Versionen und wie die Unterstützung getestet wird](#unterstützte-versionen-und-wie-die-unterstützung-getestet-wird)
3. [Client-Versionen: PHP, libpq, pg_dump und psql](#client-versionen-php-libpq-pg_dump-und-psql)
4. [Eine neue Installation auf 18 oder 19](#eine-neue-installation-auf-18-oder-19)
5. [Eine bestehende Installation aktualisieren](#eine-bestehende-installation-aktualisieren)
6. [Ein Cluster auf 18 und 19](#ein-cluster-auf-18-und-19)
7. [Nachweis aus dem Produktivbetrieb](#nachweis-aus-dem-produktivbetrieb)
8. [Bekannte Grenzen](#bekannte-grenzen)
9. [Verwandte Seiten](#verwandte-seiten)

## Kurz gesagt

| | |
|---|---|
| Unterstützt | PostgreSQL 13, 14, 15, 16, 17, 18 und 19, für die Datenbank der Website und für die Metadaten des DFS-Clusters. 13 ist das Minimum (`pgcrypto` ist ab 13 eine vertrauenswürdige Erweiterung). |
| Bei jedem Push getestet | Die Datenbank-Testsuites auf PostgreSQL 13, 15, 18 und 19; das DFS-Cluster-Backend auf 13 bis 19. |
| Von Hand geprüft auf 18.7 und 19.0 | Installation, die Upgrade-Sicherung, die Datenbankstufe des Upgrades, eine Wiederherstellungsprobe, der Schema-Rundweg, die Umwandlung von MySQL nach PostgreSQL, das DFS-Cluster-Backend, das Lesereplikat mit einem Streaming-Standby und der SQL-Abfrage-Cache. |
| Client-Regel | `pg_dump` und `psql` müssen mindestens so neu sein wie der Server. Exponential findet einen solchen Client selbst; `upgrade.ini [UpgradeSettings] PostgreSQLClientDir` nennt einen. |
| PHP | Die Erweiterungen `pgsql` (der Treiber `eZPostgreSQLDB` der Website) und `pdo_pgsql` (das DFS-Cluster-Backend und sein Gateway) auf jedem Webserver. |
| Cluster | Das Kernel-Backend `expDFSFileHandlerPostgresqlBackend`, ein Primärserver mit einem Streaming-Standby und Lesereplikate über `[DatabaseSettings] UseSlaveServer`. |

## Unterstützte Versionen und wie die Unterstützung getestet wird

Exponential braucht nichts von PostgreSQL, was eine neuere Hauptversion wegnimmt: Der Treiber nutzt einfaches SQL,
Sequenzen namens `<tabelle>_<spalte>_seq` und `digest()` aus `pgcrypto`; das DFS-Cluster-Backend nutzt
`INSERT ... ON CONFLICT`, `FOR UPDATE SKIP LOCKED`, `lock_timeout` und Indizes mit `text_pattern_ops`, alles ab 13
vorhanden. Die Unterstützung wird daraus nicht abgeleitet, sondern getestet:

**Kontinuierliche Integration** (`.github/workflows/phpunit.yml`, bei jedem Push):

| Job | PostgreSQL | Was läuft |
|---|---|---|
| Database tests (postgresql 13, 15, 18 and 19, PHP 8.3) | `postgres:13`, `postgres:15`, `postgres:18`, `postgres:19` | Jede Datenbank-Testsuite (`tests/runtests.php --dsn ... --db-per-test`) gegen eine frische Datenbank mit `pgcrypto`. 13 ist die älteste unterstützte Version, 15 die, auf der die Suites zuvor allein liefen, 18 und 19 die beiden neuesten. Jeder Lauf installiert den PGDG-Client seines eigenen Servers, weil die Prüfungen von Sicherungspunkt und Sicherung die Testdatenbank sichern und `pg_dump` einen neueren Server ablehnt. |
| DFS cluster backends (PostgreSQL 13 bis 19, PHP 8.3; 18 auch auf PHP 8.5) | jede Hauptversion von 13 bis 19 | Die Paritätsprüfungen, die jedes Cluster-Backend besteht (`expDFSPostgresqlBackendIntegrationTest`), das Gateway, die Indexnutzung von Präfixsuchen und das Upgrade von Tabellen der alten Erweiterung. |

**Von Hand geprüft auf 18.7 und 19.0**, über die Suites hinaus: eine neue Installation, `exp:upgrade:backup`, die
Datenbankstufe von `exp:upgrade`, eine Wiederherstellungsprobe (sichern, in eine neue Datenbank zurückspielen,
vergleichen), der Schema-Rundweg (`ezsqldumpschema.php` und `ezsqlinsertschema.php`), die Umwandlung einer
MySQL-Datenbank nach PostgreSQL (Byte für Byte dieselbe Ausgabe wie auf 13), das DFS-Cluster-Backend mit seinem
Gateway, das Lesereplikat auf einem Streaming-Standby mit synchronem Anwenden und der SQL-Abfrage-Cache im
gemeinsamen Modus.

Die übrige Unterstützungsmatrix, für jede Datenbank, steht in
[Datenbankunterstützung](../../hilfe/datenbankunterstuetzung.md).

## Client-Versionen: PHP, libpq, pg_dump und psql

Drei Arten von Clients sprechen mit dem Server. Nur für die dritte gilt eine strenge Versionsregel.

| Client | Wer ihn nutzt | Versionsregel |
|---|---|---|
| PHP `pgsql` (libpq) | der Treiber `eZPostgreSQLDB` der Website (`pg_connect`, `pg_pconnect`) | Jede libpq, gegen die Ihr PHP gebaut ist, arbeitet mit einem neueren Server. Für Passwörter mit `scram-sha-256`, die PostgreSQL ab 14 standardmäßig nutzt, braucht es libpq 10 oder neuer. |
| PHP `pdo_pgsql` (libpq) | das DFS-Cluster-Backend und sein Gateway `index_cluster.php` | Wie bei `pgsql`. |
| `pg_dump`, `pg_restore`, `psql` | die Upgrade-Sicherung, die Prüfung des Sicherungspunkts, das Zurückrollen, der Migrations-Diff und `ezconvertdbcharset.php` | **Mindestens die Hauptversion des Servers.** `pg_dump` lehnt einen neueren Server ab („aborting because of server version mismatch“). |

Ein Webserver, der den Client von PostgreSQL 13 behalten hat, nachdem sein Datenbankserver auf 18 umgezogen ist, kann
die Website weiter ausliefern, aber mit diesem Client keine Sicherung machen, und `exp:upgrade --apply` hielte bei
seinem Sicherungspunkt vor dem ersten Schreiben an („the caption failed, nothing was changed“). Ab 6.0.15 wählt
Exponential den Client selbst (`expUpgradeBackup::postgresqlTool()`) und nimmt den ersten dieser Orte, dessen
Hauptversion mindestens die des Servers ist:

1. `settings/upgrade.ini [UpgradeSettings] PostgreSQLClientDir`, ein `bin`-Verzeichnis, wenn gesetzt;
2. das Programm im `PATH`;
3. die versionierten Installationen, neueste Hauptversion zuerst: `/usr/pgsql-<major>/bin` (PGDG-Pakete für RHEL
   und seine Nachbauten), `/usr/lib/postgresql/<major>/bin` (Debian und Ubuntu), `/opt/exp-pg<major>/current/bin`,
   `/usr/local/pgsql/bin`.

Ohne einen ausreichend neuen nimmt es den neuesten, den es gefunden hat, und `pg_dump` nennt den Unterschied selbst.
Den Client fest einstellen:

```ini
# settings/override/upgrade.ini.append.php
[UpgradeSettings]
PostgreSQLClientDir=/usr/pgsql-18/bin
```

Installieren Sie den Client der Hauptversion des Servers neben dem alten; den alten müssen Sie nicht entfernen:

```bash
# RHEL, Rocky, AlmaLinux mit dem PGDG-Repository
dnf install postgresql18
# Debian und Ubuntu mit dem PGDG-Repository
apt install postgresql-client-18
```

Dann prüfen:

```bash
./console exp:upgrade:backup                 # sichert die Datenbank mit dem gewählten Client
./console exp:upgrade:backup --format=json   # dasselbe als Maschinenausgabe
```

## Eine neue Installation auf 18 oder 19

Nichts unterscheidet sich von früheren Versionen; [PostgreSQL einrichten](../../hilfe/datenbankadministratoren/postgresql-einrichten.md)
und [9.4 PostgreSQL](../../installation/09-datenbanken.md#94-postgresql) haben jeden Schritt. Kurz:

```bash
sudo -u postgres createuser --pwprompt exponential
sudo -u postgres createdb --owner=exponential --encoding=UTF8 --template=template0 exponential
sudo -u postgres psql -d exponential -c 'CREATE EXTENSION IF NOT EXISTS pgcrypto;'
```

```ini
# settings/override/site.ini.append.php
[DatabaseSettings]
DatabaseImplementation=ezpostgresql
Server=localhost
Port=5432
User=exponential
Password=<das Passwort>
Database=exponential
UsePersistentConnection=disabled
```

Der Benutzer der Website muss die Datenbank **besitzen**: Ab PostgreSQL 15 dürfen andere Benutzer keine Tabellen in
ihrem Schema `public` anlegen. `pgcrypto` kommt mit dem Contrib-Paket des Servers (`postgresql18-contrib` auf
PGDG-RPM-Systemen; unter Debian gehört es zum Serverpaket). Der Installer legt die Erweiterung an, wenn er kann; der
Einrichtungsassistent meldet „The 'digest' function is not available in your database“, wenn nicht.

`Server` wird libpq als `host` übergeben, ein Pfad ist also ein Socket-Verzeichnis: `Server=/run/postgresql` verbindet
über den Unix-Socket, wenn die Datenbank auf dem Webserver selbst läuft.

## Eine bestehende Installation aktualisieren

Ein Hauptversions-Upgrade von PostgreSQL (13 auf 18, 16 auf 19 und so weiter) ändert den Server, nicht Exponential:
Schema und Daten der Website ziehen unverändert um, und dafür ist kein Upgrade-Schritt von Exponential nötig. Planen
Sie es wie jede Datenbankänderung.

### Bevor Sie beginnen

1. **Installieren Sie den Client des neuen Servers auf jedem Webserver** (siehe
   [Client-Versionen](#client-versionen-php-libpq-pg_dump-und-psql)) und setzen Sie `PostgreSQLClientDir`, wenn er
   nicht unter den Standardpfaden liegt.
2. **Sichern Sie mit den Werkzeugen, mit denen Sie zurückspielen:** `./console exp:upgrade:backup` (ein `pg_dump`
   nach `var/backup/upgrade/<zeit>/` und eine Kopie von `settings/override`) oder Ihr eigenes `pg_dump -Fc` mit dem
   neuen Client.
3. **Halten Sie fest, was die Datenbank nutzt:**

   ```sql
   SELECT extname, extversion FROM pg_extension;   -- pgcrypto und plpgsql; alles andere braucht auch das Paket des neuen Servers
   SELECT count(*) FROM pg_tables WHERE schemaname = 'public';
   ```

   Notieren Sie die Zeilenzahl jeder Tabelle, um nach dem Upgrade vergleichen zu können (ein Skript, das über
   `pg_tables` läuft und `SELECT count(*)` ausführt, genügt).
4. **Schalten Sie die Website in den Wartungsmodus**, damit während des Umzugs kein Redakteur schreibt:
   `php bin/php/maintenance.php on --message="Datenbank-Upgrade" --allow-root-user`.

### Weg 1: pg_upgrade (schnell, für große Datenbanken)

`pg_upgrade` bringt die Datendateien des alten Clusters in wenigen Minuten auf die neue Hauptversion, auch bei
Datenbanken von vielen Gigabyte. Folgen Sie der [Dokumentation von pg_upgrade](https://www.postgresql.org/docs/current/pgupgrade.html);
für eine Exponential-Website zählt:

- Installieren Sie zuerst den neuen Server **und sein Contrib-Paket**: `pg_upgrade` verweigert den Lauf, wenn eine
  Erweiterung des alten Clusters (`pgcrypto`) auf dem neuen Server keine Bibliothek hat.
- **Prüfsummen der Daten.** Ab PostgreSQL 18 schaltet `initdb` Datenprüfsummen standardmäßig ein, und `pg_upgrade`
  verlangt, dass beide Cluster übereinstimmen. Hat der alte Cluster keine Prüfsummen, legen Sie den neuen mit
  `--no-data-checksums` an (oder schalten Sie die Prüfsummen im alten Cluster vorher bei gestopptem Server mit
  `pg_checksums --enable` ein).
- Führen Sie zuerst `pg_upgrade --check` aus; es ändert nichts und nennt, was das Upgrade aufhalten würde.
- Sammeln Sie danach Statistiken: Ab PostgreSQL 18 behält `pg_upgrade` die Planerstatistiken, aber nicht die
  erweiterten Statistiken, und ältere Zielversionen behalten keine. `vacuumdb --all --analyze-in-stages` erledigt
  beides.
- Aktualisieren Sie die Erweiterungen auf die Version des neuen Servers: `ALTER EXTENSION pgcrypto UPDATE;` in der
  Datenbank der Website.
- **Ein Streaming-Standby** kann einem `pg_upgrade` seines Primärservers nicht von selbst folgen. Bauen Sie ihn
  entweder mit `pg_basebackup` vom aktualisierten Primärserver neu auf oder nutzen Sie das `rsync`-Verfahren aus der
  Dokumentation von `pg_upgrade`. Exponential liest vom Primärserver, solange ein Replikat fehlt (siehe
  [Lesereplikate](#lesereplikate-auf-18-und-19)), die Website läuft also weiter, während der Standby neu entsteht.

### Weg 2: Sichern und Zurückspielen (einfach, für kleine und mittlere Datenbanken)

Sichern Sie mit dem **neuen** Client vom alten Server und spielen Sie in den neuen Server zurück:

```bash
/usr/pgsql-18/bin/pg_dump -h old-db -U exponential -Fc exponential > exponential.dump
/usr/pgsql-18/bin/createdb -h new-db -U postgres --owner=exponential --encoding=UTF8 --template=template0 exponential
/usr/pgsql-18/bin/psql -h new-db -U postgres -d exponential -c 'CREATE EXTENSION IF NOT EXISTS pgcrypto;'
/usr/pgsql-18/bin/pg_restore -h new-db -U exponential -d exponential --no-owner exponential.dump
/usr/pgsql-18/bin/vacuumdb -h new-db -U exponential --analyze-in-stages exponential
```

Die Sicherung enthält die aktuellen Werte der Sequenzen; nach einem vollständigen Zurückspielen ist nichts zu
korrigieren. Haben Sie nur einzelne Tabellen zurückgespielt, korrigieren Sie deren Sequenzen, wie
[PostgreSQL betreiben](../../hilfe/datenbankadministratoren/postgresql-betrieb.md#sequenzen-korrigieren) zeigt.

### Die Website umstellen und prüfen

1. Richten Sie `[DatabaseSettings] Server` und `Port` (und im Cluster `[eZDFSClusteringSettings] DBHost` und `DBPort`
   sowie `CLUSTER_STORAGE_HOST`/`CLUSTER_STORAGE_PORT` in `config.cluster.php`) auf den neuen Server, wenn er nicht die
   alte Adresse übernommen hat.
2. Leeren Sie den INI-Cache und deployen Sie: `php bin/php/ezcache.php --clear-tag=ini --allow-root-user` und
   `./console exp:velocity deploy --allow-root-user`.
3. Prüfen Sie:

   | Prüfung | Wie |
   |---|---|
   | Die Website antwortet | `curl -s https://<ihr-host>/ezinfo/is_alive` gibt `Exponential is alive` aus |
   | Dieselben Tabellen und Zeilen | vergleichen Sie mit den vorher notierten Zahlen; auf 18 und 19 muss jede Tabelle übereinstimmen |
   | Erweiterungen | `SELECT extname, extversion FROM pg_extension;` zeigt `pgcrypto` in der Version des neuen Servers |
   | Sicherungen funktionieren | `./console exp:upgrade:backup` läuft durch und nennt den neuen Client |
   | Upgrade-Schritte | `./console exp:upgrade:status` zeigt nichts Offenes, was vorher nicht offen war |
   | Cluster-Metadaten | `SELECT count(*) FROM ezdfsfile;` in der Cluster-Datenbank stimmt, und ein Bild der Website lädt über `index_cluster.php` |
   | Passwort-Hashes | `SELECT rolname FROM pg_authid WHERE rolpassword LIKE 'md5%';` nennt keine Anmeldung von Exponential (siehe [Bekannte Grenzen](#bekannte-grenzen)) |

4. Nehmen Sie die Website aus dem Wartungsmodus: `php bin/php/maintenance.php off --allow-root-user`.

Der SQL-Abfrage-Cache hält die Zeit des letzten Schreibens jeder Tabelle fest, nicht die Zeilen selbst; nach einem
Zurückspielen stellt `php bin/php/ezcache.php --clear-id=querycache --allow-root-user` sicher, dass kein Ergebnis von
vor dem Umzug genutzt wird.

### Von MySQL oder MariaDB auf PostgreSQL 18 oder 19

Die Schema-Werkzeuge wandeln eine MySQL- oder MariaDB-Datenbank über das datenbankneutrale Format `.dba` nach
PostgreSQL um ([Zwischen Engines wechseln](../../hilfe/datenbankadministratoren/zwischen-engines-wechseln.md)). Drei
Fehler, die eine solche Umwandlung aufhielten, sind in 6.0.15 behoben:

- `ezsqldumpschema.php --output-types=data` setzt Tabellen- und Spaltennamen auf MySQL in Anführungszeichen, eine
  Spalte mit dem Namen eines reservierten Worts (`interval`, `group`) hält die Sicherung also nicht mehr auf.
- Der PostgreSQL-Schema-Handler bildet Spalten vom Typ `timestamp`, `datetime`, `date` und `time` aus Tabellen von
  Erweiterungen und Dritten ab (sie scheiterten mit „Unhandled column type: timestamp“).
- Eine `NOT NULL`-Zahl- oder Datumsspalte ohne Standardwert bekommt kein `DEFAULT ''`, und die Spaltenlisten von
  `INSERT` für PostgreSQL setzen reservierte Wörter in Anführungszeichen, Tabellen mit solchen Spalten werden also
  angelegt und gefüllt.

Die Umwandlung liefert auf 13, 18 und 19 dieselbe Ausgabe.

## Ein Cluster auf 18 und 19

Ein Cluster aus Exponential-Webservern nutzt PostgreSQL in bis zu drei Rollen. Es sind getrennte Entscheidungen; es
kann derselbe Server sein oder verschiedene:

| Rolle | Einstellungen | Nötig für |
|---|---|---|
| Die Datenbank der Website (Primärserver) | `site.ini [DatabaseSettings]` | jede Installation |
| Die Metadaten des DFS-Clusters | `file.ini [eZDFSClusteringSettings]` und `config.cluster.php` | mehrere Webserver, die gespeicherte Dateien und Caches teilen |
| Lesereplikate (Standbys) | `site.ini [DatabaseSettings] UseSlaveServer` und `SlaveServerArray[]` | anonyme Seiten von einem Standby nahe am Webserver lesen |

Der Überblick über alle Teile eines Clusters steht in [Clustering in Exponential 6.0](clustering-ueberblick.md).

### Das DFS-Backend auf PostgreSQL

Das Kernel-Backend `expDFSFileHandlerPostgresqlBackend` führt den Index jeder gespeicherten Datei und jeder
Cache-Datei in den Tabellen `ezdfsfile` und `ezdfsfile_cache`; die Dateien selbst liegen auf einem Share, das jeder
Webserver einhängt. In `settings/override/file.ini.append.php`:

```ini
[ClusteringSettings]
FileHandler=eZDFSFileHandler

[eZDFSClusteringSettings]
MountPointPath=/mnt/exp-dfs
DBBackend=expDFSFileHandlerPostgresqlBackend
DBHost=cluster-db.internal
DBPort=5432
# oder, auf dem Datenbankserver selbst, das Verzeichnis des Unix-Sockets (es tritt an die Stelle von DBHost):
# DBSocket=/run/postgresql
DBName=exp_cluster
DBUser=exp_cluster
DBPassword=<das Passwort>
# Verbindungsversuche und Versuche einer Anweisung, die auf einen Deadlock oder eine Sperre traf
DBConnectRetries=3
DBExecuteRetries=20
# Sekunden, die eine Anweisung auf eine Zeilensperre wartet, bevor ihre Transaktion neu versucht wird (Standard 10)
DBLockTimeout=10
# eine Verbindung je PHP-Worker zwischen Anfragen behalten (Standard: aus)
DBPersistentConnection=enabled
```

Und in `config.cluster.php`, für das Gateway `index_cluster.php`, das gespeicherte Bilder und den öffentlichen Cache
ausliefert:

```php
<?php
define( 'CLUSTER_STORAGE_BACKEND', 'dfspostgresql' );
define( 'CLUSTER_STORAGE_HOST', 'cluster-db.internal' );
define( 'CLUSTER_STORAGE_PORT', 5432 );
define( 'CLUSTER_STORAGE_USER', 'exp_cluster' );
define( 'CLUSTER_STORAGE_PASS', '<das Passwort>' );
define( 'CLUSTER_STORAGE_DB', 'exp_cluster' );
define( 'CLUSTER_STORAGE_CHARSET', 'utf8' );
define( 'CLUSTER_MOUNT_POINT_PATH', '/mnt/exp-dfs' );
```

Tabellen: `psql -v ON_ERROR_STOP=1 -h cluster-db.internal -U exp_cluster -d exp_cluster -f kernel/sql/postgresql/cluster_dfs_schema.sql`.
Auf einem einzelnen Server erledigt `./console exp:cluster:enable --apply` alles, mit den Tabellen in der Datenbank
der Website ([Ein DFS-Cluster mit einem Knoten in einem Befehl](cluster-befehle.md)).

**Ein Hin und Zurück je Anweisung.** Das Backend bindet seine Werte in PHP (emulierte Prepares von PDO; die Werte sind
nur Ganzzahlen, Zeichenketten und Nullwerte), jede Anweisung kostet also einen Weg zum Server statt drei (Prepare,
Execute und das `DEALLOCATE`). In einem Cluster, dessen Datenbank auf einer anderen Maschine liegt, sank so die Zeit
jeder Prüfung einer Cache-Datei auf ein Drittel. Dafür ist nichts einzustellen.

**Persistente Verbindungen.** `DBPersistentConnection=enabled` behält eine Verbindung je PHP-Worker zwischen Anfragen
und spart die Anmelde-Wege einer neuen Verbindung (mit `scram-sha-256` sind es mehrere). Zählen Sie zuerst: Jedes Kind
von PHP-FPM und jeder Velocity-Worker jedes Webservers hält eine Verbindung zur Cluster-Datenbank **und**, mit
`[DatabaseSettings] UsePersistentConnection=enabled`, eine zur Datenbank der Website. Die Summe muss unter
`max_connections` des Servers bleiben, mit Platz für Cronjobs, die Konsole und Ihre eigenen Sitzungen. PDO rollt eine
offene Transaktion am Ende einer Anfrage zurück.

**Begrenztes Warten.** Jede Verbindung setzt `lock_timeout` aus `DBLockTimeout`, keine Anweisung wartet also ewig auf
eine Zeilensperre; ein Deadlock (`40P01`), eine nicht rechtzeitig gewährte Sperre (`55P03`) und ein
Serialisierungsfehler (`40001`) werden bis zu `DBExecuteRetries`-mal wiederholt. Ein Cluster-Knoten behält und
liefert nie eine zu kurze oder gescheiterte Kopie einer Datei vom Share: Die Größe wird an der temporären Kopie
geprüft, bevor sie an ihren Platz umbenannt wird, und ein Abruf, der bei jedem Versuch scheitert, entfernt die alte
lokale Kopie.

Alles Weitere (Herkunft, was gegenüber der alten Erweiterung behoben wurde, der Umstieg von `ezpostgresqlcluster`,
die Tests) steht in [Clustering mit PostgreSQL](postgresql-dfs-cluster.md).

### Ein Primärserver und ein Streaming-Standby

Die übliche Form im Produktivbetrieb auf 18 und 19 ist ein Primärserver und mindestens ein physischer Standby, der
über Streaming-Replikation gespeist wird. Exponential schreibt nur auf den Primärserver; der Standby ist eine heiße
Reserve und mit den Einstellungen unten ein Lesereplikat für anonyme Seiten.

- **Eine Adresse für den Primärserver.** Jeder Webserver erreicht den Primärserver über eine Adresse, die bei einem
  Failover mitwandert (eine virtuelle IP, ein DNS-Name mit kurzer TTL oder ein Proxy vor der Datenbank). Die
  Einstellungen von Exponential nennen diese Adresse; ein Failover ändert also keine Einstellung.
- **Die Cluster-Metadaten** liegen ebenfalls auf dem Primärserver. Das DFS-Backend liest und schreibt immer den
  Primärserver, nie ein Replikat; ein beförderter Standby bringt seine Tabellen mit.
- **Verbindungspooler.** Das DFS-Backend setzt `client_encoding` und `lock_timeout` für seine Sitzung, und der Treiber
  der Website verlässt sich ebenfalls auf Sitzungszustand. Ein Pooler vor PostgreSQL muss Sitzungen erhalten
  (Session-Pooling) und darf eine Sitzung nicht nach jeder Transaktion einem anderen Client geben.

### Lesereplikate auf 18 und 19

`eZPostgreSQLDB` schickt eine Anweisung nur dann an einen Standby, wenn sie nicht veraltet lesen kann: ein einfaches
`SELECT` einer anonymen Webanfrage mit `GET` oder `HEAD`, die noch nichts geschrieben hat, außerhalb einer
Transaktion und nur, solange der Standby streamt. Jeder Fehler führt die Anweisung auf dem Primärserver aus. In
`settings/override/site.ini.append.php`:

```ini
[DatabaseSettings]
UseSlaveServer=enabled
SlaveServerArray[]
SlaveServerArray[]=127.0.0.1
SlaveServerPort[]
SlaveServerPort[]=5433
SlaverServerUser[]
SlaverServerUser[]=exponential
SlaverServerPassword[]
SlaverServerPassword[]=<das Passwort>
SlaverServerDatabase[]
SlaverServerDatabase[]=exponential
```

Die Schlüssel `SlaverServerUser`, `SlaverServerPassword` und `SlaverServerDatabase` sind im Kernel so geschrieben und
müssen genau so eingetragen werden. Mit mehreren Einträgen `SlaveServerArray[]` wählt jede Anfrage zufällig einen.

Auf dem Primärserver, damit ein Besucher direkt nach einer Änderung sie auf jedem Knoten sieht:

```sql
ALTER SYSTEM SET synchronous_commit = 'local';   -- nur die Datenbank der Website wartet, nicht jede Datenbank
ALTER SYSTEM SET synchronous_standby_names = '<application_name des Standbys>';
SELECT pg_reload_conf();
ALTER DATABASE exponential SET synchronous_commit = remote_apply;
GRANT pg_read_all_stats TO exponential;           -- der Treiber liest pg_stat_wal_receiver auf dem Standby
```

Ohne `pg_read_all_stats` sieht der Treiber nicht, dass der Standby streamt, und liest ihn nie. Ein Knoten, auf dessen
Replikat-Adresse nichts lauscht, liest vom Primärserver; ein Satz Einstellungen dient also jedem Knoten eines
Clusters, auch wenn nur einige einen lokalen Standby haben. Die ganze Regel und dieselbe Funktion für MySQL, MariaDB
und Oracle: [Lesereplikate](../../bc/6.0/postgresql-lesereplikat.md).

### Gemeinsamer Zustand der Caches

Zwei Caches führen eine kleine Zustandsdatei, die jeder Webserver eines Clusters teilen muss, und beide finden das
DFS-Share von selbst:

| Cache | Einstellung | Standard im Cluster |
|---|---|---|
| Der HTTP-Cache: ein Leeren auf einem Server erreicht die Seiten jedes Servers | `httpcache.ini [HttpCacheSettings] SharedStateDir` | der Ordner `exphttpcache` auf dem Share von `MountPointPath` |
| Der SQL-Abfrage-Cache im gemeinsamen Modus: ein Schreiben auf einem Server macht die Ergebnisse jedes Servers veraltet | `querycache.ini [QueryCacheSettings] SharedStateDir` | der Ordner `querycache` auf dem Share von `MountPointPath` |

Beide nehmen stattdessen einen Pfad oder `disabled`. Einzelheiten:
[Clustering in Exponential 6.0](clustering-ueberblick.md#caches-im-cluster).

## Nachweis aus dem Produktivbetrieb

Produktive Installationen wurden mit `pg_upgrade` auf PostgreSQL 18.7 aktualisiert: ein Primärserver mit einem
Streaming-Standby, der 20 Datenbanken mit 2.936 Tabellen und 59,5 Millionen Zeilen bedient. Die Datenbanken wurden nach
dem Upgrade verglichen, und alle 20 waren mit dem Stand davor identisch. Der Kernel selbst wurde auf 18 und 19 so
nachgewiesen, wie unter
[Unterstützte Versionen](#unterstützte-versionen-und-wie-die-unterstützung-getestet-wird) beschrieben.

## Bekannte Grenzen

- **MD5-Passwörter.** PostgreSQL 18 erklärt die Passwort-Authentifizierung mit `md5` für veraltet und warnt, wenn eine
  Rolle noch einen MD5-Hash hat. Setzen Sie `password_encryption = 'scram-sha-256'` und setzen Sie die Passwörter der
  Anmeldungen von Website und Cluster neu (`\password exponential` in `psql`); ändern Sie `md5` in `pg_hba.conf` zu
  `scram-sha-256`. Auf den Webservern braucht es libpq 10 oder neuer.
- **Der Client der Sicherung.** `exp:upgrade:backup`, die Prüfung des Sicherungspunkts, das Zurückrollen und
  `ezconvertdbcharset.php` brauchen ein `pg_dump`/`psql`, das mindestens so neu ist wie der Server. Installieren Sie es
  auf jedem Webserver, der sie ausführt.
- **Das Gateway verbindet sich je Anfrage.** `index_cluster.php` öffnet für jede Datei, die es ausliefert, eine neue
  Verbindung zur Cluster-Datenbank; `DBPersistentConnection` ist eine Einstellung des Backends, und das
  PostgreSQL-Gateway nutzt `CLUSTER_PERSISTENT_CONNECTION` nicht. Lassen Sie den Webserver Bilder cachen
  (`CLUSTER_ENABLE_HTTP_CACHE`) oder stellen Sie ein CDN davor.
- **Replikate sind physische Standbys.** Die Prüfung des Lesereplikats liest `pg_is_in_recovery()` und
  `pg_stat_wal_receiver`; ein logisches Replikat ist für sie ein Primärserver und würde ohne Prüfung des Rückstands
  gelesen. Nutzen Sie Streaming-Standbys.
- **Das DFS-Backend liest nur den Primärserver.** Die Metadaten der Dateien ändern sich mit jedem Schreiben in den
  Cache und werden daher nie von einem Replikat gelesen.
- **Synchrones Anwenden wartet auf den Standby.** Mit `remote_apply` warten Commits der Datenbank der Website, solange
  der Standby weg ist. Überwachen Sie den Standby und leeren Sie `synchronous_standby_names` automatisch, wenn er nicht
  mehr streamt.
- **Der Einrichtungsassistent prüft auf PostgreSQL 8.0.** Nutzen Sie trotzdem 13 oder neuer; ältere Versionen sind
  nicht getestet, und ihnen fehlt das vertrauenswürdige `pgcrypto`.

## Verwandte Seiten

- [Clustering in Exponential 6.0](clustering-ueberblick.md)
- [Clustering mit PostgreSQL: das DFS-Backend im Kernel](postgresql-dfs-cluster.md)
- [Lesereplikate: PostgreSQL, MySQL und MariaDB, Oracle](../../bc/6.0/postgresql-lesereplikat.md)
- [Ein DFS-Cluster mit einem Knoten in einem Befehl](cluster-befehle.md)
- [Datenbankunterstützung](../../hilfe/datenbankunterstuetzung.md)
- [PostgreSQL einrichten](../../hilfe/datenbankadministratoren/postgresql-einrichten.md)
- [PostgreSQL betreiben](../../hilfe/datenbankadministratoren/postgresql-betrieb.md)
- [Zwischen Engines wechseln](../../hilfe/datenbankadministratoren/zwischen-engines-wechseln.md)
- [Aktualisieren mit der Konsole](../../anleitungen/aktualisieren-mit-der-konsole.md)
- [Changelog 6.0.15](../../../changelogs/6.0/6.0.15.md)
