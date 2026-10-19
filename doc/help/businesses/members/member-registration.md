---
title: Member registration
audience: businesses, website-creator, admin
category: business-solutions
order: 20
summary: How people become members of an Exponential member site - opening registration, e-mail confirmation, where new members are placed, approval by the organization, accounts created by the office, sign-in, password reset, spam protection, and keeping the member list clean.
keywords: member registration, join, sign up, user/register, VerifyUserType, DefaultUserPlacement, Members group, approval, Unactivated users, login, password reset, CAPTCHA, Check user accounts, member list
type: help
---
# Member registration

This page is for the people who run a member organization and the person who sets up the site. It explains how
someone becomes a member on the site, what they experience, what the organization controls, and what keeps the
member list tidy. The technical page is
[Member registration and members-only areas](../../creators/website-creators/member-registration-and-areas.md).

## Contents

1. [What a new member experiences](#what-a-new-member-experiences)
2. [Opening registration](#opening-registration)
3. [Where new members land](#where-new-members-land)
4. [When the organization must approve](#when-the-organization-must-approve)
5. [Accounts created by the office](#accounts-created-by-the-office)
6. [Signing in and forgotten passwords](#signing-in-and-forgotten-passwords)
7. [Keeping bots out](#keeping-bots-out)
8. [Keeping the member list clean](#keeping-the-member-list-clean)
9. [Checklist](#checklist)
10. [Related pages](#related-pages)

## What a new member experiences

1. They open **Register** (`user/register`) and fill in the form: name, login, e-mail address, password.
2. The site shows a confirmation page (`user/success`) and sends an activation mail.
3. They click the link in the mail (`user/activate`); the account works.
4. If the organization approves members, they get a "registration approved" mail when someone has approved them.
5. They sign in (`user/login`) and edit their own account (`user/edit`).

Every page is a template that the site's design overrides so it looks like the site
([The pages involved](../../creators/website-creators/member-registration-and-areas.md#the-pages-involved)). The
password rules apply here too: a minimum length of 10 and a weak password check are shipped
(`settings/site.ini [UserSettings]`), and further rules can be switched on
([Changing your password](../../../features/6.0/modern-password-change.md)).

## Opening registration

On a new installation, registering needs the policy `user/register`. For open registration, add *User / Register*
to the Anonymous role. The login pages show a *Register* or *Sign Up* link only to visitors who may register, so a
site without open registration shows no dead link
([Roles and policies, 8](../../../guides/roles-and-policies.md#8-decide-who-may-register-and-who-may-read-node-data)).

## Where new members land

`site.ini [UserSettings] DefaultUserPlacement` is the node of the user group new registrations go to (shipped `12`).
Create a group *Members*, point the setting to it in the public siteaccess, and give that group a *Member* role with
`content/read` for the members section and `user/login` for the public siteaccess, and nothing else
([A members role](../../creators/website-creators/member-registration-and-areas.md#a-members-role)).

| Setting (`[UserSettings]`) | Recommended for a member site |
|---|---|
| `DefaultUserPlacement` | the Members group (or an Applicants group if you approve) |
| `VerifyUserType` | `email` (shipped): the account is activated by a link |
| `RegistrationEmail` | the mailbox of whoever looks after members |
| `PasswordInRegistrationEmail` | `disabled` (shipped): passwords are never mailed |

## When the organization must approve

Some organizations admit members only after checking them. Exponential supports this in two ways
([Registration and account approval](../extranets/registration-and-account-approval.md)):

- **Review afterwards.** The office gets the "New user registered" mail with signed **View**, **Activate** and
  **Reject and remove** links for each registration. Links expire after 7 days by default and approve or reject only
  once; opening a link changes nothing until the signed-in office user confirms
  ([Signed links in the "New user registered" mail](../../../features/6.0/registration-admin-links.md)).
- **Hold until approved.** With an Approve workflow the account waits for an approval item; the office approves or
  rejects it from the mail or the collaboration inbox, and the workflow cronjob must run.

A practical pattern: registrations land in an *Applicants* group with no member rights; after approval the office adds
the account to the *Members* group.

## Accounts created by the office

When members join on paper or through another membership system, the office creates accounts in the administration
under the Members group. Members then set their own password with **Forgot your password?**, whose keys expire
([Reset a user's password](../../../features/6.0/reset-user-password.md)). For larger numbers, an import by a developer
is possible ([Creating content in code](../../creators/app-creators/creating-content-in-code.md)).

## Signing in and forgotten passwords

- Sign-in, sign-out, the account page and password change are described for members in
  [Your account and signing in](../../accounts-and-sign-in.md).
- The forgotten password page does not reveal whether an address has an account (ezwebin 6.0.7,
  [ezwebin](../../../features/6.0/extensions/ezwebin.md)).
- Two-step sign-in can be offered to members as an option
  ([Two-factor and sign-in](../intranets/two-factor-and-sign-in.md#two-step-sign-in)).

## Keeping bots out

Registration forms attract automated sign-ups. Add a CAPTCHA field (reCAPTCHA or hCaptcha) to the user class, and keep
e-mail verification on ([Spam protection for forms](../../creators/website-creators/spam-protection-for-forms.md),
[hcaptcha](../../../features/6.0/extensions/hcaptcha.md)).

## Keeping the member list clean

| Task | Where |
|---|---|
| Registrations never confirmed | **Users > Unactivated users**: activate, resend, remove; older than 30 days marked *Old* ([Roles and policies, 7](../../../guides/roles-and-policies.md#7-clean-up-unactivated-users)) |
| Members who never signed in, or not for a long time | **Users > Check user accounts**, filters *Never signed in* and *Not seen for N days* ([Check user accounts](../../../features/6.0/user-hygiene.md)) |
| One person with two accounts | the filter *Shared e-mail address* |
| Members who left | **Disable**, or **Deactivate and move** when enabled |

## Checklist

- [ ] Registration open or closed, decided.
- [ ] Members group and Member role with minimal rights.
- [ ] `DefaultUserPlacement` set in the public siteaccess.
- [ ] Registration, activation and password pages styled like the site.
- [ ] CAPTCHA on the user class; e-mail verification on.
- [ ] Approval process decided and tested if members are checked.
- [ ] The registration form says what data is kept and why ([Consent and forms](../security/consent-and-forms.md)).
- [ ] A routine for cleaning unactivated and unused accounts.

## Related pages

- [Member registration and members-only areas](../../creators/website-creators/member-registration-and-areas.md)
- [Members-only content](members-only-content.md)
- [Registration and account approval](../extranets/registration-and-account-approval.md)
- [User accounts and passwords](../security/user-accounts-and-passwords.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
