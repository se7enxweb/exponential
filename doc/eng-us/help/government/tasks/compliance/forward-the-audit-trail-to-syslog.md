---
title: Forward the audit trail to syslog
audience: government, admin, hosting
category: public-sector
order: 220
summary: Step by step - send Exponential's audit records to your central log collector or SIEM over TLS with the syslog sink, choose the channels, test delivery with exp:audit sinks test, and keep it running.
keywords: syslog, TLS, SIEM, log forwarding, AuditSink_syslog, Transport, Host, Port, Facility, MinSeverity, Sinks, exp:audit sinks test, central logging, public sector
type: help
---
# Forward the audit trail to syslog

For administrators and hosting teams with a central log collector. Records that leave the server at once cannot be
rewritten quietly by an attacker who takes the server ([Forwarding the audit trail to a SIEM](../../security/forwarding-audit-to-a-siem.md)).

## Contents

1. [Before you start](#before-you-start)
2. [Steps](#steps)
3. [Check that it worked](#check-that-it-worked)
4. [Related pages](#related-pages)

## Before you start

- The collector's host, TLS port and facility from your security operations team; its certificate is trusted by the
  server's system CA store.
- The firewall lets the web server reach the collector.
- Agree the retention of the records in the collector with your DPO.

## Steps

1. In `settings/override/audit.ini.append.php`:

   ```ini
   [AuditSink_syslog]
   Transport=tls
   Host=siem.example.gov
   Port=6514
   Facility=local4
   MinSeverity=info

   [AuditChannel_content]
   Sinks[]=syslog

   [AuditChannel_commerce]
   Sinks[]=syslog
   ```

   The `access` and `system` channels ship with the `syslog` sink already; `Sinks[]` lines append to the shipped lists.
2. Clear the INI cache, reload PHP-FPM, restart Velocity.

## Check that it worked

```bash
./console exp:audit sinks list --allow-root-user
./console exp:audit sinks test syslog --allow-root-user
```

The test sends one record; ask the SOC to confirm it arrived. Then sign in to the administration and check that the
`access.session.login` record arrives too. If delivery fails, records are spooled and sent by the cronjob part;
`./console exp:audit sinks flush --apply syslog --allow-root-user` sends what waits.

## Related pages

- [Forwarding the audit trail to a SIEM](../../security/forwarding-audit-to-a-siem.md)
- [Audit guide, 6.5 SIEM forwarding](../../../../bc/6.0/audit.md#65-siem-forwarding)
- [Verify the audit trail](verify-the-audit-trail.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
