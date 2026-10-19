---
title: Give an auditor read access to the audit trail
audience: government, admin
category: public-sector
order: 150
summary: Step by step - create a read-only "Auditor" role with audit/read limited by channel, masked log access and version reading, assign it to an auditors' group, and check that nothing can be changed.
keywords: auditor, audit/read, Channel limitation, read-only role, audit trail, Audit tab, internal audit, security officer, setup/logs, masked logs
type: help
---
# Give an auditor read access to the audit trail

This task is for administrators. At the end, an auditor or security officer can read the **Audit** tab and the masked
logs, and cannot change content, settings or the record. Design:
[Role design: auditors](../../operations/people-and-permissions/role-design-auditors.md).

## Contents
1. [Before you start](#before-you-start)
2. [Create the role](#create-the-role)
3. [Assign it](#assign-it)
4. [Check it worked](#check-it-worked)
5. [Related pages](#related-pages)

## Before you start

- An administrator account (only Administrator holds `audit/read` and `audit/manage` in a new installation;
  with escalation prevention you can only grant what you hold).
- The auditor has a personal account; for an external auditor, an account that is disabled when the engagement ends.
- A decision on which channels: `access`, `content`, `system`, `commerce` (and `read` if reads are recorded; an
  extension's own channel such as `ai` if relevant).

## Create the role

1. **Users > Roles and policies > New role**, name it "Auditor (read only)".
2. **New policy**: module `audit`, function `read`, **Grant limited access**, Channel: the chosen channels. **OK**.
3. **New policy**: `setup` / `logs`, **Grant full access** (for the function).
4. **New policy**: `setup` / `system_info`.
5. **New policy**: `content` / `read`, and `content` / `versionread` and `diff`, limited to the areas under audit or
   unlimited.
6. **New policy**: `user` / `login`, SiteAccess: the administration siteaccess.
7. **Save**.

Do not add `audit/manage`, `setup/unmasklogs`, the `role` module or `user/hygiene` (which can disable accounts).

## Assign it

Create a user group "Auditors" if needed, then on the role page **Assign** and choose the group. Add the auditor's
account to the group.

## Check it worked

1. Sign in as the auditor in a private window.
2. The **Audit** tab is there: **Dashboard**, **Console**, **Charts**, **Alerts**, **Export**. Channels not granted are
   not shown.
3. **Archives** and **Settings** (which need `audit/manage`) are refused; **Verify now** is not offered.
4. **Setup > Log files** opens with addresses masked; there is no **Show unmasked** switch.
5. No **Edit** button on content.

Change record: role name, channels granted, group, auditor (function and organization), start and planned end date.

## Related pages
- [Export audit records for an auditor](../operations/export-audit-records-for-an-auditor.md)
- [Give log access without admin rights](give-log-access-without-admin-rights.md)
- [Audit trail](../../../../features/6.0/audit-trail.md#who-may-look)
- [Audit trail verification](../../operations/monitoring/audit-trail-verification.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
