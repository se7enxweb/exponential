---
title: Users and registration
audience: cms-developer
category: cms-development
order: 20
summary: For CMS developers building member areas and sign-up, users as content objects, user groups, self-registration and its settings, activation by e-mail or by an administrator, passwords, login handlers, two-factor sign-in, and the templates of the user module.
keywords: users, user accounts, user class, user group, registration, sign up, self registration, DefaultUserPlacement, VerifyUserType, activation, unactivated users, password rules, MinPasswordLength, WeakPasswordCheck, login handler, LDAP, two-factor, member area, profile
type: help
---
# Users and registration

This page is for developers who build sites where visitors sign up, sign in and have a profile. In Exponential a
user account is a content object, so extending a profile is the same work as extending any class, and access is the
same role system as for content.

## Contents

1. [Users are content](#users-are-content)
2. [User groups and roles](#user-groups-and-roles)
3. [Self-registration](#self-registration)
4. [Activation and approval](#activation-and-approval)
5. [Passwords](#passwords)
6. [Sign-in: handlers and two-factor](#sign-in-handlers-and-two-factor)
7. [The user module's pages and templates](#the-user-modules-pages-and-templates)
8. [Users in templates and PHP](#users-in-templates-and-php)
9. [Privacy](#privacy)

## Users are content

A user is an object of the user class (`site.ini [UserSettings] UserClassID=4`) with an `ezuser` attribute that holds
the login, the e-mail address and the password hash. Add attributes for a profile (a portrait, a biography, a
company) in the class editor like for any class. Groups are objects of the user group class (`UserGroupClassID=3`).
Every account, also the anonymous visitor (`AnonymousUserID=10`), lives in the user tree below node 5.

## User groups and roles

A user belongs to the groups its nodes are in; a user with two locations is in two groups. Roles are assigned to
groups, so moving or adding a location changes what a user may do. Typical groups: *Members* for registered
visitors, *Editors*, *Administrators*. Keep editors and members in separate groups so a member can never gain
editing rights by a role meant for staff ([Sections, roles and policies](sections-roles-and-policies.md)).

## Self-registration

Visitors register on `/user/register` when the Anonymous role has `user/register`. The settings are in
`site.ini [UserSettings]`:

| Setting | Shipped | Meaning |
|---|---|---|
| `DefaultUserPlacement` | `12` | the node of the group where new accounts are placed |
| `DefaultSectionID` | `0` | the section of new accounts; 0 takes the section of the location |
| `VerifyUserType` | `email` | the new account must be confirmed through a link sent by e-mail; empty disables it |
| `RegistrationFeedback` | `email` | the administrator is told about new accounts (`RegistrationEmail` names the address) |
| `RegisterAutoLogin` | `enabled` | an account that is active at once is signed in after registering |
| `RequireUniqueEmail` | `true` | one account per e-mail address |
| `GeneratePasswordIfEmpty` | `true` | an empty password is generated and sent |

Change them in your siteaccess or extension settings, never in `settings/site.ini`. A workflow on the registration
can hold accounts for approval ([Workflows and triggers](workflows-and-triggers.md)). Protect the registration form
from spam with a captcha datatype on the user class (`recaptcha`, `hcaptcha`; [Choosing datatypes](choosing-datatypes.md)).

## Activation and approval

Unconfirmed accounts are listed under **Users** > **Unactivated users** (`user/unactivated`), where an administrator
can activate or remove them; `ActivationByAdministratorSendsApprovalMail=enabled` sends the user the approval mail.
The administrator's mail about a new registration carries signed links to view, approve or reject the account
(`RegistrationAdminLinks=enabled`; [Signed links in the registration mail](../../features/6.0/registration-admin-links.md)).
Old unactivated accounts are cleaned up as described in
[Roles, policies and unactivated users, section 7](../../guides/roles-and-policies.md#7-clean-up-unactivated-users).

## Passwords

| Setting | Shipped | Meaning |
|---|---|---|
| `[UserSettings] MinPasswordLength` | `10` | minimum length |
| `[UserSettings] WeakPasswordCheck` | `enabled` | refuses well-known passwords and their variants, for every new password |
| `[UserSettings] HashType` | `php_default` | PHP's `password_hash()`; old hashes are upgraded at the next sign-in |
| `[UserSettings] ForgotPasswordKeyLifetime` | `3600` | seconds a "forgot password" link works |
| `[PasswordSettings]` | all rules off | more rules for new passwords ([Modern password change](../../features/6.0/modern-password-change.md)) |

Never store, log or mail passwords in clear text, and keep `PasswordInRegistrationEmail=disabled`. Administrators
reset a password with `./console exp:resetuserpassword --help` ([Reset a user's password](../../features/6.0/reset-user-password.md)).

## Sign-in: handlers and two-factor

`[UserSettings] LoginHandler[]` lists the handlers tried in order; `standard` checks the database. The kernel also
has handlers for LDAP (`ldap.ini`) and a text file, and an extension can add its own in
`extension/<ext>/login_handler/` ([Handlers named by settings](../extension-development/part-3-extension-developer/handlers-named-by-settings.md)).
Two-factor sign-in comes from the `sevenx_authentication_2fa` extension of the default distribution
([Two-factor sign-in](../../guides/two-factor-login.md)). `MaxNumberOfFailedLogin` (0, off, when shipped) can lock an
account after failed attempts.

## The user module's pages and templates

The module `user` has the views `login`, `logout`, `register`, `activate`, `forgotpassword`, `password`, `edit`,
`preferences` and `unactivated` among others (`kernel/user/`). Their templates are in `user/` of the design chain;
override them in your design extension to match the site, keeping the field names the module reads
([User and login templates](../designers/templates/user-and-login-templates.md)). A profile page is the full view of
the user object, so it is styled with an override for the user class.

## Users in templates and PHP

```smarty
{def $current = fetch( 'user', 'current_user' )}
{if $current.is_logged_in}
    <a href={'/user/edit'|ezurl}>{$current.contentobject.name|wash()}</a>
{/if}
{undef $current}
```

Output that depends on the user must not land in a cache shared by all visitors: see
[Caching for CMS developers](caching-for-cms-developers.md). In PHP, `eZUser::currentUser()` returns the signed-in
user and `eZUser::fetchByName()` or `eZUser::fetchByEmail()` find others ([eZUser and access](../extension-development/part-8-reference/ezuser-and-access.md)).

## Privacy

User objects hold personal data. Collect only what the site needs, keep test accounts at `example.invalid`, and plan
how accounts are retired: **Deactivate and move** (`DeactivateAndMove`, off by default) disables accounts and parks
them in a hidden group without removing them ([Check user accounts](../../features/6.0/user-hygiene.md)).

## Related pages

- [Accounts and sign-in](../accounts-and-sign-in.md): the user's view
- [Security checklist](security-checklist.md)
- [Forms and information collection](forms-and-information-collection.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
