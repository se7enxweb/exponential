---
title: browse.ini [users]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [users] in browse.ini: StartNode, TopLevelNodes"
keywords: Einstellung, browse.ini, users, INI, setting, StartNode, TopLevelNodes
type: generated
generated: exp:help:examples
---
# browse.ini [users]

Der Block `[users]` von `settings/browse.ini`, mit 2 Einstellung(en).

> Beispiel einer Aktion; die Gruppe heißt genauso wie die Aktion  
> \[ExampleAction\]  
> # The node ID where the browsing should start  
> StartNode=content  
> # Type of selection, possible values are:  
> #   single - Allow for just one item to be selected  
> #   multiple - Allow for multiple items to be selected  
> SelectionType=single  
> # The type of the return values, possible values are:  
> #   NodeID - Returns the node ids of the selected items  
> #   ObjectID - Returns the object ids of the selected items  
> ReturnType=NodeID  
> # Overrides for start node, the value begins with StartNode  
> # and a \_ (underscore) and the override key name to match  
> # The key value is placed inside the \[\] (brackets)  
> StartNode\_classgroup\[2\]=users  
> #  
> # By specifying class identifier, only objects of specified class can be selected  

Bearbeiten Sie `settings/browse.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/browse.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/browse.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/browse.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## StartNode

Standard: `users`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/browse.ini.append.php
[users]
StartNode=users
*/ ?>
```

## TopLevelNodes

Standard: eine Liste mit 1: `users`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/browse.ini.append.php
[users]
# ein leeres "TopLevelNodes[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
TopLevelNodes[]
TopLevelNodes[]=users
*/ ?>
```

## Siehe auch

- [Alle Blöcke von browse.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/browse.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
