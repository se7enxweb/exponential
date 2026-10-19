---
title: GDPR basics
audience: businesses, admin, project-manager
category: business-solutions
order: 20
summary: The EU General Data Protection Regulation in outline for a business running Exponential - roles, principles and people's rights - mapped to the software features and commands that help, and to the decisions that remain yours. Not legal advice.
keywords: GDPR, General Data Protection Regulation, data protection, privacy, controller, processor, principles, consent, right of access, right to erasure, portability, storage limitation, privacy notice, DPO, not legal advice
type: help
---
# GDPR basics

This page is for owners and managers of businesses that serve people in the European Union, or otherwise fall
under the General Data Protection Regulation (GDPR). It gives the outline of the regulation in plain words and
shows, for each part, which features of Exponential help and what you must still do yourself.

> **Warning**
> This page is basics, not legal advice. It does not say whether your site complies. Read the regulation text,
> Regulation (EU) 2016/679, in the official journal (<https://eur-lex.europa.eu/eli/reg/2016/679/oj>), and take
> advice from a lawyer or your data protection officer (DPO). Other laws may also apply, for example on e-mail
> marketing; the project maps several of them in
> [E-mail preferences: the law checklist](../../../specifications/6.0/mail-preferences-compliance.md).

## Contents

1. [Who decides: controller and processor](#who-decides-controller-and-processor)
2. [The principles, and what helps](#the-principles-and-what-helps)
3. [People's rights, and the tools](#peoples-rights-and-the-tools)
4. [Security of processing](#security-of-processing)
5. [What the software cannot do for you](#what-the-software-cannot-do-for-you)
6. [Getting started in five steps](#getting-started-in-five-steps)

## Who decides: controller and processor

In a typical setup your business decides why and how personal data on your site is used, and a host or partner runs
the infrastructure for you. Which legal role each party has, and which contract you need between you, is for you
and your counsel to settle ([Compliance and GDPR](../../hosting/security/compliance-and-gdpr.md#roles-who-decides-what)).
Ask your host and partner how they support data protection requests; see
[Questions to ask a partner](../choosing/questions-to-ask-a-partner.md).

## The principles, and what helps

The regulation sets principles for processing personal data. In outline, with the features that support each:

| Principle (in outline) | What helps in Exponential | Source |
|---|---|---|
| Lawful, fair, transparent | Mail says why it was sent; footers and the preference page can link your privacy notice | [The law checklist](../../../specifications/6.0/mail-preferences-compliance.md) |
| Purpose limitation | One consent switch per mail category | [E-mail preferences](../../../features/6.0/mail-preferences.md) |
| Data minimization | Request logs mask client addresses by default; the audit trail truncates addresses and hashes sessions | [Compliance and GDPR](../../hosting/security/compliance-and-gdpr.md#privacy-defaults-in-the-logs) |
| Accuracy | Users change their own account data; staff edit user objects in the administration | [Accounts and sign-in](../../accounts-and-sign-in.md) |
| Storage limitation | Retention settings for logs, the audit trail, the consent log, trash and sessions | [Data retention and deletion](data-retention-and-deletion.md) |
| Integrity and confidentiality | bcrypt passwords, two-step sign-in, security headers, roles | [User accounts and passwords](user-accounts-and-passwords.md) |
| Accountability | A record of consent changes, and a tamper-evident audit trail | [Compliance evidence from the audit trail](compliance-evidence-from-the-audit-trail.md) |

## People's rights, and the tools

| Request | What the software offers | Source |
|---|---|---|
| See their data (access) | For mail data, the person's own download, or `exp:mail:preferences export`; audit records about a user with `exp:audit export --subject-user=<id>` | [Compliance and GDPR](../../hosting/security/compliance-and-gdpr.md#requests-from-data-subjects) |
| Take their data elsewhere (portability) | "Download my e-mail data" in JSON and CSV | [The law checklist](../../../specifications/6.0/mail-preferences-compliance.md) |
| Correct their data | Account editing by the user or by staff | [Accounts and sign-in](../../accounts-and-sign-in.md) |
| Erase their data | `exp:mail:preferences erase`; removing a user account also erases its mail preferences; form collections can be removed in **Setup > Collected information** | [Data retention and deletion](data-retention-and-deletion.md) |
| Object to marketing | "Stop all optional e-mail" by link puts the address on the suppression list | [Consent and forms](consent-and-forms.md) |

Some data is not covered by a single command: orders, newsletter subscriber records in `cjw_newsletter`, and copies
in backups. Plan how you handle each; [Personal data in Exponential](personal-data-in-exponential.md) lists where
they are.

> **Note**
> The audit trail is built so that single records cannot be deleted without breaking its hash chain. The audit guide
> describes keeping records for their retention period and pseudonymizing personal fields after a set number of days
> ([Audit: privacy and GDPR tasks](../../../bc/6.0/audit.md#412-privacy-and-gdpr-tasks)). Whether that answers an
> erasure request in your case is a legal question.

## Security of processing

What the software contributes is described in [Security basics for businesses](security-basics-for-businesses.md)
and [Compliance and GDPR](../../hosting/security/compliance-and-gdpr.md#security-of-processing). What the host
contributes (isolation, encrypted backups, patching) belongs in your hosting contract.

## What the software cannot do for you

- Decide your legal basis for each kind of processing.
- Write your privacy notice and your record of processing activities.
- Fill in the sender name and postal address of your mail footer (`mailpreferences.ini [FooterSettings]`, empty after
  an installation).
- Decide retention periods, and set backup retention to match.
- Judge third-party services your templates load (fonts, maps, analytics); the Content Security Policy makes them
  visible, it does not judge them.
- Sign contracts with your host, partner, payment provider or a hosted AI provider.

## Getting started in five steps

1. List the personal data you hold, with [Personal data in Exponential](personal-data-in-exponential.md).
2. Answer the questions in [Accessibility, security and compliance checklists](../../leadership/accessibility-security-and-compliance-checklists.md#data-protection-and-privacy-questions-to-answer)
   with your DPO or lawyer.
3. Set retention periods and the matching settings ([Data retention and deletion](data-retention-and-deletion.md)).
4. Write down how you answer requests to see, export or erase data, and who does it.
5. Review it once a year with the [annual review checklist](../templates/annual-review-checklist.md).

## Related pages

- [Compliance and GDPR for hosts](../../hosting/security/compliance-and-gdpr.md)
- [Consent and forms](consent-and-forms.md)
- [Protecting customer data (partners)](../../partners/protecting-customer-data.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
