---
title: mailpreferences.ini [Category_legal]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [Category_legal] in mailpreferences.ini: Name, Description, Essential, Frequencies, DoubleOptIn, HandlerClass"
keywords: Einstellung, mailpreferences.ini, Category_legal, INI, setting, Name, Description, Essential, Frequencies, DoubleOptIn, HandlerClass
type: generated
generated: exp:help:examples
---
# mailpreferences.ini [Category_legal]

Der Block `[Category_legal]` von `settings/mailpreferences.ini`, mit 6 Einstellung(en).

Bearbeiten Sie `settings/mailpreferences.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/mailpreferences.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/mailpreferences.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/mailpreferences.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Name

Standard: `Legal and service notices`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/mailpreferences.ini.append.php
[Category_legal]
Name=Legal and service notices
*/ ?>
```

## Description

Standard: `Changes to the terms and the privacy notice, and notices we are required to send.`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/mailpreferences.ini.append.php
[Category_legal]
Description=Changes to the terms and the privacy notice, and notices we are required to send.
*/ ?>
```

## Essential

Standard: `true`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/mailpreferences.ini.append.php
[Category_legal]
Essential=true
*/ ?>
```

## Frequencies

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/mailpreferences.ini.append.php
[Category_legal]
# ein leeres "Frequencies[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Frequencies[]
*/ ?>
```

## DoubleOptIn

Standard: `false`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/mailpreferences.ini.append.php
[Category_legal]
DoubleOptIn=false
*/ ?>
```

## HandlerClass

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/mailpreferences.ini.append.php
[Category_legal]
HandlerClass=
*/ ?>
```

## Siehe auch

- [Alle Blöcke von mailpreferences.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/mailpreferences.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
