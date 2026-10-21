---
title: menu.ini [Leftmenu_my]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [Leftmenu_my] in menu.ini: Name, Links, LinkNames, PolicyList_dashboard, PolicyList_collaboration, PolicyList_my_pending, PolicyList_my_drafts, PolicyList_my_bookmarks ..."
keywords: Einstellung, menu.ini, Leftmenu_my, INI, setting, Name, Links, LinkNames, PolicyList_dashboard, PolicyList_collaboration, PolicyList_my_pending, PolicyList_my_drafts, PolicyList_my_bookmarks, PolicyList_my_notifications, PolicyList_help_center, PolicyList_audit, PolicyList_change_password, LinkIcons, LinkDescriptions, TrailLinks
type: generated
generated: exp:help:examples
---
# menu.ini [Leftmenu_my]

Der Block `[Leftmenu_my]` von `settings/menu.ini`, mit 15 Einstellung(en).

> Name=Media Menu  
> Links\[\]  
> LinkNames\[\]  

Bearbeiten Sie `settings/menu.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/menu.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/menu.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/menu.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Name

Standard: `dashboard`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_my]
Name=dashboard
*/ ?>
```

## Links

> Die eigene Arbeit des Benutzers, direkt nach Zusammenarbeit  
> Die E-Mail-Einstellungen: welche optionalen E-Mails der Benutzer erhält, Einwilligungsverlauf, Datendownload  
> Das Hilfecenter (exphelp/dashboard), die Startseite der Hilfe: Anleitungen, Suche, wie man einen Fehler meldet  
> Die Audit-Konsole, nur mit audit/read (doc/eng-us/bc/6.0/audit.md, "Placement"); der letzte Link des Kernels, sodass die Links,  
> die Erweiterungen anhängen, danach folgen  

Standard: ein Hash mit 11: `dashboard=content/dashboard`, `collaboration=collaboration/view/summary`, `my_pending=content/pendinglist`, `my_drafts=content/draft`, `my_bookmarks=content/bookmark`, `my_mail=mailpreferences/settings`, `my_notifications=notification/settings`, `help_center=exphelp/dashboard`, `audit=audit/console`, `change_password=user/password`, `edit_profile=user/edit/(action)/edit`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_my]
# ein leeres "Links[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Links[]
Links[dashboard]=content/dashboard
Links[collaboration]=collaboration/view/summary
Links[my_pending]=content/pendinglist
Links[my_drafts]=content/draft
Links[my_bookmarks]=content/bookmark
Links[my_mail]=mailpreferences/settings
Links[my_notifications]=notification/settings
Links[help_center]=exphelp/dashboard
Links[audit]=audit/console
Links[change_password]=user/password
Links[edit_profile]=user/edit/(action)/edit
*/ ?>
```

## LinkNames

Standard: ein Hash mit 1: `audit=Audit trail`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_my]
# ein leeres "LinkNames[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
LinkNames[]
LinkNames[audit]=Audit trail
*/ ?>
```

## PolicyList_dashboard

Standard: eine Liste mit 1: `content/dashboard`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_my]
# ein leeres "PolicyList_dashboard[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
PolicyList_dashboard[]
PolicyList_dashboard[]=content/dashboard
*/ ?>
```

## PolicyList_collaboration

Standard: eine Liste mit 1: `collaboration/*`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_my]
# ein leeres "PolicyList_collaboration[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
PolicyList_collaboration[]
PolicyList_collaboration[]=collaboration/*
*/ ?>
```

## PolicyList_my_pending

Standard: eine Liste mit 1: `content/pendinglist`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_my]
# ein leeres "PolicyList_my_pending[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
PolicyList_my_pending[]
PolicyList_my_pending[]=content/pendinglist
*/ ?>
```

## PolicyList_my_drafts

Standard: eine Liste mit 1: `content/edit`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_my]
# ein leeres "PolicyList_my_drafts[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
PolicyList_my_drafts[]
PolicyList_my_drafts[]=content/edit
*/ ?>
```

## PolicyList_my_bookmarks

Standard: eine Liste mit 1: `content/bookmark`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_my]
# ein leeres "PolicyList_my_bookmarks[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
PolicyList_my_bookmarks[]
PolicyList_my_bookmarks[]=content/bookmark
*/ ?>
```

## PolicyList_my_notifications

Standard: eine Liste mit 1: `notification/use`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_my]
# ein leeres "PolicyList_my_notifications[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
PolicyList_my_notifications[]
PolicyList_my_notifications[]=notification/use
*/ ?>
```

## PolicyList_help_center

Standard: eine Liste mit 1: `exphelp/read`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_my]
# ein leeres "PolicyList_help_center[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
PolicyList_help_center[]
PolicyList_help_center[]=exphelp/read
*/ ?>
```

## PolicyList_audit

