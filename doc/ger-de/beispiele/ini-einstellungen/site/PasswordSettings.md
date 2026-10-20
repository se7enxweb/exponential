---
title: site.ini [PasswordSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [PasswordSettings] in site.ini: RequireLowercase, RequireUppercase, RequireDigit, RequireSymbol, MinCharacterClasses, ForbidLogin, ForbidCurrentPassword, EndOtherSessions ..."
keywords: Einstellung, site.ini, PasswordSettings, INI, setting, RequireLowercase, RequireUppercase, RequireDigit, RequireSymbol, MinCharacterClasses, ForbidLogin, ForbidCurrentPassword, EndOtherSessions, ChangeNotificationMail, StrengthMeter, GenerateButton
type: generated
generated: exp:help:examples
---
# site.ini [PasswordSettings]

Der Block `[PasswordSettings]` von `settings/site.ini`, mit 11 Einstellung(en).

Bearbeiten Sie `settings/site.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/site.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/site.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/site.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## RequireLowercase

> The rules a new password must meet (user/password, registration, the user account in  
> content/edit, bin/php/resetuserpassword.php), besides the length  
> (\[UserSettings\] MinPasswordLength), and what happens after a change.  
> Every rule is off by default: a site keeps the rules it had.  
> Guide: doc/eng-us/features/6.0/modern-password-change.md  
>
> At least one lowercase letter (any script, not only a-z)  

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[PasswordSettings]
RequireLowercase=disabled
*/ ?>
```

## RequireUppercase

> At least one uppercase letter  

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[PasswordSettings]
RequireUppercase=disabled
*/ ?>
```

## RequireDigit

> At least one digit  

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[PasswordSettings]
RequireDigit=disabled
*/ ?>
```

## RequireSymbol

> At least one character that is neither a letter nor a digit (a space counts)  

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[PasswordSettings]
RequireSymbol=disabled
*/ ?>
```

## MinCharacterClasses

> At least this many of the four kinds above (0 to 4; 0 = no such rule)  

Standard: `0`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[PasswordSettings]
MinCharacterClasses=0
*/ ?>
```

## ForbidLogin

> The new password must not contain the user's login name (logins of 3 or more characters)  

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[PasswordSettings]
ForbidLogin=disabled
*/ ?>
```

## ForbidCurrentPassword

> The new password must differ from the current one  

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[PasswordSettings]
ForbidCurrentPassword=disabled
*/ ?>
```

## EndOtherSessions

> After a password change: the user's other sessions are signed out on their next  
> request (a sign-in keeps a stamp of the password in the session, checked by  
> eZUser::currentUser()); this one stays signed in, with a new session id. The database  
> session handler also deletes them at once. Sessions from before the upgrade (no stamp)  
> and accounts without a password of their own (LDAP, SSO) are not checked.  
> disabled: no stamp, no check, other sessions stay.  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[PasswordSettings]
EndOtherSessions=enabled
*/ ?>
```

## ChangeNotificationMail

> After a change: mail "your password was changed" to the user (mail category  
> security, essential; template design:user/password\_changed\_mail.tpl). The mail never  
> contains the password.  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[PasswordSettings]
ChangeNotificationMail=enabled
*/ ?>
```

## StrengthMeter

> For the page's script: show the strength meter, offer "Generate a strong password"  
> (its length is \[UserSettings\] GeneratePasswordLength, at least 12)  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[PasswordSettings]
StrengthMeter=enabled
*/ ?>
```

## GenerateButton

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[PasswordSettings]
GenerateButton=enabled
*/ ?>
```

## Siehe auch

- [Alle Blöcke von site.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/site.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
