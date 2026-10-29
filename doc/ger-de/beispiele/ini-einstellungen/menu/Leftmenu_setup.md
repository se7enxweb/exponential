---
title: menu.ini [Leftmenu_setup]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [Leftmenu_setup] in menu.ini: Name, Groups, GroupNames, GroupTabs, GroupSections, GroupLinks_content_model, GroupLinks_site, GroupLinks_design ..."
keywords: Einstellung, menu.ini, Leftmenu_setup, INI, setting, Name, Groups, GroupNames, GroupTabs, GroupSections, GroupLinks_content_model, GroupLinks_site, GroupLinks_design, GroupLinks_workflow, GroupLinks_users, GroupLinks_data, GroupLinks_system, GroupLinks_ai, GroupLinks_updates, LeadLinks, MegaMenuLinks, LinkIcons, LinkDescriptions, LinkFigureTemplates, Links, LinkNames, PolicyList_cache, PolicyList_audit, PolicyList_classes, PolicyList_collected, PolicyList_contentfinder, PolicyList_unreferenced_objects, PolicyList_broken_relations, PolicyList_storage_files, PolicyList_maintenance
type: generated
generated: exp:help:examples
---
# menu.ini [Leftmenu_setup]

Der Block `[Leftmenu_setup]` von `settings/menu.ini`, mit 63 Einstellung(en).

Bearbeiten Sie `settings/menu.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/menu.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/menu.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/menu.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Name

Standard: `setup`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_setup]
Name=setup
*/ ?>
```

## Groups

> Gruppen des linken Menüs (admin4, parts/ini\_menu.tpl): Groups\[\] in ihrer Reihenfolge, GroupNames\[<group>\] der Titel,  
> GroupLinks\_<group>\[\] die Link-Schlüssel. Links in keiner Gruppe folgen unter „Mehr“, der letzten Gruppe. Für eine einfache Liste  
> setzen Sie Groups\[\] in einem Override zurück (eine Zeile Groups\[\] ohne Werte). Nur die Anordnung ändert sich: Der Zugriff wird wie bisher  
> pro Link geprüft.  
> Die Gruppen ai und updates enthalten die Links von Erweiterungen: Ein dort aufgeführter Schlüssel, dessen Erweiterung nicht aktiv ist, zeigt  
> nichts, und eine Gruppe ohne einen Link, den der Benutzer öffnen darf, wird nicht gezeichnet. Eine Erweiterung legt ihren eigenen Link mit  
> GroupLinks\_<group>\[\]=<key> in ihrer menu.ini.append.php in eine Gruppe (doc/eng-us/features/6.0/admin4-design.md, "Setup overview").  

Standard: eine Liste mit 10: `content_model`, `site`, `design`, `workflow`, `users`, `audit`, `data`, `system`, `ai`, `updates`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_setup]
# ein leeres "Groups[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Groups[]
Groups[]=content_model
Groups[]=site
Groups[]=design
Groups[]=workflow
Groups[]=users
Groups[]=audit
Groups[]=data
Groups[]=system
Groups[]=ai
Groups[]=updates
*/ ?>
```

## GroupNames

Standard: ein Hash mit 10: `content_model=Content model`, `site=Site and URLs`, `design=Design`, `workflow=Workflows`, `users=Users and messages`, `audit=Audit`, `data=Data tables`, `system=System`, `ai=AI`, `updates=Updates`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_setup]
# ein leeres "GroupNames[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
GroupNames[]
GroupNames[content_model]=Content model
GroupNames[site]=Site and URLs
GroupNames[design]=Design
GroupNames[workflow]=Workflows
GroupNames[users]=Users and messages
GroupNames[audit]=Audit
GroupNames[data]=Data tables
GroupNames[system]=System
GroupNames[ai]=AI
GroupNames[updates]=Updates
*/ ?>
```

## GroupTabs

> Eine Gruppe, die für einen Reiter des Hauptmenüs steht (admin4): GroupTabs\[<group>\]=<tab> zeigt die Gruppe nur, solange der Reiter nicht im  
> Hauptmenü ist (in \[TopAdminMenu\] Tabs\[\] ausgelassen, in HiddenTabs\[\] genannt oder in SetupTabs\[\] genannt für einen Benutzer, der  
> setup/menu öffnen darf); dann markieren die Seiten des Reiters (sein  
> NavigationPartIdentifier) den Reiter Setup und zeichnen dieses Menü. Solange der Reiter angezeigt wird, sind die Gruppe und ihre Links  
> gar nicht hier. GroupSections\[<group>\]=<section> fügt der Gruppe die Links eines anderen linken Menüs hinzu, nach ihren  
> GroupLinks\_<group>\[\]: Links\[\], Namen, PolicyList\[\] und PolicyList\_<key>\[\], Enabled\_<key>\[\] und Kachel-  
> einstellungen (LinkIcons\[\], LinkDescriptions\[\], LinkFigureTemplates\[\]) dieses Abschnitts, sodass das Menü des Reiters und die Gruppe eine Liste bleiben.  
> Ein Setup-Link auf eine Adresse, die auch das hinzugefügte Menü hat, erscheint nur in dieser Gruppe.  

