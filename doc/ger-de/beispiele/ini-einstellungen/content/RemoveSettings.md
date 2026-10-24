---
title: content.ini [RemoveSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [RemoveSettings] in content.ini: MaxNodesRemoveSubtree, DefaultRemoveAction, ShowRemoveToTrashCheck, HideRemoveConfirmation, ReverseRelationCheck"
keywords: Einstellung, content.ini, RemoveSettings, INI, setting, MaxNodesRemoveSubtree, DefaultRemoveAction, ShowRemoveToTrashCheck, HideRemoveConfirmation, ReverseRelationCheck
type: generated
generated: exp:help:examples
---
# content.ini [RemoveSettings]

Der Block `[RemoveSettings]` von `settings/content.ini`, mit 5 Einstellung(en).

Bearbeiten Sie `settings/content.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/content.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/content.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/content.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## MaxNodesRemoveSubtree

> Höchstzahl der Knoten, die mit der Funktion Teilbäume entfernen entfernt werden  

Standard: `100`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/content.ini.append.php
[RemoveSettings]
MaxNodesRemoveSubtree=100
*/ ?>
```

## DefaultRemoveAction

> delete oder trash  

Standard: `trash`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/content.ini.append.php
[RemoveSettings]
DefaultRemoveAction=trash
*/ ?>
```

## ShowRemoveToTrashCheck

> true oder false  

Standard: `true`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/content.ini.append.php
[RemoveSettings]
ShowRemoveToTrashCheck=true
*/ ?>
```

## HideRemoveConfirmation

> true oder false  

Standard: `false`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/content.ini.append.php
[RemoveSettings]
HideRemoveConfirmation=false
*/ ?>
```

## ReverseRelationCheck

> Was content/removeobject tut, wenn andere Objekte auf ein Objekt verweisen, das das Entfernen wegnimmt (ein Objekt  
> geht, wenn jede seiner Platzierungen in den entfernten Teilbäumen liegt). Verweise sind die Zeilen von  
> ezcontentobject\_link aus der veröffentlichten Version eines verweisenden Objekts (Objektverknüpfungen, Einbettungen und Links in  
> Rich Text per Objekt- oder Knoten-ID, Verknüpfungsfelder), dazu das, was die Provider von \[ReverseRelationSettings\] melden  
> (Layout-Blöcke, die einen Knoten innerhalb der Teilbäume nennen). doc/eng-us/features/6.0/reverse-relation-check.md  
>  disabled - keine Prüfung  
>  warn     - (Standard) die Bestätigungsseite listet die verweisenden Objekte pro ausgewähltem Eintrag auf (Link, Klasse, Art  
>             des Verweises und Feld); sie erscheint auch, wenn jede Platzierung eines solchen Objekts ausgewählt ist,  
>             das früher sofort entfernt wurde; mit HideRemoveConfirmation=true gibt es keine Seite  
>  block    - wie warn, und das Entfernen wird abgelehnt, solange solche Verweise bestehen: Die Schaltfläche OK ist deaktiviert, und  
>             der Server lehnt das Entfernen auch von sich aus ab: eine abgeschickte Bestätigung, HideRemoveConfirmation,  
>             ein Entfernen als Inhaltsauftrag und das Entfernen der letzten Platzierungen im Reiter Platzierungen. Standardmäßig  
>             aus.  

Standard: `warn`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/content.ini.append.php
[RemoveSettings]
ReverseRelationCheck=warn
*/ ?>
```

## Siehe auch

- [Alle Blöcke von content.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/content.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
