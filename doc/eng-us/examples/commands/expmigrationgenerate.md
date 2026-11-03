---
title: exp:expmigrationgenerate
audience: developer, admin
category: examples
summary: "Write a new migration file: --skeleton writes an empty migration (PHP, or with --type=yml, sql, ini or graphql that kind); --from-recording turns a recording of changes into a YAML migration; --from-content, --from-class, --from-role, --from-section and --from-settings turn what ..."
keywords: exp:expmigrationgenerate, expmigrationgenerate, console, command, CLI, expmigrationgenerate.php, exp:migration:generate
type: generated
generated: exp:help:examples
---
# exp:expmigrationgenerate

Write a new migration file: --skeleton writes an empty migration (PHP, or with --type=yml, sql, ini or graphql that kind); --from-recording turns a recording of changes into a YAML migration; --from-content, --from-class, --from-role, --from-section and --from-settings turn what the installation has into one; --from-diff turns what changed since a caption, another installation or a database dump into one; a dry run unless --apply.

Script `bin/php/expmigrationgenerate.php`, code in `kernel/private/classes/commands/expmigrationgenerate.php`. Run it from the installation root, as the user the web server runs as:

```bash
./console exp:expmigrationgenerate --help
./console exp:migration:generate --help
php bin/php/expmigrationgenerate.php --help
```

## Help text

```text
Write a new migration
--skeleton writes an empty PHP migration named YYYYMMDDHHMMSS_<name>.php after the time of now,
with the header (description, author), plan(), up() and down(), in the site's migrations directory
(the first [MigrationSettings] DirectoryList entry) or, with --source=extension:<name>, in the
extension's. The class is named after the name and the time, in the namespace Site\Migrations or
Extension\<Name>\Migrations. --type=yml writes YYYYMMDDHHMMSS_<name>.yml instead (the header and an
empty list of steps), --type=graphql the same with an example of the step graphql, --type=ini an INI
migration and --type=sql an SQL migration (with --engine=mysql, pgsql, sqlite or oracle the file of one
engine, with --engine=mongodb the .mongodb.json file). Without --apply it shows the file and writes
nothing; an existing file is never replaced.
--from-recording=<id> turns a recording of changes (exp:migration:record:start) into a YAML
migration: its events folded into one step per thing changed, checked as exp:migration:check checks
a file. What no step type repeats is listed, and named in the file, to be done by hand.
--from-content=<node>[,<node>...] (with --depth=<levels below> and --max-objects=<n>), --from-class,
--from-role, --from-section (identifiers or names, comma-separated) and --from-settings=<file>[,...]
(of settings/override, or with --siteaccess=<name> of that siteaccess) write a YAML migration that
makes those things as they are now (mode ensure). Personal data stays out: a user is named by
secrets, and secret settings are left to be set by hand.
--from-diff=<caption|address|dump> compares this installation with a saved state: a caption
(Sicherungspunkt) by id, another installation by its address (https://..., read once through its REST
route /api/migration/v1/snapshot with an API key of the scope migration:read, --token; never written
to), or a database dump (a SQLite file, or an SQL dump of this installation's engine, loaded into a
throwaway database). --compare names what (classes, roles, sections, states, languages, content,
settings; content with --subtree=<node>). What was added or changed becomes a step that makes it as it
is now (mode ensure), what was removed a delete step; run on the saved state, the migration makes it
like this installation. What no step type repeats is listed to be done by hand.

./console exp:migration:generate --skeleton add_news_class
./console exp:migration:generate --skeleton news_folder --type=yml --apply
./console exp:migration:generate --skeleton news_index --type=sql --engine=pgsql --apply
./console exp:migration:generate --skeleton news_settings --type=ini --apply
./console exp:migration:generate --skeleton add_news_class --description="The news class" --apply
./console exp:migration:generate --skeleton news_folder --source=extension:expnews --apply
./console exp:migration:generate --from-recording=20261101-120000-a1b2c3 news_section --apply
./console exp:migration:generate --from-class=news_article --from-role="News editor" news_setup
./console exp:migration:generate --from-content=path:news --depth=1 news_tree --apply
./console exp:migration:generate --from-diff=20261102-101500 news_changes
./console exp:migration:generate --from-diff=var/backup/before.sqlite --compare=classes,roles --apply
./console exp:migration:generate --from-diff=https://staging.example.com --token=staging_api_key --subtree=path:news
```

## Options

| Option | Value | Help |
|---|---|---|
| `--skeleton` |  | An empty migration with its header: PHP with plan(), up() and down(), or the kind --type names |
| `--source` | required | The source: site or extension:<name> (generate: where the file goes; rollback: which migration, when two sources have its id) |
| `--description` | required | The description in the header of the new migration |
| `--author` | required | The author in the header of the new migration |
| `--type` | required | The type of the new migration: php (the default), yml, sql, ini or graphql (a YAML migration with a graphql step to fill in) |
| `--apply` |  | Make the changes (the default is a dry run that lists them and changes nothing) |
| `--dry-run` |  | The default, accepted for scripts that pass it: list what would change, change nothing |
| `--format` | required | text (default) or json (the machine output exp-migration/1) |
| `--from-recording` | required | Generate a YAML migration from a recording of changes (its id) |
| `--engine` | required | With --type=sql: the file of one engine (mysql, pgsql, sqlite, oracle, mongodb) instead of the portable one |
| `--from-content` | required | Generate a YAML migration of content: the nodes (id, remote:<remote id>, path:<url path>), comma-separated |
| `--depth` | required | With --from-content: the levels below each node that go in too (default 0: the node alone) |
| `--max-objects` | required | With --from-content, or --from-diff with --subtree: at most this many objects (default 500) |
| `--from-class` | required | Generate a YAML migration of content classes (identifiers, comma-separated) |
| `--from-role` | required | Generate a YAML migration of roles with their policies (names, comma-separated) |
| `--from-section` | required | Generate a YAML migration of sections (identifiers, comma-separated) |
| `--from-settings` | required | Generate a YAML migration of settings files of settings/override (site.ini ..., comma-separated) |
| `--siteaccess` | required | With --from-settings: the files of this siteaccess instead of settings/override |
| `--from-diff` | required | Generate a YAML migration of what changed since a saved state: a caption id, the address of another installation (https://...) or a database dump |
| `--compare` | required | With --from-diff: what to compare (classes, roles, sections, states, languages, content, settings; comma-separated, default all but content) |
| `--subtree` | required | With --from-diff: compare the content of the subtree of this node (id, remote:<remote id>, path:<url path>) |
| `--token` | required | With --from-diff=<address>: the API key of the other installation with the scope migration:read, by secret name or env:VARIABLE |
| `--backup-dir` | required | Where the captions made without git_manager are (default var/backup/upgrade) |

Read from the option definitions in the code (`startup()`, `getOptions()`, `options()` or `ezcConsoleOption`). Every script also takes the standard options of the script framework (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

Arguments: `[name]`.

## Related

- [All console commands](README.md)

---

Generated by `./console exp:help:examples` from `bin/php/expmigrationgenerate.php`, `kernel/private/classes/commands/expmigrationgenerate.php`. Do not edit this page: change the source and generate again ([how](../README.md#how-the-generated-pages-are-made)). Free documentation: [GFDL 1.3 or later](../../help/free-documentation.md).
