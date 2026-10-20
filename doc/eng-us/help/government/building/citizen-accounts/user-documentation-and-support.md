---
title: User documentation and support
audience: government, admin, project-manager, trainer
category: public-sector
order: 80
summary: What to tell people who hold accounts on a public-sector Exponential site, which help pages to reuse, and how support staff help with sign-in, passwords and two-step sign-in without ever seeing or asking for a password.
keywords: user documentation, support, help desk, account help, forgot password, two-step sign-in recovery, identity check, My account, help pages, accessibility of help
type: help
---
# User documentation and support

This page is for administrators, project managers and trainers who prepare help for account holders and the people
who support them. Good help prevents most support requests; a clear support procedure keeps the rest safe.

## Contents

1. [What account holders need to know](#what-account-holders-need-to-know)
2. [Help pages you can reuse](#help-pages-you-can-reuse)
3. [A support procedure](#a-support-procedure)
4. [Common requests and answers](#common-requests-and-answers)

## What account holders need to know

| Topic | Say |
|---|---|
| Why the account exists | what it allows, and what it does not (transactions elsewhere) |
| What data it holds | name, e-mail address and choices; link the privacy notice |
| Signing in | where the sign-in is, that the browser must accept cookies |
| Forgotten password | the **Forgot your password?** link; the link in the mail works once and for one hour |
| Two-step sign-in | how to turn it on, what to do if the phone is lost |
| E-mail choices | **E-mail from us (optional)** and how to change it; one-click unsubscribe in every optional mail |
| Ending the account | how to ask, what happens to the data |
| Support | how to reach support, and that staff never ask for a password |

## Help pages you can reuse

The help of this repository is free documentation under the GNU FDL 1.3 ([Free documentation](../../../free-documentation.md));
you may copy and adapt it for your users.

| Page | For |
|---|---|
| [Your account and signing in](../../../accounts-and-sign-in.md) | everyone with an account |
| [Notifications: the user's guide](../../../../features/6.0/notifications.md) | people who follow content |
| [E-mail preferences](../../../../features/6.0/mail-preferences.md) | everyone who gets mail |
| [Logging into the Editor](../../../logging-into-the-editor.md) | occasional staff editors |

Make your adapted help accessible and in the languages of your users; keep it on the site, not only as a PDF.

## A support procedure

| Rule | Why |
|---|---|
| Never ask for or accept a password | staff do not need it; it trains people to give it away |
| Check identity before changing anything on an account | an attacker can call support pretending to be someone |
| Prefer self-service (forgot password, my account) | fewer chances for mistakes |
| Record what was done, not the data | audit and accountability |
| Escalate unusual patterns (many locked accounts, many resets) | possible attack |

Identity checks for public accounts are hard because the account usually has only an e-mail address. A common
practice: a support action that changes sign-in (reset of two-step sign-in, change of e-mail address) is done only
after a confirmation mail to the address on the account, or through a channel where the person is known.

## Common requests and answers

| Request | Support action |
|---|---|
| "I forgot my password" | point to **Forgot your password?**; check the mail transport works if no mail arrives |
| "No activation mail" | **Users > Unactivated users**, **Send the activation mail again**; check the address is right |
| "My account is locked" | **Users > Check user accounts**, filter *Locked*, **Unlock** after checking |
| "I lost my phone" (two-step sign-in) | after the identity check, set *Two-step sign-in* to *E-mail one-time code* on the user object ([Two-step sign-in](two-factor-sign-in.md#recovery)) |
| "Stop sending me mail" | the one-click link in the mail, or **E-mail from us** in My account; an administrator can see the state in **Setup > E-mail preferences** |
| "Delete my account" | [Deactivation and removal](deactivation-and-removal.md#answering-a-deletion-request) |

## Related pages

- [Citizen accounts](README.md)
- [Trainers start here](../../../trainers/trainers-start-here.md)
- [When something goes wrong](../../../when-something-goes-wrong.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
