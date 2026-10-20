---
title: Zwei Wege zum Bauen, die Begriffe
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 40
summary: Die Begriffe von Layouts und Templates, wie diese Seiten sie verwenden; Layout, Layouttyp, Zone, Block, Blockdefinition, Ansichtstyp, Ansichtstyp der Elemente, Sammlung, gemeinsames Layout, verknüpfte Zone, Template-Block, Design, Design-Erweiterung, Override, Siteaccess, Geltungsbereich, Knoten-ID.
keywords: Glossar, Begriffe, Layout, Zone, Block, Blockdefinition, Ansichtstyp, Ansichtstyp der Elemente, Sammlung, manuell, dynamisch, gemeinsames Layout, verknüpfte Zone, Template-Block, Design-Erweiterung, Override, Siteaccess, Geltungsbereich, glossary, terms, block definition, view type, item view type, collection, manual, dynamic, shared layout, linked zone, template block, design extension, scope
type: help
---
# Zwei Wege zum Bauen, die Begriffe

| Begriff | Bedeutet | Wo Sie ihm begegnen |
|---|---|---|
| **Layout** | Eine Seitenstruktur: ein Layouttyp plus die Blöcke, die in seinen Zonen platziert sind | Layouts > Layouts; Tabelle `explayouts_layout` |
| **Layouttyp** | Die Menge der Zonen, die ein Layout hat (zum Beispiel `layout_2`: header, post_header, main, pre_footer, footer) | Die erste Auswahl beim Anlegen eines Layouts; `[LayoutType_<id>]` in der `explayouts.ini` |
| **Zone** | Ein benannter Bereich eines Layouts, der Blöcke enthält | Die gestrichelten Bereiche des Editors (HEADER, MAIN ...) |
| **Block** | Ein Teil einer Seite: ein Titel, eine Liste, ein Bild, eine Spalte | Die Blöcke des Editors |
| **Blockdefinition** (Blocktyp) | Was ein Block ist: Name, Handler-Klasse, Ansichtstypen, Kategorie, ob er eine Sammlung hat | `[BlockDefinition_<id>]` in der `explayouts.ini` |
| **Ansichtstyp** | Ein Aussehen eines Blocktyps, pro Block gewählt | List: list, grid, list_numbered, list_zigzag, list_accordion, grid_featured |
| **Ansichtstyp der Elemente** | Wie ein Block jeden seiner Einträge zeigt (Zeile, Karte, Overlay ...) | Listen und Raster; Templates `content/views/<view>/` |
| **Container-Block** | Ein Block mit Platzhaltern, die andere Blöcke enthalten | Column (Platzhalter `main`), Two columns (`left`, `right`) |
| **Sammlung** | Die Einträge eines Blocks | Die Einträge eines List-Blocks |
| **Manuelle Sammlung** | Von Hand ausgewählte Einträge; jeder ist eine Knoten-ID | Die drei Artikel des Prefooters |
| **Dynamische Sammlung** | Einträge aus einer Abfrage auf den Inhaltsbaum | Eine Liste der neuesten Artikel eines Bereichs |
| **Gemeinsames Layout** | Ein Layout, mit dessen Zonen andere Layouts verknüpft sind | Header / Footer, Prefooter |
| **Verknüpfte Zone** | Eine Zone, die die Blöcke einer Zone eines gemeinsamen Layouts zeigt | `header` jedes Layouts von Fit & Healthy |
| **Template-Block** | Ein Block, der ein in ihm genanntes Template darstellt | Kopfzeile, Brotkrumenpfad und Fußzeile von Header / Footer |
| **Layout-Zuordnung** (Regel) | Welches Layout eine Seite bekommt: nach Knoten, Teilbaum, Pfad oder Inhaltsklasse | Layouts > Layout-Zuordnungen |
| **Entwurf / veröffentlicht** | Ein Layout wird in einem Entwurf geändert; **Layout veröffentlichen** macht es live | Die Schaltflächen Verwerfen und Layout veröffentlichen des Editors |
| **Design** | Eine benannte Menge von Templates und Stylesheets | `media` für Fit & Healthy |
| **Design-Erweiterung** | Eine Erweiterung, die ein Design unter `design/<design>/templates/` mitbringt | `sevenx_themes_media` |
| **Override** | Ein Template in einem Design, das ein Template desselben Pfads aus einem Design weiter unten in der Liste ersetzt | `pagelayout/header.tpl` Ihrer Erweiterung |
| **Siteaccess** | Eine Konfiguration der Installation: eine Site, eine Sprachversion, die Administration | `site`, `bold`, `admin` |
| **INI-Geltungsbereich** | Wo eine Einstellung geschrieben wird: global, ein Siteaccess, eine Erweiterung | `exp:ini scopes` |
| **Knoten-ID** | Der Platz einer Seite im Inhaltsbaum | `Knoten 192` in der Administration |

## Verwandte Seiten

- [Zwei Wege zum Bauen](../../../eng-us/help/build-two-ways/two-ways-to-build.md)
- [Zwei Wege zum Bauen, alle Seiten](README.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
