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

> System settings and dangerous setup actions.  

Standard: `true`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[ReauthGroup_system]
Enabled=true
*/ ?>
```

## Rules

Standard: eine Liste mit 18: `setup/*`, `!setup/info`, `!setup/logs`, `!setup/logshealth`, `!setup/logsdigest`, `!setup/logsfeed`, `!setup/templatelist`, `!setup/templateview`, `!setup/menu`, `!setup/preloadstream`, `!setup/cronjobsstream`, `!setup/staticcachestream` ....

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
Rules[]=update/*
Rules[]=git_manager/*
*/ ?>
```

## Siehe auch

- [Alle Blöcke von site.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/site.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
