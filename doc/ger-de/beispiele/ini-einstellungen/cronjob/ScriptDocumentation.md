---
title: cronjob.ini [ScriptDocumentation]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [ScriptDocumentation] in cronjob.ini: Link, DocumentationBase"
keywords: Einstellung, cronjob.ini, ScriptDocumentation, INI, setting, Link, DocumentationBase
type: generated
generated: exp:help:examples
---
# cronjob.ini [ScriptDocumentation]

Der Block `[ScriptDocumentation]` von `settings/cronjob.ini`, mit 2 Einstellung(en).

Bearbeiten Sie `settings/cronjob.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/cronjob.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/cronjob.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/cronjob.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Link

> A "Docs" link beside a script at Setup > Cronjobs. A script names its documentation  
> with the @link tag of its header (or of its class file), a URL or a path such as  
> doc/eng-us/guides/sessions.md. An installation can give or replace one per script, by the  
> file name as Scripts\[\] has it; none removes the link the header gives.  
> Guide: doc/eng-us/features/6.0/cronjobs-console.md  
> Link\[session\_gc.php\]=https://intranet.example.com/operations/sessions  
> Link\[sitemap.php\]=none  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/cronjob.ini.append.php
[ScriptDocumentation]
# ein leeres "Link[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Link[]
*/ ?>
```

## DocumentationBase

> Where a path is looked up. Empty: the documentation of Exponential on GitHub.  
> Only http and https links are shown.  

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/cronjob.ini.append.php
[ScriptDocumentation]
DocumentationBase=
*/ ?>
```

## Siehe auch

- [Alle Blöcke von cronjob.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/cronjob.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
