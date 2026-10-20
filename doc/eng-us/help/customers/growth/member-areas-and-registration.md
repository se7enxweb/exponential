---
title: Member areas and registration
audience: customer, businesses, admin
category: customer-success
order: 16
summary: What a site owner decides and runs when visitors get accounts on an Exponential site - open or approved registration, e-mail verification, the Member role, members-only areas, unfinished registrations, account clean-up, member e-mail and support questions.
keywords: members, registration, member area, members-only, Member role, user/register, activation, unactivated users, account hygiene, two-step sign-in, site owner
type: help
---
# Member areas and registration

This page is for owners who let visitors create accounts: a members area, a customer portal, a community, or simply
comments under a name. The site builder sets up the pages and settings
([Member registration and members-only areas](../../creators/website-creators/member-registration-and-areas.md));
the planning view is [Member sites](../../businesses/members/README.md). This page covers what you decide and what
your team does every month.

## Contents

1. [Decisions](#decisions)
2. [How it works, in short](#how-it-works-in-short)
3. [The monthly routine](#the-monthly-routine)
4. [Members' questions](#members-questions)
5. [Before you open registration](#before-you-open-registration)

## Decisions

| Question | Options | Read |
|---|---|---|
| Who may register? | anyone, or only people you approve | [Registration and account approval](../../businesses/extranets/registration-and-account-approval.md) |
| How is an account confirmed? | a link in a mail (`VerifyUserType=email`) | [Member registration and members-only areas](../../creators/website-creators/member-registration-and-areas.md) |
| What do members see that visitors do not? | a members-only section | [Members-only content](../../businesses/members/members-only-content.md) |
| Which e-mail do members get? | essential account mail always; optional mail only when they turn it on | [E-mail preferences](../../../features/6.0/mail-preferences.md) |
| Two-step sign-in for members? | optional for members, required for staff | [Accounts, passwords and two-step sign-in](../day-to-day/accounts-passwords-and-two-factor.md) |
| What data do you ask for? | as little as needed, explained on the form | [Personal data in Exponential](../../businesses/security/personal-data-in-exponential.md) |

## How it works, in short

From [Member registration and members-only areas](../../creators/website-creators/member-registration-and-areas.md):

- Registration, activation, forgotten password, sign-in and editing one's own account are pages of the site
  (`user/register`, `user/activate`, `user/forgotpassword`, `user/login`, `user/edit`), styled like the site.
- New accounts are placed in one user group (`DefaultUserPlacement`); it should be a Members group, never an
  editors' group.
- A **Member** role gives that group reading of the members section and signing in to the public site, and nothing
  else.
- The members' folder is in its own section, which anonymous visitors may not read; visitors who open it are asked to
  sign in.
- The password is never mailed (`PasswordInRegistrationEmail=disabled`); password rules apply to registration too
  ([Changing your password](../../../features/6.0/modern-password-change.md)).

## The monthly routine

| Task | Where | Who |
|---|---|---|
| Registrations nobody finished | **Users > Unactivated users**; a registration older than 30 days is marked **Old**; activate or remove ([Roles and policies](../../../guides/roles-and-policies.md)) | account manager |
| Accounts never used, not seen for a long time, locked | **Users > Check user accounts**, filters **Never signed in**, **Not seen for N days**, **Locked** ([Check user accounts](../../../features/6.0/user-hygiene.md)) | account manager |
| Spam registrations | the same page, then disable | account manager |
| The Member role still gives only what members need | **Users > Roles and policies** | administrator |
| Members' requests about their data | your data protection routine | owner |

> **Note**
> Activating a registration by hand sends the person the "registration approved" mail unless your administrator
> switched that off ([Roles and policies](../../../guides/roles-and-policies.md)). Removing a user is final; disabling
> is reversible ([Check user accounts](../../../features/6.0/user-hygiene.md)).

## Members' questions

| Question | Answer |
|---|---|
| "I forgot my password" | the "Forgot your password?" link on the sign-in page ([Your account and signing in](../../accounts-and-sign-in.md)) |
| "The activation mail did not arrive" | check the spam folder; an account manager can activate it on Unactivated users |
| "I lost my phone" (two-step sign-in) | an administrator switches the account to e-mail codes or off ([Two-factor login](../../../guides/two-factor-login.md)) |
| "Stop sending me mail" | the one-click unsubscribe link in every optional mail, or their preference page ([E-mail preferences](../../../features/6.0/mail-preferences.md)) |
| "What do you know about me?" | members can download their e-mail choices as JSON or CSV; for everything else, your data protection routine |

## Before you open registration

- [ ] Registration places users in the Members group only.
- [ ] E-mail verification on; a CAPTCHA on the user class ([Spam protection for forms](../../creators/website-creators/spam-protection-for-forms.md)).
- [ ] The Member role grants only what members need.
- [ ] The registration form says what data is kept and why.
- [ ] Clean-up of inactive and unverified accounts is in the routine above.

This list follows the website creators' checklist
([Member registration and members-only areas](../../creators/website-creators/member-registration-and-areas.md)).

## Related pages

- [Member sites](../../businesses/members/README.md)
- [Member registration](../../businesses/members/member-registration.md)
- [Customer and partner areas](../../businesses/extranets/customer-and-partner-areas.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
