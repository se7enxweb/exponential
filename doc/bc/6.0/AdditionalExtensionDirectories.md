# Additional Extension Directories

- **Status:** Implemented, tested and stable
- **Author:** Felix Woldt (JAC Systeme GmbH) — original RFC
- **Target repository:** se7enxweb/exponential
- **Introduced in:** Exponential 6.0 (BC)
- **Affected components:** `eZExtension`, `ezpExtension`, legacy autoloader (`bin/php/ezpgenerateautoloads.php`, `kernel/private/classes/ezautoloadgenerator.php`), `autoload.php`, kernel settings/design/module resolution

## 1. Overview

Exponential 6.0 adds support for **additional extension repository roots**. The classic `extension/<name>/` layout remains the default; projects may declare any number of extra roots through `AdditionalExtensionDirectories[]` in `site.ini` or `settings/override/`. A package under any additional root is structurally and functionally a normal legacy extension.

The recommended directory layout keeps the two extension roots together:

```
ezroot/
    extension/          # third-party / community extensions
    extension_src/      # project- / customer-specific extensions
```

The acceptance criterion is the same as the original RFC:

```bash
cp -r extension/acme_customer_extension extension_src/acme_customer_extension
```

This must work without any file change, manifest rewrite or `composer dump-autoload`.

## 2. Configuration

### 2.1 INI setting

A new array setting is added to `[ExtensionSettings]`. It is **commented out by default** so existing installations are unaffected until a project explicitly enables it.

```ini
[ExtensionSettings]
# the classic single extension root (always active)
ExtensionDirectory=extension

# Optional additional roots, scanned in declared order.
# Later entries have higher priority.
#AdditionalExtensionDirectories[]
#AdditionalExtensionDirectories[]=extension_src
```

Because the list is an ordinary INI array, it is:

- **extensible** — add as many roots as the project needs (`sites/customer_a`, `sites/customer_b`, …)
- **extendible** — other extensions or `settings/override/` can append further roots without touching the kernel; PHP code can also add roots via `eZExtension::filterExtensionRootDirectories( $roots )`
- **customizable** — the directory names, order and count are project-specific, not hardcoded

### 2.2 Activation

Extensions are still activated through `[ExtensionSettings]/ActiveExtensions[]` and `[ExtensionSettings]/ActiveAccessExtensions[]`. The kernel resolves each listed extension name against the configured roots using the precedence rule documented below.

```ini
[ExtensionSettings]
ActiveExtensions[]
ActiveExtensions[]=site_app
ActiveExtensions[]=acme_customer_extension
```

## 3. Public API

### 3.1 `eZExtension::extensionRootDirectories()`

```php
public static function extensionRootDirectories() : array;
```

Returns the merged list of configured extension repository roots in priority order (low → high):

```php
[
    'extension',
    'extension_src',
]
```

The list is built from:

1. `ExtensionSettings/ExtensionDirectory` (default `extension`)
2. `ExtensionSettings/AdditionalExtensionDirectories[]` (if set)
3. filtered through `eZExtension::filterExtensionRootDirectories( $roots )`

Empty and duplicate entries are removed. Paths are returned as declared (relative to the project root by default).

### 3.2 `eZExtension::extensionPath( $name )`

```php
public static function extensionPath( $extensionName ) : string|false;
```

Returns the root-relative path of the named extension, taking precedence into account. The package name is matched case-insensitively; the returned path preserves the directory casing that exists on disk.

Example:

```php
echo eZExtension::extensionPath( 'ezfind' );
// extension/ezfind

echo eZExtension::extensionPath( 'acme_customer_extension' );
// extension_src/acme_customer_extension
```

If the extension is not found in any configured root, `false` is returned.

### 3.3 `eZExtension::expandedPathList( $extensions, $subdirectory = false )`

```php
public static function expandedPathList( $extensionList, $subdirectory = false ) : array;
```

Expands an array of extension names into absolute paths, optionally appending a subdirectory. Extensions not found in any configured root are skipped.

```php
$paths = eZExtension::expandedPathList(
    array( 'ezfind', 'acme_customer_extension' ),
    'design'
);
// array(
//     'extension/ezfind/design',
//     'extension_src/acme_customer_extension/design',
// )
```

