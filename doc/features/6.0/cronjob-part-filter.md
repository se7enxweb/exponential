# Leaving out a cronjob part

Read this page if you want to stop cronjob parts for a while without changing the crontab (during a release, a data
migration or a search index rebuild), or if you build an extension that decides whether a part may run.

## In short

- Before `runcronjobs.php` runs the scripts of a part, it asks the `ezpEvent` filter `cronjob/part/run`.
- The listener gets `true` and returns `true` to run the part. Anything else leaves the part out: the run prints a
  notice, runs none of its scripts and ends with exit code 0.
- Register the listener in `site.ini [RunnableSettings] Listeners[]`. That list is attached to the command line;
  `[Event] Listeners[]` is attached to web requests only.
- Without listeners every part runs, as before.

## The filter

```php
$run = ezpEvent::getInstance()->filter( 'cronjob/part/run', true, $part, $siteaccess, $scripts, $single );
```

| Argument | Value |
|---|---|
| `$run` | `true`, or what the listener before this one returned |
| `$part` | the part as given on the command line (`frequent`, `infrequent`, ...); `''` for the scripts of `[CronjobSettings]` (no part given) and for a single script run with `--script=<file>` |
| `$siteaccess` | the siteaccess the scripts run in |
| `$scripts` | the file names of the scripts the run would start, as `cronjob.ini` lists them (one name with `--script`) |
| `$single` | `true` for a single script run with `--script`, `false` for a part and for the scripts of `[CronjobSettings]` |

A single script run with `--script` (also how **Setup > Cronjobs** starts one script) is asked with the part `''`:
it does not say which part the script belongs to. A listener that stops a part has to look at `$scripts` to stop its
scripts when they run alone, as the example below does.

**Only `true` runs the part.** `false`, `null`, `1` or `'yes'` leave it out, so a listener that forgets its
`return` stops every part: hand `$run` back when the listener has no opinion. Several listeners run in the order
they are registered, each getting what the one before returned.

A listener that cannot be called (its class is not autoloadable, for instance because the autoload arrays were not
regenerated) is skipped and logged, and the part runs. Try a stop switch once before relying on it.

The filter is asked after the scripts are chosen and before the first one starts, so a part left out takes no script
lock and writes no audit record. `--list` is not affected.

`./console cron:<part>` and the cronjob page of the admin (**Setup > Cronjobs**) start `runcronjobs.php`, so the
filter applies to them as well.

## Example: a stop file per part

In `extension/<name>/settings/site.ini.append.php`:

```ini
[RunnableSettings]
Listeners[]=cronjob/part/run@myExtCronjobStop::partRun
```

```php
class myExtCronjobStop
{
    /**
     * Leaves a run out while var/cronjob_stop/all.stop exists, a part while var/cronjob_stop/<part>.stop
     * exists, and a script run alone while var/cronjob_stop/<script>.stop exists (notification.php.stop).
     *
     * @param bool $run
     * @param string $part
     * @param string $siteaccess
     * @param string[] $scripts
     * @param bool $single
     * @return bool
     */
    public static function partRun( $run, $part, $siteaccess, $scripts, $single = false )
    {
        $dir = eZSys::varDirectory() . '/cronjob_stop';
        if ( file_exists( "$dir/all.stop" ) )
            return false;
        if ( $single )
            return file_exists( $dir . '/' . basename( $scripts[0] ) . '.stop' ) ? false : $run;
        $name = $part !== '' ? $part : 'default';
        return file_exists( "$dir/$name.stop" ) ? false : $run;
    }
}
```

A listener that keeps its decision somewhere other than a file (the database, a cache) works the same way; it is
called once per run of `runcronjobs.php`, after the siteaccess and the extension settings are loaded, so it may be
registered in an extension or a siteaccess.

## Tests

`CronjobPartRunFilterTest` (no database): without listeners every part runs; a listener gets the part, the
siteaccess, the scripts and whether a single script runs, and can leave the part out; only `true` runs it; a
listener in `[RunnableSettings] Listeners[]` is attached; `runcronjobs.php` asks the filter before the first script
and ends without error when the part is left out.

## Related pages

- [Commands, cronjob parts and module views as classes](../../bc/6.0/cli_cronjob_view_abstractions.md)
- [Access and view cache filters for extensions](access-and-cache-filters.md)
- [Extension points](../../bc/6.0/rad-extension-points.md)
