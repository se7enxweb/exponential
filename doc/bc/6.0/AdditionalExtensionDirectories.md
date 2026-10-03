# Additional extension directories

Read this page if you want to keep your own extensions apart from third-party ones, for example in `extension_src/`
next to `extension/`. Exponential 6.0 can load extensions from more than one directory. Nothing changes until you
switch it on: an existing installation behaves exactly as before.

## In short

| | |
|---|---|
| What changed | New setting `site.ini [ExtensionSettings] AdditionalExtensionDirectories[]` (empty by default). Each entry is one more extension root. Settings, designs, modules, translations and autoloads are found in every root. |
| Who is affected | Nobody until the setting is used. Code that builds extension paths by hand as `extension/<name>` will not find an extension that lives in another root; use `eZExtension::extensionPath()`. |
| How to check | `grep -n "AdditionalExtensionDirectories" settings/site.ini` |
| How to fix | Nothing to fix. To use it, follow [Move an extension to a second root](#move-an-extension-to-a-second-root). |

A package in any root is an ordinary legacy extension. The test of the design is that this works without any change
to a file, a manifest or `composer dump-autoload`:

```bash
cp -r extension/acme_customer_extension extension_src/acme_customer_extension
```

## Move an extension to a second root

The recommended layout keeps the two roots side by side:

```
ezroot/
    extension/          # third-party and community extensions
    extension_src/      # project and customer extensions
```

1. Create the new root:

   ```bash
   mkdir extension_src
   ```

2. Add it in `settings/override/site.ini.append.php`:

   ```ini
   [ExtensionSettings]
   AdditionalExtensionDirectories[]
   AdditionalExtensionDirectories[]=extension_src
   ```

3. Copy the extension you want to change:

   ```bash
   cp -r extension/acme_customer_extension extension_src/acme_customer_extension
   ```

4. Make your changes inside `extension_src/`.
5. Regenerate the legacy autoloads (or run `composer run legacy-scripts`):

   ```bash
   php bin/php/ezpgenerateautoloads.php
   ```

   To see which roots the generator scans without writing anything, use the dry run:
   `php bin/php/ezpgenerateautoloads.php -e -n`.

`composer dump-autoload` is not needed. You can run `extension/` and `extension_src/` side by side for as long as you
like.

Activation does not change: list the extension in `ActiveExtensions[]` or `ActiveAccessExtensions[]` as before. The
kernel looks the name up in the configured roots.

```ini
[ExtensionSettings]
ActiveExtensions[]
ActiveExtensions[]=site_app
ActiveExtensions[]=acme_customer_extension
```

## Settings

| File | Block | Key | Default | Scope |
|---|---|---|---|---|
| `site.ini` | `[ExtensionSettings]` | `ExtensionDirectory` | `extension` (always active) | global |
| `site.ini` | `[ExtensionSettings]` | `AdditionalExtensionDirectories[]` | empty list; scanned in the order written, a later entry has higher priority | global |

The shipped `settings/site.ini` carries the setting with its example commented out:

```ini
[ExtensionSettings]
ExtensionDirectory=extension
AdditionalExtensionDirectories[]
#AdditionalExtensionDirectories[]=extension_src
```

Because it is an ordinary INI array, you can add as many roots as you need, other extensions and `settings/override/`
can append roots without touching the kernel, and PHP code can change the list through
`eZExtension::filterExtensionRootDirectories()`.

### Example layouts

```ini
# Sibling root (recommended)
[ExtensionSettings]
AdditionalExtensionDirectories[]=extension_src
```

```ini
# An "extensions/" directory, for projects that already use that name
[ExtensionSettings]
AdditionalExtensionDirectories[]=extensions
```

```ini
# A top-level src/ directory, as in the original proposal
[ExtensionSettings]
AdditionalExtensionDirectories[]=src
```

```ini
# One root per customer or project
[ExtensionSettings]
AdditionalExtensionDirectories[]=sites/customer_a
AdditionalExtensionDirectories[]=sites/customer_b
```

## Which copy wins

If the same extension name exists in two roots, the root that comes later replaces the earlier one completely.
Files are not merged per subdirectory.

```ini
[ExtensionSettings]
ExtensionDirectory=extension
AdditionalExtensionDirectories[]
AdditionalExtensionDirectories[]=extension_src
```

With both `extension/foobar/` and `extension_src/foobar/`, the kernel uses only `extension_src/foobar/`. The autoload
generator applies the same rule and keeps the later copy without a warning, so check for duplicate names yourself
when you copy an extension. This is what makes a `composer update` of a vendor extension safe while your local copy
in `extension_src/` is active.

## Extension structure

A package in any root has the same layout as a classic extension. There is no new manifest, no PSR-4 requirement and
no change to class naming.

```
extension_src/acme_customer_extension/
├── extension.xml
├── settings/
│   └── site.ini.append.php
├── classes/
├── modules/
├── design/
├── autoloads/
└── ...
```

### A whole site in one package

One package can hold a complete site: design, grouped and ungrouped siteaccesses, code and templates. Several sites
can live side by side and be activated independently.

```
extension_src/site_app/
├── extension.xml
├── settings/
│   ├── override__app_user/
│   │   └── site.ini.append.php
│   ├── siteaccess/
│   │   ├── app_user__de/
│   │   ├── app_user__en/
│   │   └── app_admin/
│   └── siteaccess.ini.append.php
├── design/
│   ├── app_user/
│   └── app_admin/
├── classes/
├── modules/
└── autoloads/
```

## Settings, designs and overrides

The usual resolution applies to every root:

- `settings/*.ini.append.php` of each active package is loaded.
- `design/<siteaccess>/...` is found in each active package.
- `settings/override/` inside a package follows the usual extension, siteaccess and override rules, including `__`
  grouping.
- The global `settings/override/` stays on top of everything.

Load order, lowest priority first:

```
base package settings/
extension/package settings/siteaccess/<sa>/
extension/package settings/override__<group>/
extension/package settings/override/
global settings/override/
```

The only difference is that a package may live in any configured root.

## Autoloads

`bin/php/ezpgenerateautoloads.php` and `eZAutoloadGenerator` walk every configured root the same way they walk
`extension/*`:

- `var/autoload/ezp_extension.php` has the classes of all roots.
- `var/autoload/ezp_override.php` has the kernel overrides of all roots.
- `var/autoload/ezp_tests.php` has the test classes of all roots.
- Adding, moving or removing a package needs no `composer dump-autoload`; `vendor/autoload.php` is not touched.
- Class-based overrides (`[ClassSettings]`, workflow handlers, datatypes, operators) keep working, because
  `autoloads/*.php` is collected from every root.

The generator loads `eZExtension` through `require_once 'autoload.php';`.

## PHP API

All methods are static methods of `eZExtension` (`lib/ezutils/classes/ezextension.php`).

| Method | Returns |
|---|---|
| `extensionRootDirectories()` | The roots in priority order, low to high: `ExtensionDirectory`, then `AdditionalExtensionDirectories[]`, with empty entries and duplicates removed, passed through `filterExtensionRootDirectories()`. Paths as written in the setting. |
| `extensionPath( $name )` | The path of the extension in the root that wins, or `false`. The name matches case-insensitively; the result keeps the case on disk. |
| `expandedPathList( $extensions, $subdirectory = false )` | The paths of several extensions, each with `$subdirectory` appended. Extensions not found are skipped. |
| `extensionName( $name )` | The directory name as it exists on disk (case corrected). |
| `filterExtensionRootDirectories( $roots )` | Hook for code that extends `eZExtension`. The default returns the list unchanged. |

```php
echo eZExtension::extensionPath( 'ezfind' );
// extension/ezfind

echo eZExtension::extensionPath( 'acme_customer_extension' );
// extension_src/acme_customer_extension

$paths = eZExtension::expandedPathList(
    array( 'ezfind', 'acme_customer_extension' ),
    'design'
);
// array(
//     'extension/ezfind/design',
//     'extension_src/acme_customer_extension/design',
// )
```

Path and name lookups are cached in memory for the current request. `eZExtension::clearActiveExtensionsMemoryCache()`
clears the in-memory list of active extensions.

## Kernel code that uses the new helpers

Every kernel call site that used to hard-code `extension/` or build a path from `eZExtension::baseDirectory()` now
uses the helpers above.

| Area | File(s) | Mechanism |
|------|---------|-----------|
| Extension metadata | `lib/ezutils/classes/ezextension.php`, `kernel/private/classes/ezpextension.php` | `extensionRootDirectories()`, `extensionPath()` |
| Autoload generation | `kernel/private/classes/ezautoloadgenerator.php`, `bin/php/ezpgenerateautoloads.php` | `getExtensionRoots()`, `collectExtensionPackages()` |
| Design / templates | `kernel/common/eztemplatedesignresource.php`, `kernel/visual/templatecreate.php`, `bin/php/eztc.php` | `extensionPath()`, `expandedPathList()` |
| Modules | `lib/ezutils/classes/ezmodule.php` | `extensionPath()` |
| Settings / siteaccess | `kernel/settings/edit.php`, `kernel/classes/ezsiteaccess.php`, `runcronjobs.php`, `bin/php/ezwebincommon.php` | `extensionPath()` |
| Datatypes | `kernel/classes/ezdatatype.php`, `bin/php/ezimportdbafile.php` | `extensionPath()` |
| Content / upload / tree / edit | `kernel/classes/ezcontentupload.php`, `kernel/classes/ezcontentobjecttreenode.php`, `kernel/classes/ezcontentobjectedithandler.php` | `extensionPath()` |
| Workflow / notification | `kernel/classes/ezworkflowtype.php`, `kernel/classes/workflowtypes/event/ezpaymentgateway/ezpaymentgatewaytype.php`, `kernel/classes/notification/eznotificationeventtype.php`, `kernel/classes/notification/eznotificationeventfilter.php` | `extensionPath()` |
| Shop handlers | `kernel/classes/ezvatmanager.php`, `kernel/classes/ezshippingmanager.php`, `kernel/shop/classes/exchangeratehandlers/ezexchangeratesupdatehandler.php` | `extensionPath()` |
| i18n | `lib/ezi18n/classes/eztstranslator.php` | `expandedPathList()` |
| RSS | `kernel/classes/ezrssimport.php` | `extensionPath()` |
| Icons | `kernel/common/ezwordtoimageoperator.php` | `expandedPathList()` |
| SOAP | `soap.php` | `extensionPath()` |
| Package handling | `kernel/classes/packagehandlers/ezextension/ezextensionpackagehandler.php`, `kernel/classes/packagecreators/ezextension/ezextensionpackagecreator.php`, `kernel/classes/packagehandlers/ezinstallscript/ezinstallscriptpackagehandler.php`, `kernel/classes/packagehandlers/ezfile/ezfilepackagehandler.php` | `extensionRootDirectories()`, `extensionPath()`, `baseDirectory()` |
| Setup / upgrade | `kernel/setup/extensions.php`, `kernel/setup/systemupgrade.php` | `extensionRootDirectories()`, `extensionPath()` |
| Test toolkit | `tests/toolkit/ezptestrunner.php`, `tests/toolkit/ezpextensionhelper.php` | `extensionRootDirectories()`, `extensionPath()` |

## Security

The kernel takes the roots as written; it does not check them. Treat every root exactly like `extension/`:

- Only trusted deployment users may write to `settings/override/`, `config.php` and any INI file that declares a root.
- `autoloads/*.php` in every root is included while the autoloads are generated, so every root needs the same
  ownership and integrity rules as `extension/`.
- Prefer roots relative to the installation directory and inside it.

## Backward compatibility

- `ExtensionDirectory=extension` stays the default.
- With `AdditionalExtensionDirectories[]` empty, the kernel behaves exactly as before.
- Existing packages in `extension/` are not moved, renamed or migrated.
- Composer scripts keep calling `bin/php/ezpgenerateautoloads.php`; it simply scans more roots when they are set.
- INI, template, class override and autoload mechanisms are unchanged.

## Tests

`tests/tests/kernel/classes/eZExtensionAdditionalDirectoriesTest.php` checks that:

- `extensionRootDirectories()` merges `ExtensionDirectory` and `AdditionalExtensionDirectories[]`;
- `extensionPath()` finds an extension in the base root and in an additional root;
- a later root wins when the same name exists in several roots;
- an unknown name returns `false`;
- names match case-insensitively and keep their case on disk;
- active extensions are resolved from additional roots.

## Related pages

- [Additional extension directories (feature)](../../features/6.0/additional-extension-directories.md)
- [Specification: extension metadata](../../specifications/6.0/extension-metadata.md)
- [Specification: INI override directories and placements](../../specifications/6.0/ini-override-placements.md)
- [Extensions: behaviour changes](extensions-behaviour-changes.md)
- [Extensions guide](../../guides/extensions.md)
