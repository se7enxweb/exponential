---
title: mailpreferences.ini [Category_collaboration]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [Category_collaboration] in mailpreferences.ini: Name, Description, Essential, DefaultOn, Frequencies, DoubleOptIn, HandlerClass"
keywords: Einstellung, mailpreferences.ini, Category_collaboration, INI, setting, Name, Description, Essential, DefaultOn, Frequencies, DoubleOptIn, HandlerClass
type: generated
generated: exp:help:examples
---
# mailpreferences.ini [Category_collaboration]

Der Block `[Category_collaboration]` von `settings/mailpreferences.ini`, mit 7 Einstellung(en).

Bearbeiten Sie `settings/mailpreferences.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/mailpreferences.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/mailpreferences.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/mailpreferences.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Name

Standard: `Collaboration and approvals`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/mailpreferences.ini.append.php
[Category_collaboration]
Name=Collaboration and approvals
*/ ?>
```

## Description

Standard: `Mail about content waiting for your approval and the messages of your collaboration items.`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/mailpreferences.ini.append.php
[Category_collaboration]
Description=Mail about content waiting for your approval and the messages of your collaboration items.
*/ ?>
```

## Essential

Standard: `false`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/mailpreferences.ini.append.php
[Category_collaboration]
Essential=false
*/ ?>
```

## DefaultOn

Standard: `false`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/mailpreferences.ini.append.php
[Category_collaboration]
DefaultOn=false
*/ ?>
```

## Frequencies

Standard: eine Liste mit 3: `immediate`, `daily`, `weekly`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/mailpreferences.ini.append.php
[Category_collaboration]
# ein leeres "Frequencies[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Frequencies[]
Frequencies[]=immediate
Frequencies[]=daily
Frequencies[]=weekly
*/ ?>
```

## DoubleOptIn

Standard: `false`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/mailpreferences.ini.append.php
[Category_collaboration]
DoubleOptIn=false
*/ ?>
```

## HandlerClass

Standard: `expNotificationMailCategoryHandler`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/mailpreferences.ini.append.php
[Category_collaboration]
HandlerClass=expNotificationMailCategoryHandler
*/ ?>
```

## Siehe auch

- [Alle Blöcke von mailpreferences.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/mailpreferences.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
