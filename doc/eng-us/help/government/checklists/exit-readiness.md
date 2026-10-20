---
title: Exit readiness
audience: government, project-manager, admin, hosting
category: public-sector
order: 230
summary: A checklist that keeps a public body ready to leave its supplier, its host or Exponential itself at any time - code and access you control, documented exports tested, keys and backups in your hands, contract terms and a transition plan.
keywords: exit, exit readiness, lock-in, export, xrowextract, database dump, handover, keys, repository, transition, supplier change, public sector
type: help
---
# Exit readiness

This checklist is for the contract manager and site administrator of a public body. Being able to leave is what keeps
a supplier relationship fair. Exponential's license and exports make leaving possible; this checklist keeps it
practical. The export tools come from [Migrating away from Exponential](../../../install/migration/leaving-exponential.md).

![Backups: a full site backup or the database and files, the base of an exit plan](../../images/government/gov-backups.webp)

> **Note** This page describes features and common practice; it is not legal advice. Check your contract with your
> legal counsel.

## Contents

1. [What you control](#what-you-control)
2. [Exports tested](#exports-tested)
3. [Keys and backups](#keys-and-backups)
4. [Contract](#contract)
5. [Plan](#plan)

## What you control

- [ ] The software is GPL; you may give it to another supplier without permission ([Licensing questions](../../project/licensing-faq.md)).
- [ ] Your custom extensions, designs and settings are in a repository your organization owns ([Develop with Git](../../develop-with-git.md)).
- [ ] Named staff of your organization have administrator accounts on the site and access to the hosting.
- [ ] Documentation of the content model, roles, settings and deployment is in your hands.
- [ ] No kernel files changed in place; **Setup > Upgrade check** confirms it ([The upgrade check](../../../guides/upgrade-check.md)).

## Exports tested

Run an export test at least once a year, on a copy:

- [ ] Site archive of every class: `./console ext:xrowextract:archive --set=everything --format=zip --output=<dir>`; manifest opened, row counts compared with the admin's counts ([Migrating away from Exponential](../../../install/migration/leaving-exponential.md#2-the-complete-export-step-by-step)).
- [ ] The storage directory copied with it.
- [ ] A database dump with the engine's tool ([Database backups per engine](../../hosting/operations/database-backups-per-engine.md)).
- [ ] Layouts, if used: `./console exp:layouts export --all --output=<file.zip>`.
- [ ] Form submissions, consent records and the audit log exported where needed.
- [ ] Redirect list from URL aliases and wildcards ([section 4](../../../install/migration/leaving-exponential.md#4-addresses-for-the-redirects)).
- [ ] Note: the site archive never contains password hashes; users set new passwords on a new system unless it can verify standard PHP hashes ([section 5](../../../install/migration/leaving-exponential.md#5-users-and-personal-data)).

## Keys and backups

- [ ] Backups reach a place your organization controls, not only the host's ([Backup readiness](backup-readiness.md)).
- [ ] Keys that cannot be recreated are in your hands: audit signing keys, the e-mail preferences secret, TLS keys ([Keys and secrets](../../hosting/operations/backups-what-to-back-up.md#keys-and-secrets-that-cannot-be-recreated)).
- [ ] Domain names and DNS registered to your organization.

## Contract

- [ ] Exit clauses: handover period, cooperation with a successor, data return and deletion ([Exit plans](../procurement/exit-plans/README.md)).
- [ ] Notice periods and end dates known ([The end of a contract](../procurement/lifecycle/end-of-a-contract.md)).
- [ ] No clause that claims ownership of the code you paid for, or forbids giving it to another supplier.

## Plan

- [ ] A written exit plan, updated every year ([Annual review](annual-review.md)).
- [ ] For a change of host only: [Moving a site between hosts](../../hosting/migrations/moving-a-site-between-hosts.md).
- [ ] For a change of system: the target's import route chosen ([Migrating away from Exponential](../../../install/migration/leaving-exponential.md)).
- [ ] The old site kept read-only until the new one is signed off.
- [ ] Contractor access closed at the end ([Offboarding a contractor](offboarding-a-contractor.md)).

## Related pages

- [Government checklists](README.md)
- [Before signing a contract](before-signing.md)
- [Leaving Exponential (trainer lesson)](../../trainers/migration/module-08-leaving-and-capstone/leaving-exponential.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