Standard: ein Hash mit 2: `design=design`, `audit=audit`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_setup]
# ein leeres "GroupTabs[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
GroupTabs[]
GroupTabs[design]=design
GroupTabs[audit]=audit
*/ ?>
```

## GroupSections

Standard: ein Hash mit 2: `design=Leftmenu_design`, `audit=Leftmenu_audit`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_setup]
# ein leeres "GroupSections[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
GroupSections[]
GroupSections[design]=Leftmenu_design
GroupSections[audit]=Leftmenu_audit
*/ ?>
```

## GroupLinks_content_model

Standard: eine Liste mit 10: `classes`, `sections`, `states`, `languages`, `interface_languages`, `contentfinder`, `unreferenced_objects`, `broken_relations`, `storage_files`, `collected`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_setup]
# ein leeres "GroupLinks_content_model[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
GroupLinks_content_model[]
GroupLinks_content_model[]=classes
GroupLinks_content_model[]=sections
GroupLinks_content_model[]=states
GroupLinks_content_model[]=languages
GroupLinks_content_model[]=interface_languages
GroupLinks_content_model[]=contentfinder
GroupLinks_content_model[]=unreferenced_objects
GroupLinks_content_model[]=broken_relations
GroupLinks_content_model[]=storage_files
GroupLinks_content_model[]=collected
*/ ?>
```

## GroupLinks_site

Standard: eine Liste mit 9: `preload`, `rss`, `packages`, `pdf_export`, `search_statistics`, `url_management`, `url_translator`, `url_wildcards`, `rad`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_setup]
# ein leeres "GroupLinks_site[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
GroupLinks_site[]
GroupLinks_site[]=preload
GroupLinks_site[]=rss
GroupLinks_site[]=packages
GroupLinks_site[]=pdf_export
GroupLinks_site[]=search_statistics
GroupLinks_site[]=url_management
GroupLinks_site[]=url_translator
GroupLinks_site[]=url_wildcards
GroupLinks_site[]=rad
*/ ?>
```

## GroupLinks_design

Standard: eine Liste mit 1: `design_node`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_setup]
# ein leeres "GroupLinks_design[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
GroupLinks_design[]
GroupLinks_design[]=design_node
*/ ?>
```

## GroupLinks_workflow

Standard: eine Liste mit 3: `workflows`, `workflow_processes`, `triggers`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_setup]
# ein leeres "GroupLinks_workflow[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
GroupLinks_workflow[]
GroupLinks_workflow[]=workflows
GroupLinks_workflow[]=workflow_processes
GroupLinks_workflow[]=triggers
*/ ?>
```

## GroupLinks_users

Standard: eine Liste mit 6: `user_hygiene`, `sessions`, `notification`, `mailpreferences`, `oauth_list`, `audit`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_setup]
# ein leeres "GroupLinks_users[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
GroupLinks_users[]
GroupLinks_users[]=user_hygiene
GroupLinks_users[]=sessions
GroupLinks_users[]=notification
GroupLinks_users[]=mailpreferences
GroupLinks_users[]=oauth_list
GroupLinks_users[]=audit
*/ ?>
```

## GroupLinks_data

Standard: eine Liste mit 4: `datatables`, `data_connections`, `secrets`, `data_syncs`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_setup]
# ein leeres "GroupLinks_data[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
GroupLinks_data[]
GroupLinks_data[]=datatables
GroupLinks_data[]=data_connections
GroupLinks_data[]=secrets
GroupLinks_data[]=data_syncs
*/ ?>
```

## GroupLinks_system

> Erweiterungen: expqueue (die Auftragswarteschlange, auch mit expai ausgeliefert), sevenx\_dse (der Datenbankeditor)  

