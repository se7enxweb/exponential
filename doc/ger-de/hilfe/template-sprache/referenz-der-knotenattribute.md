---
title: Referenz der Knotenattribute
audience: developer, designer, app-creator
category: development
order: 210
summary: Jedes Attribut, das ein Template von einem Knoten (eZContentObjectTreeNode) lesen kann - die 16 Datenbankfelder und die 35 Funktionsattribute seiner Definition, die PHP-Methode hinter jedem und der Wert, den jedes für einen echten Artikel auf einer Entwicklungsinstallation zurückgab.
keywords: Knotenattribute, eZContentObjectTreeNode, definition, function_attributes, name, data_map, children, path, url_alias, can_edit, class_identifier, Referenz, node attributes, reference
type: help
---
# Referenz der Knotenattribute

`$node` in einem Knoten-Template ist ein `eZContentObjectTreeNode` (`kernel/classes/ezcontentobjecttreenode.php`). Was ein Template daraus
lesen kann, ist genau das, was `eZContentObjectTreeNode::definition()` aufführt: die Datenbankfelder `fields` der Tabelle
`ezcontentobject_tree` und die `function_attributes`, die bei jedem Lesen eine PHP-Methode aufrufen (siehe
[Das Objektmodell in Templates](das-objektmodell-in-templates.md) zum Protokoll).

## Inhalt

