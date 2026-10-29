---
title: menu.ini [Leftmenu_user]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [Leftmenu_user] in menu.ini: Name, Links, LinkNames, PolicyList_roles_and_policies, PolicyList_unactivated, PolicyList_user_hygiene, PolicyList_user_audit, PolicyList_session_report ..."
keywords: Einstellung, menu.ini, Leftmenu_user, INI, setting, Name, Links, LinkNames, PolicyList_roles_and_policies, PolicyList_unactivated, PolicyList_user_hygiene, PolicyList_user_audit, PolicyList_session_report, LinkIcons, LinkDescriptions
type: generated
generated: exp:help:examples
---
# menu.ini [Leftmenu_user]

Der Block `[Leftmenu_user]` von `settings/menu.ini`, mit 10 Einstellung(en).

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
> Das Benutzer-Audit-Log (doc/eng-us/features/6.0/user-audit.md): wer pro Konto etwas geändert hat, angemeldet war oder abgewiesen wurde  
> Der Anmeldebericht (doc/eng-us/features/6.0/user-sessions.md): wer angemeldet ist, Anmeldungen und Fehlschläge pro Tag,  
> Benutzer überall abmelden; funktioniert mit jedem Session-Handler  

Standard: ein Hash mit 5: `roles_and_policies=role/list`, `unactivated=user/unactivated`, `user_hygiene=user/hygiene`, `user_audit=user/audit`, `session_report=user/sessionreport`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_user]
# ein leeres "Links[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Links[]
Links[roles_and_policies]=role/list
Links[unactivated]=user/unactivated
Links[user_hygiene]=user/hygiene
Links[user_audit]=user/audit
Links[session_report]=user/sessionreport
*/ ?>
```

## LinkNames

Standard: ein Hash mit 3: `user_hygiene=Check user accounts`, `user_audit=User audit log`, `session_report=Sign-in report`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_user]
# ein leeres "LinkNames[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
LinkNames[]
LinkNames[user_hygiene]=Check user accounts
LinkNames[user_audit]=User audit log
LinkNames[session_report]=Sign-in report
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

## PolicyList_user_audit

Standard: eine Liste mit 1: `user/audit`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_user]
# ein leeres "PolicyList_user_audit[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
PolicyList_user_audit[]
PolicyList_user_audit[]=user/audit
*/ ?>
```

## PolicyList_session_report

Standard: eine Liste mit 1: `user/sessions_manage`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_user]
# ein leeres "PolicyList_session_report[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
PolicyList_session_report[]
PolicyList_session_report[]=user/sessions_manage
*/ ?>
```

## LinkIcons

> Symbole und Beschreibungen dieser Links im Mega-Menü des Reiters Benutzer in admin4 (\[Leftmenu\_user\_mega\] fügt dieses Menü  
> seiner Gruppe "access" hinzu); die Beschreibungen werden in design/admin/parts/user/menu übersetzt  

Standard: ein Hash mit 5: `roles_and_policies=shield-lock`, `unactivated=person-slash`, `user_hygiene=person-badge`, `user_audit=journal-text`, `session_report=bar-chart-line`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_user]
# ein leeres "LinkIcons[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
LinkIcons[]
LinkIcons[roles_and_policies]=shield-lock
LinkIcons[unactivated]=person-slash
LinkIcons[user_hygiene]=person-badge
LinkIcons[user_audit]=journal-text
LinkIcons[session_report]=bar-chart-line
*/ ?>
```

## LinkDescriptions

Standard: ein Hash mit 5: `roles_and_policies=Who may do what, and where.`, `unactivated=Accounts not yet activated; activate or remove them.`, `user_hygiene=Accounts nobody uses or that need a look; disable, unlock or export them.`, `user_audit=Who changed, signed in or was refused, per account; filter and export.`, `session_report=Who is signed in, sign-ins and failures per day; sign a user out everywhere.`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_user]
# ein leeres "LinkDescriptions[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
LinkDescriptions[]
LinkDescriptions[roles_and_policies]=Who may do what, and where.
LinkDescriptions[unactivated]=Accounts not yet activated; activate or remove them.
LinkDescriptions[user_hygiene]=Accounts nobody uses or that need a look; disable, unlock or export them.
LinkDescriptions[user_audit]=Who changed, signed in or was refused, per account; filter and export.
LinkDescriptions[session_report]=Who is signed in, sign-ins and failures per day; sign a user out everywhere.
*/ ?>
```

## Siehe auch

- [Alle Blöcke von menu.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/menu.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
