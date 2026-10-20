---
title: menu.ini [Leftmenu_design]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [Leftmenu_design] in menu.ini: Name, Links, LinkNames, PolicyList_look_and_feel, PolicyList_toolbar_management, PolicyList_menu_management, PolicyList_template_editor, LinkIcons ..."
keywords: Einstellung, menu.ini, Leftmenu_design, INI, setting, Name, Links, LinkNames, PolicyList_look_and_feel, PolicyList_toolbar_management, PolicyList_menu_management, PolicyList_template_editor, LinkIcons, LinkDescriptions
type: generated
generated: exp:help:examples
---
# menu.ini [Leftmenu_design]

Der Block `[Leftmenu_design]` von `settings/menu.ini`, mit 9 Einstellung(en).

> Definition des eigenen Hauptmenüeintrags  
> Hinweis: Dies ist nur ein Beispiel  
>
> \[Topmenu\_custom\]  
> # Uses a custom navigation part (See list NavigationPart group above)  
> NavigationPartIdentifier=ezcustomnavigationpart  
> Name=Custom  
> Tooltip=A custom tab for Exponential  
> URL\[\]  
> URL\[default\]=custom/list  
> Enabled\[\]  
> Enabled\[default\]=true  
> Enabled\[browse\]=false  
> Enabled\[edit\]=false  
> Shown\[\]  
> Shown\[default\]=true  
> Shown\[edit\]=true  
> Shown\[navigation\]=true  
> # We don't show it in browse mode  
> Shown\[browse\]=false  
> \[Leftmenu\_example\]  
> Übersetzter Wert des Namens (verwenden Sie hier also eng-GB)  
> Hinweis: Wenn der Name übersetzbar sein soll, müssen Sie  
>       die Zeichenkette versteckt in einem Template mit einem Aufruf |i18n() hinzufügen oder  
>       eine Dummy-PHP-Datei mit folgendem Inhalt anlegen:  
> ezpI18n::tr( 'design/admin/parts/example/menu', 'Example menu', 'Leftmenu name for example menu' );  
> Dann nimmt ezlupdate sie in die .ts-Datei auf.  
> Hinweis 2: Für eigene Menüs (auf eigenen Reitern) können Sie ini\_menu.tpl auch einen Hash von Übersetzungen übergeben,  
> sodass es deren Zeichenketten verwendet, wenn der Name des Menüs oder der Index des Links im Hash vorkommt.  
> (Beispiel siehe design/admin/templates/parts/setup/menu.tpl)  
> Name=Content Menu  
> Richtlinienliste für Menüeinträge, blendet die Box aus, wenn false  
> der Wert kann die Form "<node\_id>" oder "<module>/<function>" haben ("<module>/\*" für ein Modul ohne  
> Richtlinienfunktionen)  
> PolicyList\[\]=<value>  
> Liste der Link-URLs und Indizes; der Index wird für die Einstellungen LinkNames und PolicyList verwendet  
> Links\[\]  
> Name des Links; zum Übersetzen siehe den Hinweis zu 'Name'  
> LinkNames\[<Links\_index>\]=<value>  
> Richtlinienliste pro Link, mit dem Index aus dem Array Links als Teil des Namens;  
> der Wert kann die Form "<node\_id>" oder "<module>/<function>" haben  
> Der Menüeintrag ist deaktiviert (kein Link), wenn dies false ergibt  
> PolicyList\_<Links\_index>\[\]=<value>  
> Enabled legt fest, ob der Menüeintrag im aktuellen ui\_context anklickbar ist, oder der Wert 'default'  
> Standardmäßig gilt 'default' als true und 'edit' + 'browse' als false  
> Enabled\_<Links\_index>\[browse\]=false  
> Entfernen Sie den Kommentar der folgenden Zeilen, um dem Inhaltsbereich ein linkes Menü hinzuzufügen  
> \[Leftmenu\_content\]  
> Name=Content Menu  
> Links\[languages\]=content/translations  
> LinkNames\[languages\]=Languages  
> PolicyList\_languages\[\]=content/translations  

