---
title: Offboarding staff
audience: customer, admin, project-manager
category: customer-success
order: 7
summary: What to do on the last working day of an editor, freelancer or agency on an Exponential site - disable rather than remove, hand over drafts and approvals, revoke API keys and tokens, and check the result - with a record to keep.
keywords: offboarding, leaver, disable account, deactivate and move, user hygiene, API keys, tokens, drafts, approvals, access removal
type: help
---
# Offboarding staff

This page is for the manager and the account manager when someone leaves the team, a freelancer's contract ends or an
agency hands back the work. Access should end on the last working day, not at the next yearly review
([Access reviews](../../businesses/security/access-reviews.md)). In Exponential the safe way is to **disable** the
account rather than remove it.

## Contents

1. [Disable, do not remove](#disable-do-not-remove)
2. [Before the last day](#before-the-last-day)
3. [On the last day](#on-the-last-day)
4. [After the last day](#after-the-last-day)
5. [Record](#record)

## Disable, do not remove

- A user object never goes to the trash: removing a user is final
  ([Check user accounts](../../../features/6.0/user-hygiene.md)).
- **Disable** on **Users > Check user accounts** stops the account from signing in and ends its sessions. Its
  content, groups and roles stay, so the history of who wrote what is kept.
- If your administrator switched on **Deactivate and move** (`site.ini [UserSettings] DeactivateAndMove=enabled`,
  off by default), the account is disabled and moved into one hidden group without roles; **Reactivate and move back**
  undoes it ([Check user accounts](../../../features/6.0/user-hygiene.md)).

Each change is recorded in the audit trail (`access.user.disable`, channel *access*), so you have evidence of the
date ([The audit trail](../../../features/6.0/audit-trail.md)).

## Before the last day

- [ ] Ask the person to finish or hand over their **drafts**. Drafts are personal: only their creator sees them on
      *My drafts* ([My drafts and my pending items](../../../guides/drafts-and-pending.md)). Someone else can make a
      new draft from any version on the versions page ([Versions of an object](../../../guides/content-history.md)).
- [ ] If they are an **approver**, add a replacement to the approval workflow's approver users or groups first, so
      nothing waits for a person who is gone ([Workflows](../../../guides/workflows.md)).
- [ ] If they receive form mails, newsletter approvals or notifications on behalf of the team, move those to a role
      address or a colleague.
- [ ] List the layouts, newsletters and campaigns they own and name a new owner.

## On the last day

1. **Disable the account** on **Users > Check user accounts** (or the account's **Edit user settings**).
2. **Revoke API keys and tokens.** Personal API keys end when the owner is disabled or removed, and administrators
   can revoke any key at **Setup > oAuth admin > API keys** ([Personal API keys](../../../guides/api-keys.md)).
   Remote service tokens of a disabled user answer 401
   ([Remote services](../../../features/6.0/remote-services-expservices.md)). Check both lists anyway.
3. **Remove role assignments made to the person directly** (not through a group), if any.
4. If the person had administrator access, ask your host or partner whether they also had server, database or
   control panel access, and have that closed too.

> **Warning**
> Do not delete the account to "clean up". Their content stays, but the account cannot be restored, and you lose
> the link between the person and what they did.

## After the last day

- Check that pending versions they sent are decided: *My pending items* of the approvers, or **Setup > Workflow
  processes** ([Workflows](../../../guides/workflows.md)).
- At the next review, filter **Disabled** and **Not seen for N days** on Check user accounts to confirm nothing was
  missed.
- Update the [Team roles worksheet](team-roles-worksheet.md).

## Record

```text
OFFBOARDING RECORD
Person or company: ______________________   Last working day: __________
Drafts handed over to: ______________   Approver replaced by: ______________
Account disabled on: __________   by: ______________
API keys / tokens revoked? yes / none found
Direct role assignments removed? yes / none
Server or panel access closed (asked host/partner)? yes / not applicable
Checked by (manager): ______________
```

## Related pages

- [Onboarding new staff](onboarding-new-staff.md)
- [Accounts, passwords and two-step sign-in](accounts-passwords-and-two-factor.md)
- [Access reviews](../../businesses/security/access-reviews.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
