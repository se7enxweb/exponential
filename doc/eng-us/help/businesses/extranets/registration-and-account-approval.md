---
title: Registration and account approval
audience: businesses, admin, partner
category: business-solutions
order: 20
summary: How outside users get accounts on an Exponential extranet - open registration with e-mail verification, the administrator's "New user registered" mail with signed approve and reject links, an Approve workflow that holds accounts until someone decides, accounts created by staff, and spam protection.
keywords: registration, account approval, user/register, VerifyUserType, RegistrationFeedback, RegistrationEmail, signed links, approve, reject, Unactivated users, user/activation, Approve event, DefaultUserPlacement, CAPTCHA, partner accounts, extranet
type: help
---
# Registration and account approval

This page is for portal owners who decide how partners and customers get an account. Exponential supports three ways:
outside users register and confirm their e-mail address, your staff approve each registration before it works, or
your staff create the accounts themselves. The page explains each, what the approver experiences, and what protects
the process.

## Contents

1. [Three ways to create accounts](#three-ways-to-create-accounts)
2. [Who may register at all](#who-may-register-at-all)
3. [Where new accounts are placed](#where-new-accounts-are-placed)
4. [E-mail verification](#e-mail-verification)
5. [The administrator's mail with signed links](#the-administrators-mail-with-signed-links)
6. [Holding accounts until approved](#holding-accounts-until-approved)
7. [Unactivated users](#unactivated-users)
8. [Accounts created by your staff](#accounts-created-by-your-staff)
9. [Keeping bots out](#keeping-bots-out)
10. [Checklist](#checklist)
11. [Related pages](#related-pages)

## Three ways to create accounts

| Way | Fits when | What controls it |
|---|---|---|
| Registration with e-mail verification, reviewed afterwards | many partners, low risk content in the first group | `VerifyUserType=email`, the "New user registered" mail |
| Registration held until an approver decides | every account must be checked before it works | an *Approve* event in the publish workflow |
| Accounts created by staff | few partners, contracts first | the administration, no public registration |

## Who may register at all

On a new installation, registering needs the policy `user/register`. For open registration on the portal, add
*User / Register* to the Anonymous role. Login pages show a *Register* link only to visitors who may open
`user/register`. An upgraded site keeps registration open until `PolicyOmitListExclude[]=user/register` is set
([Roles and policies, 8](../../../guides/roles-and-policies.md#8-decide-who-may-register-and-who-may-read-node-data)).

The function `user/register` has no limitations (`kernel/user/module.php`), so the policy opens registration on every
siteaccess of the installation that the Anonymous role reaches. If only the portal should accept registrations,
decide with your partner how: for example a separate installation, or accepting that `user/register` answers on every
siteaccess and placing every registration in a group without rights until someone approves it (below).

## Where new accounts are placed

`site.ini [UserSettings] DefaultUserPlacement` is the node of the user group that new registrations land in (shipped
`12`). Set it in the portal's siteaccess to a group of its own, for example *Partner applicants*, so registrations
never land in an editors' group ([Member registration, Registration settings](../../creators/website-creators/member-registration-and-areas.md#registration-settings)).

A useful pattern: the applicants group has only `user/login` for the portal and read access to a welcome page. After
approval, an administrator adds the account to its partner organization's group, which carries the real roles
([Customer and partner areas](customer-and-partner-areas.md)).

## E-mail verification

With `VerifyUserType=email` (the shipped value) the account is activated by a link in a mail to the new user. The
activation mail, the "activation sent again" mail and the "registration approved" mail are HTML mails with a
plain-text part and use https links ([Signed links, The mails the new user gets](../../../features/6.0/registration-admin-links.md#the-mails-the-new-user-gets)).
`PasswordInRegistrationEmail=disabled` keeps passwords out of mail.

## The administrator's mail with signed links

With `RegistrationFeedback=email` every registration sends the administrator (`RegistrationEmail`, else
`[MailSettings] AdminEmail`) the mail "New user registered". Since 6.0.15 it lists the account and its state and
carries up to five links: **View account**, **Activate account** or **Approve**, **Reject and remove**,
**Open approval item**, and **All unactivated users**
([Signed links in the "New user registered" mail](../../../features/6.0/registration-admin-links.md)).

What protects these links ([Security](../../../features/6.0/registration-admin-links.md#security)):

- Each link is signed with HMAC-SHA-256 under a key derived from the site secret, names one account and one action,
  and expires after `RegistrationActionTTL` seconds (7 days by default).
- Approve and reject links work once.
- Opening a link changes nothing. The approver must be signed in with the policy `user/activation` (or be approver
  of the waiting approval item) and press a button on a confirmation page. Mail scanners that open every link do no
  harm.
- Approving, rejecting and every refused link are recorded in the audit trail.

If the administration's `SiteURL` is not an address the approver can reach from the mail, set
`RegistrationAdminBaseURL` ([Roles and policies, 7](../../../guides/roles-and-policies.md#7-clean-up-unactivated-users)).

## Holding accounts until approved

To make sure no account works before someone has checked it, the registration's publication can wait for an
approval item: an *Approve* event in the workflow triggered before publishing. The account is then in the state
`workflow`, "Waiting for approval before it is published"; **Approve** in the mail accepts the approval item and
publishes the account at once, **Reject** denies it and removes the account
([What approve and reject do](../../../features/6.0/registration-admin-links.md#what-approve-and-reject-do)).

The Approve event narrows what it holds by section, language, version and user group
([Workflows, 3.2](../../../guides/workflows.md#32-set-up-the-approve-event)). Ask your partner to configure it so that
it applies to the portal's registrations, and test one registration before going live. The workflow cronjob must run
([Workflows, 6](../../../guides/workflows.md#6-the-workflow-cronjob-how-processes-resume)).

## Unactivated users

**Users > Unactivated users** lists people who registered but never clicked their activation link. Approvers can
activate, send the activation mail again, or remove them; registrations older than 30 days are marked **Old**
([Roles and policies, 7](../../../guides/roles-and-policies.md#7-clean-up-unactivated-users)).

## Accounts created by your staff

For a small number of partners, staff create accounts in the administration under the partner's group. No public
registration is needed. The password rules apply to every form that sets a password
([Changing your password](../../../features/6.0/modern-password-change.md)), and users reset forgotten passwords
themselves ([Reset a user's password](../../../features/6.0/reset-user-password.md)).

## Keeping bots out

- A CAPTCHA field (reCAPTCHA or hCaptcha) on the user class protects the registration form
  ([Spam protection for forms](../../creators/website-creators/spam-protection-for-forms.md)).
- Form tokens protect every form against cross-site requests.
- Unactivated registrations are cleaned up regularly.

## Checklist

- [ ] Account creation way chosen.
- [ ] `user/register` given to Anonymous only if registration is open.
- [ ] `DefaultUserPlacement` points to an applicants group with minimal rights.
- [ ] `RegistrationEmail` reaches a monitored mailbox.
- [ ] Approve and reject links tested from the mail.
- [ ] If accounts must be held: Approve event configured and one registration approved and one rejected in a test.
- [ ] CAPTCHA on the registration form.
- [ ] The registration form says what data is kept and why.

## Related pages

- [Member registration](../members/member-registration.md)
- [Member registration and members-only areas](../../creators/website-creators/member-registration-and-areas.md)
- [Roles, policies and unactivated users](../../../guides/roles-and-policies.md)
- [Consent and forms](../security/consent-and-forms.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
