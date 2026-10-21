---
title: menu.ini [Topmenu_content]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [Topmenu_content] in menu.ini: Icon, URL, NavigationPartIdentifier, Enabled, Shown, PolicyList"
keywords: Einstellung, menu.ini, Topmenu_content, INI, setting, Icon, URL, NavigationPartIdentifier, Enabled, Shown, PolicyList
type: generated
generated: exp:help:examples
---
# menu.ini [Topmenu_content]

Der Block `[Topmenu_content]` von `settings/menu.ini`, mit 6 Einstellung(en).

> Beispiel für das Hinzufügen eines eigenen Eintrags im Hauptmenü  
> Siehe den auskommentierten Eintrag Topmenu\_custom ganz unten  
> Tabs\[\]=custom  
> Hier die Einstellungen, die das Verhalten jedes  
> Eintrags im oberen Menü der Administration steuern.  
> URL\['ui\_context'\] - auf welche Ansicht der Eintrag in verschiedenen Kontexten zeigt.  
> NavigationPartIdentifier  - Bezeichner des Navigationsbereichs des Menüeintrags.  
> Name - Name, der auf dem Menüeintrag angezeigt wird; ist er nicht gesetzt, wird der Standardwert  
>        verwendet. Schreiben Sie ihn auf Englisch: Er wird wie jeder andere Oberflächen-  
>        text im Kontext übersetzt, den TranslationContext nennt, und so angezeigt, wie er ist, wenn  
>        die aktuelle Sprache keine Übersetzung dafür hat.  
> Tooltip - Tooltip, der für den Menüeintrag angezeigt wird; ist er nicht gesetzt, wird der Standardwert  
>           verwendet. Wie Name übersetzt.  
> TranslationContext - (optional) der Übersetzungskontext, in dem Name und Tooltip  
>        nachgeschlagen werden, design/admin/pagelayout (der Kontext der Standardnamen  
>        der Reiter), wenn er nicht gesetzt ist. Eine Erweiterung, die einen Reiter hinzufügt, legt die  
>        Übersetzungen seines Namens und Tooltips unter diesem Kontext in ihre eigene translation.ts.  
> Icon - (optional) der Name eines Symbols, das admin4 vor den Namen des Reiters zeichnet: ein Name aus design.ini  
>        \[AdminDesignSettings\] IconList\[\] (Bootstrap Icons); ein unbekannter Name zeichnet kein Symbol.  
> Enabled\[ui\_context\] - legt fest, ob der Menüeintrag im aktuellen ui\_context anklickbar ist.  
> Shown\[ui\_context\]  - legt fest, ob der Menüeintrag im aktuellen ui\_context anklickbar ist  
> PolicyList\[\] - Liste von Richtlinien, in der Form "<node\_id>" oder "<module>/<function>"; ein Modul ohne  
>                Richtlinienfunktionen (visual, class, role, workflow ...) ist "<module>/\*"  
>                Bei einer Knoten-ID wird content/read geprüft!  

Bearbeiten Sie `settings/menu.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/menu.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/menu.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/menu.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Icon

Standard: `diagram-3`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Topmenu_content]
Icon=diagram-3
*/ ?>
```

## URL

Standard: ein Hash mit 2: `default=content/view/full/2`, `browse=content/browse/2`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Topmenu_content]
# ein leeres "URL[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
URL[]
URL[default]=content/view/full/2
URL[browse]=content/browse/2
*/ ?>
```

## NavigationPartIdentifier

Standard: `ezcontentnavigationpart`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Topmenu_content]
NavigationPartIdentifier=ezcontentnavigationpart
*/ ?>
```

## Enabled

> Name=Content structure  
> Tooltip=Manage the main content structure of the site.  

Standard: ein Hash mit 3: `default=true`, `browse=true`, `edit=false`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Topmenu_content]
# ein leeres "Enabled[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Enabled[]
Enabled[default]=true
Enabled[browse]=true
Enabled[edit]=false
*/ ?>
```

## Shown

Standard: ein Hash mit 3: `default=true`, `navigation=true`, `browse=true`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Topmenu_content]
# ein leeres "Shown[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Shown[]
Shown[default]=true
Shown[navigation]=true
Shown[browse]=true
*/ ?>
```

## PolicyList

Standard: eine Liste mit 1: `2`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Topmenu_content]
# ein leeres "PolicyList[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
PolicyList[]
PolicyList[]=2
*/ ?>
```

## Siehe auch

- [Alle Blöcke von menu.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/menu.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
