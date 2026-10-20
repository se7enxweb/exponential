---
title: Sessions, sign-out and shared computers
audience: government, admin
category: public-sector
order: 80
summary: How long staff stay signed in to an Exponential intranet (SessionTimeout, ActivityTimeout), how to shorten it, how to see and end sessions in Setup > Sessions with the database handler, and advice for shared computers and lockout.
keywords: sessions, SessionTimeout, ActivityTimeout, sign-out, logout, shared computers, Setup Sessions, ezpSessionHandlerDB, lockout, MaxNumberOfFailedLogin
type: help
---
# Sessions, sign-out and shared computers

This page is for administrators. A session keeps one browser signed in; it ends `site.ini [Session] SessionTimeout`
seconds after its last use, 259200 (three days) by default ([Sessions](../../../../guides/sessions.md)).

## Contents

1. [The timeouts](#the-timeouts)
2. [Seeing and ending sessions](#seeing-and-ending-sessions)
3. [Shared computers](#shared-computers)
4. [Failed sign-ins](#failed-sign-ins)

## The timeouts

| Setting (`site.ini [Session]`) | Shipped | Meaning |
|---|---|---|
| `SessionTimeout` | `259200` | seconds after the last use when a session ends |
| `ActivityTimeout` | `3600` | after this pause, the next request counts as a new visit |

Three days suits a public site's members; for an intranet with sensitive content, set a shorter `SessionTimeout` in the
intranet siteaccess and test that staff are asked to sign in again after the pause you chose.

## Seeing and ending sessions

**Setup > Sessions** (`/setup/session`): with the default handler (PHP's own storage) the page shows who signed in
recently but cannot list or remove sessions one by one. With `[Session] Handler=ezpSessionHandlerDB` sessions are kept
in the database and the page lists them and removes them after a confirmation, for example all sessions of a leaver.
Disabling an account also ends its sessions (**Users > Check user accounts**, **Disable**).

## Shared computers

- Train staff to use **Sign out** on shared computers.
- Shorter session timeouts on the intranet siteaccess.
- Browsers' password saving off on shared machines (an IT policy, not an Exponential setting).

## Failed sign-ins

`site.ini [UserSettings] MaxNumberOfFailedLogin=0` (shipped) means no lockout. Setting a number locks an account after
that many failed sign-ins; `TrustedIPList[]` lists addresses always allowed. **Users > Check user accounts** shows locked
accounts and has **Unlock**. Brute-force sign-ins also raise an alert in the audit trail
([Audit trail](../../../../features/6.0/audit-trail.md)).

## Related pages

- [Sessions](../../../../guides/sessions.md)
- [Check user accounts](../../../../features/6.0/user-hygiene.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
