---
title: collaboration.ini [ViewSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [ViewSettings] in collaboration.ini: ViewList, GroupViewList"
keywords: Einstellung, collaboration.ini, ViewSettings, INI, setting, ViewList, GroupViewList
type: generated
generated: exp:help:examples
---
# collaboration.ini [ViewSettings]

Der Block `[ViewSettings]` von `settings/collaboration.ini`, mit 2 Einstellung(en).

Bearbeiten Sie `settings/collaboration.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/collaboration.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/collaboration.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/collaboration.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## ViewList

Standard: eine Liste mit 1: `summary`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/collaboration.ini.append.php
[ViewSettings]
# ein leeres "ViewList[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
ViewList[]
ViewList[]=summary
*/ ?>
```

## GroupViewList

Standard: eine Liste mit 1: `list`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/collaboration.ini.append.php
[ViewSettings]
# ein leeres "GroupViewList[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
GroupViewList[]
GroupViewList[]=list
*/ ?>
```

## Siehe auch

- [Alle Blöcke von collaboration.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/collaboration.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
