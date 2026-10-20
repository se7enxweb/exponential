---
title: Membership sites: start here
audience: customer, businesses
category: customer-success
order: 60
summary: For clubs, associations, professional bodies and customer communities that run a member site on Exponential: registrations to approve, members-only content, member mail and preferences, account upkeep and the data members can ask for.
keywords: membership site, members, club, association, community, registration, approval, members-only, mail preferences, user accounts, check user accounts, forums, events
type: help
---
# Membership sites: start here

A membership site has two audiences: the public, who should see enough to want to join, and the members, who
expect their area, their mail and their data to be handled with care. Running it means approving or welcoming new
members, keeping members-only content behind the door, sending the right mail to the right people, and tidying up
accounts that are no longer used.

This page is for the membership manager, the association's board member who answers for the site, and the
volunteers who help. Planning a member site is covered in [Member sites for businesses](../../businesses/members/README.md).

## What Exponential gives a membership site

| Need | What carries it | Where it is explained |
|---|---|---|
| People register themselves | User registration, with an approval step where wanted | [Member registration and areas](../../creators/website-creators/member-registration-and-areas.md) |
| You hear about new registrations | The "New user registered" mail with signed links to view, approve or reject the account (6.0.15) | [Registration admin links](../../../features/6.0/registration-admin-links.md) |
| Members-only content | Sections and roles: anonymous visitors see one part, members another | [Members-only content](../../businesses/members/members-only-content.md), [Sections](../../../guides/sections.md) |
| Members choose their mail | E-mail preferences: one page per person to choose and stop mail, and to download their data | [E-mail preferences](../../../features/6.0/mail-preferences.md) |
| Newsletters to members | The CJW Newsletter extension | [Newsletters](../growth/newsletters.md) |
| Events, forums | Event calendar and forum classes on the demonstration installation | [Events and calendars](../../businesses/members/events-and-calendars.md) |
| Old and duplicate accounts found | **Users > Check user accounts** (`user/hygiene`) | [Check user accounts](../../../features/6.0/user-hygiene.md) |

The demonstration installation at <https://alpha.se7enx.com> has the classes Event calendar, Event, Forums, Forum,
Forum topic and Forum reply; whether your site uses them depends on how it was built.

## The membership manager's routines

- **When someone registers**: the "New user registered" mail arrives (if your site sends it). Open the link, check
  the account, approve or reject it. The page asks you to confirm before anything changes
  (`doc/features/6.0/registration-admin-links.md`).
- **Every week**: answer member questions, check that new members can see what they should, publish the member
  news.
- **Every quarter**: open **Users > Check user accounts** and look at the filters *Never signed in*, *Not seen for N
  days* (180 by default), *Disabled*, *Locked* and *Shared e-mail address*. Decide with your board what happens to
  accounts that are no longer used, and write the decision down so it is applied the same way every time.
- **When a member leaves**: disable or remove the account as your rules say, and remove them from lists they no
  longer want ([Data export and erasure](../commerce/data-export-and-erasure.md)).

> **Note**
> Member data is personal data. What you may keep, for how long and what a member may ask for depends on the law
> where you are. These pages describe what the software offers; they are not legal advice.

## Volunteers and roles

Many associations run on volunteers who change every year. Give each task its own role (membership manager, news
editor, events editor) instead of giving everyone administrator rights, and keep a handover list for when a
volunteer leaves ([Onboarding new staff](../day-to-day/onboarding-new-staff.md),
[Offboarding staff](../day-to-day/offboarding-staff.md)).

## Your next step

Follow the [membership site reading path](membership-site-reading-path.md).

## Related pages

- [Membership site reading path](membership-site-reading-path.md)
- [Member site checklist (businesses)](../../businesses/members/member-site-checklist.md)
- [Member communications](../../businesses/members/member-communications.md)
- [Associations](../../businesses/associations/README.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
