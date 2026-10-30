---
title: Exponential's security posture at a glance
audience: government, project-manager, admin
category: public-sector
order: 20
summary: For public bodies assessing Exponential - the project's own status against the OWASP Top 10, OWASP ASVS, Content Security Policy, the OWASP cheat sheets and coordinated disclosure, what is on by default, what is off by default, and what the operator must decide.
keywords: security posture, OWASP Top 10, ASVS, CSP, secure by default, defaults, audit trail, two-step sign-in, lockout, HSTS, disclosure, security assessment, public sector, government
type: reference
---
# Exponential's security posture at a glance

This page is for project managers, information security officers and administrators in public bodies who need a
one-page view of Exponential's security before reading the detail. Every status comes from the project's own
statement, [Security standards](../../../standards/security.md); the words "Partly complies", "Unverified" and
"Complies" are kept as written there. The project makes no claim of an external assessment.

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
> **On LiteSpeed or OpenLiteSpeed,** the server's own page cache can also answer anonymous visitors and crawlers without PHP: [Page caching with LiteSpeed and OpenLiteSpeed](../../../guides/litespeed-page-cache.md).

## Contents

1. [Status by standard](#status-by-standard)
2. [On by default](#on-by-default)
3. [Off by default: your decisions](#off-by-default-your-decisions)
4. [Known gaps](#known-gaps)
5. [Related pages](#related-pages)

## Status by standard

| Standard | Status | In one line |
|---|---|---|
| OWASP Top 10 (2021) | **Partly complies** | mitigations in all ten categories; A09 Logging and monitoring is rated **Complies** |
| OWASP ASVS 4.0.3 | **Unverified** | no assessment; level 1 needs `MinPasswordLength` raised to 12 |
| Content Security Policy Level 3 | **Partly complies** | report-only on the administration by default, off on public sites |
| OWASP Cheat Sheet Series | **Partly complies** | CSRF tokens, password storage, session cookies, headers, escaping, uploads |
| Coordinated vulnerability disclosure | **Partly complies** | `SECURITY.md` with a private channel; no `security.txt` shipped |

"Complies" would need an external assessment, which has not been made
([Security standards: Status](../../../standards/security.md#status)).

## On by default

| Defense | Source |
|---|---|
| Security headers on every page (`nosniff`, `SAMEORIGIN`, `frame-ancestors 'self'`, `Referrer-Policy`, `Permissions-Policy`) | `site.ini [HTTPHeaderSettings] SecurityHeaders[]` |
| Session cookie `HttpOnly`, `SameSite=Lax`, `Secure` over HTTPS | `site.ini [Session]` |
| Passwords hashed with `password_hash()` (bcrypt), old hashes upgraded at sign-in | `[UserSettings] HashType=php_default`, `UpdateHash=true` |
| Well-known and common passwords refused | `WeakPasswordCheck=enabled` |
| Other sessions signed out after a password change | `[PasswordSettings] EndOtherSessions=enabled` |
| CSRF form tokens on every form | extension `ezformtoken`, activated by the setup wizard |
| Tamper-evident audit trail with alert rules | `audit.ini [AuditSettings] Audit=enabled` |
| Request classifier writing probes and attacks to `security.log` | `requestclassifier.ini` |
| Setup wizard closed on an installed site | `expSetupWizardGuard` |
| No PHP executed below the document root; dot files refused | `.htaccess_root` |
| Debug output off | `[DebugSettings] DebugOutput=disabled` |
| Secrets in `config.php` masked in console output and diffs; every edit backed up to a `0700` directory | `exp:config:*`, `exp:setup:*` ([from the console](../../../guides/setup-files-from-the-console.md#10-security)) |

## Off by default: your decisions

| Setting | Shipped | Why it is off | Page |
|---|---|---|---|
| Account lockout | `MaxNumberOfFailedLogin=0` | a lockout also lets anyone lock a known account | Account lockout |
| Two-step sign-in required | `Enforce2FA=disabled` | needs enrolment of every user | Two-step sign-in |
| CSP enforced | `Mode=auto` (report-only on admin) | extensions and designs must be checked first | Content Security Policy |
| HSTS | empty | must only be set once every name is HTTPS-only | HTTPS and HSTS |
| Minimum password length 12 or more | 10 | a policy decision | Password policy |
| Configuration locked | `ManagedByDeployment=disabled` | only where settings come from version control | Configuration managed by the deployment |
| Deactivate and move for leavers | `DeactivateAndMove=disabled` | opt-in feature | Roles and least privilege |

## Known gaps

From [Security standards: Known gaps](../../../standards/security.md#known-gaps):

- lockout off by default; brute force is detected by alert rules, not throttled by the kernel;
- two-step sign-in is opt-in;
- no prepared statements in the database layer (values are escaped);
- output escaping in templates is not automatic (`|wash` must be applied);
- older password hash types still readable for migrated accounts until they sign in;
- no dependency vulnerability scan in CI;
- HSTS empty by default.

## Related pages

- [Security standards](../../../standards/security.md)
- [Security hardening](../../security-hardening.md)
- [Chapter 13: Security hardening for production](../../../install/13-security-hardening.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
