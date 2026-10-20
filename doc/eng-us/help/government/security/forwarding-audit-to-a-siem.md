---
title: Forwarding the audit trail to a SIEM
audience: government, admin, hosting
category: public-sector
order: 100
summary: For public bodies with a central security operations function - how to send Exponential's audit records to a SIEM or log collector with the syslog sink (UDP, TCP, TLS) and the signed webhook sink, which channels to forward, and how to test delivery.
keywords: SIEM, security operations, SOC, log forwarding, syslog, TLS, webhook, signed webhook, X-Exponential-Signature, audit sinks, central logging, exp:audit sinks, NIS2, monitoring
type: help
---
# Forwarding the audit trail to a SIEM

This page is for administrators and hosting teams of public bodies that have a security operations center or a
central log platform. Forwarding audit records off the web server means a compromised machine cannot quietly rewrite
its own history, and lets your analysts correlate the site with the rest of your estate
([Security hardening: Watching](../../security-hardening.md#watching-logs-the-request-classifier-and-the-audit-trail)).
The configuration comes from [6.5 SIEM forwarding](../../../bc/6.0/audit.md#65-siem-forwarding).

## Contents

1. [The two sinks](#the-two-sinks)
2. [A configuration](#a-configuration)
3. [Testing delivery](#testing-delivery)
4. [Operating it](#operating-it)
5. [Related pages](#related-pages)

## The two sinks

| Sink | Transport | Good for |
|---|---|---|
| `syslog` | local syslog/journald, UDP, TCP or TLS to a collector | the full stream, into any SIEM that reads syslog |
| `webhook` | HTTPS POST, batched, signed with a shared secret (`X-Exponential-Signature`) | selected security events, into a SIEM's HTTP ingest |

Sinks are attached per channel (`[AuditChannel_<channel>] Sinks[]`); the webhook and network syslog deliveries are
spooled and sent by the cronjob part.

## A configuration

In `settings/override/audit.ini.append.php` (never in version control):

```ini
[AuditSink_syslog]
Transport=tls
Host=siem.example.gov
Port=6514
Facility=local4
MinSeverity=info

[AuditSink_webhook]
URL=https://siem.example.gov/api/ingest/exponential
SigningSecret=<output of: openssl rand -hex 32>
BatchSize=200
MinSeverity=notice
Events[]
Events[]=access.*
Events[]=system.audit.*
Events[]=system.setting.*

[AuditChannel_content]
Sinks[]=syslog

[AuditChannel_access]
Sinks[]=webhook

[AuditChannel_system]
Sinks[]=webhook

[AuditChannel_commerce]
Sinks[]=syslog
```

`Sinks[]` lines append to the shipped lists, so `access` and `system` keep their shipped `syslog` entry. With TLS,
PHP verifies the collector's certificate against the system CA store.

## Testing delivery

```bash
./console exp:audit sinks list --allow-root-user
./console exp:audit sinks test syslog --allow-root-user
./console exp:audit sinks test webhook --allow-root-user
```

Each test sends one record; the receiver must answer with a 2xx status. The receiver checks the webhook signature;
in PHP the check is `expAuditWebhookSink::verify( $body, $headers, $secret )`.

## Operating it

- Forward the daily signed checkpoints (`system.audit.checkpoint`) as a minimum: they let you prove later that the
  local chain was not recomputed.
- Monitor the spool: `./console exp:audit sinks flush --allow-root-user` sends what waits; the dashboard's Operations card
  shows the sinks.
- Remember the privacy settings apply before forwarding: addresses truncated, sessions hashed. Agree the retention in
  the SIEM with your data protection officer.
- In a cluster, each server forwards its own records ([6.3 Cluster](../../../bc/6.0/audit.md#63-cluster)).

## Related pages

- [Audit guide, 6.5 SIEM forwarding](../../../bc/6.0/audit.md#65-siem-forwarding)
- [Audit integrity and verification](audit-integrity-and-verification.md)
- [Audit trail for hosts](../../hosting/security/audit-trail.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
