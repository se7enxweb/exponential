---
title: Das Objektmodell in Templates
audience: developer, designer, app-creator
category: development
order: 200
summary: Wie ein Template die PHP-Objekte des Inhaltsmodells sieht - Knoten, Inhaltsobjekt, Version, Data Map, Inhaltsobjekt-Attribut und der Inhalt seines Datentyps - und das Attributprotokoll (attributes, hasAttribute, attribute), das die Engine für jeden Punkt in einem Variablenpfad nutzt, an echten Inhalten belegt.
keywords: Objektmodell, Knoten, Inhaltsobjekt, Version, data_map, Attribut, content, eZContentObjectTreeNode, eZContentObject, eZContentObjectAttribute, hasAttribute, Attributprotokoll, Punktschreibweise, object model, node, content object, attribute protocol, dot notation
type: help
---
# Das Objektmodell in Templates

Inhalts-Templates arbeiten nicht mit Datenbankzeilen. Sie arbeiten mit PHP-Objekten des Kernels, und jeder Punkt in einem Variablenpfad
(`$node.object.data_map.title.content`) ist ein Aufruf in eines davon. Diese Seite erklärt, welchen Objekten ein Template begegnet, wie sie
zusammenhängen und welches kleine Protokoll die Template-Engine nutzt, um sie zu lesen. Die Tabellen aller Attribute stehen auf den
Referenzseiten, auf die am Ende verwiesen wird.

## Inhalt

