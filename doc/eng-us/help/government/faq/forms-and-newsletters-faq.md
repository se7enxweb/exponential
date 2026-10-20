---
title: "FAQ: forms and newsletters"
audience: government, editor, admin, project-manager
category: public-sector
order: 170
summary: Answers to the questions public bodies ask about forms and newsletters in Exponential - contact and feedback forms, where submissions go, spam protection, consent, newsletter subscriptions with double opt-in, unsubscribe, statistics and retention.
keywords: forms FAQ, contact form, feedback, information collection, submissions, spam, CAPTCHA, newsletter, cjw_newsletter, double opt-in, unsubscribe, consent, retention, public sector
type: help
---
# FAQ: forms and newsletters

This page answers the questions public bodies ask about collecting information from citizens and sending newsletters
with Exponential. The answers come from [Collected information](../../../guides/collected-information.md),
[Newsletters with cjw_newsletter](../../creators/website-creators/newsletters-with-cjw-newsletter.md) and
[E-mail preferences](../../../features/6.0/mail-preferences.md).

> **Note** This page describes features and common practice; it is not legal advice. Check the law that applies to
> your organization with your legal counsel.

## Contents

1. [Can we put forms on the site?](#can-we-put-forms-on-the-site)
2. [Where do submissions go?](#where-do-submissions-go)
3. [How are forms protected against spam?](#how-are-forms-protected-against-spam)
4. [How long are submissions kept?](#how-long-are-submissions-kept)
5. [Can we send newsletters?](#can-we-send-newsletters)
6. [How do people unsubscribe?](#how-do-people-unsubscribe)
7. [Are opens and clicks tracked?](#are-opens-and-clicks-tracked)

## Can we put forms on the site?

Yes. Information collection turns content classes into forms, polls and surveys; editors place them as content
([Forms with the information collector](../../creators/website-creators/forms-with-the-information-collector.md),
[Building a contact form](../../creators/website-creators/building-a-contact-form.md)).

## Where do submissions go?

They are stored and listed under **Setup > Collected information**
([Collected information](../../../guides/collected-information.md)). They can be exported to CSV with
`./console ext:bccie:export` ([Migrating away from Exponential](../../../install/migration/leaving-exponential.md#1-what-to-export)).

## How are forms protected against spam?

Every form carries a CSRF token. Against bots, the recaptcha and hcaptcha extensions exist; they are third-party
services that need a mention in your privacy notice and an accessible alternative
([Spam protection for forms](../../creators/website-creators/spam-protection-for-forms.md)).

## How long are submissions kept?

Until someone removes them. The site must add a consent text and a retention rule itself
([Privacy standards](../../../standards/privacy.md#known-gaps), [Form submissions](../../creators/website-creators/form-submissions.md)).

## Can we send newsletters?

Yes, with `cjw_newsletter`: editions written as content, lists with double opt-in, sending by cron in the background,
bounce handling and a blacklist, and an archive of every send
([cjw_newsletter](../../../features/6.0/extensions/cjw_newsletter.md)). A course exists for editors and administrators
([Newsletters course](../../trainers/newsletter/README.md)).

## How do people unsubscribe?

Optional mail that passes Exponential's mail gate carries a `List-Unsubscribe` header with one-click unsubscribe and a
footer link, which work without signing in ([Privacy standards](../../../standards/privacy.md#can-spam-act-and-one-click-unsubscribe)).
People manage their categories on the e-mail preferences page ([E-mail preferences](../../../features/6.0/mail-preferences.md)).

## Are opens and clicks tracked?

The newsletter extension can count opens and clicks per person on your own server; this requires the person's prior
consent in the newsletter statistics category ([Privacy standards](../../../standards/privacy.md#third-party-services-in-shipped-extensions)).

## Related pages

- [Government FAQ](README.md)
- [Privacy checklist](../checklists/privacy-checklist.md)
- [Mail consent and bounces (for hosts)](../../hosting/operations/mail-consent-and-bounces.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
