# PHP 8.0 support

Exponential 6 runs on PHP 8.0 and later. PHP 8.0 is the oldest supported version because it is the stock PHP of
Red Hat Enterprise Linux 9 (and of AlmaLinux 9 and Rocky Linux 9). Read this page if you run Exponential on RHEL 9
without a newer PHP, if you write kernel or extension code, or if you wonder why a function newer than 8.0 is
wrapped in `function_exists()`.

## In short

| | |
|---|---|
| Oldest PHP | 8.0 for the kernel and the shipped extensions, under Apache with mod_php or PHP-FPM. |
| Exception | Exponential Velocity, the optional application server, needs PHP 8.1 or later (see below). |
| What changed in 6.0.15 | The few places that needed PHP 8.1 or later were made to work on 8.0; CI checks 8.0 on every push. |
| Behaviour on 8.1 and later | Unchanged. Every fallback is used only where PHP lacks the function. |
| Composer | `composer.json` still declares `^8.1` until the packages listed under "Installing with Composer" allow 8.0. |

## What was found and how it was fixed

The whole tree was checked with PHPCompatibility for PHP 8.0 (`testVersion` 8.0), every PHP file was linted with
PHP 8.0.30, every class of the kernel and of 63 extensions was loaded with PHP 8.0.30, and a complete installation
was served by PHP 8.0.30: the front page, 35 public pages, the administration interface logged in (dashboard,
content views, setup, notification and e-mail preference pages, the layout editor) and the editor siteaccess, with
no PHP error, warning or deprecation. The findings:

| Where | What | PHP | Fix |
|---|---|---|---|
| `kernel/classes/expvelocityconfiglayout.php` | `array_is_list()` | 8.1 | defined in `lib/phpcompat.php` when missing |
| `kernel/classes/subitems/expsubitemscsvexport.php` | `fputcsv()` with `$eol` (an `ArgumentCountError` on 8.0) | 8.1 | on 8.0 the line is written without `$eol` and its `"\n"` replaced by `"\r\n"` |
| `lib/eztemplate/classes/eztemplatestringoperator.php` (`wash`), `kernel/common/ezsimpletagsoperator.php` and 26 other kernel, library, ezjscore, ezoe and expservices files | `htmlspecialchars()`, `htmlentities()`, `html_entity_decode()` and `htmlspecialchars_decode()` without `$flags` | 8.1 changed the default | the PHP 8.1 default `ENT_QUOTES \| ENT_SUBSTITUTE \| ENT_HTML401` is passed explicitly, so single quotes are escaped on 8.0 too |
| `kernel/classes/contentjob/*`, `kernel/classes/ini/expinieditor.php` | `fsync()` | 8.1 | already behind `function_exists()`; on 8.0 the data is flushed (`fflush()`) and left to the operating system |
| `kernel/classes/contentjob/expcontentjobworker.php` | `memory_reset_peak_usage()` | 8.2 | already behind `function_exists()` |
| `lib/eztemplate/classes/eztemplatestringsoperator.php` | `str_increment()`, `str_decrement()` | 8.3 | already behind `function_exists()`; the operators return their input on older PHP |
| `lib/ezdb/classes/ezdbquerycache.php` | `hash( 'xxh128' )` | 8.1 | already checks `hash_algos()`; md5 on 8.0 |

The `htmlspecialchars()` change is the one that matters for security: PHP 8.0's default leaves `'` alone, so a
`{$value|wash}` inside a single-quoted HTML attribute would not have been safe on 8.0. Passing the flags gives
every PHP version the same output, the one 8.1 and later already produced. If you have compiled templates from
before the change, clear the template cache once (`php bin/php/ezcache.php --clear-id=template`).

Found on the way, and broken on every PHP 8, not only on 8.0 (fixed in the same change or in the extension's
repository):

| Where | What | Fix |
|---|---|---|
| `kernel/private/modules/oauth/authorize.php` | `implode( $pieces, $glue )` (a `TypeError` since 8.0) | arguments swapped |
| `kernel/classes/datatypes/ezuser/ezldapuser.php` | `crypt()` without a salt (an `ArgumentCountError` since 8.0) | a random password from `random_bytes()` |
| syndication: `eZSyndicateType::onPublish()` | by-reference parameters against the by-value parent (a fatal error when the class loads) | `&` removed |
| ezstarrating: `ezsrRatingObjectTreeNode::makeObjectsArray()` | lacks the parameters the kernel's method has (a fatal error when the class loads) | parameters added |
| ezstarrating `ezsrratingobject.php`, swark `SwarkJSONEncodeOperator`, `SwarkVariableNamesOperator` | `implode( $pieces, $glue )` | arguments swapped |
| birthday `ezbirthday.php` | `each()`, removed in 8.0 | `key()` and `current()` |
| enhancedezbinaryfile | `split()`, removed in 7.0 | `explode()` |

Not changed, because they behave the same on 8.0 as on 8.5: optional parameters before required ones (a
deprecation since 8.0 in a few old signatures), the mysqli error mode (8.0 does not throw by default, and the driver
checks return values, which is what it expects), `never` as the return type of closures in xrowextract (8.0 reads it
as a class name; the closures always exit, so it is never checked).

## How the compatibility functions work

`lib/phpcompat.php` defines a function only when PHP does not have it, with the same result as PHP's own:

```php
if ( !function_exists( 'array_is_list' ) )
{
    function array_is_list( array $array ) { ... }
}
```

`autoload.php` reads it before anything else, and only on PHP older than 8.1 (`PHP_VERSION_ID < 80100`), so on 8.1
and later the file is not even opened and the built-in functions run. Every entry point (index.php, the CLI scripts,
cronjobs, the tests) starts with `autoload.php`, so they all have the functions.

Rules for new code:

- A function newer than PHP 8.0 that can be written in PHP with the same result goes into `lib/phpcompat.php`.
- One that cannot (`fsync()`, `memory_reset_peak_usage()`, `imageavif()` ...) is called behind
  `function_exists()`, with a fallback that is correct on 8.0.
- No syntax newer than 8.0: enums, `readonly`, `never`, `new` in initializers, first-class callable syntax
  `foo(...)`, intersection types, typed class constants. `#[\Override]` and other attributes are harmless.
