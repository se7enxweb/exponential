---
title: Two-step sign-in
audience: government, admin, website-creator
category: public-sector
order: 40
summary: How to use the sevenx_authentication_2fa extension for public account holders and staff on a public-sector site - authenticator app or e-mail codes, opt-in or enforced, the policies roles need, recovery when a phone is lost, and social login considerations.
keywords: two-step sign-in, two-factor, 2FA, TOTP, authenticator app, e-mail one-time code, Enforce2FA, DefaultMethod, user2fa/setup, user2fa/verify, recovery, social login, OAuth
type: help
---
# Two-step sign-in

This page is for administrators and website creators who decide how account holders prove who they are. Two-step
sign-in makes a stolen password useless on its own. It is provided by the extension `sevenx_authentication_2fa`, which
the installer activates in opt-in mode. Sources: [Two-step sign-in](../../../../guides/two-factor-login.md) and
[sevenx_authentication_2fa](../../../../features/6.0/extensions/sevenx_authentication_2fa.md).

## Contents

1. [The methods](#the-methods)
2. [Staff: enforce it](#staff-enforce-it)
3. [The public: offer it](#the-public-offer-it)
4. [Recovery](#recovery)
5. [Social login](#social-login)
6. [Checks](#checks)

## The methods

| Method | How it works | Fits |
|---|---|---|
| Authenticator app (TOTP, RFC 6238) | a 6-digit code from an app on the phone; the key is set up by QR code | staff; public users who want it |
| E-mail one-time code | a code sent by e-mail, with a link that completes the sign-in in the same browser | users without a smartphone; recovery |

Codes work once; after five wrong codes the sign-in starts again with the password; wrong codes count as failed
sign-ins. Every step is logged to `var/log/auth.log`, never a code or a secret.

## Staff: enforce it

Staff accounts can change the site; require the second step for them.

1. Check the setup: the extension active, `LoginHandler[]` `sevenxUser2fa` before `standard`, the field
   *Two-step sign-in* on the user class, and `user2fa/setup` and `user2fa/verify` in every role that signs in. The
   installer does this for Member, Editor and Partner ([Extensions it activates](../../../site-packages/sevenx-multisite/extensions-it-activates.md)).
2. Set a `SecretKey` in `settings/override/sevenxauthentication2fa.ini.append.php` so authenticator secrets are encrypted
   (the installer makes one; keep it out of version control; losing it means everyone sets up again).
3. Enrol administrators first.
4. Set `[General] Enforce2FA=enabled` and `DefaultMethod=totp`; clear the INI cache
   (`php bin/php/ezcache.php --clear-tag=ini`) and deploy.

`Enforce2FA` is a setting of the installation or of a siteaccess. To enforce it for staff but not for the public,
set it in the administration siteaccess's settings and try it there first, as the guide describes for testing on one
siteaccess.

## The public: offer it

For public accounts, offering it is usually better than forcing it: some people have no smartphone, and a forced
second step can lock people out of a public service.

| Practice | Why |
|---|---|
| Offer the authenticator app and e-mail codes | inclusion |
| Explain it in plain language on **My account** | [Your account and signing in](../../../accounts-and-sign-in.md#two-step-sign-in) |
| Require it only where the account gives access to sensitive information | proportionate security |

## Recovery

| Situation | What to do |
|---|---|
| A staff member lost the phone | an administrator opens the user's object, sets *Two-step sign-in* to *E-mail one-time code* or *Disabled*, publishes; the user sets the app up again |
| A public user lost the phone | the same, after checking identity by a route you trust (not by an e-mail from an unknown address) |
| Someone wants to switch method | they do it themselves; switching or turning off asks for a current code |
| The `SecretKey` was lost | everyone with the app sets it up again |

Write the identity check for recovery into your support procedure ([User documentation and support](user-documentation-and-support.md)).

## Social login

The extension includes social login handler skeletons (Google, Facebook, Twitter/X, Instagram, Meta, ID.me) that show
*Continue with ...* buttons for enabled providers; a user found this way still passes their own second step. For a
public body, social login sends sign-in data to a private company; many public bodies avoid it or use a government
identity service instead, which is a development task. Decide with your data protection officer.

## Checks

- [ ] A test staff account must set up the app before it gets in (with `Enforce2FA=enabled` on the administration).
- [ ] A test public account signs in without a second step, and can turn it on under **My account**.
- [ ] A wrong code is refused; the same code twice is refused.
- [ ] Recovery by an administrator works and is in the audit trail.

## Related pages

- [Citizen accounts](README.md)
- [Sign-in and sessions](sign-in-and-sessions.md)
- [Security hardening](../../../security-hardening.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