Standard: eine Liste mit 1: `audit/read`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_my]
# ein leeres "PolicyList_audit[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
PolicyList_audit[]
PolicyList_audit[]=audit/read
*/ ?>
```

## PolicyList_change_password

Standard: eine Liste mit 1: `user/password`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_my]
# ein leeres "PolicyList_change_password[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
PolicyList_change_password[]
PolicyList_change_password[]=user/password
*/ ?>
```

## LinkIcons

> Keine PolicyList: Das Profil wird über content/edit bearbeitet, offen mit user/selfedit oder mit  
> content/edit auf dem eigenen Objekt des Benutzers; CheckViewAccess fragt das Objekt selbst (canEdit).  
> Symbole und Beschreibungen einiger dieser Links für das Mega-Menü des Reiters Benutzer in admin4 (\[Leftmenu\_user\_mega\] fügt  
> dieses Menü seiner Gruppe "account" hinzu); die Beschreibungen werden in design/admin/parts/my/menu übersetzt  
> Symbole und Beschreibungen der anderen Links für das Mega-Menü des Reiters Dashboard in admin4 (\[Leftmenu\_my\_mega\] fügt  
> dieses Menü seinen Gruppen hinzu), einschließlich der Links, die die Erweiterung expai hier anhängt; übersetzt in  
> design/admin/parts/my/menu  

Standard: ein Hash mit 15: `my_notifications=bell`, `my_mail=envelope`, `change_password=key`, `edit_profile=person-circle`, `help_center=question-circle`, `dashboard=speedometer2`, `collaboration=inbox`, `my_pending=hourglass-split`, `my_drafts=pencil-square`, `my_bookmarks=bookmark`, `expai=stars`, `expai_vector=magic` ....

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_my]
# ein leeres "LinkIcons[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
LinkIcons[]
LinkIcons[my_notifications]=bell
LinkIcons[my_mail]=envelope
LinkIcons[change_password]=key
LinkIcons[edit_profile]=person-circle
LinkIcons[help_center]=question-circle
LinkIcons[dashboard]=speedometer2
LinkIcons[collaboration]=inbox
LinkIcons[my_pending]=hourglass-split
LinkIcons[my_drafts]=pencil-square
LinkIcons[my_bookmarks]=bookmark
LinkIcons[expai]=stars
LinkIcons[expai_vector]=magic
LinkIcons[expai_chain]=link-45deg
LinkIcons[expai_log]=journal-text
LinkIcons[expai_help]=info-circle
*/ ?>
```

## LinkDescriptions

Standard: ein Hash mit 15: `my_notifications=The content you follow and how you are told.`, `my_mail=Which optional e-mail you get; your consents.`, `change_password=Set a new password for your account.`, `edit_profile=Your name, e-mail address and other details.`, `help_center=Guides, search and how to report an error.`, `dashboard=Your start page: your work, your tasks and what changed.`, `collaboration=Items that wait for your approval, and yours that wait for others.`, `my_pending=Your items that wait to be published.`, `my_drafts=The drafts you started and have not published yet.`, `my_bookmarks=The content you marked to find it again.`, `expai=The start page of Exponential Intelligence: its tools and their use.`, `expai_vector=Threads that change content with AI, under your control.` ....

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_my]
# ein leeres "LinkDescriptions[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
LinkDescriptions[]
LinkDescriptions[my_notifications]=The content you follow and how you are told.
LinkDescriptions[my_mail]=Which optional e-mail you get; your consents.
LinkDescriptions[change_password]=Set a new password for your account.
LinkDescriptions[edit_profile]=Your name, e-mail address and other details.
LinkDescriptions[help_center]=Guides, search and how to report an error.
LinkDescriptions[dashboard]=Your start page: your work, your tasks and what changed.
LinkDescriptions[collaboration]=Items that wait for your approval, and yours that wait for others.
LinkDescriptions[my_pending]=Your items that wait to be published.
LinkDescriptions[my_drafts]=The drafts you started and have not published yet.
LinkDescriptions[my_bookmarks]=The content you marked to find it again.
LinkDescriptions[expai]=The start page of Exponential Intelligence: its tools and their use.
LinkDescriptions[expai_vector]=Threads that change content with AI, under your control.
LinkDescriptions[expai_chain]=Content written in steps: the runs and their checkpoints.
LinkDescriptions[expai_log]=The AI requests, their tokens and their cost.
LinkDescriptions[expai_help]=How the AI features work and how to use them.
*/ ?>
```

## TrailLinks

> Passwort ändern und Profil bearbeiten schließen das Menü ab, nach den Links, die Erweiterungen anhängen (parts/ini\_menu.tpl)  

Standard: eine Liste mit 2: `change_password`, `edit_profile`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_my]
# ein leeres "TrailLinks[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
TrailLinks[]
TrailLinks[]=change_password
TrailLinks[]=edit_profile
*/ ?>
```

## Siehe auch

- [Alle Blöcke von menu.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/menu.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
