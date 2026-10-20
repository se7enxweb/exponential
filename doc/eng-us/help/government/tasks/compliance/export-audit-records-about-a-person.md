---
title: Export audit records about a person
audience: government, admin
category: public-sector
order: 260
summary: Step by step - for an access request, export every audit record made by or about one user (pseudonymized ones included) with exp:audit export --subject-user, review it for other people's data, and hand it over.
keywords: audit export, subject-user, access request, DSAR, GDPR article 15, pseudonymized, jsonl, csv, review, redaction, exp:audit, public sector
type: help
---
# Export audit records about a person

For administrators answering an access request from a staff member or a registered user. The audit has a dedicated
export for this ([Audit guide, 4.12 Privacy and GDPR tasks](../../../../bc/6.0/audit.md#412-privacy-and-gdpr-tasks)).

> **Note** This page describes features and common practice; it is not legal advice. Check the law that applies to
> your organization with your legal counsel.

## Contents

1. [Before you start](#before-you-start)
2. [Steps](#steps)
3. [Review before you send](#review-before-you-send)
4. [Related pages](#related-pages)

## Before you start

- The person's user id (Users > Check user accounts, or the user's node).
- Shell access; a private working directory `var/tmp/<case>/`.
- Records older than 90 days are found from the index as long as it keeps them (730 days); for full values of older
  archived days, restore them first ("Restore an archived audit day").

## Steps

```bash
./console exp:audit export --subject-user=<id> --format=jsonl --out=var/tmp/<case>/audit-<id>.jsonl --allow-root-user
./console exp:audit export --subject-user=<id> --format=csv --out=var/tmp/<case>/audit-<id>.csv --allow-root-user
```

The command prints the number of records and the sha256. Pseudonymized records are included. The export is itself
recorded (`system.audit.export`).

## Review before you send

- The guide's warning: the file "holds records of other people's actions on that user too" (an administrator who
  changed the account, for example). Remove or redact other people's data as your law requires.
- Explain the fields in a short note (time in UTC, event name, result, truncated network address).
- Records are not erased on request during their retention; say until when they are kept.
- Delete the working files after the hand-over; record what was sent.

## Related pages

- [The right of access](../../privacy/right-of-access.md)
- [Privacy in the audit trail](../../privacy/audit-trail-privacy.md)
- [Answer an access request for mail preferences](answer-an-access-request-for-mail-preferences.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
