---
title: ezjscore.ini [ezjscServer_expsubitems]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [ezjscServer_expsubitems] in ezjscore.ini (ezjscore): Class"
keywords: Einstellung, ezjscore.ini, ezjscServer_expsubitems, INI, setting, Class
type: generated
generated: exp:help:examples
---
# ezjscore.ini [ezjscServer_expsubitems]

Der Block `[ezjscServer_expsubitems]` von `extension/ezjscore/settings/ezjscore.ini` (Erweiterung ezjscore), mit 1 Einstellung(en).

Bearbeiten Sie `extension/ezjscore/settings/ezjscore.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/ezjscore.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/ezjscore.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/ezjscore.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Class

> Die Tabellenoptionen der Unterelemente-Liste der Administration (settings/subitems.ini, settings/subitemscolumns.ini):  
> <root>/ezjscore/call/expsubitems::columns::2  
>   die Spalten, die der Benutzer unter Knoten 2 sehen darf, die Standards, Vorlagen und die gespeicherte Auswahl  
> <root>/ezjscore/call/expsubitems::rows::2::10::0::name::1?columns=parentnodeid,urlalias  
>   die Liste von ezjscnode::subtree plus die angeforderten Spalten pro Eintrag  
> <root>/ezjscore/call/expsubitems::savepreference\[::2\]  (POST preference=<json>)  
> Jede Funktion prüft selbst: ein angemeldeter Benutzer mit content/read, der den Elternknoten lesen darf;  
> die Richtlinien der Spalten kommen aus subitemscolumns.ini. Das HardLimit oben gilt auch für rows.  

Standard: `expSubitemsServerFunctions`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/ezjscore.ini.append.php
[ezjscServer_expsubitems]
Class=expSubitemsServerFunctions
*/ ?>
```

## Siehe auch

- [Alle Blöcke von ezjscore.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `extension/ezjscore/settings/ezjscore.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
