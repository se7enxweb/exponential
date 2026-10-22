---
title: "Ansichtsmodi gestalten"
audience: theme-designer
category: theme-design
order: 9
summary: Wie Theme-Designer die Ansichten jeder Inhaltsklasse planen - full, line, embed, embed-inline und eigene Modi wie card - mit einer Ansichtsmatrix, wie wenige Modi ein Design wirklich braucht und was Sie dem Entwickler übergeben.
keywords: Theme-Designer, Ansichtsmodi, Vollansicht, Zeilenansicht, Einbettung, Karte, Teaser, Ansichtsmatrix, eigener Ansichtsmodus, theme designer, view modes, full view, line view, embed, embed-inline, card, teaser, view matrix, custom view mode, node_view_gui
type: help
---
# Ansichtsmodi gestalten

Diese Seite ist für Theme-Designer, die planen, wie jede Art von Inhalt an jeder Stelle aussieht, an der sie erscheint.
In Exponential wird dasselbe Objekt von verschiedenen Templates gezeichnet, je nachdem, wo es gezeigt wird: auf seiner
eigenen Seite, als Eintrag in einer Liste, als Einbettung in einem anderen Text. Jede dieser Darstellungen ist ein
**Ansichtsmodus**. Die technische Seite, einschließlich der vorhandenen Templates und wie ein Modus gewählt wird, steht
in [Ansichtsmodi](../designer/vorlagen/ansichtsmodi.md); diese Seite handelt davon, welche Modi Ihr Design braucht und
wie Sie sie zeichnen.

## Inhalt

