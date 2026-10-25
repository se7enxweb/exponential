---
title: menu.ini [Leftmenu_setup]
audience: developer, admin
category: examples
summary: "The settings of [Leftmenu_setup] in menu.ini: Name, Groups, GroupNames, GroupTabs, GroupSections, GroupLinks_content_model, GroupLinks_site, GroupLinks_design ..."
keywords: menu.ini, Leftmenu_setup, INI, setting, Name, Groups, GroupNames, GroupTabs, GroupSections, GroupLinks_content_model, GroupLinks_site, GroupLinks_design, GroupLinks_workflow, GroupLinks_users, GroupLinks_data, GroupLinks_system, GroupLinks_ai, GroupLinks_updates, LeadLinks, MegaMenuLinks, LinkIcons, LinkDescriptions, LinkFigureTemplates, Links, LinkNames, PolicyList_cache, PolicyList_audit, PolicyList_classes, PolicyList_collected, PolicyList_contentfinder, PolicyList_unreferenced_objects, PolicyList_broken_relations, PolicyList_storage_files, PolicyList_maintenance
type: generated
generated: exp:help:examples
---
# menu.ini [Leftmenu_setup]

The block `[Leftmenu_setup]` of `settings/menu.ini`, with 63 setting(s).

Never edit `settings/menu.ini` itself: an update replaces it. Put the settings you change in an override, which wins over the shipped file:

- `settings/override/menu.ini.append.php` for every siteaccess,
- `settings/siteaccess/<siteaccess>/menu.ini.append.php` for one siteaccess,
- `extension/<extension>/settings/menu.ini.append.php` in an extension (read when the extension is active).

## Name

Default: `setup`.

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

Default: a list of 10: `content_model`, `site`, `design`, `workflow`, `users`, `audit`, `data`, `system`, `ai`, `updates`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_setup]
# an empty "Groups[]" first replaces the shipped list; without it the items are added
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

Default: a hash of 10: `content_model=Content model`, `site=Site and URLs`, `design=Design`, `workflow=Workflows`, `users=Users and messages`, `audit=Audit`, `data=Data tables`, `system=System`, `ai=AI`, `updates=Updates`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_setup]
# an empty "GroupNames[]" first replaces the shipped list; without it the items are added
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

> A group that stands for a top tab (admin4): GroupTabs\[<group>\]=<tab> shows the group only while the tab is not in  
> the top menu (left out of \[TopAdminMenu\] Tabs\[\], named in HiddenTabs\[\], or named in SetupTabs\[\] for a user who  
> may open setup/menu); then the tab's pages (its  
> NavigationPartIdentifier) mark the Setup tab and draw this menu. While the tab is shown, the group and its links  
> are not here at all. GroupSections\[<group>\]=<section> adds the links of another left menu to the group, after its  
> GroupLinks\_<group>\[\]: that section's Links\[\], names, PolicyList\[\] and PolicyList\_<key>\[\], Enabled\_<key>\[\] and tile  
> settings (LinkIcons\[\], LinkDescriptions\[\], LinkFigureTemplates\[\]), so the tab's menu and the group stay one list.  
> A Setup link to an address the added menu has too shows in that group only.  

Default: a hash of 2: `design=design`, `audit=audit`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_setup]
# an empty "GroupTabs[]" first replaces the shipped list; without it the items are added
GroupTabs[]
GroupTabs[design]=design
GroupTabs[audit]=audit
*/ ?>
```

## GroupSections

Default: a hash of 2: `design=Leftmenu_design`, `audit=Leftmenu_audit`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_setup]
# an empty "GroupSections[]" first replaces the shipped list; without it the items are added
GroupSections[]
GroupSections[design]=Leftmenu_design
GroupSections[audit]=Leftmenu_audit
*/ ?>
```

## GroupLinks_content_model

