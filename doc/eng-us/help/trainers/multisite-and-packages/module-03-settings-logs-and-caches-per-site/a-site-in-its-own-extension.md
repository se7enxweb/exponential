---
title: "Lesson 3.1: A site in its own extension"
audience: trainer, admin, developer
category: training
order: 31
summary: Learners move the settings of the second site out of settings/siteaccess into a site extension with exp:ini move-all, check every value in effect stayed the same, and read the backups and the rollback rule. 55 minutes.
keywords: multisite and site packages, site extension, exp:ini move-all, exp:ini move, --create-extension, --activate, ActiveAccessExtensions, extension siteaccess settings, var/backup/ini, rollback, --dry-run
type: help
---
# Lesson 3.1: A site in its own extension

A site that lives in its own extension keeps its settings, templates and design in one directory that can be put
under version control and deployed on its own. Settings that collected in `settings/siteaccess/site_b` can be moved
there with one command, which checks that no value in effect changes and rolls back when one would. This lesson
moves the second site of the lab into an extension `site_clubb`.

## At a glance

| | |
|---|---|
| Course | [Multisite and site packages](../README.md) |
| Module | [Module 3: Settings, logs and caches per site](README.md) |
| Duration | 55 minutes (20 presenting, 35 exercise) |
| Level | Advanced |
| Audience | Administrators and developers |
| Prerequisites | [Module 1](../module-01-siteaccesses-for-several-sites/README.md): `site_b` and `site_b_ger` exist |

## Learning objectives

After this lesson, learners can:

- explain why each site of a multisite benefits from an extension of its own;
- preview a move of settings into an extension with `--dry-run` and read both diffs;
- move every settings file of a siteaccess into a new, activated site extension;
- explain the load order rule that can make a move change a value, and how the command protects against it;
- find the backup of each changed file.

## Trainer's notes

### How to present it

