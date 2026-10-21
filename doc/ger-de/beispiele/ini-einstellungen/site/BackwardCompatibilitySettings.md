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

> z. B.: DefaultFeedItemClasses\[folder\]=article  
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
> Z. B.:  
> \[RSSSettings\_article\]  
> FeedObjectAttributeMap\[title\]=title  
> FeedObjectAttributeMap\[description\]=intro  
> FeedObjectAttributeMap\[category\]=tags  

Bearbeiten Sie `settings/site.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/site.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/site.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/site.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## UsingDesignAdmin34

> Aktiviert oder deaktiviert die Kompatibilität mit dem Administrations-Design von  
> eZPublish 3.4. Da manche neuen Funktionen im Design 3.4 nicht verfügbar sind,  
> können Sie diese Einstellung auf 'enabled' setzen, um  
> die 'neuen Funktionen' abzuschalten (z. B. sind 'Platzierungen' dann beim Bearbeiten  
> eines Objekts immer sichtbar), um unerwartete Situationen zu vermeiden.  

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[BackwardCompatibilitySettings]
UsingDesignAdmin34=disabled
*/ ?>
```

## AdvancedObjectRelationList

> Aktiviert oder deaktiviert die erweiterte Bearbeitungsoberfläche für den  
> Datentyp "ezobjectrelationlist" (alias "Objektverknüpfungen").  
> Die Standardeinstellung ist "disabled": Das  
> System bietet dann eine Oberfläche, die zum  
> Datentyp "ezobjectrelation" passt. Bei "enabled" wird die alte/erweiterte  
> Oberfläche angezeigt, und der Datentyp erlaubt  
> eingebettetes Bearbeiten von Objekten. Außerdem erlaubt er dann  
> das Anlegen neuer Objekte (ohne Platzierungen).  

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[BackwardCompatibilitySettings]
AdvancedObjectRelationList=disabled
*/ ?>
```

## ObjectRelationTyped

> Aktiviert oder deaktiviert die Kompatibilität mit typisierten Objektverknüpfungen  
> aus Exponential 3.9. Wenn deaktiviert, überspringt das System Verknüpfungen  
> vom Typ "XML linked", wenn es alle Verknüpfungen holt oder untersucht.  

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
