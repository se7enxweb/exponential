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

> Legt fest, welche Arten verknüpfter Objekte gewählt werden, um beim Veröffentlichen des aktuellen Objekts  
> den View-Cache zu leeren.  
> Mögliche Werte sind:  
> common                    - allgemeine Verknüpfungen auf Objektebene  
> embedded                  - eingebettete Objekte  
> linked                    - verlinkte Objekte  
> attribute                 - Verknüpfungen über Attribute der Datentypen 'object relation' oder  
>                             'object relations'  
> reverse\_common            - umgekehrte allgemeine Verknüpfungen (Objekte, die mit dem gerade  
>                             veröffentlichten verknüpft sind) auf Objektebene  
> reverse\_embedded          - Objekte, die das aktuelle Objekt eingebettet haben  
> reverse\_linked            - Objekte, die das aktuelle Objekt verlinkt haben  
> reverse\_attribute         - Objekte, die das aktuelle Objekt in ihren Attributen als verknüpft haben.  

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

> Steuert den intelligenten Handler des View-Caches; verwenden Sie enabled, disabled oder exclusive.  
> Ist der Handler deaktiviert, leert das System den View-Cache  
> aller veröffentlichten Knoten des aktuellen Objekts, seines Elternknotens (seiner Elternknoten), umgekehrt  
> verknüpfter Objekte und Objekte mit denselben Schlüsselwörtern (falls vorhanden).  
> Wenn aktiviert, versucht es zusätzlich, den in viewcache.ini gesetzten Regeln  
> für komplexere Beziehungen zwischen Objekten zu folgen, z. B. um eine Forums-  
> liste zu leeren, wenn ein neues Thema oder eine Antwort gepostet wird.  
> Bei exclusive werden die Einstellungen für bestimmte Klassen ausschließlich verwendet  
> (falls definiert), statt zusätzlich zu den Standardeinstellungen.  

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/viewcache.ini.append.php
[ViewCacheSettings]
SmartCacheClear=disabled
*/ ?>
```

## KeywordNodesCacheClearLimit

> Die Höchstzahl verlinkter Schlüsselwort-Knoten, die durchgegangen werden,  
> wenn nach Objekten gesucht wird, deren Cache geleert werden muss.  
> Setzen Sie eine Ganzzahl zum Aktivieren, 0, um das Leeren nach Schlüsselwörtern global abzuschalten,  
> oder disabled für gar keine Grenze (Standardwert)  

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
