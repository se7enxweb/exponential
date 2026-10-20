---
title: rest.ini [ezp7xRestContentController_viewContent_OutputSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [ezp7xRestContentController_viewContent_OutputSettings] in rest.ini: Template"
keywords: Einstellung, rest.ini, ezp7xRestContentController_viewContent_OutputSettings, INI, setting, Template
type: generated
generated: exp:help:examples
---
# rest.ini [ezp7xRestContentController_viewContent_OutputSettings]

Der Block `[ezp7xRestContentController_viewContent_OutputSettings]` von `settings/rest.ini`, mit 1 Einstellung(en).

> The same template for the content controller of the ezprestapi extension, which serves the node reads of  
> REST API v1 and v2 (the XHTML renderer looks the section up by controller class and action)  

Bearbeiten Sie `settings/rest.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/rest.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/rest.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/rest.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Template

Standard: `rest_pagelayout.tpl`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/rest.ini.append.php
[ezp7xRestContentController_viewContent_OutputSettings]
Template=rest_pagelayout.tpl
*/ ?>
```

## Siehe auch

- [Alle Blöcke von rest.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/rest.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
