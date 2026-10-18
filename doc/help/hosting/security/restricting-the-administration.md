---
title: Restricting the administration
audience: hosting, admin
category: hosting
order: 60
summary: How a host keeps the Exponential administration of each customer site out of reach of everyone else: its own host name, network limits in Apache, nginx or in front of Velocity, request-shield's browser check, least-privilege roles, ManagedByDeployment, the closed setup wizard, and the checks for each.
keywords: administration, admin siteaccess, RequireUserLogin, host name, HostMatchType, Require ip, allow deny, basic authentication, VPN, request-shield, roles, policies, least privilege, ManagedByDeployment, setup wizard, CheckValidity, AllowWizardOnInstalledSite
type: help
---
# Restricting the administration

This page is for system administrators at a host and for support staff who set up a customer's site. The
administration is where content is published, roles are granted and settings are changed; the sign-in
(`/user/login`, `/admin/user/login`) is the most attacked address of any site
([Security hardening](../../security-hardening.md#brute-force-and-credential-stuffing-on-the-sign-in)). The
reference is [13.5 The administration siteaccess](../../../install/13-security-hardening.md#135-the-administration-siteaccess).

## Contents

1. [What is there by default](#what-is-there-by-default)
2. [Its own host name](#its-own-host-name)
3. [Network restrictions](#network-restrictions)
4. [A browser check in front](#a-browser-check-in-front)
5. [Least privilege](#least-privilege)
6. [Configuration from version control](#configuration-from-version-control)
7. [The setup wizard stays closed](#the-setup-wizard-stays-closed)
8. [Checklist](#checklist)

## What is there by default

- The administration is a siteaccess of its own (usually `admin`). The installer writes
  `[SiteAccessSettings] RequireUserLogin=true` into its `site.ini.append.php`, so every page asks for a sign-in.
- Access to each function is decided by roles and policies (`setup/*`, `content/*`, `audit/*` ...).
- The sign-in costs the same for an unknown login as for a wrong password, so it does not reveal which accounts exist.
- The audit's alert rules `brute_force` (20 failures from one network in 5 minutes) and `brute_force_user` (10 for
  one account in 15 minutes) report attacks ([Audit trail](audit-trail.md)).

## Its own host name

Give the administration a host name of its own, for example `admin.example.com`, matched by
`[SiteAccessSettings] HostMatchType`, rather than a path below the public site. It can then be restricted as a unit:
its own virtual host, its own certificate, its own firewall rule. For hosts this also makes support simpler: the
customer's editors know one address, and the public site's cache and CDN never see administration traffic.

## Network restrictions

From the strongest to the simplest, per web server:

| Server | How |
|---|---|
| Apache | on the administration's virtual host, `Require ip` with the customer's networks, or HTTP basic authentication (`AuthType Basic`, `mod_auth_basic`) |
| nginx | `allow` and `deny` in the administration's server block |
| Velocity | no HTTP authentication of its own: the firewall, a VPN, a reverse proxy in front, or a separate Velocity bound to an internal address |

```apache
<Location />
    Require ip 203.0.113.0/24 2001:db8::/48
</Location>
```

Behind a proxy, the address Exponential sees is the proxy's unless `TrustedProxies[]` is set; a restriction in the
proxy itself avoids that question ([TLS and HTTPS](tls-and-https.md#proxies-that-terminate-tls)).

> **Note**
> Customers with editors who travel or work from home may not have a fixed network. A VPN, or the browser check
> below, then fits better than an address list. Agree it with the customer and write it into the site's documentation.

## A browser check in front

request-shield's Exponential examples put a browser check before the sign-in form and the administration, scan every
parameter for attacks, and can limit the administration to a network (`EXP-ADMIN-NET`). The administration's
background requests (the Admin 4 mega menu) get a machine-readable answer instead of a check page. Choose the main
rules file by layout: `exponential-admin-uri.rules` for `/admin` on the same host, `exponential-admin-host.rules` for an
administration host of its own. See [request-shield and request rules](request-shield-and-request-rules.md).

## Least privilege

Give editors the roles they need, not Administrator. Policies that matter for security
([13.5](../../../install/13-security-hardening.md#135-the-administration-siteaccess)):

| Policy | Gives |
|---|---|
| `setup/setup` | settings views and changes |
| `setup/managecache` | cache clearing |
| `audit/read`, `audit/manage` | the audit trail; archives, settings, "Verify now" |
| `mailpreferences/administrate`, `mailpreferences/export` | the mail preference system; exports with addresses |
| `contentfinder/search`, `contentfinder/delete` | finding content by id or path, and removing it from there |
| `user/login` per siteaccess | who may sign in to the administration at all |

One personal account per person, and `admin` retired as a sign-in
([Account hygiene](../../change-the-admin-password.md#account-hygiene)). The host's own support staff should have
personal accounts too, created and removed with their employment, never a shared "support" login.

## Configuration from version control

With `[ConfigurationSettings] ManagedByDeployment=enabled` on a production administration siteaccess, the
administration cannot run the setup wizard, edit settings or templates, activate extensions or install packages
([Configuration managed by the deployment](../../../features/6.0/managed-by-deployment.md)). Use it where settings and
templates come from Git: a stolen administrator session can then no longer change code or configuration.

## The setup wizard stays closed

On an installed site, `setup/init` only shows "This site is already installed" and writes nothing. Keep:

```ini
# settings/override/site.ini.append.php
[SiteAccessSettings]
CheckValidity=false

[SetupSettings]
AllowWizardOnInstalledSite=disabled
```

While `CheckValidity` is `true` (the shipped default before an installation), every request is sent to the setup
wizard. Never set it back on a live site; the [setup wizard on shared hosting](../provisioning/setup-wizard-on-shared-hosting.md)
page covers the provisioning side.

## Checklist

- [ ] The administration on its own host name.
- [ ] Restricted by network, VPN, basic authentication or the shield's browser check.
- [ ] No shared logins; personal accounts for editors and for the host's support staff; `admin` retired.
- [ ] Roles reviewed: `setup/setup`, `audit/*`, `mailpreferences/export` only where needed.
- [ ] Two-step sign-in required for administrators ([Two-step sign-in](two-factor-sign-in.md)).
- [ ] `ManagedByDeployment=enabled` where configuration comes from version control.
- [ ] `CheckValidity=false`, `AllowWizardOnInstalledSite=disabled`.

## Related pages

- [Security for hosts: overview](README.md)
- [Chapter 13: The administration siteaccess](../../../install/13-security-hardening.md#135-the-administration-siteaccess)
- [Change the administrator password](../../change-the-admin-password.md)
- [Request rules](../../../bc/6.0/view_full_security.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
