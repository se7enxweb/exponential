---
title: The end of a contract
audience: government, project-manager, admin, partner
category: public-sector
order: 90
summary: What a public body does when a contract for an Exponential site ends - renew, re-tender with the same software, or leave it - and the handover of code, data, keys, documentation and knowledge that each choice needs.
keywords: end of contract, renewal, re-tender, handover, transition, exit, keys, documentation, knowledge transfer, offboarding supplier, public sector
type: help
---
# The end of a contract

This page is for the contract manager of a public body whose contract for an Exponential site, its hosting or its
support is coming to an end. It sets out the three choices and the handover each needs. Detailed exit planning is in
the exit-plans part of this procurement section; this page is the lifecycle view.

> **Note** This page describes features and common practice; it is not legal advice. Check the law that applies to
> your organization with your legal counsel.

## Contents

1. [Three choices](#three-choices)
2. [Start early](#start-early)
3. [What to hand over in every case](#what-to-hand-over-in-every-case)
4. [Re-tender with the same software](#re-tender-with-the-same-software)
5. [Leaving Exponential](#leaving-exponential)
6. [Closing the old supplier's access](#closing-the-old-suppliers-access)

## Three choices

| Choice | What changes | Effort |
|---|---|---|
| Renew with the same supplier | terms only | lowest |
| Re-tender, keep Exponential | the supplier, maybe the host | moderate: a handover |
| Leave Exponential | the system | highest: a migration ([Migrating away from Exponential](../../../../install/migration/leaving-exponential.md)) |

Because Exponential is free software under the GPL, the second choice needs no permission from anyone: a new
supplier can take over the same code ([Licensing questions](../../../project/licensing-faq.md)).

## Start early

A transition takes time: a tender, a handover period, perhaps a hosting move. Start the decision well before the end
date, and check the notice periods in your contract. During the last months, the outgoing supplier still has to
apply security updates ([Security updates and patching cadence](security-updates-and-patching-cadence.md)).

## What to hand over in every case

| Item | Why | Read |
|---|---|---|
| The code: your own extensions, designs and settings, in a repository you control | the new supplier must be able to rebuild the site | [Develop with Git](../../../develop-with-git.md) |
| A full backup: database and files taken together | the data is yours | [Backups: what to back up](../../../hosting/operations/backups-what-to-back-up.md) |
| Keys that cannot be recreated: audit signing keys, the e-mail preferences secret, TLS keys | losing them breaks audit verification and unsubscribe links | [Backups: keys and secrets](../../../hosting/operations/backups-what-to-back-up.md#keys-and-secrets-that-cannot-be-recreated) |
| Administrator access, transferred to named people in your organization | so nobody is locked out | [Change the admin password](../../../change-the-admin-password.md) |
| Documentation: content model, roles, settings, runbooks, deployment | knowledge leaves with people | [Operating a site](../../../../guides/operating-a-site.md) |
| The list of versions in use | the starting point of the next supplier | Setup > System information ([System information](../../../../guides/system-information.md)) |
| Open issues and known risks | nothing should be lost in the change | your ticket system |

Store credentials and keys with the same care as the server: the settings in `settings/override/` contain database
passwords ([Backups: what to back up](../../../hosting/operations/backups-what-to-back-up.md#the-files-to-back-up)).

## Re-tender with the same software

- Give bidders the documentation and an anonymized description of the site; never real personal data.
- Plan an overlap during which both suppliers have access.
- If the host changes too, follow [Moving a site between hosts](../../../hosting/migrations/moving-a-site-between-hosts.md).
- Check after the move: **Setup > Upgrade check** for changed files, the system information health checks, a test
  of every form and of sign-in.

## Leaving Exponential

The exports are documented: an xrowextract site archive per class with a manifest, packages, layouts, the storage
directory, a database dump, form submissions, consent records and the audit log
([Migrating away from Exponential](../../../../install/migration/leaving-exponential.md#1-what-to-export)). Keep the
old site read-only until the new one is signed off, and build a redirect table from the URL aliases so old addresses
keep working ([section 4](../../../../install/migration/leaving-exponential.md#4-addresses-for-the-redirects)).

## Closing the old supplier's access

- Remove or deactivate their accounts; consider **Deactivate and move** where it is enabled
  ([Check user accounts](../../../../features/6.0/user-hygiene.md)).
- Revoke their personal API keys ([Personal API keys](../../../../guides/api-keys.md)) and end their sessions
  (**Setup > Sessions**, [Sessions](../../../../guides/sessions.md)).
- Change shared passwords and server credentials; rotate keys they knew.
- Check the audit trail for their last actions ([Security and audit](../../../../guides/security-and-audit.md)).

The checklist "Offboarding a contractor" in the government checklists section lists these steps.

## Related pages

- [The product lifecycle](README.md)
- [Keeping a site current over a multi-year contract](keeping-a-site-current.md)
- [Customer offboarding (for hosts)](../../../hosting/checklists/customer-offboarding.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
