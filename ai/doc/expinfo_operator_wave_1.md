# expInfo / expinfo operator — Wave 1

## Story

We need a stable, read-only way to inspect eZ Publish installation metadata from both PHP and templates. This wave introduces `expInfo`, a new utility class, and `expinfo`, a template operator that exposes it.

## What was added

- `lib/ezutils/classes/expinfo.php`
  - `expInfo` PHP API class.
  - `activeExtensions()` — metadata for active extensions.
  - `availableExtensions()` — metadata for every extension on disk.
  - `extensionInfo( $name )` — metadata for a single extension.
  - `kernelInfo( $section )` — massive installation/kernel info array.
- `kernel/setup/extensions.php`
  - Now delegates extension metadata collection to `expInfo::availableExtensions()`.
- `lib/eztemplate/classes/eztemplateexpinfooperator.php`
  - `expinfo` template operator.
- `lib/eztemplate/classes/eztemplateautoload.php`
  - Operator registration.

## Template usage

```tpl
{def $active=expinfo()}
{def $all=expinfo('all')}
{def $one=expinfo('bcwebshop')}
{def $kernel=expinfo('kernel')}

Version: {$kernel.version.full}
PHP:     {$kernel.php.version}
DB:      {$kernel.database.version}
Active:  {$kernel.extensions.active_count}
Git:     {$kernel.git.branch} / {$kernel.git.last_commit}
```

## PHP usage

```php
$active = expInfo::activeExtensions();
$all    = expInfo::availableExtensions();
$one    = expInfo::extensionInfo( 'bcwebshop' );
$kernel = expInfo::kernelInfo();
$v      = expInfo::kernelInfo( 'version' );
```

## Build / test commands used

```bash
php -l lib/ezutils/classes/expinfo.php
php -l lib/eztemplate/classes/eztemplateexpinfooperator.php
php bin/php/ezpgenerateautoloads.php -e
php bin/php/ezexec.php --allow-root-user --siteaccess=admin ai/bin/one/test_expinfo.php
php bin/php/ezexec.php --allow-root-user --siteaccess=admin ai/bin/one/test_expinfo_kernel.php
php bin/php/ezexec.php --allow-root-user --siteaccess=admin ai/bin/one/test_expinfo_tpl.php
```

## Verification

- `expinfo()` returns 36 active extensions.
- `expinfo('all')` returns 71 available extensions.
- `expinfo('bcwebshop').name` returns `BC Web Shop`.
- `expinfo('kernel').version.full` returns `6.0.15stable`.
- `expinfo('kernel').database.version` returns `10.5.29-MariaDB`.
- `expinfo('kernel').git.branch` returns `main`.

## Security notes

- Database and INI passwords are redacted to `***` in `kernelInfo` output.
- `expInfo` only reads metadata; it does not modify extensions or settings.

## Files changed

- `lib/ezutils/classes/expinfo.php`
- `lib/eztemplate/classes/eztemplateexpinfooperator.php`
- `lib/eztemplate/classes/eztemplateautoload.php`
- `kernel/setup/extensions.php`
- `autoload/ezp_kernel.php` (regenerated)

## Temporary test artifacts

- `ai/bin/one/test_expinfo.php`
- `ai/bin/one/test_expinfo_kernel.php`
