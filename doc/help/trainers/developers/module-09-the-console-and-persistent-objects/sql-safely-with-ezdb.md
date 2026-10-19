---
title: "Lesson 9.4: SQL safely with eZDB"
audience: trainer, developer
category: training
order: 94
summary: Learners write the queries persistent objects cannot express with eZDB, escape every string and cast every number, page results with arrayQuery, group writes in transactions, and recognize the dialect and engine differences that make raw SQL less portable. 45 minutes.
keywords: developers course, eZDB, arrayQuery, query, escapeString, int cast, SQL injection, begin, commit, rollback, transaction, offset, limit, dialects, SQLite, MySQL, PostgreSQL, MongoDB, databases standard
type: help
---
# Lesson 9.4: SQL safely with eZDB

Sometimes a report or a clean-up needs a query that persistent objects cannot express: a join, a group by, an
update of many rows. `eZDB` runs such SQL on the configured database. It is also where most security bugs of old
extensions were: a value from a request put into SQL without escaping. In this lesson learners follow the two rules
that prevent them and learn what changes between database engines.

## At a glance

| | |
|---|---|
| Course | [Developers](../README.md) |
| Module | [Module 9: The console and persistent objects](README.md) |
| Duration | 45 minutes (20 presenting, 25 exercise) |
| Level | Advanced |
| Audience | Developers |
| Prerequisites | [Lesson 9.3](persistent-objects.md) |

## Learning objectives

After this lesson, learners can:

- run a read query with `$db->arrayQuery()` and page it with `offset` and `limit`;
- escape every string with `$db->escapeString()` inside single quotes and cast every number with `(int)`;
- group writes with `$db->begin()`, `$db->commit()` and `$db->rollback()`;
- name dialect differences between SQLite, MySQL and PostgreSQL, and the limits on MongoDB;
- choose between a persistent object and raw SQL.

## Trainer's notes

### How to present it

Source: [Database and SQL standards](../../../../standards/databases-and-sql.md), sections on the eZDB rules and the
dialect differences.

1. **The two rules** (5 minutes). Every string from a request, a setting or the database is escaped with
   `$db->escapeString( $value )` inside single quotes; every number is cast with `(int)` before it goes into SQL.
   Show the example:

   ```php
   $db = eZDB::instance();
   $rows = $db->arrayQuery(
       "SELECT id, name FROM ezsection WHERE identifier = '" . $db->escapeString( $identifier ) . "'"
       . " AND id <> " . (int)$excludeId,
       array( 'offset' => 0, 'limit' => 10 )
   );
   ```

2. **Results** (3 minutes). `arrayQuery()` returns an array of rows (column to value); its second argument pages the
   result in the engine's own syntax. `query()` runs a statement and returns `true` or `false`.
3. **Transactions** (4 minutes). `begin()` and `commit()` nest by counting; only the outermost pair sends `BEGIN`
   and `COMMIT`. `rollback()` rolls back the whole transaction. On SQLite a writer lock serializes writers.
4. **Dialects** (5 minutes). Row limits, new IDs, string concatenation, substrings and `UPDATE ... JOIN` differ per
   engine; let `arrayQuery()`'s `offset` and `limit` and persistent objects handle what they can. On MongoDB, SQL the
   adapter cannot translate returns an empty result with a warning, and transactions do nothing.
5. **Choosing** (3 minutes). Persistent objects first; raw SQL for reports and bulk changes, always with the two rules.

### Common questions

**Is `escapeString()` enough for a number?** No: a number outside quotes cannot be "escaped around"; cast it.

**Can I use prepared statements?** The kernel's interface builds SQL as strings; the two rules are its protection.
Follow them without exception.

**Where are the security fixes of older releases described?** In the 6.0.13 hardening notes linked from the
database standard.

### Pitfalls

- **`LIMIT` written by hand.** It differs per engine; use the `offset` and `limit` parameters.
- **A transaction left open.** A `begin()` without `commit()` or `rollback()` on an error path holds locks; write the
  rollback in the error branch.
- **Testing only on SQLite.** A query that works on SQLite may fail on MySQL or PostgreSQL; test on the engine of the
  live site.

## Hands-on exercise

On the development installation, with the table `mysite_greeting` of lesson 9.3.

1. Create `scripts/report.php`:

   ```php
   <?php
   // Greetings per name: php bin/php/ezexec.php extension/mysite_hello/scripts/report.php
   $cli = eZCLI::instance();
   $db = eZDB::instance();
   $since = time() - 86400;
   $rows = $db->arrayQuery( "SELECT name, COUNT(*) AS total FROM mysite_greeting"
                            . " WHERE created > " . (int)$since
                            . " GROUP BY name ORDER BY total DESC",
                            array( 'offset' => 0, 'limit' => 10 ) );
   foreach ( $rows as $row )
   {
       $cli->output( $row['name'] . ': ' . $row['total'] );
   }
   ```

2. Run it with `ezexec.php`.
3. Add a lookup by a name given in an environment variable, escaped:

   ```php
   $name = (string)getenv( 'GREET_NAME' );
   $count = $db->arrayQuery( "SELECT COUNT(*) AS total FROM mysite_greeting WHERE name = '"
                             . $db->escapeString( $name ) . "'" );
   $cli->output( 'Greetings for ' . $name . ': ' . $count[0]['total'] );
   ```

4. Run it with `GREET_NAME="Ada" php bin/php/ezexec.php extension/mysite_hello/scripts/report.php`, then with
   `GREET_NAME="x' OR '1'='1"`. Compare: the second finds no rows, because the quote is escaped.
5. Add a transaction that renames all `Ada` rows and rolls back:

   ```php
   $db->begin();
   $db->query( "UPDATE mysite_greeting SET name = 'Ada L.' WHERE name = 'Ada'" );
   $db->rollback();
   ```

   Run the report again: nothing changed.
6. Read the dialect table in the database standard and find how a row limit is written on PostgreSQL.
7. Commit the script.

## Expected result

- Step 2 prints a line per name with its count.
- Step 4: a count for `Ada`, and `0` for the injection attempt.
- Step 5: the names are unchanged after the rollback.
- Step 6: `LIMIT count OFFSET offset`.
- The trainer asks each learner to state the two rules.

## Quiz

1. What are the two rules for values in SQL?
2. Which method returns the rows of a `SELECT`?
   A. `query()` B. `arrayQuery()` C. `begin()` D. `escapeString()`
3. How are `offset` and `limit` given so they work on every engine?
4. What happens with nested `begin()` and `commit()` calls?
5. Why prefer a persistent object where one fits?

<details>
<summary>Answers</summary>

1. Escape every string with `$db->escapeString()` inside single quotes; cast every number with `(int)`.
2. **B.** `arrayQuery()`.
3. As the second argument of `arrayQuery()`: `array( 'offset' => ..., 'limit' => ... )`.
4. They nest by counting; only the outermost pair sends `BEGIN` and `COMMIT`.
5. It escapes for you and runs on every supported database, MongoDB included.

</details>

## Further reading

- [Database and SQL standards](../../../../standards/databases-and-sql.md).
- [Security standard](../../../../standards/security.md).
- [MongoDB as the database](../../../../features/6.0/mongodb-database-support.md): what raw SQL cannot do there.

## Related pages

- [Module 9: The console and persistent objects](README.md)
- [Developers: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