Standard: eine Liste mit 12: `cache`, `system_information`, `help_center`, `upgrade_check`, `maintenance`, `cronjobs`, `logs`, `extensions`, `ini`, `sitedata`, `expqueue`, `dse`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_setup]
# ein leeres "GroupLinks_system[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
GroupLinks_system[]
GroupLinks_system[]=cache
GroupLinks_system[]=system_information
GroupLinks_system[]=help_center
GroupLinks_system[]=upgrade_check
GroupLinks_system[]=maintenance
GroupLinks_system[]=cronjobs
GroupLinks_system[]=logs
GroupLinks_system[]=extensions
GroupLinks_system[]=ini
GroupLinks_system[]=sitedata
GroupLinks_system[]=expqueue
GroupLinks_system[]=dse
*/ ?>
```

## GroupLinks_ai

> Erweiterungen: expai  

Standard: eine Liste mit 3: `expai_summary`, `expai_models`, `expai_budgets`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_setup]
# ein leeres "GroupLinks_ai[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
GroupLinks_ai[]
GroupLinks_ai[]=expai_summary
GroupLinks_ai[]=expai_models
GroupLinks_ai[]=expai_budgets
*/ ?>
```

## GroupLinks_updates

> Erweiterungen: ezupdate, git\_manager  

Standard: eine Liste mit 3: `ezupdate`, `git_manager`, `git_manager_backup`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_setup]
# ein leeres "GroupLinks_updates[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
GroupLinks_updates[]
GroupLinks_updates[]=ezupdate
GroupLinks_updates[]=git_manager
GroupLinks_updates[]=git_manager_backup
*/ ?>
```

## LeadLinks

> Links, die vor den Gruppen und in keiner davon angezeigt werden: die Übersicht (setup/menu), jeder Link dieses Menüs als Kachel  
> in denselben Gruppen. Der Link einer Erweiterung in keiner Gruppe erscheint unter „Mehr“ im Menü und in der Übersicht.  

Standard: eine Liste mit 1: `overview`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_setup]
# ein leeres "LeadLinks[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
LeadLinks[]
LeadLinks[]=overview
*/ ?>
```

## MegaMenuLinks

> Das Mega-Menü von admin4 (Prototyp, \[TopAdminMenu\] MegaMenuTabs\[\]=setup): die Links dieses Menüs, die es zeigt, nach Link-Schlüssel,  
> in dieser Reihenfolge; höchstens 19 (6 bis 19 lesen sich am besten), der Rest ist einen Klick entfernt unter „Zu Setup“ (die Übersicht).  
> Jeder Link behält seine Gruppe (GroupNames\[\]) und seine Zugriffsprüfungen: Ein Link, den der Benutzer nicht öffnen darf oder dessen Erweiterung nicht  
> aktiv ist, entfällt, und ebenso eine Gruppe, in der kein Link übrig bleibt. Die Gruppen folgen hier der Reihenfolge ihres ersten Links.  
> Eine auf keine Werte zurückgesetzte Liste zeigt jeden Link des Menüs, wie die Übersicht.  
> Logdateien (setup/logs) nahmen den Platz der Linkverwaltung (url/list) ein, die einen Klick entfernt in der  
> Gruppe „Website und URLs“ der Übersicht bleibt. Gesammelte Informationen (Inhaltsmodell) und RSS (Website und URLs) kamen  
> in 6.0.15 hinzu, ebenso Websites vorwärmen und Pakete (Website und URLs) sowie EI: Modelle und EI: Budgets (KI) und oAuth admin  
> (Benutzer und Nachrichten): Die Liste enthält neunzehn.  

