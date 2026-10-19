---
title: Check who gets audit alerts
audience: government, admin, project-manager
category: public-sector
order: 190
summary: Step by step - list the audit alert rules and the addresses each one mails, fix the recipients so alerts reach a team mailbox instead of a placeholder or one person, and test a rule against a past day.
keywords: audit alerts, recipients, AdminEmail, alert rules, exp:audit alerts, security team, mailbox, AuditAlertSettings, replay, test
type: help
---
# Check who gets audit alerts

This task is for administrators and security officers, once at go-live and after staff changes. At the end, every
alert reaches the people who must act on it. Background: [Audit alerts](../../operations/monitoring/audit-alerts.md).

## Contents
1. [Before you start](#before-you-start)
2. [Steps](#steps)
3. [Change the recipients](#change-the-recipients)
4. [Check it worked](#check-it-worked)
5. [Related pages](#related-pages)

## Before you start

- Shell access; the change to settings goes through your change process.

## Steps

```bash
./console exp:audit alerts list --allow-root-user
./console exp:audit alerts recipients --allow-root-user
./console exp:audit alerts recipients --rule=admin_role_granted --allow-root-user
```

`recipients` lists every address with the entries that produced it and any problem (an unknown group, a disabled
user, a missing role). Without configuration, mail goes to `site.ini [MailSettings] AdminEmail`, which on many
installations is still a placeholder.

## Change the recipients

In `settings/override/audit.ini.append.php`, for all rules or per rule:

```ini
[AuditAlertSettings]
Recipients[]
Recipients[]=address:security-team@example.gov

[AlertRule_admin_role_granted]
Recipients[]
Recipients[]=group:security
```

Recipients can be `address:`, `group:<name>` (an `[AlertRecipients_<name>]` list), `user:<id>`, `login:<login>`,
`usergroup:<id>` or `role:<name>`. Prefer a team mailbox or a role, so leave and staff changes do not break alerting.
Clear the INI cache afterwards (`php bin/php/ezcache.php --clear-tag=ini --allow-root-user`).

## Check it worked

- `./console exp:audit alerts recipients --allow-root-user` shows the new addresses and no problems.
- Replay a rule against a past day to see what it would have sent:

  ```bash
  ./console exp:audit alerts test mass_delete --replay=2026-09-01 --allow-root-user
  ```

- Mail is sent by the `audit` cronjob part; make sure it runs ([Check that cronjobs run](check-that-cronjobs-run.md)).

## Related pages
- [Verify the audit trail](verify-the-audit-trail.md)
- [Audit alerts](../../operations/monitoring/audit-alerts.md)
- [Mail problems](../../operations/incidents/mail-problems.md)
- [Audit guide, section 4.5](../../../../bc/6.0/audit.md#45-alerts-and-their-recipients)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
