---
title: Security for hosts: overview
audience: hosting
category: hosting
order: 10
summary: The security section of the hosting help: what Exponential protects by default, what a host must add on the server, and one page per topic, from hardening, TLS and the Content Security Policy to isolation between customers, the audit trail, compliance and retention.
keywords: security, hosting, overview, hardening, TLS, HTTPS, CSP, request-shield, request rules, administration, passwords, two-factor, audit trail, security updates, disclosure, isolation, multi-tenant, PHP-FPM pools, GDPR, retention
type: help
---
# Security for hosts: overview

This section is for the security lead, the system administrators and the product managers of a hosting company that
runs Exponential sites for customers. It answers two questions: what does Exponential already do by itself, and what
is the host's part of the work on the server, in the panel and in the contract.

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
> panel: [Plesk](../panels/plesk.md), [cPanel](../panels/cpanel.md), [DirectAdmin](../panels/directadmin.md), [ISPConfig](../panels/ispconfig.md).

Exponential 6 is built to be secure by default: a fresh installation serves only a fixed list of files, sends
security headers, keeps debug output off, protects forms with tokens, hashes passwords with bcrypt, keeps a
tamper-evident audit trail and refuses to run its setup wizard on an installed site. The reasoning behind each
default is on [Security hardening](../../security-hardening.md) and the setting-by-setting reference is
[chapter 13 of the installation book](../../../install/13-security-hardening.md). The pages here take the host's view:
many sites, many customers, one team.

Back to [Hosting companies: start here](../hosting-companies-start-here.md).

## Contents

1. [Pages in this section](#pages-in-this-section)
2. [Who does what](#who-does-what)
3. [Where to start](#where-to-start)
4. [Related pages](#related-pages)

## Pages in this section

| Page | What it answers |
|---|---|
| [Hardening for hosts](hardening-for-hosts.md) | the hardening a host applies to every Exponential site, layer by layer |
| [TLS and HTTPS](tls-and-https.md) | HTTPS on every name, HSTS, secure cookies, and proxies that terminate TLS |
| [Content Security Policy](content-security-policy.md) | the shipped report-only policy, the reports, and moving a siteaccess to enforce |
| [request-shield and request rules](request-shield-and-request-rules.md) | the firewall in front of the kernel, the kernel's request classifier and its request rules |
| [Restricting the administration](restricting-the-administration.md) | its own host name, network limits, least privilege and `ManagedByDeployment` |
| [First sign-in and default passwords](first-sign-in-and-default-passwords.md) | what each installer sets as the administrator password, and what a host does after provisioning |
| [Demo mode for hosts](demo-mode-for-hosts.md) | running a public demo with `EXP_DEMO_MODE=1`, and keeping it away from every production site |
| [Two-step sign-in](two-factor-sign-in.md) | the sevenx_authentication_2fa extension: turning it on, requiring it, lost phones |
| [Audit trail](audit-trail.md) | what is recorded, verifying the hash chain, alerts, sinks and keys |
| [Security updates and disclosure](security-updates-and-disclosure.md) | supported versions, following releases, updating, and how to report a vulnerability |
| [Multi-tenant isolation](multi-tenant-isolation.md) | how far one customer's site is kept from another's, and which setup gives which boundary |
| [Per-customer users and pools](per-customer-users-and-pools.md) | one operating-system user, one PHP-FPM pool or Velocity instance, one database user per customer |
| [Compliance and GDPR](compliance-and-gdpr.md) | what the software provides for data protection and e-mail law, and what stays with host and customer |
| [Log and data retention](log-and-data-retention.md) | how long each log and record is kept, and the settings that change it |

## Who does what

Security is shared between the software, the host and the customer. A clear split avoids gaps that each side assumes
the other covers.

| Area | Exponential provides | The host provides | The customer decides |
|---|---|---|---|
| Served files | the rewrite rules of `.htaccess_root` and Velocity's list; `exp:setup:htaccess` to copy them and to show drift | a web server configuration that keeps them | nothing, unless they bring their own rules |
| `config.php` | `exp:config:*` with masked secrets, backups and a `php -l` check | mode `0640`, out of version control, `var/backup/` protected and cleaned | the values their site needs |
| Transport | HSTS and certificate handling in Velocity; secure cookies | certificates, renewal, redirects, the TLS terminator | which host names exist |
| Accounts | bcrypt hashes, password rules, the weak password check, two-step sign-in | a provisioning flow that never leaves a known password | who gets which role |
| Isolation | nothing between installations: that is the operating system's job | one user, one pool, one database user per customer | nothing |
| Logging | `security.log`, `auth.log`, `csp.log`, the audit trail with hash chains | disk space, forwarding off the machine, retention in backups | retention values that their privacy notice states |
| Updates | immutable releases, changelogs, the upgrade check | a routine that applies them | approval of a maintenance window, where the contract asks for it |

> **Note**
> This split is a starting point for your own service description, not a statement of what any contract must say.
> Plans, SLAs and support tiers are options a host chooses; see [The hosting business](../business/README.md).

## Where to start

For a host that is setting up its first Exponential offer:

1. Read [Hardening for hosts](hardening-for-hosts.md) and turn it into your server template.
2. Decide the isolation model with [Multi-tenant isolation](multi-tenant-isolation.md) and
   [Per-customer users and pools](per-customer-users-and-pools.md).
3. Make provisioning safe: [First sign-in and default passwords](first-sign-in-and-default-passwords.md).
4. Make watching possible: [Audit trail](audit-trail.md) and the [Security log](../operations/security-log.md).
5. Write the update routine: [Security updates and disclosure](security-updates-and-disclosure.md).
6. Before go-live and every quarter, run the [Security review checklist](../checklists/security-review.md).

When something has already gone wrong, the [Compromised site](../runbooks/compromised-site.md) runbook is the page to
open.

## Related pages

- [Hosting companies: start here](../hosting-companies-start-here.md)
- [Security hardening](../../security-hardening.md)
- [Chapter 13: Security hardening for production](../../../install/13-security-hardening.md)
- [Security review checklist](../checklists/security-review.md)
- [Compromised site](../runbooks/compromised-site.md)
- [Setting up .htaccess and config.php from the console](../../../guides/setup-files-from-the-console.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
