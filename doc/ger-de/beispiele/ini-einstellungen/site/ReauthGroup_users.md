---
title: site.ini [ReauthGroup_users]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [ReauthGroup_users] in site.ini: Enabled, Rules"
keywords: Einstellung, site.ini, ReauthGroup_users, INI, setting, Enabled, Rules
type: generated
generated: exp:help:examples
---
# site.ini [ReauthGroup_users]

Der Block `[ReauthGroup_users]` von `settings/site.ini`, mit 2 Einstellung(en).

Bearbeiten Sie `settings/site.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/site.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/site.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/site.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Enabled

> Benutzer, Rollen und Richtlinien verwalten und neue Benutzerkonten freigeben oder ablehnen.  

Standard: `true`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[ReauthGroup_users]
Enabled=true
*/ ?>
```

## Rules

> Another user's devices in full (their addresses and browsers) and signing them out  
> Approve and Deny of a new user account waiting for approval (the workflow event  
> "Approve user accounts"); content approvals address no account and never ask here  

Standard: eine Liste mit 13: `content/edit other`, `user/password other`, `user/sessions other *`, `user/sessionreport *`, `user/setting`, `user/unactivated`, `user/hygiene`, `user/deactivate`, `user/registration`, `content/removeuserobject ConfirmButton`, `collaboration/action other CollaborationAction_Accept CollaborationAction_Deny CollaborationAction_Defer`, `role/*` ....

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[ReauthGroup_users]
# ein leeres "Rules[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Rules[]
Rules[]=content/edit other
Rules[]=user/password other
Rules[]=user/sessions other *
Rules[]=user/sessionreport *
Rules[]=user/setting
Rules[]=user/unactivated
Rules[]=user/hygiene
Rules[]=user/deactivate
Rules[]=user/registration
Rules[]=content/removeuserobject ConfirmButton
Rules[]=collaboration/action other CollaborationAction_Accept CollaborationAction_Deny CollaborationAction_Defer
Rules[]=role/*
Rules[]=oauthadmin/*
*/ ?>
```

## Siehe auch

- [Alle Blöcke von site.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/site.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
