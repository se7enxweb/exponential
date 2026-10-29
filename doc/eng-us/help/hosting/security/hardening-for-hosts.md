---
title: Hardening for hosts
audience: hosting, admin
category: hosting
order: 20
summary: The hardening a host applies to every Exponential site, layer by layer: served files, secrets, debug output, the administration, passwords, sessions, headers and TLS, proxies, file ownership, Velocity, mail, the audit trail and updates, with the settings and checks for each and a server-template checklist.
keywords: hardening, security, hosting, server template, htaccess_root, rewrite rules, settings/override, secrets, debug, DebugOutput, administration, passwords, sessions, CookieSecure, security headers, HSTS, TrustedProxies, file permissions, FileSettings, Velocity, AllowRootWorkers, audit, request-shield, updates
featured: true
type: help
---
# Hardening for hosts

This page is for system administrators and security staff of a host who build the server template that every
Exponential site is created from. It goes through the layers of a site from the outside in, says what Exponential
does by default, and names the setting or command that the host sets or checks. It follows the order of
[chapter 13 of the installation book](../../../install/13-security-hardening.md), which has every detail, and the
threat-by-threat view of [Security hardening](../../security-hardening.md).

> **Note**
> Settings are changed in overrides, never in the shipped files: `settings/override/<file>.ini.append.php` for the
> whole installation, `settings/siteaccess/<siteaccess>/<file>.ini.append.php` for one siteaccess. After a change,
> clear the INI cache (`php bin/php/ezcache.php --clear-tag=ini`), and under Velocity run `exp:velocity deploy`
> ([chapter 13](../../../install/13-security-hardening.md#contents-of-this-chapter)).

## Contents

1. [The principles](#the-principles)
2. [Served files](#served-files)
3. [Secrets](#secrets)
4. [Debug output](#debug-output)
5. [Administration, passwords and sessions](#administration-passwords-and-sessions)
6. [Headers, TLS and proxies](#headers-tls-and-proxies)
7. [Files, users and processes](#files-users-and-processes)
8. [Watching](#watching)
9. [A shield in front](#a-shield-in-front)
10. [Updates](#updates)
11. [Server-template checklist](#server-template-checklist)

## The principles

[Security hardening](../../security-hardening.md#principles-secure-by-default) measures Exponential against the
OpenBSD way of working: secure by default, least privilege, privilege separation, minimal attack surface, audit, and
fixing forward. For a host that means one rule above all: **do not loosen a default to make something work; find the
cause.** A support ticket that ends with "we switched the rewrite rule off" has opened a hole on that site.

## Served files

The document root of Exponential is the installation root, so `settings/`, `var/`, `vendor/` and the SQLite database
lie inside it. Safety comes from routing: only a fixed list of asset paths is served as files, everything else goes
to `index.php` ([13.2](../../../install/13-security-hardening.md#132-what-the-web-server-must-never-hand-out)).

| Web server | What the host does |
|---|---|
| Apache, LiteSpeed with `.htaccess` | `cp .htaccess_root .htaccess`; compare after every upgrade and merge new rules |
| nginx | the derived server block of [8.6](../../../install/08-serving-the-site.md#86-nginx-with-php-fpm), with the dot-file and script rules ahead of every static location |
| Velocity | nothing; `FollowSymlinks=disabled` (the default) also refuses links out of the root |
| any | never a generic "serve the file if it exists" configuration |

Check from outside after every change of the web server's configuration:

```bash
for p in /.git/config /.env /settings/site.ini /composer.json /kernel/classes/ezini.php /var/log/error.log; do
  printf '%-32s %s\n' "$p" "$(curl -s -o /dev/null -w '%{http_code}' https://example.com$p)"
done
```

Each line must show `404`, or `200` with an Exponential page and never the file's content. Web server details:
[Choosing a web server](../servers/choosing-a-web-server.md) and
[Chapter 8](../../../install/08-serving-the-site.md).

## Secrets

`settings/override/` holds the database password (`site.ini.append.php`), the audit signing and pseudonym keys
(`audit.ini.append.php`), the site secret of the mail preferences (`mailpreferences.ini.append.php`, which also keys
the session password stamp) and, when set, Velocity's dashboard token
([13.3](../../../install/13-security-hardening.md#133-secrets-in-settingsoverride)).

- Mode `0640` or stricter, never in version control, never in a public place.
- Back it up as a secret, encrypted, with the database ([Backups: what to back up](../operations/backups-what-to-back-up.md)).
- Copy it with every move of the site. A copy without `mailpreferences.ini.append.php` generates a new site secret:
  old unsubscribe links stop matching and every user is signed out once.
- Remove `kickstart.ini` and `var/log/initial-admin-password` after provisioning
  ([First sign-in and default passwords](first-sign-in-and-default-passwords.md)).

## Debug output

`[DebugSettings] DebugOutput=disabled` and `[TemplateSettings] Debug=disabled` ship off. Keep them off. When support
must debug a live site, limit it to the support desk's address and remove the entry again:

```ini
# settings/override/site.ini.append.php
[DebugSettings]
DebugOutput=enabled
DebugByIP=enabled
DebugIPList[]
DebugIPList[]=203.0.113.10
```

In PHP-FPM's `php.ini` or pool: `display_errors=Off`, `log_errors=On`, `expose_php=Off`
([13.4](../../../install/13-security-hardening.md#134-debug-output-and-error-display)). Behind a proxy, `DebugByIP`
is only as reliable as `TrustedProxies[]` (see [TLS and HTTPS](tls-and-https.md#proxies-that-terminate-tls)).

## Administration, passwords and sessions

| Topic | Default | Host's part |
|---|---|---|
| Administration siteaccess | `RequireUserLogin=true` | its own host name, network limits: [Restricting the administration](restricting-the-administration.md) |
| Setup wizard | closed on an installed site; `CheckValidity=false` written by the installer | keep `[SetupSettings] AllowWizardOnInstalledSite=disabled` |
| Passwords | `MinPasswordLength=10`, `WeakPasswordCheck=enabled`, bcrypt | offer a policy such as `MinPasswordLength=15`, `ForbidLogin`, `ForbidCurrentPassword` ([13.6](../../../install/13-security-hardening.md#136-passwords-and-sign-in)) |
| Lockout | `MaxNumberOfFailedLogin=0` (off) | decide with the customer; a lockout also lets anyone lock a known account |
| Two-step sign-in | extension required by `composer.json` and activated by the installers; `Enforce2FA=disabled` | recommend `Enforce2FA` for administrators: [Two-step sign-in](two-factor-sign-in.md) |
| Session cookie | `HttpOnly`, `SameSite=Lax`, `CookieSecure=auto` | `CookieSecure=true` on HTTPS-only sites ([13.7](../../../install/13-security-hardening.md#137-sessions-and-cookies)) |
| Form tokens | `ezformtoken` active | keep it in `ActiveExtensions[]` ([13.8](../../../install/13-security-hardening.md#138-form-tokens-against-cross-site-request-forgery)) |
| Configuration from Git | `ManagedByDeployment=disabled` | `enabled` on production administration siteaccesses when settings and templates come from version control |

A password change signs out every other session of that user (`[PasswordSettings] EndOtherSessions=enabled`). Keep it
on: it is what makes a password change useful after an account was taken over.

## Headers, TLS and proxies

Five security headers go out on every page (`X-Content-Type-Options`, `X-Frame-Options`,
`Content-Security-Policy: frame-ancestors 'self'`, `Referrer-Policy`, `Permissions-Policy`); HSTS is off until it is
switched on. Apache and nginx add nothing to static files by themselves. The host's part:

- HTTPS on every name, HTTP redirected, HSTS raised once everything works: [TLS and HTTPS](tls-and-https.md).
- The full policy (`script-src`, `style-src`) is report-only on the administration as shipped:
  [Content Security Policy](content-security-policy.md).
- Behind a load balancer, CDN or panel proxy: list exactly your proxies in `site.ini [HTTPHeaderSettings]
  TrustedProxies[]` (Velocity: `Q.webserver.proxy.trusted`), and make sure the application server cannot be reached
  around them ([13.10](../../../install/13-security-hardening.md#1310-behind-a-proxy-trusted-headers)).

## Files, users and processes

"If the PHP process can change only `var/` and `settings/`, and the web server refuses to run anything below the
root, a flaw in an extension cannot plant code"
([13.11](../../../install/13-security-hardening.md#1311-file-permissions-and-ownership)).

- One user writes: PHP-FPM pool, Velocity workers, cronjobs and console scripts run as the site's user.
- The code (`kernel/`, `lib/`, `vendor/`, `index.php`) belongs to another user and is read-only to the site's user.
- No world-writable files. The shipped `[FileSettings]` values are `0777` and `0666`; set the hardened ones:

```ini
# settings/override/site.ini.append.php
[FileSettings]
StorageDirPermissions=0770
StorageFilePermissions=0660
TemporaryPermissions=0770
LogFilePermissions=0660
```

- `TemporaryDir` ships as `/tmp/`, shared with every user of the machine; give each site one of its own below `var/`.
- Velocity: `[ServerSettings] Host=127.0.0.1` when a proxy is in front, `User` and `Group` set to the site's user,
  `AllowRootWorkers=disabled`, dashboard token and control panel password set or the views kept local
  ([13.12](../../../install/13-security-hardening.md#1312-velocity-specific-hardening)).

How this scales to many customers: [Per-customer users and pools](per-customer-users-and-pools.md) and
[Writable directories and permissions](../basics/writable-directories-and-permissions.md).

## Watching

Hardening without watching only delays the moment you learn of an attack.

| Source | What it records | Host's page |
|---|---|---|
| `var/log/security.log` | the request classifier's verdicts: probes, traversal, injection, attack tools, 404 bursts | [Security log](../operations/security-log.md) |
| the audit trail | sign-ins, role and setting changes, removals, commands; hash-chained | [Audit trail](audit-trail.md) |
| `var/log/auth.log` | every step of two-step sign-in (never a code or a secret) | [Two-step sign-in](two-factor-sign-in.md) |
| `var/log/csp.log` | Content Security Policy violations | [Content Security Policy](content-security-policy.md) |

Run the `frequent` cronjob group so the audit's alert rules run, forward the trail off the machine with a sink, and
read `security.log` weekly ([Security hardening](../../security-hardening.md#watching-logs-the-request-classifier-and-the-audit-trail)).

## A shield in front

[request-shield](../../security-hardening.md#a-shield-in-front-request-shield), a small web application firewall for
PHP by the CJW Network, refuses scanners and floods before the kernel starts. It is recommended for every site that
can be reached from the internet and is not part of Exponential's `composer.json`. A host can offer it as part of a
managed plan: [request-shield and request rules](request-shield-and-request-rules.md).

## Updates

Only the 6.0 line receives security fixes, published as the next 6.0.x release. Follow releases, update a copy first,
check file consistency after every update, and keep PHP, the database server, the operating system and Velocity's
engine current as well: [Security updates and disclosure](security-updates-and-disclosure.md).

## Server-template checklist

- [ ] Rewrite rules: a current `.htaccess_root` copy, the derived nginx block, or Velocity; the outside check returns
      no file.
- [ ] SOAP only where a customer needs it: `index_soap.php` let through (it was `soap.php` before 6.0.15) and limited
      to the callers' addresses; `soap.php` refused once nothing calls it
      ([the SOAP entry script is now index_soap.php](../../../bc/6.0/soap-entry-script-index-soap.md)).
- [ ] `settings/override/` mode `0640` or stricter, out of version control, backed up encrypted.
- [ ] Provisioning removes `kickstart.ini` and `var/log/initial-admin-password`, and never leaves a known password.
- [ ] `DebugOutput=disabled`; PHP `display_errors=Off`, `expose_php=Off`.
- [ ] Administration on its own host name, restricted by network, VPN or the shield's browser check.
- [ ] `CheckValidity=false`, `AllowWizardOnInstalledSite=disabled`.
- [ ] `ezformtoken` active; `EndOtherSessions=enabled`.
- [ ] HTTPS on every name, HTTP redirected, `CookieSecure=true`, HSTS raised once stable.
- [ ] `TrustedProxies[]` lists exactly your proxies, or stays at the loopback default.
- [ ] One operating-system user per site; code read-only to it; hardened `[FileSettings]`; private `TemporaryDir`.
- [ ] Velocity bound to `127.0.0.1` behind a proxy; workers never root.
- [ ] `frequent` cronjob group running as the site's user; audit alert recipients checked; a sink configured.
- [ ] An update routine with named owners.

The setting-by-setting go-live list is [13.16](../../../install/13-security-hardening.md#1316-go-live-checklist);
the recurring review is the [Security review checklist](../checklists/security-review.md).

**Demo mode stays off.** `EXP_DEMO_MODE=1` installs and keeps a known administrator password; set it only for a public demo's own pool, virtual host or unit, never server-wide or in a template, and alert on `demo/mode` lines in the `security.log` of customer sites. [Demo mode for hosts](demo-mode-for-hosts.md).

## Related pages

- [Security for hosts: overview](README.md)
- [Security hardening](../../security-hardening.md)
- [Chapter 13: Security hardening for production](../../../install/13-security-hardening.md)
- [New server checklist](../checklists/new-server.md)
- [Compromised site](../runbooks/compromised-site.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
