---
title: admininterface.ini [AdditionalTab_roles]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [AdditionalTab_roles] in admininterface.ini: Title, Description, NavigationPartName, HeaderTemplate, Template"
keywords: Einstellung, admininterface.ini, AdditionalTab_roles, INI, setting, Title, Description, NavigationPartName, HeaderTemplate, Template
type: generated
generated: exp:help:examples
---
# admininterface.ini [AdditionalTab_roles]

Der Block `[AdditionalTab_roles]` von `settings/admininterface.ini`, mit 5 Einstellung(en).

> Additional tab configuration example  
> \[AdditionalTab\_customtab\]  
> Title=Example Tab  
> Description=Some example tab description  
>
> Translation context of Title and Description (the extension's catalogue holds the messages); without it  
> they are shown as written  
> TranslationContext=extension/myextension/tab  
>
> Navigation part where the tab will be available (ezcontentnavigationpart, ezusernavigationpart, ...)  
> NavigationPartName=ezcontentnavigationpart  
>
> Custom tab templates should be placed under design/admin/templates/tabs/  
> E.g design/admin/templates/tabs/custom\_tab.tpl  
>
> Tab header template, if empty then Title and Description are used by default header template  
> HeaderTemplate=  
> Tab content template  
> Template=custom\_tab.tpl  

Bearbeiten Sie `settings/admininterface.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/admininterface.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/admininterface.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/admininterface.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Title

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/admininterface.ini.append.php
[AdditionalTab_roles]
Title=
*/ ?>
```

## Description

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/admininterface.ini.append.php
[AdditionalTab_roles]
Description=
*/ ?>
```

## NavigationPartName

Standard: `ezusernavigationpart`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/admininterface.ini.append.php
[AdditionalTab_roles]
NavigationPartName=ezusernavigationpart
*/ ?>
```

## HeaderTemplate

Standard: `user/roles_header.tpl`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/admininterface.ini.append.php
[AdditionalTab_roles]
HeaderTemplate=user/roles_header.tpl
*/ ?>
```

## Template

Standard: `user/roles.tpl`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/admininterface.ini.append.php
[AdditionalTab_roles]
Template=user/roles.tpl
*/ ?>
```

## Siehe auch

- [Alle Blöcke von admininterface.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/admininterface.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
