---
title: membermanagement.ini [MemberManagementSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [MemberManagementSettings] in membermanagement.ini: MemberManagement, Rules, ProtectedGroups"
keywords: Einstellung, membermanagement.ini, MemberManagementSettings, INI, setting, MemberManagement, Rules, ProtectedGroups
type: generated
generated: exp:help:examples
---
# membermanagement.ini [MemberManagementSettings]

Der Block `[MemberManagementSettings]` von `settings/membermanagement.ini`, mit 3 Einstellung(en).

Bearbeiten Sie `settings/membermanagement.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/membermanagement.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/membermanagement.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/membermanagement.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## MemberManagement

> enabled | disabled. Disabled: der Setup-Schritt und der Befehl ändern nichts; die Prüfung unten bleibt, wie dort gesetzt.  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/membermanagement.ini.append.php
[MemberManagementSettings]
MemberManagement=enabled
*/ ?>
```

## Rules

> Die verwendeten Regeln: je ein Block \[MemberRule\_<name>\].  

Standard: eine Liste mit 1: `editors_manage_members`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/membermanagement.ini.append.php
[MemberManagementSettings]
# ein leeres "Rules[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Rules[]
Rules[]=editors_manage_members
*/ ?>
```

## ProtectedGroups

> Gruppen, die keine Regel verwalten darf: eine Regel, deren verwaltete Gruppen eine davon sind, sie enthalten oder unterhalb davon liegen, wird abgelehnt (der Setup-  
> Schritt überspringt sie, der Befehl schlägt fehl). Standard: Administrator users und Editors.  

Standard: eine Liste mit 2: `remote:9b47a45624b023b1a76c73b74d704acf`, `remote:3c160cca19fb135f83bd02d911f04db2`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/membermanagement.ini.append.php
[MemberManagementSettings]
# ein leeres "ProtectedGroups[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
ProtectedGroups[]
ProtectedGroups[]=remote:9b47a45624b023b1a76c73b74d704acf
ProtectedGroups[]=remote:3c160cca19fb135f83bd02d911f04db2
*/ ?>
```

## Siehe auch

- [Alle Blöcke von membermanagement.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/membermanagement.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
