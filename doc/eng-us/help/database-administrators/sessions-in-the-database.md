---
title: Sessions in the database
audience: dba
category: database-administration
order: 35
summary: When Exponential keeps sessions in the ezsession table and when in PHP's own storage, what the table holds, how to switch, how expired rows are removed (ezsessiongc.php and the session_gc.php cronjob script, which no shipped part runs), what ForceStart costs, and why several web servers need database sessions or sticky routing.
keywords: sessions, ezsession, ezpSessionHandlerDB, ezpSessionHandlerPHP, Session Handler, SessionTimeout, ForceStart, ezsessiongc, session_gc.php, cronjob, garbage collection, ezuservisit, several web servers, sticky sessions, query cache ExcludeTables
type: help
---
# Sessions in the database

This page is for database administrators who decide whether an Exponential site keeps its sessions in the database,
and who look after the `ezsession` table when it does. It answers: which handler keeps sessions where, what the table
holds, how expired rows are removed, and what changes with several web servers. Source:
[Sessions](../../guides/sessions.md) and `settings/site.ini [Session]`.

## Contents

1. [Two handlers](#two-handlers)
2. [The ezsession table](#the-ezsession-table)
3. [Switching to the database handler](#switching-to-the-database-handler)
4. [Removing expired sessions](#removing-expired-sessions)
5. [Anonymous sessions and ForceStart](#anonymous-sessions-and-forcestart)
6. [Several web servers](#several-web-servers)
7. [Checklist](#checklist)

## Two handlers

`site.ini [Session] Handler` decides:

| Value | Where sessions are kept | Administration |
|---|---|---|
| empty (the default): `ezpSessionHandlerPHP` | PHP's own session storage (`session.save_handler`, usually files on the web server) | **Setup > Sessions** shows recent sign-ins from `ezuservisit`, but cannot list or remove sessions |
| `ezpSessionHandlerDB` | the table `ezsession` of the site's database | **Setup > Sessions** lists, counts and removes sessions |

A session ends `[Session] SessionTimeout` seconds after its last use (default 259200, three days). With the PHP handler
PHP removes expired sessions itself (`session.gc_maxlifetime`, which Exponential sets to `SessionTimeout`).
`ezuservisit` (last visit, sign-in count, failed attempts) is kept with either handler.

## The ezsession table

From `kernel/sql/mysql/kernel_schema.sql`:

| Column | Meaning |
|---|---|
| `session_key` | the primary key (32 characters) |
| `expiration_time` | when the session ends; indexed |
| `user_id` | the signed-in user, or the anonymous user; indexed |
| `user_hash` | a hash bound to the user |
| `data` | the serialized session data |

Session keys are credentials: whoever has one is signed in as that user. Treat the table like a password store;
the administration never shows the keys, only a short reference. The SQL query cache never caches reads of
`ezsession` (`querycache.ini ExcludeTables[]=ezsession`).

## Switching to the database handler

```ini
# settings/override/site.ini.append.php
[Session]
Handler=ezpSessionHandlerDB
```

```bash
php bin/php/ezcache.php --clear-tag=ini --allow-root-user
./console exp:velocity deploy --allow-root-user
```

Everybody signs in again once: sessions kept by PHP are not carried over. The change note
[ezpSessionHandlerDB on PHP 8](../../bc/6.0/ezpsessionhandlerdb-php8-bugfix-and-tests.md) covers a fix to this
handler.

## Removing expired sessions

With the database handler expired rows stay in `ezsession` until something removes them:

- **Setup > Sessions, Remove timed out / old sessions**: removes the expired rows and the baskets they leave; nobody
  is signed out.
- **`php bin/php/ezsessiongc.php`** (`exp:ezsessiongc`): the same from a shell, "as defined by
  site.ini[Session]SessionTimeout".
- **`cronjobs/session_gc.php`**: the cronjob script. No part of the shipped `settings/cronjob.ini` runs it; add it to a
  part in your override:

  ```ini
  # settings/override/cronjob.ini.append.php
  [CronjobPart-infrequent]
  Scripts[]=session_gc.php
  ```

Watch the figure "timed out but still in the table" on **Setup > Sessions**: it is red when there are any.

## Anonymous sessions and ForceStart

`[Session] ForceStart=enabled` opens a session for every anonymous visitor. With the database handler that writes a
row for every visit; set it only if anonymous visitors must be counted, and size the table and its cleanup for it.

## Several web servers

With the PHP handler each web server knows only its own sessions. On several servers either keep sessions in the
database (every server reads the same table) or route each user to one server by the session cookie
([Several web nodes, sessions](../hosting/operations/several-web-nodes.md#sessions)). SQLite cannot be the database
of a multi-server site, so database sessions there mean a server engine.

## Checklist

- [ ] The handler is chosen deliberately: PHP files on one server, the database for several.
- [ ] With the database handler, `session_gc.php` runs from a part, or `ezsessiongc.php` from cron.
- [ ] `ForceStart` stays disabled unless anonymous sessions are needed.
- [ ] Backups of `ezsession` are treated as secrets, or the table is left out of copies given to others.

## Related pages

- [Sessions: who is signed in](../../guides/sessions.md)
- [Housekeeping: cronjobs and queue tables](housekeeping-cronjobs-and-queues.md)
- [13.7 Sessions and cookies](../../install/13-security-hardening.md#137-sessions-and-cookies)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
