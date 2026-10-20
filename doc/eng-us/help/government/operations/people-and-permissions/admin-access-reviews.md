---
title: Admin access reviews
audience: government, admin, project-manager
category: public-sector
order: 150
summary: A periodic review of who holds which rights on a public website - administrators, role changers, account administrators, auditors and departments - using the role list badges, role pages, Check user accounts, the CSV export and the audit trail, with a record an auditor can follow.
keywords: access review, user access review, recertification, entitlement review, admin rights, full access, Can change roles, role list, audit, CSV export, quarterly review, ISO 27001, NIST
type: help
---
# Admin access reviews

This page is for administrators and managers who must recertify access rights on a schedule, as many security
frameworks and internal audit plans ask (for example the access control themes of
[ISO/IEC 27001](https://www.iso.org/standard/27001) and the
[NIST Cybersecurity Framework](https://www.nist.gov/cyberframework)). It shows where Exponential gives you each answer
and how to leave a record.

## Contents
1. [What the review answers](#what-the-review-answers)
2. [Step 1: the roles](#step-1-the-roles)
3. [Step 2: the people](#step-2-the-people)
4. [Step 3: what happened](#step-3-what-happened)
5. [Step 4: act and record](#step-4-act-and-record)
6. [Schedule](#schedule)
7. [Related pages](#related-pages)

## What the review answers

1. Who has full access, and should they?
2. Who can change roles (and so give themselves more)?
3. Does each other role still match its purpose?
4. Is every account still needed, personal and protected?
5. Was any right granted outside the process since the last review?

## Step 1: the roles

**Users > Roles and policies** (`/role/list`). The list shows badges per role:

| Badge | Meaning | Review question |
|---|---|---|
| *Full access* (red) | a policy over every module | is each assignment a named administrator? |
| *Can change roles* (amber) | a policy over the `role` module | is it limited to system administrators? |
| *Not assigned* | gives nobody anything | remove it, or document why it is kept |

Order the list by number of assignments to see the widest roles first. Open each role: the page shows its policies as
sentences, the users and groups it is assigned to with their limitations, and **users affected in all**
([Roles guide, section 2](../../../../guides/roles-and-policies.md#2-read-one-role)). Compare with your role record
([Roles, policies and limitations](roles-policies-and-limitations.md#documenting-access-for-an-audit)). A limitation
marked *(no handler, denies)* gives no access and should be cleaned up.

## Step 2: the people

**Users > Check user accounts** (`/user/hygiene`):

- Filter **Not seen for N days** and **Never signed in**: accounts nobody uses.
- Each row lists the roles an account has, its own and inherited from groups; search for the administrators found
  in step 1.
- **Shared e-mail address**: shared accounts that should be personal.
- **Export the list as CSV** for the review file
  ([Export the account list for a review](../../tasks/people-and-permissions/export-the-account-list-for-a-review.md)).

On each administrator's account page (`/user/edit/<id>`) check the hint about two-step sign-in and the date of the
last password change ([Security and audit, step 6](../../../../guides/security-and-audit.md#6-check-an-account-on-its-account-page-2-minutes)).

## Step 3: what happened

In the **Audit** tab (`/audit/console`), or on the shell, look at the `access` channel since the last review:

```bash
./console exp:audit search --name='access.*' --from='<date of last review>' --limit=50 --allow-root-user
./console exp:audit alerts list --allow-root-user
```

The alert rule `admin_role_granted` fires whenever an administrator role is granted; each fired alert should match a
request in your ticket system. Check that the chain is intact: `./console exp:audit verify --allow-root-user`
([Audit trail verification](../monitoring/audit-trail-verification.md)).

## Step 4: act and record

| Finding | Action |
|---|---|
| Administrator not needed | remove the assignment on the role page (**Remove selected**) |
| Role broader than its purpose | edit the policies; save; note the change |
| Unused account | **Disable** on Check user accounts |
| Shared account | create personal accounts; disable the shared one |
| Grant without a ticket | investigate; see [Suspected account compromise](../incidents/suspected-account-compromise.md) |

Record: date, reviewer, the CSV export, the list of roles with badges, findings and actions. Role changes and
disabling are in the audit trail themselves, which links the record to the evidence.

## Schedule

Exponential does not schedule reviews. Common practice is quarterly for full access and role changers, twice a year
for everything else, and at once when a contract ends or a team reorganizes. Put the date in your calendar and your
access policy ([Least-privilege principles](least-privilege-principles.md#a-one-page-access-policy)).

## Related pages
- [Audit who has admin rights](../../tasks/people-and-permissions/audit-who-has-admin-rights.md)
- [User account hygiene](user-account-hygiene.md)
- [Separation of duties](separation-of-duties.md)
- [Security review checklist (hosting)](../../../hosting/checklists/security-review.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
