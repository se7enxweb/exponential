---
title: Error handling and logging in integrations
audience: integrator, developer
category: integration
order: 330
summary: How an integration should fail, retry and leave a trace in Exponential - classes of errors, retries and dead letters, transactions, eZDebug and eZLog, the log files under var/log, error references, the log viewer and exp:logs, the audit trail, and what never belongs in a log.
keywords: error handling, retry, backoff, dead letter, transaction, eZDebug, writeError, writeWarning, eZLog, log file, var/log, error.log, error reference, ERR-, log viewer, exp:logs, audit, masking, personal data in logs
type: help
---
# Error handling and logging in integrations

This page is for integration developers. An integration fails sooner or later: the other system is down, a record is
invalid, a credential expires. Whether that is a non-event or an incident depends on how the code fails, retries and
reports. The kernel's logging tools are [eZDebug and logging](../extension-development/part-8-reference/ezdebug-and-logging.md).

## Contents

1. [Three kinds of errors](#three-kinds-of-errors)
2. [Retries and the dead letter](#retries-and-the-dead-letter)
3. [Transactions](#transactions)
4. [Writing to the logs](#writing-to-the-logs)
5. [Reading the logs](#reading-the-logs)
6. [What never goes into a log](#what-never-goes-into-a-log)

## Three kinds of errors

| Kind | Examples | Handling |
|---|---|---|
| **Transient** | Timeout, `429`, `502`, `503`, a lock wait | Retry with growing delays; alert only when retries are exhausted |
| **Permanent for one record** | Invalid value, unknown class, a remote id in use, `400`, `422` | Skip the record, log it with its key and the reason, continue; report the list after the run |
| **Fatal for the run** | Credential refused (`401`), source file missing or truncated, database unavailable | Stop the run at once with a non-zero exit code; alert |

A run that skips records must still say so: the summary line counts them, and the exit code or a status flag tells
monitoring that the run was not clean.

## Retries and the dead letter

- Retry transient errors with exponential backoff and a cap (for example 2, 4, 8 ... minutes, at most an hour), and
  honor `Retry-After` from the other system.
- After a fixed number of attempts, move the item to a **dead letter** state (a status in your outbox table, or the
  dead letter of `expqueue`) and alert. An item retried forever hides a real problem.
- Make retries safe: the receiver must recognize a repeated delivery by an idempotency key
  ([Writing an export](writing-an-export-or-outbound-sync.md#the-outbox-sender)).

## Transactions

`eZContentFunctions::createAndPublishObject()` manages its own transaction. When your code writes several rows of
your own tables that belong together, wrap them:

```php
<?php
$db = eZDB::instance();
$db->begin();
$db->query( 'UPDATE myintegration_outbox SET sent = ' . time() . ' WHERE id = ' . (int)$rowID );
$db->query( 'INSERT INTO myintegration_delivery ( outbox_id, answered ) VALUES ( ' . (int)$rowID . ', ' . time() . ' )' );
$db->commit();
```

Never hold a transaction open across a call to another system: a slow answer holds database locks for every editor.

## Writing to the logs

| Call | Goes to (under `var/log/`, or the site's log directory) | Use for |
|---|---|---|
| `eZDebug::writeError( $message, __METHOD__ )` | `error.log` | Failures someone must look at |
| `eZDebug::writeWarning( $message, __METHOD__ )` | `warning.log` (when that level is logged) | Skipped records, retries |
| `eZDebug::writeNotice( $message, __METHOD__ )` | `notice.log` (when logged) | Detail for debugging |
| `eZLog::write( $message, 'myintegration.log' )` | `var/log/myintegration.log` | The integration's own trail: one summary line per run, one line per skipped record |

Which levels reach the log files is set in `site.ini [DebugSettings] AlwaysLog[]` (by default `error`). Use your own
`eZLog` file for the integration's trail, so operators find it without searching the kernel logs.

A good summary message (`eZLog` adds the date in front):

```text
import erp-products: rows=20412 created=12 updated=388 unchanged=19990 hidden=3 skipped=19 failed=0 seconds=274
```

## Reading the logs

- **Setup > Log files** (the log viewer) lists, follows and searches the log files and their rotations, with masking
  of personal data ([The log viewer](../../features/6.0/log-viewer.md)).
- **Error references**: an error page shows a reference such as `ERR-0123456789`, also written to `error.log`; the log
  viewer and `php bin/php/console exp:logs --reference=ERR-...` show every line of that request
  ([Finding an error by its reference](../../guides/finding-an-error-by-its-reference.md)).
- From a shell:

  ```bash
  php bin/php/console exp:logs --list
  php bin/php/console exp:logs --tail=var/log/myintegration.log --lines=50
  php bin/php/console exp:logs --search=erp-product --file=var/log/error.log --rotations
  ```

- **The audit trail** answers "who changed what": writes through `expservices`, API key use and refusals, content
  removals ([Audit](../../bc/6.0/audit.md)). An integration that acts for users can record its own events through
  the audit API.
- Log files are rotated by the cronjob part `logs` (`explogrotate.php`) ([Log rotation](../../features/6.0/log-rotation.md)).

## What never goes into a log

- Passwords, API keys, tokens, signing secrets, session ids.
- Full payloads containing personal data; log keys and counts instead.
- Card data of any kind.

The log viewer masks e-mail and IP addresses and tokens, but masking is a safety net, not a permission to write them.

## Related pages

- [eZDebug and logging](../extension-development/part-8-reference/ezdebug-and-logging.md)
- [Debugging and logs for app creators](../creators/app-creators/debugging-and-logs.md)
- [Monitoring integrations](monitoring-integrations.md)
- [Log viewer and rotation for hosts](../hosting/operations/log-viewer-and-rotation.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
