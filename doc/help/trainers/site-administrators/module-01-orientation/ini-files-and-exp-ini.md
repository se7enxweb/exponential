---
title: "Lesson 1.5: INI files and exp:ini on the command line"
audience: trainer, admin
category: training
order: 15
summary: Learners find the INI files of an installation, read and write a setting with exp:ini in every common scope, preview changes with --dry-run, and know where backups and the audit record go. 35 minutes.
keywords: site administration, ini, exp:ini, settings, scope, global override, siteaccess, dry-run, where, get, set, rem, toggle, backup, ini cache
type: help
---
# Lesson 1.5: INI files and exp:ini on the command line

The settings page is comfortable, but administrators who work in a terminal, script a deployment or support many
sites need the same work on the command line. `exp:ini` reads and changes settings in every scope, keeps comments and
order in the files, previews every change with `--dry-run`, and backs up each file it changes.

## At a glance

| | |
|---|---|
| Course | [Site administration](../README.md) |
| Module | [Module 1: Getting oriented as a site administrator](README.md) |
| Duration | 35 minutes (10 presenting, 25 exercise) |
| Level | Intermediate |
| Audience | Site administrators comfortable with a terminal |
| Prerequisites | [Lesson 1.4](the-settings-page.md); a terminal in the training installation's root |

## Learning objectives

After this lesson, learners can:

- name the directories that hold INI files and the order in which they are read;
- read a value and its origin with `exp:ini get` and `exp:ini where`;
- write and remove a value in the `global` and `siteaccess:<name>` scopes, previewing with `--dry-run` first;
- find the backup of a file `exp:ini` changed;
- explain when an INI change reaches the web server.

## Trainer's notes

### How to present it

Source: [Change settings from the command line: exp:ini](../../../../features/6.0/exp-ini-command.md) and the full
reference [console exp:ini](../../../../bc/6.0/console-exp-ini.md).

1. **Where INI files live** (3 minutes). Show the tree in a terminal: `settings/` (the defaults, never edited),
   `settings/siteaccess/<siteaccess>/`, `settings/override/`, and `extension/<ext>/settings/`. Files that are not
   defaults end in `.ini.append.php`; the PHP comment at the top keeps them from being served as text.
2. **The scopes** (3 minutes). Put the scope table on the projector:

   | Scope | File written |
   |---|---|
   | `global` | `settings/override/<file>.ini.append.php` |
   | `siteaccess:<sa>` | `settings/siteaccess/<sa>/<file>.ini.append.php` |
   | `extension:<ext>` | `extension/<ext>/settings/<file>.ini.append.php` |
   | `default` | `settings/<file>.ini`, refused unless `--allow-default` |

3. **The actions** (2 minutes): `get`, `set`, `add`, `rem`, `clear`, `toggle`, `where`, `list`. `exp:ini --help`
   lists them; `exp:ini help <action>` explains one.
4. **Safety** (2 minutes). A backup of each changed file goes to `var/backup/ini/<timestamp>/`; writes are recorded
   in the audit trail as `system.setting.write`, with secrets as `[secret]`.

### Common questions

**Is `exp:ini` the same as editing the file?** It edits the file line by line, so comments, blank lines and order
stay as you wrote them. A text editor does the same, but gives no backup, no preview and no audit record.

**When does the site use my change?** After the INI cache is cleared:
`php bin/php/ezcache.php --clear-tag=ini`. `exp:ini` prints a hint saying where the change takes effect. Settings
that Velocity reads only at its start need `exp:velocity restart`.

**Can I run it as root?** Add `--allow-root-user`. Better: run it as the user that owns the files, so the web server
can still read them.

### Pitfalls

- **Quoting.** Values with spaces need quotes: `"Training site"`. Show a broken example once.
- **Forgetting the scope.** Without a scope, `set` has nowhere to write; with the wrong one it writes where the learner
  did not expect. Always run with `--dry-run` first and read the diff.
- **Editing `settings/*.ini`.** Default files are replaced at every upgrade. That is why `default` is refused.

## Hands-on exercise

In a terminal, in the root of your training installation:

1. List the actions:

   ```bash
   php bin/php/console exp:ini --help
   ```

2. Read the site name in effect and where it comes from:

   ```bash
   php bin/php/console exp:ini get site.ini/SiteSettings/SiteName
   php bin/php/console exp:ini where site.ini/SiteSettings/SiteName
   ```

3. Preview a change for the public siteaccess only, and read the diff:

   ```bash
   php bin/php/console exp:ini set site.ini/SiteSettings/SiteName "CLI training site" siteaccess:site --dry-run
   ```

4. Run the same command without `--dry-run`.
5. Run `where` again (step 2) and find the new file in the list.
6. List the backup directory and find the copy of the file before the change:

   ```bash
   ls var/backup/ini/
   ```

7. Clear the INI cache so the site uses the change, then reload the public site:

   ```bash
   php bin/php/ezcache.php --clear-tag=ini
   ```

8. Open **Setup > Ini settings** for `site.ini` and `site` and check that the page shows the same origin.
9. Remove the change and clear the INI cache again:

   ```bash
   php bin/php/console exp:ini rem site.ini/SiteSettings/SiteName siteaccess:site
   php bin/php/ezcache.php --clear-tag=ini
   ```

10. Run `where` a last time and check that the siteaccess file no longer sets the value.

## Expected result

- Step 3 prints a diff and writes nothing.
- After step 4, `where` lists `settings/siteaccess/site/site.ini.append.php` as the file in effect.
- A timestamped directory exists under `var/backup/ini/`.
- After step 7 the public site shows "CLI training site"; after step 9 the earlier name is back.
- The trainer checks by asking a learner to read the output of `where` aloud.

## Quiz

1. Which scope writes `settings/override/site.ini.append.php`?
   A. `default` B. `global` C. `siteaccess:admin` D. `extension:site`
2. What does `--dry-run` do?
3. Where does `exp:ini` put a backup of a file it changes?
4. You changed a setting with `exp:ini`, but the site does not show it. What do you run?
5. Why is the `default` scope refused unless you add `--allow-default`?

<details>
<summary>Answers</summary>

1. **B.** ([exp:ini scopes](../../../../features/6.0/exp-ini-command.md#scopes)).
2. It prints what would change, as a diff, and writes nothing.
3. In `var/backup/ini/<timestamp>/`.
4. `php bin/php/ezcache.php --clear-tag=ini`, and `exp:velocity restart` for a setting Velocity reads only at its
   start.
5. The default files are replaced by every upgrade, so a change there would be lost.

</details>

## Further reading

- [Change settings from the command line: exp:ini](../../../../features/6.0/exp-ini-command.md): the actions and scopes.
- [console exp:ini](../../../../bc/6.0/console-exp-ini.md): the full reference with the output of each action.
- [The settings page](../../../../guides/settings-page.md): the same settings in the browser.
- [Caches](../../../../guides/caches.md): the INI cache and the others (Module 5).

## Related pages

- [Module 1: Getting oriented as a site administrator](README.md)
- [Site administration: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
