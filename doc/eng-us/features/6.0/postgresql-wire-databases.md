# Databases of the PostgreSQL wire protocol

This page is for administrators who want to run Exponential on a database that speaks PostgreSQL's protocol without
being PostgreSQL, or on PostgreSQL at a cloud service with rules of its own, and for contributors who work on the
driver. Exponential 6.0.15 has one driver for all of them, `expPostgreSQLWireDB` in `lib/ezdb/classes/`, a subclass of
the PostgreSQL driver `eZPostgreSQLDB`, with one profile per database in `expPostgreSQLWireProfile`:

| Database | `DatabaseImplementation` | Driver class | Level reached | Tested live |
|---|---|---|---|---|
| CockroachDB 23.2 and later | `cockroachdb` (`cockroach`, `crdb`) | `expCockroachDB` | full site database | yes: CockroachDB 25.2 in a container |
| YugabyteDB 2.20 and later (YSQL) | `yugabytedb` (`yugabyte`, `ysql`) | `expYugabyteDB` | full site database | yes: YugabyteDB 2.25 in a container |
| Google Cloud Spanner, PostgreSQL dialect | `spanner` (`spannerpg`, `pgadapter`) | `expSpannerPGDB` | data-table connection and import | yes: the Spanner emulator behind PGAdapter |
| Neon | `neon` | `expNeonDB` | full site database (PostgreSQL) | **no**: connection profile, unit tests and this page |
| Supabase | `supabase` | `expSupabaseDB` | full site database (PostgreSQL) | **no**: connection profile, unit tests and this page |
| Amazon Aurora PostgreSQL | `aurora` (`aurorapostgresql`) | `expAuroraPostgreSQLDB` | full site database (PostgreSQL) | **no**: connection profile, unit tests and this page |

"Full site database" means what it means for PostgreSQL itself: the installer creates the kernel schema and the data
of a site package in it, and the site runs from it. Neon, Supabase and Aurora are PostgreSQL servers, so a site runs
there as on PostgreSQL; what their profiles add (TLS, poolers, cold starts, failover) was written from the services'
documentation and checked by unit tests only. No account was opened at any of them to test this.

## Contents

