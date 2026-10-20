---
title: Shared editing with volunteers
audience: businesses, editor, admin
category: business-solutions
order: 20
summary: How a small group lets several volunteers edit its Exponential site safely - one account per person, a few simple roles limited to sections, two-step sign-in, optional approval before publishing, versions to undo mistakes, the audit trail, and house rules everyone can follow.
keywords: volunteer editors, shared editing, roles, policies, permissions, two-step sign-in, approval workflow, versions, undo, audit trail, house rules, small group
type: help
---
# Shared editing with volunteers

This page is for the web coordinator or administrator of a club, society or other volunteer-run group. It explains how
to let several volunteers edit without stepping on each other, how to limit the damage a mistake can do, and how to
undo it. It keeps to the few features a small group needs; the full guides are linked.

## Contents

1. [One account per person](#one-account-per-person)
2. [A few simple roles](#a-few-simple-roles)
3. [Two-step sign-in](#two-step-sign-in)
4. [Approval before publishing](#approval-before-publishing)
5. [Undoing mistakes with versions](#undoing-mistakes-with-versions)
6. [Who did what](#who-did-what)
7. [Editing the same page at the same time](#editing-the-same-page-at-the-same-time)
8. [House rules](#house-rules)
9. [Getting volunteers started](#getting-volunteers-started)

## One account per person

"One personal account per person; no shared logins"
([Handing the site over to editors, Accounts and roles](../../creators/website-creators/handing-over-to-editors.md#accounts-and-roles)).
For a group this matters more than for a company: when a volunteer leaves, you disable one account instead of changing
a password everyone knew, and the audit trail can tell who did what.

## A few simple roles

"A **role** is a set of **policies**", and "a role does nothing until it is **assigned** to users or user groups"
([Roles, policies and unactivated users](../../../guides/roles-and-policies.md)). A small group rarely needs more than
four:

| Role | Can | Limit it to |
|---|---|---|
| Author | Create and edit drafts | Their section (news, events, a working group's pages) |
| Editor | Publish | The public sections |
| Form reader | Read form submissions (`infocollector/read`) | Nothing else |
| Administrator | Everything | Two people only |

Policies can be limited to sections, classes, subtrees, languages or siteaccesses (same guide), so the events team can
edit only the calendar. Assign roles to **user groups** ("Authors", "Editors") rather than to individuals; a new
volunteer then just joins a group.

> **Warning**
> The roles guide marks two kinds of role in color: a policy over every module gives full access (red), and a policy
> over the role module lets its users give themselves any access (amber). Keep both for the administrators.

## Two-step sign-in

Two-step sign-in adds a code from an authenticator app or an e-mail after the password, so "the password alone is then
useless to anyone else". It is provided by `sevenx_authentication_2fa`, which `composer.json` requires and the
installers activate; it takes about 15 minutes to turn on and 2 minutes per user to set up
([Two-step sign-in](../../../guides/two-factor-login.md)). Use it at least for everyone who can publish; volunteers
often reuse passwords across sites.

## Approval before publishing

If new volunteers should not publish directly, use an approval workflow: "before publishing content, run the approval
workflow". The version then waits as "pending" until an approver decides, and the workflow cronjob publishes it once
approved ([Workflows](../../../guides/workflows.md)). The guide has a worked example
([Approval before publishing](../../../guides/workflows.md#3-worked-example-approval-before-publishing)).

Keep it light: approval for new authors in their first months, direct publishing for experienced editors. "Without that
cronjob, approved content is never published", so check that cron runs.

## Undoing mistakes with versions

Every publication of an object is a version. The versions page lets editors compare two versions, make a new draft from
an old one and publish it, and remove old drafts ([Versions of an object](../../../guides/content-history.md)). For editors, see also
[Publishing and versions](../../publishing-and-versions.md). Tell volunteers this on their first day: most mistakes are
one click from undone, so they need not be afraid to edit.

Content moved to the trash can be restored with its **Restore** button, and the trash view shows who trashed an item
and where it was ([The trash view](../../../bc/6.0/trash.md)). User accounts are the exception: "A user object never
goes to the trash", so removing one is final ([Check user accounts](../../../features/6.0/user-hygiene.md)).

## Who did what

The audit trail records "sign-ins and refusals, content changes, settings writes, cache clears, deploys, orders and
VAT changes" in a tamper-evident log that "is on by default, needs no setup"
([The audit trail](../../../features/6.0/audit-trail.md)). For a group it answers the question "who changed the
meeting date?" without blame, and it shows the next administrator what happened before them.

## Editing the same page at the same time

- Content: each editor works in their own draft; the versions page shows drafts by others.
- Layouts: "Drafts are per layout, not per user: two people editing the same layout overwrite each other"
  ([Landing pages with layouts](../../creators/website-creators/landing-pages-with-layouts.md#drafts-and-publishing)).
  Agree that one person edits the home page layout at a time.

## House rules

From [Handing the site over to editors, House rules for content](../../creators/website-creators/handing-over-to-editors.md#house-rules-for-content),
plus two for groups:

- [ ] Titles short and clear; they become addresses.
- [ ] Every image with an alternative text.
- [ ] Link texts that say where they lead.
- [ ] Headings in order; no bold paragraphs as headings.
- [ ] Remove, do not hide forever; ask for a redirect when a page goes.
- [ ] No personal data of members on public pages.
- [ ] Ask before changing a page another volunteer owns.

## Getting volunteers started

1. Create the account in the right user group; ask them to set up two-step sign-in.
2. Walk through the admin tour with your own content ([The admin tour](../../admin-tour.md)); "a training session
   following the admin tour with the site's own content takes most editors from zero to working"
   ([Handing the site over to editors, Training](../../creators/website-creators/handing-over-to-editors.md#training)).
3. Give them the house rules and the help pages [Getting started](../../getting-started.md) and
   [Editing content](../../editing-content.md).
4. Let them follow the pages they look after, so they get notified of changes ([Notifications](../../../features/6.0/notifications.md)).

## Related pages

- [Associations and small groups](README.md)
- [Handover when volunteers change](handover-when-volunteers-change.md)
- [Roles, policies and unactivated users](../../../guides/roles-and-policies.md)
- [Access reviews](../security/access-reviews.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
