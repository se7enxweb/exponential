---
title: Check the database schema
audience: government, admin, developer
category: public-sector
order: 220
summary: Step by step - run the database consistency check on Setup > Upgrade check, read missing, changed and extra tables, download the SQL, and apply only what you understand, after a backup.
keywords: database consistency, schema check, Check database consistency, missing table, changed table, Download SQL, db_schema.dba, upgrade, MongoDB collections
type: help
---
# Check the database schema

This task is for administrators and developers after an upgrade, an extension update or a database move. At the end
you know whether the database matches what Exponential and its active extensions expect.
Reference: [The upgrade check, section 6](../../../../guides/upgrade-check.md#6-run-the-database-consistency-check).

## Contents
1. [Before you start](#before-you-start)
2. [Steps](#steps)
3. [Reading the result](#reading-the-result)
4. [Applying SQL safely](#applying-sql-safely)
5. [Related pages](#related-pages)

## Before you start

- An administrator account.
- A recent backup ([Take a backup before a change](take-a-backup-before-a-change.md)).

## Steps

1. **Setup > Upgrade check** (`/setup/systemupgrade`).
2. **Check database consistency**. It compares the database with `share/db_schema.dba` and the schema files of active
   extensions.
3. Read the figures and the card per table; **Download SQL** for the record. The page never runs any SQL.

## Reading the result

| Kind | Meaning | Do |
|---|---|---|
| Missing table | shipped with Exponential or an active extension, not in the database; the feature fails | run its CREATE statement after a backup |
| Changed table | fields or indexes differ | read each statement; those marked **Removes something** can remove data |
| Table not in the schema | an own extension's table, or one of a removed extension | leave it unless you know the data is not needed |
| Table of an extension that is not active | belongs to an installed, inactive extension | no SQL is written for it; activate or leave |
| Engine notes | differences the engine's schema handler writes no SQL for | nothing |

On MongoDB the check compares collections and offers the `mongosh` command for missing ones.

## Applying SQL safely

1. On staging first.
2. One statement at a time, with the vendor's client.
3. Run the check again: the table disappears from the findings.
4. Note the statements in the change record.

## Related pages
- [Check upgrade consistency](check-upgrade-consistency.md)
- [Upgrade overview for the public sector](../../operations/upgrades-and-change/upgrade-overview-for-public-sector.md)
- [PHP and database upgrades](../../operations/upgrades-and-change/php-and-database-upgrades.md)
- [Database support](../../../database-support.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
