---
title: content.ini [HideSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [HideSettings] in content.ini: RootNodeList, HideDateAttributeList, ReverseRelationCheck"
keywords: Einstellung, content.ini, HideSettings, INI, setting, RootNodeList, HideDateAttributeList, ReverseRelationCheck
type: generated
generated: exp:help:examples
---
# content.ini [HideSettings]

Der Block `[HideSettings]` von `settings/content.ini`, mit 3 Einstellung(en).

Bearbeiten Sie `settings/content.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/content.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/content.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/content.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## RootNodeList

> Example:  
>
> RootNodeList\[\]  
> RootNodeList\[\]=2  
> HideDateAttributeList\[\]  
> HideDateAttributeList\[article\]=hide\_date  
>
> List of root nodes where nodes can be hidden.  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/content.ini.append.php
[HideSettings]
# ein leeres "RootNodeList[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
RootNodeList[]
*/ ?>
```

## HideDateAttributeList

> Class attribute map, describing which date/time fields to use.  
> The systems uses the class identifier to determine which classes to hide in the cronjob.  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/content.ini.append.php
[HideSettings]
# ein leeres "HideDateAttributeList[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
HideDateAttributeList[]
*/ ?>
```

## ReverseRelationCheck

> What content/hide and "Hide selected" of the sub items list do before hiding a node that other objects  
> point at (what the hide takes away entirely, counted as for \[RemoveSettings\] ReverseRelationCheck), or whose  
> object has further locations. The hidecron cronjob is not affected. doc/eng-us/features/6.0/reverse-relation-check.md  
>  disabled - no check: the hide is done at once, as before  
>  warn     - (default) a confirmation page lists the references and the further locations first; a hide  
>             without either is done as before  

Standard: `warn`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/content.ini.append.php
[HideSettings]
ReverseRelationCheck=warn
*/ ?>
```

## Siehe auch

- [Alle Blöcke von content.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/content.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
