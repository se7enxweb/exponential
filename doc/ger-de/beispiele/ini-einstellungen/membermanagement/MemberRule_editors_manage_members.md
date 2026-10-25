---
title: membermanagement.ini [MemberRule_editors_manage_members]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [MemberRule_editors_manage_members] in membermanagement.ini: RoleName, ManagerGroups, ManagedGroups, PolicySet, EntryNodes"
keywords: Einstellung, membermanagement.ini, MemberRule_editors_manage_members, INI, setting, RoleName, ManagerGroups, ManagedGroups, PolicySet, EntryNodes
type: generated
generated: exp:help:examples
---
# membermanagement.ini [MemberRule_editors_manage_members]

Der Block `[MemberRule_editors_manage_members]` von `settings/membermanagement.ini`, mit 5 Einstellung(en).

> A rule: who manages whom, with which policy set, in which role.  
>   RoleName          the role the policies go into; created when missing, assigned to every manager group without a  
>                     limitation (each policy carries its own: the managed subtrees)  
>   ManagerGroups\[\]   the groups whose members manage  
>   ManagedGroups\[\]   the groups whose accounts (and sub-groups) they manage  
>   PolicySet         a \[MemberPolicySet\_<name>\] block  
>   EntryNodes\[\]      nodes the managers may read on their own (read limited to that node), so that the Users tab and  
>                     the user root open; "users" is content.ini \[NodeSettings\] UserRootNode  

Bearbeiten Sie `settings/membermanagement.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/membermanagement.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/membermanagement.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/membermanagement.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## RoleName

Standard: `Member management`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/membermanagement.ini.append.php
[MemberRule_editors_manage_members]
RoleName=Member management
*/ ?>
```

## ManagerGroups

Standard: eine Liste mit 1: `remote:3c160cca19fb135f83bd02d911f04db2`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/membermanagement.ini.append.php
[MemberRule_editors_manage_members]
# ein leeres "ManagerGroups[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
ManagerGroups[]
ManagerGroups[]=remote:3c160cca19fb135f83bd02d911f04db2
*/ ?>
```

## ManagedGroups

Standard: eine Liste mit 1: `remote:5f7f0bdb3381d6a461d8c29ff53d908f`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/membermanagement.ini.append.php
[MemberRule_editors_manage_members]
# ein leeres "ManagedGroups[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
ManagedGroups[]
ManagedGroups[]=remote:5f7f0bdb3381d6a461d8c29ff53d908f
*/ ?>
```

## PolicySet

Standard: `member_management`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/membermanagement.ini.append.php
[MemberRule_editors_manage_members]
PolicySet=member_management
*/ ?>
```

## EntryNodes

Standard: eine Liste mit 1: `users`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/membermanagement.ini.append.php
[MemberRule_editors_manage_members]
# ein leeres "EntryNodes[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
EntryNodes[]
EntryNodes[]=users
*/ ?>
```

## Siehe auch

- [Alle Blöcke von membermanagement.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/membermanagement.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
