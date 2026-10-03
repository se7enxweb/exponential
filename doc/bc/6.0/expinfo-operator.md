# expInfo / expinfo operator

## Added: expInfo PHP API and expinfo template operator

This release adds a new read-only utility class and matching template operator for querying Exponential installation and extension metadata.

### PHP API

`lib/ezutils/classes/expinfo.php` now contains `expInfo` with the following static methods:

- `expInfo::activeExtensions()`  
  Returns metadata for all active extensions, keyed by extension directory name.

- `expInfo::availableExtensions()`  
  Returns metadata for every extension found on disk, with an `active` boolean.

- `expInfo::extensionInfo( $name, $activeOnly = false )`  
  Returns metadata for a single extension, or `false` if not found (or, with `$activeOnly`, not active).

- `expInfo::hasActiveExtension( $name )` and `expInfo::activeNames()`  
  A boolean test and the list of active extension names.

- `expInfo::kernelInfo( $section = false )`  
  Returns a large, read-only array describing the installation kernel. With a section name (a key of the table below) only that section is returned. The result is built once per request and cached in memory.

### Template operator

`lib/eztemplate/classes/eztemplateexpinfooperator.php` registers the new `expinfo` operator:

```tpl
{expinfo()}                     {* active extensions array *}
{expinfo('all')}                {* all available extensions array *}
{expinfo('bcwebshop')}          {* single extension info array *}
{expinfo('kernel')}             {* the whole kernel info array; a single section is a PHP call, see above *}
```

An extension's metadata array holds the keys `Name`, `Version`, `Copyright`, `License`, `info_url`, `description`, `author`, `summary`, `meta`, `mtime` and `active` (and more; the exact list is whatever the extension's `ezinfo.php` or `extension.xml` provides, normalised).

### Check it yourself

Put `{expinfo( 'no_such_ext' )|attribute( show )}` in a scratch template, or call the class from a script run with `php bin/php/ezexec.php <script> --allow-root-user`. On this installation `expInfo::kernelInfo()` returns exactly the thirteen sections listed below, and `expInfo::extensionInfo( 'no_such_ext' )` returns `false`.

### Kernel info sections

`expinfo('kernel')` returns the following top-level sections:

| Section      | Contents                                                       |
|--------------|----------------------------------------------------------------|
| `version`    | `ExponentialSDK` version, state, alias, edition (`lib/version.php`) |
| `php`        | PHP version, SAPI, loaded extensions, INI limits               |
| `memory`     | Current and peak memory usage                                  |
| `server`     | Hostname, kernel/var/cache/www paths, server software          |
| `database`   | DB type/version/name/server/user (password redacted)          |
| `ini`        | Site name, siteaccess, designs, languages, DB settings         |
| `user`       | Current user id/login if available                             |
| `extensions` | Active and available extension counts and name lists           |
| `cache`      | Cache directory size and compiled template count               |
| `filesystem` | Root/var/extension directory sizes and disk free/total         |
| `timestamps` | mtime of `lib/version.php`, `index.php`, `composer.lock`, etc. |
| `git`        | Branch, last commit hash/date, dirty file count                |
| `composer`   | Installed composer package count and versions                  |

### Security

- Database and INI passwords are always returned as `***`.
- No secrets, private keys, or credentials are exposed.

### Additional changes

- The Setup > Extensions view (entry point `kernel/setup/extensions.php`, code in `kernel/private/classes/views/setup/extensions.php`) collects extension metadata with `expInfo::availableExtensions()` instead of an inline helper.

## Build notes

- Regenerate autoloads after deployment: `php bin/php/ezpgenerateautoloads.php -e`
- Clear caches after class/INI changes: `php bin/php/ezcache.php --clear-all --allow-root-user`
- Reload the PHP-FPM that serves the site so the web runtime picks up the new class.

## See also

- [Extension metadata specification](../../specifications/6.0/extension-metadata.md)
- [Extension list and downloads](../../features/6.0/extension-list-and-downloads.md)
- [September 2026, first half](../../history/2026/2026-09a.md#1-to-2-september-knowing-what-is-installed)
- [Changelog 6.0.15](../../changelogs/6.0/6.0.15.md)
