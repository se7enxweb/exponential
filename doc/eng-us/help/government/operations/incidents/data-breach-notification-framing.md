---
title: Data breach notification framing
audience: government, admin, project-manager
category: public-sector
order: 70
summary: How the technical team supports a public body's decision on notifying a personal data breach - establishing what data, whose, and when, with the audit trail, logs and exports Exponential provides - without giving legal advice.
keywords: data breach, personal data breach, notification, GDPR, supervisory authority, data protection officer, evidence, audit trail, timeline, affected persons, incident, privacy
type: help
---
# Data breach notification framing

> **Note** This page describes features and common practice; it is not legal advice. Check the law that applies to
> your organization with your legal counsel.

This page is for administrators and incident leads who must give the data protection officer and legal counsel the
facts they need to decide on notifying a personal data breach. In the EU, the
[General Data Protection Regulation](https://eur-lex.europa.eu/eli/reg/2016/679/oj) sets duties to notify the
supervisory authority (Article 33) and, in some cases, the people affected (Article 34); other jurisdictions have
their own rules. The decision is theirs; the facts are yours.

## Contents
1. [Bring the right people in early](#bring-the-right-people-in-early)
2. [The questions you will be asked](#the-questions-you-will-be-asked)
3. [Where the answers are](#where-the-answers-are)
4. [What personal data an Exponential site may hold](#what-personal-data-an-exponential-site-may-hold)
5. [Handling the evidence](#handling-the-evidence)
6. [Related pages](#related-pages)

## Bring the right people in early

As soon as personal data may be involved, tell the data protection officer, whatever the hour: notification duties
can run on short deadlines from the moment the organization becomes aware. Keep a timeline from the first sign.

## The questions you will be asked

1. What happened, and when did it start and end?
2. Which categories of data, and roughly how many records and people?
3. Was the data seen, copied, changed or deleted, or only exposed?
4. Is it contained?
5. What are the likely consequences for the people?
6. What has been done, and what will be done?

## Where the answers are

| Question | Source in Exponential |
|---|---|
| When it started | audit trail (`access` sign-ins, `content` changes), `security.log`, web server logs |
| Which accounts acted | `./console exp:audit search --login=<login> --allow-root-user`; the console filtered by user |
| What was exported | `data.export.csv` events (the account list, sub-items exports) with row counts; audit exports |
| Which records changed | the `content` channel; versions pages |
| Whether the record is reliable | `./console exp:audit verify --allow-root-user` and outside copies (syslog, webhook) |
| What requests reached the site | `security.log` (probes, injections, with status), `404.log` |

Note the limits: node views, searches and downloads are recorded only when reads are switched on (the `read` channel);
logs mask addresses; a request log does not prove what a response contained.

## What personal data an Exponential site may hold

| Data | Where | Reference |
|---|---|---|
| User accounts (name, login, e-mail, password hash) | users and the user class | [User account hygiene](../people-and-permissions/user-account-hygiene.md) |
| Form submissions | collected information | [Collected information](../../../../guides/collected-information.md) |
| Newsletter subscribers and consent log | cjw_newsletter, e-mail preferences | [Mail preferences](../../../../features/6.0/mail-preferences.md) |
| Shop orders, if a shop runs | orders | [Store dashboard](../../../../features/6.0/store-dashboard.md) |
| Logs and audit (masked addresses, pseudonymized where configured) | `var/log/`, audit | [Privacy standard](../../../../standards/privacy.md) |
| Backups of all of the above | your backup storage | [Keys and secrets in backups](../backups-and-recovery/keys-and-secrets-in-backups.md) |

Your records of processing should already say which of these your site holds.

## Handling the evidence

- Copy logs and the audit directory to a protected place before rotation or pruning removes them; consider raising
  retention temporarily.
- Exports for the investigation are themselves personal data: restricted access, short retention.
- Do not unmask logs more widely than needed (`setup/unmasklogs` for the investigator only).

## Related pages
- [Suspected account compromise](suspected-account-compromise.md)
- [Compliance and GDPR (hosting)](../../../hosting/security/compliance-and-gdpr.md)
- [Log retention and rotation](../monitoring/log-retention-and-rotation.md)
- [Post-incident review](post-incident-review.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
