# YAML migration steps

This page is the guide to the step types of YAML migrations: what the kernel's step types do, how they find what they
change, how a step works in several languages, which steps undo themselves, and how an extension adds a step type of
its own. The engine around the steps (placeholders, conditions, loops, the id map) is described in
[the file format](../specifications/6.0/migration-file-format.md#the-yaml-migration); every key of every step type is
listed on its generated reference page ([all step types](../examples/migration-steps/README.md)).

[Guides](README.md) · [Migrations](migrations.md) · Related: [The tutorial](migrations-tutorial.md#12-a-yaml-migration),
[PHP migrations](migrations-php.md), [Rollback and captions](migrations-rollback-and-captions.md)

## The kernel's step types

| Step type | What it does | Modes |
|---|---|---|
| [`reference`](../examples/migration-steps/reference.md) | sets references for the steps after it | |
| [`assert`](../examples/migration-steps/assert.md) | fails the migration when a condition is false | |
| [`content`](../examples/migration-steps/content.md) | creates, updates, ensures or deletes a content object | create, update, ensure, delete |
| [`location`](../examples/migration-steps/location.md) | adds, changes, removes, moves, copies, hides or shows a node | create, update, delete, move, copy, hide, unhide |
| [`content_version`](../examples/migration-steps/content_version.md) | restores an earlier version or removes old versions | restore, cleanup |

Every step goes through the kernel's own API, the way the administration does it: a new object is made by its class
(`eZContentClass::instantiate()`), a new version by `createNewVersionIn()`, and both are published by the publish
operation; locations change through the operations `addlocation`, `removelocation`, `delete`, `move`, `hide`, `sort`,
`updatepriority` and `updatemainassignment`. So the URL aliases, the search index, the caches and the audit log follow
a step as they follow an edit in the administration.

## Naming what a step changes

Keys of the type `identifier` take an id, a remote id, a path or a name, and a placeholder in their place:

```yaml
parent: 2                          # the node id
parent: remote:news-root           # the node with this remote id
parent: object-remote:news-folder  # the main node of the object with this remote id
parent: path:media/images          # the node at this URL path
class: article                     # a class by its identifier (or its id, or remote:<class remote id>)
section: standard                  # a section by its identifier or id
parent: "${idmap.node.news}"       # the id map of the environment
parent: "${ref.news}"              # a value an earlier step kept with references:
```

Ids differ between installations; remote ids and paths do not. Give the objects a migration creates a `remote_id`,
and later migrations can find them on every installation.

## Content

```yaml
- type: content
  mode: ensure
  class: folder
  parent: object-remote:news-folder
  remote_id: news-archive
  language: eng-GB
  fields:
      name: Archive
      short_description: "<p>Older news.</p>"
  translations:
      ger-DE: { name: Archiv }
  references: { archive: node_id }
```

- **create** makes a new object and fails when the `remote_id` is taken. **ensure** finds the object by `remote_id`:
  it creates it when it is missing, and otherwise changes only the fields whose text differs, so the step can run
  again and again. **update** changes an object named by `object:` or `node:`; **delete** removes it with all its
  locations (`trash: true` puts it in the trash).
- `fields:` are written in the language of `language:` (default: the default language of content). Each value goes
  through the `fromString()` of the attribute's datatype, the format of the package import: text as it is, XML text
  as XML, a relation list as ids joined with `-`, a checkbox as `1` or `0`.
- `translations:` has one block per further language. Each block is published as a version of its own, as a
  translator in the administration would; a language the object does not have yet is added.
- A create or an update makes a **new version** and publishes it. `in_place: true` instead writes into the published
  version, without a new version and without the publish operation's workflows: a correction the version history does
  not show. The object's name, the caches and the search index are brought up to date.
- `missing: skip` lets an update or delete pass when the object is not there.

## Locations

```yaml
- type: location
  mode: create
  object: remote:news-archive
  parent: path:frontpage
  priority: 10
  references: { archive_on_front: node_id }
- type: location
  mode: move
  node: object-remote:news-archive
  parent: path:media/archive
```

`create` adds a location (and changes nothing when the object is below that parent already), `update` sets
`priority`, `sort_field`/`sort_order` and `main`, `delete` removes a location (with the last one the object goes too,
`trash: true` to the trash), `move` and `copy` take a node below another parent, `hide` and `unhide` hide or show a
node and its subtree. `copy` copies the object of the node (its published version, all languages).

## Versions

`content_version` with `mode: restore` publishes a copy of an earlier version as a new version (what "Copy and edit"
and publish does in the version history), `mode: cleanup` removes archived versions so that `keep:` of them are left,
and with `drafts: true` the drafts as well.

## Workflows and notifications

A step runs the operation without the workflows of its trigger, and publishes without a notification event: a
migration should not send mails or wait for an approval. `workflows: true` runs the workflows of `workflow.ini` as
the administration does, `notify: true` makes the notification event. A workflow that cancels or defers the operation
fails the step.

## Undoing a migration

A migration without `down:` can be undone by itself when each of its steps can:

| Step | Undoes itself |
|---|---|
| `content` create with a `remote_id` | the object is removed again |
| `location` create | the location is removed again |
| `location` hide / unhide | the node is shown / hidden again |
| `reference`, `assert` | nothing to undo |

Every other step (an update, an ensure, a delete, a move ...) cannot know what was there before: give the migration a
`down:` list, or roll the run back to the caption taken before it. `exp:migration:rollback` refuses a migration that
cannot be undone and says so.

## Transactions

The runner runs each migration in a transaction where the engine has them (PostgreSQL, SQLite, and MySQL/MariaDB for
migrations without schema changes); a step that fails rolls back the whole migration, the objects its earlier steps
made included. MongoDB has no transactions: a failed migration leaves what its earlier steps did, and the caption
taken before the run is the way back.

## A step type of your own

An extension adds a step type with a class and one line of settings:

```ini
# extension/news/settings/migration.ini.append.php
[StepSettings]
StepTypeList[news_import]=expNewsImportStep
```

The class extends `expMigrationStepTypeBase` and writes `name()`, `schema()` and `apply()`; `validate()`, `caches()`
and `plan()` have defaults. `schema()` lists the modes, the keys with their types, the values `references:` can take
and an example; the engine checks every step against it before anything runs. A type that can undo its steps
implements `expMigrationRevertibleStepType` (`revert()`), one that can undo only some of them
`expMigrationPartlyRevertibleStepType` (`revertible()` says which). `./console exp:help:examples --area=migration-steps`
makes its reference page.

## Read on

- [All step types](../examples/migration-steps/README.md), one generated page each
- [The file format: the YAML migration](../specifications/6.0/migration-file-format.md#the-yaml-migration)
- [Migrations: a first migration, step by step](migrations-tutorial.md)
