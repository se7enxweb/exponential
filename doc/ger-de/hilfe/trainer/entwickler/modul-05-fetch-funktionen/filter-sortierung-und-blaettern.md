---
title: "Lektion 5.2: Filter, Sortierung und Seitennavigation"
audience: trainer, developer
category: training
order: 52
summary: Die Teilnehmenden grenzen Fetches nach Klasse und Attribut ein, holen mit tree und depth einen ganzen Teilbaum, sortieren nach einem oder mehreren Feldern und teilen eine lange Liste mit offset, view_parameters und dem Google-Navigator in Seiten auf. 55 Minuten.
keywords: entwickler, fetch, tree, tree_count, depth, class_filter_type, class_filter_array, attribute_filter, sort_by, offset, limit, seitennavigation, sortierung, filter, teilbaum, view_parameters, navigator, google.tpl, DefaultClassFilterType, developers course, filters, sorting, paging
type: help
---
# Lektion 5.2: Filter, Sortierung und Seitennavigation

Echte Listen sind gefiltert und sortiert: die zehn neuesten Artikel unterhalb eines Bereichs, die Veranstaltungen
dieses Monats, Seite 3 einer Suche. Diese Lektion vermittelt die Parameter, die das in der Datenbank erledigen, sodass
das Template nur die Elemente erhält, die es anzeigt, und das Navigator-Template des Kernels, das die Seitenlinks
zeichnet.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Entwickler](../README.md) |
| Modul | [Modul 5: Fetch-Funktionen](README.md) |
| Dauer | 55 Minuten (20 Präsentation, 35 Übung) |
| Niveau | Fortgeschrittene |
| Zielgruppe | Entwickler und Template-Entwickler |
| Voraussetzungen | [Lektion 5.1](knoten-und-listen-abrufen.md) |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- mit `class_filter_type` und `class_filter_array` nach Klasse filtern;
- mit `attribute_filter` nach Attributwerten filtern;
- mit `tree`, `tree_count` und `depth` einen Teilbaum holen;
- mit `sort_by` nach einem oder mehreren Feldern sortieren;
- eine Liste mit `offset`, `limit`, `$view_parameters.offset` und `design:navigator/google.tpl` in Seiten aufteilen.

## Hinweise für Trainer

### So präsentieren Sie es

