---
title: Consent and forms
audience: businesses, admin, sales-marketing, website-creator
category: business-solutions
order: 40
summary: How Exponential handles consent for e-mail (essential and optional mail, double opt-in, one-click unsubscribe, the consent log and the suppression list), what newsletters add, and how to run contact and lead forms that collect only what you need. Not legal advice.
keywords: consent, opt-in, double opt-in, unsubscribe, mail preferences, mail gate, consent log, suppression list, newsletter, forms, information collector, privacy notice, CAPTCHA, form token, marketing mail
type: help
---
# Consent and forms

This page is for owners, marketing staff and administrators who send mail to customers or collect data through
forms. It explains what Exponential does for consent, what each part means for your business, and what you must set
up or write yourself.

> **Warning**
> Basics, not legal advice. Consent rules differ by country and by the kind of mail. Read the laws that apply to you
> (the project maps several in [E-mail preferences: the law checklist](../../../specifications/6.0/mail-preferences-compliance.md))
> and ask a lawyer or your data protection officer.

## Contents

1. [The mail gate in one paragraph](#the-mail-gate-in-one-paragraph)
2. [Essential and optional mail](#essential-and-optional-mail)
3. [Double opt-in, unsubscribe and the consent log](#double-opt-in-unsubscribe-and-the-consent-log)
4. [Newsletters](#newsletters)
5. [Forms that collect data](#forms-that-collect-data)
6. [What you must set up yourself](#what-you-must-set-up-yourself)
7. [Checklist](#checklist)

## The mail gate in one paragraph

Since 6.0.15 every mail the installation sends passes a mail gate, whatever transport is used. Optional mail goes
only to people who turned it on, one mail per recipient, with a footer and one-click unsubscribe headers (RFC 8058).
Essential mail always goes. Every change of a preference is written to a consent log with the exact text the person
saw ([E-mail preferences: administrator's guide](../../../guides/mail-preferences-administrator.md#1-what-the-system-does-in-one-paragraph)).

## Essential and optional mail

| Kind | Examples | Goes to |
|---|---|---|
| Essential (`security`, `orders`, `legal`, `admin`) | password changed, order confirmation | always |
| Optional | newsletters, offers, news | only people who switched the category on; off until they do |

Source: [Compliance and GDPR](../../hosting/security/compliance-and-gdpr.md#mail-consent-unsubscribe-and-suppression).
Optional categories cannot be on by default unless an administrator explicitly allows it
(`[CategorySettings] AllowDefaultOn`), and the sign-up form shows every box unticked
([The law checklist](../../../specifications/6.0/mail-preferences-compliance.md#gdpr-and-the-eprivacy-directive-european-union)).

## Double opt-in, unsubscribe and the consent log

- **Double opt-in** for newsletters, offers and news, and for a new e-mail address: nothing of the category is sent
  until the person confirms.
- **Opening a link is not consent**: the confirm and unsubscribe pages change nothing until their button is pressed.
- **Withdrawal is as easy as consent**: the same box turns a category off, "Turn off all optional e-mail" is one
  button, every optional mail has a one-click link.
- **Consent log**: time, category, old and new state, source, the exact text shown, IP address, acting user.
- **Suppression list**: hard bounces, complaints, "stop all" and legal requests; only a salted hash of each address
  is stored.
- **The person's own copy**: "Download my e-mail data" in JSON and CSV.

All from [The law checklist](../../../specifications/6.0/mail-preferences-compliance.md). Check the state of your site
with `php bin/php/console exp:mail:status`.

## Newsletters

The newsletter extension `cjw_newsletter`, required by `composer.json`, lets visitors subscribe to lists "with a
double opt-in", and offers subscribe, confirm, change and unsubscribe pages
([cjw_newsletter](../../../features/6.0/extensions/cjw_newsletter.md)). Opens and clicks are counted only when the
site switches tracking on (`[TrackingSettings] Tracking=disabled` by default), and per person only with consent
([Compliance and GDPR](../../hosting/security/compliance-and-gdpr.md#what-stays-with-the-host-and-the-customer)).
The business side is in [Newsletters: opt-in and consent](../newsletters/opt-in-and-consent.md).

## Forms that collect data

Contact, feedback and lead forms use the information collector: each sending is stored as a collection
([Forms with the information collector](../../creators/website-creators/forms-with-the-information-collector.md)).

| Good practice | How in Exponential |
|---|---|
| Collect only what you need | Mark only the necessary class attributes as information collectors |
| Say what happens with the data | Put a short notice and a link to your privacy notice next to the form, in the template |
| Record agreement where you need it | A checkbox attribute (`ezboolean`) can collect information, so a ticked box is stored with the sending |
| Limit who reads submissions | Give `infocollector/read` only to the people who process them |
| Remove what you no longer need | **Setup > Collected information** removes collections after a confirmation ([Collected information](../../../guides/collected-information.md)) |
| Stop spam and forged posts | Form tokens (`ezformtoken`) and reCAPTCHA or hCaptcha ([Spam protection for forms](../../creators/website-creators/spam-protection-for-forms.md)) |

> **Note**
> A checkbox on a form is stored as one value of that sending. It is not entered in the mail consent log and does
> not switch on any mail category. If a form should sign someone up for mail, use the mail preference or
> newsletter sign-up, which have their own double opt-in.

A CAPTCHA service is a third party that receives data from your visitors' browsers; name it in your privacy notice.

## What you must set up yourself

From [Left to the site](../../../specifications/6.0/mail-preferences-compliance.md#left-to-the-site):

1. The postal address in the mail footer (`mailpreferences.ini [FooterSettings] OrganisationAddress`, empty after an
   installation) and the sender's name (`OrganisationName`).
2. A category for every commercial mail sent by custom code.
3. Keeping `[GateSettings] SplitRecipients=enabled`.
4. DKIM signing of the unsubscribe headers by the mail server, often your host's job
   ([Mail consent and bounces](../../hosting/operations/mail-consent-and-bounces.md)).
5. A link to the privacy notice on the registration form, if your design wants one.

## Checklist

- [ ] Mail footer filled in; `exp:mail:status` reports no problems.
- [ ] Every marketing mail has an optional category.
- [ ] Newsletter sign-up uses double opt-in; tracking left off unless your notice covers it.
- [ ] Every form asks only for what it needs and links the privacy notice.
- [ ] `infocollector/read` given to few people; old collections removed on a schedule.
- [ ] CAPTCHA and other third-party services named in the privacy notice.
- [ ] Legal questions taken to your adviser.

## Related pages

- [GDPR basics](gdpr-basics.md)
- [Personal data in Exponential](personal-data-in-exponential.md)
- [Building a contact form](../../creators/website-creators/building-a-contact-form.md)
- [Lists and subscriptions](../newsletters/lists-and-subscriptions.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
