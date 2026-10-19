---
title: "Lesson 3.3: Database servers for a site"
audience: trainer, hosting, admin
category: training
order: 33
summary: Learners create a PostgreSQL or MariaDB database and user with only the rights Exponential needs, install a training site on it with exp:install, and recognize the usual connection, character set and sequence problems. 45 minutes.
keywords: hosting and operations, PostgreSQL, MariaDB, MySQL, MongoDB, Oracle, create database, utf8mb4, pgcrypto, sequences, InnoDB, exp:install --db, EXP_INSTALL_DB_PASSWORD, replicas
type: help
---
# Lesson 3.3: Database servers for a site

When a site needs a database server, hosting staff create the database, its user and its rights, and hand the
installer the connection. This lesson shows the steps for PostgreSQL and MariaDB (and MySQL, which uses the same
driver), the rights that are enough, and the problems support staff meet afterwards. MongoDB and Oracle are covered
by their own host pages.

> **Note**
> Use a database server on the learner's own virtual machine. Never create databases or users on a shared customer
> server in class.

## At a glance

| | |
|---|---|
| Course | [Hosting and operations](../README.md) |
| Module | [Module 3: Databases](README.md) |
| Duration | 45 minutes (15 presenting, 30 exercise) |
| Level | Intermediate |
| Audience | System administrators and hosting support staff |
| Prerequisites | [Lesson 3.2](sqlite-in-production.md); PostgreSQL or MariaDB installed on the learner's virtual machine, with the PHP extension `pgsql` or `mysqli` |

## Learning objectives

After this lesson, learners can:

- create a PostgreSQL database owned by the site's user, or a MariaDB database in `utf8mb4` with per-database rights;
- install a site on it with `exp:install --db=...` without putting the password in the shell history;
- explain why `pgcrypto` and PostgreSQL sequences matter;
- recognize the usual problems: the "database cannot be reached" page, `?` instead of accented letters, duplicate
  keys after an import.

## Trainer's notes

### How to present it

