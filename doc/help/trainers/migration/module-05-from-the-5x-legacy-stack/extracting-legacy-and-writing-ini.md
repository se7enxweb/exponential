---
title: "Lesson 5.2: Extracting the legacy part and writing the configuration as INI"
audience: trainer, admin, developer
category: training
order: 52
summary: Learners extract the legacy part of a 5.x project into a new Exponential directory, copy the right files, replace the built-in extensions, and write down as INI settings what the Symfony side used to inject into legacy at run time, including siteaccess matching. 60 minutes.
keywords: migrating to exponential, 5.x, extraction, ezpublish_legacy, built-in extensions, ezpublish.yml, parameters.yml, site.ini, siteaccess matching, MatchOrder, HTMLForms Secret, exp:ini, injected settings
type: help
---
# Lesson 5.2: Extracting the legacy part and writing the configuration as INI

Under the dual-kernel stack the legacy kernel did not read several of its own settings: the bridge overwrote them
with values from the Symfony configuration. After the extraction nothing overwrites them. This lesson teaches the
extraction of chapter 15.5 and the translation of chapter 15.6, the step that most often leaves a migrated site
pointing at the wrong database or with no form token secret.

## At a glance

| | |
|---|---|
| Course | [Migrating to Exponential](../README.md) |
| Module | [Module 5: From the 5.x legacy stack](README.md) |
| Duration | 60 minutes (25 presenting, 35 exercise) |
| Level | Advanced |
| Audience | Administrators and developers |
| Prerequisites | [Lesson 5.1](which-variant-and-what-carries-over.md); shell access to a training installation |

## Learning objectives

After this lesson, learners can:

- create the new Exponential directory next to the old project and say why not inside it;
- list what to copy from `ezpublish_legacy/` and what not;
- name the replacement of a 5.x built-in extension;
- list five settings the Symfony side injected and where each is written in INI;
- translate a YAML siteaccess matcher into `[SiteAccessSettings]` and check a value with `exp:ini`.

## Trainer's notes

### How to present it

