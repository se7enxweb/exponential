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

> enabled: categorised optional mail is checked and blocked when the person  
> has not switched the category on. disabled: every mail is sent as before  
> (optional mail still gets its footer and headers).  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/mailpreferences.ini.append.php
[GateSettings]
Gate=enabled
*/ ?>
```

## EssentialSenders

> Mail without a category is always sent, as before. It is logged as  
> "uncategorised" unless its sender is listed here: an e-mail address of the  
> From header, or a file of the code that hands the mail to eZMailTransport  
> (path relative to the installation), or a class name in that call stack.  
> Senders listed here are logged as essential.  

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

> The gate's own log of decisions (blocked, sent, uncategorised), JSON lines,  
> without addresses, relative to the var directory. Rotated at MaxLogSize bytes.  

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

> Optional mail to several recipients is sent as one message per recipient,  
> so each gets a working unsubscribe link of its own.  

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
