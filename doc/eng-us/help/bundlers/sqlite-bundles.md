---
title: SQLite bundles
audience: bundler, hosting
category: bundling
order: 250
summary: Why SQLite 3, the default database, suits many bundles, what the driver does for concurrent use, the four files and their permissions, the one-writer-host rule, backups, and when a bundle should offer a database server instead.
keywords: SQLite, SQLite 3, default database, var/storage/sqlite3, WAL, writer-lock, one machine, permissions, backup, appliance, demo, classroom, bundle
type: help
---
# SQLite bundles

This page is for bundlers who ship Exponential on SQLite 3, the default database. SQLite removes the database server
from the bundle: nothing to install, start, secure or update separately.

## Contents

1. [Why SQLite suits bundles](#why-sqlite-suits-bundles)
2. [What the driver adds](#what-the-driver-adds)
3. [Files and permissions](#files-and-permissions)
4. [The one-host rule](#the-one-host-rule)
5. [Backups](#backups)
6. [When to offer a server instead](#when-to-offer-a-server-instead)

## Why SQLite suits bundles

- "SQLite 3 is the default. The setup wizard preselects it, `exp:install` uses it when no `--db` is given, and the
  kickstarter's template names it first. It needs no database server: the whole content repository is one file"
  ([Database support](../database-support.md#the-supported-databases-in-order)).
- "With the driver Exponential ships it is a full production database for a site served from one machine."
- `exp:install` without options installs on SQLite with the file `exponential.db`
  ([Install in one command](../../features/6.0/install-in-one-command.md#choose-a-database)).

## What the driver adds

From [1. SQLite 3, the default](../database-support.md#1-sqlite-3-the-default):

- write-ahead logging, "so page views keep reading while an editor publishes";
- connection PRAGMAs chosen for a web server;
- since 6.0.15, a **write queue**: "every transaction takes an exclusive lock on `<database>.writer-lock` and then runs
  `BEGIN IMMEDIATE`, so concurrent publishes wait their turn (up to `SQLiteTransactionWait` seconds, default 25)".

## Files and permissions

"The database is four files: the database, `-wal`, `-shm` and `.writer-lock`. Every process that uses the site (the
web server's PHP, Velocity's workers, cronjobs, console commands) must be able to write the directory and the files;
give them one common group, the directory mode `2775` and a umask of `0002`. Never serve the directory over HTTP (the
shipped rewrite rules do not)."

For a bundle:

- [ ] `var/storage/sqlite3/` exists and belongs to the site's user (or the common group) in the image.
- [ ] The installer runs as that user.
- [ ] Cron and console commands run as that user.
- [ ] The web server configuration you ship keeps the routing of the shipped rules
      ([Web server examples](../../examples/web-servers/README.md)).

## The one-host rule

"Choose a database server instead when more than one machine must write the database (WAL needs shared memory on one
host, so a network file system is not a substitute)". For containers: "With SQLite, run **one** container that
writes" ([Containers](../hosting/models/containers-docker-and-podman.md#the-database-and-the-one-writer-rule)).

A bundle on SQLite should say so plainly in its documentation, so nobody scales it to three replicas on a shared
volume.

## Backups

The backup set of any installation is "the database and `var/storage`, `settings/override` and `settings/siteaccess`"
([Containers](../hosting/models/containers-docker-and-podman.md#volumes-what-must-persist)). For SQLite, back up the
database consistently rather than copying a file in the middle of a write; the methods are in
[SQLite for hosts: Backup and restore](../hosting/basics/sqlite-for-hosts.md#backup-and-restore) and
[Chapter 9.2](../../install/09-databases.md#92-sqlite-3-a-production-database-in-one-file).

## When to offer a server instead

| Need | Choose |
|---|---|
| Several web servers or replicas | PostgreSQL or another server |
| Replication, database users and grants | a server |
| Many editors publishing large changes all day | a server |
| Everything else on one machine | SQLite is fine |

A bundle can offer both: SQLite by default, PostgreSQL by option (`--db=pgsql`), with the switch documented.
[Changing the database engine](../hosting/migrations/changing-the-database-engine.md) covers moving later.

## Related pages

- [Database choices for bundles](database-choices-for-bundles.md)
- [SQLite for hosts](../hosting/basics/sqlite-for-hosts.md)
- [Education and demo bundles](education-and-demo-bundles.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
