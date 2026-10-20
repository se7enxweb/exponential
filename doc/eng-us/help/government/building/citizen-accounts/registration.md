---
title: Registration
audience: government, admin, website-creator
category: public-sector
order: 20
summary: How members of the public register on an Exponential site - the user/register page, e-mail verification, the group new users are placed in, staff approval, the "New user registered" mail with signed links, and cleaning up unactivated registrations.
keywords: registration, user/register, VerifyUserType, DefaultUserPlacement, RegistrationEmail, RegistrationFeedback, activation, unactivated users, approval, signed links, mail preferences consent
type: help
---
# Registration

This page is for administrators and website creators who open registration to the public. It shows the pages, the
settings and the administration screens involved. Sources: [Member registration and members-only areas](../../../creators/website-creators/member-registration-and-areas.md)
and [Roles and policies, 7](../../../../guides/roles-and-policies.md#7-clean-up-unactivated-users).

## Contents

1. [The pages](#the-pages)
2. [Settings](#settings)
3. [What the registration form asks](#what-the-registration-form-asks)
4. [Approval by staff](#approval-by-staff)
5. [Unactivated registrations](#unactivated-registrations)

## The pages

| Page | View | Open to visitors by |
|---|---|---|
| Register | `user/register` | `site.ini [SiteAccessSettings] AnonymousAccessList[]` |
| Registration done | `user/success` | the same |
| Activate (link in the mail) | `user/activate` | the same |
| Forgot password | `user/forgotpassword` | the same |
| Sign in, sign out | `user/login`, `user/logout` | always |

Override their templates in your public design so they match the site.

## Settings

`site.ini [UserSettings]`, in `settings/override/site.ini.append.php` or the public siteaccess:

| Key | Use |
|---|---|
| `DefaultUserPlacement` | the node of the user group new accounts go to (shipped `12`); point it to a "Members" or "Public accounts" group, never a staff group |
| `VerifyUserType=email` | the account works only after the link in the mail is clicked |
| `RegistrationEmail` | who is told about new registrations |
| `RegistrationFeedback=email` | the administrator mail with signed action links (below) |
| `PasswordInRegistrationEmail=disabled` | never mail the password |
| `MinPasswordLength`, `WeakPasswordCheck` | password rules ([Password rules](password-rules.md)) |

The role of the group (for example the shipped Member role) decides what accounts may do; give it only what the
purpose needs ([Roles and policies](../../../../guides/roles-and-policies.md)).

## What the registration form asks

The user class decides the fields: first and last name, login, e-mail, password in the default class, plus fields
you add. Ask for nothing you do not need. Since 6.0, registration can show **E-mail from us (optional)**: opt-in
choices for mail categories, recorded with the text shown in a consent log ([Mail preferences](../../../../features/6.0/mail-preferences.md),
[Your account and signing in](../../../accounts-and-sign-in.md#register-an-account)).

Protect the form against automated registrations: the form token is automatic; e-mail verification helps; a CAPTCHA
on the user class is possible with the third-party implications described in
[Spam protection and third parties](../forms/spam-protection-and-third-parties.md).

## Approval by staff

For accounts that give access to more than public information (a partners area), staff approve each registration:

| Way | How |
|---|---|
| The "New user registered" mail | with `RegistrationFeedback=email`, the administrator gets a mail with signed links: **View account**, **Activate account** (or **Approve**), **Reject and remove**; each link opens a confirmation in the administration, works once and expires (`RegistrationActionTTL`, 7 days) ([Signed links](../../../../features/6.0/registration-admin-links.md)) |
| A workflow | an approval event on registration, decided in the collaboration inbox |
| **Users > Unactivated users** | **Activate selected users** for registrations that wait |

## Unactivated registrations

**Users > Unactivated users** lists people who registered but never clicked the activation link: search, order by
date, registrations older than 30 days marked **Old**. Actions: **Activate selected users**, **Send the activation
mail again**, **Remove selected users**, and **Remove all unactivated users** (in batches of 50, recorded in the audit
as `access.user.remove`). Run it monthly: a registration never confirmed is personal data with no purpose.

## Related pages

- [Citizen accounts](README.md)
- [Sign-in and sessions](sign-in-and-sessions.md)
- [Registration admin links](../../../../features/6.0/registration-admin-links.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