Sources: [chapter 15.5](../../../../install/15-migrating-from-5x-legacy.md#155-extract-the-legacy-part) and
[15.6](../../../../install/15-migrating-from-5x-legacy.md#156-translate-the-symfony-configuration-into-ini).

1. **A new directory** (3 minutes). `composer create-project se7enxweb/exponential exponential`; never
   `composer require` inside the old project, whose `symfony-app-dir` would put Exponential into the wrong place.
2. **What to copy** (6 minutes). Show the table of 15.5: settings overrides and siteaccess settings, your own
   extensions and designs (not `admin`, `admin2`, `base`, `standard`), the storage with the same `<site>` directory
   name, packages, `config.php`. Copy the files symlinks point at, not the links (`cp -rL`).
3. **Built-in extensions** (4 minutes). Not copied: Exponential has its own. ezjscore, ezoe and ezformtoken are in the
   kernel repository; ezflow, ezwebin and others installed by default; ezfind suggested; `ez_network` has no
   equivalent and is removed from `ActiveExtensions`.
4. **Injected settings** (7 minutes). Show the table of 15.6: database connection, `VarDir`, `AnonymousUserID`, path
   prefix and root node, the image alias list (the YAML `image_variations` replaced the legacy list), ImageMagick
   options, and the form token secret `[HTMLForms] Secret`. Since 6.0.15 a POST without a valid form token is refused
   with HTTP 403, so the secret matters.
5. **Siteaccesses** (5 minutes). In YAML: list, groups, match, system. In Exponential: the list and the matching are
   global (`[SiteAccessSettings]`), each siteaccess's configuration is in `settings/siteaccess/<name>/`; groups have no
   direct equivalent.

### Common questions

**Why do policies on `user/login` break if we rename a siteaccess?** The limitation stores a CRC32 of the name: keep
the siteaccess names (15.1).

**Can we keep a Symfony front end?** Yes, with the se7enxweb legacy bridge on current Symfony versions; chapter 15
assumes Exponential runs on its own ([Legacy bridge](../../../../features/6.0/legacy-bridge.md)).

**How do I see which file sets a value?** `php bin/php/console exp:ini where <file>/<block>/<key>` (15.6).

### Pitfalls

- **Copying symlinks.** The new tree then points into the old project; when the old project is archived, the site
  breaks.
- **Running `composer update` over checkouts.** Chapter 15.1: never `composer update` over your own checkouts.
- **Leaving the database settings to chance.** In a 5.x project they were often empty or stale in the legacy files.

## Hands-on exercise

Use your own training installation for the read-only commands; the translation part is on paper.

1. In the installation root, see which file sets the database name and the storage directory:

   ```bash
   php bin/php/console exp:ini where site.ini/DatabaseSettings/Database
   php bin/php/console exp:ini where site.ini/FileSettings/VarDir
   ```

2. Read the siteaccess list and the matching order:

   ```bash
   php bin/php/console exp:ini get site.ini/SiteAccessSettings/AvailableSiteAccessList
   php bin/php/console exp:ini get site.ini/SiteAccessSettings/MatchOrder
   ```

3. Read the image alias list:

   ```bash
   php bin/php/console exp:ini get image.ini/AliasSettings/AliasList
   ```

4. Translate this YAML of an old 5.x project into INI, using the siteaccess table of chapter 15.6. Write the file
   name for each part:

   ```yaml
   ezpublish:
       siteaccess:
           default_siteaccess: eng
           list: [eng, ger, admin]
           match:
               Map\URI: { en: eng, de: ger, admin: admin }
       system:
           ger:
               languages: [ger-DE, eng-GB]
   ```

5. The same project's `parameters.yml` has a `secret`, a Doctrine connection with `pdo_mysql`, and `var_dir:
   var/mysite`. Write the INI settings for each, with the block and key from chapter 15.6 (use made-up values, never a
   real password).
6. List what you copy from `ezpublish_legacy/` of this project, and which built-in extensions you leave behind.

## Expected result

- Steps 1 to 3 show the files that set each value and the current lists of the training installation.
- Step 4: `[SiteSettings] DefaultAccess=eng`; `AvailableSiteAccessList[]` with `eng`, `ger` and `admin`;
  `MatchOrder=uri`, `URIMatchType=map`, `URIMatchMapItems[]=en;eng` and the other two; in
  `settings/siteaccess/ger/site.ini.append.php` a `[RegionalSettings] SiteLanguageList[]` with `ger-DE` then `eng-GB`.
- Step 5: `[HTMLForms] Secret` with a long random value; `DatabaseImplementation=ezmysqli` and the connection keys;
  `[FileSettings] VarDir=var/mysite`.
- Step 6: overrides, siteaccess settings, own extensions and designs, storage, packages, `config.php`; not ezjscore,
  ezoe, ezformtoken or the other built-in extensions.
- The trainer checks step 4 on one sheet.

## Quiz

1. Why must Exponential not be installed with `composer require` inside the old 5.x project?
2. Which setting must get a long random value, because since 6.0.15 a POST without a valid form token is refused?
   A. `[UserSettings] HashType` B. `[HTMLForms] Secret` C. `[SiteSettings] DefaultAccess` D. `[FileSettings] VarDir`
3. What does the Doctrine driver `pdo_pgsql` become in `DatabaseImplementation`?
4. Where does the configuration of one siteaccess live in Exponential?
5. What happens to the 5.x built-in extension `ez_network`?

<details>
<summary>Answers</summary>

1. The old `composer.json` sets `symfony-app-dir`, so it would land in `ezpublish_legacy/` of a Symfony application
   that no longer matches ([chapter 15.5](../../../../install/15-migrating-from-5x-legacy.md#155-extract-the-legacy-part)).
2. **B** ([chapter 15.6](../../../../install/15-migrating-from-5x-legacy.md#156-translate-the-symfony-configuration-into-ini)).
3. `ezpostgresql` (chapter 15.6).
4. In `settings/siteaccess/<name>/`; the list and the matching are global in `[SiteAccessSettings]` (chapter 15.6).
5. It has no equivalent; remove it from `ActiveExtensions` (chapter 15.5).

</details>

## Further reading

- [Chapter 15.6: Translate the Symfony configuration into INI](../../../../install/15-migrating-from-5x-legacy.md#156-translate-the-symfony-configuration-into-ini):
  every injected setting and siteaccess matcher.
- [Chapter 10.2: Siteaccesses and site addresses](../../../../install/10-after-installing.md#102-siteaccesses-and-site-addresses):
  siteaccess matching in detail.
- [Chapter 17.4: Settings, INI and YAML](../../../../install/17-migration-reference.md#174-settings-ini-and-yaml):
  the lookup table.

## Related pages

- [Module 5: From the 5.x legacy stack](README.md)
- [Migrating to Exponential: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
