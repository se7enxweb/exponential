---
title: admininterface.ini [AdditionalTab_audit]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [AdditionalTab_audit] in admininterface.ini: Title, Description, NavigationPartName, HeaderTemplate, Template"
keywords: Einstellung, admininterface.ini, AdditionalTab_audit, INI, setting, Title, Description, NavigationPartName, HeaderTemplate, Template
type: generated
generated: exp:help:examples
---
# admininterface.ini [AdditionalTab_audit]

Der Block `[AdditionalTab_audit]` von `settings/admininterface.ini`, mit 5 Einstellung(en).

Bearbeiten Sie `settings/admininterface.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/admininterface.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/admininterface.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/admininterface.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Title

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/admininterface.ini.append.php
[AdditionalTab_audit]
Title=
*/ ?>
```

## Description

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/admininterface.ini.append.php
[AdditionalTab_audit]
Description=
*/ ?>
```

## NavigationPartName

Standard: `ezcontentnavigationpart`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/admininterface.ini.append.php
[AdditionalTab_audit]
NavigationPartName=ezcontentnavigationpart
*/ ?>
```

## HeaderTemplate

Standard: `audit/node_header.tpl`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/admininterface.ini.append.php
[AdditionalTab_audit]
HeaderTemplate=audit/node_header.tpl
*/ ?>
```

## Template

Standard: `audit/node.tpl`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/admininterface.ini.append.php
[AdditionalTab_audit]
Template=audit/node.tpl
*/ ?>
```

## Siehe auch

- [Alle Blöcke von admininterface.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/admininterface.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
