---
title: exphelp.ini [TreeSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [TreeSettings] in exphelp.ini: PageOrder"
keywords: Einstellung, exphelp.ini, TreeSettings, INI, setting, PageOrder
type: generated
generated: exp:help:examples
---
# exphelp.ini [TreeSettings]

Der Block `[TreeSettings]` von `settings/exphelp.ini`, mit 1 Einstellung(en).

Bearbeiten Sie `settings/exphelp.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/exphelp.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/exphelp.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/exphelp.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## PageOrder

> The place of a page in the page tree (/exphelp/contents and the sidebar of every page), the folder lists and the  
> category lists. Pages are sorted by their order, then by title; a page's order is its front matter "order", else 0  
> for a folder's README.md/index.md and 1000 for any other page. An entry here wins over the front matter, so the  
> pages that cannot carry front matter (the project files, plain text pages) can be placed too. Folders still come  
> before the pages of their level.  
> Keys are page ids: the path below the language directory (INSTALL.md, glossary.md, release-overview),  
> project/<path> for a project file, ext/<extension>/<path> for an extension's page. The id is the same in every  
> language, so one entry places the page and its translations. PageOrder\[\] alone (empty) clears this list.  
> The top level of the tree is placed here and only here: its pages carry no front matter order of their own  
> (glossary.md keeps order 9999 in its front matter, the same as here). The same list is the built-in default  
> (expHelpAudience::PAGE\_ORDER).  
> PageOrder\[<page id>\]=<integer>  

Standard: ein Hash mit 13: `project/README.md=0`, `INSTALL.md=10`, `languages.md=20`, `branch-overview.md=30`, `release-overview=40`, `project/SECURITY.md=50`, `project/CONTRIBUTING.md=100`, `project/CODE_OF_CONDUCT.md=105`, `project/COPYRIGHT.md=110`, `README-LICENSE.md=120`, `LICENSE-GFDL-1.3.txt=130`, `project/LICENSE.md=140` ....

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[TreeSettings]
# ein leeres "PageOrder[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
PageOrder[]
PageOrder[project/README.md]=0
PageOrder[INSTALL.md]=10
PageOrder[languages.md]=20
PageOrder[branch-overview.md]=30
PageOrder[release-overview]=40
PageOrder[project/SECURITY.md]=50
PageOrder[project/CONTRIBUTING.md]=100
PageOrder[project/CODE_OF_CONDUCT.md]=105
PageOrder[project/COPYRIGHT.md]=110
PageOrder[README-LICENSE.md]=120
PageOrder[LICENSE-GFDL-1.3.txt]=130
PageOrder[project/LICENSE.md]=140
PageOrder[glossary.md]=9999
*/ ?>
```

## Siehe auch

- [Alle Blöcke von exphelp.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/exphelp.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
