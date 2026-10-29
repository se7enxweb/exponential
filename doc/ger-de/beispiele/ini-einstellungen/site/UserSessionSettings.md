---
title: site.ini [UserSessionSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [UserSessionSettings] in site.ini: Tracking, TouchInterval, RecordIP, GeoIPDatabase, GeoIPLanguage, RetentionDays, NewDeviceMail, NewDeviceAdminMail ..."
keywords: Einstellung, site.ini, UserSessionSettings, INI, setting, Tracking, TouchInterval, RecordIP, GeoIPDatabase, GeoIPLanguage, RetentionDays, NewDeviceMail, NewDeviceAdminMail, AdminMailReceivers, FirstSignInMail, SecurityAlertMail, FailedSignInAlertThreshold
type: generated
generated: exp:help:examples
---
# site.ini [UserSessionSettings]

Der Block `[UserSessionSettings]` von `settings/site.ini`, mit 12 Einstellung(en).

Bearbeiten Sie `settings/site.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/site.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/site.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/site.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Tracking

> "Your devices": every sign-in is a row of the table expuser\_session (when the table is there), with when it  
> started and was last used, the address, the browser and system, the sign-in method and Remember me. The user sees  
> them at user/sessions and signs out one session or all others; an administrator with the policy user/sessions does  
> the same for any user (user/sessions/<user id>). A session signed out there ends on its next request, on every  
> session handler. Guide: doc/eng-us/features/6.0/user-sessions.md  
> disabled: no rows are written and no session is checked (the pages say so).  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[UserSessionSettings]
Tracking=enabled
*/ ?>
```

## TouchInterval

> The last use and address of a session are written at most this often (seconds), or when the address changes.  

Standard: `60`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[UserSessionSettings]
TouchInterval=60
*/ ?>
```

## RecordIP

> How the addresses of sessions are kept: full, truncated (the network only: 203.0.113.x, 2001:db8:85a3::/48),  
> disabled (none), or audit: as audit.ini says for the audit (\[UserAuditSettings\] RecordIP=disabled: none,  
> \[AuditPrivacySettings\] Field\[actor.ip\]=off: none, truncate or hash: the network, full: the address).  

Standard: `audit`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[UserSessionSettings]
RecordIP=audit
*/ ?>
```

## GeoIPDatabase

> The approximate place of a sign-in, from a GeoIP database on this server in the MaxMind DB format (.mmdb, e.g.  
> GeoLite2-City.mmdb), relative to the installation or absolute; empty: no places. Needs the PHP extension maxminddb  
> or the Composer package maxmind-db/reader. Nothing is ever asked of another server. With it, a sign-in from a  
> country the account has not signed in from within RetentionDays is a "new location" (event and mail).  

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[UserSessionSettings]
GeoIPDatabase=
*/ ?>
```

## GeoIPLanguage

> The language of the place names the database holds (en when a name is not in it).  

Standard: `en`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[UserSessionSettings]
GeoIPLanguage=en
*/ ?>
```

## RetentionDays

> Ended sessions (signed out, revoked, expired) are kept this many days, for the list of recent sign-ins and to know  
> a device seen before; the cronjob usersessions.php removes them afterwards. Sessions not used for \[Session\]  
> SessionTimeout are ended by the same cronjob.  

Standard: `90`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[UserSessionSettings]
RetentionDays=90
*/ ?>
```

## NewDeviceMail

> "New sign-in to your account" to the user, when a sign-in comes from a browser, system and device kind not seen  
> for the account within RetentionDays (category security). Each user can turn it off on the page.  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[UserSessionSettings]
NewDeviceMail=enabled
*/ ?>
```

## NewDeviceAdminMail

> The same as a note to the administrators: AdminMailReceivers\[\], else \[MailSettings\] AdminEmail (category admin).  

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[UserSessionSettings]
NewDeviceAdminMail=disabled
*/ ?>
```

## AdminMailReceivers

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[UserSessionSettings]
# ein leeres "AdminMailReceivers[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
AdminMailReceivers[]
*/ ?>
```

## FirstSignInMail

> Whether the very first sign-in an account has here counts as a new device (there is nothing to compare it with).  

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[UserSessionSettings]
FirstSignInMail=disabled
*/ ?>
```

## SecurityAlertMail

> "Security alert" to the owner of an account when two-step sign-in is turned on or off, a recovery code is used, sign-ins  
> fail FailedSignInAlertThreshold times in a row (0: never) or the account is locked (category security, design:user/  
> security\_alert\_mail.tpl, links to user/security). A user cannot turn these off; disabled turns them off for the site.  
> Sent from the user audit log (audit.ini \[UserAuditSettings\]): without it no alert is sent.  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[UserSessionSettings]
SecurityAlertMail=enabled
*/ ?>
```

## FailedSignInAlertThreshold

Standard: `5`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[UserSessionSettings]
FailedSignInAlertThreshold=5
*/ ?>
```

## Siehe auch

- [Alle Blöcke von site.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/site.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
