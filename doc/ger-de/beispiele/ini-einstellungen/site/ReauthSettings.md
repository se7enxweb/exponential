---
title: site.ini [ReauthSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [ReauthSettings] in site.ini: Reauth, Minutes, MaxFailures, Groups, CodeHandlers"
keywords: Einstellung, site.ini, ReauthSettings, INI, setting, Reauth, Minutes, MaxFailures, Groups, CodeHandlers
type: generated
generated: exp:help:examples
---
# site.ini [ReauthSettings]

Der Block `[ReauthSettings]` von `settings/site.ini`, mit 5 Einstellung(en).

Bearbeiten Sie `settings/site.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/site.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/site.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/site.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Reauth

> "Verify it's you": before a sensitive action the signed-in user confirms who they are  
> again, with the password, a passkey or the code of a two-step sign-in app, and is not  
> asked again for Minutes. Only the web pages ask (index.php and Velocity); REST, SOAP,  
> WebDAV, the expservices login and the command line are never asked. Anonymous visitors  
> are never asked. The groups and their switches are edited on setup/reauth.  
> Guide: doc/eng-us/guides/verify-its-you.md  
>
> enabled or disabled (all groups).  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[ReauthSettings]
Reauth=enabled
*/ ?>
```

## Minutes

> Minutes a confirmation counts.  

Standard: `10`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[ReauthSettings]
Minutes=10
*/ ?>
```

## MaxFailures

> Wrong answers in a row after which the user is signed out.  

Standard: `5`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[ReauthSettings]
MaxFailures=5
*/ ?>
```

## Groups

> The groups, each \[ReauthGroup\_<name>\] with Enabled=true|false and Rules\[\].  

Standard: eine Liste mit 5: `account`, `users`, `system`, `export`, `content`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[ReauthSettings]
# ein leeres "Groups[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Groups[]
Groups[]=account
Groups[]=users
Groups[]=system
Groups[]=export
Groups[]=content
*/ ?>
```

## CodeHandlers

> Classes implementing expReauthCodeHandler: they check the code of a two-step sign-in app.  
> expReauthTotpCode reads the codes of sevenx\_authentication\_2fa when that extension is active.  

Standard: eine Liste mit 1: `expReauthTotpCode`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[ReauthSettings]
# ein leeres "CodeHandlers[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
CodeHandlers[]
CodeHandlers[]=expReauthTotpCode
*/ ?>
```

## Siehe auch

- [Alle Blöcke von site.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/site.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
