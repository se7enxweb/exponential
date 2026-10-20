---
title: cronjob.ini [CronjobPart-contentcheck]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [CronjobPart-contentcheck] in cronjob.ini: Scripts"
keywords: Einstellung, cronjob.ini, CronjobPart-contentcheck, INI, setting, Scripts
type: generated
generated: exp:help:examples
---
# cronjob.ini [CronjobPart-contentcheck]

Der Block `[CronjobPart-contentcheck]` von `settings/cronjob.ini`, mit 1 Einstellung(en).

> Der Referenzindex der Inhaltsprüfungen für sich: php runcronjobs.php contentcheck. Nur  
> nötig mit contentcheck.ini \[UnreferencedSettings\] BuildOnRequest=false (sehr große  
> Installationen); führen Sie ihn öfter als IndexTTL aus. Anleitung: doc/eng-us/features/6.0/content-checks.md  

Bearbeiten Sie `settings/cronjob.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/cronjob.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/cronjob.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/cronjob.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Scripts

Standard: eine Liste mit 1: `contentcheck.php`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/cronjob.ini.append.php
[CronjobPart-contentcheck]
# ein leeres "Scripts[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Scripts[]
Scripts[]=contentcheck.php
*/ ?>
```

## Siehe auch

- [Alle Blöcke von cronjob.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/cronjob.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
