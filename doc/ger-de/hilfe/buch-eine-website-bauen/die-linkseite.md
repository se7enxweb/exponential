---
title: "Die Linkseite: Kategorien, Linkobjekte und ein Baummenü"
audience: builder, designer
category: books
order: 170
summary: Ein Linkbereich aus Kategorie-Ordnern und Link-Objekten, angezeigt als aufklappbarer Baum, und derselbe Inhalt an zwei Stellen mit einem zweiten Ort.
keywords: Links, Linkklasse, Ordner, Kategorie, Baummenü, Ort, zusätzlicher Ort, Teilbaum, Sektion, links, link class, folder, category, tree menu, treemenu, location, additional location, subtree, section, Building book
type: help
---
# Die Linkseite: Kategorien, Linkobjekte und ein Baummenü

Die letzte Seite der Clubwebsite ist eine Sammlung von Links in Kategorien, angezeigt als Baum: Die Kategorie-Ordner klappen auf
und zu, und die Links darin werden aufgelistet. Auf die Baumform kommt es an: Die Struktur der Website und die Struktur des
Menüs sind dasselbe ([der Knotenbaum](../buch-lernen/der-knotenbaum-und-die-sektionen.md)).

## Der Inhalt

1. Legen Sie an der Wurzel einen Ordner **Links** an und geben Sie ihm eine eigene Sektion (**Links**), aus demselben Grund
   wie zuvor: Ein Template-Override kann die Sektion berücksichtigen.
2. Darin Ordner als Kategorien: *Chess sites*, *Educational sites*, *Local sites*, *News sites*, jeder mit einer Beschreibung.
3. In jedem Objekte der mitgelieferten Klasse **Link**: ein **Name** und eine **Location** (der Link mit eigenem Text; die
   Beschreibung kann leer bleiben). Das Buch nannte diese Felder Title und URL.

## Das Template

Das Menü des Buches wird aus dem Pfad und den Unterelementen des Knotens gebaut. Heute ist genau dafür der Operator `treemenu`
da ([Menü- und Baumoperatoren](../template-operatoren/menue-und-baum-operatoren.md)): Er nimmt den Pfad der aktuellen Seite und
gibt die Menüeinträge der Reihe nach zurück, jeden mit seiner Ebene und einer Markierung für den ausgewählten. Eine
baumartige Anzeige für den Ordner Links:

```
{def $items=treemenu( $module_result.path, false(), false(), 1, 3 )}
<ul class="link-tree">
{foreach $items as $item}
    <li class="level-{$item.level}{if $item.is_selected} selected{/if}">
        <a href={$item.url_alias|ezurl}>{$item.text|wash}</a>
    </li>
{/foreach}
</ul>
```

(Rücken Sie im Stylesheet über die Klasse `level-N` ein.) Die Einträge einer Kategorie sind die Link-Objekte; zeigen Sie
einen mit dem eigenen Text und der Adresse des Links:

```
{foreach fetch( 'content', 'list', hash( 'parent_node_id', $node.node_id,
                                         'class_filter_type', 'include',
                                         'class_filter_array', array( 'link' ) ) ) as $link}
    <p>{attribute_view_gui attribute=$link.data_map.location}</p>
{/foreach}
```

(Die Klasse Link hat die Attribute `name`, `description` und `location`, einen Datentyp, der einen Link mit seinem Text
speichert. Die eigene Ansicht des Attributs zeichnet ihn. Prüfen Sie die Bezeichner Ihrer Website unter **Setup > Klassen**.)
Legen Sie das Template in eine Regel für die **Sektion** `Links` (`Match[section_identifier]=links`, Quelle
`node/view/full.tpl`), damit jeder Ordner darunter als Baum gezeichnet wird.

## Derselbe Inhalt an zwei Stellen

Das Buch endet mit einem kleinen Kniff: einem Link, der zu zwei Kategorien gehört. Sie kopieren das Objekt nicht. Sie geben
ihm einen zweiten **Ort**: im Bearbeitungsformular **Orte > Orte hinzufügen**, und wählen Sie die zweite Kategorie. Beide
Kategorien listen nun dasselbe Objekt, und eine Änderung zeigt sich in beiden ([der Knotenbaum und die Sektionen](../buch-lernen/der-knotenbaum-und-die-sektionen.md),
[Objekte anlegen und bearbeiten](../buch-lernen/objekte-anlegen-und-bearbeiten.md)).

## Was Sie haben

Eine Startseite, Neuigkeiten mit Archiv, eine Mitgliederliste, ein moderiertes Gästebuch und einen Baum aus Links: fünf
Seiten, zwei Erweiterungen, eine Handvoll Klassen und Templates, kein PHP. Wie es weitergeht: Die vollständige Liste dessen,
was sich der Club noch wünschen könnte, steht in
[Eine Website bauen, ohne Code zu schreiben](../erweiterungsentwicklung/teil-2-builder/eine-website-ohne-code-bauen.md)
(Bereiche nur für Mitglieder, Menüs mit Cache) und in [Exponential lernen](../buch-lernen/README.md).

## Quelle und Lizenz

Bearbeitet nach dem Kapitel über die Linkseite des Tutorials *Building an eZ publish site* (geschrieben für eZ publish 3.2,
2003), ursprünglich von ez.no, 2024 von 7x erneut unter der GNU Free Documentation License veröffentlicht,
[Book-Building-An-eZPublish-Site](https://github.com/se7enxweb/Book-Building-An-eZPublish-Site). Copyright (c)
1999-2024 7x und Graham Brookins. Lizenziert unter der GNU Free Documentation License, Version 1.3 oder einer späteren
Version, ohne unveränderliche Abschnitte, ohne vordere und ohne hintere Umschlagtexte ([die Lizenz](../../../eng-us/LICENSE-GFDL-1.3.txt)).
Änderungen: für Exponential 6 neu geschrieben: der Operator treemenu und die Klasse Link, wie sie ausgeliefert werden.
