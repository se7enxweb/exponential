---
title: Roles for your team
audience: customer, admin, project-manager
category: customer-success
order: 2
summary: How a site owner thinks about who on the team may do what - the roles a standard installation ships, the jobs people actually do, limits by section, subtree and language, and the permissions to keep for a few people only.
keywords: roles, policies, permissions, team, editor role, administrator, member, anonymous, least privilege, section limitation, subtree, site owner
type: help
---
# Roles for your team

This page is for the owner or manager who decides who on the team may do what. In Exponential every permission comes
from a **role**: a set of **policies**, each allowing one module or function, possibly limited to sections, classes,
subtrees, languages or siteaccesses. A role does nothing until it is **assigned** to users or user groups
([Roles and policies](../../../guides/roles-and-policies.md)). Your administrator makes the changes; this page helps
you decide what to ask for.

## Contents

1. [The roles you start with](#the-roles-you-start-with)
2. [Think in jobs, not in people](#think-in-jobs-not-in-people)
3. [Limit by place and language](#limit-by-place-and-language)
4. [Permissions to keep for a few people](#permissions-to-keep-for-a-few-people)
5. [How often to look](#how-often-to-look)

## The roles you start with

The demonstration installation at `https://alpha.se7enx.com` shows the roles a standard installation works with
(read on the live installation, October 2026):

| Role | Who has it | What it gives, in short |
|---|---|---|
| **Anonymous** | every visitor who is not signed in | reading the public parts of the site |
| **Member** | registered visitors | signing in, their own account, members-only reading |
| **Editor** | the editorial team | signing in to the administration, creating and editing content, translations, versions, the dashboard, two-step sign-in, help |
| **Administrator** | very few people | everything, in every module |

The Administrator role is "full access": the role list marks it in red. A role that may change roles is marked amber,
because its holders can give themselves any access ([Roles and policies](../../../guides/roles-and-policies.md)).
Your own site may have more roles that your partner added; ask for the list.

> **Note**
> Prefer assigning roles to **groups**, not to single users: every member of the group, now and later, gets the role.
> The users tree has groups such as Administrator users, Editors and Members
> ([Handing the site over to editors](../../creators/website-creators/handing-over-to-editors.md)).

## Think in jobs, not in people

List the jobs on your team, then give each job one role. The website creators' handover lists the usual ones:
author, editor (publishes), layouts editor, form processor (`infocollector/read`) and shop staff
([Handing the site over to editors](../../creators/website-creators/handing-over-to-editors.md)).

| Job | Typically needs | Ask your administrator for |
|---|---|---|
| Author | write drafts, not publish | an editor role behind an approval workflow ([Approvals and review](approvals-and-review.md)) |
| Editor | write and publish | the Editor role, limited to the team's part of the tree |
| Approver | decide on pending versions | membership of the approver group of the approval workflow |
| Layouts editor | change landing pages | the `explayouts` policies ([Landing pages with layouts](../growth/landing-pages-with-layouts.md)) |
| Form processor | read and export form submissions | `infocollector/read` ([Collected information](../../../guides/collected-information.md)) |
| Account manager | review and disable accounts | `user/hygiene` ([Check user accounts](../../../features/6.0/user-hygiene.md)) |
| Translator | translate into one language | translate and edit limited by **Language** ([Translation routine](translation-routine.md)) |

The [Team roles worksheet](team-roles-worksheet.md) turns this table into a form you fill in.

## Limit by place and language

A role can be assigned **with a limitation**: only in a subtree or only in a section. One shared Editor role,
assigned to each department's group with a Section limitation, means one role to maintain instead of one per team
([Departments, roles and sections](../../businesses/intranets/departments-roles-and-sections.md)). The policies to
edit, translate and create content can also be limited by Language
([Content languages](../../../guides/content-languages.md)).

## Permissions to keep for a few people

| Permission | Why it is sensitive |
|---|---|
| Any role with full access | can do everything |
| The `role` module | can grant itself any access |
| `infocollector/read` | reads every form submission, which can be personal data |
| `user/hygiene` | sees the name, login, states, groups and roles of every account |
| `audit/read`, `audit/manage` | reads or manages the audit trail |

Sources: [Access reviews](../../businesses/security/access-reviews.md),
[Check user accounts](../../../features/6.0/user-hygiene.md). `PreventPrivilegeEscalation` is enabled by default in
`settings/site.ini`, so nobody can grant more than they hold.

## How often to look

- When someone joins, changes job or leaves: [Onboarding new staff](onboarding-new-staff.md),
  [Offboarding staff](offboarding-staff.md).
- Once a year, a full review: [Access reviews](../../businesses/security/access-reviews.md).
- After your partner delivers a change that adds a module or a role: ask what it gives and to whom.

## Related pages

- [Choosing roles for new staff](choosing-roles-for-new-staff.md)
- [Team roles worksheet](team-roles-worksheet.md)
- [Roles and policies](../../../guides/roles-and-policies.md)
- [Access reviews](../../businesses/security/access-reviews.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