1. [Die Modi, denen Sie begegnen](#die-modi-denen-sie-begegnen)
2. [Die Ansichtsmatrix](#die-ansichtsmatrix)
3. [Wie viele Modi ein Design braucht](#wie-viele-modi-ein-design-braucht)
4. [Jeden Modus gestalten](#jeden-modus-gestalten)
5. [Einen eigenen Modus ergänzen](#einen-eigenen-modus-ergänzen)
6. [Was Sie dem Entwickler übergeben](#was-sie-dem-entwickler-übergeben)

## Die Modi, denen Sie begegnen

| Modus | Wo er erscheint | Schwerpunkt des Designs |
|---|---|---|
| `full` | die eigene Seite des Objekts, innerhalb des Rahmens | Lesen, Hierarchie, alle Felder |
| `line` | ein Eintrag in einer Liste von Unterelementen, in Suchergebnissen und manchen Blöcken | Überfliegen: Titel, Datum, eine Zeile Kontext |
| `embed` | ein Objekt, das ein Redakteur in formatierten Text setzt, als eigener Block | passt in eine Textspalte; Bild- oder Dateieinbettungen sind häufig |
| `embed-inline` | ein Objekt innerhalb einer Textzeile | nicht mehr als ein Link oder ein kleines Label |
| eigene (zum Beispiel `card`) | Listen, Raster, Layoutblöcke | was Ihr Design festlegt |

Das mitgelieferte Kernel-Design hat weitere kurze Modi (`text_linked`, `tiny`, `listitem`, `thumb`, `print` und andere);
die meisten Designs lassen sie, wie sie sind.

## Die Ansichtsmatrix

Legen Sie eine Matrix aus Klassen und Modi an und markieren Sie, welche Zellen Ihr Design verwendet:

| Klasse | full | line | card | embed | embed-inline |
|---|---|---|---|---|---|
| article | ja | ja | ja | ja | Link |
| folder | ja | ja | ja | nein | Link |
| image | ja | nein | nein | ja | nein |
| event | ja | ja | ja | ja | Link |
| file | Download | ja | nein | ja | Link |

Jedes "ja" ist ein Mockup und später ein Template (oder ein Fall in einem gemeinsamen Template). Jedes "nein" stellt
trotzdem etwas dar, über das Standard-Template dieses Modus; prüfen Sie also, wie es aussieht, und entscheiden Sie, ob
es akzeptabel ist.

## Wie viele Modi ein Design braucht

Weniger, als Sie denken. Jeder zusätzliche Modus ist ein Template pro Klasse, das geschrieben, getestet und gepflegt
werden muss. Eine typische Inhaltswebsite braucht `full`, `line`, einen Kartenmodus und `embed`. Bevor Sie einen Modus
ergänzen, fragen Sie, ob der Unterschied stattdessen sein kann:

- ein **CSS-Kontext**: dasselbe `line`-Markup, in einem Seitenleistenblock anders gestaltet;
- ein **Ansichtstyp eines Layoutblocks**: ein Raster- oder Listenstil, den Redakteure pro Block wählen
  ([Layoutblöcke entwerfen](layoutbloecke-gestalten.md));
- ein **Bild-Alias**: dieselbe Karte mit einem anderen Zuschnitt.

## Jeden Modus gestalten

**Full.** Gestalten Sie das Leseerlebnis: Schlagzeile, Metadaten, Vorspann, Haupttext, verwandte Inhalte. Zeichnen Sie
es auch mit leeren optionalen Feldern ([Für Redakteure gestalten](fuer-redakteure-gestalten.md)). Der Rahmen darum ist das
Pagelayout, das Sie einmal gestalten ([Kopf- und Fußbereiche gestalten](kopf-und-fussbereiche-gestalten.md)).

**Line.** Eine oder zwei Zeilen: der Titel als Link, Datum oder Typ, höchstens eine Zeile Einleitung. Zeilen erscheinen
in langen Listen, daher zählt der vertikale Rhythmus mehr als Dekoration
([Inhaltslisten und Karten gestalten](inhaltslisten-und-karten-gestalten.md)).

**Card.** Bild, Titel, kurzer Text, vielleicht ein Label. Karten erscheinen in Rastern mit zwei bis vier Spalten und
müssen mit und ohne Bild funktionieren.

**Embed.** Muss in die Textspalte jedes Felds mit formatiertem Text passen, in jeder Breite, und muss zwischen zwei
Absätzen gewollt aussehen. Bilder, die Redakteure einbetten, erhalten ihre Größe meist aus dem Alias, den der Redakteur
im Online-Editor wählt.

**Embed-inline.** Beschränken Sie sich auf Inline-Elemente; alles auf Blockebene zerbricht den Absatz drumherum.

## Einen eigenen Modus ergänzen

Ein eigener Modus ist ein neuer Name (zum Beispiel `card`) mit einem Standard-Template für alle Klassen und Overrides
für die Klassen, die ein eigenes brauchen. Die Schritte stehen in [Eigene Ansichtsmodi](../designer/vorlagen/eigene-ansichtsmodi.md),
und das Medien-Theme zeigt Beispiele ([Ein Rundgang durch das Medien-Theme](../designer/theme-design/rundgang-durch-das-media-theme.md)).
Benennen Sie Ihre Modi nach ihrer Rolle (`card`, `teaser`) statt nach ihrem Aussehen (`blue_box`), denn das Aussehen
wird sich ändern.

## Was Sie dem Entwickler übergeben

- Die Ansichtsmatrix, mit "ja", "nein" und "Standard reicht" in jeder Zelle.
- Ein Mockup pro "ja", mit dem kürzesten und dem längsten Inhalt.
- Für jeden Modus die verwendeten Felder, aus dem [Zuordnungsblatt](ein-design-auf-inhaltsklassen-abbilden.md#das-zuordnungsblatt).
- Welchen Bild-Alias jeder Modus verwendet ([Bildzuschnitte und Art Direction gestalten](bildzuschnitte-und-art-direction-gestalten.md)).

## Verwandte Seiten

- [Ansichtsmodi](../designer/vorlagen/ansichtsmodi.md)
- [Vollansicht](../designer/vorlagen/vollansicht.md), [Zeilenansicht](../designer/vorlagen/zeilenansicht.md), [Einbettungsansicht](../designer/vorlagen/embed-ansicht.md)
- [Knoten-Templates nach Klasse](../designer/vorlagen/knoten-templates-nach-klasse.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite dürfen Sie unter der GNU FDL 1.3 frei kopieren, übersetzen und verbessern.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
