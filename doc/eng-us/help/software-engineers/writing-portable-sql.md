---
title: Writing portable SQL
audience: software-engineer, developer
category: software-engineering
order: 220
summary: How to write queries that run on every database Exponential supports - escaping and casting, limits as parameters, the dialect differences of each driver (row limits, ids, concatenation, MD5, bitwise operators, joined updates, empty strings, case), and how to test a query on more than one engine.
keywords: SQL, portable SQL, dialect, escapeString, cast, LIMIT, OFFSET, sequences, concatenation, MD5, bitwise, UPDATE JOIN, empty string, Oracle, SQLite, PostgreSQL, MySQL, MongoDB, software engineer
type: help
---
# Writing portable SQL

This page is for engineers who write SQL in kernel or extension code. Exponential runs on six databases, so a query
that works on your development database is not finished until it works on the others. The rules and the dialect table
come from [Database and SQL standards](../../standards/databases-and-sql.md).

## Contents

1. [The two mandatory rules](#the-two-mandatory-rules)
2. [Prefer the API to SQL](#prefer-the-api-to-sql)
3. [Dialect differences](#dialect-differences)
4. [Patterns that stay portable](#patterns-that-stay-portable)
5. [Testing on more than one engine](#testing-on-more-than-one-engine)

## The two mandatory rules

1. **Escape every string** with `$db->escapeString( $value )`, inside single quotes. Each driver uses the engine's own
   function; the Oracle driver doubles single quotes.
2. **Cast every number** with `(int)` before it goes into SQL. A cast cannot be escaped around.

These rules are what the 6.0.13 security fixes restored in the kernel (integer casts, `escapeString()`, a column
whitelist for `ORDER BY`); the same patterns in extension code are not fixed by a kernel update
([Security hardening of 6.0.13](../../bc/6.0/hardening.md)).

## Prefer the API to SQL

- `eZPersistentObject::fetchObjectList()` and the other persistent-object methods escape for you.
- Content queries go through fetch functions or the content classes; they apply permissions and work on every engine,
  MongoDB included.
- Pass offset and limit as the parameters of `arrayQuery()`, so each driver writes its own syntax.

## Dialect differences

| Topic | SQLite 3 | PostgreSQL | MySQL, MariaDB | Oracle | MongoDB adapter |
|---|---|---|---|---|---|
| Row limit | `LIMIT offset, count` | `LIMIT count OFFSET offset` | `LIMIT offset, count` | `OFFSET n ROWS FETCH NEXT m ROWS ONLY` | `limit` and `skip` |
| New ids | `lastInsertRowID()` | sequence `<table>_<column>_seq` | `AUTO_INCREMENT` | sequence `s_<table>` | a counter document per sequence |
| String concatenation | `\|\|` | `\|\|` | `concat( ... )` | `\|\|` | `concat( ... )`, read by the adapter |
| MD5 | user function `md5()` | needs `pgcrypto` | `MD5()` | `STANDARD_HASH( s, 'MD5' )` | hashed in PHP |
| MySQL `UPDATE ... JOIN` | rewritten (needs SQLite 3.33) | not rewritten | native | not rewritten | not applicable |
| Empty string | `''` | `''` | `''` | stored as NULL | not checked |
| String comparison | `=` with case | with case | without case (default collations) | with case | with case unless a collation is set |
| Start of a transaction | `BEGIN IMMEDIATE` after a writer lock | `BEGIN WORK` | `BEGIN WORK` | no auto commit | none |

The full table, with sources, is in [Dialect differences, driver by driver](../../standards/databases-and-sql.md#dialect-differences-driver-by-driver).

## Patterns that stay portable

| Instead of | Write |
|---|---|
| `LIMIT 10` in the SQL string | `array( 'offset' => 0, 'limit' => 10 )` as the parameter |
| `UPDATE a JOIN b ...` | `UPDATE a SET ... WHERE id IN ( SELECT ... )`, or two statements in a transaction |
| Comparing to `''` | handle `''` and `NULL` alike, because Oracle stores the empty string as NULL |
| Relying on case-insensitive `=` | normalize case in PHP or on both sides |
| `SET @var := ...` | read the value in PHP and pass it into the next statement |
| An engine function (`GROUP_CONCAT`, `ILIKE`) | do the work in PHP, or branch on the driver with a fallback for every engine |
| `ORDER BY` a column name from the request | a whitelist of allowed columns |

## Testing on more than one engine

- SQLite needs no server: run your code against an SQLite installation as a second engine.
- CI runs the database tests on MySQL 8.0 and PostgreSQL 15 (PHP 8.3); reproduce them with
  `tests/runtests.php --dsn ... --db-per-test` against a throwaway database ([CI gates](ci-gates.md)).
- Tests that need a live database read its location from environment variables and skip when it is not set
  ([DB-less tests and test suites](db-less-tests-and-test-suites.md)).

## Related pages

- [Persistence and the database layer](persistence-and-the-database-layer.md)
- [Database code for PHP 8](../extension-development/part-4-upgrading/database-code-for-php-8.md)
- [Chapter 9, cross-engine notes](../../install/09-databases.md#98-cross-engine-notes)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
