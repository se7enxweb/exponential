---
title: Separating the administration
audience: government, admin, hosting
category: public-sector
order: 330
summary: For public bodies that want the editing interface out of the public's reach - giving the administration siteaccess its own host, restricting it by network, VPN or a second authentication, request rules for system URLs, and what Velocity needs.
keywords: administration siteaccess, admin host, network separation, IP restriction, VPN, basic authentication, Require ip, nginx allow deny, request rules, RequireUserLogin, CheckValidity, Velocity, attack surface, public sector
type: help
---
# Separating the administration

This page is for administrators and hosting teams. The administration is where accounts with power sign in; the public
does not need to reach it. Exponential's administration is a siteaccess of its own, which makes it possible to restrict
it as a unit ([13.5 The administration siteaccess](../../../install/13-security-hardening.md#135-the-administration-siteaccess)).

## Contents

1. [What is already in place](#what-is-already-in-place)
2. [Options, strongest first](#options-strongest-first)
3. [Keep system URLs off the public site](#keep-system-urls-off-the-public-site)
4. [Checking](#checking)
5. [Related pages](#related-pages)

## What is already in place

- The installer writes `[SiteAccessSettings] RequireUserLogin=true` for the administration siteaccess: every page asks
  for a sign-in.
- Access to each function is decided by roles (`setup/*`, `content/*`, `audit/*` ...).
- The setup wizard is closed on an installed site.

## Options, strongest first

1. **Its own host name**, for example `admin.example.gov`, matched by `[SiteAccessSettings] HostMatchType`, rather than
   `/admin` below the public site.
2. **Restrict the network** on that host. Apache:

   ```apache
   <Location />
       Require ip 203.0.113.0/24 2001:db8::/48
   </Location>
   ```

   or HTTP basic authentication in front; nginx: `allow` and `deny`.
3. **A VPN** for remote staff, with the administration reachable only from it.
4. **request-shield** with the browser check before the sign-in and, optionally, only the office network
   (`EXP-ADMIN-NET` in the example rules).
5. **Velocity** has no HTTP authentication of its own: restrict the administration host with the firewall, a VPN or a
   reverse proxy in front, or run a separate Velocity bound to an internal address.

Combine with two-step sign-in: network restriction keeps strangers out, two-step sign-in protects against stolen
passwords from inside.

## Keep system URLs off the public site

The request rules (`settings/requestrules.ini`) decide before a module view runs: keep anonymous visitors off system
URLs such as `/content/view/full/123`, require a sign-in for a section, refuse requests from a network
([Request rules](../../../bc/6.0/view_full_security.md)). request-shield's example rules also answer 404 for
administration modules (`setup`, `class`, `role`, `workflow`, `package`) on the public siteaccesses.

## Checking

```bash
curl -s -o /dev/null -w '%{http_code}\n' https://admin.example.gov/user/login    # from outside: refused (403) or unreachable
curl -s -o /dev/null -w '%{http_code}\n' https://www.example.gov/setup/info     # public siteaccess: no admin module
```

Test from a network outside the allowed range, for example a mobile connection.

## Related pages

- [13.5 The administration siteaccess](../../../install/13-security-hardening.md#135-the-administration-siteaccess)
- [Restricting the administration (hosts)](../../hosting/security/restricting-the-administration.md)
- [request-shield in front of a public site](request-shield.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
