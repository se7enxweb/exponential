---
title: Mapping requirements to evidence
audience: government, project-manager, partner, sales-marketing
category: public-sector
order: 150
summary: A reference table that maps common public-sector CMS requirements to the exact Exponential pages, settings and tests that evidence them, with the documented status, for evaluators who check answers and bidders who write them.
keywords: evidence, mapping, compliance matrix, requirements traceability, standards, WCAG, OWASP, GDPR, i18n, status, Complies, Partly complies, Unverified, evaluation, bid response
type: reference
---
# Mapping requirements to evidence

This page is for evaluators who check a bid and for bidders who answer a tender with Exponential. It maps the
requirement areas of this folder to the evidence the repository actually holds: a documentation page, a setting, a
test, a command. The status column copies the status words of the [standards pages](../../../../standards/README.md)
exactly; do not upgrade them in a bid.

## Contents
1. [How to use this table](#how-to-use-this-table)
2. [Accessibility](#accessibility)
3. [Security](#security)
4. [Privacy](#privacy)
5. [Languages and web standards](#languages-and-web-standards)
6. [Operations, records and exit](#operations-records-and-exit)
7. [Release and quality practice](#release-and-quality-practice)

## How to use this table

- **Bidders**: cite the page and section, and quote the status as written. Where the status is "Partly complies" or
  "Unverified", say what you will do about the gap for this customer.
- **Evaluators**: open the link and check that it says what the bid claims. Settings can be checked in a test
  installation made with `./console exp:install` ([Installing Exponential in one command](../../../../features/6.0/install-in-one-command.md)).

## Accessibility

| Requirement area | Evidence | Status |
|---|---|---|
| WCAG 2.2 AA, administration | [Accessibility standards](../../../../standards/accessibility.md); `tests/tests/design/` (labels, landmarks, contrast, keyboard) | Partly complies |
| WCAG 2.2 AA, public site | none for shipped public designs | not audited |
| WAI-ARIA 1.2 | same page, admin4 patterns | Partly complies |
| EN 301 549 | same page | Unverified |
| Section 508 / VPAT | same page; no VPAT or ACR | Unverified |
| Right-to-left | [Right-to-left languages](../../../../features/6.0/right-to-left.md) | documented |

## Security

| Requirement area | Evidence | Status |
|---|---|---|
| OWASP Top 10 (2021) | [Security standards](../../../../standards/security.md), table A01 to A10 | Partly complies |
| OWASP ASVS 4.0.3 | same page | Unverified (no assessment made) |
| Content Security Policy | [Content Security Policy](../../../../features/6.0/content-security-policy.md); `settings/site.ini [ContentSecurityPolicy]` | Partly complies |
| Security logging | [Audit trail](../../../../features/6.0/audit-trail.md); `settings/audit.ini [AuditSettings] Audit=enabled` | Complies (A09) |
| Two-step sign-in | [Two-step sign-in](../../../../guides/two-factor-login.md) | opt-in extension |
| Hardening steps | [Security hardening for production](../../../../install/13-security-hardening.md), [Security hardening](../../../security-hardening.md) | documented |
| Vulnerability disclosure | [SECURITY.md](https://github.com/se7enxweb/exponential/blob/main/SECURITY.md) | Partly complies (no security.txt) |

## Privacy

| Requirement area | Evidence | Status |
|---|---|---|
| GDPR features | [Privacy standards](../../../../standards/privacy.md) | Partly complies (features provided) |
| Cookies (ePrivacy) | same page, cookie table | Partly complies (no consent banner) |
| Consent records, unsubscribe | [E-mail preferences](../../../../features/6.0/mail-preferences.md) | CAN-SPAM Complies; RFC 8058 Complies |
| Log minimization | [Log viewer](../../../../features/6.0/log-viewer.md), [Request logs](../../../../features/6.0/request-logs.md) | documented |
| Account clean-up | [Check user accounts](../../../../features/6.0/user-hygiene.md) | documented |

## Languages and web standards

| Requirement area | Evidence | Status |
|---|---|---|
| UTF-8 | [Internationalization standards](../../../../standards/internationalization.md) | Complies |
| 4-byte UTF-8 on MySQL/MariaDB | same page | Partly complies |
| Plural forms | same page | Does not comply |
| HTTP, HTML, CSS, cookies, robots | [Web platform standards](../../../../standards/web-platform.md) | per row of that page |
| APIs (REST, OAuth, LDAP, RSS, SMTP) | [APIs and protocols standards](../../../../standards/apis-and-protocols.md) | per row of that page |
| PDF accessibility (PDF/UA) | [Documentation and publishing standards](../../../../standards/documentation-and-publishing.md) | Does not comply |

## Operations, records and exit

| Requirement area | Evidence |
|---|---|
| Backups | [Backups: what to back up](../../../hosting/operations/backups-what-to-back-up.md), [10.9 Backups and restore](../../../../install/10-after-installing.md#109-backups-and-restore) |
| Maintenance windows | [Maintenance mode](../../../../guides/maintenance-mode.md) |
| Upgrade checks | [The upgrade check](../../../../guides/upgrade-check.md) |
| Version history | [Versions of an object](../../../../guides/content-history.md) |
| Exports | [Migrating away from Exponential](../../../../install/migration/leaving-exponential.md) |
| Databases | [Databases and SQL standards](../../../../standards/databases-and-sql.md), [Database support](../../../database-support.md) |

## Release and quality practice

| Requirement area | Evidence | Status |
|---|---|---|
| Permanent release tags | [Source control and releases](../../../../standards/source-control-and-releases.md) | Complies (by policy) |
| Signed commits or tags | same page | Does not comply |
| Automated tests and CI | [Testing and continuous integration](../../../../standards/testing-and-ci.md) | Complies |
| Code coverage reporting | same page | Does not comply |

## Related pages
- [Requirements for a public-sector CMS](README.md)
- [What Exponential can and cannot evidence](what-exponential-can-and-cannot-evidence.md)
- [How to write verifiable requirements](how-to-write-verifiable-requirements.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
