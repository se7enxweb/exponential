---
title: Debugging and logs
audience: app-creator, developer, admin
category: app-creation
order: 520
summary: Find out what your application does - debug output and the debug bar, template path comments, eZDebug in code, the log files and Setup > Logs, error references, request logs, and debugging under Velocity.
keywords: debug, debugging, debug bar, DebugOutput, eZDebug, writeError, logs, error.log, warning.log, log viewer, error reference, request logs, template path comments, Velocity, app creator
type: help
---
# Debugging and logs

This page is for application creators who need to see inside a request. Exponential has a debug output and debug bar for
development, log files for everything, and an error reference that links what a user sees to the log line.

## Contents

1. [Debug output and the debug bar](#debug-output-and-the-debug-bar)
2. [Writing debug and log lines](#writing-debug-and-log-lines)
3. [The log files](#the-log-files)
4. [From an error page to the log line](#from-an-error-page-to-the-log-line)
5. [Debugging under Velocity](#debugging-under-velocity)

## Debug output and the debug bar

On a development machine:

```ini
# settings/override/site.ini.append.php (development only)
[DebugSettings]
DebugOutput=enabled

[TemplateSettings]
ShowTemplatePathComments=enabled
```

The debug bar shows queries, templates used, settings, timings and caches for the page
([Exp debug bar](../../../features/6.0/exp-debug-bar.md), [Debug bar](../../../bc/6.0/debug-bar.md)). Template path comments
show which file wrote which markup ([Template path comments](../../../features/6.0/template-path-comments.md)). Never
enable debug output on a production siteaccess; it can show internals.

## Writing debug and log lines

```php
eZDebug::writeNotice( 'Imported 12 items', __METHOD__ );
eZDebug::writeWarning( 'Item 42 had no title', __METHOD__ );
eZDebug::writeError( 'Feed could not be read: ' . $message, __METHOD__ );
eZLog::write( 'archive sent for object ' . (int)$objectID, 'expdemo.log' );
```

Warnings and errors go to `var/log/warning.log` and `var/log/error.log` whatever the debug setting; `eZLog::write()`
writes a log of your own in the log directory. Never log passwords, keys, tokens or personal data.

## The log files

| File | Holds |
|---|---|
| `var/log/error.log`, `warning.log` | errors and warnings |
| `var/log/csp.log` | Content Security Policy reports |
| `var/log/security.log` | security events |
| your own `*.log` | what your code writes |

**Setup > Logs** shows them with a digest of repeated messages ([Log viewer](../../../features/6.0/log-viewer.md));
logs rotate ([Log rotation](../../../features/6.0/log-rotation.md)). Request logs record requests with timings
([Request logs](../../../features/6.0/request-logs.md)).

## From an error page to the log line

Error pages carry a reference; search the logs for it to find the full message
([Finding an error by its reference](../../../guides/finding-an-error-by-its-reference.md)).

## Debugging under Velocity

- Workers keep classes and settings in memory: after a change, `exp:velocity deploy` (or `restart`), or you debug old
  code.
- The server's own logs are under `var/vc/qbix/run/` and the configured log directory
  ([8.3.12 Logs](../../../install/08-serving-the-site.md#8312-logs)).
- Compare with PHP's built-in server (`--engine=php`), which runs each request fresh, when you suspect state kept between
  requests ([Velocity persistent worker server](../../../features/6.0/velocity-persistent-worker-server.md)).

## Related pages

- [Testing applications](testing-applications.md)
- [Debug output improvements](../../../features/6.0/debug-output-improvements.md)
- [When something goes wrong](../../when-something-goes-wrong.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
