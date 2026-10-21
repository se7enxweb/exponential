---
title: "Lektion 4.2: Datentypen und ihre Templates"
audience: trainer, developer
category: training
order: 42
summary: Die Teilnehmenden finden die Ansichts- und Bearbeitungs-Templates eines Datentyps, zeigen ein Feld mit attribute_view_gui und überschreiben die Ansicht eines Attributs für eine Klasse mit den Schlüsseln attribute_identifier und class_identifier. 45 Minuten.
keywords: entwickler, datentyp, datentyp-template, ezstring, ezxmltext, ezimage, content/datatype/view, content/datatype/edit, attribute_view_gui, attribute_edit_gui, override, attribute_identifier, class_identifier, data_type_string, content, developers course, datatype, datatype template
type: help
---
# Lektion 4.2: Datentypen und ihre Templates

Jedes Attribut wird vom Template seines Datentyps angezeigt: eine Textzeile von `ezstring.tpl`, Rich Text von seinem
eigenen, ein Bild von `ezimage.tpl`. Diese Lektion zeigt den Teilnehmenden, wo diese Templates liegen, was der
`content` eines Attributs bei verschiedenen Datentypen ist und wie man die Ansicht eines Attributs einer Klasse mit
einem Override ändert, statt das Datentyp-Template für die ganze Website zu ändern.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Entwickler](../README.md) |
| Modul | [Modul 4: Inhaltsklassen und Datentypen](README.md) |
| Dauer | 45 Minuten (15 Präsentation, 30 Übung) |
| Niveau | Fortgeschrittene |
| Zielgruppe | Entwickler und Template-Bauer |
| Voraussetzungen | [Lektion 4.1](klassen-attribute-und-bezeichner.md) und [Lektion 3.2](../modul-03-designs-overrides-und-debugging/override-regeln.md) |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- die Templates eines Datentyps in `content/datatype/view/` und `content/datatype/edit/` finden;
- `data_type_string`, `has_content` und `content` eines Attributs in einem Template lesen;
- erklären, warum `content` bei `ezstring` ein String ist, bei `ezxmltext` oder `ezimage` aber ein Objekt;
- das Ansichts-Template eines Attributs mit `Match[attribute_identifier]` und `Match[class_identifier]` überschreiben.

## Hinweise für Trainer

### So präsentieren Sie es

Quellen: `kernel/common/eztemplateautoload.php` (die Definition von `attribute_view_gui`) und
`design/standard/templates/content/datatype/`.

1. **Wo die Templates liegen** (3 Minuten). `ls design/standard/templates/content/datatype/view/` und
   `.../edit/`: eine Datei pro Datentyp-String, `ezstring.tpl`, `ezimage.tpl` und so weiter. `attribute_view_gui`
   verwendet `view/`, `attribute_edit_gui` (im Bearbeitungsformular) `edit/`. Ein Attribut als Informationssammler
   verwendet `collect/`.
2. **Was `content` ist** (4 Minuten). `$attribute.content` ist das, was der Datentyp zurückgibt: ein String bei
   `ezstring`, eine Ganzzahl bei `ezinteger`, ein Objekt bei `ezxmltext` oder `ezimage`. `$attribute.has_content`
   sagt, ob etwas gespeichert ist. Datentyp-Templates kennen ihren Inhalt; allgemeiner Code sollte das nicht.
3. **Die Override-Schlüssel eines Attributs** (4 Minuten). `attribute_view_gui` setzt die Schlüssel
   `attribute_identifier`, `attribute`, `class_identifier` und `class`. Ein Override mit
   `Source=content/datatype/view/ezstring.tpl`, `Match[class_identifier]=note` und
   `Match[attribute_identifier]=title` ändert nur den Titel von Notizen.
4. **Die Datentyp-Templates des Kernels nicht bearbeiten** (2 Minuten). Der Override betrifft ein Feld; `ezstring.tpl`
   in Ihrem Design zu bearbeiten würde jede Textzeile der Website ändern.
5. **`has_content` vor der Ausgabe** (2 Minuten). `{if $node.data_map.image.has_content}` vermeidet leeres Markup.

### Häufige Fragen

**Wie zeige ich ein Bild in einer bestimmten Größe?** Das Template des Bild-Datentyps nimmt einen Parameter
`image_class` an, zum Beispiel `{attribute_view_gui attribute=$node.data_map.image image_class='medium'}`; die Größen
sind Bild-Aliase in `image.ini`.

**Kann ich einem Datentyp-Template eigene Parameter übergeben?** Ja, jeder zusätzliche Parameter von
`attribute_view_gui` wird im Template zu einer Variablen.

**Wo ist das Template des Bearbeitungsformulars?** In `content/datatype/edit/<datatype>.tpl`, das
`attribute_edit_gui` im Bearbeitungsformular für Inhalte verwendet.

### Stolperfallen

