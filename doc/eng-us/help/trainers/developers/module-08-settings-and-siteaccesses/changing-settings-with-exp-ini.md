---
title: "Lesson 8.3: Changing settings with exp:ini"
audience: trainer, developer
category: training
order: 83
summary: Learners use the actions of exp:ini (get, set, add, rem, clear, toggle, copy, move), preview every change with --dry-run, find the backup, read exit codes, and move a block of settings from the global override into their extension. 50 minutes.
keywords: developers course, exp:ini, ini.php, get, set, add, rem, clear, toggle, copy, move, move-all, dry-run, backup, var/backup/ini, exit codes, json, show-secrets, create-extension, audit
type: help
---
# Lesson 8.3: Changing settings with exp:ini

`exp:ini` reads and changes settings in every scope, line by line, keeping comments and order, with a backup and an
audit record for every write. It is the safe replacement for editing settings files by hand or with `sed`. In this
lesson learners use its actions, preview changes, and move settings that collected in the global override into their
extension, where they travel with the code.

## At a glance

| | |
|---|---|
| Course | [Developers](../README.md) |
| Module | [Module 8: Settings and siteaccesses](README.md) |
| Duration | 50 minutes (15 presenting, 35 exercise) |
| Level | Intermediate |
| Audience | Developers and administrators |
| Prerequisites | [Lesson 8.2](siteaccess-settings.md) |

## Learning objectives

After this lesson, learners can:

- use `get`, `set`, `add`, `rem`, `clear`, `toggle` and `copy` with the right scope;
- preview any change with `--dry-run` and find the backup of a written file;
- read the exit code of a command for scripts;
- move a block of settings into an extension with `move`;
- explain what `exp:ini` does with secrets and with the INI cache.

## Trainer's notes

### How to present it

Sources: [Change settings from the command line: exp:ini](../../../../features/6.0/exp-ini-command.md) and the full
reference [console exp:ini](../../../../bc/6.0/console-exp-ini.md).

1. **Five commands for a day** (4 minutes). `set`, `get`, `toggle`, `where`, `rem`. A setting is
   `<file>/<Block>/<Variable>`; `.ini` is optional; arrays end in `[]`, hash entries are `Variable[key]`.
2. **Safety** (3 minutes). Each changed file is copied to `var/backup/ini/<timestamp>/` first (mode 0600); writes are
   atomic and refused if the file changed meanwhile (exit 4); a write is recorded in the audit trail as
   `system.setting.write`; after a write the INI cache tag is cleared.
3. **Secrets** (2 minutes). Variables named like Password, Secret, Token, ApiKey and similar are shown as `********`;
   `--show-secrets` shows them.
4. **Exit codes** (2 minutes). 0 done or nothing to change, 1 usage error, 2 not found, 3 refused, 4 write failed.
   Scripts can rely on them.
5. **Moving into an extension** (4 minutes). `exp:ini move <file>/<Block> <from> <to>` writes the target first, then
   removes the block from the source; `--dry-run` shows both diffs; `--create-extension` creates a new extension for
   the move.

### Common questions

**Is `exp:ini` the same as `php bin/php/ini.php`?** Yes; the console runs that script.

**Why does a change not show on Velocity?** Velocity's workers keep the settings of their warm-up; they see the
change after `exp:velocity restart` (lesson 13.2).

**Can I add my own action?** Yes: actions and scope providers are classes registered in `settings/ini.ini
[IniCommandSettings]`.

### Pitfalls

- **Running as root.** `exp:ini` keeps owner and group of changed files, but a new file created as root can break
  the site. Run as the site user.
- **Forgetting the scope.** `set` needs a scope; `get` without one shows the value in effect.
- **Writing the default scope.** `settings/<file>.ini` is refused unless `--allow-default`; do not use it.

## Hands-on exercise

On the development installation.

1. List the actions: `php bin/php/console exp:ini actions`.
2. Set the site name for your public siteaccess, previewing first:

   ```bash
   php bin/php/console exp:ini set site.ini/SiteSettings/SiteName "Developer course" siteaccess:<sa> --dry-run
   php bin/php/console exp:ini set site.ini/SiteSettings/SiteName "Developer course" siteaccess:<sa>
   ```

3. Find the backup: `ls var/backup/ini/`.
4. Toggle the view cache globally and back:
   `php bin/php/console exp:ini toggle site.ini/ContentSettings/ViewCaching global` (run it twice).
5. Add and remove an array value:

   ```bash
   php bin/php/console exp:ini add site.ini/DebugSettings/DebugIPList[] 127.0.0.1 global
   php bin/php/console exp:ini add site.ini/DebugSettings/DebugIPList[] 127.0.0.1 global
   php bin/php/console exp:ini rem site.ini/DebugSettings/DebugIPList[] 127.0.0.1 global
   ```

6. Ask for a setting nobody sets and read the exit code:
   `php bin/php/console exp:ini get site.ini/MySettings/Nothing; echo "exit $?"`.
7. Move the `[TemplateSettings]` block of `site.ini` from the global override into your extension, previewing first:

   ```bash
   php bin/php/console exp:ini move site.ini/TemplateSettings global extension:mysite_hello --dry-run
   ```

   Read both diffs. Run it without `--dry-run` only if the block holds development settings you want in the
   extension; otherwise stop here.
8. Read the JSON form of one command: `php bin/php/console exp:ini get site.ini/SiteSettings/SiteName --json`.

## Expected result

- Step 2: the dry run prints a diff and "nothing written"; the real run names the file written.
- Step 3: a timestamped folder with the copy of the changed file.
- Step 5: the second `add` says "Nothing to change"; `rem` removes the value.
- Step 6: `exit 2`.
- Step 7: two diffs, the target extension file and the global override.
- The trainer asks each learner what exit code 3 means.

## Quiz

1. Which option shows a change without writing it?
2. Which exit code means "refused"?
   A. 1 B. 2 C. 3 D. 4
3. Where is the backup of a file changed by `exp:ini`?
4. What does `exp:ini move` write first: the target or the source?
5. How are passwords shown by `get`, `list` and `where`?

<details>
<summary>Answers</summary>

1. `--dry-run`.
2. **C.** 3: for example the default scope, an unknown siteaccess, or a move that would change a value in effect.
3. In `var/backup/ini/<timestamp>/<path>`.
4. The target, then the block leaves the source.
5. As `********`, unless `--show-secrets` is given.

</details>

## Further reading

- [Change settings from the command line: exp:ini](../../../../features/6.0/exp-ini-command.md).
- [console exp:ini](../../../../bc/6.0/console-exp-ini.md): every action, option and exit code, with output.
- [Audit trail](../../../../features/6.0/audit-trail.md): where settings writes are recorded.

## Related pages

- [Module 8: Settings and siteaccesses](README.md)
- [Developers: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
