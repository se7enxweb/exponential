---
title: menu.ini [Leftmenu_setup]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [Leftmenu_setup] in menu.ini: Name, Groups, GroupNames, GroupTabs, GroupSections, GroupLinks_content_model, GroupLinks_site, GroupLinks_design ..."
keywords: Einstellung, menu.ini, Leftmenu_setup, INI, setting, Name, Groups, GroupNames, GroupTabs, GroupSections, GroupLinks_content_model, GroupLinks_site, GroupLinks_design, GroupLinks_workflow, GroupLinks_users, GroupLinks_system, GroupLinks_ai, GroupLinks_updates, LeadLinks, MegaMenuLinks, LinkIcons, LinkDescriptions, LinkFigureTemplates, Links, LinkNames, PolicyList_cache, PolicyList_audit, PolicyList_classes, PolicyList_collected, PolicyList_contentfinder, PolicyList_unreferenced_objects, PolicyList_broken_relations, PolicyList_storage_files, PolicyList_maintenance, PolicyList_cronjobs
type: generated
generated: exp:help:examples
---
# menu.ini [Leftmenu_setup]

Der Block `[Leftmenu_setup]` von `settings/menu.ini`, mit 58 Einstellung(en).

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

> Groups of the left menu (admin4, parts/ini\_menu.tpl): Groups\[\] in their order, GroupNames\[<group>\] the title,  
> GroupLinks\_<group>\[\] the link keys. Links in no group follow under "More", the last group. For one plain list  
> reset Groups\[\] (a line Groups\[\] with no values) in an override. Only the arrangement changes: access is checked per  
> link as before.  
> The groups ai and updates hold the links of extensions: a key listed there whose extension is not active shows  
> nothing, and a group without a link the user may open is not drawn. An extension puts its own link in a group with  
> GroupLinks\_<group>\[\]=<key> in its menu.ini.append.php (doc/eng-us/features/6.0/admin4-design.md, "Setup overview").  

Standard: eine Liste mit 9: `content_model`, `site`, `design`, `workflow`, `users`, `audit`, `system`, `ai`, `updates`.

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
Groups[]=system
Groups[]=ai
Groups[]=updates
*/ ?>
```

## GroupNames

Standard: ein Hash mit 9: `content_model=Content model`, `site=Site and URLs`, `design=Design`, `workflow=Workflows`, `users=Users and messages`, `audit=Audit`, `system=System`, `ai=AI`, `updates=Updates`.

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
GroupNames[system]=System
GroupNames[ai]=AI
GroupNames[updates]=Updates
*/ ?>
```

## GroupTabs

> A group that stands for a top tab (admin4): GroupTabs\[<group>\]=<tab> shows the group only while the tab is not in  
> the top menu (left out of \[TopAdminMenu\] Tabs\[\], named in HiddenTabs\[\], or named in SetupTabs\[\] for a user who  
> may open setup/menu); then the tab's pages (its  
> NavigationPartIdentifier) mark the Setup tab and draw this menu. While the tab is shown, the group and its links  
> are not here at all. GroupSections\[<group>\]=<section> adds the links of another left menu to the group, after its  
> GroupLinks\_<group>\[\]: that section's Links\[\], names, PolicyList\[\] and PolicyList\_<key>\[\], Enabled\_<key>\[\] and tile  
> settings (LinkIcons\[\], LinkDescriptions\[\], LinkFigureTemplates\[\]), so the tab's menu and the group stay one list.  
> A Setup link to an address the added menu has too shows in that group only.  

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

## GroupLinks_system

> extensions: expqueue (the job queue, also shipped with expai), sevenx\_dse (the database editor)  

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

> extensions: expai  

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

> extensions: ezupdate, git\_manager  

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

> Links shown before the groups and in none of them: the overview (setup/menu), every link of this menu as a tile  
> in the same groups. An extension's link in no group shows under "More" in the menu and on the overview.  

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

> admin4's mega menu (prototype, \[TopAdminMenu\] MegaMenuTabs\[\]=setup): the links of this menu it shows, by link key,  
> in this order; at most 19 (6 to 19 read best), the rest is one click away under "Go to Setup" (the overview).  
> Each link keeps its group (GroupNames\[\]) and its access checks: a link the user may not open, or whose extension is not  
> active, is left out, and so is a group with no link left. The groups follow the order of their first link here.  
> A list reset to no values shows every link of the menu, as the overview does.  
> Log files (setup/logs) took the place of Link management (url/list), which stays one click away in the  
> group "Site and URLs" of the overview. Collected information (Content model) and RSS (Site and URLs) were added  
> in 6.0.15, and so were Preload sites and Packages (Site and URLs) and EI: Models and EI: Budgets (AI) and oAuth admin  
> (Users and messages): the list holds nineteen.  

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

> The Setup overview (setup/menu) draws this menu as tiles. Per link key, all optional:  
>   LinkIcons\[<key>\]=<icon>         an icon of design.ini \[AdminDesignSettings\] IconList\[\] (a Bootstrap icon name,  
>                                   e.g. bar-chart); a name not listed there, or no entry, draws the plug icon  
>   LinkDescriptions\[<key>\]=<text>  one line on what the function does, translated in the menu's context  
>                                   (design/admin/parts/setup/menu) like LinkNames\[\]  
>   LinkFigureTemplates\[<key>\]=design:<path>.tpl  a template that draws the tile's figure, included with  
>                                   figure\_key=<key> for a shown tile only; empty or not design: draws none  
> An extension sets them for its own link in its menu.ini.append.php (doc/eng-us/features/6.0/admin4-design.md).  

