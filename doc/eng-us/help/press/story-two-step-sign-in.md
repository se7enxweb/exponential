---
title: "Story: two-step sign-in"
audience: press
category: press-media
order: 62
summary: A feature story about two-step sign-in in Exponential - authenticator apps and e-mail codes from the extension sevenx_authentication_2fa, what protects it, what it costs users in time - with printable facts, sources, a discrepancy to watch and the claims to avoid.
keywords: story, two-step sign-in, two-factor authentication, 2FA, TOTP, authenticator app, e-mail code, social login, sevenx_authentication_2fa, press
type: help
---
# Story: two-step sign-in

This page is for journalists and creators who write about account security in content management systems. It covers
the two-step sign-in of Exponential: what a user sees, what protects it, when it arrived, and one point where two
sources disagree.

## Contents

1. [The story in one paragraph](#the-story-in-one-paragraph)
2. [What is new, and when](#what-is-new-and-when)
3. [Facts you can print](#facts-you-can-print)
4. [Where the sources differ](#where-the-sources-differ)
5. [What not to claim](#what-not-to-claim)
6. [Angles and headline ideas](#angles-and-headline-ideas)
7. [Who to ask](#who-to-ask)
8. [Related pages](#related-pages)

## The story in one paragraph

"After the password, a code from an authenticator app on the phone, or one sent by e-mail: the password alone is then
useless to anyone else" ([Two-step sign-in](../../guides/two-factor-login.md)). The feature comes from the extension
`sevenx_authentication_2fa`, which the kernel's `composer.json` requires and the installers activate. The guide puts the
cost in plain terms: "15 minutes to turn it on, 2 minutes per user to set it up." A new installation requires it for
administrators and editors; members of other groups choose for themselves, and the site owner decides whom it applies to.

## What is new, and when

| What | When | Source |
|---|---|---|
| The extension imported (version 1.0.0) | 21 July 2026 | [sevenx_authentication_2fa](../../features/6.0/extensions/sevenx_authentication_2fa.md) |
| The guide, social login buttons on the admin4 login page, codes accepted once and counted as failed logins, secrets encryptable at rest | 6 October 2026 | [6.0.15 changelog](../../../changelogs/6.0/6.0.15.md#6-october-2026-two-step-sign-in) |
| Every new installation requires two-step sign-in of administrators and editors | 23 October 2026 | [6.0.15 changelog](../../../changelogs/6.0/6.0.15.md#2026-10-23-every-new-installation-requires-two-step-sign-in-of-administrators-and-editors-and-has-imports) |

The kernel changes are part of the 6.0.15 development line, which is not released; the extension is released on its
own.

## Facts you can print

| Fact | Source |
|---|---|
| Authenticator apps that follow RFC 6238 work, for example Google Authenticator, Authy and Microsoft Authenticator. | [sevenx_authentication_2fa, What it offers](../../features/6.0/extensions/sevenx_authentication_2fa.md#what-it-offers) |
| An e-mail one-time code is the alternative when an app is not used. | same |
| "The password alone never signs in": the user is signed in, with a new session id, only after the right code. | [Two-step sign-in, What protects it](../../guides/two-factor-login.md#what-protects-it) |
| Each code works once; after five wrong codes the sign-in starts again with the password. | [Two-step sign-in, What a user sees](../../guides/two-factor-login.md#what-a-user-sees) |
| Changing or switching off the method asks for a current code, "so someone at an unattended computer cannot weaken the account". | same |
| The QR code is drawn on the server; the key goes to no other service. | [Two-step sign-in, What protects it](../../guides/two-factor-login.md#what-protects-it) |
| Every step is written to `var/log/auth.log`, never a code or a secret. | same |
| Social login handlers exist as skeletons for Google, Facebook, Twitter/X, Instagram, Meta and ID.me. | [sevenx_authentication_2fa, What it offers](../../features/6.0/extensions/sevenx_authentication_2fa.md#what-it-offers) |
| The Velocity control panel has optional two-factor authentication of its own. | [Velocity control panel](../../features/6.0/velocity-control-panel.md#two-factor-authentication-optional) |

## Where the sources differ

The extension page says the link in the e-mail "completes the login when clicked, even in another browser"
([sevenx_authentication_2fa](../../features/6.0/extensions/sevenx_authentication_2fa.md#what-it-offers)); the newer guide
says "the link in the mail only works in the browser where the password was typed"
([Two-step sign-in](../../guides/two-factor-login.md#what-protects-it)). Do not describe the e-mail link's behavior
without asking 7x which is current.

## What not to claim

- "Two-step sign-in is mandatory for everyone." A new installation requires it for administrators and editors only;
  other users choose for themselves, and the site owner decides whom it applies to.
- "Social login works out of the box with every provider." The handlers are described as skeletons ready to customize.
- "Accounts cannot be taken over." Say what the second step protects against, and stop there.

## Angles and headline ideas

```text
"Two minutes per user: two-step sign-in comes to Exponential"
"The password alone is not enough: how Exponential's second step works"
"An open source CMS that keeps your authenticator key on its own server"
```

## Who to ask

| Question | Ask | Where |
|---|---|---|
| The extension, the e-mail link question | 7x | info@se7enx.com, or <https://exponential.earth/about/contact-us> |
| A weakness in the sign-in | privately | security@se7enx.com ([SECURITY.md](https://github.com/se7enxweb/exponential/blob/main/SECURITY.md)) |

## Related pages

- [Story: security](story-security.md)
- [Your account and signing in](../accounts-and-sign-in.md)
- [Talking points: security](../advocates/talking-points-security.md)
- [Feature stories: index](feature-stories-index.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