Default: a list of 10: `classes`, `sections`, `states`, `languages`, `interface_languages`, `contentfinder`, `unreferenced_objects`, `broken_relations`, `storage_files`, `collected`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_setup]
# an empty "GroupLinks_content_model[]" first replaces the shipped list; without it the items are added
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

Default: a list of 9: `preload`, `rss`, `packages`, `pdf_export`, `search_statistics`, `url_management`, `url_translator`, `url_wildcards`, `rad`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_setup]
# an empty "GroupLinks_site[]" first replaces the shipped list; without it the items are added
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

Default: a list of 1: `design_node`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_setup]
# an empty "GroupLinks_design[]" first replaces the shipped list; without it the items are added
GroupLinks_design[]
GroupLinks_design[]=design_node
*/ ?>
```

## GroupLinks_workflow

Default: a list of 3: `workflows`, `workflow_processes`, `triggers`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_setup]
# an empty "GroupLinks_workflow[]" first replaces the shipped list; without it the items are added
GroupLinks_workflow[]
GroupLinks_workflow[]=workflows
GroupLinks_workflow[]=workflow_processes
GroupLinks_workflow[]=triggers
*/ ?>
```

## GroupLinks_users

Default: a list of 6: `user_hygiene`, `sessions`, `notification`, `mailpreferences`, `oauth_list`, `audit`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_setup]
# an empty "GroupLinks_users[]" first replaces the shipped list; without it the items are added
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

Default: a list of 4: `datatables`, `data_connections`, `secrets`, `data_syncs`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_setup]
# an empty "GroupLinks_data[]" first replaces the shipped list; without it the items are added
GroupLinks_data[]
GroupLinks_data[]=datatables
GroupLinks_data[]=data_connections
GroupLinks_data[]=secrets
GroupLinks_data[]=data_syncs
*/ ?>
```

## GroupLinks_system

> extensions: expqueue (the job queue, also shipped with expai), sevenx\_dse (the database editor)  

Default: a list of 12: `cache`, `system_information`, `help_center`, `upgrade_check`, `maintenance`, `cronjobs`, `logs`, `extensions`, `ini`, `sitedata`, `expqueue`, `dse`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_setup]
# an empty "GroupLinks_system[]" first replaces the shipped list; without it the items are added
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

Default: a list of 3: `expai_summary`, `expai_models`, `expai_budgets`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_setup]
# an empty "GroupLinks_ai[]" first replaces the shipped list; without it the items are added
GroupLinks_ai[]
GroupLinks_ai[]=expai_summary
GroupLinks_ai[]=expai_models
GroupLinks_ai[]=expai_budgets
*/ ?>
```

## GroupLinks_updates

> extensions: ezupdate, git\_manager  

Default: a list of 3: `ezupdate`, `git_manager`, `git_manager_backup`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_setup]
# an empty "GroupLinks_updates[]" first replaces the shipped list; without it the items are added
GroupLinks_updates[]
GroupLinks_updates[]=ezupdate
GroupLinks_updates[]=git_manager
GroupLinks_updates[]=git_manager_backup
*/ ?>
```

## LeadLinks

> Links shown before the groups and in none of them: the overview (setup/menu), every link of this menu as a tile  
> in the same groups. An extension's link in no group shows under "More" in the menu and on the overview.  

Default: a list of 1: `overview`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_setup]
# an empty "LeadLinks[]" first replaces the shipped list; without it the items are added
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

Default: a list of 19: `classes`, `sections`, `collected`, `rss`, `preload`, `packages`, `audit`, `oauth_list`, `cache`, `cronjobs`, `logs`, `extensions` ....

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_setup]
# an empty "MegaMenuLinks[]" first replaces the shipped list; without it the items are added
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

Default: a hash of 57: `audit=journal-check`, `broken_relations=exclamation-diamond`, `storage_files=files`, `cache=arrow-clockwise`, `classes=diagram-3`, `collected=inbox`, `contentfinder=search`, `cronjobs=clock`, `design_node=brush`, `dse=database`, `expai_budgets=piggy-bank`, `expai_models=cpu` ....

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_setup]
# an empty "LinkIcons[]" first replaces the shipped list; without it the items are added
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
# ... 37 more in the shipped file
*/ ?>
```

