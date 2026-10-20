---
title: "Welcher Weg: die Entscheidungstabelle"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 20
summary: Für jede Art von Änderung an einer Site (Layout, Aussehen eines Blocks, Menü, Rahmen, Inhaltsansicht, neuer Blocktyp) der Weg, sie zu bauen (Layout-Editor, Template-Override, Einstellungen, Inhalt), und warum, mit dem Beispiel aus Fit & Healthy für jede Zeile.
keywords: Entscheidungstabelle, wann Layouts verwenden, wann Templates verwenden, Template-Override, Layout-Editor, Einstellungen, Inhalt, Blocktypen, Menüs, Kopfzeile, Fußzeile, decision table, when to use layouts, when to use templates, template override, Layouts editor, settings, content, block types, menus, header, footer
type: help
---
# Welcher Weg: die Entscheidungstabelle

Jede Zeile ist eine Art von Änderung. **Weg** ist das, was Sie verwenden; **Auch** ist der zweite Weg, wenn der erste nicht genügt.

## Zusammenstellung der Seite

| Änderung | Weg | Auch | Beispiel auf Fit & Healthy |
|---|---|---|---|
| Blöcke auf einer Landingpage anordnen | Layout-Editor | | Landing Page / Home (28 Blöcke) |
| Auf Artikelseiten einen Block über der Fußzeile hinzufügen | Layout-Editor, gemeinsames Layout Prefooter | | Prefooter: „Most popular articles“ |
| Einen Block auf jeder Seite zeigen | Layout-Editor, gemeinsames Layout Header / Footer, Zone `post_header` oder `pre_footer` | | Zone `header`: Template-Block des Brotkrumenpfads |
| Einem Seitentyp ein eigenes Layout geben | Layouts > Layout-Zuordnungen | | Full / Article für Artikel |
| Die Einträge einer Liste von Hand auswählen | Layout-Editor, List-Block, manuelle Sammlung | | Liste im Prefooter: drei Artikel |
| Eine Liste, die sich selbst aktualisiert | Layout-Editor, List-Block, dynamische Sammlung | Template mit `fetch('content','list', ...)` | |

## Aussehen und Markup

| Änderung | Weg | Auch | Beispiel |
|---|---|---|---|
| Wie jeder Block eines Typs aussieht | Template-Override `explayouts/block/<type>.tpl` | Blockoptionen (Reiter Design) pro Block | `explayouts/block/list.tpl` |
| Ein neues Aussehen für einen Blocktyp, pro Block gewählt | Neuer **Ansichtstyp** in der `explayouts.ini` plus sein Template | | List: list, grid, list_numbered, list_zigzag, list_accordion, grid_featured |
| Wie eine Inhaltsklasse in einer Liste erscheint | Template `content/views/<view>/<class>.tpl` | Der Ansichtstyp der Elemente des Blocks | `content/views/line/...` |
| Die HTML-Seite um alles herum | Template `pagelayout.tpl` | | `extension/sevenx_themes_media/design/media/templates/pagelayout.tpl` |
| Markup von Kopf- oder Fußzeile | Template `pagelayout/header.tpl`, `pagelayout/footer.tpl` | Platziert durch Template-Blöcke | |
| Farben, Schriften | Stylesheets des Designs | Blockoption Hintergrundfarbe | `background_color:color = primary` an der Spalte der Kopfzeile |

## Menüs und Navigation

| Änderung | Weg | Auch |
|---|---|---|
| Einträge des Kopfmenüs hinzufügen, entfernen, umordnen | Einstellungen: `menu.ini [SiteInfo] MainMenuID[]` | [Einen Eintrag zum Kopfmenü hinzufügen](../../../eng-us/help/build-two-ways/add-a-header-menu-item.md) |
| Erste Zeile der Fußzeile | Einstellungen: `FooterMenuID[]` | [Die Einträge des Fußzeilenmenüs](eintraege-des-fusszeilenmenues.md) |
| Zweite Zeile der Fußzeile | Inhalt: Menu items in Footer links | [Fußzeilen-Links aus dem Inhaltsbaum](fusszeilen-links-aus-dem-inhaltsbaum.md) |
| Text eines Menüeintrags | Inhalt: die Seite umbenennen | |
| Ein Menü, das von selbst dem Baum folgt | Template-Override des Menü-Templates | |

## Neue Fähigkeiten

| Änderung | Weg |
|---|---|
| Eine neue Art von Block | `[BlockDefinition_<id>]` in der `explayouts.ini` (Name, Handler, Ansichtstypen, Kategorie), eine Handler-Klasse, ein Template `explayouts/block/<id>.tpl` |
| Ein neuer Layouttyp (andere Zonen) | `[LayoutType_<id>]` in der `explayouts.ini` plus sein Template |
| Ein Template, das der Template-Block des Editors nennen kann | `explayouts/tpl_block/<name>.tpl` in einem Design |

## Faustregeln

1. **Ändern Redakteure es oft?** Layouts oder Inhalte. Templates brauchen ein Deployment.
2. **Muss es überall gleich aussehen?** Template. Ein Layout kann jeder mit dem Editor ändern.
3. **Eine Liste von Seiten, die jemand von Hand auswählt?** Eine manuelle Sammlung in einem Block oder eine Liste in den Einstellungen wie bei den Menüs.
4. **Nicht sicher?** Bauen Sie das Template des Blocks einmal (Weg 1) und lassen Sie Redakteure es platzieren (Weg 2).

## Verwandte Seiten

- [Zwei Wege zum Bauen](../../../eng-us/help/build-two-ways/two-ways-to-build.md)
- [Gemeinsame Layouts und Zonenverknüpfung](../../../eng-us/help/build-two-ways/shared-layouts-and-zone-linking.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
