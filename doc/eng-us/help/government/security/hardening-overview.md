---
title: Hardening an Exponential site for a public body
audience: government, admin, developer, hosting
category: public-sector
order: 50
summary: For administrators and hosting teams of public sites - the hardening layers of Exponential in the order to work through them (web server, settings and secrets, administration, sign-in, sessions, headers, CSP, uploads, logging, updates), with the setting and the source page for each.
keywords: hardening, security baseline, secure configuration, layers, web server rules, settings override, administration siteaccess, passwords, sessions, headers, CSP, uploads, logging, updates, request-shield, public sector
type: help
---
# Hardening an Exponential site for a public body

This page is for administrators, developers and hosting teams who prepare a public site for production. It orders the
hardening work into layers and points to the detailed page for each. The project's two sources are
[Security hardening](../../security-hardening.md), the attacker-minded guide, and
[Chapter 13: Security hardening for production](../../../install/13-security-hardening.md), the setting-by-setting
reference. Since September 2026 a default installation is safe without extra work; hardening means keeping those
defaults and closing what is left to the operator.

> **Warning**
> **`.htaccess` slows down every request.** When `.htaccess` files are enabled (`AllowOverride` anything but `None`), let alone
> used, Apache looks for them and reads and evaluates them on every request, in every directory of the path, and
> page loading times go up drastically ([measured](../../../guides/setup-files-from-the-console.md#measured-htaccess-against-rules-in-the-virtual-host)). The only real fix is to switch the feature off for
> the virtual host: `AllowOverride None` in the HTTPS virtual host, and the rules in the virtual host's own
> configuration as mod_rewrite rules. That is far faster in production speed tests and well worth the time it takes
> to optimize a production configuration. Switch over right away: copy the rules of the
> [example HTTPS virtual host](../../../examples/web-servers/apache-virtual-host.md) into your virtual host (with the static cache, also
> [`staticcache_offload.conf`](../../../examples/staticcache_offload.conf)), set `AllowOverride None`, run `apachectl configtest`, reload Apache and
> test the site. The same rules for other servers: [nginx](../../../examples/web-servers/nginx.md), [LiteSpeed](../../../examples/web-servers/litespeed.md),
> [Caddy](../../../examples/web-servers/caddy.md), [IIS](../../../examples/web-servers/iis.md), [Velocity](../../../examples/web-servers/velocity.md) (built in); where virtual host rules go in a control
> panel: [Plesk](../../hosting/panels/plesk.md), [cPanel](../../hosting/panels/cpanel.md), [DirectAdmin](../../hosting/panels/directadmin.md), [ISPConfig](../../hosting/panels/ispconfig.md).

## Contents

1. [The principles](#the-principles)
2. [The layers, in order](#the-layers-in-order)
3. [What not to do](#what-not-to-do)
4. [Related pages](#related-pages)

## The principles

The hardening guide follows the OpenBSD yardstick: secure by default, least privilege, privilege separation, minimal
attack surface, audit, and fixing forward in public
([Principles: secure by default](../../security-hardening.md#principles-secure-by-default)). For a public body the
practical meaning is: do not loosen a default to make something work; find the cause.

## The layers, in order

| Layer | Do | Page in this section |
|---|---|---|
| 1. Web server | current rewrite rules (`.htaccess_root` or the derived nginx block; `./console exp:setup:htaccess` exits 1 when `.htaccess` has drifted); nothing below the root executed; dot files 404 | File uploads and SVG; HTTPS and HSTS |
| 2. Secrets | `settings/override/` and `config.php` never in version control, mode 0640, backed up as a secret; old copies in `var/backup/` removed | Secrets in settings/override; [config.php from the console](../../../guides/setup-files-from-the-console.md#10-security) |
| 3. Administration | its own host name, restricted by network or a second authentication, `RequireUserLogin=true`, setup wizard closed | Separating the administration |
| 4. Accounts | one personal account per person, `admin` retired, least-privilege roles | Roles and least privilege |
| 5. Sign-in | two-step sign-in, password policy, lockout decision | Two-step sign-in; Password policy; Account lockout |
| 6. Sessions | `CookieSecure=true`, sensible timeout, separate cookie per siteaccess | Sessions and cookies |
| 7. Headers and TLS | security headers present, HTTPS everywhere, HSTS when stable | Security headers; HTTPS and HSTS |
| 8. Content Security Policy | report-only, then enforce on the administration, then on sites | Content Security Policy; From report-only to enforce |
| 9. Forms | `ezformtoken` active | CSRF and form tokens |
| 10. Debug | `DebugOutput=disabled`, PHP `display_errors=Off` | Debug output in production |
| 11. Files | one writer, no world-writable files, code read-only | File permissions and ownership |
| 12. Configuration lock | `ManagedByDeployment=enabled` where configuration comes from version control | Configuration managed by the deployment |
| 13. A shield in front | request-shield, monitor mode first | request-shield |
| 14. Logging | audit cronjob running, alert recipients, SIEM forwarding, weekly review | Audit trail; Log review routine |
| 15. Updates | follow releases, test in a copy, `composer audit`, file consistency check | Patching cadence; File consistency check |
| 16. Recovery | backups off the machine, restore tested | Backups and recovery |

## What not to do

- Do not set `CheckValidity=true` or `AllowWizardOnInstalledSite=enabled` on a live site.
- Do not widen `TrustedProxies[]` to a provider network or `0.0.0.0/0`
  ([13.10](../../../install/13-security-hardening.md#1310-behind-a-proxy-trusted-headers)).
- Do not add `'unsafe-inline'` to the administration's policy to silence reports without knowing their source.
- Do not run cronjobs or the console as root except for the documented tasks.
- Do not give the Administrator role to colleagues "for a moment"; make a role.

## Related pages

- [Security hardening](../../security-hardening.md)
- [Chapter 13: Security hardening for production](../../../install/13-security-hardening.md)
- [Security and audit: lock it down, see who did what](../../../guides/security-and-audit.md)
- [Security for hosts](../../hosting/security/README.md)
- [Setting up .htaccess and config.php from the console](../../../guides/setup-files-from-the-console.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