- **Den `content` eines Rich-Text-Feldes ausgeben.** `{$node.data_map.body.content}` gibt ein Objekt aus, kein HTML.
  Verwenden Sie `attribute_view_gui`.
- **Der Bezeichner der falschen Klasse.** `attribute_identifier=title` passt auf jede Klasse mit einem `title`, sofern
  die Regel nicht zusätzlich `Match[class_identifier]` hat.
- **Die Caches vergessen.** Wie bei jedem neuen Override: `ini`, `template-override`, `template` und `content`.

## Praktische Übung

Auf der Entwicklungsinstallation, mit der Klasse **Note** aus Lektion 4.1 und einer veröffentlichten Notiz.
Verwenden Sie die Spielwiese aus Lektion 2.1 und ändern Sie die Knoten-ID in `play.php` in die Knoten-ID der Notiz.

1. Listen Sie die Datentyp-Templates auf: `ls design/standard/templates/content/datatype/view/`.
2. Geben Sie in `play.tpl` für jedes Attribut seinen Bezeichner, seinen Datentyp und aus, ob es Inhalt hat:

   ```
   {foreach $node.data_map as $identifier => $attribute}
   {$identifier}: {$attribute.data_type_string} {cond( $attribute.has_content, 'filled', 'empty' )}
   {/foreach}
   ```

3. Geben Sie den Inhalt des Titels direkt und über sein Datentyp-Template aus und vergleichen Sie:

   ```
   [{$node.data_map.title.content|wash}]
   [{attribute_view_gui attribute=$node.data_map.title}]
   ```

4. Öffnen Sie `design/standard/templates/content/datatype/view/ezstring.tpl` und lesen Sie es.
5. Legen Sie das Override-Template
   `extension/mysite_hello/design/<design>/override/templates/datatype/note_title.tpl` an:

   ```
   <span class="note-title">{$attribute.content|wash|upcase}</span>
   ```

6. Fügen Sie die Regel wie in Lektion 3.2 mit `exp:ini set` im Siteaccess-Bereich der Erweiterung hinzu: Block
   `mysite_note_title`, `Source=content/datatype/view/ezstring.tpl`, `MatchFile=datatype/note_title.tpl`,
   `Subdir=templates`, `Match[class_identifier]=note`, `Match[attribute_identifier]=title`.
7. Leeren Sie die Caches `ini`, `template-override`, `template` und `content` und öffnen Sie die Notiz auf der Website.
8. Öffnen Sie einen Artikel: Sein Titel ist unverändert.
9. Committen Sie das Template und die Einstellungsdatei.

## Erwartetes Ergebnis

- Schritt 2 gibt `title: ezstring filled` und `body: eztext filled` (oder `empty`) aus.
- Schritt 3 gibt zweimal denselben Text aus; den zweiten aus `ezstring.tpl`.
- Schritt 7: Der Titel der Notiz steht in Großbuchstaben in `<span class="note-title">`; mit eingeschalteten
  Pfadkommentaren zeigt der Kommentar `(content/datatype/view/ezstring.tpl)`.
- Schritt 8: Artikel sind unverändert.
- Der Trainer prüft in der Einstellungsdatei einer Person die beiden Zeilen `Match`.

## Quiz

1. In welchem Ordner sucht `attribute_view_gui` nach dem Template eines Attributs `ezimage`?
2. Welche zwei Schlüssel beschränken einen Override auf den Titel von Notizen?
   A. `node` und `object` B. `class_identifier` und `attribute_identifier` C. `section` und `viewmode` D. `class` und `depth`
3. Was sagt Ihnen `$attribute.has_content`?
4. Warum sollte man `ezstring.tpl` nicht in das eigene Design kopieren, um den Titel einer Klasse zu ändern?
5. Was ist `$node.data_map.body.content` bei einem Rich-Text-Feld?

<details>
<summary>Antworten</summary>

1. In `content/datatype/view/ezimage.tpl` der Design-Kette.
2. **B.** `Match[class_identifier]=note` und `Match[attribute_identifier]=title`.
3. Ob im Attribut ein Wert gespeichert ist.
4. Es würde jede Textzeile der Website ändern; ein Override begrenzt die Änderung.
5. Ein Objekt, das den strukturierten Rich Text enthält, kein HTML; sein Datentyp-Template rendert es.

</details>

## Weiterführende Literatur

- [Das Inhaltsmodell und das Bearbeiten von Inhalten](../../../../anleitungen/inhaltsmodell-und-bearbeitung.md): Datentypen im
  Editor.
- [Rich Text: Zusatzzeichen und TeX-Quelltext](../../../../../eng-us/features/6.0/rich-text-supplementary-characters-and-tex-source.md):
  wie Rich Text gespeichert wird.
- [Templates und Design](../../../../anleitungen/templates-und-design.md): Overrides.

## Verwandte Seiten

- [Modul 4: Inhaltsklassen und Datentypen](README.md)
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
