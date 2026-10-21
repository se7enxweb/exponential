---
title: Embed-Inline-Ansicht
audience: designer, developer
category: templates
order: 540
summary: Objekte, die innerhalb einer Zeile von Rich Text eingebettet sind - das Tag und der Ansichtsmodus embed-inline, seine Templates, die Ausgabe inline halten (keine Blockelemente) und typische Verwendungen wie kleine Icons, Links auf Dateien und Erwähnungen im Fließtext.
keywords: embed-inline, Inline-Einbettung, Rich Text inline, embed-inline.tpl, Inline-Objekt, Inline-Link, Inline-Bild, content/view/embed-inline.tpl, inline embed, rich text inline, inline object, inline link, inline image
type: help
---
# Embed-Inline-Ansicht

Diese Seite ist für Designer, die Objekte gestalten, die **innerhalb eines Satzes** von Rich Text eingebettet sind, nicht als
Block. Das Tag `embed-inline` verwendet den gleichnamigen Ansichtsmodus.

## Inhalt

1. [Templates](#templates)
2. [Inline bleiben](#inline-bleiben)
3. [Typische Verwendungen](#typische-verwendungen)

## Templates

- `content/datatype/view/ezxmltags/embed-inline.tpl` (und `embed-inline_node.tpl` für Knoten) ist das Tag-Template.
- `content/view/embed-inline.tpl` und `node/view/embed-inline.tpl` zeichnen das Objekt; überschreiben Sie sie pro Klasse mit
  `Source=content/view/embed-inline.tpl`.
- `content.ini [embed-inline]` enthält `AvailableViewModes[]`, `AvailableClasses[]` und `CustomAttributes[]` für den Editor.

## Inline bleiben

Die Ausgabe steht innerhalb eines Absatzes, also darf sie nur Inline-Elemente enthalten: `<a>`, `<span>`, `<img>`, `<abbr>`.
Ein `<div>` oder `<figure>` würde den Absatz zerreißen (Browser schließen ihn vor dem Block).

```tpl
{* override/templates/embed-inline/file.tpl *}
<span class="inline-file">{attribute_view_gui attribute=$object.data_map.file}</span>
```

Das mitgelieferte `content/datatype/view/ezbinaryfile.tpl` gibt einen Inline-Download-Link mit Dateiname und Größe aus
(`content/download/<object>/<attribute>/version/<version>/file/<name>`) und lässt sich hier also unverändert
wiederverwenden.

## Typische Verwendungen

- Ein kleiner verlinkter Name eines anderen Artikels.
- Ein Dateilink mit seinem Typ.
- Ein winziges Bild (ein Icon oder eine Flagge) mit `alt`-Text.

## Verwandte Seiten

- [Embed-Ansicht](embed-ansicht.md)
- [Rich-Text-Templates](rich-text-templates.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
