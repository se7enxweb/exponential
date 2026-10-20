---
title: debugbar.ini [Setting_mail_debug_sending]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [Setting_mail_debug_sending] in debugbar.ini: File, Block, Variable, Type, Label, Group, Help"
keywords: Einstellung, debugbar.ini, Setting_mail_debug_sending, INI, setting, File, Block, Variable, Type, Label, Group, Help
type: generated
generated: exp:help:examples
---
# debugbar.ini [Setting_mail_debug_sending]

Der Block `[Setting_mail_debug_sending]` von `settings/debugbar.ini`, mit 7 Einstellung(en).

> --------------------------------------------------------------------- mail  

Bearbeiten Sie `settings/debugbar.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/debugbar.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/debugbar.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/debugbar.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## File

Standard: `site.ini`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/debugbar.ini.append.php
[Setting_mail_debug_sending]
File=site.ini
*/ ?>
```

## Block

Standard: `MailSettings`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/debugbar.ini.append.php
[Setting_mail_debug_sending]
Block=MailSettings
*/ ?>
```

## Variable

Standard: `DebugSending`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/debugbar.ini.append.php
[Setting_mail_debug_sending]
Variable=DebugSending
*/ ?>
```

## Type

Standard: `bool`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/debugbar.ini.append.php
[Setting_mail_debug_sending]
Type=bool
*/ ?>
```

## Label

Standard: `Redirect all mail`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/debugbar.ini.append.php
[Setting_mail_debug_sending]
Label=Redirect all mail
*/ ?>
```

## Group

Standard: `mail`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/debugbar.ini.append.php
[Setting_mail_debug_sending]
Group=mail
*/ ?>
```

## Help

Standard: `Sends every mail to the debug receiver instead of its recipients.`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/debugbar.ini.append.php
[Setting_mail_debug_sending]
Help=Sends every mail to the debug receiver instead of its recipients.
*/ ?>
```

## Siehe auch

- [Alle Blöcke von debugbar.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/debugbar.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
