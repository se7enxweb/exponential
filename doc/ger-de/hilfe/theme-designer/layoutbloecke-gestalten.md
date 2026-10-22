---
title: "Layoutblöcke entwerfen"
audience: theme-designer
category: theme-design
order: 26
summary: Wie Theme-Designer für Exponential Layouts gestalten, wo Redakteure Seiten aus Blöcken bauen - Blocktypen und Ansichtstypen statt Seiten entwerfen, jede Reihenfolge und jede Zonenbreite, Container, gemeinsame Layouts für Kopf- und Fußbereich und ein Blatt zur Übergabe der Blockdesigns.
keywords: Theme-Designer, Layouts, Blöcke, Blocktypen, Ansichtstypen, Zonen, Layouttypen, Landingpage, Seitenbaukasten, Container, gemeinsame Layouts, Blockdesign, theme designer, layouts, blocks, block types, view types, zones, layout types, landing page, page builder, containers, shared layouts, block design
type: help
---
# Layoutblöcke entwerfen

Diese Seite ist für Theme-Designer, deren Website **Exponential Layouts** verwendet, den Blockeditor, in dem Redakteure
Seiten selbst anordnen. Auf diesen Seiten gestalten Sie keine Seite; Sie gestalten die **Blöcke**, die Redakteure
platzieren, die **Ansichtstypen**, die sie für jeden Block wählen, und die **Layouttypen**, deren Zonen die Blöcke
aufnehmen. Die technische Seite behandelt die Hilfe für Designer ausführlich:
[Layouts für Designer](../designer/theme-design/layouts-fuer-designer.md),
[Blockdefinitionen](../designer/theme-design/blockdefinitionen.md), [Block-Ansichts-Templates](../designer/theme-design/block-ansichtstemplates.md),
[Layout-Blöcke gestalten](../designer/theme-design/layout-bloecke-gestalten.md) und
[Tutorial: eine Landingpage mit Layouts bauen](../designer/theme-design/tutorial-eine-landingpage-mit-layouts-bauen.md).

## Inhalt

