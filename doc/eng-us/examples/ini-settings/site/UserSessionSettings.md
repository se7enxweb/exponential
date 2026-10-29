---
title: site.ini [UserSessionSettings]
audience: developer, admin
category: examples
summary: "The settings of [UserSessionSettings] in site.ini: Tracking, TouchInterval, RecordIP, GeoIPDatabase, GeoIPLanguage, RetentionDays, NewDeviceMail, NewDeviceAdminMail ..."
keywords: site.ini, UserSessionSettings, INI, setting, Tracking, TouchInterval, RecordIP, GeoIPDatabase, GeoIPLanguage, RetentionDays, NewDeviceMail, NewDeviceAdminMail, AdminMailReceivers, FirstSignInMail
type: generated
generated: exp:help:examples
---
# site.ini [UserSessionSettings]

The block `[UserSessionSettings]` of `settings/site.ini`, with 10 setting(s).

Never edit `settings/site.ini` itself: an update replaces it. Put the settings you change in an override, which wins over the shipped file:

- `settings/override/site.ini.append.php` for every siteaccess,
- `settings/siteaccess/<siteaccess>/site.ini.append.php` for one siteaccess,
- `extension/<extension>/settings/site.ini.append.php` in an extension (read when the extension is active).

## Tracking

> "Your devices": every sign-in is a row of the table expuser\_session (when the table is there), with when it  
> started and was last used, the address, the browser and system, the sign-in method and Remember me. The user sees  
> them at user/sessions and signs out one session or all others; an administrator with the policy user/sessions does  
> the same for any user (user/sessions/<user id>). A session signed out there ends on its next request, on every  
> session handler. Guide: doc/eng-us/features/6.0/user-sessions.md  
> disabled: no rows are written and no session is checked (the pages say so).  

Default: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[UserSessionSettings]
Tracking=enabled
*/ ?>
```

## TouchInterval

> The last use and address of a session are written at most this often (seconds), or when the address changes.  

Default: `60`.

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

Default: `audit`.

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

Default: empty.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[UserSessionSettings]
GeoIPDatabase=
*/ ?>
```

## GeoIPLanguage

> The language of the place names the database holds (en when a name is not in it).  

Default: `en`.

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

Default: `90`.

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

Default: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[UserSessionSettings]
NewDeviceMail=enabled
*/ ?>
```

## NewDeviceAdminMail

> The same as a note to the administrators: AdminMailReceivers\[\], else \[MailSettings\] AdminEmail (category admin).  

Default: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[UserSessionSettings]
NewDeviceAdminMail=disabled
*/ ?>
```

## AdminMailReceivers

Default: an empty list.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[UserSessionSettings]
# an empty "AdminMailReceivers[]" first replaces the shipped list; without it the items are added
AdminMailReceivers[]
*/ ?>
```

## FirstSignInMail

> Whether the very first sign-in an account has here counts as a new device (there is nothing to compare it with).  

Default: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[UserSessionSettings]
FirstSignInMail=disabled
*/ ?>
```

## Related

- [All blocks of site.ini](README.md)
- [All INI files](../README.md)
- [The settings page](../../../guides/settings-page.md)

---

Generated by `./console exp:help:examples` from `settings/site.ini`. Do not edit this page: change the source and generate again ([how](../../README.md#how-the-generated-pages-are-made)). Free documentation: [GFDL 1.3 or later](../../../help/free-documentation.md).
