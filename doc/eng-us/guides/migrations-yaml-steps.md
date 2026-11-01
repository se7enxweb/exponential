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
| [`content_class`](../examples/migration-steps/content_class.md) | creates, changes, ensures or deletes a class with its attributes | create, update, ensure, delete |
| [`content_class_group`](../examples/migration-steps/content_class_group.md) | creates, renames, ensures or deletes a class group | create, update, ensure, delete |
| [`user`](../examples/migration-steps/user.md) | creates, changes, ensures or deletes a user account | create, update, ensure, delete |
| [`user_group`](../examples/migration-steps/user_group.md) | creates, changes, ensures or deletes a user group | create, update, ensure, delete |
| [`role`](../examples/migration-steps/role.md) | a role with its policies and limitations, and its assignments | create, update, ensure, delete, assign, unassign |
| [`section`](../examples/migration-steps/section.md) | a section, and its assignment to a part of the tree | create, update, ensure, delete, assign |
| [`language`](../examples/migration-steps/language.md) | adds, renames, ensures or removes a language of content | create, update, ensure, delete |
| [`object_state_group`](../examples/migration-steps/object_state_group.md) | an object state group with its states | create, update, ensure, delete |

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

## Classes and class groups

```yaml
- type: content_class_group
  mode: ensure
  name: News
- type: content_class
  mode: ensure
  identifier: news_article
  language: eng-GB
  name: News article
  names: { ger-DE: Nachrichtenartikel }
  name_pattern: "<title>"
  groups: [ News ]
  attributes:
    - { identifier: title, type: ezstring, name: Title, names: { ger-DE: Titel }, required: true, settings: { data_int1: 255 } }
    - { identifier: body, type: ezxmltext, name: Body }
```

A class is created with its names in each language (`name:` in the language of `language:`, `names:` for the others),
its groups (by name) and its attributes. An attribute names its datatype (`type:`), its names and descriptions, the
flags `required`, `searchable`, `translatable` and `information_collector`, a `category`, a `placement`, and its
datatype settings as the columns the datatype's class edit stores them in (`settings: { data_int1: 255 }`:
`data_int1` to `data_int4`, `data_float1` to `data_float4`, `data_text1` to `data_text5`; the reference of each
datatype says which column holds what). `ensure` creates a missing class and otherwise adds the missing attributes and
changes what differs; `update` changes a class with `add_attributes:`, `update_attributes:` (by identifier) and
`remove_attributes:`. An attribute is added as the setup wizard adds one (`eZSiteInstaller::addClassAttributes()`):
every object of the class gets it in each of its versions and languages. A removed attribute takes its data in every
object with it. A datatype cannot be changed; remove the attribute and add a new one. A class with objects is deleted
only with `with_objects: true`, and its objects with it.

## Users, groups and roles

```yaml
- type: user_group
  mode: ensure
  parent: 5
  remote_id: news-editors
  name: News editors
  references: { editors: node_id }
- type: user
  mode: ensure
  login: editor
  email: editor@example.com
  password: "${ref.editor_password}"
  groups: [ "${ref.editors}" ]
  fields: { first_name: News, last_name: Editor }
- type: role
  mode: ensure
  name: News editor
  policies:
    - { module: content, function: read }
    - { module: content, function: edit, limitations: { Class: [ news_article ], Subtree: [ "path:news" ] } }
- type: role
  mode: assign
  role: News editor
  groups: [ "remote:news-editors" ]
  subtree: "path:news"
```

A user group is a content object of the class `user_group`, so `user_group` creates, translates and deletes it as
`content` does. A user is an object of the class `user` in one or more groups (`groups:` names their nodes, the first
is the main location); its account is set from `login:` and `email:`, and its password is hashed as the kernel hashes
it. Never write a password into a migration file: take it from a reference or the environment, or give an existing
hash with `password_hash:` and `password_hash_type:`. No plan, log or record shows a password. `ensure` leaves the
password of an existing account alone.

Policies are written as in the administration, and their limitation values may name what they limit to as the rest
of the migration does: `Class` and `ParentClass` take class identifiers, `Section` and `UserSection` section
identifiers, `Node` node identifiers, `Subtree` and `UserSubtree` node identifiers (stored as the path of the node),
`SiteAccess` siteaccess names, `StateGroup_<group>` state identifiers. `mode: assign` gives the role to users
(`users:`, by login) and groups (`groups:`, object identifiers), limited to a subtree or a section or not at all;
`mode: unassign` takes it back.

## Sections, languages and object states

`section` creates a section (`identifier`, `name`, `navigation_part`) and assigns it with `mode: assign` to a node's
object and, by `scope:`, the objects below it (`subtree`), only those that had the same section (`inherited`), or the
object alone (`object`), as section/assign of the administration does. `language` adds a language of content (the
locale must be one the installation knows); which languages a siteaccess shows stays a setting. A section or a
language in use is not deleted. `object_state_group` creates a group with its states and their names in each
language; the first state is the state of every object, and a removed state's objects get the first state left. The
groups of the kernel (`ez_lock` and the other identifiers starting with `ez`) are not changed.

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
| `content_class`, `content_class_group`, `section`, `language`, `object_state_group`, `role`, `user` create | removed again (a class only while it has no objects, a section or a language only while unused) |
| `user_group` create with a `remote_id` | the group is removed again |
| `role` assign / unassign | the assignment is taken back / made again |
| `reference`, `assert` | nothing to undo |

Every other step (an update, an ensure, a delete, a move, a section assign ...) cannot know what was there before: give the migration a
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
