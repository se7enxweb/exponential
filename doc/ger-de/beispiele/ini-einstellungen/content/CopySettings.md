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

> Höchstzahl der Knoten, die mit der Funktion Teilbaum kopieren kopiert werden  

Standard: `30`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/content.ini.append.php
[CopySettings]
MaxNodesCopySubtree=30
*/ ?>
```

## VersionHandling

> Verwenden Sie user-defined, last-published oder all  

Standard: `all`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/content.ini.append.php
[CopySettings]
VersionHandling=all
*/ ?>
```

## CreatorHandling

> Verwenden Sie user-defined, keep-unchanged oder current, um  
> den aktuellen Benutzer als Ersteller kopierter Inhaltsobjekte zu setzen  

Standard: `current`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/content.ini.append.php
[CopySettings]
CreatorHandling=current
*/ ?>
```

## TimeHandling

> Verwenden Sie user-defined, keep-unchanged oder current  

Standard: `current`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/content.ini.append.php
[CopySettings]
TimeHandling=current
*/ ?>
```

## ShowCopySubtreeNotification

> Ob nach dem Kopieren eines Teilbaums eine Benachrichtigung angezeigt wird  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/content.ini.append.php
[CopySettings]
ShowCopySubtreeNotification=enabled
*/ ?>
```

## ClassRedirect

> Steuert, wohin nach dem Kopieren einer Klasse weitergeleitet wird  
> Verwenden Sie eines von:  
> grouplist - zur Liste der Klassengruppen weiterleiten  
> classlist - zur Gruppe der kopierten Klasse weiterleiten  
> classedit - in den Bearbeitungsmodus der kopierten Klasse weiterleiten  
> classview - in die Ansicht der kopierten Klasse weiterleiten  

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
