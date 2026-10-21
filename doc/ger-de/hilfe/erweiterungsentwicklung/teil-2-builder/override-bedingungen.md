---
title: "Override-Bedingungen: Klasse, Knoten, Sektion, View-Modus und mehr"
audience: developer, designer, app-creator
category: extension-development
order: 110
summary: Jeder Match-Schlüssel, den ein Override verwenden kann - für Vollansichten, für Listen und Einbettungen mit node_view_gui und für Felder, die mit attribute_view_gui gezeichnet werden - aus dem Kernel-Code übernommen, der sie setzt, mit Beispielen und Regeln für Kombinationen.
keywords: Override-Bedingungen, Match, Bedingung, Klasse, Knoten, Sektion, Status, Tiefe, View-Modus, override conditions, class_identifier, node, object, parent_node, section_identifier, state_identifier, depth, url_alias, remote_id, viewmode, attribute_identifier, class_group, parent_class_identifier
type: help
---
# Override-Bedingungen: Klasse, Knoten, Sektion, View-Modus und mehr

Teil 2, Kapitel 11. Die `Match[<key>]`-Zeilen eines Overrides entscheiden, für welche Inhalte er gilt. Dieses Kapitel
listet jeden Schlüssel auf, übernommen aus dem Code, der sie setzt: `eZNodeviewfunctions::generateNodeViewData()` in
`kernel/classes/eznodeviewfunctions.php` für Vollansichten und die Weiterleitungsregeln in
`kernel/common/eztemplateautoload.php` für `node_view_gui` und `attribute_view_gui`.

## Schlüssel für eine Knotenansicht (Vollansicht einer Seite und node_view_gui)

| Schlüssel | Wert | Beispiel | Verwendung |
|---|---|---|---|
| `class_identifier` | der Klassenbezeichner | `article` | das Aussehen einer Inhaltsart (der meistgenutzte Schlüssel) |
| `class` | die numerische Klassen-ID | `16` | dasselbe, über die ID (bevorzugen Sie Bezeichner: Sie überstehen Neuinstallationen) |
| `class_group` | die IDs der Klassengruppen, in denen die Klasse ist | `1` | jede Klasse einer Gruppe |
| `node` | die Knoten-ID | `2` | eine einzelne Seite, zum Beispiel die Startseite |
| `object` | die Objekt-ID | `1234` | ein Objekt, an jeder Platzierung |
| `parent_node` | die Knoten-ID des Elternknotens | `59` | alles direkt unter einem Ordner |
| `parent_class_identifier` | der Klassenbezeichner des Elternknotens | `folder` | Unterknoten einer Art von Container |
| `parent_class` | die Klassen-ID des Elternknotens | `1` | dasselbe, über die ID |
| `section` | die Sektions-ID | `1` | eine Sektion, über die ID |
| `section_identifier` | der Sektionsbezeichner | `standard` | eine Sektion (bevorzugen Sie dies) |
| `state` | IDs von Objektstatus | `2` | Inhalte in einem Status, über die ID |
| `state_identifier` | Bezeichner `<group>/<state>` | `ez_lock/locked` | Inhalte in einem Status |
| `depth` | die Tiefe des Knotens im Baum | `3` | eine Ebene des Baums |
| `url_alias` | der URL-Alias des Knotens | `about-us` | eine Seite, über ihre Adresse |
| `remote_id` | die Remote-ID des Objekts | `a1b2...` | ein Objekt, stabil über Installationen hinweg |
| `node_remote_id` | die Remote-ID des Knotens | `c3d4...` | eine Platzierung, stabil über Installationen hinweg |
| `parent_node_remote_id`, `parent_object_remote_id` | die Remote-IDs des Elternknotens | | Unterknoten eines Ortes, stabil über Installationen hinweg |

Eine Vollansicht kennt außerdem `viewmode` (`full` oder den Modus in der Adresse), `view_offset` (den Seiten-Offset einer
seitenweisen Ansicht) und `navigation_part_identifier` (zu welchem Menüteil der Administration sie gehört).

Remote-IDs sind die richtige Wahl für Regeln, die mit einer Erweiterung ausgeliefert werden: Knoten- und Objekt-IDs
unterscheiden sich zwischen Installationen, Remote-IDs nicht.

## Schlüssel für ein Feld (attribute_view_gui und attribute_edit_gui)

`{attribute_view_gui attribute=$node.data_map.image}` zeichnet ein Feld mit dem Template
`content/datatype/view/<datatype>.tpl`. Ein Override auf diese `Source` kann prüfen:

| Schlüssel | Wert | Beispiel |
|---|---|---|
| `attribute_identifier` | der Bezeichner des Klassenattributs | `image` |
| `attribute` | die ID des Klassenattributs | `245` |
| `class_identifier` | der Klassenbezeichner des Objekts | `article` |
| `class` | die Klassen-ID des Objekts | `16` |

```ini
[view_article_image]
Source=content/datatype/view/ezimage.tpl
MatchFile=datatype/view/article_image.tpl
Subdir=templates
Match[class_identifier]=article
Match[attribute_identifier]=image
```

## Bedingungen kombinieren

Alle `Match`-Zeilen eines Blocks müssen zutreffen:

```ini
[line_article_in_news]
Source=node/view/line.tpl
MatchFile=line/news_article.tpl
Subdir=templates
Match[class_identifier]=article
Match[parent_node]=59
```

Ein „oder“ gibt es nicht: Schreiben Sie zwei Blöcke. Bei einem Wert, der eine Liste ist (`class_group`, `state`,
`state_identifier`), ist die Bedingung erfüllt, wenn die Liste den Wert enthält.

## Beispiele nach Aufgabe

**Die Startseite:**

```ini
[full_frontpage]
Source=node/view/full.tpl
MatchFile=full/frontpage.tpl
Subdir=templates
Match[node]=2
```

**Jeder Artikel in Listenansichten:**

```ini
[line_article]
Source=node/view/line.tpl
MatchFile=line/article.tpl
Subdir=templates
Match[class_identifier]=article
```

**Eine Sektion mit eigenem Aussehen:**

```ini
[full_folder_intranet]
Source=node/view/full.tpl
MatchFile=full/intranet_folder.tpl
Subdir=templates
Match[class_identifier]=folder
Match[section_identifier]=intranet
```

## Override-Schlüssel anderer Templates

Andere Templates setzen eigene Schlüssel; die Template-Tabelle der Debug-Ausgabe zeigt, was jede Anfrage angeboten hat.
Eingebettete Objekte in Rich Text fügen zum Beispiel Schlüssel für die Ansicht und die Klasse der Einbettung hinzu (das
Media-Theme prüft bei Einbettungen `Match[classification]`). Im Zweifel suchen Sie im Code, der das Template darstellt,
nach `setKeys` oder `attribute_keys`:

```bash
grep -rn "setKeys\|'attribute_keys'" kernel/ | head
```

## Verwandte Seiten

- Zurück: [Template-Overrides: die Grundlagen](grundlagen-der-template-overrides.md)
- Weiter: [View-Modi für Builder](view-modi-fuer-builder.md)
- [Objektstatus](../../../anleitungen/objektzustaende.md), [Sektionen](../../../anleitungen/sektionen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website mit Dashboard
ist für Mitte 2027 geplant: Bitte werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen
früher kommen. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
