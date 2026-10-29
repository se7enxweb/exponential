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

> „Identität bestätigen“: Vor einer heiklen Aktion bestätigt der angemeldete Benutzer noch einmal, wer er ist  
> (Passwort und Passkey, siehe Factors\[\]), und wird  
> Minutes lang nicht wieder gefragt. Nur die Webseiten fragen (index.php und Velocity); REST, SOAP,  
> WebDAV, die expservices-Anmeldung und die Kommandozeile werden nie gefragt. Anonyme Besucher  
> werden nie gefragt. Die Gruppen und ihre Schalter werden auf setup/reauth bearbeitet.  
> Anleitung: doc/ger-de/anleitungen/identitaet-bestaetigen.md  
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

> Falsche Antworten hintereinander, nach denen der Benutzer abgemeldet wird.  

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

> Die Gruppen, jede \[ReauthGroup\_<Name>\] mit Enabled=true|false und Rules\[\].  

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

> Klassen, die expReauthCodeHandler umsetzen: Sie prüfen den Code einer App der zweistufigen Anmeldung.  
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