Source: [Chapter 9, sections 9.3 and 9.4](../../../../install/09-databases.md#93-mysql-and-mariadb),
[PostgreSQL for hosts](../../../hosting/basics/postgresql-for-hosts.md) and
[MariaDB for hosts](../../../hosting/basics/mariadb-for-hosts.md).

1. **PostgreSQL** (5 minutes). `createuser --pwprompt`, then `createdb --owner=... --encoding=UTF8
   --template=template0`. The owner matters: from PostgreSQL 15 on, ordinary users may not create tables in the
   `public` schema of a database they do not own. `pgcrypto` provides `digest()`; the installer creates it when the
   server ships it (a trusted extension from PostgreSQL 13). After an import with explicit ids the sequences stay
   behind; correct them with `setval`.
2. **MariaDB and MySQL** (5 minutes). Create the database in `utf8mb4`; grant `SELECT, INSERT, UPDATE, DELETE,
   CREATE, DROP, INDEX, ALTER, LOCK TABLES, CREATE TEMPORARY TABLES` on that database only. No global privilege is
   needed. The installer creates InnoDB tables when the server offers it.
3. **Installing** (3 minutes). Pass the password in `EXP_INSTALL_DB_PASSWORD`, so it stays out of the shell history.
4. **Other engines** (2 minutes). MongoDB needs PHP 8.2, a Composer library and indexes; Oracle needs `oci8` and is
   usually the customer's own database. Point to their host pages.

### Common questions

**Can reads go to a replica?** On MySQL and MariaDB, `UseSlaveServer=enabled` with the replica lists sends reads to a
replica chosen at random per request; writes go to `Server`. Replication lag means a page may show content a few
seconds old.

**Why do accented letters come back as `?`?** The connection fell back to another charset. Enable
`[DebugSettings] AlwaysLog[]=warning`, load a page, and look for "could not set the connection charset" in
`var/log/warning.log`.

**The wizard refuses the database's charset.** It was created in `latin1` or another single-byte charset; recreate it
in `utf8mb4`.

**PostgreSQL refuses the connection.** Check `listen_addresses` in `postgresql.conf` and `pg_hba.conf`.

### Pitfalls

- **Passwords on the projector.** Use a throwaway password for the training database and never show a real one.
- **Granting everything.** Learners used to `GRANT ALL ON *.*` should see that per-database rights are enough.
- **Installing into an existing training database.** `exp:install` installs into the database named; use a new,
  empty database for the exercise.

## Hands-on exercise

Use your own virtual machine with PostgreSQL (steps 1 to 4) or MariaDB (steps 1a to 4a), and a fresh copy of the
code for a second training installation.

1. PostgreSQL: create the user and the database (enter a training password when asked):

   ```bash
   sudo -u postgres createuser --pwprompt exptraining
   sudo -u postgres createdb --owner=exptraining --encoding=UTF8 --template=template0 exptraining
   ```

   1a. MariaDB: in the `mariadb` client as an administrator, run:

   ```sql
   CREATE DATABASE exptraining CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   CREATE USER 'exptraining'@'localhost' IDENTIFIED BY '<a training password>';
   GRANT SELECT, INSERT, UPDATE, DELETE, CREATE, DROP, INDEX, ALTER,
         LOCK TABLES, CREATE TEMPORARY TABLES ON exptraining.* TO 'exptraining'@'localhost';
   ```

2. Preview the install command with `--help` and find the `--db` values: `php bin/php/console exp:install --help`.
3. Install, with the password in the environment:

   ```bash
   read -rs EXP_INSTALL_DB_PASSWORD; export EXP_INSTALL_DB_PASSWORD
   php bin/php/console exp:install --db=pgsql --db-host=127.0.0.1 --db-name=exptraining --db-user=exptraining
   ```

   3a. MariaDB: the same with `--db=mariadb`.
4. PostgreSQL: check that `pgcrypto` exists:
   `psql -h 127.0.0.1 -U exptraining -d exptraining -c '\dx'`.

   4a. MariaDB: check the table engine of one table:
   `mariadb -u exptraining -p -e "SHOW TABLE STATUS FROM exptraining LIKE 'ezcontentobject'\G"`.
5. Start the site with `php bin/php/console exp:velocity start` and open it.
6. In the administration, open **Setup**, **System information** and check the **Database** card.
7. Run `unset EXP_INSTALL_DB_PASSWORD` and check that `history | tail -5` shows no password.

## Expected result

- The install finishes and the site answers.
- Step 4 lists `pgcrypto`, or step 4a shows `Engine: InnoDB`.
- Step 6 shows PostgreSQL or MariaDB with its server version and `UTF8` or `utf8mb4`.
- Step 7: the password does not appear in the shell history.
- The trainer checks by asking each learner to read the **Database** card's engine and character set aloud.

## Quiz

1. Why must the PostgreSQL user own the site's database?
2. Which MariaDB rights does the site's user need?
   A. `ALL PRIVILEGES ON *.*` B. `SUPER` C. per-database rights including `LOCK TABLES` and `CREATE TEMPORARY TABLES`
   D. read-only rights
3. What is `pgcrypto` needed for?
4. After an import with explicit ids, inserts fail with a duplicate key on PostgreSQL. What is wrong?
5. How does `exp:install` take the database password without showing it in the shell history?

<details>
<summary>Answers</summary>

1. From PostgreSQL 15 on, ordinary users may not create tables in the `public` schema of a database they do not own.
2. **C.** No global privilege is needed ([Create the database and the user](../../../../install/09-databases.md#create-the-database-and-the-user)).
3. The driver computes MD5 with `digest()`, which comes from `pgcrypto`.
4. The sequences stayed behind their tables; correct them with `setval`.
5. From the environment variable `EXP_INSTALL_DB_PASSWORD`.

</details>

## Further reading

- [PostgreSQL for hosts](../../../hosting/basics/postgresql-for-hosts.md)
- [MariaDB for hosts](../../../hosting/basics/mariadb-for-hosts.md) and
  [MySQL for hosts](../../../hosting/basics/mysql-for-hosts.md)
- [MongoDB for hosts](../../../hosting/basics/mongodb-for-hosts.md) and
  [Oracle for hosts](../../../hosting/basics/oracle-for-hosts.md)
- [Databases and users in hosting panels](../../../hosting/panels/databases-and-users.md)

## Related pages

- [Module 3: Databases](README.md)
- [Hosting and operations: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
