---
title: Consent records for mail
audience: government, admin, project-manager
category: public-sector
order: 110
summary: For public bodies that send newsletters and notices by e-mail - how Exponential records every consent change (time, category, old and new state, source, exact wording, acting user), how to read, filter and export the consent log, who may, and how long it is kept.
keywords: consent log, records of consent, GDPR article 7(1), expmail_consent_log, mail preferences, wording, source, export CSV, exp:mail:consent, mailpreferences/export, retention, RetentionDays, proof of consent, public sector
type: help
---
# Consent records for mail

This page is for administrators and data protection officers of public bodies that send optional e-mail (newsletters,
alerts, consultations). Where consent is the legal basis, you must be able to show it was given. Exponential keeps a
consent log of every change of a person's mail preferences
([Mail preferences for administrators: The consent log](../../../guides/mail-preferences-administrator.md#8-the-consent-log)).

> **Note** This page describes features and common practice; it is not legal advice. Check the law that applies to
> your organization with your legal counsel.

## Contents

1. [What is recorded](#what-is-recorded)
2. [Reading the log](#reading-the-log)
3. [Exporting](#exporting)
4. [Who may](#who-may)
5. [Retention](#retention)
6. [Related pages](#related-pages)

## What is recorded

Table `expmail_consent_log`, one row per change ([Privacy standards](../../../standards/privacy.md#gdpr)):

| Field | Example |
|---|---|
| time | when the change happened |
| person | address or user |
| category | `newsletter`, `marketing`, `content` ... or the main switch |
| change | `off -> on`, `off -> pending`, `on -> off` |
| source | the person, a confirmation link, an administrator, an import, an unsubscribe |
| wording | the exact text shown when the choice was made |
| acting user, siteaccess, IP address | who made the change and where |

Each change is also an audit event, `access.user.consent.change`. A change an administrator makes for someone is
recorded with the source "Changed by an administrator" and the administrator's user id.

## Reading the log

**Setup > E-mail preferences > Consent log**: newest first, filter by person (address or user id), category, source
and date range; filters are in the address, so a view can be bookmarked. On the console:

```bash
./console exp:mail:consent list --email=<address> --limit=10 --allow-root-user
```

Addresses are hidden in console output unless you add `--addresses`.

## Exporting

**Export as CSV** exports exactly what the filter shows. The CSV holds addresses and IP addresses: store it like any
personal data and delete it when the request it answers is done. Cells starting with `=`, `+`, `-` or `@` get a leading
apostrophe so a spreadsheet does not run them.

## Who may

The policy `mailpreferences/export` is needed to export; give it to few people. Viewing the administration pages needs
`mailpreferences/administrate` ([13.5 The administration siteaccess](../../../install/13-security-hardening.md#135-the-administration-siteaccess)).

## Retention

The cronjob part `mailpreferences` (group `infrequent`) removes consent log rows of people who are gone (an anonymized
record, a removed account, an address with no preference left) once they are older than
`mailpreferences.ini [ConsentSettings] RetentionDays` (1095 days: the account's lifetime plus three years). Preview:
`./console exp:mail:consent cleanup --dry-run --allow-root-user`. Rows of people who still receive mail are kept, as
proof of their current consent.

## Related pages

- [Mail preferences for administrators](../../../guides/mail-preferences-administrator.md)
- [Mail categories and opt-in](mail-categories-and-opt-in.md)
- [The law checklist for mail preferences](../../../specifications/6.0/mail-preferences-compliance.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
