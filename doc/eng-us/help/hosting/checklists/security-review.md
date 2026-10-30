---
title: Security review checklist
audience: hosting, admin
category: hosting
order: 50
summary: A security review a host runs for an Exponential site before go-live, after an incident and once a quarter: versions, accounts, settings, served files, headers and CSP, request logs, isolation, secrets, backups and the disclosure path.
keywords: security review, checklist, audit, hardening, versions, accounts, passwords, two-factor, debug, served files, rewrite rules, CSP, headers, TLS, security.log, request-shield, isolation, secrets, backups, SECURITY.md
type: help
---
# Security review checklist

This checklist is for the host's security reviewer or system administrator. Use it before a site goes live, after a
security incident, and at least once a quarter. The reasons behind each item are on
[Security hardening](../../security-hardening.md) and in
[chapter 13 of the installation book](../../../install/13-security-hardening.md); the hosting view is on
[Hardening for hosts](../security/hardening-for-hosts.md).

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
> **On LiteSpeed or OpenLiteSpeed,** the server's own page cache can also answer anonymous visitors and crawlers without PHP: [Page caching with LiteSpeed and OpenLiteSpeed](../../../guides/litespeed-page-cache.md).

```text
Site: <domain>        Reviewer: <name>        Date: <date>        Reason: <go-live | incident | quarterly>
```

## Versions

- [ ] Exponential is on the newest 6.0.x release, or the gap is documented with a date to close it. Only the 6.0 line
      gets security fixes (`SECURITY.md`).
- [ ] Extensions are on current releases.
- [ ] PHP and the operating system receive security updates.

## Accounts

- [ ] No default or shared administrator password ([The default administrator password](../../default-administrator-password.md)).
- [ ] Administrators use two-factor sign-in, where the customer agreed ([Two-factor sign-in](../security/two-factor-sign-in.md)).
- [ ] Every account with administration rights belongs to a named person who still needs it.
- [ ] Anonymous has no policies beyond what the public site needs.

## Settings and served files

- [ ] Debug output is off on public siteaccesses.
- [ ] Only the asset paths are served as files; `settings/`, `var/log`, `var/cache` and source files are not
      reachable from the web. Test it: a request for a file under `settings/` must not return it.
- [ ] The setup wizard stays closed: `settings/override/site.ini.append.php` has `[SiteAccessSettings] CheckValidity=false`
      ([Chapter 13](../../../install/13-security-hardening.md), [First sign-in and default passwords](../security/first-sign-in-and-default-passwords.md)).
- [ ] Uploads are stored under `var/` and are not executed as PHP.

## Transport and headers

- [ ] HTTPS everywhere, certificate valid, HTTP redirects ([TLS and HTTPS](../security/tls-and-https.md)).
- [ ] The Content Security Policy is enforced in the administration and produces no unexpected reports
      ([Content Security Policy](../security/content-security-policy.md)).
- [ ] The administration is restricted by network or a second factor, as agreed ([Restricting the administration](../security/restricting-the-administration.md)).

## Logs and detection

- [ ] `security.log` is written and reviewed ([Security log](../operations/security-log.md)).
- [ ] Behind a proxy or load balancer, `TrustedProxies[]` is configured so the logs see real client addresses
      ([Security hardening](../../security-hardening.md)).
- [ ] A shield in front of the site is decided: request-shield or the kernel's request rules
      ([request-shield and request rules](../security/request-shield-and-request-rules.md)).
- [ ] The audit trail is on and readable by the reviewer ([Audit trail](../security/audit-trail.md)).

## Isolation and secrets

- [ ] The site runs as its own system user, with its own PHP-FPM pool or Velocity instance ([Multi-tenant isolation](../security/multi-tenant-isolation.md)).
- [ ] `settings/override` and backups that contain it are readable only by the site's user and the backup system.
- [ ] No secrets in Git repositories, tickets or shared folders.
- [ ] The database user of the site can reach only the site's database.

## Backups and recovery

- [ ] Backups are stored off the machine and cannot be deleted from the site's account.
- [ ] A restore was tested within the agreed period ([Restore and restore tests](../operations/restore-and-restore-tests.md)).

## Disclosure

- [ ] Your team knows how to report a vulnerability in Exponential privately, as `SECURITY.md` describes
      ([Security updates and disclosure](../security/security-updates-and-disclosure.md)).
- [ ] Your own incident procedure is current ([Compromised site](../runbooks/compromised-site.md)).

**Demo mode.**

- [ ] `grep -r EXP_DEMO_MODE` over the PHP-FPM pools, virtual hosts, `.htaccess` files, systemd units and drop-ins, crontabs, `/etc/environment` and container definitions finds only the configuration of public demos; no production site has a `demo/mode` line in `security.log` or a `system.demo.mode` audit event ([Demo mode for hosts](../security/demo-mode-for-hosts.md)).

## Related pages

- [Security review summary for leaders](../../leadership/accessibility-security-and-compliance-checklists.md)
- [Monthly operations checklist](monthly-operations.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
