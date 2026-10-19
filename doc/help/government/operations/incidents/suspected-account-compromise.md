---
title: Suspected account compromise
audience: government, admin, project-manager
category: public-sector
order: 60
summary: Runbook for a staff or administrator account that may be used by someone else - disable it, end its sessions, look at what it did in the audit trail, undo changes, reset the password and second factor, and check for escalated rights.
keywords: account compromise, stolen password, hijacked account, phishing, unauthorized access, disable account, sessions, audit trail, password reset, two-step sign-in, role changes, incident
type: help
---
# Suspected account compromise

This runbook is for administrators and security officers when a staff account may be in the wrong hands: a phished
password, sign-ins from unknown places, an alert such as `brute_force_user` or `admin_role_granted`, or changes the
owner did not make. Start with [Incident response basics](incident-response-basics.md).

## Contents
1. [Contain the account](#contain-the-account)
2. [What did it do?](#what-did-it-do)
3. [Undo](#undo)
4. [Give it back to its owner](#give-it-back-to-its-owner)
5. [If it was an administrator](#if-it-was-an-administrator)
6. [Related pages](#related-pages)

## Contain the account

1. **Disable** it: **Users > Check user accounts**, find it, **Disable**, **Confirm**. Its sessions end at once and it
   cannot sign in; content, groups and roles stay (`access.user.disable`).
2. With the database session handler, **Setup > Sessions** can also remove the account's sessions explicitly
   ([Sessions](../../../../guides/sessions.md)).
3. Revoke API keys and OAuth tokens the account holds (its account page, **API keys**).
4. If the account had full access, consider maintenance mode and network restrictions on the administration while
   you look.

## What did it do?

```bash
./console exp:audit search --login=<login> --from='<since when>' --limit=200 --allow-root-user
./console exp:audit search --channel=access --login=<login> --limit=50 --allow-root-user
./console exp:audit verify --allow-root-user
```

Look for: sign-ins (`access.session.*`) and their masked networks; role assignments; settings writes; publications,
removals, moves; exports (`data.export.csv`). In the browser: **Audit** tab, **Console**, filter by user. Keep an
export of the results as evidence ([Export audit records for an auditor](../../tasks/operations/export-audit-records-for-an-auditor.md)).

## Undo

| Found | Undo |
|---|---|
| Content changed or published | versions page, **Make a new draft** from the last good version, publish |
| Content removed | restore from the trash (the trash records who trashed what) |
| Roles or assignments added | remove them on the role pages |
| New accounts created | disable them; check their activity the same way |
| Settings written | revert through your deployment; check `settings/override` against version control |

## Give it back to its owner

1. Verify the owner's identity through your service desk.
2. Set a new password ([Reset a staff password](../../tasks/people-and-permissions/reset-a-staff-password.md)); a
   password change signs out the account's other sessions.
3. Reset the second step if the phone or mailbox may be compromised
   ([Help a user who lost their phone](../../tasks/people-and-permissions/help-a-user-who-lost-their-phone.md)), and
   require two-step sign-in if it was off.
4. **Enable** the account.
5. Ask the owner how the credentials were exposed (phishing, reuse); tell your security team.

## If it was an administrator

Treat it as a possible compromise of the whole site: check file integrity (`php bin/php/checkmanifest.php --all
--extensions`), every role with *Full access* or *Can change roles*, settings and extensions, and rotate the secrets
the site holds (database password, API keys). Follow [Compromised site (hosting runbook)](../../../hosting/runbooks/compromised-site.md).
If personal data may have been accessed, see [Data breach notification framing](data-breach-notification-framing.md).

## Related pages
- [Audit alerts](../monitoring/audit-alerts.md)
- [Two-step sign-in for staff](../people-and-permissions/two-factor-for-staff.md)
- [Unlock a locked account](../../tasks/people-and-permissions/unlock-a-locked-account.md)
- [Security and audit guide](../../../../guides/security-and-audit.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
