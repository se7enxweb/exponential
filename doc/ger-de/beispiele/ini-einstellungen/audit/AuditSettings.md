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

> enabled | disabled. Vereinbarungsgemäß in jeder Installation an (anders als in den Releases 4.x).  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/audit.ini.append.php
[AuditSettings]
Audit=enabled
*/ ?>
```

## LogDir

> Wohin die Live-Dateien der Kanäle geschrieben werden, relativ zum var-Verzeichnis der Website  
> (\[FileSettings\] VarDir von site.ini). Mit Eigentümer und Gruppe des Website-Benutzers angelegt.  

Standard: `log/audit`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/audit.ini.append.php
[AuditSettings]
LogDir=log/audit
*/ ?>
```

## AuditFileNames

> Kompatibilität mit 4.x: die alten Dateinamen pro Ereignis. Beibehalten, damit die alten Namen in Filtern  
> als Aliase aufgelöst werden und die eigenen Namen einer Website ein Zuhause behalten; siehe \[AuditCompatSettings\].  

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

> Was zu tun ist, wenn das Audit nicht schreiben kann (Festplatte voll, Berechtigungen):  
>   continue  die Anfrage läuft weiter; der Fehler steht in error.log (AUDIT-UNWRITTEN), und das  
>             Dashboard warnt  
>   refuse    Aktionen der Art „immer sofort geschrieben“ (ImmediateEvents\[\]) werden abgelehnt,  
>             solange ihr Kanal nicht geschrieben werden kann, damit nichts Sicherheitsrelevantes  
>             unprotokolliert geschieht: ein POST an eine Ansicht aus AlwaysModules\[\] durch einen angemeldeten Benutzer (503 mit  
>             einer Fehlerseite), ein Schreibzugriff auf Einstellungen (exp:ini, Debug-Leiste) und die Verwaltungsaktionen  
>             von exp:audit (Exit-Code 2). Seiten, Bearbeiten von Inhalten, GET-Anfragen, anonyme Besucher,  
>             Lesen und Prüfen des Audits werden nie abgelehnt. Jede Ablehnung wird als  
>             AUDIT-REFUSED in error.log protokolliert. Anleitung: "When the audit cannot write".  
> Standard: continue (eine volle Festplatte darf die Website nicht lahmlegen).  

Standard: `continue`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/audit.ini.append.php
[AuditSettings]
OnWriteFailure=continue
*/ ?>
```

## RefuseExemptViews

> Mit refuse: POSTs an Modul/Ansicht, die nie abgelehnt werden, damit man sich weiterhin an- und  
> abmelden kann (ihre Einträge gehen als AUDIT-UNWRITTEN nach error.log).  

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