Standard: eine Liste mit 19: `classes`, `sections`, `collected`, `rss`, `preload`, `packages`, `audit`, `oauth_list`, `cache`, `cronjobs`, `logs`, `extensions` ....

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_setup]
# ein leeres "MegaMenuLinks[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
MegaMenuLinks[]
MegaMenuLinks[]=classes
MegaMenuLinks[]=sections
MegaMenuLinks[]=collected
MegaMenuLinks[]=rss
MegaMenuLinks[]=preload
MegaMenuLinks[]=packages
MegaMenuLinks[]=audit
MegaMenuLinks[]=oauth_list
MegaMenuLinks[]=cache
MegaMenuLinks[]=cronjobs
MegaMenuLinks[]=logs
MegaMenuLinks[]=extensions
MegaMenuLinks[]=system_information
MegaMenuLinks[]=ini
MegaMenuLinks[]=expai_summary
MegaMenuLinks[]=expai_models
MegaMenuLinks[]=expai_budgets
MegaMenuLinks[]=ezupdate
MegaMenuLinks[]=git_manager
*/ ?>
```

## LinkIcons

> Die Setup-Übersicht (setup/menu) zeichnet dieses Menü als Kacheln. Pro Link-Schlüssel, alles optional:  
>   LinkIcons\[<key>\]=<icon>         ein Symbol aus design.ini \[AdminDesignSettings\] IconList\[\] (der Name eines Bootstrap-Symbols,  
>                                   z. B. bar-chart); ein dort nicht aufgeführter Name oder kein Eintrag zeichnet das Stecker-Symbol  
>   LinkDescriptions\[<key>\]=<text>  eine Zeile dazu, was die Funktion tut, im Kontext des Menüs übersetzt  
>                                   (design/admin/parts/setup/menu) wie LinkNames\[\]  
>   LinkFigureTemplates\[<key>\]=design:<path>.tpl  ein Template, das die Abbildung der Kachel zeichnet, mit  
>                                   figure\_key=<key> nur für eine angezeigte Kachel eingebunden; leer oder nicht design: zeichnet keine  
> Eine Erweiterung setzt sie für ihren eigenen Link in ihrer menu.ini.append.php (doc/eng-us/features/6.0/admin4-design.md).  

Standard: ein Hash mit 57: `audit=journal-check`, `broken_relations=exclamation-diamond`, `storage_files=files`, `cache=arrow-clockwise`, `classes=diagram-3`, `collected=inbox`, `contentfinder=search`, `cronjobs=clock`, `design_node=brush`, `dse=database`, `expai_budgets=piggy-bank`, `expai_models=cpu` ....

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_setup]
# ein leeres "LinkIcons[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
LinkIcons[]
LinkIcons[audit]=journal-check
LinkIcons[broken_relations]=exclamation-diamond
LinkIcons[storage_files]=files
LinkIcons[cache]=arrow-clockwise
LinkIcons[classes]=diagram-3
LinkIcons[collected]=inbox
LinkIcons[contentfinder]=search
LinkIcons[cronjobs]=clock
LinkIcons[design_node]=brush
LinkIcons[dse]=database
LinkIcons[expai_budgets]=piggy-bank
LinkIcons[expai_models]=cpu
LinkIcons[expai_summary]=stars
LinkIcons[expqueue]=list-task
LinkIcons[extensions]=puzzle
LinkIcons[ezupdate]=cloud-arrow-down
LinkIcons[git_manager]=git
LinkIcons[git_manager_backup]=archive
LinkIcons[global_setting]=globe
LinkIcons[ini]=sliders
# ... 37 weitere in der ausgelieferten Datei
*/ ?>
```

## LinkDescriptions

Standard: ein Hash mit 57: `audit=Who changed what: the audit settings and archives.`, `broken_relations=Visible content that links to deleted, hidden or trashed content.`, `storage_files=Files in the storage that nothing uses, and files with the wrong owner or mode.`, `cache=Clear or regenerate the caches.`, `classes=The content classes and their attributes.`, `collected=What visitors sent through forms.`, `contentfinder=Find content by its id, remote id or path.`, `cronjobs=The cronjob parts, their scripts and last runs.`, `design_node=The design's top node and the objects below it.`, `dse=Browse and query the database directly.`, `expai_budgets=AI budgets, queued requests and what they cost.`, `expai_models=The AI providers and models, and their tests.` ....

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_setup]
# ein leeres "LinkDescriptions[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
LinkDescriptions[]
LinkDescriptions[audit]=Who changed what: the audit settings and archives.
LinkDescriptions[broken_relations]=Visible content that links to deleted, hidden or trashed content.
LinkDescriptions[storage_files]=Files in the storage that nothing uses, and files with the wrong owner or mode.
LinkDescriptions[cache]=Clear or regenerate the caches.
LinkDescriptions[classes]=The content classes and their attributes.
LinkDescriptions[collected]=What visitors sent through forms.
LinkDescriptions[contentfinder]=Find content by its id, remote id or path.
LinkDescriptions[cronjobs]=The cronjob parts, their scripts and last runs.
LinkDescriptions[design_node]=The design's top node and the objects below it.
LinkDescriptions[dse]=Browse and query the database directly.
LinkDescriptions[expai_budgets]=AI budgets, queued requests and what they cost.
LinkDescriptions[expai_models]=The AI providers and models, and their tests.
LinkDescriptions[expai_summary]=What the AI features send where, on one printable page.
LinkDescriptions[expqueue]=Background jobs: waiting, running and failed.
LinkDescriptions[extensions]=Activate and deactivate extensions.
LinkDescriptions[ezupdate]=Packages with a newer release, and their updates.
LinkDescriptions[git_manager]=The Git checkout of the installation and its updates.
LinkDescriptions[git_manager_backup]=Make, download and remove backups.
LinkDescriptions[global_setting]=The settings of the whole site.
LinkDescriptions[ini]=Read and edit the INI settings.
# ... 37 weitere in der ausgelieferten Datei
*/ ?>
```

## LinkFigureTemplates

Standard: ein Hash mit 6: `classes=design:setup/setupmenu_figure.tpl`, `sections=design:setup/setupmenu_figure.tpl`, `languages=design:setup/setupmenu_figure.tpl`, `extensions=design:setup/setupmenu_figure.tpl`, `sessions=design:setup/setupmenu_figure.tpl`, `cronjobs=design:setup/setupmenu_figure.tpl`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_setup]
# ein leeres "LinkFigureTemplates[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
LinkFigureTemplates[]
LinkFigureTemplates[classes]=design:setup/setupmenu_figure.tpl
LinkFigureTemplates[sections]=design:setup/setupmenu_figure.tpl
LinkFigureTemplates[languages]=design:setup/setupmenu_figure.tpl
LinkFigureTemplates[extensions]=design:setup/setupmenu_figure.tpl
LinkFigureTemplates[sessions]=design:setup/setupmenu_figure.tpl
LinkFigureTemplates[cronjobs]=design:setup/setupmenu_figure.tpl
*/ ?>
```

