---
title: Separating the administration from the public site
audience: government, hosting, admin
category: public-sector
order: 90
summary: For IT teams that must keep the editing interface of a public-sector site off the public internet. How siteaccesses separate the administration from the public site, how to give it its own host name, restrict it to your network, and limit who may sign in where.
keywords: siteaccess, administration, admin siteaccess, editor siteaccess, host name, HostMatchType, HostMatchMapItems, internal network, VPN, Require ip, RequireUserLogin, user/login, SiteAccess limitation, intranet
type: help
---
# Separating the administration from the public site

This page is for IT and security teams in public bodies whose policy says that editing interfaces must not be
reachable from the public internet, or only through the organization's network. It answers how Exponential separates
the administration from the public site, how to give the administration its own host name and network restriction,
and how roles decide who may sign in where. The references are
[13.5 The administration siteaccess](../../../../install/13-security-hardening.md#135-the-administration-siteaccess),
[10.2 Siteaccesses and site addresses](../../../../install/10-after-installing.md#102-siteaccesses-and-site-addresses)
and the hosting page [Restricting the administration](../../../hosting/security/restricting-the-administration.md).

## Contents

1. [Siteaccesses in one paragraph](#siteaccesses-in-one-paragraph)
2. [Give the administration its own host name](#give-the-administration-its-own-host-name)
3. [Restrict it to your network](#restrict-it-to-your-network)
4. [Decide who may sign in where](#decide-who-may-sign-in-where)
5. [Behind a central proxy](#behind-a-central-proxy)
6. [Check it](#check-it)
7. [Checklist](#checklist)

## Siteaccesses in one paragraph

A siteaccess is one way into the installation: a name, its settings in `settings/siteaccess/<name>/` and a rule for
which requests it answers. The installer creates at least the public siteaccess and the administration (usually
`admin`), and writes `[SiteAccessSettings] RequireUserLogin=true` for the administration, so every page of it asks
for a sign-in. Both share the same database; the separation is about addresses, networks and permissions, not data.

## Give the administration its own host name

The installer offers three layouts: URL (`/admin`), host (`admin.example.gov`) and port. For production the host
layout is the one to use: the administration can then be restricted as a unit, with its own virtual host, certificate
and firewall rule ([10.2](../../../../install/10-after-installing.md#102-siteaccesses-and-site-addresses)).

```ini
# settings/override/site.ini.append.php
[SiteAccessSettings]
MatchOrder=host;uri
HostMatchType=map
HostMatchMapItems[]
HostMatchMapItems[]=www.example.gov;site
HostMatchMapItems[]=admin.example.gov;admin
```

Set `[SiteSettings] SiteURL` in each siteaccess to its host name, add the names to DNS and to the web server, and clear
the INI cache:

```bash
php bin/php/ezcache.php --clear-tag=ini --allow-root-user
```

Many public bodies publish the administration host only in internal DNS. That keeps it out of public view, but treat
it as an addition to network restriction, not a replacement.

## Restrict it to your network

From the strongest to the simplest ([Restricting the administration](../../../hosting/security/restricting-the-administration.md)):

| Server | How |
|---|---|
| Apache | on the administration's virtual host, `Require ip` with your networks, or HTTP basic authentication |
| nginx | `allow` and `deny` in the administration's server block |
| Velocity | no HTTP authentication of its own: the firewall, a VPN, a reverse proxy in front, or a separate Velocity bound to an internal address |

```apache
<Location />
    Require ip 203.0.113.0/24 2001:db8::/48
</Location>
```

The addresses above are documentation examples; use your organization's networks. Editors who work from home then
reach the administration through your VPN.

## Decide who may sign in where

Signing in is itself a permission: the policy `user/login` can be limited to siteaccesses. A role page shows it as a
sentence such as *May log in, on the siteaccesses site and bold* ([Roles and policies](../../../../guides/roles-and-policies.md)).
Use it to make sure that:

- public accounts (for example people who registered for a newsletter) may sign in on the public siteaccess only;
- staff roles may sign in on the administration;
- contractors get the administration only for as long as their contract runs (see the people and permissions pages
  of this section, for example [Role design for contractors](../people-and-permissions/role-design-contractors.md)).

Exponential also has an **editor siteaccess**, a simplified editing interface for content editors
([Setup wizard and the editor siteaccess](../../../../features/6.0/setup-wizard-and-editor-siteaccess.md)). It refuses the
role module, so roles are always managed in the administration siteaccess. Give it the same network restriction as
the administration.

## Behind a central proxy

When a central reverse proxy or load balancer terminates TLS, Exponential believes forwarded headers only from the
proxies listed in `site.ini [HTTPHeaderSettings] TrustedProxies[]`. Without that, HTTPS requests look like plain HTTP
and every visitor has the proxy's address, which breaks network-based rules and the audit trail's addresses. See
[Several web servers behind a load balancer](../availability/several-web-servers-behind-a-load-balancer.md).

## Check it

```bash
curl -s -o /dev/null -w '%{http_code} %{redirect_url}\n' https://admin.example.gov/
curl -s -o /dev/null -w '%{http_code}\n' https://www.example.gov/
```

From inside your network, expect a redirect to the sign-in from the administration host and `200` from the public
host. From outside, the administration host must not answer at all, or answer 403.

## Checklist

- [ ] The administration has its own host name, matched by `HostMatchMapItems[]`.
- [ ] The web server, firewall or proxy restricts that host to your networks or VPN.
- [ ] `RequireUserLogin=true` is set for the administration siteaccess.
- [ ] `user/login` policies limit public accounts to the public siteaccess.
- [ ] `TrustedProxies[]` lists the central proxy, if there is one.
- [ ] A test from outside your network shows no administration.

## Related pages

- [Hosting](README.md) index of this folder
- [Hardening the host](hardening-the-host.md)
- [Security hardening](../../../security-hardening.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
