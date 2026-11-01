---
title: "Migrations: recording changes"
audience: admin, developer
category: administration
order: 5
summary: Setup > Migrations > Recording in the administration. Record your own changes, or every change of the site in a site-wide window, as the steps of a migration; see what a recording captured and the migration it would make; stop and discard recordings; the Recording bar.
keywords: migrations, recording, record changes, session recording, site-wide recording, Recording bar, generate a migration, exp:migration:record, migration/recording, migration/edit, Verify it's you, personal data
context: migration/recording
type: help
---
# Migrations: recording changes

**Setup > Migrations > Recording** keeps what you change in the administration as the steps of a migration: content
you publish, classes you store, roles you assign, settings you write. When you are done, stop the recording, look at
what it captured, and generate a migration that makes the same changes on staging and production. How the recording
works, its commands and the steps it records are in the guide
[Migrations: generators and recording changes](../../guides/migrations-generators-and-recording.md).

**Who may use this page:** users with the policy `migration/edit`. In a new installation only the Administrator role
has it. Opening the site-wide window asks ["Verify it's you"](../../guides/verify-its-you.md) first.

## Record your own changes

The card **My changes** records only what you change, wherever you change it (here, in the website sidebar, or in a
command run as you), until you stop it.

1. Give the recording a label if you like (what it is for), then choose **Start recording my changes**.
2. A red **Recording…** bar now shows on every page of the administration. Make your changes as usual.
3. Choose **Stop recording** in the bar or on this page.

The card then says how many changes the recording has; **Show what it captured** opens it.

## The site-wide window

The card **Site-wide window** records every change of every user, and of the console, until it is closed. Use it when
several people prepare a release together. While it is open, every user of the administration sees the Recording bar,
because their changes are recorded too; holders of `migration/edit` can close it from the bar or this page.

Only one site-wide window and one recording of your own changes can run at a time. A change made while both run is in
both.

## What a recording captured

Choose a recording in the list (or **Show** in the bar). The page has three parts:

- **The migration it would make:** one step per thing changed, in the order they were first changed. A thing created
  and changed is one step that creates it with its last values; a thing created and removed again is left out. When
  the installation does not have the YAML step types the migration needs yet, a note names them, and the console
  command `./console exp:migration:generate --from-recording=<id>` lists the same.
- **What it captured:** every change in the order it was made: the time, the change (the audit event), the step it
  stands for and what it is about, by remote id or identifier, never by a number that differs between installations.
- **Discard:** removes the recording and what it captured. What was changed stays changed. Tick
  **Yes, discard this recording** first.

The page lists the first 1000 changes; `./console exp:migration:record:show <id>` lists every one.

## What is never recorded

- The account of a user (login, e-mail address, password) and the fields of user accounts: a recording keeps only
  that a user was created or changed, its groups and whether it is enabled. "Left out on purpose" names what was
  left out.
- The value of a setting whose name says it is a secret.
- Who made a change, in the migration: a generated migration names nobody.
- Reading, searching, signing in and clearing caches: they change nothing.
- The changes a migration run makes: they are a migration already.

## When something goes wrong

| What you see | Why, and what to do |
|---|---|
| "The recording tables do not exist yet" | The installation has not run the upgrade that makes them: `./console exp:upgrade --apply` (step `6.0.15-migration-recording-tables`). |
| "Recording is switched off in migration.ini" | `[RecordingSettings] Recording=disabled`; switch it on in `settings/override/migration.ini.append.php`. |
| "A recording of this kind is running already" | You are recording already, or a site-wide window is open. Stop it first. |
| The bar shows on a page but no recording is listed | The file of running recordings in `var/` is out of date: `./console exp:migration:record:status` writes it again. |
| A recording says it stopped adding at a number of events | `[RecordingSettings] MaxEvents` was reached. Stop it and start a new one. |

## Related pages

- [Migrations in the administration](README.md)
- [Migrations: generators and recording changes](../../guides/migrations-generators-and-recording.md)
- [Recipe: record an administrator session](../../examples/cookbook/migrations-record-an-admin-session.md)
- [The view migration/recording](../../examples/module-views/migration/recording.md) and [the policies of the migration module](../../examples/policies/migration.md)
