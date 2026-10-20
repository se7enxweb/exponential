---
title: Restrict the administration by network
audience: government, admin, hosting
category: public-sector
order: 140
summary: Step by step - limit the administration siteaccess of an Exponential site to your office network or VPN with Apache Require ip or nginx allow/deny, after giving it its own host name, and check from outside.
keywords: administration, admin host, IP restriction, Require ip, nginx allow deny, VPN, network separation, HostMatchType, Velocity firewall, request-shield EXP-ADMIN-NET, public sector
type: help
---
# Restrict the administration by network

For administrators and hosting teams. The public does not need to reach the administration. Limiting it to your network
removes most password guessing from the internet ([Separating the administration](../../security/separating-the-administration.md)).

## Contents

1. [Before you start](#before-you-start)
2. [Steps](#steps)
3. [Check that it worked](#check-that-it-worked)
4. [If something goes wrong](#if-something-goes-wrong)
5. [Related pages](#related-pages)

## Before you start

- The administration has its own host name (for example `admin.example.gov`, matched by
  `[SiteAccessSettings] HostMatchType`); restricting `/admin` below the public host is harder to get right.
- The address ranges of your offices and VPN; remote staff without VPN need another way (VPN, or request-shield's
  browser check instead of a network rule).
- A way back in if you lock yourself out (console access to the server).

## Steps

**Apache**, on the administration's virtual host:

```apache
<Location />
    Require ip 203.0.113.0/24 2001:db8::/48
</Location>
```

**nginx**, in the administration's server block:

```nginx
allow 203.0.113.0/24;
allow 2001:db8::/48;
deny all;
```

**Velocity** has no HTTP authentication or address rules for this; use the firewall, a VPN, a reverse proxy in front, or a
separate Velocity bound to an internal address ([13.5](../../../../install/13-security-hardening.md#135-the-administration-siteaccess)).

Reload the web server.

## Check that it worked

```bash
curl -s -o /dev/null -w '%{http_code}\n' https://admin.example.gov/user/login
```

From a mobile connection (outside the ranges): `403` or no connection. From the office: `200`.

## If something goes wrong

- Behind a proxy, the web server sees the proxy's address: restrict at the proxy, or use the forwarded address only
  from trusted proxies (Apache `mod_remoteip`, nginx `realip`).
- Locked out: restore the previous configuration from the server console.

## Related pages

- [Separating the administration](../../security/separating-the-administration.md)
- [Restricting the administration (hosts)](../../../hosting/security/restricting-the-administration.md)
- [request-shield in front of a public site](../../security/request-shield.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
