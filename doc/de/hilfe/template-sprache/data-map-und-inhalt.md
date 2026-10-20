---
title: Data Map und Inhalt
audience: developer, designer, app-creator
category: development
order: 240
summary: Die Felder eines Inhaltsobjekts in Templates lesen - data_map, has_content, content, die rohen data_text und data_int und attribute_view_gui - Feld für Feld an einem echten Artikel auf alpha belegt (ezstring, ezxmltext, ezimage, ezobjectrelationlist, eztags, Daten).
keywords: data_map, has_content, content, data_text, data_int, attribute_view_gui, ezstring, ezxmltext, output_text, ezimage, Bildvariante, Bild-Alias, ezobjectrelationlist, relation_list, eztags, Datum, datetime, image alias, dates
type: help
---
# Data Map und Inhalt

`data_map` ist die Tür zu den Feldern eines Inhaltsobjekts: ein Hash vom Bezeichner des Klassenattributs zu einem `eZContentObjectAttribute`.
Diese Seite liest die Felder eines echten Artikels auf alpha, Knoten 96 „Why Should You Choose Strength Training?“ (Klasse `ng_article`), und
zeigt, was jede Art von Feld einem Template gibt. Die Tabellen der Attribute stehen in der
[Referenz von Inhaltsattribut und Version](referenz-von-inhaltsattribut-und-version.md).

## Inhalt

