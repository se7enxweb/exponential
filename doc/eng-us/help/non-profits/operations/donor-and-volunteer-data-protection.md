---
title: Donor and volunteer data protection
audience: non-profit, businesses, admin, government
category: business-solutions
order: 40
summary: Protecting the personal data of donors, volunteers, beneficiaries and subscribers on a non-profit's Exponential site - where it lives (form submissions, user accounts, orders, newsletter subscriptions, logs, exports, backups), who may see it, collecting less, retention and deletion, requests from people, processors, and security basics. Basics, not legal advice.
keywords: data protection, privacy, GDPR, personal data, donors, volunteers, beneficiaries, subscribers, collected information, orders, retention, deletion, right of access, right to erasure, processors, privacy notice, two-factor login
type: help
---
# Donor and volunteer data protection

This page is for the director, the data protection contact and the coordinators. Non-profits hold personal data that
people trust them with: donors' names and addresses, volunteers' availability and sometimes background information,
and occasionally sensitive data about the people they serve. Losing that trust costs more than any fine.

> **Note**
> Basics, not legal advice. Which law applies (for example the GDPR in Europe) and what it requires is a question for
> your data protection officer or lawyer.

## Contents

1. [Where personal data lives](#where-personal-data-lives)
2. [Collect less](#collect-less)
3. [Who may see it](#who-may-see-it)
4. [How long you keep it](#how-long-you-keep-it)
5. [Requests from people](#requests-from-people)
6. [Processors and third parties](#processors-and-third-parties)
7. [Security basics](#security-basics)
8. [Sensitive data about the people you serve](#sensitive-data-about-the-people-you-serve)
9. [Checklist](#checklist)

## Where personal data lives

From [Personal data in Exponential](../../businesses/security/personal-data-in-exponential.md):

| Data | Where | Whose |
|---|---|---|
| Form submissions | Collected information | Volunteers, people asking for help |
| User accounts | The users tree | Staff, volunteers, members |
| Orders | The shop | Donors using the shop route |
| Newsletter subscriptions | `cjw_newsletter` | Supporters |
| Mail preferences and consent | Kernel tables | Subscribers |
| Logs and the audit trail | `var/` log directories | Everyone who visits or signs in (minimized) |
| Search phrases | Search statistics, if on | Visitors |
| Exports and backups | Wherever you put them | All of the above |

Exports and backups are the easiest to forget. Write down where they go.

## Collect less

- Ask in forms only what you need ([Volunteer form field worksheet](../templates/volunteer-form-field-worksheet.md)).
- Do not ask donors for data the payment provider already holds.
- Do not log search phrases unless you will use them.
- See [Data minimization](../../government/privacy/data-minimization.md).

## Who may see it

| Data | Permission | Give it to |
|---|---|---|
| Form submissions | `infocollector/read` | Coordinators only |
| Orders | Shop roles | One or two people |
| Subscribers | Newsletter administration | The newsletter editor |
| Audit trail | `audit/read` | Administrators |

Review the holders every quarter ([Roles and permissions worksheet](../templates/roles-and-permissions-worksheet.md),
[Access reviews](../../businesses/security/access-reviews.md)).

## How long you keep it

Write a retention plan and follow it. Defaults and settings for logs, audit records, trash, sessions and form data are
in [Data retention and deletion](../../businesses/security/data-retention-and-deletion.md).

| Data | Suggested question to decide |
|---|---|
| Volunteer applications not taken forward | How many months? |
| Event sign-ups | Delete after the event? |
| Accounts of departed volunteers | Deactivate now, remove when? ([When volunteers leave](when-volunteers-leave.md)) |
| CSV exports | Delete when the task is done |
| Backups | How long is the rotation? |

## Requests from people

People may ask what you hold about them, to correct it, or to delete it.

| Request | Where to look | Read |
|---|---|---|
| Access | Accounts, submissions, orders, subscriptions | [Right of access](../../government/privacy/right-of-access.md) |
| Erasure | The same; then exports; backups expire | [Right to erasure](../../government/privacy/right-to-erasure.md) |
| Stop newsletters | Unsubscribe, blacklist | [One-click unsubscribe](../../government/privacy/one-click-unsubscribe.md) |
| Donor data in orders | Shop | [Data export and erasure](../../customers/commerce/data-export-and-erasure.md) |

Log requests and answers ([GDPR request log template](../../customers/commerce/gdpr-request-log-template.md)).
Some data cannot be exported automatically ([What cannot be exported](../../government/privacy/what-cannot-be-exported.md)).

## Processors and third parties

Name every service that receives personal data in your privacy notice
([Privacy notice outline](../../government/privacy/privacy-notice-outline.md)):

- the host;
- the payment provider or donation platform;
- the mail delivery service;
- the CAPTCHA provider (`hcaptcha` or `recaptcha`) ([Captchas](../../government/privacy/captchas.md));
- an analytics service, maps, video embeds;
- an AI provider, if Exponential Intelligence is used ([Exponential Intelligence](../../customers/growth/exponential-intelligence.md)).

## Security basics

- Two-factor login for everyone who can read personal data ([Two-factor login](../../../guides/two-factor-login.md)).
- HTTPS everywhere.
- Updates applied on time ([Backups and updates routine](backups-and-updates-routine.md)).
- Backups protected like the live data.
- See [Security basics for businesses](../../businesses/security/security-basics-for-businesses.md).

## Sensitive data about the people you serve

Health, religion, immigration status, criminal records and similar data need special care in most laws. Prefer not to
collect them through the website at all; take them in person or through a system built for it. If you must, involve
your data protection officer before the form goes live.

## Checklist

- [ ] Inventory of where personal data lives, including exports and backups.
- [ ] Forms minimized; sensitive data kept off the website.
- [ ] Permissions limited and reviewed quarterly.
- [ ] Retention plan written and followed.
- [ ] Privacy notice names every processor.
- [ ] Two-factor login for everyone with access to personal data.
- [ ] A named person handles requests, with a log.

See also the [Data protection checklist](../checklists/data-protection-checklist.md) and [GDPR basics](../../businesses/security/gdpr-basics.md).

## Related pages

- [Operations for non-profits](README.md)
- [Personal data inventory (government)](../../government/privacy/personal-data-inventory.md)
- [Consent and forms](../../businesses/security/consent-and-forms.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
