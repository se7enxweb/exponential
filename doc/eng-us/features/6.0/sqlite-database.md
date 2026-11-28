# SQLite database support

This page is for anyone who wants to try Exponential, develop on a laptop, run automated tests, or host a small site
on a plan that offers no MySQL. With SQLite, a complete Exponential site runs from one file on disk, with no database
server to install, configure or secure.

> **Warning**
> **`.htaccess` slows down every request.** When `.htaccess` files are enabled (`AllowOverride` anything but `None`), let alone
> used, Apache looks for them and reads and evaluates them on every request, in every directory of the path, and
> page loading times go up drastically ([measured](../../guides/setup-files-from-the-console.md#measured-htaccess-against-rules-in-the-virtual-host)). The only real fix is to switch the feature off for
> the virtual host: `AllowOverride None` in the HTTPS virtual host, and the rules in the virtual host's own
> configuration as mod_rewrite rules. That is far faster in production speed tests and well worth the time it takes
> to optimize a production configuration. Switch over right away: copy the rules of the
> [example HTTPS virtual host](../../examples/web-servers/apache-virtual-host.md) into your virtual host (with the static cache, also
> [`staticcache_offload.conf`](../../examples/staticcache_offload.conf)), set `AllowOverride None`, run `apachectl configtest`, reload Apache and
> test the site. The same rules for other servers: [nginx](../../examples/web-servers/nginx.md), [LiteSpeed](../../examples/web-servers/litespeed.md),
> [Caddy](../../examples/web-servers/caddy.md), [IIS](../../examples/web-servers/iis.md), [Velocity](../../examples/web-servers/velocity.md) (built in); where virtual host rules go in a control
> panel: [Plesk](../../help/hosting/panels/plesk.md), [cPanel](../../help/hosting/panels/cpanel.md), [DirectAdmin](../../help/hosting/panels/directadmin.md), [ISPConfig](../../help/hosting/panels/ispconfig.md).
> **On LiteSpeed or OpenLiteSpeed,** the server's own page cache can also answer anonymous visitors and crawlers without PHP: [Page caching with LiteSpeed and OpenLiteSpeed](../../guides/litespeed-page-cache.md).

SQLite support arrived in January 2024 (releases 6.0.1 to 6.0.3, see the
[January 2024 chronicle](../../history/2024/2024-01a.md)) and has been refined
since. Everything below describes the code as it is now; a note says where a
setting came in a later release than the first driver.

> **Supported databases (Exponential 6.0.x), in order of preference:** 1. SQLite 3 (the default) · 2. PostgreSQL ·
> 3. MongoDB · 4. MariaDB · 5. Oracle · 6. MySQL · 7. Microsoft SQL Server and Azure SQL (more databases from 6.0.15:
> [Choosing a database](../../help/choosing-a-database.md)). Details: [Database
> support](../../help/database-support.md).

## What you get

- A kernel database driver, `eZSQLite3DB`
  (`lib/ezdb/classes/ezsqlite3db.php`), selected with the implementation
  alias `sqlite3`.
- A schema handler, `eZSQLiteSchema`
  (`lib/ezdbschema/classes/ezsqliteschema.php`), so the setup wizard, the
  package installer and schema tools such as `bin/php/ezsqldumpschema.php` and `bin/php/ezsqlinsertschema.php` can create and
  read tables.
- SQLite-flavoured SQL files in `kernel/sql/sqlite/` (`schema.sql`,
  `cleandata.sql`), used by the setup wizard for a clean install.
- A setup wizard that lists SQLite as a database choice, tests that the PHP
  `sqlite3` extension is loaded and explains what to do if it is not.

## Try it in five minutes

1. Check that PHP has the extension:

   ```bash
   php -m | grep -i sqlite3
   ```

   It must print `sqlite3`. If it prints nothing, install your distribution's
   `php-sqlite3` package (or enable `extension=sqlite3` in `php.ini`) and reload
   PHP-FPM.

2. Open the setup wizard in the browser (`/setup`). On the database page choose
   **SQLite**. The wizard preselects it when the PHP `sqlite3` extension is
   present (setting `[DatabaseSettings] DefaultType=sqlite3` in
   `settings/setup.ini`, see below). If the extension is missing the wizard
   says so and falls back to the first available database system.

3. Give the database a name (the wizard proposes `sqlite.db`, constant `SQLITE_DEFAULT_FILE_NAME`). A name without a path is
   created as a file in `var/storage/sqlite3/`:

   ```bash
   ls -l var/storage/sqlite3/
   ```

4. Finish the wizard as usual. The site now runs from that single file.

## Settings

| File | Block | Key | Default | Scope | What it does |
|---|---|---|---|---|---|
| `settings/site.ini` | `[DatabaseSettings]` | `DatabaseImplementation` | `ezmysqli` | siteaccess | Set to `sqlite3` to use the driver. |
| `settings/site.ini` | `[DatabaseSettings]` | `Database` | (site specific) | siteaccess | The database file. A name without a slash lives in `var/storage/sqlite3/`; an absolute path (starting with `/`) is used as it is; `:memory:` is a throw-away in-memory database. |
| `settings/site.ini` | `[DatabaseSettings]` | `ImplementationAlias[sqlite3]` | `eZSQLite3DB` | global | Maps the alias to the driver class. |
| `settings/dbschema.ini` | `[SchemaSettings]` | `SchemaHandlerClasses[sqlite3]` | `eZSQLiteSchema` | global | Schema handler used by installers and schema tools. |
| `settings/setup.ini` | `[DatabaseSettings]` | `DefaultType` | `sqlite3` | global | Database system the wizard lists first. One of `sqlite3`, `mysqli`, `pgsql`, `mongodb`. |
| `settings/site.ini` | `[DatabaseSettings]` | `SQLitePragmas[]` | empty list | siteaccess | Extra `name=value` PRAGMAs applied on every connection (later releases). |
| `settings/site.ini` | `[DatabaseSettings]` | `SQLiteTransactionWait` | `25` | siteaccess | Seconds a transaction waits at its start for the other writers before giving up with "database is busy" (later releases). Keep it below the request timeout: Velocity ends a request after `Q.webserver.requestTimeout`, 30 s unless changed, hence 25; under PHP-FPM the limit is the pool's `request_terminate_timeout`. Raise both together. |

The driver's built-in PRAGMA defaults (also later releases) are
`synchronous=NORMAL`, `cache_size=-65536` (64 MB), `mmap_size=268435456`,
`temp_store=MEMORY` and `busy_timeout=5000`, and the journal runs in WAL mode.
See [the driver specification](../../specifications/6.0/sqlite3-database-driver.md).

## Using a database file that another application shares

Since April 2026 (`cefe8f13a2`) the driver accepts an absolute path in
`Database`, and registers the `eZSQLite3DB` class in the kernel autoload array.
That lets a legacy bridge point Exponential at the very file a Symfony/Doctrine
application uses, so both stacks see the same data:

```ini
# settings/siteaccess/<name>/site.ini.append.php
[DatabaseSettings]
DatabaseImplementation=sqlite3
Database=/absolute/path/to/shared.db
```

The directory of a new database is created recursively (`ae5feaf6a7`), so a
nested path works on first use.

## Limits and good practice

- SQLite allows one writer at a time. Many editors publishing at the same moment
  queue behind each other (see `SQLiteTransactionWait`); for a busy editorial
  team choose MySQL, PostgreSQL or MongoDB.
- One machine writes. Several can serve the site with LiteFS, one of them the writing primary (next section).
- Do not put the database file inside a directory the web server serves
  directly. In the shipped `.htaccess` only a short list of `var/` paths (public images, public caches, preview images of packages) is served as files; the rest goes to `index.php`. Verify on your own host that the database file is not downloadable: `curl -sI https://www.example.com/var/storage/sqlite3/sqlite.db`. A
  status other than 200 is what you want.
- Run `ANALYZE` once after a big import so the query planner has statistics.
- Back up with `sqlite3 <file> ".backup <target>"`, not by copying the file
  while the site is busy.

## Several servers: LiteFS and Litestream

A SQLite site can run on several servers. Two open source tools from Fly.io do the replication below Exponential; the
driver needs no change, and the write-ahead log it switches on for every connection (`PRAGMA journal_mode = wal`) is
what both of them read.

| | LiteFS | Litestream |
|---|---|---|
| Job | one SQLite database on several nodes: cluster hosting | a continuous copy off the host: backup and disaster recovery |
| Writers | one, the primary; the other nodes hold read-only replicas | the application, as before; Litestream only reads |
| Several web servers | yes | no: a restore, not a second live copy |
| Failover | by hand with a static lease (every node's config names the primary), automatic with a Consul lease | restore the latest copy, or a point in time |

What is supported, and what is not:

- **One writer, always.** Only the primary writes the database; a replica refuses writes. Every request that may write
  (POST, PUT, PATCH, DELETE: publishing, logging in, forms) has to reach the primary. Exponential Velocity does that
  itself: with `writeForward` set for the site's hosts, a replica sends those requests to the primary named in
  LiteFS's `.primary` file, marked so that they are never forwarded twice, and serves every read from its own copy.
  (LiteFS's own HTTP proxy forwards writes only on Fly.io.) Behind another web server, route the write methods to the
  primary in the load balancer.
- **Replicas read a moment behind.** LiteFS ships each transaction to the replicas as it commits; a page read on a
  replica right after a write can show the state from just before it.
- **Litestream is not a cluster.** It replicates the database to S3-compatible object storage, SFTP or a directory, for
  backup and restore to a point in time. It never makes a second writer: two machines never write the same database.
- **Storage files are not in SQLite.** `var/storage` (images, files) needs a share every node sees: the DFS cluster,
  with its file metadata in a server database (MariaDB/MySQL, PostgreSQL, Oracle, SQL Server or MongoDB). The SQLite
  DFS backend stays on one machine ([Clustering on SQLite](sqlite-dfs-cluster.md)).

**Set up LiteFS**

1. Install LiteFS on every node, with FUSE (`/dev/fuse` and `fusermount3`). Open its replication port (20202 by
   default) between the nodes, on a private network only.
2. Write `litefs.yml` on each node: the FUSE mount (for example `/var/lib/litefs/<site>/mnt`, with `allow-other` so
   the web server's user can open it), the data directory, and the lease. With a static lease every node names the
   primary (`candidate: true` on the primary only) and the replicas carry its advertise URL.
3. Start the primary first, then the replicas, and import the site's database on the primary:
   `litefs import -name exponential.db var/storage/sqlite3/exponential.db`.
4. Point the site at the file in the mount, on every node:

       [DatabaseSettings]
       DatabaseImplementation=sqlite3
       Database=/var/lib/litefs/<site>/mnt/exponential.db

5. Send the writes to the primary (Velocity's `writeForward`, or the load balancer), clear the caches, then publish
   through a replica's host name and read the change back on the replica.

To move the primary with a static lease, stop every node (the old primary first) so that there are never two writers,
change the configs and start the new primary first. Writes the old primary had not shipped yet are lost; a Consul
lease fails over by itself.

**Back up with Litestream**

For a site on one server, `litestream replicate` runs as a service with one entry per database:

    # /etc/litestream.yml
    dbs:
      - path: /var/www/example.com/var/storage/sqlite3/exponential.db
        replicas:
          - url: s3://backup-bucket/example.com/exponential.db

`litestream restore -o <new file> s3://backup-bucket/example.com/exponential.db` brings back the latest copy
(`-timestamp` a point in time). Restore into a new file, check it with `PRAGMA integrity_check`, and only then point
the site at it. On a LiteFS cluster, take the backup from the primary (`litefs export -name exponential.db <file>`, or
`sqlite3 <file> ".backup <target>"`).

7x offers hosted SQLite clusters of Exponential built this way (7x MasterRec): LiteFS across its servers, Velocity
forwarding the writes, and a continuous Litestream copy to a backup server.

## Related pages

- Specifications: [SQLite3 database driver](../../specifications/6.0/sqlite3-database-driver.md), [database drivers and installers, 16 to 30 September 2026](../../specifications/6.0/database-drivers-2026-09.md), [SQLite and Oracle driver behaviour (October 2026)](../../specifications/6.0/database-drivers-sqlite-oracle.md), [platform SQLite installer](../../specifications/6.0/platform-sqlite-installer.md)
- [SQLite: transactions queue for the write lock](../../bc/6.0/sqlite-transactions.md)
- [Install in one command](install-in-one-command.md), [SQLite for Exponential Platform](platform-sqlite-install.md), [MongoDB database support](mongodb-database-support.md)
- [PHP 8 support](../../bc/6.0/php8.md) (the countable and `is_countable` fixes that came out of the SQLite test runs)
- Changelogs: [6.0.1](../../../changelogs/6.0/6.0.1.md) (the driver), [6.0.13](../../../changelogs/6.0/6.0.13.md) (absolute paths, autoload), [6.0.14](../../../changelogs/6.0/6.0.14.md) (schema reading)
- History: [January 2024, first half](../../history/2024/2024-01a.md), [January 2024, second half](../../history/2024/2024-01b.md), [April 2026](../../history/2026/2026-04.md), [June 2026, second half](../../history/2026/2026-06b.md) (dropping indexes)

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../help/partners/partners-start-here.md) and [How to apply](../../help/partners/how-to-apply.md).
<!-- exp-partner-block:end -->
