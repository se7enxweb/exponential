---
title: ezjscore.ini [ezjscServer_expdebugbar]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [ezjscServer_expdebugbar] in ezjscore.ini (ezjscore): Class"
keywords: Einstellung, ezjscore.ini, ezjscServer_expdebugbar, INI, setting, Class
type: generated
generated: exp:help:examples
---
# ezjscore.ini [ezjscServer_expdebugbar]

Der Block `[ezjscServer_expdebugbar]` von `extension/ezjscore/settings/ezjscore.ini` (Erweiterung ezjscore), mit 1 Einstellung(en).

> File=kernel/classes/subitems/expsubitemsserverfunctions.php  

Bearbeiten Sie `extension/ezjscore/settings/ezjscore.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/ezjscore.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/ezjscore.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/ezjscore.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Class

> The Exp Debug bar (settings/debugbar.ini, guide doc/eng-us/bc/6.0/debug-bar.md):  
> <root>/ezjscore/call/expdebugbar::settings\[::<siteaccess>\]  
>   every debug setting with its value in effect, where it comes from and the value of each scope  
> <root>/ezjscore/call/expdebugbar::set      (POST setting, op, value, scope, ezxform\_token)  
> <root>/ezjscore/call/expdebugbar::undo     (POST entry or group, ezxform\_token)  
> <root>/ezjscore/call/expdebugbar::preset   (POST preset + scope, or save, or delete, ezxform\_token)  
> <root>/ezjscore/call/expdebugbar::cache    (action=list; POST action=clear by=tag|id|all|node|velocity|opcache)  
> <root>/ezjscore/call/expdebugbar::iptest   (address, list)  
> <root>/ezjscore/call/expdebugbar::log, expdebugbar::summary  
> <root>/ezjscore/call/expdebugbar::panelheight  (POST height in pixels or 0, ezxform\_token: user preference  
>   expdebugbar\_panel\_height, the height of the open panel)  
> Every function checks itself: reading needs debug output for the request or setup/setup or  
> setup/managecache; changing settings needs setup/setup, clearing caches setup/managecache;  
> panelheight a signed-in user who sees the debug output.  

Standard: `expDebugBarServerFunctions`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/ezjscore.ini.append.php
[ezjscServer_expdebugbar]
Class=expDebugBarServerFunctions
*/ ?>
```

## Siehe auch

- [Alle Blöcke von ezjscore.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `extension/ezjscore/settings/ezjscore.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
