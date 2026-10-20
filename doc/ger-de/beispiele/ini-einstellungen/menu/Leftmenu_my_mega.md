---
title: menu.ini [Leftmenu_my_mega]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [Leftmenu_my_mega] in menu.ini: Name, Links, LinkNames, Groups, GroupNames, GroupSections, MegaMenuLinks"
keywords: Einstellung, menu.ini, Leftmenu_my_mega, INI, setting, Name, Links, LinkNames, Groups, GroupNames, GroupSections, MegaMenuLinks
type: generated
generated: exp:help:examples
---
# menu.ini [Leftmenu_my_mega]

Der Block `[Leftmenu_my_mega]` von `settings/menu.ini`, mit 7 Einstellung(en).

> Das Mega-Menü des Reiters Dashboard in admin4 (\[TopAdminMenu\] MegaMenuTabs\[\]=dashboard, \[Topmenu\_dashboard\]  
> MegaMenuTemplate, gezeichnet von parts/my/mega\_menu.tpl über parts/ini\_menu.tpl). Kein linkes Menü: Das linke Menü des Reiters Dashboard  
> ist \[Leftmenu\_my\], eine Liste. Dieser Abschnitt sortiert nur die Funktionen des Reiters in Gruppen und verwendet die Menüs,  
> die sie definieren, wieder, statt sie zu wiederholen: Jede Gruppe fügt \[Leftmenu\_my\] hinzu (GroupSections\[\]), mit den Links, die die  
> Erweiterungen dort anhängen (Exponential Intelligence: expai, expai\_vector, expai\_chain, expai\_log, expai\_help; das  
> Hilfecenter, das eine Website als overview-help\_center zur Auswahl hinzufügt, sobald sein Link da ist), die Gruppe "ei" fügt auch \[Leftmenu\_design\] hinzu (Fehlender Alternativtext, expai\_alttext), die Gruppe "activity"  
> fügt \[Leftmenu\_audit\] hinzu (die Audit-Konsole und die KI-Ereignisse). Ihre Adressen, Richtlinien, Symbole und Beschreibungen sind  
> die ihrer Menüs, sodass ein Link einer Erweiterung, die nicht aktiv ist, oder einer, den der Benutzer nicht öffnen darf, nicht angezeigt wird.  
> Leftmenu\_my\_\* benannt, damit er den Übersetzungskontext des Menüs des Reiters teilt, design/admin/parts/my/menu.  
> MegaMenuLinks\[\] wählt daraus als <group>-<key>, in dieser Reihenfolge (die Gruppen folgen der Reihenfolge ihres ersten Links:  
> das Audit vor Exponential Intelligence, Passwort ändern und Profil bearbeiten zuletzt, wie im linken Menü); höchstens 19  
> werden angezeigt. Mein Warenkorb und die bevorzugte Währung bleiben im linken Menü.  

Bearbeiten Sie `settings/menu.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/menu.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/menu.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/menu.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Name

Standard: `dashboard`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_my_mega]
Name=dashboard
*/ ?>
```

## Links

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_my_mega]
# ein leeres "Links[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Links[]
*/ ?>
```

## LinkNames

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_my_mega]
# ein leeres "LinkNames[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
LinkNames[]
*/ ?>
```

## Groups

Standard: eine Liste mit 5: `overview`, `work`, `activity`, `ei`, `account`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_my_mega]
# ein leeres "Groups[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Groups[]
Groups[]=overview
Groups[]=work
Groups[]=activity
Groups[]=ei
Groups[]=account
*/ ?>
```

## GroupNames

Standard: ein Hash mit 5: `overview=Overview`, `work=Your work`, `activity=Activity and audit`, `ei=Exponential Intelligence`, `account=My account`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_my_mega]
# ein leeres "GroupNames[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
GroupNames[]
GroupNames[overview]=Overview
GroupNames[work]=Your work
GroupNames[activity]=Activity and audit
GroupNames[ei]=Exponential Intelligence
GroupNames[account]=My account
*/ ?>
```

## GroupSections

Standard: ein Hash mit 5: `overview=Leftmenu_my`, `work=Leftmenu_my`, `activity=Leftmenu_audit`, `ei=Leftmenu_my;Leftmenu_design`, `account=Leftmenu_my`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_my_mega]
# ein leeres "GroupSections[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
GroupSections[]
GroupSections[overview]=Leftmenu_my
GroupSections[work]=Leftmenu_my
GroupSections[activity]=Leftmenu_audit
GroupSections[ei]=Leftmenu_my;Leftmenu_design
GroupSections[account]=Leftmenu_my
*/ ?>
```

## MegaMenuLinks

Standard: eine Liste mit 18: `overview-dashboard`, `overview-help_center`, `work-my_drafts`, `work-my_pending`, `work-collaboration`, `work-my_bookmarks`, `activity-console`, `activity-expai_events`, `ei-expai`, `ei-expai_vector`, `ei-expai_chain`, `ei-expai_alttext` ....

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_my_mega]
# ein leeres "MegaMenuLinks[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
MegaMenuLinks[]
MegaMenuLinks[]=overview-dashboard
MegaMenuLinks[]=overview-help_center
MegaMenuLinks[]=work-my_drafts
MegaMenuLinks[]=work-my_pending
MegaMenuLinks[]=work-collaboration
MegaMenuLinks[]=work-my_bookmarks
MegaMenuLinks[]=activity-console
MegaMenuLinks[]=activity-expai_events
MegaMenuLinks[]=ei-expai
MegaMenuLinks[]=ei-expai_vector
MegaMenuLinks[]=ei-expai_chain
MegaMenuLinks[]=ei-expai_alttext
MegaMenuLinks[]=ei-expai_log
MegaMenuLinks[]=ei-expai_help
MegaMenuLinks[]=account-my_notifications
MegaMenuLinks[]=account-my_mail
MegaMenuLinks[]=account-change_password
MegaMenuLinks[]=account-edit_profile
*/ ?>
```

## Siehe auch

- [Alle Blöcke von menu.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/menu.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
