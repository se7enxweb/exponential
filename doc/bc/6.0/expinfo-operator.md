# expInfo / expinfo operator

## Added: expInfo PHP API and expinfo template operator

This release adds a new read-only utility class and matching template operator for querying eZ Publish installation and extension metadata.

### PHP API

`lib/ezutils/classes/expinfo.php` now contains `expInfo` with the following static methods:

- `expInfo::activeExtensions()`  
  Returns metadata for all active extensions, keyed by extension directory name.

- `expInfo::availableExtensions()`  
  Returns metadata for every extension found on disk, with an `active` boolean.

- `expInfo::extensionInfo( $name )`  
  Returns metadata for a single extension, or `false` if not found.

- `expInfo::kernelInfo( $section )`  
  Returns a massive, read-only array describing the installation kernel.

### Template operator

`lib/eztemplate/classes/eztemplateexpinfooperator.php` registers the new `expinfo` operator:

```tpl
{expinfo()}                     {* active extensions array *}
{expinfo('all')}                {* all available extensions array *}
{expinfo('bcwebshop')}          {* single extension info array *}
{expinfo('kernel')}             {* massive kernel info array *}
```

### Kernel info sections

`expinfo('kernel')` returns the following top-level sections:

| Section      | Contents                                                       |
|--------------|----------------------------------------------------------------|
| `version`    | eZ Publish SDK version from `lib/version.php`                  |
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

- `kernel/setup/extensions.php` now delegates extension metadata collection to `expInfo::availableExtensions()`, removing the inline `fetchExtensionDetails()` helper.

## Build notes

- Regenerate autoloads after deployment: `php bin/php/ezpgenerateautoloads.php -e`
- Clear caches after class/INI changes: `php bin/php/ezcache.php --clear-all --allow-root-user`
- Restart PHP-FPM so the web runtime picks up the new class.
