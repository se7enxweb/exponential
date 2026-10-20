---
title: Member site planning
audience: businesses, project-manager, website-creator
category: business-solutions
order: 10
summary: What a member organization decides before building its site on Exponential - membership levels, what is public and what is for members, how people join, how members hear from the organization, events, volunteers who run it, and personal data.
keywords: member site planning, membership levels, members area, public content, registration, approval, newsletter, events, volunteers, personal data, GDPR, budget, requirements
type: help
---
# Member site planning

This page is for the board, the managing director or the communications lead of a member organization. It lists the
decisions that shape a member site, and for each decision names the Exponential feature that answers it and the page
that explains it. A short planning round before the first page is built saves rework later.

## Contents

1. [Why members come to the site](#why-members-come-to-the-site)
2. [Public and members-only](#public-and-members-only)
3. [Membership levels](#membership-levels)
4. [How people join](#how-people-join)
5. [How members hear from you](#how-members-hear-from-you)
6. [Events and community](#events-and-community)
7. [Who runs the site](#who-runs-the-site)
8. [Personal data](#personal-data)
9. [What is not built in](#what-is-not-built-in)
10. [Planning worksheet](#planning-worksheet)
11. [Related pages](#related-pages)

## Why members come to the site

Write down the three or four reasons a member would sign in. Typical reasons:

- news and publications for members;
- documents: minutes, guidelines, templates, recordings;
- events: what, when, where, and how to sign up;
- the newsletter and its archive;
- a place to discuss with other members.

Each reason becomes an area of the site and a page of this section.

## Public and members-only

| Content | Usually |
|---|---|
| Who you are, why join, contact | public |
| News teasers | public |
| Full publications, member documents | members only |
| Event announcements | public or members only |
| Event documents, recordings | members only |
| Forum | members only |

In Exponential the difference is a **section**: content in a members section is readable only by roles that include
that section, and the default Anonymous role reads Section Standard only
([Sections, 13.1](../../../guides/sections.md#131-a-members-area)). Visitors who open a members page are asked to sign
in ([Members-only content](members-only-content.md)).

## Membership levels

If some members get more than others (for example full members and associate members), each level is a user group,
and each level's extra content is a section that only that group's role reads. Keep levels few; every level is a role
to maintain and a combination to test.

| Level | User group | Reads |
|---|---|---|
| Associate member | Associates | Members section |
| Full member | Full members | Members section, Full members section |
| Board | Board | all of the above, Board section |

## How people join

| Way | Fits when | Page |
|---|---|---|
| Open registration with e-mail confirmation | anyone may join, for free | [Member registration](member-registration.md) |
| Registration approved by the organization | membership must be checked | [Registration and account approval](../extranets/registration-and-account-approval.md) |
| Accounts created by the office | members join on paper or through another system | [Member registration](member-registration.md#accounts-created-by-the-office) |

## How members hear from you

- A newsletter with double opt-in, editions sent by cron, bounces handled
  ([Member communications](member-communications.md)).
- Notifications when new content appears in areas a member follows.
- Every person chooses on one page which optional mail they get, and every optional mail has a one-click unsubscribe
  ([E-mail preferences: the user's guide](../../../features/6.0/mail-preferences.md)).

## Events and community

Event pages and calendars come with the `ezwebin` classes; sign-up uses a form. Forums are available as classes of
`ezwebin` too ([Events and calendars](events-and-calendars.md)).

## Who runs the site

Member organizations often rely on volunteers. Plan:

- an owner on the board or staff;
- editors per area, each with a role limited to their section;
- someone who approves new members;
- a host or partner for the technical side ([Choosing hosting](../choosing/choosing-hosting.md)).

The [Associations](../associations/README.md) section covers working with volunteers and handing over when they
change.

## Personal data

A member site stores names, e-mail addresses, sign-in records and, depending on the classes, more (a date of birth
with the `birthday` datatype, for example). Collect only what you need, say why on the registration form, and agree
retention with your data protection contact ([GDPR basics](../security/gdpr-basics.md),
[Personal data in Exponential](../security/personal-data-in-exponential.md)). This page is not legal advice.

## What is not built in

Searched in the repository on 19 October 2026: recurring membership fees, automatic membership expiry, event
ticketing with seat limits, and a member directory page are not documented features. A one-off fee can be sold as a
product through the built-in shop ([Stores](../stores/README.md)); the rest are extension projects or integrations to
plan with a partner ([Choosing a partner](../choosing/choosing-a-partner.md)).

## Planning worksheet

- [ ] The reasons members sign in.
- [ ] Public and members-only content, area by area.
- [ ] Membership levels and their groups.
- [ ] How people join, and who approves.
- [ ] Newsletter and notifications plan.
- [ ] Events plan.
- [ ] Owner, editors, approver and host named.
- [ ] Personal data list and retention.
- [ ] Integrations for fees or ticketing, if needed.

## Related pages

- [Member sites](README.md)
- [Member site checklist](member-site-checklist.md)
- [Small group site planning](../associations/small-group-site-planning.md)
- [Planning your website](../../creators/website-creators/planning-your-website.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
