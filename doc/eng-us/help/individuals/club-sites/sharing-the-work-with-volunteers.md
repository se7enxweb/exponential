---
title: Sharing the work with volunteers
audience: individual, website-creator, customer
category: website-creation
order: 1716
summary: How the one volunteer who runs a club site on Exponential stops being the only one - roles for editors and a deputy administrator, approval before publishing, a small handbook, two-step sign-in, and a handover that works when volunteers change.
keywords: volunteers, sharing work, editors, roles, Editor role, deputy, approval workflow, handbook, handover, continuity, two-factor, passwords, club, committee, bus factor
type: help
---
# Sharing the work with volunteers

This page is for individuals who run a club site and do not want to be the only person who can post the match
result or reset a password. Exponential is built for several people with different rights; using that early keeps
the site alive when you are busy, ill or leave.

## Contents

1. [Who needs which rights](#who-needs-which-rights)
2. [Roles that ship](#roles-that-ship)
3. [Setting up a volunteer editor](#setting-up-a-volunteer-editor)
4. [Approval before publishing](#approval-before-publishing)
5. [The deputy administrator](#the-deputy-administrator)
6. [A one-page handbook](#a-one-page-handbook)
7. [Handover](#handover)
8. [Checklist](#checklist)

## Who needs which rights

| Volunteer | Needs | Does not need |
|---|---|---|
| News writer | Create and edit news and events | Users, settings, classes |
| Photographer | Upload images into galleries | Everything else |
| Newsletter sender | The newsletter module | Content outside the newsletter |
| Membership secretary | Move users between groups, disable accounts | Settings, classes |
| Deputy administrator | Everything | |

Give each person the least they need. A volunteer who can only edit news cannot break the site by accident.

## Roles that ship

A new installation has an *Editor* role and an *Administrator* role, and the default site package adjusts the
Editor's policies during installation (`sevenx-multi-site-installer.php`). Look at **Users > Roles** to see exactly
what each allows on your site ([Roles and policies](../../../guides/roles-and-policies.md),
[Roles for your team](../../customers/day-to-day/roles-for-your-team.md)).

For narrower volunteers, copy the Editor role and limit it to a subtree (only News and Calendar) or to certain
classes ([Choosing roles for new staff](../../customers/day-to-day/choosing-roles-for-new-staff.md)).

## Setting up a volunteer editor

1. Create a user group "Volunteers" and assign it a role limited to the parts they edit.
2. Create the volunteer's personal account in that group ([Inviting family members](../family-sites/inviting-family-members.md#creating-an-account-yourself) works the same way).
3. Show them the editor or administration once ([Logging into the editor](../../logging-into-the-editor.md),
   [Editing content](../../editing-content.md)).
4. Ask them to turn on two-step sign-in ([Two-factor sign-in](../running-it/two-factor-sign-in.md)).
5. Check their first post together.

## Approval before publishing

If several people write in the club's name, an approval workflow holds a volunteer's changes until an approver
publishes them ([Approvals and review](../../customers/day-to-day/approvals-and-review.md),
[Workflows](../../../guides/workflows.md)). For two or three trusted volunteers it may be more trouble than it is
worth; for a larger group, or for anything about members, it is worth it.

## The deputy administrator

Always two administrators. The deputy:

- has a personal administrator account (never a shared "admin" login);
- knows where the hosting account, the domain and the backups are;
- can restore a backup, or knows who can ([Backups for one person](../running-it/backups-for-one-person.md));
- is told when you change something important.

## A one-page handbook

Put an article in the members' or volunteers' area with:

1. How to sign in, and where to ask for help.
2. How to post news (with two screenshots).
3. How to add an event.
4. How to send the newsletter, if they do.
5. House rules: photos with consent, no member data on public pages.
6. Who to call when something is broken.

Keep it short enough that people actually read it.

## Handover

When you step down, go through the
[handover when volunteers change](../../businesses/associations/handover-when-volunteers-change.md) page with your
successor, and then:

- [ ] Successor has a personal administrator account; yours is disabled after the handover.
- [ ] Hosting and domain accounts are in the club's name, with the successor's contact.
- [ ] Backups tested together once.
- [ ] Handbook updated.
- [ ] Open tasks written down.

## Checklist

- [ ] At least two administrators with personal accounts.
- [ ] Volunteers have only the rights they need.
- [ ] Two-step sign-in for everyone who can publish.
- [ ] Approval workflow where several people write.
- [ ] A one-page handbook.
- [ ] Handover list ready before you need it.

## Related pages

- [Club sites](README.md)
- [Shared editing with volunteers](../../businesses/associations/shared-editing-with-volunteers.md)
- [Handing over to editors](../../creators/website-creators/handing-over-to-editors.md)
- [Offboarding staff](../../customers/day-to-day/offboarding-staff.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
