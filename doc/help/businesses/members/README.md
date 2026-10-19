---
title: Member sites
audience: businesses, project-manager, website-creator
category: business-solutions
order: 1
summary: The landing page for organizations that run a member site on Exponential - clubs, professional bodies, communities and customer clubs with registration, members-only content, newsletters and events - with every page of the section, a suggested order and the neighboring sections.
keywords: member site, members area, membership, club, association, community, registration, members-only content, newsletter, events, calendar, forums
type: help
---
# Member sites

This section is for organizations whose website has members: professional associations, clubs, alumni networks,
customer clubs and communities. Members register, sign in, read what only members may read, receive newsletters and
come to events. The section is written for the people who run the organization and links to the technical pages for
whoever builds the site.

## Contents

1. [What a member site on Exponential is](#what-a-member-site-on-exponential-is)
2. [The pages of this section](#the-pages-of-this-section)
3. [A suggested order](#a-suggested-order)
4. [Member sites compared with intranets and extranets](#member-sites-compared-with-intranets-and-extranets)
5. [Neighboring sections](#neighboring-sections)
6. [Related pages](#related-pages)

## What a member site on Exponential is

A member site is a public website with a members area. The pieces come from the kernel and from extensions listed in
`composer.json`:

| Need | Feature | Source |
|---|---|---|
| People register and confirm their address | `user/register`, e-mail verification | [Member registration and members-only areas](../../creators/website-creators/member-registration-and-areas.md) |
| Pages only members may read | a members section that the Anonymous role does not read | [Sections, 13.1](../../../guides/sections.md#131-a-members-area) |
| Newsletters with double opt-in | `cjw_newsletter` | [cjw_newsletter](../../../features/6.0/extensions/cjw_newsletter.md) |
| Mail when new content appears | notifications | [Notifications: the user's guide](../../../features/6.0/notifications.md) |
| Each person chooses what mail they get | e-mail preferences | [E-mail preferences: the user's guide](../../../features/6.0/mail-preferences.md) |
| Events and calendars | the `event`, `event_calendar` and `multicalendar` classes of `ezwebin` | [Events and calendars](events-and-calendars.md) |
| Discussions | the forum classes of `ezwebin` | [ezwebin](../../../features/6.0/extensions/ezwebin.md) |

> **Note**
> This section describes what the repository and the demonstration installation contain. It names no member
> organizations and quotes no statistics. Where something is not built in, such as recurring membership fees or
> event ticketing, the page says so.

## The pages of this section

| Page | What it answers |
|---|---|
| [Member site planning](member-site-planning.md) | What to decide first: membership levels, what is public, who runs the site |
| [Member registration](member-registration.md) | How people join, confirm and sign in, and how the organization approves them |
| [Members-only content](members-only-content.md) | Areas, levels and downloads that only members can open |
| [Member communications](member-communications.md) | Newsletters, notifications and e-mail preferences |
| [Events and calendars](events-and-calendars.md) | Event pages, calendars and sign-up forms, and what is not built in |
| [Member site checklist](member-site-checklist.md) | Everything to tick before inviting members |
| [Member site FAQ](member-site-faq.md) | Short answers to common questions |

## A suggested order

1. [Member site planning](member-site-planning.md) with the board or management of the organization.
2. [Member registration](member-registration.md) and [Members-only content](members-only-content.md) to decide how
   the site is open and closed.
3. [Member communications](member-communications.md) and [Events and calendars](events-and-calendars.md) for the
   activities members come for.
4. [Member site checklist](member-site-checklist.md) before the launch.
5. Keep the [Member site FAQ](member-site-faq.md) for questions from members and volunteers.

## Member sites compared with intranets and extranets

| | Member site | Intranet | Extranet |
|---|---|---|---|
| Who signs in | members of the public who join | staff | people of partner organizations |
| Public part | large | none | small or none |
| How accounts arrive | open registration, sometimes approved | directory or administrators | approved registration or created by staff |
| Typical extras | newsletters, events, forums | documents, approval | files, ordering |

## Neighboring sections

- [Associations and small groups](../associations/README.md): running a site with volunteers.
- [Non-profits](../nonprofits/README.md): donations, volunteers and campaigns.
- [Newsletters](../newsletters/README.md): the newsletter system in depth.
- [Extranets](../extranets/README.md) and [Intranets](../intranets/README.md): the same tools for partners and staff.
- [Security](../security/README.md): GDPR basics, consent and accounts.
- Public bodies: Governments start here.

## Related pages

- [Businesses: start here](../businesses-start-here.md)
- [Which section fits your organization](../which-section-fits-your-organization.md)
- [What visitors may see: the Anonymous role](../../creators/website-creators/visitor-permissions.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
