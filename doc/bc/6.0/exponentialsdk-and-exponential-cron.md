# ExponentialSDK and exponential.cron

Two names that carried the old product name were changed in 6.0.15. Both old
names keep working; nothing in an existing installation, extension or crontab
has to change.

---

## The version class: `ExponentialSDK`

`lib/version.php` declares `ExponentialSDK`, the class that reports the
release (`VERSION_MAJOR`, `VERSION_MINOR`, `VERSION_RELEASE`, `VERSION_STATE`,
`VERSION_DEVELOPMENT`, `VERSION_ALIAS`, `EDITION`, and the static methods
`version()`, `majorVersion()`, `minorVersion()`, `release()`, `state()`,
`developmentVersion()`, `alias()`, `databaseVersion()`, `databaseRelease()`).
The kernel calls it by that name.

`eZPublishSDK`, the former name, is an empty subclass in `lib/ezpublishsdk.php`:

```php
class eZPublishSDK extends ExponentialSDK
{
}
```

Every constant and static method is inherited, so these keep returning exactly
what they returned before:

| Old code | Still works |
|---|---|
| `eZPublishSDK::version()`, `::majorVersion()`, ... | yes, same values |
| `eZPublishSDK::VERSION_MAJOR`, `::EDITION`, ... | yes, same values |
| `class_exists( 'eZPublishSDK' )` | yes, through the autoloader |
| `require 'lib/version.php'` without the autoloader | yes, the file loads `lib/ezpublishsdk.php` itself, so both names are declared |
| `new ReflectionClass( 'eZPublishSDK' )`, `getConstant()` | yes, constants are inherited |
| `is_subclass_of( 'eZPublishSDK', 'ExponentialSDK' )` | true |

The one difference: an object created as `new ExponentialSDK()` is not
`instanceof eZPublishSDK`. The class is only ever used statically, so no code
is known to depend on that.

`eZPublishSDK` is deprecated; new code should use `ExponentialSDK`. It will not
be removed within the 6.x line.

The database rows `ezpublish-version` and `ezpublish-release` in `ezsite_data`
keep their names: they are stored data read by upgrade scripts.

### After updating a running server

Exponential Velocity workers keep the class map from their warm-up. Until
Velocity is restarted they do not know `ExponentialSDK`, and a page that
reaches updated code calling it (setup views, the RSS feed, PDF export, the
about page, the XML text editor) fails. Restart Velocity after deploying this
change. PHP-FPM needs only its usual reload.

---

## The example crontab: `exponential.cron`

The example crontab in the installation root is `exponential.cron`. Copy it,
set `EXPONENTIALROOT` and `PHP`, and install it:

```bash
crontab exponential.cron
```

`ezpublish.cron` is kept, marked deprecated, with the same entries, so
instructions and deployment scripts that install the old file name keep
working. It also keeps its former variable `EZPUBLISHROOT` and placeholder
`/path/to/the/ez/publish/directory`, so a script that fills those in by name
still finds them. A crontab cannot include another file, which is why it is a
copy and not a pointer; keep its entries in step with `exponential.cron`.

A crontab installed earlier from `ezpublish.cron` is not affected: `crontab`
copies the file's contents, so the installed entries run
`runcronjobs.php` directly and never read the file again.
