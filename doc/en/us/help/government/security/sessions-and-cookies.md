---
title: Sessions and cookies
audience: government, admin
category: public-sector
order: 200
summary: For administrators of public sites - the session cookie flags Exponential sets by default, recommended settings for an HTTPS-only site, separate cookies per siteaccess, session timeouts, and how to sign users out after a suspected compromise.
keywords: sessions, session cookie, CookieSecure, CookieHttponly, CookieSameSite, SessionTimeout, SessionNameHandler, session hijacking, session.use_strict_mode, ezpSessionHandlerDB, Setup Sessions, sign out, password stamp, public sector
type: help
---
# Sessions and cookies

This page is for administrators. A stolen session cookie is as good as the password. Exponential sets safe cookie flags
by default and signs other sessions out when a password changes; a few settings make it stricter
([13.7 Sessions and cookies](../../../install/13-security-hardening.md#137-sessions-and-cookies)).

## Contents

1. [The defaults](#the-defaults)
2. [Recommended for an HTTPS-only site](#recommended-for-an-https-only-site)
3. [Separate cookies for the administration](#separate-cookies-for-the-administration)
4. [Signing users out](#signing-users-out)
5. [Pitfalls](#pitfalls)
6. [Related pages](#related-pages)

## The defaults

`settings/site.ini [Session]` (the cookie keys are commented out; their defaults apply):

| Key | Default | Meaning |
|---|---|---|
| `CookieSecure` | `auto` | `Secure` when the request came over HTTPS |
| `CookieHttponly` | `true` | scripts cannot read the cookie |
| `CookieSameSite` | `Lax` | not sent with most cross-site requests |
| `SessionTimeout` | `259200` | seconds (3 days) |
| `SessionNameHandler` | `default` | `custom` gives every siteaccess its own cookie |

Also: a new session id at sign-in; logging out ends the session for good; a password change signs out every other
session of the user (`EndOtherSessions=enabled`); a disabled account's sessions end at their next request.

## Recommended for an HTTPS-only site

```ini
# settings/override/site.ini.append.php
[Session]
CookieSecure=true
CookieHttponly=true
CookieSameSite=Lax
SessionTimeout=28800
```

and in PHP's configuration `session.use_strict_mode=1`. Choose the timeout with your editors: a short one limits how
long a stolen session is useful, but signs out people who work slowly.

## Separate cookies for the administration

```ini
[Session]
SessionNameHandler=custom
SessionNamePrefix=eZSESSID
SessionNamePerSiteAccess=enabled
```

A sign-in on the public site (for example a member area) is then not a sign-in in the administration.

## Signing users out

- **After a suspected compromise of one account**: change its password; every other session ends at its next request.
  Disable the account if needed (Users > Check user accounts, **Disable**).
- **Everybody**: with the database session handler (`[Session] Handler=ezpSessionHandlerDB`), **Setup > Sessions** lists
  and removes sessions, with a confirmation ([Sessions](../../../guides/sessions.md)). With the default handler the page
  cannot remove sessions one by one; switching handler signs everybody out once.
- Removing a session does not block the user; disable the account or change the password as well.

## Pitfalls

- `CookieSecure=true` behind an HTTPS proxy that is not trusted: users are signed out on every page. Configure
  `TrustedProxies[]` (see "Proxies and trusted headers").
- `CookieSameSite=Strict` drops the session on the first click from an e-mail into a signed-in page.
- `CookieSameSite=None` is honored only together with `Secure`.

## Related pages

- [13.7 Sessions and cookies](../../../install/13-security-hardening.md#137-sessions-and-cookies)
- [Sessions: who is signed in](../../../guides/sessions.md)
- [Security hardening: Session hijacking](../../security-hardening.md#session-hijacking)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
