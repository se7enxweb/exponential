# Clustering in Exponential 6.0

Diese Seite ist für Administratoren und Architekten, die eine Exponential-Website auf mehreren Webservern betreiben
oder das vorhaben. Sie erklärt die Teile eines Clusters an einer Stelle (die Anwendungsserver, das gemeinsame
Datei-Backend, den Primärserver der Datenbank und seine Replikate, die Caches, wie ein Leeren jeden Server erreicht und
wie Sie prüfen, ob der Cluster gesund ist) und verweist auf die Seite, die jeden Teil ausführlich behandelt. Alles
hier gehört zum Kernel von Exponential 6.0.15; eine Erweiterung ist nur nötig, wo eine Zeile das sagt.

## Inhalt

1. [Die Teile eines Clusters](#die-teile-eines-clusters)
2. [Anwendungsserver](#anwendungsserver)
3. [Gemeinsame Dateien: der DFS-Cluster-Datei-Handler](#gemeinsame-dateien-der-dfs-cluster-datei-handler)
4. [Dateien ausliefern: das Cluster-Gateway](#dateien-ausliefern-das-cluster-gateway)
5. [Die Datenbank: Primärserver und Replikate](#die-datenbank-primärserver-und-replikate)
6. [Caches im Cluster](#caches-im-cluster)
7. [Wie eine Änderung jeden Server erreicht](#wie-eine-änderung-jeden-server-erreicht)
8. [Zustandsprüfungen und Überwachung](#zustandsprüfungen-und-überwachung)
9. [Befehle und Wartung](#befehle-und-wartung)
10. [Den Datenbankserver unter einem laufenden Cluster aktualisieren](#den-datenbankserver-unter-einem-laufenden-cluster-aktualisieren)
11. [Einen Cluster Schritt für Schritt einrichten](#einen-cluster-schritt-für-schritt-einrichten)
12. [Alle Cluster-Seiten](#alle-cluster-seiten)

## Die Teile eines Clusters

Ein typischer Cluster: zwei oder mehr Anwendungsserver hinter einem Load Balancer, ein gemeinsamer Datenträger, ein
Primärserver der Datenbank mit einem Streaming-Standby und auf Wunsch ein gemeinsamer Cache-Server.

```text
                       Besucher und Redakteure
                                  |
                          +---------------+
                          | Load Balancer |   Zustandsprüfung: /ezinfo/is_alive
                          +---------------+
                           /             \
          +----------------------+   +----------------------+
          | Anwendungsserver 1   |   | Anwendungsserver 2   |   PHP-FPM- oder Velocity-Worker,
          |  eigenes var/        |   |  eigenes var/        |   lokale Kopien der Cache-Dateien
          +----------------------+   +----------------------+
             |       |       |          |       |       |
             |       |       +----------+-------+------------> gemeinsamer Datenträger (NFS): gespeicherte
             |       |                  |       |               Dateien, Cache-Dateien, gemeinsamer Cache-Zustand
             |       +------------------+-------+------------> Cache-Server (optional): Valkey oder Redis
             |                          |
     +-----------------+  Streaming  +-----------------+
     | Primärserver    | ----------> | Standby         |   Schreiben und Redakteure: Primärserver;
     | Website+Cluster |             | (Lesereplikat)  |   anonyme Seiten: Standby, wenn aktuell
     +-----------------+             +-----------------+
```

| Teil | Was er enthält | Von jedem Server geteilt? | Seite |
|---|---|---|---|
| Anwendungsserver | den Code, `settings/`, ein eigenes `var/` | auf jedem derselbe Code und dieselben Einstellungen, nie dasselbe `var/` | [Anwendungsserver](#anwendungsserver) |
| Gemeinsamer Datenträger | die Referenzkopie jeder gespeicherten Datei und Cache-Datei | ja, unter demselben Pfad eingehängt | [Clustering und gemeinsamer Speicher](../../hilfe/hosting/betrieb/clustering-und-gemeinsamer-speicher.md) |
| Cluster-Metadaten | eine Zeile je Datei im Cluster (`ezdfsfile`, `ezdfsfile_cache`) | ja, eine Datenbank | [Gemeinsame Dateien](#gemeinsame-dateien-der-dfs-cluster-datei-handler) |
| Datenbank der Website | Inhalte, Benutzer, in der Datenbank gespeicherte Einstellungen | ja, der Primärserver | [Die Datenbank](#die-datenbank-primärserver-und-replikate) |
| Lesereplikate | eine Kopie der Datenbank der Website | jeder Server kann sein eigenes haben | [Lesereplikate](../../bc/6.0/postgresql-lesereplikat.md) |
| Gemeinsamer Cache-Zustand | Leerungszeiten des HTTP-Caches, Schreibzeiten des SQL-Abfrage-Caches | ja, auf dem gemeinsamen Datenträger | [Caches im Cluster](#caches-im-cluster) |
| Cache-Server | Einträge von View-Cache und kompilierten INI-Dateien (optional) | ja | [sevenx_valkey_cache](erweiterungen/sevenx_valkey_cache.md) |

Ein einzelner Server braucht nichts davon, gleich wie groß er ist. Ein **Cluster mit einem Knoten** (derselbe Aufbau
auf einer Maschine) ist nützlich, wenn später ein zweiter Server dazukommt: `exp:cluster:enable` richtet ihn mit einem
Befehl ein, und der zweite Server hängt danach dasselbe Share ein und nutzt dieselben Einstellungen
([Ein DFS-Cluster mit einem Knoten in einem Befehl](cluster-befehle.md)).

## Anwendungsserver

Jeder Anwendungsserver führt denselben Code und dieselben `settings/` aus und liefert die Website über PHP-FPM hinter
Apache, nginx, LiteSpeed oder Caddy aus, oder über die persistenten Worker von Exponential Velocity. Was gleich sein
muss und was nicht:

- **Derselbe Code und dieselben Einstellungen.** Spielen Sie sie auf alle Server gemeinsam ein (`./console exp:velocity deploy`
  auf jedem). Eine INI-Änderung auf nur einem Server ist eine andere Website.
- **Ein eigenes `var/`.** Der DFS-Handler hält lokale Kopien der Cache-Dateien im `var/` jedes Servers und hält sie mit
  dem Share im Gleichklang. Teilen Sie `var/` selbst nie zwischen Servern.
- **Sitzungen.** Die mitgelieferte `site.ini [Session] Handler` ist leer und überlässt die Sitzungen PHP
  (`session.save_handler`, standardmäßig Dateien). Im Cluster muss ein angemeldeter Redakteur auf jedem Server erkannt
  werden: Nutzen Sie `Handler=ezpSessionHandlerDB` (Sitzungen in der Datenbank der Website), einen Sitzungs-Handler von
  PHP auf einem gemeinsamen Valkey- oder Redis-Server oder Sticky Sessions am Load Balancer.
- **Cronjobs** laufen auf einem Server oder auf jedem Server nur für die Teile, die für ihn lokal sind. Das Pausieren
  von Cronjobs über Server hinweg braucht `cronjob.ini [PauseSettings] Directory` in einem gemeinsamen Ordner
  ([Cronjob-Teile pausieren](cronjobs-pausieren.md)).
- **Velocity**-Worker laden Klassen und Einstellungen beim Start: Nach einer Änderung `exp:velocity deploy` auf jedem
  Server.

## Gemeinsame Dateien: der DFS-Cluster-Datei-Handler

`file.ini [ClusteringSettings] FileHandler=eZDFSFileHandler` speichert jede Datei von `var/`, die der Cluster verwaltet
(gespeicherte Bilder und Binärdateien, View-Cache, Template-Blöcke, gepackte Skripte und Stylesheets), auf dem Share,
das `[eZDFSClusteringSettings] MountPointPath` nennt, und indiziert sie in einer Metadaten-Datenbank. Ein Server liest
eine Cache-Datei von seiner eigenen Platte, solange der Index sie als aktuell führt, und holt sie vom Share, wenn
nicht. Ein Prozess erzeugt eine abgelaufene Cache-Datei neu, während die anderen warten oder die veraltete Kopie
ausliefern (Zeilen `.generating`); ein Cluster stürmt also nicht auf einmal los.

Das Metadaten-Backend wählt `[eZDFSClusteringSettings] DBBackend`. Jedes Backend gehört zum Kernel und besteht
dieselben Paritätsprüfungen:

| Datenbank | `DBBackend` | Gateway (`CLUSTER_STORAGE_BACKEND`) | Server | Seite |
|---|---|---|---|---|
| MySQL, MariaDB | `eZDFSFileHandlerMySQLiBackend` (Standard) | `dfsmysqli` | beliebig viele | [Clustering und gemeinsamer Speicher](../../hilfe/hosting/betrieb/clustering-und-gemeinsamer-speicher.md) |
| PostgreSQL 13 bis 19 | `expDFSFileHandlerPostgresqlBackend` | `dfspostgresql` | beliebig viele | [Clustering mit PostgreSQL](postgresql-dfs-cluster.md), [PostgreSQL 18 und 19](postgresql-18-und-19.md) |
| Oracle 12.1 und neuer | `expDFSFileHandlerOracleBackend` | `dfsoracle` | beliebig viele | [Clustering mit Oracle](oracle-dfs-cluster.md) |
| MongoDB 5.0 und neuer | `expDFSFileHandlerMongoDBBackend` | `dfsmongodb` | beliebig viele, ein Server oder ein Replica Set | [Clustering mit MongoDB](mongodb-dfs-cluster.md) |
| SQLite 3.24 und neuer | `expDFSFileHandlerSQLiteBackend` | `dfssqlite` | **nur eine Maschine** | [Clustering mit SQLite](sqlite-dfs-cluster.md) |

Die Datenbank der Website und die Cluster-Metadaten sind getrennte Entscheidungen: Eine Website auf MySQL kann ihre
Cluster-Metadaten in PostgreSQL halten und umgekehrt. Die Einstellungen, die auf jedem SQL-Backend zählen:

| `[eZDFSClusteringSettings]` | Ausgeliefert | Bedeutung |
|---|---|---|
| `MountPointPath` | leer | das Share, auf jedem Server derselbe Pfad |
| `DBBackend` | `eZDFSFileHandlerMySQLiBackend` | das Metadaten-Backend (oben) |
| `DBHost`, `DBPort`, `DBSocket` | `dbhost`, `3306`, leer | wo die Metadaten-Datenbank liegt; `DBSocket` (ein Socket-Pfad, bei PostgreSQL das Socket-Verzeichnis) tritt an die Stelle des Hosts |
| `DBName`, `DBUser`, `DBPassword` | `cluster`, `root`, leer | die Datenbank und ihre Anmeldung; Oracle: `DBName` ist der Connect-String; SQLite: die Datenbankdatei |
| `DBConnectRetries` | `3` | Verbindungsversuche |
| `DBExecuteRetries` | `20` | Versuche einer Anweisung, die auf einen Deadlock oder eine Sperre traf |
| `DBLockTimeout` | nicht gesetzt (10 s) | PostgreSQL, Oracle, SQLite, MongoDB: wie lange eine Anweisung auf eine Sperre wartet, bevor sie neu versucht wird |
| `DBPersistentConnection` | nicht gesetzt (aus) | PostgreSQL und Oracle: eine Verbindung je PHP-Worker zwischen Anfragen behalten |
| `MaxCopyRetries` | `5` | Versuche einer Kopie vom Share |
| `MetaDataTableNameCache` | nicht gesetzt | eine eigene Cache-Tabelle, für Installationen, die die Speicherzeilen teilen, aber nicht die Caches |

Ein Knoten behält und liefert nie eine zu kurze oder gescheiterte Kopie vom Share: Jedes Backend prüft die Größe an
der temporären Kopie, bevor es sie an ihren Platz umbenennt, und entfernt die alte lokale Kopie, wenn keine
vollständige Kopie gelingt.

Jedes Backend sendet die Cluster-Ereignisse (`file.ini [ClusterEventsSettings] ClusterEvents=enabled` mit einer
`Listener`-Klasse); ein Listener, der Metadaten in Memcached oder Redis hält, arbeitet also mit jedem von ihnen, nicht
nur mit MySQLi ([Cluster-Ereignisse](../4.7/cluster-ereignisse.md)).

`exp:cluster:enable` macht das Share für seine Gruppe beschreibbar, damit die anderen Knoten, die es über NFS als seine
Gruppe erreichen, hineinschreiben können. Hängen Sie das Share auf jedem Server mit demselben numerischen Benutzer und
derselben Gruppe ein.

## Dateien ausliefern: das Cluster-Gateway

Gespeicherte Bilder und der öffentliche Cache (gepackte Skripte und Stylesheets, Textbilder) liegen auf dem Share,
nicht auf dem Server, der die Anfrage bekam. Der Webserver schickt diese Adressen an `index_cluster.php`, das die Datei
in der Metadaten-Datenbank nachschlägt und vom Share streamt, mit Byte-Bereichen und HTTP-Caching. Seine Einstellungen
sind Konstanten in `config.cluster.php`: `CLUSTER_STORAGE_BACKEND`, `CLUSTER_STORAGE_HOST`, `CLUSTER_STORAGE_PORT`,
`CLUSTER_STORAGE_USER`, `CLUSTER_STORAGE_PASS`, `CLUSTER_STORAGE_DB`, `CLUSTER_STORAGE_CHARSET`,
`CLUSTER_MOUNT_POINT_PATH` und auf Wunsch `CLUSTER_ENABLE_HTTP_CACHE`, `CLUSTER_ENABLE_HTTP_RANGE`,
`CLUSTER_EXPIRY_TIMEOUT` und `CLUSTER_IMAGE_ALIAS_ON_DEMAND`. Auf PostgreSQL hält `CLUSTER_PERSISTENT_CONNECTION`
(Standard false) auf true gesetzt die Datenbankverbindung des Gateways zwischen Anfragen im PHP-Worker offen; ein
Worker, der viele Bilder ausliefert, meldet sich also einmal an. Zählen Sie diese Verbindungen gegen `max_connections`.

```apache
# Apache, im virtuellen Host, vor den eigenen Regeln der Website
RewriteRule ^/var/([^/]+/)?storage/images(-versioned)?/.* /index_cluster.php [PT,END]
RewriteRule ^/var/([^/]+/)?cache/(texttoimage|public)/.* /index_cluster.php [PT,END]
```

```nginx
location ~ ^/var/([^/]+/)?(storage/images(-versioned)?|cache/(texttoimage|public))/ { rewrite ^ /index_cluster.php last; }
```

Ohne die Regel für den öffentlichen Cache antwortet ein Server mit 404 für eine gepackte Datei, die ein anderer Server
erzeugt hat. Ein Bild-Alias, der noch nicht erzeugt wurde, wird an `index_image.php` weitergereicht, das ihn im
Cluster erzeugt (`CLUSTER_IMAGE_ALIAS_ON_DEMAND`, standardmäßig an; [Bild-Aliase auf Abruf](bild-aliase-auf-abruf.md)).
Die Regeln für jeden Webserver und für `.htaccess` stehen in
[Ein DFS-Cluster mit einem Knoten in einem Befehl](cluster-befehle.md).

## Die Datenbank: Primärserver und Replikate

Jeder Server schreibt auf einen **Primärserver**. Erreichen Sie ihn über eine Adresse, die bei einem Failover
mitwandert (eine virtuelle IP, ein DNS-Name mit kurzer TTL oder ein Datenbank-Proxy); ein Failover ändert dann keine
Einstellung. Die Cluster-Metadaten liegen meist auf demselben Primärserver.

Ein **Standby**, der über Streaming-Replikation gespeist wird, ist eine heiße Reserve und ab 6.0.15 ein
**Lesereplikat**: `site.ini [DatabaseSettings] UseSlaveServer=enabled` mit `SlaveServerArray[]`, `SlaveServerPort[]`,
`SlaverServerUser[]`, `SlaverServerPassword[]` und `SlaverServerDatabase[]`. Eine Regel gilt für PostgreSQL, MySQL,
MariaDB und Oracle: Eine Anweisung läuft nur dann auf dem Replikat, wenn sie ein einfaches `SELECT` einer anonymen
Webanfrage mit `GET` oder `HEAD` ist, die noch nichts geschrieben hat, außerhalb einer Transaktion und nur, solange das
Replikat aktuell ist. Redakteure, Cronjobs, Skripte und Formulare lesen immer vom Primärserver, das erste Schreiben
bindet den Rest einer Anfrage an den Primärserver, und jeder Fehler auf dem Replikat führt die Anweisung auf dem
Primärserver aus. Eine Änderung wird nie veraltet zurückgelesen:

| Datenbank | Wie „aktuell“ entschieden wird | Einstellungen |
|---|---|---|
| PostgreSQL | der Standby streamt (`pg_stat_wal_receiver`); der Primärserver wendet Commits auf dem Standby an, bevor sie zurückkehren (`synchronous_commit = remote_apply`) | `UseSlaveServer`, die Schlüssel `SlaveServer*` |
| MySQL, MariaDB | beide Replikations-Threads laufen, der Rückstand ist höchstens `SlaveServerMaxLag`, das Replikat hat die GTID-Position des Primärservers vom Beginn der Anfrage angewendet | `SlaveServerStatusCheck`, `SlaveServerMaxLag`, `SlaveServerCatchUp`, `SlaveServerCatchUpWait` |
| Oracle (ezoracle) | der Standby ist schreibgeschützt mit Apply geöffnet, und sein Apply-Rückstand liegt in `SlaveServerMaxLag`; `STANDBY_MAX_DATA_DELAY` | dieselben |
| SQLite | kein Replikat: `UseSlaveServer` wird ignoriert | keine |
| MongoDB | die eigenen Read Preferences des Replica Sets; `UseSlaveServer` wird nicht gelesen | keine |

Ein Server ohne Replikat unter der eingetragenen Adresse liest vom Primärserver; ein Satz Einstellungen dient also
jedem Server, auch wenn nur einige einen lokalen Standby haben. Das DFS-Metadaten-Backend nutzt immer den
Primärserver. Einzelheiten: [Lesereplikate](../../bc/6.0/postgresql-lesereplikat.md); auf PostgreSQL 18 und 19:
[PostgreSQL 18 und 19](postgresql-18-und-19.md#ein-cluster-auf-18-und-19).

## Caches im Cluster

| Cache | Wo er im Cluster liegt | Was ihn auf jedem Server richtig hält |
|---|---|---|
| View-Cache, Template-Blöcke, andere Cache-Dateien | das Share, mit lokalen Kopien auf jedem Server | die DFS-Metadaten: Eine abgelaufene Datei ist für jeden Server zugleich abgelaufen |
| Rollenabhängiger HTTP-Cache (`httpcache.ini`) | das eigene `var/<site>/cache/exphttpcache/` jedes Servers | `[HttpCacheSettings] SharedStateDir`: Jedes Leeren wird auch in eine Zustandsdatei geschrieben, die alle Server lesen |
| SQL-Abfrage-Cache, gemeinsamer Modus (`querycache.ini`) | Ergebnisse: APCu oder eigener Ordner jedes Servers | `[QueryCacheSettings] SharedStateDir`: die Zeit des letzten Schreibens jeder Tabelle, in einer Datei, die alle Server lesen |
| Antwort-Cache von Velocity | jeder Velocity-Server | im Cluster nimmt er die gemeinsame Zustandsdatei des HTTP-Caches als Generationsmarke |
| Gepackte Skripte und Stylesheets | das Share | über das Gateway ausgeliefert (die Regel für den öffentlichen Cache oben) |
| Valkey oder Redis (Erweiterung `sevenx_valkey_cache`) | ein Cache-Server für alle Anwendungsserver | ein Server für alle; `InstallationHash` auf eine feste Zeichenkette gesetzt, damit jeder Server dieselben Schlüssel nutzt |

Beide Einstellungen `SharedStateDir` arbeiten gleich:

```ini
# settings/override/httpcache.ini.append.php        (und querycache.ini.append.php, [QueryCacheSettings])
[HttpCacheSettings]
# leer (Standard): mit eZDFSFileHandler ein Ordner auf seinem Share (exphttpcache, querycache) unter MountPointPath;
# auf einem einzelnen Server wird nichts geteilt
SharedStateDir=
# oder ein Ordner, den jeder Server einhängt, absolut oder relativ zur Installation
#SharedStateDir=/mnt/shared/mysite/exphttpcache
# oder nie teilen
#SharedStateDir=disabled
```

Es kostet ein kleines Lesen einer Datei je Anfrage. Für den Abfrage-Cache muss die Sperrdatei `state.lock` auf dem
Share von jedem Benutzer sperrbar sein, unter dem die Website läuft (NFSv4 oder NFSv3 mit `lockd`); ein Root-Prozess
auf einem Share, das Root herabstuft, kann sie nicht schreiben. Ein fehlender oder unlesbarer gemeinsamer Zustand
leert nichts und bricht nichts. Seiten:
[Der rollenabhängige HTTP-Cache](../../bc/6.0/httpcache.md#mehrere-webserver-ein-cluster),
[Der SQL-Abfrage-Cache](../../bc/6.0/sql-abfrage-cache.md),
[Antwort-Cache von Velocity](velocity-antwort-cache.md),
[sevenx_valkey_cache](erweiterungen/sevenx_valkey_cache.md),
[Valkey und Redis](../../hilfe/hosting/leistung/valkey-und-redis.md).

## Wie eine Änderung jeden Server erreicht

Ein Redakteur veröffentlicht auf Server 1. Was die anderen Server sehen, und wann:

1. **Die Datenbank.** Die neue Version steht beim Commit im Primärserver. Mit synchronem Anwenden (PostgreSQL) oder dem
   GTID-Aufholen (MySQL, MariaDB) liefert auch ein Lesereplikat sie ab der nächsten Anfrage.
2. **Cache-Dateien.** Der View-Cache der geänderten Knoten wird in den Cluster-Metadaten als abgelaufen markiert; jeder
   Server sieht das beim nächsten Lesen und holt die Datei oder erzeugt sie neu.
3. **HTTP-Cache und Velocity.** Das Leeren wird in den gemeinsamen Zustand geschrieben; jeder Server behandelt die
   geleerten Seiten ab seiner nächsten Anfrage als veraltet. Vor 6.0.15 blieb eine Änderung auf den anderen Servern
   unsichtbar, bis ihre Kopie ablief (bis zu `MaxAge`, standardmäßig eine Stunde).
4. **SQL-Abfrage-Cache.** Die Schreibzeit der geänderten Tabellen wird in den gemeinsamen Zustand geschrieben;
   Ergebnisse jedes Servers, die diese Tabellen lasen, sind ab seiner nächsten Anfrage veraltet.
5. **Valkey oder Redis.** Der Handler der Erweiterung leert die View- und Blockeinträge, die unter den geänderten
   Knoten-IDs indiziert sind, auf dem einen gemeinsamen Server.

Caches von Hand leeren: `php bin/php/ezcache.php --clear-tag=content --allow-root-user` (oder `./console exp:cache`)
markiert die Cache-Dateien in den Cluster-Metadaten als abgelaufen und wirkt damit für alle Server zugleich; lokale
Dinge wie OPcache, APCu und die Worker von Velocity brauchen `exp:velocity deploy` auf jedem Server.

## Zustandsprüfungen und Überwachung

| Was | Wie |
|---|---|
| Ein Server und seine Datenbank | `GET /ezinfo/is_alive` antwortet `Exponential is alive` (200, `text/plain`), wenn die Datenbank verbunden ist, und `No connection`, wenn nicht. Richten Sie die Zustandsprüfung des Load Balancers darauf; prüfen Sie den Text, nicht nur den Statuscode ([ezinfo/is_alive](../../bc/6.0/ezinfo-ist-erreichbar.md)). |
| Das Share | Überwachen Sie den Mount auf jedem Server: Fällt er weg, kann kein Server mehr Dateien lesen. `exp:cluster:enable` legt eine Markierungsdatei `.exp-cluster.json` in das Share; ob sie lesbar ist, ist ein günstiger Test des Mounts. |
| Die Cluster-Metadaten | Sie liegen auf dem Weg jedes Dateizugriffs: dieselben Sicherungen und dieselbe Überwachung wie die Datenbank der Website. |
| Übereinstimmung von Share und Metadaten | `php bin/php/dfscleanup.php -S` und `-B` (nur Bericht) nach einem Vorfall und vor einer Migration. |
| Replikation von PostgreSQL | auf dem Primärserver `pg_stat_replication` (`state = streaming`, `sync_state = sync` mit synchronem Anwenden); auf dem Standby `pg_stat_wal_receiver`. |
| Replikation von MySQL und MariaDB | `SHOW REPLICA STATUS` auf dem Replikat. |
| Welcher Cache antwortete | `X-Exp-Cache: HIT`, `STALE` oder `MISS (Grund)` auf jeder Antwort des HTTP-Caches. |

Velocity prüft seine eigenen Worker und seine Cluster-Partner über einen eigenen Endpunkt; es nutzt `ezinfo/is_alive`
nicht.

## Befehle und Wartung

| Befehl | Was er tut |
|---|---|
| `./console exp:cluster:enable` / `exp:cluster:disable` | eine Installation zu einem DFS-Cluster mit einem Knoten machen und zurück (Share, Tabellen in der Datenbank der Website, Einstellungen, Dateien verschoben), ein Probelauf ohne `--apply`; `--backend` (`mysqli`, `postgresql`, `sqlite`, `mongodb`, `oracle`), `--share`, `--no-move` ([Seite](cluster-befehle.md)) |
| `php bin/php/clusterize.php` (`./console exp:clusterize`) | die Dateien von `var/` in den Cluster verschieben; `-u` verschiebt sie zurück ([Beispiel](../../beispiele/befehle/clusterize.md)) |
| `php bin/php/clusterpurge.php` (`./console exp:clusterpurge`) | abgelaufene Cluster-Dateien physisch entfernen, ein Probelauf ohne `--apply`; auch der Cronjob-Teil `cluster_maintenance`, den `cronjob.ini [AdminSettings] ForbiddenParts[]` von den Schaltflächen der Administration fernhält ([Beispiel](../../beispiele/befehle/clusterpurge.md)) |
| `php bin/php/dfscleanup.php` | das Share mit den Metadaten vergleichen (`-S`, `-B`), löschen, was nicht existiert (`-D`) |
| `./console exp:upgrade:backup` | die Datenbanksicherung des Upgrades, für sich allein; auf PostgreSQL mit einem Client, der mindestens so neu ist wie der Server ([PostgreSQL 18 und 19](postgresql-18-und-19.md#client-versionen-php-libpq-pg_dump-und-psql)) |

Führen Sie `clusterpurge.php` nach Zeitplan von **einem** Server aus.

## Den Datenbankserver unter einem laufenden Cluster aktualisieren

Ein Hauptversions-Upgrade des Datenbankservers (etwa PostgreSQL 18 auf 19) betrifft jeden Server des Clusters zugleich,
denn alle schreiben auf denselben Primärserver, und dort liegen auch die DFS-Metadaten. Die Reihenfolge, die die
Ausfallzeit kurz hält und die Daten nachweislich gleich lässt (das ganze Verfahren für PostgreSQL mit jedem Befehl:
[Von 18 auf 19, Schritt für Schritt](postgresql-18-und-19.md#von-18-auf-19-schritt-für-schritt-pg_upgrade-auf-einem-server)):

1. **Zuerst die Clients, auf jedem Server.** Installieren Sie den Client der neuen Hauptversion (`pg_dump`, `psql`) auf
   jedem Anwendungsserver und auf den Datenbankservern und machen Sie ihn zu dem auf `PATH`, oder setzen Sie
   `upgrade.ini [UpgradeSettings] PostgreSQLClientDir`. Ein neuerer Client arbeitet mit dem alten Server; ein älterer
   verweigert den neuen, und Sicherungen, die Sicherung vor dem Upgrade und Timer, die Datenbanken sichern, würden nach
   dem Umstieg fehlschlagen.
2. **Ohne Ausfallzeit vorbereiten.** Den neuen Server neben dem alten installieren, sein Datenverzeichnis mit Kodierung,
   Locale und Prüfsummen-Einstellung des alten anlegen und `pg_upgrade --check` gegen den laufenden Server ausführen.
3. **Anhalten, was von selbst schreibt:** Sicherungs-Timer, Cronjobs auf dem Datenbankserver, die Replikat-Überwachung.
4. **Synchrone Replikation lösen** (`synchronous_standby_names = ''`): Der Standby kann der neuen Hauptversion nicht
   folgen, und Commits würden auf ihn warten.
5. **Die Türen schließen.** Halten Sie den einen Weg an, auf dem jeder Anwendungsserver die Datenbank erreicht (einen
   Datenbank-Proxy, eine virtuelle IP oder die Firewall-Regel), damit während des Umstiegs nichts schreibt. Websites mit
   HTTP-Cache liefern weiter zwischengespeicherte Seiten aus; Anfragen, die die Datenbank brauchen, bekommen für diese
   Zeit eine Fehlerseite.
6. **Zählen, aktualisieren, wieder zählen.** Genaue Zeilenzahlen jeder Tabelle und jede Sequenzposition vorher und
   nachher; auf einem Dateisystem mit Reflinks braucht `pg_upgrade --clone` auch für viele Gigabyte nur Minuten und lässt
   den alten Cluster startbar. Nur Versionen von Erweiterungen dürfen abweichen.
7. **Die Türen öffnen**, dann Statistiken sammeln (`vacuumdb --all --analyze-in-stages`), während die Websites schon
   laufen.
8. **Den Standby neu aufbauen** aus dem neuen Primärserver (ein neuer Slot, `pg_basebackup`) und die synchrone
   Replikation wieder einschalten, sobald er streamt. Bis dahin liest jeder Anwendungsserver den Primärserver: Die Regel
   der Lesereplikate verlangt einen streamenden Standby, es wird also nichts veraltet gelesen.
9. **Jeden Server prüfen, nicht nur den Load Balancer:** Startseite und eine nicht zwischengespeicherte Suche auf jedem
   Knoten, eine Bearbeitung auf einem Knoten sofort auf den anderen sichtbar, ein auf einem Knoten hochgeladenes Bild
   von jedem Knoten Byte für Byte ausgeliefert, alle Zustandsprüfungen des Load Balancers grün.

Behalten Sie das alte Datenverzeichnis, bis der Cluster einige Tage auf der neuen Version lief: Es ist zusammen mit dem
alten Datenverzeichnis des Standbys der Weg zurück.

## Einen Cluster Schritt für Schritt einrichten

1. Wählen Sie die Metadaten-Datenbank (Tabelle oben) und das Share (NFS, auf jedem Server unter demselben Pfad
   eingehängt, mit demselben numerischen Benutzer und derselben Gruppe).
2. Auf dem ersten Server: `./console exp:cluster:enable --share=/mnt/exp-dfs/example.com`, um den Plan zu sehen, dann
   dasselbe mit `--apply`. Es legt das Share an, die Tabellen in der Datenbank der Website, die Einstellungen in
   `settings/override/file.ini.append.php` und `config.cluster.php`, und verschiebt die Dateien hinein. Für eine
   eigene Metadaten-Datenbank schreiben Sie die Einstellungen selbst, wie die Seiten der Backends zeigen.
3. Tragen Sie die Gateway-Regeln in den Webserver ein und laden Sie ihn neu.
4. Richten Sie die Sitzungen für mehrere Server ein (`[Session] Handler`) und das Lesereplikat, falls vorhanden.
5. Nehmen Sie den zweiten Server dazu: derselbe Code, dieselben `settings/override` und `config.cluster.php`, das
   Share eingehängt, ein eigenes `var/`, dieselben Gateway-Regeln; dort `exp:velocity deploy`.
6. Stellen Sie beide hinter den Load Balancer, mit der Zustandsprüfung auf `/ezinfo/is_alive`.
7. Prüfen Sie: auf einem Server veröffentlichen und die Seite auf dem anderen laden (der HTTP-Cache muss sie sofort
   zeigen); ein Bild über jeden Server laden; `dfscleanup.php -S` und `-B` melden keine Unterschiede.

## Alle Cluster-Seiten

**Funktionen des Kernels (6.0)**

- [PostgreSQL 18 und 19](postgresql-18-und-19.md): Versionen, Clients, Aktualisieren, ein Cluster auf 18 und 19
- [Clustering mit PostgreSQL: das DFS-Backend im Kernel](postgresql-dfs-cluster.md)
- [Clustering mit Oracle: das DFS-Backend im Kernel](oracle-dfs-cluster.md)
- [Clustering mit MongoDB: das DFS-Backend im Kernel](mongodb-dfs-cluster.md)
- [Clustering mit SQLite: das Ein-Knoten-DFS-Backend im Kernel](sqlite-dfs-cluster.md)
- [Ein DFS-Cluster mit einem Knoten in einem Befehl](cluster-befehle.md): `exp:cluster:enable` und `exp:cluster:disable`
- [Bild-Aliase auf Abruf](bild-aliase-auf-abruf.md): das Gateway reicht fehlende Aliase an `index_image.php` weiter
- [Antwort-Cache von Velocity](velocity-antwort-cache.md)
- [sevenx_valkey_cache: Cache-Backend für Redis und Valkey](erweiterungen/sevenx_valkey_cache.md)

**Verhalten und Einstellungen (6.0)**

- [Lesereplikate: PostgreSQL, MySQL und MariaDB, Oracle](../../bc/6.0/postgresql-lesereplikat.md)
- [Der rollenabhängige HTTP-Cache](../../bc/6.0/httpcache.md): mehrere Webserver
- [Der SQL-Abfrage-Cache](../../bc/6.0/sql-abfrage-cache.md): gemeinsamer Modus im Cluster
- [ezinfo/is_alive](../../bc/6.0/ezinfo-ist-erreichbar.md)

**Hilfe und Betrieb**

- [Clustering und gemeinsamer Speicher](../../hilfe/hosting/betrieb/clustering-und-gemeinsamer-speicher.md)
- [Clustering: DFS und seine Metadaten-Datenbank](../../hilfe/datenbankadministratoren/clustering-dfs-und-die-datenbank.md)
- [Lesereplikate](../../hilfe/verwaltung/betrieb/verfuegbarkeit/lese-replikate.md)
- [Statisches Veröffentlichen unter Windows und in Clustern](../../anleitungen/statisches-veroeffentlichen-unter-windows-und-in-clustern.md)
- [Datenbankunterstützung](../../hilfe/datenbankunterstuetzung.md)

**Geschichte**

- [Konfigurierbares DFS-Cluster-Backend (5.4)](../5.4/konfigurierbares-dfs-cluster-backend.md)
- [Cluster-Ereignisse (4.7)](../4.7/cluster-ereignisse.md)
