---
title: Privacy and data protection requirements
audience: government, project-manager, admin, partner
category: public-sector
order: 60
summary: Requirement wording for the privacy part of a CMS tender - data minimization, consent records, data subject requests, retention, cookies, third parties and processing agreements - with what Exponential provides and what stays with your organization.
keywords: privacy, data protection, GDPR, requirements, consent, erasure, right of access, retention, cookies, ePrivacy, processor, data processing agreement, DPA, pseudonymization, logs, tender
type: help
---
# Privacy and data protection requirements

This page is for data protection officers, procurement teams and project managers who write the privacy part of a
CMS tender. It gives requirement wording and maps each requirement to what
[Privacy standards](../../../../standards/privacy.md) documents for Exponential. Software provides features; your
organization stays the controller and remains responsible for purposes, legal bases, notices and requests.

> **Note** This page describes features and common practice; it is not legal advice. Check the law that applies to
> your organization with your legal counsel.

## Contents
1. [Background](#background)
2. [Requirement wording](#requirement-wording)
3. [What Exponential provides](#what-exponential-provides)
4. [What stays with your organization or the supplier](#what-stays-with-your-organization-or-the-supplier)
5. [Pitfalls](#pitfalls)

## Background

- **In the EU**, the [GDPR](https://eur-lex.europa.eu/eli/reg/2016/679/oj) governs personal data, including data
  protection by design and by default (art. 25) and security of processing (art. 32). The
  [ePrivacy Directive](https://eur-lex.europa.eu/eli/dir/2002/58/oj) governs cookies and marketing e-mail.
- **Elsewhere**, national privacy laws apply; the requirement areas below are common to most of them.
- A supplier who hosts or supports the site usually acts as a **processor** and needs a data processing agreement.

## Requirement wording

| Id | Requirement |
|---|---|
| PRI-01 | The system minimizes personal data in logs: IP addresses truncated or masked, no passwords, tokens or form contents written. Describe the defaults. |
| PRI-02 | Logs and audit records have configurable retention periods and are removed automatically. State the defaults. |
| PRI-03 | Consent for optional e-mail is opt-in per purpose, recorded with time and the text shown, and can be withdrawn as easily as given. |
| PRI-04 | The organization can answer an access request: export the data held about a person in a machine-readable format. Describe which data each tool covers. |
| PRI-05 | The organization can erase a person's account and the data linked to it; describe what is erased, anonymized or kept. |
| PRI-06 | The administration loads no scripts, fonts or other resources from third-party hosts. |
| PRI-07 | The public site sets only strictly necessary cookies unless the organization adds a service with consent. List every cookie. |
| PRI-08 | Every third-party service the delivered site uses is listed with its purpose and the data it receives. |
| PRI-09 | The supplier signs a data processing agreement and lists its sub-processors and data locations. |

## What Exponential provides

| Id | Documented feature | Gap |
|---|---|---|
| PRI-01 | Audit: addresses truncated (/24, IPv6 /48), session ids hashed, no passwords or POST bodies; request logs `MaskIP=enabled`; log viewer masks IPs, e-mail addresses and secrets ([Log viewer](../../../../features/6.0/log-viewer.md)) | Web server access logs are outside Exponential |
| PRI-02 | Audit kept 90 days live and 2 years archived per channel, pseudonymized after a set period; 404, security and CSP logs have `MaxAgeDays` ([Log rotation](../../../../features/6.0/log-rotation.md)) | Kernel logs such as `error.log` rotate by size, with no age limit by default |
| PRI-03 | E-mail categories off until turned on, consent log table `expmail_consent_log`, one-click unsubscribe ([E-mail preferences](../../../../features/6.0/mail-preferences.md)) | Commercial mail sent without a category gets no footer |
| PRI-04 | Mail preferences and consent history as JSON or CSV; audit records about a user with `./console exp:audit export --subject-user=<id>` | **No single "export all my data"** function |
| PRI-05 | Removing a user erases mail preferences and anonymizes the consent log; "Deactivate and move" for restriction; **Check user accounts** finds unused accounts ([Check user accounts](../../../../features/6.0/user-hygiene.md)) | Removal is manual, one account at a time; form submissions are reached separately |
| PRI-06 | Admin scripts served locally; admin4 loads no external fonts or scripts | - |
| PRI-07 | Kernel sets only functional cookies (session, `is_logged_in`, UI state in the administration) | **No cookie consent banner** in kernel or shipped designs |
| PRI-08 | Extensions that contact third parties (analytics, reCAPTCHA, hCaptcha, social login) are installed but not active by default | Whether a site package activates one: check `ActiveExtensions[]` |
| PRI-09 | - | A service matter for the supplier; see [Hosting: compliance and GDPR](../../../hosting/security/compliance-and-gdpr.md) |

## What stays with your organization or the supplier

- The record of processing activities, the privacy notice and the legal bases.
- A cookie consent solution, if the site adds analytics, embedded media or other third-party services.
- Retention rules for information collection forms; the site must add a consent text and a retention rule itself
  ([Collected information](../../../../guides/collected-information.md)).
- Search phrases are stored and can contain personal data ([URLs and aliases](../../../../guides/urls-and-aliases.md)).
- Backups contain personal data and credentials; their retention and encryption belong in the hosting contract
  ([Backups: what to back up](../../../hosting/operations/backups-what-to-back-up.md)).

## Pitfalls

- Asking the software to "be GDPR compliant". A product cannot be; ask for the features above and score them.
- Forgetting the third-party extensions a supplier adds during the build. Ask for the final `ActiveExtensions[]` list
  as a deliverable.
- Treating newsletter statistics as harmless: per-person open and click counting needs prior consent
  ([Privacy standards](../../../../standards/privacy.md)).

## Related pages
- [Requirements for a public-sector CMS](README.md)
- [Tender template: privacy and data processing clause](../tender-templates/privacy-and-data-processing-clause.md)
- [Records and audit requirements](records-and-audit-requirements.md)
- [Mail preferences for administrators](../../../../guides/mail-preferences-administrator.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