## LinkDescriptions

Default: a hash of 57: `audit=Who changed what: the audit settings and archives.`, `broken_relations=Visible content that links to deleted, hidden or trashed content.`, `storage_files=Files in the storage that nothing uses, and files with the wrong owner or mode.`, `cache=Clear or regenerate the caches.`, `classes=The content classes and their attributes.`, `collected=What visitors sent through forms.`, `contentfinder=Find content by its id, remote id or path.`, `cronjobs=The cronjob parts, their scripts and last runs.`, `design_node=The design's top node and the objects below it.`, `dse=Browse and query the database directly.`, `expai_budgets=AI budgets, queued requests and what they cost.`, `expai_models=The AI providers and models, and their tests.` ....

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_setup]
# an empty "LinkDescriptions[]" first replaces the shipped list; without it the items are added
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
# ... 37 more in the shipped file
*/ ?>
```

## LinkFigureTemplates

Default: a hash of 6: `classes=design:setup/setupmenu_figure.tpl`, `sections=design:setup/setupmenu_figure.tpl`, `languages=design:setup/setupmenu_figure.tpl`, `extensions=design:setup/setupmenu_figure.tpl`, `sessions=design:setup/setupmenu_figure.tpl`, `cronjobs=design:setup/setupmenu_figure.tpl`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_setup]
# an empty "LinkFigureTemplates[]" first replaces the shipped list; without it the items are added
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
> The data tables, the named database connections and the secret store (doc/eng-us/features/6.0/data-tables.md,  
> doc/eng-us/features/6.0/secret-store.md)  
> The rows of ezsite\_data, read only (Setup > Site data)  
> The Help center, the start page of the help (exphelp/dashboard)  
> Removed from 4.3. Enable if you have special requirements.  
> Links\[templates\]=visual/templatelist  
> PolicyList\_templates\[\]=visual/\*  
> Removed from 4.3. Enable if you have special requirements.  
> Links\[toolbar\_management\]=visual/toolbarlist  
> PolicyList\_toolbar\_management\[\]=visual/\*  
> RAD closes the group "Site and URLs"  

Default: a hash of 44: `overview=setup/menu`, `cache=setup/cache`, `audit=audit/settings`, `classes=class/grouplist`, `collected=infocollector/overview`, `contentfinder=contentfinder/search`, `unreferenced_objects=contentcheck/unreferenced`, `broken_relations=contentcheck/brokenrelations`, `storage_files=contentcheck/storage`, `maintenance=setup/maintenance`, `cronjobs=setup/cronjobs`, `logs=setup/logs` ....

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_setup]
# an empty "Links[]" first replaces the shipped list; without it the items are added
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
# ... 24 more in the shipped file
*/ ?>
```

## LinkNames

Default: a hash of 18: `audit=Audit`, `contentfinder=Content finder`, `unreferenced_objects=Unreferenced objects`, `broken_relations=Broken relations`, `storage_files=Storage files`, `maintenance=Maintenance`, `cronjobs=Cronjobs`, `logs=Log files`, `design_node=Design content`, `interface_languages=Interface languages`, `preload=Preload Sites`, `user_hygiene=Check user accounts` ....

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_setup]
# an empty "LinkNames[]" first replaces the shipped list; without it the items are added
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

Default: a list of 1: `setup/managecache`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_setup]
# an empty "PolicyList_cache[]" first replaces the shipped list; without it the items are added
PolicyList_cache[]
PolicyList_cache[]=setup/managecache
*/ ?>
```

## PolicyList_audit

Default: a list of 1: `audit/manage`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_setup]
# an empty "PolicyList_audit[]" first replaces the shipped list; without it the items are added
PolicyList_audit[]
PolicyList_audit[]=audit/manage
*/ ?>
```

