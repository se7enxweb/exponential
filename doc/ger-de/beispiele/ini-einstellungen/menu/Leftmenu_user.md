---
title: menu.ini [Leftmenu_user]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [Leftmenu_user] in menu.ini: Name, Links, LinkNames, PolicyList_roles_and_policies, PolicyList_unactivated, PolicyList_user_hygiene, LinkIcons, LinkDescriptions"
keywords: Einstellung, menu.ini, Leftmenu_user, INI, setting, Name, Links, LinkNames, PolicyList_roles_and_policies, PolicyList_unactivated, PolicyList_user_hygiene, LinkIcons, LinkDescriptions
type: generated
generated: exp:help:examples
---
# menu.ini [Leftmenu_user]

Der Block `[Leftmenu_user]` von `settings/menu.ini`, mit 8 Einstellung(en).

Bearbeiten Sie `settings/menu.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/menu.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/menu.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/menu.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Name

Standard: `access_controll`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_user]
Name=access_controll
*/ ?>
```

## Links

> Benutzerkonten prüfen (doc/eng-us/features/6.0/user-hygiene.md), auch eine Kachel der Setup-Übersicht unter demselben Schlüssel  

Standard: ein Hash mit 3: `roles_and_policies=role/list`, `unactivated=user/unactivated`, `user_hygiene=user/hygiene`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_user]
# ein leeres "Links[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Links[]
Links[roles_and_policies]=role/list
Links[unactivated]=user/unactivated
Links[user_hygiene]=user/hygiene
*/ ?>
```

## LinkNames

Standard: ein Hash mit 1: `user_hygiene=Check user accounts`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_user]
# ein leeres "LinkNames[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
LinkNames[]
LinkNames[user_hygiene]=Check user accounts
*/ ?>
```

## PolicyList_roles_and_policies

Standard: eine Liste mit 1: `role/*`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_user]
# ein leeres "PolicyList_roles_and_policies[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
PolicyList_roles_and_policies[]
PolicyList_roles_and_policies[]=role/*
*/ ?>
```

## PolicyList_unactivated

Standard: eine Liste mit 1: `user/activation`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_user]
# ein leeres "PolicyList_unactivated[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
PolicyList_unactivated[]
PolicyList_unactivated[]=user/activation
*/ ?>
```

## PolicyList_user_hygiene

Standard: eine Liste mit 1: `user/hygiene`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_user]
# ein leeres "PolicyList_user_hygiene[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
PolicyList_user_hygiene[]
PolicyList_user_hygiene[]=user/hygiene
*/ ?>
```

## LinkIcons

> Symbole und Beschreibungen dieser Links im Mega-Menü des Reiters Benutzer in admin4 (\[Leftmenu\_user\_mega\] fügt dieses Menü  
> seiner Gruppe "access" hinzu); die Beschreibungen werden in design/admin/parts/user/menu übersetzt  

Standard: ein Hash mit 3: `roles_and_policies=shield-lock`, `unactivated=person-slash`, `user_hygiene=person-badge`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_user]
# ein leeres "LinkIcons[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
LinkIcons[]
LinkIcons[roles_and_policies]=shield-lock
LinkIcons[unactivated]=person-slash
LinkIcons[user_hygiene]=person-badge
*/ ?>
```

## LinkDescriptions

Standard: ein Hash mit 3: `roles_and_policies=Who may do what, and where.`, `unactivated=Accounts not yet activated; activate or remove them.`, `user_hygiene=Accounts nobody uses or that need a look; disable, unlock or export them.`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_user]
# ein leeres "LinkDescriptions[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
LinkDescriptions[]
LinkDescriptions[roles_and_policies]=Who may do what, and where.
LinkDescriptions[unactivated]=Accounts not yet activated; activate or remove them.
LinkDescriptions[user_hygiene]=Accounts nobody uses or that need a look; disable, unlock or export them.
*/ ?>
```

## Siehe auch

- [Alle Blöcke von menu.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/menu.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