## Links

> Kein PolicyList\_overview\[\]: die Ansicht öffnet sich mit jeder einzelnen Funktion des Moduls setup (kernel/setup/module.php),  
> was ein einzelner PolicyList-Eintrag nicht ausdrücken kann; CheckViewAccess fragt die Ansicht selbst.  
> Die Audit-Einstellungen und Archive (doc/eng-us/bc/6.0/audit.md, "Placement"), nur mit audit/manage  
> Der Inhalts-Finder (doc/eng-us/features/6.0/content-finder.md): Inhalt nach ID oder Pfad finden, nur mit contentfinder/search  
> Die Inhaltsprüfungen (doc/eng-us/features/6.0/content-checks.md): nicht referenzierte Objekte und defekte Relationen, jeweils nur  
> mit ihrer Richtlinie  
> Die Cronjob-Konsole. LinkNames ist das, als was der Eintrag erscheint: ohne es  
> fällt das Menü auf den Schlüssel zurück, und "cronjobs" ist keine Bezeichnung.  
> Der Log-Viewer (doc/eng-us/features/6.0/log-viewer.md): Logdateien verfolgen, durchsuchen und querverweisen, nur mit setup/logs  
> Die Seite des Tabs Design (sein URL\[default\]), in der Gruppe, die für den Tab steht (GroupTabs\[design\]): nur angezeigt,  
> solange der Tab nicht im oberen Menü ist  
> Entfernt seit 4.3. Aktivieren, wenn Sie besondere Anforderungen haben.  
> Links\[global\_setting\]=content/edit/52  
> PolicyList\_global\_setting\[\]=54  
> Die Übersetzungs-Fallbacks jeder Oberflächensprache (doc/eng-us/languages.md), nur lesbar  
> Die E-Mail-Einstellungen für Administratoren: Status, Kategorien, Sperrliste, Einwilligungs-Log  
> Entfernt seit 4.3. Aktivieren, wenn Sie besondere Anforderungen haben.  
> Links\[look\_and\_feel\]=content/edit/54  
> PolicyList\_look\_and\_feel\[\]=56  
> Die Menüs der klassischen Site-Designs (visual/menuconfig) sind ein Link des Menüs Design (\[Leftmenu\_design\])  
> Der Benachrichtigungsstatus (ausstehende Ereignisse, Digest-Nachrichten, die letzten Läufe des Benachrichtigungs-Cronjobs, Probleme)  
> Preload Sites, RSS und Packages öffnen die Gruppe "Site und URLs" (GroupLinks\_site\[\]), RAD schließt sie: eine Gruppe zeichnet ihre  
> Links in der Reihenfolge von Links\[\], also ist ihre Reihenfolge hier die Reihenfolge der Gruppe  
> Standardmäßig in 4.3 deaktiviert, da es auch im Tab Benutzerkonto vorhanden ist.  
> Links\[roles\_and\_policies\]=role/list  
> PolicyList\_roles\_and\_policies\[\]=role/\*  
> Benutzerkonten prüfen (doc/eng-us/features/6.0/user-hygiene.md): nie angemeldet, lange nicht gesehen, deaktiviert, gesperrt,  
> verwaist, eine gemeinsame E-Mail-Adresse; nur mit user/hygiene. Derselbe Schlüssel steht in \[Leftmenu\_user\].  
> Die Datentabellen, die benannten Datenbankverbindungen und der Secret-Store (doc/eng-us/features/6.0/data-tables.md,  
> doc/eng-us/features/6.0/secret-store.md)  
> Die Zeilen von ezsite\_data, nur lesbar (Setup > Site-Daten)  
> Das Hilfe-Center, die Startseite der Hilfe (exphelp/dashboard)  
> Entfernt seit 4.3. Aktivieren, wenn Sie besondere Anforderungen haben.  
> Links\[templates\]=visual/templatelist  
> PolicyList\_templates\[\]=visual/\*  
> Entfernt seit 4.3. Aktivieren, wenn Sie besondere Anforderungen haben.  
> Links\[toolbar\_management\]=visual/toolbarlist  
> PolicyList\_toolbar\_management\[\]=visual/\*  
> RAD schließt die Gruppe "Site und URLs"  