1. [Die Teile gestalten, nicht die Seite](#die-teile-gestalten-nicht-die-seite)
2. [Blocktypen und Ansichtstypen](#blocktypen-und-ansichtstypen)
3. [In jeder Reihenfolge, in jeder Breite](#in-jeder-reihenfolge-in-jeder-breite)
4. [Container](#container)
5. [Layouttypen und Zonen](#layouttypen-und-zonen)
6. [Gemeinsame Layouts](#gemeinsame-layouts)
7. [Ansichtstypen für Redakteure benennen](#ansichtstypen-für-redakteure-benennen)
8. [Das Blatt für Blockdesigns](#das-blatt-für-blockdesigns)

## Die Teile gestalten, nicht die Seite

Ein Mockup einer Landingpage ist trotzdem nützlich, als Beispiel dafür, was Redakteure bauen könnten. Was Sie aber
übergeben, ist eine Menge von Blöcken, jeder für sich gestaltet und neben jedem anderen geprüft. Fragen Sie sich bei
jedem Block: Sieht er direkt unter einem Hero richtig aus, direkt über dem Fußbereich, neben sich selbst und allein auf
einer Seite?

## Blocktypen und Ansichtstypen

Ein **Blocktyp** (Titel, Text, Liste, Galerie, Hero ...) legt fest, was ein Block enthält und welche Einstellungen
Redakteure ausfüllen. Ein **Ansichtstyp** ist eine der Darstellungen, die ein Blocktyp anbietet; Redakteure wählen eine
pro Block. Einen Ansichtstyp zu einem bestehenden Blocktyp hinzuzufügen braucht ein Template und eine Zeile
Einstellungen, kein PHP; das macht es zum günstigsten Weg, Redakteuren ein neues Aussehen zu geben
([Blockdefinitionen](../designer/theme-design/blockdefinitionen.md#einen-view-typ-hinzufügen-ohne-php)). Einen Blocktyp
hinzuzufügen braucht einen Entwickler.

Wird also ein neues Aussehen gewünscht, fragen Sie zuerst, ob es ein neuer Ansichtstyp eines bestehenden Blocks ist. Ein
Aussehen "Nachrichtenkarten" ist ein Ansichtstyp des Listenblocks, kein neuer Block.

## In jeder Reihenfolge, in jeder Breite

- **Reihenfolge:** Redakteure können jeden Block nach jedem anderen setzen. Der Abstand zwischen Blöcken kommt aus einer
  Regel des Designs, nicht aus den Außenabständen jedes Blocks
  ([Layout-Blöcke gestalten](../designer/theme-design/layout-bloecke-gestalten.md#abstände-zwischen-blöcken)).
- **Breite:** Ein Block kann in einer Zone voller Breite stehen, in einer Spalte eines Containers oder in einer schmalen
  Seitenzone. Zeichnen Sie jeden Ansichtstyp in voller Breite und bei etwa 300 Pixeln
  ([Abstände und Raster](abstaende-und-raster.md#raster-innerhalb-von-zonen)).
- **Inhalt:** Ein Listenblock kann einen Eintrag zeigen oder fünfzig; ein Textblock kann eine Zeile sein oder ein langer
  Artikel. Verwenden Sie die Stressfälle ([Mit echten Inhalten gestalten](mit-echten-inhalten-gestalten.md#die-stressfälle)).
- **Leer:** Ein Block, der nichts zu zeigen hat, sollte keine Lücke hinterlassen
  ([Leere Zustände und Fehlerzustände gestalten](leere-zustaende-und-fehlerzustaende-gestalten.md#leere-inhalte)).

## Container

Container-Blöcke (zwei, drei oder vier Spalten) enthalten andere Blöcke in ihren Platzhaltern. Gestalten Sie den Abstand
der Spalten und den Punkt, an dem sie auf kleinen Bildschirmen untereinander rutschen, und prüfen Sie Blöcke in einer
Spalte eines dreispaltigen Containers: Das ist die schmalste Stelle, an der die meisten Blöcke je stehen werden.

## Layouttypen und Zonen

Ein Layouttyp ist ein Raster benannter Zonen (zum Beispiel eine Hauptzone und eine Seitenleiste). Redakteure wählen einen
Layouttyp, wenn sie ein Layout anlegen; jeder Layouttyp ist also ein Seitengerüst, das Sie einmal gestalten
([Layouttypen und Zonen](../designer/theme-design/layouttypen-und-zonen.md)). Halten Sie ihre Zahl klein: Zwei oder
drei Layouttypen decken eine Inhaltswebsite meist ab.

## Gemeinsame Layouts

Manche Layouts sind **gemeinsam**: Ihre Zonen (oft ein Kopfbereich, ein Fußbereich oder ein Bereich vor dem Fußbereich)
sind in viele andere Layouts verknüpft, sodass eine Änderung überall erscheint. Gestalten Sie diese Blöcke mit derselben
Sorgfalt wie den Rahmen, denn sie erscheinen auf vielen Seiten
([Gemeinsame Layouts und verknüpfte Zonen](../designer/theme-design/gemeinsame-layouts-und-verknuepfte-zonen.md)).

## Ansichtstypen für Redakteure benennen

Redakteure wählen Ansichtstypen aus einer Liste von Namen. Benennen Sie jeden danach, wie er aussieht und wo er passt:

| Schlechter Name | Besserer Name |
|---|---|
| `style2` | Karten, drei Spalten |
| `dark` | Hervorgehobenes Band |
| `zigzag` | Bild und Text im Wechsel |

Der Bezeichner in den Einstellungen verwendet das Präfix Ihres Themes (`mytheme_cards`); die Beschriftung, die
Redakteure sehen, wird übersetzt ([Für Redakteure gestalten](fuer-redakteure-gestalten.md#redakteuren-sagen-was-das-design-erwartet)).

## Das Blatt für Blockdesigns

Übergeben Sie für jeden Blocktyp und Ansichtstyp:

| Punkt | Inhalt |
|---|---|
| Block- und Ansichtstyp | list, `mytheme_cards` |
| Zweck | drei bis zwölf Einträge als Karten |
| Verwendete Einstellungen | Anzahl der Einträge, Datum zeigen ja oder nein |
| Ansicht der Einträge | der Kartenmodus ([Ansichtsmodi gestalten](ansichtsmodi-gestalten.md)) |
| Breiten | voll, zweispaltig, Seitenzone |
| Randfälle | ein Eintrag, kein Bild, leere Sammlung |
| Themes | hell, dunkel, von rechts nach links |

Die [Styleguide-Seite](eine-styleguide-seite-bauen.md) kann jeden Blocktyp und Ansichtstyp mit Beispielinhalten zeigen,
oder Sie bauen auf der Entwicklungsinstallation ein Testlayout mit allen.

## Verwandte Seiten

- [Layouts für Designer](../designer/theme-design/layouts-fuer-designer.md)
- [Der Layout-Editor](../layout-editor.md)
- [Kurs zu Exponential Layouts für Trainer](../trainer/layouts/README.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite dürfen Sie unter der GNU FDL 1.3 frei kopieren, übersetzen und verbessern.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
