---
title: "Lektion 2.6: include, node_view_gui und attribute_view_gui"
audience: trainer, developer
category: training
order: 26
summary: Die Teilnehmenden teilen Templates mit include und benannten Parametern auf, zeigen Knoten mit node_view_gui in einem View-Modus und zeigen Inhaltsfelder mit attribute_view_gui über die Templates ihres Datentyps an. 50 Minuten.
keywords: entwickler, template-sprache, include, design:, node_view_gui, view-modus, line, full, attribute_view_gui, data_map, datentyp-template, content_view_gui, developers course, template language, view mode, datatype template
type: help
---
# Lektion 2.6: include, node_view_gui und attribute_view_gui

Echte Templates sind klein und verwenden einander wieder. Diese Lektion vermittelt die drei Wege, auf denen ein
Template ein anderes aufruft: `{include}` für einen Teil, den Sie selbst schreiben, `{node_view_gui}`, um einen Knoten
in einem View-Modus wie `line` zu zeigen, und `{attribute_view_gui}`, um ein Feld eines Inhaltselements mit dem
Template seines Datentyps zu zeigen. Zusammen setzen sie jede Seite einer Website zusammen.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Entwickler](../README.md) |
| Modul | [Modul 2: Die Template-Sprache](README.md) |
| Dauer | 50 Minuten (20 Präsentation, 30 Übung) |
| Niveau | Fortgeschrittene |
| Zielgruppe | Entwickler und Template-Bauer |
| Voraussetzungen | [Lektion 2.5](links-und-design-dateien.md) |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- ein Template mit `{include uri='design:...'}` einbinden und ihm benannte Parameter übergeben;
- einen Knoten mit `{node_view_gui content_node=$child view='line'}` in einem View-Modus zeigen;
- ein Feld mit `{attribute_view_gui attribute=$node.data_map.<identifier>}` zeigen;
- das Template, das jede Funktion verwendet hat, in `node/view/` und `content/datatype/view/` finden;
- erklären, warum Rich Text mit `attribute_view_gui` angezeigt und nicht direkt ausgegeben wird.

## Hinweise für Trainer

### So präsentieren Sie es

