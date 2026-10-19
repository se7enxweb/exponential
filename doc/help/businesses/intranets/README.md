---
title: Intranets
audience: businesses, project-manager, admin
category: business-solutions
order: 1
summary: The landing page for organizations that run their staff intranet on Exponential - what the section covers, who it is for, every page with one line, a suggested reading order and the neighboring sections.
keywords: intranet, staff portal, employee portal, departments, roles, policies, sections, documents, file library, search, news, announcements, approval, workflow, two-factor, LDAP, directory, notifications, rollout
type: help
---
# Intranets

This section is for organizations that want one private website for their own staff: a place for news, policies,
forms, documents and department pages that only employees can open. It is written for the managers who decide on an
intranet and for the person who will run it. Each page says in plain language what Exponential offers for the task
and then links to the technical page for whoever sets it up.

## Contents

1. [What an intranet on Exponential is](#what-an-intranet-on-exponential-is)
2. [The pages of this section](#the-pages-of-this-section)
3. [A suggested order](#a-suggested-order)
4. [What you need before you start](#what-you-need-before-you-start)
5. [Neighboring sections](#neighboring-sections)
6. [Related pages](#related-pages)

## What an intranet on Exponential is

An intranet built with Exponential is an ordinary Exponential site whose visitors must sign in. The building blocks
are all part of the kernel that every installation has:

| Building block | What it gives an intranet | Source |
|---|---|---|
| A siteaccess with `RequireUserLogin=true` | every page asks for a sign-in first | [Member registration and members-only areas](../../creators/website-creators/member-registration-and-areas.md#a-members-only-area) |
| User groups, roles and policies | departments, editors, readers, and who may do what | [Roles, policies and unactivated users](../../../guides/roles-and-policies.md) |
| Sections | areas of content that only some groups may read or edit | [Sections](../../../guides/sections.md#132-an-intranet-with-departments) |
| Object states | stages such as Draft, In review, Approved | [Object states](../../../guides/object-states.md) |
| Workflows | approval before publishing | [Workflows](../../../guides/workflows.md) |
| Files and `content/download` | a document library whose downloads respect permissions | [Files and downloads](../../creators/website-creators/files-and-downloads.md) |
| Search | staff find only what they may read | [Site search](../../creators/website-creators/site-search.md) |
| Notifications | mail when something is published below an item a user follows | [Notifications: the user's guide](../../../features/6.0/notifications.md) |
| The audit trail | who did what, with a tamper-evident record | [The audit trail](../../../features/6.0/audit-trail.md) |

Two more pieces are available as extensions or settings: two-step sign-in (the `sevenx_authentication_2fa`
extension, listed in `composer.json`) and sign-in against a company directory (the kernel's LDAP and text file login
handlers, configured in `settings/ldap.ini` and `settings/textfile.ini`).

> **Note**
> This section describes what the repository contains. It names no customers and quotes no statistics. Where a
> feature does not exist, the page says so.

## The pages of this section

| Page | What it answers |
|---|---|
| [Intranet planning](intranet-planning.md) | What to decide before the first page is built: audience, structure, owners, budget questions |
| [Departments, roles and sections](departments-roles-and-sections.md) | How departments become user groups, sections and limited roles |
| [Documents and file libraries](documents-and-file-libraries.md) | Storing policies, forms and files so that only the right people can download them |
| [Intranet search](intranet-search.md) | The built-in search, Solr through `ezfind`, and what staff can find |
| [News and announcements](news-and-announcements.md) | Publishing company news, front pages, feeds and "notify me" |
| [Approval workflows](approval-workflows.md) | Approval before publishing, and review stages with object states |
| [Two-factor and sign-in](two-factor-and-sign-in.md) | Passwords, lockouts, two-step sign-in and social login |
| [Directory sign-in and LDAP](directory-sign-in-and-ldap.md) | What the LDAP and text file login handlers really do, and what is not built in |
| [Notifications for staff](notifications-for-staff.md) | Subscriptions, digests, collaboration mail and the cronjob behind them |
| [Intranet rollout checklist](intranet-rollout-checklist.md) | A checklist from the first test to the day staff are invited |
| [Intranet FAQ](intranet-faq.md) | Short answers to the questions managers ask most |

## A suggested order

1. Read [Intranet planning](intranet-planning.md) with the people who will own the intranet.
2. Decide the structure in [Departments, roles and sections](departments-roles-and-sections.md).
3. Decide how staff sign in: [Two-factor and sign-in](two-factor-and-sign-in.md) and
   [Directory sign-in and LDAP](directory-sign-in-and-ldap.md).
4. Plan the content: [Documents and file libraries](documents-and-file-libraries.md),
   [News and announcements](news-and-announcements.md), [Intranet search](intranet-search.md).
5. Decide who approves what: [Approval workflows](approval-workflows.md) and
   [Notifications for staff](notifications-for-staff.md).
6. Work through the [Intranet rollout checklist](intranet-rollout-checklist.md).
7. Keep the [Intranet FAQ](intranet-faq.md) at hand for questions from colleagues.

## What you need before you start

- [ ] An Exponential installation, or a partner or host who provides one
  ([Choosing hosting](../choosing/choosing-hosting.md)).
- [ ] A host name for the intranet and an HTTPS certificate
  ([Going live: hosts and HTTPS](../../creators/website-creators/going-live-hosts-https.md)).
- [ ] A named owner for the intranet and one for each department area.
- [ ] A list of the groups of staff and what each group may read and edit.
- [ ] A decision on sign-in: local accounts, a directory, two-step sign-in.
- [ ] A working mail setup for activation, password reset and notifications
  ([Mail delivery](../../hosting/operations/mail-delivery.md)).

## Neighboring sections

- [Extranets](../extranets/README.md): the same tools pointed at partners, customers, dealers and suppliers.
- [Member sites](../members/README.md): registration from the public, members-only content and events.
- [Security](../security/README.md): access reviews, accounts and passwords, GDPR basics.
- [Reliability](../reliability/README.md): keeping the intranet available every working day.
- [Costs](../costs/README.md), including the [worked example for an intranet](../costs/worked-example-intranet.md).
- Public bodies: Governments start here.

## Related pages

- Businesses: start here
- Which section fits your organization
- [Planning an Exponential project](../../leadership/planning-an-exponential-project.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
