---
title: Choosing roles for new staff
audience: customer, admin, project-manager
category: customer-success
order: 3
summary: A short decision guide for the owner or manager when a new person joins - which group and role to ask for, which limits to add, what to leave out, and how to write the request so the administrator can act on it.
keywords: new staff, roles, user group, editor, approver, translator, least privilege, access request, limitation, onboarding
type: help
---
# Choosing roles for new staff

This page is for the manager who has to tell the administrator what a new colleague may do. The rule is simple: give
the smallest role that lets the person do their job, in the smallest part of the site, and add more only when it is
needed. Exponential makes this practical because a role can be limited to a section, a subtree or a language
([Roles and policies](../../../guides/roles-and-policies.md)).

## Contents

1. [Five questions](#five-questions)
2. [Common answers](#common-answers)
3. [What to leave out](#what-to-leave-out)
4. [Writing the request](#writing-the-request)

## Five questions

1. **What will they do in the first month?** Write, publish, approve, translate, process forms, run the store?
2. **Where in the tree?** The whole site, one department's folder, one brand of a multisite?
3. **In which languages?** All, or only the one they translate into?
4. **Must their work be checked before it goes live?** Then they belong behind an approval
   ([Approvals and review](approvals-and-review.md)).
5. **Do they need the administration at all?** Members and customers sign in on the public site only.

## Common answers

| New person | Group and role to ask for | Limits |
|---|---|---|
| Editor in a department | the editors' group with the Editor role | assigned with a Subtree or Section limitation for the department ([Departments, roles and sections](../../businesses/intranets/departments-roles-and-sections.md)) |
| Junior writer or intern | the editors' group, kept out of the approval's *Excluded user groups* so their versions wait for an approver | the same as above, in a section the approval covers |
| Approver or editor-in-chief | added to the approver group of the approval workflow | the workflow's affected sections ([Workflows](../../../guides/workflows.md)) |
| Translator or agency | a translator role | edit, translate and create limited by Language ([Translation workflow](../../businesses/multilingual/translation-workflow.md)) |
| Person who answers contact forms | a role with `infocollector/read` | nothing else ([Collected information](../../../guides/collected-information.md)) |
| Store staff | a shop role your partner set up | the store's part of the tree ([Store dashboard](../../../features/6.0/store-dashboard.md)) |
| Temporary helper | the smallest of the above | an end date in your own notes, see [Offboarding staff](offboarding-staff.md) |

> **Tip**
> If two people need "a bit more" than the Editor role, ask for a second role with only that extra and assign both.
> Changing the shared Editor role changes it for everyone who has it.

## What to leave out

- **Full access.** Administrator is for the few people who run the installation, not for the person who "needs to
  change one setting".
- **The role module.** Whoever may change roles can give themselves anything; the role list marks such a role amber.
- **Account review** (`user/hygiene`) and **audit** policies: keep them with the account manager and the owner.
- **Shared accounts.** One personal account per person; no shared logins
  ([Handing the site over to editors](../../creators/website-creators/handing-over-to-editors.md)).

## Writing the request

Send the administrator a request they can act on without asking back:

```text
ACCESS REQUEST
Name: ______________________   Start date: __________   End date (if temporary): __________
Job on the site: ______________________________________________
Group: ______________________   Role(s): ______________________
Limited to subtree/section: ______________________   Languages: ______________
Behind an approval? yes / no      Two-step sign-in required? yes / no
Requested by: ______________   Approved by (site owner): ______________
```

Keep the request: it is the record you compare with at the yearly
[access review](../../businesses/security/access-reviews.md).

## Related pages

- [Roles for your team](roles-for-your-team.md)
- [Onboarding new staff](onboarding-new-staff.md)
- [Team roles worksheet](team-roles-worksheet.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
