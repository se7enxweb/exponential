---
title: mailpreferences.ini [Category_security]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [Category_security] in mailpreferences.ini: Name, Description, Essential, Frequencies, DoubleOptIn, HandlerClass"
keywords: Einstellung, mailpreferences.ini, Category_security, INI, setting, Name, Description, Essential, Frequencies, DoubleOptIn, HandlerClass
type: generated
generated: exp:help:examples
---
# mailpreferences.ini [Category_security]

Der Block `[Category_security]` von `settings/mailpreferences.ini`, mit 6 Einstellung(en).

Bearbeiten Sie `settings/mailpreferences.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/mailpreferences.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/mailpreferences.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/mailpreferences.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Name

Standard: `Account security`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/mailpreferences.ini.append.php
[Category_security]
Name=Account security
*/ ?>
```

## Description

Standard: `Password reset, account activation, confirmation of a new e-mail address and security alerts.`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/mailpreferences.ini.append.php
[Category_security]
Description=Password reset, account activation, confirmation of a new e-mail address and security alerts.
*/ ?>
```

## Essential

Standard: `true`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/mailpreferences.ini.append.php
[Category_security]
Essential=true
*/ ?>
```

## Frequencies

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/mailpreferences.ini.append.php
[Category_security]
# ein leeres "Frequencies[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Frequencies[]
*/ ?>
```

## DoubleOptIn

Standard: `false`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/mailpreferences.ini.append.php
[Category_security]
DoubleOptIn=false
*/ ?>
```

## HandlerClass

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/mailpreferences.ini.append.php
[Category_security]
HandlerClass=
*/ ?>
```

## Siehe auch

- [Alle Blöcke von mailpreferences.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/mailpreferences.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
