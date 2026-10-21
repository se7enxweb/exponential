---
title: Members and registration
audience: individual, website-creator, customer
category: personal-sites
order: 1712
summary: How a volunteer lets club members sign up and sign in on Exponential - the registration pages, activation by mail, the Members group and Member role of the default package, approving new members, a members' area, what members may do, passwords and two-step sign-in, and keeping the member list tidy.
keywords: members, registration, sign up, user/register, activation, VerifyUserType, DefaultUserPlacement, Members group, Member role, approve members, members area, password reset, two-factor, member list, club
type: help
---
# Members and registration

This page is for individuals who run a club site and want members to have accounts. Registration is built in; what
you decide is who becomes a member and what members may do.

## Contents

1. [The pages involved](#the-pages-involved)
2. [What the default package set up](#what-the-default-package-set-up)
3. [Open or approved membership](#open-or-approved-membership)
4. [Setting it up](#setting-it-up)
5. [What members may do](#what-members-may-do)
6. [Passwords and two-step sign-in](#passwords-and-two-step-sign-in)
7. [Keeping the member list tidy](#keeping-the-member-list-tidy)
8. [Checklist](#checklist)

## The pages involved

| Page | Address |
|---|---|
| Register | `user/register` |
| Registration done | `user/success` |
| Activate the account (link in the mail) | `user/activate` |
| Forgot password | `user/forgotpassword` |
| Log in, log out | `user/login`, `user/logout` |
| Edit own account | `user/edit` |

Source and settings: [Member registration and members-only areas](../../creators/website-creators/member-registration-and-areas.md).

## What the default package set up

The installer of the default site package (`sevenx_multisite`) renames the "Guest accounts" user group to
**Members** and gives it the role **Member** (`sevenx-multi-site-installer.php`). The Member role may, in the
Standard section, create comments and forum topics and replies, edit those, edit its own account
(`user/selfedit`), use notifications and change its password. It does **not** read a private section by itself; you
add that for your members' area.

New registrations land in the group set by `site.ini [UserSettings] DefaultUserPlacement`.

## Open or approved membership

| Model | How | Suits |
|---|---|---|
| **Open** | Registrations land directly in Members | Clubs where the site's members' area holds nothing sensitive |
| **Approved** | Registrations land in a "New registrations" group without access; a volunteer moves real members into Members | Most clubs: the member list matches the club's list |
| **Invited only** | Registration closed; the administrator creates accounts | Small clubs, committees |

## Setting it up

1. Decide the model above.
2. For **approved**: create a group "New registrations" with no extra roles and point `DefaultUserPlacement` to it in
   the public siteaccess's settings.
3. Make sure `VerifyUserType=email` (activation by a mail link) and `RegistrationEmail` (who is told about new
   registrations) are set ([Registration settings](../../creators/website-creators/member-registration-and-areas.md#registration-settings)).
4. Create the members' area: a folder in its own section, and a role with `content/read` on that section assigned
   to Members ([Private areas with roles and sections](../family-sites/private-areas-with-roles-and-sections.md)).
5. Override the look of the registration and login pages in your design if needed.
6. Protect the registration form against bots ([Spam and security basics](../running-it/spam-and-security-basics.md)).
7. Register a test member yourself, activate, sign in, open the members' area.

Outgoing mail must work: without it nobody can activate an account or reset a password.

## What members may do

| Right | Typical setting |
|---|---|
| Read the members' area | Yes |
| Comment on news | Yes, or only in the members' area |
| Start and reply in forums | Yes, if you use forums |
| Fill in sign-up forms | Yes |
| Publish news or events | No; that is for editors ([Sharing the work with volunteers](sharing-the-work-with-volunteers.md)) |

## Passwords and two-step sign-in

- Password rules and expiring reset links are built in ([Modern password change](../../../features/6.0/modern-password-change.md),
  [Reset user password](../../../features/6.0/reset-user-password.md)).
- Two-step sign-in can be offered to members and required for editors and administrators
  ([Two-factor sign-in](../running-it/two-factor-sign-in.md)).

## Keeping the member list tidy

- Once a year, compare the site's Members group with the club's membership list.
- Move people who left out of Members (or disable their accounts); keep what they wrote.
- Remove registrations that were never activated.
- Do not keep more about members than the club needs ([Personal data in Exponential](../../businesses/security/personal-data-in-exponential.md)).

## Checklist

- [ ] Membership model decided with the committee.
- [ ] `DefaultUserPlacement`, `VerifyUserType`, `RegistrationEmail` set.
- [ ] Members' area readable by Members only, tested as a stranger.
- [ ] Registration protected against bots.
- [ ] Activation and reset mails arrive.
- [ ] Yearly comparison with the club's list in the calendar.

## Related pages

- [Club sites](README.md)
- [Member registration](../../businesses/members/member-registration.md)
- [Members-only content](../../businesses/members/members-only-content.md)
- [Member areas and registration (customers)](../../customers/growth/member-areas-and-registration.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
