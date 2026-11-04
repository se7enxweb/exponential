---
title: menu.ini [Leftmenu_help]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [Leftmenu_help] in menu.ini: Name, Links, LinkNames, PolicyList_dashboard, PolicyList_contents, PolicyList_search, PolicyList_extensions, PolicyList_report"
keywords: Einstellung, menu.ini, Leftmenu_help, INI, setting, Name, Links, LinkNames, PolicyList_dashboard, PolicyList_contents, PolicyList_search, PolicyList_extensions, PolicyList_report
type: generated
generated: exp:help:examples
---
# menu.ini [Leftmenu_help]

Der Block `[Leftmenu_help]` von `settings/menu.ini`, mit 8 Einstellung(en).

> Das linke Menü der Hilfeansichten (exphelp/\*, design:exphelp/menu.tpl): Sie gehören zum Reiter Setup  
> (ezsetupnavigationpart), der markiert bleibt, zeichnen aber dieses Menü statt des Setup-Menüs. Jeder Link wird nur  
> einem Benutzer gezeigt, der ihn öffnen darf; die Namen werden im Kontext kernel/exphelp übersetzt.  

Bearbeiten Sie `settings/menu.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/menu.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/menu.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/menu.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Name

Standard: `help`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_help]
Name=help
*/ ?>
```

## Links

Standard: ein Hash mit 5: `dashboard=exphelp/dashboard`, `contents=exphelp/contents`, `search=exphelp/search`, `extensions=exphelp/extensions`, `report=exphelp/report`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_help]
# ein leeres "Links[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Links[]
Links[dashboard]=exphelp/dashboard
Links[contents]=exphelp/contents
Links[search]=exphelp/search
Links[extensions]=exphelp/extensions
Links[report]=exphelp/report
*/ ?>
```

## LinkNames

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_help]
# ein leeres "LinkNames[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
LinkNames[]
*/ ?>
```

## PolicyList_dashboard

Standard: eine Liste mit 1: `exphelp/read`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_help]
# ein leeres "PolicyList_dashboard[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
PolicyList_dashboard[]
PolicyList_dashboard[]=exphelp/read
*/ ?>
```

## PolicyList_contents

Standard: eine Liste mit 1: `exphelp/read`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_help]
# ein leeres "PolicyList_contents[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
PolicyList_contents[]
PolicyList_contents[]=exphelp/read
*/ ?>
```

## PolicyList_search

Standard: eine Liste mit 1: `exphelp/read`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_help]
# ein leeres "PolicyList_search[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
PolicyList_search[]
PolicyList_search[]=exphelp/read
*/ ?>
```

## PolicyList_extensions

Standard: eine Liste mit 1: `exphelp/read`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_help]
# ein leeres "PolicyList_extensions[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
PolicyList_extensions[]
PolicyList_extensions[]=exphelp/read
*/ ?>
```

## PolicyList_report

Standard: eine Liste mit 1: `exphelp/read`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_help]
# ein leeres "PolicyList_report[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
PolicyList_report[]
PolicyList_report[]=exphelp/read
*/ ?>
```

## Siehe auch

- [Alle Blöcke von menu.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/menu.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