## PolicyList_classes

Default: a list of 1: `class/*`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_setup]
# an empty "PolicyList_classes[]" first replaces the shipped list; without it the items are added
PolicyList_classes[]
PolicyList_classes[]=class/*
*/ ?>
```

## PolicyList_collected

Default: a list of 1: `infocollector/read`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_setup]
# an empty "PolicyList_collected[]" first replaces the shipped list; without it the items are added
PolicyList_collected[]
PolicyList_collected[]=infocollector/read
*/ ?>
```

## PolicyList_contentfinder

Default: a list of 1: `contentfinder/search`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_setup]
# an empty "PolicyList_contentfinder[]" first replaces the shipped list; without it the items are added
PolicyList_contentfinder[]
PolicyList_contentfinder[]=contentfinder/search
*/ ?>
```

## PolicyList_unreferenced_objects

Default: a list of 1: `contentcheck/unreferenced`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_setup]
# an empty "PolicyList_unreferenced_objects[]" first replaces the shipped list; without it the items are added
PolicyList_unreferenced_objects[]
PolicyList_unreferenced_objects[]=contentcheck/unreferenced
*/ ?>
```

## PolicyList_broken_relations

Default: a list of 1: `contentcheck/brokenrelations`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_setup]
# an empty "PolicyList_broken_relations[]" first replaces the shipped list; without it the items are added
PolicyList_broken_relations[]
PolicyList_broken_relations[]=contentcheck/brokenrelations
*/ ?>
```

## PolicyList_storage_files

Default: a list of 1: `contentcheck/storage`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_setup]
# an empty "PolicyList_storage_files[]" first replaces the shipped list; without it the items are added
PolicyList_storage_files[]
PolicyList_storage_files[]=contentcheck/storage
*/ ?>
```

## PolicyList_maintenance

Default: a list of 1: `setup/administrate`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_setup]
# an empty "PolicyList_maintenance[]" first replaces the shipped list; without it the items are added
PolicyList_maintenance[]
PolicyList_maintenance[]=setup/administrate
*/ ?>
```

## PolicyList_cronjobs

Default: a list of 1: `setup/managecronjobs`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_setup]
# an empty "PolicyList_cronjobs[]" first replaces the shipped list; without it the items are added
PolicyList_cronjobs[]
PolicyList_cronjobs[]=setup/managecronjobs
*/ ?>
```

## PolicyList_logs

Default: a list of 1: `setup/logs`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_setup]
# an empty "PolicyList_logs[]" first replaces the shipped list; without it the items are added
PolicyList_logs[]
PolicyList_logs[]=setup/logs
*/ ?>
```

## PolicyList_extensions

Default: a list of 1: `setup/setup`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_setup]
# an empty "PolicyList_extensions[]" first replaces the shipped list; without it the items are added
PolicyList_extensions[]
PolicyList_extensions[]=setup/setup
*/ ?>
```

## PolicyList_design_node

Default: a list of 1: `58`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_setup]
# an empty "PolicyList_design_node[]" first replaces the shipped list; without it the items are added
PolicyList_design_node[]
PolicyList_design_node[]=58
*/ ?>
```

## PolicyList_ini

Default: a list of 1: `settings/*`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_setup]
# an empty "PolicyList_ini[]" first replaces the shipped list; without it the items are added
PolicyList_ini[]
PolicyList_ini[]=settings/*
*/ ?>
```

## PolicyList_languages

Default: a list of 1: `content/translations`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_setup]
# an empty "PolicyList_languages[]" first replaces the shipped list; without it the items are added
PolicyList_languages[]
PolicyList_languages[]=content/translations
*/ ?>
```

## PolicyList_interface_languages

