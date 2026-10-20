---
title: viewcache.ini [ViewCacheSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [ViewCacheSettings] in viewcache.ini: ClearRelationTypes, SmartCacheClear, KeywordNodesCacheClearLimit"
keywords: Einstellung, viewcache.ini, ViewCacheSettings, INI, setting, ClearRelationTypes, SmartCacheClear, KeywordNodesCacheClearLimit
type: generated
generated: exp:help:examples
---
# viewcache.ini [ViewCacheSettings]

Der Block `[ViewCacheSettings]` von `settings/viewcache.ini`, mit 3 Einstellung(en).

Bearbeiten Sie `settings/viewcache.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/viewcache.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/viewcache.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/viewcache.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## ClearRelationTypes

> Determines which types of related objects will be choosen to clear view cache  
> when publishing current object.  
> Possible values are:  
> common                    - common relations made on object level  
> embedded                  - embedded objects  
> linked                    - linked objects  
> attribute                 - relations made with attributes of 'object relation' or  
>                             'object relations' datatypes  
> reverse\_common            - reverse common relations (objects that relate the one currently  
>                             being published) on object level  
> reverse\_embedded          - objects that have current object as embedded  
> reverse\_linked            - objects that have linked current object  
> reverse\_attribute         - objects that have current object as related in their attributes.  

Standard: eine Liste mit 3: `common`, `reverse_common`, `reverse_embedded`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/viewcache.ini.append.php
[ViewCacheSettings]
# ein leeres "ClearRelationTypes[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
ClearRelationTypes[]
ClearRelationTypes[]=common
ClearRelationTypes[]=reverse_common
ClearRelationTypes[]=reverse_embedded
*/ ?>
```

## SmartCacheClear

> Controls the smart viewcache handler, use enabled, disabled or exclusive.  
> When the handler is disabled the system will clear the viewcache  
> for all published nodes of the current object, its parent(s), reverse  
> related objects and objects with same keywords (if any).  
> When enabled it will in addition try to follow the rules set in viewcache.ini  
> for more advanced relations between objects. e.g. to clear a forum  
> listing when a new topic or reply is posted.  
> When exclusive, settings for specific classes are used exclusively  
> (if defined), instead of in addition to the default settings.  

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/viewcache.ini.append.php
[ViewCacheSettings]
SmartCacheClear=disabled
*/ ?>
```

## KeywordNodesCacheClearLimit

> The maximum number of linked keyword nodes to go trough  
> when looking for objects that needs to have its cache cleared.  
> Set to integer to activate, 0 to disable keyword clearing globally  
> or disabled to not have any limit at all (default value)  

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/viewcache.ini.append.php
[ViewCacheSettings]
KeywordNodesCacheClearLimit=disabled
*/ ?>
```

## Siehe auch

- [Alle Blöcke von viewcache.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/viewcache.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