1. [Die Felder einer Klasse](#die-felder-einer-klasse)
2. [content, data_text und value](#content-data_text-und-value)
3. [Rich Text (ezxmltext)](#rich-text-ezxmltext)
4. [Bilder (ezimage)](#bilder-ezimage)
5. [Beziehungen (ezobjectrelationlist)](#beziehungen-ezobjectrelationlist)
6. [Tags (eztags)](#tags-eztags)
7. [Daten](#daten)
8. [attribute_view_gui oder content?](#attribute_view_gui-oder-content)
9. [Leere Felder](#leere-felder)
10. [Verwandte Seiten](#verwandte-seiten)

## Die Felder einer Klasse

Eine Schleife über `data_map` führt die Bezeichner, Datentypen und für jedes Feld auf, ob es etwas enthält:

```tpl
{def $node = fetch( 'content', 'node', hash( 'node_id', 96 ) )}
{$node.name|wash} ({$node.class_identifier})

{foreach $node.data_map as $identifier => $attribute}
- {$identifier}: {$attribute.data_type_string}, has_content: {cond( $attribute.has_content, 'yes', 'no' )}

{/foreach}
```

Ausgabe auf alpha:

```text
Why Should You Choose Strength Training? (ng_article)
- title: ezstring, has_content: yes
- teaser_title: ezstring, has_content: yes
- authors: ezobjectrelationlist, has_content: yes
- image: ezimage, has_content: yes
- teaser_image: ezimage, has_content: yes
- full_intro: ezxmltext, has_content: yes
- teaser_intro: ezxmltext, has_content: no
- body: ezxmltext, has_content: yes
- related_content: ezobjectrelationlist, has_content: no
- related_multimedia: ezobjectrelationlist, has_content: no
- main_topic: eztags, has_content: yes
- tags: eztags, has_content: yes
- sponsored_content_disclosure: ezstring, has_content: no
- css_class: ezstring, has_content: no
- url_text: ezstring, has_content: no
- metadata: xrowmetadata, has_content: yes
- additional_image: ezimage, has_content: no
- icon_title: ezimage, has_content: no
```

Die Reihenfolge ist die der Klassenattribute. Ein Feld, das die Klasse hat, das aber leer ist, steht trotzdem in der Zuordnung; `has_content`
fragt den Datentyp, ob etwas darin ist.

## content, data_text und value

`content` ist, was der Datentyp aus den gespeicherten Daten macht; `data_text`, `data_int` und `data_float` sind die gespeicherten Daten
selbst; `value` ist `content`, außer wenn ein Formular gerade einen Wert für das Feld gesendet hat. Bei einer Textzeile stimmen sie überein:

```tpl
{def $title = fetch( 'content', 'node', hash( 'node_id', 96 ) ).data_map.title}
content:   {$title.content|wash}
data_text: {$title.data_text|wash}
value:     {$title.value|wash}
sort key:  {$title.sort_key_string|wash}
```

Ausgabe auf alpha:

```text
content:   Why Should You Choose Strength Training?
data_text: Why Should You Choose Strength Training?
value:     Why Should You Choose Strength Training?
sort key:  why should you choose strength training?
```

`sort_key_string` ist die klein geschriebene Form, nach der der Kernel sortiert (`sort_by` auf `attribute`). Bevorzugen Sie in Templates
`content`: `data_text` ist eine Einzelheit der Speicherung, die sich von Datentyp zu Datentyp unterscheidet.

## Rich Text (ezxmltext)

Der Inhalt eines Felds `ezxmltext` ist ein Objekt `eZXMLText`. `output` gibt den Ausgabe-Handler für den aktuellen Siteaccess und
`output_text` das gerenderte HTML; `data_text` enthält das gespeicherte XML:

```tpl
{def $intro = fetch( 'content', 'node', hash( 'node_id', 96 ) ).data_map.full_intro}
{$intro.content|get_class}
{$intro.content.output|get_class}
{$intro.content.output.output_text|strip_tags|trim|shorten( 120 )}
Stored XML starts with: {$intro.data_text|shorten( 60 )|wash}
```

Ausgabe auf alpha:

```text
ezxmltext
explblockxhtmlxmloutput
If you only had 1 hour a week to exercise, that hour should be spent strength training.
Stored XML starts with: &lt;section xmlns:image=&quot;http://ez.no/namespaces/ezpublish3/...
```

Auf alpha ist der Ausgabe-Handler `ExplBlockXHTMLXMLOutput` (von `get_class` klein geschrieben), eine Unterklasse von `eZXHTMLXMLOutput` des
Kernels, die die Erweiterung explayouts in `ezxml.ini` (`HandlerClass`) registriert. In einer Seite schreiben Sie normalerweise
`{attribute_view_gui attribute=$node.data_map.body}`, was dasselbe `output_text` über das Ansichts-Template des Datentyps rendert (siehe
[Templates für Rich Text](../../../en/us/help/designers/templates/rich-text-templates.md)).

## Bilder (ezimage)

Der Inhalt eines Felds `ezimage` ist ein `eZImageAliasHandler`. Indizieren Sie ihn mit einem Aliasnamen aus `image.ini`, um diese Variante zu
bekommen, oder lesen Sie `original`:

```tpl
{def $image = fetch( 'content', 'node', hash( 'node_id', 96 ) ).data_map.image
     $large = $image.content['large']}
{$image.content|get_class}
original: {$image.content.original.width}x{$image.content.original.height} {$image.content.original.mime_type}
large:    {$large.width}x{$large.height} {$large.url}
```

Ausgabe auf alpha:

```text
ezimagealiashandler
original: 1920x1080 image/jpeg
large:    1200x675 var/site/storage/images/fit-healthy/fitness/why-should-you-choose-strength-training/583-1-eng-US/why-should-you-choose-strength-training_large.jpg
```

Das Lesen eines Alias tut mehr als lesen. `eZImageAliasHandler::imageAlias()` (`kernel/classes/datatypes/ezimage/ezimagealiashandler.php`)
legt die Variante an, wenn sie zum ersten Mal verlangt wird: Es skaliert die Datei, vermerkt sie in der Tabelle `ezimagefile` und speichert den
Alias im XML des Attributs. So entstehen Varianten auf einer Website, und es bedeutet, dass ein Template, das einen noch von niemandem
genutzten Alias nennt, den ersten Besucher für das Skalieren bezahlen lässt. Das Beispiel nutzt `large`, das für dieses Bild bereits existierte.

## Beziehungen (ezobjectrelationlist)

Der Inhalt eines Felds `ezobjectrelationlist` ist ein Array mit einem Schlüssel, `relation_list`, einer Liste von Hashes. Jeder Hash bezeichnet
ein verwandtes Objekt; holen Sie das Objekt oder den Knoten, um es zu lesen:

```tpl
{def $authors = fetch( 'content', 'node', hash( 'node_id', 96 ) ).data_map.authors}
{$authors.content|get_type}, keys: {foreach $authors.content as $key => $value}{$key}{delimiter}, {/delimiter}{/foreach}


{foreach $authors.content.relation_list as $relation}
- object {$relation.contentobject_id}, node {$relation.node_id}: {fetch( 'content', 'object', hash( 'object_id', $relation.contentobject_id ) ).name|wash}

{/foreach}
```

Ausgabe auf alpha:

```text
array[1], keys: relation_list
- object 185, node 187: John Doe
```

Die Schlüssel eines Eintrags, wie alpha sie zurückgab:

```tpl
{def $relation = fetch( 'content', 'node', hash( 'node_id', 96 ) ).data_map.authors.content.relation_list.0}
{foreach $relation as $key => $value}{$key}={$value}{delimiter}, {/delimiter}{/foreach}
```

Ausgabe auf alpha:

```text
identifier=, priority=1, in_trash=, contentobject_id=185, is_modified=, contentobject_version=1, node_id=187, parent_node_id=186, contentclass_id=57, contentclass_identifier=ng_person, contentobject_remote_id=media-o-1013
```

Mit `contentclass_identifier` kann ein Template entscheiden, wie es einen Eintrag zeigt, bevor es ihn holt, und `node_id` ist der Hauptknoten
des verwandten Objekts, bereit für `fetch( 'content', 'node', hash( 'node_id', ... ) )` oder für `node_view_gui`.

## Tags (eztags)

Der Datentyp `eztags` (Erweiterung `eztags`) gibt ein Objekt `eZTags` zurück; `tags` ist die Liste der Tag-Objekte und `keyword_string` die zu
einer Zeichenkette verbundenen Schlüsselwörter:

```tpl
{def $tags = fetch( 'content', 'node', hash( 'node_id', 96 ) ).data_map.tags}
{$tags.content|get_class}
{foreach $tags.content.tags as $tag}{$tag.keyword|wash} (id {$tag.id}){delimiter}, {/delimiter}{/foreach}
```

Ausgabe auf alpha:

```text
eztagsStrength training (id 49)
```

```tpl
{def $dm = fetch( 'content', 'node', hash( 'node_id', 96 ) ).data_map}
main_topic: {foreach $dm.main_topic.content.tags as $tag}{$tag.keyword|wash} (parent {$tag.parent_id}){/foreach}

tag keywords as a string: {$dm.tags.content.keyword_string|wash}
body is empty: {cond( $dm.body.content.is_empty, 'yes', 'no' )}
```

Ausgabe auf alpha:

```text
main_topic: Strength training (parent 36)
tag keywords as a string: Strength training
body is empty: no
```

`is_empty` an einem Inhalt `eZXMLText` ist ein weiterer Weg zu fragen, ob ein Rich-Text-Feld Text hat.

## Daten

`ng_article` hat kein eigenes Datumsfeld; die Zeiten der Veröffentlichung und Änderung des Objekts sind Unix-Zeitstempel, formatiert mit den
Locale-Operatoren:

```tpl
{def $node = fetch( 'content', 'node', hash( 'node_id', 96 ) )}
published: {$node.object.published} = {$node.object.published|datetime( 'custom', '%Y-%m-%d' )}
modified:  {$node.object.modified|l10n( 'shortdate' )}
```

Ausgabe auf alpha:

```text
published: 1532345053 = 2018-07-23
modified:  09/24/2020
```

`l10n( 'shortdate' )` nutzt das Format der Locale des Siteaccess (hier `eng-US`); `datetime( 'custom', ... )` nimmt eine Formatzeichenkette.
Der Inhalt eines Felds `ezdatetime` ist ein Objekt `eZDateTime`, dessen Attribut `timestamp` durch dieselben Operatoren geht.

## attribute_view_gui oder content?

`attribute_view_gui` rendert ein Feld mit dem Ansichts-Template seines Datentyps (`content/datatype/view/<datatype>.tpl` oder einem Override);
das Lesen von `content` gibt Ihnen den Wert, den Sie selbst formatieren:

```tpl
{def $node = fetch( 'content', 'node', hash( 'node_id', 96 ) )}
[{attribute_view_gui attribute=$node.data_map.title}]
[{attribute_view_gui attribute=$node.data_map.full_intro}]
```

Ausgabe auf alpha:

```text
[
Why Should You Choose Strength Training?]
[
<p>If you only had 1 hour a week to exercise, that hour should be spent strength training.</p>
]
```

Nutzen Sie `attribute_view_gui`, wenn Sie das eigene Markup des Datentyps wollen (Rich Text, Bilder, Dateien, Matrizen): Das Template des
Datentyps kümmert sich um Maskierung, Aliase und Links. Lesen Sie `content`, wenn Sie den Wert in Ihrem eigenen Markup, einer Bedingung oder
einer Berechnung brauchen, und maskieren Sie Text selbst mit `wash` (siehe [Wash und Maskierung](../../../en/us/help/designers/templates/wash-and-escaping.md)).

## Leere Felder

Prüfen Sie `has_content`, bevor Sie ein Feld zeigen, damit leere Felder keine leeren Hüllen hinterlassen:

```tpl
{def $dm = fetch( 'content', 'node', hash( 'node_id', 96 ) ).data_map}
teaser: {if $dm.teaser_intro.has_content}{attribute_view_gui attribute=$dm.teaser_intro}{else}empty, use full_intro{/if}

related: {if $dm.related_content.has_content}listed below{else}none{/if}
```

Ausgabe auf alpha:

```text
teaser: empty, use full_intro
related: none
```

Beachten Sie die Leerzeile im Template zwischen den beiden Zeilen: Der Parser entfernt den Zeilenumbruch direkt nach `{/if}`, also braucht es
eine Leerzeile, um einen zu behalten (siehe die Leerraumregel in [Fehler und Fehlersuche](fehler-und-fehlersuche.md)).

## Verwandte Seiten

- [Das Objektmodell in Templates](das-objektmodell-in-templates.md)
- [Referenz von Inhaltsattribut und Version](referenz-von-inhaltsattribut-und-version.md)
- [Attribute view gui](../../../en/us/help/designers/templates/attribute-view-gui.md)
- [Ansichts-Templates von Datentypen](../../../en/us/help/designers/templates/datatype-view-templates.md)
- [Datentypen mit Beispielen](../../../en/us/examples/datatypes/README.md)
- [Freie Dokumentation](../../../en/us/help/free-documentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
