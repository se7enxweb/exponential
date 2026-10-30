---
title: membermanagement.ini [MemberGuardSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [MemberGuardSettings] in membermanagement.ini: Guard, GuardedClasses, GuardedFunctions, SettingRequiresEdit"
keywords: Einstellung, membermanagement.ini, MemberGuardSettings, INI, setting, Guard, GuardedClasses, GuardedFunctions, SettingRequiresEdit
type: generated
generated: exp:help:examples
---
# membermanagement.ini [MemberGuardSettings]

Der Block `[MemberGuardSettings]` von `settings/membermanagement.ini`, mit 4 Einstellung(en).

Bearbeiten Sie `settings/membermanagement.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/membermanagement.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/membermanagement.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/membermanagement.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Guard

> Die Zugriffsprüfung: ein Konto (ein Objekt einer Klasse mit einem Benutzerkonto-Attribut oder einer Klasse in GuardedClasses),  
> das einen Ort unterhalb einer geschützten Gruppe hat, darf nur von jemandem bearbeitet, verschoben, entfernt, versteckt oder in seinen Orten  
> geändert werden, der das an diesem Ort darf, nicht nur an einem anderen Ort desselben Kontos (eine auf  
> Members beschränkte Richtlinie erreicht sonst einen Administrator, der auch einen Ort unterhalb von Members hat).  
> enabled | disabled  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/membermanagement.ini.append.php
[MemberGuardSettings]
Guard=enabled
*/ ?>
```

## GuardedClasses

> Klassen, die zusätzlich zu denen mit einem Benutzerkonto-Attribut geschützt werden (Bezeichner).  

Standard: eine Liste mit 1: `user_group`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/membermanagement.ini.append.php
[MemberGuardSettings]
# ein leeres "GuardedClasses[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
GuardedClasses[]
GuardedClasses[]=user_group
*/ ?>
```

## GuardedFunctions

> Die geschützten Funktionen.  

Standard: eine Liste mit 7: `edit`, `move`, `remove`, `manage_locations`, `hide`, `translate`, `versionremove`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/membermanagement.ini.append.php
[MemberGuardSettings]
# ein leeres "GuardedFunctions[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
GuardedFunctions[]
GuardedFunctions[]=edit
GuardedFunctions[]=move
GuardedFunctions[]=remove
GuardedFunctions[]=manage_locations
GuardedFunctions[]=hide
GuardedFunctions[]=translate
GuardedFunctions[]=versionremove
*/ ?>
```

## SettingRequiresEdit

> user/setting (aktivieren, deaktivieren, die Anzahl gleichzeitiger Anmeldungen) verlangt auch content/edit für das Konto, damit  
> user/preferences allein nicht jedes Konto der Installation erreicht.  enabled | disabled  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/membermanagement.ini.append.php
[MemberGuardSettings]
SettingRequiresEdit=enabled
*/ ?>
```

## Siehe auch

- [Alle Blöcke von membermanagement.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/membermanagement.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
