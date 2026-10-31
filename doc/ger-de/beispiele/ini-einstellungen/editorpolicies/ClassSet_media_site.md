---
title: editorpolicies.ini [ClassSet_media_site]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [ClassSet_media_site] in editorpolicies.ini: Classes, Parents, ContainersAreParents, WebsiteToolbar"
keywords: Einstellung, editorpolicies.ini, ClassSet_media_site, INI, setting, Classes, Parents, ContainersAreParents, WebsiteToolbar
type: generated
generated: exp:help:examples
---
# editorpolicies.ini [ClassSet_media_site]

Der Block `[ClassSet_media_site]` von `settings/editorpolicies.ini`, mit 4 Einstellung(en).

Bearbeiten Sie `settings/editorpolicies.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/editorpolicies.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/editorpolicies.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/editorpolicies.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Classes

> Die Inhaltsklassen der Media-Site (ng\_\*): was ein Redakteur im Inhaltsbaum anlegt und über die  
> Website-Werkzeugleiste bearbeitet. Classes\[\] sind Klassenbezeichner; \* passt auf beliebigen Text (fnmatch).  

Standard: eine Liste mit 1: `ng_*`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/editorpolicies.ini.append.php
[ClassSet_media_site]
# ein leeres "Classes[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Classes[]
Classes[]=ng_*
*/ ?>
```

## Parents

> Die Klassen, unter denen die Klassen der Gruppe angelegt werden (content/create ParentClass). Die Container-Klassen  
> von Classes\[\] zählen ebenfalls, wenn ContainersAreParents=true ist.  

Standard: eine Liste mit 2: `folder`, `frontpage`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/editorpolicies.ini.append.php
[ClassSet_media_site]
# ein leeres "Parents[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Parents[]
Parents[]=folder
Parents[]=frontpage
*/ ?>
```

## ContainersAreParents

Standard: `true`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/editorpolicies.ini.append.php
[ClassSet_media_site]
ContainersAreParents=true
*/ ?>
```

## WebsiteToolbar

> true: auch die Website-Werkzeugleiste (websitetoolbar/use, wo ezwt installiert ist) für die Klassen der Gruppe.  

Standard: `true`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/editorpolicies.ini.append.php
[ClassSet_media_site]
WebsiteToolbar=true
*/ ?>
```

## Siehe auch

- [Alle Blöcke von editorpolicies.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/editorpolicies.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
