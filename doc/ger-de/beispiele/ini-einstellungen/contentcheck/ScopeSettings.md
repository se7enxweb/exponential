---
title: contentcheck.ini [ScopeSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [ScopeSettings] in contentcheck.ini: PresetList, SiteRow, SiteRowParent, SiteRowLimit, ViewList"
keywords: Einstellung, contentcheck.ini, ScopeSettings, INI, setting, PresetList, SiteRow, SiteRowParent, SiteRowLimit, ViewList
type: generated
generated: exp:help:examples
---
# contentcheck.ini [ScopeSettings]

Der Block `[ScopeSettings]` von `settings/contentcheck.ini`, mit 5 Einstellung(en).

Bearbeiten Sie `settings/contentcheck.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/contentcheck.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/contentcheck.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/contentcheck.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## PresetList

> Wo eine Prüfung beginnt (doc/eng-us/features/6.0/content-checks.md, "Where a check starts"): beim  
> Knoten in ihrem Formular oder ihrer Adresse, sonst bei der letzten Wahl des angemeldeten Benutzers (die Einstellung  
> admin\_contentcheck\_scope), sonst bei content.ini \[NodeSettings\] RootNode.  
> Die Schnellwahl-Schaltflächen der Prüfungen, in dieser Reihenfolge: tree („Ganzer Baum“, Knoten 1, für Benutzer  
> mit unbeschränktem content/read), content („Websites“, content.ini RootNode), media („Medien“,  
> MediaRootNode), users („Benutzer“, UserRootNode), setup (SetupRootNode), design (DesignRootNode),  
> eine Knoten-ID oder ein URL-Alias-Pfad wie media/images (diese beiden zeigen den Namen des Knotens). Eine Schaltfläche,  
> deren Knoten nicht existiert oder die der Benutzer nicht lesen darf, entfällt. Jeder andere Knoten, Benutzer  
> darunter, wird mit der Knotenauswahl gewählt. Die Wurzeln der Inhalte und Medien sind die aus  
> content.ini \[NodeSettings\] (RootNode, MediaRootNode); setzen Sie sie dort.  

Standard: eine Liste mit 3: `tree`, `content`, `media`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/contentcheck.ini.append.php
[ScopeSettings]
# ein leeres "PresetList[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
PresetList[]
PresetList[]=tree
PresetList[]=content
PresetList[]=media
*/ ?>
```

## SiteRow

> Die zweite Zeile unter den Schaltflächen: die Websites, Sektionen und obersten Ordner unter der Inhaltswurzel,  
> damit eine Prüfung im Teilbaum einer Website beginnen kann. Live aus dem Inhaltsbaum gelesen: die sichtbaren  
> Unterknoten (nicht verborgen, nicht unsichtbar), die der Benutzer lesen darf, in der Sortierung der Inhaltswurzel.  
> SiteRow=disabled blendet sie aus. SiteRowParent ist ein Vorlagenschlüssel, eine Knoten-ID oder ein URL-Alias-Pfad  
> (Standard: content). SiteRowLimit Schaltflächen höchstens (1 bis 50); bei mehr öffnet „Mehr ...“ die  
> Knotenauswahl auf dieser Ebene.  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/contentcheck.ini.append.php
[ScopeSettings]
SiteRow=enabled
*/ ?>
```

## SiteRowParent

Standard: `content`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/contentcheck.ini.append.php
[ScopeSettings]
SiteRowParent=content
*/ ?>
```

## SiteRowLimit

Standard: `12`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/contentcheck.ini.append.php
[ScopeSettings]
SiteRowLimit=12
*/ ?>
```

## ViewList

> Die Ansichten, die einen Startort nehmen und zu denen die Knotenauswahl zurückkehren darf  

Standard: eine Liste mit 3: `unreferenced`, `brokenrelations`, `storage`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/contentcheck.ini.append.php
[ScopeSettings]
# ein leeres "ViewList[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
ViewList[]
ViewList[]=unreferenced
ViewList[]=brokenrelations
ViewList[]=storage
*/ ?>
```

## Siehe auch

- [Alle Blöcke von contentcheck.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/contentcheck.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
