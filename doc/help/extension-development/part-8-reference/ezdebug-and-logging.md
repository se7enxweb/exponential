---
title: eZDebug and logging
audience: developer
category: extension-development
order: 30
summary: Write to the debug output and log files from an extension - writeNotice, writeWarning, writeError, writeDebug, timing and accumulators, eZLog::write - and the debug settings.
keywords: eZDebug, writeNotice, writeWarning, writeError, writeDebug, accumulatorStart, accumulatorStop, addTimingPoint, eZLog, log files, var/log, DebugSettings, AlwaysLog
type: help
---
# eZDebug and logging

Part 8, reference 3. `eZDebug` (`lib/ezutils/classes/ezdebug.php`) collects messages for the debug output and writes
them to log files; `eZLog` (`lib/ezfile/classes/ezlog.php`) writes lines to a log file of your choice. How to read the
output is in [Debug output and logs for developers](../part-1-from-user-to-developer/debug-output-and-logs-for-developers.md)
and [Debugging extensions](../part-3-extension-developer/debugging-extensions.md).

## Messages

All are static and take the message, a label (usually `__METHOD__`) and an optional CSS class for the debug output:

| Method | Level | Log file (under `var/log/`, or the site's log directory) |
|---|---|---|
| `eZDebug::writeNotice( $string, $label = "", $backgroundClass = "" )` | notice | `notice.log` |
| `eZDebug::writeWarning( $string, $label = "", $backgroundClass = "" )` | warning | `warning.log` |
| `eZDebug::writeError( $string, $label = "", $backgroundClass = "" )` | error | `error.log` |
| `eZDebug::writeDebug( $string, $label = "", $backgroundClass = "" )` | debug | `debug.log` |
| `eZDebug::writeStrict( $string, $label = "", $backgroundClass = "" )` | strict | `strict.log` |

`$string` may be an array or object; it is printed readably.

```php
<?php
eZDebug::writeNotice( 'Imported 12 items', __METHOD__ );
eZDebug::writeWarning( "Setting [ListSettings] Limit missing, using 10", __METHOD__ );
eZDebug::writeError( "Item $id not found", __METHOD__ );
```

Which levels end up in the log files is set in `site.ini [DebugSettings]`: `AlwaysLog[]` lists the levels written even
when the debug output is off (by default `error`).

## Timing

```php
<?php
eZDebug::accumulatorStart( 'myext_import', 'myext', 'Import' );
// ... the work ...
eZDebug::accumulatorStop( 'myext_import' );

eZDebug::addTimingPoint( 'myext: before rendering' );
```

| Method | Shows |
|---|---|
| `accumulatorStart( $key, $inGroup = false, $name = false, $recursive = false )` and `accumulatorStop( $key, $recursive = false )` | the total time spent between start and stop, summed over calls, in the debug output's time accumulators |
| `addTimingPoint( $description = "" )` | a point in the timing table with time and memory |

## Is the debug output on?

`eZDebug::isDebugEnabled()` returns whether debug output is collected for this request. Use it to skip expensive
preparation of debug messages.

## Your own log file

```php
<?php
eZLog::write( "Order $orderID sent to the warehouse", 'myext.log' );
```

`eZLog::write( $message, $logName = 'common.log', $dir = 'var/log' )` appends a dated line. Pass the site's log
directory if your installation uses one (`eZSys::varDirectory() . '/log'`). Never write passwords, tokens or personal
data into logs.

## The settings

`site.ini [DebugSettings]`:

| Setting | Effect |
|---|---|
| `DebugOutput` | `enabled` shows the debug output |
| `DebugByIP`, `DebugIPList[]` | show it only to listed addresses |
| `DebugByUser`, `DebugUserIDList[]` | show it only to the listed user IDs |
| `AlwaysLog[]` | levels written to log files whatever the above say |

Turn the debug output on only on development copies or for your own address.

## Related pages

- Previous: [eZINI: reading settings](ezini.md)
- Next: [eZDB: the database layer](ezdb.md)
- [Debug bar](../../../bc/6.0/debug-bar.md)
- [When something goes wrong](../../when-something-goes-wrong.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
