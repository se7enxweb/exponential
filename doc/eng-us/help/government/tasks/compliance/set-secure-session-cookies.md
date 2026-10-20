---
title: Set secure session cookies
audience: government, admin
category: public-sector
order: 120
summary: Step by step - on an HTTPS-only site, force the Secure flag on Exponential's session cookie, keep HttpOnly and SameSite=Lax, choose a session timeout with your editors, and check the flags with curl.
keywords: session cookie, CookieSecure, CookieHttponly, CookieSameSite, SessionTimeout, Secure flag, HTTPS, session.use_strict_mode, Set-Cookie, public sector
type: help
---
# Set secure session cookies

For administrators of HTTPS-only sites. By default the session cookie is `Secure` only when the request itself came
over HTTPS (`CookieSecure=auto`); `true` forces it, so the cookie is never sent over plain HTTP
([13.7 Sessions and cookies](../../../../install/13-security-hardening.md#137-sessions-and-cookies)).

## Contents

1. [Before you start](#before-you-start)
2. [Steps](#steps)
3. [Check that it worked](#check-that-it-worked)
4. [If something goes wrong](#if-something-goes-wrong)
5. [Related pages](#related-pages)

## Before you start

- The site is served only over HTTPS; behind a proxy, `TrustedProxies[]` lists it and it sets `X-Forwarded-Proto`.
- Agree a session timeout with editors (shipped: 259200 seconds, three days).

## Steps

1. In `settings/override/site.ini.append.php`:

   ```ini
   [Session]
   CookieSecure=true
   CookieHttponly=true
   CookieSameSite=Lax
   SessionTimeout=28800
   ```

2. In PHP's configuration for the site, `session.use_strict_mode=1`.
3. `php bin/php/ezcache.php --clear-tag=ini --allow-root-user`; reload PHP-FPM or deploy on Velocity.

## Check that it worked

```bash
curl -sI https://www.example.gov/user/login | grep -i set-cookie
```

shows `Secure; HttpOnly; SameSite=Lax` on the session cookie. Sign in and work for a while: you stay signed in.

## If something goes wrong

- **Signed out on every page**: the kernel thinks requests are plain HTTP (proxy not trusted, or no
  `X-Forwarded-Proto`); fix the proxy settings ([Proxies and trusted headers](../../security/proxies-and-trusted-headers.md)).
- `SameSite=Strict` drops the session on the first click from an e-mail; keep `Lax` unless you know you need `Strict`.

## Related pages

- [Sessions and cookies](../../security/sessions-and-cookies.md)
- [Separate session cookies for the administration](separate-session-cookies-for-the-administration.md)
- [Add HSTS](add-hsts.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
