---
title: "Lesson 4.3: Files, settings, designs and porting extensions to PHP 8"
audience: trainer, admin, developer
category: training
order: 43
summary: Learners carry the storage, the settings overrides and the designs of an old site into Exponential 6 the right way, review the settings that must change, and port their own extensions from PHP 5 to PHP 8 with the checklist of chapter 14.9. 60 minutes.
keywords: migrating to exponential, var storage, VarDir, settings override, siteaccess, design extension, templates, eztemplatecheck, PHP 8, porting extensions, exp:checkclasses, autoloads, each, create_function, YUI, jQuery 4
type: help
---
# Lesson 4.3: Files, settings, designs and porting extensions to PHP 8

Next to the database run two more tracks: the files and settings, and the code. This lesson covers what to copy
(and what never to copy), the settings that must change, where designs belong, and the checklist for porting your
own extensions from PHP 5 to PHP 8, which chapter 14 calls the largest part of most migrations.

## At a glance

| | |
|---|---|
| Course | [Migrating to Exponential](../README.md) |
| Module | [Module 4: From the 4.x line](README.md) |
| Duration | 60 minutes (25 presenting, 35 exercise) |
| Level | Advanced |
| Audience | Administrators (files and settings) and developers (code) |
| Prerequisites | [Lesson 4.2](the-database-runbook.md); PHP knowledge for the code part; shell access to a training installation |

## Learning objectives

After this lesson, learners can:

- name what to copy from the old site (storage, overrides, siteaccess settings, configuration files) and what never
  to copy (caches, the default `settings/*.ini`);
- explain why `[FileSettings] VarDir` must stay exactly as it was;
- name five settings that change between 4.x and Exponential 6;
- move a design into a design extension and check templates with `eztemplatecheck.php`;
- run the porting checklist on an extension and fix three PHP 8 breaks.

## Trainer's notes

### How to present it

