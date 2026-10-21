---
title: Volunteers as editors
audience: non-profit, editor, admin, businesses
category: non-profits
order: 60
summary: How a non-profit lets volunteers edit its Exponential site without risking it - one account per person, groups and roles limited by section, approval before publishing with the cronjob it needs, two-step sign-in with sevenx_authentication_2fa, versions and the trash to undo mistakes, the audit trail, layouts editing one at a time, and pages only staff may change.
keywords: volunteer editors, roles, groups, section limitation, approval workflow, pending, cronjob, two-factor login, sevenx_authentication_2fa, versions, trash, audit trail, layouts drafts, house rules
type: help
---
# Volunteers as editors

This page is for staff who decide who may change the site, and the administrator who sets it up. Many non-profit sites
are kept alive by volunteers who write news, update events and translate pages. That works well when each volunteer can
do exactly their job and every mistake can be undone. The same advice for small groups is in
[Shared editing with volunteers](../../businesses/associations/shared-editing-with-volunteers.md); this page adapts it
to a non-profit with paid staff and volunteers side by side.

## Contents

1. [Principles](#principles)
2. [Accounts](#accounts)
3. [Groups and roles](#groups-and-roles)
4. [Pages only staff may change](#pages-only-staff-may-change)
5. [Approval before publishing](#approval-before-publishing)
6. [Two-step sign-in](#two-step-sign-in)
7. [Undoing mistakes](#undoing-mistakes)
8. [Who did what](#who-did-what)
9. [Editing at the same time](#editing-at-the-same-time)
10. [Checklist](#checklist)

## Principles

- **One account per person.** No shared logins.
- **The smallest role that does the job.**
- **Every mistake reversible.** Versions, trash, approval.
- **Every action traceable.** The audit trail, without blame.

## Accounts

"One personal account per person; no shared logins"
([Handing the site over to editors](../../creators/website-creators/handing-over-to-editors.md#accounts-and-roles)).
When a volunteer leaves, you disable one account instead of changing a password everyone knew
([When volunteers leave](../operations/when-volunteers-leave.md)).

## Groups and roles

Assign roles to user groups, not to people; a new volunteer then just joins a group. Policies can be limited to
sections, classes, subtrees, languages or siteaccesses ([Roles and policies](../../../guides/roles-and-policies.md)).

| Group | Role | Limited to |
|---|---|---|
| Volunteer authors | Create and edit drafts, publish through approval | Their section: news, events, a project's pages |
| Volunteer translators | Translate | One language ([Translator roles and permissions](../../government/languages/translator-roles-and-permissions.md)) |
| Staff editors | Publish | The public sections |
| Coordinators | `infocollector/read` | Nothing else |
| Administrators | Everything | Two people, staff where possible |

Fill in the [Roles and permissions worksheet](../templates/roles-and-permissions-worksheet.md) before creating
accounts.

> **Warning**
> A policy over every module gives full access, and a policy over the role module lets its holders give themselves any
> access. Keep both for administrators ([Shared editing with volunteers](../../businesses/associations/shared-editing-with-volunteers.md#a-few-simple-roles)).

## Pages only staff may change

Some pages carry legal or financial weight. Put them in a section volunteer roles do not cover:

- the Donate page and the giving page ([Donor trust and thank-you](../donations/donor-trust-and-thank-you.md));
- the privacy notice and the accessibility statement;
- annual publications and financial documents ([Transparency](../transparency/README.md));
- board pages and governance documents.

Sections are explained in [Sections and object states](../../creators/website-creators/sections-and-object-states.md).

## Approval before publishing

With an approval workflow, a volunteer's version waits as "pending" until an approver decides, and the workflow cronjob
publishes it once approved. "Without that cronjob, approved content is never published"
([Workflows](../../../guides/workflows.md), [Approvals and review](../../customers/day-to-day/approvals-and-review.md)).

Keep it light: approval for new authors in their first months, direct publishing for experienced ones. Name at least
two approvers so nothing waits during holidays.

## Two-step sign-in

Two-step sign-in adds a code from an authenticator app or an e-mail after the password. It is provided by
`sevenx_authentication_2fa`, which `composer.json` requires ([Two-factor login](../../../guides/two-factor-login.md)).
Use it for everyone who can publish, and always for administrators and coordinators: volunteers often reuse passwords
across sites.

## Undoing mistakes

- **Versions.** Every publication is a version; editors can compare two versions and publish an old one again
  ([Versions of an object](../../../guides/content-history.md), [Drafts, versions and undo](../../customers/day-to-day/drafts-versions-and-undo.md)).
- **Trash.** Removed content can be restored, and the trash view shows who trashed it
  ([Archiving and the trash](../../customers/day-to-day/archiving-and-the-trash.md)).
- **Exception:** user accounts do not go to the trash; removing one is final
  ([Check user accounts](../../../features/6.0/user-hygiene.md)).

Tell volunteers this on their first day. People edit better when they are not afraid of breaking something.

## Who did what

The audit trail records content changes, sign-ins and more, on by default
([The audit trail](../../../features/6.0/audit-trail.md)). It answers "who changed the event date?" without
guessing, and shows the next coordinator what happened before them. Reading it needs `audit/read`.

## Editing at the same time

- Content: each editor works in their own draft.
- Layouts: "Drafts are per layout, not per user: two people editing the same layout overwrite each other"
  ([Landing pages with layouts](../../creators/website-creators/landing-pages-with-layouts.md#drafts-and-publishing)).
  Agree that one person edits a campaign or home page layout at a time.

## Checklist

- [ ] One account per person; groups, not individuals, hold roles.
- [ ] Volunteer roles limited to their sections.
- [ ] Sensitive pages in a staff-only section.
- [ ] Approval workflow for new authors; cron running; two approvers named.
- [ ] Two-step sign-in for everyone who publishes.
- [ ] Volunteers trained ([Training volunteer editors](training-volunteer-editors.md)).

## Related pages

- [Volunteers](README.md)
- [Roles for your team](../../customers/day-to-day/roles-for-your-team.md)
- [Choosing roles for new staff](../../customers/day-to-day/choosing-roles-for-new-staff.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
