---
title: ezxml.ini [ezxhtml]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [ezxhtml] in ezxml.ini: RenderParagraphInTableCells, ShowURLAliasForProtectedLinks"
keywords: Einstellung, ezxml.ini, ezxhtml, INI, setting, RenderParagraphInTableCells, ShowURLAliasForProtectedLinks
type: generated
generated: exp:help:examples
---
# ezxml.ini [ezxhtml]

Der Block `[ezxhtml]` von `settings/ezxml.ini`, mit 2 Einstellung(en).

> TagPresets\[full\]=All tags  
> TagPresets\[mini\]=Just formatting allowed  
> Handler specific options  

Bearbeiten Sie `settings/ezxml.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/ezxml.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/ezxml.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/ezxml.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## RenderParagraphInTableCells

> Determines to insert <p> tag inside a table cell in the output or not  
> if there is only one <paragraph> tag inside a cell.  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/ezxml.ini.append.php
[ezxhtml]
RenderParagraphInTableCells=enabled
*/ ?>
```

## ShowURLAliasForProtectedLinks

> Show URL alias for links the current user does not have read access to.  
> If disabled (recommended) the link will be /content/view/full/\[nodeID\] instead.  

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/ezxml.ini.append.php
[ezxhtml]
ShowURLAliasForProtectedLinks=disabled
*/ ?>
```

## Siehe auch

- [Alle Blöcke von ezxml.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/ezxml.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
