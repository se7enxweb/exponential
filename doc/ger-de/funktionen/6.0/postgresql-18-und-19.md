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
8. [Fehlerbehebung](#fehlerbehebung)
9. [Bekannte Grenzen](#bekannte-grenzen)
10. [Verwandte Seiten](#verwandte-seiten)

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

### Von 18 auf 19, Schritt für Schritt (pg_upgrade auf einem Server)

Mit diesem Ablauf wurden produktive Server von PostgreSQL 18.7 auf 19.0 gebracht. Er ist für RHEL und seine Nachbauten
(AlmaLinux, Rocky) mit den PGDG-Paketen geschrieben; auf Debian und Ubuntu laufen dieselben Schritte mit
`pg_upgradecluster` oder den Programmen in `/usr/lib/postgresql/19/bin`. Jeder Befehl läuft als `root` auf dem
Datenbankserver, wenn nichts anderes dasteht. Der alte Cluster wird nie verändert: Ein Zurück bleibt möglich, bis Sie ihn
selbst löschen.

**1. Das Repository.** PostgreSQL 19 braucht ein aktuelles PGDG-Repository-Paket; ältere enthalten nur
`pgdg19-updates-testing`, nicht `pgdg19`:

```bash
dnf install https://download.postgresql.org/pub/repos/yum/reporpms/EL-9-x86_64/pgdg-redhat-repo-latest.noarch.rpm
grep -c '^\[pgdg19\]' /etc/yum.repos.d/pgdg-redhat-all.repo     # muss 1 ausgeben
```

Haben Sie die Repository-Datei früher bearbeitet (etwa um alle PGDG-Repositories abzuschalten), behält `rpm` Ihre Datei
und legt die neue als `pgdg-redhat-all.repo.rpmnew` ab. Dann fehlt der Abschnitt `[pgdg19]`, und `dnf` meldet
„Unknown repo: 'pgdg19'“. Sichern Sie Ihre Datei, setzen Sie die `.rpmnew` an ihre Stelle und tragen Sie Ihre Änderungen
erneut ein. Prüfen Sie vor der ersten Installation den Schlüssel: sein Fingerabdruck ist
`D4BF 08AE 67A0 B4C7 A1DB CCD2 40BC A2B4 08B4 0D20`.

**2. Die Pakete, neben 18.** Installieren Sie Server, Contrib-Paket (für `pgcrypto`) und Client; 18 bleibt installiert
und läuft weiter:

```bash
dnf --enablerepo=pgdg19 install postgresql19 postgresql19-libs postgresql19-server postgresql19-contrib
/usr/pgsql-19/bin/postgres --version
```

Sehen Sie sich die Transaktion zuerst an (`--assumeno`): Sie darf die System-`libpq`, gegen die PHP gelinkt ist, nicht
ersetzen und nichts entfernen. Auf EL 10 braucht der Server `numactl-libs` aus dem BaseOS-Repository; schalten Sie für
diese Installation also nicht alle anderen Repositories ab.

**3. Zuerst der Client.** Machen Sie den 19er-Client zu dem, der auf `PATH` gefunden wird (`pg_dump` 19 sichert Server
13 bis 18, das ist also vor dem Server-Umstieg gefahrlos), und tun Sie dasselbe auf jedem Webserver, der Sicherungen
macht (siehe [Client-Versionen](#client-versionen-php-libpq-pg_dump-und-psql)):

```bash
for b in psql pg_dump pg_dumpall pg_restore createdb dropdb vacuumdb pg_isready; do
    ln -sfn /usr/pgsql-19/bin/$b /usr/local/bin/$b
done
pg_dump --version     # pg_dump (PostgreSQL) 19.0
```

**4. Ein neuer, leerer Cluster mit Kodierung, Locale und Prüfsummen-Einstellung des alten.** Lesen Sie sie aus dem
alten Server und dem alten Datenverzeichnis:

```bash
sudo -u postgres psql -At -c "SELECT pg_encoding_to_char(encoding), datcollate, datctype FROM pg_database WHERE datname = 'template1'"
/usr/pgsql-18/bin/pg_controldata /var/lib/pgsql/18/data | grep 'Data page checksum version'   # 0 = aus
sudo -u postgres /usr/pgsql-19/bin/initdb -D /var/lib/pgsql/19/data -E UTF8 \
    --locale=C.UTF-8 --lc-collate=C.UTF-8 --lc-ctype=C.UTF-8 --no-data-checksums
```

Übernehmen Sie die alten Werte genau. `--no-data-checksums` nur, wenn der alte Cluster keine hat: Ab 18 schaltet
`initdb` sie standardmäßig ein, und `pg_upgrade` bricht mit „old cluster does not use data checksums but the new one
does“ ab.

**5. Die Einstellungen mitnehmen.** `pg_upgrade` kopiert Daten, keine Konfiguration. Übertragen Sie, was Sie in der
alten `postgresql.conf` geändert haben (Speicher, Verbindungen, Adressen, Protokoll, Zeitzone), und kopieren Sie
`pg_hba.conf`, `pg_ident.conf` und `postgresql.auto.conf` nach dem Upgrade (Schritt 7). So entgeht Ihnen nichts: Listen
Sie, was der alte Server aus seinen Dateien übernommen hat, und prüfen Sie jeden Namen gegen 19:

```sql
SELECT name, current_setting(name) FROM pg_settings
 WHERE source = 'configuration file' AND sourcefile NOT LIKE '%postgresql.auto.conf'
   AND name NOT IN ('data_directory', 'hba_file', 'ident_file', 'config_file', 'external_pid_file');
```

```bash
sudo -u postgres /usr/pgsql-19/bin/postgres --describe-config | cut -f1 | sort > /root/pg19-params.txt   # Namen, die 19 kennt
sudo -u postgres /usr/pgsql-19/bin/postgres -D /var/lib/pgsql/19/data -C shared_buffers                  # 19 liest die neue Datei
```

Achten Sie auf `unix_socket_directories`: Die Betriebssystem-Pakete von RHEL legen den Socket nach
`/var/run/postgresql`, die PGDG-Builds nach `/run/postgresql` und `/tmp`. Behalten Sie das Verzeichnis, das Webserver
und Skripte schon benutzen.

**6. Prüfen, während 18 noch bedient.** `pg_upgrade --check` gegen den laufenden alten Server liest dessen Katalog und
ändert nichts:

```bash
cd /var/lib/pgsql/19 && sudo -u postgres /usr/pgsql-19/bin/pg_upgrade \
    -b /usr/pgsql-18/bin -B /usr/pgsql-19/bin -d /var/lib/pgsql/18/data -D /var/lib/pgsql/19/data \
    -p 5432 -P 50432 -U postgres --check --clone
```

Am Ende muss `*Clusters are compatible*` stehen. 19 fügt zwei Prüfungen hinzu (Unstimmigkeiten bei NOT-NULL-Constraints,
Verwendung von `gist_inet_ops`/`gist_cidr_ops`); eine Exponential-Datenbank besteht beide.

**7. Der Umstieg.** Zählen Sie die Zeilen jeder Tabelle genau (Schritt 3 von [Bevor Sie beginnen](#bevor-sie-beginnen)),
halten Sie den alten Server an, aktualisieren Sie, kopieren Sie die Zugriffsdateien und starten Sie 19:

```bash
systemctl stop postgresql-18
cd /var/lib/pgsql/19 && sudo -u postgres /usr/pgsql-19/bin/pg_upgrade \
    -b /usr/pgsql-18/bin -B /usr/pgsql-19/bin -d /var/lib/pgsql/18/data -D /var/lib/pgsql/19/data \
    -p 50431 -P 50432 -U postgres --clone
for f in pg_hba.conf pg_ident.conf postgresql.auto.conf; do
    install -o postgres -g postgres -m 0600 /var/lib/pgsql/18/data/$f /var/lib/pgsql/19/data/$f
done
systemctl disable postgresql-18 && systemctl enable --now postgresql-19
```

Die Übertragungsart richtet sich nach dem Dateisystem:

| Art | Wann | Dauer | Der alte Cluster danach |
|---|---|---|---|
| `--clone` | XFS mit `reflink=1`, Btrfs (beide im selben Dateisystem) | so schnell wie `--link` | unverändert und startbar |
| `--copy` | alles andere; kleine Datenbanken | proportional zur Größe | unverändert und startbar |
| `--link` | sehr große Datenbanken ohne Reflink | Sekunden | **nicht mehr startbar**, sobald 19 lief |
| `--swap` (ab 18) | das neue Datenverzeichnis darf geleert werden | Sekunden | nicht startbar |

`xfs_info / | grep reflink` zeigt, ob `--clone` geht. Auf ZFS ist Block-Cloning oft abgeschaltet
(`zfs_bclone_enabled=0`); nehmen Sie dort `--copy`.

**8. Erweiterungen und Statistiken.** Hat `pg_upgrade` eine `update_extensions.sql` geschrieben, führen Sie sie als
Superuser aus (`psql -f update_extensions.sql -d postgres`). Danach `vacuumdb --all --analyze-in-stages`. 18 und 19
übernehmen die Planer-Statistiken durch `pg_upgrade`; die Websites dürfen also wieder herein, bevor das Analyze fertig
ist.

**9. Vergleichen.** Zählen Sie erneut: Jede Tabelle, jede Sequenzposition, jede Rolle und jede Datenbank-Einstellung
(`ALTER DATABASE ... SET`, `pg_db_role_setting`) muss gleich sein. Nur Versionen von Erweiterungen dürfen abweichen.

**10. Den alten Cluster später entfernen, nicht jetzt.** `pg_upgrade` schreibt `delete_old_cluster.sh`. Behalten Sie das
alte Datenverzeichnis, bis die Website einige Tage auf 19 lief; es ist Ihr Weg zurück.

Wenn der Name der Unit für andere Software zählt (ein Hosting-Panel, Proxys mit `After=postgresql.service`), können Sie
die Unit `postgresql` behalten und sie mit einem Drop-in auf 19 richten, statt auf `postgresql-19` zu wechseln:

```ini
# /etc/systemd/system/postgresql.service.d/pg19.conf
[Service]
Environment=PGDATA=/var/lib/pgsql/19/data
ExecStartPre=
ExecStartPre=/usr/pgsql-19/bin/postgresql-19-check-db-dir ${PGDATA}
ExecStart=
ExecStart=/usr/pgsql-19/bin/postgres -D ${PGDATA}
```

Drop-ins werden in Namensreihenfolge gelesen; eine spätere Datei gewinnt. `pg19.conf` überschreibt also ein älteres
`pg18.conf`, und wer sie entfernt, bekommt 18 zurück.

### Ein Primärserver mit Streaming-Standby: die Reihenfolge

`pg_upgrade` übernimmt keine physischen Replikations-Slots, und ein Standby muss dieselbe Hauptversion wie sein
Primärserver haben. Aktualisieren Sie in dieser Reihenfolge:

1. Die 19er-Pakete auf dem Primärserver **und** auf dem Rechner des Standbys installieren.
2. Synchrone Replikation auf dem Primärserver vor dem Umstieg lösen:
   `ALTER SYSTEM SET synchronous_standby_names = ''; SELECT pg_reload_conf();`. Sonst wartet jeder Commit einer
   Datenbank mit `remote_apply` (oder des ganzen Servers bei `synchronous_commit = on`) auf einen Standby, der sich nicht
   mehr verbinden kann.
3. Den alten Standby anhalten, wenn Sie die Türen schließen (sein Datenverzeichnis behalten), dann den Primärserver
   aktualisieren (Schritte 4 bis 9). Ein 18er-Standby kann einem 19er-Primärserver nicht folgen; ohne etwas auf der
   Replikat-Adresse liest jede Website den Primärserver.
4. Den Slot auf dem neuen Primärserver anlegen: `SELECT pg_create_physical_replication_slot('<Slot-Name>');`.
5. Den Standby aus dem 19er-Primärserver in ein **neues** Datenverzeichnis aufbauen und das alte behalten:
   `pg_basebackup -d "host=<Primärserver> user=<Replikations-Login> application_name=<Name>" -D <neues Verzeichnis> -S <Slot> -X stream -R -c fast`.
   Seine eigenen Einstellungen (Port, `hot_standby_feedback`, `primary_conninfo`) kommen nach der Sicherung hinzu.
6. Sobald er streamt (`SELECT status FROM pg_stat_wal_receiver` auf dem Standby ergibt `streaming`), die synchrone
   Replikation wieder einschalten: `ALTER SYSTEM SET synchronous_standby_names = '<Name>'`.

`ALTER DATABASE ... SET synchronous_commit = remote_apply` ist eine Datenbank-Einstellung; `pg_upgrade` übernimmt sie,
sie gilt also wieder, sobald der Standby genannt ist.

### Auf einem Server mit Plesk

Plesk dokumentiert PostgreSQL bis 18.x unter Linux. Ein Plesk-Server betreibt 19 einwandfrei, mit diesen Schritten nach
dem Upgrade:

1. Plesk auf die neuen Programme und Daten richten, in `/etc/psa/psa.conf` (vorher sichern):
   `PGSQL_BIN_D /usr/pgsql-19/bin`, `PGSQL_DATA_D /var/lib/pgsql/19/data`, `PGSQL_CONF_D /var/lib/pgsql/19/data`.
2. `plesk sbin packagemng -sdf`.
3. Die Registrierung des Datenbankservers mit dem Passwort des Administrators erneuern (in der Umgebung, nicht auf der
   Befehlszeile): `PSA_PASSWORD='<Passwort>' plesk bin database-server --update-server localhost:5432 -type postgresql -admin postgres -passwd ""`.
   Die Antwort muss „Server localhost:5432 is successfully updated“ lauten.
4. Prüfen: `plesk db "SELECT host, port, server_version, last_error FROM DatabaseServers WHERE type = 'postgresql'"`
   zeigt `19.0` und `no_error`; dann im Panel eine Datenbank und einen Benutzer anlegen, sichern und wieder entfernen.

Was Sie sehen werden: Die Komponentenliste (`packagemng -l`) nennt weiter die Version des Betriebssystem-Pakets
`postgresql-server`, solange es installiert bleibt; Plesk nimmt den Server trotzdem an. Meldungen wie
`sw-engine: /lib64/libpq.so.5: no version information available` stammen von einem PGDG-Paket `libpq5`, das die libpq des
Betriebssystems ersetzt, und sind harmlos. Die Plesk-eigenen Sicherungen nutzen `PGSQL_BIN_D` und sichern nach Schritt 1
mit dem 19er-Client.

### Zurück auf 18

Möglich, solange das alte Datenverzeichnis unverändert ist (`--clone` oder `--copy`) und nichts, was auf 19 geschrieben
wurde, erhalten bleiben muss:

```bash
systemctl disable --now postgresql-19
systemctl enable --now postgresql-18        # oder das Drop-in entfernen und: systemctl daemon-reload && systemctl start postgresql
```

Auf einem Plesk-Server `psa.conf` aus der Sicherung zurückholen und `--update-server` erneut ausführen. Schreibvorgänge
auf 19 sind nicht im 18er-Cluster. Ein aus 19 neu aufgebauter Standby kann 18 nicht folgen: Starten Sie stattdessen das
alte Datenverzeichnis des Standbys, es hat seinen Slot noch auf dem 18er-Primärserver.

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
offene Transaktion am Ende einer Anfrage zurück. Das Gateway `index_cluster.php` behält seine Verbindung mit
`define( 'CLUSTER_PERSISTENT_CONNECTION', true );` in `config.cluster.php` (Standard false): Ein Worker, der Bild um
Bild ausliefert, meldet sich dann einmal an. Auf 19 belegt: Fünf Gateway-Anfragen eines Workers nutzten eine
Backend-PID, ohne die Konstante hatte jede eine neue.

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

Dieselben Installationen gingen danach auf **PostgreSQL 19.0**, mit dem Verfahren aus
[Von 18 auf 19, Schritt für Schritt](#von-18-auf-19-schritt-für-schritt-pg_upgrade-auf-einem-server), in drei Stufen.

**1. Zuerst ein einzelner Server, und Exponential dagegen nachgewiesen.** Ein Server ohne Produktivdaten ging mit
`pg_upgrade --copy` von 18.7 auf 19.0: `--check` kompatibel, 35 Einstellungen übernommen, keine davon 19 unbekannt,
3,9 Sekunden Ausfallzeit, alle Zählungen gleich, die Plesk-Registrierung auf `19.0` ohne Fehler, und eine Datenbank samt
Benutzer im Panel angelegt, gesichert und wieder entfernt. Danach lief Exponential über das Netz gegen diesen Server, so
wie ein Webserver in einem anderen Rechenzentrum ihn nutzen würde:

| Was | Ergebnis auf 19.0 |
|---|---|
| Alle Datenbank-Testsuiten, genau wie in der CI (`tests/runtests.php --dsn postgresql://... --db-per-test`) | 27.993 Tests, 845.012 Zusicherungen; kein PostgreSQL-Fehler (ein Zeit-Test schlug unter Last fehl und lief im zweiten Anlauf durch) |
| Das DFS-Cluster-Backend (`expDFSPostgresqlBackendIntegrationTest`) | 29 Tests, 161 Zusicherungen |
| Lesereplikate gegen einen 19er-Primärserver und einen 19er-Streaming-Standby | 19 Prüfungen: anonyme Lesezugriffe auf dem Standby; ein Schreibvorgang, eine Transaktion, ein POST oder ein Redakteur bindet an den Primärserver; ein angehaltener Stream fällt auf den Primärserver zurück, ein wieder laufender wird wieder gelesen; der gemeinsame SQL-Abfrage-Cache; ein Login ohne `pg_read_all_stats` liest den Primärserver |
| Eine neue Installation (`install.php --db=pgsql`) | 259 Tabellen, 323 Inhaltsobjekte |
| `exp:upgrade:backup` | gesichert mit `pg_dump` 19.0, den Exponential selbst gewählt hat |
| Die Datenbank-Stufe des Upgrades, dann ein zweiter Lauf | nichts mehr zu tun |
| Ein Wiederherstellungstest mit `psql` 19.0 in eine leere Datenbank | 323 von 323 Inhaltsobjekten |
| Inhalte | angelegt, veröffentlicht, bearbeitet, als Version 2 erneut veröffentlicht, von der Suche gefunden, nach `ezcache.php --clear-all` unverändert |
| Ein DFS-Cluster aus zwei Knoten auf 19 (`exp:cluster:enable --backend=postgresql`, ein zweiter Knoten mit gleichem Code und gleichen Einstellungen und eigenem `var/`) | ein auf einem Knoten veröffentlichtes Bild vom anderen Byte für Byte geholt; eine auf einem Knoten gespeicherte Datei auf dem anderen geholt; auf dem zweiten gelöscht und bereinigt, für den ersten verschwunden; ein Cache-Leeren im Cluster-Betrieb |

**2. Der produktive Primärserver.** Der Primärserver eines Clusters mit zehn Websites, 22 Datenbanken, 3.307 Tabellen
und 59,6 Millionen Zeilen ging in einem Fenster von 18.7 auf 19.0:

| Schritt | Dauer |
|---|---|
| Timer angehalten, synchrone Replikation gelöst, die Datenbank-Proxys angehalten (Türen zu) | Sekunden |
| Genaue Zeilenzahlen auf 18 | 204 s |
| `pg_upgrade --clone` (XFS mit Reflink) | 112 s; 19 antwortete 116 s nach dem Anhalten des alten Servers |
| Genaue Zeilenzahlen auf 19 | 51 s |
| **Ausfallzeit, Türen zu bis Türen auf** | **6 Minuten 12 Sekunden** |
| `vacuumdb --all --analyze-in-stages`, während die Websites schon liefen | 175 s |

Jede Tabelle, jede Zeilenzahl, jede Sequenzposition (2.584), jede Rolle und jede Datenbank-Einstellung war gleich;
keine Erweiterung wechselte die Version. Jede Website antwortete auf jedem Knoten und über den Load Balancer, jede
Website-Datenbank schrieb auf 19, `pg_dump` 19 sicherte jede Datenbank (die größte 846 MB komprimiert), und das
Hosting-Panel zeigte den Server als 19.0 ohne Fehler. Das alte 18er-Datenverzeichnis blieb durch `--clone` unverändert
und wird als Weg zurück aufbewahrt.

**3. Der Standby** wird aus dem 19er-Primärserver mit `pg_basebackup` in ein neues Datenverzeichnis aufgebaut (das 18er
bleibt erhalten); bis er streamt, liest jede Website den Primärserver, wie es die Regel der Lesereplikate vorsieht.

Zwei Lehren aus dem Fenster stehen im Verfahren und in der [Fehlerbehebung](#fehlerbehebung): Halten Sie den alten
Standby an, wenn die Türen schließen, denn er kann der neuen Hauptversion nicht folgen und soll nicht mehr als Replikat
angeboten werden; und prüfen Sie jede Website auf jedem Knoten direkt nach dem Öffnen: Auf einem Knoten hingen die
PHP-Prozesse einer Website (LiteSpeeds `lsphp`) ab dem Moment, in dem die Datenbank nicht erreichbar war, an einer
Sperre in PHP, und neue Prozesse desselben Elternprozesses hingen ebenso, bis die PHP-Prozesse der Website neu gestartet
wurden.

19 warnt außerdem bei jeder Anmeldung einer Rolle, die noch einen MD5-Passwort-Hash hat; das funktioniert, setzen Sie
diese Passwörter aber mit `scram-sha-256` neu (siehe [Bekannte Grenzen](#bekannte-grenzen)).

## Fehlerbehebung

| Was Sie sehen | Warum | Was zu tun ist |
|---|---|---|
| `dnf`: „Unknown repo: 'pgdg19'“ | das Repository-Paket ist älter als 19, oder seine neue Datei wurde als `.rpmnew` abgelegt, weil Sie die alte bearbeitet hatten | das aktuelle `pgdg-redhat-repo-latest` installieren und die `.rpmnew` an ihre Stelle setzen ([Schritt 1](#von-18-auf-19-schritt-für-schritt-pg_upgrade-auf-einem-server)) |
| `dnf`: „repomd.xml GPG signature verification error“ beim ersten Zugriff auf `pgdg19` | der Metadaten-Schlüssel des Repositorys ist noch nicht angenommen | Fingerabdruck prüfen, `rpm --import /etc/pki/rpm-gpg/PGDG-RPM-GPG-KEY-RHEL`, dann `dnf --enablerepo=pgdg19 makecache` und mit Ja antworten |
| `dnf` auf EL 10: nichts stellt `libnuma.so.1` bereit | `numactl-libs` liegt in BaseOS | die Repositories des Betriebssystems für die Installation nicht abschalten |
| `pg_upgrade`: „old cluster does not use data checksums but the new one does“ | ab 18 initialisiert `initdb` standardmäßig mit Prüfsummen | `initdb --no-data-checksums` neu, oder `pg_checksums --enable` auf dem angehaltenen alten Cluster |
| `pg_upgrade`: „could not load library ... pgcrypto“ | das Contrib-Paket des neuen Servers fehlt | `postgresql19-contrib` installieren |
| `pg_upgrade`: „connection to server on socket ... failed“ | es startet den alten Server auf Port 50432 mit eigenem Socket im aktuellen Verzeichnis; der Benutzer darf dort nicht schreiben, oder ein Passwort wird verlangt | aus einem Verzeichnis starten, das dem Benutzer `postgres` gehört; eine `PGPASSFILE` angeben, die `postgres` lesen darf |
| `pg_upgrade --clone`: „file cloning not supported on this platform“ | das Dateisystem kann kein Reflink (ext4, ZFS ohne Block-Cloning) | `--copy`, oder `--link`, wenn der alte Cluster danach nicht mehr starten muss |
| `pg_dump`: „aborting because of server version mismatch“ | ein `pg_dump` 18 oder älter gegen 19 | den 19er-Client auf `PATH`, oder `PostgreSQLClientDir` ([Client-Versionen](#client-versionen-php-libpq-pg_dump-und-psql)) |
| `exp:upgrade --apply`: „the caption failed, nothing was changed“ | dasselbe: der `pg_dump` der Sicherung ist zu alt | dasselbe |
| Ein Sicherungs-Timer oder die Panel-Sicherung schlägt nach dem Upgrade fehl | er ruft `/usr/bin/pg_dump` oder einen anderen festen Pfad des alten Clients | auf den 19er-Client oder `/usr/local/bin` richten |
| Jeder Commit hängt direkt nach dem Umstieg | `synchronous_standby_names` nennt einen Standby, der sich nicht mehr verbinden kann (er ist noch auf 18) | `ALTER SYSTEM SET synchronous_standby_names = ''; SELECT pg_reload_conf();`, Standby neu aufbauen, wieder einschalten |
| Der Standby auf 18 meldet „database system identifier differs“ oder „unsupported WAL format“ | ein Standby kann keinem Primärserver einer anderen Hauptversion folgen | mit `pg_basebackup` aus dem 19er-Primärserver neu aufbauen |
| `pg_basebackup`: „replication slot ... does not exist“ | `pg_upgrade` übernimmt keine physischen Slots | `SELECT pg_create_physical_replication_slot('<Slot>')` auf dem neuen Primärserver |
| Die Website liest das Replikat nach dem Neuaufbau nie | der Standby streamt noch nicht, oder dem Login der Website fehlt `pg_read_all_stats` | `SELECT status FROM pg_stat_wal_receiver` auf dem Standby; `GRANT pg_read_all_stats TO <Login>` auf dem Primärserver |
| Anmeldungen schlagen nach Sichern und Zurückspielen in einen neuen Server mit „password authentication failed“ fehl | `pg_dumpall --globals-only` wurde nicht eingespielt, oder `password_encryption` weicht ab und `pg_hba.conf` verlangt `scram-sha-256` von einem MD5-Hash | die globalen Objekte einspielen; Passwörter mit `scram-sha-256` neu setzen |
| Plesk zeigt beim Datenbankserver eine alte Version oder einen Fehler | `psa.conf` nennt noch die alten Programme, oder die Registrierung wurde nicht erneuert | die Schritte unter [Auf einem Server mit Plesk](#auf-einem-server-mit-plesk) |
| Eine Website hängt nach dem Öffnen der Türen weiter, ihre PHP-Prozesse bei 0 % CPU | PHP-Prozesse (gesehen bei LiteSpeeds `lsphp`) warten seit der Unterbrechung der Datenbank auf eine Sperre in PHP; vom selben Elternprozess gestartete Prozesse hängen ebenso | den PHP-Handler der Website neu starten (alle Prozesse samt Elternprozess); bei PHP-FPM `systemctl reload` des Pools |
| Anmeldungen protokollieren „MD5 password support is deprecated and will be removed in a future release“ | die Rolle hat noch einen MD5-Passwort-Hash (19 warnt bei jeder Anmeldung) | `SET password_encryption = 'scram-sha-256'`, das Passwort neu setzen, in `pg_hba.conf` `md5` durch `scram-sha-256` ersetzen |
| Langsame Seiten direkt nach dem Upgrade | die erweiterten Statistiken werden nicht übernommen | `vacuumdb --all --analyze-in-stages`, oder zuerst die größten Tabellen mit `ANALYZE` |
| Die Sortierung von Text sieht anders aus | Collation-Provider oder C-Bibliothek haben sich mit dem Betriebssystem geändert, nicht mit PostgreSQL | `pg_upgrade --check` meldet „objects affected by Unicode update“; die genannten Text-Indizes neu aufbauen. Mit `C.UTF-8` ändert sich die Reihenfolge nicht |

## Bekannte Grenzen

- **MD5-Passwörter.** PostgreSQL 18 erklärt die Passwort-Authentifizierung mit `md5` für veraltet und warnt, wenn eine
  Rolle noch einen MD5-Hash hat. Setzen Sie `password_encryption = 'scram-sha-256'` und setzen Sie die Passwörter der
  Anmeldungen von Website und Cluster neu (`\password exponential` in `psql`); ändern Sie `md5` in `pg_hba.conf` zu
  `scram-sha-256`. Auf den Webservern braucht es libpq 10 oder neuer.
- **Der Client der Sicherung.** `exp:upgrade:backup`, die Prüfung des Sicherungspunkts, das Zurückrollen und
  `ezconvertdbcharset.php` brauchen ein `pg_dump`/`psql`, das mindestens so neu ist wie der Server. Installieren Sie es
  auf jedem Webserver, der sie ausführt.
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
