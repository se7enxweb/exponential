---
title: Opt-in and consent
audience: businesses, sales-marketing, admin
category: business-solutions
order: 40
summary: How Exponential and cjw_newsletter handle consent for newsletters - double opt-in, the central e-mail preferences, one-click unsubscribe, the consent log, imports with a consent source, statistics and SMS only with consent. Basics, not legal advice.
keywords: double opt-in, consent, GDPR, unsubscribe, one-click unsubscribe, consent log, e-mail preferences, suppression list, newsletter statistics consent, privacy
type: help
---
# Opt-in and consent

This page is for owners, managers and data protection contacts who need to know how a newsletter run on Exponential
asks for, records and honours consent. It describes what the software does, with a source for each point, so you can
decide with your advisers whether that meets your obligations.

> **Note**
> These are basics, not legal advice. Read the regulation that applies to you (for example the EU General Data
> Protection Regulation and your national rules on electronic marketing) and ask your lawyer or data protection
> officer.

## Contents

1. [Double opt-in for public sign-ups](#double-opt-in-for-public-sign-ups)
2. [The e-mail preferences of the site](#the-e-mail-preferences-of-the-site)
3. [Unsubscribing with one click](#unsubscribing-with-one-click)
4. [The consent log](#the-consent-log)
5. [Imported subscribers](#imported-subscribers)
6. [Statistics only with consent](#statistics-only-with-consent)
7. [SMS consent](#sms-consent)
8. [Erasure and data requests](#erasure-and-data-requests)
9. [Consent checklist](#consent-checklist)

## Double opt-in for public sign-ups

A visitor who subscribes on the site gets a confirmation mail and is only counted once they click its link
("the subscriber confirms by mail (double opt-in) before the subscription counts",
[Newsletters with cjw_newsletter](../../creators/website-creators/newsletters-with-cjw-newsletter.md#subscribe-forms-on-the-site)).
Until then the subscription is **pending** and receives nothing.

Subscribing by e-mail (release 4.2.0, off by default with `[MailInSettings] MailIn=disabled`) follows the same rule:
"A subscribe mail only starts a pending subscription: the confirmation mail goes to the sender, who must open its
link (double opt-in)" (comment in the shipped `cjw_newsletter.ini`). The From header alone never changes anything.

## The e-mail preferences of the site

Since Exponential 6.0.15 every mail the installation sends passes a **mail gate**. A mail that declares a category is
checked against the recipient's preferences, the suppression list and a pending double opt-in; optional mail goes only
to people who turned it on, with a footer that says why the person gets it and how to unsubscribe
([the administrator's guide](../../../guides/mail-preferences-administrator.md#1-what-the-system-does-in-one-paragraph)).
Release 4.2.0 of the newsletter "works through the e-mail preferences of 6.0.15 instead of keeping a system of its own"
([cjw_newsletter](../../../features/6.0/extensions/cjw_newsletter.md#the-features-of-420)).

What a subscriber sees ([E-mail preferences: the user's guide](../../../features/6.0/mail-preferences.md)):

- optional e-mail is "off until you turn it on";
- one page lists each kind of e-mail with its own switch, plus a main switch for all optional mail;
- the registration form offers one unticked box per optional kind: "nothing is ticked for you";
- choices and every change to them can be downloaded as JSON or CSV.

## Unsubscribing with one click

Every optional e-mail ends with why the person got it, an **Unsubscribe** link and a link to their preference page; no
login is needed for either. Opening the link alone changes nothing, so a virus scanner that opens links cannot
unsubscribe anyone; the person presses one button. Mail programs that show their own Unsubscribe button use the same
link ([Unsubscribing from an e-mail](../../../features/6.0/mail-preferences.md#unsubscribing-from-an-e-mail)). The
administrator's guide names the one-click headers of RFC 8058.

The extension's skins also print an unsubscribe link: the website creator's checklist asks for "An unsubscribe link in
every edition (the shipped `news` and `company` skins print one)"
([Newsletter lists and subscriptions](../../creators/website-creators/newsletter-lists-and-subscriptions.md#consent)).

## The consent log

Setup > E-mail preferences > **Consent log** lists every change of a preference, newest first, "with the person, the
category, the change, the source, the text shown and the IP address". It can be filtered and exported as CSV with the
policy `mailpreferences/export`
([The consent log](../../../guides/mail-preferences-administrator.md#8-the-consent-log)). That is the record you show
when someone asks when and how they agreed.

Retention: the cronjob part `mailpreferences` removes the log rows of people who are gone once they are older than
`[ConsentSettings] RetentionDays` (1095 days by default) and removes confirmations that waited longer than
`[ConsentSettings] PendingDays` (7) ([Retention and the cronjob](../../../guides/mail-preferences-administrator.md#9-retention-and-the-cronjob)).
Check these values against your own retention rules.

## Imported subscribers

An import has no confirmation click, so the extension asks for a **consent source**: where and how the people agreed.
It is required for a real import, it is written to the consent log with the source `import`, and "you are responsible
for being able to show that consent"
([importexport.md](https://github.com/se7enxweb/cjw_newsletter/blob/4.2.0/doc/importexport.md)).

> **Warning**
> Import only people who agreed to receive this newsletter, and keep the evidence (the form, the date, the wording)
> outside the website too. "Import only people who agreed to receive the newsletter; record where the consent came
> from" ([Newsletter lists and subscriptions](../../creators/website-creators/newsletter-lists-and-subscriptions.md#importing-and-exporting)).

## Statistics only with consent

"Nothing is counted per person without that person's consent, and the consent is the optional e-mail preference
category 'Newsletter statistics' of Exponential (6.0.15 and later), off by default"
([statistics.md](https://github.com/se7enxweb/cjw_newsletter/blob/4.2.0/doc/statistics.md)). The site switch
`[TrackingSettings] Tracking` is disabled in the shipped settings, so nothing is counted until you switch it on. The
consent is checked again on every open and click; someone who withdraws it is counted anonymously from then on. Test
sends and SMS are never tracked.

## SMS consent

Newsletters by SMS need the consent category "Newsletters by SMS", confirmed with a code sent by SMS, and a STOP
keyword ends them ([sms.md](https://github.com/se7enxweb/cjw_newsletter/blob/4.2.0/doc/sms.md)). SMS is off until it is
switched on in the settings.

## Erasure and data requests

- A person can see their history on their preference page and download it.
- To erase, a person asks the administrator: preferences are removed and the history is anonymized; a record that the
  person withdrew is kept without their address, so the site can prove it stopped writing
  ([Your rights](../../../features/6.0/mail-preferences.md#your-rights)).
- When an erased person had newsletter data, the category handlers remove their newsletter user, interests, SMS data
  and per-person statistics ([cjw_newsletter](../../../features/6.0/extensions/cjw_newsletter.md#the-features-of-420)).

How to handle such requests step by step:
[Requests under data protection law](../../../guides/mail-preferences-administrator.md#6-requests-under-data-protection-law).

## Consent checklist

- [ ] The subscribe form says what subscribers get, how often, and links to your privacy notice.
- [ ] Public subscriptions use the double opt-in; nobody is added "by default".
- [ ] Every edition carries an unsubscribe link; tested from a real mailbox.
- [ ] Imports only with a written consent source and evidence kept.
- [ ] Tracking stays off, or is set to anonymous totals, unless you have decided on per-person statistics with consent.
- [ ] Retention values (`RetentionDays`, `PendingDays`) checked against your policy.
- [ ] One named person handles access, correction and erasure requests.
- [ ] Your lawyer or data protection officer has reviewed the wording.

## Related pages

- [Newsletters for businesses](README.md)
- [Lists and subscriptions](lists-and-subscriptions.md)
- GDPR basics
- Consent and forms
- [E-mail preferences: the administrator's guide](../../../guides/mail-preferences-administrator.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
