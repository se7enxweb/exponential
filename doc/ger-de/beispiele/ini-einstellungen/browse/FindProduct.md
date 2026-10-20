---
title: browse.ini [FindProduct]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [FindProduct] in browse.ini: ReturnType, SelectionType, StartNode"
keywords: Einstellung, browse.ini, FindProduct, INI, setting, ReturnType, SelectionType, StartNode
type: generated
generated: exp:help:examples
---
# browse.ini [FindProduct]

Der Block `[FindProduct]` von `settings/browse.ini`, mit 3 Einstellung(en).

> Action used when a node should be selected in discount rule edit  

Bearbeiten Sie `settings/browse.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/browse.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/browse.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/browse.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## ReturnType

Standard: `ObjectID`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/browse.ini.append.php
[FindProduct]
ReturnType=ObjectID
*/ ?>
```

## SelectionType

Standard: `multiple`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/browse.ini.append.php
[FindProduct]
SelectionType=multiple
*/ ?>
```

## StartNode

Standard: `content`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/browse.ini.append.php
[FindProduct]
StartNode=content
*/ ?>
```

## Siehe auch

- [Alle Blöcke von browse.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/browse.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
