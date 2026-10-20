---
title: audit.ini [AuditConsoleSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [AuditConsoleSettings] in audit.ini: PageSize, ReauthForManage, ReauthMinutes, MaxExportRecords, MaxBackgroundExportRecords"
keywords: Einstellung, audit.ini, AuditConsoleSettings, INI, setting, PageSize, ReauthForManage, ReauthMinutes, MaxExportRecords, MaxBackgroundExportRecords
type: generated
generated: exp:help:examples
---
# audit.ini [AuditConsoleSettings]

Der Block `[AuditConsoleSettings]` von `settings/audit.ini`, mit 5 Einstellung(en).

Bearbeiten Sie `settings/audit.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/audit.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/audit.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/audit.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## PageSize

> Zeilen pro Seite in der Zeitleiste.  

Standard: `50`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/audit.ini.append.php
[AuditConsoleSettings]
PageSize=50
*/ ?>
```

## ReauthForManage

> Q9: vor Aktionen von audit/manage in der Konsole erneut nach dem Passwort fragen („Jetzt prüfen“  
> auf dem Dashboard, die Konsole und audit/recent). Das Passwort wird wie bei der Anmeldung geprüft,  
> ohne erneute Anmeldung; festgehalten als access.session.reauth / access.session.reauth.failed.  
> enabled | disabled  

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/audit.ini.append.php
[AuditConsoleSettings]
ReauthForManage=disabled
*/ ?>
```

## ReauthMinutes

> Minuten, die ein erneut eingegebenes Passwort in dieser Sitzung gültig bleibt (nur für diesen Benutzer).  

Standard: `10`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/audit.ini.append.php
[AuditConsoleSettings]
ReauthMinutes=10
*/ ?>
```

## MaxExportRecords

> Größter Download von audit/export, in Einträgen (gestreamt, während er gelesen wird); ein größerer Filter wird abgeschnitten  
> und sagt das. „Im Hintergrund exportieren“ (der Inhaltsauftrag auditexport) schreibt stattdessen eine Datei.  

Standard: `100000`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/audit.ini.append.php
[AuditConsoleSettings]
MaxExportRecords=100000
*/ ?>
```

## MaxBackgroundExportRecords

> Größter Export im Hintergrund, in Einträgen.  

Standard: `1000000`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/audit.ini.append.php
[AuditConsoleSettings]
MaxBackgroundExportRecords=1000000
*/ ?>
```

## Siehe auch

- [Alle Blöcke von audit.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/audit.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
