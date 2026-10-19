---
title: Secrets in settings/override
audience: government, admin, hosting, developer
category: public-sector
order: 320
summary: For administrators and hosting teams of public sites - which files in settings/override hold secrets (database password, audit keys, mail preference secret, Velocity token, 2FA encryption key), what happens if they are lost or leaked, and how to protect, back up and move them.
keywords: secrets, settings/override, database password, audit keys, signing key, pseudonym key, mailpreferences TokenSecret, Velocity token, 2FA SecretKey, file permissions, backup, version control, secret management, public sector
type: help
---
# Secrets in settings/override

This page is for administrators, hosting teams and developers. Several files in `settings/override/` hold secrets that
the installer writes or the code generates on first use. Losing them breaks things; leaking them opens the site
([13.3 Secrets in settings/override](../../../install/13-security-hardening.md#133-secrets-in-settingsoverride)).

## Contents

1. [The files](#the-files)
2. [Protecting them](#protecting-them)
3. [Moving an installation](#moving-an-installation)
4. [Related pages](#related-pages)

## The files

| File | Secret | If lost or leaked |
|---|---|---|
| `site.ini.append.php` | database user and password | database access |
| `audit.ini.append.php` | audit signing key and pseudonym key | old archives can no longer be verified; pseudonyms can be reversed |
| `mailpreferences.ini.append.php` | the site secret (`[SecretSettings] TokenSecret`) | unsubscribe and preference links stop working, suppression entries stop matching, every user is signed out once |
| `velocity.ini.append.php` | `[DashboardSettings] Token` when set | remote access to server figures and `/Q/phpinfo` |
| `sevenxauthentication2fa.ini.append.php` | `[TOTPSettings] SecretKey` | everyone must set up the authenticator app again |
| `var/log/initial-admin-password` (not in settings) | the generated first administrator password | the administrator account; delete it after the first sign-in |

## Protecting them

1. **Never commit `settings/override/`** to version control and never copy it to a public place.
2. Mode `0640` or stricter, owner the site's user (or root), group the site's group. Since 6.0.15 saves from the
   administration or `exp:ini` keep the file's mode and create new files `0640`. Check that nothing is world-writable:
   `find settings extension/*/settings -perm -o+w` must print nothing.
3. **Back them up** as secrets, encrypted, together with the database.
4. Give `setup/setup` (settings views) only to administrators; the administration and `exp:ini` mask secret values.
5. With `ManagedByDeployment`, the administration cannot write settings at all; generated keys are still written.
6. Keys from a secrets store: the audit can take its keys from settings you write
   (`[AuditKeySettings] GenerateKeys=disabled`) ([4.6 Keys](../../../bc/6.0/audit.md#46-keys)).

## Moving an installation

Copy `settings/override/` with every move to another machine. A copy started without
`mailpreferences.ini.append.php` generates a new site secret on its first mail or sign-in, and old links and
suppression entries no longer match; copying the file in afterwards does not help for what was sent in between.

## Related pages

- [13.3 Secrets in settings/override](../../../install/13-security-hardening.md#133-secrets-in-settingsoverride)
- [File permissions and ownership](file-permissions-and-ownership.md)
- [Backups and recovery](backups-and-recovery.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
