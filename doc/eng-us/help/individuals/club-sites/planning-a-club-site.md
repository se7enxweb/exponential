---
title: Planning a club site
audience: individual, website-creator
category: personal-sites
order: 1711
summary: How a volunteer plans a club or hobby group site on Exponential with the committee - purpose, public and members-only parts, who does what, the tree and classes, mail and cron needs, money questions, and a plan that survives the volunteer moving on.
keywords: club site, planning, committee, volunteers, public, members only, tree, classes, events, newsletter, forms, mail, cron, hosting, budget, handover, continuity
type: help
---
# Planning a club site

This page is for individuals who have agreed to build or take over the site of a club. The hardest part of a club
site is not the software; it is that volunteers change. Plan for that from the first day. Write the answers in the
[site plan worksheet](../checklists/site-plan-worksheet.md).

## Contents

1. [Decide with the committee](#decide-with-the-committee)
2. [Public and members-only](#public-and-members-only)
3. [Who does what](#who-does-what)
4. [The tree and the classes](#the-tree-and-the-classes)
5. [What the server must do](#what-the-server-must-do)
6. [Money](#money)
7. [Continuity](#continuity)

## Decide with the committee

Ask for an hour at a committee meeting and leave with answers:

| Question | Why |
|---|---|
| What is the site for: recruiting, informing members, both? | Decides the home page |
| What must members be able to do online? | Read news, sign up for events, get the newsletter, download documents |
| Who may publish in the club's name? | Roles and approval |
| What must stay internal? | The members' area |
| Who owns the domain and the hosting account? | The club, not a volunteer personally |
| What budget is there? | Hosting, domain, mail ([Running on a small budget](../setup/running-on-a-small-budget.md)) |

## Public and members-only

| Public | Members-only |
|---|---|
| About the club, history | Minutes, rules, member list (if any) |
| How to join, fees as a text | Internal news |
| Meeting times and place, public events | Event sign-ups, results |
| Contact form | Forums, documents |
| Newsletter subscription | Newsletter archive, if desired |

The members' side is a section anonymous visitors cannot read
([Private areas with roles and sections](../family-sites/private-areas-with-roles-and-sections.md)).

## Who does what

| Role | Person | Rights |
|---|---|---|
| Site administrator | You, and one deputy | Everything |
| Editors | The secretary, the events organizer | Write and publish news and events |
| Newsletter sender | The communications volunteer | `cjw_newsletter` |
| Members | Everyone else | Read the members' area, sign up |

Details: [Sharing the work with volunteers](sharing-the-work-with-volunteers.md).

## The tree and the classes

| Folder | Classes | Section |
|---|---|---|
| About | *Article* | Standard |
| Join | *Article*, *Feedback form* | Standard |
| News | *Blog* / *Blog post* or *Folder* / *Article* | Standard |
| Calendar | *Event calendar* / *Event* | Standard |
| Photos | *Gallery* / *Image* | Standard or members |
| Members | *Folder* holding documents (*File*), sign-up forms, forums | Members-only |

All classes ship with the default site package; you need no new class to start.

## What the server must do

| Need | Because | Page |
|---|---|---|
| Send mail reliably | Registration, password reset, form notifications, newsletters | [Newsletter mail delivery](../../businesses/newsletters/newsletter-mail-delivery.md) |
| Run cron | Newsletters are sent by cron; scheduled publishing too | [Cronjobs](../../../guides/cronjobs.md) |
| HTTPS | Members sign in | [Domain names and HTTPS](../setup/domain-names-and-https.md) |
| Backups | Member data | [Backups for one person](../running-it/backups-for-one-person.md) |

Ask any host about mail and cron before you sign up ([Shared hosting or a small server](../setup/shared-hosting-or-a-small-server.md)).

## Money

- **Hosting and domain**: a recurring cost the club should pay from its own account.
- **Fees and dues**: the site can explain them; collecting them online is not built in. Most small clubs keep using
  bank transfer or a payment service and link to it.
- **Help**: if the club can spend a little, a partner can set up what you cannot
  ([When to hire help](../running-it/when-to-hire-help.md)).

## Continuity

- The domain, the hosting account and the administrator password are the club's, recorded where the committee can
  find them (a sealed envelope, a password manager shared with the chair).
- Two administrators, always.
- A one-page handbook inside the members' area: how to post news, how to add an event, how to send the newsletter.
- A handover checklist for the day you step down ([Handover when volunteers change](../../businesses/associations/handover-when-volunteers-change.md)).

## Related pages

- [Club sites](README.md)
- [Small group site planning](../../businesses/associations/small-group-site-planning.md)
- [Member site planning](../../businesses/members/member-site-planning.md)
- [Site plan worksheet](../checklists/site-plan-worksheet.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
