---
title: Audit alerts and their recipients
audience: government, admin
category: public-sector
order: 80
summary: For administrators of public sites - the seven alert rules Exponential ships (brute force, administrator role granted, settings changed out of hours, mass delete, audit switched off, broken chain), who receives the mail, how to route alerts to a security team, and how to test a rule.
keywords: audit alerts, alert rules, brute_force, brute_force_user, admin_role_granted, settings_out_of_hours, mass_delete, audit_disabled, chain_broken, recipients, mail, syslog, security team, exp:audit alerts
type: help
---
# Audit alerts and their recipients

This page is for administrators who make sure that a serious event on a public site reaches a person. The audit trail
evaluates alert rules over its records; each firing is recorded as `system.audit.alert` and sent to syslog and mail
([4.5 Alerts and their recipients](../../../bc/6.0/audit.md#45-alerts-and-their-recipients)).

## Contents

1. [The shipped rules](#the-shipped-rules)
2. [Who gets the mail](#who-gets-the-mail)
3. [Routing alerts to your security team](#routing-alerts-to-your-security-team)
4. [Testing and tuning](#testing-and-tuning)
5. [Related pages](#related-pages)

## The shipped rules

From `settings/audit.ini [AuditAlertSettings] Rules[]`:

| Rule | Fires when | Severity | Sent to |
|---|---|---|---|
| `brute_force` | 20 failed sign-ins from one network within 5 minutes | critical | syslog, mail |
| `brute_force_user` | 10 failed sign-ins for one account within 15 minutes | alert | syslog, mail |
| `admin_role_granted` | a role with `*/*`, `setup/*`, `role/*` or `audit/manage` is assigned | critical | syslog, mail |
| `settings_out_of_hours` | a setting is written outside business hours | warning | syslog |
| `mass_delete` | 500 nodes removed by one user within 10 minutes (children counted) | critical | syslog, mail |
| `audit_disabled` | the audit is switched off | emergency | syslog, mail |
| `chain_broken` | verification finds a broken chain | alert | syslog, mail |

Alerts are evaluated by the cronjob part (the `frequent` group): if it does not run, no alert fires.

## Who gets the mail

The first of these that is set:

1. the rule's own `[AlertRule_<rule>] Recipients[]`;
2. `[AuditAlertSettings] Recipients[]`;
3. `[AuditSink_mail] Receivers[]` (older name);
4. `admin`, which is `site.ini [MailSettings] AdminEmail`.

Recipients can be `admin`, an address, `group:<name>` (a list in `[AlertRecipients_<name>]`), `user:<id>`,
`login:<login>`, `usergroup:<id>`, or `role:<name or id>`. Disabled users and invalid addresses are left out. Mail goes
from the cronjob part only, one per recipient, at most one per rule and recipient within 900 seconds.

## Routing alerts to your security team

In `settings/override/audit.ini.append.php`:

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

Use a team mailbox rather than one person's address, so alerts are seen during absences. Then check:

```bash
./console exp:audit alerts recipients --allow-root-user
./console exp:audit alerts recipients --rule=admin_role_granted --allow-root-user
```

The command lists every address with the entries that produced it and any problem (an unknown group, a disabled user).

## Testing and tuning

- Replay a rule against a past day before changing it: `./console exp:audit alerts test mass_delete --replay=<YYYY-MM-DD> --allow-root-user`.
- Business hours for `settings_out_of_hours` are `[AuditAlertSettings] BusinessDays=1-5` and `BusinessHours=7-19` as shipped (in the site's
  time zone); match them to your office.
- Look at fired alerts in the Audit tab under **Alerts** (`/audit/alerts`).
- Write down, for each rule, who acts and how (see "Incident response" in this section).

## Related pages

- [Audit guide, 4.5](../../../bc/6.0/audit.md#45-alerts-and-their-recipients)
- [The audit trail for public bodies](audit-trail-for-public-bodies.md)
- [13.14 The audit log](../../../install/13-security-hardening.md#1314-the-audit-log)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
