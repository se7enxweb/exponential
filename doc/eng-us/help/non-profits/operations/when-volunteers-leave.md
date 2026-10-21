---
title: When volunteers leave
audience: non-profit, admin, businesses, editor
category: business-solutions
order: 60
summary: What a non-profit does on its Exponential site when a volunteer, staff member or board member leaves - deactivating the account (Check user accounts, and the opt-in Deactivate and move), removing group membership, changing content ownership, handing over passwords and knowledge, reading the audit trail, and a checklist - so the site never depends on one person.
keywords: offboarding, volunteer leaves, account deactivation, Check user accounts, Deactivate and move, user hygiene, group membership, content owner, handover, administrator password, audit trail, knowledge transfer
type: help
---
# When volunteers leave

This page is for the coordinator and the administrator. Volunteers come and go; so do board members and staff. A site
that depends on one person's account, password or memory is at risk every time someone leaves. The fuller story for
small groups is [Handover when volunteers change](../../businesses/associations/handover-when-volunteers-change.md).

## Contents

1. [The same week someone leaves](#the-same-week-someone-leaves)
2. [Deactivating accounts](#deactivating-accounts)
3. [Content they owned](#content-they-owned)
4. [When the person held the keys](#when-the-person-held-the-keys)
5. [Reading what happened](#reading-what-happened)
6. [Prevention](#prevention)
7. [Leaver checklist](#leaver-checklist)

## The same week someone leaves

| Task | Why |
|---|---|
| Disable the account | A dormant account is a door |
| Remove them from groups (editors, volunteers, board) | Roles come from groups |
| Remove them as approver in workflows | Otherwise approvals wait for nobody |
| Change the receiver of forms that mailed them | Applications would go to a gone person |
| Take over shared mailboxes and accounts at outside services | The donation platform, mail delivery, domain |

## Deactivating accounts

- **Check user accounts** lists accounts by filters (for example not signed in for a long time) and acts on them
  ([Check user accounts](../../../features/6.0/user-hygiene.md)).
- **Deactivate and move** (opt-in, `[UserSettings] DeactivateAndMove=enabled`) disables accounts and moves all their
  locations into one hidden group; **Reactivate and move back** puts them back
  ([Deactivate and move](../../../features/6.0/user-hygiene.md#deactivate-and-move)). This suits volunteers who may
  return.
- "A user object never goes to the trash", so removing an account is final (same page). Prefer deactivating.

Owner's view: [Offboarding staff](../../customers/day-to-day/offboarding-staff.md).

## Content they owned

Content stays when an account is disabled. Make sure someone is now responsible for each page they looked after:

- update the "owner" in your content inventory;
- change the object's owner where it matters ([Changing owners](../../../guides/changing-owners.md));
- move notifications to the new owner ([Notifications](../../../features/6.0/notifications.md)).

## When the person held the keys

If an administrator leaves:

- Change passwords of every shared account they knew (hosting panel, domain registrar, donation platform).
- Make sure at least two administrators remain.
- If nobody knows the administrator password, there is a way to reset it
  ([Reset user password](../../../features/6.0/reset-user-password.md),
  [Change the admin password](../../change-the-admin-password.md)).
- Take over their two-factor devices: their codes leave with them.

## Reading what happened

The audit trail shows what an account did before it left, without blame
([The audit trail](../../../features/6.0/audit-trail.md)). Useful when a page changed and nobody remembers why
([Versions and the audit trail](../transparency/versions-and-the-audit-trail.md)).

## Prevention

- One account per person, never shared ([Volunteers as editors](../volunteers/volunteers-as-editors.md)).
- Two administrators, at least one of them staff or a board member.
- Domain, hosting and service accounts in the organization's name.
- Settings, routines and passwords documented in a place the organization controls.
- A quarterly run of **Check user accounts**.

## Leaver checklist

- [ ] Account disabled (or deactivated and moved).
- [ ] Removed from groups and approver lists.
- [ ] Form receivers and notifications changed.
- [ ] Shared passwords changed, if they knew any.
- [ ] Pages they owned have a new owner.
- [ ] Their exports or files with personal data returned or deleted.
- [ ] Noted in the [Roles and permissions worksheet](../templates/roles-and-permissions-worksheet.md).

## Related pages

- [Operations for non-profits](README.md)
- [Handover when volunteers change](../../businesses/associations/handover-when-volunteers-change.md)
- [Taking over a site](../../customers/partners-and-hosts/taking-over-a-site.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
