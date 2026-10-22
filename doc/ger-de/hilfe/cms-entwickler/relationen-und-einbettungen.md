---
title: Relationen und Einbettungen
audience: cms-developer
category: cms-development
order: 14
summary: Für CMS-Entwickler, wie Objekte aufeinander verweisen. Die Seite behandelt Relationsattribute, Einbettungen und Links in Rich Text, die vier Relationstypen, das Lesen von Relationen und umgekehrten Relationen in Templates und PHP und was geschieht, wenn ein verknüpftes Objekt entfernt wird.
keywords: Relationen, Objektrelationen, Verknüpfungen, ezobjectrelationlist, ezobjectrelation, verknüpfte Objekte, umgekehrte Relationen, Einbettung, embed, embed-inline, Link, Relationstypen, Klasseneinschränkung, Standardort, Prüfung umgekehrter Relationen, related_objects, relations, object relations, related objects, reverse relations, reverse relation check
type: help
---
# Relationen und Einbettungen

Diese Seite richtet sich an Entwickler, die Inhalte modellieren, die auf andere Inhalte verweisen: eine Veranstaltung
und ihre Referenten, ein Artikel und seine Bilder, ein Produkt und seine Downloads. Exponential erfasst jeden solchen
Verweis als **Relation** zwischen Objekten, sodass Sie ihm in beide Richtungen folgen können und wissen, was bricht,
bevor etwas entfernt wird.

## Inhalt

