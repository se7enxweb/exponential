---
title: "Lesson 7.3: Autoloads and activation"
audience: trainer, developer
category: training
order: 73
summary: Learners regenerate the extension autoload array after adding classes, switch extensions on for the whole installation or one siteaccess, use Setup > Extensions with Review changes, and recover from a class that is not found. 45 minutes.
keywords: developers course, autoload, ezpgenerateautoloads, -e, autoload/ezp_extension.php, ActiveExtensions, ActiveAccessExtensions, Setup Extensions, Review changes, Regenerate autoload arrays, class not found, checkclasses
type: help
---
# Lesson 7.3: Autoloads and activation

Two things stand between an extension's code and a working site: the kernel must find its classes, and the
extension must be switched on. This lesson covers both: the autoload array and when to regenerate it, the two
activation settings, the **Extensions** page of the administration, and what to do when a class is not found.

## At a glance

| | |
|---|---|
| Course | [Developers](../README.md) |
| Module | [Module 7: Extensions](README.md) |
| Duration | 45 minutes (15 presenting, 30 exercise) |
| Level | Intermediate |
| Audience | Developers and administrators |
| Prerequisites | [Lesson 7.2](extension-xml-and-ezinfo.md) |

## Learning objectives

After this lesson, learners can:

- regenerate the extension autoloads with `php bin/php/ezpgenerateautoloads.php -e` and say when it is needed;
- find a class in the generated autoload array;
- switch an extension on with `ActiveExtensions[]` or for one siteaccess with `ActiveAccessExtensions[]`;
- activate, deactivate and review changes in **Setup** > **Extensions**;
- diagnose a "class not found" with the autoloads, `checkclasses.php` and the caches.

## Trainer's notes

### How to present it

Sources: [Extensions: find, install, configure, build and release](../../../../guides/extensions.md),
[The Extensions page](../../../../guides/extensions-page.md) and [RAD tools](../../../../features/6.0/rad-tools.md)
(the health check and `checkclasses.php`).

1. **Autoloads** (4 minutes). The kernel does not search folders for classes at run time; it reads generated maps
   in `autoload/`. `php bin/php/ezpgenerateautoloads.php -e` scans the extensions; `-k` the kernel; `-o` the kernel
   overrides. Run `-e` after adding, renaming or moving a class in an extension.
2. **Activation** (3 minutes). `site.ini [ExtensionSettings] ActiveExtensions[]` in `settings/override/` switches an
   extension on everywhere; `ActiveAccessExtensions[]` in a siteaccess's settings switches it on for that siteaccess.
3. **The Extensions page** (5 minutes). **Setup** > **Extensions**: one list in loading order, **Activate** and
   **Deactivate** buttons on the cards, **Review changes** shows the plan and the risks before anything is written,
   and **Regenerate autoload arrays** does the `-e` run from the browser.
4. **When a class is not found** (3 minutes). In order: is the file in `classes/` (or another folder the generator
   scans)? did `-e` run? is the extension active? were the caches cleared and, on a server that keeps PHP in memory,
   the workers reloaded (lesson 13.2)? `php bin/php/checkclasses.php` lists every declared class PHP refuses to load.

### Common questions

**Do I commit the autoload files?** The extension autoload array is generated per installation; your extension's
repository holds the classes, not the generated map.

**Why does a deactivated extension still show in the autoloads?** The generator maps classes of extensions on disk;
activation decides whether settings, designs and modules are used.

**Can activation break a site?** Yes, an extension with errors can stop every page. Activate on the development
installation first; **Review changes** warns about known risks.

### Pitfalls

- **Forgetting `-e` after a new class.** The most common cause of "class not found".
- **A class name used twice.** Two extensions with the same class name: only one of them can be loaded; give yours your
  own prefix.
- **Activating on the live site first.** Always on development, then deploy.

## Hands-on exercise

On the development installation.

1. Regenerate the extension autoloads: `php bin/php/ezpgenerateautoloads.php -e`.
2. Find your fetch function class in the generated map:
   `grep -rn "mysiteHelloFunctionCollection" autoload/`.
3. Rename the class file `classes/mysitehellofunctioncollection.php` to `classes/mysite_fetch.php`, reload the
   playground of lesson 5.4 without regenerating, and read the error. Regenerate with `-e` and run again.
4. Check that every class of the installation loads: `php bin/php/checkclasses.php --quiet-ok`.
5. Open **Setup** > **Extensions** in the administration and find the card of `mysite_hello`. Read where it loads.
6. Press **Deactivate** on `mysite_hello`, then **Review changes**, read the plan, and leave without applying
   (or discard the changes).
7. Make `mysite_hello` active for your public siteaccess only, with the console (and back again):

   ```bash
   php bin/php/console exp:ini add site.ini/ExtensionSettings/ActiveAccessExtensions mysite_hello siteaccess:<sa> --dry-run
   ```

   Read the dry run; do not run it without `--dry-run` unless your trainer asks.
8. Commit the renamed class file in the extension's repository.

## Expected result

- Step 2: one line mapping the class to its file under `extension/mysite_hello/classes/`.
- Step 3: before regenerating, the fetch fails (the debug output names the missing class); after `-e` it works.
- Step 4 prints nothing when every class loads.
- Step 6: the review lists the deactivation of `mysite_hello` and what depends on it.
- The trainer asks each learner for the four checks of "class not found".

## Quiz

1. Which option of `ezpgenerateautoloads.php` scans the extensions?
   A. `-k` B. `-o` C. `-e` D. `--all`
2. Where is the setting that activates an extension for the whole installation?
3. What does **Review changes** on the Extensions page do?
4. Name the four checks when a class of your extension is not found.
5. Which script lists every declared class PHP refuses to load?

<details>
<summary>Answers</summary>

1. **C.** `-e`.
2. `site.ini [ExtensionSettings] ActiveExtensions[]`, in `settings/override/site.ini.append.php`.
3. It shows what would change, with the risks, before anything is written.
4. The file is in a scanned folder; `-e` ran; the extension is active; the caches were cleared and the PHP workers
   reloaded.
5. `php bin/php/checkclasses.php`.

</details>

## Further reading

- [The Extensions page](../../../../guides/extensions-page.md): activation, order, review and apply.
- [Autoloads and class naming](../../../creators/app-creators/autoloads-and-class-naming.md).
- [Additional extension directories](../../../../features/6.0/additional-extension-directories.md).

## Related pages

- [Module 7: Extensions](README.md)
- [Developers: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
