---
title: Go-live security checklist for public sites
audience: government, admin, project-manager, hosting
category: public-sector
order: 60
summary: A printable security checklist for the launch of a public site on Exponential, built from the project's go-live checklist and hardening guide, with a column for the evidence a public body should file for each item.
keywords: go-live checklist, launch, security checklist, acceptance, evidence, production readiness, hardening, sign-off, public sector, government
type: help
---
# Go-live security checklist for public sites

This page is for the administrator who prepares a launch and the project manager or security officer who signs it
off. It combines [13.16 Go-live checklist](../../../install/13-security-hardening.md#1316-go-live-checklist) and the
[hardening checklist](../../security-hardening.md#checklist), and adds the evidence a public body usually files for its
security documentation.

## Contents

1. [Before the launch](#before-the-launch)
2. [Accounts and sign-in](#accounts-and-sign-in)
3. [Web server, TLS and headers](#web-server-tls-and-headers)
4. [Logging and operations](#logging-and-operations)
5. [Sign-off](#sign-off)
6. [Related pages](#related-pages)

## Before the launch

| Item | Evidence to file |
|---|---|
| Rewrite rules current; `/.git/config`, `/settings/site.ini`, `/composer.json` do not return the file | `curl` output of the three requests (404 or a page, never the file) |
| `settings/override/` not in version control, backed up as a secret, mode `0640` or stricter | `ls -l settings/override`, backup record |
| `[SiteAccessSettings] CheckValidity=false`, `AllowWizardOnInstalledSite=disabled` | `./console exp:ini get site.ini/SiteAccessSettings/CheckValidity --allow-root-user` |
| `[DebugSettings] DebugOutput=disabled`; PHP `display_errors=Off` | settings output |
| `ezformtoken` active | `./console exp:ini where site.ini/ExtensionSettings/ActiveExtensions --allow-root-user` |
| File consistency checked | `bash bin/shell/verifyfiles.sh` exit status 0, or the Upgrade check report |

## Accounts and sign-in

| Item | Evidence to file |
|---|---|
| Generated administrator password changed; `var/log/initial-admin-password` and `kickstart.ini` removed | date and who |
| One personal administrator account per person; `admin` retired as a sign-in; break-glass procedure tested | list of administrators; procedure document |
| Two-step sign-in required at least for administrators | `sevenxauthentication2fa.ini` settings; enrolment list |
| Password policy decided (`MinPasswordLength`, `[PasswordSettings]`), `EndOtherSessions=enabled` | settings output |
| Lockout decision recorded (`MaxNumberOfFailedLogin`) | decision note |
| Roles reviewed: least privilege, `audit/read` and `setup/setup` only to those who need them | role export |

## Web server, TLS and headers

| Item | Evidence to file |
|---|---|
| HTTPS on every name; HTTP redirected; certificate renewal watched | `curl -sI http://...` shows a redirect; monitoring entry |
| `[Session] CookieSecure=true`; `HttpOnly` and `SameSite` on | `curl -sI https://<host>/user/login` shows the flags |
| Security headers present | `curl -sI https://<host>/` output |
| HSTS raised once stable | header value and date |
| Behind a proxy: `TrustedProxies[]` exactly your proxies | settings output and the 13.10 check |
| The administration on its own host, restricted by network or a second authentication | web server configuration extract |
| Content Security Policy enforced on the administration, report-only on sites | `Mode` per siteaccess |

## Logging and operations

| Item | Evidence to file |
|---|---|
| `exp:audit verify` intact; `frequent` cronjob group running | command output; crontab |
| Alert recipients checked | `./console exp:audit alerts recipients --allow-root-user` |
| Audit keys backed up; forwarding to a SIEM configured | backup record; sink test output |
| Mail: `exp:mail:status` ok, footer filled in, bounce reader configured | command output |
| One user writes `var/` and `settings/`; no world-writable files; workers never root | `find settings extension/*/settings -perm -o+w` prints nothing |
| request-shield in front (monitor, then enforce) | rule files, mode |
| Backups off the machine; restore tested | restore test record |
| Update routine: who watches releases, who receives security mail | named owners |

## Sign-off

Record the date, the version (`composer show se7enxweb/exponential`), the people who checked, and open items with an
owner and a date. Keep the checklist with the evidence; it is the baseline for later audits.

## Related pages

- [13.16 Go-live checklist](../../../install/13-security-hardening.md#1316-go-live-checklist)
- [Security hardening](../../security-hardening.md)
- [Launch checklist for websites](../../creators/website-creators/launch-checklist.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
