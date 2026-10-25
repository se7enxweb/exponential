---
title: site.ini [UserSessionSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [UserSessionSettings] in site.ini: Tracking, TouchInterval, RetentionDays, NewDeviceMail, NewDeviceAdminMail, AdminMailReceivers, FirstSignInMail"
keywords: Einstellung, site.ini, UserSessionSettings, INI, setting, Tracking, TouchInterval, RetentionDays, NewDeviceMail, NewDeviceAdminMail, AdminMailReceivers, FirstSignInMail
type: generated
generated: exp:help:examples
---
# site.ini [UserSessionSettings]

Der Block `[UserSessionSettings]` von `settings/site.ini`, mit 7 Einstellung(en).

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

Standard: `300`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[UserSessionSettings]
TouchInterval=300
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

## Siehe auch

- [Alle Blöcke von site.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/site.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
