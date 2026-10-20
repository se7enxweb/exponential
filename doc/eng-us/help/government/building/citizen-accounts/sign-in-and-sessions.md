---
title: Sign-in and sessions
audience: government, admin, website-creator
category: public-sector
order: 30
summary: How sign-in and sessions work for public account holders and staff on an Exponential site - the sign-in pages, session lifetime and cookies, other sessions signed out after a password change, the Setup Sessions page, failed sign-ins and lockout.
keywords: sign-in, login, logout, sessions, SessionTimeout, ActivityTimeout, CookieSecure, session handler, ezpSessionHandlerDB, EndOtherSessions, lockout, MaxNumberOfFailedLogin, brute force
type: help
---
# Sign-in and sessions

This page is for administrators and website creators who run accounts on a public-sector site. It explains how a
sign-in becomes a session, how long it lasts, how sessions are ended, and how failed sign-ins are handled. Sources:
[Sessions](../../../../guides/sessions.md), [Security hardening](../../../security-hardening.md#brute-force-and-credential-stuffing-on-the-sign-in)
and [Security standards](../../../../standards/security.md).

## Contents

1. [The sign-in](#the-sign-in)
2. [Sessions and their lifetime](#sessions-and-their-lifetime)
3. [Ending sessions](#ending-sessions)
4. [Failed sign-ins and lockout](#failed-sign-ins-and-lockout)
5. [Public and staff sign-in apart](#public-and-staff-sign-in-apart)

## The sign-in

| Property | Exponential |
|---|---|
| Password storage | bcrypt through PHP's `password_hash()` (`HashType=php_default`), upgraded at sign-in from older hashes |
| Unknown login vs wrong password | the same cost and answer, so accounts cannot be discovered |
| Session id | renewed at sign-in |
| Cookie | `Secure` over HTTPS (`[Session] CookieSecure=auto`) |
| Second step | optional or enforced two-step sign-in ([Two-step sign-in](two-factor-sign-in.md)) |
| Record | sign-ins and failures in the audit trail ([Audit trail](../../../../features/6.0/audit-trail.md)) |

## Sessions and their lifetime

| Setting (`site.ini [Session]`) | Default | Meaning |
|---|---|---|
| `SessionTimeout` | 259200 (three days) | a session ends this long after its last use |
| `ActivityTimeout` | 3600 (one hour) | used for "last visit" and the Sessions page figures |
| `Handler` | empty (PHP's own storage) | `ezpSessionHandlerDB` keeps sessions in a table so the Sessions page can list and remove them |

For public accounts on shared or public computers (libraries, offices), a shorter `SessionTimeout` is kinder to
users who forget to sign out. Weigh it against people having to sign in more often.

## Ending sessions

| Event | Effect |
|---|---|
| The user signs out | the session ends |
| The password changes (by the user, an administrator, "Forgot your password?", the console) | the user's other sessions are signed out on their next request (`[PasswordSettings] EndOtherSessions=enabled`) ([Modern password change](../../../../features/6.0/modern-password-change.md)) |
| An administrator removes sessions | **Setup > Sessions** with a session table: tick and remove, with a confirmation; the administrator's own session is never removed |
| An account is disabled | it can no longer sign in ([Account hygiene](account-hygiene.md)) |

## Failed sign-ins and lockout

| Measure | Default | Read |
|---|---|---|
| Audit alert rules `brute_force` (20 failures from one network in 5 minutes) and `brute_force_user` (10 for one account in 15 minutes) | on with the audit trail | [Audit trail](../../../../features/6.0/audit-trail.md) |
| Lockout after N failures, `[UserSettings] MaxNumberOfFailedLogin` | off (0) | [Security hardening](../../../security-hardening.md#brute-force-and-credential-stuffing-on-the-sign-in) |
| Trusted networks exempt from lockout, `TrustedIPList[]` | empty | the same |
| Unlocking | **Users > Check user accounts**, filter *Locked*, **Unlock** | [Check user accounts](../../../../features/6.0/user-hygiene.md) |

A lockout also lets anyone lock a known account by trying wrong passwords. For public accounts, two-step sign-in and
the alert rules are often better than a low lockout threshold.

## Public and staff sign-in apart

| Practice | Why |
|---|---|
| Staff sign in on the administration siteaccess; the public on the site | different roles, different risks |
| The administration's address restricted to your network or behind request-shield | fewer attacks on staff accounts ([Security hardening](../../../security-hardening.md#a-shield-in-front-request-shield)) |
| Public accounts in their own user group with a minimal role | a compromised public account can do little |
| The `user/login` policy limited by siteaccess | the default package lets Anonymous sign in on every served siteaccess except the administration ones ([Roles and policies of sevenx_multisite](../../../site-packages/sevenx-multisite/roles-and-policies.md#anonymous)) |

## Related pages

- [Citizen accounts](README.md)
- [Two-step sign-in](two-factor-sign-in.md)
- [Sessions](../../../../guides/sessions.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
