---
title: browse.ini [NewObjectAddNodeAssignment]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [NewObjectAddNodeAssignment] in browse.ini: StartNode, SelectionType, ReturnType, ActionName, StartNode_classgroup"
keywords: Einstellung, browse.ini, NewObjectAddNodeAssignment, INI, setting, StartNode, SelectionType, ReturnType, ActionName, StartNode_classgroup
type: generated
generated: exp:help:examples
---
# browse.ini [NewObjectAddNodeAssignment]

Der Block `[NewObjectAddNodeAssignment]` von `settings/browse.ini`, mit 5 Einstellung(en).

> Aktion, mit der neu angelegte Objekte eine Platzierung wählen  
> Diese Aktion wird verwendet, wenn ein neues Objekt angelegt werden soll, aber noch keine  
> Platzierung bekannt ist (zum Beispiel aus dem linken Menü).  

Bearbeiten Sie `settings/browse.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/browse.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/browse.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/browse.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## StartNode

Standard: `content`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/browse.ini.append.php
[NewObjectAddNodeAssignment]
StartNode=content
*/ ?>
```

## SelectionType

Standard: `single`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/browse.ini.append.php
[NewObjectAddNodeAssignment]
SelectionType=single
*/ ?>
```

## ReturnType

Standard: `NodeID`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/browse.ini.append.php
[NewObjectAddNodeAssignment]
ReturnType=NodeID
*/ ?>
```

## ActionName

Standard: `SelectParentNode`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/browse.ini.append.php
[NewObjectAddNodeAssignment]
ActionName=SelectParentNode
*/ ?>
```

## StartNode_classgroup

Standard: ein Hash mit 1: `2=users`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/browse.ini.append.php
[NewObjectAddNodeAssignment]
# ein leeres "StartNode_classgroup[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
StartNode_classgroup[]
StartNode_classgroup[2]=users
*/ ?>
```

## Siehe auch

- [Alle Blöcke von browse.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/browse.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
