---
title: contentcheck.ini
audience: developer, admin
category: examples
order: 10
summary: Die Blöcke und Einstellungen von contentcheck.ini.
keywords: Beispiele, Referenz, contentcheck.ini, examples, reference
type: generated
generated: exp:help:examples
---
# contentcheck.ini

Die ausgelieferten Einstellungen von `settings/contentcheck.ini`: 7 Blöcke, 43 Einstellungen.

> Die Inhaltsprüfungen: die Assets (Bilder, Dateien, Medien) unter einem Knoten, die nichts verwendet, und sichtbare Inhalte  
> mit Links, Einbettungen oder Verknüpfungen zu gelöschten, im Papierkorb liegenden, verborgenen oder unveröffentlichten Inhalten.  
> Anleitung: doc/eng-us/features/6.0/content-checks.md  
>
> Bearbeiten Sie diese Datei nicht: Überschreiben Sie sie in settings/override/contentcheck.ini.append.php oder in  
> der contentcheck.ini.append.php eines Siteaccess.  
>
> Wer sie verwenden darf, entscheiden die Richtlinien der Rollen, nicht diese Datei: contentcheck/unreferenced öffnet die  
> Liste der nicht referenzierten Objekte, contentcheck/trash wird zusätzlich gebraucht, um sie in den Papierkorb zu verschieben  
> (und content/remove für jedes Objekt), contentcheck/brokenrelations öffnet die Liste der defekten  
> Verknüpfungen. Jede Liste zeigt nur, was content/read den Benutzer sehen lässt.  

| Block | Einstellungen |
|---|---|
| [ContentCheckSettings](ContentCheckSettings.md) | 2 |
| [UnreferencedSettings](UnreferencedSettings.md) | 10 |
| [ReferenceSettings](ReferenceSettings.md) | 2 |
| [BrokenRelationsSettings](BrokenRelationsSettings.md) | 3 |
| [ScopeSettings](ScopeSettings.md) | 5 |
| [StorageSettings](StorageSettings.md) | 10 |
| [MissingFileSettings](MissingFileSettings.md) | 11 |

- [Alle Beispiele](../../README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/contentcheck.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
