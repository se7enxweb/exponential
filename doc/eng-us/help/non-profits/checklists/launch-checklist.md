---
title: Non-profit launch checklist
audience: non-profit, businesses, project-manager, website-creator
category: business-solutions
order: 20
summary: The checklist a non-profit works through before its Exponential site goes live - ownership and access, hosting, HTTPS and cron, backups with a tested restore, donations, volunteer and contact forms, newsletter, accessibility, transparency pages, privacy notice and data protection, editors trained, measurements switched on, and sign-off.
keywords: launch checklist, go-live, relaunch, ownership, hosting, HTTPS, cron, backups, restore test, donations, forms, CAPTCHA, newsletter, accessibility, privacy notice, training, measurements, sign-off, non-profit
type: help
---
# Non-profit launch checklist

Work through this before the site, or a relaunch, goes live. Each line links to the page that explains it. For the
technical details of going live, the website creator also uses the [Launch checklist](../../creators/website-creators/launch-checklist.md).

## Contents

1. [Ownership and access](#ownership-and-access)
2. [Hosting and technology](#hosting-and-technology)
3. [Donations](#donations)
4. [Forms and volunteers](#forms-and-volunteers)
5. [Newsletter and communications](#newsletter-and-communications)
6. [Accessibility](#accessibility)
7. [Transparency and privacy](#transparency-and-privacy)
8. [People and routine](#people-and-routine)
9. [Sign-off](#sign-off)

## Ownership and access

- [ ] Domain registered in the organization's name; two people can manage it.
- [ ] Hosting contract in the organization's name.
- [ ] At least two administrator accounts, with two-factor login ([Two-factor login](../../../guides/two-factor-login.md)).
- [ ] Default administrator password changed ([Default administrator password](../../default-administrator-password.md)).
- [ ] Every editor has a personal account in the right group ([Roles and permissions worksheet](../templates/roles-and-permissions-worksheet.md)).

## Hosting and technology

- [ ] Requirements confirmed with the host ([Low-budget hosting](../operations/low-budget-hosting.md)).
- [ ] HTTPS on every page; HTTP redirects ([Going live: hosts and HTTPS](../../creators/website-creators/going-live-hosts-https.md)).
- [ ] Cron runs, including the `frequent` group ([Cronjobs](../../../guides/cronjobs.md)).
- [ ] With SQLite: the database file is not downloadable ([SQLite and Velocity](../operations/sqlite-and-velocity-for-small-teams.md)).
- [ ] Backups of database, storage and settings run; one restore tested ([Backups and updates routine](../operations/backups-and-updates-routine.md)).
- [ ] Own `[HTMLForms] Secret` set ([Spam protection for volunteer forms](../volunteers/spam-protection-for-volunteer-forms.md)).
- [ ] Mail sending tested.

## Donations

- [ ] Route chosen and written down ([Donation options at a glance](../donations/donation-options-at-a-glance.md)).
- [ ] Giving page complete ([The giving page](../donations/the-giving-page.md)).
- [ ] External platform: link and return page tested; or shop: sandbox and a small live gift tested.
- [ ] Receipts agreed with the accountant.
- [ ] Donate page editable only by named staff.

## Forms and volunteers

- [ ] Volunteer form built from the approved worksheet ([Volunteer form field worksheet](../templates/volunteer-form-field-worksheet.md)).
- [ ] CAPTCHA on public forms; tested logged out, including its accessible alternative.
- [ ] Receivers are shared mailboxes; redirect to thank-you pages.
- [ ] `infocollector/read` only for coordinators.

## Newsletter and communications

- [ ] `cjw_newsletter` lists created; rehearsal with file transport done; real sending tested ([Supporter newsletter](../communications/supporter-newsletter-with-cjw-newsletter.md)).
- [ ] Subscribe link in the footer and on thank-you pages.
- [ ] Event calendar set up, if used ([Events and calendars](../communications/events-and-calendars-for-non-profits.md)).
- [ ] Feeds for news and publications ([Open data, RSS and REST](../transparency/open-data-rss-and-rest.md)).
- [ ] Social sharing metadata checked.

## Accessibility

- [ ] Templates checked ([Accessibility basics](../accessibility/accessibility-basics-for-non-profits.md)).
- [ ] Key pages tested with the keyboard and an automated checker.
- [ ] Accessibility statement published ([Accessibility statement](../accessibility/accessibility-statement-for-non-profits.md)).

## Transparency and privacy

- [ ] Who we are, legal name and registration details shown.
- [ ] Latest annual publication and financial statements online ([Annual publications as downloads](../transparency/annual-publications-as-downloads.md)).
- [ ] Privacy notice names every processor ([Donor and volunteer data protection](../operations/donor-and-volunteer-data-protection.md)).
- [ ] Cookie and analytics decisions taken ([Measuring outcomes](../grants/measuring-outcomes-with-site-data.md)).
- [ ] [Data protection checklist](data-protection-checklist.md) completed.

## People and routine

- [ ] Editors trained ([Training volunteer editors](../volunteers/training-volunteer-editors.md)).
- [ ] Approval workflow for new authors, with two approvers.
- [ ] Monthly checklist has a name and a date in the calendar ([Monthly checklist](monthly-checklist.md)).
- [ ] Starting figures recorded for measures you will report.
- [ ] Who to call when the site is down, written down ([When to call whom](../../customers/partners-and-hosts/when-to-call-whom.md)).

## Sign-off

```text
Project owner ............................ date ________
Technical lead ........................... date ________
Data protection contact .................. date ________
```

## Related pages

- [Non-profit checklists](README.md)
- [Non-profit checklist (Businesses)](../../businesses/nonprofits/nonprofit-checklist.md)
- [Go-live checklist](../../businesses/templates/go-live-checklist.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
