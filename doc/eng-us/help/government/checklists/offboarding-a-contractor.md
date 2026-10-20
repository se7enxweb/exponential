---
title: Offboarding a contractor
audience: government, admin, project-manager, hosting
category: public-sector
order: 130
summary: How a public body removes a contractor's or supplier's access from an Exponential site and its servers when their work ends - accounts, roles, API keys, sessions, server credentials, keys and secrets, repositories - and keeps what they handed over.
keywords: offboarding, contractor, supplier, leaver, access removal, accounts, API keys, sessions, credentials, password rotation, audit, handover, public sector
type: help
---
# Offboarding a contractor

This checklist is for the site administrator and contract manager of a public body when a contractor, agency or
individual consultant finishes work on the site. Do it on the last day, not weeks later.

## Contents

1. [Before the last day](#before-the-last-day)
2. [In Exponential](#in-exponential)
3. [Servers and services](#servers-and-services)
4. [Check and record](#check-and-record)

## Before the last day

- [ ] List every access the contractor had: Exponential accounts, server logins, database users, repositories, hosting panel, DNS, monitoring, mail.
- [ ] Collect the handover: code in your repository, documentation, open issues, credentials they created ([The end of a contract](../procurement/lifecycle/end-of-a-contract.md#what-to-hand-over-in-every-case)).
- [ ] Make sure named staff of your organization have administrator access before the contractor's is removed.

## In Exponential

- [ ] Deactivate or remove the contractor's accounts; with **Deactivate and move** enabled, accounts move to a hidden group without roles and can be restored ([Check user accounts](../../../features/6.0/user-hygiene.md)).
- [ ] Content they own can be given to a staff member ([Changing the owner of content](../../../guides/changing-owners.md)).
- [ ] Remove their role assignments; check roles they may have created ([Roles and policies](../../../guides/roles-and-policies.md)).
- [ ] Revoke their personal API keys ([Personal API keys](../../../guides/api-keys.md)) and any OAuth clients (**Setup > oAuth admin**).
- [ ] Sign out their sessions in **Setup > Sessions** ([Sessions](../../../guides/sessions.md)).
- [ ] Their drafts and pending items reassigned or removed ([My drafts and my pending items](../../../guides/drafts-and-pending.md)).
- [ ] Workflows where they were the approver updated ([Workflows](../../../guides/workflows.md#102-clean-up-after-a-removed-approver)).
- [ ] Remove their addresses from maintenance-mode allow lists and debug output lists ([Maintenance mode](../../../guides/maintenance-mode.md)).

## Servers and services

- [ ] Remove their SSH keys and server accounts ([Per-customer users and pools](../../hosting/security/per-customer-users-and-pools.md)).
- [ ] Change passwords they knew: database, hosting panel, shared mailboxes.
- [ ] Rotate secrets they had access to, after checking what each one breaks: rotating the e-mail preferences secret invalidates every unsubscribe link already sent ([Backups: keys and secrets](../../hosting/operations/backups-what-to-back-up.md#keys-and-secrets-that-cannot-be-recreated)).
- [ ] Remove their access to repositories, monitoring and DNS.
- [ ] Remove their e-mail from alert recipients: `./console exp:audit alerts recipients` ([Security and audit](../../../guides/security-and-audit.md)).

## Check and record

- [ ] Review their last actions in the audit trail (`/audit/console`).
- [ ] Try signing in with their old account from a test browser: it must fail.
- [ ] Record what was removed, by whom, when.
- [ ] Confirm in writing that the contractor returned or deleted any copies of your data, as the contract requires.

## Related pages

- [Government checklists](README.md)
- [Quarterly access review](quarterly-access-review.md)
- [Customer offboarding (for hosts)](../../hosting/checklists/customer-offboarding.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
