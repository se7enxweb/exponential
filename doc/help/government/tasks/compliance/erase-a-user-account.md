---
title: Erase a user account
audience: government, admin
category: public-sector
order: 270
summary: Step by step - remove a user account for good after an erasure decision, knowing that users never go to the trash, that removal erases the account's mail preferences and anonymizes its consent log, and what to remove by hand before and after.
keywords: erase user, delete account, right to erasure, remove user, no trash, eraseForRemovedAccount, mail preferences, consent log, form submissions, UserCreatorID, content ownership, audit, public sector
type: help
---
# Erase a user account

For administrators, after an erasure request has been assessed and approved. Removing a user object in Exponential is
**final**: users never go to the trash. If the account only needs to stop working, disable it or use Deactivate and move
instead ([The right to erasure](../../privacy/right-to-erasure.md)).

> **Note** This page describes features and common practice; it is not legal advice. Check the law that applies to
> your organization with your legal counsel.

## Contents

1. [Before you start](#before-you-start)
2. [Steps](#steps)
3. [Check that it worked](#check-that-it-worked)
4. [What stays](#what-stays)
5. [Related pages](#related-pages)

## Before you start

- The decision and its date are recorded.
- The account is not the anonymous user, `UserCreatorID` or your own; protected accounts are refused by the hygiene
  tools on purpose.
- Content the user created stays and keeps the user as owner reference; decide whether to change owners first
  ([Changing owners](../../../../guides/changing-owners.md)).
- Exported what the person asked for (access requests come first).

## Steps

1. Remove the person's **form submissions**: Setup > Collected information, open each form they used, tick their
   collections, **Remove selected**.
2. Find mentions in content (names, photos) with the editors and handle them per the decision.
3. Open the user's node in the administration (Users) and **Remove** it; confirm. One account at a time.
4. If the person also had mail preferences under another address without an account:
   `./console exp:mail:preferences erase --email=<address> --yes --allow-root-user`.
5. Reset search statistics if their name appears there (Setup > Search statistics).

## Check that it worked

- The account no longer appears in Users > Check user accounts.
- `./console exp:mail:preferences show --email=<address> --allow-root-user` shows no preferences for the address.
- The consent log rows of the person are anonymized (no address, IP or user).

## What stays

- Audit records about the account, until their retention ends (deleting one would break the chain); pseudonymized after
  90 days in the index.
- Logs and backups, until their retention and rotation end.

## Related pages

- [The right to erasure](../../privacy/right-to-erasure.md)
- [Check user accounts](../../../../features/6.0/user-hygiene.md)
- [Erase mail data for an address](erase-mail-data-for-an-address.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
