---
title: "Lesson 9.5: Migrations"
audience: trainer, developer
category: training
order: 95
summary: Learners ship a database change as a migration - generate a skeleton, write plan(), up() and down() for a table, check the file without a database, read the dry run, simulate on SQLite, run it without a commit and take it back with down() - and learn why every installation then gets the same change in the same order. 50 minutes.
keywords: developers course, migrations, migration, exp:migration:generate, exp:migration:check, exp:migration:migrate, dry run, simulate, rollback, down, expMigrationPhp, expKernelTableSchema, caption, schema change, database change, deploy
type: help
---
# Lesson 9.5: Migrations

In lessons 9.3 and 9.4 learners created a table by hand on their own installation. A real site has a development
copy, a staging copy and production, and every one of them needs that table. A migration is the change written once
as a file in git and applied to every installation in the same order, with a record of what ran, a backup (a
caption) before the run and a way back. In this lesson learners write one, run it and take it back.

## At a glance

| | |
|---|---|
| Course | [Developers](../README.md) |
| Module | [Module 9: The console and persistent objects](README.md) |
| Duration | 50 minutes (20 presenting, 30 exercise) |
| Level | Advanced |
| Audience | Developers |
| Prerequisites | [Lesson 9.4](sql-safely-with-ezdb.md) |

## Learning objectives

After this lesson, learners can:

- explain what a migration is and why a table made by hand is not enough for a site with several installations;
- generate a migration with `exp:migration:generate --skeleton` and name its parts: header, namespace, class;
- write `plan()`, `up()` and `down()` for a table with `expKernelTableSchema::install()`;
- check a migration with `exp:migration:check`, read the dry run and simulate it on SQLite;
- run it with `--apply --no-commit`, read the status, and take it back with `exp:migration:rollback <id> --apply`.

## Trainer's notes

### How to present it

Source: [Migrations: the guide](../../../../guides/migrations.md) and
[the tutorial](../../../../guides/migrations-tutorial.md).

1. **The problem** (3 minutes). Ask: "You made `mysite_greeting` on your laptop. How does it get to production?"
   Collect answers (a SQL file, a note in a ticket, by hand). Then name what each lacks: an order, a record of what
   ran where, a backup, a way back.
2. **The file** (4 minutes). Show `migrations/20261101140000_mysite_note_table.php`: the id is the file name without
   `.php`; the comment lines at the top are the header (`description`, `requires`, `environments` ...); the class is
   in `Site\Migrations` and extends `\expMigrationPhp`.
3. **The three methods** (4 minutes). `plan()` says what would change and writes nothing (the dry run shows it);
   `up()` makes the change and returns what it did, or throws; `down()` undoes it. Everything goes through
   `$context->db()`, the run's connection.
4. **The life cycle** (5 minutes). Write the commands on the board: `generate`, `check`, `migrate` (a dry run),
   `dry-run --simulate --diff`, `migrate --apply`, `status`, `rollback <id> --apply`. Every command that writes is a
   dry run until it gets `--apply`.
5. **The safety net** (4 minutes). A run holds a lock, takes a caption first, runs each migration in a transaction
   where the database has one (SQLite and PostgreSQL: everything; MySQL: data only), stops at the first failure and
   restores the caption. After a run the record is committed, tagged and pushed; in class we switch that off with
   `--no-commit`.

### Demonstration

On the trainer's installation, before the exercise: run `./console exp:migration:status` (the kernel's upgrade steps
appear first, as migrations of the source `kernel`), generate a skeleton without `--apply` to show what it would
write, and run `./console exp:migration:check`.

### Common questions

**Is this the same as `exp:upgrade`?** No, but they share the record table, the lock and the captions. `exp:upgrade`
brings an installation to a new kernel release; migrations carry one site's own changes.

**Can I change a migration after it ran?** No. The record keeps a checksum and the next run refuses the changed file.
Write a new migration.

**Can a migration be YAML or SQL?** In 6.0.15 migrations are written in PHP; other types are found and checked but
not run.

### Pitfalls

- **The leading backslash.** The file has a namespace: write `\expMigrationPhp`, `\expKernelTableSchema`,
  `\RuntimeException`.
- **A second class in the file.** One class per file; `exp:migration:check` refuses two.
- **A connection of one's own.** `eZDB::instance()` works, but outside the run's transaction; use `$context->db()`.
- **Running as root.** Run the commands as your own user, as in lesson 9.1.

## Hands-on exercise

