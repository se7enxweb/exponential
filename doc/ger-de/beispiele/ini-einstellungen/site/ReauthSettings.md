---
title: site.ini [ReauthSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [ReauthSettings] in site.ini: Reauth, Minutes, MaxFailures, Factors, Groups, CodeHandlers, TriggerHandlers, BulkCount"
keywords: Einstellung, site.ini, ReauthSettings, INI, setting, Reauth, Minutes, MaxFailures, Factors, Groups, CodeHandlers, TriggerHandlers, BulkCount
type: generated
generated: exp:help:examples
---
# site.ini [ReauthSettings]

Der Block `[ReauthSettings]` von `settings/site.ini`, mit 8 Einstellung(en).

Bearbeiten Sie `settings/site.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/site.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/site.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/site.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Reauth

> "Verify it's you": before a sensitive action the signed-in user confirms who they are  
> again (the password and a passkey, see Factors\[\]) and is not  
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

> Minuten, die eine Bestätigung gilt.  

Standard: `10`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[ReauthSettings]
Minutes=10
*/ ?>
```

## MaxFailures

> Falsche Antworten in Folge, nach denen der Benutzer abgemeldet wird.  

Standard: `5`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[ReauthSettings]
MaxFailures=5
*/ ?>
```

## Factors

> Wie der Benutzer bestätigt: Gefragt wird die erste Kombination, deren Wege der Benutzer alle hat, mit  
> den späteren mit ebenso vielen Wegen daneben (nie eine schwächere). Wege: password,  
> passkey, code (die Authenticator-App einer zweistufigen Anmeldung, siehe CodeHandlers\[\]).  
> Standard: das Passwort UND ein Passkey; ohne Passkey Passwort und Code;  
> ohne beides das Passwort allein; ein Konto ohne Passwort benutzt einen Passkey.  

Standard: eine Liste mit 4: `password+passkey`, `password+code`, `password`, `passkey`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[ReauthSettings]
# ein leeres "Factors[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Factors[]
Factors[]=password+passkey
Factors[]=password+code
Factors[]=password
Factors[]=passkey
*/ ?>
```

## Groups

> Die Gruppen, jeweils \[ReauthGroup\_<name>\] mit Enabled=true|false und Rules\[\].  

Standard: eine Liste mit 6: `account`, `users`, `system`, `export`, `delete`, `content`.

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
Groups[]=delete
Groups[]=content
*/ ?>
```

## CodeHandlers

> Klassen, die expReauthCodeHandler implementieren: sie prüfen den Code einer App für die zweistufige Anmeldung.  
> expReauthTotpCode liest die Codes von sevenx\_authentication\_2fa, wenn diese Erweiterung aktiv ist.  

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

## TriggerHandlers

> Klassen, die expReauthTriggerHandler umsetzen: Erweiterungen entscheiden selbst, dass eine Anfrage  
> fragt, und nennen eine der Gruppen oben (gefragt wird nur, solange diese Gruppe eingeschaltet ist).  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[ReauthSettings]
# ein leeres "TriggerHandlers[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
TriggerHandlers[]
*/ ?>
```

## BulkCount

> Die Zahl der Elemente, ab der ein Entfernen als „bulk“ gilt (das Regel-Token bulk).  

Standard: `2`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[ReauthSettings]
BulkCount=2
*/ ?>
```

## Siehe auch

- [Alle Blöcke von site.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/site.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
