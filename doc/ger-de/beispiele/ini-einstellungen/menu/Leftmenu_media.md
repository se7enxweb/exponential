---
title: menu.ini [Leftmenu_media]
audience: developer, admin
category: examples
summary: Die Einstellungen von [Leftmenu_media] in menu.ini:
keywords: Einstellung, menu.ini, Leftmenu_media, INI, setting
type: generated
generated: exp:help:examples
---
# menu.ini [Leftmenu_media]

Der Block `[Leftmenu_media]` von `settings/menu.ini`, mit 0 Einstellung(en).

> Removed from 4.3. Enable if you have special requirements.  
> Links\[templates\]=visual/templatelist  
> PolicyList\_templates\[\]=visual/\*  

Bearbeiten Sie `settings/menu.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/menu.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/menu.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/menu.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Siehe auch

- [Alle Blöcke von menu.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/menu.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
