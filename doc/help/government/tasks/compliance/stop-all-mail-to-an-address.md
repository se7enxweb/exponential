---
title: Stop all optional mail to an address
audience: government, admin
category: public-sector
order: 290
summary: Step by step - honor a "stop writing to me" request in Exponential with a suppression list entry (reason legal), which blocks every optional mail and confirmation to that address while storing only a hash, and check it.
keywords: stop mail, do not contact, suppression list, exp:mail:suppression add, reason legal, objection, opt out, unsubscribe all, essential mail, hash, public sector
type: help
---
# Stop all optional mail to an address

For administrators when someone asks by phone, letter or mail to receive nothing more. The suppression list blocks
optional mail, confirmations and "send me a link" mails to the address; essential mail (password reset, orders) still
goes ([The suppression list](../../privacy/suppression-list.md)).

## Contents

1. [Steps](#steps)
2. [Check that it worked](#check-that-it-worked)
3. [Undo](#undo)
4. [Related pages](#related-pages)

## Steps

In the administration: **Setup > E-mail preferences** > **Suppression list** > **Add an address**, reason legal, a note
without personal data (a case reference).

Or on the console:

```bash
./console exp:mail:suppression add --email=<address> --reason=legal --note="<case reference>" --allow-root-user
```

If the person also wants their data erased, see "Erase mail data for an address". If the newsletter extension keeps
its own blacklist, it is part of the suppression list as the guide describes.

## Check that it worked

```bash
./console exp:mail:suppression check --email=<address> --allow-root-user
```

answers `suppressed: legal`. Entries are kept until an administrator lifts them (`[SuppressionSettings]
RetentionDays=0` by default).

## Undo

Only on the person's request: **Lift** on the suppression list page, or
`./console exp:mail:suppression lift --email=<address> --allow-root-user`.

## Related pages

- [The suppression list](../../privacy/suppression-list.md)
- [One-click unsubscribe](../../privacy/one-click-unsubscribe.md)
- [Mail preferences for administrators: The suppression list](../../../../guides/mail-preferences-administrator.md#7-the-suppression-list)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
