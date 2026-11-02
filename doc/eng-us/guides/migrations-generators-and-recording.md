# Migrations: generators and recording changes

Writing a migration by hand is the exact way; recording it is the quick one. While a **recording** runs, every change
you make in the administration, in the website sidebar or with a command is kept as a step of a YAML migration:
content published, a class stored, a role assigned, a setting written. Afterwards you look at what it captured and
generate a migration from it, then run that migration on staging and production.

This page explains the recording, which is in Exponential 6.0.15, and the generator that turns a recording into a
migration file, which comes with the YAML step types (see [Generating a migration](#generating-a-migration-from-a-recording)).
The other generators (from content, from a diff, the converter) follow later. The output below was produced on
SQLite on 1 November 2026.

[Guides](README.md) · [Migrations](migrations.md) · Related: [PHP migrations](migrations-php.md),
[Rollback and captions](migrations-rollback-and-captions.md), [The file format](../specifications/6.0/migration-file-format.md#recorded-events-exp-migration-recording-event1),
[Recording in the administration](../help/migrations/recording.md)

## In short

```sh
./console exp:migration:record:start --label="news section" --apply   # record my changes from now on
# ... make the changes in the administration ...
./console exp:migration:record:stop --apply                           # stop
./console exp:migration:record:show <id>                              # what it captured
./console exp:migration:record:show <id> --plan                       # the steps a migration of it would have
./console exp:migration:generate --from-recording=<id> news_section   # the migration (needs the YAML step types)
```

The same in the administration: **Setup > Migrations > Recording**, with a "Recording…" bar on every page while your
changes are recorded.

## What is recorded

A recording sees the kernel's **operations**, not web requests. It taps the same hook points the
[audit trail](../features/6.0/audit-trail.md) records from, whether the audit itself records those events or not:
when a node is moved, the kernel reports "content.node.move" once, wherever the move was made (the administration,
the website sidebar, a REST call, a command), and the recording keeps that one event. A page that only reads, a
search, a sign-in or a cache clear is not a change and is never recorded.

| Area | Recorded |
|---|---|
| Content | published (a new object, a new version, a translation added or removed), always available, initial language, object states, removed, purged, restored from the trash |
| Locations | added, moved, copied, hidden, revealed, removed, sent to the trash, sort order, priorities of the children, main location, swapped, section of a subtree |
| Classes | created, changed, copied, removed (as Setup > Classes stores them) |
| Sections, object states | created or changed, removed |
| Roles | created, changed, copied, removed, a policy added or removed, assigned and unassigned |
| Users and groups | created, changed, moved, enabled, locked and unlocked, removed (without personal data, see below); a user group is content |
| URL aliases | an alias or a wildcard added or removed |
| Settings | every variable written through the settings editor (expIniEditor), with its file and scope |
| Workflows | the triggers changed |

Each recorded event is stored in the form of the migration step it stands for, with every id in a **stable form**:
nodes and objects by their remote ids (`remote:<remote id>`, as [YAML migrations](../specifications/6.0/migration-file-format.md#identifiers-and-the-id-map)
name them), classes, sections and object states by identifier, roles by name, languages by locale. Numeric ids differ
between installations; these names do not. The values of a step are the state **after** the change (a content
object's fields in every language, a class's whole definition, a role's policies with their limitations), so the last
event of a thing is the whole of it.

A migration run is not recorded: its changes are a migration already.

## Two kinds of recording

| Kind | Records | Started by | At a time |
|---|---|---|---|
| **session** (default) | the changes of one user, wherever that user makes them: the administration, the website sidebar, a command run as that user | the user in Setup > Migrations or the website sidebar; `exp:migration:record:start --user=<login>` | one per user |
| **site-wide** | every change of every user and of the console | a holder of `migration/edit` in Setup > Migrations (asks ["Verify it's you"](verify-its-you.md)); `exp:migration:record:start --site-wide` | one |

Use a session recording to capture what you configure yourself. Use the site-wide window when several people prepare
a release together on a staging installation. A change made while both run is kept by both.

While the site-wide window is open, every user of the administration sees the "Recording…" bar, because their
changes are recorded too; only holders of `migration/edit` can close it.

## Personal data

The account of a user (login, e-mail address, password hash) and the fields of user objects are **never recorded**:
the event of a user keeps its remote id, its class, its groups and whether it is enabled, and names what was left out
in `omitted`. A generated migration will name the account by [secret references](../features/6.0/secret-store.md),
so no address and no password ever reaches a migrations repository that is pushed.

A site may allow single attributes of user objects that are no personal data (a team account's "signature", say) with
`[RecordingSettings] UserKeepAttributeList[]`. Who made a change is kept only as a user id, in the recording table of
the installation; it is never part of an event's step, and a generated migration names nobody. A setting whose name
says it is a secret (password, token, key ...) is recorded without its value (`"secret": true`).

## The commands

All of them print text, or with `--format=json` the machine output `exp-migration/1`. Start, stop and discard are
dry runs unless `--apply`. A session recording belongs to `--user` (by login; default `[RunSettings] RunAsUser` of
`migration.ini`, `admin`).

### Start

```text
$ ./console exp:migration:record:start --label="news section"
Exponential migrations: start a recording, dry run (--apply starts it)
Would record the changes of admin, "news section"
PASS

$ ./console exp:migration:record:start --label="news section" --apply
Exponential migrations: start a recording
Recording 20261101-124707-de8bac started: the changes of admin, "news section"
Next: make the changes, then ./console exp:migration:record:stop --user=admin --apply
PASS
```

A second session recording of the same user, or a second site-wide window, is refused (exit 1). Without the tables
(upgrade step `6.0.15-migration-recording-tables`, made by `./console exp:upgrade --apply`) nothing can be started.

### Status

```text
$ ./console exp:migration:record:status
Exponential migrations: recordings of changes that run
  20261101-124707-de8bac recording session     5 event(s)  2026-11-01 12:47  user #14  "news section"
The changes of admin are recorded now
PASS
```

Which recordings run is also kept in a small file, `var/<site>/migration/recording/active.json`, so that a request
knows without a database query whether anything is recorded. The status command writes it again from the database
when the two differ (after `var/` was cleared, or on a new server), and says so with a warning.

### Stop, list

```text
$ ./console exp:migration:record:stop --apply
Exponential migrations: stop a recording
  20261101-124707-de8bac stopped   session     5 event(s)  2026-11-01 12:47  user #14  "news section"
Next: ./console exp:migration:record:show 20261101-124707-de8bac, then ./console exp:migration:generate --from-recording=20261101-124707-de8bac
PASS

$ ./console exp:migration:record:list
Exponential migrations: recordings of changes, the newest first
  20261101-124707-de8bac stopped   session     5 event(s)  2026-11-01 12:47  user #14  "news section"
PASS
```

`stop` takes the recording's id, or stops `--user`'s session recording, or with `--site-wide` the window. `list
--count=<n>` lists fewer.

### Show

```text
$ ./console exp:migration:record:show 20261101-124707-de8bac
Exponential migrations: a recording of changes
  20261101-124707-de8bac stopped   session     5 event(s)  2026-11-01 12:47  user #14  "news section"

  12:47:07  content.section.change           section/ensure section news
  12:47:07  content.object.create            content/create object remote:news-folder
  12:47:07  content.node.sort                location/update location remote:89e19c337bfb26c11d3d7a34a71a6618
  12:47:07  access.role.assign               role/assign role Editor
  12:47:07  content.node.hide                location/hide location remote:89e19c337bfb26c11d3d7a34a71a6618

Summary: 5 event(s)
PASS
```

Each line is the time, the audit event and the step it stands for with what it is about. `--format=json` gives the
events in full; the content event of this session:

```json
{
    "v": 1,
    "audit": "content.object.create",
    "subject": "object remote:news-folder",
    "step": {
        "type": "content",
        "mode": "create",
        "class": "folder",
        "parent": "remote:f3e90596361e31d496d4026eb624c983",
        "remote_id": "news-folder",
        "section": "standard",
        "always_available": true,
        "initial_language": "eng-US",
        "states": {
            "ez_lock": "not_locked"
        },
        "fields": {
            "eng-US": {
                "description": "",
                "name": "News",
                "short_description": "",
                "short_name": "",
                "show_children": "1"
            }
        }
    },
    "omitted": [],
    "notes": []
}
```

`--plan` lists the steps a migration of the recording would have instead (see [Folding](#folding-the-events-into-steps)):

```text
$ ./console exp:migration:record:show 20261101-124707-de8bac --plan
Exponential migrations: the steps of a recording
  20261101-124707-de8bac stopped   session     5 event(s)  2026-11-01 12:47  user #14  "news section"

    1  section/ensure section news  (from content.section.change)
    2  content/create object remote:news-folder  (from content.object.create)
    3  location/update location remote:89e19c337bfb26c11d3d7a34a71a6618  (from content.node.sort)
    4  role/assign role Editor  (from access.role.assign)
    5  location/hide location remote:89e19c337bfb26c11d3d7a34a71a6618  (from content.node.hide)

Summary: 5 event(s), 5 step(s)
PASS
```

A step that is kept apart from the others of the same thing (an assignment, a hide) stays a step of its own, in the
order it was made.

### Discard

```text
$ ./console exp:migration:record:discard 20261101-124707-de8bac --apply
Exponential migrations: discard a recording
  20261101-124707-de8bac stopped   session     5 event(s)  2026-11-01 12:47  user #14  "news section"
Discarded: the recording and its events are removed
PASS
```

Discarding removes the record of the changes, never the changes. A running recording that is discarded stops.

Every command's options: [exp:migration:record:start](../examples/commands/expmigrationrecordstart.md),
[stop](../examples/commands/expmigrationrecordstop.md), [status](../examples/commands/expmigrationrecordstatus.md),
[list](../examples/commands/expmigrationrecordlist.md), [show](../examples/commands/expmigrationrecordshow.md),
[discard](../examples/commands/expmigrationrecorddiscard.md).

## In the administration

**Setup > Migrations > Recording** (the view `migration/recording`, policy `migration/edit`) starts and stops the
recording of your own changes, opens and closes the site-wide window, lists every recording and shows what each
captured, the steps a migration of it would have, and a Discard button. While your changes are recorded, every page of
the administration shows a red **Recording…** bar with **Show** and **Stop recording**. The website sidebar has Start
and Stop for your own recording in its Migrations panel. The page in detail:
[Recording changes in the administration](../help/migrations/recording.md).

| Policy | Allows |
|---|---|
| `migration/edit` | start and stop your own recording, open and close the site-wide window, see and discard recordings |

Opening the site-wide window asks "Verify it's you" (`site.ini [ReauthGroup_system] Rules[]=migration/recording
StartSiteButton`); your own recording does not. The console is never asked. Starting, stopping and discarding are
recorded in the audit log as `system.migration.record` (the verb says which).

## Settings

`settings/migration.ini [RecordingSettings]` ([every setting](../examples/ini-settings/migration/RecordingSettings.md)):

| Setting | Default | Meaning |
|---|---|---|
| `Recording` | `enabled` | `disabled`: nothing is recorded and no recording can be started |
| `EventList[]` | empty | the audit events a recording keeps; empty: every event of the table below |
| `UserKeepAttributeList[]` | empty | attributes of user objects that are no personal data and may be recorded |
| `MaxEvents` | `10000` | the most events one recording keeps; it then keeps running but adds nothing more and says so |

## The events and the steps they become

The table is the contract between the recording and the YAML step types of the work package 10 (the steps v1). Step
types that do not exist in this release are named as that work package will name them.

| Audit event | Step type / mode | The keys of the step |
|---|---|---|
| `content.object.create` | `content` / `create` | `class`, `parent`, `remote_id`, `section`, `always_available`, `initial_language`, `states` (group => state), `fields` (locale => attribute => value), `locations` (the other locations: `parent`, `remote_id`) |
| `content.object.publish` | `content` / `update` | `match` and the keys above but `class`, `parent`, `remote_id`; `changed` (the attributes the version changed) |
| `content.object.translate` | `content` / `update` | as publish; a note names the languages added |
| `content.object.translation.remove` | `content` / `update` | as publish, and `remove_translations` (locales) |
| `content.object.always_available`, `content.object.initial_language`, `content.object.state` | `content` / `update` | as publish |
| `content.object.remove`, `content.object.purge` | `content` / `delete` | `match` |
| `content.object.restore` | `trash` / `restore` | `object`, `parent` |
| `content.node.add` | `location` / `create` | `object`, `parent`, `remote_id` |
| `content.node.move` | `location` / `move` | `match`, `parent` |
| `content.node.copy` | `content` / `copy` | `node`, `parent`, `remote_id` (of the new object), `location_remote_id`, `subtree` (a subtree copied) |
| `content.node.hide` | `location` / `hide` | `match` |
| `content.node.reveal` | `location` / `reveal` | `match` |
| `content.node.sort` | `location` / `update` | `match`, `sort_field` (name), `sort_order` (`asc`, `desc`) |
| `content.node.priority` | `location` / `update` | `match`, `child_priorities` (child location => priority) |
| `content.node.main` | `location` / `update` | `match`, `main: true` |
| `content.node.swap` | `location` / `swap` | `match`, `with` |
| `content.node.remove` | `location` / `delete` | `match` |
| `content.node.remove.trash` | `trash` / `move` | `match` |
| `content.node.section` | `section` / `assign` | `match` (the section), `subtree` (the location) |
| `content.class.create`, `content.class.copy` | `class` / `create` | `identifier`, `names`, `descriptions`, `contentobject_name`, `url_alias_name`, `is_container`, `always_available`, `sort_field`, `sort_order`, `groups` (names), `remote_id`, `attributes` (identifier => `datatype`, `names`, `descriptions`, `placement`, `required`, `searchable`, `translatable`, `information_collector`, `category`, `settings`: the datatype's `data_int1` ... `data_text5` that are set) |
| `content.class.change` | `class` / `update` | `match` and the keys of create but `identifier` |
| `content.class.remove` | `class` / `delete` | `match` |
| `content.section.change` | `section` / `ensure` | `match`, `identifier`, `name`, `navigation_part` |
| `content.section.remove` | `section` / `delete` | `match` |
| `content.state.change` | `object_state_group` / `ensure` | `match`, `identifier`, `names`, `states` (identifier => names), `default_language` |
| `content.state.remove` | `object_state_group` / `delete` | `match` |
| `content.urlalias.change` (added) | `url_alias` / `ensure` | `path`, `node` |
| `content.urlalias.change` (removed) | `url_alias` / `delete` | `match` (a location, or `global`), `which` (`all`, `selected`) |
| `access.role.create`, `access.role.copy` | `role` / `create` | `name`, `policies` (`module`, `function`, `limitations`: identifier => values) |
| `access.role.change`, `access.policy.add`, `access.policy.remove` | `role` / `update` | `match`, `policies` (all of them, as after the change) |
| `access.role.remove` | `role` / `delete` | `match` |
| `access.role.assign` | `role` / `assign` | `match` (the role), `user` or `group`, `limitation` (`subtree` or `section`, or another identifier with its value) |
| `access.role.unassign` | `role` / `unassign` | as assign |
| `access.user.create` (and a user published as content) | `user` / `create` | `remote_id`, `class`, `groups`, `enabled`, `fields` (only `UserKeepAttributeList[]`); `omitted`: login, email, password and the other attributes |
| `access.user.change`, `access.user.move`, `access.user.activate`, `access.user.lock`, `access.user.unlock` | `user` / `update` | `match` and the keys of create but `remote_id` |
| `access.user.remove` | `user` / `delete` | `match` |
| `system.setting.write` | `ini` / `set` (`remove` when the variable was taken out) | `file`, `scope` (`override`, `siteaccess:<name>`, `extension:<name>`), `block`, `variable`, `value`, or `secret: true` without a value |
| `system.workflow.trigger.change` | `workflow_trigger` / `ensure` | `triggers` (`module`, `function`, `connect`, `workflow`: the workflow's id, noted) |

Values in stable forms: limitation values of roles become class identifiers (Class, ParentClass), section
identifiers (Section, UserSection), location remote ids (Node, Subtree, UserSubtree) and `<group>/<state>` (state
limitations); others are kept as the kernel stores them, with a note. Attribute values are their text form (what
`fromString()` reads back), relations as lists of `remote:` ids; files (images, binary files, media) keep their path
with a note that a file step copies them.

## Folding the events into steps

A recording keeps every event; a migration needs one step per thing. The generator (and `show --plan`) folds them:

1. Events about the same thing (the same subject) become one step, at the place of the first.
2. A create followed by updates is one create with the last values.
3. A thing created and removed in the same recording leaves no step.
4. Anything followed by a removal is the removal.
5. Otherwise the last event wins (it carries the whole state).
6. Events that are not a thing's state are kept one by one, in order: assignments, moves, copies, swaps, hides and
   reveals, URL aliases.

## Generating a migration from a recording

`./console exp:migration:generate --from-recording=<id> [name]` turns a recording into a YAML migration. It needs the
YAML step types the recording uses (content, location, class, role, user, section ...), which come with the YAML
steps v1. Until the installation has them, it lists the steps and what is missing, writes nothing and ends with exit 1:

```text
$ ./console exp:migration:generate --from-recording=20261101-124707-de8bac news_section
Exponential migrations: generate from a recording
  20261101-124707-de8bac stopped   session     5 event(s)  2026-11-01 12:47  user #14  "news section"
    1  section/ensure section news
    2  content/create object remote:news-folder
    3  location/update location remote:89e19c337bfb26c11d3d7a34a71a6618
    4  role/assign role Editor
    5  location/hide location remote:89e19c337bfb26c11d3d7a34a71a6618
Step types missing: content, location, role, section
FAIL: needs the YAML step types content, location, role, section (the YAML steps v1 of work package 10): nothing is written until this installation has them
FAIL (exit 1)
```

Generating from content, from a diff and the converter from the reference bundle's YAML follow in a later release.

## Related pages

- [Recording changes in the administration](../help/migrations/recording.md)
- [Recipe: record an administrator session](../examples/cookbook/migrations-record-an-admin-session.md)
- [The migration file format: recorded events](../specifications/6.0/migration-file-format.md#recorded-events-exp-migration-recording-event1)
- [Migrations](migrations.md) · [The audit trail](../features/6.0/audit-trail.md) · ["Verify it's you"](verify-its-you.md)