- An argument a function gained later (`fputcsv()`'s `$eol` in 8.1) is a fatal error on 8.0: branch on
  `PHP_VERSION_ID`.
- `phpcs.xml.dist` runs PHPCompatibility with `testVersion` `8.0-`, and the Quality workflow's job `php80` lints
  every file and boots the kernel on PHP 8.0 ([quality checks](../../features/6.0/quality-checks.md)).

## Exponential Velocity needs PHP 8.1

Velocity (`vendor/se7enxweb/exponential-velocity`) declares `"php": ">=8.1"` and documents 8.1 as its minimum. Its
code was checked too (PHPCompatibility, `php -l` with 8.0): it uses 8.1 and later functions only behind
`function_exists()`/`class_exists()`, but it is developed and tested on 8.1 and later, and was not changed. So:

- Apache or PHP-FPM serving the site: PHP 8.0 or later.
- Velocity: PHP 8.1 or later. On RHEL 9, install a newer PHP next to the stock one for it (see below), or leave
  Velocity off; the site runs without it.

## Red Hat Enterprise Linux 9

- **Stock PHP 8.0**: `dnf install php php-fpm php-mysqlnd php-gd php-intl php-mbstring php-xml php-pdo php-opcache`
  installs PHP 8.0 from AppStream. That is enough for Exponential under Apache or PHP-FPM.
- **Newer PHP from AppStream modules**: RHEL 9 also offers the module streams `php:8.1`, `php:8.2` and `php:8.3`
  (`dnf module list php`; switch with `dnf module reset php` and `dnf module enable php:8.3`, then
  `dnf distro-sync`). Each of them runs Exponential and Velocity.
- **Remi or Plesk**: the Remi repository (`php:remi-8.x` module streams) and Plesk's `/opt/plesk/php/8.x` builds
  provide every version from 8.0 to 8.5 side by side.
- Extensions the stock packages leave out (`php-pecl-redis`, `php-pecl-apcu`, `oci8`) come from EPEL, Remi or the
  vendor; Exponential uses them only when their features are configured.

## Installing with Composer on PHP 8.0

The code runs on 8.0, but the Composer metadata of some packages does not allow it yet, so a plain
`composer install` on PHP 8.0 refuses today. Until the packages below are released with `^8.0` in their `"php"`
requirement, install with `--ignore-platform-req=php` (the CI job `php80` does the same):

```sh
composer install --no-dev --ignore-platform-req=php
```

| Package | Declares | Blocks 8.0 |
|---|---|---|
| `se7enxweb/exponential-velocity` | `>=8.1` | yes, and stays so: Velocity needs 8.1 (see above) |
| `se7enxweb/exponential-legacy-installer` 2.2.3 | `^7.4 \|\| ^8.1 \|\| ^8.2` | yes, metadata only |
| explayouts, explayouts-api, explayouts-content-browser(-core, -ui), explayouts-core, explayouts-relation-list-query, explayouts-site-api, explayouts-standard, explayouts-tags-query, explayouts-ui, explayouts-ui-api, expquery-translator, expsite-api, expsite-app, expsite-core, expsite-data-media, expsite-installer, sevenx-themes-media, exp_enhanced_link, expchangeclass | `^8.1 \|\| ^8.2 \|\| ^8.3 \|\| ^8.4` | yes, metadata only |
| `google/recaptcha` (via recaptcha) | 2.1.0 needs `>=8.4` | no: the requirement is `*`, so Composer picks 1.3.x on 8.0 |

Every other production package (the Zeta Components, `symfony/polyfill-php73` and the other se7enxweb
extensions) already installs on 8.0. `composer.json` keeps `"php": "^8.1 || ..."` until the list above is
empty apart from Velocity, and Velocity is either made optional (`suggest`) or keeps the minimum at 8.1 for
Composer installs.
