---
title: Small group checklist
audience: businesses, project-manager, editor, admin
category: business-solutions
order: 60
summary: One checklist for a club, society or volunteer-run group on Exponential - planning, volunteer editing, events and news, forms, data protection, operations, launch, the monthly routine and the yearly handover - each item linked to the page that explains it.
keywords: small group checklist, club website checklist, association, volunteers, events, forms, handover, monthly routine, yearly review
type: help
---
# Small group checklist

This page is for the web coordinator of a club, society or other volunteer-run group. It collects the checks of this
section in the order you meet them, from planning to the yearly handover. Copy it into a document the committee
shares; every item links to the page that explains it.

## Contents

1. [Planning](#planning)
2. [Volunteer editing](#volunteer-editing)
3. [Events and news](#events-and-news)
4. [Forms](#forms)
5. [Data protection](#data-protection)
6. [Operations](#operations)
7. [Launch](#launch)
8. [Every month](#every-month)
9. [Every year, and at every handover](#every-year-and-at-every-handover)
10. [Sign-off](#sign-off)

## Planning

From [Small group site planning](small-group-site-planning.md):

- [ ] Two or three things the site must do, written down.
- [ ] Pages listed; shipped `ezwebin` classes used instead of custom ones.
- [ ] Roles named (coordinator, editors, form reader, administrator, deputy), not people.
- [ ] Public, members-only and committee-only content separated.
- [ ] Domain, hosting, backups and sender addresses recorded where the committee controls them.

## Volunteer editing

From [Shared editing with volunteers](shared-editing-with-volunteers.md):

- [ ] One personal account per person; no shared logins.
- [ ] Roles assigned to user groups, limited to sections.
- [ ] Full-access and role-editing policies only for the two administrators.
- [ ] Two-step sign-in for everyone who can publish.
- [ ] Approval for new authors, if wanted, with the workflow cronjob running.
- [ ] House rules given to every volunteer; admin tour done with the site's own content.

## Events and news

From [Events and news for groups](events-and-news-for-groups.md):

- [ ] News folder and event calendar from the shipped classes.
- [ ] An events editor named.
- [ ] Feeds for news and events active; members told how to follow or subscribe.
- [ ] Home page shows next events and latest news automatically.

## Forms

From [Forms and member feedback](forms-and-member-feedback.md):

- [ ] Contact and join forms, each with a role address as receiver.
- [ ] Redirect to a thank-you page.
- [ ] Token on, CAPTCHA added, tested logged out.
- [ ] `infocollector/read` only for the people who answer.

## Data protection

Basics, not legal advice ([Security and data protection](../security/README.md)):

- [ ] No personal data of members on public pages.
- [ ] Privacy notice covers forms, CAPTCHA provider and any newsletter.
- [ ] Deletion schedule for form submissions and exports.
- [ ] A named role answers access and erasure requests.

## Operations

From [Low-budget operations](../nonprofits/low-budget-operations.md):

- [ ] Hosting model chosen; database chosen (SQLite unless many editors publish at once).
- [ ] Backups scheduled and a restore tested.
- [ ] Updates applied on a schedule, tested on a copy first.
- [ ] Cron running.

## Launch

- [ ] The website creator's [launch checklist](../../creators/website-creators/launch-checklist.md) passed.
- [ ] HTTPS on every host.
- [ ] Editors handed over with a short editor guide
      ([Handing the site over to editors](../../creators/website-creators/handing-over-to-editors.md)).

## Every month

- [ ] Calendar current; past events archived.
- [ ] Form submissions answered; old ones removed on schedule.
- [ ] Logs looked at; backups verified.
- [ ] Accounts not seen for a long time reviewed in **Users > Check user accounts**
      ([Check user accounts](../../../features/6.0/user-hygiene.md)).

## Every year, and at every handover

From [Handover when volunteers change](handover-when-volunteers-change.md):

- [ ] Registrar, hosting, mail and password manager access transferred to the new holders.
- [ ] Incoming administrator added before the outgoing one is retired.
- [ ] Leavers' accounts disabled (or deactivated and moved); content owners changed where needed.
- [ ] Editor guide, house rules and the list of who does what updated.
- [ ] Features nobody used removed; the site kept small.
- [ ] A date for the next review in the group's calendar.

## Sign-off

| Area | Checked by (role) | Date |
|---|---|---|
| Planning | | |
| Volunteer editing | | |
| Events and news | | |
| Forms | | |
| Operations | | |
| Handover | | |

## Related pages

- [Associations and small groups](README.md)
- [Non-profit checklist](../nonprofits/nonprofit-checklist.md)
- [Annual review checklist](../templates/annual-review-checklist.md)
- [Monthly operations checklist](../templates/monthly-operations-checklist.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
