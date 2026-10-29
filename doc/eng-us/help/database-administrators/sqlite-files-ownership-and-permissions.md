---
title: SQLite files, ownership and permissions
audience: dba
category: database-administration
order: 11
summary: The four files of an Exponential SQLite database, which process must be able to write which file, how to set up one common group with the setgid bit and a umask of 0002 so that PHP-FPM, Velocity, cronjobs and console commands never lock each other out, and how to make sure the database is never served over HTTP.
keywords: SQLite, permissions, ownership, group, setgid, 2775, umask 0002, attempt to write a readonly database, -wal, -shm, writer-lock, var/storage/sqlite3, modfix, HTTP, download, security
type: help
---
# SQLite files, ownership and permissions

This page is for database administrators and system administrators who set up or repair the files of an Exponential
SQLite database. With SQLite the file system is the access control: the right owner, group and mode are what grants
and roles are on a server database. This page answers: which files exist, who must write them, how to set them up
once so that every process keeps working, and how to check that the database cannot be downloaded. Source:
[Files, ownership and mixed users](../../install/09-databases.md#files-ownership-and-mixed-users).

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
> panel: [Plesk](../hosting/panels/plesk.md), [cPanel](../hosting/panels/cpanel.md), [DirectAdmin](../hosting/panels/directadmin.md), [ISPConfig](../hosting/panels/ispconfig.md).

## Contents

1. [The four files](#the-four-files)
2. [Who uses the database](#who-uses-the-database)
3. [Set it up once](#set-it-up-once)
4. [Check after a day](#check-after-a-day)
5. [What not to do](#what-not-to-do)
6. [Never serve the database over HTTP](#never-serve-the-database-over-http)
7. [Checklist](#checklist)

## The four files

| File | What it is | Who needs to write it |
|---|---|---|
| `exponential.db` | the database | every process that writes |
| `exponential.db-wal` | the write-ahead log | every process that writes |
| `exponential.db-shm` | the shared-memory index of the WAL | **every process, readers too** |
| `exponential.db.writer-lock` | the driver's write queue | any process (read-only is enough for `flock()`) |

SQLite creates and removes `-wal` and `-shm` itself, so the **directory** must be writable by every process as well.
The driver creates `var/storage/sqlite3/` with mode `0775`, but it does not set the mode or owner of the files: they
get the owner and umask of whichever process created them first.

## Who uses the database

On a typical server three kinds of process open it:

- the web server's PHP (PHP-FPM or mod_php), as the site user;
- Velocity workers;
- cronjobs and console commands, often another user, sometimes `root`.

If one of them creates `-shm` with mode `0644`, the others fail with "attempt to write a readonly database".

## Set it up once

```bash
# one group that every process runs with (the web server's group is usual)
chgrp -R <web group> var/storage/sqlite3
# the setgid bit: files created in the directory inherit its group
chmod 2775 var/storage/sqlite3
chmod 664 var/storage/sqlite3/*
```

Then give every process a umask of `0002`, so new files are group-writable:

- in a systemd unit: `UMask=0002`;
- at the top of a cron line: `umask 002; cd /path/to/installation && php runcronjobs.php ...`;
- in the PHP-FPM pool and Velocity's service, through their service manager.

[Chapter 8](../../install/08-serving-the-site.md#87-file-permissions-and-ownership) covers the users the servers run
as; [Writable directories and permissions](../hosting/basics/writable-directories-and-permissions.md) the same for
hosting companies.

## Check after a day

```bash
ls -l var/storage/sqlite3/
```

Every file should be group-writable and carry the common group (owner, group and sizes are yours):

```text
-rw-rw-r-- 1 example www-data 187678720 Oct  5 10:14 exponential.db
-rw-rw-r-- 1 example www-data     32768 Oct  5 10:20 exponential.db-shm
-rw-rw-r-- 1 example www-data   4124152 Oct  5 10:20 exponential.db-wal
-rw-rw-r-- 1 example www-data         0 Oct  5 09:58 exponential.db.writer-lock
```

## What not to do

- **Do not run `bin/modfix.sh`** (`bin:modfix`) to fix this. It makes `var/storage` and other directories
  world-writable (mode `777`, files `666`), and the script itself warns that this is not secure. Fix the group and
  mode as above.
- **Do not delete `-wal` or `-shm`** while any process has the database open.
- **Do not put the database on a network file system** shared by several machines: WAL needs shared memory on one
  host.
- **Do not copy a busy database file** as a backup; use `.backup` or `VACUUM INTO`
  ([Backups per engine](backups-per-engine.md)).

## Never serve the database over HTTP

The database lives below `var/storage`, which the shipped `.htaccess` sends to `index.php` and which Velocity's list
of static files leaves out. If the web server is configured by hand, check it on your own host; anything but `200`
is right:

```bash
curl -sI https://www.example.com/var/storage/sqlite3/exponential.db | head -1
```

A database kept at an absolute path outside the document root (`Database=/srv/exponential/data/site.db`) cannot be
served at all; the same ownership rules apply to that directory.

## Checklist

- [ ] One common group for every process; directory `2775`; files `664`.
- [ ] Every process (PHP-FPM, Velocity, cron, console) runs with umask `0002`.
- [ ] No file in `var/storage/sqlite3/` is owned by a user the others cannot write for.
- [ ] `curl -sI` on the database address does not return `200`.
- [ ] Backups of the file are readable only by administrators.

## Related pages

- [SQLite 3 for database administrators](sqlite-for-database-administrators.md)
- [Securing the database](securing-the-database.md)
- [Troubleshooting database errors](troubleshooting-database-errors.md)
- [SQLite for hosts](../hosting/basics/sqlite-for-hosts.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
