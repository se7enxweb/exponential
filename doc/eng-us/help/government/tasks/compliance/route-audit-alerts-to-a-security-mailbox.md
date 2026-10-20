---
title: Route audit alerts to a security mailbox
audience: government, admin
category: public-sector
order: 180
summary: Step by step - send Exponential's audit alerts (brute force, administrator role granted, mass delete, audit switched off, broken chain) to your security team's mailbox instead of the site's AdminEmail, and check the recipients.
keywords: audit alerts, alert recipients, AlertRecipients, security mailbox, exp:audit alerts recipients, AdminEmail, brute_force, admin_role_granted, mass_delete, audit_disabled, chain_broken, public sector
type: help
---
# Route audit alerts to a security mailbox

For administrators. By default, alert mail goes to the site's `[MailSettings] AdminEmail`, which is often a web team
address. Serious events should reach the people who act on them ([Audit alerts and their recipients](../../security/audit-alerts-and-recipients.md)).

## Contents

1. [Before you start](#before-you-start)
2. [Steps](#steps)
3. [Check that it worked](#check-that-it-worked)
4. [Related pages](#related-pages)

## Before you start

- A team mailbox (for example `security-team@example.gov`) read during and outside office hours as your procedure says.
- The `frequent` cronjob group runs (alerts are evaluated and mailed by the cronjob part).
- Mail sending works on the server (a test with the file transport first, if unsure).

## Steps

1. Check the current recipients:

   ```bash
   ./console exp:audit alerts recipients --allow-root-user
   ```

2. In `settings/override/audit.ini.append.php`:

   ```ini
   [AuditAlertSettings]
   Recipients[]
   Recipients[]=group:security

   [AlertRecipients_security]
   Addresses[]
   Addresses[]=security-team@example.gov
   Recipients[]
   Recipients[]=role:Administrator
   ```

3. For one rule only (for example `settings_out_of_hours` to the web team), set `[AlertRule_<rule>] Recipients[]`.
4. Clear the INI cache: `php bin/php/ezcache.php --clear-tag=ini --allow-root-user`.

## Check that it worked

```bash
./console exp:audit alerts recipients --allow-root-user
./console exp:audit alerts recipients --rule=brute_force --allow-root-user
```

lists the addresses with the entries that produced them, and any problem. Mail goes at most once per rule and recipient
within 900 seconds.

## Related pages

- [Audit alerts and their recipients](../../security/audit-alerts-and-recipients.md)
- [Audit guide, 4.5](../../../../bc/6.0/audit.md#45-alerts-and-their-recipients)
- [Verify the audit trail](verify-the-audit-trail.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
