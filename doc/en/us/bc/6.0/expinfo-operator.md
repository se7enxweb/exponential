# expInfo class and expinfo operator

Read this page if you want to read installation or extension metadata from PHP or from a template, or if you
maintain an override of the Setup > Extensions view. 6.0.15 adds `expInfo`, a read-only PHP class, and the
matching `expinfo` template operator. Nothing existing changes behaviour; the Setup > Extensions view now uses the
new class internally.

## In short

| | |
|---|---|
| What changed | New class `expInfo` (`lib/ezutils/classes/expinfo.php`) and template operator `expinfo`. |
| Who is affected | Template and extension authors who want the data; overrides of Setup > Extensions. |
| How to check | Put `{expinfo( 'no_such_ext' )\|attribute( show )}` in a scratch template; it shows `false`. |
| How to fix | Nothing to fix. Regenerate autoloads, clear caches and reload PHP-FPM after deploying (see "Deploy"). |

## PHP API

`lib/ezutils/classes/expinfo.php` declares `expInfo` with these static methods:

| Method | Returns |
|---|---|
| `expInfo::activeExtensions()` | Metadata of all active extensions, keyed by extension directory name. |
| `expInfo::availableExtensions()` | Metadata of every extension found on disk, each with an `active` boolean. |
| `expInfo::extensionInfo( $name, $activeOnly = false )` | Metadata of one extension, or `false` when it is not found (or, with `$activeOnly`, not active). |
| `expInfo::hasActiveExtension( $name )` | `true` or `false`. |
| `expInfo::activeNames()` | The list of active extension names. |
| `expInfo::kernelInfo( $section = false )` | A large read-only array describing the installation. With a section name (see the table below) only that section. Built once per request and kept in memory. |

An extension's metadata array holds the keys `Name`, `Version`, `Copyright`, `License`, `info_url`, `description`,
`author`, `summary`, `meta`, `mtime` and `active`, and more: the exact list is whatever the extension's `ezinfo.php`
or `extension.xml` provides, normalised.

## Template operator

`lib/eztemplate/classes/eztemplateexpinfooperator.php` registers `expinfo`:

```tpl
{expinfo()}                     {* active extensions array *}
{expinfo('all')}                {* all available extensions array *}
{expinfo('bcwebshop')}          {* single extension info array *}
{expinfo('kernel')}             {* the whole kernel info array; a single section is a PHP call, see above *}
```

## Kernel info sections

`expinfo('kernel')` and `expInfo::kernelInfo()` return these thirteen top-level sections:

| Section | Contents |
|---|---|
| `version` | `ExponentialSDK` version, state, alias, edition (`lib/version.php`) |
| `php` | PHP version, SAPI, loaded extensions, INI limits |
| `memory` | Current and peak memory usage |
| `server` | Host name, kernel/var/cache/www paths, server software |
| `database` | Database type, version, name, server, user (password redacted) |
| `ini` | Site name, siteaccess, designs, languages, database settings |
| `user` | Current user id and login, if available |
| `extensions` | Active and available extension counts and name lists |
| `cache` | Cache directory size and compiled template count |
| `filesystem` | Root, var and extension directory sizes; disk free and total |
| `timestamps` | mtime of `lib/version.php`, `index.php`, `composer.lock` and others |
| `git` | Branch, last commit hash and date, dirty file count |
| `composer` | Installed Composer package count and versions |

## Security

- Database and INI passwords are always returned as `***`.
- No secrets, private keys or credentials are exposed.

## Check it yourself

- In a template: put `{expinfo( 'no_such_ext' )|attribute( show )}` in a scratch template and view it. The result is
  `false`.
- From PHP: call the class from a script run with `php bin/php/ezexec.php <script> --allow-root-user`.
  `expInfo::kernelInfo()` returns exactly the thirteen sections above, and `expInfo::extensionInfo( 'no_such_ext' )`
  returns `false`.

## Setup > Extensions

The Setup > Extensions view (entry point `kernel/setup/extensions.php`, code in
`kernel/private/classes/views/setup/extensions.php`) now collects extension metadata with
`expInfo::availableExtensions()` instead of an inline helper. An override of that view can call the same method.

## Deploy

```bash
php bin/php/ezpgenerateautoloads.php -e
php bin/php/ezcache.php --clear-all --allow-root-user
```

Then reload the PHP-FPM that serves the site, so the web runtime picks up the new class.

## Related pages

- [Extension metadata specification](../../specifications/6.0/extension-metadata.md)
- [Extension list and downloads](../../features/6.0/extension-list-and-downloads.md)
- [RAD tools](../../features/6.0/rad-tools.md)
- [ExponentialSDK and exponential.cron](exponentialsdk-and-exponential-cron.md)
- [September 2026, first half](../../history/2026/2026-09a.md#1-to-2-september-knowing-what-is-installed)
- [Changelog 6.0.15](../../changelogs/6.0/6.0.15.md)
