---
title: content.ini [ClassAttributeSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [ClassAttributeSettings] in content.ini: CategoryList, DefaultCategory"
keywords: Einstellung, content.ini, ClassAttributeSettings, INI, setting, CategoryList, DefaultCategory
type: generated
generated: exp:help:examples
---
# content.ini [ClassAttributeSettings]

Der Block `[ClassAttributeSettings]` von `settings/content.ini`, mit 2 Einstellung(en).

Bearbeiten Sie `settings/content.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/content.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/content.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/content.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## CategoryList

> List of attribute categories, by default used for grouping  
> attributes in edit interface.  
> Key is identifier and value is used in UI as name  
> Note: Limit on identifier is 25 charters!  

Standard: ein Hash mit 2: `content=Content`, `meta=Meta`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/content.ini.append.php
[ClassAttributeSettings]
# ein leeres "CategoryList[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
CategoryList[]
CategoryList[content]=Content
CategoryList[meta]=Meta
*/ ?>
```

## DefaultCategory

> Default Category for attributes without or with invalid category  
> Note: Must be defined in CategoryList!  

Standard: `content`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/content.ini.append.php
[ClassAttributeSettings]
DefaultCategory=content
*/ ?>
```

## Siehe auch

- [Alle Blöcke von content.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/content.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
