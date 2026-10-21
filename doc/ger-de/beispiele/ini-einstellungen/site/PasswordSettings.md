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

> Die Regeln, die ein neues Passwort erfüllen muss (user/password, Registrierung, das Benutzerkonto in  
> content/edit, bin/php/resetuserpassword.php), neben der Länge  
> (\[UserSettings\] MinPasswordLength), und was nach einer Änderung geschieht.  
> Jede Regel ist standardmäßig aus: Eine Website behält die Regeln, die sie hatte.  
> Anleitung: doc/eng-us/features/6.0/modern-password-change.md  
>
> Mindestens ein Kleinbuchstabe (jede Schrift, nicht nur a-z)  

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[PasswordSettings]
RequireLowercase=disabled
*/ ?>
```

## RequireUppercase

> Mindestens ein Großbuchstabe  

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[PasswordSettings]
RequireUppercase=disabled
*/ ?>
```

## RequireDigit

> Mindestens eine Ziffer  

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[PasswordSettings]
RequireDigit=disabled
*/ ?>
```

## RequireSymbol

> Mindestens ein Zeichen, das weder Buchstabe noch Ziffer ist (ein Leerzeichen zählt)  

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[PasswordSettings]
RequireSymbol=disabled
*/ ?>
```

## MinCharacterClasses

> Mindestens so viele der vier Arten oben (0 bis 4; 0 = keine solche Regel)  

Standard: `0`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[PasswordSettings]
MinCharacterClasses=0
*/ ?>
```

## ForbidLogin

> Das neue Passwort darf den Anmeldenamen des Benutzers nicht enthalten (Anmeldenamen ab 3 Zeichen)  

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[PasswordSettings]
ForbidLogin=disabled
*/ ?>
```

## ForbidCurrentPassword

> Das neue Passwort muss sich vom aktuellen unterscheiden  

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[PasswordSettings]
ForbidCurrentPassword=disabled
*/ ?>
```

## EndOtherSessions

> Nach einer Passwortänderung: Die anderen Sitzungen des Benutzers werden bei ihrer nächsten  
> Anfrage abgemeldet (eine Anmeldung behält einen Stempel des Passworts in der Sitzung, geprüft von  
> eZUser::currentUser()); diese bleibt angemeldet, mit einer neuen Sitzungs-ID. Der Session-Handler  
> der Datenbank löscht sie außerdem sofort. Sitzungen von vor dem Upgrade (kein Stempel)  
> und Konten ohne eigenes Passwort (LDAP, SSO) werden nicht geprüft.  
> disabled: kein Stempel, keine Prüfung, andere Sitzungen bleiben.  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[PasswordSettings]
EndOtherSessions=enabled
*/ ?>
```

## ChangeNotificationMail

> Nach einer Änderung: die Mail „Ihr Passwort wurde geändert“ an den Benutzer (Mailkategorie  
> security, essential; Template design:user/password\_changed\_mail.tpl). Die Mail enthält nie  
> das Passwort.  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[PasswordSettings]
ChangeNotificationMail=enabled
*/ ?>
```

## StrengthMeter

> Für das Skript der Seite: die Stärkeanzeige zeigen, „Starkes Passwort erzeugen“ anbieten  
> (seine Länge ist \[UserSettings\] GeneratePasswordLength, mindestens 12)  

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
