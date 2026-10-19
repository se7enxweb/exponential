---
title: "Lesson 8.1: How settings are read"
audience: trainer, developer
category: training
order: 81
summary: Learners read the format of .ini and .ini.append.php files, follow the order in which eZINI reads the scopes, explain arrays, resets and hashes, and trace any value to its file with exp:ini where and the settings page. 45 minutes.
keywords: developers course, INI, eZINI, ini.append.php, settings/override, settings/siteaccess, scopes, load order, arrays, reset line, hash, exp:ini where, settings page, Ini settings, placement
type: help
---
# Lesson 8.1: How settings are read

Almost every behavior of Exponential is a setting in an `.ini` file, and the same setting can be written in many
files. This lesson explains the file format, the order in which the files are read, and how arrays add up, so that
learners can answer the everyday question "where does this value come from?" in one command.

## At a glance

| | |
|---|---|
| Course | [Developers](../README.md) |
| Module | [Module 8: Settings and siteaccesses](README.md) |
| Duration | 45 minutes (20 presenting, 25 exercise) |
| Level | Intermediate |
| Audience | Developers and administrators |
| Prerequisites | [Module 7](../module-07-extensions/README.md) |

## Learning objectives

After this lesson, learners can:

- read the format of `settings/site.ini` and of an `.ini.append.php` file;
- name the scopes and the order in which they are read;
- explain how arrays add up, what the reset line `Variable[]` does, and how hash entries are written;
- trace a value with `exp:ini where` and on the settings page of the administration.

## Trainer's notes

### How to present it

Sources: [Specification: INI override directories and placements](../../../../specifications/6.0/ini-override-placements.md),
[console exp:ini](../../../../bc/6.0/console-exp-ini.md#where-and-list) and
[The settings page](../../../../guides/settings-page.md).

1. **The format** (4 minutes). `settings/site.ini` holds the defaults: `[Block]` lines and `Variable=value`
   lines; `#` starts a comment. An `.ini.append.php` file has the same content inside `<?php /* #?ini
   charset="utf-8"?` and `*/ ?>`, so a web server never shows it as text.
2. **The order** (5 minutes). `settings/<file>.ini` first, then the override directories. `eZINI` merges them in
   the scope order `sa-extension`, `siteaccess`, `extension`, `override`: a later file wins. So
   `settings/override/` (the global scope) wins over everything; a setting in an extension's root `settings/`
   wins over `settings/siteaccess/<sa>/`.
3. **Arrays** (4 minutes). `List[]=a` in one file and `List[]=b` in a later one give `a, b`. The line `List[]` alone
   resets the array: values of earlier files are dropped. A hash entry is `List[key]=value`.
4. **The tools** (4 minutes). `exp:ini where <file>/<Block>/<Variable> [<sa>]` lists every file in load order and
   the value in effect, and lists files of inactive extensions separately. In the administration, **Setup** >
   **Ini settings** shows every value with the file it comes from.
5. **Caches** (2 minutes). Settings are cached; `config.php` turns off the file time check, so a hand edit is seen
   only after `php bin/php/ezcache.php --clear-tag=ini`. `exp:ini` clears it after each write.

### Common questions

**Why does my siteaccess setting not win?** The same variable is set in `settings/override/` or in an active
extension's root `settings/`, which are read later. `exp:ini where` shows it.

**Can a setting differ per machine?** Yes: [Settings per environment](../../../../features/6.0/environment-settings.md)
adds variant files such as `site.ini.test.append.php`, chosen by the constant `EXP_ENV`.

**Are settings files secret?** Some hold database passwords. `exp:ini` and the settings page mask values whose
names look like secrets.

### Pitfalls

- **Editing `settings/site.ini`.** It is the kernel's default file and is replaced at every update. Write overrides.
- **A forgotten reset line.** An array without `Variable[]` keeps the values of earlier files; with it, it drops them.
  Both are intended in different places; read the result with `where`.
- **Hand edits without clearing the INI cache.** The old value stays in effect.

## Hands-on exercise

On the development installation.

1. Open `settings/site.ini` and find the block `[SiteSettings]` and the variable `SiteName`.
2. Trace it: `php bin/php/console exp:ini where site.ini/SiteSettings/SiteName <sa>`.
3. Trace an array: `php bin/php/console exp:ini where site.ini/ExtensionSettings/ActiveExtensions`. Count how
   many files add to it.
4. Find a reset line in the defaults: `grep -n "^AvailableSiteAccessList\[\]$" settings/site.ini`. It starts the
   array empty; overrides then add the siteaccesses of this installation.
5. List the variables of a block in effect: `php bin/php/console exp:ini list site.ini/DebugSettings`.
6. List the blocks of the global override: `php bin/php/console exp:ini list site.ini global`. Notice masked
   values, if any.
7. In the administration, open **Setup** > **Ini settings**, choose `site.ini` and find `SiteName` with the file
   it comes from.
8. Write down, for one setting of your choice, the files in load order and the value in effect.

## Expected result

- Step 2: `settings/site.ini` with the default, the siteaccess file with the site's name, and `In effect:`.
- Step 3: several files, some from extensions; arrays add up.
- Step 6: secrets such as passwords appear as `********`.
- Step 7: the same value and file as step 2.
- The trainer checks step 8 for two learners.

## Quiz

1. What wraps the content of an `.ini.append.php` file, and why?
2. Which file wins: `settings/siteaccess/site/site.ini.append.php` or `settings/override/site.ini.append.php`?
   A. The siteaccess file B. The override file C. The one changed last D. Neither: they are merged by name
3. What does the line `ActiveExtensions[]` alone do?
4. Which command lists every file that sets a variable, in load order?
5. Why is a hand edit of a settings file not seen at once?

<details>
<summary>Answers</summary>

1. `<?php /* ... */ ?>`, so the web server runs it as PHP and never shows the settings as text.
2. **B.** The override (global) scope is read last and wins.
3. It resets the array: values from files read earlier are dropped.
4. `exp:ini where <file>/<Block>/<Variable>`.
5. Settings are cached and `config.php` turns off the file time check; clear the `ini` cache tag.

</details>

## Further reading

- [Specification: INI override directories and placements](../../../../specifications/6.0/ini-override-placements.md).
- [The settings page](../../../../guides/settings-page.md): reading and changing settings in the administration.
- [Settings per environment](../../../../features/6.0/environment-settings.md).

## Related pages

- [Module 8: Settings and siteaccesses](README.md)
- [Developers: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