Quellen: [Templates und Design, Teil 6](../../../../anleitungen/templates-und-design.md#6-die-template-sprache-in-zehn-minuten)
und die Funktionsdefinitionen in `kernel/common/eztemplateautoload.php`.

1. **`include`** (4 Minuten). `{include uri='design:parts/header.tpl' title=$node.name}`. `design:` bedeutet „ein
   Pfad innerhalb der Design-Kette“; die zusätzlichen Parameter werden im eingebundenen Template zu Variablen. Das
   eingebundene Template sieht seine Parameter, nicht die übrigen Variablen des Aufrufers.
2. **`node_view_gui`** (5 Minuten). `{node_view_gui content_node=$child view='line'}` rendert den Knoten mit
   `node/view/line.tpl` aus der Design-Kette oder mit einem passenden Override (Modul 3). Zeigen Sie
   `ls design/standard/templates/node/view/`: `full`, `line`, `listitem`, `embed`, `text_linked` und weitere.
3. **`data_map`** (3 Minuten). `$node.data_map` ist ein Hash der Felder des Elements nach Attributbezeichner:
   `$node.data_map.title`, `$node.data_map.body`.
4. **`attribute_view_gui`** (5 Minuten). `{attribute_view_gui attribute=$node.data_map.body}` wählt das Template des
   Datentyps des Feldes aus `content/datatype/view/`, zum Beispiel `ezstring.tpl` oder das Rich-Text-Template. Zeigen
   Sie `ls design/standard/templates/content/datatype/view/`.
5. **Warum nicht direkt ausgeben?** (3 Minuten). Der Inhalt eines Rich-Text-Feldes sind strukturierte Daten, kein HTML.
   Erst das Template seines Datentyps macht daraus sicheres HTML. Ein Bildfeld braucht seinen Alias und seinen
   Alternativtext. Überlassen Sie das dem Datentyp.

### Häufige Fragen

**Kann ich einem eingebundenen Template einen ganzen Knoten übergeben?** Ja: `{include uri='design:parts/teaser.tpl' item=$child}`.

**Woher kommt `view='line'`?** Aus den Templates der View-Modi in `node/view/`. Eine Website kann eigene View-Modi
hinzufügen; `[ContentSettings]` und die Override-Regeln entscheiden, welche Templates es gibt (Modul 3).

**Was ist `content_view_gui`?** Dieselbe Idee für ein Inhaltsobjekt statt eines Knotens: `{content_view_gui
content_object=$object view='embed'}`, mit Templates in `content/view/`.

### Stolperfallen

- **Ein fehlendes eingebundenes Template.** Ein `{include}` einer Datei, die kein Design hat, gibt nichts aus; die
  Debug-Ausgabe nennt das Template, das nicht gefunden wurde. Prüfen Sie den Pfad nach `design:`.
- **Ein Bezeichner, den es nicht gibt.** `$node.data_map.titel` (ein Tippfehler) liefert nichts zum Anzeigen. Schlagen
  Sie den Bezeichner in der Klasse nach (Modul 4) oder mit `{$node.data_map|attribute( 'show' )}`.
- **`node_view_gui` mit `view='full'` in einer Liste aufrufen.** Das rendert pro Element eine ganze Seite. Listen
  verwenden `line`, `listitem` oder einen eigenen View-Modus.

## Praktische Übung

Verwenden Sie die Spielwiese aus Lektion 2.1 und führen Sie sie nach jedem Schritt aus.

1. Legen Sie `extension/mysite_hello/scripts/teaser.tpl` an:

   ```
   <article>
       <h2><a href={$item.url_alias|ezurl}>{$item.name|wash}</a></h2>
       <p>{$label|wash}</p>
   </article>
   ```

2. Binden Sie es in `play.tpl` über seinen Dateipfad ein und übergeben Sie zwei Parameter:

   ```
   {include uri='file:extension/mysite_hello/scripts/teaser.tpl' item=$node label='Included'}
   ```

3. Führen Sie die Spielwiese aus. Binden Sie es dann in einem `foreach` für die ersten drei Kinder ein.
4. Zeigen Sie das erste Kind im View-Modus `line`:

   ```
   {foreach $node.children as $child max 1}
       {node_view_gui content_node=$child view='line'}
   {/foreach}
   ```

5. Listen Sie die Bezeichner der Felder des Knotens auf: `{foreach $node.data_map as $identifier => $attribute}{$identifier} {/foreach}`.
6. Zeigen Sie ein Feld mit dem Template seines Datentyps, mit einem Bezeichner aus Schritt 5 (bei einem Ordner `name`):
   `{attribute_view_gui attribute=$node.data_map.name}`.
7. Zeigen Sie den Datentyp jedes Feldes: `{foreach $node.data_map as $identifier => $attribute}{$identifier}={$attribute.data_type_string} {/foreach}`.
8. Suchen Sie die in den Schritten 4 und 6 verwendeten Templates in `design/standard/templates/node/view/` und
   `design/standard/templates/content/datatype/view/` (das Design der Website kann sie überschreiben).
9. Committen Sie beide Templates.

## Erwartetes Ergebnis

- Schritt 3 gibt ein `<article>` für Knoten 2 und je eines für drei Kinder aus.
- Schritt 4 gibt das HTML der Zeilenansicht des ersten Kindes aus.
- Schritt 5 listet je nach Klasse Bezeichner wie `name`, `short_description` oder `description`.
- Schritt 7 gibt Paare wie `name=ezstring` aus.
- Der Trainer prüft, dass alle Teilnehmenden die Template-Datei hinter Schritt 6 nennen können.

## Quiz

1. Was bedeutet `design:` in `{include uri='design:parts/header.tpl'}`?
2. Welche Funktion zeigt einen Knoten im View-Modus `line`?
   A. `include` B. `attribute_view_gui` C. `node_view_gui` D. `content_view_gui`
3. Was ist `$node.data_map`?
4. Wo findet `attribute_view_gui` das Template für ein Feld des Datentyps `ezstring`?
5. Warum sollte ein Rich-Text-Feld nicht mit `{$attribute.content}` ausgegeben werden?

<details>
<summary>Antworten</summary>

1. Einen Pfad innerhalb der Design-Kette: Das erste Design, das `templates/parts/header.tpl` hat, antwortet.
2. **C.** `{node_view_gui content_node=$child view='line'}`.
3. Ein Hash der Felder des Elements (der Attribute des Inhaltsobjekts) nach Attributbezeichner.
4. In `content/datatype/view/ezstring.tpl` der Design-Kette, sofern kein Override passt.
5. Sein Inhalt sind strukturierte Daten; erst das Template seines Datentyps macht daraus sicheres HTML.

</details>

## Weiterführende Literatur

- [Templates und Design](../../../../anleitungen/templates-und-design.md): die Template-Suche und Overrides.
- [Das Inhaltsmodell und das Bearbeiten von Inhalten](../../../../anleitungen/inhaltsmodell-und-bearbeitung.md): Klassen,
  Attribute und Datentypen hinter `data_map`.
- [View-Modi in der Versionsvorschau](../../../../../eng-us/features/6.0/version-preview-view-modes.md): View-Modi in der
  Vorschau.

## Verwandte Seiten

- [Modul 2: Die Template-Sprache](README.md)
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
