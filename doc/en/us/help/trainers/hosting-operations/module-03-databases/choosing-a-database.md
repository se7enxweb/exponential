---
title: "Lesson 3.1: Choosing a database"
audience: trainer, hosting, admin
category: training
order: 31
summary: Learners name the six supported databases in the project's official order, compare them for a hosting offer, and find out which database a site uses in the administration and on the command line. 40 minutes.
keywords: hosting and operations, database, official order, SQLite, PostgreSQL, MongoDB, MariaDB, Oracle, MySQL, Microsoft SQL Server, DatabaseImplementation, exp:ini, System information
type: help
---
# Lesson 3.1: Choosing a database

Exponential 6.x supports six databases, and the project recommends them in a fixed order. Hosting staff must be able
to say which one a plan includes, what each costs to run, and which database a customer's site actually uses. This
lesson gives them the order, the comparison and the two checks.

## At a glance

| | |
|---|---|
| Course | [Hosting and operations](../README.md) |
| Module | [Module 3: Databases](README.md) |
| Duration | 40 minutes (20 presenting, 20 exercise) |
| Level | Intermediate |
| Audience | System administrators, hosting support staff and product managers |
| Prerequisites | [Module 2](../module-02-velocity/README.md); an administrator account on the learner's training installation |

## Learning objectives

After this lesson, learners can:

- list the six supported databases in the official order and explain what the order means;
- compare them on the questions that matter to a host: a server to run, several web servers, PHP extension, backup
  tool;
- choose a database for a single-machine plan and for a plan with several web servers;
- find a site's database in **Setup**, **System information** and with `exp:ini`.

## Trainer's notes

### How to present it

Source: [Database support](../../../database-support.md) and
[Databases in the official order](../../../hosting/basics/databases-in-the-official-order.md).

1. **The order** (4 minutes). Read the support statement aloud: 1. SQLite 3 (the default), 2. PostgreSQL,
   3. MongoDB, 4. MariaDB, 5. Oracle, 6. MySQL. Microsoft SQL Server is known to be possible with additional work and
   has no driver in 6.0.x. The order is the order of recommendation for new sites; it does not mean the later ones
   work less well. Ask learners to use the same order in their own material.
2. **Side by side** (6 minutes). Show the comparison table of the hosting page: `DatabaseImplementation` value, PHP
   extension, `exp:install --db=` value, whether several web servers can share the database (all but SQLite), and the
   backup tool.
3. **Choosing** (5 minutes). More than one machine writing the database: a database server, PostgreSQL first. A
   single machine, shared or not: SQLite covers it with nothing to run. Customers who bring MongoDB or Oracle: offer
   it as an option with its requirements.
4. **Which database does this site use?** (5 minutes). Show the **Database** card on **System information** and the
   `exp:ini get` command.

### Common questions

**Is SQLite a toy for development?** No. Chapter 9 explains why it is a production database for one machine: WAL, a
connection set up for a web server and a write queue (lesson 3.2).

**Why is MySQL last?** Not because it works less well, but because the project recommends the others first for new
sites. It is tested in the project's CI.

**Can I offer Microsoft SQL Server?** There is no driver in 6.0.x; it is known to be possible with additional work.
Do not put it on an order form.

**Which character set?** UTF-8 everywhere: `utf8mb4` for MySQL and MariaDB, `UTF8` for PostgreSQL, `AL32UTF8` for
Oracle.

### Pitfalls

- **Learners reorder the list by habit.** Correct it gently every time; the order is part of the project's support
  statement.
- **"Supported" versus "tested".** The comparison table says which engines run in the project's CI. Do not promise more
  than the table says.
- **Reading settings files by hand.** `settings/override/site.ini.append.php` may hold a password. Use `exp:ini get`
  for one value instead of printing the file on a projector.

## Hands-on exercise

Use your own training installation (installed with `exp:install`, SQLite by default).

1. Sign in to the administration of your training site as an administrator.
2. Open **Setup**, then **System information**.
3. Find the **Database** card and note the engine, the server version or file, the character set, the number of
   tables and the size.
4. Scroll to **Database connection** and note what it shows.
5. In a terminal, in the installation root, ask for the driver:

   ```bash
   php bin/php/console exp:ini get site.ini/DatabaseSettings/DatabaseImplementation
   ```

6. Check which PHP database extensions your PHP has: `php -m | grep -iE 'sqlite3|pgsql|mysqli|mongodb|oci8'`.
7. Open [Databases in the official order](../../../hosting/basics/databases-in-the-official-order.md) and, for two
   imagined plans, write one sentence each: a single-machine plan for small sites, and a plan with two web servers
   behind a load balancer.

## Expected result

- Steps 3 and 5 agree: on a default training installation the engine is SQLite and the driver is `sqlite3`.
- Step 6 lists at least `sqlite3`.
- Step 7: the single-machine plan can use SQLite; the two-server plan needs a database server, PostgreSQL first.
- The trainer checks by asking three learners for their step 7 sentences.

## Quiz

1. Put these in the official order: MySQL, SQLite 3, MariaDB, PostgreSQL.
2. Which supported database cannot be shared by several web servers?
   A. PostgreSQL B. MariaDB C. SQLite 3 D. MongoDB
3. What `DatabaseImplementation` value do MySQL and MariaDB use?
4. Where in the administration do you see the database engine and its size?
5. True or false: a database later in the order works less well than one earlier in it.

<details>
<summary>Answers</summary>

1. SQLite 3, PostgreSQL, MariaDB, MySQL.
2. **C.** WAL uses shared memory on one machine; a network file system is not a substitute.
3. `ezmysqli`.
4. **Setup**, **System information**, the **Database** card
   ([Database support](../../../database-support.md#which-database-is-my-site-using)).
5. **False.** The order is a recommendation for new sites, not a quality ranking.

</details>

## Further reading

- [Database support](../../../database-support.md): the support statement and every detail.
- [Chapter 9: Databases](../../../../install/09-databases.md): the operator's reference.
- [Databases in the official order](../../../hosting/basics/databases-in-the-official-order.md): the comparison for
  hosts.
- [Microsoft SQL Server status](../../../hosting/basics/microsoft-sql-server-status.md): what "possible with
  additional work" means.

## Related pages

- [Module 3: Databases](README.md)
- [Hosting and operations: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