Quellen: `kernel/content/function_definition.php`, `design/standard/templates/node/view/full.tpl` (seine
Seitennavigation) und
[Das Inhaltsmodell und das Bearbeiten von Inhalten, Teil 9](../../../../anleitungen/inhaltsmodell-und-bearbeitung.md#standard-klassenfilter-für-fetches-template-entwickler).

1. **Klassenfilter** (4 Minuten). `'class_filter_type', 'include', 'class_filter_array', array( 'article' )`.
   Ohne `class_filter_type` wird das Array ignoriert, sofern nicht `content.ini [FetchSettings] DefaultClassFilterType`
   gesetzt ist; schreiben Sie immer beide.
2. **`tree` und `depth`** (3 Minuten). `tree` geht unter die Unterelemente hinab; `depth` begrenzt die Tiefe, gezählt
   vom übergeordneten Knoten aus (`'depth', 2` sind Kinder und Enkel). `tree_count` zählt.
3. **Attributfilter** (4 Minuten). `'attribute_filter', array( array( 'article/title', 'like', '*news*' ) )`:
   `class/attribute`, ein Operator (`=`, `!=`, `<`, `>`, `like`, `in`, `between` ...), ein Wert. Mehrere Bedingungen
   werden standardmäßig mit „und“ verknüpft. Eingebaute Felder wie `published` oder `name` funktionieren ebenfalls.
4. **Sortierung** (3 Minuten). `'sort_by', array( 'published', false() )`: Feld und aufsteigend (`true()`) oder
   absteigend (`false()`). Mehrere Felder: `array( array( 'priority', true() ), array( 'name', true() ) )`.
5. **Seitennavigation** (6 Minuten). Eine Vollansicht erhält `$view_parameters`; `/(offset)/20` in der Adresse setzt
   `$view_parameters.offset`. Holen Sie mit `'offset', $view_parameters.offset`, zählen Sie mit `list_count` und
   binden Sie den Navigator so ein, wie es `node/view/full.tpl` des Kernels tut:

   ```
   {include name=navigator
            uri='design:navigator/google.tpl'
            page_uri=$node.url_alias
            item_count=$list_count
            view_parameters=$view_parameters
            item_limit=$page_limit}
   ```

### Häufige Fragen

**Was ist der Unterschied zwischen `list` und `tree`?** `list` gibt nur die Kinder zurück; `tree` den ganzen
Teilbaum, begrenzt durch `depth`.

**Wie sortiere ich so, wie es die Redaktion in der Administration gewählt hat?** Verwenden Sie die eigene
Sortierreihenfolge des Knotens: `'sort_by', $node.sort_array`.

**Kann ich nach einer Beziehung oder einem Schlagwort filtern?** Dafür gibt es Fetches: `related_objects`,
`reverse_related_objects` und `keyword` in `kernel/content/function_definition.php`.

### Stolperfallen

- **`class_filter_array` ohne `class_filter_type`.** Auf einer Standardinstallation wird es ignoriert: Die Liste
  enthält jede Klasse.
- **`tree` ohne `depth` und `limit`.** Das kann die ganze Website zurückgeben.
- **Attributfilter auf einem Sortierschlüssel.** Gefiltert wird über den Sortierschlüssel des Attributs: Ein
  Rich-Text-Feld lässt sich nicht sinnvoll filtern.

## Praktische Übung

Verwenden Sie den Template-Spielplatz und führen Sie ihn nach jedem Schritt aus.

1. Zählen Sie die Ordner im Teilbaum von Knoten 2, drei Ebenen tief:

   ```
   {fetch( 'content', 'tree_count', hash( 'parent_node_id', 2,
                                          'depth', 3,
                                          'class_filter_type', 'include',
                                          'class_filter_array', array( 'folder' ) ) )}
   ```

2. Entfernen Sie `class_filter_type` aus Schritt 1 und führen Sie es erneut aus. Vergleichen Sie die Zahlen.
3. Listen Sie drei Elemente des Teilbaums nach Namen sortiert auf und überspringen Sie das erste:

   ```
   {foreach fetch( 'content', 'tree', hash( 'parent_node_id', 2, 'depth', 3, 'limit', 3, 'offset', 1,
                                             'sort_by', array( 'name', true() ) ) ) as $item}
       {$item.name|wash} (depth {$item.depth})
   {/foreach}
   ```

4. Filtern Sie Ordner, deren Name ein `e` enthält:

   ```
   {foreach fetch( 'content', 'tree', hash( 'parent_node_id', 2, 'limit', 5,
                                             'class_filter_type', 'include', 'class_filter_array', array( 'folder' ),
                                             'attribute_filter', array( array( 'folder/name', 'like', '*e*' ) ) ) ) as $f}
       {$f.name|wash}
   {/foreach}
   ```

5. Sortieren Sie die Unterelemente von Knoten 2 so, wie die Redaktion sie eingestellt hat: `'sort_by', $node.sort_array`.
6. Schreiben Sie eine Liste mit Seitennavigation für den Vollansichts-Override aus Lektion 3.2: Fügen Sie
   `mysite_article.tpl` eine Liste der Geschwister des Artikels mit `limit` 2, `offset` `$view_parameters.offset`,
   der Anzahl und dem oben gezeigten Navigator-Include hinzu (setzen Sie `$page_limit` auf 2 und `$list_count` mit
   `list_count`).
7. Öffnen Sie einen Artikel auf der Website und folgen Sie den Seitenlinks; prüfen Sie, dass die Adresse
   `/(offset)/2` erhält.
8. Committen Sie die Templates.

## Erwartetes Ergebnis

- Schritt 2 gibt eine größere Zahl aus als Schritt 1: Ohne `class_filter_type` wird der Klassenfilter ignoriert.
- Schritt 3 gibt drei Namen in alphabetischer Reihenfolge mit ihrer Tiefe aus.
- Schritt 4 gibt Ordnernamen aus, die ein `e` enthalten.
- Schritt 7: Der Navigator zeigt Seitenzahlen; jede Seite zeigt zwei Geschwister; die Adresse enthält `(offset)`.
- Der Trainer prüft den Navigator auf der Website einer Person.

## Quiz

1. Welche zwei Parameter filtern nach Klasse?
2. Was gibt `'depth', 2` in einem `tree`-Fetch zurück?
   A. Nur die Kinder B. Kinder und Enkel C. Nur die Enkel D. Den ganzen Teilbaum
3. Schreiben Sie ein `sort_by` für „die neuesten zuerst“.
4. Woher erhält eine Vollansicht den Offset der aktuellen Seite?
5. Warum reicht `class_filter_array` allein auf einer Standardinstallation nicht aus?

<details>
<summary>Antworten</summary>

1. `class_filter_type` und `class_filter_array`.
2. **B.** Kinder und Enkel.
3. `'sort_by', array( 'published', false() )`.
4. Aus `$view_parameters.offset`, gesetzt durch `/(offset)/<n>` in der Adresse.
5. Ohne `class_filter_type` wird das Array ignoriert, sofern nicht `DefaultClassFilterType` in `content.ini` gesetzt ist.

</details>

## Weiterführende Literatur

- [Das Inhaltsmodell und das Bearbeiten von Inhalten](../../../../anleitungen/inhaltsmodell-und-bearbeitung.md#standard-klassenfilter-für-fetches-template-entwickler):
  `DefaultClassFilterType`.
- [Eigene Anzahl Elemente pro Seite](../../../../funktionen/6.0/eigene-anzahl-eintraege-pro-seite.md) und
  [Seitennavigation in Admin-Listen](../../../../funktionen/6.0/seitenweise-listen-im-admin.md): Seitennavigation in der
  Administration.
- [Sortierung der Bestellliste](../../../../funktionen/6.0/sortierung-der-bestellliste.md): Sortierung in einer anderen Liste des
  Kernels.

## Verwandte Seiten

- [Modul 5: Fetch-Funktionen](README.md)
- [Entwickler: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../trainer-hier-beginnen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
