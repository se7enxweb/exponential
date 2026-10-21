---
title: Knoten, Objekte und Attribute
audience: designer, developer
category: templates
order: 440
summary: Die drei Dinge, mit denen Inhalts-Templates arbeiten - der Knoten (ein Platz im Baum), das Inhaltsobjekt (der Inhalt mit seinen Versionen) und seine Attribute (die Felder in data_map) -, mit den Knotenattributen, die Designer am häufigsten verwenden, und wie Sie den Inhalt eines Felds lesen.
keywords: Knoten, Inhaltsobjekt, Attribut, data_map, contentobject_attribute, has_content, content, url_alias, class_identifier, children, parent, path, can_edit, Hauptknoten, Orte, node, content object, attribute, main node, locations
type: help
---
# Knoten, Objekte und Attribute

Diese Seite ist für Designer, die View-Templates schreiben. Inhalte in Exponential haben drei Schichten; Templates bewegen sich
mit dem Punkt zwischen ihnen.

## Inhalt

1. [Die drei Schichten](#die-drei-schichten)
2. [Attribute des Knotens](#attribute-des-knotens)
3. [Attribute des Objekts](#attribute-des-objekts)
4. [Felder: data_map](#felder-data_map)
5. [Mehrere Orte](#mehrere-orte)

## Die drei Schichten

| Schicht | Was sie ist | In Templates |
|---|---|---|
| **Knoten** | ein Platz des Inhalts im Baum (ein Objekt kann mehrere haben) | `$node` in View-Templates |
| **Inhaltsobjekt** | der Inhalt selbst, mit Versionen und Sprachen | `$node.object` |
| **Attribut** | ein Feld des Objekts: Titel, Text, Bild | `$node.data_map.<identifier>` |

## Attribute des Knotens

Aus `eZContentObjectTreeNode` (`kernel/classes/ezcontentobjecttreenode.php`), die, die Designer am häufigsten verwenden:

| Attribut | Liefert |
|---|---|
| `name` | den Namen des Objekts in der aktuellen Sprache |
| `node_id`, `parent_node_id`, `contentobject_id`, `depth` | IDs und Tiefe |
| `url_alias` | den Pfad für `ezurl` |
| `class_identifier`, `class_name` | die Inhaltsklasse |
| `data_map` | die Felder |
| `object` | das Inhaltsobjekt |
| `children`, `children_count`, `subtree` | darunter (Fetches; siehe [Fetch-Leistung](fetch-leistung.md)) |
| `parent`, `path`, `path_array`, `path_with_names` | darüber |
| `sort_array` | die Sortierreihenfolge, die der Redakteur für seine Kinder gewählt hat |
| `can_read`, `can_edit`, `can_create`, `can_remove`, `can_hide`, `can_move` | die Berechtigungen des aktuellen Benutzers darauf |
| `is_main`, `is_hidden`, `is_invisible` | Hauptort, Zustände des Versteckens |
| `creator`, `remote_id` | den Ersteller; die stabile Remote-ID |

## Attribute des Objekts

`$node.object.published` und `modified` (Zeitstempel), `owner`, `current_language`, `available_languages`, `main_node`,
`assigned_nodes`, `section_id`, `remote_id`.

## Felder: data_map

`$node.data_map` ist ein Hash der Felder nach Bezeichner. Jedes Feld (ein `eZContentObjectAttribute`) hat:

| Attribut | Liefert |
|---|---|
| `has_content` | ob es gefüllt ist |
| `content` | seinen Wert, in einer Form, die vom Datentyp abhängt (eine Zeichenkette, ein Bildobjekt, ein Datum ...) |
| `data_text`, `data_int`, `data_float` | die gespeicherten Rohwerte |
| `contentclass_attribute_identifier`, `contentclass_attribute.name` | seinen Bezeichner und seine Beschriftung |
| `data_type_string` | den Datentyp (`ezstring`, `ezxmltext`, `ezimage` ...) |

Zeichnen Sie ein Feld mit seinem Datentyp-Template, das sein Markup kennt:

```tpl
{if $node.data_map.intro.has_content}
    {attribute_view_gui attribute=$node.data_map.intro}
{/if}
```

Lesen Sie `content` nur selbst, wenn Sie einen Teil davon brauchen (die URL eines Bildalias, ein Datum für `l10n`).

## Mehrere Orte

Ein Objekt, das in zwei Ordnern liegt, hat zwei Knoten. Links und kanonische URLs sollten den Hauptknoten verwenden:
`$node.object.main_node.url_alias`. Im Pagelayout hat ihn `$module_result.content_info.main_node_url_alias`.

## Verwandte Seiten

- [Attribute und Punktnotation](attribute-und-punktnotation.md)
- [attribute_view_gui](attribute-view-gui.md)
- [Ansichtstemplates von Datentypen](datentyp-ansichtstemplates.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für
Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen früher
bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
