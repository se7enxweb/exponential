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

> Maximum number of nodes to remove using Remove Subtrees feature  

Standard: `100`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/content.ini.append.php
[RemoveSettings]
MaxNodesRemoveSubtree=100
*/ ?>
```

## DefaultRemoveAction

> delete or trash  

Standard: `trash`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/content.ini.append.php
[RemoveSettings]
DefaultRemoveAction=trash
*/ ?>
```

## ShowRemoveToTrashCheck

> true or false  

Standard: `true`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/content.ini.append.php
[RemoveSettings]
ShowRemoveToTrashCheck=true
*/ ?>
```

## HideRemoveConfirmation

> true or false  

Standard: `false`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/content.ini.append.php
[RemoveSettings]
HideRemoveConfirmation=false
*/ ?>
```

## ReverseRelationCheck

> What content/removeobject does when other objects point at an object that the removal takes away (an object  
> goes when every one of its locations lies inside the removed subtrees). References are the rows of  
> ezcontentobject\_link from the published version of a referring object (object relations, embeds and links in  
> rich text by object or node id, relation fields), plus what the providers of \[ReverseRelationSettings\] report  
> (Layouts blocks that name a node inside the subtrees). doc/eng-us/features/6.0/reverse-relation-check.md  
>  disabled - no check  
>  warn     - (default) the confirmation page lists the referring objects per selected item (link, class, kind  
>             of reference and field); it is also shown when every location of such an object is selected,  
>             which was removed at once before; with HideRemoveConfirmation=true there is no page  
>  block    - as warn, and the removal is refused while such references exist: the OK button is disabled, and  
>             the server refuses the removal on its own as well: a posted confirmation, HideRemoveConfirmation,  
>             a removal as a content job, the removal of the last locations on the Locations tab and a delete  
>             over WebDAV. Off by default.  

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