Standard: ein Hash mit 53: `audit=journal-check`, `broken_relations=exclamation-diamond`, `storage_files=files`, `cache=arrow-clockwise`, `classes=diagram-3`, `collected=inbox`, `contentfinder=search`, `cronjobs=clock`, `design_node=brush`, `dse=database`, `expai_budgets=piggy-bank`, `expai_models=cpu` ....

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
# ... 33 weitere in der ausgelieferten Datei
*/ ?>
```

## LinkDescriptions

Standard: ein Hash mit 53: `audit=Who changed what: the audit settings and archives.`, `broken_relations=Visible content that links to deleted, hidden or trashed content.`, `storage_files=Files in the storage that nothing uses, and files with the wrong owner or mode.`, `cache=Clear or regenerate the caches.`, `classes=The content classes and their attributes.`, `collected=What visitors sent through forms.`, `contentfinder=Find content by its id, remote id or path.`, `cronjobs=The cronjob parts, their scripts and last runs.`, `design_node=The design's top node and the objects below it.`, `dse=Browse and query the database directly.`, `expai_budgets=AI budgets, queued requests and what they cost.`, `expai_models=The AI providers and models, and their tests.` ....

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
# ... 33 weitere in der ausgelieferten Datei
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

> No PolicyList\_overview\[\]: the view opens with any one of the setup module's functions (kernel/setup/module.php),  
> which one PolicyList entry cannot say; CheckViewAccess asks the view itself.  
> The audit settings and archives (doc/eng-us/bc/6.0/audit.md, "Placement"), only with audit/manage  
> The content finder (doc/eng-us/features/6.0/content-finder.md): find content by id or path, only with contentfinder/search  
> The content checks (doc/eng-us/features/6.0/content-checks.md): unreferenced objects and broken relations, each only  
> with its policy  
> The cronjobs console. LinkNames is what the entry reads as: without it the  
> menu falls back to the key, and "cronjobs" is not a label.  
> The log viewer (doc/eng-us/features/6.0/log-viewer.md): follow, search and cross-reference the log files, only with setup/logs  
> The Design tab's page (its URL\[default\]), in the group that stands for the tab (GroupTabs\[design\]): shown only  
> while the tab is not in the top menu  
> Removed from 4.3. Enable if you have special requirements.  
> Links\[global\_setting\]=content/edit/52  
> PolicyList\_global\_setting\[\]=54  
> The translation fallbacks of every interface language (doc/eng-us/languages.md), read only  
> The e-mail preferences for administrators: status, categories, suppression list, consent log  
> Removed from 4.3. Enable if you have special requirements.  
> Links\[look\_and\_feel\]=content/edit/54  
> PolicyList\_look\_and\_feel\[\]=56  
> The menus of the classic site designs (visual/menuconfig) are a link of the Design menu (\[Leftmenu\_design\])  
> The notification status (pending events, digest messages, the last runs of the notification cronjob, problems)  
> Preload Sites, RSS and Packages open the group "Site and URLs" (GroupLinks\_site\[\]), RAD closes it: a group draws its  
> links in the order of Links\[\], so their order here is the order of the group  
> Disabled by default in 4.3 as it is also present in user account tab.  
> Links\[roles\_and\_policies\]=role/list  
> PolicyList\_roles\_and\_policies\[\]=role/\*  
> Check user accounts (doc/eng-us/features/6.0/user-hygiene.md): never signed in, not seen for long, disabled, locked,  
> orphaned, a shared e-mail address; only with user/hygiene. The same key is in \[Leftmenu\_user\].  
> The rows of ezsite\_data, read only (Setup > Site data)  
> The Help center, the start page of the help (exphelp/dashboard)  
> Removed from 4.3. Enable if you have special requirements.  
> Links\[templates\]=visual/templatelist  
> PolicyList\_templates\[\]=visual/\*  
> Removed from 4.3. Enable if you have special requirements.  
> Links\[toolbar\_management\]=visual/toolbarlist  
> PolicyList\_toolbar\_management\[\]=visual/\*  
> RAD closes the group "Site and URLs"  

Standard: ein Hash mit 40: `overview=setup/menu`, `cache=setup/cache`, `audit=audit/settings`, `classes=class/grouplist`, `collected=infocollector/overview`, `contentfinder=contentfinder/search`, `unreferenced_objects=contentcheck/unreferenced`, `broken_relations=contentcheck/brokenrelations`, `storage_files=contentcheck/storage`, `maintenance=setup/maintenance`, `cronjobs=setup/cronjobs`, `logs=setup/logs` ....

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
# ... 20 weitere in der ausgelieferten Datei
*/ ?>
```

## LinkNames

Standard: ein Hash mit 14: `audit=Audit`, `contentfinder=Content finder`, `unreferenced_objects=Unreferenced objects`, `broken_relations=Broken relations`, `storage_files=Storage files`, `maintenance=Maintenance`, `cronjobs=Cronjobs`, `logs=Log files`, `design_node=Design content`, `interface_languages=Interface languages`, `preload=Preload Sites`, `user_hygiene=Check user accounts` ....

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

> The views of oauthadmin define no policy functions: the module's own access (oauthadmin/\*), as the kernel checks it  

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
