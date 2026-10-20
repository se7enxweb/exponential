---
title: "FAQ: exit and lock-in"
audience: government, project-manager, admin
category: public-sector
order: 200
summary: Answers to the lock-in questions public bodies ask about Exponential - can we change supplier, can we leave the product, what can be exported and in which formats, what happens to users and addresses, and where lock-in really comes from.
keywords: lock-in FAQ, exit, vendor lock-in, change supplier, export, data portability, xrowextract, CSV, JSON, database dump, redirects, passwords, public sector
type: help
---
# FAQ: exit and lock-in

This page answers the lock-in questions public bodies ask about Exponential. The export facts come from
[Migrating away from Exponential](../../../install/migration/leaving-exponential.md).

> **Note** This page describes features and common practice; it is not legal advice. Check your contract with your
> legal counsel.

## Contents

1. [Can we change supplier and keep Exponential?](#can-we-change-supplier-and-keep-exponential)
2. [Can we leave Exponential altogether?](#can-we-leave-exponential-altogether)
3. [What can be exported, in which formats?](#what-can-be-exported-in-which-formats)
4. [What happens to user passwords?](#what-happens-to-user-passwords)
5. [Will old addresses keep working?](#will-old-addresses-keep-working)
6. [Where does lock-in really come from?](#where-does-lock-in-really-come-from)

## Can we change supplier and keep Exponential?

Yes. The software is GPL; a new supplier can take over the same code without permission from anyone
([Licensing questions](../../project/licensing-faq.md)). What a handover needs is listed in
[The end of a contract](../procurement/lifecycle/end-of-a-contract.md).

## Can we leave Exponential altogether?

Yes. "Your content is yours": it is stored in an open database schema and ordinary files, with tools that write it out
in plain formats ([Migrating away from Exponential](../../../install/migration/leaving-exponential.md)). Guides exist
for several target systems.

## What can be exported, in which formats?

| Data | Format |
|---|---|
| All content, per class, self-describing | CSV (or XML, JSON) with `manifest.json`, in a ZIP or TAR archive |
| Classes and content for another Exponential | `.ezpkg` packages |
| Layouts | ZIP transfer file |
| Images and files | the original files from the storage directory |
| The whole database | neutral schema format or SQL |
| Form submissions, consent records | CSV |
| Content over HTTP | JSON through REST |

Source: [Migrating away from Exponential, section 1](../../../install/migration/leaving-exponential.md#1-what-to-export).

## What happens to user passwords?

The site archive never contains password hashes; users set new passwords on the target, unless the target can verify
standard PHP `password_hash()` values, which a database export can supply. Decide with your data protection advisor
([section 5](../../../install/migration/leaving-exponential.md#5-users-and-personal-data)).

## Will old addresses keep working?

If you build a redirect table from the URL aliases and wildcards and install it on the new system before switching
([section 4](../../../install/migration/leaving-exponential.md#4-addresses-for-the-redirects)).

## Where does lock-in really come from?

Not from the license. It comes from contracts that keep code or data from you, custom code nobody else knows,
documentation that stays with the supplier, credentials and keys you do not hold, and skills you did not build. The
[Exit readiness](../checklists/exit-readiness.md) checklist addresses each.

## Related pages

- [Government FAQ](README.md)
- [Exit readiness](../checklists/exit-readiness.md)
- [Leaving Exponential (trainer lesson)](../../trainers/migration/module-08-leaving-and-capstone/leaving-exponential.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