### 3.4 `eZExtension::extensionName( $name )`

```php
public static function extensionName( $extensionName ) : string|false;
```

Looks for an extension directory matching `$extensionName` in any configured root and returns the actual directory name (case-corrected) as it exists on disk. Returns `false` if no matching directory is found.

### 3.5 `eZExtension::filterExtensionRootDirectories( $roots )`

```php
public static function filterExtensionRootDirectories( $roots ) : array;
```

Protected filter hook. By default it removes empty values and duplicates. Projects can extend `eZExtension` and override this method to add, remove or validate roots dynamically from PHP.

## 4. Precedence Rule

If the same folder/package name exists in two roots, the root later in `AdditionalExtensionDirectories[]` completely replaces the earlier one. There is **no** per-file merge at the subfolder level.

Example:

```ini
[ExtensionSettings]
ExtensionDirectory=extension
AdditionalExtensionDirectories[]
AdditionalExtensionDirectories[]=extension_src
```

With `extension/foobar/` and `extension_src/foobar/`, the kernel uses `extension_src/foobar/` exclusively for that siteaccess.

The autoload-array generator collects packages from every root and emits a warning for each collision:

```
Extension 'foobar' in extension_src/ overrides the extension of the same name in extension/
```

This avoids accidental shadowing and makes `composer update` of a vendor extension safe while a local `extension_src/` copy is active.

## 5. Extension Structure

A package under any additional root uses the exact same internal layout as a classic extension:

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

No new manifest, no PSR-4 requirement, no class-name convention change. `cp -r` between `extension/` and `extension_src/` just works.

### 5.1 Site extension pattern

A single additional-root package can encapsulate a complete site (design + grouped/un-grouped siteaccesses + code + templates):

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

Because the root is configurable, multiple full sites can live side-by-side and be activated independently.

## 6. Autoloading and Extension Scan

The legacy autoload array generator (`bin/php/ezpgenerateautoloads.php`) and `eZAutoloadGenerator` walk every configured root exactly as they already walk `extension/*`.

Consequences:

- `var/autoload/ezp_extension.php` contains class entries from all roots
- `var/autoload/ezp_override.php` contains kernel overrides from all roots
- `var/autoload/ezp_tests.php` contains test classes from all roots
- no `composer dump-autoload` is required when adding/moving/removing packages
- the Composer autoloader (`vendor/autoload.php`) is not touched
- class-based kernel overrides (`[ClassSettings]`, workflow handlers, datatypes, operators) keep working because `autoloads/*.php` in each root is collected exactly as for `extension/`

The generator is loaded with `eZExtension` support by `require_once 'autoload.php';` in `bin/php/ezpgenerateautoloads.php`.

## 7. Settings, Design and Kernel Overrides

Because a package under `extension_src/` has the same structure as one under `extension/`, the existing resolution paths continue to work:

- `settings/*.ini.append.php` from each active package is loaded
- `design/<siteaccess>/...` is resolved from each active package
- `settings/override/` inside an extension/package follows the same ext-siteaccess-override rules (including `__` grouping)
- `settings/override/` at the global level still sits on top of everything

Load order (low → high) stays consistent:

```
base package settings/
extension/package settings/siteaccess/<sa>/
extension/package settings/override__<group>/
extension/package settings/override/
global settings/override/
```

with the only change that packages may live in any configured root.

## 8. Updated Consumers

All core call sites that previously hard-coded `extension/` or called `eZExtension::baseDirectory()` to build a single extension path now use the new helpers. The main consumers updated are:

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

## 9. Caching

`eZExtension` caches the discovered root list and the per-extension path/name lookups. These caches are keyed with the current root list so that a change in `AdditionalExtensionDirectories[]` invalidates them automatically. Memory caches can be cleared with `eZExtension::clearActiveExtensionsMemoryCache()`.

## 10. Security Considerations

- Paths in `AdditionalExtensionDirectories[]` are validated before use. They must be relative to the project root, must not contain `..`, and must resolve to a directory that is inside the project root.
- Absolute paths are rejected unless explicitly allow-listed in `config.php` (`EZP_ALLOWED_EXTENSION_ROOTS`).
- The autoload array generator does not follow symlinks that escape the configured root.
- Files under `settings/override/`, `config.php` and any INI file that declares additional roots must be writable only by trusted deployment users.
- Because `autoloads/*.php` in an additional root is `include`d during autoload generation, the same ownership and integrity rules that apply to `extension/` must apply to every configured root.

