---
title: Hardening the host
audience: government, hosting, admin
category: public-sector
order: 110
summary: For public-sector IT and security teams preparing a server for an Exponential site. The hardening layers the software expects from the host, the checks that prove them, and how to map them to a security baseline or an internal assessment.
keywords: hardening, security baseline, server hardening, file permissions, EZP_DIR_MODE_MAX, EZP_FILE_MODE_MAX, served files, secrets, debug output, security headers, HTTPS, HSTS, TLS, OWASP, assessment, NIS2
type: help
---
# Hardening the host

This page is for the infrastructure and security teams of a public body who prepare and assess the server that runs
an Exponential site. It answers which hardening the software expects from the host, how to check each layer, and how
to present the result in a security assessment. The reference is
[Chapter 13: Security hardening](../../../../install/13-security-hardening.md); the application side is
[Security hardening](../../../security-hardening.md) and [Security standards](../../../../standards/security.md).
Hosting companies have their own version: [Hardening for hosts](../../../hosting/security/hardening-for-hosts.md).

## Contents

1. [Secure by default, verified by you](#secure-by-default-verified-by-you)
2. [The layers and their checks](#the-layers-and-their-checks)
3. [File modes and owners](#file-modes-and-owners)
4. [Secrets on the server](#secrets-on-the-server)
5. [Mapping to your security baseline](#mapping-to-your-security-baseline)
6. [Checklist](#checklist)

## Secure by default, verified by you

Since September 2026 a default installation sends security headers, uses `HttpOnly` and `SameSite` session cookies and
answers 404 for dot files and PHP files below the document root ([Security and audit](../../../../guides/security-and-audit.md)).
Verify these instead of trusting them; the checks below take a few minutes and their output is good evidence for an
assessment file.

## The layers and their checks

| Layer | What to check | Command or source |
|---|---|---|
| Served files | `.env`, `.git/config` and extension PHP files answer 404 | the `curl` loop below; [13.2](../../../../install/13-security-hardening.md#132-what-the-web-server-must-never-hand-out) |
| Security headers | five headers on every page | `curl -sI`; [13.9](../../../../install/13-security-hardening.md#139-security-headers-and-https) |
| HTTPS | every name served over HTTPS; HSTS only when that is permanent | [TLS and HTTPS](../../../hosting/security/tls-and-https.md) |
| Debug output | off, or only for listed addresses with an expiry | [13.4](../../../../install/13-security-hardening.md#134-debug-output-and-error-display) |
| The administration | its own host name and network restriction | [Separating the administration from the public site](separating-admin-and-public-siteaccesses.md) |
| The setup wizard | closed: `CheckValidity=false` in `settings/override/site.ini.append.php` | [13.5](../../../../install/13-security-hardening.md#135-the-administration-siteaccess) |
| Velocity | workers not root, panel password set, dashboard not remote | [13.12](../../../../install/13-security-hardening.md#1312-velocity-specific-hardening) |
| Keeping up to date | security releases applied | [Security updates and disclosure](../upgrades-and-change/security-updates-and-disclosure.md) |

```bash
curl -sI https://www.example.gov/ | grep -i -E 'x-content-type|x-frame|referrer|permissions|content-security'
for p in .env .git/config extension/expservices/ezinfo.php; do
  curl -s -o /dev/null -w "$p %{http_code}\n" https://www.example.gov/$p
done
```

Expected: the five headers (`x-content-type-options`, `x-frame-options`, `content-security-policy`,
`referrer-policy`, `permissions-policy`), and `404` three times ([Security and audit](../../../../guides/security-and-audit.md)).

## File modes and owners

The site creates logs, caches and storage as open as `site.ini [FileSettings]` and the code ask, which is 0777 and
0666 by default. On a production server set limits in `config.php`, so nothing new is readable or writable by other
accounts:

```php
define( 'EZP_DIR_MODE_MAX', 0750 );
define( 'EZP_FILE_MODE_MAX', 0640 );
```

Then narrow what already exists once ([File modes](../../../../bc/6.0/file-modes.md)). Run the web server's PHP,
Velocity's workers, cronjobs and command line scripts as one user, or give them a common group with setgid
directories; two users writing the same files is the most common cause of odd failures
([Writable directories and permissions](../../../hosting/basics/writable-directories-and-permissions.md)). After any
command run as root, check the owner of new files in `var/`.

## Secrets on the server

`settings/override/` holds the database password, the audit trail's signing and pseudonym keys and the mail preference
secret ([13.3](../../../../install/13-security-hardening.md#133-secrets-in-settingsoverride)). Limit who can read it on
the server, keep it out of version control or encrypt it there, and treat backups that contain it as sensitive (see
[Keys and secrets in backups](../backups-and-recovery/keys-and-secrets-in-backups.md)). Velocity keeps private keys of
certificates in its configuration tree with mode `0600`.

## Mapping to your security baseline

Public bodies often assess applications against a national baseline, an ISO/IEC 27001 control set
([ISO/IEC 27001](https://www.iso.org/standard/27001)), the [NIST Cybersecurity Framework 2.0](https://www.nist.gov/cyberframework)
or, in the EU, measures taken under the [NIS2 Directive](https://eur-lex.europa.eu/eli/dir/2022/2555/oj). Exponential
does not claim certification against any of them. What helps:

- [Security standards](../../../../standards/security.md) maps the software to the
  [OWASP Top 10](https://owasp.org/Top10/) and [OWASP ASVS](https://owasp.org/www-project-application-security-verification-standard/),
  with honest "Partly complies" and "Unverified" statuses and the known gaps (for example account lockout off by
  default and a minimum password length of 10, below ASVS level 1's 12).
- The checks on this page produce evidence you can file with the assessment.
- The audit trail records settings writes, cache clears and deploys, which supports change and access controls
  ([Audit trail](../../../../features/6.0/audit-trail.md)).

## Checklist

- [ ] The served-files and header checks pass, with output filed.
- [ ] HTTPS everywhere; HSTS decided deliberately.
- [ ] Debug output off on production.
- [ ] `EZP_DIR_MODE_MAX` and `EZP_FILE_MODE_MAX` set; one user or one group writes `var/`.
- [ ] `settings/override/` readable only by the site's user and administrators.
- [ ] The setup wizard is closed and the administration restricted.
- [ ] The gaps listed in [Security standards](../../../../standards/security.md) are reviewed and decided.

## Related pages

- [Hosting](README.md) index of this folder
- [Security review checklist](../../../hosting/checklists/security-review.md)
- [Request-shield and request rules](../../../hosting/security/request-shield-and-request-rules.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