Bearbeiten Sie `settings/menu.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/menu.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/menu.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/menu.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Name

Standard: `design`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_design]
Name=design
*/ ?>
```

## Links

> Die Menüs der klassischen Website-Designs (visual/menuconfig, doc/eng-us/guides/classic-menu-settings.md); seine Seite markiert  
> den Reiter Design (kernel/visual/module.php)  

Standard: ein Hash mit 4: `look_and_feel=content/edit/54`, `toolbar_management=visual/toolbarlist`, `menu_management=visual/menuconfig`, `template_editor=visual/templatelist`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_design]
# ein leeres "Links[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Links[]
Links[look_and_feel]=content/edit/54
Links[toolbar_management]=visual/toolbarlist
Links[menu_management]=visual/menuconfig
Links[template_editor]=visual/templatelist
*/ ?>
```

## LinkNames

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_design]
# ein leeres "LinkNames[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
LinkNames[]
*/ ?>
```

## PolicyList_look_and_feel

Standard: eine Liste mit 1: `56`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_design]
# ein leeres "PolicyList_look_and_feel[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
PolicyList_look_and_feel[]
PolicyList_look_and_feel[]=56
*/ ?>
```

## PolicyList_toolbar_management

Standard: eine Liste mit 1: `visual/*`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_design]
# ein leeres "PolicyList_toolbar_management[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
PolicyList_toolbar_management[]
PolicyList_toolbar_management[]=visual/*
*/ ?>
```

## PolicyList_menu_management

Standard: eine Liste mit 1: `visual/*`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_design]
# ein leeres "PolicyList_menu_management[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
PolicyList_menu_management[]
PolicyList_menu_management[]=visual/*
*/ ?>
```

## PolicyList_template_editor

Standard: eine Liste mit 1: `visual/*`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_design]
# ein leeres "PolicyList_template_editor[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
PolicyList_template_editor[]
PolicyList_template_editor[]=visual/*
*/ ?>
```

## LinkIcons

> Die Kacheln dieser Links, wo die Setup-Übersicht dieses Menü zeigt (\[Leftmenu\_setup\] GroupSections\[design\]);  
> übersetzt in design/admin/parts/design/menu  
> Der Link Fehlender Alternativtext, den die Erweiterung expai hier anhängt, für das Mega-Menü des Reiters Dashboard in admin4  
> (\[Leftmenu\_my\_mega\] fügt dieses Menü seiner Gruppe "ei" hinzu)  

Standard: ein Hash mit 6: `look_and_feel=palette`, `toolbar_management=tools`, `menu_management=menu-button-wide`, `template_editor=code-square`, `templates=file-earmark-code`, `expai_alttext=images`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_design]
# ein leeres "LinkIcons[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
LinkIcons[]
LinkIcons[look_and_feel]=palette
LinkIcons[toolbar_management]=tools
LinkIcons[menu_management]=menu-button-wide
LinkIcons[template_editor]=code-square
LinkIcons[templates]=file-earmark-code
LinkIcons[expai_alttext]=images
*/ ?>
```

## LinkDescriptions

Standard: ein Hash mit 6: `look_and_feel=The look of the site.`, `toolbar_management=The toolbars and their tools.`, `menu_management=The menus of the classic site designs.`, `template_editor=List and edit templates.`, `templates=The templates of the designs and their overrides.`, `expai_alttext=Images without alternative text, and suggestions for it.`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_design]
# ein leeres "LinkDescriptions[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
LinkDescriptions[]
LinkDescriptions[look_and_feel]=The look of the site.
LinkDescriptions[toolbar_management]=The toolbars and their tools.
LinkDescriptions[menu_management]=The menus of the classic site designs.
LinkDescriptions[template_editor]=List and edit templates.
LinkDescriptions[templates]=The templates of the designs and their overrides.
LinkDescriptions[expai_alttext]=Images without alternative text, and suggestions for it.
*/ ?>
```

## Siehe auch

- [Alle Blöcke von menu.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/menu.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