## 11. Backward Compatibility

- `ExtensionDirectory=extension` is the default and remains untouched
- `AdditionalExtensionDirectories[]` is commented out by default; if absent or empty the kernel behaves exactly as before
- Existing `extension/` packages are not moved, renamed or migrated
- Composer scripts keep calling `bin/php/ezpgenerateautoloads.php`; the generator simply scans more roots when configured
- All existing INI, template, class-override and autoload mechanisms keep working unchanged
- Active-extension caches are invalidated when the root list changes

## 12. Suggested INI Configuration

All example `AdditionalExtensionDirectories[]` lines are commented out so they are documentation only; projects must explicitly uncomment them to enable the feature.

### 12.1 Minimal

```ini
[ExtensionSettings]
# Default root. Existing installations keep this unchanged.
ExtensionDirectory=extension

# Optional second root for project-/customer-specific extensions.
# Uncomment to enable.
#AdditionalExtensionDirectories[]
#AdditionalExtensionDirectories[]=extension_src
```

### 12.2 Sibling root name (`extension_src/`)

```ini
[ExtensionSettings]
ExtensionDirectory=extension
#AdditionalExtensionDirectories[]
#AdditionalExtensionDirectories[]=extension_src
```

### 12.3 Multi-tenant / multi-project

```ini
[ExtensionSettings]
ExtensionDirectory=extension
#AdditionalExtensionDirectories[]
#AdditionalExtensionDirectories[]=sites/customer_a
#AdditionalExtensionDirectories[]=sites/customer_b
```

### 12.4 Copy-paste alternative root examples (all commented out)

Uncomment the block that matches the layout you want and place the active lines under `[ExtensionSettings]`.

```ini
# Block #1: recommended sibling root (extension/ + extension_src/)
# Keeps the two extension roots together under ezroot/.
[ExtensionSettings]
ExtensionDirectory=extension
#AdditionalExtensionDirectories[]
#AdditionalExtensionDirectories[]=extension_src
```

```ini
# Block #2: use an "extensions/" directory instead of extension_src/
# Useful if your project already follows an extensions/ naming convention.
[ExtensionSettings]
ExtensionDirectory=extension
#AdditionalExtensionDirectories[]
#AdditionalExtensionDirectories[]=extensions
```

```ini
# Block #3: older /src style
# Use this if you prefer the original RFC top-level src/ layout.
[ExtensionSettings]
ExtensionDirectory=extension
#AdditionalExtensionDirectories[]
#AdditionalExtensionDirectories[]=src
```

## 13. Migration Path

1. **Update the kernel** — already done in Exponential 6.0.
2. **Create the new root:** `mkdir extension_src`.
3. **Enable the setting** by uncommenting `AdditionalExtensionDirectories[]=extension_src` in `settings/override/site.ini.append.php` or `site.ini`.
4. **Copy a vendor/community extension:** `cp -r extension/acme_customer_extension extension_src/acme_customer_extension`.
5. **Customize** inside `extension_src/`.
6. **Regenerate legacy autoloads:** `php bin/php/ezpgenerateautoloads.php` (or `composer run legacy-scripts`).
7. **No `composer dump-autoload` required.**

The switch is optional and incremental; projects can run `extension/` and `extension_src/` in parallel indefinitely.

## 14. Testing

PHPUnit coverage is provided by `tests/tests/kernel/classes/eZExtensionAdditionalDirectoriesTest.php`. The test verifies:

- `extensionRootDirectories()` merges `ExtensionDirectory` and `AdditionalExtensionDirectories[]`
- `extensionPath()` returns the correct root for an extension in the base and in an additional root
- Later roots win when the same package name exists in multiple roots
- Unknown extension names return `false`
- Case-insensitive name lookup with case-preserving return values
- Active-extension resolution picks the correct package from additional roots

Syntax checks and a direct PHP smoke test against the new API pass. The legacy autoload generator dry-run (`php bin/php/ezpgenerateautoloads.php -e -n`) executes successfully and scans the configured roots.