Default: a list of 1: `setup/system_info`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_setup]
# an empty "PolicyList_interface_languages[]" first replaces the shipped list; without it the items are added
PolicyList_interface_languages[]
PolicyList_interface_languages[]=setup/system_info
*/ ?>
```

## PolicyList_mailpreferences

Default: a list of 1: `mailpreferences/administrate`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_setup]
# an empty "PolicyList_mailpreferences[]" first replaces the shipped list; without it the items are added
PolicyList_mailpreferences[]
PolicyList_mailpreferences[]=mailpreferences/administrate
*/ ?>
```

## PolicyList_notification

Default: a list of 1: `notification/administrate`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_setup]
# an empty "PolicyList_notification[]" first replaces the shipped list; without it the items are added
PolicyList_notification[]
PolicyList_notification[]=notification/administrate
*/ ?>
```

## PolicyList_preload

Default: a list of 1: `setup/preload`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_setup]
# an empty "PolicyList_preload[]" first replaces the shipped list; without it the items are added
PolicyList_preload[]
PolicyList_preload[]=setup/preload
*/ ?>
```

## PolicyList_rss

Default: a list of 1: `rss/edit`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_setup]
# an empty "PolicyList_rss[]" first replaces the shipped list; without it the items are added
PolicyList_rss[]
PolicyList_rss[]=rss/edit
*/ ?>
```

## PolicyList_packages

Default: a list of 1: `package/list`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_setup]
# an empty "PolicyList_packages[]" first replaces the shipped list; without it the items are added
PolicyList_packages[]
PolicyList_packages[]=package/list
*/ ?>
```

## PolicyList_pdf_export

Default: a list of 1: `pdf/edit`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_setup]
# an empty "PolicyList_pdf_export[]" first replaces the shipped list; without it the items are added
PolicyList_pdf_export[]
PolicyList_pdf_export[]=pdf/edit
*/ ?>
```

## PolicyList_search_statistics

Default: a list of 1: `search/*`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_setup]
# an empty "PolicyList_search_statistics[]" first replaces the shipped list; without it the items are added
PolicyList_search_statistics[]
PolicyList_search_statistics[]=search/*
*/ ?>
```

## PolicyList_sessions

Default: a list of 1: `setup/administrate`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_setup]
# an empty "PolicyList_sessions[]" first replaces the shipped list; without it the items are added
PolicyList_sessions[]
PolicyList_sessions[]=setup/administrate
*/ ?>
```

## PolicyList_user_hygiene

Default: a list of 1: `user/hygiene`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_setup]
# an empty "PolicyList_user_hygiene[]" first replaces the shipped list; without it the items are added
PolicyList_user_hygiene[]
PolicyList_user_hygiene[]=user/hygiene
*/ ?>
```

## PolicyList_datatables

Default: a list of 1: `datatable/read`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_setup]
# an empty "PolicyList_datatables[]" first replaces the shipped list; without it the items are added
PolicyList_datatables[]
PolicyList_datatables[]=datatable/read
*/ ?>
```

## PolicyList_data_connections

Default: a list of 1: `datatable/connections`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_setup]
# an empty "PolicyList_data_connections[]" first replaces the shipped list; without it the items are added
PolicyList_data_connections[]
PolicyList_data_connections[]=datatable/connections
*/ ?>
```

## PolicyList_secrets

Default: a list of 1: `secrets/manage`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_setup]
# an empty "PolicyList_secrets[]" first replaces the shipped list; without it the items are added
PolicyList_secrets[]
PolicyList_secrets[]=secrets/manage
*/ ?>
```

## PolicyList_data_syncs

Default: a list of 1: `datatable/sync`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_setup]
# an empty "PolicyList_data_syncs[]" first replaces the shipped list; without it the items are added
PolicyList_data_syncs[]
PolicyList_data_syncs[]=datatable/sync
*/ ?>
```

## PolicyList_sitedata

Default: a list of 1: `setup/system_info`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_setup]
# an empty "PolicyList_sitedata[]" first replaces the shipped list; without it the items are added
PolicyList_sitedata[]
PolicyList_sitedata[]=setup/system_info
*/ ?>
```

