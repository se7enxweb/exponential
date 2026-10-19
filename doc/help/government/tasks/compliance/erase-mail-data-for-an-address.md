---
title: Erase mail data for an address
audience: government, admin
category: public-sector
order: 280
summary: Step by step - erase the mail preferences and pending confirmations of an e-mail address and anonymize its consent log with exp:mail:preferences erase, for subscribers without an account, and decide whether to add a legal suppression entry.
keywords: erase, right to erasure, mail preferences, consent log anonymized, exp:mail:preferences erase, newsletter subscriber, suppression list, legal, GDPR article 17, public sector
type: help
---
# Erase mail data for an address

For administrators handling an erasure request from someone who receives mail from the site, often a newsletter
subscriber without a user account. (For an account, removing it erases its mail data automatically; see "Erase a user
account".)

> **Note** This page describes features and common practice; it is not legal advice. Check the law that applies to
> your organization with your legal counsel.

## Contents

1. [Before you start](#before-you-start)
2. [Steps](#steps)
3. [Check that it worked](#check-that-it-worked)
4. [Related pages](#related-pages)

## Before you start

- The request is assessed and the identity of the person (control of the address) verified.
- If the person asked for a copy first, export it ("Answer an access request for mail preferences").
- Shell access.

## Steps

1. Look at what exists: `./console exp:mail:preferences show --email=<address> --allow-root-user`.
2. Erase:

   ```bash
   ./console exp:mail:preferences erase --email=<address> --yes --allow-root-user
   ```

   Without `--yes` the command refuses. It answers with the number of preferences and pending confirmations removed
   and of consent log rows anonymized. With the newsletter extension (cjw_newsletter 4.2), the person's newsletter user,
   interests, SMS data and per-person statistics go through its erasure hook.
3. If the person must never be written to again (and wants that), add a suppression entry; only a hash is stored:

   ```bash
   ./console exp:mail:suppression add --email=<address> --reason=legal --note="<case reference>" --allow-root-user
   ```

## Check that it worked

- `show` reports nothing left for the address.
- `./console exp:mail:consent list --email=<address> --allow-root-user` shows no rows with the address; anonymized rows
  keep time, category and change only, so you can still prove mail stopped.
- Record the case: date, command output, whether a suppression entry was added.

## Related pages

- [The right to erasure](../../privacy/right-to-erasure.md)
- [The suppression list](../../privacy/suppression-list.md)
- [Mail preferences for administrators: Requests](../../../../guides/mail-preferences-administrator.md#6-requests-under-data-protection-law)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
