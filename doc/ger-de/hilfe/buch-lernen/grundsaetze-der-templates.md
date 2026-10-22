---
title: "Grundsätze der Templates: Seitenlayout, Inhaltsansichten, Attribute"
audience: builder, designer, developer
category: books
order: 400
summary: Wie eine Seite aus Templates zusammengesetzt wird, von pagelayout.tpl bis hinunter zu einem einzelnen Attribut; Ansichtsmodi, eine eigene Ansicht, ein Drucklayout und wohin Stile und Bilder gehören.
keywords: Template, Seitenlayout, Inhaltsansicht, Ansichtsmodus, Attribut-Template, Stylesheet, Design, Layout, Buch Learning, template, pagelayout, content view, view mode, full, line, embed, attribute template, include, stylesheet, design, layout set, Learning book
type: help
---
# Grundsätze der Templates: Seitenlayout, Inhaltsansichten, Attribute

Das Template-System ist hierarchisch. Ein Template der ersten Ebene, `pagelayout.tpl`, steuert den Rahmen. Darin
zeichnen **Inhaltsansicht**-Templates das, was angefordert wurde. Darin wiederum zeichnen **Attribut**-Templates
einzelne Felder. Fast jedes Template kann durch ein spezifischeres überschrieben werden, abhängig von der Sektion, der
Klasse, der Tiefe im Baum und mehr ([Templates überschreiben](templates-ueberschreiben.md)).

## Das Seitenlayout

`pagelayout.tpl` wird immer aufgerufen. Es zeichnet den Kopf (Stile, Skripte), das obere Menü, die Navigation, ein
Suchfeld, die Fußzeile und setzt die Inhaltsansicht in die Mitte (`$module_result.content`). Es kann überschrieben
werden: Die Startseite kann ein anderes Layout haben als ein Produktkatalog.

Weitere Layouts für eine druckfreundliche Seite oder ein Pop-up werden mit einem URL-Teil, `/layout/set/<name>/`, vor
der Adresse gewählt: `/layout/set/print/<address>` verwendet das Layout mit dem Namen `print`. Die Layouts sind in
`layout.ini` definiert, jedes mit dem Seitenlayout-Template, das es verwendet:

```ini
[print]
PageLayout=print_pagelayout.tpl
```

Die mitgelieferte Datei definiert `fullscreen`, `popup` und `print`. (Das Buch schreibt die URL als `/set/layout/...`;
das Modul ist `layout`, die View `set`.) Ein Override kann eine solche Seite mit dem Schlüssel `layout` erkennen ([Templates und Design](../../anleitungen/templates-und-design.md)).

## Inhaltsansichten

Eine Inhaltsansicht ist die Darstellung dessen, was die URL angefordert hat. Die klassische Adresse `/content/view/full/2`
bedeutet: Zeige Knoten 2 in der Ansicht **full**. Bei sprechenden Adressen ist die Ansicht implizit: die Vollansicht.
Weitere Standardansichten, denen Sie begegnen:

| Ansicht | Verwendet für |
|---|---|
| `full` | die Seite des Knotens |
| `line` | eine Zeile in einer Liste von Unterelementen |
| `embed`, `embed-inline` | ein Objekt, das in einen Text eingefügt ist |
| `sitemap`, `print`, `pdf` | eine Sitemap, eine Druckseite, ein PDF |

Die Templates liegen in `design/<design>/templates/node/view/<view>.tpl`. Um **einen eigenen Ansichtsmodus** zu
erstellen, legen Sie eine Datei mit Ihrem Namen in diesen Ordner, zum Beispiel `extended.tpl` mit mehr Einzelheiten als
die Vollansicht (die Änderungshistorie, der letzte Bearbeiter, verwandte Objekte), und rufen Sie sie als
`/content/view/extended/<node id>` auf, oder fordern Sie sie in einem Template mit `fetch` und `node_view_gui` an
([Ansichtsmodi für Builder](../erweiterungsentwicklung/teil-2-builder/view-modi-fuer-builder.md)).

Inhaltsansichten können für Klassen, Sektionen oder einen einzelnen Knoten überschrieben werden. Die eigene Vollansicht
einer Klasse ist der häufigste Override überhaupt.

## Attribut-Templates

Eine Vollansicht gibt die Felder normalerweise nicht selbst aus: Sie bittet jedes Attribut, sich mit `attribute_view_gui`
selbst zu zeichnen, und das **Ansichtstemplate** des Datentyps erledigt das (eine Textzeile als Text, ein Bild als Bild
eines bestimmten Alias, Rich Text als HTML, siehe [Rich-Text-Tags](rich-text-tags.md)). Auch diese Attribut-Templates
können überschrieben werden, nach Klasse, Knoten und Sektion.

## Modulare Templates

Code, der an mehreren Stellen verwendet wird, kommt in eine eigene Datei und wird mit `include` eingebunden, mit
Parametern:

```
{include uri='design:parts/footer.tpl' show_social=true()}
```

Typische Teile: die Stilliste im Kopf, ein Kalender, Navigatoren, Menüblöcke.

## Stylesheets und Bilder

Stil gehört in CSS-Dateien, damit die Templates nur Markup und Template-Code enthalten. In Exponential werden
Stylesheets in `design.ini` (`[StylesheetSettings]`) aufgeführt und vom Packer gebündelt; sie werden nicht in ein
Template geschrieben ([Die Design-Kette](../erweiterungsentwicklung/teil-2-builder/die-design-kette.md)). Bilder, die zum
Design gehören (Logos, Symbole), liegen im Ordner `images/` des Designs. Inhaltsbilder kommen aus `ezimage`-Attributen.

## Bearbeitungs-Templates

Auf einer gemeinschaftlich gepflegten Website möchten Sie Redakteure vielleicht nicht in der Administration haben.
Bearbeitungs-Templates können zusammen mit einem Modul, das sie verwendet, eine maßgeschneiderte Oberfläche im eigenen
Design der Website anbieten, unter dem normalen Siteaccess
([Administrationsseiten im Stil von admin4](../erweiterungsentwicklung/teil-3-erweiterungsentwickler/admin-seiten-im-admin4-stil.md)).

## Caching

Die Verarbeitung von Templates ist aufwendig. Kompilierte Templates, der Inhalts-(View-)Cache und Cache-Blöcke senken
den Aufwand ([Caching in Templates](caching-in-templates.md)).

Weiter: [Grundlagen der Template-Syntax](grundlagen-der-template-syntax.md).

## Quelle und Lizenz

Bearbeitet nach Kapitel 3, „Displaying Content with eZ publish Templates“, von *Learning eZ publish: Building Content Management Solutions* von Paul Borgermans, Tony Wood,
Paul Forsyth, Martin Bauer, Björn Dieding und Ben Pirt (zusätzliches Material von Bruce Morrison), zweite Auflage, 2024,
[Book-Learning-eZPublish](https://github.com/se7enxweb/Book-Learning-eZPublish). Copyright (c) 2004-2024 7x und Graham
Brookins. Lizenziert unter der GNU Free Documentation License, Version 1.3 oder einer späteren Version, ohne
unveränderliche Abschnitte, ohne vordere und ohne hintere Umschlagtexte ([die Lizenz](../../../eng-us/LICENSE-GFDL-1.3.txt)).
Änderungen: umgestellt und gekürzt; Syntax, Operatoren, Einstellungen und Beispiele auf Exponential 6 aktualisiert (foreach, aktuelle Einstellungen, kein Inline-Stil und kein Inline-Skript).
