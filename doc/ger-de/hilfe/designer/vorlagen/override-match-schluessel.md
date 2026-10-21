---
title: Override-Match-Schlüssel
audience: designer, developer
category: templates
order: 580
summary: Jeder Match-Schlüssel, den ein Override einer Inhaltsansicht verwenden kann (class_identifier, class, node, object, parent_node, section, section_identifier, depth, url_alias, viewmode, Remote-IDs, Klasse des Elternknotens, Klassengruppe, Zustand), was jeder vergleicht, welche einen Umzug auf eine andere Installation überstehen, und der Präfix-Abgleich von url_alias.
keywords: Override-Match-Schlüssel, Match, class_identifier, node, parent_node, section, depth, url_alias, viewmode, remote_id, node_remote_id, parent_class_identifier, class_group, state, Match-Bedingungen, override match keys, match conditions
type: help
---
# Override-Match-Schlüssel

Diese Seite ist eine Referenz für die Bedingungen von Override-Regeln (`Match[<key>]=<value>`). Die Schlüssel, die eine
Vollansicht setzt, werden in `kernel/classes/eznodeviewfunctions.php` gebaut; `node_view_gui` und `content_view_gui` setzen
ähnliche Listen (siehe [node_view_gui](node-view-gui.md), [content_view_gui](content-view-gui.md)).

## Inhalt

1. [Die Schlüssel](#die-schlüssel)
2. [Übertragbare Schlüssel](#übertragbare-schlüssel)
3. [Wie Werte verglichen werden](#wie-werte-verglichen-werden)
4. [Schlüssel kombinieren](#schlüssel-kombinieren)

## Die Schlüssel

| Schlüssel | Vergleicht | Beispiel |
|---|---|---|
| `class_identifier` | den Bezeichner der Inhaltsklasse | `Match[class_identifier]=article` |
| `class` | die Klassen-ID | `Match[class]=16` |
| `node` | die Knoten-ID | `Match[node]=2` |
| `object` | die Objekt-ID | `Match[object]=57` |
| `parent_node` | die ID des Elternknotens | `Match[parent_node]=61` |
| `section` | die Sektions-ID | `Match[section]=6` |
| `section_identifier` | den Bezeichner der Sektion | `Match[section_identifier]=news` |
| `depth` | die Tiefe des Knotens | `Match[depth]=2` |
| `url_alias` | den Anfang des URL-Alias (Präfix) | `Match[url_alias]=news` |
| `viewmode` | den Ansichtsmodus (Vollansichten) | `Match[viewmode]=full` |
| `remote_id` | die Remote-ID des Objekts | `Match[remote_id]=cjw-starter-o-root` |
| `node_remote_id` | die Remote-ID des Knotens | |
| `parent_node_remote_id`, `parent_object_remote_id` | die Remote-IDs des Elternknotens | |
| `parent_class`, `parent_class_identifier` | die Klasse des Elternknotens | `Match[parent_class_identifier]=blog` |
| `class_group` | eine ID einer Klassengruppe, zu der die Klasse gehört | |
| `state` | eine ID eines Objektzustands, den das Objekt hat | |
| `state_identifier` | einen Bezeichner eines Objektzustands (`<group>/<state>`), den das Objekt hat | |
| `navigation_part_identifier` | den Navigationsteil (meist für die Administration) | |
| `view_offset` | den Blätterversatz der Ansicht | `Match[view_offset]=0` (nur die erste Seite) |

## Übertragbare Schlüssel

IDs (`node`, `object`, `class`, `section`, `parent_node`) unterscheiden sich zwischen Installationen: Ein Design, das sie
verwendet, funktioniert nur auf der Website, für die es gemacht wurde. Bevorzugen Sie Bezeichner und Remote-IDs:
`class_identifier`, `section_identifier`, `remote_id`, `node_remote_id`, `parent_class_identifier`. Das Theme starter gleicht
seine Wurzelseite genau aus diesem Grund über `Match[remote_id]=cjw-starter-o-root` ab.

## Wie Werte verglichen werden

- Ein einfacher Wert muss gleich sein.
- Ist der Wert der Seite eine Liste (`class_group`, `state`), trifft die Regel zu, wenn die Liste den Wert enthält.
- `url_alias` trifft zu, wenn der Alias der Seite mit dem Wert **beginnt**, sodass `Match[url_alias]=news` den ganzen
  Teilbaum News abdeckt (`eZTemplateDesignResource` behandelt es als Teilbaum-Abgleich).

## Schlüssel kombinieren

Alle Bedingungen einer Regel müssen zutreffen:

```ini
[mytheme_full_article_in_blog]
Source=node/view/full.tpl
MatchFile=full/blog_article.tpl
Subdir=templates
Match[class_identifier]=article
Match[parent_class_identifier]=blog
Priority=5
```

Innerhalb einer Regel gibt es kein „oder“: Schreiben Sie stattdessen zwei Regeln mit demselben `MatchFile`.

## Verwandte Seiten

- [Override-Grundlagen](override-grundlagen.md)
- [Einen einzelnen Knoten überschreiben](einen-einzelnen-knoten-ueberschreiben.md)
- [Override nach Sektion](override-nach-sektion.md)
- [Override nach Elternknoten](override-nach-elternknoten.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
