# ExponentialSDK and exponential.cron

Read this page if your code calls the version class `eZPublishSDK`, or if your deployment installs the example
crontab `ezpublish.cron`. Both names were changed in 6.0.15. The old names keep working: nothing in an existing
installation, extension or crontab has to change. The one thing to do is restart Velocity after deploying.

## In short

| | |
|---|---|
| What changed | The version class is `ExponentialSDK` (`eZPublishSDK` is an empty subclass). The example crontab is `exponential.cron` (`ezpublish.cron` is a kept copy). |
| Who is affected | Nobody needs to change code. Velocity users must restart after deploying. |
| How to check | `grep -n "class ExponentialSDK" lib/version.php` and `ls exponential.cron ezpublish.cron` |
| How to fix | Restart Velocity after deploying. Use the new names in new code and new crontabs. |

## The version class: `ExponentialSDK`

`lib/version.php` declares `ExponentialSDK`, the class that reports the release. The kernel calls it by that name.
It holds:

- the constants `VERSION_MAJOR`, `VERSION_MINOR`, `VERSION_RELEASE`, `VERSION_STATE`, `VERSION_DEVELOPMENT`,
  `VERSION_ALIAS` and `EDITION`;
- the static methods `version()`, `majorVersion()`, `minorVersion()`, `release()`, `state()`,
  `developmentVersion()`, `alias()`, `databaseVersion()` and `databaseRelease()`.

The former name, `eZPublishSDK`, is an empty subclass in `lib/ezpublishsdk.php`:

```php
class eZPublishSDK extends ExponentialSDK
{
}
```

Every constant and static method is inherited, so old code returns exactly what it returned before:

| Old code | Still works |
|---|---|
| `eZPublishSDK::version()`, `::majorVersion()`, ... | yes, same values |
| `eZPublishSDK::VERSION_MAJOR`, `::EDITION`, ... | yes, same values |
| `class_exists( 'eZPublishSDK' )` | yes, through the autoloader |
| `require 'lib/version.php'` without the autoloader | yes; the file loads `lib/ezpublishsdk.php` itself, so both names are declared |
| `new ReflectionClass( 'eZPublishSDK' )`, `getConstant()` | yes; constants are inherited |
| `is_subclass_of( 'eZPublishSDK', 'ExponentialSDK' )` | true |

The one difference: an object created as `new ExponentialSDK()` is not `instanceof eZPublishSDK`. The class is only
used statically, and no code is known to depend on that.

`eZPublishSDK` is deprecated; use `ExponentialSDK` in new code. It will not be removed within the 6.x line.

The database rows `ezpublish-version` and `ezpublish-release` in `ezsite_data` keep their names. They are stored data
that upgrade scripts read.

### After updating a running server

Exponential Velocity workers keep the class map from their warm-up. Until Velocity is restarted they do not know
`ExponentialSDK`, and any page that reaches updated code calling it fails: the setup views, the RSS feed, PDF
export, the about page and the XML text editor. **Restart Velocity after deploying this change.** PHP-FPM needs only
its usual reload.

## The example crontab: `exponential.cron`

The example crontab in the installation root is `exponential.cron`. Copy it, set `EXPONENTIALROOT` and `PHP`, and
install it:

```bash
crontab exponential.cron
```

`ezpublish.cron` is kept, marked deprecated, with the same entries, so instructions and deployment scripts that
install the old file name keep working. It also keeps its old variable `EZPUBLISHROOT` and the placeholder
`/path/to/the/ez/publish/directory`, so a script that fills those in by name still finds them. A crontab cannot
include another file, which is why it is a copy and not a pointer. If you maintain both, keep their entries in step.

A crontab installed earlier from `ezpublish.cron` is not affected. `crontab` copies the file's contents, so the
installed entries run `runcronjobs.php` directly and never read the file again.

## Related pages

- [Version information and the expinfo operator](expinfo-operator.md)
- [Console and cronjob parts](console.md)
- [Velocity engines: deploying a PHP change](velocity-engines.md#deploying-a-php-change-expvelocity-deploy)
- [Changelog 6.0.15](../../changelogs/6.0/6.0.15.md)
