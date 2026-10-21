---
title: ezjscore.ini [ezjscServer_ezajaxuploader]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [ezjscServer_ezajaxuploader] in ezjscore.ini (ezjscore): Class, Functions, RequirePolicy"
keywords: Einstellung, ezjscore.ini, ezjscServer_ezajaxuploader, INI, setting, Class, Functions, RequirePolicy
type: generated
generated: exp:help:examples
---
# ezjscore.ini [ezjscServer_ezajaxuploader]

Der Block `[ezjscServer_ezajaxuploader]` von `extension/ezjscore/settings/ezjscore.ini` (Erweiterung ezjscore), mit 3 Einstellung(en).

Bearbeiten Sie `extension/ezjscore/settings/ezjscore.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/ezjscore.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/ezjscore.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/ezjscore.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Class

Standard: `ezjscServerFunctionsAjaxUploader`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/ezjscore.ini.append.php
[ezjscServer_ezajaxuploader]
Class=ezjscServerFunctionsAjaxUploader
*/ ?>
```

## Functions

> Jeder Aufruf prüft canUpload() des Upload-Handlers (content/create und die Klassenbeschränkungen des Attributs).  
> RequirePolicy=enabled verlangt außerdem ezjscore/call mit der Funktionsliste ezajaxuploader; gewähren Sie sie den Rollen,  
> die über den AJAX-Uploader hochladen, bevor Sie es einschalten.  

Standard: eine Liste mit 1: `ezajaxuploader`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/ezjscore.ini.append.php
[ezjscServer_ezajaxuploader]
# ein leeres "Functions[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Functions[]
Functions[]=ezajaxuploader
*/ ?>
```

## RequirePolicy

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/ezjscore.ini.append.php
[ezjscServer_ezajaxuploader]
RequirePolicy=disabled
*/ ?>
```

## Siehe auch

- [Alle Blöcke von ezjscore.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `extension/ezjscore/settings/ezjscore.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
