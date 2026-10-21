---
title: ezjscore.ini [AjaxUploader]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [AjaxUploader] in ezjscore.ini (ezjscore): AjaxUploadHandler"
keywords: Einstellung, ezjscore.ini, AjaxUploader, INI, setting, AjaxUploadHandler
type: generated
generated: exp:help:examples
---
# ezjscore.ini [AjaxUploader]

Der Block `[AjaxUploader]` von `extension/ezjscore/settings/ezjscore.ini` (Erweiterung ezjscore), mit 1 Einstellung(en).

Bearbeiten Sie `extension/ezjscore/settings/ezjscore.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/ezjscore.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/ezjscore.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/ezjscore.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## AjaxUploadHandler

Standard: ein Hash mit 2: `ezobjectrelationlist=ezpRelationListAjaxUploader`, `ezobjectrelation=ezpRelationAjaxUploader`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/ezjscore.ini.append.php
[AjaxUploader]
# ein leeres "AjaxUploadHandler[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
AjaxUploadHandler[]
AjaxUploadHandler[ezobjectrelationlist]=ezpRelationListAjaxUploader
AjaxUploadHandler[ezobjectrelation]=ezpRelationAjaxUploader
*/ ?>
```

## Siehe auch

- [Alle Blöcke von ezjscore.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `extension/ezjscore/settings/ezjscore.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
