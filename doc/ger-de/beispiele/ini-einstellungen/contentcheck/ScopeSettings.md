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

> Where a check starts (doc/eng-us/features/6.0/content-checks.md, "Where a check starts"): from the  
> node in its form or address, else the signed-in user's last choice (the preference  
> admin\_contentcheck\_scope), else content.ini \[NodeSettings\] RootNode.  
> The quick scope buttons the checks offer, in this order: tree ("Whole tree", node 1, for users  
> with an unlimited content/read), content ("Websites", content.ini RootNode), media ("Media",  
> MediaRootNode), users ("Users", UserRootNode), setup (SetupRootNode), design (DesignRootNode),  
> a node id, or a URL alias path such as media/images (these two show the node's name). A button  
> whose node does not exist or that the user may not read is left out. Any other node, Users  
> among them, is chosen with the node picker. The content and media roots are the ones of  
> content.ini \[NodeSettings\] (RootNode, MediaRootNode); set them there.  

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

> The second row beneath the buttons: the sites, sections and top folders under the content root,  
> so a check can start in one site's subtree. Read live from the content tree: the visible  
> children (not hidden, not invisible) the user may read, in the content root's sort order.  
> SiteRow=disabled hides it. SiteRowParent is a preset key, a node id or a URL alias path  
> (default: content). SiteRowLimit buttons at most (1 to 50); with more, "More..." opens the  
> node picker at that level.  

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

> The views that take a start location and that the node picker may return to  

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
