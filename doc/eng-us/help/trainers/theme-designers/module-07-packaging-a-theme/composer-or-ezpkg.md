---
title: "Lesson 7.3: A Composer package or an .ezpkg"
audience: trainer, designer, developer
category: training
order: 73
summary: Learners choose how to ship a theme (Composer, Composer with .ezpkg inside, or .ezpkg only), export their theme as an .ezpkg with ezpm after switching extension export on for it, inspect the archive, and switch export off again. 45 minutes.
keywords: theme design, package, ezpkg, Composer, composer.json, ezpublish-legacy-extension, ezpm.php, create, add, export, ezextension, ExtensionExportSettings, AllowedExtensions, package/export_extension, secret check, site style, Setup Packages
type: help
---
# Lesson 7.3: A Composer package or an .ezpkg

A finished theme is handed out as a Composer package, as an Exponential package (`.ezpkg`), or both. The choice
depends on who installs it and whether it carries database content such as content classes. In this lesson learners
make the choice for their theme and build an `.ezpkg` from it on the command line.

## At a glance

| | |
|---|---|
| Course | [Theme design](../README.md) |
| Module | [Module 7: Packaging a theme](README.md) |
| Duration | 45 minutes (15 presenting, 30 exercise) |
| Level | Intermediate |
| Audience | Designers and developers who publish themes |
| Prerequisites | [Lesson 7.1](theme-extension-metadata.md); shell access to the learner's own installation |

## Learning objectives

After this lesson, learners can:

- choose between a Composer package and an `.ezpkg` with the decision table;
- name the four package types of **Setup > Packages > Create new package**;
- switch extension export on for one extension, and explain the secret check;
- create, fill and export a package with `ezpm.php`, and inspect the archive.

## Trainer's notes

### How to present it

Sources: [Composer package or .ezpkg](../../../designers/extensions/composer-or-ezpkg.md),
[Exporting an extension as a package](../../../designers/extensions/extension-export-package.md) and
[Exporting and importing with ezpm](../../../designers/extensions/exporting-with-ezpm.md).

1. **The decision table** (5 minutes).

   | Question | Composer package | `.ezpkg` |
   |---|---|---|
   | Who installs it? | Developers and hosts who run `composer` | Administrators who work in **Setup > Packages** |
   | What does it carry? | Files: the theme extension | Files and database content: classes, objects, a site style |
   | How is it updated? | `composer update <vendor>/<theme>` | Installing the next package version |

   Three answers: Composer only; Composer with `.ezpkg` class packages inside (`packages/`); `.ezpkg` only.
2. **Package types** (3 minutes). **Setup > Packages**, **Create new package**: **Content object export**, **Content
   class export**, **Extension export** ("Exporting extensions has to be switched on first") and **Site style** (two
   stylesheets, their images and a thumbnail).
3. **Export is off by default** (4 minutes). `settings/package.ini [ExtensionExportSettings] AllowedExtensions[]` is
   empty: no extension may be exported. The administrator lists the extension there, and in the administration the role
   also needs `package/export_extension`. Every way of exporting refuses an extension that holds secret values or key
   material.
4. **ezpm** (3 minutes). `php ezpm.php help` lists the commands: `create`, `add`, `export`, `import`, `check`,
   `install`, `validate`, `list`, `info`.

### Common questions

**Why is export switched off?** An extension may hold settings with passwords or keys; exporting it would put them in a
file that is handed around. The secret check refuses those, and the allow-list makes export a deliberate decision.

**Can I ship the theme's content classes with Composer?** Yes, as `.ezpkg` class packages in the theme's `packages/`
folder, imported by the installer ([Bundling content class packages](../../../designers/extensions/bundling-content-class-packages.md)).

**What does `composer.json` need?** `type: ezpublish-legacy-extension` and `extra.ezpublish-legacy-extension-name`
with the folder name ([composer.json for a theme](../../../designers/extensions/composer-json-for-a-theme.md)).

### Pitfalls

- **Leaving export switched on.** End the exercise by removing the allow-list entry.
- **Exporting into the installation's web-served tree.** Write the archive to `var/tmp/` and move it where it belongs.
- **A secret in the theme.** If the check refuses the theme, read why; never weaken the check to get the package out.

## Hands-on exercise

On your own training installation, in the installation root.

1. In the administration, open **Setup > Packages**, press **Create new package** and read the four types. Press
   **Cancel**.
2. Decide for `training_theme` which of the three answers fits, and write one sentence why.
3. Allow the export of your theme only:

   ```bash
   php bin/php/console exp:ini add 'package.ini/ExtensionExportSettings/AllowedExtensions[]' training_theme global
   php bin/php/ezcache.php --clear-tag=ini
   ```

4. Create and fill the package:

   ```bash
   php ezpm.php create training_theme_pkg 'Training theme' 0.1.0 install
   php ezpm.php add training_theme_pkg ezextension training_theme
   ```

5. Export it: `php ezpm.php export training_theme_pkg -d var/tmp`.
6. Find the file (`ls var/tmp/training_theme_pkg*`) and list its content with `tar tzf <file> | head -30`.
7. Check the list: `package.xml`, the theme's files, and no `.bak`, `.env` or key files.
8. Validate the archive as a receiving installation would: `php ezpm.php validate <file>`.
9. Switch export off again:

   ```bash
   php bin/php/console exp:ini rem 'package.ini/ExtensionExportSettings/AllowedExtensions[]' training_theme global
   php bin/php/ezcache.php --clear-tag=ini
   ```

## Expected result

- Step 5 writes an archive in `var/tmp/` whose name starts with `training_theme_pkg-0.1.0` and ends in `.ezpkg` (the
  name carries the version, and may carry the package's release number).
- Step 6 lists `package.xml` and the files of `training_theme`.
- Step 8 reports the archive valid.
- Step 9: `php bin/php/console exp:ini where package/ExtensionExportSettings/AllowedExtensions` no longer lists the
  theme in the override file.
- The trainer checks one learner's archive list on the projector.

## Quiz

1. A theme needs two content classes of its own. Which way of shipping fits best?
2. Why can no extension be exported on a fresh installation?
3. Which four package types does **Create new package** offer?
4. Which `ezpm.php` command writes the archive?
   A. `create` B. `add` C. `export` D. `install`
5. What do you do if the secret check refuses your theme?

<details>
<summary>Answers</summary>

1. Composer with the class packages inside (`packages/`), or an `.ezpkg` that carries them
   ([Composer package or .ezpkg](../../../designers/extensions/composer-or-ezpkg.md)).
2. `package.ini [ExtensionExportSettings] AllowedExtensions[]` is empty by default.
3. Content object export, Content class export, Extension export, Site style.
4. **C.** `export <package> -d <dir>`.
5. Read the finding and remove the secret from the theme; never weaken the check
   ([The secret check](../../../designers/extensions/package-secret-check.md)).

</details>

## Further reading

- [What an Exponential package is](../../../designers/extensions/what-an-exponential-package-is.md).
- [The package creator wizard, step by step](../../../designers/extensions/package-creator-wizard-step-by-step.md):
  the same in the browser.
- [The site style package](../../../designers/extensions/site-style-package.md): sharing only a color scheme.
- [ezpm: the package manager on the command line](../../../../features/6.0/ezpm-package-manager-cli.md).

## Related pages

- [Module 7: Packaging a theme](README.md)
- [Theme design: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
