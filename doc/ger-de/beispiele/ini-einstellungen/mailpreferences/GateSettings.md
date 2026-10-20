---
title: mailpreferences.ini [GateSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [GateSettings] in mailpreferences.ini: Gate, EssentialSenders, LogFile, MaxLogSize, SplitRecipients"
keywords: Einstellung, mailpreferences.ini, GateSettings, INI, setting, Gate, EssentialSenders, LogFile, MaxLogSize, SplitRecipients
type: generated
generated: exp:help:examples
---
# mailpreferences.ini [GateSettings]

Der Block `[GateSettings]` von `settings/mailpreferences.ini`, mit 5 Einstellung(en).

Bearbeiten Sie `settings/mailpreferences.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/mailpreferences.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/mailpreferences.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/mailpreferences.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Gate

> enabled: Kategorisierte optionale Mail wird geprüft und blockiert, wenn die Person  
> die Kategorie nicht eingeschaltet hat. disabled: Jede Mail wird wie bisher gesendet  
> (optionale Mail erhält trotzdem ihre Fußzeile und Header).  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/mailpreferences.ini.append.php
[GateSettings]
Gate=enabled
*/ ?>
```

## EssentialSenders

> Mail ohne Kategorie wird wie bisher immer gesendet. Sie wird als  
> "uncategorised" protokolliert, außer ihr Absender steht hier: eine E-Mail-Adresse aus dem  
> Header From, eine Datei des Codes, der die Mail an eZMailTransport übergibt  
> (Pfad relativ zur Installation), oder ein Klassenname in diesem Aufrufstapel.  
> Hier aufgeführte Absender werden als essential protokolliert.  

Standard: eine Liste mit 7: `kernel/private/classes/views/user/register.php`, `kernel/private/classes/views/user/forgotpassword.php`, `kernel/user/ezuseroperationcollection.php`, `kernel/classes/confirmorderhandlers/ezdefaultconfirmorderhandler.php`, `kernel/classes/audit/sinks/expauditmailsink.php`, `kernel/setup/steps/ezstep_registration.php`, `expMailPreferencesService`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/mailpreferences.ini.append.php
[GateSettings]
# ein leeres "EssentialSenders[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
EssentialSenders[]
EssentialSenders[]=kernel/private/classes/views/user/register.php
EssentialSenders[]=kernel/private/classes/views/user/forgotpassword.php
EssentialSenders[]=kernel/user/ezuseroperationcollection.php
EssentialSenders[]=kernel/classes/confirmorderhandlers/ezdefaultconfirmorderhandler.php
EssentialSenders[]=kernel/classes/audit/sinks/expauditmailsink.php
EssentialSenders[]=kernel/setup/steps/ezstep_registration.php
EssentialSenders[]=expMailPreferencesService
*/ ?>
```

## LogFile

> Das eigene Protokoll der Entscheidungen des Gates (blocked, sent, uncategorised), JSON-Zeilen,  
> ohne Adressen, relativ zum var-Verzeichnis. Bei MaxLogSize Bytes rotiert.  

Standard: `log/mailgate.jsonl`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/mailpreferences.ini.append.php
[GateSettings]
LogFile=log/mailgate.jsonl
*/ ?>
```

## MaxLogSize

Standard: `5242880`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/mailpreferences.ini.append.php
[GateSettings]
MaxLogSize=5242880
*/ ?>
```

## SplitRecipients

> Optionale Mail an mehrere Empfänger wird als eine Nachricht pro Empfänger gesendet,  
> damit jeder einen eigenen, funktionierenden Abmeldelink erhält.  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/mailpreferences.ini.append.php
[GateSettings]
SplitRecipients=enabled
*/ ?>
```

## Siehe auch

- [Alle Blöcke von mailpreferences.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/mailpreferences.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
