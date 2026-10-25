---
title: site.ini [ReauthGroup_export]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [ReauthGroup_export] in site.ini: Enabled, Rules"
keywords: Einstellung, site.ini, ReauthGroup_export, INI, setting, Enabled, Rules
type: generated
generated: exp:help:examples
---
# site.ini [ReauthGroup_export]

Der Block `[ReauthGroup_export]` von `settings/site.ini`, mit 2 Einstellung(en).

Bearbeiten Sie `settings/site.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/site.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/site.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/site.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Enabled

> Exporting data.  

Standard: `true`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[ReauthGroup_export]
Enabled=true
*/ ?>
```

## Rules

Standard: eine Liste mit 9: `infocollector/export *`, `audit/export *`, `content/subitemsexport *`, `mailpreferences/export *`, `bccie/export *`, `bccie/doexport *`, `newsletter/subscription_list_csvexport *`, `newsletter/subscriber_export *`, `newsletter/statistics_export *`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[ReauthGroup_export]
# ein leeres "Rules[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Rules[]
Rules[]=infocollector/export *
Rules[]=audit/export *
Rules[]=content/subitemsexport *
Rules[]=mailpreferences/export *
Rules[]=bccie/export *
Rules[]=bccie/doexport *
Rules[]=newsletter/subscription_list_csvexport *
Rules[]=newsletter/subscriber_export *
Rules[]=newsletter/statistics_export *
*/ ?>
```

## Siehe auch

- [Alle Blöcke von site.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/site.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
