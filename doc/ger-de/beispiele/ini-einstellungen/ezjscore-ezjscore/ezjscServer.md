---
title: ezjscore.ini [ezjscServer]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [ezjscServer] in ezjscore.ini (ezjscore): FunctionList"
keywords: Einstellung, ezjscore.ini, ezjscServer, INI, setting, FunctionList
type: generated
generated: exp:help:examples
---
# ezjscore.ini [ezjscServer]

Der Block `[ezjscServer]` von `extension/ezjscore/settings/ezjscore.ini` (Erweiterung ezjscore), mit 1 Einstellung(en).

> Beispiel: JavaScript-Dateien von einer statischen Subdomäne ausliefern (ohne Schrägstrich am Ende!)  
> CustomHosts\[.js\]=http://static.example.com  

Bearbeiten Sie `extension/ezjscore/settings/ezjscore.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/ezjscore.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/ezjscore.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/ezjscore.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## FunctionList

> Liste der Berechtigungsfunktionen, wie sie das Berechtigungssystem von Exponential verwendet  
> ezjscnode::load, ::subtree und ::updatepriority, wenn \[ezjscServer\_ezjscnode\] RequirePolicy=enabled  
> ezajaxuploader (der AJAX-Uploader von Verknüpfungsattributen), wenn \[ezjscServer\_ezajaxuploader\] RequirePolicy=enabled  

Standard: eine Liste mit 3: `ezjsctemplate`, `ezjscnode`, `ezajaxuploader`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/ezjscore.ini.append.php
[ezjscServer]
# ein leeres "FunctionList[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
FunctionList[]
FunctionList[]=ezjsctemplate
FunctionList[]=ezjscnode
FunctionList[]=ezajaxuploader
*/ ?>
```

## Siehe auch

- [Alle Blöcke von ezjscore.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `extension/ezjscore/settings/ezjscore.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
