---
title: menu.ini [Leftmenu_user_mega]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [Leftmenu_user_mega] in menu.ini: Name, Links, LinkNames, PolicyList_accounts, PolicyList_sessions, Groups, GroupNames, GroupLinks_users ..."
keywords: Einstellung, menu.ini, Leftmenu_user_mega, INI, setting, Name, Links, LinkNames, PolicyList_accounts, PolicyList_sessions, Groups, GroupNames, GroupLinks_users, GroupSections, LinkIcons, LinkDescriptions, MegaMenuLinks
type: generated
generated: exp:help:examples
---
# menu.ini [Leftmenu_user_mega]

Der Block `[Leftmenu_user_mega]` von `settings/menu.ini`, mit 12 Einstellung(en).

> Das Mega-Menü des Reiters Benutzer in admin4 (\[TopAdminMenu\] MegaMenuTabs\[\]=users, \[Topmenu\_users\] MegaMenuTemplate,  
> gezeichnet von parts/user/mega\_menu.tpl über parts/ini\_menu.tpl). Kein linkes Menü: Das linke Menü des Reiters Benutzer ist der  
> Benutzerbaum und \[Leftmenu\_user\] (parts/user/menu.tpl), und ein Baum ist keine Liste von Links. Dieser Abschnitt sammelt nur  
> die Funktionen des Reiters in Gruppen und verwendet die Menüs, die sie schon definieren, wieder, statt sie zu wiederholen:  
> GroupSections\[\] fügt \[Leftmenu\_user\] (Rollen und Richtlinien, Nicht aktivierte Benutzer) und \[Leftmenu\_my\] (die eigenen  
> Einstellungen des Benutzers) mit ihren Adressen, Richtlinien, Symbolen und Beschreibungen hinzu; seine eigenen Links sind die zwei, für die der Reiter Benutzer keinen  
> Menüeintrag hat, der Benutzerbaum und die Sitzungen. Leftmenu\_user\_\* benannt, damit er den Übersetzungskontext  
> des Menüs des Reiters teilt, design/admin/parts/user/menu. MegaMenuLinks\[\] wählt aus all diesen, einen Link eines hinzugefügten  
> Abschnitts als <group>-<key>; höchstens 12 werden angezeigt, jeder in seiner Gruppe und nur, wenn der Benutzer ihn öffnen darf.  

Bearbeiten Sie `settings/menu.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/menu.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/menu.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/menu.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Name

Standard: `users`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_user_mega]
Name=users
*/ ?>
```

## Links

> Der Benutzerbaum, die eigene Seite des Reiters Benutzer (seine PolicyList\[\] und URL\[default\]; content.ini \[NodeSettings\] UserRootNode:  
> eine Website mit einem anderen Wurzelknoten der Benutzer ändert diese beiden Zeilen, wie sie \[Topmenu\_users\] ändert)  

Standard: ein Hash mit 2: `accounts=content/view/full/5`, `sessions=setup/session`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_user_mega]
# ein leeres "Links[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Links[]
Links[accounts]=content/view/full/5
Links[sessions]=setup/session
*/ ?>
```

## LinkNames

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_user_mega]
# ein leeres "LinkNames[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
LinkNames[]
*/ ?>
```

## PolicyList_accounts

Standard: eine Liste mit 1: `5`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_user_mega]
# ein leeres "PolicyList_accounts[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
PolicyList_accounts[]
PolicyList_accounts[]=5
*/ ?>
```

## PolicyList_sessions

Standard: eine Liste mit 1: `setup/administrate`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_user_mega]
# ein leeres "PolicyList_sessions[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
PolicyList_sessions[]
PolicyList_sessions[]=setup/administrate
*/ ?>
```

## Groups

Standard: eine Liste mit 3: `users`, `access`, `account`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_user_mega]
# ein leeres "Groups[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Groups[]
Groups[]=users
Groups[]=access
Groups[]=account
*/ ?>
```

## GroupNames

Standard: ein Hash mit 3: `users=Users`, `access=Access control`, `account=My account`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_user_mega]
# ein leeres "GroupNames[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
GroupNames[]
GroupNames[users]=Users
GroupNames[access]=Access control
GroupNames[account]=My account
*/ ?>
```

## GroupLinks_users

Standard: eine Liste mit 2: `accounts`, `sessions`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_user_mega]
# ein leeres "GroupLinks_users[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
GroupLinks_users[]
GroupLinks_users[]=accounts
GroupLinks_users[]=sessions
*/ ?>
```

## GroupSections

Standard: ein Hash mit 2: `access=Leftmenu_user`, `account=Leftmenu_my`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_user_mega]
# ein leeres "GroupSections[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
GroupSections[]
GroupSections[access]=Leftmenu_user
GroupSections[account]=Leftmenu_my
*/ ?>
```

## LinkIcons

Standard: ein Hash mit 2: `accounts=people`, `sessions=person-check`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_user_mega]
# ein leeres "LinkIcons[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
LinkIcons[]
LinkIcons[accounts]=people
LinkIcons[sessions]=person-check
*/ ?>
```

## LinkDescriptions

Standard: ein Hash mit 2: `accounts=Users and user groups; create and edit accounts.`, `sessions=Who is signed in; end sessions.`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_user_mega]
# ein leeres "LinkDescriptions[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
LinkDescriptions[]
LinkDescriptions[accounts]=Users and user groups; create and edit accounts.
LinkDescriptions[sessions]=Who is signed in; end sessions.
*/ ?>
```

## MegaMenuLinks

Standard: eine Liste mit 11: `accounts`, `sessions`, `access-roles_and_policies`, `access-unactivated`, `access-user_hygiene`, `access-user_audit`, `access-session_report`, `account-edit_profile`, `account-change_password`, `account-my_notifications`, `account-my_mail`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_user_mega]
# ein leeres "MegaMenuLinks[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
MegaMenuLinks[]
MegaMenuLinks[]=accounts
MegaMenuLinks[]=sessions
MegaMenuLinks[]=access-roles_and_policies
MegaMenuLinks[]=access-unactivated
MegaMenuLinks[]=access-user_hygiene
MegaMenuLinks[]=access-user_audit
MegaMenuLinks[]=access-session_report
MegaMenuLinks[]=account-edit_profile
MegaMenuLinks[]=account-change_password
MegaMenuLinks[]=account-my_notifications
MegaMenuLinks[]=account-my_mail
*/ ?>
```

## Siehe auch

- [Alle Blöcke von menu.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/menu.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
