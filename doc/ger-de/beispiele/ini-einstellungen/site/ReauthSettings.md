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

> "Bestätigen Sie, dass Sie es sind": vor einer sensiblen Aktion bestätigt der angemeldete Benutzer erneut, wer er ist,  
> mit dem Passwort, einem Passkey oder dem Code einer App für die zweistufige Anmeldung, und wird für  
> Minutes nicht erneut gefragt. Nur die Webseiten fragen (index.php und Velocity); REST, SOAP,  
> WebDAV, die expservices-Anmeldung und die Kommandozeile werden nie gefragt. Anonyme Besucher  
> werden nie gefragt. Die Gruppen und ihre Schalter werden unter setup/reauth bearbeitet.  
> Anleitung: doc/eng-us/guides/verify-its-you.md  
>
> enabled oder disabled (alle Gruppen).  

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

## Groups

> Die Gruppen, jeweils \[ReauthGroup\_<name>\] mit Enabled=true|false und Rules\[\].  

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

## Siehe auch

- [Alle Blöcke von site.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/site.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
