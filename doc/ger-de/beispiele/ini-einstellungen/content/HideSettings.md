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

> Beispiel:  
>
> RootNodeList\[\]  
> RootNodeList\[\]=2  
> HideDateAttributeList\[\]  
> HideDateAttributeList\[article\]=hide\_date  
>
> Liste der Wurzelknoten, unter denen Knoten verborgen werden können.  

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

> Zuordnung von Klassenattributen, die festlegt, welche Datums-/Zeitfelder verwendet werden.  
> Das System bestimmt über den Klassenbezeichner, welche Klassen der Cronjob verbirgt.  

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

> Was content/hide und „Auswahl verbergen“ in der Liste der Unterelemente tun, bevor ein Knoten verborgen wird, auf den andere Objekte  
> verweisen (was das Verbergen ganz wegnimmt, gezählt wie bei \[RemoveSettings\] ReverseRelationCheck) oder dessen  
> Objekt weitere Platzierungen hat. Der Cronjob hidecron ist nicht betroffen. doc/eng-us/features/6.0/reverse-relation-check.md  
>  disabled - keine Prüfung: das Verbergen geschieht wie bisher sofort  
>  warn     - (Standard) eine Bestätigungsseite listet zuerst die Verweise und die weiteren Platzierungen auf; ein Verbergen  
>             ohne beides geschieht wie bisher  

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
