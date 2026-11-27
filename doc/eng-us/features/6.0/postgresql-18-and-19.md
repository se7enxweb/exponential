# PostgreSQL 18 and 19

This page is for administrators and database administrators who run Exponential on PostgreSQL 18 or 19, plan to
upgrade an existing installation to one of them, or run a cluster of web servers on them. From Exponential 6.0.15
PostgreSQL 13 to 19 are supported and tested; 18 and 19, the two newest major releases, are covered by every database
test suite, by the cluster backend tests and by production upgrades. This page says how support is tested, which
client programs a site needs, how to upgrade, how to run a cluster on 18 and 19 with the exact settings, and what the
known limits are.

## Contents

1. [In short](#in-short)
2. [Supported versions and how support is tested](#supported-versions-and-how-support-is-tested)
3. [Client versions: PHP, libpq, pg_dump and psql](#client-versions-php-libpq-pg_dump-and-psql)
4. [A new installation on 18 or 19](#a-new-installation-on-18-or-19)
5. [Upgrading an existing installation](#upgrading-an-existing-installation)
6. [Running a cluster on 18 and 19](#running-a-cluster-on-18-and-19)
7. [Proof from production](#proof-from-production)
8. [Troubleshooting](#troubleshooting)
9. [Known limits](#known-limits)
10. [Databases that speak PostgreSQL's protocol](#databases-that-speak-postgresqls-protocol)
11. [Related pages](#related-pages)

## In short

| | |
|---|---|
| Supported | PostgreSQL 13, 14, 15, 16, 17, 18 and 19, for the site's database and for the DFS cluster metadata. 13 is the minimum (`pgcrypto` is a trusted extension from 13 on). |
| Tested on every push | The database test suites on PostgreSQL 13, 15, 18 and 19; the DFS cluster backend on 13 to 19. |
| Checked by hand on 18.7 and 19.0 | Install, the upgrade backup, the database stage of the upgrade, a restore drill, the schema round trip, the MySQL to PostgreSQL conversion, the DFS cluster backend, the read replica with a streaming standby and the SQL query cache. |
| Client rule | `pg_dump` and `psql` must be at least as new as the server. Exponential finds such a client itself; `upgrade.ini [UpgradeSettings] PostgreSQLClientDir` names one. |
| PHP | The extensions `pgsql` (the site's driver `eZPostgreSQLDB`) and `pdo_pgsql` (the DFS cluster backend and its gateway) on every web server. |
| Cluster | The kernel's DFS backend `expDFSFileHandlerPostgresqlBackend`, a primary and a streaming standby, and read replicas through `[DatabaseSettings] UseSlaveServer`. |

## Supported versions and how support is tested

Exponential needs nothing from PostgreSQL that a newer major release takes away: the driver uses plain SQL, sequences
named `<table>_<column>_seq` and `digest()` from `pgcrypto`; the DFS cluster backend uses `INSERT ... ON CONFLICT`,
`FOR UPDATE SKIP LOCKED`, `lock_timeout` and `text_pattern_ops` indexes, all there from 13 on. Support is not assumed
from that, it is tested:

**Continuous integration** (`.github/workflows/phpunit.yml`, on every push):

| Job | PostgreSQL | What runs |
|---|---|---|
| Database tests (postgresql 13, 15, 18 and 19, PHP 8.3) | `postgres:13`, `postgres:15`, `postgres:18`, `postgres:19` | Every database test suite (`tests/runtests.php --dsn ... --db-per-test`) against a fresh database with `pgcrypto`. 13 is the oldest supported release, 15 the one the suites ran on alone before, 18 and 19 the two newest. Each leg installs the PGDG client of its own server, because the caption and backup checks dump the test database and `pg_dump` refuses a newer server. |
| DFS cluster backends (PostgreSQL 13 to 19, PHP 8.3; 18 also on PHP 8.5) | every major from 13 to 19 | The parity checks every cluster backend passes (`expDFSPostgresqlBackendIntegrationTest`), the gateway, the index use of prefix searches and the upgrade of tables made by the old extension. |

**Checked by hand on 18.7 and 19.0**, beyond the suites: a new install, `exp:upgrade:backup`, the database stage of
`exp:upgrade`, a restore drill (dump, restore into a new database, compare), the schema round trip
(`ezsqldumpschema.php` and `ezsqlinsertschema.php`), the conversion of a MySQL database to PostgreSQL (byte for byte
the same output as on 13), the DFS cluster backend with its gateway, the read replica on a streaming standby with
synchronous apply, and the SQL query cache in shared mode.

The rest of the support matrix, for every engine, is in [Database support](../../help/database-support.md).

## Client versions: PHP, libpq, pg_dump and psql

Three kinds of client talk to the server. Only the third has a strict version rule.

| Client | Who uses it | Version rule |
|---|---|---|
| PHP `pgsql` (libpq) | the site's driver `eZPostgreSQLDB` (`pg_connect`, `pg_pconnect`) | Any libpq your PHP build links works with a newer server. libpq 10 or later is needed for `scram-sha-256` passwords, which PostgreSQL 14 and later use by default. |
| PHP `pdo_pgsql` (libpq) | the DFS cluster backend and its gateway `index_cluster.php` | The same as `pgsql`. |
| `pg_dump`, `pg_restore`, `psql` | the upgrade backup, the caption check, the rollback, the migration diff and `ezconvertdbcharset.php` | **At least the server's major version.** `pg_dump` refuses a newer server ("aborting because of server version mismatch"). |

A web server that kept the PostgreSQL 13 client after its database server moved to 18 can still serve the site, but it
cannot make a backup with that client, and `exp:upgrade --apply` would stop at its caption before its first write
("the caption failed, nothing was changed"). From 6.0.15 Exponential picks the client itself
(`expUpgradeBackup::postgresqlTool()`), taking the first of these whose major version is at least the server's:

1. `settings/upgrade.ini [UpgradeSettings] PostgreSQLClientDir`, a `bin` directory, when it is set;
2. the program on `PATH`;
3. the versioned installations, newest major first: `/usr/pgsql-<major>/bin` (PGDG packages for RHEL and its
   rebuilds), `/usr/lib/postgresql/<major>/bin` (Debian and Ubuntu), `/opt/exp-pg<major>/current/bin`,
   `/usr/local/pgsql/bin`.

Without one new enough it uses the newest it found, and `pg_dump` names the mismatch itself. To pin the client:

```ini
# settings/override/upgrade.ini.append.php
[UpgradeSettings]
PostgreSQLClientDir=/usr/pgsql-18/bin
```

Install the client of the server's major version next to the old one; you do not have to remove the old one:

```bash
# RHEL, Rocky, AlmaLinux with the PGDG repository
dnf install postgresql18
# Debian and Ubuntu with the PGDG repository
apt install postgresql-client-18
```

Then check it:

```bash
./console exp:upgrade:backup                 # dumps the database with the client it chose
./console exp:upgrade:backup --format=json   # the same, as machine output
```

## A new installation on 18 or 19

Nothing differs from earlier releases; [PostgreSQL setup](../../help/database-administrators/postgresql-setup.md) and
[9.4 PostgreSQL](../../install/09-databases.md#94-postgresql) have every step. In short:

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
Password=<the password>
Database=exponential
UsePersistentConnection=disabled
```

The site's user must **own** the database: from PostgreSQL 15 on, other users may not create tables in its `public`
schema. `pgcrypto` comes with the server's contrib package (`postgresql18-contrib` on PGDG RPM systems; it is part of
the server package on Debian). The installer creates it when it can; the setup wizard says "The 'digest' function is
not available in your database" when it cannot.

`Server` is passed to libpq as `host`, so a path is a socket directory: `Server=/run/postgresql` connects through the
Unix socket when the database runs on the web server itself.

## Upgrading an existing installation

A major upgrade of PostgreSQL (13 to 18, 16 to 19, and so on) changes the server, not Exponential: the site's schema
and data move as they are, and no Exponential upgrade step is needed for it. Plan it like any database change.

### Before you start

1. **Install the new server's client on every web server** (see [Client versions](#client-versions-php-libpq-pg_dump-and-psql))
   and set `PostgreSQLClientDir` if it is not on the standard paths.
2. **Take a backup with the tools you will restore with:** `./console exp:upgrade:backup` (a `pg_dump` into
   `var/backup/upgrade/<time>/`, and a copy of `settings/override`), or your own `pg_dump -Fc` with the new client.
3. **List what the database uses:**

   ```sql
   SELECT extname, extversion FROM pg_extension;   -- pgcrypto, and plpgsql; anything else needs the new server's package too
   SELECT count(*) FROM pg_tables WHERE schemaname = 'public';
   ```

   Keep a row count of every table, so you can compare after the upgrade (a script that loops over `pg_tables` and
   runs `SELECT count(*)` is enough).
4. **Put the site into maintenance**, so no editor writes during the move:
   `php bin/php/maintenance.php on --message="Database upgrade" --allow-root-user`.

### Path 1: pg_upgrade (fast, for large databases)

`pg_upgrade` moves the data files of the old cluster to the new major version in minutes, also for databases of many
gigabytes. Follow PostgreSQL's [pg_upgrade documentation](https://www.postgresql.org/docs/current/pgupgrade.html); the
points that matter for an Exponential site:

- Install the new server **and its contrib package** first: `pg_upgrade` refuses to run when an extension the old
  cluster uses (`pgcrypto`) has no library on the new server.
- **Data checksums.** From PostgreSQL 18 on, `initdb` turns data checksums on by default, and `pg_upgrade` needs both
  clusters to match. When the old cluster has no checksums, initialise the new one with `--no-data-checksums` (or turn
  checksums on in the old cluster first with `pg_checksums --enable` while it is stopped).
- Run `pg_upgrade --check` first; it changes nothing and lists what would stop the upgrade.
- After the upgrade, gather statistics: PostgreSQL 18 and later keep the planner statistics through `pg_upgrade`, but
  not extended statistics, and earlier target versions keep none. `vacuumdb --all --analyze-in-stages` does both.
- Update the extensions to the new server's version: `ALTER EXTENSION pgcrypto UPDATE;` in the site's database.
- **A streaming standby** cannot follow a `pg_upgrade` of its primary on its own. Either rebuild it from the upgraded
  primary with `pg_basebackup`, or use the `rsync` procedure in PostgreSQL's `pg_upgrade` documentation. Exponential
  reads the primary while a replica is missing (see [Read replicas](#read-replicas-on-18-and-19)), so the site runs
  while the standby is rebuilt.

### Path 2: dump and restore (simple, for small and medium databases)

Dump with the **new** client against the old server, restore into the new server:

```bash
/usr/pgsql-18/bin/pg_dump -h old-db -U exponential -Fc exponential > exponential.dump
/usr/pgsql-18/bin/createdb -h new-db -U postgres --owner=exponential --encoding=UTF8 --template=template0 exponential
/usr/pgsql-18/bin/psql -h new-db -U postgres -d exponential -c 'CREATE EXTENSION IF NOT EXISTS pgcrypto;'
/usr/pgsql-18/bin/pg_restore -h new-db -U exponential -d exponential --no-owner exponential.dump
/usr/pgsql-18/bin/vacuumdb -h new-db -U exponential --analyze-in-stages exponential
```

The dump carries the sequences' current values, so nothing needs correcting after a full restore. If you restored
only some tables, correct their sequences as
[Operating PostgreSQL](../../help/database-administrators/postgresql-operations.md#correcting-sequences) shows.

### From 18 to 19, step by step (pg_upgrade on one server)

This is the procedure that moved production servers from PostgreSQL 18.7 to 19.0, written for RHEL and its rebuilds
(AlmaLinux, Rocky) with the PGDG packages; on Debian and Ubuntu the same steps use `pg_upgradecluster` or the
`/usr/lib/postgresql/19/bin` programs. Every command runs on the database server as `root` unless it says otherwise.
The old cluster is never changed: rollback stays possible until you delete it yourself.

**1. The repository.** PostgreSQL 19 needs a current PGDG repository package; older ones carry only
`pgdg19-updates-testing`, not `pgdg19`:

```bash
dnf install https://download.postgresql.org/pub/repos/yum/reporpms/EL-9-x86_64/pgdg-redhat-repo-latest.noarch.rpm
grep -c '^\[pgdg19\]' /etc/yum.repos.d/pgdg-redhat-all.repo     # must print 1
```

If you edited the repository file before (for example to disable every PGDG repository), `rpm` keeps your file and
writes the new one as `pgdg-redhat-all.repo.rpmnew`. Then there is no `[pgdg19]` section and `dnf` says
"Unknown repo: 'pgdg19'". Keep a copy of your file, move the `.rpmnew` into place and make your edits again.
Check the key before the first install: its fingerprint is `D4BF 08AE 67A0 B4C7 A1DB CCD2 40BC A2B4 08B4 0D20`.

**2. The packages, next to 18.** Install the server, its contrib package (for `pgcrypto`) and the client; 18 stays
installed and keeps running:

```bash
dnf --enablerepo=pgdg19 install postgresql19 postgresql19-libs postgresql19-server postgresql19-contrib
/usr/pgsql-19/bin/postgres --version
```

Look at the transaction first (`--assumeno`): it must not replace the system `libpq` that PHP links, and must not
remove anything. On EL 10 the server needs `numactl-libs` from the BaseOS repository, so do not disable every other
repository for this install.

**3. The client first.** Make the 19 client the one found on `PATH` (`pg_dump` 19 dumps 13 to 18 servers, so this is
safe before the server moves), and do the same on every web server that makes backups (see
[Client versions](#client-versions-php-libpq-pg_dump-and-psql)):

```bash
for b in psql pg_dump pg_dumpall pg_restore createdb dropdb vacuumdb pg_isready; do
    ln -sfn /usr/pgsql-19/bin/$b /usr/local/bin/$b
done
pg_dump --version     # pg_dump (PostgreSQL) 19.0
```

**4. A new, empty cluster with the old one's encoding, locale and checksum setting.** Read them from the old server
and the old data directory:

```bash
sudo -u postgres psql -At -c "SELECT pg_encoding_to_char(encoding), datcollate, datctype FROM pg_database WHERE datname = 'template1'"
/usr/pgsql-18/bin/pg_controldata /var/lib/pgsql/18/data | grep 'Data page checksum version'   # 0 = off
sudo -u postgres /usr/pgsql-19/bin/initdb -D /var/lib/pgsql/19/data -E UTF8 \
    --locale=C.UTF-8 --lc-collate=C.UTF-8 --lc-ctype=C.UTF-8 --no-data-checksums
```

Use the old values exactly. `--no-data-checksums` only when the old cluster has none: from 18 on `initdb` turns them
on by default, and `pg_upgrade` stops with "old cluster does not use data checksums but the new one does".

**5. Carry the settings.** `pg_upgrade` copies data, not configuration. Bring over what you changed in the old
`postgresql.conf` (memory, connections, listen addresses, logging, time zone), and copy `pg_hba.conf`,
`pg_ident.conf` and `postgresql.auto.conf` after the upgrade (step 7). A way that misses nothing: list what the old
server took from its files and check each name against 19:

```sql
SELECT name, current_setting(name) FROM pg_settings
 WHERE source = 'configuration file' AND sourcefile NOT LIKE '%postgresql.auto.conf'
   AND name NOT IN ('data_directory', 'hba_file', 'ident_file', 'config_file', 'external_pid_file');
```

```bash
sudo -u postgres /usr/pgsql-19/bin/postgres --describe-config | cut -f1 | sort > /root/pg19-params.txt   # names 19 knows
sudo -u postgres /usr/pgsql-19/bin/postgres -D /var/lib/pgsql/19/data -C shared_buffers                  # 19 reads the new file
```

Mind `unix_socket_directories`: the OS packages of RHEL put the socket in `/var/run/postgresql`, the PGDG builds in
`/run/postgresql` and `/tmp`. Keep the directory the web servers and scripts already use.

**6. Check, while 18 is still serving.** `pg_upgrade --check` against the running old server reads its catalogue and
changes nothing:

```bash
cd /var/lib/pgsql/19 && sudo -u postgres /usr/pgsql-19/bin/pg_upgrade \
    -b /usr/pgsql-18/bin -B /usr/pgsql-19/bin -d /var/lib/pgsql/18/data -D /var/lib/pgsql/19/data \
    -p 5432 -P 50432 -U postgres --check --clone
```

It must end with `*Clusters are compatible*`. 19 adds two checks to the list (not-null constraint inconsistencies,
uses of `gist_inet_ops`/`gist_cidr_ops`); an Exponential database passes both.

**7. The switch.** Take the exact row counts of every table (step 3 of [Before you start](#before-you-start)), stop
the old server, upgrade, copy the access files, start 19:

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

Choose the transfer mode by the file system:

| Mode | When | Time | The old cluster afterwards |
|---|---|---|---|
| `--clone` | XFS with `reflink=1`, Btrfs (same file system for both) | as fast as `--link` | untouched and startable |
| `--copy` | anything else; small databases | proportional to the size | untouched and startable |
| `--link` | very large databases, no reflink | seconds | **not startable** once 19 has started |
| `--swap` (18 and later) | the new data directory may be emptied | seconds | not startable |

`xfs_info / | grep reflink` tells whether `--clone` works. On ZFS block cloning is often switched off
(`zfs_bclone_enabled=0`); use `--copy` there.

**8. Extensions and statistics.** If `pg_upgrade` wrote `update_extensions.sql`, run it as the superuser
(`psql -f update_extensions.sql -d postgres`). Then `vacuumdb --all --analyze-in-stages`. 18 and 19 carry the
planner statistics through `pg_upgrade`, so the sites can be let back in before the analyze finishes.

**9. Compare.** Take the row counts again: every table, every sequence position, every role and every database
setting (`ALTER DATABASE ... SET`, `pg_db_role_setting`) must be the same. Only extension versions may differ.

**10. Remove the old cluster later, not now.** `pg_upgrade` writes `delete_old_cluster.sh`. Keep the old data
directory until the site has run on 19 for some days; it is your rollback.

If the unit name matters to other software (a hosting panel, proxies with `After=postgresql.service`), you can keep
the unit `postgresql` and point it at 19 with a drop-in instead of switching to `postgresql-19`:

```ini
# /etc/systemd/system/postgresql.service.d/pg19.conf
[Service]
Environment=PGDATA=/var/lib/pgsql/19/data
ExecStartPre=
ExecStartPre=/usr/pgsql-19/bin/postgresql-19-check-db-dir ${PGDATA}
ExecStart=
ExecStart=/usr/pgsql-19/bin/postgres -D ${PGDATA}
```

Drop-ins are read in name order; a later file wins, so `pg19.conf` overrides an older `pg18.conf` and removing it
brings 18 back.

### A primary with a streaming standby: the order

`pg_upgrade` does not carry physical replication slots, and a standby must run the same major version as its primary.
Upgrade in this order:

1. Install the 19 packages on the primary **and** on the standby's host.
2. Release synchronous replication on the primary before the switch:
   `ALTER SYSTEM SET synchronous_standby_names = ''; SELECT pg_reload_conf();`. Otherwise every commit of a database
   with `remote_apply` (or of the whole server, with `synchronous_commit = on`) waits for a standby that cannot connect
   any more.
3. Stop the old standby when you close the doors (keep its data directory), then upgrade the primary (steps 4 to 9).
   An 18 standby cannot follow a 19 primary; with nothing on the replica address every site reads the primary.
4. Re-create the slot on the new primary: `SELECT pg_create_physical_replication_slot('<slot name>');`.
5. Rebuild the standby from the 19 primary into a **new** data directory and keep its old one:
   `pg_basebackup -d "host=<primary> user=<replication login> application_name=<name>" -D <new dir> -S <slot> -X stream -R -c fast`.
   Its own settings (port, `hot_standby_feedback`, the `primary_conninfo`) go in after the backup.
6. When it streams (`SELECT status FROM pg_stat_wal_receiver` on the standby says `streaming`), arm synchronous
   replication again: `ALTER SYSTEM SET synchronous_standby_names = '<name>'`.

`ALTER DATABASE ... SET synchronous_commit = remote_apply` is a database setting; `pg_upgrade` carries it, so it is
active again the moment the standby is named.

### On a server managed by Plesk

Plesk documents PostgreSQL up to 18.x on Linux. A Plesk server runs 19 well, with these steps after the upgrade:

1. Point Plesk at the new programs and data in `/etc/psa/psa.conf` (keep a copy):
   `PGSQL_BIN_D /usr/pgsql-19/bin`, `PGSQL_DATA_D /var/lib/pgsql/19/data`, `PGSQL_CONF_D /var/lib/pgsql/19/data`.
2. `plesk sbin packagemng -sdf`.
3. Update the database server registration with the administrator's password (in the environment, not on the
   command line): `PSA_PASSWORD='<password>' plesk bin database-server --update-server localhost:5432 -type postgresql -admin postgres -passwd ""`.
   It must answer "Server localhost:5432 is successfully updated".
4. Check: `plesk db "SELECT host, port, server_version, last_error FROM DatabaseServers WHERE type = 'postgresql'"`
   shows `19.0` and `no_error`; then create a database and a user in the panel, back it up and remove it again.

What you will see: the component list (`packagemng -l`) still names the OS `postgresql-server` package version when
that package stays installed; Plesk accepts the server regardless. Messages such as
`sw-engine: /lib64/libpq.so.5: no version information available` come from a PGDG `libpq5` package replacing the
OS libpq and are harmless. Plesk's own backups use `PGSQL_BIN_D`, so they dump with the 19 client after step 1.

### Rolling back

Possible as long as the old data directory is untouched (`--clone` or `--copy`) and nothing written on 19 needs to be
kept:

```bash
systemctl disable --now postgresql-19
systemctl enable --now postgresql-18        # or remove the drop-in and: systemctl daemon-reload && systemctl start postgresql
```

Restore `psa.conf` from the copy on a Plesk server and run `--update-server` again. Writes made on 19 are not in the
18 cluster. A standby rebuilt from 19 cannot follow 18: start the standby's old data directory instead, it still has
its slot on the 18 primary.

### Switching the site and checking it

1. Point `[DatabaseSettings] Server` and `Port` (and, in a cluster, `[eZDFSClusteringSettings] DBHost` and `DBPort`
   and the `CLUSTER_STORAGE_HOST`/`CLUSTER_STORAGE_PORT` of `config.cluster.php`) at the new server, unless the new
   server took over the old address.
2. Clear the INI cache and deploy: `php bin/php/ezcache.php --clear-tag=ini --allow-root-user` and
   `./console exp:velocity deploy --allow-root-user`.
3. Check:

   | Check | How |
   |---|---|
   | The site answers | `curl -s https://<your-host>/ezinfo/is_alive` prints `Exponential is alive` |
   | The same tables and rows | compare the counts you took before; on 18 and 19 every table must match |
   | Extensions | `SELECT extname, extversion FROM pg_extension;` shows `pgcrypto` at the new server's version |
   | Backups work | `./console exp:upgrade:backup` finishes and names the new client |
   | Upgrade steps | `./console exp:upgrade:status` shows nothing pending that was not pending before |
   | Cluster metadata | `SELECT count(*) FROM ezdfsfile;` in the cluster database matches, and an image of the site loads through `index_cluster.php` |
   | Password hashes | `SELECT rolname FROM pg_authid WHERE rolpassword LIKE 'md5%';` lists no Exponential login (see [Known limits](#known-limits)) |

4. Take the site out of maintenance: `php bin/php/maintenance.php off --allow-root-user`.

The SQL query cache keeps the time of the last write of every table, not the rows themselves; after a restore,
`php bin/php/ezcache.php --clear-id=querycache --allow-root-user` makes sure no result from before the move is used.

### Moving from MySQL or MariaDB to PostgreSQL 18 or 19

The schema tools convert a MySQL or MariaDB database into PostgreSQL through the engine-neutral `.dba` format
([Moving a site to another engine](../../help/database-administrators/moving-between-engines.md)). Three faults that
stopped such a conversion are fixed in 6.0.15:

- `ezsqldumpschema.php --output-types=data` quotes table and column names on MySQL, so a column named like a reserved
  word (`interval`, `group`) no longer stops the dump.
- The PostgreSQL schema handler maps `timestamp`, `datetime`, `date` and `time` columns of extension and third-party
  tables (they failed with "Unhandled column type: timestamp").
- A `NOT NULL` number or date column without a default gets no `DEFAULT ''`, and the `INSERT` column lists for
  PostgreSQL quote reserved words, so tables with such columns are created and filled.

The conversion gives the same output on 13, 18 and 19.

## Running a cluster on 18 and 19

A cluster of Exponential web servers uses PostgreSQL in up to three roles. They are separate choices and can be the
same server or different ones:

| Role | Settings | Needed for |
|---|---|---|
| The site's database (primary) | `site.ini [DatabaseSettings]` | every installation |
| The DFS cluster metadata | `file.ini [eZDFSClusteringSettings]` and `config.cluster.php` | several web servers sharing stored files and caches |
| Read replicas (standbys) | `site.ini [DatabaseSettings] UseSlaveServer` and `SlaveServerArray[]` | reading anonymous pages from a standby close to a web server |

The overview of every part of a cluster is [Clustering in Exponential 6.0](clustering-overview.md).

### The DFS backend on PostgreSQL

The kernel's backend `expDFSFileHandlerPostgresqlBackend` keeps the index of every stored and cache file in the tables
`ezdfsfile` and `ezdfsfile_cache`; the files themselves are on a share every web server mounts. In
`settings/override/file.ini.append.php`:

```ini
[ClusteringSettings]
FileHandler=eZDFSFileHandler

[eZDFSClusteringSettings]
MountPointPath=/mnt/exp-dfs
DBBackend=expDFSFileHandlerPostgresqlBackend
DBHost=cluster-db.internal
DBPort=5432
# or, on the database server itself, the directory of the Unix socket (it takes the place of DBHost):
# DBSocket=/run/postgresql
DBName=exp_cluster
DBUser=exp_cluster
DBPassword=<the password>
# connection attempts, and tries of a statement that hit a deadlock or a lock wait
DBConnectRetries=3
DBExecuteRetries=20
# seconds a statement waits for a row lock before its transaction is tried again (default 10)
DBLockTimeout=10
# keep one connection per PHP worker between requests (default: off)
DBPersistentConnection=enabled
```

And in `config.cluster.php`, for the gateway `index_cluster.php` that serves stored images and the public cache:

```php
<?php
define( 'CLUSTER_STORAGE_BACKEND', 'dfspostgresql' );
define( 'CLUSTER_STORAGE_HOST', 'cluster-db.internal' );
define( 'CLUSTER_STORAGE_PORT', 5432 );
define( 'CLUSTER_STORAGE_USER', 'exp_cluster' );
define( 'CLUSTER_STORAGE_PASS', '<the password>' );
define( 'CLUSTER_STORAGE_DB', 'exp_cluster' );
define( 'CLUSTER_STORAGE_CHARSET', 'utf8' );
define( 'CLUSTER_MOUNT_POINT_PATH', '/mnt/exp-dfs' );
```

Tables: `psql -v ON_ERROR_STOP=1 -h cluster-db.internal -U exp_cluster -d exp_cluster -f kernel/sql/postgresql/cluster_dfs_schema.sql`.
On a single server, `./console exp:cluster:enable --apply` does all of it, with the tables in the site's own database
([A one-node DFS cluster in one command](cluster-commands.md)).

**One round trip per statement.** The backend binds its values in PHP (PDO emulated prepares; the values are only
integers, strings and nulls), so each statement is one round trip to the server instead of three (prepare, execute
and the `DEALLOCATE`). On a cluster whose database is on another machine this cut the time of every cache file check
to a third. Nothing needs to be set for it.

**Persistent connections.** `DBPersistentConnection=enabled` keeps one connection per PHP worker between requests and
saves the login round trips of a new connection (with `scram-sha-256` these are several). Count first: every PHP-FPM
child and every Velocity worker of every web server holds one connection, to the cluster database **and**, with
`[DatabaseSettings] UsePersistentConnection=enabled`, one to the site's database. The sum must stay below the server's
`max_connections`, with room for cronjobs, the console and your own sessions. PDO rolls back an open transaction when
a request ends. The gateway `index_cluster.php` keeps its connection with `define( 'CLUSTER_PERSISTENT_CONNECTION',
true );` in `config.cluster.php` (default false): a worker that serves image after image then logs in once. Proven on
19: five gateway requests of one worker used one backend pid, without the constant each had a new one.

**Bounded waits.** Every connection sets `lock_timeout` from `DBLockTimeout`, so no statement waits for a row lock
forever; a deadlock (`40P01`), a lock not granted in time (`55P03`) and a serialization failure (`40001`) are retried up
to `DBExecuteRetries` times. A cluster node never keeps or serves a short or failed copy of a file from the share: the
size is checked on the temporary copy before it is renamed into place, and a fetch that fails on every try removes
the old local copy.

Everything else (credit, what was fixed against the old extension, the move from `ezpostgresqlcluster`, the tests) is
on [Clustering on PostgreSQL](postgresql-dfs-cluster.md).

### A primary and a streaming standby

The usual production shape on 18 and 19 is one primary and at least one physical standby fed by streaming
replication. Exponential writes to the primary only; the standby is a hot spare and, with the settings below, a read
replica for anonymous pages.

- **One address for the primary.** Every web server reaches the primary through an address that moves with it on a
  failover (a virtual IP, a DNS name with a short TTL, or a proxy in front of the database). Exponential's settings
  name that address, so a failover changes no setting.
- **The cluster metadata** lives on the primary too. The DFS backend always reads and writes the primary, never a
  replica; a standby that is promoted takes its tables along.
- **Connection poolers.** The DFS backend sets `client_encoding` and `lock_timeout` for its session, and the site's
  driver relies on session state as well. A pooler in front of PostgreSQL must keep sessions (session pooling), not
  hand a session to another client after each transaction.

### Read replicas on 18 and 19

`eZPostgreSQLDB` sends a statement to a standby only when it cannot read stale: a plain `SELECT` of an anonymous `GET`
or `HEAD` web request that has written nothing, outside a transaction, and only while the standby is streaming. Any
error runs the statement on the primary. In `settings/override/site.ini.append.php`:

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
SlaverServerPassword[]=<the password>
SlaverServerDatabase[]
SlaverServerDatabase[]=exponential
```

The key names `SlaverServerUser`, `SlaverServerPassword` and `SlaverServerDatabase` are spelled this way in the kernel
and must be written exactly so. With several `SlaveServerArray[]` entries each request picks one at random.

On the primary, so that a visitor right after an edit sees it on any node:

```sql
ALTER SYSTEM SET synchronous_commit = 'local';   -- only the site's database waits, not every database
ALTER SYSTEM SET synchronous_standby_names = '<standby application_name>';
SELECT pg_reload_conf();
ALTER DATABASE exponential SET synchronous_commit = remote_apply;
GRANT pg_read_all_stats TO exponential;           -- the driver reads pg_stat_wal_receiver on the standby
```

Without `pg_read_all_stats` the driver cannot see that the standby is streaming and never reads it. A node where
nothing listens on the replica's address reads the primary, so one set of settings serves every node of a cluster when
only some have a local standby. The whole rule, and the same feature on MySQL, MariaDB and Oracle:
[Read replicas](../../bc/6.0/postgresql-read-replica.md).

### Shared state of the caches

Two caches keep a small state file that every web server of a cluster must share, and both find the DFS share on their
own:

| Cache | Setting | Default in a cluster |
|---|---|---|
| The HTTP cache: a purge on one server reaches the pages of every server | `httpcache.ini [HttpCacheSettings] SharedStateDir` | the folder `exphttpcache` on the share of `MountPointPath` |
| The SQL query cache in shared mode: a write on one server makes the results of every server stale | `querycache.ini [QueryCacheSettings] SharedStateDir` | the folder `querycache` on the share of `MountPointPath` |

Both take a path instead, or `disabled`. Details: [Clustering in Exponential 6.0](clustering-overview.md#caches-in-a-cluster).

## Proof from production

Production installations were upgraded to PostgreSQL 18.7 with `pg_upgrade`: a primary with a streaming standby,
serving 20 databases with 2,936 tables and 59.5 million rows. The databases were compared after the upgrade and all
20 were identical to what they held before. The kernel itself was proven on 18 and 19 as described in
[Supported versions](#supported-versions-and-how-support-is-tested).

The same installations then moved on to **PostgreSQL 19.0**, with the procedure in
[From 18 to 19, step by step](#from-18-to-19-step-by-step-pg_upgrade-on-one-server), in three stages.

**1. A single server first, and Exponential proven against it.** A server with no production data went from 18.7 to
19.0 with `pg_upgrade --copy`: `--check` compatible, 35 settings carried over, none unknown to 19, downtime 3.9 seconds,
every count identical, Plesk's registration updated to `19.0` with no error, and a database and a user created, backed
up and removed through the panel. Exponential was then run against that server over the network, as a web server in
another data centre would use it:

| What | Result on 19.0 |
|---|---|
| Every database test suite, exactly as CI runs them (`tests/runtests.php --dsn postgresql://... --db-per-test`) | 27,993 tests, 845,012 assertions; no PostgreSQL failure (one timing test failed under load and passed when run again) |
| The DFS cluster backend (`expDFSPostgresqlBackendIntegrationTest`) | 29 tests, 161 assertions |
| Read replicas against a 19 primary and a 19 streaming standby | 19 checks: anonymous reads on the standby, a write, a transaction, a POST or an editor pins the primary, a stopped stream falls back to the primary and a restarted one is read again, the shared SQL query cache; a login without `pg_read_all_stats` reads the primary |
| A new installation (`install.php --db=pgsql`) | 259 tables, 323 content objects |
| `exp:upgrade:backup` | dumped with `pg_dump` 19.0, which Exponential picked itself |
| The database stage of the upgrade, then a second run | nothing left to do |
| A restore drill with `psql` 19.0 into an empty database | 323 of 323 content objects |
| Content | created, published, edited, published again as version 2, found by the search, the same after `ezcache.php --clear-all` |
| A two-node DFS cluster on 19 (`exp:cluster:enable --backend=postgresql`, a second node with the same code and settings and its own `var/`) | an image published on one node fetched byte for byte by the other; a file stored on one node fetched on the other; deleted and purged on the second, gone for the first; a cache clear in cluster mode |

**2. The production primary.** The primary of a cluster serving ten sites, 22 databases, 3,307 tables and 59.6 million
rows, moved from 18.7 to 19.0 in one window:

| Step | Time |
|---|---|
| Timers paused, synchronous replication released, the database proxies stopped (the doors closed) | seconds |
| Exact row counts on 18 | 204 s |
| `pg_upgrade --clone` (XFS with reflink) | 112 s; 19 answered 116 s after the old server stopped |
| Exact row counts on 19 | 51 s |
| **Downtime, doors closed to doors open** | **6 minutes 12 seconds** |
| `vacuumdb --all --analyze-in-stages`, with the sites already running | 175 s |

Every table, row count, sequence position (2,584), role and database setting was identical; no extension changed
version. Every site answered on every node and through the load balancer, every site database committed on 19,
`pg_dump` 19 dumped every database (the largest 846 MB compressed), and the hosting panel showed the server as 19.0 with
no error. The old 18 data directory was left untouched by `--clone` and kept for rollback.

**3. The standby and the rest.** The standby was rebuilt from the 19 primary with `pg_basebackup` into a new data
directory (the 18 one kept): 4.7 GB in 17 minutes over the private network with a bandwidth limit, then streaming with
nothing to replay. Until then every site read the primary, as the read replica rule says; with the standby listening on
its old address again, the sites read it again without a settings change. Synchronous replication was armed again
once it had caught up, and a request-by-request check confirmed the routing: anonymous `GET` and `HEAD` on the
standby's node read the standby; a signed-in editor, a `POST` and the other nodes read the primary. The hosting
panel's own PostgreSQL server on that node went from 18.7 to 19.0 with `pg_upgrade --copy` in 17.6 seconds of
downtime, with its panel registration updated and a database and user created, backed up and removed through the
panel.

Two lessons from the window are in the procedure and the [troubleshooting table](#troubleshooting): stop the old standby
when the doors close, because it cannot follow the new major version and should not be offered as a replica any more;
and check every site on every node right after the doors open: on one node the PHP processes of one site (LiteSpeed's
`lsphp`) hung, waiting on a lock inside PHP from the moment the database became unreachable, and new ones started from
the same parent hung too, until the site's PHP processes were restarted.

19 also warns at every login of a role that still has an MD5 password hash; it works, but set those passwords again with
`scram-sha-256` (see [Known limits](#known-limits)).

## Troubleshooting

| What you see | Why | What to do |
|---|---|---|
| `dnf`: "Unknown repo: 'pgdg19'" | the repository package is older than 19, or its new file was saved as `.rpmnew` because you edited the old one | install the current `pgdg-redhat-repo-latest` and move the `.rpmnew` into place ([step 1](#from-18-to-19-step-by-step-pg_upgrade-on-one-server)) |
| `dnf`: "repomd.xml GPG signature verification error" on the first use of `pgdg19` | the repository's metadata key was not accepted yet | check the fingerprint, `rpm --import /etc/pki/rpm-gpg/PGDG-RPM-GPG-KEY-RHEL`, then `dnf --enablerepo=pgdg19 makecache` and answer yes |
| `dnf` on EL 10: nothing provides `libnuma.so.1` | `numactl-libs` is in BaseOS | do not disable the OS repositories for the install |
| `pg_upgrade`: "old cluster does not use data checksums but the new one does" | 18 and later initialise with checksums by default | `initdb --no-data-checksums` again, or `pg_checksums --enable` on the stopped old cluster |
| `pg_upgrade`: "could not load library ... pgcrypto" | the new server's contrib package is missing | install `postgresql19-contrib` |
| `pg_upgrade`: "connection to server on socket ... failed" | it starts the old server on port 50432 with its own socket in the current directory; the user cannot write there, or a password is asked | run it from a directory the `postgres` user owns; give it a `PGPASSFILE` the `postgres` user can read |
| `pg_upgrade --clone`: "file cloning not supported on this platform" | the file system has no reflink (ext4, ZFS without block cloning) | `--copy`, or `--link` if you accept that the old cluster cannot start again |
| `pg_dump`: "aborting because of server version mismatch" | an 18 or older `pg_dump` against 19 | the 19 client on `PATH`, or `PostgreSQLClientDir` ([client versions](#client-versions-php-libpq-pg_dump-and-psql)) |
| `exp:upgrade --apply`: "the caption failed, nothing was changed" | the same: the caption's `pg_dump` is too old | the same |
| A backup timer or panel backup fails after the upgrade | it calls `/usr/bin/pg_dump` or another fixed path of the old client | point it at the 19 client or at `/usr/local/bin` |
| Every commit hangs right after the switch | `synchronous_standby_names` names a standby that cannot connect any more (it is still on 18) | `ALTER SYSTEM SET synchronous_standby_names = ''; SELECT pg_reload_conf();`, rebuild the standby, arm it again |
| The standby on 18 logs "database system identifier differs" or "unsupported WAL format" | a standby cannot follow a primary of another major version | rebuild it with `pg_basebackup` from the 19 primary |
| `pg_basebackup`: "replication slot ... does not exist" | `pg_upgrade` does not carry physical slots | `SELECT pg_create_physical_replication_slot('<slot>')` on the new primary |
| The site never reads the replica after the rebuild | the standby is not streaming yet, or the site's login lost `pg_read_all_stats` | `SELECT status FROM pg_stat_wal_receiver` on the standby; `GRANT pg_read_all_stats TO <login>` on the primary |
| Logins fail with "password authentication failed" after a dump and restore into a new server | `pg_dumpall --globals-only` was not restored, or `password_encryption` differs and `pg_hba.conf` demands `scram-sha-256` from an MD5 hash | restore the globals; set the passwords again with `scram-sha-256` |
| Plesk: the database server shows an old version or an error | `psa.conf` still names the old programs, or the registration was not updated | the steps in [On a server managed by Plesk](#on-a-server-managed-by-plesk) |
| A site still hangs after the doors opened, with its PHP processes at 0 % CPU | PHP processes (seen with LiteSpeed `lsphp`) waiting on a lock inside PHP since the database became unreachable; processes forked from the same parent hang too | restart the site's PHP handler (all its processes, the parent included); with PHP-FPM `systemctl reload` of its pool |
| Logins log "MD5 password support is deprecated and will be removed in a future release" | the role still has an MD5 password hash (19 warns at every login) | `SET password_encryption = 'scram-sha-256'`, set the password again, change `md5` to `scram-sha-256` in `pg_hba.conf` |
| Slow pages right after the upgrade | the extended statistics are not carried | `vacuumdb --all --analyze-in-stages`, or `ANALYZE` the largest tables first |
| Sorting of text looks different | the collation provider or the C library changed with the operating system, not with PostgreSQL | `pg_upgrade --check` reports "objects affected by Unicode update"; reindex the text indexes it names. With `C.UTF-8` the order does not change |

## Known limits

- **MD5 passwords.** PostgreSQL 18 deprecates `md5` password authentication and warns when a role still has an MD5
  hash. Set `password_encryption = 'scram-sha-256'` and set the passwords of the site's and the cluster's logins again
  (`\password exponential` in `psql`); change `md5` to `scram-sha-256` in `pg_hba.conf`. libpq 10 or later is
  needed on the web servers.
- **The client of the backup.** `exp:upgrade:backup`, the caption check, the rollback and `ezconvertdbcharset.php` need
  a `pg_dump`/`psql` at least as new as the server. Install it on every web server that runs them.
- **Replicas are physical standbys.** The read replica check reads `pg_is_in_recovery()` and `pg_stat_wal_receiver`;
  a logical replica is a primary to it and would be read without a lag check. Use streaming standbys.
- **The DFS backend reads the primary only.** File metadata changes with every cache write, so it is never read from a
  replica.
- **Synchronous apply waits for the standby.** With `remote_apply`, commits of the site's database wait while the
  standby is gone. Watch the standby and empty `synchronous_standby_names` automatically when it stops streaming.
- **The setup wizard checks for PostgreSQL 8.0.** Use 13 or later regardless; older releases are not tested and lack
  the trusted `pgcrypto`.

## Databases that speak PostgreSQL's protocol

CockroachDB, YugabyteDB, Neon, Supabase, Amazon Aurora PostgreSQL and Google Cloud Spanner (through PGAdapter) speak
PostgreSQL's protocol and run on `expPostgreSQLWireDB`, the PostgreSQL driver with a profile per database
(`DatabaseImplementation` `cockroachdb`, `yugabytedb`, `neon`, `supabase`, `aurorapostgresql`, `spanner`). The
versions on this page are PostgreSQL's own; what each of those databases does differently (locks, retries, poolers,
TLS, DDL) and which of them are untested (Neon, Supabase, Aurora) is on
[Databases of the PostgreSQL wire protocol](postgresql-wire-databases.md). A managed PostgreSQL that is PostgreSQL itself (Amazon RDS, Google
Cloud SQL, Azure Database for PostgreSQL) uses `ezpostgresql` as here.

## Related pages

- [Clustering in Exponential 6.0](clustering-overview.md)
- [Clustering on PostgreSQL: the DFS backend in the kernel](postgresql-dfs-cluster.md)
- [Read replicas: PostgreSQL, MySQL and MariaDB, Oracle](../../bc/6.0/postgresql-read-replica.md)
- [A one-node DFS cluster in one command](cluster-commands.md)
- [Database support](../../help/database-support.md)
- [PostgreSQL setup](../../help/database-administrators/postgresql-setup.md)
- [Operating PostgreSQL](../../help/database-administrators/postgresql-operations.md)
- [Moving a site to another engine](../../help/database-administrators/moving-between-engines.md)
- [Upgrading with the console](../../guides/upgrading-with-the-console.md)
- [6.0.15 changelog](../../../changelogs/6.0/6.0.15.md)
