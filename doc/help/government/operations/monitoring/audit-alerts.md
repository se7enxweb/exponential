---
title: Audit alerts
audience: government, admin, project-manager
category: public-sector
order: 70
summary: The seven alert rules shipped with Exponential's audit trail (brute force, an administrator role granted, settings written out of hours, mass deletes, the audit switched off, a broken chain), who receives them, how to tune and test them, and how a public body routes them to its security team.
keywords: audit alerts, alert rules, brute force, admin_role_granted, settings out of hours, mass delete, audit disabled, chain broken, recipients, syslog, webhook, SIEM, security monitoring
type: help
---
# Audit alerts

This page is for security officers and administrators who must be told when something risky happens on a public
website. The audit trail evaluates alert rules on its records and sends alerts to syslog, mail or a webhook. Sources:
`settings/audit.ini` and [the audit guide, section 4.5](../../../../bc/6.0/audit.md#45-alerts-and-their-recipients).

## Contents
1. [The shipped rules](#the-shipped-rules)
2. [Who receives them](#who-receives-them)
3. [Where to see them](#where-to-see-them)
4. [Tuning and testing](#tuning-and-testing)
5. [Routing to your security team](#routing-to-your-security-team)
6. [Related pages](#related-pages)

## The shipped rules

| Rule | Fires when | Severity | Sinks |
|---|---|---|---|
| `brute_force` | 20 failed sign-ins from one network within 300 seconds | critical | syslog, mail |
| `brute_force_user` | 10 failed sign-ins for one account within 900 seconds | alert | syslog, mail |
| `admin_role_granted` | a role with `*/*`, `setup/*`, `role/*` or `audit/manage` is assigned | critical | syslog, mail |
| `settings_out_of_hours` | a setting is written outside business days 1-5, hours 7-19 (site time zone) | warning | syslog |
| `mass_delete` | 500 nodes removed by one user within 600 seconds (children counted) | critical | syslog, mail |
| `audit_disabled` | the audit is switched off | emergency | syslog |
| `chain_broken` | verification finds a broken chain | alert | syslog, mail |

Alerts are evaluated when records are flushed and in the `audit` cronjob part (frequent part); make sure that part
runs ([Cronjob monitoring](cronjob-monitoring.md)).

## Who receives them

Mail goes to the rule's `Recipients[]`, else `[AuditAlertSettings] Recipients[]`, else `[AuditSink_mail] Receivers[]`,
else the site's `AdminEmail`. Recipients can be addresses, named groups (`group:security`), users, user groups or a
role (`role:Administrator`). At most one mail per rule and recipient within 900 seconds. Check:

```bash
./console exp:audit alerts recipients --allow-root-user
./console exp:audit alerts list --allow-root-user
```

Use a function mailbox (for example the security team's shared address), not one person's address, so alerts survive
leave and staff changes.

## Where to see them

- **Audit** tab, **Alerts** (`/audit/alerts`): fired alerts and the rules with kind, event, threshold, sinks and
  state.
- The audit dashboard's Alerts panel.
- Syslog: with `Transport=local` the records reach journald, `journalctl -t exponential`.

## Tuning and testing

Replay a rule against a past day before changing it:

```bash
./console exp:audit alerts test mass_delete --replay=2026-09-01 --allow-root-user
```

Override thresholds, business hours (`BusinessDays`, `BusinessHours`) or recipients in
`settings/override/audit.ini.append.php`, through your change process. Typical public-sector adjustments: business
hours matching your service hours; the security team as recipient of `admin_role_granted` and `brute_force`;
`settings_out_of_hours` also by mail if settings should never change outside a window.

## Routing to your security team

If your organization runs a security operations center or a SIEM, feed it from the syslog sink or the signed webhook
sink (HMAC-SHA-256 over timestamp and body, spooled with retries) rather than from mail
([Audit trail, "Sinks, alerts and archives"](../../../../features/6.0/audit-trail.md#sinks-alerts-and-archives)).
Agree what each alert means for them and who calls whom; the [incident pages](../incidents/README.md) give the first
steps.

## Related pages
- [Audit trail verification](audit-trail-verification.md)
- [Check who gets audit alerts](../../tasks/operations/check-who-gets-audit-alerts.md)
- [Suspected account compromise](../incidents/suspected-account-compromise.md)
- [Audit trail (hosting)](../../../hosting/security/audit-trail.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
