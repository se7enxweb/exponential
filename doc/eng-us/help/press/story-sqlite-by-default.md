---
title: "Story: SQLite by default"
audience: press
category: press-media
order: 69
summary: A feature story about SQLite as the default database of Exponential - a complete site in one file with no database server, since January 2024 - with what the driver adds, when it fits and when it does not, printable facts and sources.
keywords: story, SQLite, SQLite 3, default database, no database server, one file, write queue, WAL, small sites, press
type: help
---
# Story: SQLite by default

This page is for journalists and creators who write about simple hosting, small organizations, education or
developer laptops. Exponential's default database is SQLite: the whole site in one file. This page explains the
story, when it fits, and what not to promise.

## Contents

1. [The story in one paragraph](#the-story-in-one-paragraph)
2. [What is new, and when](#what-is-new-and-when)
3. [Facts you can print](#facts-you-can-print)
4. [When it fits, and when it does not](#when-it-fits-and-when-it-does-not)
5. [What not to claim](#what-not-to-claim)
6. [Angles and headline ideas](#angles-and-headline-ideas)
7. [Who to ask](#who-to-ask)
8. [Related pages](#related-pages)

## The story in one paragraph

"With SQLite, a complete Exponential site runs from one file on disk, with no database server to install, configure
or secure" ([SQLite database support](../../features/6.0/sqlite-database.md)). It arrived with the first months of the
6.0 line in January 2024 and became the default: of the setup wizard in September 2026 and of the one-command
installer (`./console exp:install`), which installs on SQLite unless told otherwise
([Install in one command](../../features/6.0/install-in-one-command.md)). The README calls it "an incredible
improvement to the kernel" ([README, Recent improvements](https://github.com/se7enxweb/exponential/blob/main/README.md#recent-improvements-to-exponential)).

## What is new, and when

| What | When | Source |
|---|---|---|
| 6.0.0 is stable, and SQLite arrives as a database that needs no server | January 2024 | [January 2024](../../history/2024/2024-01a.md) |
| SQLite support in releases 6.0.1 to 6.0.3, refined since | January to April 2024 | [SQLite database support](../../features/6.0/sqlite-database.md) |
| SQLite is the setup wizard's default; one-command install on SQLite | 16 to 30 September 2026 | [6.0.15 changelog](../../../changelogs/6.0/6.0.15.md#16-to-30-september-2026) |
| A write queue: concurrent publishes wait their turn instead of failing with "database is locked" | 1 and 2 October 2026 | [6.0.15 changelog](../../../changelogs/6.0/6.0.15.md#1-and-2-october-2026) |

The 2026 changes are in the 6.0.15 development line, which is not released.

## Facts you can print

| Fact | Source |
|---|---|
| SQLite 3 is first in the official database order and the default. | [README](https://github.com/se7enxweb/exponential/blob/main/README.md#requirements) |
| The database is four files: the database, `-wal`, `-shm` and `.writer-lock`. | [Database support, SQLite 3](../database-support.md#1-sqlite-3-the-default) |
| Write-ahead logging lets page views keep reading while an editor publishes. | same |
| A queued transaction waits up to 25 seconds by default (`SQLiteTransactionWait`). | same |
| "The project's own load tests had concurrent readers scale with the number of processes and 16 concurrent writers complete without errors, queued." | same |
| Exponential Platform Legacy also offers SQLite ("now with SQLite support"). | [Exponential Platform Legacy](../who-is-the-exponential-foundation.md#exponential-platform-legacy) |

## When it fits, and when it does not

| Fits | Choose a database server instead |
|---|---|
| One machine, any number of readers, an editorial team | More than one machine must write the database |
| Trying Exponential, a laptop, automated tests, a small site | Many editors publish large changes at the same moment all day |
| A hosting plan without MySQL | You need replication or database-level users and grants |

Source: [Database support, SQLite 3](../database-support.md#1-sqlite-3-the-default) and
[SQLite, Limits and good practice](../../features/6.0/sqlite-database.md#limits-and-good-practice).

## What not to claim

- "SQLite is enough for every site." The sources list when to choose a server.
- "No database at all." There is a database; it is a file, not a server.
- "Faster than MySQL." The sources publish no comparison.

## Angles and headline ideas

```text
"A whole website in one file: Exponential makes SQLite the default"
"No database server to secure: the case for SQLite in a CMS"
"Sixteen writers, no errors: how Exponential queues SQLite publishes"
```

## Who to ask

| Question | Ask | Where |
|---|---|---|
| The SQLite work | 7x | info@se7enx.com, or <https://exponential.earth/about/contact-us> |
| Small-site experiences | the community | <https://share.exponential.earth/forums> |

## Related pages

- [Story: six databases](story-databases.md)
- [Story: install in one command](story-install-in-one-command.md)
- [SQLite database support](../../features/6.0/sqlite-database.md)
- [Feature stories: index](feature-stories-index.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
