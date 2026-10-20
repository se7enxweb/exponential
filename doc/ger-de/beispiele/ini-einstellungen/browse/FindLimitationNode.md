---
title: browse.ini [FindLimitationNode]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [FindLimitationNode] in browse.ini: ReturnType, SelectionType, StartNode"
keywords: Einstellung, browse.ini, FindLimitationNode, INI, setting, ReturnType, SelectionType, StartNode
type: generated
generated: exp:help:examples
---
# browse.ini [FindLimitationNode]

Der Block `[FindLimitationNode]` von `settings/browse.ini`, mit 3 Einstellung(en).

> Aktion, wenn beim Bearbeiten einer Richtlinie ein Knoten ausgewählt werden soll  

Bearbeiten Sie `settings/browse.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/browse.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/browse.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/browse.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## ReturnType

Standard: `NodeID`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/browse.ini.append.php
[FindLimitationNode]
ReturnType=NodeID
*/ ?>
```

## SelectionType

Standard: `multiple`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/browse.ini.append.php
[FindLimitationNode]
SelectionType=multiple
*/ ?>
```

## StartNode

Standard: `content`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/browse.ini.append.php
[FindLimitationNode]
StartNode=content
*/ ?>
```

## Siehe auch

- [Alle Blöcke von browse.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/browse.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