Source: [Changing settings from the command line: exp:ini, Moving settings into an extension](../../../../bc/6.0/console-exp-ini.md#moving-settings-into-an-extension).

1. **Why an extension per site** (4 minutes). One directory per site: settings, templates, design and translations
   travel together, can be versioned and deployed alone, and a new team member finds everything in one place. The
   theme design course covers the design part.
2. **The commands** (5 minutes):

   ```bash
   exp:ini move <file>/<Block> <from> <to>
   exp:ini move <file> <from> <to>
   exp:ini move-all <from> <to> [--files=site,content]
   ```

   Scopes for this lesson: `siteaccess:site_b` (from) and `extension:site_clubb:siteaccess:site_b` (to).
3. **Safety** (6 minutes). The target is written first, then the block leaves the source; files are never deleted.
   Before and after the write, the command reads the value in effect of every moved variable for every siteaccess; if
   any differs, both files are put back and the move is refused with exit 3. Load order explains why: an extension's
   settings load before `settings/siteaccess/<sa>`, which loads before `settings/override`, so a value moved out of the
   override into an extension can lose to a siteaccess file. Every changed file is first copied to
   `var/backup/ini/<timestamp>/<path>` (mode 0600).
4. **Creating and activating** (5 minutes). `--create-extension` makes `extension/<ext>/` with `settings/`, an
   `extension.xml` and an `ezinfo.php`. `--activate` adds it to `ActiveAccessExtensions[]` of the siteaccess for an
   extension siteaccess target. `ActiveExtensions` and `ActiveAccessExtensions` never move into an extension, because
   an extension cannot activate itself.

### Common questions

**Do I need to regenerate autoloads?** Not for a settings-only extension: a new extension has no classes. Once you
add PHP classes, run `php bin/php/ezpgenerateautoloads.php -e`.

**Can I undo a move?** Move it back the same way (`move-all` with the scopes swapped), or restore the files from
`var/backup/ini/`.

**Why does `--dry-run` not catch the load order problem?** It cannot compare values in effect, because nothing is
written; the comparison happens when the move is made.

**What about Velocity?** Workers keep the settings of their warm-up; restart Velocity after the move.

### Pitfalls

- **Forgetting `--activate`.** The move is refused while the extension is inactive, and the message prints the
  `exp:ini add ...` command that activates it. Read the message; it is the next step.
- **Names.** Extension names are `a-z`, `0-9` and `_` only.
- **Root-owned files.** Run as the user that serves the site. When run as root, the command keeps the owner of a
  changed file and gives new files the owner of `extension/`, but a habit of working as the site user avoids
  surprises.

## Hands-on exercise

Use your training installation from module 2. Commands run in the installation root.

1. Record the values in effect before the move:

   ```bash
   php bin/php/console exp:ini where site.ini/SiteSettings/SiteName site_b
   php bin/php/console exp:ini where content.ini/NodeSettings/RootNode site_b
   ```

2. Preview the move:

   ```bash
   php bin/php/console exp:ini move-all siteaccess:site_b extension:site_clubb:siteaccess:site_b \
       --create-extension --activate --dry-run
   ```

   Read the diffs and the summary line.
3. Run it without `--create-extension` and `--activate` first, and read the refusal and its exit code (`echo $?`).
4. Run the full move:

   ```bash
   php bin/php/console exp:ini move-all siteaccess:site_b extension:site_clubb:siteaccess:site_b \
       --create-extension --activate
   ```

5. List what was made: `ls -R extension/site_clubb`.
6. Repeat step 1. Compare the file named for each value with the first time.
7. Do the same for the German siteaccess:

   ```bash
   php bin/php/console exp:ini move-all siteaccess:site_b_ger extension:site_clubb:siteaccess:site_b_ger --activate
   ```

   If the command is refused, read its message: the refusals of a move name the option or the command that is
   missing (as in step 3). Run it again with that option.

8. Find the backups: `ls var/backup/ini/`.
9. Restart Velocity if it serves the lab, then open `/site_b` and `/site_b_ger`: both look exactly as before.
10. Read `extension/site_clubb/extension.xml` and `ezinfo.php` and note the version and license they were given.

## Expected result

- Step 3 is refused with exit code 3 and a message naming `--create-extension`.
- After step 4, `extension/site_clubb/settings/siteaccess/site_b/` holds `site.ini.append.php` and
  `content.ini.append.php`; `settings/siteaccess/site_b/site.ini.append.php` keeps only `ActiveAccessExtensions[]`
  (with a warning that it stays).
- Step 6 shows the same values in effect, now from the extension's files.
- Both sites render as before; `var/backup/ini/` has one timestamped directory per write.
- The trainer checks by running step 6 on one lab on the projector.

## Quiz

1. In which order are these loaded: the global override, an extension's settings, `settings/siteaccess/<sa>`?
2. A move would change a value in effect. What does `exp:ini` do?
   A. Writes it anyway B. Rolls both files back and refuses with exit 3 C. Deletes the source D. Asks a question
3. Why do `ActiveAccessExtensions[]` stay in the siteaccess directory?
4. Where is the copy of a file made before each write?
5. Does a settings-only extension need `php bin/php/ezpgenerateautoloads.php -e`?

<details>
<summary>Answers</summary>

1. An extension's settings, then `settings/siteaccess/<sa>`, then the global override, which has the last word.
2. **B.** Unless `--force` is given
   ([exp:ini](../../../../bc/6.0/console-exp-ini.md#moving-settings-into-an-extension)).
3. An extension cannot activate itself: its settings are read only once it is active.
4. In `var/backup/ini/<timestamp>/<path>`, mode 0600.
5. No: it has no classes. It needs autoloads once it gets PHP classes.

</details>

## Further reading

- [Changing settings from the command line: exp:ini](../../../../bc/6.0/console-exp-ini.md): every action, option and
  exit code.
- [Console: exp:ini](../../../../features/6.0/exp-ini-command.md): the feature page.
- [Settings files in an extension](../../../designers/extensions/settings-files-in-an-extension.md) and
  [Siteaccess settings in an extension](../../../designers/extensions/siteaccess-settings-in-an-extension.md).
- [Extension loading order](../../../../features/6.0/extension-loading-order.md).

## Related pages

- [Module 3: Settings, logs and caches per site](README.md)
- [Multisite and site packages: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