1. [Drei Arten, auf Inhalte zu verweisen](#drei-arten-auf-inhalte-zu-verweisen)
2. [Relationsattribute](#relationsattribute)
3. [Einbettungen und Links in Rich Text](#einbettungen-und-links-in-rich-text)
4. [Die Relationstypen](#die-relationstypen)
5. [Relationen in Templates](#relationen-in-templates)
6. [Umgekehrte Relationen](#umgekehrte-relationen)
7. [Relationen in PHP](#relationen-in-php)
8. [Verknüpfte Inhalte entfernen](#verknüpfte-inhalte-entfernen)

## Drei Arten, auf Inhalte zu verweisen

| Art | Verwenden Sie sie, wenn | Beispiel |
|---|---|---|
| Ein Relationsattribut (`ezobjectrelationlist`) | der Verweis strukturierte Daten sind, die das Template platziert | `speakers` einer Veranstaltung, `related_articles` eines Artikels |
| Eine Einbettung in Rich Text | ein Redakteur ein Objekt an einer Stelle im Text platziert | ein Bild oder ein Video mitten in einem Artikel |
| Ein Link in Rich Text | Text auf eine andere Seite verweist | "lesen Sie unseren [Datenschutzhinweis]" |

Eine vierte Möglichkeit, eine zweite **Platzierung**, setzt dasselbe Objekt an zwei Stellen des Baums. Sie ist keine
Relation: Verwenden Sie sie nur, wenn das Objekt wirklich an beide Stellen gehört
([Objekte, Knoten und Platzierungen](objekte-knoten-und-platzierungen.md)).

## Relationsattribute

`ezobjectrelationlist` (Objekt Vernüpfungsliste) enthält eine geordnete Liste von Objekten. Im Klasseneditor legen Sie
je Attribut fest (`kernel/classes/datatypes/ezobjectrelationlist/ezobjectrelationlisttype.php`):

| Einstellung | Wirkung |
|---|---|
| Auswahlmethode | *Durchsuchen* (im Baum auswählen) oder eine Liste von Kandidaten im Bearbeitungsformular |
| Erlaubte Klassen (`class_constraint_list`) | nur Objekte dieser Klassen können gewählt werden, zum Beispiel `exp_speaker` |
| Standardort (`default_placement`) | wo das Durchsuchen beginnt und wohin Objekte kommen, die aus dem Bearbeitungsformular angelegt werden |

Ein Bearbeitungsformular oder ein Durchsuchen nimmt für ein Attribut höchstens 1000 verknüpfte Objekte auf
(`MAX_RELATIONS`). `ezobjectrelation` enthält ein einzelnes Objekt mit weniger Optionen; bevorzugen Sie den
Listen-Datentyp auch für ein einziges Ziel
([Datentypen wählen](datentypen-waehlen.md#entscheidungen-die-sich-schwer-rückgängig-machen-lassen)).

## Einbettungen und Links in Rich Text

In `ezxmltext` ist eine Einbettung `<embed object_id="..." view="embed"/>` (oder `node_id`), und eine Inline-Einbettung
ist `<embed-inline>`. Links auf Inhalte verwenden `<link object_id="...">` oder `node_id`, und externe Links zeigen auf
einen Eintrag der Linkliste. Jede Einbettung wird mit dem Ansichtsmodus **embed** der Klasse des eingebetteten Objekts
gezeichnet, sodass das Artikel-Template nie wissen muss, was eingebettet wurde. Welche Ansichtsmodi und Klassen
Redakteure für Einbettungen wählen können, legen `content.ini [embed]` und `[embed-inline]` fest
(`AvailableViewModes[]`, `AvailableClasses[]`). Templates: [Embed-Ansicht](../designer/vorlagen/embed-ansicht.md) und
[Embed-inline-Ansicht](../designer/vorlagen/embed-inline-ansicht.md).

## Die Relationstypen

Jede Relation ist eine Zeile in `ezcontentobject_link` mit einem Typ, Konstanten von `eZContentObject`:

| Konstante | Wert | Entsteht durch |
|---|---|---|
| `RELATION_COMMON` | 1 | die **Verknüpfungen** eines Objekts, die Redakteure in seiner Liste verknüpfter Objekte hinzufügen |
| `RELATION_EMBED` | 2 | eine Einbettung in Rich Text |
| `RELATION_LINK` | 4 | einen Link auf Inhalte in Rich Text |
| `RELATION_ATTRIBUTE` | 8 | ein Relationsattribut (`ezobjectrelation`, `ezobjectrelationlist`) |

Die Relationen werden je Version gespeichert, eine alte Version behält also die Relationen, die sie hatte.

## Relationen in Templates

Der Inhalt des Relationsattributs ist ein Array mit dem Schlüssel `relation_list`; jedes Element hat
`contentobject_id` und `node_id`. Die einfachste Ausgabe ist `attribute_view_gui`, das jedes Ziel mit seiner
Embed-Ansicht zeichnet. Für eigenes Markup:

```smarty
{def $speakers = $node.data_map.speakers}
{if $speakers.has_content}
<ul class="exp-speakers">
    {foreach $speakers.content.relation_list as $item}
        {def $speaker = fetch( 'content', 'object', hash( 'object_id', $item.contentobject_id ) )}
        {if and( $speaker, $speaker.can_read )}
            <li>{content_view_gui view='line' content_object=$speaker}</li>
        {/if}
        {undef $speaker}
    {/foreach}
</ul>
{/if}
```

Prüfen Sie `can_read`: Eine Relation gewährt keinen Zugriff auf ihr Ziel. Das mitgelieferte View-Template des
Datentyps (`design/standard/templates/content/datatype/view/ezobjectrelationlist.tpl`) überspringt außerdem Ziele im
Papierkorb und verborgene Ziele; der Datentyp ist unter
[ezobjectrelationlist](../../beispiele/datentypen/ezobjectrelationlist.md) beschrieben.

Der Fetch `related_objects` gibt die Ziele eines Objekts direkt zurück, auf Wunsch nach Attribut gefiltert:

```smarty
{def $related = fetch( 'content', 'related_objects', hash(
        'object_id', $node.contentobject_id,
        'attribute_identifier', 'exp_event/speakers' ) )}
```

## Umgekehrte Relationen

`reverse_related_objects` beantwortet die umgekehrte Frage: "Welche Objekte zeigen auf dieses?" Auf der Seite eines
Referenten listet es die Veranstaltungen des Referenten, ohne ein Feld beim Referenten:

```smarty
{def $events = fetch( 'content', 'reverse_related_objects', hash(
        'object_id', $node.contentobject_id,
        'all_relations', true() ) )}
```

Deshalb speichern Sie einen Verweis nur auf einer Seite: Die andere Seite ist einen Fetch entfernt. Für beide gibt es
Zählvarianten (`related_objects_count`, `reverse_related_objects_count`).

## Relationen in PHP

```php
<?php
$object = eZContentObject::fetchByRemoteID( 'exp-event-2026-opening' );
$speakers = $object->relatedObjects( false, false, 0, false,
    array( 'AllRelations' => eZContentObject::RELATION_ATTRIBUTE ) );
$pointingHere = $object->reverseRelatedObjectList( false, 0, false,
    array( 'AllRelations' => true ) );
```

Lesen Sie die Signaturen in `kernel/classes/ezcontentobject.php`, bevor Sie sich auf einen Parameter verlassen; die
Referenz des Buchs enthält die üblichen Aufrufe
([eZContentObject](../erweiterungsentwicklung/teil-8-referenz/ezcontentobject.md)). Um ein Relationsattribut aus einem
Import zu setzen, übergeben Sie die Objekt-IDs der Ziele im `fromString()`-Format des Attributs
([Inhalte aus PHP](inhalte-aus-php.md)).

## Verknüpfte Inhalte entfernen

Wer ein Objekt entfernt oder verbirgt, auf das andere zeigen, hinterlässt Lücken. Mit `content.ini [RemoveSettings]
ReverseRelationCheck=warn` (und demselben Schlüssel in `[HideSettings]`), dem mitgelieferten Standard, listet die
Administration die Objekte auf, die darauf verweisen, einschließlich Blöcken von Layouts, bevor sie etwas entfernt
oder verbirgt ([Prüfung umgekehrter Relationen](../../funktionen/6.0/pruefung-umgekehrter-beziehungen.md)). Templates sollten
trotzdem damit rechnen, dass ein Ziel fehlt, verborgen oder nicht lesbar ist, wie es das Beispiel oben tut.

## Verwandte Seiten

- [Rich Text](rich-text.md)
- [Ein Inhaltsmodell entwerfen, Schritt 4](ein-inhaltsmodell-entwerfen.md#schritt-4-entscheiden-was-eine-relation-ist)
- [Inhalte in Templates abrufen](inhalte-in-templates-abrufen.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
