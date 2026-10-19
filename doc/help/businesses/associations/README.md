---
title: Associations and small groups
audience: businesses, editor, project-manager
category: business-solutions
order: 1
summary: The landing page of the associations section - what clubs, societies, user groups, parent associations and other volunteer-run groups can run on Exponential, the pages of the section, and the order to read them.
keywords: association, club, society, small group, volunteer-run, user group, community group, events, news, volunteer editors, handover
type: help
---
# Associations and small groups

This section is for clubs, societies, user groups, parent and neighborhood associations, and any group run mostly by
volunteers. Such groups share three problems: editors change every year or two, nobody is paid to look after the
site, and the site must still carry the group's news, events and forms reliably. The pages explain how to set up
Exponential so that a changing team of volunteers can run it, and how to hand it over cleanly.

## Contents

1. [What a small group typically needs](#what-a-small-group-typically-needs)
2. [The pages of this section](#the-pages-of-this-section)
3. [Suggested reading order](#suggested-reading-order)
4. [Neighboring sections](#neighboring-sections)

## What a small group typically needs

| Need | What exists in Exponential | Page |
|---|---|---|
| Several volunteers editing safely | Personal accounts, roles limited to a subtree or section, versions of every change | [Shared editing with volunteers](shared-editing-with-volunteers.md) |
| News and an events calendar | The `ezwebin` classes `article`, `event`, `event_calendar`, `blog`, and feeds | [Events and news for groups](events-and-news-for-groups.md) |
| Contact, feedback, polls, registrations | Information collector forms with the types form, feedback and poll | [Forms and member feedback](forms-and-member-feedback.md) |
| Taking over when volunteers leave | Account checks, owner change, password reset, audit trail | [Handover when volunteers change](handover-when-volunteers-change.md) |
| Low running cost | No license fee; SQLite as the default database | [Low-budget operations](../nonprofits/low-budget-operations.md) |

The `ezwebin` site interface, which ships these classes (`extension/ezwebin/packages/`), and the `ezownerchange`
extension used at handover are both in the `require` block of `composer.json`.

> **Note**
> Larger member organizations with registration, member-only areas and member communication should also read the
> [Members](../members/README.md) section. Charities and foundations will find donations and campaigns in
> [Non-profits](../nonprofits/README.md).

## The pages of this section

| Page | What it answers |
|---|---|
| [Small group site planning](small-group-site-planning.md) | What the site is for, the few pages a group needs, who does what, and a planning worksheet |
| [Shared editing with volunteers](shared-editing-with-volunteers.md) | Accounts, simple roles, approval, versions, house rules |
| [Events and news for groups](events-and-news-for-groups.md) | News, an events calendar, feeds, notifications and a newsletter |
| [Forms and member feedback](forms-and-member-feedback.md) | Contact and feedback forms, polls, registrations, ratings, and who reads them |
| [Handover when volunteers change](handover-when-volunteers-change.md) | A clean handover of accounts, content ownership, knowledge and access |
| [Small group checklist](small-group-checklist.md) | One checklist from planning to the yearly handover |

## Suggested reading order

1. [Small group site planning](small-group-site-planning.md)
2. [Shared editing with volunteers](shared-editing-with-volunteers.md)
3. [Events and news for groups](events-and-news-for-groups.md)
4. [Forms and member feedback](forms-and-member-feedback.md)
5. [Handover when volunteers change](handover-when-volunteers-change.md): read it before you need it.
6. [Small group checklist](small-group-checklist.md)

## Neighboring sections

- [Non-profits](../nonprofits/README.md): donations, volunteer sign-up, campaigns, accessibility, low budgets.
- [Members](../members/README.md): member registration and member-only content.
- [Newsletters](../newsletters/README.md): mail to members and supporters.
- [Libraries and catalogs](../libraries/README.md): for groups that keep a collection.
- [Security and data protection](../security/README.md): accounts, access reviews, GDPR basics.
- Public bodies have their own section: Government: start here.

## Related pages

- [Businesses: start here](../businesses-start-here.md)
- [Which section fits your organization?](../which-section-fits-your-organization.md)
- [Handing the site over to editors](../../creators/website-creators/handing-over-to-editors.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