Sources: [chapter 14.6](../../../../install/14-migrating-from-4x.md#146-files-storage-images-binary-files-and-the-cluster),
[14.7](../../../../install/14-migrating-from-4x.md#147-settings-and-siteaccesses),
[14.8](../../../../install/14-migrating-from-4x.md#148-designs-templates-and-javascript) and
[14.9](../../../../install/14-migrating-from-4x.md#149-porting-your-own-extensions-from-php-5-to-php-8).

1. **Files** (5 minutes). Copy `var/` (at least the storage); keep `VarDir` as it was, because every stored image
   path starts with it; never copy caches; image aliases are rebuilt on demand. Do not use the old `chmod -R a+rwx`
   advice.
2. **Settings** (6 minutes). Copy only `settings/override/`, `settings/siteaccess/<name>/` and the configuration
   files; never the old `settings/*.ini`. Walk through the table "Settings to change": `ezmysql` to `ezmysqli`,
   charset `utf-8`, `ezdhtml` out of `ActiveExtensions`, the admin `[DesignSettings]` from a fresh admin siteaccess,
   override blocks sorted by `Priority`, the YUI keys deleted, `*.ini.append` renamed to `*.ini.append.php`. Rewrite
   rules come from [chapter 8](../../../../install/08-serving-the-site.md), not from the old site.
3. **Designs** (4 minutes). Move your designs into a design extension; never edit packaged extensions (they are
   replaced on every update). YUI loads nothing any more; jQuery is 4.0.0.
4. **Porting** (10 minutes). The seven-step checklist of 14.9: inventory, lint every file, regenerate autoloads and
   load every class with `exp:checkclasses`, check characters around PHP tags, fix the PHP 8 breaks, then
   deprecations, templates and settings, give the extension a version. Show the table "Functions and syntax removed
   from PHP" (`each()`, `create_function()`, `$string{0}`, `split()`, the `mysql_*` family, `implode()` argument
   order).

### Common questions

**Why does a template override no longer win?** Override blocks are sorted by `Priority` since July 2026, or the
extension order changed (14.13).

**`Class '...' not found`?** The autoload array was not regenerated: `php bin/php/ezpgenerateautoloads.php -e`, then
clear the caches (14.13).

**Can our TinyMCE 3 plugins for the online editor stay?** On the default engine, yes; ezoe runs TinyMCE 3.5.12 by
default and TinyMCE 8 is opt-in (14.8).

### Pitfalls

- **Copying the whole old `settings/` directory.** The new defaults are part of the code; the old files hide them.
- **A parse error in one extension.** It stops the whole site, not only the extension: lint first.
- **Editing ezwebin or ezflow in place.** The next update overwrites it; overrides go into your own design
  extension.

## Hands-on exercise

Part 1 runs on your training installation; part 2 is a code-reading exercise for developers (administrators do the
settings review instead).

1. In the installation root, check the templates of your public siteaccess (use its name from
   `settings/siteaccess/`):

   ```bash
   ls settings/siteaccess/
   php bin/php/eztemplatecheck.php -s<siteaccess>
   ```

2. Lint every PHP file of one extension of your choice:

   ```bash
   find extension/<extension> -name '*.php' -print0 | xargs -0 -n1 php -l | grep -v '^No syntax errors'
   ```

3. Load every class and report the ones PHP refuses:

   ```bash
   php bin/php/console exp:checkclasses
   ```

4. Read the storage directory setting:

   ```bash
   php bin/php/console exp:ini get site.ini/FileSettings/VarDir
   ```

5. **Developers**: rewrite this PHP 5 code for PHP 8, using the tables of chapter 14.9:

   ```php
   while ( list( $key, $value ) = each( $items ) ) { $out[] = $key; }
   $double = create_function( '$a', 'return $a * 2;' );
   $first = $name{0};
   $parts = split( ',', $csv );
   $line = implode( $parts, ';' );
   ```

6. **Administrators**: for an old site with `DatabaseImplementation=ezmysql`, `ezdhtml` in `ActiveExtensions[]`,
   `ezjscore.ini` with `PreferredLibrary=yui3` and a file `site.ini.append`, write what you change and why, using the
   table "Settings to change" of chapter 14.7.

## Expected result

- Step 1 ends without errors on the shipped templates; step 2 prints nothing for a clean extension.
- Step 3 lists no class, or the classes PHP refuses with the reason.
- Step 4 prints the `VarDir` of the installation (for example `var/site` or the site package's own).
- Step 5: `foreach ( $items as $key => $value )`, a closure `function ( $a ) { return $a * 2; }`, `$name[0]`,
  `explode( ',', $csv )`, `implode( ';', $parts )`.
- Step 6: `ezmysqli`, remove `ezdhtml` (ezoe replaced it), delete the YUI keys, rename to `site.ini.append.php` with
  the `<?php /* #?ini charset="utf-8"?` first line.
- The trainer checks step 5 or step 6 for each learner.

## Quiz

1. Why must `[FileSettings] VarDir` stay as it was on the old site?
2. Which of these do you **not** copy from the old installation?
   A. `settings/override/` B. `settings/siteaccess/<name>/` C. `var/cache/` D. `var/<VarDir>/storage/`
3. Which command reports every class PHP refuses to load?
4. Where do changes you made inside a packaged extension such as ezwebin go?
5. What replaces `create_function()` in PHP 8?

<details>
<summary>Answers</summary>

1. The paths of every stored image and file start with it
   ([chapter 14.6](../../../../install/14-migrating-from-4x.md#146-files-storage-images-binary-files-and-the-cluster)).
2. **C.** Caches are rebuilt by the new code (chapter 14.6).
3. `php bin/php/console exp:checkclasses`
   ([chapter 14.9](../../../../install/14-migrating-from-4x.md#149-porting-your-own-extensions-from-php-5-to-php-8)).
4. Into your own design extension, as overrides
   ([chapter 14.8](../../../../install/14-migrating-from-4x.md#148-designs-templates-and-javascript)).
5. A closure, `function ( $a ) { ... }` (chapter 14.9, "Functions and syntax removed from PHP").

</details>

## Further reading

- [Chapter 14.7: Settings and siteaccesses](../../../../install/14-migrating-from-4x.md#147-settings-and-siteaccesses):
  the full table of settings to change.
- [Chapter 14.9: Porting your own extensions](../../../../install/14-migrating-from-4x.md#149-porting-your-own-extensions-from-php-5-to-php-8):
  every PHP 8 break with before and after code.
- [YUI removed](../../../../bc/6.0/yui-removal.md): replacing YUI in templates of your own.

## Related pages

- [Module 4: From the 4.x line](README.md)
- [Migrating to Exponential: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
