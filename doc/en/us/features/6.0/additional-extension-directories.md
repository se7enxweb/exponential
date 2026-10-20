# Extensions in more than one folder

This page is for developers and administrators who want to keep their own project extensions apart from the
extensions they install. Until 2026 every extension had to live in `extension/`. That folder is also where community
extensions land, so your project code and the code you merely use sat side by side, and a clean checkout of a vendor
extension could overwrite your changes. Since 26 July 2026 (commit `17e7c11e71`) you can declare extra extension
folders. A typical split:

```
extension/        extensions you install (community, vendor)
extension_src/    extensions you write for this project or this customer
```

Moving an extension between the folders is a plain copy: no manifest, no `composer dump-autoload`.

## Set it up

1. Declare the extra folder in `settings/override/site.ini.append.php`:

   ```ini
   [ExtensionSettings]
   AdditionalExtensionDirectories[]
   AdditionalExtensionDirectories[]=extension_src
   ActiveExtensions[]=acme_customer_extension
   ```

2. Create the folder, move or copy an extension in, and refresh the autoload array:

   ```bash
   mkdir extension_src
   cp -r extension/acme_customer_extension extension_src/acme_customer_extension
   php bin/php/ezpgenerateautoloads.php -e
   php bin/php/ezcache.php --clear-all --allow-root-user
   ```

3. Open **Setup > Extensions**: the extension is listed and active, whichever root holds it.

Extensions are still switched on with `ActiveExtensions[]` and `ActiveAccessExtensions[]`; the kernel finds each one in
whichever root holds it.

| File | Block | Key | Default | Scope | Meaning |
|---|---|---|---|---|---|
| `settings/site.ini` | `ExtensionSettings` | `ExtensionDirectory` | `extension` | global | The classic root; always active |
| `settings/site.ini` | `ExtensionSettings` | `AdditionalExtensionDirectories[]` | empty list | global | More roots, scanned in the order written. A later root has higher priority. |

## When two roots hold the same extension

The root listed later wins completely for that extension: there is no merge, file by file. The autoload generator
prints a warning for each such collision, so a shadowing copy is never a surprise. This makes it safe to update a
vendor extension in `extension/` while a modified copy in `extension_src/` stays active.

## Names and case

The extension name in `ActiveExtensions[]`, `module.ini [ModuleSettings] ExtensionRepositories[]` and
`design.ini [ExtensionSettings] DesignExtensions[]` may differ in letter case from the folder (`adminaid` for
`AdminAid`). `eZExtension::extensionName( $name )` returns the real folder name, so the setting works on case
sensitive file systems (19 July, `c99f395fb5`). On case insensitive systems (macOS, Windows) the real case is also
returned and case tests are skipped (15 August, `c52c58c533`).

## For developers

Never build `'extension/' . $name`. Use:

| Call | Returns |
|---|---|
| `eZExtension::extensionRootDirectories()` | The merged list of roots, lowest priority first |
| `eZExtension::extensionPath( $name )` | The path of an extension, or `false` |
| `eZExtension::expandedPathList( $names, $subdirectory )` | Paths, for example of every `design` folder |
| `eZExtension::extensionName( $name )` | The folder name with the real case |
| `eZExtension::filterExtensionRootDirectories( $roots )` | The hook to compute roots at run time |

About 40 files that used to hard code `extension/` now use these calls: design and template lookup, modules, settings
editing, the autoload generator, datatypes, workflow types, notification handlers, shop managers, translations and the
command line tools (`bin/php/ezpgenerateautoloads.php`, `eztc.php`, `ezimportdbafile.php`, `ezwebincommon.php`,
`runcronjobs.php`).

The complete reference, with the load order of settings and the site extension pattern, is in
[Additional Extension Directories](../../bc/6.0/AdditionalExtensionDirectories.md).

## Limits

- Roots are relative to the installation directory unless you write an absolute path.
- Run `php bin/php/ezpgenerateautoloads.php -e` after moving an extension between roots.

## Related pages

- [Additional Extension Directories (reference)](../../bc/6.0/AdditionalExtensionDirectories.md)
- [Extension list and downloads](extension-list-and-downloads.md) (lists and downloads extensions from every root), [extension loading order](extension-loading-order.md), [extension module override](extension-module-override.md)
- [Per-site settings inside extensions](multi-site-ini-overrides.md), [INI override directories and placements](../../specifications/6.0/ini-override-placements.md)
- [Icon themes in extensions](icon-themes-in-extensions.md), [extension metadata](../../specifications/6.0/extension-metadata.md)
- [Behaviour changes of the legacy extensions (September and October 2026)](../../bc/6.0/extensions-behaviour-changes.md)
- [6.0.15 changelog](../../changelogs/6.0/6.0.15.md)
- History: [July 2026](../../history/2026/2026-07.md), [August 2026](../../history/2026/2026-08.md), [January 2026](../../history/2026/2026-01.md), [October 2024](../../history/2024/2024-10.md), [September 2024](../../history/2024/2024-09.md), [January 2024, second half](../../history/2024/2024-01b.md)
