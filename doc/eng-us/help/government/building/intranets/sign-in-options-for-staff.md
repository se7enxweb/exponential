---
title: Sign-in options for staff
audience: government, admin, project-manager
category: public-sector
order: 50
summary: The sign-in options an Exponential intranet has (local accounts, the LDAP and text-file login handlers of the kernel, two-step sign-in from sevenx_authentication_2fa), how login handlers are chained, and what is not available.
keywords: sign-in, login handler, LoginHandler, standard, LDAP, textfile, single sign-on, SingleSignOnHandlerArray, two-step, 2FA, staff accounts, directory
type: help
---
# Sign-in options for staff

This page is for administrators and project managers choosing how staff sign in. The kernel tries the login handlers
of `site.ini [UserSettings] LoginHandler[]` in order; the shipped value is `standard`, with `LDAP` and `textfile`
listed as commented examples.

## Contents

1. [The options](#the-options)
2. [Chaining handlers](#chaining-handlers)
3. [Adding two-step sign-in](#adding-two-step-sign-in)
4. [What is not available](#what-is-not-available)

## The options

| Option | Where | Note |
|---|---|---|
| Local accounts (`standard`) | the kernel | password rules in `[PasswordSettings]`; lockout after failed sign-ins is off by default (`MaxNumberOfFailedLogin=0`) |
| LDAP directory (`LDAP`) | the kernel, `settings/ldap.ini` | see "LDAP sign-in"; no 6.x guide |
| Text file (`textfile`) | the kernel, `settings/textfile.ini` | users read from a file; for special cases |
| Two-step sign-in | `sevenx_authentication_2fa` | a code from an app or e-mail after the password ([Two-factor login](../../../../guides/two-factor-login.md)) |

## Chaining handlers

```ini
# settings/override/site.ini.append.php (or the intranet siteaccess)
[UserSettings]
LoginHandler[]
LoginHandler[]=standard
LoginHandler[]=LDAP
```

Handlers are tried in order; keeping `standard` first lets local administrator accounts sign in when the directory is
unavailable. `ExtensionDirectory[]` names extensions that provide further handlers.

## Adding two-step sign-in

Two-step sign-in works with local accounts; for staff with administrative rights it is the main protection against
stolen passwords (see "Two-step sign-in for staff").

## What is not available

Checked in `settings/site.ini`: no SAML or OpenID Connect handler ships with the kernel (`SingleSignOnHandlerArray[]`
is empty). A connection to your identity provider needs an extension that provides a login or single sign-on handler;
treat it as development work and a security review.

## Related pages

- [LDAP sign-in](ldap-sign-in.md)
- [Accounts and sign-in](../../../accounts-and-sign-in.md)
- [Security and audit](../../../../guides/security-and-audit.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
