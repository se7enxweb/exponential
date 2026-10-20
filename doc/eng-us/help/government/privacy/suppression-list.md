---
title: The suppression list
audience: government, admin
category: public-sector
order: 140
summary: For administrators - how Exponential's mail suppression list blocks optional mail to hard bounces, complaints, stop-all requests and legal requests while storing only a salted hash of each address, how to add, check and lift entries, and its retention.
keywords: suppression list, do not mail, hard bounce, complaint, legal request, salted hash, exp:mail:suppression, bounce reader, blacklist, newsletter, retention, stop all mail, public sector
type: help
---
# The suppression list

This page is for administrators who handle mail requests. No optional mail goes to an address on the suppression list:
hard bounces, complaints (marked as spam), "stop all optional e-mail" requests, legal requests, entries made by an
administrator, and the newsletter blacklist. Essential mail still goes
([Mail preferences for administrators: The suppression list](../../../guides/mail-preferences-administrator.md#7-the-suppression-list)).

> **Note** This page describes features and common practice; it is not legal advice. Check the law that applies to
> your organization with your legal counsel.

## Contents

1. [Privacy by design](#privacy-by-design)
2. [Managing entries](#managing-entries)
3. [Bounces](#bounces)
4. [Retention](#retention)
5. [Related pages](#related-pages)

## Privacy by design

Only a salted hash of each address is stored, so the list cannot be read; you find an entry by typing the address. This
lets a public body honor "never write to me again" without keeping a readable list of the people who asked.

## Managing entries

**Setup > E-mail preferences > Suppression list**: **Check an address**, **Add an address** (with a reason and a note
without personal data), **Lift** an entry. On the console:

```bash
./console exp:mail:suppression check --email=<address> --allow-root-user
./console exp:mail:suppression add --email=<address> --reason=legal --note="<case reference, no personal data>" --allow-root-user
./console exp:mail:suppression list --reason=legal --limit=5 --allow-root-user
./console exp:mail:suppression lift --email=<address> --allow-root-user
```

A person who stopped all mail themselves lifts their own entry by turning optional mail on again. Entries for bounces,
complaints and legal requests stay until an administrator lifts them, and they also stop "send me a link" and
confirmation mails.

## Bounces

The bounce reader reads a bounce mailbox set in `[BounceSettings]` and adds hard bounces automatically; it does nothing
until its mailbox is filled in, and the status page shows its last read. Without it, add hard bounces by hand with
reason `bounce`. The newsletter extension (cjw_newsletter 4.2) uses the same reader.

## Retention

Entries are kept forever by default (`[SuppressionSettings] RetentionDays=0`), because forgetting an entry would mean
writing again to someone who asked not to be written to. If your schedule needs a limit, set `RetentionDays`; the
cronjob part `mailpreferences` applies it.

## Related pages

- [Mail preferences for administrators](../../../guides/mail-preferences-administrator.md)
- [One-click unsubscribe](one-click-unsubscribe.md)
- [The right to erasure](right-to-erasure.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
