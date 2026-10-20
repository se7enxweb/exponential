---
title: content.ini [CopySettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [CopySettings] in content.ini: MaxNodesCopySubtree, VersionHandling, CreatorHandling, TimeHandling, ShowCopySubtreeNotification, ClassRedirect"
keywords: Einstellung, content.ini, CopySettings, INI, setting, MaxNodesCopySubtree, VersionHandling, CreatorHandling, TimeHandling, ShowCopySubtreeNotification, ClassRedirect
type: generated
generated: exp:help:examples
---
# content.ini [CopySettings]

Der Block `[CopySettings]` von `settings/content.ini`, mit 6 Einstellung(en).

Bearbeiten Sie `settings/content.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/content.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/content.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/content.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## MaxNodesCopySubtree

> Maximum number of nodes to copy using Copy Subtree feature  

Standard: `30`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/content.ini.append.php
[CopySettings]
MaxNodesCopySubtree=30
*/ ?>
```

## VersionHandling

> Use either user-defined, last-published or all  

Standard: `all`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/content.ini.append.php
[CopySettings]
VersionHandling=all
*/ ?>
```

## CreatorHandling

> Use either user-defined, keep-unchanged or current to  
> set current user as creator of copied contentobjects  

Standard: `current`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/content.ini.append.php
[CopySettings]
CreatorHandling=current
*/ ?>
```

## TimeHandling

> Use either user-defined, keep-unchanged or current  

Standard: `current`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/content.ini.append.php
[CopySettings]
TimeHandling=current
*/ ?>
```

## ShowCopySubtreeNotification

> Show or not copy subtree notification after copying subtree  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/content.ini.append.php
[CopySettings]
ShowCopySubtreeNotification=enabled
*/ ?>
```

## ClassRedirect

> Controls where it will redirect after copying a class  
> Use one of:  
> grouplist - Redirect to the main classgroup list  
> classlist - Redirect to group of the copied class  
> classedit - Redirect to edit mode of copied class  
> classview - Redirect to view mode of copied class  

Standard: `classlist`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/content.ini.append.php
[CopySettings]
ClassRedirect=classlist
*/ ?>
```

## Siehe auch

- [Alle Blöcke von content.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/content.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
