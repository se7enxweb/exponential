---
title: Forms and collected information
audience: customer, editor, admin
category: customer-success
order: 15
summary: The owner's view of forms on an Exponential site - contact, feedback, poll and lead forms with the information collector, who reads and answers submissions, mail to the right team, spam protection, exporting and deleting, and a retention routine.
keywords: forms, contact form, feedback, poll, lead form, information collector, collected information, form submissions, CSV export, spam, CAPTCHA, retention, personal data
type: help
---
# Forms and collected information

This page is for owners who collect information from visitors: contact and feedback forms, polls, sign-ups, lead
forms. In Exponential a form is an item of a class whose fields are marked **Information collector**; each time a
visitor sends it, one **collection** is stored ([Collected information](../../../guides/collected-information.md)).
The form itself is built by your site builder
([Forms with the information collector](../../creators/website-creators/forms-with-the-information-collector.md));
what happens to the submissions is your routine.

## Contents

1. [What you get](#what-you-get)
2. [Who reads the submissions](#who-reads-the-submissions)
3. [Mail to the right team](#mail-to-the-right-team)
4. [Spam](#spam)
5. [Export, deletion and retention](#export-deletion-and-retention)
6. [Before you add a new form](#before-you-add-a-new-form)

## What you get

- **Setup > Collected information** lists the items that collected, with figures, search, sorting and paging; each
  links to its collections, to the item and to a **CSV export** ([Collected information](../../../guides/collected-information.md)).

  ![Setup, Collected information: the figures, a search for an object and each form with its collections and the CSV export](../../images/creators/collected-information.webp)

- Three form types are known: form, feedback and poll; a poll can be answered once per user, and polls send no mail by
  default (`settings/collect.ini`, see
  [Forms with the information collector](../../creators/website-creators/forms-with-the-information-collector.md)).
- Visitors who are not signed in may send forms by default (`CollectAnonymousData=enabled`).

## Who reads the submissions

Everything on the collected information pages needs the policy `infocollector/read`, and with it a person reads
**every** form's submissions ([Collected information](../../../guides/collected-information.md)). Give it to the people
who answer forms, not to every editor ([Roles for your team](../day-to-day/roles-for-your-team.md)).

| Form | Who answers | How fast (your promise) |
|---|---|---|
| Contact | | |
| Feedback | | |
| Lead or quote request | | |
| Poll | nobody; read the results | |

## Mail to the right team

The default receiver of form mails is `site.ini [InformationCollectionSettings] EmailReceiver`. Mail templates can set
the receiver per form; the shipped ezwebin templates take it from a `recipient` field of the form item when it has one,
so each form can mail its own team
([Forms with the information collector](../../creators/website-creators/forms-with-the-information-collector.md)).

> **Tip**
> Use a team or role address as receiver, not a person's address. When someone leaves, nothing gets lost
> ([Offboarding staff](../day-to-day/offboarding-staff.md)).

## Spam

Two layers protect forms: a **form token** proves a post came from your own page (the `ezformtoken` extension), and a
**CAPTCHA** (reCAPTCHA or hCaptcha) asks the visitor to prove they are human. Contact and feedback forms should have
both ([Spam protection for forms](../../creators/website-creators/spam-protection-for-forms.md)). A CAPTCHA service
loads a script from its provider; mention it in your privacy information. Spam collections can be removed in bulk on
**Setup > Collected information**.

## Export, deletion and retention

- **Export** a form's submissions as CSV when they need to go into another system.
- **Removing** deletes every collection of the ticked forms, or the ticked collections of one form, after a
  confirmation; the forms stay. It cannot be undone: export first if the data is still needed
  ([Collected information](../../../guides/collected-information.md)).
- Decide how long submissions are kept, write it in your privacy information, and delete on that schedule. This is not
  legal advice ([Consent and forms](../../businesses/security/consent-and-forms.md),
  [Data retention and deletion](../../businesses/security/data-retention-and-deletion.md)).

A monthly routine:

- [ ] Every new submission was answered.
- [ ] Spam removed.
- [ ] Submissions older than your retention period exported if needed, then removed.
- [ ] CSV exports stored only where your rules allow, and deleted when no longer needed.

## Before you add a new form

- [ ] Ask only for what you need; every field is data you must look after.
- [ ] Say on the form what you do with the data and how long you keep it.
- [ ] Choose who receives the mail and who answers.
- [ ] Token and CAPTCHA on; tested signed out.
- [ ] Labels visible and errors clear ([Accessibility for site builders](../../creators/website-creators/accessibility-for-site-builders.md)).
- [ ] The form is listed in your retention routine.

## Related pages

- [Collected information](../../../guides/collected-information.md)
- [Reading and exporting form submissions](../../creators/website-creators/form-submissions.md)
- [Forms and member feedback](../../businesses/associations/forms-and-member-feedback.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
