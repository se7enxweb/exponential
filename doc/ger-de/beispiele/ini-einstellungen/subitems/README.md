---
title: subitems.ini
audience: developer, admin
category: examples
order: 10
summary: Die Blöcke und Einstellungen von subitems.ini.
keywords: Beispiele, Referenz, subitems.ini, examples, reference
type: generated
generated: exp:help:examples
---
# subitems.ini

Die ausgelieferten Einstellungen von `settings/subitems.ini`: 7 Blöcke, 21 Einstellungen.

> Die Liste der Unterelemente der Administration („Tabellenoptionen“): welche Spalten standardmäßig angezeigt werden, die Vorlagen,  
> die alle wählen können, die Standards pro Ort, die automatischen Attributspalten und der CSV-  
> Export. Die Spalten selbst sind in subitemscolumns.ini definiert (Blöcke \[Column\_<key>\]).  
> Anleitung: doc/eng-us/bc/6.0/subitems-table-options.md  
>
> Unten verwendete Spaltenschlüssel: die 15 eingebauten Spalten (thumbnail, name, visibility, type, modifier,  
> modified, published, translations, section, nodeid, noderemoteid, objectid, objectremoteid,  
> objectstate, priority), jedes \[Column\_<key>\] aus subitemscolumns.ini und die automatischen  
> Attributspalten attr:<class identifier>/<attribute identifier>. Unbekannte Schlüssel werden ignoriert, und  
> eine Spalte, die der Benutzer nicht sehen darf (ihre Policy\[\]), entfällt überall, wo sie genannt wird.  

| Block | Einstellungen |
|---|---|
| [SubitemsSettings](SubitemsSettings.md) | 7 |
| [Preset_seo](Preset_seo.md) | 2 |
| [Preset_editorial](Preset_editorial.md) | 2 |
| [Preset_technical](Preset_technical.md) | 2 |
| [Preset_translations](Preset_translations.md) | 2 |
| [Defaults_media](Defaults_media.md) | 3 |
| [Defaults_users](Defaults_users.md) | 3 |

- [Alle Beispiele](../../README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/subitems.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
