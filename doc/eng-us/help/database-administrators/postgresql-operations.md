---
title: "Operating PostgreSQL: sequences, case and gotchas"
audience: dba
category: database-administration
order: 13
summary: Day-to-day knowledge for running Exponential on PostgreSQL - how auto-increment columns map to sequences named table_column_seq, how to correct them after loading data, the sequence rename that sites from 5.x need, case-sensitive comparison, persistent connections, backups and the errors you are most likely to meet.
keywords: PostgreSQL, sequences, setval, nextval, currval, _seq, sequence rename, duplicate key, case sensitive, lower, pg_pconnect, persistent connection, pg_dump, pg_restore, backticks, gotchas
type: help
---
# Operating PostgreSQL: sequences, case and gotchas

This page is for database administrators who run an Exponential site on PostgreSQL. It answers: how the schema
handles auto-increment ids, what to do after a data load or a partial restore, what changed in sequence names
between 5.x and 6.0, how comparisons differ from MySQL, and which errors are common. Sources:
[9.4 PostgreSQL](../../install/09-databases.md#94-postgresql) and
[11.3 The 6.12, 7.2 and 7.3 directories](../../install/11-upgrading.md#the-612-72-and-73-directories).

## Contents

1. [Sequences](#sequences)
2. [Correcting sequences](#correcting-sequences)
3. [Sequence names on sites from 5.x](#sequence-names-on-sites-from-5x)
4. [Case and comparison](#case-and-comparison)
5. [Persistent connections](#persistent-connections)
6. [Backups](#backups)
7. [Gotchas](#gotchas)

## Sequences

Auto-increment columns are integers with a default of `nextval('<table>_<column>_seq')`. The schema handler
(`lib/ezdbschema/classes/ezpgsqlschema.php`) creates each sequence before its table (`CREATE SEQUENCE IF NOT EXISTS`),
and the driver's `lastSerialID()` reads `currval('<table>_<column>_seq')` after an insert. For example, the ids of
`ezcontentobject` come from `ezcontentobject_id_seq`.

## Correcting sequences

When data is loaded with explicit ids (an import, a restore of selected tables, a move from another engine), the
sequences stay behind and the next insert fails with a duplicate key. The schema installer corrects them itself after
it loads data (`correctSequenceValues()`); for one table by hand:

```sql
SELECT setval('ezcontentobject_id_seq', (SELECT max(id) FROM ezcontentobject));
```

The kernel ships `kernel/sql/postgresql/setval.sql` with such statements for the kernel's tables. Read it before
you run it, and run it as the site's account.

## Sequence names on sites from 5.x

Up to 5.4 the sequences were named `<table>_s`. The 6.0 kernel reads `<table>_<column>_seq`, so on a database that
still has the old names every insert that needs its new id fails. The 5.4 to 6.0.0 update file
(`update/database/postgresql/6.0/dbupdate-5.4-to-6.0.sql`) renames them, each only when the old name exists and the
new one is free, and points each column default at the new name; the 6.0.0 to 6.0.15 file does the same again for a
site that reached 6.0 with an older copy. Its guarded steps are `DO` blocks.

Do not apply `update/database/postgresql/7.2/dbupdate-6.13.0-to-7.2.0.sql` as a file: it comes from the upstream
kernel of 2018, starts from a version no Exponential database has, and its change is already in the 6.0 files.

Check with `\ds` in `psql`: the list shows `<table>_<column>_seq` names only.

## Case and comparison

PostgreSQL compares strings with case. MySQL's default collations do not. A login or identifier lookup written for
MySQL (in an extension, or in your own report query) may therefore find nothing on PostgreSQL. Compare `lower(...)`
on both sides where case must not matter ([Character sets and collations](character-sets-and-collations.md)).

## Persistent connections

`UsePersistentConnection=enabled` makes the driver use `pg_pconnect` instead of `pg_connect`, keeping one connection
per PHP process. Under Velocity's persistent workers each worker keeps its connection for its whole life anyway.
Count the connections you need: PHP-FPM workers plus Velocity workers plus cron, against `max_connections` on the
server. A connection pooler in front of PostgreSQL is a server-side choice; Exponential has no setting of its own for
one, so test it on staging before production.

## Backups

```bash
pg_dump -U exponential -Fc exponential > exponential.dump
pg_restore -U exponential -d exponential --clean exponential.dump
```

The custom format (`-Fc`) is compressed and restores selectively. Point-in-time recovery and streaming replication are
PostgreSQL's own features and work with Exponential as with any client; configure them on the server
([Backups per engine](backups-per-engine.md)).

## Gotchas

| Symptom | Cause |
|---|---|
| "The 'digest' function is not available in your database ..." | `pgcrypto` is missing; create it ([Setting up PostgreSQL](postgresql-setup.md#pgcrypto)) |
| Connection refused | `listen_addresses` or `pg_hba.conf` |
| Duplicate key on insert after an import | Sequences behind their tables; correct them as above |
| Every insert fails after an upgrade from 5.x | Old `<table>_s` sequence names; apply the current 6.0 update file |
| An extension's SQL fails on PostgreSQL only | MySQL syntax such as backtick quoting or MySQL-only `SET` statements ([Reviewing extension SQL](portable-sql-for-extensions.md)) |
| A login works only in one spelling | Case-sensitive comparison |

PostgreSQL's `errorNumber()` in Exponential is the SQLSTATE (a string such as `'23505'` for a duplicate key); before
6.0.15 it was `1` for every error ([Database error codes and messages](error-codes-and-messages.md)).

## Related pages

- [Setting up PostgreSQL](postgresql-setup.md)
- [Upgrades and the SQL update files](upgrades-and-sql-update-files.md)
- [PostgreSQL for hosts](../hosting/basics/postgresql-for-hosts.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
