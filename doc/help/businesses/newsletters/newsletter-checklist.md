---
title: Newsletter checklist
audience: businesses, project-manager, editor, admin
category: business-solutions
order: 80
summary: One checklist for a newsletter on Exponential with cjw_newsletter - decisions, setup, consent, rehearsal, the first real send, every send after it, and the monthly routine - each item linked to the page that explains it.
keywords: newsletter checklist, go-live, first send, consent checklist, rehearsal, monthly routine, cjw_newsletter, newsletter launch
type: help
---
# Newsletter checklist

This page is for the person who has to say "the newsletter is ready". It collects the checks of this section in the
order you meet them, from the first decision to the routine after launch. Copy it into your project tracker; every
item links to the page that explains it.

## Contents

1. [Decisions](#decisions)
2. [Setup](#setup)
3. [Consent and data protection](#consent-and-data-protection)
4. [Rehearsal](#rehearsal)
5. [Going live](#going-live)
6. [Before every send](#before-every-send)
7. [After every send](#after-every-send)
8. [Every month](#every-month)
9. [Sign-off](#sign-off)

## Decisions

From [Planning a newsletter](newsletter-planning.md):

- [ ] The promise to subscribers is written in one sentence (what, how often).
- [ ] Lists are named; interests and languages decided.
- [ ] Writer, approver, sender and administrator are named people.
- [ ] Approval per list: on or off.
- [ ] Tracking decided: off, anonymous totals, or per person with consent.
- [ ] Mail provider, sender address and domain chosen.
- [ ] Two or three numbers chosen for the monthly review.

## Setup

From [Newsletters with cjw_newsletter](../../creators/website-creators/newsletters-with-cjw-newsletter.md#setting-it-up):

- [ ] `cjw_newsletter` active and its class packages installed.
- [ ] A folder for newsletters, and `[NewsletterSettings] RootFolderNodeId` set to it in
      `settings/override/cjw_newsletter.ini.append.php`.
- [ ] A newsletter system and the lists created below it.
- [ ] Subscribe, configure and unsubscribe pages linked from the site (footer or a block).
- [ ] Anonymous visitors have the newsletter module's subscribe policies.
- [ ] Roles: subscriber data and `newsletter/import_export` only for named people
      ([Lists and subscriptions](lists-and-subscriptions.md#who-may-see-subscriber-data)).
- [ ] Approvers set in `[ApprovalSettings]` if approval is used
      ([Writing and sending editions](writing-and-sending-editions.md#approval-before-sending)).
- [ ] Skins allowed per list chosen, or a skin of your own built by a designer.

## Consent and data protection

From [Opt-in and consent](opt-in-and-consent.md) (basics, not legal advice):

- [ ] The subscribe form states the promise and links to the privacy notice.
- [ ] Double opt-in works for public sign-ups.
- [ ] Imports only with a written consent source and evidence kept outside the website.
- [ ] The e-mail preference page reachable from every mail; unsubscribe tested.
- [ ] Retention settings of the consent log checked against your policy.
- [ ] A named person for access and erasure requests.
- [ ] Wording reviewed by your lawyer or data protection officer.

## Rehearsal

From [Newsletter mail delivery](newsletter-mail-delivery.md#rehearsal-first):

- [ ] `TransportMethodCronjob`, `TransportMethodPreview` and `TransportMethodDirectly` all set to `file`
      (the shipped preview transport really sends).
- [ ] `FileTransportMailDir` writable by the web server user and the cron user.
- [ ] Cron parts `cjw_newsletter` and `cjw_newsletter_mailbox` running for the right siteaccess.
- [ ] Subscribe, confirm, send, receive (as files), unsubscribe: all passed with test addresses.
- [ ] The `.eml` files opened in a mail program: look, links and unsubscribe checked.

## Going live

- [ ] SPF and DKIM set up for the sender domain at your provider.
- [ ] Preview transport on `smtp`; a test to internal addresses checked in several mail programs.
- [ ] Provider rate limits known; `[ThrottleSettings]` set if needed
      ([Newsletter mail delivery](newsletter-mail-delivery.md#rate-limits-and-batches)).
- [ ] Bounce mailbox configured and its cron part running
      ([Bounces and list hygiene](bounces-and-list-hygiene.md#how-returned-mail-reaches-the-newsletter)).
- [ ] Old bounces and unsubscribes from a previous tool imported into the suppression list.
- [ ] Remaining transports switched to `smtp` on a planned day.
- [ ] Someone available to watch the first real send and abort it if needed.

## Before every send

From [Sending newsletters safely](../../creators/website-creators/sending-newsletters.md#before-every-send) and
[Writing and sending editions](writing-and-sending-editions.md):

- [ ] Preview as a subscriber checked; test group mail received.
- [ ] Subject, preheader and sender name right.
- [ ] Every link works and points to the public site.
- [ ] Images have alternative texts; the mail reads well without images.
- [ ] The right list is selected.
- [ ] Approved, if the list requires approval; no change made since.
- [ ] The send time suits your readers, and someone is around afterwards.

## After every send

- [ ] The dashboard shows the send as finished, without problems.
- [ ] Bounces and unsubscribes looked at; anything unusual explained.
- [ ] The edition is in the archive.

## Every month

From [Bounces and list hygiene](bounces-and-list-hygiene.md#a-monthly-hygiene-routine):

- [ ] Dashboard problems worked through.
- [ ] Bounce cron part ran recently.
- [ ] Old pending subscriptions reviewed.
- [ ] Exports and CSV files deleted when no longer needed.
- [ ] Roles reviewed for people who changed jobs or left.
- [ ] The chosen numbers recorded and discussed.

## Sign-off

| Area | Checked by | Date |
|---|---|---|
| Decisions | | |
| Setup | | |
| Consent and data protection | | |
| Rehearsal | | |
| Going live | | |

## Related pages

- [Newsletters for businesses](README.md)
- Go-live checklist
- Monthly operations checklist
- [Launch checklist for websites](../../creators/website-creators/launch-checklist.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
