---
title: Outgoing webhooks from the audit trail
audience: integrator, developer, admin
category: integration
order: 190
summary: Using the audit trail's signed webhook sink as an outgoing event feed - what it sends, how to route content events such as content.object.publish to it, the HMAC signature and how a receiver checks it, delivery with spool and retries, testing, and its limits.
keywords: webhook, audit, audit trail, expAuditWebhookSink, AuditSink_webhook, SigningSecret, X-Exponential-Signature, HMAC-SHA-256, content.object.publish, content.node.remove, spool, retries, syslog sink, SIEM, event feed
type: help
---
# Outgoing webhooks from the audit trail

This page is for integrators who want Exponential to **push events** to another system over HTTPS without writing
code: a security information and event management system (SIEM), a log platform, or a service that must learn about
publications and removals. The audit trail records what happens in the installation and can deliver its records to
**sinks** besides its own files: syslog, e-mail and a **signed webhook** (`kernel/classes/audit/sinks/expauditwebhooksink.php`).
The audit itself is described in [Audit](../../bc/6.0/audit.md).

## Contents

1. [What the webhook sends](#what-the-webhook-sends)
2. [Sending security events to a SIEM](#sending-security-events-to-a-siem)
3. [Sending content events to another system](#sending-content-events-to-another-system)
4. [Checking the signature on the receiving side](#checking-the-signature-on-the-receiving-side)
5. [Delivery, retries and testing](#delivery-retries-and-testing)
6. [Limits](#limits)

## What the webhook sends

Batches of audit records as JSON, by HTTP `POST`:

```text
{"v":1,"installation":"<installation id>","site":"<SiteName>","batch":"b-...","events":[<record>, ...]}
```

| Header | Value |
|---|---|
| `X-Exponential-Timestamp` | Epoch seconds when the batch was signed |
| `X-Exponential-Batch` | The batch id, for de-duplication |
| `X-Exponential-Signature` | `sha256=<hex HMAC-SHA-256 of timestamp + "." + body, keyed with SigningSecret>` |

A record names the event (`content.object.publish`, `access.session.login.failed` ...), the actor, the object and
target by type, id and identifying fields, the result and a request id. Records never contain content attribute
values; the receiver reads the content it needs through the REST API or `expservices`.

## Sending security events to a SIEM

The `access` and `system` channels carry sign-ins, refusals, role changes and settings changes. In
`settings/override/audit.ini.append.php`:

```ini
[AuditSink_webhook]
URL=https://siem.example.com/ingest/exponential
SigningSecret=<a long random value>
MinSeverity=notice

[AuditChannel_access]
Sinks[]
Sinks[]=syslog
Sinks[]=webhook

[AuditChannel_system]
Sinks[]
Sinks[]=syslog
Sinks[]=webhook
```

The empty `Sinks[]` line clears the shipped list before the new one; keep `syslog` if you want it too. The secret is
masked in the administration; keep it in `settings/override`, never in Git.

## Sending content events to another system

Content events are recorded per event name. `content.object.publish` and `content.object.create` are **off** by
default; removals, moves, hides, state and section changes are on. To feed publications and removals to a webhook:

```ini
[AuditEventSettings]
Enabled[]=content.object.create
Enabled[]=content.object.publish

[AuditChannel_content]
Sinks[]
Sinks[]=webhook

[AuditSink_webhook]
URL=https://hub.example.com/hooks/exponential
SigningSecret=<a long random value>
Events[]
Events[]=content.object.publish
Events[]=content.node.remove.*
Events[]=content.object.remove
Events[]=content.node.move
# only the listed events: a record outside Events[] is still taken when it reaches MinSeverity
MinSeverity=emergency
```

A sink takes a record that matches `Events[]` whatever its severity, and any other record routed to it when it
reaches `MinSeverity`; `emergency` therefore keeps the sink to the listed events in practice.

> **Note**
> The audit is designed as a record of what happened, not as a message bus: records always go through the spool
> (a request never waits for the network) and the audit cronjob part delivers them, so delivery follows the cron
> schedule, typically every few minutes. The shape of a record follows the audit's event model. If the receiving system needs
> its own payload, a guaranteed order per object, or sub-second delivery, use a workflow event with an outbox
> ([Workflows and triggers](workflows-and-triggers-as-hooks.md)).

## Checking the signature on the receiving side

Reject a request when the signature does not match or the timestamp is more than 300 seconds away; de-duplicate by
the batch id and by each record's event id. In Python:

```python
import hashlib, hmac, time

def verify(secret: bytes, timestamp: str, body: bytes, signature: str, tolerance=300) -> bool:
    if not timestamp.isdigit() or abs(time.time() - int(timestamp)) > tolerance:
        return False
    expected = 'sha256=' + hmac.new(secret, timestamp.encode() + b'.' + body, hashlib.sha256).hexdigest()
    return hmac.compare_digest(expected, signature)
```

A receiver written in PHP with the kernel loaded can call `expAuditWebhookSink::verify( $secret, $timestamp, $body,
$signature )`, which answers `''` when valid, else `bad_signature` or `stale_timestamp`.

## Delivery, retries and testing

| Setting (`[AuditSink_webhook]`) | Default | Meaning |
|---|---|---|
| `BatchSize` | `100` | Records per request |
| `BatchSeconds` | `10` | Seconds a batch may wait |
| `Timeout` | `5` | Seconds per request |
| `Retries` | `5` | Attempts before a batch stays in the spool |
| `RetryBackoff` | `30` | Seconds before the first retry; doubled each time |

Every record goes to the spool first (`[AuditSinkSettings] SpoolDir`, `log/audit/spool`); the audit cronjob part
(in `frequent`, and as the part `audit`) delivers it and retries what failed. A `2xx` answer delivers a batch. A
retried batch keeps its batch id. Delivery is at least once: the receiver must de-duplicate by event id.

```bash
php bin/php/console exp:audit sinks list --allow-root-user          # ready? spooled? channels
php bin/php/console exp:audit sinks test webhook --allow-root-user  # one test record, exit 0 when delivered
php bin/php/console exp:audit sinks flush --apply --allow-root-user         # send what is spooled now
```

## Limits

- Only the audit trail sends webhooks; there is no generic outgoing webhook per content class or field.
- There is no incoming webhook endpoint; a receiver of other systems' webhooks is a module or service function of
  your extension.
- An event that is not recorded (switched off in `[AuditEventSettings]`, or below `MinSeverity` of the audit) is not
  sent.

## Related pages

- [Audit](../../bc/6.0/audit.md), sections "Sinks and alerts" and "The developer API"
- [Audit trail for hosts](../hosting/security/audit-trail.md)
- [API and protocol standards, webhooks](../../standards/apis-and-protocols.md#webhooks)
- [Monitoring integrations](monitoring-integrations.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
