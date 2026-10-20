---
title: Records of processing for a website
audience: government, project-manager, admin
category: public-sector
order: 280
summary: For public bodies keeping a record of processing activities - Exponential does not keep it for you; this page gives the typical processing activities of an Exponential site with the facts each entry needs (data, recipients, retention, security measures) taken from the product.
keywords: record of processing activities, ROPA, GDPR article 30, processing activities, website, user accounts, forms, newsletter, audit trail, logs, retention, recipients, security measures, documentation, public sector
type: help
---
# Records of processing for a website

This page is for data protection officers and project managers. Keeping the record of processing activities (GDPR art.
30) is the operator's job; the project's statement says so explicitly ([Privacy standards: Status](../../../standards/privacy.md#status)).
Exponential cannot write your record, but it can supply the facts for it. Below, the processing activities a typical
public site has, with the facts from the product.

> **Note** This page describes features and common practice; it is not legal advice. Check the law that applies to
> your organization with your legal counsel.

## Contents

1. [Typical activities](#typical-activities)
2. [Facts per activity](#facts-per-activity)
3. [Security measures to cite](#security-measures-to-cite)
4. [Related pages](#related-pages)

## Typical activities

1. Publishing information (content may name staff or citizens).
2. Staff accounts for editing (authentication, roles).
3. Public user accounts (registration, member areas), if any.
4. Contact and request forms.
5. Newsletters and alerts by e-mail.
6. Security logging and audit.
7. Operational logs and statistics.
8. Optional third-party services (analytics, CAPTCHA, maps, AI).

## Facts per activity

| Activity | Data | Where (see the inventory) | Default retention | Recipients |
|---|---|---|---|---|
| Staff accounts | name, login, e-mail, visits, roles | user objects, `ezuservisit` | none: review with Check user accounts | hosting processor |
| Forms | what visitors type | information collections; receiver mailbox | none: manual removal | form owners; mail provider |
| E-mail | address, choices, consent history with IP | mail preferences, consent log, suppression hashes | consent log 1095 days for people who are gone | mail server; newsletter extension |
| Audit | actors, truncated addresses, actions | audit files and index | 90 days live, 730 archived; pseudonymized after 90 | SIEM if forwarded |
| Request logs | masked addresses, user agents, URLs | `404.log`, `security.log` | 30 and 90 days | host |
| Search statistics | phrases | `ezsearch_search_phrase` (no user or address) | until reset; off by default | none |
| Third-party services | depends on the service | the provider | the provider's | the provider |

Fill in purpose and legal basis yourself; they depend on your tasks and law.

## Security measures to cite

From the security section of this help: password hashing (bcrypt), optional two-step sign-in, roles with least
privilege, CSRF form tokens, session cookie flags, security headers, Content Security Policy, tamper-evident audit trail
with alerts, masked logs, HTTPS and HSTS, backups. Name only what you have actually configured.

## Related pages

- [Where personal data lives in Exponential](personal-data-inventory.md)
- [Retention of personal data: an overview](retention-overview.md)
- [Mapping the GDPR to Exponential](gdpr-mapping.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
