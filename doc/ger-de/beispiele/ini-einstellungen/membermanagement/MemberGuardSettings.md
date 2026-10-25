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

> The access check: an account (an object of a class with a user account attribute, or of a class in GuardedClasses)  
> that has a location below a protected group may be edited, moved, removed, hidden or have its locations changed  
> only by someone who may do that at that location, not merely at another location of the same account (a policy  
> limited to Members otherwise reaches an administrator who also has a location below Members).  
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

> Classes guarded besides those with a user account attribute (identifiers).  

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

> The functions guarded.  

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

> user/setting (enable, disable, the number of concurrent logins) also asks for content/edit of the account, so that  
> user/preferences alone does not reach every account of the installation.  enabled | disabled  

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