1. [Die Kette vom Knoten zum Inhalt](#die-kette-vom-knoten-zum-inhalt)
2. [Ein Gang die Kette hinab auf alpha](#ein-gang-die-kette-hinab-auf-alpha)
3. [Das Attributprotokoll](#das-attributprotokoll)
4. [Arrays und Objekte nutzen denselben Punkt](#arrays-und-objekte-nutzen-denselben-punkt)
5. [Vor dem Lesen prüfen](#vor-dem-lesen-prüfen)
6. [Was ein fehlendes Attribut tut](#was-ein-fehlendes-attribut-tut)
7. [Knoten oder Objekt?](#knoten-oder-objekt)
8. [Verwandte Seiten](#verwandte-seiten)

## Die Kette vom Knoten zum Inhalt

```text
eZContentObjectTreeNode      a place in the content tree        $node
  .object  ──────────────►  eZContentObject                     the content item, all its versions
       .current  ────────►  eZContentObjectVersion              the published version
  .data_map  ────────────►  array( identifier => eZContentObjectAttribute )
       .title  ──────────►  eZContentObjectAttribute            one field of the class
            .content  ───►  datatype content                    string, ezxmltext, eZImageAliasHandler, array ...
```

- **Knoten** (`eZContentObjectTreeNode`, `kernel/classes/ezcontentobjecttreenode.php`): ein Ort im Baum. Ein Objekt kann mehrere Knoten
  (Orte) haben; jeder hat seinen eigenen Elternknoten, Pfad, URL-Alias und seine eigenen Kinder.
- **Inhaltsobjekt** (`eZContentObject`, `kernel/classes/ezcontentobject.php`): der Inhalt selbst, mit seiner Klasse, Sektion, seinem
  Eigentümer, seinen Zuständen, Sprachen und Versionen.
- **Version** (`eZContentObjectVersion`, `kernel/classes/ezcontentobjectversion.php`): eine Version des Objekts; `current_version` ist die
  veröffentlichte.
- **Inhaltsobjekt-Attribut** (`eZContentObjectAttribute`, `kernel/classes/ezcontentobjectattribute.php`): ein Feld einer Version in einer
  Sprache. `data_map` ist die Zuordnung vom Bezeichner des Klassenattributs zu diesen Objekten.
- **Inhalt**: was der Datentyp des Felds aus den gespeicherten Daten macht (`eZContentObjectAttribute::content()` fragt
  `objectAttributeContent()` des Datentyps). Er ist eine einfache Zeichenkette bei `ezstring`, ein Objekt `eZXMLText` bei `ezxmltext`, ein
  `eZImageAliasHandler` bei `ezimage`, ein Array bei `ezobjectrelationlist` und so weiter.

Die `data_map` des Knotens und die `data_map` des Objekts enthalten normalerweise dieselben Attribute: `eZContentObjectTreeNode::dataMap()`
(`kernel/classes/ezcontentobjecttreenode.php`) gibt die Data Map des Objekts des Knotens für die am Knoten gespeicherte Version
(`contentobject_version`) zurück, oder den neuesten Entwurf des aktuellen Benutzers, solange
`eZContentObjectTreeNode::setUseCurrentUserDraft( true )` eingeschaltet ist (standardmäßig aus). `eZContentObject::fetchDataMap()` behält jede
Zuordnung im globalen `$eZContentObjectDataMapCache`, sodass zweimaliges Lesen in einer Anfrage eine Abfrage kostet.

## Ein Gang die Kette hinab auf alpha

Knoten 96 auf alpha ist ein `ng_article` unter „Fitness“ in der Website Fit & Healthy:

```tpl
{def $node = fetch( 'content', 'node', hash( 'node_id', 96 ) )}
node:      {$node|get_class} {$node.node_id}, parent {$node.parent_node_id}, depth {$node.depth}, path {$node.path_string}
object:    {$node.object|get_class} {$node.object.id} "{$node.object.name|wash}", class {$node.object.class_identifier}
version:   {$node.object.current|get_class} {$node.object.current.version} of {$node.object.versions|count}
data_map:  {$node.data_map|get_type}
attribute: {$node.data_map.title|get_class} {$node.data_map.title.id}, datatype {$node.data_map.title.data_type_string}
content:   {$node.data_map.title.content|get_type} "{$node.data_map.title.content|wash}"
```

Ausgabe auf alpha:

```text
node:      ezcontentobjecttreenode 96, parent 92, depth 4, path /1/2/89/92/96/
object:    ezcontentobject 94 "Why Should You Choose Strength Training?", class ng_article
version:   ezcontentobjectversion 1 of 1
data_map:  array[18]
attribute: ezcontentobjectattribute 580, datatype ezstring
content:   string[40] "Why Should You Choose Strength Training?"
```

`get_class` gibt den Klassennamen in Kleinbuchstaben zurück und `get_type` den Typ mit seiner Größe (siehe die Typ-Operatoren). Der Artikel
hat eine Version, und sein Feld `title` ist ein `ezstring`, dessen Inhalt eine einfache Zeichenkette ist.

## Das Attributprotokoll

Jede Klasse, die der Kernel an Templates übergibt, setzt drei Methoden um, einmal in `eZPersistentObject` definiert
(`kernel/classes/ezpersistentobject.php`):

| Methode | Was sie tut |
|---|---|
| `attributes()` | die Namen, die ein Template lesen darf: die Datenbankfelder `fields` von `definition()`, dann ihre `function_attributes`, dann ihre `functions` |
| `hasAttribute( $name )` | true, wenn der Name einer davon ist |
| `attribute( $name )` | zuerst ein Funktionsattribut: ruft die in `function_attributes` genannte PHP-Methode auf; sonst die in `fields` genannte Mitgliedsvariable; sonst eine Methode aus `functions`; sonst protokolliert sie `Attribute '...' does not exist` und gibt null zurück |

`$node.name` ist also nicht die Datenbankspalte: `eZContentObjectTreeNode::definition()` ordnet `name` dem Funktionsattribut `getName()` zu
und `$node.data_map` der Methode `dataMap()`. Wenn ein Name sowohl Feld als auch Funktionsattribut ist (`remote_id` an Knoten und Objekt,
`name` am Objekt), gewinnt das Funktionsattribut, weil `attribute()` zuerst `function_attributes` prüft.

Die Seite der Engine steht in `lib/eztemplate/classes/eztemplate.php`: Für jeden Punkt prüft sie `is_object()` und ruft dann `hasAttribute()`
und `attribute()` auf; bei einem Array prüft sie `isset( $array[$key] )`. Jede PHP-Klasse, die `attributes()`, `hasAttribute()` und
`attribute()` umsetzt, lässt sich an ein Template übergeben und mit Punkten lesen; so stellen Erweiterungen ihre eigenen Objekte bereit. Die
kompilierte Form eines Templates tut dasselbe über `compiledFetchAttribute()` (siehe [Der Template-Compiler](der-template-compiler.md)).

Ein Funktionsattribut führt bei jedem Lesen PHP aus. `$node.children` holt alle Kinder, `$node.object.versions` lädt alle Versionen; lesen Sie
solche Attribute einmal mit `def` in eine Variable statt in einer Schleife.

## Arrays und Objekte nutzen denselben Punkt

Ein Punkt liest einen Array-Schlüssel genauso wie ein Objektattribut. Eine Zahl liest ein Listenelement, und eckige Klammern nehmen einen
Ausdruck, der Schlüssel kann also aus einer Variablen kommen:

```tpl
{def $node = fetch( 'content', 'node', hash( 'node_id', 96 ) )
     $path = $node.path
     $field = 'title'}
{$path|count} ancestors: {$path.0.name|wash}, then {$path[1].name|wash}, then {$path[2].name|wash}
path_array: {$node.path_array|implode( '/' )}
dynamic key: {$node.data_map[$field].content|wash}
```

Ausgabe auf alpha:

```text
3 ancestors: Websites, then Fit &amp; Healthy, then Fitness
path_array: 1/2/89/92/96
dynamic key: Why Should You Choose Strength Training?
```

`$node.path` hält die Vorfahren als Knoten (ab der ersten Ebene unter der Wurzel, ohne den Knoten selbst), und `path_array` die Knoten-IDs des
ganzen Pfads. Das `&amp;` in `Fit &amp; Healthy` ist `wash`, das das kaufmännische Und für HTML maskiert.

## Vor dem Lesen prüfen

`is_set()` stellt dieselbe Frage ohne Fehler; ein Template, das mehreren Klassen dient, kann so auf ein Feld prüfen:

```tpl
{def $node = fetch( 'content', 'node', hash( 'node_id', 96 ) )}
{if is_set( $node.data_map.image )}has an image field{/if}


{if is_set( $node.data_map.price )}has a price field{else}no price field{/if}
```

Ausgabe auf alpha:

```text
has an image field
no price field
```

## Was ein fehlendes Attribut tut

Das Lesen eines nicht vorhandenen Attributs gibt nichts aus und protokolliert einen Fehler, der die Klasse nennt. Der Wert ist null, das
Template läuft also weiter:

```tpl
{def $node = fetch( 'content', 'node', hash( 'node_id', 96 ) )}
[{$node.no_such_attribute}]
```

Ausgabe auf alpha:

```text
[]
```

Von der Template-Engine protokolliert:

```text
ERROR : No such attribute for object(eZContentObjectTreeNode): no_such_attribute
Choose one of following:
```

Die Meldung stammt aus `eZTemplate::elementValue()`. Wenn die Debug-Ausgabe für Ihre IP eingeschaltet ist, erscheint sie im Debug-Bereich der
Seite, mit Template-Datei, Zeile und Spalte. Siehe [Fehler und Fehlersuche](fehler-und-fehlersuche.md) zu den anderen Meldungen.

## Knoten oder Objekt?

Beide führen zum selben Inhalt; womit Sie beginnen, entscheidet, was Sie sonst noch erreichen:

```tpl
{def $node = fetch( 'content', 'node', hash( 'node_id', 96 ) )
     $object = fetch( 'content', 'object', hash( 'object_id', $node.contentobject_id ) )}
by node:   {$node.name|wash} / main node {$node.object.main_node_id}
by object: {$object.name|wash} / main node {$object.main_node_id} / {$object.assigned_nodes|count} location(s)
same data_map: {if eq( $node.data_map.title.id, $object.data_map.title.id )}yes{else}no{/if}
```

Ausgabe auf alpha:

```text
by node:   Why Should You Choose Strength Training? / main node 96
by object: Why Should You Choose Strength Training? / main node 96 / 1 location(s)
same data_map: yes
```

- Beginnen Sie beim **Knoten** für alles, was den Ort im Baum betrifft: Elternknoten, Kinder, Pfad, URL-Alias, Sichtbarkeit, Sortierung, die
  Prüfungen `can_*` dieses Orts.
- Beginnen Sie beim **Objekt** für alles, was das Inhaltselement betrifft: Klasse, Sektion, Eigentümer, Zustände, Versionen, Sprachen,
  Beziehungen, alle seine Orte (`assigned_nodes`).
- `content_view_gui` nimmt ein Objekt, `node_view_gui` einen Knoten (siehe [Node view gui](../../../eng-us/help/designers/templates/node-view-gui.md) und
  [Content view gui](../../../eng-us/help/designers/templates/content-view-gui.md)).

## Verwandte Seiten

- [Referenz der Knotenattribute](referenz-der-knotenattribute.md)
- [Referenz der Attribute von Inhaltsobjekten](referenz-der-objektattribute.md)
- [Referenz von Inhaltsattribut und Version](referenz-von-inhaltsattribut-und-version.md)
- [Data Map und Inhalt](data-map-und-inhalt.md)
- [Knoten, Objekte und Attribute](../../../eng-us/help/designers/templates/nodes-objects-attributes.md) (die Einführung für Designer)
- [Attribute und Punktschreibweise](../../../eng-us/help/designers/templates/attributes-and-dot-notation.md)
- [Die Content-API](../../../eng-us/help/creators/app-creators/the-content-api.md) (dieselben Objekte aus PHP)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
