---
title: browse.ini [SwapNode]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [SwapNode] in browse.ini: StartNode, SelectionType, ReturnType, StartNode_classgroup"
keywords: Einstellung, browse.ini, SwapNode, INI, setting, StartNode, SelectionType, ReturnType, StartNode_classgroup
type: generated
generated: exp:help:examples
---
# browse.ini [SwapNode]

Der Block `[SwapNode]` von `settings/browse.ini`, mit 4 Einstellung(en).

> Aktion zum Tauschen eines Knotens  

Bearbeiten Sie `settings/browse.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/browse.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/browse.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/browse.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## StartNode

Standard: `content`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/browse.ini.append.php
[SwapNode]
StartNode=content
*/ ?>
```

## SelectionType

Standard: `single`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/browse.ini.append.php
[SwapNode]
SelectionType=single
*/ ?>
```

## ReturnType

Standard: `NodeID`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/browse.ini.append.php
[SwapNode]
ReturnType=NodeID
*/ ?>
```

## StartNode_classgroup

Standard: ein Hash mit 1: `2=users`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/browse.ini.append.php
[SwapNode]
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
