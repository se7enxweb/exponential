---
title: Handover when volunteers change
audience: businesses, admin, editor, project-manager
category: business-solutions
order: 50
summary: How a small group hands its Exponential site from one volunteer team to the next without losing access or knowledge - what to hand over, retiring accounts with Check user accounts and Deactivate and move, changing content owners, resetting a lost administrator password, reading the audit trail, and a handover checklist.
keywords: handover, volunteer change, committee change, user accounts, deactivate, change owner, ezownerchange, reset password, audit trail, continuity, small group
type: help
---
# Handover when volunteers change

This page is for the outgoing and the incoming web coordinator or administrator of a club, society or other
volunteer-run group. Committees change every year or two; the site should not have to be rebuilt each time. This page
lists what to hand over, the Exponential features that make it clean, and what to do when the previous administrator
can no longer be reached.

![Workflow processes waiting, to check before a volunteer leaves](../../images/help/bu-workflow.webp)

## Contents

1. [What a handover covers](#what-a-handover-covers)
2. [Before the handover](#before-the-handover)
3. [Retiring accounts](#retiring-accounts)
4. [Changing who owns content](#changing-who-owns-content)
5. [Roles move with groups, not people](#roles-move-with-groups-not-people)
6. [When nobody knows the administrator password](#when-nobody-knows-the-administrator-password)
7. [Reading what happened before you](#reading-what-happened-before-you)
8. [After the handover](#after-the-handover)
9. [Handover checklist](#handover-checklist)

## What a handover covers

| Area | Hand over |
|---|---|
| Access outside the site | Domain registrar, hosting account, mail account, password manager |
| Access inside the site | Administrator role for the newcomers, retirement of leavers' accounts |
| Content | Who owns which pages; who edits which section |
| Knowledge | The short editor guide, the house rules, where backups are, the monthly routine |
| Open work | Drafts, pending approvals, unanswered form submissions |

The editor guide described in [Handing the site over to editors](../../creators/website-creators/handing-over-to-editors.md#the-editor-guide)
is the core document: it names "roles, not ... people who may leave".

## Before the handover

- [ ] Update the editor guide and the list of who does what.
- [ ] Make a fresh backup and confirm it can be restored
      ([Backups and restores](../../creators/website-creators/backups-and-restores.md)).
- [ ] Note open drafts and pending approvals; waiting workflow processes are listed in **Setup > Workflow processes**
      ([Workflows](../../../guides/workflows.md)).
- [ ] Process or export open form submissions ([Forms and member feedback](forms-and-member-feedback.md#tidying-up)).
- [ ] Move form receivers and the newsletter sender to role addresses if they still point to a person.

## Retiring accounts

**Users > Check user accounts** (`user/hygiene`) lists accounts with filters such as **Never signed in**, **Not seen
for N days** (180 by default), **Disabled** and **Locked**, and can **Disable**, **Enable** and **Unlock** ticked
accounts after a confirmation ([Check user accounts](../../../features/6.0/user-hygiene.md)).

Prefer disabling to removing: "A user object never goes to the trash in Exponential ... removing it is final" (same
page). A disabled account keeps the person's content and history and can sign in again if they come back.

**Deactivate and move** goes further: it disables accounts and moves all their locations into one hidden group
without roles, remembering where they were; **Reactivate and move back** reverses it. It is opt-in:
`site.ini [UserSettings] DeactivateAndMove=disabled` ships, and an administrator switches it on
([Deactivate and move](../../../features/6.0/user-hygiene.md#deactivate-and-move)). For a group whose volunteers often
return, it keeps the user list clean without losing anyone.

## Changing who owns content

Every content object has an owner, and roles can limit editing to one's own content (the `Owner` limitation). When
someone leaves, use **Change owner** in the context menu of a node, with a preview of every object the change reaches,
for one object or a whole subtree. It needs the policies `owner/change` (and `owner/subtree` for a subtree), and each
change is recorded in the audit as `content.ezownerchange.change`
([Changing the owner of content](../../../guides/changing-owners.md)). The `ezownerchange` extension is required by
`composer.json`, and "new installations have it switched on" (same guide).

## Roles move with groups, not people

If roles are assigned to user groups ("Editors", "Form readers"), a handover is a matter of moving people between
groups: the newcomer joins "Editors", the leaver is disabled. Roles assigned to individuals have to be found and
reassigned one by one ([Roles, policies and unactivated users](../../../guides/roles-and-policies.md)). See also
[Shared editing with volunteers](shared-editing-with-volunteers.md#a-few-simple-roles).

Two administrators at all times: the incoming one is added before the outgoing one is retired.

## When nobody knows the administrator password

It happens when the only administrator has gone. Someone with access to the server can set a new password with
`bin/php/resetuserpassword.php`, for example generating a new 20-character password for `admin`; "the generated
password is printed once" ([Reset a user password from the command line](../../../features/6.0/reset-user-password.md)).
This needs access to the server, which is one more reason to keep the hosting account in the group's hands.

> **Warning**
> Change the password of every account the previous team shared, and turn on two-step sign-in for the new
> administrators ([Two-step sign-in](../../../guides/two-factor-login.md)).

## Reading what happened before you

The audit trail records sign-ins and refusals, content changes, settings writes and more, is on by default and can be
read in the administration or on the command line ([The audit trail](../../../features/6.0/audit-trail.md)). A new
administrator can see which accounts were active recently and what changed in the last months, without having to ask.
The log files are in the log viewer ([Log viewer](../../../features/6.0/log-viewer.md)).

## After the handover

- [ ] The newcomers sign in, set up two-step sign-in and edit a test page.
- [ ] Leavers' accounts disabled (or deactivated and moved); their content re-owned where needed.
- [ ] The editor guide shows the new role holders.
- [ ] The monthly routine has new names next to each task
      ([Monthly operations checklist](../templates/monthly-operations-checklist.md)).
- [ ] A date for the next handover review is in the group's calendar.

## Handover checklist

| Item | Outgoing | Incoming | Done |
|---|---|---|---|
| Domain registrar access transferred | | | |
| Hosting account access transferred | | | |
| Mail and newsletter sender addresses are role addresses | | | |
| Password manager access transferred | | | |
| Backup made and restore tested | | | |
| Incoming administrator added, two-step sign-in on | | | |
| Leavers' accounts disabled, roles via groups checked | | | |
| Content owners changed where needed | | | |
| Open drafts, approvals and form submissions handed over | | | |
| Editor guide and house rules updated | | | |

## Related pages

- [Associations and small groups](README.md)
- [Shared editing with volunteers](shared-editing-with-volunteers.md)
- [Access reviews](../security/access-reviews.md)
- [User accounts and passwords](../security/user-accounts-and-passwords.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
