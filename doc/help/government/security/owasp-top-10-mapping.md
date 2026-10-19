---
title: Mapping the OWASP Top 10 to Exponential
audience: government, admin, developer, project-manager
category: public-sector
order: 30
summary: A category-by-category view of the OWASP Top 10 (2021) for public bodies - the mitigations Exponential ships, the operator's part, and the evidence to show an auditor, based on the project's security statement.
keywords: OWASP Top 10, A01, A02, A03, A04, A05, A06, A07, A08, A09, A10, broken access control, injection, XSS, misconfiguration, vulnerable components, authentication, integrity, logging, SSRF, mapping, audit evidence
type: reference
---
# Mapping the OWASP Top 10 to Exponential

This page is for information security officers, auditors and administrators who use the OWASP Top 10
(<https://owasp.org/Top10/>) as a checklist. For each category it repeats what Exponential ships, from
[Security standards: OWASP Top 10](../../../standards/security.md#owasp-top-10-2021), and adds what the operator of a
public site must do and what evidence to keep. The project rates the whole **Partly complies**.

## Contents

1. [The map](#the-map)
2. [Evidence to keep](#evidence-to-keep)
3. [Related pages](#related-pages)

## The map

| ID | Category | Exponential ships | Your part |
|---|---|---|---|
| A01 | Broken Access Control | roles and policies checked for every module view; content read access per node; downloads only through `content/download`; PHP below the root refused; safe redirects; setup wizard locked when installed | least-privilege roles, one account per person, request rules for system URLs, review roles yearly |
| A02 | Cryptographic Failures | `password_hash()` (bcrypt), upgrade of old hashes; tokens from `random_bytes()`; `Secure` cookies over HTTPS; HSTS available | HTTPS on every name, `CookieSecure=true`, HSTS raised when stable |
| A03 | Injection | `wash` escaping in templates; SQL escaping and integer casts reviewed; `escapeshellarg()`; CRLF stripped from headers | review custom extensions for the same discipline; request-shield in front |
| A04 | Insecure Design | secure defaults since September 2026; login fails closed; `ManagedByDeployment` | turn on `ManagedByDeployment` where configuration comes from version control |
| A05 | Security Misconfiguration | security headers; debug limited to listed addresses; **Setup > Upgrade check** file consistency | keep debug off; check file consistency after updates; go-live checklist |
| A06 | Vulnerable and Outdated Components | jQuery 4, YUI removed; supported versions in `SECURITY.md`; `composer audit` recommended | patching cadence; `composer audit`; follow extension releases (no dependency scan in the project's CI) |
| A07 | Identification and Authentication Failures | minimum length 10, weak password check, uniform answers, new session id at sign-in, other sessions ended on password change; optional lockout; optional two-step sign-in | require two-step sign-in, raise the minimum length, decide on lockout |
| A08 | Software and Data Integrity Failures | immutable release tags; `share/filelist.md5` checked in CI and in the admin; CSRF tokens; SVG sanitizer; hash-chained audit | verify files after updates; keep `ezformtoken` active |
| A09 | Security Logging and Monitoring Failures | audit trail on by default with alert rules; `security.log`; `csp.log` — rated **Complies** | run the `frequent` cronjob group, route alert mail to people, forward to a SIEM, review logs weekly |
| A10 | Server-Side Request Forgery | the online editor's "upload from URL" refuses private and reserved addresses and re-checks redirects; the link checker skips private hosts unless listed | keep `AllowedPrivateHosts` empty unless needed |

## Evidence to keep

| Category | Evidence an auditor can check |
|---|---|
| A01 | export of roles and assignments; request rules in use (`settings/requestrules.ini`) |
| A02 | `curl -sI` output showing headers and cookie flags; certificate expiry monitoring |
| A05 | `bash bin/shell/verifyfiles.sh` exit 0 after the last update; `DebugOutput=disabled` |
| A06 | update log with dates; `composer audit` output |
| A07 | password and 2FA settings; list of users without a second step |
| A08 | `exp:audit verify` output; file consistency report |
| A09 | alert recipients (`./console exp:audit alerts recipients`), SIEM forwarding configuration, weekly log review notes |

## Related pages

- [Security standards](../../../standards/security.md)
- [Security hardening](../../security-hardening.md)
- OWASP, [Top 10](https://owasp.org/Top10/)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
