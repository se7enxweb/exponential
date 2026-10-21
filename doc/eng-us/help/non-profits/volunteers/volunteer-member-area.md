---
title: A members area for volunteers
audience: non-profit, website-creator, businesses, editor
category: non-profits
order: 50
summary: Building a volunteers-only area on a non-profit's Exponential site - accounts by invitation or registration with e-mail verification, a Volunteers user group and role, a section Anonymous may not read, protected downloads of guides and rotas, news and events for volunteers, and what is not built in (shift scheduling, hours tracking).
keywords: members area, volunteers area, volunteers only, user registration, user/register, VerifyUserType, DefaultUserPlacement, user group, role, section, content/read, protected downloads, rota, guides
type: help
---
# A members area for volunteers

This page is for the website creator and the coordinator. Active volunteers often need information the public should
not see: rotas, induction guides, contact lists, internal news. Exponential builds such an area from three parts:
accounts, a role, and a section. The full recipe is [Member registration and areas](../../creators/website-creators/member-registration-and-areas.md).

## Contents

1. [Do you need it?](#do-you-need-it)
2. [Accounts: invitation or registration](#accounts-invitation-or-registration)
3. [Group, role and section](#group-role-and-section)
4. [What to put in the area](#what-to-put-in-the-area)
5. [What is not built in](#what-is-not-built-in)
6. [Checklist](#checklist)

## Do you need it?

| You have | Then |
|---|---|
| A few volunteers and a mailing list | Probably not; a newsletter list for volunteers is enough |
| Many volunteers, documents to share, changing rotas | Yes |
| Sensitive information (contact lists, safeguarding) | Yes, with care: every account is a door |

## Accounts: invitation or registration

| Way | How | When |
|---|---|---|
| By invitation | The coordinator creates accounts for accepted volunteers in the users tree | Small number, you want control |
| By registration | Visitors register at `user/register`; the account is activated by a link in a mail (`VerifyUserType=email`) | Many volunteers; add a CAPTCHA to the user class |

With registration, set `site.ini [UserSettings] DefaultUserPlacement` to a group with **no** rights beyond the
public site (for example "Registered"), and move accepted volunteers into the Volunteers group yourself. Otherwise
anyone who registers sees the volunteers area. Source:
[Member registration and areas](../../creators/website-creators/member-registration-and-areas.md#registration-settings).

## Group, role and section

1. **Group.** In the users tree, create a group "Volunteers".
2. **Section.** Create a section "Volunteers" and assign the volunteers' folder to it. Anonymous may not read it
   ([Sections and object states, members area recipe](../../creators/website-creators/sections-and-object-states.md#recipe-a-members-area)).
3. **Role.** Create a role "Volunteer" with `content/read` limited to the Volunteers section and `user/login` for the
   public siteaccess. Assign it to the Volunteers group. Give it nothing else
   ([Roles and policies](../../../guides/roles-and-policies.md)).

Visitors who open the area are asked to log in. Files in the section can only be downloaded by those who may read it,
because downloads go through `content/download`, which checks permission
([Files and downloads](../../creators/website-creators/files-and-downloads.md#protected-downloads)).

## What to put in the area

| Content | Class |
|---|---|
| Induction guide, safety rules | Article, or a File (PDF) |
| Current rota | A File (spreadsheet or PDF) replaced each month, or an article |
| Internal news | Articles in a news folder |
| Volunteer events | Event objects in an event calendar in the section ([Events and calendars](../communications/events-and-calendars-for-non-profits.md)) |
| Feedback from volunteers | A form object in the section |

Do not put personal contact lists in the area unless every volunteer has agreed; prefer a contact form or a shared
mailbox.

## What is not built in

| Not built in | Plan instead |
|---|---|
| Shift scheduling with sign-up slots | A rota file, or an outside scheduling tool linked from the area |
| Hours tracking | A spreadsheet or outside tool |
| Messaging between volunteers | Your usual channels |

## Checklist

- [ ] Registration (if used) places new accounts in a group without area rights.
- [ ] Volunteers group, Volunteer role and Volunteers section created and tested.
- [ ] Logged out, the area asks for login; files cannot be downloaded.
- [ ] CAPTCHA on registration; e-mail verification on.
- [ ] No contact lists without consent.
- [ ] Departing volunteers removed from the group ([When volunteers leave](../operations/when-volunteers-leave.md)).

## Related pages

- [Volunteers](README.md)
- [Members-only content](../../businesses/members/members-only-content.md)
- [Visitor permissions](../../creators/website-creators/visitor-permissions.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
