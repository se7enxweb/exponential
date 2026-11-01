---
title: site.ini [PageLayoutHooks]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [PageLayoutHooks] in site.ini: BodyEnd"
keywords: Einstellung, site.ini, PageLayoutHooks, INI, setting, BodyEnd
type: generated
generated: exp:help:examples
---
# site.ini [PageLayoutHooks]

Der Block `[PageLayoutHooks]` von `settings/site.ini`, mit 1 Einstellung(en).

> AllowedDeletionDirs\[\]=/var/share/somedir  

Bearbeiten Sie `settings/site.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/site.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/site.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/site.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## BodyEnd

> Templates an extension adds at the end of every page body, in this order (design: paths without "design:").  
> Every pagelayout includes design:pagelayout/hooks/body\_end.tpl just before </body>, once per engine:  
>   .tpl  {include uri='design:pagelayout/hooks/body\_end.tpl'}  
>   .stpl {expinclude uri='design:pagelayout/hooks/body\_end.tpl'}  
>   .twig {% expinclude uri='design:pagelayout/hooks/body\_end.tpl' %}  
> What a hook prints may depend only on what the pagelayout's cache blocks key on (siteaccess, node, signed-in  
> state, roles). Guide: doc/eng-us/features/6.0/pagelayout-hooks.md  
> BodyEnd\[\]=expwt/hook.tpl  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[PageLayoutHooks]
# ein leeres "BodyEnd[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
BodyEnd[]
*/ ?>
```

## Siehe auch

- [Alle Blöcke von site.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/site.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
