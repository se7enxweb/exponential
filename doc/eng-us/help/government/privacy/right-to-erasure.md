---
title: The right to erasure
audience: government, admin, project-manager
category: public-sector
order: 170
summary: For public bodies answering erasure requests - what removing a user account in Exponential erases automatically (mail preferences, consent log anonymized, newsletter data through the erasure hooks), what has to be removed by hand (form submissions, content, search phrases), what is kept on purpose (audit records), and the steps.
keywords: right to erasure, right to be forgotten, GDPR article 17, delete user, eraseForRemovedAccount, exp:mail:preferences erase, consent log anonymized, audit records kept, article 17(3), form submissions, trash, backups, public sector
type: help
---
# The right to erasure

This page is for data protection officers and administrators. Public bodies often have grounds to keep data (a legal
obligation, a public task, archiving); erasure requests need a legal assessment first. When data must go, Exponential
erases some of it automatically and leaves the rest to separate tools
([Privacy standards: GDPR](../../../standards/privacy.md#gdpr)).

> **Note** This page describes features and common practice; it is not legal advice. Check the law that applies to
> your organization with your legal counsel.

## Contents

1. [What removing an account does](#what-removing-an-account-does)
2. [Erasing mail data without an account](#erasing-mail-data-without-an-account)
3. [What you remove by hand](#what-you-remove-by-hand)
4. [What is kept, and why](#what-is-kept-and-why)
5. [Steps](#steps)
6. [Related pages](#related-pages)

## What removing an account does

- Removing a user account is **final**: a user object never goes to the trash (`eZContentObjectTreeNode::isNodeTrashAllowed()`),
  and it is done one account at a time with the node's own **Remove**
  ([Check user accounts](../../../features/6.0/user-hygiene.md)).
- `eZUser::removeUser()` calls `expMailPreferences::eraseForRemovedAccount()`
  (`kernel/classes/datatypes/ezuser/ezuser.php`): mail preferences and pending confirmations are removed and the consent
  log is anonymized. (An older sentence in the administrator's guide says this does not happen yet; the code does it.)
- The newsletter extension (cjw_newsletter 4.2) hooks into the erasure: an erased person's newsletter user, interests,
  SMS data and per-person statistics go ([cjw_newsletter](../../../features/6.0/extensions/cjw_newsletter.md)).

## Erasing mail data without an account

For an address without an account (a newsletter subscriber):

```bash
./console exp:mail:preferences erase --email=<address> --yes --allow-root-user
```

It answers with the number of preferences and confirmations removed and consent log rows anonymized. The anonymized
rows keep time, category and change, without address, IP or user, so you can still prove that mail stopped. Without
`--yes` the command refuses. If the person must never be written to again, add the address to the suppression list
with reason `legal` (only a hash is stored).

## What you remove by hand

| Data | Tool |
|---|---|
| Form submissions | Setup > Collected information: open the form, tick the person's collections, remove |
| Content the person appears in | editors; then empty the trash for those items, and remove old versions if needed |
| Search phrases containing the name | Setup > Search statistics, Reset statistics |
| Shop orders | the shop tools, subject to accounting retention |
| Exports made earlier | delete the files |

## What is kept, and why

- **Audit records** are kept for their retention period: deleting one would break the hash chain. The audit guide
  refers to art. 17(3)(b) and (e); answer with the retention period and the pseudonymization already applied
  ([Audit guide 4.12](../../../bc/6.0/audit.md#412-privacy-and-gdpr-tasks)).
- **Logs** age out with their retention (see "Log retention").
- **Backups** age out with your backup rotation; most organizations do not edit backups but make sure a restore does
  not bring erased data back into use.

## Steps

1. Assess the request (grounds to keep?).
2. Export what the person may want first, if they asked for it.
3. Remove form submissions and content mentions.
4. Remove the account (node, Remove) or, if the account must be retired but its history kept, see
   "Restriction and Deactivate and move".
5. Erase mail data for addresses without an account; add a legal suppression entry if asked.
6. Record what you did, with dates, and answer the person, mentioning what is kept and until when.

## Related pages

- [Mail preferences for administrators: Requests](../../../guides/mail-preferences-administrator.md#6-requests-under-data-protection-law)
- [Restriction and Deactivate and move](restriction-and-deactivate-and-move.md)
- [The suppression list](suppression-list.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