1. [Wie diese Tabelle entstand](#wie-diese-tabelle-entstand)
2. [Datenbankfelder](#datenbankfelder)
3. [Funktionsattribute](#funktionsattribute)
4. [Ein Beispiel](#ein-beispiel)
5. [Hinweise](#hinweise)
6. [Verwandte Seiten](#verwandte-seiten)

## Wie diese Tabelle entstand

Ein nur lesendes CLI-Skript auf einer Entwicklungsinstallation (mit `bin/php/ezexec.php -s site` als Benutzer des Webservers ausgeführt) rief
`eZContentObjectTreeNode::definition()` auf, führte `fields` und `function_attributes` auf und las jeden Namen mit `$node->attribute( $name )`
an Knoten 96, einem `ng_article`. Die Spalte „Wert auf einer Entwicklungsinstallation“ ist der PHP-Typ und, bei kurzen Werten, der zurückgegebene Wert. Das Skript läuft
als anonymer Benutzer, die Prüfungen `can_*` zeigen also, was ein anonymer Besucher tun darf. Sein Kern:

```php
$node = eZContentObjectTreeNode::fetch( 96 );
$def = $node->definition();
foreach ( $def['fields'] as $name => $field )
    echo $name, ' ', $field['name'], ' ', $field['datatype'], "\n";
foreach ( $def['function_attributes'] as $name => $method )
    echo $name, ' ', $method, ' ', gettype( $node->attribute( $name ) ), "\n";
```

## Datenbankfelder

| Attribut | Datenbankspalte / Mitglied | Datentyp | Wert auf einer Entwicklungsinstallation (Knoten 96) |
|---|---|---|---|
| `node_id` | `NodeID` | integer | integer 96 |
| `parent_node_id` | `ParentNodeID` | integer | integer 92 |
| `main_node_id` | `MainNodeID` | integer | integer 96 |
| `contentobject_id` | `ContentObjectID` | integer | integer 94 |
| `contentobject_version` | `ContentObjectVersion` | integer | integer 1 |
| `contentobject_is_published` | `ContentObjectIsPublished` | integer | integer 1 |
| `depth` | `Depth` | integer | integer 4 |
| `sort_field` | `SortField` | integer | integer 2 |
| `sort_order` | `SortOrder` | integer | integer 0 |
| `priority` | `Priority` | integer | integer 0 |
| `modified_subnode` | `ModifiedSubNode` | integer | integer 1790970543 |
| `path_string` | `PathString` | string | string "/1/2/89/92/96/" |
| `path_identification_string` | `PathIdentificationString` | text | string (59 Bytes) |
| `remote_id` | `RemoteID` | string | string "media-n-729" |
| `is_hidden` | `IsHidden` | integer | integer 0 |
| `is_invisible` | `IsInvisible` | integer | integer 0 |

## Funktionsattribute

`remote_id` steht in beiden Listen; ein Template liest das Funktionsattribut (`remoteID()`).

| Funktionsattribut | PHP-Methode | Wert auf einer Entwicklungsinstallation (Knoten 96) |
|---|---|---|
| `name` | `eZContentObjectTreeNode::getName()` | string "Why Should You Choose Strength Training?" |
| `data_map` | `eZContentObjectTreeNode::dataMap()` | array(18) aus eZContentObjectAttribute |
| `remote_id` | `eZContentObjectTreeNode::remoteID()` | string "media-n-729" |
| `object` | `eZContentObjectTreeNode::object()` | Objekt eZContentObject |
| `subtree` | `eZContentObjectTreeNode::subTree()` | array(0) |
| `children` | `eZContentObjectTreeNode::children()` | array(0) |
| `children_count` | `eZContentObjectTreeNode::childrenCount()` | integer 0 |
| `view_count` | `eZContentObjectTreeNode::viewCount()` | integer 0 |
| `contentobject_version_object` | `eZContentObjectTreeNode::contentObjectVersionObject()` | Objekt eZContentObjectVersion |
| `sort_array` | `eZContentObjectTreeNode::sortArray()` | array(1) aus array |
| `can_read` | `eZContentObjectTreeNode::canRead()` | boolean true |
| `can_pdf` | `eZContentObjectTreeNode::canPdf()` | boolean true |
| `can_create` | `eZContentObjectTreeNode::canCreate()` | boolean true |
| `can_edit` | `eZContentObjectTreeNode::canEdit()` | boolean false |
| `can_hide` | `eZContentObjectTreeNode::canHide()` | boolean false |
| `can_remove` | `eZContentObjectTreeNode::canRemove()` | boolean false |
| `can_move` | `eZContentObjectTreeNode::canMoveFrom()` | boolean false |
| `can_move_from` | `eZContentObjectTreeNode::canMoveFrom()` | boolean false |
| `can_add_location` | `eZContentObjectTreeNode::canAddLocation()` | boolean false |
| `can_remove_location` | `eZContentObjectTreeNode::canRemoveLocation()` | boolean false |
| `can_view_embed` | `eZContentObjectTreeNode::canViewEmbed()` | boolean false |
| `is_main` | `eZContentObjectTreeNode::isMain()` | boolean true |
| `creator` | `eZContentObjectTreeNode::creator()` | Objekt eZContentObject |
| `path_with_names` | `eZContentObjectTreeNode::pathWithNames()` | string (59 Bytes) |
| `path` | `eZContentObjectTreeNode::fetchPath()` | array(3) aus eZContentObjectTreeNode |
| `path_array` | `eZContentObjectTreeNode::pathArray()` | array(5) aus integer |
| `parent` | `eZContentObjectTreeNode::fetchParent()` | Objekt eZContentObjectTreeNode |
| `url` | `eZContentObjectTreeNode::url()` | string (47 Bytes) |
| `url_alias` | `eZContentObjectTreeNode::urlAlias()` | string (47 Bytes) |
| `class_identifier` | `eZContentObjectTreeNode::classIdentifier()` | string "ng_article" |
| `class_name` | `eZContentObjectTreeNode::className()` | string "NG Article" |
| `hidden_invisible_string` | `eZContentObjectTreeNode::hiddenInvisibleString()` | string "-/-" |
| `hidden_status_string` | `eZContentObjectTreeNode::hiddenStatusString()` | string "Visible" |
| `classes_js_array` | `eZContentObjectTreeNode::availableClassesJsArray()` | string (3218 Bytes) |
| `is_container` | `eZContentObjectTreeNode::classIsContainer()` | integer 1 |

## Ein Beispiel

Einige dieser Attribute an Knoten 89, der Startseite „Fit & Healthy“:

```tpl
{def $node = fetch( 'content', 'node', hash( 'node_id', 89 ) )}
name:            {$node.name|wash}
class:           {$node.class_identifier} ({$node.class_name|wash})
url_alias:       {$node.url_alias}
path_string:     {$node.path_string}
depth:           {$node.depth}
children_count:  {$node.children_count}
sort_array:      {$node.sort_array.0|implode( ', ' )}
is_main:         {cond( $node.is_main, 'yes', 'no' )}
hidden status:   {$node.hidden_status_string}
can_read:        {cond( $node.can_read, 'yes', 'no' )}, can_edit: {cond( $node.can_edit, 'yes', 'no' )}
current user:    {fetch( 'user', 'current_user' ).login}
```

Ausgabe:

```text
name:            Fit &amp; Healthy
class:           ng_frontpage (NG Frontpage)
url_alias:       
path_string:     /1/2/89/
depth:           2
children_count:  16
sort_array:      published, 1
is_main:         yes
hidden status:   Visible
can_read:        yes, can_edit: no
current user:    anonymous
```

## Hinweise

- `url_alias` von Knoten 89 ist leer. Der Siteaccess `site` hat ein Pfadpräfix (`[SiteAccessSettings] PathPrefix`), und `urlAlias()`
  entfernt dieses Präfix vom Anfang des Alias; Knoten 89 ist der Knoten am Präfix selbst. `path_with_names` ist der vollständige Aliaspfad
  ohne diesen Schritt, und `url` gibt `url_alias` zurück, solange `[URLTranslator] Translation` eingeschaltet ist, sonst
  `content/view/full/<node id>`:

```tpl
{def $node = fetch( 'content', 'node', hash( 'node_id', 96 ) )}
PathPrefix:      {ezini( 'SiteAccessSettings', 'PathPrefix' )}
path_with_names: {$node.path_with_names}
url_alias:       {$node.url_alias}
url:             {$node.url}
```

Ausgabe:

```text
PathPrefix:      fit-healthy
path_with_names: fit-healthy/fitness/why-should-you-choose-strength-training
url_alias:       fitness/why-should-you-choose-strength-training
url:             fitness/why-should-you-choose-strength-training
```

- `sort_array` ist eine Liste von Paaren `array( sort field, ascending )`, die Sortierung der Kinder des Knotens, wie
  `fetch( 'content', 'list' )` sie versteht (hier: nach Veröffentlichungsdatum, aufsteigend ist `1`).
- `children`, `subtree` und `children_count` führen bei jedem Lesen eine Abfrage aus. Nutzen Sie in einer Liste mit vielen Einträgen
  stattdessen `fetch( 'content', 'list', ... )` mit `limit` (siehe [Leistung von fetch](../designer/vorlagen/fetch-leistung.md)).
- `can_read`, `can_edit`, `can_hide`, `can_remove`, `can_move`, `can_create` prüfen die Richtlinien des aktuellen Benutzers an diesem Ort;
  ihre Ergebnisse hängen davon ab, wer die Seite ansieht, weshalb solche Prüfungen außerhalb von Ausgaben im View-Cache gehören oder in
  Templates, deren Cache nach Rolle variiert (die Schlüssel des Content-View-Cache enthalten die Rollen-IDs des Benutzers).
- `classes_js_array` gibt eine Zeichenkette mit einem JavaScript-Array für das Menü zum Anlegen an dieser Stelle in der Administration zurück;
  Templates der Website brauchen es nicht.
- `creator` gibt das Inhaltsobjekt des Benutzers zurück, der die an diesem Knoten gespeicherte Version angelegt hat.

## Verwandte Seiten

- [Das Objektmodell in Templates](das-objektmodell-in-templates.md)
- [Referenz der Attribute von Inhaltsobjekten](referenz-der-objektattribute.md)
- [Referenz von Inhaltsattribut und Version](referenz-von-inhaltsattribut-und-version.md)
- [Knoten, Objekte und Attribute](../designer/vorlagen/knoten-objekte-und-attribute.md)
- [Knoten und Listen holen](../designer/vorlagen/knoten-und-listen-abrufen.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