1. [Why a profile and not a new driver](#why-a-profile-and-not-a-new-driver)
2. [Settings](#settings)
3. [What the driver does differently](#what-the-driver-does-differently)
4. [CockroachDB](#cockroachdb)
5. [YugabyteDB](#yugabytedb)
6. [Google Cloud Spanner (PostgreSQL dialect)](#google-cloud-spanner-postgresql-dialect)
7. [Neon](#neon)
8. [Supabase](#supabase)
9. [Amazon Aurora PostgreSQL](#amazon-aurora-postgresql)
10. [Installing a site](#installing-a-site)
11. [Data-table connections](#data-table-connections)
12. [Tests](#tests)

## Why a profile and not a new driver

All six databases take PostgreSQL's SQL and PHP's `pgsql` extension talks to them. What differs is a list of
details: whether `LOCK TABLE` exists, whether a transaction may have to be run again, what a sequence looks like,
whether a primary key may be added after `CREATE TABLE`, whether a pooler sits in front. The driver keeps
`databaseName()` as `postgresql`, so the kernel, the schema handler (`eZPgsqlSchema`), the SQL helpers and the
installer treat each of them as PostgreSQL, and the profile changes the details where the database needs it.
`databaseVariant()` names the database, and `wireProfile()` returns its profile with the effective settings.

## Settings

```ini
[DatabaseSettings]
DatabaseImplementation=cockroachdb
Server=crdb1.example.com
Port=26257
User=exponential
Password=...
Database=exponential

[PostgreSQLWireSettings]
SSLMode=verify-full
SSLRootCert=/etc/ssl/crdb/ca.crt
```

`[PostgreSQLWireSettings]` of `site.ini` (an empty value keeps the profile's default):

| Setting | Meaning |
|---|---|
| `SSLMode`, `SSLRootCert` | libpq's `sslmode` and CA file. Neon, Supabase and Aurora default to `require` |
| `ConnectTimeout` | seconds per connection attempt (libpq `connect_timeout`) |
| `ConnectAttempts`, `ConnectWait` | attempts to connect and the wait before the second (ms, doubled each time): a Neon compute waking up, an Aurora writer in failover |
| `SerializationRetries`, `RetryWait` | how often a single statement outside a transaction is sent again after `40001` (serialization failure) or `40P01` (deadlock), and the first wait (ms) |
| `ReconnectOnLoss` | `enabled`: a statement outside a transaction whose connection was lost runs again on a new connection |
| `Pooler` | `auto` (from host and port), `none`, `session` or `transaction` |
| `Isolation` | `default` (the profile's), `read committed`, `repeatable read`, `serializable` |
| `ApplicationName` | what `pg_stat_activity` shows |
| `Endpoint` | Neon: the endpoint id, for a libpq without SNI (`options=endpoint=<id>`) |
| `Options` | more libpq startup options (`-c name=value`); not sent behind a transaction pooler |
| `SessionStatements[]` | more statements after every connect |

The installer writes the TLS and pooler settings it was given (`--db-sslmode`, `--db-sslrootcert`, `--db-pooler`,
`--db-endpoint`) into every siteaccess and into `settings/override`, as it does for SQL Server's encryption.

## What the driver does differently

- **Connecting.** The connection string carries `sslmode`, `sslrootcert`, `connect_timeout` and `application_name`
  from the profile and the settings, every value quoted for libpq. A failed connect is tried `ConnectAttempts` times
  with a growing wait. After connecting, the profile's session statements run (CockroachDB: `READ COMMITTED` and
  temporary tables); a statement the server refuses is logged and the connection is used without it.
- **Poolers.** Behind a pooler in transaction mode (PgBouncer, Supavisor on port 6543, Neon's `-pooler` host) the
  next statement may run on another server connection. So no session statement is sent, startup options are left
  out (except Neon's endpoint), and an `INSERT` into a table with a serial column gets `RETURNING <column>`:
  `lastSerialID()` answers from it instead of asking `currval()`, which might ask another session.
- **Retries.** A single statement outside a transaction that fails with `40001` or `40P01` is sent again, up to
  `SerializationRetries` times; one whose connection was lost (`57P01` "terminating connection due to administrator
  command", `08006`, "server closed the connection unexpectedly") is sent again on a new connection. Inside a
  transaction nothing is repeated: the transaction is lost as a whole, the kernel rolls it back, and only the code
  that made it can run it again. `retryTransaction( $work )` does that for a closure.
- **Locks.** `lock()` (MySQL's `LOCK TABLES`) runs `LOCK TABLE` where it exists. CockroachDB has no `LOCK TABLE` and
  YugabyteDB refuses its default mode (`ACCESS EXCLUSIVE`), so there the driver keeps one row per table in
  `exp_wire_lock` and locks it with `SELECT ... FOR UPDATE`: a second `lock()` of the same table waits until the first
  `unlock()`. The rows are made before the transaction, so a second locker always finds the row to wait on. Advisory
  locks are never used: CockroachDB's `pg_try_advisory_lock()` answers true without locking anything.
- **New ids.** `lastSerialID()` reads `currval()`, as on PostgreSQL, or the `RETURNING` value (above).
- **`md5()`** is PostgreSQL's built-in `md5()`. `eZPostgreSQLDB` uses pgcrypto's `digest()`, which YugabyteDB, Neon
  and Aurora do not have by default; the installer's `digest()` check is not made for these drivers.
- **`BEGIN`, `COMMIT`, `ROLLBACK`** without PostgreSQL's optional `WORK`, which CockroachDB does not take.
- **Schema.** `eZPgsqlSchema` asks the profile: the primary key goes into `CREATE TABLE` on CockroachDB, YugabyteDB
  and Spanner (adding it afterwards replaces CockroachDB's hidden `rowid` key, rewrites a YugabyteDB table, and
  Spanner does not take it); no `USING btree` on YugabyteDB (its index method is `lsm`) and Spanner; `int4` for
  `INTEGER` on CockroachDB (whose `INTEGER` is 64 bits); bit-reversed sequences, `bigint` ids and no `setval()` on
  Spanner. The serial defaults CockroachDB and Spanner report back are read as `auto_increment`.
- **Versions.** `databaseServerVersion()` reads CockroachDB's `CockroachDB CCL v25.2.4` and YugabyteDB's
  `15.12-YB-2.25.2.0` correctly.
- **Replicas.** Aurora's readers have no WAL receiver (they replicate in the storage): the replica check asks
  `pg_is_in_recovery()` alone there.

## CockroachDB

**Measured** (CockroachDB 25.2.4, one node in a container, 2 CPUs; PHP 8.5): `exp:install --apply --db=cockroachdb`
installed `sevenx_multisite` in 16 steps, all 9 checks passed, in 47 minutes (most of it DDL and catalogue reads).
The site then held the package's 291 objects (and the help manuals, which are built in the background
afterwards: 2100 objects in all half an hour later); publishing an article, its URL alias, the
search, moving it to the trash and the transaction counter were checked on it. The errors of that run came from
extensions the test tree did not have (xrowmetadata, eztags, explayouts ...), none from the database.

| Works | Does not, or differently |
|---|---|
| A full site: the kernel schema, the data of a site package, content, the search, the admin | `LOCK TABLE`: replaced by row locks in `exp_wire_lock` |
| Sequences and `nextval()` defaults as on PostgreSQL; `currval()`, `setval()` | `pg_advisory_lock()` does not exist; `pg_try_advisory_lock()` locks nothing |
| `ON CONFLICT`, `STRING_AGG`, `FOR UPDATE`, `EXTRACT(EPOCH ...)`, `md5()` | Temporary tables only with `experimental_enable_temp_tables` (the driver sets it) |
| `READ COMMITTED` (23.2 and later, the driver's default); `SERIALIZABLE` with `Isolation=serializable` | `SERIALIZABLE` (the server's default) restarts transactions (`40001`): single statements are retried, transactions need `retryTransaction()` |
| Row locks, `SELECT ... FOR UPDATE`, transactions, the query cache | DDL is slow (a schema change per statement, about a second): installing the kernel schema takes minutes |
| Several nodes behind one address (any node takes any statement) | DDL inside a transaction commits it first (`autocommit_before_ddl`) |
| | `INTEGER` is `INT8`: the schema writes `int4`; `DROP DATABASE` needs `CASCADE` (the driver adds it) |

## YugabyteDB

**Measured** (YugabyteDB 2.25.2, `yugabyted` in a container, 2 CPUs; PHP 8.5): `exp:install --apply --db=yugabytedb`
installed `sevenx_multisite` in 16 steps, all 9 checks passed, in 42 minutes; 310 content objects, 311 nodes; the
same article, URL alias, search and trash checks passed. The errors of the run came from extensions the test tree did
not have, as on CockroachDB. One thing to know: the upgrade's safety copy ("caption") is made with the server's
`pg_dump`; a dump from PostgreSQL 17's client sets `transaction_timeout`, which YugabyteDB 2.25 (PostgreSQL 15) does
not know, so restoring it failed. Use YugabyteDB's own `ysql_dump` for backups.

| Works | Does not, or differently |
|---|---|
| A full site: the kernel schema, the data, content, the admin | `LOCK TABLE ... ACCESS EXCLUSIVE`: replaced by row locks in `exp_wire_lock` |
| PostgreSQL 15 SQL (2.25), sequences, `currval()`, `setval()`, temporary tables | Advisory locks only with the preview flag `ysql_yb_enable_advisory_locks` |
| `ON CONFLICT`, `STRING_AGG`, `FOR UPDATE`, `md5()` | No pgcrypto by default: no `digest()` (the driver uses `md5()`) |
| Transactions, row locks | `READ COMMITTED` needs the flag `yb_enable_read_committed_isolation`; without it it runs as `REPEATABLE READ`, and conflicts end in `40001` |
| | Sequences hand out numbers in blocks per node (`CACHE 100` at least): ids are unique, not in insertion order |
| | Adding a primary key after `CREATE TABLE` rewrites the table: the schema writes it inline; `USING btree` becomes `lsm` |
| | DDL is slow (about a second a statement) |

## Google Cloud Spanner (PostgreSQL dialect)

Spanner speaks PostgreSQL's protocol through **PGAdapter**, a proxy Google publishes (a Java program or a container)
that the application connects to as if it were PostgreSQL. The driver was tested against the Spanner emulator behind
PGAdapter (`gcr.io/cloud-spanner-pg-adapter/pgadapter-emulator`).

**Level: data-table connection and import.** A site cannot run on Spanner: the kernel's ids are 32-bit numbers it
often chooses itself, while Spanner only has bit-reversed sequences (64-bit numbers in no order, no `currval()`), and
the kernel's search and tree code use temporary tables and table locks, which Spanner does not have.

| Works | Does not |
|---|---|
| A data-table connection (`datatable.ini`, engine `spanner`): tables made by the schema handler, rows read and written by persistent classes | A site database (above) |
| Tables with a primary key in `CREATE TABLE` (without a constraint name), indexes, unique indexes | `CREATE SEQUENCE` with `START`, `CACHE` ...: only `BIT_REVERSED_POSITIVE` |
| New ids from a bit-reversed sequence, read with `RETURNING` | `currval()`, `lastval()`, `setval()` |
| `ON CONFLICT` (upsert), `STRING_AGG`, `LIMIT`/`OFFSET`, `UNION`, `GROUP BY ... HAVING`, transactions | `LIMIT ALL` (the driver writes the largest number), `FOR UPDATE` outside a read-write transaction (the driver leaves it out) |
| Importing rows into the site through the connection (a data-table synchronisation `table_to_content` reads them with persistent classes; the classes are tested on Spanner, the synchronisation itself was not run there) | Temporary tables, `LOCK TABLE`, advisory locks, `md5()` |
| Reading the schema back (from `information_schema`; PGAdapter only emulates `pg_class`) | `smallint`, `real`, `timestamp without time zone` (the schema writes `bigint`, `double precision`, `timestamptz`) |
| | DDL inside a transaction. (`DROP TABLE` of a table with indexes works: the driver sets PGAdapter's `spanner.support_drop_cascade`) |

## Neon

Neon is PostgreSQL (16 and 17) with storage and compute apart; a compute that has been idle scales to zero.

| Works (by the documentation; not tested live) | Does not, or differently |
|---|---|
| A full site, as on PostgreSQL | A suspended compute starts on the first connection: the profile waits and tries again (5 attempts, 0.5 s doubled, 15 s each) |
| TLS (`sslmode=require` by default) | The `-pooler` host is PgBouncer in transaction mode: no session statements, `RETURNING` for new ids, no temporary tables across statements |
| Read replicas (`UseSlaveServer` with a read replica compute) | A libpq without SNI needs the endpoint id: `Endpoint=<id>` |
| | No pgcrypto unless `CREATE EXTENSION pgcrypto` (not needed by this driver) |

## Supabase

Supabase gives every project a PostgreSQL database (15 and 17).

| Works (by the documentation; not tested live) | Does not, or differently |
|---|---|
| A full site, as on PostgreSQL, on the direct host `db.<project>.supabase.co` port 5432 | The direct host answers on IPv6 only, unless the IPv4 add-on is bought |
| Supavisor in session mode (`<region>.pooler.supabase.com`, port 5432): as a direct connection | Supavisor in transaction mode (port 6543): no session statements, `RETURNING` for new ids, no temporary tables across statements |
| TLS (`sslmode=require` by default) | The pooler takes the user as `postgres.<project ref>` |

## Amazon Aurora PostgreSQL

| Works (by the documentation; not tested live) | Does not, or differently |
|---|---|
| A full site, as on PostgreSQL, on the cluster (writer) endpoint | A failover drops every connection: outside a transaction the driver connects again and repeats the statement; a transaction in flight is lost |
| Readers through the reader endpoint (`UseSlaveServer`, `SlaveServerArray`): the replica check asks `pg_is_in_recovery()` | Readers have no WAL receiver: PostgreSQL's replica check would never read them |
| TLS (`sslmode=require`; `verify-full` with the RDS CA bundle in `SSLRootCert`) | IAM authentication: the token is the password and expires after 15 minutes; a long-running process must be given a new one |
| RDS Proxy (session pinning, detected from the `.proxy-` host) | |

## Installing a site

`exp:install` (bin/php/install.php), the kickstarter and the setup wizard know CockroachDB, YugabyteDB, Neon, Supabase
and Aurora. The database must exist, except on CockroachDB and YugabyteDB, where the user may create it.

```bash
./console exp:install --apply --db=cockroachdb --db-host=crdb1 --db-user=root
./console exp:install --apply --db=yugabytedb --db-host=yb1 --db-user=yugabyte
./console exp:install --apply --db=neon --db-host=ep-cool-1.eu-central-1.aws.neon.tech --db-user=app --db-name=neondb
./console exp:install --apply --db=supabase --db-host=db.abcd.supabase.co --db-name=postgres
./console exp:install --apply --db=aurora --db-host=site.cluster-x.eu-west-1.rds.amazonaws.com \
    --db-sslmode=verify-full --db-sslrootcert=/etc/ssl/rds-global-bundle.pem
```

`--db-sslmode`, `--db-sslrootcert`, `--db-pooler` and `--db-endpoint` set `[PostgreSQLWireSettings]`. The kickstarter
takes `Type=cockroachdb` (also `cockroach`, `crdb`), `yugabytedb` (`yugabyte`, `ysql`), `neon`, `supabase` or `aurora`,
with `SSLMode`, `SSLRootCert`, `Pooler` and `Endpoint` in `[database_init]`. The setup wizard lists them after
PostgreSQL when PHP has the `pgsql` extension. Install through a direct connection or a session pooler: the schema is
created statement by statement and a transaction pooler does not change that, but nothing gains from it.

## Data-table connections

`datatable.ini [ConnectionSettings] Engines[]` has `cockroachdb`, `yugabytedb`, `spanner`, `neon`, `supabase` and
`aurora`. A connection opens through the driver (PHP's `pgsql`), its options `sslmode` and `pooler` become the
connection's `[PostgreSQLWireSettings]`. Neon, Supabase and Aurora are marked "untested here" in the list of engines.

## Tests

- `tests/tests/lib/ezdb/pgwire/` (testsuite `pgwire`, no server): the profiles (`expPostgreSQLWireProfileTest`), the
  DDL of each database (`expPostgreSQLWireSchemaTest`), the driver's retries, reconnects, ids and helpers with the
  server replaced (`expPostgreSQLWireDBTest`).
- `tests/tests/lib/ezdb/pgwire/live/` (testsuite `pgwire-live`): the same checks on every database with a server
  (schema and data through the schema handler, new ids, helpers, transactions, `lock()` against a second connection,
  temporary tables), and per database what it refuses. Servers: `EXP_TEST_PGWIRE_COCKROACHDB_HOST`, `_PORT`, `_USER`,
  `_PASSWORD`, `_DATABASE`, the same for `YUGABYTEDB` and `SPANNER`; without them the tests skip.
- CI: the job "PostgreSQL wire protocol" runs the live suite against CockroachDB, YugabyteDB and the Spanner emulator
  with PGAdapter in containers.

Related: [Database support](../../help/database-support.md), [PostgreSQL 18 and 19](postgresql-18-and-19.md),
[Microsoft SQL Server driver](sqlserver-database-driver.md).
