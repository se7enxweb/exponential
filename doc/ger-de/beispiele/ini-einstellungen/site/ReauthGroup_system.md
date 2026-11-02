---
title: site.ini [ReauthGroup_system]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [ReauthGroup_system] in site.ini: Enabled, Rules"
keywords: Einstellung, site.ini, ReauthGroup_system, INI, setting, Enabled, Rules
type: generated
generated: exp:help:examples
---
# site.ini [ReauthGroup_system]

Der Block `[ReauthGroup_system]` von `settings/site.ini`, mit 2 Einstellung(en).

Bearbeiten Sie `settings/site.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/site.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/site.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/site.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Enabled

> Systemeinstellungen und gefährliche Setup-Aktionen.  

Standard: `true`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[ReauthGroup_system]
Enabled=true
*/ ?>
```

## Rules

> Migrations: a run, a resume or a simulation, a rollback, and (later) the editor and the generators; seeing them  
> (status, history, log, the dry run) is never asked  
> Remotes and deploys: saving or removing a remote, a deploy (not its preflight), approving the next stage of a  
> staged deploy, rolling a deploy back; the lists and Test connection are never asked  
> a site-wide recording window (every user's changes); a user's own recording is not asked  
> Static publishing: a publish, a rollback, saving a target, the deploy key and the switch; seeing, building and the  
> preview are never asked  

Standard: eine Liste mit 34: `setup/*`, `!setup/info`, `!setup/logs`, `!setup/logshealth`, `!setup/logsdigest`, `!setup/logsfeed`, `!setup/templatelist`, `!setup/templateview`, `!setup/menu`, `!setup/preloadstream`, `!setup/cronjobsstream`, `!setup/staticcachestream` ....

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[ReauthGroup_system]
# ein leeres "Rules[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Rules[]
Rules[]=setup/*
Rules[]=!setup/info
Rules[]=!setup/logs
Rules[]=!setup/logshealth
Rules[]=!setup/logsdigest
Rules[]=!setup/logsfeed
Rules[]=!setup/templatelist
Rules[]=!setup/templateview
Rules[]=!setup/menu
Rules[]=!setup/preloadstream
Rules[]=!setup/cronjobsstream
Rules[]=!setup/staticcachestream
Rules[]=settings/edit
Rules[]=package/*
Rules[]=package/upload *
Rules[]=secrets/*
Rules[]=migration/run
Rules[]=migration/rollback
Rules[]=migration/edit
Rules[]=migration/dryrun SimulateButton
# ... 14 weitere in der ausgelieferten Datei
*/ ?>
```

## Siehe auch

- [Alle Blöcke von site.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/site.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
