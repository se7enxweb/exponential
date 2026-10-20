---
title: audit.ini [AuditSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [AuditSettings] in audit.ini: Audit, LogDir, AuditFileNames, OnWriteFailure, RefuseExemptViews"
keywords: Einstellung, audit.ini, AuditSettings, INI, setting, Audit, LogDir, AuditFileNames, OnWriteFailure, RefuseExemptViews
type: generated
generated: exp:help:examples
---
# audit.ini [AuditSettings]

Der Block `[AuditSettings]` von `settings/audit.ini`, mit 5 Einstellung(en).

Bearbeiten Sie `settings/audit.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/audit.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/audit.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/audit.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Audit

> enabled | disabled. On in every installation by convention (unlike the 4.x releases).  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/audit.ini.append.php
[AuditSettings]
Audit=enabled
*/ ?>
```

## LogDir

> Where the live channel files are written, relative to the site's var directory  
> (\[FileSettings\] VarDir of site.ini). Created with the site user's owner and group.  

Standard: `log/audit`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/audit.ini.append.php
[AuditSettings]
LogDir=log/audit
*/ ?>
```

## AuditFileNames

> 4.x compatibility: the old per-event file names. Kept so the old names resolve as  
> aliases in filters and so a site's own names keep a home; see \[AuditCompatSettings\].  

Standard: ein Hash mit 13: `user-login=login.log`, `user-failed-login=failed_login.log`, `content-delete=content_delete.log`, `content-move=content_move.log`, `content-hide=content_hide.log`, `role-change=role_change.log`, `role-assign=role_assign.log`, `section-assign=section_assign.log`, `state-assign=state_assign.log`, `order-delete=order_delete.log`, `location-assign=location_assign.log`, `location-remove=location_remove.log` ....

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/audit.ini.append.php
[AuditSettings]
# ein leeres "AuditFileNames[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
AuditFileNames[]
AuditFileNames[user-login]=login.log
AuditFileNames[user-failed-login]=failed_login.log
AuditFileNames[content-delete]=content_delete.log
AuditFileNames[content-move]=content_move.log
AuditFileNames[content-hide]=content_hide.log
AuditFileNames[role-change]=role_change.log
AuditFileNames[role-assign]=role_assign.log
AuditFileNames[section-assign]=section_assign.log
AuditFileNames[state-assign]=state_assign.log
AuditFileNames[order-delete]=order_delete.log
AuditFileNames[location-assign]=location_assign.log
AuditFileNames[location-remove]=location_remove.log
AuditFileNames[main-node-update]=location_main_update.log
*/ ?>
```

## OnWriteFailure

> What to do when the audit cannot write (disk full, permissions):  
>   continue  the request goes on; the failure is in error.log (AUDIT-UNWRITTEN) and the  
>             dashboard warns  
>   refuse    actions of the "always written at once" kind (ImmediateEvents\[\]) are refused  
>             while their channel cannot be written, so nothing security-relevant happens  
>             unrecorded: a POST to a view of AlwaysModules\[\] by a signed-in user (503 with  
>             an error page), a settings write (exp:ini, debug bar), and exp:audit's manage  
>             actions (exit 2). Pages, content editing, GET requests, anonymous visitors,  
>             reading and verifying the audit are never refused. Each refusal is logged as  
>             AUDIT-REFUSED in error.log. Guide: "When the audit cannot write".  
> Default: continue (a full disk must not take the site down).  

Standard: `continue`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/audit.ini.append.php
[AuditSettings]
OnWriteFailure=continue
*/ ?>
```

## RefuseExemptViews

> With refuse: module/view POSTs that are never refused, so people can still sign in and  
> out (their records go to error.log as AUDIT-UNWRITTEN).  

Standard: eine Liste mit 2: `user/login`, `user/logout`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/audit.ini.append.php
[AuditSettings]
# ein leeres "RefuseExemptViews[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
RefuseExemptViews[]
RefuseExemptViews[]=user/login
RefuseExemptViews[]=user/logout
*/ ?>
```

## Siehe auch

- [Alle Blöcke von audit.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/audit.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