Standard: ein Hash mit 44: `overview=setup/menu`, `cache=setup/cache`, `audit=audit/settings`, `classes=class/grouplist`, `collected=infocollector/overview`, `contentfinder=contentfinder/search`, `unreferenced_objects=contentcheck/unreferenced`, `broken_relations=contentcheck/brokenrelations`, `storage_files=contentcheck/storage`, `maintenance=setup/maintenance`, `cronjobs=setup/cronjobs`, `logs=setup/logs` ....

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_setup]
# ein leeres "Links[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Links[]
Links[overview]=setup/menu
Links[cache]=setup/cache
Links[audit]=audit/settings
Links[classes]=class/grouplist
Links[collected]=infocollector/overview
Links[contentfinder]=contentfinder/search
Links[unreferenced_objects]=contentcheck/unreferenced
Links[broken_relations]=contentcheck/brokenrelations
Links[storage_files]=contentcheck/storage
Links[maintenance]=setup/maintenance
Links[cronjobs]=setup/cronjobs
Links[logs]=setup/logs
Links[extensions]=setup/extensions
Links[design_node]=content/view/full/58
Links[ini]=settings/view
Links[languages]=content/translations
Links[interface_languages]=setup/languages
Links[mailpreferences]=mailpreferences/admin/status
Links[notification]=notification/status
Links[preload]=setup/preload
# ... 24 weitere in der ausgelieferten Datei
*/ ?>
```

## LinkNames

Standard: ein Hash mit 18: `audit=Audit`, `contentfinder=Content finder`, `unreferenced_objects=Unreferenced objects`, `broken_relations=Broken relations`, `storage_files=Storage files`, `maintenance=Maintenance`, `cronjobs=Cronjobs`, `logs=Log files`, `design_node=Design content`, `interface_languages=Interface languages`, `preload=Preload Sites`, `user_hygiene=Check user accounts` ....

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_setup]
# ein leeres "LinkNames[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
LinkNames[]
LinkNames[audit]=Audit
LinkNames[contentfinder]=Content finder
LinkNames[unreferenced_objects]=Unreferenced objects
LinkNames[broken_relations]=Broken relations
LinkNames[storage_files]=Storage files
LinkNames[maintenance]=Maintenance
LinkNames[cronjobs]=Cronjobs
LinkNames[logs]=Log files
LinkNames[design_node]=Design content
LinkNames[interface_languages]=Interface languages
LinkNames[preload]=Preload Sites
LinkNames[user_hygiene]=Check user accounts
LinkNames[datatables]=Data tables
LinkNames[data_connections]=Connections
LinkNames[secrets]=Secrets
LinkNames[data_syncs]=Synchronisations
LinkNames[sitedata]=Site data
LinkNames[oauth_list]=oAuth admin
*/ ?>
```

## PolicyList_cache

Standard: eine Liste mit 1: `setup/managecache`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_setup]
# ein leeres "PolicyList_cache[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
PolicyList_cache[]
PolicyList_cache[]=setup/managecache
*/ ?>
```

## PolicyList_audit

Standard: eine Liste mit 1: `audit/manage`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_setup]
# ein leeres "PolicyList_audit[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
PolicyList_audit[]
PolicyList_audit[]=audit/manage
*/ ?>
```

## PolicyList_classes

Standard: eine Liste mit 1: `class/*`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_setup]
# ein leeres "PolicyList_classes[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
PolicyList_classes[]
PolicyList_classes[]=class/*
*/ ?>
```

## PolicyList_collected

Standard: eine Liste mit 1: `infocollector/read`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_setup]
# ein leeres "PolicyList_collected[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
PolicyList_collected[]
PolicyList_collected[]=infocollector/read
*/ ?>
```

## PolicyList_contentfinder

Standard: eine Liste mit 1: `contentfinder/search`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_setup]
# ein leeres "PolicyList_contentfinder[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
PolicyList_contentfinder[]
PolicyList_contentfinder[]=contentfinder/search
*/ ?>
```

## PolicyList_unreferenced_objects

Standard: eine Liste mit 1: `contentcheck/unreferenced`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_setup]
# ein leeres "PolicyList_unreferenced_objects[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
PolicyList_unreferenced_objects[]
PolicyList_unreferenced_objects[]=contentcheck/unreferenced
*/ ?>
```

