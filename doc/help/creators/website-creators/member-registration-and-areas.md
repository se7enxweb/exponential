---
title: Member registration and members-only areas
audience: website-creator, admin
category: website-creation
order: 580
summary: Let visitors register and log in - user/register, e-mail verification, where new users are placed, the Members role, login and password reset pages, a members-only section, and privacy and spam considerations.
keywords: registration, user/register, members area, login, VerifyUserType, DefaultUserPlacement, RegistrationEmail, password reset, forgot password, member role, two-factor, website creator
type: help
---
# Member registration and members-only areas

This page is for website creators who give visitors accounts: a members area, an intranet, a customer portal, or simply
comments under a name.

## Contents

1. [The pages involved](#the-pages-involved)
2. [Registration settings](#registration-settings)
3. [A members role](#a-members-role)
4. [A members-only area](#a-members-only-area)
5. [Passwords and sign-in security](#passwords-and-sign-in-security)
6. [Checklist](#checklist)

## The pages involved

| Page | View | Open to visitors by |
|---|---|---|
| Register | `user/register` | `site.ini [SiteAccessSettings] AnonymousAccessList[]` |
| Registration done | `user/success` | the same |
| Activate the account (link in the mail) | `user/activate` | the same |
| Forgot password | `user/forgotpassword` | the same |
| Log in, log out | `user/login`, `user/logout` | always |
| Edit own account | `user/edit` | logged in |

Override their templates (`user/register.tpl`, `user/login.tpl` ...) in your design so they look like the site.

## Registration settings

`site.ini [UserSettings]`:

| Key | Meaning |
|---|---|
| `DefaultUserPlacement` | the node of the user group new users are placed in (shipped `12`) |
| `VerifyUserType` | `email`: the account is activated by a link in a mail |
| `RegistrationEmail` | who is told about new registrations |
| `PasswordInRegistrationEmail` | `disabled`: the password is never mailed |
| `MinPasswordLength`, `WeakPasswordCheck` | password rules ([Modern password change](../../../features/6.0/modern-password-change.md)) |

Create a user group "Members" and set `DefaultUserPlacement` to its node in the public siteaccess, so registrations
never land in an editors' group.

## A members role

Create a role "Member" with `content/read` for the members section and `user/login` for the public siteaccess, and
assign it to the Members group ([Roles and policies](../../../guides/roles-and-policies.md)). Give it nothing else.

## A members-only area

Assign the members' folder to its own section, which Anonymous may not read
([Sections and object states](sections-and-object-states.md#recipe-a-members-area)). Visitors who open it are asked to
log in. A whole siteaccess can require login instead (`[SiteAccessSettings] RequireUserLogin=true` in its settings),
which suits an intranet.

## Passwords and sign-in security

- Password reset is built in and its keys expire ([Reset user password](../../../features/6.0/reset-user-password.md)).
- Two-step login for members is available ([Two-factor login](../../../guides/two-factor-login.md)).
- Sessions and logouts: [Sessions](../../../guides/sessions.md).

## Checklist

- [ ] Registration places users in the Members group only.
- [ ] E-mail verification on; CAPTCHA on the user class ([Spam protection](spam-protection-for-forms.md)).
- [ ] The Member role grants only what members need.
- [ ] The registration form says what data is kept and why.
- [ ] Inactive and unverified accounts are cleaned up ([User hygiene](../../../features/6.0/user-hygiene.md)).

## Related pages

- [What visitors may see: the Anonymous role](visitor-permissions.md)
- [Accounts and sign-in](../../accounts-and-sign-in.md)
- [Sections and object states for site builders](sections-and-object-states.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
