---
title: Debugging, logging and errors
audience: web-framework-developer, developer
category: web-frameworks
order: 370
summary: The Exponential counterparts of the Symfony profiler, Laravel Telescope and Debugbar, Django Debug Toolbar and framework loggers - debug output and the Exp debug bar, template path comments, eZDebug and eZLog, the log files and the log viewer, error references, the audit trail, and debugging under persistent workers.
keywords: debugging, debug bar, profiler, DebugOutput, eZDebug, writeNotice, writeWarning, writeError, eZLog, logs, error.log, warning.log, log viewer, template path comments, error reference, audit trail, Monolog, Telescope, Django Debug Toolbar, web framework developer
type: help
---
# Debugging, logging and errors

This page is for developers who rely on a profiler toolbar and a PSR-3 logger. Exponential has its own debug output,
turned into a pinned **debug bar**, its own logging calls, and an administration page that reads the logs
([Debugging and logs](../creators/app-creators/debugging-and-logs.md)).

## Contents

1. [Debug output and the debug bar](#debug-output-and-the-debug-bar)
2. [Writing debug and log lines](#writing-debug-and-log-lines)
3. [The log files](#the-log-files)
4. [From an error page to the log line](#from-an-error-page-to-the-log-line)
5. [Debugging under Velocity](#debugging-under-velocity)
6. [Compared with other frameworks](#compared-with-other-frameworks)

## Debug output and the debug bar

```ini
# settings/override/site.ini.append.php, or the dev variant of it (development only)
[DebugSettings]
DebugOutput=enabled

[TemplateSettings]
ShowTemplatePathComments=enabled
```

The debug bar shows queries, templates used, settings, timings and caches for the page, and has controls for the debug
settings and the caches ([Exp debug bar](../../features/6.0/exp-debug-bar.md)). It can be shown to your own address only.
Template path comments print which file wrote which markup ([Template path comments](../../features/6.0/template-path-comments.md)).
Never enable debug output on a production siteaccess for everyone; it shows internals. Put it in the `dev` variant of
your settings ([Environments and secrets](environments-and-secrets.md)).

## Writing debug and log lines

```php
eZDebug::writeNotice( 'Imported 12 items', __METHOD__ );
eZDebug::writeWarning( 'Item 42 had no title', __METHOD__ );
eZDebug::writeError( 'Feed could not be read: ' . $message, __METHOD__ );
eZLog::write( 'archive sent for object ' . (int)$objectID, 'expdemo.log' );
```

Warnings and errors go to `var/log/warning.log` and `var/log/error.log` whatever the debug setting; `eZLog::write()`
writes a log of your own in the log directory. These are not PSR-3 loggers; if you use Monolog in your extension, it
writes its own files. Never log passwords, keys, tokens or personal data.

## The log files

| File | Holds |
|---|---|
| `var/log/error.log`, `warning.log` | errors and warnings |
| `var/log/security.log` | security events |
| `var/log/csp.log` | Content Security Policy reports |
| `var/log/requestrules.log` | the `log` action of request rules |
| your own `*.log` | what your code writes |

**Setup > Logs** shows them with a digest of repeated messages ([Log viewer](../../features/6.0/log-viewer.md)); logs
rotate ([Log rotation](../../features/6.0/log-rotation.md)); request logs record requests with timings
([Request logs](../../features/6.0/request-logs.md)). Security-relevant actions belong in the audit trail
([Audit trail](../../features/6.0/audit-trail.md)). In a multisite installation the log paths can differ per site
([Multi-site log and cache paths](../../features/6.0/multi-site-log-and-cache-paths.md)).

## From an error page to the log line

Error pages carry a reference; search the logs for it to find the full message
([Finding an error by its reference](../../guides/finding-an-error-by-its-reference.md)). The first page for any
problem is [When something goes wrong](../when-something-goes-wrong.md).

## Debugging under Velocity

- Workers keep classes and settings in memory: after a change, `./console exp:velocity deploy` (or `restart`), or you
  are debugging old code.
- Compare with PHP's built-in server engine (`--engine=php`), which runs each request fresh, when you suspect state kept
  between requests ([Velocity engines](../../bc/6.0/velocity-engines.md)).
- Never wrap `eZExecution::cleanExit()` in `catch ( Exception $e )`: under Velocity it ends the request with an exception.

## Compared with other frameworks

| Framework | Tool | Exponential |
|---|---|---|
| Symfony | web profiler, Monolog | debug bar, `eZDebug`, `eZLog`, **Setup > Logs** |
| Laravel | Debugbar, Telescope, `Log::` | the same |
| Django | Debug Toolbar, `logging` | the same |
| Rails | logs, `byebug` | the same; Xdebug works as in any PHP project |
| Spring | Actuator, SLF4J | **Setup > System information**, request logs |

## Related pages

- [Debug bar guide](../../bc/6.0/debug-bar.md)
- [Debug output improvements](../../features/6.0/debug-output-improvements.md)
- [Template errors and debugging](../template-language/errors-and-debugging.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
