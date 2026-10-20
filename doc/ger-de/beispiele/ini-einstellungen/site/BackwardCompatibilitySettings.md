---
title: site.ini [BackwardCompatibilitySettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [BackwardCompatibilitySettings] in site.ini: UsingDesignAdmin34, AdvancedObjectRelationList, ObjectRelationTyped"
keywords: Einstellung, site.ini, BackwardCompatibilitySettings, INI, setting, UsingDesignAdmin34, AdvancedObjectRelationList, ObjectRelationTyped
type: generated
generated: exp:help:examples
---
# site.ini [BackwardCompatibilitySettings]

Der Block `[BackwardCompatibilitySettings]` von `settings/site.ini`, mit 3 Einstellung(en).

> eg: DefaultFeedItemClasses\[folder\]=article  
> # Default class attribute to rss/atom feed attribute mapping  
> \[RSSSettings\_<class\_identifier>\]  
> # Requried to be able to map title attribute  
> FeedObjectAttributeMap\[title\]=<class\_attribute\_identifier>  
> # Optional to map description field  
> FeedObjectAttributeMap\[description\]=<class\_attribute\_identifier>  
> # Optional to map category field  
> FeedObjectAttributeMap\[category\]=<class\_attribute\_identifier>  
> # Optional to fetch subtree of these items  
> Subnodes\[<source\_class\_identifier>\]=true  
> Eg:  
> \[RSSSettings\_article\]  
> FeedObjectAttributeMap\[title\]=title  
> FeedObjectAttributeMap\[description\]=intro  
> FeedObjectAttributeMap\[category\]=tags  

Bearbeiten Sie `settings/site.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/site.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/site.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/site.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## UsingDesignAdmin34

> Enables or disables compatibility with admin design from  
> eZPublish 3.4. Since some of the new features are unavailable from  
> 3.4 design you can set this setting to 'enabled' to turn  
> 'new features' off ( e.g. 'locations' will always be visible  
> during editing an object ) to prevent unexpected situations.  

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[BackwardCompatibilitySettings]
UsingDesignAdmin34=disabled
*/ ?>
```

## AdvancedObjectRelationList

> Enables or disables the advanced edit interface for the  
> "ezobjectrelationlist" (aka "Object relations") datatype.  
> The default setting is "disabled", which means that the  
> system will provide an interface that is consistent with the  
> "ezobjectrelation" datatype. When "enabled", the old/advanced  
> interface will be shown, and thus the datatype will allow  
> embedded editing of objects. In addition, it will also allow  
> the creation of new objects (without node assignments).  

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[BackwardCompatibilitySettings]
AdvancedObjectRelationList=disabled
*/ ?>
```

## ObjectRelationTyped

> Enables or disables compatibility with typed object relations  
> from Exponential 3.9. If disabled, the system will skip relations  
> of the "XML linked" type when fetching/inspecting all relations.  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[BackwardCompatibilitySettings]
ObjectRelationTyped=enabled
*/ ?>
```

## Siehe auch

- [Alle Blöcke von site.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/site.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
