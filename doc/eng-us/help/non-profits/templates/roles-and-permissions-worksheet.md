---
title: Roles and permissions worksheet
audience: non-profit, businesses, admin, project-manager
category: non-profits
order: 40
summary: A worksheet for deciding who in a non-profit may do what on its Exponential site - the jobs (author, editor, approver, layouts editor, form processor, account manager, translator, administrator), the shipped roles Anonymous, Member, Editor and Administrator, limits by section, subtree and language, two-factor login, and a yearly review - with one row per person or group.
keywords: roles, permissions, worksheet, Editor, Administrator, Member, Anonymous, section limitation, subtree, language, infocollector/read, user/hygiene, two-factor login, approval workflow, volunteers, staff
type: help
---
# Roles and permissions worksheet

Fill this in before you create accounts, and review it every year. It applies the advice of
[Roles for your team](../../customers/day-to-day/roles-for-your-team.md) and
[Volunteers as editors](../volunteers/volunteers-as-editors.md) to a non-profit, where many editors are volunteers
who come and go.

## Contents

1. [The roles you start with](#the-roles-you-start-with)
2. [Step 1: list the jobs](#step-1-list-the-jobs)
3. [Step 2: one row per group](#step-2-one-row-per-group)
4. [Step 3: the sensitive permissions](#step-3-the-sensitive-permissions)
5. [Step 4: review](#step-4-review)

## The roles you start with

A standard installation works with these roles ([Roles for your team](../../customers/day-to-day/roles-for-your-team.md#the-roles-you-start-with)):

| Role | Who has it | In short |
|---|---|---|
| Anonymous | Every visitor not signed in | Reading the public site |
| Member | Registered visitors | Signing in, members-only reading |
| Editor | The editorial team | Creating and editing content in the administration |
| Administrator | Very few people | Everything |

Assign roles to **groups**, not single users, and limit them by section or subtree where you can.

## Step 1: list the jobs

Tick the jobs your organization has and name who does them:

| Job | Needs | We have it? | Done by |
|---|---|---|---|
| Author | Write drafts, not publish (Editor role behind an approval workflow) | | |
| Editor | Write and publish in their area | | |
| Approver | Approve pending versions ([Approvals and review](../../customers/day-to-day/approvals-and-review.md)) | | |
| Layouts editor | Change campaign and landing pages (`explayouts` policies) | | |
| Form processor | Read and export submissions (`infocollector/read`) | | |
| Newsletter editor | Write and send newsletter editions | | |
| Account manager | Review and disable accounts (`user/hygiene`) | | |
| Translator | Translate into one language (limited by Language) | | |
| Shop staff | Handle donation orders, if you use the shop | | |
| Administrator | Everything; two people at most | | |

## Step 2: one row per group

| Group | Role(s) | Limited to (section, subtree, language) | Two-factor login | Members |
|---|---|---|---|---|
| Administrators | Administrator | none | Required | |
| Staff editors | Editor | | Required | |
| Volunteer authors | Editor + approval workflow | e.g. section "News" | Recommended | |
| Volunteer coordinators | `infocollector/read` | | Required | |
| Translators | Editor, translate | Language: | Recommended | |
| Board members (reading board papers) | Member + board section | section "Board" | Recommended | |
| _your group_ | | | | |

Two-factor login: [Two-factor login](../../../guides/two-factor-login.md). Sections for limits:
[Sections and object states](../../creators/website-creators/sections-and-object-states.md).

## Step 3: the sensitive permissions

Write the names of the few people who hold each of these:

| Permission | Why it is sensitive | Held by |
|---|---|---|
| Administrator (full access) | Can do everything | |
| A role that may change roles | Holders can give themselves any access | |
| `infocollector/read` | Reads volunteers' personal data | |
| `audit/read` | Reads the audit trail | |
| Shop orders | Sees donors' names and addresses | |
| Newsletter subscribers | Sees supporters' mail addresses | |

## Step 4: review

| When | What | Read |
|---|---|---|
| A volunteer leaves | Deactivate the account and move it out of the group, the same week | [When volunteers leave](../operations/when-volunteers-leave.md) |
| Every quarter | Run **Check user accounts** for accounts not used for a long time | [Check user accounts](../../../features/6.0/user-hygiene.md) |
| Every year | Go through this worksheet line by line | [Access reviews](../../businesses/security/access-reviews.md) |

```text
Reviewed by ............................ date ________
Changes made ........................... ______________________
```

## Related pages

- [Templates and worksheets](README.md)
- [Team roles worksheet](../../customers/day-to-day/team-roles-worksheet.md)
- [Roles and policies](../../../guides/roles-and-policies.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
