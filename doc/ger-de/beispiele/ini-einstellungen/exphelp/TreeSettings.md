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

> Der Platz einer Seite im Seitenbaum (/exphelp/contents und die Seitenleiste jeder Seite), in den Ordnerlisten und den  
> Kategorielisten. Seiten werden nach ihrer Reihenfolge, dann nach Titel sortiert; die Reihenfolge einer Seite ist "order" aus ihrem Front Matter, sonst 0  
> für die README.md/index.md eines Ordners und 1000 für jede andere Seite. Ein Eintrag hier gewinnt gegen das Front Matter, sodass auch die  
> Seiten platziert werden können, die kein Front Matter tragen können (die Projektdateien, Seiten mit reinem Text). Ordner stehen weiterhin  
> vor den Seiten ihrer Ebene.  
> Die Schlüssel sind Seiten-IDs: der Pfad unter dem Sprachverzeichnis (INSTALL.md, glossary.md, release-overview),  
> project/<path> für eine Projektdatei, ext/<extension>/<path> für die Seite einer Erweiterung. Die ID ist in jeder  
> Sprache gleich, ein Eintrag platziert also die Seite und ihre Übersetzungen. PageOrder\[\] allein (leer) leert diese Liste.  
> Die oberste Ebene des Baums wird hier und nur hier platziert: Ihre Seiten tragen keine eigene Reihenfolge im Front Matter  
> (glossary.md behält order 9999 in seinem Front Matter, wie hier). Dieselbe Liste ist der eingebaute Standard  
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
