# Migrations: generators and recording changes

Writing a migration by hand is the exact way; recording it is the quick one. While a **recording** runs, every change
you make in the administration, in the website sidebar or with a command is kept as a step of a YAML migration:
content published, a class stored, a role assigned, a setting written. Afterwards you look at what it captured and
generate a migration from it, then run that migration on staging and production.

This page explains the recording and the generators of Exponential 6.0.15: the one that turns a recording into a
migration file, the one that writes a migration of what an installation has now (content, classes, roles, sections,
settings), the one that writes a migration of what changed since a saved state (a caption, another installation, a
database dump), the skeletons of every kind of migration file, the editor of the administration and the converter
from the reference bundle's YAML. The output below was produced on SQLite on 1 and 2 November 2026.

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
./console exp:migration:generate --from-recording=<id> news_section   # the migration of it, a dry run
./console exp:migration:generate --from-recording=<id> news_section --apply   # written to migrations/
./console exp:migration:generate --from-class=news_article --from-role="News editor" news_setup   # of what is there
./console exp:migration:generate --skeleton news_index --type=sql --apply      # an empty SQL migration
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
a release together on a staging installation. A change made while both run is kept by both. Either can be opened for
a window of time that closes by itself (`--for=<minutes>`, `--until=<time>`; [A recording window of time](migrations-packages-deploy-and-long-runs.md#a-recording-window-of-time)).

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
captured, the steps a migration of it would have, a **Generate migration** button that writes that migration to the
site's migrations directory, and a Discard button. While your changes are recorded, every page of
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

The table is what a recording keeps for each event: the recorded step, in the format of the recording
(`exp-migration-recording-event/1`). The generator turns each recorded step into the step of a YAML step type
([below](#from-the-recorded-step-to-the-migration-step)).

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

`./console exp:migration:generate --from-recording=<id> [name]` turns a recording into a YAML migration: the events
folded into steps (above), each in the form of the step type that repeats it, checked as `exp:migration:check` checks
a file, and written as `migrations/YYYYMMDDHHMMSS_<name>.yml` with `--apply` (without it, the command shows the file
and writes nothing; an existing file is never replaced). Without a name the recording's label names it. In the
administration the recording's page has the same as a **Generate migration** button.

```text
$ ./console exp:migration:generate --from-recording=20261101-124707-de8bac news_section
Exponential migrations: generate from a recording, dry run (--apply writes the file)
  20261101-124707-de8bac stopped   session     8 event(s)  2026-11-01 12:47  user #14  "news section"
    1  section/ensure section news
    2  content/create object remote:news-folder
    3  location/update location remote:89e19c337bfb26c11d3d7a34a71a6618
    4  role/assign role Editor
    5  user/create user remote:a8a1fb4f3c2c41d6b0a9c5b37c3e6a51
    6  location/hide location remote:89e19c337bfb26c11d3d7a34a71a6618
    7  location/swap location remote:89e19c337bfb26c11d3d7a34a71a6618
Left out on purpose: first_name, last_name, signature, image, login, email, password
Not generated, to be done by hand:
  - location/swap location remote:89e19c337bfb26c11d3d7a34a71a6618: a swap of two locations has no step: swap remote:89e19c337bfb26c11d3d7a34a71a6618 and remote:2c5e7b3f0d1e4a6b8c9d0e1f2a3b4c5d by hand
File migrations/20261101125500_news_section.yml
```

The file it writes:

```yaml
# description: Recording 20261101-124707-de8bac: news section
#
# Generated from the recording 20261101-124707-de8bac (8 events).
# Check it before the run: ./console exp:migration:check and ./console exp:migration:dry-run
#
# Not generated, to be done by hand:
# - location/swap location remote:89e19c337bfb26c11d3d7a34a71a6618: a swap of two locations has no step: swap remote:89e19c337bfb26c11d3d7a34a71a6618 and remote:2c5e7b3f0d1e4a6b8c9d0e1f2a3b4c5d by hand
steps:
    - type: section
      mode: ensure
      identifier: news
      name: News
      navigation_part: ezcontentnavigationpart
    - type: content
      mode: create
      class: folder
      parent: "remote:f3e90596361e31d496d4026eb624c983"
      remote_id: news-folder
      section: news
      always_available: true
      language: eng-GB
      fields: { name: News, short_description: "<p>What is new</p>" }
      translations: { ger-DE: { name: Nachrichten } }
      comment: "translations added: ger-DE"
    - type: location
      mode: update
      node: "remote:89e19c337bfb26c11d3d7a34a71a6618"
      sort_field: published
      sort_order: desc
    - type: role
      mode: assign
      role: Editor
      groups: [ "remote:3c160cca19fb135f83bd02d911f04db2" ]
      subtree: "remote:89e19c337bfb26c11d3d7a34a71a6618"
    - type: user
      mode: create
      secrets:
          login: user_a8a1fb4f3c2c_login
          email: user_a8a1fb4f3c2c_email
          password: user_a8a1fb4f3c2c_password
      remote_id: a8a1fb4f3c2c41d6b0a9c5b37c3e6a51
      groups: [ "remote:3c160cca19fb135f83bd02d911f04db2" ]
      enabled: true
      comment: "personal data left out: a generated migration names the account by secret references; set the secrets user_a8a1fb4f3c2c_login, user_a8a1fb4f3c2c_email, user_a8a1fb4f3c2c_password (Setup > Secrets, or env:<variable>) before the run; left out: first_name, last_name, signature, image, login, email, password"
    - type: location
      mode: hide
      node: "remote:89e19c337bfb26c11d3d7a34a71a6618"
```

Read it, make the changes listed as "to be done by hand" (or add the steps yourself), run
`./console exp:migration:check` and `./console exp:migration:dry-run`, and commit it like any migration. When an
extension that gives a step type is not active, the generator names the step types missing and writes nothing.

### From the recorded step to the migration step

| Recorded | Migration step |
|---|---|
| `content` / `create`, `update` | `content` / `create` or `update` (`object:` the remote id); the fields of the initial language as `fields`, the other languages as `translations`; further locations as `location` / `create` steps (with remote ids of their own) |
| `content` / `delete` | `content` / `delete` |
| `content` / `copy` | `location` / `copy` (the object of the node; the copy gets remote ids of its own) |
| `location` / `create`, `move`, `hide`, `delete` | `location` with the same mode (`node:` the location) |
| `location` / `reveal` | `location` / `unhide` |
| `location` / `update` | `location` / `update` with `sort_field`, `sort_order`, `main`; one `location` / `update` with `priority` per child for `child_priorities` |
| `trash` / `move`, `restore` | `trash` with the same mode |
| `class` / `create` | `content_class` / `create`, the attributes as a list (`type` is the datatype) |
| `class` / `update` | `content_class` / `ensure` with the whole definition (it adds and changes attributes, removes none) |
| `role` / `create`, `update` | `role` / `create` or `ensure` with the policies (ensure adds those the role lacks, removes none) |
| `role` / `assign`, `unassign` | `role` with `users` or `groups` and `subtree` or `section` |
| `user` / `create` | `user` / `create` with `secrets` for login, e-mail and password ([below](#personal-data-and-secrets)) |
| `user` / `update`, `delete` | `user` / `update` or `delete` (`user:` the remote id) |
| `section`, `object_state_group` | the same step type, mode `ensure` (or `assign`, `delete`) |
| `url_alias` / `ensure` | `url_alias` / `ensure` with `path` and `node`; a removal of every custom address of a location as `delete` with `which: all` |
| `ini` / `set`, `remove` | `settings` with `set` or `remove` (`siteaccess` for a siteaccess's file) |

Not generated, and named at the top of the file instead: a swap of two locations, workflow triggers (workflows have
no identifier yet), settings of an extension (migrations write only `settings/override` and
`settings/siteaccess/<name>`), secret settings (they never go into a migration), the removal of single URL aliases
the kernel does not name, and object states of content (no step type sets them; the step's comment names them).
What a step cannot carry is in its `comment`. A text that contains `${` is written as `$${`, so the engine does not
take it for a placeholder.

### Personal data and secrets

A recording never keeps the account of a user (login, e-mail address, password) nor the fields of user objects that
`[RecordingSettings] UserKeepAttributeList[]` does not name. A generated user step names the account by secrets
instead, which the step reads when it runs:

```yaml
    - type: user
      mode: create
      secrets: { login: user_a8a1fb4f3c2c_login, email: user_a8a1fb4f3c2c_email, password: user_a8a1fb4f3c2c_password }
      groups: [ "remote:3c160cca19fb135f83bd02d911f04db2" ]
```

Each name is a secret of **Setup > Secrets**, or `env:<VARIABLE>` for an environment variable of the run. Set them on
each installation before the run; a missing secret fails the step with its name, never a value. The key `secrets`
works in hand-written user steps too.

## Generating a migration of what is there

The same generator writes a migration of what an installation has now, to repeat it on another one:

| Option | Generates |
|---|---|
| `--from-content=<node>[,...]` | the objects of the nodes (`2`, `remote:<remote id>`, `path:<url path>`); `--depth=<n>` adds the levels below, `--max-objects=<n>` stops after that many objects (default 500), parents first |
| `--from-class=<identifier>[,...]` | content classes with their attributes |
| `--from-role=<name>[,...]` | roles with their policies and limitations in stable forms |
| `--from-section=<identifier>[,...]` | sections |
| `--from-settings=<file>[,...]` | the settings of `settings/override/<file>.append.php`; with `--siteaccess=<name>` of that siteaccess |

The options combine; the steps use mode `ensure` where the step type has it (content, classes, roles, sections), so
the migration also runs where some of it exists already. The same rules apply as for a recording: stable
identifiers, users by secrets, secret settings left out.

```text
$ ./console exp:migration:generate --from-class=news_article --from-role="News editor" --from-section=news news_setup --apply
```

## Generating a migration from a difference

`--from-diff` compares the installation now with a saved state and writes a migration of what changed. Run on the
saved state, the migration makes it like this installation: changes made in the administration of one installation
become a migration for the others, without a recording running at the time.

| `--from-diff=` | The saved state |
|---|---|
| a caption id (`20261102-101500`) | the caption (Sicherungspunkt): its database dump and its copy of `settings/override`. `./console exp:migration:caption --list` lists them; `--backup-dir` names the folder of captions made without git_manager |
| a file (`var/backup/before.sqlite`, `before.sql`) | a database dump of the same schema: a SQLite file (read in place, read-only), or an SQL dump of this installation's engine (SQLite, MySQL or MariaDB, PostgreSQL) loaded into a throwaway database `<database>_migdiff_<pid>` with the installation's account and dropped afterwards. A MongoDB dump is not read: restore it into a database of its own and compare with that installation by its address |
| an address (`https://staging.example.com`) | another installation, read once through its REST route `GET /api/migration/v1/snapshot` (policy `migration/read`; an API key with the scope `migration:read`, named by `--token=<secret name>` or `--token=env:VARIABLE`). Nothing is written there |

`--compare` names what is compared; the default is everything but content:

| Kind | Compared by | Steps |
|---|---|---|
| `classes` | identifier: the definition and every attribute | `content_class` ensure, or delete |
| `roles` | name: the policies with their limitations (not the assignments: they name users) | `role` ensure, or delete |
| `sections` | identifier: name and navigation part | `section` ensure, or delete |
| `states` | identifier of the object state group: its states and names | `object_state_group` ensure, or delete |
| `languages` | locale: the name | `language` ensure, or delete |
| `content` | with `--subtree=<node>`: the objects of that subtree by remote id (modified, version, section, parent); `--max-objects` (default 500) refuses a larger subtree instead of comparing part of it | `content` ensure (parents first); delete of the topmost removed object |
| `settings` | `settings/override`: every variable of every file (only a caption or another installation has them) | `settings` set, or remove |

What was added or changed is generated as `--from-content`, `--from-class` and the others generate it (mode
`ensure`, stable identifiers, users by secrets); what was removed becomes a delete step, after everything that is
made. Each side is read as a snapshot of fingerprints (a SHA-1 of what a step would set), so another installation
never hands out a value: the values in the migration are always this installation's. Secret settings are left out of
both snapshots: set them by hand. A moved object is listed in the header, to be done by hand with a location step.

```text
$ ./console exp:migration:generate --from-diff=var/backup/before.sqlite --compare=classes,roles,sections,languages,content --subtree=2 news_changes
Exponential migrations: generate from a difference, dry run (--apply writes the file)
Compared with the dump before.sqlite
  classes    0 added, 1 changed, 0 removed
  roles      0 added, 0 changed, 1 removed
  sections   1 added, 1 changed, 0 removed
  languages  1 added, 0 changed, 0 removed
  content    0 added, 1 changed, 0 removed
    1  language/ensure language nor-NO
    2  section/ensure section diff_news
    3  section/ensure section standard
    4  class/create class folder
    5  content/create object remote:9459d3c29e15006e45197295722c7ade
    6  role/delete role Editor
File migrations/20261102120000_news_changes.yml

    # description: What changed since the dump before.sqlite
    #
    # Generated from the difference to the dump before.sqlite (classes 0 added, 1 changed, 0 removed; roles 0 added,
    # 0 changed, 1 removed; sections 1 added, 1 changed, 0 removed; ...).
    # Run on that state, it makes it like this installation.
    # Check it before the run: ./console exp:migration:check and ./console exp:migration:dry-run
    steps:
        - type: language
          mode: ensure
          locale: nor-NO
          name: Norsk
        - type: section
          mode: ensure
          identifier: standard
          name: "Standard (changed)"
          navigation_part: ezcontentnavigationpart
        ...
        - type: role
          mode: delete
          role: Editor
```

(The plan lines name the recorded form, `create`; the steps use `ensure`.) Without differences nothing is written and
the command ends with 0 and "no differences: nothing to generate". With `--apply` the file is written; an existing
file is never replaced.

## Skeletons

`--skeleton <name>` writes an empty migration to fill in, of the kind `--type` names:

| `--type` | File | Content |
|---|---|---|
| `php` (default) | `<id>.php` | a class with `plan()`, `up()` and `down()` |
| `yml` | `<id>.yml` | the header and `steps: []`, an example in comments |
| `graphql` | `<id>.yml` | the same with an example of the step `graphql` |
| `ini` | `<id>.ini` | an INI migration with examples of blocks in comments |
| `sql` | `<id>.sql` | an SQL migration for every SQL engine, with `-- down` |
| `sql --engine=<engine>` | `<id>.mysql.sql`, `<id>.pgsql.sql`, `<id>.sqlite.sql`, `<id>.oracle.sql`, `<id>.mongodb.json` | the file of one engine |

The formats: [SQL and settings](migrations-sql-and-settings.md), [GraphQL](graphql-recipes.md).

## The editor in the administration

**Setup > Migrations > Editor** (`migration/editor`, policy `migration/edit`) writes and changes YAML migrations
without a shell. `migration/editor` starts a new one; `migration/editor/<migration id>` opens one of the searched
migration folders, as long as it has not run.

- **The text** is the migration as it is saved, header lines included. Write steps by hand, or:
- **Add a step**: choose a step type and *Show its form*. The form is made from the step type's own schema: every key
  with its type (a node or object identifier, a whole number, true/false, a list or map written as YAML), the values
  it takes (a choice list), its default and the modes it belongs to. Below it `if`, `references` (name=value pairs,
  with the values the step gives), `environments`, `engines` and `comment`. *Add step* puts it at the end of `steps`
  or `down`.
- **Steps** lists every step with *Edit* (loads it into the form; *Change step* puts it back in its place), move up,
  move down and *Remove*. Loops and steps of a type the installation lacks have no form: change them in the text.
- **Check** runs what `exp:migration:check` runs: the header, the document and every step through its step type.
  **Preview** shows the YAML as it will be saved.
- **Save** writes the file through the kernel: a new migration as `<now>_<name>.yml` into the site's migrations folder
  (the first `[MigrationSettings] DirectoryList` entry) or an active extension's `ExtensionDirectory`, never over a
  file; an existing one in place. A text that does not pass the check is not saved, and a migration that has run is
  never saved again (the checksum guard would refuse it): its change goes into a new migration.

When a step is added or changed in the form, the text is written again from the parsed migration: the header lines
stay, comments between the steps do not. The editor writes nothing outside the migration folders.

## Converting migrations of the reference bundle

`exp:migration:convert <file or folder>` reads YAML migrations of the migration bundle of the Symfony based platform
(the "reference bundle": a list of steps with `type`, `mode`, `match`, `attributes` and `references`) and writes a
YAML migration of Exponential for each file. Only the documented file format is read; no code of the bundle is used.

```text
$ ./console exp:migration:convert var/import/20240115093000_news.yml
Exponential migrations: convert, dry run (--apply writes the files)
var/import/20240115093000_news.yml -> migrations/20240115093000_news.yml: 1 step(s) converted, 1 left to do by hand
    by hand: step 2 (php): the step type php has no counterpart that converts; write it by hand: {"type":"php","class":"X"}
    | # description: Converted from 20240115093000_news.yml
    | #
    | # Converted by exp:migration:convert. Check it before the run: ./console exp:migration:check and ./console exp:migration:dry-run
    | #
    | # Not converted, to be done by hand:
    | # - step 2 (php): the step type php has no counterpart that converts; write it by hand: {"type":"php","class":"X"}
    | steps:
    |     - type: section
    |       mode: create
    |       identifier: news
    |       name: News
Converted 1 step(s), 1 to do by hand
PASS
```

| The bundle's step | Becomes |
|---|---|
| `content` create, update, delete | `content`: `content_type` → `class`, `parent_location` → `parent`, `attributes` → `fields`, `lang` → `language`, `match` (content id, remote id, location id or remote id) → `object` or `node` |
| `content_type` create, update, delete | `content_class`: names and descriptions per language, `content_type_group` → `groups`, attributes (`info-collector`, `disable-translation`, `field-settings`, `position`); an update's attributes go to `update_attributes` (move new ones to `add_attributes`) |
| `content_type_group` | `content_class_group` |
| `location` create, update, delete | `location` create (one per parent), update (priority, sorting), `move` (a new parent), `hide`/`unhide`, delete |
| `role` create, update, delete, `assign` | `role` with `policies` (limitations as identifier => values), `assign` steps with `users`/`groups`, one Subtree or Section limitation |
| `section`, `user`, `user_group`, `language`, `tag` | the step types of the same name (`username` → `login`, first and last name → `fields`) |
| `sql` | `sql` with one key per engine |
| `reference` set, `trash` purge | `reference` `set`, `trash` purge |

`reference:<name>` and `[reference:<name>]` become `${ref.<name>}`; the references a step sets keep their names, with
the value names of the kernel's step (`location_id` → `node_id`, `content_id` → `object_id` ...). What does not convert
is never guessed: PHP and service calls, HTTP, mail, processes, loops, files, migration definitions, other modes and
keys without a counterpart go into the list at the top of the new file, to be done by hand. Every converted step is
checked by its step type; one that does not pass is in that list too, with the reason.

The new file keeps the date and time at the start of the old name, so the order stays; a name without one gets the
time of now. `--source=extension:<name>` writes into an extension's migrations folder, `--author` into the header.
Without `--apply` nothing is written, and an existing file is never replaced.

## Related pages

- [Recording changes in the administration](../help/migrations/recording.md)
- [Generating migrations](../help/migrations/generating-migrations.md) and [the migration editor](../help/migrations/the-migration-editor.md), step by step with screenshots
- [Recipe: record an administrator session](../examples/cookbook/migrations-record-an-admin-session.md)
- [The migration file format: recorded events](../specifications/6.0/migration-file-format.md#recorded-events-exp-migration-recording-event1)
- [Migrations](migrations.md) · [The audit trail](../features/6.0/audit-trail.md) · ["Verify it's you"](verify-its-you.md)
- [`exp:migration:convert`](../examples/commands/expmigrationconvert.md) · [The view migration/editor](../examples/module-views/migration/editor.md)
