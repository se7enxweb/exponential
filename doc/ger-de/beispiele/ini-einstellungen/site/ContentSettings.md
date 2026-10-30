---
title: site.ini [ContentSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [ContentSettings] in site.ini: SurplusNode, CacheDir, ViewCaching, CachedViewModes, ViewCacheTweaks, CachedViewPreferences, ComplexDisplayViewModes, CacheThreshold ..."
keywords: Einstellung, site.ini, ContentSettings, INI, setting, SurplusNode, CacheDir, ViewCaching, CachedViewModes, ViewCacheTweaks, CachedViewPreferences, ComplexDisplayViewModes, CacheThreshold, EditDirtyObjectAction, PreViewCache, PreCacheSiteaccessArray, PreviewCacheUsers, StaticCache, StaticCacheHandler, CacheGenerationTimeout, ContentClassEditHandler, ContentObjectNameLimit, ContentObjectNameLimitSequence, DatatypeBlackListForExternal, RedirectAfterPublish
type: generated
generated: exp:help:examples
---
# site.ini [ContentSettings]

Der Block `[ContentSettings]` von `settings/site.ini`, mit 20 Einstellung(en).

Bearbeiten Sie `settings/site.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/site.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/site.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/site.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## SurplusNode

> Knoten, unter dem alle neuen verknüpften Objekte standardmäßig abgelegt werden  
> VERALTET: Erweiterte Einstellungen stehen in content.ini  

Standard: `3`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[ContentSettings]
SurplusNode=3
*/ ?>
```

## CacheDir

> Wo die Dateien des View-Caches abgelegt werden; ein Unterverzeichnis des globalen Cache-Verzeichnisses.  

Standard: `content`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[ContentSettings]
CacheDir=content
*/ ?>
```

## ViewCaching

> Ob der View-Cache verwendet wird  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[ContentSettings]
ViewCaching=enabled
*/ ?>
```

## CachedViewModes

> Eine Liste der Ansichtsmodi, die gecacht werden  

Standard: `full;sitemap;pdf`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[ContentSettings]
CachedViewModes=full;sitemap;pdf
*/ ?>
```

## ViewCacheTweaks

> Anpassungen des View-Caches für die Cache-Strategie bestimmter Knoten-IDs  
> ViewCacheTweaks\[<node\_id>\]=<setting>\[;<setting2>\]  
> derzeit unterstützte Einstellungen:  
> disabled: wie cache\_ttl=0 im Template, nur etwas effizienter, weil es vorab bekannt ist  
> pr\_user: Seite pro Benutzer cachen, effizienter, als den View-Cache abzuschalten und Cache-Blöcke zu verwenden  
> protocol: baut je nach Protokoll der Anfrage (http oder https) einen anderen Cache  
> ignore\_discountlist die Rabattliste des Shops des Benutzers nicht in den Cache-Hash aufnehmen  
> ignore\_userroles die Rollen des Benutzers nicht in den Cache-Hash aufnehmen  
> ignore\_userlimitedlist die Liste eingeschränkter Richtlinienzuweisungen des Benutzers nicht in den Cache-Hash aufnehmen  
> ignore\_viewparameters die View-Parameter nicht in den Cache-Hash aufnehmen  
> ignore\_userpreferences die Benutzereinstellungen nicht in den Cache-Hash aufnehmen  
> ignore\_siteaccess\_name den Namen des Siteaccess nicht in den Cache-Pfad aufnehmen, stattdessen den Standard-Siteaccess verwenden  
> ignore\_siteaccess\_type den Typ des Siteaccess nicht in den Cache-Hash aufnehmen  
> Hinweis: Sie können als <node\_id> auch 'global' setzen, um Anpassungen global für alle zu setzen, außer 'disabled', wofür es andere Einstellungen gibt  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[ContentSettings]
# ein leeres "ViewCacheTweaks[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
ViewCacheTweaks[]
*/ ?>
```

## CachedViewPreferences

> Eine durch Semikolons getrennte Liste der Benutzereinstellungen (eventuell mit dem Standardwert nach dem '='),  
> von denen der durch einen Array-Schlüssel bestimmte Ansichtsmodus abhängt.  
> Bsp.: CachedViewPreferences\[full\]=show\_more\_info=0;show\_right\_menu  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[ContentSettings]
# ein leeres "CachedViewPreferences[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
CachedViewPreferences[]
*/ ?>
```

## ComplexDisplayViewModes

> Eine Liste der Ansichtsmodi, die mehr als nur ihre unmittelbaren Unterknoten anzeigen  
> Diese Ansichtsmodi sind zu komplex, um Datei für Datei aufgeräumt zu werden, und müssen  
> global ablaufen.  

Standard: `sitemap`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[ContentSettings]
ComplexDisplayViewModes=sitemap
*/ ?>
```

## CacheThreshold

> Die Schwelle für das Aufräumen von Dateien; wird sie überschritten, wird stattdessen ein globaler Ablauf verwendet  
> Der Wert ist die Zahl der betroffenen Knoten  

Standard: `250`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[ContentSettings]
CacheThreshold=250
*/ ?>
```

## EditDirtyObjectAction

> Was beim Bearbeiten eines Objekts geschieht, das Entwurfsversionen hat, die neuer als die aktuelle Version sind.  
> Verwenden Sie 'showversions', um die zu bearbeitende Version zu wählen, oder 'usecurrent', um immer die aktuelle Version zu bearbeiten.  

Standard: `showversions`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[ContentSettings]
EditDirtyObjectAction=showversions
*/ ?>
```

## PreViewCache

> Auf 'enabled' setzen, damit Exponential die Dateien des View-Caches beim Veröffentlichen vorab erzeugt  

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[ContentSettings]
PreViewCache=disabled
*/ ?>
```

## PreCacheSiteaccessArray

> Liste der Siteaccesses, für die beim Veröffentlichen der Vorschau-Cache erzeugt wird.  

Standard: eine Liste mit 2: `admin`, `base`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[ContentSettings]
# ein leeres "PreCacheSiteaccessArray[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
PreCacheSiteaccessArray[]
PreCacheSiteaccessArray[]=admin
PreCacheSiteaccessArray[]=base
*/ ?>
```

## PreviewCacheUsers

> Eine Liste der Benutzer, für die der Inhalts-Cache vorab erzeugt wird  
> Die Liste sollte die Benutzer-ID oder einen besonderen Bezeichner enthalten, mögliche Bezeichner sind:  
> - 'anonymous' für den anonymen Benutzer.  
> - 'current' für den aktuell angemeldeten Benutzer  
> Standard ist, für anonyme Benutzer vorab zu erzeugen  

Standard: eine Liste mit 1: `anonymous`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[ContentSettings]
# ein leeres "PreviewCacheUsers[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
PreviewCacheUsers[]
PreviewCacheUsers[]=anonymous
*/ ?>
```

## StaticCache

> Auf 'enabled' setzen, damit Exponential alle veröffentlichten Objekte als statisches  
> HTML speichert. Das erfordert außerdem Einstellungen in staticcache.ini und besondere URL-Rewrite-  
> Regeln.  

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[ContentSettings]
StaticCache=disabled
*/ ?>
```

## StaticCacheHandler

> Setzt einen Cache-Handler  

Standard: `eZStaticCache`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[ContentSettings]
StaticCacheHandler=eZStaticCache
*/ ?>
```

## CacheGenerationTimeout

> Höchste Zeit für die Erzeugung eines Caches. Bleibt eine Datei länger als  
> diesen Wert in Sekunden im Erzeugungsmodus, gilt sie als abgelaufen (der Prozess ist zum Beispiel gestorben),  
> und der anfragende Prozess übernimmt die Erzeugung  

Standard: `60`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[ContentSettings]
CacheGenerationTimeout=60
*/ ?>
```

## ContentClassEditHandler

> Handler, der Änderungen an einer Inhaltsklasse speichert, wenn Objekte existieren  
> und aktualisiert werden müssen. Das kann eine riesige Aufgabe sein, die zu Zeitüberschreitungen führt;  
> erwägen Sie den 'eZContentClassEditDeferredHandler' aus der  
> Erweiterung eZ Script Monitor.  

Standard: `eZContentClassEditHandler`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[ContentSettings]
ContentClassEditHandler=eZContentClassEditHandler
*/ ?>
```

## ContentObjectNameLimit

> Längengrenze für contentObjectName, muss zwischen 1 und 255 liegen.  

Standard: `150`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[ContentSettings]
ContentObjectNameLimit=150
*/ ?>
```

## ContentObjectNameLimitSequence

> Endsequenz für den Objektnamen, wenn die Längengrenze erreicht ist.  

Standard: `...`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[ContentSettings]
ContentObjectNameLimitSequence=...
*/ ?>
```

## DatatypeBlackListForExternal

> Liste der Datentypen, die ihren Inhalt nicht über externe Aufrufe (Ajax/REST) preisgeben sollen  

Standard: eine Liste mit 1: `ezuser`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[ContentSettings]
# ein leeres "DatatypeBlackListForExternal[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
DatatypeBlackListForExternal[]
DatatypeBlackListForExternal[]=ezuser
*/ ?>
```

## RedirectAfterPublish

> Mit dieser Einstellung ändern Sie die Weiterleitung nach dem Veröffentlichen eines Knotens.  
> Sie kann folgende Werte enthalten:  
> - parentNode - zum Elternknoten des Hauptknotens des bearbeiteten Objekts weiterleiten (Standardwert)  
> - node       - zum Hauptknoten des bearbeiteten Objekts weiterleiten  
> - rootNode   - zum Wurzelknoten des Siteaccess weiterleiten (d. h. content.ini/NodeSettings/rootNode )  
> - uri        - auf eine bestimmte URI weiterleiten (siehe die Einstellung RedirectAfterPublishUri unten)  

Standard: `parentNode`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[ContentSettings]
RedirectAfterPublish=parentNode
*/ ?>
```

## Siehe auch

- [Alle Blöcke von site.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/site.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