## PolicyList_broken_relations

Standard: eine Liste mit 1: `contentcheck/brokenrelations`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_setup]
# ein leeres "PolicyList_broken_relations[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
PolicyList_broken_relations[]
PolicyList_broken_relations[]=contentcheck/brokenrelations
*/ ?>
```

## PolicyList_storage_files

Standard: eine Liste mit 1: `contentcheck/storage`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_setup]
# ein leeres "PolicyList_storage_files[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
PolicyList_storage_files[]
PolicyList_storage_files[]=contentcheck/storage
*/ ?>
```

## PolicyList_maintenance

Standard: eine Liste mit 1: `setup/administrate`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_setup]
# ein leeres "PolicyList_maintenance[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
PolicyList_maintenance[]
PolicyList_maintenance[]=setup/administrate
*/ ?>
```

## PolicyList_cronjobs

Standard: eine Liste mit 1: `setup/managecronjobs`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_setup]
# ein leeres "PolicyList_cronjobs[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
PolicyList_cronjobs[]
PolicyList_cronjobs[]=setup/managecronjobs
*/ ?>
```

## PolicyList_logs

Standard: eine Liste mit 1: `setup/logs`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_setup]
# ein leeres "PolicyList_logs[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
PolicyList_logs[]
PolicyList_logs[]=setup/logs
*/ ?>
```

## PolicyList_extensions

Standard: eine Liste mit 1: `setup/setup`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_setup]
# ein leeres "PolicyList_extensions[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
PolicyList_extensions[]
PolicyList_extensions[]=setup/setup
*/ ?>
```

## PolicyList_design_node

Standard: eine Liste mit 1: `58`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_setup]
# ein leeres "PolicyList_design_node[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
PolicyList_design_node[]
PolicyList_design_node[]=58
*/ ?>
```

## PolicyList_ini

Standard: eine Liste mit 1: `settings/*`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_setup]
# ein leeres "PolicyList_ini[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
PolicyList_ini[]
PolicyList_ini[]=settings/*
*/ ?>
```

## PolicyList_languages

Standard: eine Liste mit 1: `content/translations`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_setup]
# ein leeres "PolicyList_languages[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
PolicyList_languages[]
PolicyList_languages[]=content/translations
*/ ?>
```

## PolicyList_interface_languages

Standard: eine Liste mit 1: `setup/system_info`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_setup]
# ein leeres "PolicyList_interface_languages[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
PolicyList_interface_languages[]
PolicyList_interface_languages[]=setup/system_info
*/ ?>
```

## PolicyList_mailpreferences

Standard: eine Liste mit 1: `mailpreferences/administrate`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_setup]
# ein leeres "PolicyList_mailpreferences[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
PolicyList_mailpreferences[]
PolicyList_mailpreferences[]=mailpreferences/administrate
*/ ?>
```

## PolicyList_notification

Standard: eine Liste mit 1: `notification/administrate`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_setup]
# ein leeres "PolicyList_notification[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
PolicyList_notification[]
PolicyList_notification[]=notification/administrate
*/ ?>
```

## PolicyList_preload

Standard: eine Liste mit 1: `setup/preload`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_setup]
# ein leeres "PolicyList_preload[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
PolicyList_preload[]
PolicyList_preload[]=setup/preload
*/ ?>
```

## PolicyList_rss

Standard: eine Liste mit 1: `rss/edit`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_setup]
# ein leeres "PolicyList_rss[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
PolicyList_rss[]
PolicyList_rss[]=rss/edit
*/ ?>
```

## PolicyList_packages

Standard: eine Liste mit 1: `package/list`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_setup]
# ein leeres "PolicyList_packages[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
PolicyList_packages[]
PolicyList_packages[]=package/list
*/ ?>
```

## PolicyList_pdf_export

Standard: eine Liste mit 1: `pdf/edit`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_setup]
# ein leeres "PolicyList_pdf_export[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
PolicyList_pdf_export[]
PolicyList_pdf_export[]=pdf/edit
*/ ?>
```

## PolicyList_search_statistics

Standard: eine Liste mit 1: `search/*`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_setup]
# ein leeres "PolicyList_search_statistics[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
PolicyList_search_statistics[]
PolicyList_search_statistics[]=search/*
*/ ?>
```

## PolicyList_sessions

Standard: eine Liste mit 1: `setup/administrate`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_setup]
# ein leeres "PolicyList_sessions[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
PolicyList_sessions[]
PolicyList_sessions[]=setup/administrate
*/ ?>
```

## PolicyList_user_hygiene

