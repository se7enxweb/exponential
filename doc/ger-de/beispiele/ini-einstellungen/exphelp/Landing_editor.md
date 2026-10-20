---
title: exphelp.ini [Landing_editor]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [Landing_editor] in exphelp.ini: StartPage, Related"
keywords: Einstellung, exphelp.ini, Landing_editor, INI, setting, StartPage, Related
type: generated
generated: exp:help:examples
---
# exphelp.ini [Landing_editor]

Der Block `[Landing_editor]` von `settings/exphelp.ini`, mit 2 Einstellung(en).

> One group per audience, all settings optional. Page ids are paths below doc/eng-us/ (the same in every language); a page that is not in the help (not  
> written yet, hidden) is left out. Empty lists are filled from the index: featured pages first, then help pages and  
> guides in their order.  
>   StartPage=<page id>        the "Start here" button  
>   ReadingPath\[\]=<page id>    the reading path, in order  
>   InfoPages\[\]=<page id>      the info boxes  
>   KeyPages\[\]=<page id>       the key pages  
>   Categories\[\]=<category>    the categories with a search button (default: every category with pages for it)  
>   Related\[\]=<audience>       related audiences  
>   Summary=<sentence>         instead of the built-in one (translated in kernel/exphelp when the catalogue has it)  
>   Blocks\[\]=<block>           instead of \[LandingSettings\] Blocks\[\]  

Bearbeiten Sie `settings/exphelp.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/exphelp.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/exphelp.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/exphelp.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## StartPage

Standard: `help/getting-started.md`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[Landing_editor]
StartPage=help/getting-started.md
*/ ?>
```

## Related

Standard: eine Liste mit 3: `user`, `admin`, `website-creator`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[Landing_editor]
# ein leeres "Related[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Related[]
Related[]=user
Related[]=admin
Related[]=website-creator
*/ ?>
```

## Siehe auch

- [Alle Blöcke von exphelp.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/exphelp.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