## PolicyList_states

Default: a list of 1: `state/administrate`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_setup]
# an empty "PolicyList_states[]" first replaces the shipped list; without it the items are added
PolicyList_states[]
PolicyList_states[]=state/administrate
*/ ?>
```

## PolicyList_system_information

Default: a list of 1: `setup/system_info`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_setup]
# an empty "PolicyList_system_information[]" first replaces the shipped list; without it the items are added
PolicyList_system_information[]
PolicyList_system_information[]=setup/system_info
*/ ?>
```

## PolicyList_help_center

Default: a list of 1: `exphelp/read`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_setup]
# an empty "PolicyList_help_center[]" first replaces the shipped list; without it the items are added
PolicyList_help_center[]
PolicyList_help_center[]=exphelp/read
*/ ?>
```

## PolicyList_upgrade_check

Default: a list of 1: `setup/setup`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_setup]
# an empty "PolicyList_upgrade_check[]" first replaces the shipped list; without it the items are added
PolicyList_upgrade_check[]
PolicyList_upgrade_check[]=setup/setup
*/ ?>
```

## PolicyList_triggers

Default: a list of 1: `trigger/*`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_setup]
# an empty "PolicyList_triggers[]" first replaces the shipped list; without it the items are added
PolicyList_triggers[]
PolicyList_triggers[]=trigger/*
*/ ?>
```

## PolicyList_url_management

Default: a list of 1: `url/*`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_setup]
# an empty "PolicyList_url_management[]" first replaces the shipped list; without it the items are added
PolicyList_url_management[]
PolicyList_url_management[]=url/*
*/ ?>
```

## PolicyList_url_translator

Default: a list of 1: `content/urltranslator`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_setup]
# an empty "PolicyList_url_translator[]" first replaces the shipped list; without it the items are added
PolicyList_url_translator[]
PolicyList_url_translator[]=content/urltranslator
*/ ?>
```

## PolicyList_url_wildcards

Default: a list of 1: `content/urltranslator`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_setup]
# an empty "PolicyList_url_wildcards[]" first replaces the shipped list; without it the items are added
PolicyList_url_wildcards[]
PolicyList_url_wildcards[]=content/urltranslator
*/ ?>
```

## PolicyList_rad

Default: a list of 1: `setup/setup`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_setup]
# an empty "PolicyList_rad[]" first replaces the shipped list; without it the items are added
PolicyList_rad[]
PolicyList_rad[]=setup/setup
*/ ?>
```

## PolicyList_workflows

Default: a list of 1: `workflow/*`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_setup]
# an empty "PolicyList_workflows[]" first replaces the shipped list; without it the items are added
PolicyList_workflows[]
PolicyList_workflows[]=workflow/*
*/ ?>
```

## PolicyList_workflow_processes

Default: a list of 1: `workflow/*`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_setup]
# an empty "PolicyList_workflow_processes[]" first replaces the shipped list; without it the items are added
PolicyList_workflow_processes[]
PolicyList_workflow_processes[]=workflow/*
*/ ?>
```

## PolicyList_oauth_list

> The views of oauthadmin define no policy functions: the module's own access (oauthadmin/\*), as the kernel checks it  

Default: a list of 1: `oauthadmin/*`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_setup]
# an empty "PolicyList_oauth_list[]" first replaces the shipped list; without it the items are added
PolicyList_oauth_list[]
PolicyList_oauth_list[]=oauthadmin/*
*/ ?>
```

## Related

- [All blocks of menu.ini](README.md)
- [All INI files](../README.md)
- [The settings page](../../../guides/settings-page.md)

---

Generated by `./console exp:help:examples` from `settings/menu.ini`. Do not edit this page: change the source and generate again ([how](../../README.md#how-the-generated-pages-are-made)). Free documentation: [GFDL 1.3 or later](../../../help/free-documentation.md).
