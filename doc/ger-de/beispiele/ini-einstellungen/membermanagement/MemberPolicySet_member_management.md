---
title: membermanagement.ini [MemberPolicySet_member_management]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [MemberPolicySet_member_management] in membermanagement.ini: Policies"
keywords: Einstellung, membermanagement.ini, MemberPolicySet_member_management, INI, setting, Policies
type: generated
generated: exp:help:examples
---
# membermanagement.ini [MemberPolicySet_member_management]

Der Block `[MemberPolicySet_member_management]` von `settings/membermanagement.ini`, mit 1 Einstellung(en).

> A policy set: Policies\[\] lines of the form module/function\[;Limitation=value,value;...\]. Class and ParentClass take  
> class identifiers. Every content policy whose function takes a Subtree limitation gets one: the rule's managed  
> groups. Policies outside the content module get no Subtree (user/preferences opens user/setting, which the guard and  
> SettingRequiresEdit above still limit to the accounts the manager may edit).  

Bearbeiten Sie `settings/membermanagement.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/membermanagement.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/membermanagement.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/membermanagement.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Policies

Standard: eine Liste mit 9: `content/read`, `content/create;Class=user,user_group;ParentClass=user_group`, `content/edit;Class=user,user_group`, `content/remove;Class=user,user_group`, `content/manage_locations;Class=user,user_group`, `content/versionread`, `content/versionremove`, `content/diff`, `user/preferences`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/membermanagement.ini.append.php
[MemberPolicySet_member_management]
# ein leeres "Policies[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Policies[]
Policies[]=content/read
Policies[]=content/create;Class=user,user_group;ParentClass=user_group
Policies[]=content/edit;Class=user,user_group
Policies[]=content/remove;Class=user,user_group
Policies[]=content/manage_locations;Class=user,user_group
Policies[]=content/versionread
Policies[]=content/versionremove
Policies[]=content/diff
Policies[]=user/preferences
*/ ?>
```

## Siehe auch

- [Alle Blöcke von membermanagement.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/membermanagement.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