Standard: eine Liste mit 1: `user/hygiene`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_setup]
# ein leeres "PolicyList_user_hygiene[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
PolicyList_user_hygiene[]
PolicyList_user_hygiene[]=user/hygiene
*/ ?>
```

## PolicyList_datatables

Standard: eine Liste mit 1: `datatable/read`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_setup]
# ein leeres "PolicyList_datatables[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
PolicyList_datatables[]
PolicyList_datatables[]=datatable/read
*/ ?>
```

## PolicyList_data_connections

Standard: eine Liste mit 1: `datatable/connections`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_setup]
# ein leeres "PolicyList_data_connections[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
PolicyList_data_connections[]
PolicyList_data_connections[]=datatable/connections
*/ ?>
```

## PolicyList_secrets

Standard: eine Liste mit 1: `secrets/manage`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_setup]
# ein leeres "PolicyList_secrets[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
PolicyList_secrets[]
PolicyList_secrets[]=secrets/manage
*/ ?>
```

## PolicyList_data_syncs

Standard: eine Liste mit 1: `datatable/sync`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_setup]
# ein leeres "PolicyList_data_syncs[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
PolicyList_data_syncs[]
PolicyList_data_syncs[]=datatable/sync
*/ ?>
```

## PolicyList_sitedata

Standard: eine Liste mit 1: `setup/system_info`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_setup]
# ein leeres "PolicyList_sitedata[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
PolicyList_sitedata[]
PolicyList_sitedata[]=setup/system_info
*/ ?>
```

## PolicyList_states

Standard: eine Liste mit 1: `state/administrate`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_setup]
# ein leeres "PolicyList_states[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
PolicyList_states[]
PolicyList_states[]=state/administrate
*/ ?>
```

## PolicyList_system_information

Standard: eine Liste mit 1: `setup/system_info`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_setup]
# ein leeres "PolicyList_system_information[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
PolicyList_system_information[]
PolicyList_system_information[]=setup/system_info
*/ ?>
```

## PolicyList_help_center

Standard: eine Liste mit 1: `exphelp/read`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_setup]
# ein leeres "PolicyList_help_center[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
PolicyList_help_center[]
PolicyList_help_center[]=exphelp/read
*/ ?>
```

## PolicyList_upgrade_check

Standard: eine Liste mit 1: `setup/setup`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_setup]
# ein leeres "PolicyList_upgrade_check[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
PolicyList_upgrade_check[]
PolicyList_upgrade_check[]=setup/setup
*/ ?>
```

## PolicyList_triggers

Standard: eine Liste mit 1: `trigger/*`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_setup]
# ein leeres "PolicyList_triggers[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
PolicyList_triggers[]
PolicyList_triggers[]=trigger/*
*/ ?>
```

## PolicyList_url_management

Standard: eine Liste mit 1: `url/*`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_setup]
# ein leeres "PolicyList_url_management[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
PolicyList_url_management[]
PolicyList_url_management[]=url/*
*/ ?>
```

## PolicyList_url_translator

Standard: eine Liste mit 1: `content/urltranslator`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_setup]
# ein leeres "PolicyList_url_translator[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
PolicyList_url_translator[]
PolicyList_url_translator[]=content/urltranslator
*/ ?>
```

## PolicyList_url_wildcards

Standard: eine Liste mit 1: `content/urltranslator`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_setup]
# ein leeres "PolicyList_url_wildcards[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
PolicyList_url_wildcards[]
PolicyList_url_wildcards[]=content/urltranslator
*/ ?>
```

## PolicyList_rad

Standard: eine Liste mit 1: `setup/setup`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_setup]
# ein leeres "PolicyList_rad[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
PolicyList_rad[]
PolicyList_rad[]=setup/setup
*/ ?>
```

## PolicyList_workflows

Standard: eine Liste mit 1: `workflow/*`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_setup]
# ein leeres "PolicyList_workflows[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
PolicyList_workflows[]
PolicyList_workflows[]=workflow/*
*/ ?>
```

## PolicyList_workflow_processes

Standard: eine Liste mit 1: `workflow/*`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_setup]
# ein leeres "PolicyList_workflow_processes[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
PolicyList_workflow_processes[]
PolicyList_workflow_processes[]=workflow/*
*/ ?>
```

## PolicyList_oauth_list

> Die Ansichten von oauthadmin definieren keine Richtlinienfunktionen: der eigene Zugriff des Moduls (oauthadmin/\*), wie der Kernel ihn prüft  

Standard: eine Liste mit 1: `oauthadmin/*`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_setup]
# ein leeres "PolicyList_oauth_list[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
PolicyList_oauth_list[]
PolicyList_oauth_list[]=oauthadmin/*
*/ ?>
```

## Siehe auch

- [Alle Blöcke von menu.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/menu.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