On the development installation (SQLite), from the installation root.

1. Check that the record tables exist: `./console exp:upgrade:status` shows `6.0.15-migration-tables` as `done`; if
   it is pending, run `./console exp:upgrade --apply`.
2. Generate the migration:

   ```bash
   ./console exp:migration:generate --skeleton mysite_note_table --description="A table for notes on greetings, with a first note" --apply
   ```

3. Open the file it names and replace the class body so that it creates the table `mysite_note` with the columns
   `id` and `text`, writes one note, and drops the table in `down()`.
4. Check it without a database: `./console exp:migration:check`.
5. Read the dry run: `./console exp:migration:migrate`.
6. Simulate it: `./console exp:migration:dry-run --simulate --diff`. Find the line that says the table was added.
7. Run it without a commit: `./console exp:migration:migrate --apply --no-commit`.
8. Read the record: `./console exp:migration:status`, then `./console exp:migration:migrate` again.
9. Take it back: `./console exp:migration:rollback <id>` (a dry run), then with `--apply`.
10. Run it once more with `--apply --no-commit`, and commit the migration file to your branch.

### Solution

```php
<?php
// description: A table for notes on greetings, with a first note
// author: Course learner
namespace Site\Migrations;

final class MysiteNoteTable20261101140000 extends \expMigrationPhp
{
    const TABLE = 'mysite_note';

    public static function schema()
    {
        return array( self::TABLE => array(
            'name' => self::TABLE,
            'fields' => array(
                'id' => array( 'length' => 11, 'type' => 'int', 'not_null' => '1', 'default' => 0 ),
                'text' => array( 'length' => 255, 'type' => 'varchar', 'not_null' => '1', 'default' => '' ),
            ),
            'indexes' => array( 'PRIMARY' => array( 'type' => 'primary', 'fields' => array( 'id' ) ) ),
        ) );
    }

    public function plan( $context )
    {
        return array( 'create the table ' . self::TABLE, 'write the first note' );
    }

    public function up( $context )
    {
        $messages = array();
        if ( !\expKernelTableSchema::install( array( self::TABLE ), $context->db(), self::schema(), $messages ) )
            throw new \RuntimeException( 'the table could not be created: ' . implode( '; ', $messages ) );
        $context->db()->query( 'INSERT INTO ' . self::TABLE . " ( id, text ) VALUES ( 1, 'First note' )" );
        return array( 'created ' . self::TABLE, 'wrote the note 1' );
    }

    public function down( $context )
    {
        $context->db()->query( 'DROP TABLE ' . self::TABLE );
        \expKernelTableSchema::reset();
        return array( 'dropped ' . self::TABLE );
    }
}
```

## Expected result

- Step 4 lists the migration as `ok` and ends with `PASS`.
- Step 5 lists it as `would-apply` with the two lines of `plan()`.
- Step 6 lists it as `simulated` with `+ table mysite_note (2 columns, 1 row)` and warns that the database was rolled
  back.
- Step 7 lists it as `applied` with the two lines of `up()`; the log is in `migrations/runs/<run-id>.log`.
- Step 8 shows it as `done`; the second dry run does nothing.
- Step 9 lists it as `would-undo`, then `undone`; the status shows it as `pending` again.
- The trainer asks each learner to say what the dry run, the simulation and the run each changed in the database.

## Quiz

1. What is the id of the migration `migrations/20261101140000_mysite_note_table.php`?
2. Which method may never write to the database?
   A. `up()` B. `down()` C. `plan()` D. none of them
3. What does `exp:migration:migrate` do without `--apply`?
4. Why does the simulation not run on MySQL?
5. A migration ran on staging and you find a mistake in it. What do you do?

<details>
<summary>Answers</summary>

1. `20261101140000_mysite_note_table`.
2. **C.** `plan()`: it runs on every dry run.
3. A dry run: it lists every pending migration with its plan and changes nothing.
4. MySQL commits a schema change at once, so a transaction cannot roll it back.
5. Write a new migration that corrects it; the file that ran stays as it is (the checksum guard refuses a changed
   file).

</details>

## Further reading

- [Migrations: the hub](../../../migrations/README.md).
- [Migrations: a first PHP migration, step by step](../../../../guides/migrations-tutorial.md).
- [PHP migrations](../../../../guides/migrations-php.md).
- [Add a database table](../../../migrations/recipes/add-a-database-table.md).

## Related pages

- [Module 9: The console and persistent objects](README.md)
- [Developers: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
