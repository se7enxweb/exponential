---
title: Remove a leaver
audience: government, admin
category: public-sector
order: 50
summary: Step by step - on a staff member's last day, disable the account so it cannot sign in, hand over drafts and approvals, remove direct assignments, and decide whether to retire the account or remove it for good.
keywords: leaver, offboarding, last day, disable account, Check user accounts, Disable, sessions end, handover, remove user, retire account
type: help
---
# Remove a leaver

This task is for administrators and service-desk staff. At the end, a colleague who left can no longer sign in, and
their content, drafts and approval duties are handed over. "Remove" here means removing access: removing the account
itself is final and rarely the right choice. Background:
[Joiners, movers and leavers](../../operations/people-and-permissions/joiners-movers-leavers.md).

## Contents
1. [Before you start](#before-you-start)
2. [Disable the account](#disable-the-account)
3. [Hand over](#hand-over)
4. [Retire or remove](#retire-or-remove)
5. [Check it worked](#check-it-worked)
6. [Related pages](#related-pages)

## Before you start

- The leaving date, from HR or the manager.
- The function `user/hygiene` (Users > Check user accounts) and `content/edit` on the user account; administrators
  have both.

## Disable the account

1. Open **Users > Check user accounts** (`/user/hygiene`).
2. Type the name or login in **Find an account**.
3. Tick the account and press **Disable**. The confirmation says how many accounts will change; press **Confirm**.
4. The account can no longer sign in and its sessions end. Content, groups and roles stay. The audit trail records
   `access.user.disable`.

## Hand over

1. **Drafts**: drafts are visible on **My drafts** only to their author; ask the person, before they leave, to publish
   or remove them. Afterwards an administrator can find drafts on each object's versions page.
2. **Approvals**: remove the person from approvers' groups and from **Users who approve content** of any Approve
   event (**Setup > Workflows**). Waiting requests that named only them must be cancelled and resent
   ([Workflow troubleshooting](../../operations/editorial-workflows/workflow-troubleshooting.md)).
3. **Direct role assignments**: on each role page that lists the person, tick the assignment and **Remove selected**.
4. **Ownership**: if roles use `Owner = Self`, change the owner of their content with **Change owner**
   ([Content ownership](../../operations/editorial-workflows/content-ownership.md)).
5. **Shell and server access**, debug IP entries and API keys are separate from the account; remove them under your
   server access process.

## Retire or remove

| Choice | How | Effect |
|---|---|---|
| Keep disabled | nothing more | account stays where it is, cannot sign in |
| Deactivate and move | [Deactivate and move a leaver](deactivate-and-move-a-leaver.md) | disabled, moved into a hidden group without roles; reversible |
| Remove | the account node's **Remove** | final: "A user object never goes to the trash in Exponential" ([Check user accounts](../../../../features/6.0/user-hygiene.md#in-short)) |

Most public bodies keep the account disabled or deactivated, because content and the audit trail refer to it.

## Check it worked

- **Check user accounts**, filter **Disabled**: the account is listed.
- A sign-in attempt with the account fails.
- The role pages no longer list the person directly.

Change record: date disabled, handover done, request reference.

## Related pages
- [Deactivate and move a leaver](deactivate-and-move-a-leaver.md)
- [Remove a role assignment](remove-a-role-assignment.md)
- [Sign out all sessions of a user](../operations/sign-out-all-sessions-of-a-user.md)
- [Check user accounts](../../../../features/6.0/user-hygiene.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
