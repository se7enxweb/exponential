---
title: The right of access
audience: government, admin, project-manager
category: public-sector
order: 150
summary: For public bodies answering a data subject access request about their website - what Exponential can export directly (mail preferences and consent history, audit records), where to collect the rest (account, form submissions, newsletter, orders, logs), and a step-by-step routine with what to review before handing anything out.
keywords: right of access, subject access request, DSAR, GDPR article 15, portability, article 20, export, mail preferences export, consent history, exp:audit export subject-user, form submissions, user account, review, redaction, public sector
type: help
---
# The right of access

This page is for data protection officers and administrators who answer access requests. Exponential has dedicated
exports for mail preferences and audit records; the rest is collected through separate admin tools, because there is
no single "export all my data" function ([Privacy standards: Known gaps](../../../standards/privacy.md#known-gaps)).

![Collected information: what visitors sent through forms, to find for a request for access](../../images/government/gov-forms.webp)

![E-mail preferences: the consent log and the suppression list a request for access may touch](../../images/government/gov-mail-consent.webp)

> **Note** This page describes features and common practice; it is not legal advice. Check the law that applies to
> your organization with your legal counsel.

## Contents

1. [Before you start](#before-you-start)
2. [Dedicated exports](#dedicated-exports)
3. [Collecting the rest](#collecting-the-rest)
4. [Review before you send](#review-before-you-send)
5. [Related pages](#related-pages)

## Before you start

- Verify the person's identity as your procedure says.
- Find the account: Users > Check user accounts, search by name, login or (with `role/assign`) e-mail address. Note the
  user id.
- Work in a private directory, for example `var/tmp/<case reference>/`, and delete it when the case is closed.

## Dedicated exports

| Data | How |
|---|---|
| Mail preferences and consent history | the person can download them on their page; or `./console exp:mail:preferences export --email=<address> --format=json --output=var/tmp/<case>/mail.json --allow-root-user` (written with mode 0600); in the admin, Setup > E-mail preferences > **A user** > **Download as JSON** or **CSV** (`mailpreferences/export`) |
| Audit records by or about the user | `./console exp:audit export --subject-user=<id> --format=jsonl --out=var/tmp/<case>/audit.jsonl --allow-root-user`; includes pseudonymized records |

Sources: [Mail preferences for administrators: Requests](../../../guides/mail-preferences-administrator.md#6-requests-under-data-protection-law),
[Audit guide 4.12](../../../bc/6.0/audit.md#412-privacy-and-gdpr-tasks).

## Collecting the rest

| Data | Where |
|---|---|
| Account fields | the user's node in the content tree, or the account page `/user/edit/<id>` |
| Last visit, number of sign-ins, groups, roles | Users > Check user accounts (CSV export of a filtered list) |
| Form submissions | Setup > Collected information: the forms the person used; export CSV and filter to their rows |
| Newsletter subscriptions (if active) | the newsletter extension's user list and subscriber export |
| Content they created | content search by owner; versions pages show creators |
| Orders (shop) | the shop's order views |
| Logs | rarely needed; masked views in Setup > Log files |

See "What cannot be exported" for what the tools do not cover.

## Review before you send

- The audit export "holds records of other people's actions on that user too" (the guide's warning): remove or redact
  other people's data as your law requires.
- Form exports contain other people's submissions unless filtered.
- Convert to a readable format if the person is not technical; keep JSON for portability requests.
- Record what you sent and when; delete the working directory afterwards.

## Related pages

- [What cannot be exported](what-cannot-be-exported.md)
- [Mail preferences for administrators](../../../guides/mail-preferences-administrator.md)
- [Where personal data lives in Exponential](personal-data-inventory.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
