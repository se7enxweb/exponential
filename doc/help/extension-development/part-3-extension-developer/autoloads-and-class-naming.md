---
title: Autoloads and class naming
audience: developer, app-creator
category: extension-development
order: 80
summary: How the kernel finds your classes: the autoload arrays, ezpgenerateautoloads.php and what it scans, naming that avoids collisions, and what to do when a class is not found.
keywords: autoload, ezpgenerateautoloads, ezp_extension.php, class not found, class naming, file naming, prefix, exp prefix, autoloadignore, Composer autoload, PSR-4, duplicate class
type: help
---
# Autoloads and class naming

Part 3, chapter 8. The kernel does not use `include` lines for classes. It keeps **autoload arrays**, maps from class
name to file, and loads a class the first time code uses it. Your extension's classes are added to those maps by a
generator you run after adding or renaming a class.

## The arrays

| File | Holds | Generated with |
|---|---|---|
| `autoload/ezp_kernel.php` | the kernel's classes | `-k` (kernel maintainers only) |
| `var/autoload/ezp_extension.php` | the classes of the extensions | `-e` |
| `var/autoload/ezp_override.php` | kernel classes replaced by extensions | `-o` |
| `var/autoload/ezp_tests.php` | test classes | `-s` |

The extension array lives in `var/`, because it describes this installation's extensions, not the kernel.

## The generator

```bash
php bin/php/ezpgenerateautoloads.php -e
```

Expected: `Found <n> classes, added <n> of them to the autoload array.`. Useful options (from `--help`):

| Option | Effect |
|---|---|
| `-e` | the extension array |
| `-n` | dry run: count, write nothing |
| `-v` | list what it finds |
| `--exclude <dir>` | skip folders |
| `<extension>` argument | one extension only |

## What it scans

In extension mode it walks the whole folder of every extension on disk (active or not, in every extension root) for PHP files that declare a class, interface or
trait, and skips each extension's `tests/` folder (test classes go into the tests array) and `tests/` of Composer
packages vendored inside the extension (`kernel/private/classes/ezautoloadgenerator.php`). Folders listed in the root's
`.autoloadignore` are never walked.

So your classes can live in `classes/`, `classes/<subfolder>/`, `autoloads/`, `modules/` ... wherever it makes sense.
`classes/` is the convention.

## Naming

Class names are global: two extensions with a class `Helper` break each other. The conventions of this ecosystem:

| Kind | Convention | Example |
|---|---|---|
| Your classes | a prefix of your extension or organization, camel case | `mysiteHelloGreeter`, `acmeShopExporter` |
| New classes of this project | the `exp` prefix | `expInfo`, `expExtensionCatalogue` |
| The kernel's historical classes | `eZ...`, `ezp...` | `eZINI`, `ezpExtension` (never reuse the prefix for new code) |
| File name | the class name in lower case | `classes/mysitehellogreeter.php` |
| Datatypes | `<datatype>type.php`, class `<Name>Type` | `datatypes/mysiterating/mysiteratingtype.php` |
| Metadata | `<extension>Info` in `ezinfo.php` | `mysite_helloInfo` |

One class per file. Do not declare functions or constants at file level in a class file: autoloading only works for
classes.

## Composer libraries inside an extension

An extension that needs a third-party library can require it in its `composer.json`; Composer's own autoloader loads
it, beside the kernel's arrays. A small library vendored into the extension (`extension/<ext>/vendor/...`) is found by
the generator too; prefer a Composer requirement where possible.

## When a class is not found

1. `php bin/php/ezpgenerateautoloads.php -e -v` and look for your class in the output.
2. Is the file in an extension root (`extension/` or a folder of `AdditionalExtensionDirectories[]`)? Every extension on disk is scanned, active or not, but only an active extension's settings register its parts.
3. Is there a parse error in the file? `php -l` it; a file PHP cannot parse is skipped.
4. Does another extension declare the same class name? The generator warns about duplicates.
5. Clear the caches and make the running PHP see the new map: reload PHP-FPM or `./console exp:velocity deploy`.

`php bin/php/checkclasses.php --quiet-ok` then loads every class and lists those PHP refuses.

## Related pages

- Previous: [Settings in an extension](settings-in-an-extension.md)
- Next: [Modules and views](modules-and-views.md)
- [Kernel class overrides: the last resort](kernel-overrides-the-last-resort.md)
- [Debugging extensions](debugging-extensions.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
