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

> The admin subitems list ("Table options"): which columns are shown by default, the presets  
> everybody can choose, the defaults per place, the automatic attribute columns and the CSV  
> export. The columns themselves are defined in subitemscolumns.ini (\[Column\_<key>\] blocks).  
> Guide: doc/eng-us/bc/6.0/subitems-table-options.md  
>
> Column keys used below: the 15 built-in columns (thumbnail, name, visibility, type, modifier,  
> modified, published, translations, section, nodeid, noderemoteid, objectid, objectremoteid,  
> objectstate, priority), every \[Column\_<key>\] of subitemscolumns.ini, and the automatic  
> attribute columns attr:<class identifier>/<attribute identifier>. Unknown keys are ignored, and  
> a column the user may not see (its Policy\[\]) is left out wherever it is named.  

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
