---
title: Logs, the log viewer and request logs
audience: php-developer, developer
category: php-development
order: 390
summary: The log files a PHP developer reads and writes - error.log, warning.log, deprecated.log, 404.log, security.log and your own via eZLog::write() - the log viewer in Setup > Logs and on the command line with explogs.php, error references, request classification, rotation, and rules for log lines.
keywords: logs, eZLog, error.log, warning.log, notice.log, deprecated.log, 404.log, security.log, request logs, log viewer, explogs.php, error reference, log rotation, request classifier, expRequestLog, privacy, masking
type: help
---
# Logs, the log viewer and request logs

This page is for PHP developers who write log lines or look for what went wrong. Exponential writes its logs under
`var/log/` (or the log directory of the site) and shows them in **Setup > Logs** and on the command line. Since 6.0.15
not-found answers and suspicious requests have logs of their own, so `error.log` keeps the real faults.

## Contents

1. [The logs](#the-logs)
2. [Writing your own log](#writing-your-own-log)
3. [The log viewer](#the-log-viewer)
4. [The log viewer on the command line](#the-log-viewer-on-the-command-line)
5. [Error references](#error-references)
6. [Request logs: 404 and security](#request-logs-404-and-security)
7. [Rotation](#rotation)
8. [Rules for log lines](#rules-for-log-lines)

## The logs

| File | Holds |
|---|---|
| `error.log` | real faults: a database error, a template that does not compile, a PHP error, `eZDebug::writeError()` |
| `warning.log` | warnings, including refused form tokens, when the level is logged |
| `deprecated.log` | PHP deprecations, each distinct message once per request (`DeprecatedLog=enabled`) |
| `404.log` | answers 404 and 410, with the reason the kernel found |
| `notice.log` | answers 401 and 403, and notices when logged |
| `security.log` | requests the request classifier names a probe, traversal, injection, attack tool or burst |
| `debugbar.log` | writes made from the debug bar (in `var/<site>/log/`) |
| your own `*.log` | what your code writes with `eZLog::write()` |

## Writing your own log

```php
eZLog::write( 'archived object ' . (int)$objectID . ' version ' . (int)$version, 'acmereport.log' );
```

`eZLog::write( $message, $logName = 'common.log', $dir = 'var/log' )` appends a line with the time. A command can use
`$this->log( $message )`, which writes `var/log/<script name>.log` by default
([Write a console command](write-a-console-command.md#the-helpers)).

## The log viewer

**Setup > Logs** lists every log of the kernel, the extensions and the web server, with search, a navigator,
permalinks to lines, live following, an error digest of repeated messages, log health, archives and downloads. E-mail
addresses, IP addresses and secrets are masked by default; a user with `setup/unmasklogs` can switch masking off
for themselves, and the audit trail records it ([Log viewer](../../features/6.0/log-viewer.md)).

## The log viewer on the command line

`bin/php/explogs.php` is the same viewer, read only:

```bash
php bin/php/explogs.php --list                                        # the log files: group, size, time, lines a minute
php bin/php/explogs.php --tail=var/log/error.log --lines=50           # the last 50 entries
php bin/php/explogs.php --tail=var/log/error.log --follow             # follow it
php bin/php/explogs.php --search=timeout --file=var/log/error.log --level=error,warning
php bin/php/explogs.php --reference=<reference>                       # where an error page's reference is
php bin/php/explogs.php --related=var/log/error.log:<offset>          # the lines of the same request
```

It exits with 0 when something was found, 1 when nothing was, 2 on an error, so scripts can use it.

## Error references

An error page shows a reference. The same reference is in the log line, with the request it belongs to, so a user's
screenshot leads straight to the message: `--reference=<reference>` on the command line, or the search of the log
viewer ([Finding an error by its reference](../../guides/finding-an-error-by-its-reference.md)).

## Request logs: 404 and security

`expRequestLog` classifies every request once, whichever way it ends (normal end, `eZExecution::cleanExit()`, the error
module, a fatal error), and writes **one line per request in one log**:

- a missing page goes to `404.log` with the reason (an undefined module or view, a node that is not there, a request
  rule);
- a probe or attack goes to `security.log` with its category and rule, whatever the status;
- a real fault stays in `error.log`.

Nothing is blocked; the classifier only classifies. Client addresses are masked, query values replaced by `***`.
Listeners can act on a verdict ([Request logs](../../features/6.0/request-logs.md)). For your code this means: a view
that answers "not found" with `$Module->handleError( eZError::KERNEL_NOT_FOUND, 'kernel' )` lands in `404.log`, not in
`error.log`, so do not log it again as an error.

## Rotation

When a log outgrows `site.ini [FileSettings] LogFileMaxSize` (2 MB), the next write rotates it, keeping
`LogFileRotateCount` (3) archives. `eZDebug` and `eZLog` use the same rotator, so your own log rotates too, safely even
when Velocity and PHP-FPM write the same file ([Log rotation](../../features/6.0/log-rotation.md)):

```bash
php bin/php/explogs.php --archives
php bin/php/explogs.php --rotate=error
php bin/php/explogs.php --prune --dry-run
```

## Rules for log lines

- **One event, one line.** Remove line breaks from values, so one entry cannot fake another.
- **Name the source.** `__METHOD__` as the `eZDebug` label; your extension's name in your own log.
- **Ids, not data.** Log ids and counts, not content, e-mail addresses or names.
- **Never secrets.** No passwords, tokens, keys, session ids.
- **Levels mean something.** `writeError()` for faults someone must fix, `writeWarning()` for problems the code handled,
  notices for information.

## Related pages

- [Debugging with eZDebug](debugging-with-ezdebug.md)
- [Debug output and logs for developers](../extension-development/part-1-from-user-to-developer/debug-output-and-logs-for-developers.md)
- [When something goes wrong](../when-something-goes-wrong.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
