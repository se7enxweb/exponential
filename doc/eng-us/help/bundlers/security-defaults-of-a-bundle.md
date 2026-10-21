---
title: Security defaults of a bundle
audience: bundler, hosting
category: partnership
order: 380
summary: The security state a bundle should hand over - what Exponential already does by default, the settings a bundle should decide (file modes, debug output, CheckValidity, cookies, HTTPS and headers, trusted proxies, demo mode off), what must never ship, and the go-live checklist of the installation guide applied to bundles.
keywords: security, defaults, hardening, bundle, CheckValidity, DebugOutput, CookieSecure, HTTPS, HSTS, security headers, TrustedProxies, file permissions, demo mode, ezformtoken, audit, go-live checklist
type: help
---
# Security defaults of a bundle

This page is for bundlers who want their bundle to be safe when it is switched on, before the recipient has read
anything. The reference is [Chapter 13: Security hardening for production](../../install/13-security-hardening.md);
the overview for administrators is [Security hardening](../security-hardening.md).

## Contents

1. [What Exponential already does](#what-exponential-already-does)
2. [What a bundle should decide](#what-a-bundle-should-decide)
3. [What must never ship](#what-must-never-ship)
4. [The go-live checklist for bundles](#the-go-live-checklist-for-bundles)
5. [Security problems after release](#security-problems-after-release)

## What Exponential already does

From the defaults table in [13.1 The layers at a glance](../../install/13-security-hardening.md#131-the-layers-at-a-glance):

| Layer | Default |
|---|---|
| Rewrite rules | strict list, PHP below the root refused |
| Secrets | generated into `settings/override` |
| Debug output | off |
| Admin siteaccess | sign-in required |
| Passwords | 10 characters, bcrypt |
| Session cookie | `HttpOnly`, `SameSite=Lax`, `Secure` over HTTPS |
| Form tokens | active |
| Forwarded headers | believed only from `127.0.0.1` and `::1` |
| Security headers | five headers on every page |
| Audit | on |

## What a bundle should decide

| Topic | Decision | Where |
|---|---|---|
| File modes | The shipped `[FileSettings]` modes are `0777`/`0666`; `site.ini` recommends `0770`/`0660` with the right user and group | [File permissions and var/ ownership](file-permissions-and-var-ownership.md) |
| Ownership | one user (or one group) writes `var/` and `settings/`; workers never root | the same page |
| HTTPS | on every name, HTTP redirected; HSTS raised once stable | [13.9](../../install/13-security-hardening.md#139-security-headers-and-https) |
| Cookies | `[Session] CookieSecure=true` on an HTTPS-only bundle | [13.7](../../install/13-security-hardening.md#137-sessions-and-cookies) |
| Proxies | `TrustedProxies[]` lists exactly the proxies your bundle puts in front | [13.10](../../install/13-security-hardening.md#1310-behind-a-proxy-trusted-headers) |
| Administration | its own host where the bundle allows it (`exp:install --access=host --host ... --admin-host ...`) | [13.5](../../install/13-security-hardening.md#135-the-administration-siteaccess) |
| Settings from the browser | `[ConfigurationSettings] ManagedByDeployment=enabled` for bundles managed by deployment | [ManagedByDeployment](../../features/6.0/managed-by-deployment.md) |
| Weak passwords | add your product name to `WeakPasswordList[]` | [Default credentials](default-credentials-and-first-sign-in.md) |

## What must never ship

- A filled `settings/override/` or `settings/siteaccess/` from your build ([First boot and install once](first-boot-and-install-once.md#what-must-not-be-in-the-bundle)).
- A `kickstart.ini` with passwords; `var/log/initial-admin-password`; logs of your build.
- `EXP_DEMO_MODE=1` in any unit, pool, image or crontab of a bundle for real sites
  ([Demo mode must be off](../../install/13-security-hardening.md#demo-mode-must-be-off)).
- `[DebugSettings] DebugOutput=enabled` or PHP `display_errors=On`.
- Web server rules that serve `settings/`, `var/`, `.git` or `composer.json`.
- Private keys or tokens in extensions: exports refuse "an extension that holds a secret value or key material",
  and so should your build.

## The go-live checklist for bundles

Apply [13.16 Go-live checklist](../../install/13-security-hardening.md#1316-go-live-checklist) to the installed bundle
in your test environment. The items a bundle can settle before release:

- [ ] `/.git/config`, `/settings/site.ini`, `/composer.json` do not return the file.
- [ ] `settings/override/` has mode `0640` or stricter after the install.
- [ ] `[DebugSettings] DebugOutput=disabled`; `display_errors=Off`.
- [ ] `[SiteAccessSettings] CheckValidity=false` after the install (the installer writes it).
- [ ] No **Demo mode** banner.
- [ ] `ezformtoken` active.
- [ ] Security headers present.
- [ ] One user or group writes `var/` and `settings/`; no world-writable files; workers never root.

The rest (personal accounts, HTTPS certificates, mail, audit alert recipients, the update routine) belongs to the
recipient; list it in your getting-started text.

## Security problems after release

- A vulnerability in Exponential: report it privately to security@se7enx.com, "not in the public issue tracker"
  (`SECURITY.md`); fixes arrive in the next 6.0.x release.
- Your bundle then needs a release with that version ([Updates and upgrade paths](updates-and-upgrade-paths.md)).
- Partners: [Escalations and security issues](../partners/escalations-and-security-issues.md).

## Related pages

- [Hardening for hosts](../hosting/security/hardening-for-hosts.md)
- [Security responsibilities](../partners/security-responsibilities.md)
- [Default credentials and first sign-in](default-credentials-and-first-sign-in.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
