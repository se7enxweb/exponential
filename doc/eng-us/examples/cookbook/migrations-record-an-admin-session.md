---
title: Record an administrator session as a migration
audience: developer, admin
category: examples
order: 140
summary: Record what you configure in the administration - a section, a folder, a role assignment - as the steps of a migration, look at what was captured, and see the migration it would make, on the console and in Setup > Migrations > Recording.
keywords: migrations, recording, record changes, exp:migration:record:start, exp:migration:record:stop, exp:migration:record:show, exp:migration:generate --from-recording, Setup > Migrations > Recording, Recording bar, remote id
type: example
---
# Record an administrator session as a migration

You configure a news section on a staging installation: a section, a folder for it, an editors' role on the folder,
and the folder kept hidden until launch. Instead of writing that as a migration by hand, record it while you click,
then turn the recording into a migration for production.

What you need: Exponential 6.0.15 with the upgrade step `6.0.15-migration-recording-tables` applied
(`./console exp:upgrade --apply`), and the policy `migration/edit` (the Administrator role has it). The output below
was produced on SQLite on 1 November 2026. The guide: [Generators and recording changes](../../guides/migrations-generators-and-recording.md).

## 1. Start recording

In the administration: **Setup > Migrations > Recording**, label "news section", **Start recording my changes**. A red
**Recording…** bar now shows on every page. On the console, the same:

```text
$ ./console exp:migration:record:start --label="news section" --apply
Exponential migrations: start a recording
Recording 20261101-124707-de8bac started: the changes of admin, "news section"
Next: make the changes, then ./console exp:migration:record:stop --user=admin --apply
PASS
```

## 2. Make the changes

As usual, in the administration:

1. **Setup > Sections**: a new section "News" with the identifier `news`.
2. **Content structure**: a folder "News" below the root (give it the remote id `news-folder` on its Details tab
   if you want a name of your choice in the migration).
3. On the folder, **Sorting**: by published, newest first.
4. **Roles and policies > Editor > Assign with limitation**: the editors' group, limited to the subtree of the folder.
5. On the folder: **Hide**.

## 3. Stop and look

Choose **Stop recording** in the bar. Then **Show** on the recording, or on the console:

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

Every change is named by what stays the same on production: the folder by its remote id, its location by the
location's remote id, the section by its identifier, the role by its name. `--format=json` shows the values of each
step, such as the folder's fields in every language.

## 4. The migration it would make

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

Had you renamed the folder after publishing it, step 2 would still be one creation, with the last name.

## 5. Generate the migration

```text
$ ./console exp:migration:generate --from-recording=20261101-124707-de8bac news_section --apply
Exponential migrations: generate from a recording
  20261101-124707-de8bac stopped   session     5 event(s)  2026-11-01 12:47  user #14  "news section"
    1  section/ensure section news
    2  content/create object remote:news-folder
    3  location/update location remote:89e19c337bfb26c11d3d7a34a71a6618
    4  role/assign role Editor
    5  location/hide location remote:89e19c337bfb26c11d3d7a34a71a6618
Wrote migrations/20261101125500_news_section.yml
Next: ./console exp:migration:check and ./console exp:migration:dry-run
PASS
```

The file has one step per line above, in the form of the step types `section`, `content`, `location` and `role`
([the file it writes](../../guides/migrations-generators-and-recording.md#generating-a-migration-from-a-recording)).
Check it, commit it and run it on production like any migration ([Migrations](../../guides/migrations.md)). In the
administration, **Generate migration** on the recording's page does the same.

## 6. Clean up

A recording you no longer need:

```text
$ ./console exp:migration:record:discard 20261101-124707-de8bac --apply
Exponential migrations: discard a recording
  20261101-124707-de8bac stopped   session     5 event(s)  2026-11-01 12:47  user #14  "news section"
Discarded: the recording and its events are removed
PASS
```

## What goes wrong most often

- **Changes of a colleague are missing.** A session recording keeps only your own changes. Open the site-wide window
  (Setup > Migrations > Recording) when several people work together.
- **Users are in the recording without their names.** On purpose: accounts and personal fields are never recorded;
  the migration will name accounts by secret references.
- **The bar stays after the recording was stopped elsewhere.** Reload the page; if it stays,
  `./console exp:migration:record:status` writes the file of running recordings again.

## Source files

`kernel/classes/migration/expmigrationrecording.php`, `expmigrationrecorder.php`, `expmigrationrecordingevent.php`,
`expmigrationrecordinggenerator.php`, `kernel/private/classes/commands/expmigrationrecord*.php`,
`kernel/private/classes/views/migration/recording.php`, `settings/migration.ini [RecordingSettings]`.
