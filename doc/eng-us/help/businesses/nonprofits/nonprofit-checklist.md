---
title: Non-profit checklist
audience: businesses, project-manager, editor, admin
category: business-solutions
order: 70
summary: One checklist for a non-profit site on Exponential - planning, donations, volunteer forms, campaigns, newsletter, accessibility, data protection, operations on a small budget, launch and the monthly routine - each item linked to the page that explains it.
keywords: nonprofit checklist, charity website checklist, launch, donations, volunteers, campaigns, accessibility, data protection, operations, monthly routine
type: help
---
# Non-profit checklist

This page is for whoever has to say "the site is ready" at a non-profit. It gathers the checks of this section in
project order, so you can copy it into a shared document and tick it off with your board or volunteers. Every item
links to the page that explains it.

## Contents

1. [Planning](#planning)
2. [Donations](#donations)
3. [Volunteers and forms](#volunteers-and-forms)
4. [Campaigns](#campaigns)
5. [Newsletter](#newsletter)
6. [Accessibility](#accessibility)
7. [Data protection](#data-protection)
8. [Operations](#operations)
9. [Launch](#launch)
10. [Every month](#every-month)
11. [Sign-off](#sign-off)

## Planning

From [Non-profit site planning](nonprofit-site-planning.md):

- [ ] Goals written, each with an observable sign.
- [ ] Audiences and their top tasks listed; main menu drafted.
- [ ] Pages list agreed (home, about, our work, get help, donate, volunteer, news, reports, contact, privacy,
      accessibility statement).
- [ ] Editors, form readers and administrator named; a second person for every role.
- [ ] Features chosen, fewest first.

## Donations

From [Donations and fundraising pages](donations-and-fundraising-pages.md):

- [ ] Option chosen: external platform, shop items with `ezpaypal`, or both; reason written down.
- [ ] It is understood there is no dedicated donation module, free-amount field or recurring giving in Exponential.
- [ ] Giving page complete and tested on a phone.
- [ ] Shop option: sandbox payment completed, order became paid; canceled payment leaves it unpaid.
- [ ] Receipts and tax handling agreed with the accountant.

## Volunteers and forms

From [Volunteers and sign-up forms](volunteers-and-sign-up-forms.md):

- [ ] Form fields limited to what is needed; consent wording next to the checkbox.
- [ ] Receiver set per form; redirect to a thank-you page.
- [ ] Form token active; CAPTCHA added; tested logged out.
- [ ] File uploads only if needed, with `MaxFiles` set.
- [ ] `infocollector/read` only for coordinators.

## Campaigns

From [Campaigns and landing pages](campaigns-and-landing-pages.md):

- [ ] Editors trained on the layouts editor; one person edits a layout at a time.
- [ ] Campaign rules with time conditions checked on the site.
- [ ] Every button tested logged out.

## Newsletter

From the [Newsletters](../newsletters/README.md) section:

- [ ] Promise to supporters written; double opt-in on.
- [ ] Rehearsal with the file transport passed (all three transports on `file`).
- [ ] Sender domain with SPF and DKIM.
- [ ] Bounce handling set up.

## Accessibility

From [Accessibility for non-profits](accessibility-for-nonprofits.md):

- [ ] Template requirements in the brief or contract.
- [ ] Image fields require alternative texts; editors have the one-page list.
- [ ] Donate, volunteer and contact pages tested by keyboard, at 200% zoom and with a screen reader.
- [ ] Accessibility statement published with a way to report problems.

## Data protection

Basics, not legal advice; see [Security and data protection](../security/README.md):

- [ ] Privacy notice names the payment provider, CAPTCHA provider and analytics.
- [ ] Retention for form submissions, newsletter data and exports decided.
- [ ] A named person for access and erasure requests.
- [ ] Administrator accounts protected by two-factor login ([Two-factor login](../../../guides/two-factor-login.md)).

## Operations

From [Low-budget operations](low-budget-operations.md):

- [ ] Hosting model chosen; database chosen (SQLite unless many editors publish at once).
- [ ] Backups of database and storage scheduled; a restore tested.
- [ ] Update routine agreed, with a copy to test on.
- [ ] Cron running.
- [ ] Monthly checks have a name and a date.

## Launch

- [ ] The website creator's [launch checklist](../../creators/website-creators/launch-checklist.md) passed.
- [ ] HTTPS on every host ([Going live: hosts and HTTPS](../../creators/website-creators/going-live-hosts-https.md)).
- [ ] Editors handed over with a short guide
      ([Handing the site over to editors](../../creators/website-creators/handing-over-to-editors.md)).
- [ ] Old site's addresses redirected ([Redirects and moved content](../../creators/website-creators/redirects-and-moved-content.md)).

## Every month

- [ ] Form submissions processed and old ones removed on schedule.
- [ ] Donations or orders checked against the provider's records.
- [ ] Logs and the newsletter dashboard looked at.
- [ ] Accounts of departed volunteers deactivated ([Check user accounts](../../../features/6.0/user-hygiene.md)).
- [ ] Updates checked; backups verified.

## Sign-off

| Area | Checked by | Date |
|---|---|---|
| Planning | | |
| Donations | | |
| Forms | | |
| Accessibility | | |
| Data protection | | |
| Operations | | |

## Related pages

- [Non-profits](README.md)
- [Go-live checklist](../templates/go-live-checklist.md)
- [Monthly operations checklist](../templates/monthly-operations-checklist.md)
- [Small group checklist](../associations/small-group-checklist.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
