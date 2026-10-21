---
title: content.ini [NodeSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [NodeSettings] in content.ini: RootNode, UserRootNode, MediaRootNode, SetupRootNode, DesignRootNode"
keywords: Einstellung, content.ini, NodeSettings, INI, setting, RootNode, UserRootNode, MediaRootNode, SetupRootNode, DesignRootNode
type: generated
generated: exp:help:examples
---
# content.ini [NodeSettings]

Der Block `[NodeSettings]` von `settings/content.ini`, mit 5 Einstellung(en).

Bearbeiten Sie `settings/content.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/content.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/content.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/content.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## RootNode

> Die Knoten-ID des normalen Inhaltsbaums  

Standard: `2`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/content.ini.append.php
[NodeSettings]
RootNode=2
*/ ?>
```

## UserRootNode

> Die Knoten-ID des Benutzerbaums  

Standard: `5`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/content.ini.append.php
[NodeSettings]
UserRootNode=5
*/ ?>
```

## MediaRootNode

> Die Knoten-ID des Medienbaums  

Standard: `43`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/content.ini.append.php
[NodeSettings]
MediaRootNode=43
*/ ?>
```

## SetupRootNode

> Die Knoten-ID des Setup-Baums  

Standard: `48`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/content.ini.append.php
[NodeSettings]
SetupRootNode=48
*/ ?>
```

## DesignRootNode

> Die Knoten-ID des Design-Baums  

Standard: `58`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/content.ini.append.php
[NodeSettings]
DesignRootNode=58
*/ ?>
```

## Siehe auch

- [Alle Blöcke von content.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/content.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
