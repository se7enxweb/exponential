---
title: Separate session cookies for the administration
audience: government, admin
category: public-sector
order: 60
summary: Step by step - give every siteaccess its own session cookie in Exponential (SessionNameHandler=custom), so a sign-in on the public site is not a sign-in in the administration, and check the result.
keywords: session cookie, SessionNameHandler, custom, SessionNamePrefix, SessionNamePerSiteAccess, administration, member area, siteaccess, separation, Velocity SkipCookies, public sector
type: help
---
# Separate session cookies for the administration

For administrators of sites with a member area or other sign-in on the public siteaccess. With separate cookies, a
session on the public site does not open the administration, and the reverse
([13.7 Sessions and cookies](../../../../install/13-security-hardening.md#137-sessions-and-cookies)).

## Contents

1. [Before you start](#before-you-start)
2. [Steps](#steps)
3. [Check that it worked](#check-that-it-worked)
4. [Related pages](#related-pages)

## Before you start

- Everyone is signed out once when the cookie names change; do it outside office hours and tell editors.
- Under Velocity, the response cache derives its "personal, do not cache" cookie from `SessionNamePrefix` automatically
  with `custom`.

## Steps

1. In `settings/override/site.ini.append.php`:

   ```ini
   [Session]
   SessionNameHandler=custom
   SessionNamePrefix=eZSESSID
   SessionNamePerSiteAccess=enabled
   ```

2. `php bin/php/ezcache.php --clear-tag=ini --allow-root-user`, then reload PHP-FPM or
   `./console exp:velocity deploy --allow-root-user`.

## Check that it worked

1. Sign in on the public site (member area), then open the administration in the same browser: it asks for a sign-in.
2. In the browser's developer tools, the two hosts or paths carry session cookies with different names.
3. Check that a cache in front (Varnish, CDN) treats the new cookie names as personal.

## Related pages

- [Sessions and cookies](../../security/sessions-and-cookies.md)
- [Set secure session cookies](set-secure-session-cookies.md)
- [Separating the administration](../../security/separating-the-administration.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
